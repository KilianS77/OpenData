<div class="min-h-screen bg-gradient-to-br from-gray-50 to-white p-6">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-6 tracking-tight">Participer à une manifestation</h1>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg text-red-700 text-sm shadow-sm">
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
                <div class="mb-4 p-4 bg-green-50 border-l-4 border-green-500 rounded-r-lg text-green-700 text-sm shadow-sm">
                    <?php 
                    echo $_SESSION['success'];
                    unset($_SESSION['success']);
                    ?>
                </div>
            <?php endif; ?>
            
            <div class="mb-6 p-4 bg-gray-50 rounded-xl border border-gray-200 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-900 mb-2"><?php echo htmlspecialchars($activityName); ?></h2>
                <?php if ($activityAddress): ?>
                    <p class="text-sm text-gray-600 font-light">📍 <?php echo htmlspecialchars($activityAddress); ?></p>
                <?php endif; ?>
                <p class="text-sm text-gray-600 font-light mt-2">
                    📅 Du <?php echo date('d/m/Y', strtotime($dateDebut)); ?> au <?php echo date('d/m/Y', strtotime($dateFin)); ?>
                </p>
            </div>
            
            <form method="POST" action="index.php?ctl=participation&action=creer_participation_manifestation" class="space-y-6">
                <input type="hidden" name="activity_type" value="<?php echo htmlspecialchars($activityType); ?>">
                <input type="hidden" name="activity_id" value="<?php echo htmlspecialchars($activityId); ?>">
                <input type="hidden" name="activity_description" value="<?php echo htmlspecialchars($activityDescription); ?>">
                <input type="hidden" name="date_debut" value="<?php echo htmlspecialchars($dateDebut); ?>">
                <input type="hidden" name="date_fin" value="<?php echo htmlspecialchars($dateFin); ?>">
                
                <!-- Date de présence (obligatoire, entre date_debut et date_fin) -->
                <div>
                    <label for="date_presence" class="block text-sm font-semibold text-gray-700 mb-2">
                        Date de participation <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="date" 
                        id="date_presence" 
                        name="date_presence" 
                        required
                        min="<?php echo htmlspecialchars($dateDebut); ?>"
                        max="<?php echo htmlspecialchars($dateFin); ?>"
                        value="<?php echo isset($formData['date_presence']) ? htmlspecialchars($formData['date_presence']) : ''; ?>"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:border-red-500 focus:ring-2 focus:ring-red-500 focus:ring-opacity-20 text-sm transition-all duration-200"
                    >
                    <p class="mt-1 text-xs text-gray-500 font-light">
                        Veuillez choisir une date entre le <?php echo date('d/m/Y', strtotime($dateDebut)); ?> et le <?php echo date('d/m/Y', strtotime($dateFin)); ?>
                    </p>
                </div>
                
                <div class="flex space-x-4 pt-4">
                    <button 
                        type="submit" 
                        class="flex-1 px-6 py-3 bg-red-500 text-white font-semibold rounded-lg shadow-md hover:bg-red-600 hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200"
                    >
                        Confirmer la participation
                    </button>
                    <a 
                        href="index.php?ctl=evenements&action=liste" 
                        class="flex-1 px-6 py-3 bg-white border-2 border-red-500 text-red-500 font-semibold rounded-lg hover:bg-red-500 hover:text-white transition-all duration-200 text-center shadow-sm hover:shadow-md"
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
    const selectedDate = new Date(dateInput.value);
    const dateDebut = new Date(dateInput.min);
    const dateFin = new Date(dateInput.max);
    
    dateDebut.setHours(0, 0, 0, 0);
    dateFin.setHours(0, 0, 0, 0);
    selectedDate.setHours(0, 0, 0, 0);
    
    if (selectedDate < dateDebut || selectedDate > dateFin) {
        alert('La date doit être comprise entre le ' + dateDebut.toLocaleDateString('fr-FR') + ' et le ' + dateFin.toLocaleDateString('fr-FR'));
        dateInput.value = '';
    }
});
</script>


