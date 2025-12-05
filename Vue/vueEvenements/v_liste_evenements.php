<div class="min-h-screen bg-white p-6">
    <div class="max-w-7xl mx-auto">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-light text-gray-900 tracking-tight">Événements</h1>
            <button id="refreshBtn" type="button" class="px-4 py-2 bg-red-500 text-white text-sm font-light hover:bg-red-600 transition-colors">
                Actualiser
            </button>
        </div>
        
        <div id="syncStatus" class="mb-4 hidden">
            <div class="p-4 bg-blue-50 border border-blue-200 text-blue-700 text-sm">
                <p>Synchronisation en cours...</p>
            </div>
        </div>
        
        <!-- Activités du jour -->
        <section class="mb-12">
            <h2 class="text-2xl font-light text-gray-900 mb-6 tracking-tight">Activités du jour</h2>
            <?php if (empty($activitesDuJour)): ?>
                <p class="text-gray-500 font-light">Aucune activité prévue aujourd'hui.</p>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($activitesDuJour as $activite): ?>
                        <div class="bg-white border border-gray-200 p-6 hover:border-red-500 transition-colors">
                            <div class="mb-4">
                                <span class="px-3 py-1 text-xs border border-red-500 text-red-500 font-light">
                                    <?php 
                                    if ($activite['type'] === 'manifestations_sportives') {
                                        echo 'Manifestation sportive';
                                    } else {
                                        echo 'Événement culturel';
                                    }
                                    ?>
                                </span>
                            </div>
                            
                            <h3 class="text-lg font-light text-gray-900 mb-3">
                                <?php echo htmlspecialchars($activite['manifestation'] ?? $activite['nom_du_spectacle'] ?? 'Sans titre'); ?>
                            </h3>
                            
                            <?php if ($activite['type'] === 'manifestations_sportives'): ?>
                                <?php if ($activite['date_debut'] && $activite['date_de_fin']): ?>
                                    <p class="text-sm text-gray-600 mb-2 font-light">
                                        Du <?php echo date('d/m/Y', strtotime($activite['date_debut'])); ?>
                                        au <?php echo date('d/m/Y', strtotime($activite['date_de_fin'])); ?>
                                    </p>
                                <?php elseif ($activite['date_debut']): ?>
                                    <p class="text-sm text-gray-600 mb-2 font-light">
                                        Le <?php echo date('d/m/Y', strtotime($activite['date_debut'])); ?>
                                    </p>
                                <?php endif; ?>
                                
                                <?php if ($activite['lieu']): ?>
                                    <p class="text-sm text-gray-600 mb-2 font-light">
                                        📍 <?php echo htmlspecialchars($activite['lieu']); ?>
                                    </p>
                                <?php endif; ?>
                                
                                <?php if ($activite['association_ou_service']): ?>
                                    <p class="text-sm text-gray-500 mb-2 font-light">
                                        Organisé par : <?php echo htmlspecialchars($activite['association_ou_service']); ?>
                                    </p>
                                <?php endif; ?>
                                
                                <?php if ($activite['commune']): ?>
                                    <p class="text-sm text-gray-500 font-light">
                                        <?php echo htmlspecialchars($activite['commune']); ?>
                                    </p>
                                <?php endif; ?>
                            <?php else: // agenda_culturel ?>
                                <?php if ($activite['date']): ?>
                                    <p class="text-sm text-gray-600 mb-2 font-light">
                                        Le <?php echo date('d/m/Y', strtotime($activite['date'])); ?>
                                        <?php if ($activite['horaire']): ?>
                                            à <?php echo htmlspecialchars($activite['horaire']); ?>
                                        <?php endif; ?>
                                    </p>
                                <?php endif; ?>
                                
                                <?php if ($activite['lieu_de_representation']): ?>
                                    <p class="text-sm text-gray-600 mb-2 font-light">
                                        📍 <?php echo htmlspecialchars($activite['lieu_de_representation']); ?>
                                    </p>
                                <?php endif; ?>
                                
                                <?php if ($activite['thematique']): ?>
                                    <p class="text-sm text-gray-500 mb-2 font-light">
                                        Thématique : <?php echo htmlspecialchars($activite['thematique']); ?>
                                    </p>
                                <?php endif; ?>
                                
                                <?php if ($activite['commune']): ?>
                                    <p class="text-sm text-gray-500 font-light">
                                        <?php echo htmlspecialchars($activite['commune']); ?>
                                    </p>
                                <?php endif; ?>
                            <?php endif; ?>
                            
                            <?php if (isset($_SESSION['connect']) && $_SESSION['connect'] === true): ?>
                                <div class="mt-4 pt-4 border-t border-gray-200">
                                    <a href="index.php?ctl=participation&action=participer_evenement&activity_type=<?php echo urlencode($activite['type']); ?>&activity_id=<?php echo urlencode($activite['id']); ?>" 
                                       class="inline-block px-4 py-2 bg-red-500 text-white text-sm font-light hover:bg-red-600 transition-colors">
                                        Participer
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
        
        <!-- Activités futures -->
        <section>
            <h2 class="text-2xl font-light text-gray-900 mb-6 tracking-tight">Activités futures</h2>
            <?php if (empty($activitesFutures)): ?>
                <p class="text-gray-500 font-light">Aucune activité future prévue.</p>
            <?php else: ?>
                <?php 
                // Séparer les activités avec date et sans date pour l'affichage
                $activitesAvecDate = [];
                $activitesSansDate = [];
                foreach ($activitesFutures as $activite) {
                    $hasDate = false;
                    if ($activite['type'] === 'manifestations_sportives') {
                        $hasDate = !empty($activite['date_debut']) || !empty($activite['date_de_fin']);
                    } else { // agenda_culturel
                        $hasDate = !empty($activite['date']);
                    }
                    
                    if ($hasDate) {
                        $activitesAvecDate[] = $activite;
                    } else {
                        $activitesSansDate[] = $activite;
                    }
                }
                ?>
                
                <?php if (!empty($activitesAvecDate)): ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                        <?php foreach ($activitesAvecDate as $activite): ?>
                        <div class="bg-white border border-gray-200 p-6 hover:border-red-500 transition-colors">
                            <div class="mb-4">
                                <span class="px-3 py-1 text-xs border border-red-500 text-red-500 font-light">
                                    <?php 
                                    if ($activite['type'] === 'manifestations_sportives') {
                                        echo 'Manifestation sportive';
                                    } else {
                                        echo 'Événement culturel';
                                    }
                                    ?>
                                </span>
                            </div>
                            
                            <h3 class="text-lg font-light text-gray-900 mb-3">
                                <?php echo htmlspecialchars($activite['manifestation'] ?? $activite['nom_du_spectacle'] ?? 'Sans titre'); ?>
                            </h3>
                            
                            <?php if ($activite['type'] === 'manifestations_sportives'): ?>
                                <?php if ($activite['date_debut'] && $activite['date_de_fin']): ?>
                                    <p class="text-sm text-gray-600 mb-2 font-light">
                                        Du <?php echo date('d/m/Y', strtotime($activite['date_debut'])); ?>
                                        au <?php echo date('d/m/Y', strtotime($activite['date_de_fin'])); ?>
                                    </p>
                                <?php elseif ($activite['date_debut']): ?>
                                    <p class="text-sm text-gray-600 mb-2 font-light">
                                        Le <?php echo date('d/m/Y', strtotime($activite['date_debut'])); ?>
                                    </p>
                                <?php endif; ?>
                                
                                <?php if ($activite['lieu']): ?>
                                    <p class="text-sm text-gray-600 mb-2 font-light">
                                        📍 <?php echo htmlspecialchars($activite['lieu']); ?>
                                    </p>
                                <?php endif; ?>
                                
                                <?php if ($activite['association_ou_service']): ?>
                                    <p class="text-sm text-gray-500 mb-2 font-light">
                                        Organisé par : <?php echo htmlspecialchars($activite['association_ou_service']); ?>
                                    </p>
                                <?php endif; ?>
                                
                                <?php if ($activite['commune']): ?>
                                    <p class="text-sm text-gray-500 font-light">
                                        <?php echo htmlspecialchars($activite['commune']); ?>
                                    </p>
                                <?php endif; ?>
                            <?php else: // agenda_culturel ?>
                                <?php if ($activite['date']): ?>
                                    <p class="text-sm text-gray-600 mb-2 font-light">
                                        Le <?php echo date('d/m/Y', strtotime($activite['date'])); ?>
                                        <?php if ($activite['horaire']): ?>
                                            à <?php echo htmlspecialchars($activite['horaire']); ?>
                                        <?php endif; ?>
                                    </p>
                                <?php endif; ?>
                                
                                <?php if ($activite['lieu_de_representation']): ?>
                                    <p class="text-sm text-gray-600 mb-2 font-light">
                                        📍 <?php echo htmlspecialchars($activite['lieu_de_representation']); ?>
                                    </p>
                                <?php endif; ?>
                                
                                <?php if ($activite['thematique']): ?>
                                    <p class="text-sm text-gray-500 mb-2 font-light">
                                        Thématique : <?php echo htmlspecialchars($activite['thematique']); ?>
                                    </p>
                                <?php endif; ?>
                                
                                <?php if ($activite['commune']): ?>
                                    <p class="text-sm text-gray-500 font-light">
                                        <?php echo htmlspecialchars($activite['commune']); ?>
                                    </p>
                                <?php endif; ?>
                            <?php endif; ?>
                            
                            <?php if (isset($_SESSION['connect']) && $_SESSION['connect'] === true): ?>
                                <div class="mt-4 pt-4 border-t border-gray-200">
                                    <a href="index.php?ctl=participation&action=participer_evenement&activity_type=<?php echo urlencode($activite['type']); ?>&activity_id=<?php echo urlencode($activite['id']); ?>" 
                                       class="inline-block px-4 py-2 bg-red-500 text-white text-sm font-light hover:bg-red-600 transition-colors">
                                        Participer
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($activitesSansDate)): ?>
                    <div class="mt-8">
                        <h3 class="text-xl font-light text-gray-700 mb-4 tracking-tight">Événements sans date</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <?php foreach ($activitesSansDate as $activite): ?>
                                <div class="bg-white border border-gray-200 p-6 hover:border-red-500 transition-colors">
                                    <div class="mb-4">
                                        <span class="px-3 py-1 text-xs border border-red-500 text-red-500 font-light">
                                            <?php 
                                            if ($activite['type'] === 'manifestations_sportives') {
                                                echo 'Manifestation sportive';
                                            } else {
                                                echo 'Événement culturel';
                                            }
                                            ?>
                                        </span>
                                    </div>
                                    
                                    <h3 class="text-lg font-light text-gray-900 mb-3">
                                        <?php echo htmlspecialchars($activite['manifestation'] ?? $activite['nom_du_spectacle'] ?? 'Sans titre'); ?>
                                    </h3>
                                    
                                    <?php if ($activite['type'] === 'manifestations_sportives'): ?>
                                        <?php if ($activite['lieu']): ?>
                                            <p class="text-sm text-gray-600 mb-2 font-light">
                                                📍 <?php echo htmlspecialchars($activite['lieu']); ?>
                                            </p>
                                        <?php endif; ?>
                                        
                                        <?php if ($activite['association_ou_service']): ?>
                                            <p class="text-sm text-gray-500 mb-2 font-light">
                                                Organisé par : <?php echo htmlspecialchars($activite['association_ou_service']); ?>
                                            </p>
                                        <?php endif; ?>
                                        
                                        <?php if ($activite['commune']): ?>
                                            <p class="text-sm text-gray-500 font-light">
                                                <?php echo htmlspecialchars($activite['commune']); ?>
                                            </p>
                                        <?php endif; ?>
                                    <?php else: // agenda_culturel ?>
                                        <?php if ($activite['lieu_de_representation']): ?>
                                            <p class="text-sm text-gray-600 mb-2 font-light">
                                                📍 <?php echo htmlspecialchars($activite['lieu_de_representation']); ?>
                                            </p>
                                        <?php endif; ?>
                                        
                                        <?php if ($activite['thematique']): ?>
                                            <p class="text-sm text-gray-500 mb-2 font-light">
                                                Thématique : <?php echo htmlspecialchars($activite['thematique']); ?>
                                            </p>
                                        <?php endif; ?>
                                        
                                        <?php if ($activite['commune']): ?>
                                            <p class="text-sm text-gray-500 font-light">
                                                <?php echo htmlspecialchars($activite['commune']); ?>
                                            </p>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    
                                    <?php if (isset($_SESSION['connect']) && $_SESSION['connect'] === true): ?>
                                        <div class="mt-4 pt-4 border-t border-gray-200">
                                            <a href="index.php?ctl=participation&action=participer_evenement&activity_type=<?php echo urlencode($activite['type']); ?>&activity_id=<?php echo urlencode($activite['id']); ?>" 
                                               class="inline-block px-4 py-2 bg-red-500 text-white text-sm font-light hover:bg-red-600 transition-colors">
                                                Participer
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </section>
    </div>
</div>

<script>
// Fonction pour synchroniser les événements depuis l'API
async function syncEvenementsFromAPI() {
    try {
        const statusDiv = document.getElementById('syncStatus');
        statusDiv.classList.remove('hidden');
        statusDiv.innerHTML = '<div class="p-4 bg-blue-50 border border-blue-200 text-blue-700 text-sm"><p>Synchronisation en cours...</p></div>';
        
        console.log('Synchronisation des événements depuis l\'API...');
        const response = await fetch('index.php?ctl=evenements&action=sync', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            }
        });
        
        if (!response.ok) {
            console.error(`Erreur HTTP ${response.status} lors de la synchronisation`);
            statusDiv.innerHTML = '<div class="p-4 bg-red-50 border border-red-200 text-red-700 text-sm"><p>Erreur lors de la synchronisation</p></div>';
            setTimeout(() => statusDiv.classList.add('hidden'), 3000);
            return { success: false, error: `HTTP ${response.status}` };
        }
        
        let result;
        try {
            result = await response.json();
        } catch (e) {
            console.error('❌ Erreur parsing JSON:', e);
            statusDiv.innerHTML = '<div class="p-4 bg-red-50 border border-red-200 text-red-700 text-sm"><p>Erreur lors de la synchronisation</p></div>';
            setTimeout(() => statusDiv.classList.add('hidden'), 3000);
            return { success: false, error: 'Erreur parsing JSON: ' + e.message };
        }
        
        if (result.success) {
            console.log(`✅ Synchronisation terminée: ${result.total_saved} créés, ${result.total_updated} mis à jour, ${result.total_errors} erreurs`);
            statusDiv.innerHTML = '<div class="p-4 bg-green-50 border border-green-200 text-green-700 text-sm"><p>Synchronisation terminée avec succès</p></div>';
            setTimeout(() => {
                statusDiv.classList.add('hidden');
                // Recharger la page pour afficher les nouveaux événements
                window.location.reload();
            }, 1500);
        } else {
            console.error('❌ Échec de la synchronisation:', result);
            statusDiv.innerHTML = '<div class="p-4 bg-red-50 border border-red-200 text-red-700 text-sm"><p>Erreur lors de la synchronisation</p></div>';
            setTimeout(() => statusDiv.classList.add('hidden'), 3000);
        }
        return result;
    } catch (error) {
        console.error('❌ Erreur lors de la synchronisation:', error);
        const statusDiv = document.getElementById('syncStatus');
        statusDiv.innerHTML = '<div class="p-4 bg-red-50 border border-red-200 text-red-700 text-sm"><p>Erreur lors de la synchronisation</p></div>';
        setTimeout(() => statusDiv.classList.add('hidden'), 3000);
        return { success: false, error: error.message };
    }
}

// Synchroniser au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    syncEvenementsFromAPI();
});

// Bouton actualiser
document.getElementById('refreshBtn').addEventListener('click', function() {
    syncEvenementsFromAPI();
});
</script>

