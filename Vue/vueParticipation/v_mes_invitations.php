<div class="min-h-screen bg-gradient-to-br from-gray-50 to-white p-6">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Mes invitations</h1>
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
            
            <?php if (empty($invitations)): ?>
                <div class="text-center py-12 bg-white border border-gray-200 rounded-xl shadow-sm">
                    <p class="text-gray-500 font-light mb-4">Vous n'avez aucune invitation en attente</p>
                </div>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($invitations as $invitation): ?>
                        <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm hover:shadow-md hover:border-blue-300 transition-all duration-200">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="mb-3">
                                        <p class="text-sm font-semibold text-gray-900">
                                            👤 <?php echo htmlspecialchars($invitation['from_user_name']); ?>
                                        </p>
                                        <p class="text-xs text-gray-500 font-light">
                                            <?php echo htmlspecialchars($invitation['from_user_email']); ?>
                                        </p>
                                    </div>
                                    
                                    <h3 class="text-lg font-bold text-gray-900 mb-2">
                                        <?php echo htmlspecialchars($invitation['activity_name'] ?? 'Activité'); ?>
                                    </h3>
                                    <?php if (!empty($invitation['activity_address'])): ?>
                                        <p class="text-sm text-gray-600 font-light mb-3">
                                            📍 <?php echo htmlspecialchars($invitation['activity_address']); ?>
                                        </p>
                                    <?php endif; ?>
                                    
                                    <div class="flex items-center space-x-4 text-sm text-gray-500 font-light">
                                        <?php if ($invitation['date_presence']): ?>
                                            <span class="flex items-center gap-1">
                                                📅 <?php echo date('d/m/Y', strtotime($invitation['date_presence'])); ?>
                                            </span>
                                        <?php endif; ?>
                                        
                                        <?php if ($invitation['heure_presence']): ?>
                                            <span class="flex items-center gap-1">
                                                🕐 <?php echo date('H:i', strtotime($invitation['heure_presence'])); ?>
                                            </span>
                                        <?php endif; ?>
                                        
                                        <span class="px-3 py-1 text-xs font-semibold bg-blue-100 text-blue-700 rounded-full">
                                            <?php echo htmlspecialchars($invitation['activity_type']); ?>
                                        </span>
                                    </div>
                                    
                                    <?php if (!empty($invitation['message'])): ?>
                                        <p class="text-sm text-gray-600 font-light mt-3 italic">
                                            "<?php echo htmlspecialchars($invitation['message']); ?>"
                                        </p>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="ml-4 flex gap-2">
                                    <a 
                                        href="index.php?ctl=participation&action=accepter_invitation&id=<?php echo $invitation['id']; ?>" 
                                        class="px-4 py-2 text-sm font-semibold text-green-500 border-2 border-green-500 rounded-lg hover:bg-green-500 hover:text-white transition-all duration-200 shadow-sm hover:shadow-md"
                                    >
                                        Accepter
                                    </a>
                                    <a 
                                        href="index.php?ctl=participation&action=refuser_invitation&id=<?php echo $invitation['id']; ?>" 
                                        class="px-4 py-2 text-sm font-semibold text-red-500 border-2 border-red-500 rounded-lg hover:bg-red-500 hover:text-white transition-all duration-200 shadow-sm hover:shadow-md"
                                    >
                                        Refuser
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

