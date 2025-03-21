<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Role.php';
require_once __DIR__ . '/../models/Session.php';
require_once __DIR__ . '/../models/Security.php';

class UserController {
    private $pdo;
    private $userModel;
    private $roleModel;
    private $sessionModel;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->userModel = new User($pdo);
        $this->roleModel = new Role($pdo);
        $this->sessionModel = new Session($pdo);
    }

    private function restrictToLoggedIn() {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['role_id'])) {
            header("Location: /login?error=Connexion requise");
            exit;
        }
        return true;
    }

    private function restrictToRole($requiredRoleId = 1) {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['role_id'])) {
            header("Location: /login?error=Connexion requise");
            exit;
        }
        if ($_SESSION['role_id'] != $requiredRoleId) {
            header("Location: /dashboard?error=Accès refusé : rôle insuffisant");
            exit;
        }
        return true;
    }

    public function dashboard() {
        $this->restrictToRole(1); // Réservé aux admins

        // Liste des utilisateurs
        $stmt = $this->pdo->query("SELECT u.id, u.username, u.email, u.status, r.name as role, u.role_id 
                                   FROM users u JOIN roles r ON u.role_id = r.id");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $roles = $this->roleModel->getAllRoles();

        // Toutes les sessions
        $sessionsStmt = $this->pdo->query("SELECT s.*, u.username 
                                          FROM sessions s 
                                          JOIN users u ON s.user_id = u.id 
                                          ORDER BY s.login_time DESC");
        $allSessions = $sessionsStmt->fetchAll(PDO::FETCH_ASSOC);

        // Statistiques
        $stats = [
            'total_users' => $this->pdo->query("SELECT COUNT(*) FROM users")->fetchColumn(),
            'active_users' => $this->pdo->query("SELECT COUNT(*) FROM users WHERE status = 'active'")->fetchColumn(),
            'inactive_users' => $this->pdo->query("SELECT COUNT(*) FROM users WHERE status = 'inactive'")->fetchColumn(),
            'total_actions' => $this->pdo->query("SELECT COUNT(*) FROM sessions")->fetchColumn()
        ];

        require __DIR__ . '/../views/dashboard.php';
    }

    // Les autres méthodes (create, update, delete, profile) restent inchangées
    public function create() {
        $this->restrictToRole(1);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrfResult = Security::verifyCSRFToken($_POST['csrf_token']);
            if (!$csrfResult['success']) {
                header("Location: /dashboard?error=" . urlencode($csrfResult['message']));
                exit;
            }

            $username = Security::sanitizeInput($_POST['username']);
            $email = Security::sanitizeInput($_POST['email'], 'email');
            $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
            $role_id = (int)$_POST['role_id'];
            $status = $_POST['status'];

            if (!$this->roleModel->roleExists($role_id)) {
                header("Location: /dashboard?error=Rôle invalide");
                exit;
            }

            $stmt = $this->pdo->prepare(
                "INSERT INTO users (username, email, password, role_id, status) 
                 VALUES (:username, :email, :password, :role_id, :status)"
            );
            $result = $stmt->execute([
                'username' => $username,
                'email' => $email,
                'password' => $password,
                'role_id' => $role_id,
                'status' => $status
            ]);

            if ($result) {
                $newUserId = $this->pdo->lastInsertId();
                $this->sessionModel->logAction($_SESSION['user_id'], "create_user_$newUserId");
            }

            header("Location: /dashboard?" . ($result ? "success=Utilisateur ajouté" : "error=Erreur lors de l'ajout"));
            exit;
        }
    }

    public function update($id) {
        $this->restrictToRole(1);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrfResult = Security::verifyCSRFToken($_POST['csrf_token']);
            if (!$csrfResult['success']) {
                header("Location: /dashboard?error=" . urlencode($csrfResult['message']));
                exit;
            }

            $username = Security::sanitizeInput($_POST['username']);
            $email = Security::sanitizeInput($_POST['email'], 'email');
            $role_id = (int)$_POST['role_id'];
            $status = $_POST['status'];

            if (!$this->roleModel->roleExists($role_id)) {
                header("Location: /dashboard?error=Rôle invalide");
                exit;
            }

            $stmt = $this->pdo->prepare(
                "UPDATE users SET username = :username, email = :email, role_id = :role_id, status = :status 
                 WHERE id = :id"
            );
            $result = $stmt->execute([
                'username' => $username,
                'email' => $email,
                'role_id' => $role_id,
                'status' => $status,
                'id' => $id
            ]);

            if ($result) {
                $this->sessionModel->logAction($_SESSION['user_id'], "update_user_$id");
            }

            header("Location: /dashboard?" . ($result ? "success=Utilisateur mis à jour" : "error=Erreur lors de la mise à jour"));
            exit;
        }
    }

    public function delete($id) {
        $this->restrictToRole(1);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrfResult = Security::verifyCSRFToken($_POST['csrf_token']);
            if (!$csrfResult['success']) {
                header("Location: /dashboard?error=" . urlencode($csrfResult['message']));
                exit;
            }

            $stmt = $this->pdo->prepare("DELETE FROM sessions WHERE user_id = :id");
            $stmt->execute(['id' => $id]);

            $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = :id");
            $result = $stmt->execute(['id' => $id]);

            if ($result) {
                $this->sessionModel->logAction($_SESSION['user_id'], "delete_user_$id");
            }

            header("Location: /dashboard?" . ($result ? "success=Utilisateur supprimé" : "error=Erreur lors de la suppression"));
            exit;
        }
    }

    public function profile() {
        $this->restrictToLoggedIn();
        $userId = $_SESSION['user_id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrfResult = Security::verifyCSRFToken($_POST['csrf_token']);
            if (!$csrfResult['success']) {
                header("Location: /profile?error=" . urlencode($csrfResult['message']));
                exit;
            }

            if (isset($_POST['delete_account'])) {
                $stmt = $this->pdo->prepare("DELETE FROM sessions WHERE user_id = :id");
                $stmt->execute(['id' => $userId]);

                $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = :id");
                $result = $stmt->execute(['id' => $userId]);

                if ($result) {
                    $this->sessionModel->logAction($userId, "delete_account");
                    session_destroy();
                    setcookie('token', '', time() - 3600, '/', '', true, true);
                    header("Location: /login?success=Compte supprimé");
                    exit;
                } else {
                    header("Location: /profile?error=Erreur lors de la suppression du compte");
                    exit;
                }
            } else {
                $username = Security::sanitizeInput($_POST['username']);
                $email = Security::sanitizeInput($_POST['email'], 'email');
                $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_BCRYPT) : null;

                $query = "UPDATE users SET username = :username, email = :email";
                $params = ['username' => $username, 'email' => $email, 'id' => $userId];
                if ($password) {
                    $query .= ", password = :password";
                    $params['password'] = $password;
                }
                $query .= " WHERE id = :id";

                $stmt = $this->pdo->prepare($query);
                $result = $stmt->execute($params);

                if ($result) {
                    $this->sessionModel->logAction($userId, "update_profile");
                }

                header("Location: /profile?" . ($result ? "success=Profil mis à jour" : "error=Erreur lors de la mise à jour"));
                exit;
            }
        }

        $stmt = $this->pdo->prepare("SELECT username, email FROM users WHERE id = :id");
        $stmt->execute(['id' => $userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        $sessions = $this->sessionModel->getSessionHistory($userId);
        require __DIR__ . '/../views/profile.php';
    }
}
?>