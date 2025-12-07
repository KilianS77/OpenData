<!-- Tailwind CSS CDN -->
<script src="https://cdn.tailwindcss.com"></script>

<header class="bg-white border-b border-gray-200 shadow-sm">
    <div class="container mx-auto px-6 py-4">
        <div class="flex items-center justify-between">
            <!-- Logo / Titre -->
            <div class="flex items-center">
                <a href="index.php" class="text-2xl font-semibold text-gray-900 hover:text-red-500 transition-colors">
                    Activités Melun
                </a>
            </div>

            <!-- Navigation -->
            <nav class="flex items-center space-x-2">
                <?php if (isset($_SESSION['connect']) && $_SESSION['connect'] === true): ?>
                    <!-- Utilisateur connecté -->
                    <a href="index.php?ctl=map" 
                       class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-red-500 hover:bg-gray-50 rounded-lg transition-all duration-200">
                        Map
                    </a>
                    <a href="index.php?ctl=evenements&action=liste" 
                       class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-red-500 hover:bg-gray-50 rounded-lg transition-all duration-200">
                        Événements
                    </a>
                    <a href="index.php?ctl=participation&action=mes_participations" 
                       class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-red-500 hover:bg-gray-50 rounded-lg transition-all duration-200">
                        Mes participations
                    </a>
                    <a href="index.php?ctl=participation&action=participations_autres" 
                       class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-red-500 hover:bg-gray-50 rounded-lg transition-all duration-200">
                        Participations des autres
                    </a>
                    <a href="index.php?ctl=participation&action=mes_invitations" 
                       class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-red-500 hover:bg-gray-50 rounded-lg transition-all duration-200">
                        Invitations
                    </a>
                    <a href="index.php?ctl=amis&action=liste" 
                       class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-red-500 hover:bg-gray-50 rounded-lg transition-all duration-200">
                        Mes amis
                    </a>
                    <a href="index.php?ctl=parametres&action=afficher_parametres" 
                       class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-red-500 hover:bg-gray-50 rounded-lg transition-all duration-200">
                        Paramètres
                    </a>
                    <a href="index.php?ctl=connexion&action=deconnexion" 
                       class="px-4 py-2 text-sm font-medium text-white bg-red-500 hover:bg-red-600 rounded-lg shadow-sm hover:shadow-md transition-all duration-200">
                        Déconnexion
                    </a>
                <?php else: ?>
                    <!-- Utilisateur non connecté -->
                    <a href="index.php?ctl=evenements&action=liste" 
                       class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-red-500 hover:bg-gray-50 rounded-lg transition-all duration-200">
                        Événements
                    </a>
                    <a href="index.php?ctl=connexion&action=connexion" 
                       class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-red-500 hover:bg-gray-50 rounded-lg transition-all duration-200">
                        Connexion
                    </a>
                    <a href="index.php?ctl=connexion&action=inscription" 
                       class="px-4 py-2 text-sm font-medium text-white bg-red-500 hover:bg-red-600 rounded-lg shadow-sm hover:shadow-md transition-all duration-200">
                        Créer un compte
                    </a>
                <?php endif; ?>
            </nav>
        </div>
    </div>
</header>
