<!-- Landing Page -->
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-50 flex items-center justify-center p-8 relative overflow-hidden">
    <!-- Background animation avec Three.js -->
    <canvas id="background-canvas" class="absolute inset-0 w-full h-full opacity-30"></canvas>
    
    <div class="text-center max-w-4xl mx-auto relative z-10">
        <!-- Titre principal -->
        <h1 class="text-6xl font-bold text-gray-900 mb-6 tracking-tight animate-fade-in-up gradient-text">
            Découvrez Melun
        </h1>
        
        <!-- Sous-titre -->
        <p class="text-xl text-gray-600 mb-12 font-light animate-fade-in-up stagger-1">
            Explorez les activités, équipements sportifs et événements de votre ville sur une carte interactive
        </p>

        <!-- Bouton principal "Voir la map" -->
        <a href="index.php?ctl=map" 
           class="inline-block px-12 py-4 bg-red-500 text-white text-lg font-semibold tracking-wide rounded-lg shadow-lg hover:bg-red-600 hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300 hover-lift ripple animate-fade-in-up stagger-2">
            <span class="flex items-center justify-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                </svg>
                <span>Voir la map</span>
            </span>
        </a>

        <!-- Informations supplémentaires -->
        <div class="mt-24 grid grid-cols-1 md:grid-cols-3 gap-8 observe-on-scroll">
            <div class="bg-white border border-gray-200 rounded-xl p-8 shadow-sm hover:shadow-lg hover:border-red-300 transition-all duration-300 card-hover card-animate">
                <div class="w-16 h-16 bg-gradient-to-br from-red-100 to-red-200 rounded-xl flex items-center justify-center mb-4 mx-auto animate-float">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Équipements Sportifs</h3>
                <p class="text-sm text-gray-600 font-light">Découvrez tous les équipements sportifs de Melun</p>
            </div>
            
            <div class="bg-white border border-gray-200 rounded-xl p-8 shadow-sm hover:shadow-lg hover:border-red-300 transition-all duration-300 card-hover card-animate">
                <div class="w-16 h-16 bg-gradient-to-br from-red-100 to-red-200 rounded-xl flex items-center justify-center mb-4 mx-auto animate-float stagger-1">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Aires de Jeux</h3>
                <p class="text-sm text-gray-600 font-light">Trouvez les aires de jeux près de chez vous</p>
            </div>
            
            <div class="bg-white border border-gray-200 rounded-xl p-8 shadow-sm hover:shadow-lg hover:border-red-300 transition-all duration-300 card-hover card-animate">
                <div class="w-16 h-16 bg-gradient-to-br from-red-100 to-red-200 rounded-xl flex items-center justify-center mb-4 mx-auto animate-float stagger-2">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Points d'Intérêt</h3>
                <p class="text-sm text-gray-600 font-light">Explorez les lieux incontournables de la ville</p>
            </div>
        </div>

        <!-- Message pour utilisateurs non connectés -->
        <?php if (!isset($_SESSION['connect']) || $_SESSION['connect'] !== true): ?>
            <div class="mt-16 bg-blue-50 border-l-4 border-blue-400 rounded-r-lg p-6 text-left shadow-sm animate-fade-in-up stagger-3">
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-sm text-blue-800 font-medium">
                        Créez un compte pour participer aux événements et inviter vos amis
                    </p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Three.js pour animation de fond -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<script>
// Animation de fond subtile avec Three.js
document.addEventListener('DOMContentLoaded', () => {
    const canvas = document.getElementById('background-canvas');
    if (!canvas) return;
    
    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
    const renderer = new THREE.WebGLRenderer({ canvas: canvas, alpha: true, antialias: true });
    renderer.setSize(window.innerWidth, window.innerHeight);
    renderer.setPixelRatio(window.devicePixelRatio);
    
    // Créer des particules flottantes
    const particlesGeometry = new THREE.BufferGeometry();
    const particlesCount = 50;
    const posArray = new Float32Array(particlesCount * 3);
    
    for (let i = 0; i < particlesCount * 3; i++) {
        posArray[i] = (Math.random() - 0.5) * 20;
    }
    
    particlesGeometry.setAttribute('position', new THREE.BufferAttribute(posArray, 3));
    
    const particlesMaterial = new THREE.PointsMaterial({
        size: 0.05,
        color: 0xef4444,
        transparent: true,
        opacity: 0.3
    });
    
    const particlesMesh = new THREE.Points(particlesGeometry, particlesMaterial);
    scene.add(particlesMesh);
    
    camera.position.z = 5;
    
    function animate() {
        requestAnimationFrame(animate);
        particlesMesh.rotation.y += 0.001;
        particlesMesh.rotation.x += 0.0005;
        renderer.render(scene, camera);
    }
    
    animate();
    
    window.addEventListener('resize', () => {
        camera.aspect = window.innerWidth / window.innerHeight;
        camera.updateProjectionMatrix();
        renderer.setSize(window.innerWidth, window.innerHeight);
    });
});
</script>


