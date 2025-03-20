<?php
class User {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function register($username, $email, $password) {
        $stmt = $this->pdo->prepare("SELECT id FROM users WHERE username = :username OR email = :email");
        $stmt->execute(['username' => $username, 'email' => $email]);
        if ($stmt->fetch()) {
            return ['success' => false, 'message' => "Le nom d'utilisateur ou l'email est déjà pris."];
        }

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->pdo->prepare(
            "INSERT INTO users (username, email, password, role_id) 
             VALUES (:username, :email, :password, :role_id)"
        );
        $result = $stmt->execute([
            'username' => $username,
            'email' => $email,
            'password' => $hashedPassword,
            'role_id' => 2
        ]);

        if ($result) {
            return ['success' => true, 'message' => "Inscription réussie !"];
        } else {
            return ['success' => false, 'message' => "Erreur lors de l'inscription."];
        }
    }

    public function login($username, $password) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            if ($user['status'] === 'inactive') {
                return ['success' => false, 'message' => "Votre compte est désactivé."];
            }
            return ['success' => true, 'user' => $user];
        } else {
            return ['success' => false, 'message' => "Nom d'utilisateur ou mot de passe incorrect."];
        }
    }

    // Nouvelle fonction : générer un code 2FA temporaire
    public function generate2FACode($userId) {
        // Générer un code aléatoire de 6 chiffres
        $code = rand(100000, 999999);
        $expires = time() + 300; // Expire dans 5 minutes

        // Stocker le code dans twofa_secret avec l'expiration
        $stmt = $this->pdo->prepare(
            "UPDATE users SET twofa_secret = :code WHERE id = :id"
        );
        $result = $stmt->execute(['code' => "$code|$expires", 'id' => $userId]);

        if ($result) {
            return ['success' => true, 'code' => $code];
        } else {
            return ['success' => false, 'message' => "Erreur lors de la génération du code 2FA."];
        }
    }

    // Nouvelle fonction : vérifier le code 2FA
    public function verify2FACode($userId, $code) {
        $stmt = $this->pdo->prepare("SELECT twofa_secret FROM users WHERE id = :id");
        $stmt->execute(['id' => $userId]);
        $secret = $stmt->fetchColumn();

        if (!$secret) {
            return ['success' => false, 'message' => "Aucun code 2FA généré."];
        }

        // Séparer le code et l'expiration (stockés comme "code|expiration")
        list($storedCode, $expires) = explode('|', $secret);

        if (time() > $expires) {
            return ['success' => false, 'message' => "Le code 2FA a expiré."];
        }

        if ($storedCode === $code) {
            // Effacer le code après vérification réussie
            $this->pdo->prepare("UPDATE users SET twofa_secret = NULL WHERE id = :id")
                      ->execute(['id' => $userId]);
            return ['success' => true, 'message' => "Code 2FA valide."];
        } else {
            return ['success' => false, 'message' => "Code 2FA incorrect."];
        }
    }
}?>