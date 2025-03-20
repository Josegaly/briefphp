<?php
class Security {
    // Générer un token CSRF
    public static function generateCSRFToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Code aléatoire sécurisé
        }
        return $_SESSION['csrf_token'];
    }

    // Vérifier un token CSRF
    public static function verifyCSRFToken($token) {
        if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
            return ['success' => false, 'message' => "Erreur de sécurité (CSRF)."];
        }
        // Effacer le token après usage pour éviter la réutilisation
        unset($_SESSION['csrf_token']);
        return ['success' => true];
    }

    // Valider et nettoyer une entrée
    public static function sanitizeInput($input, $type = 'string') {
        switch ($type) {
            case 'email':
                return filter_var($input, FILTER_SANITIZE_EMAIL);
            case 'string':
                return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
            default:
                return $input;
        }
    }
}?>