<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/JWT.php';
require_once __DIR__ . '/../models/Security.php';

class AuthController {
    private $pdo;
    private $userModel;
    private $jwt;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->userModel = new User($pdo);
        $this->jwt = new JWT();
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
            header("Location: /register?" . ($result['success'] ? "success=" : "error=") . urlencode($result['message']));
            exit;
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

            if (isset($_POST['twofa_code'])) {
                // Étape 2FA
                $userId = (int)$_POST['user_id'];
                $twofaCode = Security::sanitizeInput($_POST['twofa_code']);
                $result = $this->userModel->verify2FACode($userId, $twofaCode);

                if ($result['success']) {
                    $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = :id");
                    $stmt->execute(['id' => $userId]);
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);

                    $token = $this->jwt->generateToken([
                        'user_id' => $user['id'],
                        'role_id' => $user['role_id']
                    ]);
                    setcookie('token', $token, time() + 3600, '/', '', true, true);

                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['role_id'] = $user['role_id'];
                    header("Location: /dashboard");
                    exit;
                } else {
                    header("Location: /login?step=2fa&user_id=$userId&error=" . urlencode($result['message']));
                    exit;
                }
            } else {
                // Étape identifiants
                $username = Security::sanitizeInput($_POST['username']);
                $password = $_POST['password'];

                $result = $this->userModel->login($username, $password);
                if ($result['success']) {
                    $twofaResult = $this->userModel->generate2FACode($result['user']['id']);
                    if ($twofaResult['success']) {
                        echo "Code 2FA (simulé) : " . $twofaResult['code']; // À remplacer par un email réel
                        header("Location: /login?step=2fa&user_id=" . $result['user']['id']);
                        exit;
                    } else {
                        header("Location: /login?error=" . urlencode($twofaResult['message']));
                        exit;
                    }
                } else {
                    header("Location: /login?error=" . urlencode($result['message']));
                    exit;
                }
            }
        }
        require __DIR__ . '/../views/login.php';
    }
}?>


