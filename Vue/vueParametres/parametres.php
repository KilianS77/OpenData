<?php
require_once __DIR__ . '/../../Model/SettingsModel.php';

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['connect']) || $_SESSION['connect'] !== true) {
    header('Location: index.php?ctl=connexion&action=connexion');
    exit();
}

$userId = $_SESSION['user_id'] ?? null;
$settings = SettingsModel::getSettings($userId);

// Valeurs par défaut si aucun paramètre n'existe
$participationVisibility = $settings['participation_visibility'] ?? 'friends_only';
$viewParticipations = $settings['view_participations'] ?? 'friends_only';
?>

<div class="min-h-screen bg-gradient-to-br from-gray-50 to-white py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
            <!-- En-tête -->
            <div class="bg-gradient-to-r from-red-500 to-red-600 px-6 py-6">
                <h1 class="text-3xl font-bold text-white tracking-tight">Paramètres</h1>
            </div>
            
            <!-- Contenu -->
            <div class="px-6 py-8 space-y-8">
                <!-- Section Visibilité de mes participations -->
                <div class="border-b border-gray-200 pb-6">
                    <h2 class="text-xl font-medium text-gray-900 mb-4">Visibilité de mes participations</h2>
                    <p class="text-sm text-gray-600 mb-4">Choisissez qui peut voir vos participations aux activités</p>
                    
                    <div class="space-y-3">
                        <label class="flex items-center p-4 border-2 rounded-xl cursor-pointer transition-all hover:bg-gray-50 hover:shadow-sm <?php echo $participationVisibility === 'friends_only' ? 'border-red-500 bg-red-50 shadow-sm' : 'border-gray-200'; ?>">
                            <input 
                                type="radio" 
                                name="participation_visibility" 
                                value="friends_only" 
                                class="w-5 h-5 text-red-500 focus:ring-red-500 focus:ring-2"
                                <?php echo $participationVisibility === 'friends_only' ? 'checked' : ''; ?>
                                onchange="updateSetting('participation_visibility', this.value)"
                            >
                            <span class="ml-3 text-gray-700 font-semibold">Mes amis uniquement</span>
                        </label>
                        
                        <label class="flex items-center p-4 border-2 rounded-xl cursor-pointer transition-all hover:bg-gray-50 hover:shadow-sm <?php echo $participationVisibility === 'public' ? 'border-red-500 bg-red-50 shadow-sm' : 'border-gray-200'; ?>">
                            <input 
                                type="radio" 
                                name="participation_visibility" 
                                value="public" 
                                class="w-5 h-5 text-red-500 focus:ring-red-500 focus:ring-2"
                                <?php echo $participationVisibility === 'public' ? 'checked' : ''; ?>
                                onchange="updateSetting('participation_visibility', this.value)"
                            >
                            <span class="ml-3 text-gray-700 font-semibold">Tout le monde</span>
                        </label>
                    </div>
                </div>
                
                <!-- Section Voir les participations -->
                <div class="border-b border-gray-200 pb-6">
                    <h2 class="text-xl font-medium text-gray-900 mb-4">Voir les participations</h2>
                    <p class="text-sm text-gray-600 mb-4">Choisissez quelles participations vous souhaitez voir</p>
                    
                    <div class="space-y-3">
                        <label class="flex items-center p-4 border-2 rounded-xl cursor-pointer transition-all hover:bg-gray-50 hover:shadow-sm <?php echo $viewParticipations === 'friends_only' ? 'border-red-500 bg-red-50 shadow-sm' : 'border-gray-200'; ?>">
                            <input 
                                type="radio" 
                                name="view_participations" 
                                value="friends_only" 
                                class="w-5 h-5 text-red-500 focus:ring-red-500 focus:ring-2"
                                <?php echo $viewParticipations === 'friends_only' ? 'checked' : ''; ?>
                                onchange="updateSetting('view_participations', this.value)"
                            >
                            <span class="ml-3 text-gray-700 font-semibold">Mes amis uniquement</span>
                        </label>
                        
                        <label class="flex items-center p-4 border-2 rounded-xl cursor-pointer transition-all hover:bg-gray-50 hover:shadow-sm <?php echo $viewParticipations === 'public' ? 'border-red-500 bg-red-50 shadow-sm' : 'border-gray-200'; ?>">
                            <input 
                                type="radio" 
                                name="view_participations" 
                                value="public" 
                                class="w-5 h-5 text-red-500 focus:ring-red-500 focus:ring-2"
                                <?php echo $viewParticipations === 'public' ? 'checked' : ''; ?>
                                onchange="updateSetting('view_participations', this.value)"
                            >
                            <span class="ml-3 text-gray-700 font-semibold">Tout le monde</span>
                        </label>
                    </div>
                </div>
                
                <!-- Message de confirmation -->
                <div id="message" class="hidden fixed top-20 right-4 bg-green-500 text-white px-6 py-4 rounded-xl shadow-xl z-50 font-semibold">
                    <span id="messageText"></span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updateSetting(settingType, value) {
    // Sauvegarder l'ancienne valeur
    const previousRadio = document.querySelector(`input[name="${settingType}"]:checked`);
    const previousValue = previousRadio ? previousRadio.value : null;
    
    // Réinitialiser tous les labels
    document.querySelectorAll(`input[name="${settingType}"]`).forEach(input => {
        input.closest('label').classList.remove('border-red-500', 'bg-red-50');
        input.closest('label').classList.add('border-gray-200');
    });
    
    // Trouver le label du radio sélectionné
    const radio = document.querySelector(`input[name="${settingType}"][value="${value}"]`);
    const label = radio.closest('label');
    
    // Afficher un indicateur de chargement
    label.classList.add('opacity-50');
    
    // Envoyer la requête AJAX
    fetch('index.php?ctl=parametres&action=update_setting', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `setting_type=${encodeURIComponent(settingType)}&value=${encodeURIComponent(value)}`
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Erreur HTTP: ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        label.classList.remove('opacity-50');
        
        if (data.success) {
            // Mettre à jour l'apparence du label sélectionné
            label.classList.remove('border-gray-200');
            label.classList.add('border-red-500', 'bg-red-50');
            
            // Afficher le message de confirmation
            showMessage('Paramètre mis à jour avec succès', 'success');
            
            // Recharger la page après 1 seconde pour s'assurer que tout est synchronisé
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            // En cas d'erreur, remettre l'ancienne valeur
            if (previousValue) {
                radio.checked = false;
                const previousRadioInput = document.querySelector(`input[name="${settingType}"][value="${previousValue}"]`);
                if (previousRadioInput) {
                    previousRadioInput.checked = true;
                    previousRadioInput.closest('label').classList.remove('border-gray-200');
                    previousRadioInput.closest('label').classList.add('border-red-500', 'bg-red-50');
                }
            }
            
            const errorMsg = data.message || 'Erreur lors de la mise à jour';
            showMessage(errorMsg, 'error');
            console.error('Erreur de mise à jour:', data);
        }
    })
    .catch(error => {
        label.classList.remove('opacity-50');
        
        // En cas d'erreur, remettre l'ancienne valeur
        if (previousValue) {
            radio.checked = false;
            const previousRadioInput = document.querySelector(`input[name="${settingType}"][value="${previousValue}"]`);
            if (previousRadioInput) {
                previousRadioInput.checked = true;
                previousRadioInput.closest('label').classList.remove('border-gray-200');
                previousRadioInput.closest('label').classList.add('border-red-500', 'bg-red-50');
            }
        }
        
        console.error('Erreur:', error);
        showMessage('Erreur de connexion. Vérifiez votre connexion internet.', 'error');
    });
}

function showMessage(text, type = 'success') {
    const messageDiv = document.getElementById('message');
    const messageText = document.getElementById('messageText');
    
    if (!messageDiv || !messageText) {
        console.error('Éléments de message non trouvés');
        return;
    }
    
    messageText.textContent = text;
    messageDiv.className = `fixed top-20 right-4 px-6 py-4 rounded-xl shadow-xl z-50 font-semibold ${
        type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
    }`;
    messageDiv.classList.remove('hidden');
    
    setTimeout(() => {
        messageDiv.classList.add('hidden');
    }, 5000);
}
</script>

<style>
input[type="radio"]:checked + span {
    font-weight: 600;
}
</style>
