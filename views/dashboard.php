<?php require_once __DIR__ . '/layouts/header.php'; ?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <h4 class="fw-bold mb-0 text-dark"><i class="bi bi-speedometer2 me-2 text-success"></i>Panel de Control General (Dashboard)</h4>
        <p class="text-muted small mb-0">Indicadores productivos, sanitarios y de inventario en tiempo real (RF-09)</p>
    </div>
    <div class="d-flex gap-2">
        <a href="index.php?action=bovinos" class="btn btn-outline-success btn-touch">
            <i class="bi bi-list-ul me-1"></i> Ver Hato Bovino
        </a>
        <a href="index.php?action=bovino_create" class="btn btn-success btn-touch shadow-sm">
            <i class="bi bi-plus-circle me-1"></i> Registrar Nuevo Bovino
        </a>
    </div>
</div>

<!-- Tarjetas de KPIs -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card shadow-sm p-3 border-start border-success border-4 h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small fw-semibold">HATO BOVINO ACTIVO</div>
                    <div class="fs-2 fw-bold text-success"><?= $totalBovinos ?> <span class="fs-6 fw-normal text-muted">cabezas</span></div>
                </div>
                <i class="bi bi-tags-fill fs-1 text-success opacity-25"></i>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card shadow-sm p-3 border-start border-danger border-4 h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small fw-semibold">EN PERÍODO DE CARENCIA (RN-03)</div>
                    <div class="fs-2 fw-bold text-danger"><?= $enCarencia ?> <span class="fs-6 fw-normal text-muted">bloqueados</span></div>
                </div>
                <i class="bi bi-shield-exclamation fs-1 text-danger opacity-25"></i>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card shadow-sm p-3 border-start border-warning border-4 h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small fw-semibold">STOCK CRÍTICO (RF-08)</div>
                    <div class="fs-2 fw-bold text-warning"><?= $alertasStock ?> <span class="fs-6 fw-normal text-muted">alertas</span></div>
                </div>
                <i class="bi bi-exclamation-triangle-fill fs-1 text-warning opacity-25"></i>
            </div>
        </div>
    </div>
    <?php if (($_SESSION['user_role'] ?? '') === 'admin'): ?>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card shadow-sm p-3 border-start border-primary border-4 h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small fw-semibold">VALOR DEL INVENTARIO (RN-06)</div>
                    <div class="fs-4 fw-bold text-primary mt-1">Gs. <?= number_format($valorDeposito, 0, ',', '.') ?></div>
                </div>
                <i class="bi bi-cash-coin fs-1 text-primary opacity-25"></i>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<div class="row g-4">
    <!-- Columna Izquierda: Alertas Sanitarias y Stock -->
    <div class="col-12 col-lg-6">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-danger"><i class="bi bi-capsule me-2"></i>Animales en Retiro Farmacológico — Bloqueo de Venta (RN-03)</h6>
                <span class="badge bg-danger"><?= count($animalesCarencia) ?> activos</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 small">
                        <thead class="table-light">
                            <tr>
                                <th>RP / Carimba</th>
                                <th>Nombre / Categoría</th>
                                <th>Liberación Sanitaria</th>
                                <th class="text-end">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($animalesCarencia)): ?>
                                <tr><td colspan="4" class="text-center py-3 text-muted">No hay animales en período de retiro farmacológico.</td></tr>
                            <?php else: ?>
                                <?php foreach ($animalesCarencia as $ac): ?>
                                <tr>
                                    <td class="fw-bold text-danger"><?= htmlspecialchars($ac['rp']) ?></td>
                                    <td><?= htmlspecialchars($ac['nombre'] ?: '-') ?> <span class="badge bg-secondary"><?= ucfirst($ac['categoria']) ?></span></td>
                                    <td><span class="badge bg-danger-subtle text-danger border border-danger">Hasta: <?= date('d/m/Y', strtotime($ac['fecha_liberacion_carencia'])) ?></span></td>
                                    <td class="text-end">
                                        <a href="index.php?action=bovino_show&id=<?= $ac['id'] ?>" class="btn btn-sm btn-outline-primary">Ficha</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-warning-emphasis"><i class="bi bi-box-seam me-2"></i>Alertas de Stock Mínimo en Depósito (RF-08 / RN-08)</h6>
                <a href="index.php?action=insumos" class="btn btn-sm btn-outline-secondary">Ir a Depósito</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 small">
                        <thead class="table-light">
                            <tr>
                                <th>Insumo</th>
                                <th>Tipo</th>
                                <th>Saldo Actual</th>
                                <th>Mínimo</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($insumosCriticos)): ?>
                                <tr><td colspan="5" class="text-center py-3 text-muted">Todos los insumos superan el umbral mínimo.</td></tr>
                            <?php else: ?>
                                <?php foreach ($insumosCriticos as $ic): ?>
                                <tr>
                                    <td class="fw-semibold"><?= htmlspecialchars($ic['nombre_insumo']) ?></td>
                                    <td><?= ucfirst($ic['tipo_insumo']) ?></td>
                                    <td class="fw-bold text-danger"><?= number_format((float)$ic['stock_actual'], 2) ?> <?= htmlspecialchars($ic['unidad_medida']) ?></td>
                                    <td><?= number_format((float)$ic['stock_minimo'], 2) ?></td>
                                    <td><span class="badge bg-warning text-dark">Reponer Stock</span></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Columna Derecha: Últimos Pesajes (GMD) y Distribución del Hato -->
    <div class="col-12 col-lg-6">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold text-success"><i class="bi bi-graph-up-arrow me-2"></i>Últimos Controles Ponderales y GMD (RF-03 / RN-04)</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 small">
                        <thead class="table-light">
                            <tr>
                                <th>Fecha</th>
                                <th>Animal (RP)</th>
                                <th>Peso</th>
                                <th>GMD (kg/día)</th>
                                <th>Régimen</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ultimosPesajes as $up): ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($up['fecha_pesaje'])) ?></td>
                                <td>
                                    <a href="index.php?action=bovino_show&id=<?= $up['bovino_id'] ?>" class="fw-bold text-decoration-none">
                                        <?= htmlspecialchars($up['rp']) ?>
                                    </a>
                                    <small class="text-muted">(<?= htmlspecialchars($up['nombre'] ?: 'S/N') ?>)</small>
                                </td>
                                <td class="fw-bold"><?= number_format((float)$up['peso_kg'], 1) ?> kg</td>
                                <td>
                                    <?php if ($up['gmd_calculada'] !== null): ?>
                                        <span class="badge <?= (float)$up['gmd_calculada'] >= 0.7 ? 'bg-success' : 'bg-info text-dark' ?>">
                                            +<?= number_format((float)$up['gmd_calculada'], 3) ?> kg/d
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">Peso Inicial</span>
                                    <?php endif; ?>
                                </td>
                                <td><small><?= str_replace('_', ' ', ucfirst($up['sistema_alimentacion'])) ?></small></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-pie-chart-fill me-2 text-primary"></i>Composición del Hato Activo por Categoría</h6>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    <?php foreach ($porCategoria as $cat): ?>
                    <div class="col-6 col-sm-4">
                        <div class="p-3 bg-light rounded border text-center">
                            <div class="text-uppercase small text-muted fw-semibold"><?= htmlspecialchars($cat['categoria']) ?>s</div>
                            <div class="fs-4 fw-bold text-dark"><?= (int)$cat['total'] ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>