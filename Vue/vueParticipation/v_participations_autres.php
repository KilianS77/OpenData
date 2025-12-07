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
                    <?php foreach ($participations as $participation): ?>
                        <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm hover:shadow-md hover:border-red-300 transition-all duration-200">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <!-- Informations de l'utilisateur -->
                                    <div class="mb-3">
                                        <p class="text-sm font-semibold text-gray-900">
                                            👤 <?php echo htmlspecialchars($participation['user_name']); ?>
                                        </p>
                                        <p class="text-xs text-gray-500 font-light">
                                            <?php echo htmlspecialchars($participation['user_email']); ?>
                                        </p>
                                    </div>
                                    
                                    <!-- Informations de l'activité -->
                                    <h3 class="text-lg font-bold text-gray-900 mb-2">
                                        <?php echo htmlspecialchars($participation['activity_name'] ?? $participation['activity_description']); ?>
                                    </h3>
                                    <?php if (!empty($participation['activity_address'])): ?>
                                        <p class="text-sm text-gray-600 font-light mb-3">
                                            📍 <?php echo htmlspecialchars($participation['activity_address']); ?>
                                        </p>
                                    <?php endif; ?>
                                    
                                    <!-- Date et heure -->
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
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

