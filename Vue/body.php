<!-- Landing Page -->
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-white flex items-center justify-center p-8">
    <div class="text-center max-w-4xl mx-auto">
        <!-- Titre principal -->
        <h1 class="text-6xl font-bold text-gray-900 mb-6 tracking-tight">
            Découvrez Melun
        </h1>
        
        <!-- Sous-titre -->
        <p class="text-xl text-gray-600 mb-12 font-light">
            Explorez les activités, équipements sportifs et événements de votre ville sur une carte interactive
        </p>

        <!-- Bouton principal "Voir la map" -->
        <a href="index.php?ctl=map" 
           class="inline-block px-12 py-4 bg-red-500 text-white text-lg font-semibold tracking-wide rounded-lg shadow-lg hover:bg-red-600 hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
            Voir la map
        </a>

        <!-- Informations supplémentaires -->
        <div class="mt-24 grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white border border-gray-200 rounded-xl p-8 shadow-sm hover:shadow-md hover:border-red-300 transition-all duration-200">
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mb-4 mx-auto">
                    <span class="text-2xl">🏃</span>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Équipements Sportifs</h3>
                <p class="text-sm text-gray-600 font-light">Découvrez tous les équipements sportifs de Melun</p>
            </div>
            
            <div class="bg-white border border-gray-200 rounded-xl p-8 shadow-sm hover:shadow-md hover:border-red-300 transition-all duration-200">
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mb-4 mx-auto">
                    <span class="text-2xl">🎮</span>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Aires de Jeux</h3>
                <p class="text-sm text-gray-600 font-light">Trouvez les aires de jeux près de chez vous</p>
            </div>
            
            <div class="bg-white border border-gray-200 rounded-xl p-8 shadow-sm hover:shadow-md hover:border-red-300 transition-all duration-200">
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mb-4 mx-auto">
                    <span class="text-2xl">📍</span>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Points d'Intérêt</h3>
                <p class="text-sm text-gray-600 font-light">Explorez les lieux incontournables de la ville</p>
            </div>
        </div>

        <!-- Message pour utilisateurs non connectés -->
        <?php if (!isset($_SESSION['connect']) || $_SESSION['connect'] !== true): ?>
            <div class="mt-16 bg-blue-50 border-l-4 border-blue-400 rounded-r-lg p-6 text-left shadow-sm">
                <p class="text-sm text-blue-800 font-medium">
                    💡 Créez un compte pour participer aux événements et inviter vos amis
                </p>
            </div>
        <?php endif; ?>
    </div>
</div>


