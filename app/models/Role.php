<?php
class Role {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Récupère tous les rôles disponibles dans la table roles.
     * @return array Liste des rôles (id, name)
     */
    public function getAllRoles() {
        try {
            $stmt = $this->pdo->query("SELECT id, name FROM roles ORDER BY id ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ['success' => false, 'message' => "Erreur lors de la récupération des rôles : " . $e->getMessage()];
        }
    }

    /**
     * Récupère un rôle spécifique par son ID.
     * @param int $roleId ID du rôle à récupérer
     * @return array|null Infos du rôle (id, name) ou null si introuvable
     */
    public function getRoleById($roleId) {
        try {
            $stmt = $this->pdo->prepare("SELECT id, name FROM roles WHERE id = :id");
            $stmt->execute(['id' => $roleId]);
            $role = $stmt->fetch(PDO::FETCH_ASSOC);
            return $role ?: null;
        } catch (PDOException $e) {
            return ['success' => false, 'message' => "Erreur lors de la récupération du rôle : " . $e->getMessage()];
        }
    }

    /**
     * Vérifie si un rôle existe dans la base de données.
     * @param int $roleId ID du rôle à vérifier
     * @return bool True si le rôle existe, false sinon
     */
    public function roleExists($roleId) {
        $role = $this->getRoleById($roleId);
        return $role !== null;
    }
}
?>