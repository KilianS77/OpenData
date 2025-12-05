<?php
session_start();

// Vérifier si c'est une requête AJAX (pour les paramètres et activités)
$isAjaxRequest = (
    (isset($_GET['ctl']) && $_GET['ctl'] === 'parametres' && 
     isset($_GET['action']) && $_GET['action'] === 'update_setting' &&
     $_SERVER['REQUEST_METHOD'] === 'POST') ||
    (isset($_GET['ctl']) && $_GET['ctl'] === 'activity' && 
     ($_SERVER['REQUEST_METHOD'] === 'POST' || (isset($_GET['action']) && $_GET['action'] === 'get_activities')))
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
            include 'Controleur/ctlParticipation.php';
            break;

        case 'parametres':
            include 'Controleur/ctlParametres.php';
            break;

        case 'amis':
            include 'Controleur/ctlAmis.php';
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
?>
