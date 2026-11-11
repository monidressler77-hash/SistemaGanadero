<?php
// views/layouts/header.php
$currentAction = $_GET['action'] ?? 'dashboard';
$userRole      = $_SESSION['user_role'] ?? 'operador';
$userName      = $_SESSION['user_name'] ?? 'Usuario';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'FMIS Ganadero - Colonias Unidas') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', system-ui, -apple-system, sans-serif; }
        .navbar-fmis { background: linear-gradient(135deg, #1b4332 0%, #2d6a4f 100%); }
        .nav-link.active { font-weight: 600; background-color: rgba(255,255,255,0.18); border-radius: 6px; }
        .card { border: none; border-radius: 10px; }
        .btn-touch { min-height: 44px; font-weight: 500; }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">
<nav class="navbar navbar-expand-lg navbar-dark navbar-fmis shadow-sm sticky-top">
    <div class="container-fluid px-3">
        <a class="navbar-brand fw-bold d-flex align-items-center" href="index.php?action=dashboard">
            <i class="bi bi-shield-check me-2 fs-4"></i>
            <span>FMIS Colonias Unidas</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuFMIS">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="menuFMIS">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 gap-1">
                <li class="nav-item">
                    <a class="nav-link px-3 <?= $currentAction === 'dashboard' ? 'active' : '' ?>" href="index.php?action=dashboard">
                        <i class="bi bi-speedometer2 me-1"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3 <?= str_starts_with($currentAction, 'bovino') ? 'active' : '' ?>" href="index.php?action=bovinos">
                        <i class="bi bi-tags-fill me-1"></i> Módulo Bovino (Núcleo)
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3 <?= str_starts_with($currentAction, 'insumo') ? 'active' : '' ?>" href="index.php?action=insumos">
                        <i class="bi bi-box-seam me-1"></i> Insumos y Stock
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle px-3 <?= in_array($currentAction, ['porcinos', 'avicolas']) ? 'active' : '' ?>" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-diagram-3 me-1"></i> Módulos Extensibles
                    </a>
                    <ul class="dropdown-menu shadow">
                        <li><a class="dropdown-item" href="index.php?action=porcinos"><i class="bi bi-grid me-2 text-success"></i>Módulo Porcino (PoC Base)</a></li>
                        <li><a class="dropdown-item" href="index.php?action=avicolas"><i class="bi bi-egg-fried me-2 text-warning"></i>Módulo Avícola (PoC Base)</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3 <?= $currentAction === 'reportes' ? 'active' : '' ?>" href="index.php?action=reportes">
                        <i class="bi bi-file-earmark-pdf me-1"></i> Reportes PDF
                    </a>
                </li>
            </ul>
            <div class="d-flex align-items-center text-white gap-3">
                <div class="small text-end">
                    <div class="fw-semibold"><?= htmlspecialchars($userName) ?></div>
                    <span class="badge <?= $userRole === 'admin' ? 'bg-warning text-dark' : 'bg-info text-dark' ?>">
                        Rol: <?= strtoupper(htmlspecialchars($userRole)) ?>
                    </span>
                </div>
                <a href="index.php?action=logout" class="btn btn-outline-light btn-sm">
                    <i class="bi bi-box-arrow-right me-1"></i> Salir
                </a>
            </div>
        </div>
    </div>
</nav>
<main class="container-fluid py-4 px-3 px-md-4 flex-grow-1">