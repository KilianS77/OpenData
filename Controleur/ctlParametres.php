<?php
require_once __DIR__ . '/../Model/SettingsModel.php';

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['connect']) || $_SESSION['connect'] !== true) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'update_setting') {
        // Réponse JSON pour les requêtes AJAX
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Non authentifié']);
        exit();
    }
    header('Location: index.php?ctl=connexion&action=connexion');
    exit();
}

$action = $_GET['action'] ?? 'afficher_parametres';

switch ($action) {

    case 'afficher_parametres':
        include __DIR__ . '/../Vue/vueParametres/parametres.php';
        break;
    
    case 'update_setting':
        // Gérer les requêtes AJAX pour mettre à jour les paramètres
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');
            
            $userId = $_SESSION['user_id'] ?? null;
            $settingType = $_POST['setting_type'] ?? '';
            $value = $_POST['value'] ?? '';
            
            if (!$userId) {
                echo json_encode(['success' => false, 'message' => 'Utilisateur non connecté']);
                exit();
            }
            
            // Valider les valeurs
            if (!in_array($value, ['public', 'friends_only'])) {
                echo json_encode(['success' => false, 'message' => 'Valeur invalide: ' . $value]);
                exit();
            }
            
            if (empty($settingType)) {
                echo json_encode(['success' => false, 'message' => 'Type de paramètre manquant']);
                exit();
            }
            
            $success = false;
            $errorMessage = '';
            
            try {
                if ($settingType === 'participation_visibility') {
                    $success = SettingsModel::updateParticipationVisibility($userId, $value);
                } elseif ($settingType === 'view_participations') {
                    $success = SettingsModel::updateViewParticipations($userId, $value);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Type de paramètre invalide: ' . $settingType]);
                    exit();
                }
                
                if ($success) {
                    // Vérifier que la mise à jour a bien été effectuée
                    $settings = SettingsModel::getSettings($userId);
                    if ($settings && isset($settings[$settingType]) && $settings[$settingType] === $value) {
                        echo json_encode([
                            'success' => true, 
                            'message' => 'Paramètre mis à jour avec succès',
                            'value' => $value
                        ]);
                    } else {
                        echo json_encode([
                            'success' => false, 
                            'message' => 'Paramètre mis à jour mais valeur non vérifiée',
                            'debug' => $settings
                        ]);
                    }
                } else {
                    echo json_encode([
                        'success' => false, 
                        'message' => 'Erreur lors de la mise à jour en base de données'
                    ]);
                }
            } catch (Exception $e) {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Erreur: ' . $e->getMessage()
                ]);
            }
            exit();
        }
        break;
    
    default:
        include __DIR__ . '/../V/vueParametres/parametres.php';
        break;
}
?>