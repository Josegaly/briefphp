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
              'elegant': '0 10px 20px -5px rgba(0, 0, 0, 0.08)',
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

      /* Modal styles */
      .modal {
        transition: all 0.3s ease-out;
        opacity: 0;
        visibility: hidden;
      }
      .modal.active {
        opacity: 1;
        visibility: visible;
      }
      .modal-overlay {
        background-color: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
      }
      .modal-content {
        transform: translateY(-20px);
        transition: transform 0.3s ease-out;
      }
      .modal.active .modal-content {
        transform: translateY(0);
      }

      /* Sidebar styles */
      .sidebar {
        width: 250px;
        background:#3b82f6;
        border-right: 1px solid #e5e7eb;
        padding: 1.5rem;
        height: 100vh;
        position: fixed;
        top: 0;
        left: 0;
        z-index: 1000;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
      }
      .sidebar-content {
        margin-top: 4rem;
      }
      .sidebar-item {
        margin-bottom: 1rem;
      }
      .main-content {
        margin-left: 250px;
        padding: 2rem;
      }

      /* Elegant and minimalistic styles */
      .btn-elegant {
        transition: all 0.3s ease;
        border: none;
        outline: none;
      }
      .btn-elegant:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      }
      .card-elegant {
        border: none;
        box-shadow: 0 10px 20px -5px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
      }
      .card-elegant:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 25px -5px rgba(0, 0, 0, 0.1);
      }
      .table-header {
        background: linear-gradient(90deg, #3b82f6, #60a5fa);
        color: white;
      }
      .table-row {
        transition: all 0.2s ease;
      }
      .table-row:hover {
        background-color: #f9fafb;
        transform: scale(1.01);
      }
      .input-elegant {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
      }
      .input-elegant:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
      }
      .btn-icon {
        display: flex;
        align-items: center;
        gap: 0.5rem;
      }
      .chevron {
        transition: transform 0.3s ease;
      }
      .chevron.rotate {
        transform: rotate(180deg);
      }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 font-sans min-h-screen flex flex-col">
    <!-- Sidebar -->
    <aside class="sidebar">
      <div class="sidebar-content">
        <div class="sidebar-item">
          <button 
            id="toggleHistory" 
            class="w-full bg-white hover:bg-gray-50 text-gray-800 font-medium py-3 px-4 rounded-lg shadow-soft border border-gray-200 transition-colors btn-elegant btn-icon"
          >
            <span>Historique des connexions</span>
            <svg id="historyChevron" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 chevron" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
          </button>
          
          <div id="historySection" class="hidden bg-white rounded-lg shadow-soft border border-gray-200 overflow-hidden mt-2 card-elegant">
            <div class="overflow-x-auto">
              <table class="w-full">
                <thead>
                  <tr class="table-header">
                    <th class="px-4 py-3 text-left text-sm font-medium uppercase">Action</th>
                    <th class="px-4 py-3 text-left text-sm font-medium uppercase">Date</th>
                    <th class="px-4 py-3 text-left text-sm font-medium uppercase">Déconnexion</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                  <?php foreach ($sessions as $session): ?>
                    <tr class="table-row">
                      <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($session['action']); ?></td>
                      <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($session['login_time']); ?></td>
                      <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($session['logout_time'] ?: '-'); ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div class="sidebar-item">
          <form action="/profile" method="POST" onsubmit="return confirm('Voulez-vous vraiment supprimer votre compte ?');">
            <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
            <input type="hidden" name="delete_account" value="1">
            <button 
              type="submit" 
              class="w-full bg-red-50 hover:bg-red-100 text-red-600 font-medium py-2 px-4 rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 btn-elegant btn-icon"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
              </svg>
              Supprimer mon compte
            </button>
          </form>
        </div>
      </div>
    </aside>

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
    <main class="flex-grow main-content">
      <div class="max-w-md mx-auto">
        <div class="bg-white p-6 rounded-lg shadow-soft border border-gray-200 mb-8 card-elegant">
          <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold text-gray-800">Mon Profil</h2>
            <div class="flex items-center space-x-2">
              <span class="h-3 w-3 rounded-full bg-emerald-400 animate-pulse"></span>
              <span class="text-sm text-gray-500">Connecté</span>
            </div>
          </div>
          
          <!-- Profile Information -->
          <div class="space-y-4 mb-6">
            <div class="flex justify-between items-center py-3 border-b border-gray-100">
              <div>
                <p class="text-sm text-gray-500">Nom d'utilisateur</p>
                <p class="font-medium text-gray-800"><?php echo htmlspecialchars($user['username']); ?></p>
              </div>
            </div>
            
            <div class="flex justify-between items-center py-3 border-b border-gray-100">
              <div>
                <p class="text-sm text-gray-500">Email</p>
                <p class="font-medium text-gray-800"><?php echo htmlspecialchars($user['email']); ?></p>
              </div>
            </div>
          </div>
          
          <!-- Bouton pour ouvrir la modale -->
          <button 
            id="openModal"
            class="w-full bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 btn-elegant btn-icon"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
            </svg>
            Modifier mon profil
          </button>
        </div>
      </div>
    </main>

    <!-- Modal pour modifier le profil -->
    <div id="profileModal" class="modal fixed inset-0 z-50 flex items-center justify-center p-4">
      <div class="modal-overlay absolute inset-0"></div>
      
      <div class="modal-content relative bg-white rounded-lg shadow-xl w-full max-w-md mx-auto card-elegant">
        <button id="closeModal" class="absolute top-4 right-4 p-1 rounded-full hover:bg-gray-100 transition-colors">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
        
        <div class="p-6">
          <h3 class="text-xl font-bold text-gray-800 mb-6">Modifier mon profil</h3>
          
          <form action="/profile" method="POST" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
            
            <div class="space-y-2">
              <label for="modalUsername" class="block text-sm font-medium text-gray-700">Nom d'utilisateur</label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                  </svg>
                </div>
                <input 
                  type="text" 
                  name="username" 
                  id="modalUsername" 
                  value="<?php echo htmlspecialchars($user['username']); ?>" 
                  required 
                  class="w-full pl-10 pr-4 py-2 input-elegant"
                >
              </div>
            </div>
            
            <div class="space-y-2">
              <label for="modalEmail" class="block text-sm font-medium text-gray-700">Email</label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                  </svg>
                </div>
                <input 
                  type="email" 
                  name="email" 
                  id="modalEmail" 
                  value="<?php echo htmlspecialchars($user['email']); ?>" 
                  required 
                  class="w-full pl-10 pr-4 py-2 input-elegant"
                >
              </div>
            </div>
            
            <div class="space-y-2">
              <label for="modalPassword" class="block text-sm font-medium text-gray-700">Nouveau mot de passe (optionnel)</label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                  </svg>
                </div>
                <input 
                  type="password" 
                  name="password" 
                  id="modalPassword" 
                  placeholder="Nouveau mot de passe (optionnel)" 
                  class="w-full pl-10 pr-4 py-2 input-elegant"
                >
              </div>
            </div>
            
            <div class="flex space-x-3 pt-4">
              <button 
                type="submit" 
                class="flex-1 bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 btn-elegant"
              >
                Enregistrer
              </button>
              <button 
                type="button" 
                id="cancelModal"
                class="flex-1 bg-white hover:bg-gray-50 text-gray-800 font-medium py-2 px-4 rounded-md border border-gray-200 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 btn-elegant"
              >
                Annuler
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

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

      // Modal functionality
      const modal = document.getElementById('profileModal');
      const openModalBtn = document.getElementById('openModal');
      const closeModalBtn = document.getElementById('closeModal');
      const cancelModalBtn = document.getElementById('cancelModal');

      function toggleModal() {
        modal.classList.toggle('active');
      }

      openModalBtn.addEventListener('click', toggleModal);
      closeModalBtn.addEventListener('click', toggleModal);
      cancelModalBtn.addEventListener('click', toggleModal);

      // Close modal when clicking outside
      modal.addEventListener('click', (e) => {
        if (e.target === modal) {
          toggleModal();
        }
      });

      // Toggle history section
      document.getElementById('toggleHistory').addEventListener('click', function() {
        const historySection = document.getElementById('historySection');
        const chevron = document.getElementById('historyChevron');
        
        historySection.classList.toggle('hidden');
        chevron.classList.toggle('rotate');
      });

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