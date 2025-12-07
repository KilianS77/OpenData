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
                    <?php foreach ($participations as $participation): ?>
                        <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm hover:shadow-md hover:border-red-300 transition-all duration-200">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h3 class="text-lg font-bold text-gray-900 mb-2">
                                        <?php echo htmlspecialchars($participation['activity_name'] ?? $participation['activity_description']); ?>
                                    </h3>
                                    <?php if (!empty($participation['activity_address'])): ?>
                                        <p class="text-sm text-gray-600 font-light mb-3">
                                            📍 <?php echo htmlspecialchars($participation['activity_address']); ?>
                                        </p>
                                    <?php endif; ?>
                                    
                                    <div class="flex items-center space-x-4 text-sm text-gray-500 font-light">
                                        <?php if ($participation['date_presence']): ?>
                                            <span class="flex items-center gap-1">
                                                📅 <?php echo date('d/m/Y', strtotime($participation['date_presence'])); ?>
                                            </span>
                                        <?php endif; ?>
                                        
                                        <?php if ($participation['heure_presence']): ?>
                                            <span class="flex items-center gap-1">
                                                🕐 <?php echo date('H:i', strtotime($participation['heure_presence'])); ?>
                                            </span>
                                        <?php endif; ?>
                                        
                                        <span class="px-3 py-1 text-xs font-semibold bg-red-100 text-red-700 rounded-full">
                                            <?php echo htmlspecialchars($participation['activity_type']); ?>
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="ml-4 flex gap-2">
                                    <button 
                                        onclick="showInviteFriendModal(<?php echo $participation['id']; ?>, '<?php echo htmlspecialchars($participation['activity_type'], ENT_QUOTES); ?>', <?php echo $participation['activity_id']; ?>, '<?php echo htmlspecialchars($participation['date_presence'] ?? '', ENT_QUOTES); ?>', '<?php echo htmlspecialchars($participation['heure_presence'] ?? '', ENT_QUOTES); ?>')"
                                        class="px-4 py-2 text-sm font-semibold text-blue-500 border-2 border-blue-500 rounded-lg hover:bg-blue-500 hover:text-white transition-all duration-200 shadow-sm hover:shadow-md"
                                    >
                                        Inviter un ami
                                    </button>
                                    <a 
                                        href="index.php?ctl=participation&action=supprimer&id=<?php echo $participation['id']; ?>" 
                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette participation ?');"
                                        class="px-4 py-2 text-sm font-semibold text-red-500 border-2 border-red-500 rounded-lg hover:bg-red-500 hover:text-white transition-all duration-200 shadow-sm hover:shadow-md"
                                    >
                                        Supprimer
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
