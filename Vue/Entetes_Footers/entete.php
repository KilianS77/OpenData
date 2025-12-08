<!-- Tailwind CSS CDN -->
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="assets/css/animations.css">
<script src="assets/js/animations.js" defer></script>

<header class="bg-white border-b border-gray-200 shadow-sm sticky top-0 z-40 backdrop-blur-sm bg-white/95">
    <div class="container mx-auto px-6 py-4">
        <div class="flex items-center justify-between">
            <!-- Logo / Titre -->
            <div class="flex items-center animate-fade-in">
                <a href="index.php" class="flex items-center space-x-2 group">
                    <svg class="w-6 h-6 text-red-500 transition-transform group-hover:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span class="text-2xl font-bold text-gray-900 group-hover:text-red-500 transition-colors">
                        Activités Melun
                    </span>
                </a>
            </div>

            <!-- Navigation -->
            <nav class="flex items-center space-x-1 animate-fade-in-down">
                <?php if (isset($_SESSION['connect']) && $_SESSION['connect'] === true): ?>
                    <!-- Utilisateur connecté -->
                    <a href="index.php?ctl=map" 
                       class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all duration-300 relative group">
                        <svg class="w-4 h-4 inline-block mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                        </svg>
                        Map
                    </a>
                    <a href="index.php?ctl=evenements&action=liste" 
                       class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all duration-300 relative group">
                        <svg class="w-4 h-4 inline-block mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Événements
                    </a>
                    <a href="index.php?ctl=participation&action=mes_participations" 
                       class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all duration-300 relative group">
                        <svg class="w-4 h-4 inline-block mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        Mes participations
                    </a>
                    <a href="index.php?ctl=participation&action=participations_autres" 
                       class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all duration-300 relative group">
                        <svg class="w-4 h-4 inline-block mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        Participations des autres
                    </a>
                    <a href="index.php?ctl=participation&action=mes_invitations" 
                       class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all duration-300 relative group">
                        <svg class="w-4 h-4 inline-block mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        Invitations
                    </a>
                    <a href="index.php?ctl=amis&action=liste" 
                       class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all duration-300 relative group">
                        <svg class="w-4 h-4 inline-block mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        Mes amis
                    </a>
                    <a href="index.php?ctl=parametres&action=afficher_parametres" 
                       class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all duration-300 relative group">
                        <svg class="w-4 h-4 inline-block mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Paramètres
                    </a>
                    <a href="index.php?ctl=connexion&action=deconnexion" 
                       class="px-4 py-2 text-sm font-medium text-white bg-red-500 hover:bg-red-600 rounded-lg shadow-sm hover:shadow-md transition-all duration-300 hover-lift ripple">
                        Déconnexion
                    </a>
                <?php else: ?>
                    <!-- Utilisateur non connecté -->
                    <a href="index.php?ctl=evenements&action=liste" 
                       class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all duration-300 relative group">
                        <svg class="w-4 h-4 inline-block mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Événements
                    </a>
                    <a href="index.php?ctl=connexion&action=connexion" 
                       class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all duration-300 relative group">
                        Connexion
                    </a>
                    <a href="index.php?ctl=connexion&action=inscription" 
                       class="px-4 py-2 text-sm font-medium text-white bg-red-500 hover:bg-red-600 rounded-lg shadow-sm hover:shadow-md transition-all duration-300 hover-lift ripple">
                        Créer un compte
                    </a>
                <?php endif; ?>
            </nav>
        </div>
    </div>
</header>
