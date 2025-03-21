<?php require_once __DIR__ . '/../models/Security.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Admin | Système de Gestion des Utilisateurs</title>
    <meta name="description" content="Système de Gestion des Utilisateurs - Page de Connexion Admin">
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
      @keyframes fade-in-right {
        from { opacity: 0; transform: translateX(100%); }
        to { opacity: 1; transform: translateX(0); }
      }
      @keyframes fade-out-right {
        from { opacity: 1; transform: translateX(0); }
        to { opacity: 0; transform: translateX(100%); }
      }
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
            <li><a href="/login" class="text-gray-600 hover:text-gray-800">Connexion</a></li>
            <li><a href="/register" class="text-gray-600 hover:text-gray-800">Inscription</a></li>
            <li><a href="/dashboard" class="text-gray-600 hover:text-gray-800">Tableau de bord</a></li>
          </ul>
        </nav>
      </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow container mx-auto px-4 py-8 flex items-center justify-center">
      <div class="bg-white rounded-lg shadow-soft w-full max-w-md p-8 border border-gray-200">
        <div class="flex justify-center mb-6">
          <span class="bg-red-500 text-white px-3 py-1 rounded-full text-sm font-medium">Réservé aux Admins</span>
        </div>
        
        <h2 class="text-2xl font-semibold text-gray-900 mb-6 text-center">Connexion Admin</h2>
        
        <form id="adminLoginForm" action="/admin-login" method="POST" class="space-y-6">
          <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
          <div class="space-y-2">
            <label for="username" class="block text-sm font-medium text-gray-700">Nom d’utilisateur Admin</label>
            <input 
              type="text" 
              id="username" 
              name="username" 
              required 
              class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
              placeholder="Entrez votre nom d’utilisateur admin"
            >
          </div>
          
          <div class="space-y-2">
            <label for="password" class="block text-sm font-medium text-gray-700">Mot de passe Admin</label>
            <input 
              type="password" 
              id="password" 
              name="password" 
              required 
              class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
              placeholder="Entrez votre mot de passe admin"
            >
          </div>
          
          <div>
            <button 
              type="submit" 
              class="w-full bg-red-500 hover:bg-red-600 text-white font-medium py-2 px-4 rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
            >
              Connexion Admin
            </button>
          </div>
        </form>
        
        <div class="mt-6 text-center">
          <p class="text-sm text-gray-600">
            Utilisateur normal ? 
            <a href="/login" class="text-blue-500 hover:text-blue-600 font-medium">Connexion Utilisateur</a>
          </p>
        </div>
      </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 py-6">
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
          toast.addEventListener('animationend', () => {
            toastContainer.removeChild(toast);
          });
        }, duration);
      }

      // Check URL for toast messages (PHP integration)
      <?php if (isset($_GET['success'])): ?>
        showToast("<?php echo htmlspecialchars($_GET['success']); ?>", 'success');
      <?php endif; ?>
      <?php if (isset($_GET['error'])): ?>
        showToast("<?php echo htmlspecialchars($_GET['error']); ?>", 'error');
      <?php endif; ?>

      // Form validation
      function validateAdminLoginForm(e) {
        const usernameInput = document.getElementById('username');
        const passwordInput = document.getElementById('password');
        
        if (usernameInput.value.trim() === '') {
          e.preventDefault();
          showToast('Le nom d’utilisateur admin ne peut pas être vide', 'error');
          return false;
        }
        
        if (passwordInput.value.trim() === '') {
          e.preventDefault();
          showToast('Le mot de passe admin ne peut pas être vide', 'error');
          return false;
        }
        
        return true;
      }

      // Initialize on page load
      document.addEventListener('DOMContentLoaded', () => {
        const adminLoginForm = document.getElementById('adminLoginForm');
        if (adminLoginForm) {
          adminLoginForm.addEventListener('submit', validateAdminLoginForm);
        }
      });
    </script>
</body>
</html>