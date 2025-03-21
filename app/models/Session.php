<?php
class Session {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Enregistre une action dans la table sessions.
     * @param int $userId ID de l'utilisateur
     * @param string $action Type d'action (login, logout, update_profile, etc.)
     * @return bool True si succès, false sinon
     */
    public function logAction($userId, $action) {
        try {
            $stmt = $this->pdo->prepare(
                "INSERT INTO sessions (user_id, action, login_time) 
                 VALUES (:user_id, :action, NOW())"
            );
            return $stmt->execute([
                'user_id' => $userId,
                'action' => $action
            ]);
        } catch (PDOException $e) {
            return false; // Log l'erreur dans un fichier si besoin
        }
    }

    /**
     * Met à jour la déconnexion pour la dernière session de type 'login'.
     * @param int $userId ID de l'utilisateur
     * @return bool True si succès, false sinon
     */
    public function logLogout($userId) {
        try {
            $stmt = $this->pdo->prepare(
                "UPDATE sessions 
                 SET logout_time = NOW() 
                 WHERE user_id = :user_id AND action = 'login' AND logout_time IS NULL 
                 ORDER BY login_time DESC LIMIT 1"
            );
            return $stmt->execute(['user_id' => $userId]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Récupère l’historique des actions d’un utilisateur.
     * @param int $userId ID de l'utilisateur
     * @return array Liste des sessions (action, login_time, logout_time)
     */
    public function getSessionHistory($userId) {
        try {
            $stmt = $this->pdo->prepare(
                "SELECT action, login_time, logout_time 
                 FROM sessions 
                 WHERE user_id = :user_id 
                 ORDER BY login_time DESC 
                 LIMIT 10" // Limite à 10 dernières actions
            );
            $stmt->execute(['user_id' => $userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
}
?>