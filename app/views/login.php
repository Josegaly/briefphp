<?php session_start(); require_once __DIR__ .  '/../models/Security.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 50px; }
        .form-container { max-width: 400px; margin: auto; }
        input { width: 100%; padding: 8px; margin: 10px 0; }
        button { background-color: #4CAF50; color: white; padding: 10px; border: none; cursor: pointer; }
        .error { color: red; }
        .success { color: green; }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Connexion</h2>
        <?php
        if (isset($_GET['error'])) {
            echo '<p class="error">' . htmlspecialchars($_GET['error']) . '</p>';
        }
        if (isset($_GET['success'])) {
            echo '<p class="success">' . htmlspecialchars($_GET['success']) . '</p>';
        }
        if (isset($_GET['step']) && $_GET['step'] === '2fa') {
            echo '<p>Entrez le code 2FA envoyé (simulé ici).</p>';
            ?>
            <form action="/login" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
                <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($_GET['user_id']); ?>">
                <input type="text" name="twofa_code" placeholder="Code 2FA" required>
                <button type="submit">Vérifier</button>
            </form>
            <?php
        } else {
            ?>
            <form action="/login" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
                <input type="text" name="username" placeholder="Nom d'utilisateur" required>
                <input type="password" name="password" placeholder="Mot de passe" required>
                <button type="submit">Se connecter</button>
            </form>
            <p>Pas de compte ? <a href="/register">Inscrivez-vous</a></p>
            <?php
        }
        ?>
    </div>
</body>
</html>