<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/JWT.php';
require_once __DIR__ . '/../models/Role.php';
require_once __DIR__ . '/../models/Session.php';
require_once __DIR__ . '/../models/Security.php';

class AuthController {
    private $pdo;
    private $userModel;
    private $jwt;
    private $roleModel;
    private $sessionModel;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->userModel = new User($pdo);
        $this->jwt = new JWT();
        $this->roleModel = new Role($pdo);
        $this->sessionModel = new Session($pdo);
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrfResult = Security::verifyCSRFToken($_POST['csrf_token']);
            if (!$csrfResult['success']) {
                header("Location: /register?error=" . urlencode($csrfResult['message']));
                exit;
            }

            $username = Security::sanitizeInput($_POST['username']);
            $email = Security::sanitizeInput($_POST['email'], 'email');
            $password = $_POST['password'];

            $result = $this->userModel->register($username, $email, $password);
            if ($result['success']) {
                header("Location: /verify-2fa?user_id=" . $result['user_id']);
                exit;
            } else {
                header("Location: /register?error=" . urlencode($result['message']));
                exit;
            }
        }
        require __DIR__ . '/../views/register.php';
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrfResult = Security::verifyCSRFToken($_POST['csrf_token']);
            if (!$csrfResult['success']) {
                header("Location: /login?error=" . urlencode($csrfResult['message']));
                exit;
            }

            $username = Security::sanitizeInput($_POST['username']);
            $password = $_POST['password'];

            $result = $this->userModel->login($username, $password);
            if ($result['success']) {
                $token = $this->jwt->generateToken([
                    'user_id' => $result['user']['id'],
                    'role_id' => $result['user']['role_id']
                ]);
                setcookie('token', $token, time() + 3600, '/', '', true, true);

                $_SESSION['user_id'] = $result['user']['id'];
                $_SESSION['role_id'] = $result['user']['role_id'];

                $this->sessionModel->logAction($result['user']['id'], "login");

                // Redirection selon le rôle
                if ($result['user']['role_id'] == 1) {
                    header("Location: /dashboard"); // Admin vers dashboard
                } else {
                    header("Location: /profile"); // Client vers profile
                }
                exit;
            } else {
                header("Location: /login?error=" . urlencode($result['message']));
                exit;
            }
        }
        require __DIR__ . '/../views/login.php';
    }

    public function logout() {
        session_start();
        if (isset($_SESSION['user_id'])) {
            $this->sessionModel->logLogout($_SESSION['user_id']);
            session_destroy();
            setcookie('token', '', time() - 3600, '/', '', true, true);
        }
        header("Location: /login?success=Déconnexion réussie");
        exit;
    }

    public function verify2FA() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrfResult = Security::verifyCSRFToken($_POST['csrf_token']);
            if (!$csrfResult['success']) {
                header("Location: /verify-2fa?user_id=" . $_POST['user_id'] . "&error=" . urlencode($csrfResult['message']));
                exit;
            }

            $userId = (int)$_POST['user_id'];
            $twofaCode = Security::sanitizeInput($_POST['twofa_code']);
            $result = $this->userModel->verify2FACode($userId, $twofaCode);

            if ($result['success']) {
                $activationResult = $this->userModel->activateAccount($userId);
                if ($activationResult['success']) {
                    header("Location: /login?success=Compte activé, connectez-vous.");
                    exit;
                } else {
                    header("Location: /verify-2fa?user_id=$userId&error=" . urlencode($activationResult['message']));
                    exit;
                }
            } else {
                if (isset($result['new_code'])) {
                    echo "Nouveau code 2FA (simulé) : " . $result['new_code'];
                }
                header("Location: /verify-2fa?user_id=$userId&error=" . urlencode($result['message']));
                exit;
            }
        }
        require __DIR__ . '/../views/verification.php';
    }

    public function adminLogin() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrfResult = Security::verifyCSRFToken($_POST['csrf_token']);
            if (!$csrfResult['success']) {
                header("Location: /admin-login?error=" . urlencode($csrfResult['message']));
                exit;
            }

            $username = Security::sanitizeInput($_POST['username']);
            $password = $_POST['password'];

            $result = $this->userModel->login($username, $password);
            if ($result['success'] && $result['user']['role_id'] == 1) {
                $token = $this->jwt->generateToken([
                    'user_id' => $result['user']['id'],
                    'role_id' => $result['user']['role_id']
                ]);
                setcookie('token', $token, time() + 3600, '/', '', true, true);

                $_SESSION['user_id'] = $result['user']['id'];
                $_SESSION['role_id'] = $result['user']['role_id'];

                $this->sessionModel->logAction($result['user']['id'], "login");

                header("Location: /dashboard");
                exit;
            } else {
                header("Location: /admin-login?error=" . urlencode("Accès réservé aux administrateurs."));
                exit;
            }
        }
        require __DIR__ . '/../views/admin_login.php';
    }
}
?>