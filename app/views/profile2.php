<?php if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
; require_once __DIR__ . '/../models/Security.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil | Système de Gestion des Utilisateurs</title>
    <meta name="description" content="Système de Gestion des Utilisateurs - Mon Profil">
    <meta name="author" content="Lovable">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = {
        theme: {
          extend: {
            colors: {
              primary: '#3b82f6',
              secondary: '#f3f4f6',
              success: '#10b981',
              danger: '#ef4444',
              warning: '#f59e0b',
              info: '#3b82f6',
            },
            fontFamily: {
              sans: ['Inter', 'sans-serif'],
            },
            boxShadow: {
              'soft': '0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)',
            }
          }
        }
      }
    </script>

    <!-- Inter font from Google Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">

    <!-- Custom styles -->
    <style>
      .toast {
        position: fixed;
        bottom: 20px;
        right: 20px;
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        color: white;
        max-width: 300px;
        z-index: 9999;
        animation: fade-in-right 0.3s ease-out forwards;
      }
      .toast-success { background-color: #10b981; }
      .toast-error { background-color: #ef4444; }
      .toast-info { background-color: #3b82f6; }
      @keyframes fade-in-right { from { opacity: 0; transform: translateX(100%); } to { opacity: 1; transform: translateX(0); } }
      @keyframes fade-out-right { from { opacity: 1; transform: translateX(0); } to { opacity: 0; transform: translateX(100%); } }
      .fade-out { animation: fade-out-right 0.3s ease-out forwards; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 font-sans min-h-screen flex flex-col">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
      <div class="container mx-auto px-4 py-4 flex justify-between items-center">
        <h1 class="text-xl font-semibold text-gray-800">Gestion des Utilisateurs</h1>
        <nav>
          <ul class="flex space-x-6">
            <li><a href="/" class="text-gray-600 hover:text-gray-800">Accueil</a></li>
            <li><a href="/profile" class="text-blue-600 hover:text-blue-800 font-medium">Mon Profil</a></li>
            <li><a href="/logout" class="text-red-600 hover:text-red-800">Déconnexion</a></li>
          </ul>
        </nav>
      </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow container mx-auto px-4 py-8">
      <div class="max-w-md mx-auto">
        <div class="bg-white p-6 rounded-lg shadow-soft border border-gray-200 mb-8">
          <h2 class="text-2xl font-bold text-gray-800 mb-4">Mon Profil</h2>
          <a href="/logout" class="text-red-600 hover:text-red-800 block mb-4">Déconnexion</a>
          <form action="/profile" method="POST" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
            <div class="space-y-2">
              <label for="username" class="block text-sm font-medium text-gray-700">Nom d'utilisateur</label>
              <input 
                type="text" 
                name="username" 
                id="username" 
                value="<?php echo htmlspecialchars($user['username']); ?>" 
                required 
                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
              >
            </div>
            <div class="space-y-2">
              <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
              <input 
                type="email" 
                name="email" 
                id="email" 
                value="<?php echo htmlspecialchars($user['email']); ?>" 
                required 
                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
              >
            </div>
            <div class="space-y-2">
              <label for="password" class="block text-sm font-medium text-gray-700">Nouveau mot de passe (optionnel)</label>
              <input 
                type="password" 
                name="password" 
                id="password" 
                placeholder="Nouveau mot de passe (optionnel)" 
                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
              >
            </div>
            <button 
              type="submit" 
              class="w-full bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
            >
              Mettre à jour
            </button>
          </form>
          <form action="/profile" method="POST" onsubmit="return confirm('Voulez-vous vraiment supprimer votre compte ?');" class="mt-4">
            <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
            <input type="hidden" name="delete_account" value="1">
            <button 
              type="submit" 
              class="w-full bg-red-500 hover:bg-red-600 text-white font-medium py-2 px-4 rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
            >
              Supprimer mon compte
            </button>
          </form>
        </div>

        <h3 class="text-xl font-semibold text-gray-800 mb-4">Historique des actions</h3>
        <div class="bg-white rounded-lg shadow-soft border border-gray-200 overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead>
                <tr class="bg-blue-500 text-white">
                  <th class="px-6 py-3 text-left text-sm font-medium uppercase">Action</th>
                  <th class="px-6 py-3 text-left text-sm font-medium uppercase">Date</th>
                  <th class="px-6 py-3 text-left text-sm font-medium uppercase">Déconnexion</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200">
                <?php foreach ($sessions as $session): ?>
                  <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($session['action']); ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($session['login_time']); ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($session['logout_time'] ?: '-'); ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 py-6 mt-8">
      <div class="container mx-auto px-4 text-center text-gray-500">
        <p>© 2025 Système de Gestion des Utilisateurs. Tous droits réservés.</p>
      </div>
    </footer>

    <!-- Toast Container -->
    <div id="toastContainer"></div>

    <!-- JavaScript -->
    <script>
      // Toast management
      function showToast(message, type = 'info', duration = 5000) {
        const toastContainer = document.getElementById('toastContainer');
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.textContent = message;
        toastContainer.appendChild(toast);
        setTimeout(() => {
          toast.classList.add('fade-out');
          toast.addEventListener('animationend', () => toastContainer.removeChild(toast));
        }, duration);
      }

      // Show toast messages from URL
      document.addEventListener('DOMContentLoaded', () => {
        <?php if (isset($_GET['success'])): ?>
          showToast("<?php echo htmlspecialchars($_GET['success']); ?>", 'success');
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
          showToast("<?php echo htmlspecialchars($_GET['error']); ?>", 'error');
        <?php endif; ?>
      });
    </script>
</body>
</html>