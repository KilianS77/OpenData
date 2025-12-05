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
            } elseif ($activityType === 'manifestations_sportives') {
                $stmt = $db->prepare("SELECT manifestation, lieu FROM manifestations_sportives WHERE id = ?");
                $stmt->execute([$activityId]);
                $activity = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($activity) {
                    $activityName = $activity['manifestation'];
                    $activityAddress = $activity['lieu'];
                }
            } elseif ($activityType === 'agenda_culturel') {
                $stmt = $db->prepare("SELECT nom_du_spectacle, lieu_de_representation FROM agenda_culturel WHERE id = ?");
                $stmt->execute([$activityId]);
                $activity = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($activity) {
                    $activityName = $activity['nom_du_spectacle'];
                    $activityAddress = $activity['lieu_de_representation'];
                }
            } elseif ($activityType === 'points_interets') {
                $stmt = $db->prepare("SELECT libelle, adresse FROM points_interets WHERE id = ?");
                $stmt->execute([$activityId]);
                $activity = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($activity) {
                    $activityName = $activity['libelle'];
                    $activityAddress = $activity['adresse'];
                }
            }
        } catch (Exception $e) {
            error_log("Erreur récupération activité: " . $e->getMessage());
        }
        
        include __DIR__ . '/../Vue/vueParticipation/v_form_participation.php';
        break;
    
    case 'participer_manifestation':
        // Afficher le formulaire de participation pour une manifestation avec plage de dates
        $activityType = $_GET['activity_type'] ?? '';
        $activityId = $_GET['activity_id'] ?? '';
        
        if (empty($activityType) || empty($activityId) || $activityType !== 'manifestations_sportives') {
            $_SESSION['error'] = 'Informations d\'activité manquantes';
            header('Location: index.php?ctl=evenements&action=liste');
            exit();
        }
        
        try {
            require_once __DIR__ . '/../Model/MysqlDB.php';
            $db = MySqlDb::getPdoDb();
            
            $stmt = $db->prepare("SELECT manifestation, lieu, date_debut, date_de_fin, commune FROM manifestations_sportives WHERE id = ?");
            $stmt->execute([$activityId]);
            $event = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$event) {
                $_SESSION['error'] = 'Manifestation introuvable';
                header('Location: index.php?ctl=evenements&action=liste');
                exit();
            }
            
            if (!$event['date_debut'] || !$event['date_de_fin'] || $event['date_debut'] === $event['date_de_fin']) {
                // Si les dates sont identiques, rediriger vers la participation directe
                header('Location: index.php?ctl=participation&action=participer_evenement&activity_type=' . urlencode($activityType) . '&activity_id=' . urlencode($activityId));
                exit();
            }
            
            $activityName = $event['manifestation'];
            $activityAddress = $event['lieu'];
            $dateDebut = $event['date_debut'];
            $dateFin = $event['date_de_fin'];
            $activityDescription = ($event['manifestation'] ?? '') . ' - ' . ($event['lieu'] ?? '');
            
            include __DIR__ . '/../Vue/vueParticipation/v_form_participation_manifestation.php';
        } catch (Exception $e) {
            error_log("Erreur récupération manifestation: " . $e->getMessage());
            $_SESSION['error'] = 'Erreur lors de la récupération de la manifestation';
            header('Location: index.php?ctl=evenements&action=liste');
            exit();
        }
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
                // Rediriger vers le bon formulaire selon le type
                if ($activityType === 'manifestations_sportives') {
                    header('Location: index.php?ctl=participation&action=participer_manifestation&activity_type=' . urlencode($activityType) . '&activity_id=' . urlencode($activityId));
                } else {
                    header('Location: index.php?ctl=participation&action=participer&activity_type=' . urlencode($activityType) . '&activity_id=' . urlencode($activityId) . '&ActivityDescription=' . urlencode($activityDescription));
                }
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
    
    case 'creer_participation_manifestation':
        // Traiter le formulaire de participation pour une manifestation avec plage de dates
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $activityType = $_POST['activity_type'] ?? '';
            $activityId = $_POST['activity_id'] ?? '';
            $datePresence = $_POST['date_presence'] ?? '';
            $activityDescription = $_POST['activity_description'] ?? '';
            $dateDebut = $_POST['date_debut'] ?? '';
            $dateFin = $_POST['date_fin'] ?? '';
            
            // Validations
            $errors = [];
            
            if (empty($activityType) || empty($activityId) || $activityType !== 'manifestations_sportives') {
                $errors[] = 'Informations d\'activité invalides';
            }
            
            if (empty($datePresence)) {
                $errors[] = 'La date de participation est obligatoire';
            } else {
                $datePresenceObj = DateTime::createFromFormat('Y-m-d', $datePresence);
                
                if (!$datePresenceObj) {
                    $errors[] = 'Format de date invalide';
                } else {
                    // Vérifier que la date est entre date_debut et date_fin
                    if ($dateDebut && $dateFin) {
                        $dateDebutObj = new DateTime($dateDebut);
                        $dateFinObj = new DateTime($dateFin);
                        $datePresenceObj->setTime(0, 0, 0);
                        $dateDebutObj->setTime(0, 0, 0);
                        $dateFinObj->setTime(0, 0, 0);
                        
                        if ($datePresenceObj < $dateDebutObj || $datePresenceObj > $dateFinObj) {
                            $errors[] = 'La date doit être comprise entre le ' . $dateDebutObj->format('d/m/Y') . ' et le ' . $dateFinObj->format('d/m/Y');
                        } else {
                            // Normaliser la date au format Y-m-d pour la base de données
                            $datePresence = $datePresenceObj->format('Y-m-d');
                        }
                    } else {
                        $errors[] = 'Dates de la manifestation manquantes';
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
                    $errors[] = 'Vous êtes déjà inscrit à cette manifestation pour cette date';
                }
            }
            
            if (!empty($errors)) {
                $_SESSION['error'] = implode('<br>', $errors);
                $_SESSION['form_data'] = $_POST;
                header('Location: index.php?ctl=participation&action=participer_manifestation&activity_type=' . urlencode($activityType) . '&activity_id=' . urlencode($activityId));
                exit();
            }
            
            // Créer la participation (sans heure pour les manifestations)
            try {
                $result = ParticipationModel::createParticipation(
                    $userId,
                    $activityType,
                    $activityId,
                    $datePresence,
                    null, // Pas d'heure pour les manifestations
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
                        $_SESSION['error'] = 'Vous êtes déjà inscrit à cette manifestation pour cette date';
                    } else {
                        $_SESSION['error'] = 'Erreur lors de l\'enregistrement de la participation. Veuillez réessayer.';
                    }
                    $_SESSION['form_data'] = $_POST;
                    header('Location: index.php?ctl=participation&action=participer_manifestation&activity_type=' . urlencode($activityType) . '&activity_id=' . urlencode($activityId));
                    exit();
                }
            } catch (Exception $e) {
                error_log("❌ Exception lors de la création de participation: " . $e->getMessage());
                error_log("Stack trace: " . $e->getTraceAsString());
                $_SESSION['error'] = 'Erreur technique lors de l\'enregistrement: ' . htmlspecialchars($e->getMessage());
                $_SESSION['form_data'] = $_POST;
                header('Location: index.php?ctl=participation&action=participer_manifestation&activity_type=' . urlencode($activityType) . '&activity_id=' . urlencode($activityId));
                exit();
            }
        } else {
            header('Location: index.php?ctl=evenements&action=liste');
            exit();
        }
        break;
    
    case 'mes_participations':
        // Afficher toutes les participations de l'utilisateur
        $participations = ParticipationModel::getParticipationByUserId($userId);
        include __DIR__ . '/../Vue/vueParticipation/v_liste_participations.php';
        break;
    
    case 'participer_evenement':
        // Participation directe à un événement (sans formulaire)
        $activityType = $_GET['activity_type'] ?? '';
        $activityId = $_GET['activity_id'] ?? '';
        
        if (empty($activityType) || empty($activityId)) {
            $_SESSION['error'] = 'Informations d\'événement manquantes';
            header('Location: index.php?ctl=evenements&action=liste');
            exit();
        }
        
        // Vérifier que c'est bien un événement (manifestations_sportives ou agenda_culturel)
        if ($activityType !== 'manifestations_sportives' && $activityType !== 'agenda_culturel') {
            $_SESSION['error'] = 'Cette action n\'est disponible que pour les événements';
            header('Location: index.php?ctl=evenements&action=liste');
            exit();
        }
        
        try {
            require_once __DIR__ . '/../Model/MysqlDB.php';
            $db = MySqlDb::getPdoDb();
            
            $datePresence = null;
            $heurePresence = null;
            $activityDescription = '';
            
            // Récupérer les informations de l'événement
            if ($activityType === 'manifestations_sportives') {
                $stmt = $db->prepare("SELECT manifestation, lieu, date_debut, date_de_fin, commune FROM manifestations_sportives WHERE id = ?");
                $stmt->execute([$activityId]);
                $event = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$event) {
                    $_SESSION['error'] = 'Événement introuvable';
                    header('Location: index.php?ctl=evenements&action=liste');
                    exit();
                }
                
                // Si date_debut et date_de_fin sont différentes, rediriger vers le formulaire de sélection de date
                if ($event['date_debut'] && $event['date_de_fin'] && $event['date_debut'] !== $event['date_de_fin']) {
                    // Rediriger vers le formulaire avec les informations de la manifestation
                    header('Location: index.php?ctl=participation&action=participer_manifestation&activity_type=' . urlencode($activityType) . '&activity_id=' . urlencode($activityId));
                    exit();
                }
                
                // Si les dates sont identiques ou une seule date, utiliser directement
                $datePresence = $event['date_debut'] ?: $event['date_de_fin'];
                $activityDescription = ($event['manifestation'] ?? '') . ' - ' . ($event['lieu'] ?? '');
                
            } else { // agenda_culturel
                $stmt = $db->prepare("SELECT nom_du_spectacle, lieu_de_representation, date, horaire, commune FROM agenda_culturel WHERE id = ?");
                $stmt->execute([$activityId]);
                $event = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$event) {
                    $_SESSION['error'] = 'Événement introuvable';
                    header('Location: index.php?ctl=evenements&action=liste');
                    exit();
                }
                
                $datePresence = $event['date'];
                
                // Convertir l'horaire en format TIME (HH:MM:SS)
                if ($event['horaire']) {
                    $horaireStr = str_replace('h', ':', $event['horaire']);
                    if (strlen($horaireStr) == 4) { // "18:30" -> "18:30:00"
                        $horaireStr .= ':00';
                    }
                    $heurePresence = $horaireStr;
                }
                
                $activityDescription = ($event['nom_du_spectacle'] ?? '') . ' - ' . ($event['lieu_de_representation'] ?? '');
            }
            
            if (!$datePresence) {
                $_SESSION['error'] = 'Date de l\'événement non disponible';
                header('Location: index.php?ctl=evenements&action=liste');
                exit();
            }
            
            // Vérifier si déjà inscrit pour cette date
            $alreadyExists = ParticipationModel::verifierParticipationDejaExistante(
                $userId, 
                $activityId, 
                $activityType, 
                $datePresence
            );
            
            if ($alreadyExists) {
                $_SESSION['error'] = 'Vous êtes déjà inscrit à cet événement';
                header('Location: index.php?ctl=evenements&action=liste');
                exit();
            }
            
            // Créer la participation directement
            $result = ParticipationModel::createParticipation(
                $userId,
                $activityType,
                $activityId,
                $datePresence,
                $heurePresence,
                $activityDescription
            );
            
            if ($result) {
                $_SESSION['success'] = 'Vous êtes maintenant inscrit à cet événement !';
                header('Location: index.php?ctl=participation&action=mes_participations');
                exit();
            } else {
                $_SESSION['error'] = 'Erreur lors de l\'inscription. Veuillez réessayer.';
                header('Location: index.php?ctl=evenements&action=liste');
                exit();
            }
            
        } catch (Exception $e) {
            error_log("Erreur participation événement: " . $e->getMessage());
            $_SESSION['error'] = 'Erreur technique lors de l\'inscription';
            header('Location: index.php?ctl=evenements&action=liste');
            exit();
        }
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

