<?php
require_once __DIR__ . '/../Model/FriendModel.php';

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['connect']) || $_SESSION['connect'] !== true) {
    $_SESSION['error'] = 'Vous devez être connecté pour accéder à cette page';
    header('Location: index.php?ctl=connexion&action=connexion');
    exit();
}

$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    $_SESSION['error'] = 'Erreur de session';
    header('Location: index.php?ctl=connexion&action=connexion');
    exit();
}

$action = $_GET['action'] ?? 'liste';

switch ($action) {
    case 'liste':
        // Afficher la liste des amis
        $friends = FriendModel::getFriends($userId);
        $pendingRequests = FriendModel::getPendingRequests($userId);
        $sentRequests = FriendModel::getSentRequests($userId);
        include __DIR__ . '/../Vue/vueAmis/v_liste_amis.php';
        break;
    
    case 'ajouter':
        // Traiter l'ajout d'un ami par email
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            
            if (empty($email)) {
                $_SESSION['error'] = 'Veuillez entrer une adresse email';
                header('Location: index.php?ctl=amis&action=liste');
                exit();
            }
            
            // Valider le format de l'email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['error'] = 'Format d\'email invalide';
                header('Location: index.php?ctl=amis&action=liste');
                exit();
            }
            
            // Vérifier que l'utilisateur ne s'ajoute pas lui-même
            require_once __DIR__ . '/../Model/UserModel.php';
            $currentUser = UserModel::getUserById($userId);
            if ($currentUser && strtolower($currentUser['email']) === strtolower($email)) {
                $_SESSION['error'] = 'Vous ne pouvez pas vous ajouter vous-même comme ami';
                header('Location: index.php?ctl=amis&action=liste');
                exit();
            }
            
            // Chercher l'utilisateur par email
            $friend = FriendModel::findUserByEmail($email);
            
            if (!$friend) {
                $_SESSION['error'] = 'Aucun utilisateur trouvé avec cet email';
                header('Location: index.php?ctl=amis&action=liste');
                exit();
            }
            
            $friendId = $friend['id'];
            
            // Vérifier si une relation existe déjà
            if (FriendModel::friendshipExists($userId, $friendId)) {
                $_SESSION['error'] = 'Une relation d\'amitié existe déjà avec cet utilisateur';
                header('Location: index.php?ctl=amis&action=liste');
                exit();
            }
            
            // Envoyer la demande d'amitié
            $result = FriendModel::sendFriendRequest($userId, $friendId);
            
            if ($result) {
                $_SESSION['success'] = 'Demande d\'amitié envoyée à ' . htmlspecialchars($friend['name']);
            } else {
                $_SESSION['error'] = 'Erreur lors de l\'envoi de la demande d\'amitié';
            }
            
            header('Location: index.php?ctl=amis&action=liste');
            exit();
        } else {
            header('Location: index.php?ctl=amis&action=liste');
            exit();
        }
        break;
    
    case 'accepter':
        // Accepter une demande d'amitié
        $friendId = $_GET['id'] ?? null;
        
        if ($friendId) {
            $result = FriendModel::acceptFriendRequest($userId, $friendId);
            if ($result) {
                $_SESSION['success'] = 'Demande d\'amitié acceptée';
            } else {
                $_SESSION['error'] = 'Erreur lors de l\'acceptation de la demande';
            }
        }
        
        header('Location: index.php?ctl=amis&action=liste');
        exit();
        break;
    
    case 'refuser':
        // Refuser une demande d'amitié
        $friendId = $_GET['id'] ?? null;
        
        if ($friendId) {
            $result = FriendModel::declineFriendRequest($userId, $friendId);
            if ($result) {
                $_SESSION['success'] = 'Demande d\'amitié refusée';
            } else {
                $_SESSION['error'] = 'Erreur lors du refus de la demande';
            }
        }
        
        header('Location: index.php?ctl=amis&action=liste');
        exit();
        break;
    
    case 'supprimer':
        // Supprimer un ami
        $friendId = $_GET['id'] ?? null;
        
        if ($friendId) {
            $result = FriendModel::removeFriend($userId, $friendId);
            if ($result) {
                $_SESSION['success'] = 'Ami supprimé de votre liste';
            } else {
                $_SESSION['error'] = 'Erreur lors de la suppression';
            }
        }
        
        header('Location: index.php?ctl=amis&action=liste');
        exit();
        break;
    
    default:
        header('Location: index.php?ctl=amis&action=liste');
        exit();
}
?>



