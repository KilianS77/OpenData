<div class="min-h-screen bg-white p-6">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white border border-red-500 p-8">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-light text-gray-900 tracking-tight">Mes participations</h1>
                <a href="index.php?ctl=map" class="px-4 py-2 text-sm text-red-500 border border-red-500 hover:bg-red-500 hover:text-white transition-colors font-light">
                    Voir la carte
                </a>
            </div>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 text-sm">
                    <?php 
                    echo $_SESSION['error'];
                    unset($_SESSION['error']);
                    ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['success'])): ?>
                <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 text-sm">
                    <?php 
                    echo $_SESSION['success'];
                    unset($_SESSION['success']);
                    ?>
                </div>
            <?php endif; ?>
            
            <?php if (empty($participations)): ?>
                <div class="text-center py-12">
                    <p class="text-gray-500 font-light mb-4">Vous n'avez aucune participation pour le moment</p>
                    <a href="index.php?ctl=map" class="inline-block px-6 py-2 bg-red-500 text-white font-light hover:bg-red-600 transition-colors">
                        Découvrir les activités
                    </a>
                </div>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($participations as $participation): ?>
                        <div class="border border-gray-200 p-4 hover:border-red-500 transition-colors">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h3 class="text-lg font-light text-gray-900 mb-2">
                                        <?php echo htmlspecialchars($participation['activity_name'] ?? $participation['activity_description']); ?>
                                    </h3>
                                    <?php if (!empty($participation['activity_address'])): ?>
                                        <p class="text-sm text-gray-600 font-light mb-2">
                                            <?php echo htmlspecialchars($participation['activity_address']); ?>
                                        </p>
                                    <?php endif; ?>
                                    
                                    <div class="flex items-center space-x-4 text-sm text-gray-500 font-light">
                                        <?php if ($participation['date_presence']): ?>
                                            <span>
                                                📅 <?php echo date('d/m/Y', strtotime($participation['date_presence'])); ?>
                                            </span>
                                        <?php endif; ?>
                                        
                                        <?php if ($participation['heure_presence']): ?>
                                            <span>
                                                🕐 <?php echo date('H:i', strtotime($participation['heure_presence'])); ?>
                                            </span>
                                        <?php endif; ?>
                                        
                                        <span class="px-2 py-1 text-xs border border-red-500 text-red-500">
                                            <?php echo htmlspecialchars($participation['activity_type']); ?>
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="ml-4">
                                    <a 
                                        href="index.php?ctl=participation&action=supprimer&id=<?php echo $participation['id']; ?>" 
                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette participation ?');"
                                        class="px-3 py-1 text-xs text-red-500 border border-red-500 hover:bg-red-500 hover:text-white transition-colors font-light"
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


