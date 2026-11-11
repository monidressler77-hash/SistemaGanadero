<?php require_once dirname(__DIR__) . '/layouts/header.php'; ?>

<div class="alert alert-info border-start border-primary border-4 shadow-sm">
    <h6 class="fw-bold mb-1"><i class="bi bi-diagram-3-fill me-2"></i>Módulo de Extensibilidad Arquitectónica: Producción Porcina (RF-11 — PoC)</h6>
    <p class="small mb-0">
        Conforme a la delimitación de alcance del Trabajo Final de Grado, este módulo implementa la navegación desacoplada y la tabla relacional <code>lotes_porcinos</code> para la gestión por piaras, validando la escalabilidad del sistema sin alterar el núcleo bovino.
    </p>
</div>

<?php if (!empty($msg)): ?>
    <div class="alert alert-success py-2"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<div class="row g-4">
    <div class="col-12 col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white fw-bold">Registrar Lote / Piara Porcina</div>
            <div class="card-body">
                <form method="POST" action="index.php?action=porcinos">
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Código de Piara (*)</label>
                        <input type="text" name="codigo_piara" class="form-control" placeholder="Ej: POR-2026-03" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Categoría Porcina (*)</label>
                        <select name="categoria_porcina" class="form-select">
                            <option value="lechones">Lechones en Recría</option>
                            <option value="engorde">Capones en Terminación / Engorde</option>
                            <option value="cerdas_madres">Cerdas Madres Reproductoras</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Cantidad de Cabezas (*)</label>
                        <input type="number" min="1" name="cantidad_cabezas" class="form-control" value="10" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Chiquero / Galpón (*)</label>
                        <input type="text" name="galpon_corral" class="form-control" placeholder="Chiquero N° 2" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Base Alimenticia</label>
                        <input type="text" name="sistema_alimentacion" class="form-control" value="Balanceado Maíz/Soja">
                    </div>
                    <button type="submit" class="btn btn-success w-100 fw-semibold">Guardar Lote Porcino</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3 fw-bold">Piaras Registradas en Persistencia Relacional (Tabla: <code>lotes_porcinos</code>)</div>
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Código Piara</th>
                            <th>Categoría</th>
                            <th>Cabezas</th>
                            <th>Ubicación</th>
                            <th>Alimentación</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($lotes as $l): ?>
                        <tr>
                            <td class="fw-bold text-success"><?= htmlspecialchars($l['codigo_piara']) ?></td>
                            <td><?= strtoupper(str_replace('_', ' ', $l['categoria_porcina'])) ?></td>
                            <td class="fw-bold"><?= (int)$l['cantidad_cabezas'] ?></td>
                            <td><?= htmlspecialchars($l['galpon_corral']) ?></td>
                            <td><?= htmlspecialchars($l['sistema_alimentacion']) ?></td>
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