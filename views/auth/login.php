<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión — FMIS Ganadero Colonias Unidas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #1b4332 0%, #40916c 100%); min-height: 100vh; }
        .login-card { max-width: 440px; width: 100%; border-radius: 14px; border: none; }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center p-3">
    <div class="card login-card shadow-lg p-4">
        <div class="text-center mb-4">
            <div class="bg-success bg-opacity-10 text-success rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width:64px;height:64px;">
                <i class="bi bi-shield-lock-fill fs-2"></i>
            </div>
            <h4 class="fw-bold text-dark mb-1">FMIS Ganadero Modular</h4>
            <p class="text-muted small mb-0">Gestión Productiva Bovina, Porcina y Avícola — Colonias Unidas</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger py-2 small">
                <i class="bi bi-exclamation-triangle-fill me-1"></i> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="index.php?action=login" autocomplete="on">
            <div class="mb-3">
                <label class="form-label fw-semibold small">Correo Electrónico</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input type="email" name="email" class="form-control form-control-lg" placeholder="admin@ganado.py" required>
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label fw-semibold small">Contraseña</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-key"></i></span>
                    <input type="password" name="password" class="form-control form-control-lg" placeholder="••••••••" required>
                </div>
            </div>
            <button type="submit" class="btn btn-success btn-lg w-100 fw-semibold shadow-sm">
                <i class="bi bi-box-arrow-in-right me-1"></i> Ingresar al Sistema
            </button>
        </form>

        <hr class="my-4">
        <div class="bg-light rounded p-2 small text-muted">
            <div class="fw-bold text-secondary mb-1"><i class="bi bi-info-circle me-1"></i> Credenciales de Prueba (RBAC):</div>
            <div>• <strong>Admin:</strong> <code>admin@ganado.py</code> | Clave: <code>password123</code></div>
            <div>• <strong>Operador:</strong> <code>operador@ganado.py</code> | Clave: <code>password123</code></div>
        </div>
    </div>
</body>
</html>