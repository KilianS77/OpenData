<?php
session_start();

// Démarrer le output buffering pour permettre les redirections
ob_start();

// Vérifier si c'est une requête AJAX (pour les paramètres, activités, événements et amis)
$isAjaxRequest = (
    (isset($_GET['ctl']) && $_GET['ctl'] === 'parametres' && 
     isset($_GET['action']) && $_GET['action'] === 'update_setting' &&
     $_SERVER['REQUEST_METHOD'] === 'POST') ||
    (isset($_GET['ctl']) && $_GET['ctl'] === 'activity' && 
     ($_SERVER['REQUEST_METHOD'] === 'POST' || (isset($_GET['action']) && $_GET['action'] === 'get_activities'))) ||
    (isset($_GET['ctl']) && $_GET['ctl'] === 'evenements' && 
     isset($_GET['action']) && $_GET['action'] === 'sync' &&
     $_SERVER['REQUEST_METHOD'] === 'POST') ||
    (isset($_GET['ctl']) && $_GET['ctl'] === 'amis' && 
     isset($_GET['action']) && $_GET['action'] === 'get_friends_json') ||
    (isset($_GET['ctl']) && $_GET['ctl'] === 'participation' && 
     isset($_GET['action']) && $_GET['action'] === 'envoyer_invitation' &&
     $_SERVER['REQUEST_METHOD'] === 'POST')
);

// Ne pas inclure l'entête et le footer pour les requêtes AJAX
if (!$isAjaxRequest) {
    // Toujours inclure l'entête
    include 'Vue/Entetes_Footers/entete.php';
}

// Gestion des contrôleurs
if (isset($_GET['ctl'])) {
    switch ($_GET['ctl']) {

        case 'connexion':    
            include 'Controleur/ctlConnexion.php';
            break;

        case 'accueil':
            include 'Controleur/ctlAccueil.php';
            break;

        case 'map':
            include 'Vue/Map/map.php';
            break;

        case 'activity':
            include 'Controleur/ctlActivity.php';
            break;
        
        case 'participation':
            require_once __DIR__ . '/Controleur/ctlParticipation.php';
            break;

        case 'parametres':
            include 'Controleur/ctlParametres.php';
            break;

        case 'amis':
            include 'Controleur/ctlAmis.php';
            break;

        case 'evenements':
            include 'Controleur/ctlEvenements.php';
            break;

        default:
            // Par défaut, afficher la landing page
            include 'Vue/body.php';
            break;
    }
} else {
    // Par défaut, afficher la landing page (body.php)
    include 'Vue/body.php';
}

// Ne pas inclure le footer pour les requêtes AJAX
if (!$isAjaxRequest) {
    // Toujours inclure le footer
    include 'Vue/Entetes_Footers/footer.php';
}

// Vérifier si une redirection a été effectuée (headers déjà envoyés)
// Si oui, nettoyer le buffer et ne rien afficher
if (headers_sent()) {
    ob_end_clean();
} else {
    // Afficher le contenu du buffer
    ob_end_flush();
}
?>
