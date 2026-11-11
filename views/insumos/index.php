<?php require_once dirname(__DIR__) . '/layouts/header.php'; ?>
<?php $isAdmin = (($_SESSION['user_role'] ?? '') === 'admin'); ?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <div>
        <h4 class="fw-bold mb-0"><i class="bi bi-box-seam text-success me-2"></i>Almacén de Insumos, Alimentación y Costos (RF-06 / RF-08)</h4>
        <p class="text-muted small mb-0">Control de existencias de fármacos, vacunas, alimentos balanceados y sales minerales</p>
    </div>
    <?php if ($isAdmin): ?>
    <div class="d-flex gap-3">
        <div class="bg-white px-3 py-2 rounded shadow-sm border-start border-primary border-4">
            <span class="small text-muted d-block">VALOR EN DEPÓSITO (RN-06)</span>
            <strong class="text-primary">Gs. <?= number_format($valorTotalInventario, 0, ',', '.') ?></strong>
        </div>
        <div class="bg-white px-3 py-2 rounded shadow-sm border-start border-danger border-4">
            <span class="small text-muted d-block">COSTO SANITARIO APLICADO</span>
            <strong class="text-danger">Gs. <?= number_format($gastoSanitarioAcum, 0, ',', '.') ?></strong>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger shadow-sm"><i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($error) ?></div>
<?php endif; ?>
<?php if (!empty($success)): ?>
    <div class="alert alert-success shadow-sm"><i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<div class="row g-4">
    <!-- Columna Izquierda: Formularios Operativos -->
    <div class="col-12 col-lg-4">
        <!-- 1. Entrada por Compra o Salida por Alimentación/Ración -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-success text-white fw-bold">
                <i class="bi bi-arrow-left-right me-1"></i> Registrar Compra o Suministro de Ración
            </div>
            <div class="card-body">
                <form method="POST" action="index.php?action=insumos">
                    <input type="hidden" name="operacion" value="movimiento_stock">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Seleccionar Insumo (*)</label>
                        <select name="insumo_id" class="form-select" required>
                            <?php foreach ($insumos as $ins): ?>
                                <option value="<?= $ins['id'] ?>">
                                    <?= htmlspecialchars($ins['nombre_insumo']) ?> (Actual: <?= $ins['stock_actual'] ?> <?= $ins['unidad_medida'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Tipo de Movimiento (*)</label>
                        <select name="tipo_movimiento" class="form-select" required>
                            <option value="entrada">➕ Entrada / Reposición por Compra</option>
                            <option value="salida">➖ Salida / Suministro de Alimento en Corral</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Cantidad (*)</label>
                        <input type="number" step="0.01" min="0.1" name="cantidad" class="form-control" placeholder="Ej: 50.00" required>
                    </div>
                    <button type="submit" class="btn btn-success w-100 fw-semibold">
                        <i class="bi bi-check2-circle me-1"></i> Actualizar Existencias
                    </button>
                </form>
            </div>
        </div>

        <!-- 2. Alta de Nuevo Producto en Catálogo (Solo Administrador - RN-06) -->
        <?php if ($isAdmin): ?>
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white fw-bold">
                <i class="bi bi-plus-circle me-1"></i> Nuevo Insumo en Catálogo (Solo Admin)
            </div>
            <div class="card-body">
                <form method="POST" action="index.php?action=insumos">
                    <input type="hidden" name="operacion" value="nuevo_insumo">
                    <div class="mb-2">
                        <label class="form-label small fw-semibold mb-1">Nombre Comercial / Principio Activo (*)</label>
                        <input type="text" name="nombre_insumo" class="form-control form-control-sm" placeholder="Ej: Aftosa Oleosa / Expeller de Soja" required>
                    </div>
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="form-label small fw-semibold mb-1">Rubro (*)</label>
                            <select name="tipo_insumo" class="form-select form-select-sm">
                                <option value="vacuna">Vacuna</option>
                                <option value="medicamento">Medicamento</option>
                                <option value="antiparasitario">Antiparasitario</option>
                                <option value="balanceado">Balanceado</option>
                                <option value="sal_mineral">Sal Mineral</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold mb-1">Unidad (*)</label>
                            <input type="text" name="unidad_medida" class="form-control form-control-sm" placeholder="Dosis, Kg, ml" required>
                        </div>
                    </div>
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="form-label small fw-semibold mb-1">Stock Inicial</label>
                            <input type="number" step="0.01" name="stock_actual" class="form-control form-control-sm" value="20" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold mb-1">Stock Mínimo (Alerta)</label>
                            <input type="number" step="0.01" name="stock_minimo" class="form-control form-control-sm" value="5" required>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold mb-1">Días Retiro (RN-03)</label>
                            <input type="number" min="0" name="dias_retiro" class="form-control form-control-sm" value="0" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold mb-1">Costo Unit. (Gs.)</label>
                            <input type="number" step="100" name="costo_unitario" class="form-control form-control-sm" value="5000" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-dark btn-sm w-100 fw-semibold">Guardar en Almacén</button>
                </form>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Columna Derecha: Tabla Kardex de Existencias -->
    <div class="col-12 col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold">Existencias Físicas y Umbrales de Seguridad en Depósito</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Insumo / Producto</th>
                                <th>Categoría</th>
                                <th>Stock Actual</th>
                                <th>Umbral Mín.</th>
                                <th>Carencia</th>
                                <?php if ($isAdmin): ?>
                                    <th>Costo Unit.</th>
                                    <th>Subtotal (Gs.)</th>
                                <?php endif; ?>
                                <th>Estado (RF-08)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($insumos as $i): ?>
                            <?php $critico = ((float)$i['stock_actual'] <= (float)$i['stock_minimo']); ?>
                            <tr class="<?= $critico ? 'table-warning' : '' ?>">
                                <td class="fw-bold"><?= htmlspecialchars($i['nombre_insumo']) ?></td>
                                <td><span class="badge bg-secondary"><?= strtoupper(str_replace('_', ' ', $i['tipo_insumo'])) ?></span></td>
                                <td class="fw-bold <?= $critico ? 'text-danger' : 'text-success' ?>">
                                    <?= number_format((float)$i['stock_actual'], 2) ?> <?= htmlspecialchars($i['unidad_medida']) ?>
                                </td>
                                <td><?= number_format((float)$i['stock_minimo'], 2) ?></td>
                                <td><?= (int)$i['dias_retiro'] > 0 ? $i['dias_retiro'] . ' días' : '0 días' ?></td>
                                <?php if ($isAdmin): ?>
                                    <td>Gs. <?= number_format((float)$i['costo_unitario'], 0, ',', '.') ?></td>
                                    <td class="fw-semibold">Gs. <?= number_format((float)$i['stock_actual'] * (float)$i['costo_unitario'], 0, ',', '.') ?></td>
                                <?php endif; ?>
                                <td>
                                    <?php if ($critico): ?>
                                        <span class="badge bg-danger"><i class="bi bi-exclamation-circle me-1"></i>Stock Crítico</span>
                                    <?php else: ?>
                                        <span class="badge bg-success">Óptimo</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>