<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-50 flex items-center justify-center p-8">
    <div class="max-w-md w-full animate-fade-in-up">
        <!-- Carte du formulaire -->
        <div class="bg-white rounded-2xl shadow-2xl p-8 border border-gray-100 card-hover">
            <!-- Titre -->
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-gradient-to-br from-red-100 to-red-200 rounded-full flex items-center justify-center mx-auto mb-4 animate-float">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2 tracking-tight">Créer un compte</h1>
                <p class="text-sm text-gray-600 font-light">Rejoignez la communauté</p>
            </div>

            <!-- Messages d'erreur/succès -->
            <?php if (isset($_SESSION['error'])): ?>
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 rounded-r-lg p-4 shadow-sm animate-slide-in-right">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-sm text-red-700 font-medium"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Formulaire d'inscription -->
            <form method="POST" action="index.php?ctl=connexion&action=createaccount" class="space-y-6">
                <div class="relative">
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                        <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Nom
                    </label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:border-red-500 focus:ring-2 focus:ring-red-500 focus:ring-opacity-20 text-sm transition-all duration-300 hover:border-gray-400"
                        placeholder="Votre nom"
                    >
                </div>

                <div class="relative">
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                        <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        Email
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:border-red-500 focus:ring-2 focus:ring-red-500 focus:ring-opacity-20 text-sm transition-all duration-300 hover:border-gray-400"
                        placeholder="votre@email.com"
                    >
                </div>

                <div class="relative">
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                        <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        Mot de passe
                    </label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        required
                        minlength="6"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:border-red-500 focus:ring-2 focus:ring-red-500 focus:ring-opacity-20 text-sm transition-all duration-300 hover:border-gray-400"
                        placeholder="Minimum 6 caractères"
                    >
                    <p class="mt-1 text-xs text-gray-500 font-light flex items-center space-x-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Le mot de passe doit contenir au moins 6 caractères</span>
                    </p>
                </div>

                <div class="relative">
                    <label for="password_confirm" class="block text-sm font-semibold text-gray-700 mb-2">
                        <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        Confirmer le mot de passe
                    </label>
                    <input 
                        type="password" 
                        id="password_confirm" 
                        name="password_confirm" 
                        required
                        minlength="6"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:border-red-500 focus:ring-2 focus:ring-red-500 focus:ring-opacity-20 text-sm transition-all duration-300 hover:border-gray-400"
                        placeholder="Répétez le mot de passe"
                    >
                </div>

                <button 
                    type="submit" 
                    class="w-full px-4 py-3 bg-red-500 text-white font-semibold rounded-lg shadow-md hover:bg-red-600 hover:shadow-lg transform hover:-translate-y-1 transition-all duration-300 hover-lift ripple"
                >
                    <span class="flex items-center justify-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                        <span>Créer mon compte</span>
                    </span>
                </button>
            </form>

            <!-- Lien vers connexion -->
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600 font-light">
                    Déjà un compte ? 
                    <a href="index.php?ctl=connexion&action=connexion" class="text-red-500 hover:text-red-600 font-semibold transition-colors hover:underline">
                        Se connecter
                    </a>
                </p>
            </div>

            <!-- Lien retour accueil -->
            <div class="mt-4 text-center">
                <a href="index.php" class="text-sm text-gray-500 hover:text-red-500 font-medium transition-colors inline-flex items-center space-x-1 hover:underline">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    <span>Retour à l'accueil</span>
                </a>
            </div>
        </div>
    </div>
</div>

