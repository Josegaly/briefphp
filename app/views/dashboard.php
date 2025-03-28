<?php require_once __DIR__ . '/../models/Security.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord Admin | Système de Gestion des Utilisateurs</title>
    <meta name="description" content="Système de Gestion des Utilisateurs - Tableau de bord Admin">
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
      .toast { position: fixed; bottom: 20px; right: 20px; padding: 0.75rem 1.5rem; border-radius: 0.5rem; color: white; max-width: 300px; z-index: 9999; animation: fade-in-right 0.3s ease-out forwards; }
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
        <h2 class="text-xl font-semibold text-gray-800">Tableau de bord Admin</h2>
        <div class="flex items-center space-x-6">
          <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-medium">Tableau de bord Admin</span>
          <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-medium">Ajout d'un role</span>
          <nav>
            <ul class="flex space-x-6">
              <li><a href="/logout" class="text-red-600 hover:text-red-800">Déconnexion</a></li>
            </ul>
          </nav>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow container mx-auto px-4 py-8">
      <!-- Messages -->
      <?php if (isset($_GET['error']) || isset($_GET['success'])): ?>
        <script>
          document.addEventListener('DOMContentLoaded', () => {
            <?php if (isset($_GET['error'])): ?>
              showToast("<?php echo htmlspecialchars($_GET['error']); ?>", 'error');
            <?php endif; ?>
            <?php if (isset($_GET['success'])): ?>
              showToast("<?php echo htmlspecialchars($_GET['success']); ?>", 'success');
            <?php endif; ?>
          });
        </script>
      <?php endif; ?>

      <!-- Statistiques -->
      <section class="mb-8">
        <h3 class="text-2xl font-bold text-gray-800 mb-6">Statistiques</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          <div class="bg-white p-6 rounded-lg shadow-soft border border-gray-200">
            <div class="flex items-center">
              <div class="rounded-full bg-blue-100 p-3 mr-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
              </div>
              <div>
                <p class="text-sm text-gray-500 mb-1">Nombre total d'utilisateurs</p>
                <h4 class="text-xl font-bold text-gray-900"><?php echo htmlspecialchars($stats['total_users']); ?></h4>
              </div>
            </div>
          </div>
          <div class="bg-white p-6 rounded-lg shadow-soft border border-gray-200">
            <div class="flex items-center">
              <div class="rounded-full bg-green-100 p-3 mr-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
              </div>
              <div>
                <p class="text-sm text-gray-500 mb-1">Utilisateurs actifs</p>
                <h4 class="text-xl font-bold text-gray-900"><?php echo htmlspecialchars($stats['active_users']); ?></h4>
              </div>
            </div>
          </div>
          <div class="bg-white p-6 rounded-lg shadow-soft border border-gray-200">
            <div class="flex items-center">
              <div class="rounded-full bg-red-100 p-3 mr-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                </svg>
              </div>
              <div>
                <p class="text-sm text-gray-500 mb-1">Utilisateurs inactifs</p>
                <h4 class="text-xl font-bold text-gray-900"><?php echo htmlspecialchars($stats['inactive_users']); ?></h4>
              </div>
            </div>
          </div>
          <div class="bg-white p-6 rounded-lg shadow-soft border border-gray-200">
            <div class="flex items-center">
              <div class="rounded-full bg-purple-100 p-3 mr-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <div>
                <p class="text-sm text-gray-500 mb-1">Nombre total d'actions</p>
                <h4 class="text-xl font-bold text-gray-900"><?php echo htmlspecialchars($stats['total_actions']); ?></h4>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Add User Section -->
      <section class="mb-8">
        <div class="bg-white p-6 rounded-lg shadow-soft border border-gray-200">
          <h3 class="text-xl font-semibold text-gray-800 mb-4">Ajouter un utilisateur</h3>
          <form action="/user/create" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
            <div class="space-y-2">
              <label for="username" class="block text-sm font-medium text-gray-700">Nom d'utilisateur</label>
              <input type="text" name="username" id="username" placeholder="Nom d'utilisateur" required class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
            </div>
            <div class="space-y-2">
              <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
              <input type="email" name="email" id="email" placeholder="Email" required class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
            </div>
            <div class="space-y-2">
              <label for="password" class="block text-sm font-medium text-gray-700">Mot de passe</label>
              <input type="password" name="password" id="password" placeholder="Mot de passe" required class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
            </div>
            <div class="space-y-2">
              <label for="role_id" class="block text-sm font-medium text-gray-700">Rôle</label>
              <select name="role_id" id="role_id" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                <?php foreach ($roles as $role): ?>
                  <option value="<?php echo $role['id']; ?>" <?php echo $role['id'] == 2 ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($role['name']); ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="space-y-2">
              <label for="status" class="block text-sm font-medium text-gray-700">Statut</label>
              <select name="status" id="status" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                <option value="active" selected>Actif</option>
                <option value="inactive">Inactif</option>
              </select>
            </div>
            <div class="flex items-end">
              <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">Ajouter</button>
            </div>
          </form>
        </div>
      </section>

      <!-- Users Table Section -->
      <section class="mb-8">
        <div class="bg-white rounded-lg shadow-soft border border-gray-200 overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead>
                <tr class="bg-blue-500 text-white">
                  <th class="px-6 py-3 text-left text-sm font-medium uppercase">ID</th>
                  <th class="px-6 py-3 text-left text-sm font-medium uppercase">Nom</th>
                  <th class="px-6 py-3 text-left text-sm font-medium uppercase">Email</th>
                  <th class="px-6 py-3 text-left text-sm font-medium uppercase">Rôle</th>
                  <th class="px-6 py-3 text-left text-sm font-medium uppercase">Statut</th>
                  <th class="px-6 py-3 text-left text-sm font-medium uppercase">Actions</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200">
                <?php if (!empty($users)): ?>
                  <?php foreach ($users as $user): ?>
                    <tr class="hover:bg-gray-50">
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($user['id']); ?></td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($user['username']); ?></td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($user['email']); ?></td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <span class="px-2 py-1 rounded-full text-xs font-medium <?php echo $user['role'] === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800'; ?>">
                          <?php echo htmlspecialchars($user['role']); ?>
                        </span>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <span class="px-2 py-1 rounded-full text-xs font-medium <?php echo $user['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                          <?php echo htmlspecialchars($user['status']); ?>
                        </span>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <form action="/user/update/<?php echo $user['id']; ?>" method="POST" class="inline-flex space-x-2">
                          <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
                          <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required class="px-2 py-1 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                          <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required class="px-2 py-1 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                          <select name="role_id" class="px-2 py-1 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <?php foreach ($roles as $role): ?>
                              <option value="<?php echo $role['id']; ?>" <?php echo $user['role_id'] == $role['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($role['name']); ?>
                              </option>
                            <?php endforeach; ?>
                          </select>
                          <select name="status" class="px-2 py-1 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="active" <?php echo $user['status'] == 'active' ? 'selected' : ''; ?>>Actif</option>
                            <option value="inactive" <?php echo $user['status'] == 'inactive' ? 'selected' : ''; ?>>Inactif</option>
                          </select>
                          <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-1 px-2 rounded-md transition-colors">Mettre à jour</button>
                        </form>
                        <form action="/user/delete/<?php echo $user['id']; ?>" method="POST" class="inline-block ml-2">
                          <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
                          <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-medium py-1 px-2 rounded-md transition-colors">Supprimer</button>
                        </form>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr><td colspan="6" class="px-6 py-4 text-center text-gray-600">Aucun utilisateur trouvé.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- Sessions Table Section -->
      <section class="mb-8">
        <h3 class="text-2xl font-bold text-gray-800 mb-6">Historique des sessions</h3>
        <div class="bg-white rounded-lg shadow-soft border border-gray-200 overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead>
                <tr class="bg-blue-500 text-white">
                  <th class="px-6 py-3 text-left text-sm font-medium uppercase">Utilisateur</th>
                  <th class="px-6 py-3 text-left text-sm font-medium uppercase">Action</th>
                  <th class="px-6 py-3 text-left text-sm font-medium uppercase">Connexion</th>
                  <th class="px-6 py-3 text-left text-sm font-medium uppercase">Déconnexion</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200">
                <?php if (!empty($allSessions)): ?>
                  <?php foreach ($allSessions as $session): ?>
                    <tr class="hover:bg-gray-50">
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($session['username']); ?></td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($session['action']); ?></td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($session['login_time']); ?></td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($session['logout_time'] ?: '-'); ?></td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr><td colspan="4" class="px-6 py-4 text-center text-gray-600">Aucune session enregistrée.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </section>
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
    </script>
</body>
</html>