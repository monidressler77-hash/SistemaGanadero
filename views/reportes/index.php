<?php require_once dirname(__DIR__) . '/layouts/header.php'; ?>

<style>
@media print {
    .navbar, footer, .no-print { display: none !important; }
    body { background: #fff !important; }
    .card { box-shadow: none !important; border: 1px solid #ccc !important; }
}
</style>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 no-print gap-2">
    <div>
        <h4 class="fw-bold mb-0"><i class="bi bi-file-earmark-pdf-fill text-danger me-2"></i>Centro de Emisión de Reportes Zootécnicos (RF-10)</h4>
        <p class="text-muted small mb-0">Planillas oficiales listas para impresión o exportación directa a PDF para trabajo en manga</p>
    </div>
    <div class="d-flex gap-2">
        <?php if ($fichaIndividual): ?>
            <a href="index.php?action=reportes" class="btn btn-outline-secondary">Ver Reporte General del Hato</a>
        <?php endif; ?>
        <button onclick="window.print();" class="btn btn-danger fw-bold shadow-sm">
            <i class="bi bi-printer-fill me-1"></i> Descargar / Imprimir en PDF
        </button>
    </div>
</div>

<!-- Membrete del Reporte -->
<div class="card shadow-sm p-4 mb-4">
    <div class="border-bottom pb-3 mb-3 d-flex justify-content-between align-items-center">
        <div>
            <h5 class="fw-bold text-success mb-0">SISTEMA MODULAR DE GESTIÓN PRODUCTIVA PECUARIA (FMIS)</h5>
            <div class="small text-muted">Establecimiento Ganadero — Colonias Unidas, Departamento de Itapúa, Paraguay</div>
        </div>
        <div class="text-end small">
            <div><strong>Fecha de Emisión:</strong> <?= date('d/m/Y H:i') ?></div>
            <div><strong>Emitido por:</strong> <?= htmlspecialchars($_SESSION['user_name'] ?? 'Administrador') ?></div>
        </div>
    </div>

    <?php if ($fichaIndividual): ?>
        <!-- REPORTE TIPO A: FICHA CLÍNICA Y PONDERAL INDIVIDUAL -->
        <h6 class="fw-bold text-uppercase bg-light p-2 border">Ficha Técnica e Historial Clínico Individual — RP: <?= htmlspecialchars($fichaIndividual['rp']) ?></h6>
        <div class="row g-2 mb-3 small">
            <div class="col-4"><strong>RP / Carimba:</strong> <?= htmlspecialchars($fichaIndividual['rp']) ?></div>
            <div class="col-4"><strong>Nombre:</strong> <?= htmlspecialchars($fichaIndividual['nombre'] ?: 'S/N') ?></div>
            <div class="col-4"><strong>Categoría / Raza:</strong> <?= ucfirst($fichaIndividual['categoria']) ?> — <?= htmlspecialchars($fichaIndividual['raza']) ?></div>
            <div class="col-4"><strong>Propósito:</strong> <?= ucfirst(str_replace('_', ' ', $fichaIndividual['proposito'])) ?></div>
            <div class="col-4"><strong>Alimentación:</strong> <?= ucfirst(str_replace('_', ' ', $fichaIndividual['sistema_alimentacion'])) ?></div>
            <div class="col-4"><strong>Estado Sanitario:</strong> <?= strtoupper(str_replace('_', ' ', $fichaIndividual['estado_sanitario'])) ?></div>
        </div>

        <h6 class="fw-bold small mt-3">1. Historial de Pesajes y Ganancia Media Diaria (GMD)</h6>
        <table class="table table-sm table-bordered small mb-4">
            <thead class="table-light">
                <tr><th>Fecha</th><th>Peso (kg)</th><th>Días</th><th>GMD (kg/día)</th><th>Observaciones</th></tr>
            </thead>
            <tbody>
                <?php foreach ($pesajesFicha as $pf): ?>
                <tr>
                    <td><?= date('d/m/Y', strtotime($pf['fecha_pesaje'])) ?></td>
                    <td><?= number_format((float)$pf['peso_kg'], 2) ?> kg</td>
                    <td><?= (int)$pf['dias_transcurridos'] ?></td>
                    <td><?= $pf['gmd_calculada'] !== null ? '+' . number_format((float)$pf['gmd_calculada'], 3) . ' kg/d' : 'Peso Inicial' ?></td>
                    <td><?= htmlspecialchars($pf['observaciones'] ?: '—') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h6 class="fw-bold small">2. Historial de Tratamientos Sanitarios y Períodos de Carencia</h6>
        <table class="table table-sm table-bordered small mb-0">
            <thead class="table-light">
                <tr><th>Fecha</th><th>Fármaco / Vacuna</th><th>Evento</th><th>Dosis</th><th>Fin de Retiro (RN-03)</th><th>Diagnóstico</th></tr>
            </thead>
            <tbody>
                <?php foreach ($sanidadFicha as $sf): ?>
                <tr>
                    <td><?= date('d/m/Y', strtotime($sf['fecha_aplicacion'])) ?></td>
                    <td><?= htmlspecialchars($sf['nombre_insumo']) ?></td>
                    <td><?= ucfirst(str_replace('_', ' ', $sf['tipo_evento'])) ?></td>
                    <td><?= number_format((float)$sf['dosis_aplicada'], 2) ?> <?= htmlspecialchars($sf['unidad_medida']) ?></td>
                    <td><?= (int)$sf['dias_carencia'] > 0 ? date('d/m/Y', strtotime($sf['fecha_fin_retiro'])) : 'Sin retiro' ?></td>
                    <td><?= htmlspecialchars($sf['diagnostico_observacion'] ?: '—') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    <?php else: ?>
        <!-- REPORTE TIPO B: INVENTARIO CONSOLIDADO DEL HATO BOVINO E INSUMOS -->
        <h6 class="fw-bold text-uppercase bg-light p-2 border">1. Planilla Consolidada de Existencias del Hato Bovino</h6>
        <table class="table table-sm table-bordered small mb-4">
            <thead class="table-light">
                <tr>
                    <th>RP / Carimba</th>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th>Raza</th>
                    <th>Sexo</th>
                    <th>Peso Actual</th>
                    <th>Propósito / Régimen</th>
                    <th>Aptitud Sanitaria</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bovinos as $b): ?>
                <tr>
                    <td class="fw-bold"><?= htmlspecialchars($b['rp']) ?></td>
                    <td><?= htmlspecialchars($b['nombre'] ?: '—') ?></td>
                    <td><?= ucfirst($b['categoria']) ?></td>
                    <td><?= htmlspecialchars($b['raza']) ?></td>
                    <td><?= $b['sexo'] ?></td>
                    <td><?= $b['peso_actual'] ? number_format((float)$b['peso_actual'], 1) . ' kg' : 'S/P' ?></td>
                    <td><?= ucfirst($b['proposito']) ?> / <?= str_replace('_', ' ', $b['sistema_alimentacion']) ?></td>
                    <td><?= $b['estado_sanitario'] === 'en_carencia' ? 'EN RETIRO (Hasta ' . date('d/m/Y', strtotime($b['fecha_liberacion_carencia'])) . ')' : 'APTO / SANO' ?></td>
                    <td><?= strtoupper($b['estado_general']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h6 class="fw-bold text-uppercase bg-light p-2 border">2. Balance de Existencias en Depósito de Insumos</h6>
        <table class="table table-sm table-bordered small mb-0">
            <thead class="table-light">
                <tr>
                    <th>Insumo</th>
                    <th>Tipo</th>
                    <th>Stock Físico Actual</th>
                    <th>Stock Mínimo</th>
                    <th>Días Retiro</th>
                    <th>Estado de Alerta</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($insumos as $i): ?>
                <tr>
                    <td><?= htmlspecialchars($i['nombre_insumo']) ?></td>
                    <td><?= ucfirst($i['tipo_insumo']) ?></td>
                    <td class="fw-bold"><?= number_format((float)$i['stock_actual'], 2) ?> <?= htmlspecialchars($i['unidad_medida']) ?></td>
                    <td><?= number_format((float)$i['stock_minimo'], 2) ?></td>
                    <td><?= (int)$i['dias_retiro'] ?> días</td>
                    <td><?= ((float)$i['stock_actual'] <= (float)$i['stock_minimo']) ? 'ALERTA: REPONER' : 'NORMAL' ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>