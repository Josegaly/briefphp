<?php session_start(); require_once 'app/models/Security.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de bord Admin</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 50px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .form-container { margin-bottom: 20px; }
        input, select { padding: 5px; margin: 5px 0; }
        button { background-color: #4CAF50; color: white; padding: 5px; border: none; }
        .error { color: red; }
        .success { color: green; }
    </style>
</head>
<body>
    <h2>Tableau de bord Admin</h2>
    <?php
    if (isset($_GET['error'])) {
        echo '<p class="error">' . htmlspecialchars($_GET['error']) . '</p>';
    }
    if (isset($_GET['success'])) {
        echo '<p class="success">' . htmlspecialchars($_GET['success']) . '</p>';
    }
    ?>
    <div class="form-container">
        <h3>Ajouter un utilisateur</h3>
        <form action="/user/create" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
            <input type="text" name="username" placeholder="Nom d'utilisateur" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Mot de passe" required>
            <select name="role_id">
                <option value="1">Administrateur</option>
                <option value="2" selected>Client</option>
            </select>
            <select name="status">
                <option value="active" selected>Actif</option>
                <option value="inactive">Inactif</option>
            </select>
            <button type="submit">Ajouter</button>
        </form>
    </div>
    <table>
        <tr>
            <th>ID</th>
            <th>Nom</th>
            <th>Email</th>
            <th>Rôle</th>
            <th>Statut</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($users as $user): ?>
        <tr>
            <td><?php echo htmlspecialchars($user['id']); ?></td>
            <td><?php echo htmlspecialchars($user['username']); ?></td>
            <td><?php echo htmlspecialchars($user['email']); ?></td>
            <td><?php echo htmlspecialchars($user['role']); ?></td>
            <td><?php echo htmlspecialchars($user['status']); ?></td>
            <td>
                <form action="/user/update/<?php echo $user['id']; ?>" method="POST" style="display:inline;">
                    <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
                    <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    <select name="role_id">
                        <option value="1" <?php echo $user['role_id'] == 1 ? 'selected' : ''; ?>>Administrateur</option>
                        <option value="2" <?php echo $user['role_id'] == 2 ? 'selected' : ''; ?>>Client</option>
                    </select>
                    <select name="status">
                        <option value="active" <?php echo $user['status'] == 'active' ? 'selected' : ''; ?>>Actif</option>
                        <option value="inactive" <?php echo $user['status'] == 'inactive' ? 'selected' : ''; ?>>Inactif</option>
                    </select>
                    <button type="submit">Mettre à jour</button>
                </form>
                <form action="/user/delete/<?php echo $user['id']; ?>" method="POST" style="display:inline;">
                    <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
                    <button type="submit" style="background-color: red;">Supprimer</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>