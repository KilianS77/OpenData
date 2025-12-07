<div class="min-h-screen bg-gradient-to-br from-gray-50 to-white flex items-center justify-center p-8">
    <div class="max-w-md w-full">
        <!-- Carte du formulaire -->
        <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
            <!-- Titre -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2 tracking-tight">Connexion</h1>
                <p class="text-sm text-gray-600 font-light">Accédez à votre compte</p>
            </div>

            <!-- Messages d'erreur/succès -->
            <?php if (isset($_SESSION['error'])): ?>
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 rounded-r-lg p-4 shadow-sm">
                    <p class="text-sm text-red-700 font-medium"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></p>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="mb-6 bg-green-50 border-l-4 border-green-500 rounded-r-lg p-4 shadow-sm">
                    <p class="text-sm text-green-700 font-medium"><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></p>
                </div>
            <?php endif; ?>

            <!-- Formulaire de connexion -->
            <form method="POST" action="index.php?ctl=connexion&action=veriflogin" class="space-y-6">
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:border-red-500 focus:ring-2 focus:ring-red-500 focus:ring-opacity-20 text-sm transition-all duration-200"
                        placeholder="votre@email.com"
                    >
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Mot de passe</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:border-red-500 focus:ring-2 focus:ring-red-500 focus:ring-opacity-20 text-sm transition-all duration-200"
                        placeholder="••••••••"
                    >
                </div>

                <button 
                    type="submit" 
                    class="w-full px-4 py-3 bg-red-500 text-white font-semibold rounded-lg shadow-md hover:bg-red-600 hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200"
                >
                    Se connecter
                </button>
            </form>

            <!-- Lien vers inscription -->
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600 font-light">
                    Pas encore de compte ? 
                    <a href="index.php?ctl=connexion&action=inscription" class="text-red-500 hover:text-red-600 font-semibold transition-colors">
                        Créer un compte
                    </a>
                </p>
            </div>

            <!-- Lien retour accueil -->
            <div class="mt-4 text-center">
                <a href="index.php" class="text-sm text-gray-500 hover:text-red-500 font-medium transition-colors">
                    ← Retour à l'accueil
                </a>
            </div>
        </div>
    </div>
</div>

