<?php
// Définir les gradients pour les cartes d'événements (disponible dans toutes les sections)
$gradients = [
    'from-red-500 to-orange-500',
    'from-blue-500 to-purple-500',
    'from-green-500 to-teal-500',
    'from-pink-500 to-rose-500',
    'from-indigo-500 to-blue-500',
    'from-yellow-500 to-orange-500'
];
?>
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-white p-6">
    <div class="max-w-7xl mx-auto">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-4xl font-bold text-gray-900 tracking-tight">Événements</h1>
            <button id="refreshBtn" type="button" class="px-6 py-3 bg-red-500 text-white text-sm font-semibold rounded-lg shadow-md hover:bg-red-600 hover:shadow-lg transform hover:-translate-y-1 transition-all duration-300 hover-lift ripple flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                <span>Actualiser</span>
            </button>
        </div>
        
        <div id="syncStatus" class="mb-4 hidden">
            <div class="p-4 bg-blue-50 border-l-4 border-blue-500 rounded-r-lg text-blue-700 text-sm shadow-sm">
                <p class="font-medium">Synchronisation en cours...</p>
            </div>
        </div>
        
        <!-- Activités du jour -->
        <section class="mb-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-6 tracking-tight">Activités du jour</h2>
            <?php if (empty($activitesDuJour)): ?>
                <div class="bg-white rounded-xl p-8 shadow-sm border border-gray-200 text-center">
                    <p class="text-gray-500 font-light">Aucune activité prévue aujourd'hui.</p>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php 
                    $cardIndex = 0;
                    foreach ($activitesDuJour as $activite): 
                        $isSport = $activite['type'] === 'manifestations_sportives';
                        $gradient = $gradients[$cardIndex % count($gradients)];
                        $cardIndex++;
                    ?>
                        <div class="group bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-md hover:shadow-2xl transition-all duration-500 card-hover card-animate observe-on-scroll transform hover:scale-105">
                            <!-- Header avec gradient -->
                            <div class="bg-gradient-to-r <?php echo $gradient; ?> p-4 text-white relative overflow-hidden">
                                <div class="absolute top-0 right-0 w-32 h-32 bg-white opacity-10 rounded-full -mr-16 -mt-16"></div>
                                <div class="absolute bottom-0 left-0 w-24 h-24 bg-white opacity-10 rounded-full -ml-12 -mb-12"></div>
                                <div class="relative z-10 flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <?php if ($isSport): ?>
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                            </svg>
                                        <?php else: ?>
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/>
                                            </svg>
                                        <?php endif; ?>
                                        <span class="text-sm font-bold uppercase tracking-wide">
                                            <?php echo $isSport ? 'Sport' : 'Culture'; ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="p-6">
                                <h3 class="text-xl font-bold text-gray-900 mb-4 group-hover:text-red-600 transition-colors line-clamp-2">
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
                                    <p class="text-sm text-gray-600 mb-2 font-light flex items-center space-x-2">
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <span><?php echo htmlspecialchars($activite['lieu']); ?></span>
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
                                    <p class="text-sm text-gray-600 mb-2 font-light flex items-center space-x-2">
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <span><?php echo htmlspecialchars($activite['lieu_de_representation']); ?></span>
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
                                <div class="mt-6 pt-4 border-t border-gray-200">
                                    <a href="index.php?ctl=participation&action=participer_evenement&activity_type=<?php echo urlencode($activite['type']); ?>&activity_id=<?php echo urlencode($activite['id']); ?>" 
                                       class="w-full inline-flex items-center justify-center px-4 py-3 bg-red-500 text-white text-sm font-semibold rounded-lg hover:bg-red-600 shadow-md hover:shadow-lg transform hover:-translate-y-1 transition-all duration-300 hover-lift ripple">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        <span>Participer</span>
                                    </a>
                                </div>
                            <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
        
        <!-- Activités futures -->
        <section>
            <h2 class="text-2xl font-bold text-gray-900 mb-6 tracking-tight">Activités futures</h2>
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
                        <?php 
                        $cardIndex = 0;
                        foreach ($activitesAvecDate as $activite): 
                            $isSport = $activite['type'] === 'manifestations_sportives';
                            $gradient = $gradients[$cardIndex % count($gradients)];
                            $cardIndex++;
                        ?>
                        <div class="group bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-md hover:shadow-2xl transition-all duration-500 card-hover card-animate observe-on-scroll transform hover:scale-105">
                            <!-- Header avec gradient -->
                            <div class="bg-gradient-to-r <?php echo $gradient; ?> p-4 text-white relative overflow-hidden">
                                <div class="absolute top-0 right-0 w-32 h-32 bg-white opacity-10 rounded-full -mr-16 -mt-16"></div>
                                <div class="absolute bottom-0 left-0 w-24 h-24 bg-white opacity-10 rounded-full -ml-12 -mb-12"></div>
                                <div class="relative z-10 flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <?php if ($isSport): ?>
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                            </svg>
                                        <?php else: ?>
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/>
                                            </svg>
                                        <?php endif; ?>
                                        <span class="text-sm font-bold uppercase tracking-wide">
                                            <?php echo $isSport ? 'Sport' : 'Culture'; ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="p-6">
                                <h3 class="text-xl font-bold text-gray-900 mb-4 group-hover:text-red-600 transition-colors line-clamp-2">
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
                                    <p class="text-sm text-gray-600 mb-2 font-light flex items-center space-x-2">
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <span><?php echo htmlspecialchars($activite['lieu']); ?></span>
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
                                    <p class="text-sm text-gray-600 mb-2 font-light flex items-center space-x-2">
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <span><?php echo htmlspecialchars($activite['lieu_de_representation']); ?></span>
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
                                <div class="mt-6 pt-4 border-t border-gray-200">
                                    <a href="index.php?ctl=participation&action=participer_evenement&activity_type=<?php echo urlencode($activite['type']); ?>&activity_id=<?php echo urlencode($activite['id']); ?>" 
                                       class="w-full inline-flex items-center justify-center px-4 py-3 bg-red-500 text-white text-sm font-semibold rounded-lg hover:bg-red-600 shadow-md hover:shadow-lg transform hover:-translate-y-1 transition-all duration-300 hover-lift ripple">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        <span>Participer</span>
                                    </a>
                                </div>
                            <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($activitesSansDate)): ?>
                    <div class="mt-8">
                        <h3 class="text-xl font-bold text-gray-700 mb-4 tracking-tight">Événements sans date</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <?php 
                            foreach ($activitesSansDate as $activite): 
                                $isSport = $activite['type'] === 'manifestations_sportives';
                                $gradient = $gradients[($cardIndex++) % count($gradients)];
                            ?>
                                <div class="group bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-md hover:shadow-2xl transition-all duration-500 card-hover card-animate observe-on-scroll transform hover:scale-105">
                                    <!-- Header avec gradient -->
                                    <div class="bg-gradient-to-r <?php echo $gradient; ?> p-4 text-white relative overflow-hidden">
                                        <div class="absolute top-0 right-0 w-32 h-32 bg-white opacity-10 rounded-full -mr-16 -mt-16"></div>
                                        <div class="absolute bottom-0 left-0 w-24 h-24 bg-white opacity-10 rounded-full -ml-12 -mb-12"></div>
                                        <div class="relative z-10 flex items-center justify-between">
                                            <div class="flex items-center space-x-2">
                                                <?php if ($isSport): ?>
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                                    </svg>
                                                <?php else: ?>
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/>
                                                    </svg>
                                                <?php endif; ?>
                                                <span class="text-sm font-bold uppercase tracking-wide">
                                                    <?php echo $isSport ? 'Sport' : 'Culture'; ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="p-6">
                                        <h3 class="text-xl font-bold text-gray-900 mb-4 group-hover:text-red-600 transition-colors line-clamp-2">
                                            <?php echo htmlspecialchars($activite['manifestation'] ?? $activite['nom_du_spectacle'] ?? 'Sans titre'); ?>
                                        </h3>
                                    
                                    <?php if ($activite['type'] === 'manifestations_sportives'): ?>
                                        <?php if ($activite['lieu']): ?>
                                            <p class="text-sm text-gray-600 mb-2 font-light flex items-center space-x-2">
                                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                </svg>
                                                <span><?php echo htmlspecialchars($activite['lieu']); ?></span>
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
                                            <p class="text-sm text-gray-600 mb-2 font-light flex items-center space-x-2">
                                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                </svg>
                                                <span><?php echo htmlspecialchars($activite['lieu_de_representation']); ?></span>
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
                                        <div class="mt-6 pt-4 border-t border-gray-200">
                                            <a href="index.php?ctl=participation&action=participer_evenement&activity_type=<?php echo urlencode($activite['type']); ?>&activity_id=<?php echo urlencode($activite['id']); ?>" 
                                               class="w-full inline-flex items-center justify-center px-4 py-3 bg-red-500 text-white text-sm font-semibold rounded-lg hover:bg-red-600 shadow-md hover:shadow-lg transform hover:-translate-y-1 transition-all duration-300 hover-lift ripple">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                </svg>
                                                <span>Participer</span>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    </div>
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
        statusDiv.innerHTML = '<div class="p-4 bg-blue-50 border-l-4 border-blue-500 rounded-r-lg text-blue-700 text-sm shadow-sm"><p class="font-medium">Synchronisation en cours...</p></div>';
        
        console.log('Synchronisation des événements depuis l\'API...');
        const response = await fetch('index.php?ctl=evenements&action=sync', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            }
        });
        
        if (!response.ok) {
            console.error(`Erreur HTTP ${response.status} lors de la synchronisation`);
            statusDiv.innerHTML = '<div class="p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg text-red-700 text-sm shadow-sm"><p class="font-medium">Erreur lors de la synchronisation</p></div>';
            setTimeout(() => statusDiv.classList.add('hidden'), 3000);
            return { success: false, error: `HTTP ${response.status}` };
        }
        
        let result;
        try {
            result = await response.json();
        } catch (e) {
            console.error('❌ Erreur parsing JSON:', e);
            statusDiv.innerHTML = '<div class="p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg text-red-700 text-sm shadow-sm"><p class="font-medium">Erreur lors de la synchronisation</p></div>';
            setTimeout(() => statusDiv.classList.add('hidden'), 3000);
            return { success: false, error: 'Erreur parsing JSON: ' + e.message };
        }
        
        if (result.success) {
            const saved = result.total_saved || 0;
            const errors = result.total_errors || 0;
            
            console.log(`✅ Synchronisation terminée: ${saved} créés, ${errors} erreurs`);
            
            let message = '✅ Synchronisation terminée avec succès';
            if (saved > 0 || errors > 0) {
                message += `<br><span class="text-xs mt-1 block">${saved} nouveau(x) événement(s) ajouté(s)`;
                if (errors > 0) {
                    message += `, ${errors} erreur(s)`;
                }
                message += '</span>';
            } else {
                message += `<br><span class="text-xs mt-1 block">Aucun nouvel événement à ajouter</span>`;
            }
            
            statusDiv.innerHTML = `<div class="p-4 bg-green-50 border-l-4 border-green-500 rounded-r-lg text-green-700 text-sm shadow-sm"><p class="font-medium">${message}</p></div>`;
            
            // Recharger la page après un court délai pour afficher les nouveaux événements
            // Forcer le rechargement sans cache en ajoutant un paramètre timestamp
            setTimeout(() => {
                const url = new URL(window.location.href);
                url.searchParams.set('_refresh', Date.now());
                window.location.href = url.toString();
            }, 2000);
        } else {
            console.error('❌ Échec de la synchronisation:', result);
            statusDiv.innerHTML = '<div class="p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg text-red-700 text-sm shadow-sm"><p class="font-medium">Erreur lors de la synchronisation</p></div>';
            setTimeout(() => statusDiv.classList.add('hidden'), 3000);
        }
        return result;
    } catch (error) {
        console.error('❌ Erreur lors de la synchronisation:', error);
        const statusDiv = document.getElementById('syncStatus');
        statusDiv.innerHTML = '<div class="p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg text-red-700 text-sm shadow-sm"><p class="font-medium">Erreur lors de la synchronisation</p></div>';
        setTimeout(() => statusDiv.classList.add('hidden'), 3000);
        return { success: false, error: error.message };
    }
}

// Bouton actualiser
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('refreshBtn').addEventListener('click', function() {
        syncEvenementsFromAPI();
    });
});
</script>

