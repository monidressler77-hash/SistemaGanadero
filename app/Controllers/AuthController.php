<?php
// app/Controllers/AuthController.php
declare(strict_types=1);

class AuthController {
    private PDO $pdo;
    private const SESSION_TIMEOUT = 900; // 15 minutos = 900 segundos (RNF-03)

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public static function checkAuth(): void {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > self::SESSION_TIMEOUT)) {
            session_unset();
            session_destroy();
            header('Location: index.php?action=login&timeout=1');
            exit;
        }
        $_SESSION['last_activity'] = time();
    }

    public function login(): void {
        if (isset($_SESSION['user_id'])) {
            header('Location: index.php?action=dashboard');
            exit;
        }

        $error = '';
        if (isset($_GET['timeout'])) {
            $error = "Su sesión ha expirado tras 15 minutos de inactividad (Norma RNF-03).";
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email    = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');

            if ($email === '' || $password === '') {
                $error = "Complete su correo electrónico y contraseña.";
            } else {
                $stmt = $this->pdo->prepare("SELECT id, nombre, email, password, rol FROM users WHERE email = :email LIMIT 1");
                $stmt->execute(['email' => $email]);
                $user = $stmt->fetch();

                if ($user && password_verify($password, $user['password'])) {
                    session_regenerate_id(true);
                    $_SESSION['user_id']       = (int) $user['id'];
                    $_SESSION['user_name']     = $user['nombre'];
                    $_SESSION['user_email']    = $user['email'];
                    $_SESSION['user_role']     = $user['rol'];
                    $_SESSION['last_activity'] = time();

                    header('Location: index.php?action=dashboard');
                    exit;
                } else {
                    $error = "Credenciales no válidas. Verifique su correo y contraseña (TC-02).";
                }
            }
        }

        require_once dirname(__DIR__, 2) . '/views/auth/login.php';
    }

    public function logout(): void {
        $_SESSION = [];
        session_destroy();
        header('Location: index.php?action=login');
        exit;
    }
}