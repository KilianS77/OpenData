<?php
require_once __DIR__ . '/../Model/MysqlDB.php';
require_once __DIR__ . '/../API/DecodeApi.php';

// Gérer les requêtes AJAX pour synchroniser les événements
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'sync') {
    header('Content-Type: application/json');
    
    // Démarrer un buffer de sortie pour capturer les echo de syncEvenements
    ob_start();
    
    // Utiliser DecodeApi pour synchroniser uniquement les événements
    $result = DecodeApi::syncEvenements();
    
    // Nettoyer le buffer de sortie (supprime tous les echo)
    ob_clean();
    
    echo json_encode([
        'success' => $result['success'],
        'manifestations' => $result['manifestations'],
        'agenda' => $result['agenda'],
        'total_saved' => $result['manifestations']['saved'] + $result['agenda']['saved'],
        'total_updated' => $result['manifestations']['updated'] + $result['agenda']['updated'],
        'total_errors' => $result['manifestations']['errors'] + $result['agenda']['errors']
    ]);
    exit();
}

$action = $_GET['action'] ?? 'liste';

switch ($action) {
    case 'liste':
        // Récupérer les manifestations sportives et agenda culturel valides
        try {
            $db = MySqlDb::getPdoDb();
            $today = new DateTime('today');
            $now = new DateTime();
            
            // Manifestations sportives valides (date_de_fin >= aujourd'hui ou NULL)
            $stmt = $db->query("
                SELECT id, association_ou_service, manifestation, date_debut, date_de_fin, lieu, commune, 
                       'manifestations_sportives' as type
                FROM manifestations_sportives 
                WHERE date_de_fin >= CURDATE() OR date_de_fin IS NULL
                ORDER BY date_debut ASC
            ");
            $manifestations = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Filtrer les manifestations avec date_de_fin passée
            $manifestationsValides = [];
            foreach ($manifestations as $manifestation) {
                if ($manifestation['date_de_fin']) {
                    $dateFin = new DateTime($manifestation['date_de_fin']);
                    if ($dateFin >= $today) {
                        $manifestationsValides[] = $manifestation;
                    }
                } else {
                    $manifestationsValides[] = $manifestation;
                }
            }
            
            // Agenda culturel valide
            $stmt = $db->query("
                SELECT id, date, horaire, commune, thematique, nom_du_spectacle, lieu_de_representation,
                       'agenda_culturel' as type
                FROM agenda_culturel
                ORDER BY date ASC, horaire ASC
            ");
            $agenda = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Filtrer l'agenda : ne pas afficher si date < aujourd'hui OU (date = aujourd'hui ET horaire < maintenant)
            $agendaValide = [];
            foreach ($agenda as $event) {
                if ($event['date']) {
                    $eventDate = new DateTime($event['date']);
                    
                    // Si la date est passée, ne pas inclure
                    if ($eventDate < $today) {
                        continue;
                    }
                    
                    // Si la date est aujourd'hui, vérifier l'horaire
                    if ($eventDate == $today && $event['horaire']) {
                        // Convertir l'horaire (format "18h30" ou "20h00") en DateTime
                        $horaireStr = str_replace('h', ':', $event['horaire']);
                        if (strlen($horaireStr) == 4) { // "18:30" -> "18:30:00"
                            $horaireStr .= ':00';
                        }
                        try {
                            $horaireEvent = DateTime::createFromFormat('H:i:s', $horaireStr);
                            if ($horaireEvent && $horaireEvent < $now) {
                                continue; // L'horaire est passé, ne pas inclure
                            }
                        } catch (Exception $e) {
                            // Si on ne peut pas parser l'horaire, on l'inclut quand même
                        }
                    }
                }
                $agendaValide[] = $event;
            }
            
            // Séparer en activités du jour et activités futures
            $activitesDuJour = [];
            $activitesFutures = [];
            
            // Traiter les manifestations
            foreach ($manifestationsValides as $manifestation) {
                $dateDebut = $manifestation['date_debut'] ? new DateTime($manifestation['date_debut']) : null;
                $dateFin = $manifestation['date_de_fin'] ? new DateTime($manifestation['date_de_fin']) : null;
                
                // Déterminer si c'est une activité du jour ou future
                $isToday = false;
                
                if ($dateDebut && $dateFin) {
                    // Si aujourd'hui est entre date_debut et date_de_fin (inclus)
                    if ($dateDebut <= $today && $dateFin >= $today) {
                        $isToday = true;
                    } elseif ($dateDebut > $today) {
                        // Future
                    }
                } elseif ($dateDebut) {
                    // Seulement date_debut
                    if ($dateDebut == $today) {
                        $isToday = true;
                    }
                } elseif ($dateFin) {
                    // Seulement date_de_fin
                    if ($dateFin == $today) {
                        $isToday = true;
                    }
                }
                
                if ($isToday) {
                    $activitesDuJour[] = $manifestation;
                } else {
                    // Si date_debut existe et est future, ou si aucune date n'est définie
                    if (($dateDebut && $dateDebut > $today) || (!$dateDebut && !$dateFin)) {
                        $activitesFutures[] = $manifestation;
                    } elseif ($dateFin && $dateFin > $today) {
                        // Si seulement date_fin est définie et future
                        $activitesFutures[] = $manifestation;
                    }
                }
            }
            
            // Traiter l'agenda
            foreach ($agendaValide as $event) {
                if ($event['date']) {
                    $eventDate = new DateTime($event['date']);
                    if ($eventDate == $today) {
                        $activitesDuJour[] = $event;
                    } elseif ($eventDate > $today) {
                        $activitesFutures[] = $event;
                    }
                } else {
                    // Pas de date, on le met dans les futures
                    $activitesFutures[] = $event;
                }
            }
            
            // Séparer les activités futures : celles avec date et celles sans date
            $activitesFuturesAvecDate = [];
            $activitesFuturesSansDate = [];
            
            foreach ($activitesFutures as $activite) {
                $hasDate = false;
                if ($activite['type'] === 'manifestations_sportives') {
                    $hasDate = !empty($activite['date_debut']) || !empty($activite['date_de_fin']);
                } else { // agenda_culturel
                    $hasDate = !empty($activite['date']);
                }
                
                if ($hasDate) {
                    $activitesFuturesAvecDate[] = $activite;
                } else {
                    $activitesFuturesSansDate[] = $activite;
                }
            }
            
            // Trier les activités futures avec date par date
            usort($activitesFuturesAvecDate, function($a, $b) {
                $dateA = $a['date_debut'] ?? $a['date'] ?? '';
                $dateB = $b['date_debut'] ?? $b['date'] ?? '';
                if ($dateA === $dateB) {
                    // Si même date, comparer les horaires
                    $horaireA = $a['horaire'] ?? '';
                    $horaireB = $b['horaire'] ?? '';
                    return strcmp($horaireA, $horaireB);
                }
                return strcmp($dateA, $dateB);
            });
            
            // Combiner : d'abord celles avec date, puis celles sans date
            $activitesFutures = array_merge($activitesFuturesAvecDate, $activitesFuturesSansDate);
            
            include __DIR__ . '/../Vue/vueEvenements/v_liste_evenements.php';
        } catch (Exception $e) {
            error_log("Erreur récupération événements: " . $e->getMessage());
            $activitesDuJour = [];
            $activitesFutures = [];
            include __DIR__ . '/../Vue/vueEvenements/v_liste_evenements.php';
        }
        break;
    
    default:
        header('Location: index.php?ctl=evenements&action=liste');
        exit();
}
?>

