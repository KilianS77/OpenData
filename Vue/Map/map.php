<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />

<style>
    #map { 
        width: 100%; 
        height: 100%; 
        min-height: 600px;
        position: relative;
    }
    
    /* Empêcher le scroll sur la carte */
    .leaflet-container {
        height: 100% !important;
        width: 100% !important;
    }
    
    /* Z-index élevé pour les popups Leaflet */
    .leaflet-popup {
        z-index: 10000 !important;
    }
    
    .leaflet-popup-content-wrapper {
        z-index: 10001 !important;
        position: relative;
    }
    
    .leaflet-popup-content {
        z-index: 10002 !important;
        position: relative;
    }
    
    .custom-popup {
        z-index: 10000 !important;
    }
    
    /* Z-index élevé pour les boutons dans les popups */
    .leaflet-popup-content button {
        position: relative;
        z-index: 10010 !important;
    }
    
    /* Z-index élevé pour les modales */
    .fixed {
        z-index: 99999 !important;
    }
    
    /* S'assurer que les panes Leaflet ont un z-index inférieur */
    .leaflet-pane {
        z-index: 400;
    }
    
    .leaflet-map-pane {
        z-index: 400;
    }
    
    .leaflet-tile-pane {
        z-index: 200;
    }
    
    .leaflet-overlay-pane {
        z-index: 400;
    }
    
    .leaflet-shadow-pane {
        z-index: 500;
    }
    
    .leaflet-marker-pane {
        z-index: 600;
    }
    
    .leaflet-tooltip-pane {
        z-index: 650;
    }
    
    .leaflet-popup-pane {
        z-index: 10000 !important;
    }
</style>

<div class="min-h-screen bg-gradient-to-br from-gray-50 to-white p-6">
    <div class="max-w-7xl mx-auto bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        
        <!-- Header de la page -->
        <div class="bg-gradient-to-r from-red-500 to-red-600 p-6 animate-fade-in-down">
            <div class="flex items-start justify-between flex-wrap gap-4">
                <div>
                    <div class="flex items-center space-x-3 mb-2">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                        </svg>
                        <h1 class="text-3xl font-bold text-white tracking-tight">Carte Interactive</h1>
                    </div>
                    <p class="text-sm text-red-100 font-light">Découvrez et participez aux événements de votre ville</p>
                </div>
                
                <!-- Filtres dans le header -->
                <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-lg animate-scale-in">
                    <div class="flex items-center space-x-2 mb-4">
                        <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                        <h3 class="font-semibold text-sm text-gray-900 tracking-wide">Filtres</h3>
                    </div>
                    <div class="flex flex-wrap gap-4">
                        <label class="flex items-center space-x-2 cursor-pointer hover:bg-gray-50 p-2 rounded-lg transition-all duration-200 hover-lift">
                            <input type="checkbox" class="filter-checkbox w-4 h-4 text-red-500 border-gray-300 focus:ring-red-500 rounded" data-type="equipements" checked>
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            <span class="text-sm text-gray-700 font-medium">Équipements sportifs</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer hover:bg-gray-50 p-2 rounded-lg transition-all duration-200 hover-lift">
                            <input type="checkbox" class="filter-checkbox w-4 h-4 text-red-500 border-gray-300 focus:ring-red-500 rounded" data-type="aires" checked>
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="text-sm text-gray-700 font-medium">Aires de jeux</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer hover:bg-gray-50 p-2 rounded-lg transition-all duration-200 hover-lift">
                            <input type="checkbox" class="filter-checkbox w-4 h-4 text-red-500 border-gray-300 focus:ring-red-500 rounded" data-type="points" checked>
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span class="text-sm text-gray-700 font-medium">Points d'intérêt</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Container principal : Carte + Sidebar -->
        <div class="flex" style="height: calc(100vh - 20rem);">
            
            <!-- Carte OpenStreetMap -->
            <div class="flex-1 p-6" style="height: 100%; overflow: hidden;">
                <div id="map" class="border-2 border-gray-200 rounded-xl shadow-lg" style="height: 100%;"></div>
            </div>

            <!-- Sidebar avec liste des activités -->
            <div class="w-96 bg-white border-l border-gray-200 flex flex-col shadow-lg" style="height: 100%;">
                
                <!-- En-tête sidebar -->
                <div class="p-5 bg-gray-50 border-b border-gray-200 flex-shrink-0">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                            <h2 class="text-lg font-bold text-gray-900 tracking-tight">Activités</h2>
                        </div>
                        <button id="refreshBtn" type="button" class="px-4 py-2 rounded-lg bg-red-500 text-white font-semibold hover:bg-red-600 shadow-sm hover:shadow-md transition-all duration-300 hover-lift flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            <span>Actualiser</span>
                        </button>
                    </div>
                    <div class="relative">
                        <svg class="w-4 h-4 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input 
                            type="text" 
                            id="searchInput" 
                            placeholder="Rechercher une activité..." 
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 focus:border-red-500 focus:ring-1 focus:ring-red-500 text-sm font-light rounded-lg transition-all duration-200"
                        >
                    </div>
                </div>

                <!-- Liste des activités (scrollable) -->
                <div id="activitiesList" class="flex-1 overflow-y-auto p-4 space-y-3" style="min-height: 0;">
                    <div class="text-center text-gray-500 py-8 text-sm font-light">
                        <p>Chargement des activités...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>

<script>
// Variables globales
const isConnected = <?php echo (isset($_SESSION['connect']) && $_SESSION['connect'] === true) ? 'true' : 'false'; ?>;
const userId = <?php echo isset($_SESSION['user_id']) ? json_encode($_SESSION['user_id']) : 'null'; ?>;
const userName = <?php echo isset($_SESSION['user_name']) ? json_encode($_SESSION['user_name']) : 'null'; ?>;
const participationsData = {}; // Données de participation (sera chargé depuis le serveur si nécessaire)

// Fonction pour générer un hash MD5 (implémentation simple)
// Note: Pour une correspondance exacte avec PHP md5(), il faudrait utiliser une bibliothèque MD5 complète
// Cette fonction génère un hash similaire pour la cohérence des IDs
function md5(str) {
    // Implémentation MD5 simplifiée - génère un hash de 32 caractères
    let hash = 0;
    if (str.length === 0) return hash.toString(16).padStart(32, '0');
    for (let i = 0; i < str.length; i++) {
        const char = str.charCodeAt(i);
        hash = ((hash << 5) - hash) + char;
        hash = hash & hash; // Convert to 32bit integer
    }
    // Convertir en hexadécimal et prendre 32 caractères
    return Math.abs(hash).toString(16).padStart(32, '0').substring(0, 32);
}

let markers = [];
let activities = [];

// Initialisation de la carte (centrée sur Melun)
var map = L.map('map').setView([48.54, 2.66], 13);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
}).addTo(map);

// Icônes personnalisées pour les différents types
const iconTypes = {
    equipements: L.icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-blue.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34],
        shadowSize: [41, 41]
    }),
    aires: L.icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-green.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34],
        shadowSize: [41, 41]
    }),
    points: L.icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34],
        shadowSize: [41, 41]
    }),
    evenements: L.icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-violet.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34],
        shadowSize: [41, 41]
    }),
    manifestations: L.icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-orange.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34],
        shadowSize: [41, 41]
    }),
    agenda: L.icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-violet.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34],
        shadowSize: [41, 41]
    })
};

// Fonction pour synchroniser les activités depuis l'API vers la base de données
async function syncActivitiesFromAPI() {
    try {
        console.log('Synchronisation des activités depuis l\'API...');
        const response = await fetch('index.php?ctl=activity&action=sync', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            }
        });
        
        if (!response.ok) {
            console.error(`Erreur HTTP ${response.status} lors de la synchronisation`);
            const text = await response.text();
            console.error('Réponse:', text);
            return { success: false, error: `HTTP ${response.status}` };
        }
        
        let result;
        try {
            result = await response.json();
        } catch (e) {
            const text = await response.text();
            console.error('❌ Erreur parsing JSON:', e);
            console.error('Réponse brute:', text);
            return { success: false, error: 'Erreur parsing JSON: ' + e.message };
        }
        
        if (result.success) {
            console.log(`✅ Synchronisation terminée: ${result.saved} créées, ${result.updated} mises à jour, ${result.errors} erreurs`);
            if (result.total_in_db !== undefined) {
                console.log(`📊 Total d'activités en base: ${result.total_in_db}`);
            }
        } else {
            console.error('❌ Échec de la synchronisation:', result);
        }
        return result;
    } catch (error) {
        console.error('❌ Erreur lors de la synchronisation:', error);
        return { success: false, error: error.message };
    }
}

// Fonction pour charger les activités depuis la base de données
async function loadActivitiesFromDatabase() {
    try {
        console.log('Chargement des activités depuis la base de données...');
        const response = await fetch('index.php?ctl=activity&action=get_activities');
        if (!response.ok) {
            console.error(`Erreur HTTP ${response.status}`);
            return [];
        }
        const data = await response.json();
        if (data.success && data.activities) {
            console.log(`✅ ${data.activities.length} activités chargées depuis la base de données`);
            return data.activities;
        }
        return [];
    } catch (error) {
        console.error('Erreur lors du chargement depuis la base:', error);
        return [];
    }
}

// Fonction pour charger les données depuis les APIs
async function loadActivities() {
    // D'abord synchroniser depuis l'API vers la base de données
    await syncActivitiesFromAPI();
    
    // Ensuite charger depuis la base de données pour avoir les vrais IDs
    activities = await loadActivitiesFromDatabase();
    
    console.log(`Total activités chargées: ${activities.length}`);
    displayActivities();
    displayMarkers();
}

// Les participations sont maintenant gérées côté serveur via la base de données
// La synchronisation des activités se fait automatiquement au chargement de la page

// Fonction pour afficher les marqueurs sur la carte
function displayMarkers() {
    console.log('displayMarkers appelé, activités:', activities.length);
    markers.forEach(marker => map.removeLayer(marker));
    markers = [];

    const activeFilters = Array.from(document.querySelectorAll('.filter-checkbox:checked'))
        .map(cb => cb.dataset.type);
    
    console.log('Filtres actifs:', activeFilters);

    activities.forEach(activity => {
        // Mapper les types de la base de données vers les types de filtres
        let filterType, iconType;
        if (activity.type === 'aires_jeux') {
            filterType = 'aires';
            iconType = 'aires';
        } else if (activity.type === 'equipements_sportifs') {
            filterType = 'equipements';
            iconType = 'equipements';
        } else if (activity.type === 'points_interets') {
            filterType = 'points';
            iconType = 'points';
        } else {
            filterType = activity.type;
            iconType = 'points';
        }
        
        console.log('Traitement activité:', activity.id, 'type:', activity.type, 'filterType:', filterType, 'iconType:', iconType, 'filtres:', activeFilters, 'match:', activeFilters.includes(filterType), 'coords:', activity.lat, activity.lon);
        if (activeFilters.includes(filterType) && activity.lat && activity.lon && !isNaN(activity.lat) && !isNaN(activity.lon)) {
            const marker = L.marker([activity.lat, activity.lon], {
                icon: iconTypes[iconType] || iconTypes.points
            }).addTo(map);

            const canParticipate = isConnected;

            const popupContent = `
                <div class="p-3" style="min-width: 200px;">
                    <h3 class="font-semibold text-base mb-2 text-gray-900">${activity.name}</h3>
                    <p class="text-xs text-gray-600 mb-2 font-light">${activity.address || activity.commune || ''}${activity.commune && activity.address ? ', ' + activity.commune : ''}</p>
                    <p class="text-xs text-gray-500 mb-3 font-light line-clamp-2">${activity.description || ''}</p>
                    <div class="flex space-x-2 pt-2 border-t border-gray-200">
                        <button onclick="window.showActivityDetails('${activity.type}', ${activity.id})" 
                                class="flex-1 px-3 py-2 bg-white border-2 border-red-500 text-red-500 text-xs font-semibold rounded-lg hover:bg-red-500 hover:text-white shadow-sm hover:shadow-md transition-all duration-300 cursor-pointer">
                            Détails
                        </button>
                        ${canParticipate ? `
                        <a href="index.php?ctl=participation&action=participer&activity_type=${encodeURIComponent(activity.type)}&activity_id=${encodeURIComponent(activity.id)}&ActivityDescription=${encodeURIComponent((activity.name || '') + ' - ' + (activity.address || activity.commune || '') + (activity.commune && activity.address ? ', ' + activity.commune : ''))}" 
                                class="flex-1 px-3 py-2 bg-red-500 text-white text-xs font-semibold rounded-lg hover:bg-red-600 shadow-sm hover:shadow-md transition-all duration-300 text-center" style="color: white !important;">
                            Participer
                        </a>
                        ` : ''}
                    </div>
                </div>
            `;

            marker.bindPopup(popupContent, {
                maxWidth: 300,
                className: 'custom-popup'
            });
            marker.activityId = activity.id;
            markers.push(marker);
        }
    });
}

// Fonction pour afficher la liste des activités
function displayActivities() {
    const listContainer = document.getElementById('activitiesList');
    const searchInput = document.getElementById('searchInput');
    if (!searchInput) return;
    
    const searchTerm = (searchInput.value || '').toLowerCase();

    const filteredActivities = activities.filter(activity => {
        const name = (activity.name || '').toLowerCase();
        const address = (activity.address || '').toLowerCase();
        const description = (activity.description || '').toLowerCase();
        return name.includes(searchTerm) || address.includes(searchTerm) || description.includes(searchTerm);
    });

    if (filteredActivities.length === 0) {
        listContainer.innerHTML = '<div class="text-center text-gray-500 py-8"><p class="text-sm font-light">Aucune activité trouvée</p></div>';
        return;
    }

    listContainer.innerHTML = filteredActivities.map(activity => {
        const canParticipate = isConnected;
        
        return `
        <div class="bg-white border border-gray-200 p-4 hover:border-red-500 transition-colors cursor-pointer" 
             onclick="window.showActivityDetails('${activity.type}', ${activity.id})">
            <div class="flex justify-between items-start mb-3">
                <h3 class="font-semibold text-gray-900 text-sm">${activity.name}</h3>
                <span class="px-3 py-1.5 text-xs font-semibold bg-red-100 text-red-700 rounded-full">
                    ${activity.type === 'aires_jeux' ? 'Aire de jeux' : 
                      activity.type === 'equipements_sportifs' ? 'Équipement sportif' : 
                      activity.type === 'points_interets' ? 'Point d\'intérêt' : activity.type}
                </span>
            </div>
            <p class="text-xs text-gray-600 mb-2 font-light">${activity.address}</p>
            <p class="text-xs text-gray-500 mb-3 font-light">${activity.description}</p>
            <div class="flex items-center justify-end pt-2 border-t border-gray-100">
                ${canParticipate ? `
                <a href="index.php?ctl=participation&action=participer&activity_type=${encodeURIComponent(activity.type)}&activity_id=${encodeURIComponent(activity.id)}&ActivityDescription=${encodeURIComponent((activity.name || '') + ' - ' + (activity.address || '') + ', ' + (activity.commune || ''))}" 
                        onclick="event.stopPropagation();"
                        class="px-4 py-2 bg-red-500 text-white text-xs font-semibold rounded-lg hover:bg-red-600 shadow-sm hover:shadow-md transition-all duration-300 inline-block"
                        style="color: white !important;">
                    Participer
                </a>
                ` : ''}
            </div>
        </div>
    `;
    }).join('');
}

// Fonction pour afficher les détails d'une activité (accessible globalement)
window.showActivityDetails = function(activityType, activityId) {
    console.log('showActivityDetails appelé avec Type:', activityType, 'ID:', activityId);
    console.log('Activités disponibles:', activities.length);
    
    // Convertir l'ID en nombre si nécessaire pour la comparaison
    const idToFind = typeof activityId === 'string' ? parseInt(activityId) : activityId;
    
    // Rechercher par type ET ID pour éviter les conflits entre tables
    const activity = activities.find(a => {
        const aId = typeof a.id === 'string' ? parseInt(a.id) : a.id;
        return a.type === activityType && (aId === idToFind || a.id == activityId);
    });
    
    console.log('Activité trouvée:', activity);
    
    if (!activity) {
        console.error('Activité non trouvée pour Type:', activityType, 'ID:', activityId);
        console.log('Activités disponibles:', activities.map(a => ({type: a.type, id: a.id, name: a.name})));
        alert('Activité non trouvée');
        return;
    }

    const canParticipate = isConnected;

    // Fermer les modales existantes
    const existingModals = document.querySelectorAll('.activity-modal');
    existingModals.forEach(m => m.remove());
    
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center activity-modal';
    modal.style.zIndex = '99999';
    
    // Fermer la modale en cliquant sur le fond
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.remove();
        }
    });
    
    // Préparer l'affichage de l'image si disponible
    const imageSection = activity.photo && activity.photo.trim() !== '' ? `
        <div class="mb-6" id="image-container">
            <img src="${activity.photo}" 
                 alt="${activity.name}" 
                 class="w-full h-64 object-cover rounded-xl shadow-lg border border-gray-200"
                 onerror="const container = document.getElementById('image-container'); if(container) container.style.display='none';">
            ${activity.credit_photo ? `<p class="text-xs text-gray-500 mt-2 font-light text-right" id="credit-photo">${activity.credit_photo}</p>` : ''}
        </div>
    ` : '';
    
    modal.innerHTML = `
        <div class="bg-white rounded-2xl shadow-2xl border border-gray-200 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="p-8">
                <div class="flex justify-between items-start mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 tracking-tight">${activity.name}</h2>
                    <button onclick="this.closest('.activity-modal').remove()" 
                            class="text-gray-400 hover:text-gray-600 text-2xl font-light hover:bg-gray-100 rounded-full w-8 h-8 flex items-center justify-center transition-colors">&times;</button>
                </div>
                
                ${imageSection}
                
                <div class="space-y-6">
                    <div class="border-b border-gray-200 pb-4">
                        <h3 class="font-semibold text-sm text-gray-700 mb-2 tracking-wide flex items-center space-x-2">
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span>Adresse</span>
                        </h3>
                        <p class="text-sm text-gray-600 font-light">${activity.address}, ${activity.commune}</p>
                    </div>
                    
                    <div class="border-b border-gray-200 pb-4">
                        <h3 class="font-semibold text-sm text-gray-700 mb-2 tracking-wide flex items-center space-x-2">
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span>Description</span>
                        </h3>
                        <p class="text-sm text-gray-600 font-light">${activity.description || 'Aucune description disponible'}</p>
                    </div>
                    
                    ${canParticipate ? `
                    <div class="flex space-x-3 pt-4">
                        <a href="index.php?ctl=participation&action=participer&activity_type=${encodeURIComponent(activity.type)}&activity_id=${encodeURIComponent(activity.id)}&ActivityDescription=${encodeURIComponent((activity.name || '') + ' - ' + (activity.address || '') + ', ' + (activity.commune || ''))}" 
                                class="flex-1 px-4 py-2 bg-red-500 text-white font-semibold rounded-lg hover:bg-red-600 shadow-md hover:shadow-lg transform hover:-translate-y-1 transition-all duration-300 text-center hover-lift ripple">
                            <span class="flex items-center justify-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                <span>Je participe</span>
                            </span>
                        </a>
                    </div>
                    ` : ''}
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    map.setView([activity.lat, activity.lon], 15);
};

// Les fonctions participateActivity et inviteFriends ont été supprimées
// Redirection vers les pages dédiées via les liens dans les popups et modales

// Événements
document.getElementById('searchInput').addEventListener('input', displayActivities);
document.getElementById('refreshBtn').addEventListener('click', loadActivities);
document.querySelectorAll('.filter-checkbox').forEach(cb => {
    cb.addEventListener('change', displayMarkers);
});

// Charger les activités au démarrage
loadActivities();
</script>
