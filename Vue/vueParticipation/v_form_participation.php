<div class="min-h-screen bg-white p-6">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white border border-red-500 p-8">
            <h1 class="text-2xl font-light text-gray-900 mb-6 tracking-tight">Participer à une activité</h1>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 text-sm">
                    <?php 
                    echo $_SESSION['error'];
                    unset($_SESSION['error']);
                    ?>
                </div>
            <?php endif; ?>
            
            <?php 
            // Récupérer les données du formulaire en session si elles existent
            $formData = $_SESSION['form_data'] ?? [];
            unset($_SESSION['form_data']); // Nettoyer après récupération
            ?>
            
            <?php if (isset($_SESSION['success'])): ?>
                <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 text-sm">
                    <?php 
                    echo $_SESSION['success'];
                    unset($_SESSION['success']);
                    ?>
                </div>
            <?php endif; ?>
            
            <div class="mb-6 p-4 bg-gray-50 border border-gray-200">
                <h2 class="text-lg font-light text-gray-900 mb-2"><?php echo htmlspecialchars($activityName ?: $activityDescription); ?></h2>
                <?php if ($activityAddress): ?>
                    <p class="text-sm text-gray-600 font-light"><?php echo htmlspecialchars($activityAddress); ?></p>
                <?php endif; ?>
            </div>
            
            <form method="POST" action="index.php?ctl=participation&action=creer_participation" class="space-y-6">
                <input type="hidden" name="activity_type" value="<?php echo htmlspecialchars($activityType); ?>">
                <input type="hidden" name="activity_id" value="<?php echo htmlspecialchars($activityId); ?>">
                <input type="hidden" name="activity_description" value="<?php echo htmlspecialchars($activityDescription); ?>">
                
                <!-- Date de présence (obligatoire) -->
                <div>
                    <label for="date_presence" class="block text-sm font-light text-gray-700 mb-2">
                        Date de présence <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="date" 
                        id="date_presence" 
                        name="date_presence" 
                        required
                        min="<?php echo date('Y-m-d'); ?>"
                        value="<?php echo isset($formData['date_presence']) ? htmlspecialchars($formData['date_presence']) : ''; ?>"
                        class="w-full px-4 py-2 border border-gray-300 focus:border-red-500 focus:ring-1 focus:ring-red-500 text-sm font-light"
                    >
                    <p class="mt-1 text-xs text-gray-500 font-light">La date ne peut pas être antérieure à aujourd'hui</p>
                </div>
                
                <!-- Heure de présence (facultative) -->
                <div>
                    <label for="heure_presence" class="block text-sm font-light text-gray-700 mb-2">
                        Heure de présence (facultatif)
                    </label>
                    <input 
                        type="time" 
                        id="heure_presence" 
                        name="heure_presence"
                        value="<?php echo isset($formData['heure_presence']) ? htmlspecialchars($formData['heure_presence']) : ''; ?>"
                        class="w-full px-4 py-2 border border-gray-300 focus:border-red-500 focus:ring-1 focus:ring-red-500 text-sm font-light"
                    >
                    <p class="mt-1 text-xs text-gray-500 font-light">Si la date est aujourd'hui, l'heure ne peut pas être passée</p>
                </div>
                
                <div class="flex space-x-4 pt-4">
                    <button 
                        type="submit" 
                        class="flex-1 px-6 py-2 bg-red-500 text-white font-light hover:bg-red-600 transition-colors"
                    >
                        Confirmer la participation
                    </button>
                    <a 
                        href="index.php?ctl=map" 
                        class="flex-1 px-6 py-2 bg-white border border-red-500 text-red-500 font-light hover:bg-red-500 hover:text-white transition-colors text-center"
                    >
                        Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Validation côté client
document.getElementById('date_presence').addEventListener('change', function() {
    const dateInput = this;
    const heureInput = document.getElementById('heure_presence');
    const selectedDate = new Date(dateInput.value);
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    
    // Si la date est aujourd'hui, mettre à jour le min de l'heure
    if (selectedDate.getTime() === today.getTime()) {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        heureInput.min = hours + ':' + minutes;
    } else {
        heureInput.min = '';
    }
});

// Validation de l'heure si la date est aujourd'hui
document.getElementById('heure_presence').addEventListener('change', function() {
    const dateInput = document.getElementById('date_presence');
    const heureInput = this;
    const selectedDate = new Date(dateInput.value);
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    
    if (selectedDate.getTime() === today.getTime() && heureInput.value) {
        const now = new Date();
        const selectedTime = new Date(selectedDate);
        const [hours, minutes] = heureInput.value.split(':');
        selectedTime.setHours(parseInt(hours), parseInt(minutes), 0, 0);
        
        if (selectedTime < now) {
            alert('L\'heure ne peut pas être antérieure à l\'heure actuelle');
            heureInput.value = '';
        }
    }
});
</script>

