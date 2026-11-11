<?php require_once dirname(__DIR__) . '/layouts/header.php'; ?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <div>
        <h4 class="fw-bold mb-0">
            <span class="badge bg-success me-2"><?= htmlspecialchars($bovino['rp']) ?></span>
            Ficha Zootécnica y Clínica: <?= htmlspecialchars($bovino['nombre'] ?: 'Sin Apodo') ?>
        </h4>
        <p class="text-muted small mb-0">
            Categoría: <strong><?= ucfirst($bovino['categoria']) ?></strong> |
            Raza: <strong><?= htmlspecialchars($bovino['raza']) ?></strong> |
            Sexo: <strong><?= $bovino['sexo'] === 'M' ? 'Macho' : 'Hembra' ?></strong> |
            Propósito: <strong><?= str_replace('_', ' ', ucfirst($bovino['proposito'])) ?></strong> |
            Alimentación: <strong><?= str_replace('_', ' ', ucfirst($bovino['sistema_alimentacion'])) ?></strong>
        </p>
    </div>
    <div class="d-flex gap-2">
        <a href="index.php?action=reportes&bovino_id=<?= $bovino['id'] ?>" class="btn btn-outline-danger btn-sm">
            <i class="bi bi-file-earmark-pdf me-1"></i> Imprimir Ficha PDF
        </a>
        <a href="index.php?action=bovinos" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Volver al Hato
        </a>
    </div>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger shadow-sm"><i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($error) ?></div>
<?php endif; ?>
<?php if (!empty($success)): ?>
    <div class="alert alert-success shadow-sm"><i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<?php if ($bovino['estado_sanitario'] === 'en_carencia'): ?>
    <div class="alert alert-warning border-start border-danger border-4 shadow-sm">
        <i class="bi bi-shield-fill-exclamation text-danger me-2 fs-5"></i>
        <strong>ALERTA DE BIOSEGURIDAD (RN-03):</strong> Este ejemplar se encuentra bajo <strong>Período de Carencia Farmacológica</strong> hasta el <strong><?= date('d/m/Y', strtotime($bovino['fecha_liberacion_carencia'])) ?></strong>. Su venta o faena está bloqueada por el sistema.
    </div>
<?php endif; ?>

<div class="row g-4">
    <!-- Panel Lateral: Resumen del Ejemplar y Baja (RF-05) -->
    <div class="col-12 col-lg-4">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-dark text-white fw-bold">Resumen Biométrico Actual</div>
            <div class="card-body">
                <div class="row g-2 text-center mb-3">
                    <div class="col-6">
                        <div class="p-2 bg-light rounded border">
                            <div class="small text-muted">PESO ACTUAL</div>
                            <div class="fs-4 fw-bold text-success"><?= $bovino['peso_actual'] ? number_format((float)$bovino['peso_actual'], 1) . ' kg' : 'S/D' ?></div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2 bg-light rounded border">
                            <div class="small text-muted">ESTADO GENERAL</div>
                            <div class="fs-5 fw-bold text-uppercase <?= $bovino['estado_general'] === 'activo' ? 'text-primary' : 'text-danger' ?>">
                                <?= htmlspecialchars($bovino['estado_general']) ?>
                            </div>
                        </div>
                    </div>
                </div>
                <ul class="list-group list-group-flush small">
                    <li class="list-group-item d-flex justify-content-between"><span>Fecha de Nacimiento:</span> <strong><?= date('d/m/Y', strtotime($bovino['fecha_nacimiento'])) ?></strong></li>
                    <li class="list-group-item d-flex justify-content-between"><span>Estado Reproductivo:</span> <strong class="text-uppercase"><?= str_replace('_', ' ', $bovino['estado_reproductivo']) ?></strong></li>
                    <li class="list-group-item"><span>Observaciones:</span> <div class="text-muted mt-1"><?= htmlspecialchars($bovino['observaciones'] ?: 'Sin observaciones.') ?></div></li>
                </ul>
            </div>
        </div>

        <?php if ($bovino['estado_general'] === 'activo'): ?>
        <div class="card shadow-sm border-danger">
            <div class="card-header bg-danger text-white fw-bold"><i class="bi bi-box-arrow-right me-1"></i> Registrar Baja del Animal (RF-05)</div>
            <div class="card-body">
                <form method="POST" action="index.php?action=bovino_show&id=<?= $bovino['id'] ?>">
                    <input type="hidden" name="form_type" value="baja">
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Causal de Baja (*)</label>
                        <select name="tipo_baja" class="form-select form-select-sm" required>
                            <option value="venta">Venta Comercial en Pie</option>
                            <option value="consumo">Consumo Interno Familiar</option>
                            <option value="muerte">Muerte / Siniestro en Campo</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Fecha de Baja (*)</label>
                        <input type="date" name="fecha_baja" class="form-control form-control-sm" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Valor de Venta (Gs. - Opcional)</label>
                        <input type="number" name="precio_venta_gs" class="form-control form-control-sm" placeholder="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Detalle / Motivo Clínico o Destino (*)</label>
                        <input type="text" name="detalle_motivo" class="form-control form-control-sm" placeholder="Comprador o causa..." required>
                    </div>
                    <button type="submit" class="btn btn-outline-danger btn-sm w-100 fw-semibold" onclick="return confirm('¿Confirma dar de baja este ejemplar del hato activo?');">
                        Confirmar Baja del Hato
                    </button>
                </form>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Panel Principal: Control Ponderal (GMD), Sanidad (ACID) y Reproducción (FEP) -->
    <div class="col-12 col-lg-8">
        <!-- 1. CONTROL PONDERAL Y CÁLCULO DE GMD (RF-03 / CU-03) -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-success"><i class="bi bi-speedometer me-2"></i>1. Control Ponderal y Ganancia Media Diaria — GMD (RF-03 / RN-04)</h6>
            </div>
            <div class="card-body">
                <?php if ($bovino['estado_general'] === 'activo'): ?>
                <form method="POST" action="index.php?action=bovino_show&id=<?= $bovino['id'] ?>" class="row g-2 align-items-end mb-3 p-3 bg-light rounded border">
                    <input type="hidden" name="form_type" value="pesaje">
                    <div class="col-12 col-sm-3">
                        <label class="form-label small fw-semibold mb-1">Fecha de Pesada</label>
                        <input type="date" name="fecha_pesaje" class="form-control form-control-sm" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="col-12 col-sm-3">
                        <label class="form-label small fw-semibold mb-1">Peso en Báscula (kg)</label>
                        <input type="number" step="0.01" min="15" max="1500" name="peso_kg" class="form-control form-control-sm" placeholder="Ej: 395.00" required>
                    </div>
                    <div class="col-12 col-sm-4">
                        <label class="form-label small fw-semibold mb-1">Observación</label>
                        <input type="text" name="observaciones" class="form-control form-control-sm" placeholder="Control mensual...">
                    </div>
                    <div class="col-12 col-sm-2">
                        <button type="submit" class="btn btn-success btn-sm w-100 fw-semibold">+ Pesar</button>
                    </div>
                </form>
                <?php endif; ?>

                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle mb-0 small">
                        <thead class="table-light">
                            <tr>
                                <th>Fecha</th>
                                <th>Peso (kg)</th>
                                <th>Días Transcurridos</th>
                                <th>GMD Calculada (kg/día)</th>
                                <th>Observaciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pesajes as $p): ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($p['fecha_pesaje'])) ?></td>
                                <td class="fw-bold"><?= number_format((float)$p['peso_kg'], 2) ?> kg</td>
                                <td><?= (int)$p['dias_transcurridos'] ?> días</td>
                                <td>
                                    <?php if ($p['gmd_calculada'] !== null): ?>
                                        <span class="badge bg-success">+<?= number_format((float)$p['gmd_calculada'], 3) ?> kg/día</span>
                                    <?php else: ?>
                                        <span class="text-muted">Peso Base Inicial</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($p['observaciones'] ?: '—') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- 2. SANIDAD Y DEDUCCIÓN ATÓMICA DE STOCK (RF-04 / CU-04 / RN-03) -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-Shield-plus me-2"></i>2. Historial Sanitario y Cálculo de Carencia Farmacológica (RF-04 / RN-03)</h6>
            </div>
            <div class="card-body">
                <?php if ($bovino['estado_general'] === 'activo'): ?>
                <form method="POST" action="index.php?action=bovino_show&id=<?= $bovino['id'] ?>" class="row g-2 align-items-end mb-3 p-3 bg-light rounded border">
                    <input type="hidden" name="form_type" value="sanidad">
                    <div class="col-12 col-md-4">
                        <label class="form-label small fw-semibold mb-1">Fármaco / Vacuna en Depósito (*)</label>
                        <select name="insumo_id" class="form-select form-select-sm" required>
                            <?php foreach ($insumosSan as $ins): ?>
                                <option value="<?= $ins['id'] ?>">
                                    <?= htmlspecialchars($ins['nombre_insumo']) ?> (Stock: <?= $ins['stock_actual'] ?> <?= $ins['unidad_medida'] ?> | Retiro: <?= $ins['dias_retiro'] ?>d)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label small fw-semibold mb-1">Evento</label>
                        <select name="tipo_evento" class="form-select form-select-sm">
                            <option value="vacunacion">Vacunación</option>
                            <option value="desparasitacion">Desparasitación</option>
                            <option value="tratamiento_clinico">Tratamiento</option>
                        </select>
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label small fw-semibold mb-1">Dosis</label>
                        <input type="number" step="0.01" min="0.1" name="dosis_aplicada" class="form-control form-control-sm" value="1.00" required>
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label small fw-semibold mb-1">Fecha</label>
                        <input type="date" name="fecha_aplicacion" class="form-control form-control-sm" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="col-6 col-md-2">
                        <button type="submit" class="btn btn-primary btn-sm w-100 fw-semibold">Aplicar</button>
                    </div>
                    <div class="col-12 mt-2">
                        <input type="text" name="diagnostico_observacion" class="form-control form-control-sm" placeholder="Diagnóstico clínico o motivo de aplicación...">
                    </div>
                </form>
                <?php endif; ?>

                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle mb-0 small">
                        <thead class="table-light">
                            <tr>
                                <th>Fecha</th>
                                <th>Evento / Producto</th>
                                <th>Dosis</th>
                                <th>Fin de Retiro (RN-03)</th>
                                <?php if (($_SESSION['user_role'] ?? '') === 'admin'): ?><th>Costo (Gs.)</th><?php endif; ?>
                                <th>Diagnóstico</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($sanidad)): ?>
                                <tr><td colspan="6" class="text-center text-muted py-2">Sin eventos sanitarios registrados.</td></tr>
                            <?php else: ?>
                                <?php foreach ($sanidad as $s): ?>
                                <tr>
                                    <td><?= date('d/m/Y', strtotime($s['fecha_aplicacion'])) ?></td>
                                    <td><strong><?= htmlspecialchars($s['nombre_insumo']) ?></strong> <span class="badge bg-secondary"><?= ucfirst(str_replace('_',' ',$s['tipo_evento'])) ?></span></td>
                                    <td><?= number_format((float)$s['dosis_aplicada'], 2) ?> <?= htmlspecialchars($s['unidad_medida']) ?></td>
                                    <td>
                                        <?php if ((int)$s['dias_carencia'] > 0): ?>
                                            <span class="badge bg-danger"><?= date('d/m/Y', strtotime($s['fecha_fin_retiro'])) ?> (<?= $s['dias_carencia'] ?>d)</span>
                                        <?php else: ?>
                                            <span class="badge bg-success">Sin carencia</span>
                                        <?php endif; ?>
                                    </td>
                                    <?php if (($_SESSION['user_role'] ?? '') === 'admin'): ?>
                                        <td>Gs. <?= number_format((float)$s['costo_total'], 0, ',', '.') ?></td>
                                    <?php endif; ?>
                                    <td><?= htmlspecialchars($s['diagnostico_observacion'] ?: '—') ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- 3. CICLO REPRODUCTIVO Y FECHA ESTIMADA DE PARTO - FEP (RF-07) -->
        <?php if ($bovino['sexo'] === 'H'): ?>
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold text-danger"><i class="bi bi-calendar-heart me-2"></i>3. Control Reproductivo y Proyección de Parto — FEP +283 días (RF-07)</h6>
            </div>
            <div class="card-body">
                <?php if ($bovino['estado_general'] === 'activo'): ?>
                <form method="POST" action="index.php?action=bovino_show&id=<?= $bovino['id'] ?>" class="row g-2 align-items-end mb-3 p-3 bg-light rounded border">
                    <input type="hidden" name="form_type" value="reproduccion">
                    <div class="col-12 col-sm-3">
                        <label class="form-label small fw-semibold mb-1">Evento Reproductivo</label>
                        <select name="tipo_evento" class="form-select form-select-sm">
                            <option value="inseminacion">Inseminación Artificial</option>
                            <option value="monta_natural">Monta Natural</option>
                            <option value="confirmacion_prenez">Confirmación Preñez</option>
                            <option value="parto">Parto Normal</option>
                            <option value="celo">Detección de Celo</option>
                        </select>
                    </div>
                    <div class="col-12 col-sm-3">
                        <label class="form-label small fw-semibold mb-1">Fecha del Evento</label>
                        <input type="date" name="fecha_evento" class="form-control form-control-sm" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="col-12 col-sm-4">
                        <label class="form-label small fw-semibold mb-1">Toro / Pajuela / Observación</label>
                        <input type="text" name="toro_o_semen" class="form-control form-control-sm" placeholder="Ej: Toro Sultán / Pajuela Brangus">
                    </div>
                    <div class="col-12 col-sm-2">
                        <button type="submit" class="btn btn-danger btn-sm w-100 fw-semibold">Registrar</button>
                    </div>
                </form>
                <?php endif; ?>

                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle mb-0 small">
                        <thead class="table-light">
                            <tr>
                                <th>Fecha Evento</th>
                                <th>Tipo de Evento</th>
                                <th>Toro / Inseminación</th>
                                <th>Fecha Estimada de Parto (FEP)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($reproduccion)): ?>
                                <tr><td colspan="4" class="text-center text-muted py-2">Sin registros reproductivos.</td></tr>
                            <?php else: ?>
                                <?php foreach ($reproduccion as $r): ?>
                                <tr>
                                    <td><?= date('d/m/Y', strtotime($r['fecha_evento'])) ?></td>
                                    <td class="fw-semibold"><?= strtoupper(str_replace('_', ' ', $r['tipo_evento'])) ?></td>
                                    <td><?= htmlspecialchars($r['toro_o_semen'] ?: '—') ?></td>
                                    <td>
                                        <?php if ($r['fecha_estimada_parto']): ?>
                                            <span class="badge bg-primary">FEP: <?= date('d/m/Y', strtotime($r['fecha_estimada_parto'])) ?></span>
                                        <?php else: ?>
                                            —
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>