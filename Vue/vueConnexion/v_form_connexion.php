<div class="min-h-screen bg-white flex items-center justify-center p-8">
    <div class="max-w-md w-full">
        <!-- Titre -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-light text-gray-900 mb-2 tracking-tight">Connexion</h1>
            <p class="text-sm text-gray-600 font-light">Accédez à votre compte</p>
        </div>

        <!-- Messages d'erreur/succès -->
        <?php if (isset($_SESSION['error'])): ?>
            <div class="mb-6 border-l-4 border-red-500 bg-white p-4">
                <p class="text-sm text-red-500 font-light"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></p>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="mb-6 border-l-4 border-red-500 bg-white p-4">
                <p class="text-sm text-red-500 font-light"><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></p>
            </div>
        <?php endif; ?>

        <!-- Formulaire de connexion -->
        <form method="POST" action="index.php?ctl=connexion&action=veriflogin" class="space-y-6">
            <div>
                <label for="email" class="block text-sm font-light text-gray-700 mb-2">Email</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    required
                    class="w-full px-4 py-3 border border-gray-300 focus:border-red-500 focus:ring-1 focus:ring-red-500 text-sm font-light"
                    placeholder="votre@email.com"
                >
            </div>

            <div>
                <label for="password" class="block text-sm font-light text-gray-700 mb-2">Mot de passe</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    required
                    class="w-full px-4 py-3 border border-gray-300 focus:border-red-500 focus:ring-1 focus:ring-red-500 text-sm font-light"
                    placeholder="••••••••"
                >
            </div>

            <button 
                type="submit" 
                class="w-full px-4 py-3 bg-red-500 text-white font-light hover:bg-red-600 transition-colors"
            >
                Se connecter
            </button>
        </form>

        <!-- Lien vers inscription -->
        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600 font-light">
                Pas encore de compte ? 
                <a href="index.php?ctl=connexion&action=inscription" class="text-red-500 hover:text-red-600 transition-colors">
                    Créer un compte
                </a>
            </p>
        </div>

        <!-- Lien retour accueil -->
        <div class="mt-4 text-center">
            <a href="index.php" class="text-sm text-gray-500 hover:text-red-500 font-light transition-colors">
                ← Retour à l'accueil
            </a>
        </div>
    </div>
</div>

