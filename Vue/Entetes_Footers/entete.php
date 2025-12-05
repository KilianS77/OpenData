<!-- Tailwind CSS CDN -->
<script src="https://cdn.tailwindcss.com"></script>

<header class="bg-white border-b border-red-500">
    <div class="container mx-auto px-6 py-4">
        <div class="flex items-center justify-between">
            <!-- Logo / Titre -->
            <div class="flex items-center">
                <a href="index.php" class="text-2xl font-light text-gray-900">
                    Activités Melun
                </a>
            </div>

            <!-- Navigation -->
            <nav class="flex items-center space-x-3">
                <?php if (isset($_SESSION['connect']) && $_SESSION['connect'] === true): ?>
                    <!-- Utilisateur connecté -->
                    <span class="text-sm text-gray-600 mr-2">
                        <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Utilisateur'); ?>
                    </span>
                    <a href="index.php?ctl=participation&action=mes_participations" 
                       class="px-4 py-2 text-sm text-gray-700 hover:text-red-500 transition-colors border border-transparent hover:border-red-500">
                        Mes participations
                    </a>
                    <a href="index.php?ctl=participation&action=mes_invitations" 
                       class="px-4 py-2 text-sm text-gray-700 hover:text-red-500 transition-colors border border-transparent hover:border-red-500">
                        Invitations
                    </a>
                    <a href="index.php?ctl=amis&action=liste" 
                       class="px-4 py-2 text-sm text-gray-700 hover:text-red-500 transition-colors border border-transparent hover:border-red-500">
                        Mes amis
                    </a>
                    <a href="index.php?ctl=parametres&action=afficher_parametres" 
                       class="px-4 py-2 text-sm text-gray-700 hover:text-red-500 transition-colors border border-transparent hover:border-red-500">
                        Paramètres
                    </a>
                    <a href="index.php?ctl=connexion&action=deconnexion" 
                       class="px-4 py-2 text-sm text-white bg-red-500 hover:bg-red-600 transition-colors">
                        Déconnexion
                    </a>
                <?php else: ?>
                    <!-- Utilisateur non connecté -->
                    <a href="index.php?ctl=connexion&action=connexion" 
                       class="px-4 py-2 text-sm text-gray-700 hover:text-red-500 transition-colors border border-transparent hover:border-red-500">
                        Connexion
                    </a>
                    <a href="index.php?ctl=connexion&action=inscription" 
                       class="px-4 py-2 text-sm text-white bg-red-500 hover:bg-red-600 transition-colors">
                        Créer un compte
                    </a>
                <?php endif; ?>
            </nav>
        </div>
    </div>
</header>
