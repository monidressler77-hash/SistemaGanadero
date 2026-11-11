<?php
// app/Models/Bovino.php
declare(strict_types=1);

class Bovino {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function getAll(array $filters = []): array {
        $sql = "SELECT * FROM bovinos WHERE 1=1";
        $params = [];

        if (!empty($filters['q'])) {
            $sql .= " AND (rp LIKE :q OR nombre LIKE :q2 OR raza LIKE :q3)";
            $params['q']  = '%' . $filters['q'] . '%';
            $params['q2'] = '%' . $filters['q'] . '%';
            $params['q3'] = '%' . $filters['q'] . '%';
        }
        if (!empty($filters['categoria'])) {
            $sql .= " AND categoria = :categoria";
            $params['categoria'] = $filters['categoria'];
        }
        if (!empty($filters['proposito'])) {
            $sql .= " AND proposito = :proposito";
            $params['proposito'] = $filters['proposito'];
        }
        if (!empty($filters['sistema_alimentacion'])) {
            $sql .= " AND sistema_alimentacion = :sistema_alimentacion";
            $params['sistema_alimentacion'] = $filters['sistema_alimentacion'];
        }
        if (!empty($filters['estado_general'])) {
            $sql .= " AND estado_general = :estado_general";
            $params['estado_general'] = $filters['estado_general'];
        }

        $sql .= " ORDER BY estado_general ASC, id DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM bovinos WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findByRP(string $rp): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM bovinos WHERE rp = :rp LIMIT 1");
        $stmt->execute(['rp' => $rp]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): int {
        $this->pdo->beginTransaction();
        try {
            $sql = "INSERT INTO bovinos 
                    (rp, nombre, categoria, raza, sexo, fecha_nacimiento, peso_actual, proposito, sistema_alimentacion, estado_reproductivo, estado_general, observaciones)
                    VALUES (:rp, :nombre, :categoria, :raza, :sexo, :fecha_nacimiento, :peso_actual, :proposito, :sistema_alimentacion, :estado_reproductivo, 'activo', :observaciones)";
            $stmt = $this->pdo->prepare($sql);
            $pesoInicial = !empty($data['peso_inicial']) ? (float)$data['peso_inicial'] : null;

            $stmt->execute([
                'rp'                   => strtoupper(trim($data['rp'])),
                'nombre'               => trim($data['nombre'] ?? '') ?: null,
                'categoria'            => $data['categoria'],
                'raza'                 => trim($data['raza']),
                'sexo'                 => $data['sexo'],
                'fecha_nacimiento'     => $data['fecha_nacimiento'],
                'peso_actual'          => $pesoInicial,
                'proposito'            => $data['proposito'],
                'sistema_alimentacion' => $data['sistema_alimentacion'],
                'estado_reproductivo'  => $data['sexo'] === 'M' ? 'no_aplica' : ($data['estado_reproductivo'] ?? 'vacia'),
                'observaciones'        => trim($data['observaciones'] ?? '') ?: null,
            ]);

            $bovinoId = (int) $this->pdo->lastInsertId();

            // Si se indicó peso inicial al dar de alta, crear el primer registro en pesajes (RN-04)
            if ($pesoInicial !== null && $pesoInicial > 0) {
                $stmtPesaje = $this->pdo->prepare("
                    INSERT INTO pesajes (bovino_id, fecha_pesaje, peso_kg, dias_transcurridos, gmd_calculada, observaciones)
                    VALUES (:bovino_id, :fecha_pesaje, :peso_kg, 0, NULL, 'Peso inicial de registro')
                ");
                $stmtPesaje->execute([
                    'bovino_id'    => $bovinoId,
                    'fecha_pesaje' => date('Y-m-d'),
                    'peso_kg'      => $pesoInicial
                ]);
            }

            $this->pdo->commit();
            return $bovinoId;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function getPesajes(int $bovinoId): array {
        $stmt = $this->pdo->prepare("SELECT * FROM pesajes WHERE bovino_id = :id ORDER BY fecha_pesaje DESC, id DESC");
        $stmt->execute(['id' => $bovinoId]);
        return $stmt->fetchAll();
    }

    /**
     * Registra un nuevo pesaje y calcula automáticamente la GMD (RF-03 / RN-04)
     */
    public function registrarPesaje(int $bovinoId, string $fechaPesaje, float $pesoKg, string $observaciones = ''): array {
        if ($pesoKg < 15 || $pesoKg > 1500) {
            throw new Exception("Regla Biológica (CA-02): El peso debe estar entre 15 kg y 1.500 kg.");
        }

        // Buscar el último pesaje anterior
        $stmtUlt = $this->pdo->prepare("SELECT fecha_pesaje, peso_kg FROM pesajes WHERE bovino_id = :id ORDER BY fecha_pesaje DESC, id DESC LIMIT 1");
        $stmtUlt->execute(['id' => $bovinoId]);
        $ultimo = $stmtUlt->fetch();

        $dias = 0;
        $gmd  = null;

        if ($ultimo) {
            $fechaAnt = new DateTime($ultimo['fecha_pesaje']);
            $fechaAct = new DateTime($fechaPesaje);
            if ($fechaAct <= $fechaAnt) {
                throw new Exception("Consistencia Temporal (RN-04): La fecha del nuevo pesaje debe ser posterior al último pesaje registrado (" . $fechaAnt->format('d/m/Y') . ").");
            }
            $dias = (int) $fechaAnt->diff($fechaAct)->days;
            if ($dias > 0) {
                $gmd = round(($pesoKg - (float)$ultimo['peso_kg']) / $dias, 3);
            }
        }

        $this->pdo->beginTransaction();
        try {
            $stmtIns = $this->pdo->prepare("
                INSERT INTO pesajes (bovino_id, fecha_pesaje, peso_kg, dias_transcurridos, gmd_calculada, observaciones)
                VALUES (:bovino_id, :fecha_pesaje, :peso_kg, :dias, :gmd, :obs)
            ");
            $stmtIns->execute([
                'bovino_id'    => $bovinoId,
                'fecha_pesaje' => $fechaPesaje,
                'peso_kg'      => $pesoKg,
                'dias'         => $dias,
                'gmd'          => $gmd,
                'obs'          => $observaciones ?: null
            ]);

            $stmtUpd = $this->pdo->prepare("UPDATE bovinos SET peso_actual = :peso WHERE id = :id");
            $stmtUpd->execute(['peso' => $pesoKg, 'id' => $bovinoId]);

            $this->pdo->commit();
            return ['dias' => $dias, 'gmd' => $gmd];
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function getSanidad(int $bovinoId): array {
        $stmt = $this->pdo->prepare("
            SELECT s.*, i.nombre_insumo, i.unidad_medida 
            FROM sanidad s
            INNER JOIN insumos i ON s.insumo_id = i.id
            WHERE s.bovino_id = :id
            ORDER BY s.fecha_aplicacion DESC, s.id DESC
        ");
        $stmt->execute(['id' => $bovinoId]);
        return $stmt->fetchAll();
    }

    /**
     * Registra evento sanitario con descuento atómico de stock y bloqueo por carencia (RF-04 / RN-03 / RN-07)
     */
    public function registrarSanidad(int $bovinoId, int $insumoId, string $tipoEvento, string $fechaAplicacion, float $dosis, string $diagnostico = ''): void {
        if ($dosis <= 0) {
            throw new Exception("La dosis aplicada debe ser mayor a cero.");
        }

        $this->pdo->beginTransaction();
        try {
            // Bloquear fila del insumo para verificar stock (ACID)
            $stmtInsumo = $this->pdo->prepare("SELECT * FROM insumos WHERE id = :id FOR UPDATE");
            $stmtInsumo->execute(['id' => $insumoId]);
            $insumo = $stmtInsumo->fetch();

            if (!$insumo) {
                throw new Exception("El insumo seleccionado no existe en el almacén (RN-07).");
            }
            if ((float)$insumo['stock_actual'] < $dosis) {
                throw new Exception("Regla de Negocio (RN-03 / RN-04): Stock insuficiente de '{$insumo['nombre_insumo']}'. Disponible: {$insumo['stock_actual']} {$insumo['unidad_medida']}.");
            }

            $diasCarencia = (int) $insumo['dias_retiro'];
            $fechaFinRetiro = $fechaAplicacion;
            if ($diasCarencia > 0) {
                $dt = new DateTime($fechaAplicacion);
                $dt->modify("+{$diasCarencia} days");
                $fechaFinRetiro = $dt->format('Y-m-d');
            }

            $costoTotal = round($dosis * (float)$insumo['costo_unitario'], 2);

            // 1. Insertar el tratamiento sanitario
            $stmtSan = $this->pdo->prepare("
                INSERT INTO sanidad (bovino_id, insumo_id, tipo_evento, fecha_aplicacion, dosis_aplicada, dias_carencia, fecha_fin_retiro, costo_total, diagnostico_observacion)
                VALUES (:bovino_id, :insumo_id, :tipo, :fecha, :dosis, :carencia, :fin_retiro, :costo, :diag)
            ");
            $stmtSan->execute([
                'bovino_id'  => $bovinoId,
                'insumo_id'  => $insumoId,
                'tipo'       => $tipoEvento,
                'fecha'      => $fechaAplicacion,
                'dosis'      => $dosis,
                'carencia'   => $diasCarencia,
                'fin_retiro' => $fechaFinRetiro,
                'costo'      => $costoTotal,
                'diag'       => $diagnostico ?: null
            ]);

            // 2. Descontar stock atómicamente
            $stmtDesc = $this->pdo->prepare("UPDATE insumos SET stock_actual = stock_actual - :dosis WHERE id = :id");
            $stmtDesc->execute(['dosis' => $dosis, 'id' => $insumoId]);

            // 3. Actualizar estado sanitario del bovino si aplica carencia farmacológica (RN-03)
            if ($diasCarencia > 0 && $fechaFinRetiro >= date('Y-m-d')) {
                $stmtBov = $this->pdo->prepare("
                    UPDATE bovinos 
                    SET estado_sanitario = 'en_carencia', 
                        fecha_liberacion_carencia = GREATEST(COALESCE(fecha_liberacion_carencia, :fin1), :fin2)
                    WHERE id = :id
                ");
                $stmtBov->execute(['fin1' => $fechaFinRetiro, 'fin2' => $fechaFinRetiro, 'id' => $bovinoId]);
            }

            $this->pdo->commit();
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function getReproduccion(int $bovinoId): array {
        $stmt = $this->pdo->prepare("SELECT * FROM reproduccion WHERE bovino_id = :id ORDER BY fecha_evento DESC, id DESC");
        $stmt->execute(['id' => $bovinoId]);
        return $stmt->fetchAll();
    }

    /**
     * Registra evento reproductivo y calcula automáticamente la FEP (+283 días) (RF-07)
     */
    public function registrarReproduccion(int $bovinoId, string $tipoEvento, string $fechaEvento, string $toroSemen = '', string $obs = ''): ?string {
        $bovino = $this->findById($bovinoId);
        if (!$bovino || $bovino['sexo'] !== 'H') {
            throw new Exception("Regla de Negocio: Los eventos reproductivos solo son aplicables a ejemplares hembras.");
        }

        $fep = null;
        $nuevoEstadoRepro = $bovino['estado_reproductivo'];

        if (in_array($tipoEvento, ['monta_natural', 'inseminacion', 'confirmacion_prenez'], true)) {
            $dt = new DateTime($fechaEvento);
            $dt->modify('+283 days'); // Ciclo gestacional bovino estándar (CP-06)
            $fep = $dt->format('Y-m-d');
            $nuevoEstadoRepro = ($tipoEvento === 'confirmacion_prenez') ? 'prenada' : 'servida';
        } elseif ($tipoEvento === 'parto') {
            $nuevoEstadoRepro = 'lactante';
        } elseif ($tipoEvento === 'aborto' || $tipoEvento === 'celo') {
            $nuevoEstadoRepro = 'vacia';
        }

        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO reproduccion (bovino_id, tipo_evento, fecha_evento, toro_o_semen, fecha_estimada_parto, resultado_observacion)
                VALUES (:bovino_id, :tipo, :fecha, :toro, :fep, :obs)
            ");
            $stmt->execute([
                'bovino_id' => $bovinoId,
                'tipo'      => $tipoEvento,
                'fecha'     => $fechaEvento,
                'toro'      => $toroSemen ?: null,
                'fep'       => $fep,
                'obs'       => $obs ?: null
            ]);

            $stmtUpd = $this->pdo->prepare("UPDATE bovinos SET estado_reproductivo = :est WHERE id = :id");
            $stmtUpd->execute(['est' => $nuevoEstadoRepro, 'id' => $bovinoId]);

            $this->pdo->commit();
            return $fep;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Registra la baja de un bovino validando que no se venda ni consuma en período de carencia (RF-05 / RN-03 / RN-05)
     */
    public function registrarBaja(int $bovinoId, string $fechaBaja, string $tipoBaja, string $motivo, float $precioVenta = 0): void {
        $bovino = $this->findById($bovinoId);
        if (!$bovino || $bovino['estado_general'] !== 'activo') {
            throw new Exception("El animal ya se encuentra inactivo.");
        }

        // Bloqueo zoosanitario RN-03: prohibido vender o destinar a consumo si está en retiro farmacológico
        if (in_array($tipoBaja, ['venta', 'consumo'], true) && $bovino['estado_sanitario'] === 'en_carencia') {
            if (!empty($bovino['fecha_liberacion_carencia']) && $bovino['fecha_liberacion_carencia'] >= $fechaBaja) {
                throw new Exception("BLOQUEO ZOOSANITARIO (RN-03): No se permite dar de baja por '{$tipoBaja}' al animal {$bovino['rp']} porque posee carencia farmacológica vigente hasta el " . date('d/m/Y', strtotime($bovino['fecha_liberacion_carencia'])) . ".");
            }
        }

        $nuevoEstadoGeneral = match($tipoBaja) {
            'venta'   => 'vendido',
            'muerte'  => 'muerto',
            'consumo' => 'consumo',
            default   => 'vendido'
        };

        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO bajas_bovinos (bovino_id, fecha_baja, tipo_baja, detalle_motivo, precio_venta_gs)
                VALUES (:bovino_id, :fecha, :tipo, :motivo, :precio)
            ");
            $stmt->execute([
                'bovino_id' => $bovinoId,
                'fecha'     => $fechaBaja,
                'tipo'      => $tipoBaja,
                'motivo'    => $motivo,
                'precio'    => $precioVenta
            ]);

            $stmtUpd = $this->pdo->prepare("UPDATE bovinos SET estado_general = :estado WHERE id = :id");
            $stmtUpd->execute(['estado' => $nuevoEstadoGeneral, 'id' => $bovinoId]);

            $this->pdo->commit();
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}