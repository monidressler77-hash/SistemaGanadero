<?php require_once dirname(__DIR__) . '/layouts/header.php'; ?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <div>
        <h4 class="fw-bold mb-0"><i class="bi bi-tags-fill text-success me-2"></i>Catálogo Maestro del Hato Bovino (RF-02)</h4>
        <p class="text-muted small mb-0">Gestión individual por RP/Carimba, propósito productivo y régimen nutricional</p>
    </div>
    <a href="index.php?action=bovino_create" class="btn btn-success btn-touch shadow-sm">
        <i class="bi bi-plus-lg me-1"></i> + Registrar Nuevo Bovino
    </a>
</div>

<!-- Barra de Filtros Rápidos -->
<div class="card shadow-sm mb-4">
    <div class="card-body py-3">
        <form method="GET" action="index.php" class="row g-2 align-items-end">
            <input type="hidden" name="action" value="bovinos">
            <div class="col-12 col-md-3">
                <label class="form-label small fw-semibold mb-1">Buscar por RP / Nombre / Raza</label>
                <input type="text" name="q" value="<?= htmlspecialchars($filters['q']) ?>" class="form-control" placeholder="Ej: BOV-0101, Brangus...">
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small fw-semibold mb-1">Categoría</label>
                <select name="categoria" class="form-select">
                    <option value="">Todas</option>
                    <?php foreach (['ternero','desmamante','vaquilla','novillo','vaca','toro'] as $c): ?>
                        <option value="<?= $c ?>" <?= $filters['categoria'] === $c ? 'selected' : '' ?>><?= ucfirst($c) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small fw-semibold mb-1">Propósito</label>
                <select name="proposito" class="form-select">
                    <option value="">Todos</option>
                    <option value="carne" <?= $filters['proposito'] === 'carne' ? 'selected' : '' ?>>Carne</option>
                    <option value="leche" <?= $filters['proposito'] === 'leche' ? 'selected' : '' ?>>Leche</option>
                    <option value="doble_proposito" <?= $filters['proposito'] === 'doble_proposito' ? 'selected' : '' ?>>Doble Propósito</option>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small fw-semibold mb-1">Alimentación</label>
                <select name="sistema_alimentacion" class="form-select">
                    <option value="">Todos</option>
                    <option value="pastura_libre" <?= $filters['sistema_alimentacion'] === 'pastura_libre' ? 'selected' : '' ?>>Pastura Libre</option>
                    <option value="semiconfinamiento" <?= $filters['sistema_alimentacion'] === 'semiconfinamiento' ? 'selected' : '' ?>>Semiconfinamiento</option>
                    <option value="confinamiento_encierro" <?= $filters['sistema_alimentacion'] === 'confinamiento_encierro' ? 'selected' : '' ?>>Confinamiento (Encierro)</option>
                </select>
            </div>
            <div class="col-6 col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel me-1"></i> Filtrar</button>
                <a href="index.php?action=bovinos" class="btn btn-outline-secondary">Limpiar</a>
            </div>
        </form>
    </div>
</div>

<!-- Tabla del Hato -->
<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>RP / Carimba</th>
                        <th>Nombre</th>
                        <th>Categoría / Sexo</th>
                        <th>Raza</th>
                        <th>Peso Actual</th>
                        <th>Propósito y Régimen</th>
                        <th>Estado Sanitario</th>
                        <th>Estado General</th>
                        <th class="text-end">Ficha Técnica</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($bovinos)): ?>
                        <tr><td colspan="9" class="text-center py-4 text-muted">No se encontraron bovinos con los criterios seleccionados.</td></tr>
                    <?php else: ?>
                        <?php foreach ($bovinos as $b): ?>
                        <tr>
                            <td class="fw-bold text-success"><?= htmlspecialchars($b['rp']) ?></td>
                            <td><?= htmlspecialchars($b['nombre'] ?: '—') ?></td>
                            <td>
                                <span class="badge bg-dark"><?= ucfirst($b['categoria']) ?></span>
                                <span class="badge <?= $b['sexo'] === 'M' ? 'bg-primary' : 'bg-danger' ?>"><?= $b['sexo'] ?></span>
                            </td>
                            <td><?= htmlspecialchars($b['raza']) ?></td>
                            <td class="fw-bold"><?= $b['peso_actual'] ? number_format((float)$b['peso_actual'], 1) . ' kg' : 'Sin pesar' ?></td>
                            <td>
                                <span class="badge bg-secondary"><?= str_replace('_', ' ', ucfirst($b['proposito'])) ?></span>
                                <small class="text-muted d-block"><?= str_replace('_', ' ', ucfirst($b['sistema_alimentacion'])) ?></small>
                            </td>
                            <td>
                                <?php if ($b['estado_sanitario'] === 'en_carencia'): ?>
                                    <span class="badge bg-danger"><i class="bi bi-lock-fill me-1"></i>En Carencia (<?= date('d/m', strtotime($b['fecha_liberacion_carencia'])) ?>)</span>
                                <?php else: ?>
                                    <span class="badge bg-success-subtle text-success border border-success">Sano / Apto</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge <?= $b['estado_general'] === 'activo' ? 'bg-success' : 'bg-secondary' ?>">
                                    <?= strtoupper($b['estado_general']) ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="index.php?action=bovino_show&id=<?= $b['id'] ?>" class="btn btn-sm btn-primary">
                                    <i class="bi bi-eye me-1"></i> Gestionar Ficha
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>