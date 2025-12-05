<?php
require_once __DIR__ . '/../Model/ParticipationModel.php';

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

$action = $_GET['action'] ?? 'mes_participations';

switch ($action) {
    case 'participer':
        // Afficher le formulaire de participation
        $activityType = $_GET['activity_type'] ?? '';
        $activityId = $_GET['activity_id'] ?? '';
        $activityDescription = $_GET['ActivityDescription'] ?? '';
        
        if (empty($activityType) || empty($activityId)) {
            $_SESSION['error'] = 'Informations d\'activité manquantes';
            header('Location: index.php?ctl=map');
            exit();
        }
        
        // Récupérer les détails de l'activité selon le type
        $activityName = '';
        $activityAddress = '';
        
        try {
            require_once __DIR__ . '/../Model/MysqlDB.php';
            $db = MySqlDb::getPdoDb();
            
            if ($activityType === 'aires_jeux') {
                $stmt = $db->prepare("SELECT libelle, adresse FROM aires_jeux WHERE id = ?");
                $stmt->execute([$activityId]);
                $activity = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($activity) {
                    $activityName = $activity['libelle'];
                    $activityAddress = $activity['adresse'];
                }
            } elseif ($activityType === 'equipements_sportifs') {
                $stmt = $db->prepare("SELECT equip_nom, adr_num_et_rue FROM equipements_sportifs WHERE id = ?");
                $stmt->execute([$activityId]);
                $activity = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($activity) {
                    $activityName = $activity['equip_nom'];
                    $activityAddress = $activity['adr_num_et_rue'];
                }
            }
        } catch (Exception $e) {
            error_log("Erreur récupération activité: " . $e->getMessage());
        }
        
        include __DIR__ . '/../Vue/vueParticipation/v_form_participation.php';
        break;
    
    case 'creer_participation':
        // Traiter le formulaire de participation
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $activityType = $_POST['activity_type'] ?? '';
            $activityId = $_POST['activity_id'] ?? '';
            $datePresence = $_POST['date_presence'] ?? '';
            $heurePresence = $_POST['heure_presence'] ?? '';
            $activityDescription = $_POST['activity_description'] ?? '';
            
            // Validations
            $errors = [];
            
            if (empty($activityType) || empty($activityId)) {
                $errors[] = 'Informations d\'activité manquantes';
            }
            
            if (empty($datePresence)) {
                $errors[] = 'La date de présence est obligatoire';
            } else {
                // Essayer plusieurs formats de date
                $datePresenceObj = DateTime::createFromFormat('Y-m-d', $datePresence);
                if (!$datePresenceObj) {
                    $datePresenceObj = DateTime::createFromFormat('d/m/Y', $datePresence);
                }
                
                if (!$datePresenceObj) {
                    $errors[] = 'Format de date invalide: ' . htmlspecialchars($datePresence);
                } else {
                    // Créer une date pour aujourd'hui à minuit
                    $today = new DateTime('today');
                    $datePresenceObj->setTime(0, 0, 0);
                    
                    // Comparer uniquement les dates (sans l'heure)
                    if ($datePresenceObj < $today) {
                        $errors[] = 'La date ne peut pas être antérieure à aujourd\'hui (date sélectionnée: ' . $datePresenceObj->format('d/m/Y') . ', aujourd\'hui: ' . $today->format('d/m/Y') . ')';
                    } else {
                        // Normaliser la date au format Y-m-d pour la base de données
                        $datePresence = $datePresenceObj->format('Y-m-d');
                    }
                }
            }
            
            if (!empty($heurePresence)) {
                $heurePresenceObj = DateTime::createFromFormat('H:i', $heurePresence);
                
                // Si la date est aujourd'hui, vérifier que l'heure n'est pas passée
                if ($datePresence === date('Y-m-d')) {
                    if (!$heurePresenceObj) {
                        $errors[] = 'Format d\'heure invalide';
                    } else {
                        $now = new DateTime();
                        $heurePresenceObj->setDate($now->format('Y'), $now->format('m'), $now->format('d'));
                        
                        if ($heurePresenceObj < $now) {
                            $errors[] = 'L\'heure ne peut pas être antérieure à l\'heure actuelle';
                        }
                    }
                }
            }
            
            if (empty($activityDescription)) {
                $errors[] = 'La description de l\'activité est obligatoire';
            }
            
            // Vérifier si déjà inscrit pour cette date
            if (empty($errors)) {
                $alreadyExists = ParticipationModel::verifierParticipationDejaExistante(
                    $userId, 
                    $activityId, 
                    $activityType, 
                    $datePresence
                );
                
                if ($alreadyExists) {
                    $errors[] = 'Vous êtes déjà inscrit à cette activité pour cette date';
                }
            }
            
            if (!empty($errors)) {
                $_SESSION['error'] = implode('<br>', $errors);
                $_SESSION['form_data'] = $_POST;
                header('Location: index.php?ctl=participation&action=participer&activity_type=' . urlencode($activityType) . '&activity_id=' . urlencode($activityId) . '&ActivityDescription=' . urlencode($activityDescription));
                exit();
            }
            
            // Créer la participation
            $heurePresence = !empty($heurePresence) ? $heurePresence : null;
            
            try {
                $result = ParticipationModel::createParticipation(
                    $userId,
                    $activityType,
                    $activityId,
                    $datePresence,
                    $heurePresence,
                    $activityDescription
                );
                
                if ($result) {
                    $_SESSION['success'] = 'Participation enregistrée avec succès !';
                    header('Location: index.php?ctl=participation&action=mes_participations');
                    exit();
                } else {
                    // Vérifier si c'est une erreur de contrainte unique (doublon)
                    $alreadyExists = ParticipationModel::verifierParticipationDejaExistante(
                        $userId, 
                        $activityId, 
                        $activityType, 
                        $datePresence
                    );
                    
                    if ($alreadyExists) {
                        $_SESSION['error'] = 'Vous êtes déjà inscrit à cette activité pour cette date';
                    } else {
                        $_SESSION['error'] = 'Erreur lors de l\'enregistrement de la participation. Veuillez réessayer.';
                    }
                    $_SESSION['form_data'] = $_POST;
                    header('Location: index.php?ctl=participation&action=participer&activity_type=' . urlencode($activityType) . '&activity_id=' . urlencode($activityId) . '&ActivityDescription=' . urlencode($activityDescription));
                    exit();
                }
            } catch (Exception $e) {
                error_log("❌ Exception lors de la création de participation: " . $e->getMessage());
                error_log("Stack trace: " . $e->getTraceAsString());
                $_SESSION['error'] = 'Erreur technique lors de l\'enregistrement: ' . htmlspecialchars($e->getMessage());
                $_SESSION['form_data'] = $_POST;
                header('Location: index.php?ctl=participation&action=participer&activity_type=' . urlencode($activityType) . '&activity_id=' . urlencode($activityId) . '&ActivityDescription=' . urlencode($activityDescription));
                exit();
            }
        } else {
            header('Location: index.php?ctl=participation&action=mes_participations');
            exit();
        }
        break;
    
    case 'mes_participations':
        // Afficher toutes les participations de l'utilisateur
        $participations = ParticipationModel::getParticipationByUserId($userId);
        include __DIR__ . '/../Vue/vueParticipation/v_liste_participations.php';
        break;
    
    case 'supprimer':
        // Supprimer une participation
        $participationId = $_GET['id'] ?? null;
        
        if ($participationId) {
            $result = ParticipationModel::deleteParticipation($participationId, $userId);
            if ($result) {
                $_SESSION['success'] = 'Participation supprimée avec succès';
            } else {
                $_SESSION['error'] = 'Erreur lors de la suppression';
            }
        }
        
        header('Location: index.php?ctl=participation&action=mes_participations');
        exit();
        break;
    
    default:
        header('Location: index.php?ctl=participation&action=mes_participations');
        exit();
}
?>

