<!-- Landing Page -->
<div class="min-h-screen bg-white flex items-center justify-center p-8">
    <div class="text-center max-w-3xl mx-auto">
        <!-- Titre principal -->
        <h1 class="text-5xl font-light text-gray-900 mb-6 tracking-tight">
            Découvrez Melun
        </h1>
        
        <!-- Sous-titre -->
        <p class="text-lg text-gray-600 mb-16 font-light">
            Explorez les activités, équipements sportifs et événements de votre ville sur une carte interactive
        </p>

        <!-- Bouton principal "Voir la map" -->
        <a href="index.php?ctl=map" 
           class="inline-block px-16 py-4 bg-red-500 text-white text-lg font-light tracking-wide hover:bg-red-600 transition-colors">
            Voir la map
        </a>

        <!-- Informations supplémentaires -->
        <div class="mt-24 grid grid-cols-1 md:grid-cols-3 gap-12">
            <div class="border border-gray-200 p-8 hover:border-red-500 transition-colors">
                <h3 class="text-lg font-light text-gray-900 mb-3">Équipements Sportifs</h3>
                <p class="text-sm text-gray-600 font-light">Découvrez tous les équipements sportifs de Melun</p>
            </div>
            
            <div class="border border-gray-200 p-8 hover:border-red-500 transition-colors">
                <h3 class="text-lg font-light text-gray-900 mb-3">Aires de Jeux</h3>
                <p class="text-sm text-gray-600 font-light">Trouvez les aires de jeux près de chez vous</p>
            </div>
            
            <div class="border border-gray-200 p-8 hover:border-red-500 transition-colors">
                <h3 class="text-lg font-light text-gray-900 mb-3">Points d'Intérêt</h3>
                <p class="text-sm text-gray-600 font-light">Explorez les lieux incontournables de la ville</p>
            </div>
        </div>

        <!-- Message pour utilisateurs non connectés -->
        <?php if (!isset($_SESSION['connect']) || $_SESSION['connect'] !== true): ?>
            <div class="mt-16 border-l-4 border-red-500 bg-white p-6 text-left">
                <p class="text-sm text-gray-600 font-light">
                    Créez un compte pour participer aux événements et inviter vos amis
                </p>
            </div>
        <?php endif; ?>
    </div>
</div>


