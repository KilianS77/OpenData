<div class="min-h-screen bg-gradient-to-br from-gray-50 to-white p-6">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Mes participations</h1>
            </div>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg text-red-700 text-sm shadow-sm">
                    <?php 
                    echo $_SESSION['error'];
                    unset($_SESSION['error']);
                    ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['success'])): ?>
                <div class="mb-4 p-4 bg-green-50 border-l-4 border-green-500 rounded-r-lg text-green-700 text-sm shadow-sm">
                    <?php 
                    echo $_SESSION['success'];
                    unset($_SESSION['success']);
                    ?>
                </div>
            <?php endif; ?>
            
            <?php if (empty($participations)): ?>
                <div class="text-center py-12 bg-white border border-gray-200 rounded-xl shadow-sm">
                    <p class="text-gray-500 font-light mb-4">Vous n'avez aucune participation pour le moment</p>
                    <a href="index.php?ctl=map" class="inline-block px-6 py-3 bg-red-500 text-white font-semibold rounded-lg shadow-md hover:bg-red-600 hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200">
                        Découvrir les activités
                    </a>
                </div>
            <?php else: ?>
                <div class="space-y-4">
                    <?php 
                    $cardIndex = 0;
                    $gradients = [
                        'from-red-500 to-orange-500',
                        'from-blue-500 to-purple-500',
                        'from-green-500 to-emerald-500',
                        'from-pink-500 to-rose-500',
                        'from-indigo-500 to-purple-500',
                        'from-teal-500 to-blue-500'
                    ];
                    foreach ($participations as $participation): 
                        $gradient = $gradients[$cardIndex % count($gradients)];
                        $cardIndex++;
                    ?>
                        <div class="group bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-md hover:shadow-2xl transition-all duration-500 card-hover card-animate observe-on-scroll transform hover:scale-[1.02]">
                            <!-- Header avec gradient -->
                            <div class="bg-gradient-to-r <?php echo $gradient; ?> p-4 text-white relative overflow-hidden">
                                <div class="absolute top-0 right-0 w-32 h-32 bg-white opacity-10 rounded-full -mr-16 -mt-16"></div>
                                <div class="absolute bottom-0 left-0 w-24 h-24 bg-white opacity-10 rounded-full -ml-12 -mb-12"></div>
                                <div class="relative z-10 flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center backdrop-blur-sm">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-bold text-lg">Ma participation</p>
                                            <p class="text-xs text-white text-opacity-80"><?php echo htmlspecialchars($participation['activity_type']); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="p-6">
                                <!-- Informations de l'activité -->
                                <h3 class="text-xl font-bold text-gray-900 mb-4 group-hover:text-red-600 transition-colors">
                                    <?php echo htmlspecialchars($participation['activity_name'] ?? $participation['activity_description']); ?>
                                </h3>
                                
                                <?php if (!empty($participation['activity_address'])): ?>
                                    <p class="text-sm text-gray-600 font-light mb-4 flex items-center space-x-2">
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <span><?php echo htmlspecialchars($participation['activity_address']); ?></span>
                                    </p>
                                <?php endif; ?>
                                
                                <!-- Date et heure -->
                                <div class="flex items-center flex-wrap gap-4 text-sm text-gray-600 font-medium mb-6">
                                    <?php if ($participation['date_presence']): ?>
                                        <span class="flex items-center gap-2 bg-gray-50 px-3 py-2 rounded-lg">
                                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            <span><?php echo date('d/m/Y', strtotime($participation['date_presence'])); ?></span>
                                        </span>
                                    <?php endif; ?>
                                    
                                    <?php if ($participation['heure_presence']): ?>
                                        <span class="flex items-center gap-2 bg-gray-50 px-3 py-2 rounded-lg">
                                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <span><?php echo date('H:i', strtotime($participation['heure_presence'])); ?></span>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Boutons d'action -->
                                <div class="flex items-center gap-3 pt-4 border-t border-gray-200">
                                    <button 
                                        onclick="showInviteFriendModal(<?php echo $participation['id']; ?>, '<?php echo htmlspecialchars($participation['activity_type'], ENT_QUOTES); ?>', <?php echo $participation['activity_id']; ?>, '<?php echo htmlspecialchars($participation['date_presence'] ?? '', ENT_QUOTES); ?>', '<?php echo htmlspecialchars($participation['heure_presence'] ?? '', ENT_QUOTES); ?>')"
                                        class="flex-1 px-4 py-2.5 text-sm font-semibold text-blue-600 border-2 border-blue-500 rounded-lg hover:bg-blue-500 hover:text-white transition-all duration-300 shadow-sm hover:shadow-md hover-lift flex items-center justify-center space-x-2"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        <span>Inviter un ami</span>
                                    </button>
                                    <a 
                                        href="index.php?ctl=participation&action=supprimer&id=<?php echo $participation['id']; ?>" 
                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette participation ?');"
                                        class="flex-1 px-4 py-2.5 text-sm font-semibold text-red-600 border-2 border-red-500 rounded-lg hover:bg-red-500 hover:text-white transition-all duration-300 shadow-sm hover:shadow-md hover-lift flex items-center justify-center space-x-2"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        <span>Supprimer</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Fonction pour afficher la modale d'invitation d'ami
function showInviteFriendModal(participationId, activityType, activityId, datePresence, heurePresence) {
    // Charger la liste des amis
    fetch('index.php?ctl=amis&action=get_friends_json')
        .then(response => response.json())
        .then(data => {
            if (!data.success || data.friends.length === 0) {
                alert('Vous n\'avez aucun ami à inviter. Ajoutez des amis d\'abord !');
                return;
            }
            
            // Créer la modale
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center';
            modal.style.zIndex = '99999';
            
            let friendsList = data.friends.map(friend => `
                <label class="flex items-center space-x-3 p-3 hover:bg-gray-50 rounded-lg cursor-pointer border border-gray-200">
                    <input type="radio" name="selectedFriend" value="${friend.friend_user_id}" class="w-4 h-4 text-blue-600">
                    <div>
                        <span class="font-medium text-gray-900">${friend.friend_name}</span>
                        <p class="text-xs text-gray-500">${friend.friend_email}</p>
                    </div>
                </label>
            `).join('');
            
            modal.innerHTML = `
                <div class="bg-white rounded-2xl shadow-2xl border border-gray-200 max-w-md w-full mx-4">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <h2 class="text-xl font-bold text-gray-900">Inviter un ami</h2>
                            <button onclick="this.closest('.fixed').remove()" 
                                    class="text-gray-400 hover:text-gray-600 text-2xl font-light hover:bg-gray-100 rounded-full w-8 h-8 flex items-center justify-center transition-colors">&times;</button>
                        </div>
                        
                        <div class="mb-4">
                            <p class="text-sm text-gray-600 mb-3">Sélectionnez un ami à inviter :</p>
                            <div class="max-h-64 overflow-y-auto space-y-2">
                                ${friendsList}
                            </div>
                        </div>
                        
                        <div class="flex space-x-3">
                            <button onclick="this.closest('.fixed').remove()" 
                                    class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300 transition-all duration-200">
                                Annuler
                            </button>
                            <button onclick="sendInvitation('${activityType}', ${activityId}, '${datePresence}', '${heurePresence}')" 
                                    class="flex-1 px-4 py-2 bg-blue-500 text-white font-semibold rounded-lg hover:bg-blue-600 transition-all duration-200">
                                Envoyer
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            // Fermer en cliquant sur le fond
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    modal.remove();
                }
            });
            
            document.body.appendChild(modal);
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors du chargement de la liste des amis');
        });
}

// Fonction pour envoyer l'invitation
function sendInvitation(activityType, activityId, datePresence, heurePresence) {
    const selectedFriend = document.querySelector('input[name="selectedFriend"]:checked');
    
    if (!selectedFriend) {
        alert('Veuillez sélectionner un ami');
        return;
    }
    
    const friendId = selectedFriend.value;
    
    // Envoyer l'invitation via AJAX
    const formData = new FormData();
    formData.append('to_user_id', friendId);
    formData.append('activity_type', activityType);
    formData.append('activity_id', activityId);
    formData.append('date_presence', datePresence);
    formData.append('heure_presence', heurePresence);
    
    fetch('index.php?ctl=participation&action=envoyer_invitation', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Invitation envoyée avec succès !');
            // Fermer la modale
            document.querySelector('.fixed').remove();
            location.reload();
        } else {
            alert('Erreur : ' + (data.error || 'Impossible d\'envoyer l\'invitation'));
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors de l\'envoi de l\'invitation');
    });
}
</script>
