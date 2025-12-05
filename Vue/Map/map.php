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

<div class="min-h-screen bg-white p-6">
    <div class="max-w-7xl mx-auto bg-white border border-red-500">
        
        <!-- Header de la page -->
        <div class="bg-white border-b border-red-500 p-6">
            <div class="flex items-start justify-between flex-wrap gap-4">
                <div>
                    <h1 class="text-2xl font-light text-gray-900 mb-1 tracking-tight">Carte Interactive</h1>
                    <p class="text-sm text-gray-600 font-light">Découvrez et participez aux événements de votre ville</p>
                </div>
                
                <!-- Filtres dans le header -->
                <div class="bg-white border border-red-500 p-4">
                    <h3 class="font-light text-sm text-gray-900 mb-4 tracking-wide">Filtres</h3>
                    <div class="flex flex-wrap gap-4">
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" class="filter-checkbox w-4 h-4 text-red-500 border-gray-300 focus:ring-red-500" data-type="equipements" checked>
                            <span class="text-sm text-gray-700 font-light">Équipements sportifs</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" class="filter-checkbox w-4 h-4 text-red-500 border-gray-300 focus:ring-red-500" data-type="aires" checked>
                            <span class="text-sm text-gray-700 font-light">Aires de jeux</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" class="filter-checkbox w-4 h-4 text-red-500 border-gray-300 focus:ring-red-500" data-type="points" checked>
                            <span class="text-sm text-gray-700 font-light">Points d'intérêt</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" class="filter-checkbox w-4 h-4 text-red-500 border-gray-300 focus:ring-red-500" data-type="evenements" checked>
                            <span class="text-sm text-gray-700 font-light">Événements</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Container principal : Carte + Sidebar -->
        <div class="flex" style="height: calc(100vh - 20rem);">
            
            <!-- Carte OpenStreetMap -->
            <div class="flex-1 p-6" style="height: 100%; overflow: hidden;">
                <div id="map" class="border border-red-500 rounded" style="height: 100%;"></div>
            </div>

            <!-- Sidebar avec liste des activités -->
            <div class="w-96 bg-white border-l border-red-500 flex flex-col" style="height: 100%;">
                
                <!-- En-tête sidebar -->
                <div class="p-5 bg-white border-b border-red-500 flex-shrink-0">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-light text-gray-900 tracking-tight">Activités</h2>
                        <button id="refreshBtn" type="button" class="rounded-lg bg-transparent text-red-500 hover:bg-red-500 hover:text-white">
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
    })
};

// Fonction pour charger les données depuis les APIs
async function loadActivities() {
    const apis = [
        {
            url: 'https://data.melunvaldeseine.fr/api/explore/v2.1/catalog/datasets/equipements-sportifs/records?limit=-1',
            type: 'equipements'
        },
        {
            url: 'https://data.melunvaldeseine.fr/api/explore/v2.1/catalog/datasets/aires-de-jeux/records?limit=-1',
            type: 'aires'
        }
    ];

    activities = [];

    for (const api of apis) {
        try {
            console.log(`Chargement de ${api.type}...`);
            const response = await fetch(api.url);
            const data = await response.json();
            
            if (data.results && data.results.length > 0) {
                data.results.forEach(item => {
                    // La structure de l'API: { record: { id: ..., fields: { ... } } }
                    const record = item.record || {};
                    const fields = record.fields || item; // Fallback si pas de structure record
                    const recordId = record.id || item.recordid || null;
                    
                    // Pour équipements sportifs
                    if (api.type === 'equipements') {
                        // Utiliser l'ID de l'API si disponible, sinon créer un ID stable
                        let finalRecordId = recordId;
                        if (!finalRecordId) {
                            const name = fields.equip_nom || '';
                            const address = fields.adr_num_et_rue || '';
                            if (name || address) {
                                finalRecordId = `${name}_${address}`.replace(/[^a-zA-Z0-9]/g, '_').substring(0, 50);
                            } else {
                                finalRecordId = `equip_${activities.length}`;
                            }
                        }
                        
                        const activityId = `${api.type}_${finalRecordId}`;
                        const activity = {
                            id: activityId,
                            type: api.type,
                            name: fields.equip_nom || 'Nom non disponible',
                            address: fields.adr_num_et_rue || 'Adresse non disponible',
                            commune: fields.adr_commune || 'Melun',
                            lat: parseFloat(fields.equip_lat || fields.point_geo?.lat || (fields.point_geo && Array.isArray(fields.point_geo) ? fields.point_geo[0] : null) || 48.54),
                            lon: parseFloat(fields.equip_long || fields.point_geo?.lon || (fields.point_geo && Array.isArray(fields.point_geo) ? fields.point_geo[1] : null) || 2.66),
                            description: fields.equip_type || '',
                            participants: participationsData[activityId] || []
                        };
                        activities.push(activity);
                    }
                    // Pour aires de jeux
                    else if (api.type === 'aires') {
                        // Utiliser l'ID de l'API si disponible, sinon créer un ID stable
                        let finalRecordId = recordId;
                        if (!finalRecordId) {
                            const name = fields.libelle || '';
                            const address = fields.adresse || '';
                            if (name || address) {
                                finalRecordId = `${name}_${address}`.replace(/[^a-zA-Z0-9]/g, '_').substring(0, 50);
                            } else {
                                finalRecordId = `aire_${activities.length}`;
                            }
                        }
                        
                        const activityId = `${api.type}_${finalRecordId}`;
                        const activity = {
                            id: activityId,
                            type: api.type,
                            name: fields.libelle || 'Nom non disponible',
                            address: fields.adresse || 'Adresse non disponible',
                            commune: fields.commune || 'Melun',
                            lat: parseFloat(fields.geo_point_2d?.lat || (fields.geo_point_2d && Array.isArray(fields.geo_point_2d) ? fields.geo_point_2d[0] : null) || 48.54),
                            lon: parseFloat(fields.geo_point_2d?.lon || (fields.geo_point_2d && Array.isArray(fields.geo_point_2d) ? fields.geo_point_2d[1] : null) || 2.66),
                            description: fields.famille_eqpt || '',
                            participants: participationsData[activityId] || []
                        };
                        activities.push(activity);
                    }
                });
                console.log(`${api.type}: ${data.results.length} résultats chargés (total: ${data.total_count})`);
            }
        } catch (error) {
            console.error(`Erreur lors du chargement de ${api.type}:`, error);
        }
    }

    // Sauvegarder les activités dans la base de données
    saveActivitiesToDatabase();
    
    displayActivities();
    displayMarkers();
}

// Fonction pour sauvegarder les activités dans la base de données
async function saveActivitiesToDatabase() {
    if (activities.length === 0) return;
    
    try {
        const response = await fetch('index.php?ctl=activity', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ activities: activities })
        });
        
        const result = await response.json();
        if (result.success) {
            console.log(`Activités sauvegardées: ${result.saved} (erreurs: ${result.errors})`);
        }
    } catch (error) {
        console.error('Erreur lors de la sauvegarde des activités:', error);
    }
}

// Les participations sont maintenant gérées côté serveur via la base de données
// Plus besoin de charger via API

// Fonction pour afficher les marqueurs sur la carte
function displayMarkers() {
    markers.forEach(marker => map.removeLayer(marker));
    markers = [];

    const activeFilters = Array.from(document.querySelectorAll('.filter-checkbox:checked'))
        .map(cb => cb.dataset.type);

    activities.forEach(activity => {
        if (activeFilters.includes(activity.type)) {
            const marker = L.marker([activity.lat, activity.lon], {
                icon: iconTypes[activity.type] || iconTypes.points
            }).addTo(map);

            const canParticipate = isConnected;
            const participantsCount = isConnected ? activity.participants.length : '?';

            const popupContent = `
                <div class="p-3">
                    <h3 class="font-light text-base mb-2 text-gray-900">${activity.name}</h3>
                    <p class="text-xs text-gray-600 mb-2 font-light">${activity.address}, ${activity.commune}</p>
                    <p class="text-xs text-gray-500 mb-3 font-light">${activity.description}</p>
                    ${canParticipate ? `<p class="text-xs text-red-500 mb-2 font-light">${participantsCount} participant(s)</p>` : ''}
                    <div class="flex space-x-2 pt-2 border-t border-gray-200">
                        <button onclick="showActivityDetails('${activity.id}')" 
                                class="px-3 py-1 bg-white border border-red-500 text-red-500 text-xs font-light hover:bg-red-500 hover:text-white transition-colors">
                            Détails
                        </button>
                        ${canParticipate ? `
                        <a href="index.php?ctl=participation&action=participer&activity_id=${encodeURIComponent(activity.id)}&ActivityDescription=${encodeURIComponent((activity.name || '') + ' - ' + (activity.address || '') + ', ' + (activity.commune || ''))}" 
                                class="px-3 py-1 bg-red-500 text-white text-xs font-light hover:bg-red-600 transition-colors inline-block">
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
        const participantsCount = isConnected ? activity.participants.length : '?';
        
        return `
        <div class="bg-white border border-gray-200 p-4 hover:border-red-500 transition-colors cursor-pointer" 
             onclick="showActivityDetails('${activity.id}')">
            <div class="flex justify-between items-start mb-3">
                <h3 class="font-light text-gray-900 text-sm">${activity.name}</h3>
                <span class="px-2 py-1 text-xs border border-red-500 text-red-500 font-light">
                    ${activity.type}
                </span>
            </div>
            <p class="text-xs text-gray-600 mb-2 font-light">${activity.address}</p>
            <p class="text-xs text-gray-500 mb-3 font-light">${activity.description}</p>
            <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                <span class="text-xs text-gray-500 font-light">
                    ${canParticipate ? `${participantsCount} participant${participantsCount > 1 ? 's' : ''}` : 'Connectez-vous pour voir les participants'}
                </span>
                ${canParticipate ? `
                <a href="index.php?ctl=participation&action=participer&activity_id=${activity.id}" 
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
        <div class="bg-white border border-red-500 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="p-8">
                <div class="flex justify-between items-start mb-6">
                    <h2 class="text-xl font-light text-gray-900 tracking-tight">${activity.name}</h2>
                    <button onclick="this.closest('.fixed').remove()" 
                            class="text-gray-400 hover:text-gray-600 text-2xl font-light">&times;</button>
                </div>
                
                <div class="space-y-6">
                    <div class="border-b border-gray-200 pb-4">
                        <h3 class="font-light text-sm text-gray-700 mb-2 tracking-wide">Adresse</h3>
                        <p class="text-sm text-gray-600 font-light">${activity.address}, ${activity.commune}</p>
                    </div>
                    
                    <div class="border-b border-gray-200 pb-4">
                        <h3 class="font-light text-sm text-gray-700 mb-2 tracking-wide">Description</h3>
                        <p class="text-sm text-gray-600 font-light">${activity.description}</p>
                    </div>
                    
                    ${canSeeParticipants ? `
                    <div class="border-b border-gray-200 pb-4">
                        <h3 class="font-light text-sm text-gray-700 mb-3 tracking-wide">Participants (${activity.participants.length})</h3>
                        <div class="space-y-2">
                            ${activity.participants.map(p => `
                                <div class="flex items-center justify-between border border-gray-200 p-3">
                                    <span class="text-sm font-light text-gray-700">${p.name}</span>
                                    ${p.date ? `<span class="text-xs text-gray-500 font-light">${p.date}</span>` : ''}
                                </div>
                            `).join('')}
                            ${activity.participants.length === 0 ? '<p class="text-sm text-gray-500 font-light">Aucun participant pour le moment</p>' : ''}
                        </div>
                    </div>
                    ` : '<p class="text-sm text-gray-500 font-light border-b border-gray-200 pb-4">Connectez-vous pour voir les participants</p>'}
                    
                    ${canParticipate ? `
                    <div class="flex space-x-3 pt-4">
                        <a href="index.php?ctl=participation&action=participer&activity_id=${activity.id}" 
                                class="flex-1 px-4 py-2 bg-red-500 text-white font-light hover:bg-red-600 transition-colors text-center">
                            Je participe
                        </a>
                        ${canInvite ? `
                        <a href="index.php?ctl=participation&action=inviter&activity_id=${activity.id}" 
                                class="flex-1 px-4 py-2 bg-white border border-red-500 text-red-500 font-light hover:bg-red-500 hover:text-white transition-colors text-center">
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
