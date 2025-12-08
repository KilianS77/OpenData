<div class="min-h-screen bg-gradient-to-br from-gray-50 to-white p-6">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Participations des autres</h1>
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
                    <p class="text-gray-500 font-light mb-4">Aucune participation à afficher pour le moment</p>
                    <p class="text-sm text-gray-400 font-light mb-4">
                        <?php 
                        if ($viewMode === 'friends_only') {
                            echo 'Vous ne voyez que les participations de vos amis.';
                        } else {
                            echo 'Aucun utilisateur n\'a partagé ses participations publiquement pour le moment.';
                        }
                        ?>
                    </p>
                    <?php if ($viewMode === 'friends_only'): ?>
                        <a href="index.php?ctl=parametres&action=afficher_parametres" class="inline-block px-6 py-3 bg-red-500 text-white font-semibold rounded-lg shadow-md hover:bg-red-600 hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200">
                            Modifier mes paramètres
                        </a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="space-y-4">
                    <?php 
                    $cardIndex = 0;
                    $gradients = [
                        'from-blue-500 to-cyan-500',
                        'from-purple-500 to-pink-500',
                        'from-green-500 to-emerald-500',
                        'from-orange-500 to-red-500',
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
                                <div class="relative z-10 flex items-center space-x-3">
                                    <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center backdrop-blur-sm">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-bold text-lg"><?php echo htmlspecialchars($participation['user_name']); ?></p>
                                        <p class="text-xs text-white text-opacity-80"><?php echo htmlspecialchars($participation['user_email']); ?></p>
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
                                <div class="flex items-center flex-wrap gap-4 text-sm text-gray-600 font-medium mb-4">
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
                                    
                                    <span class="px-3 py-2 text-xs font-semibold bg-red-100 text-red-700 rounded-lg">
                                        <?php echo htmlspecialchars($participation['activity_type']); ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

