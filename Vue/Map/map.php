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
        <div class="bg-gradient-to-r from-red-500 to-red-600 p-6">
            <div class="flex items-start justify-between flex-wrap gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-1 tracking-tight">Carte Interactive</h1>
                    <p class="text-sm text-red-100 font-light">Découvrez et participez aux événements de votre ville</p>
                </div>
                
                <!-- Filtres dans le header -->
                <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-lg">
                    <h3 class="font-semibold text-sm text-gray-900 mb-4 tracking-wide">Filtres</h3>
                    <div class="flex flex-wrap gap-4">
                        <label class="flex items-center space-x-2 cursor-pointer hover:bg-gray-50 p-2 rounded-lg transition-colors">
                            <input type="checkbox" class="filter-checkbox w-4 h-4 text-red-500 border-gray-300 focus:ring-red-500 rounded" data-type="equipements" checked>
                            <span class="text-sm text-gray-700 font-medium">Équipements sportifs</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer hover:bg-gray-50 p-2 rounded-lg transition-colors">
                            <input type="checkbox" class="filter-checkbox w-4 h-4 text-red-500 border-gray-300 focus:ring-red-500 rounded" data-type="aires" checked>
                            <span class="text-sm text-gray-700 font-medium">Aires de jeux</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer hover:bg-gray-50 p-2 rounded-lg transition-colors">
                            <input type="checkbox" class="filter-checkbox w-4 h-4 text-red-500 border-gray-300 focus:ring-red-500 rounded" data-type="points" checked>
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
                        <h2 class="text-lg font-bold text-gray-900 tracking-tight">Activités</h2>
                        <button id="refreshBtn" type="button" class="px-4 py-2 rounded-lg bg-red-500 text-white font-semibold hover:bg-red-600 shadow-sm hover:shadow-md transition-all duration-200">
                            Actualiser
                        </button>
                    </div>
                    <input 
                        type="text" 
                        id="searchInput" 
                        placeholder="Rechercher une activité..." 
                        class="w-full px-4 py-2 border border-gray-300 focus:border-red-500 focus:ring-1 focus:ring-red-500 text-sm font-light"
                    >
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
            const participantsCount = isConnected ? (activity.participants ? activity.participants.length : 0) : '?';

            const popupContent = `
                <div class="p-3">
                    <h3 class="font-light text-base mb-2 text-gray-900">${activity.name}</h3>
                    <p class="text-xs text-gray-600 mb-2 font-light">${activity.address || activity.commune || ''}${activity.commune && activity.address ? ', ' + activity.commune : ''}</p>
                    <p class="text-xs text-gray-500 mb-3 font-light">${activity.description || ''}</p>
                    ${canParticipate ? `<p class="text-xs text-red-500 mb-2 font-light">${participantsCount} participant(s)</p>` : ''}
                    <div class="flex space-x-2 pt-2 border-t border-gray-200">
                        <button onclick="showActivityDetails('${activity.id}')" 
                                class="px-3 py-1.5 bg-white border-2 border-red-500 text-red-500 text-xs font-semibold rounded-lg hover:bg-red-500 hover:text-white shadow-sm hover:shadow-md transition-all duration-200">
                            Détails
                        </button>
                        ${canParticipate ? `
                        <a href="index.php?ctl=participation&action=participer&activity_type=${encodeURIComponent(activity.type)}&activity_id=${encodeURIComponent(activity.id)}&ActivityDescription=${encodeURIComponent((activity.name || '') + ' - ' + (activity.address || activity.commune || '') + (activity.commune && activity.address ? ', ' + activity.commune : ''))}" 
                                class="px-3 py-1.5 bg-red-500 text-white text-xs font-semibold rounded-lg hover:bg-red-600 shadow-sm hover:shadow-md transition-all duration-200 inline-block">
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
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();

    const filteredActivities = activities.filter(activity => 
        activity.name.toLowerCase().includes(searchTerm) ||
        activity.address.toLowerCase().includes(searchTerm) ||
        activity.description.toLowerCase().includes(searchTerm)
    );

    if (filteredActivities.length === 0) {
        listContainer.innerHTML = '<div class="text-center text-gray-500 py-8"><p class="text-sm font-light">Aucune activité trouvée</p></div>';
        return;
    }

    listContainer.innerHTML = filteredActivities.map(activity => {
        const canParticipate = isConnected;
        const participantsCount = isConnected ? (activity.participants ? activity.participants.length : 0) : '?';
        
        return `
        <div class="bg-white border border-gray-200 p-4 hover:border-red-500 transition-colors cursor-pointer" 
             onclick="showActivityDetails('${activity.id}')">
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
            <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                <span class="text-xs text-gray-500 font-light">
                    ${canParticipate ? `${participantsCount !== '?' ? participantsCount : 0} participant${participantsCount !== '?' && participantsCount > 1 ? 's' : ''}` : 'Connectez-vous pour voir les participants'}
                </span>
                ${canParticipate ? `
                <a href="index.php?ctl=participation&action=participer&activity_type=${encodeURIComponent(activity.type)}&activity_id=${encodeURIComponent(activity.id)}&ActivityDescription=${encodeURIComponent((activity.name || '') + ' - ' + (activity.address || '') + ', ' + (activity.commune || ''))}" 
                        onclick="event.stopPropagation();"
                        class="px-3 py-1 bg-red-500 text-white text-xs font-light hover:bg-red-600 transition-colors inline-block">
                    Participer
                </a>
                ` : ''}
            </div>
        </div>
    `;
    }).join('');
}

// Fonction pour afficher les détails d'une activité
function showActivityDetails(activityId) {
    const activity = activities.find(a => a.id === activityId);
    if (!activity) return;

    const canParticipate = isConnected;
    const canSeeParticipants = isConnected;
    const canInvite = isConnected;

    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center';
    modal.style.zIndex = '99999';
    modal.innerHTML = `
        <div class="bg-white rounded-2xl shadow-2xl border border-gray-200 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="p-8">
                <div class="flex justify-between items-start mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 tracking-tight">${activity.name}</h2>
                    <button onclick="this.closest('.fixed').remove()" 
                            class="text-gray-400 hover:text-gray-600 text-2xl font-light hover:bg-gray-100 rounded-full w-8 h-8 flex items-center justify-center transition-colors">&times;</button>
                </div>
                
                <div class="space-y-6">
                    <div class="border-b border-gray-200 pb-4">
                        <h3 class="font-semibold text-sm text-gray-700 mb-2 tracking-wide">📍 Adresse</h3>
                        <p class="text-sm text-gray-600 font-light">${activity.address}, ${activity.commune}</p>
                    </div>
                    
                    <div class="border-b border-gray-200 pb-4">
                        <h3 class="font-semibold text-sm text-gray-700 mb-2 tracking-wide">📝 Description</h3>
                        <p class="text-sm text-gray-600 font-light">${activity.description}</p>
                    </div>
                    
                    ${canSeeParticipants ? `
                    <div class="border-b border-gray-200 pb-4">
                        <h3 class="font-semibold text-sm text-gray-700 mb-3 tracking-wide">👥 Participants (${activity.participants ? activity.participants.length : 0})</h3>
                        <div class="space-y-2">
                            ${activity.participants && activity.participants.length > 0 ? activity.participants.map(p => `
                                <div class="flex items-center justify-between bg-gray-50 border border-gray-200 rounded-lg p-3 shadow-sm">
                                    <span class="text-sm font-medium text-gray-700">${p.name}</span>
                                    ${p.date ? `<span class="text-xs text-gray-500 font-light">${p.date}</span>` : ''}
                                </div>
                            `).join('') : '<p class="text-sm text-gray-500 font-light">Aucun participant pour le moment</p>'}
                        </div>
                    </div>
                    ` : '<p class="text-sm text-gray-500 font-light border-b border-gray-200 pb-4">Connectez-vous pour voir les participants</p>'}
                    
                    ${canParticipate ? `
                    <div class="flex space-x-3 pt-4">
                        <a href="index.php?ctl=participation&action=participer&activity_type=${encodeURIComponent(activity.type)}&activity_id=${encodeURIComponent(activity.id)}&ActivityDescription=${encodeURIComponent((activity.name || '') + ' - ' + (activity.address || '') + ', ' + (activity.commune || ''))}" 
                                class="flex-1 px-4 py-2 bg-red-500 text-white font-semibold rounded-lg hover:bg-red-600 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200 text-center">
                            Je participe
                        </a>
                        ${canInvite ? `
                        <a href="index.php?ctl=participation&action=inviter&activity_type=${encodeURIComponent(activity.type)}&activity_id=${encodeURIComponent(activity.id)}" 
                                class="flex-1 px-4 py-2 bg-white border-2 border-red-500 text-red-500 font-semibold rounded-lg hover:bg-red-500 hover:text-white shadow-sm hover:shadow-md transition-all duration-200 text-center">
                            Inviter des amis
                        </a>
                        ` : ''}
                    </div>
                    ` : ''}
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    map.setView([activity.lat, activity.lon], 15);
}

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
