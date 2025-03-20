<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Security.php';

class UserController {
    private $pdo;
    private $userModel;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->userModel = new User($pdo);
    }

    // Afficher le tableau de bord Admin
    public function dashboard() {
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
            header("Location: /login?error=Accès refusé");
            exit;
        }

        $stmt = $this->pdo->query("SELECT u.id, u.username, u.email, u.status, r.name as role 
                                   FROM users u JOIN roles r ON u.role_id = r.id");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require 'app/views/dashboard.php';
    }

    // Ajouter un utilisateur
    public function create() {
        session_start();
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

            header("Location: /dashboard?" . ($result ? "success=Utilisateur ajouté" : "error=Erreur lors de l'ajout"));
            exit;
        }
    }

    // Mettre à jour un utilisateur
    public function update($id) {
        session_start();
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

            header("Location: /dashboard?" . ($result ? "success=Utilisateur mis à jour" : "error=Erreur lors de la mise à jour"));
            exit;
        }
    }

    // Supprimer un utilisateur
    public function delete($id) {
        session_start();
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = :id");
        $result = $stmt->execute(['id' => $id]);
        header("Location: /dashboard?" . ($result ? "success=Utilisateur supprimé" : "error=Erreur lors de la suppression"));
        exit;
    }
}?>