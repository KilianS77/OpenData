<div class="min-h-screen bg-gradient-to-br from-gray-50 to-white p-6">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-6 tracking-tight">Mes amis</h1>
            
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
            
            <!-- Formulaire d'ajout d'ami -->
            <div class="mb-8 p-6 bg-gray-50 rounded-xl border border-gray-200 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 tracking-tight">Ajouter un ami</h2>
                <form method="POST" action="index.php?ctl=amis&action=ajouter" class="flex gap-4">
                    <input 
                        type="email" 
                        name="email" 
                        placeholder="Adresse email de l'ami"
                        required
                        class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:border-red-500 focus:ring-2 focus:ring-red-500 focus:ring-opacity-20 text-sm transition-all duration-200"
                    >
                    <button 
                        type="submit" 
                        class="px-6 py-3 bg-red-500 text-white font-semibold rounded-lg shadow-md hover:bg-red-600 hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200"
                    >
                        Envoyer une demande
                    </button>
                </form>
            </div>
            
            <!-- Demandes reçues en attente -->
            <?php if (!empty($pendingRequests)): ?>
                <div class="mb-8">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4 tracking-tight">Demandes reçues</h2>
                    <div class="space-y-3">
                        <?php foreach ($pendingRequests as $request): ?>
                            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm hover:shadow-lg hover:border-red-300 transition-all duration-300 card-hover card-animate observe-on-scroll flex items-center justify-between">
                                <div>
                                    <h3 class="font-semibold text-gray-900"><?php echo htmlspecialchars($request['sender_name']); ?></h3>
                                    <p class="text-sm text-gray-600 font-light"><?php echo htmlspecialchars($request['sender_email']); ?></p>
                                    <p class="text-xs text-gray-500 font-light mt-1">
                                        Demandé le <?php echo date('d/m/Y à H:i', strtotime($request['created_at'])); ?>
                                    </p>
                                </div>
                                <div class="flex gap-2">
                                    <a 
                                        href="index.php?ctl=amis&action=accepter&id=<?php echo $request['user_id']; ?>" 
                                        class="px-4 py-2 bg-green-500 text-white text-sm font-semibold rounded-lg shadow-sm hover:bg-green-600 hover:shadow-md transition-all duration-200"
                                    >
                                        Accepter
                                    </a>
                                    <a 
                                        href="index.php?ctl=amis&action=refuser&id=<?php echo $request['user_id']; ?>" 
                                        class="px-4 py-2 bg-gray-500 text-white text-sm font-semibold rounded-lg shadow-sm hover:bg-gray-600 hover:shadow-md transition-all duration-200"
                                    >
                                        Refuser
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Demandes envoyées en attente -->
            <?php if (!empty($sentRequests)): ?>
                <div class="mb-8">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4 tracking-tight">Demandes envoyées</h2>
                    <div class="space-y-3">
                        <?php foreach ($sentRequests as $request): ?>
                            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm hover:shadow-lg hover:border-red-300 transition-all duration-300 card-hover card-animate observe-on-scroll flex items-center justify-between">
                                <div>
                                    <h3 class="font-semibold text-gray-900"><?php echo htmlspecialchars($request['friend_name']); ?></h3>
                                    <p class="text-sm text-gray-600 font-light"><?php echo htmlspecialchars($request['friend_email']); ?></p>
                                    <p class="text-xs text-gray-500 font-light mt-1">
                                        En attente depuis le <?php echo date('d/m/Y à H:i', strtotime($request['created_at'])); ?>
                                    </p>
                                </div>
                                <div>
                                    <span class="px-3 py-1.5 text-xs font-semibold bg-yellow-100 text-yellow-800 rounded-full">
                                        En attente
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Liste des amis acceptés -->
            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-4 tracking-tight">
                    Mes amis (<?php echo count($friends); ?>)
                </h2>
                
                <?php if (empty($friends)): ?>
                    <div class="text-center py-12 bg-white border border-gray-200 rounded-xl shadow-sm">
                        <p class="text-gray-500 font-light mb-4">Vous n'avez pas encore d'amis</p>
                        <p class="text-sm text-gray-400 font-light">Utilisez le formulaire ci-dessus pour ajouter des amis par leur adresse email</p>
                    </div>
                <?php else: ?>
                    <div class="space-y-3">
                        <?php foreach ($friends as $friend): ?>
                            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm hover:shadow-lg hover:border-red-300 transition-all duration-300 card-hover card-animate observe-on-scroll flex items-center justify-between">
                                <div>
                                    <h3 class="font-semibold text-gray-900"><?php echo htmlspecialchars($friend['friend_name']); ?></h3>
                                    <p class="text-sm text-gray-600 font-light"><?php echo htmlspecialchars($friend['friend_email']); ?></p>
                                </div>
                                <div>
                                    <a 
                                        href="index.php?ctl=amis&action=supprimer&id=<?php echo $friend['friend_user_id']; ?>" 
                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet ami de votre liste ?');"
                                        class="px-4 py-2 bg-red-500 text-white text-sm font-semibold rounded-lg shadow-sm hover:bg-red-600 hover:shadow-md transition-all duration-200"
                                    >
                                        Supprimer
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

