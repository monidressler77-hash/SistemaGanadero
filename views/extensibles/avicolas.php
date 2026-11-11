<?php require_once dirname(__DIR__) . '/layouts/header.php'; ?>

<div class="alert alert-warning border-start border-warning border-4 shadow-sm">
    <h6 class="fw-bold mb-1"><i class="bi bi-egg-fried me-2"></i>Módulo de Extensibilidad Arquitectónica: Producción Avícola (RF-12 — PoC)</h6>
    <p class="small mb-0">
        Prueba de Concepto estructural conectada a la entidad <code>lotes_avicolas</code> para el control de planteles de postura de huevos caseros y pollos parrilleros en pequeñas fincas de Colonias Unidas.
    </p>
</div>

<?php if (!empty($msg)): ?>
    <div class="alert alert-success py-2"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<div class="row g-4">
    <div class="col-12 col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header bg-warning text-dark fw-bold">Registrar Lote Avícola</div>
            <div class="card-body">
                <form method="POST" action="index.php?action=avicolas">
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Código de Lote (*)</label>
                        <input type="text" name="codigo_lote" class="form-control" placeholder="Ej: AVI-2026-03" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Línea / Propósito (*)</label>
                        <select name="linea_proposito" class="form-select">
                            <option value="postura_huevos">Aves Ponedoras (Postura de Huevos)</option>
                            <option value="parrilleros_carne">Pollos Parrilleros (Carne)</option>
                            <option value="caseros_doble">Producción Familiar de Campo</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Cantidad de Aves (*)</label>
                        <input type="number" min="1" name="cantidad_aves" class="form-control" value="50" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Galpón / Gallinero (*)</label>
                        <input type="text" name="galpon" class="form-control" placeholder="Galpón Familiar N° 1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Recolección Diaria Promedio (Huevos)</label>
                        <input type="number" min="0" name="produccion_diaria_huevos" class="form-control" value="35">
                    </div>
                    <button type="submit" class="btn btn-warning w-100 fw-bold">Guardar Lote Avícola</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3 fw-bold">Planteles Avícolas Registrados (Tabla: <code>lotes_avicolas</code>)</div>
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Código Lote</th>
                            <th>Propósito</th>
                            <th>Aves Activas</th>
                            <th>Galpón</th>
                            <th>Postura Diaria</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($lotes as $l): ?>
                        <tr>
                            <td class="fw-bold text-dark"><?= htmlspecialchars($l['codigo_lote']) ?></td>
                            <td><?= strtoupper(str_replace('_', ' ', $l['linea_proposito'])) ?></td>
                            <td class="fw-bold"><?= (int)$l['cantidad_aves'] ?> aves</td>
                            <td><?= htmlspecialchars($l['galpon']) ?></td>
                            <td><span class="badge bg-warning text-dark"><?= (int)$l['produccion_diaria_huevos'] ?> huevos/día</span></td>
                            <td><span class="badge bg-success"><?= strtoupper($l['estado']) ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>