<?php
/**
 * Script pour décoder les APIs et enregistrer les données en base de données
 * Gère les aires de jeux et les équipements sportifs
 */

require_once __DIR__ . '/../Model/MysqlDB.php';

class DecodeApi {
    
    private static $db;
    
    /**
     * Initialiser la connexion à la base de données
     */
    private static function initDb() {
        if (!self::$db) {
            self::$db = MySqlDb::getPdoDb();
        }
        return self::$db;
    }
    
    /**
     * Récupérer les données depuis une URL (avec fallback cURL/file_get_contents)
     */
    private static function fetchUrl($url) {
        // Essayer d'abord avec cURL
        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);
            
            if ($httpCode === 200 && $response) {
                return $response;
            }
        }
        
        // Fallback avec file_get_contents
        if (ini_get('allow_url_fopen')) {
            $context = stream_context_create([
                'http' => [
                    'timeout' => 30,
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                    'ignore_errors' => true
                ],
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false
                ]
            ]);
            
            $response = @file_get_contents($url, false, $context);
            if ($response !== false) {
                return $response;
            }
        }
        
        return false;
    }
    
    /**
     * Synchroniser les aires de jeux
     */
    public static function syncAiresJeux() {
        $url = 'https://data.melunvaldeseine.fr/api/explore/v2.1/catalog/datasets/aires-de-jeux/records?limit=-1';
        
        echo "Synchronisation des aires de jeux...\n";
        
        $response = self::fetchUrl($url);
        if (!$response) {
            echo "❌ Impossible de récupérer les données des aires de jeux\n";
            return ['success' => false, 'saved' => 0, 'updated' => 0, 'errors' => 1];
        }
        
        $data = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo "❌ Erreur de décodage JSON: " . json_last_error_msg() . "\n";
            return ['success' => false, 'saved' => 0, 'updated' => 0, 'errors' => 1];
        }
        
        if (!isset($data['results']) || !is_array($data['results'])) {
            echo "❌ Aucun résultat trouvé\n";
            return ['success' => false, 'saved' => 0, 'updated' => 0, 'errors' => 1];
        }
        
        $db = self::initDb();
        $saved = 0;
        $updated = 0;
        $errors = 0;
        
        foreach ($data['results'] as $item) {
            try {
                // Extraire les coordonnées
                $lat = 48.54;
                $lon = 2.66;
                if (isset($item['geo_point_2d'])) {
                    if (is_array($item['geo_point_2d']) && isset($item['geo_point_2d']['lat'])) {
                        $lat = floatval($item['geo_point_2d']['lat']);
                        $lon = floatval($item['geo_point_2d']['lon']);
                    } elseif (is_array($item['geo_point_2d']) && isset($item['geo_point_2d'][0])) {
                        $lat = floatval($item['geo_point_2d'][0]);
                        $lon = floatval($item['geo_point_2d'][1]);
                    }
                }
                
                // Vérifier si l'aire existe déjà (basé sur libelle + adresse)
                $stmt = $db->prepare("SELECT id FROM aires_jeux WHERE libelle = ? AND adresse = ?");
                $stmt->execute([
                    $item['libelle'] ?? '',
                    $item['adresse'] ?? ''
                ]);
                $existing = $stmt->fetch();
                
                if ($existing) {
                    // Mettre à jour
                    $stmt = $db->prepare("
                        UPDATE aires_jeux 
                        SET famille_eqpt = ?, public_autorise = ?, tranches_age = ?, 
                            pmr = ?, acces_entree_pmr = ?, acces_sol_pmr = ?, acces_modules_pmr = ?,
                            commune = ?, codeinsee = ?, latitude = ?, longitude = ?, 
                            photo = ?, data_json = ?
                        WHERE id = ?
                    ");
                    $result = $stmt->execute([
                        $item['famille_eqpt'] ?? null,
                        $item['public_autorise'] ?? null,
                        $item['tranches_age'] ?? null,
                        $item['pmr'] ?? null,
                        $item['acces_entree_pmr'] ?? null,
                        $item['acces_sol_pmr'] ?? null,
                        $item['acces_modules_pmr'] ?? null,
                        $item['commune'] ?? null,
                        $item['codeinsee'] ?? null,
                        $lat,
                        $lon,
                        $item['photo'] ?? null,
                        json_encode($item),
                        $existing['id']
                    ]);
                    if ($result) $updated++;
                } else {
                    // Créer
                    $stmt = $db->prepare("
                        INSERT INTO aires_jeux 
                        (famille_eqpt, public_autorise, libelle, tranches_age, pmr, 
                         acces_entree_pmr, acces_sol_pmr, acces_modules_pmr, adresse, 
                         commune, codeinsee, latitude, longitude, photo, data_json)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ");
                    $result = $stmt->execute([
                        $item['famille_eqpt'] ?? null,
                        $item['public_autorise'] ?? null,
                        $item['libelle'] ?? 'Nom non disponible',
                        $item['tranches_age'] ?? null,
                        $item['pmr'] ?? null,
                        $item['acces_entree_pmr'] ?? null,
                        $item['acces_sol_pmr'] ?? null,
                        $item['acces_modules_pmr'] ?? null,
                        $item['adresse'] ?? null,
                        $item['commune'] ?? null,
                        $item['codeinsee'] ?? null,
                        $lat,
                        $lon,
                        $item['photo'] ?? null,
                        json_encode($item)
                    ]);
                    if ($result) $saved++;
                }
            } catch (Exception $e) {
                $errors++;
                error_log("Erreur aires de jeux: " . $e->getMessage());
            }
        }
        
        echo "✅ Aires de jeux: $saved créées, $updated mises à jour, $errors erreurs\n";
        return ['success' => true, 'saved' => $saved, 'updated' => $updated, 'errors' => $errors];
    }
    
    /**
     * Synchroniser les équipements sportifs
     */
    public static function syncEquipementsSportifs() {
        $url = 'https://data.melunvaldeseine.fr/api/explore/v2.1/catalog/datasets/equipements-sportifs/records?limit=-1';
        
        echo "Synchronisation des équipements sportifs...\n";
        
        $response = self::fetchUrl($url);
        if (!$response) {
            echo "❌ Impossible de récupérer les données des équipements sportifs\n";
            return ['success' => false, 'saved' => 0, 'updated' => 0, 'errors' => 1];
        }
        
        $data = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo "❌ Erreur de décodage JSON: " . json_last_error_msg() . "\n";
            return ['success' => false, 'saved' => 0, 'updated' => 0, 'errors' => 1];
        }
        
        if (!isset($data['results']) || !is_array($data['results'])) {
            echo "❌ Aucun résultat trouvé\n";
            return ['success' => false, 'saved' => 0, 'updated' => 0, 'errors' => 1];
        }
        
        $db = self::initDb();
        $saved = 0;
        $updated = 0;
        $errors = 0;
        
        foreach ($data['results'] as $item) {
            try {
                // Extraire les coordonnées
                $lat = 48.54;
                $lon = 2.66;
                if (isset($item['equip_lat']) && isset($item['equip_long'])) {
                    $lat = floatval($item['equip_lat']);
                    $lon = floatval($item['equip_long']);
                } elseif (isset($item['point_geo'])) {
                    if (is_array($item['point_geo']) && isset($item['point_geo']['lat'])) {
                        $lat = floatval($item['point_geo']['lat']);
                        $lon = floatval($item['point_geo']['lon']);
                    } elseif (is_array($item['point_geo']) && isset($item['point_geo'][0])) {
                        $lat = floatval($item['point_geo'][0]);
                        $lon = floatval($item['point_geo'][1]);
                    }
                }
                
                // Vérifier si l'équipement existe déjà (basé sur equip_nom + adr_num_et_rue)
                $stmt = $db->prepare("SELECT id FROM equipements_sportifs WHERE equip_nom = ? AND adr_num_et_rue = ?");
                $stmt->execute([
                    $item['equip_nom'] ?? '',
                    $item['adr_num_et_rue'] ?? ''
                ]);
                $existing = $stmt->fetch();
                
                if ($existing) {
                    // Mettre à jour
                    $stmt = $db->prepare("
                        UPDATE equipements_sportifs 
                        SET equip_theme = ?, equip_type = ?, adr_codepostal = ?, 
                            adr_commune = ?, adr_code_insee_com = ?, 
                            latitude = ?, longitude = ?, data_json = ?
                        WHERE id = ?
                    ");
                    $result = $stmt->execute([
                        $item['equip_theme'] ?? null,
                        $item['equip_type'] ?? null,
                        $item['adr_codepostal'] ?? null,
                        $item['adr_commune'] ?? null,
                        $item['adr_code_insee_com'] ?? null,
                        $lat,
                        $lon,
                        json_encode($item),
                        $existing['id']
                    ]);
                    if ($result) $updated++;
                } else {
                    // Créer
                    $stmt = $db->prepare("
                        INSERT INTO equipements_sportifs 
                        (equip_theme, equip_type, equip_nom, adr_codepostal, adr_commune, 
                         adr_code_insee_com, adr_num_et_rue, latitude, longitude, data_json)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ");
                    $result = $stmt->execute([
                        $item['equip_theme'] ?? null,
                        $item['equip_type'] ?? null,
                        $item['equip_nom'] ?? 'Nom non disponible',
                        $item['adr_codepostal'] ?? null,
                        $item['adr_commune'] ?? null,
                        $item['adr_code_insee_com'] ?? null,
                        $item['adr_num_et_rue'] ?? null,
                        $lat,
                        $lon,
                        json_encode($item)
                    ]);
                    if ($result) $saved++;
                }
            } catch (Exception $e) {
                $errors++;
                error_log("Erreur équipements sportifs: " . $e->getMessage());
            }
        }
        
        echo "✅ Équipements sportifs: $saved créés, $updated mis à jour, $errors erreurs\n";
        return ['success' => true, 'saved' => $saved, 'updated' => $updated, 'errors' => $errors];
    }
    
    /**
     * Synchroniser les manifestations sportives
     */
    public static function syncManifestationsSportives() {
        $url = 'https://data.melunvaldeseine.fr/api/explore/v2.1/catalog/datasets/manifestations-sportives-en-2025-a-melun/records?limit=-1';
        
        echo "Synchronisation des manifestations sportives...\n";
        
        $response = self::fetchUrl($url);
        if (!$response) {
            echo "❌ Impossible de récupérer les données des manifestations sportives\n";
            return ['success' => false, 'saved' => 0, 'updated' => 0, 'errors' => 1];
        }
        
        $data = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo "❌ Erreur de décodage JSON: " . json_last_error_msg() . "\n";
            return ['success' => false, 'saved' => 0, 'updated' => 0, 'errors' => 1];
        }
        
        if (!isset($data['results']) || !is_array($data['results'])) {
            echo "❌ Aucun résultat trouvé\n";
            return ['success' => false, 'saved' => 0, 'updated' => 0, 'errors' => 1];
        }
        
        $db = self::initDb();
        $saved = 0;
        $updated = 0;
        $errors = 0;
        
        foreach ($data['results'] as $item) {
            try {
                // Extraire les coordonnées (si disponibles dans les données)
                $lat = null;
                $lon = null;
                // Les manifestations sportives n'ont pas toujours de coordonnées dans l'API
                // On devra peut-être géocoder le lieu plus tard
                
                // Convertir les dates
                $dateDebut = null;
                $dateFin = null;
                if (isset($item['date_debut'])) {
                    $dateDebut = date('Y-m-d', strtotime($item['date_debut']));
                }
                if (isset($item['date_de_fin'])) {
                    $dateFin = date('Y-m-d', strtotime($item['date_de_fin']));
                }
                
                // Vérifier si la manifestation existe déjà (basé sur manifestation + date_debut)
                $stmt = $db->prepare("SELECT id FROM manifestations_sportives WHERE manifestation = ? AND date_debut = ?");
                $stmt->execute([
                    $item['manifestation'] ?? '',
                    $dateDebut
                ]);
                $existing = $stmt->fetch();
                
                if ($existing) {
                    // Mettre à jour
                    $stmt = $db->prepare("
                        UPDATE manifestations_sportives 
                        SET association_ou_service = ?, date_de_fin = ?, lieu = ?, 
                            adresse = ?, commune = ?, latitude = ?, longitude = ?, data_json = ?
                        WHERE id = ?
                    ");
                    $result = $stmt->execute([
                        $item['association_ou_service'] ?? null,
                        $dateFin,
                        $item['lieu'] ?? null,
                        null, // adresse (à géocoder si nécessaire)
                        null, // commune (à extraire si disponible)
                        $lat,
                        $lon,
                        json_encode($item),
                        $existing['id']
                    ]);
                    if ($result) $updated++;
                } else {
                    // Créer
                    $stmt = $db->prepare("
                        INSERT INTO manifestations_sportives 
                        (association_ou_service, manifestation, date_debut, date_de_fin, lieu, 
                         adresse, commune, latitude, longitude, data_json)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ");
                    $result = $stmt->execute([
                        $item['association_ou_service'] ?? null,
                        $item['manifestation'] ?? 'Nom non disponible',
                        $dateDebut,
                        $dateFin,
                        $item['lieu'] ?? null,
                        null, // adresse
                        null, // commune
                        $lat,
                        $lon,
                        json_encode($item)
                    ]);
                    if ($result) $saved++;
                }
            } catch (Exception $e) {
                $errors++;
                error_log("Erreur manifestations sportives: " . $e->getMessage());
            }
        }
        
        echo "✅ Manifestations sportives: $saved créées, $updated mises à jour, $errors erreurs\n";
        return ['success' => true, 'saved' => $saved, 'updated' => $updated, 'errors' => $errors];
    }
    
    /**
     * Synchroniser l'agenda culturel
     */
    public static function syncAgendaCulturel() {
        $url = 'https://data.melunvaldeseine.fr/api/explore/v2.1/catalog/datasets/agenda-culturel-communautaire-2025-2026/records?limit=-1';
        
        echo "Synchronisation de l'agenda culturel...\n";
        
        $response = self::fetchUrl($url);
        if (!$response) {
            echo "❌ Impossible de récupérer les données de l'agenda culturel\n";
            return ['success' => false, 'saved' => 0, 'updated' => 0, 'errors' => 1];
        }
        
        $data = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo "❌ Erreur de décodage JSON: " . json_last_error_msg() . "\n";
            return ['success' => false, 'saved' => 0, 'updated' => 0, 'errors' => 1];
        }
        
        if (!isset($data['results']) || !is_array($data['results'])) {
            echo "❌ Aucun résultat trouvé\n";
            return ['success' => false, 'saved' => 0, 'updated' => 0, 'errors' => 1];
        }
        
        $db = self::initDb();
        $saved = 0;
        $updated = 0;
        $errors = 0;
        
        foreach ($data['results'] as $item) {
            try {
                // Extraire les coordonnées (si disponibles dans les données)
                $lat = null;
                $lon = null;
                // L'agenda culturel n'a pas toujours de coordonnées dans l'API
                // On devra peut-être géocoder le lieu plus tard
                
                // Convertir la date
                $date = null;
                if (isset($item['date'])) {
                    $date = date('Y-m-d', strtotime($item['date']));
                }
                
                // Vérifier si l'événement existe déjà (basé sur nom_du_spectacle + date + lieu)
                $stmt = $db->prepare("SELECT id FROM agenda_culturel WHERE nom_du_spectacle = ? AND date = ? AND lieu_de_representation = ?");
                $stmt->execute([
                    $item['nom_du_spectacle'] ?? '',
                    $date,
                    $item['lieu_de_representation'] ?? ''
                ]);
                $existing = $stmt->fetch();
                
                if ($existing) {
                    // Mettre à jour
                    $stmt = $db->prepare("
                        UPDATE agenda_culturel 
                        SET horaire = ?, commune = ?, thematique = ?, 
                            adresse = ?, latitude = ?, longitude = ?, data_json = ?
                        WHERE id = ?
                    ");
                    $result = $stmt->execute([
                        $item['horaire'] ?? null,
                        $item['commune'] ?? null,
                        $item['thematique'] ?? null,
                        null, // adresse
                        $lat,
                        $lon,
                        json_encode($item),
                        $existing['id']
                    ]);
                    if ($result) $updated++;
                } else {
                    // Créer
                    $stmt = $db->prepare("
                        INSERT INTO agenda_culturel 
                        (date, horaire, commune, thematique, nom_du_spectacle, lieu_de_representation, 
                         adresse, latitude, longitude, data_json)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ");
                    $result = $stmt->execute([
                        $date,
                        $item['horaire'] ?? null,
                        $item['commune'] ?? null,
                        $item['thematique'] ?? null,
                        $item['nom_du_spectacle'] ?? 'Nom non disponible',
                        $item['lieu_de_representation'] ?? null,
                        null, // adresse
                        $lat,
                        $lon,
                        json_encode($item)
                    ]);
                    if ($result) $saved++;
                }
            } catch (Exception $e) {
                $errors++;
                error_log("Erreur agenda culturel: " . $e->getMessage());
            }
        }
        
        echo "✅ Agenda culturel: $saved créés, $updated mis à jour, $errors erreurs\n";
        return ['success' => true, 'saved' => $saved, 'updated' => $updated, 'errors' => $errors];
    }
    
    /**
     * Synchroniser les points d'intérêt
     */
    public static function syncPointsInterets() {
        $url = 'https://data.melunvaldeseine.fr/api/explore/v2.1/catalog/datasets/points-d-interets/records?limit=-1';
        
        echo "Synchronisation des points d'intérêt...\n";
        
        $response = self::fetchUrl($url);
        if (!$response) {
            echo "❌ Impossible de récupérer les données des points d'intérêt\n";
            return ['success' => false, 'saved' => 0, 'updated' => 0, 'errors' => 1];
        }
        
        $data = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo "❌ Erreur de décodage JSON: " . json_last_error_msg() . "\n";
            return ['success' => false, 'saved' => 0, 'updated' => 0, 'errors' => 1];
        }
        
        if (!isset($data['results']) || !is_array($data['results'])) {
            echo "❌ Aucun résultat trouvé\n";
            return ['success' => false, 'saved' => 0, 'updated' => 0, 'errors' => 1];
        }
        
        $db = self::initDb();
        $saved = 0;
        $updated = 0;
        $errors = 0;
        
        foreach ($data['results'] as $item) {
            try {
                // Extraire les coordonnées
                $lat = 48.54;
                $lon = 2.66;
                if (isset($item['geo_point_2d'])) {
                    if (is_array($item['geo_point_2d']) && isset($item['geo_point_2d']['lat'])) {
                        $lat = floatval($item['geo_point_2d']['lat']);
                        $lon = floatval($item['geo_point_2d']['lon']);
                    } elseif (is_array($item['geo_point_2d']) && isset($item['geo_point_2d'][0])) {
                        $lat = floatval($item['geo_point_2d'][0]);
                        $lon = floatval($item['geo_point_2d'][1]);
                    }
                } elseif (isset($item['geo_shape']['geometry']['coordinates'])) {
                    $coords = $item['geo_shape']['geometry']['coordinates'];
                    if (is_array($coords) && isset($coords[0])) {
                        if (is_array($coords[0])) {
                            $lon = floatval($coords[0][0]);
                            $lat = floatval($coords[0][1]);
                        } else {
                            $lon = floatval($coords[0]);
                            $lat = floatval($coords[1]);
                        }
                    }
                }
                
                // Vérifier si le point existe déjà (basé sur libelle + adresse)
                $stmt = $db->prepare("SELECT id FROM points_interets WHERE libelle = ? AND adresse = ?");
                $stmt->execute([
                    $item['libelle'] ?? '',
                    $item['adresse'] ?? ''
                ]);
                $existing = $stmt->fetch();
                
                if ($existing) {
                    // Mettre à jour
                    $stmt = $db->prepare("
                        UPDATE points_interets 
                        SET thematique = ?, descriptio = ?, liens_vers = ?, 
                            photo = ?, credit_photo = ?, commune = ?, code_insee = ?, 
                            latitude = ?, longitude = ?, data_json = ?
                        WHERE id = ?
                    ");
                    $result = $stmt->execute([
                        $item['thematique'] ?? null,
                        $item['descriptio'] ?? null,
                        $item['liens_vers'] ?? null,
                        $item['photo'] ?? null,
                        $item['credit_photo'] ?? null,
                        $item['commune'] ?? null,
                        $item['code_insee'] ?? null,
                        $lat,
                        $lon,
                        json_encode($item),
                        $existing['id']
                    ]);
                    if ($result) $updated++;
                } else {
                    // Créer
                    $stmt = $db->prepare("
                        INSERT INTO points_interets 
                        (libelle, thematique, descriptio, liens_vers, photo, credit_photo, 
                         adresse, commune, code_insee, latitude, longitude, data_json)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ");
                    $result = $stmt->execute([
                        $item['libelle'] ?? 'Nom non disponible',
                        $item['thematique'] ?? null,
                        $item['descriptio'] ?? null,
                        $item['liens_vers'] ?? null,
                        $item['photo'] ?? null,
                        $item['credit_photo'] ?? null,
                        $item['adresse'] ?? null,
                        $item['commune'] ?? null,
                        $item['code_insee'] ?? null,
                        $lat,
                        $lon,
                        json_encode($item)
                    ]);
                    if ($result) $saved++;
                }
            } catch (Exception $e) {
                $errors++;
                error_log("Erreur points d'intérêt: " . $e->getMessage());
            }
        }
        
        echo "✅ Points d'intérêt: $saved créés, $updated mis à jour, $errors erreurs\n";
        return ['success' => true, 'saved' => $saved, 'updated' => $updated, 'errors' => $errors];
    }
    
    /**
     * Synchroniser uniquement les événements (manifestations sportives et agenda culturel)
     */
    public static function syncEvenements() {
        echo "=== Début de la synchronisation des événements ===\n\n";
        
        $resultManifestations = self::syncManifestationsSportives();
        echo "\n";
        $resultAgenda = self::syncAgendaCulturel();
        
        echo "\n=== Résumé ===\n";
        echo "Manifestations sportives: {$resultManifestations['saved']} créées, {$resultManifestations['updated']} mises à jour\n";
        echo "Agenda culturel: {$resultAgenda['saved']} créés, {$resultAgenda['updated']} mis à jour\n";
        echo "Total erreurs: " . ($resultManifestations['errors'] + $resultAgenda['errors']) . "\n";
        
        return [
            'success' => $resultManifestations['success'] && $resultAgenda['success'],
            'manifestations' => $resultManifestations,
            'agenda' => $resultAgenda
        ];
    }
    
    /**
     * Synchroniser toutes les données (sans les événements qui sont synchronisés séparément)
     */
    public static function syncAll() {
        echo "=== Début de la synchronisation ===\n\n";
        
        $resultAires = self::syncAiresJeux();
        echo "\n";
        $resultEquipements = self::syncEquipementsSportifs();
        echo "\n";
        $resultPoints = self::syncPointsInterets();
        
        echo "\n=== Résumé ===\n";
        echo "Aires de jeux: {$resultAires['saved']} créées, {$resultAires['updated']} mises à jour\n";
        echo "Équipements sportifs: {$resultEquipements['saved']} créés, {$resultEquipements['updated']} mis à jour\n";
        echo "Points d'intérêt: {$resultPoints['saved']} créés, {$resultPoints['updated']} mis à jour\n";
        echo "Total erreurs: " . ($resultAires['errors'] + $resultEquipements['errors'] + $resultPoints['errors']) . "\n";
        
        return [
            'success' => $resultAires['success'] && $resultEquipements['success'] && $resultPoints['success'],
            'aires' => $resultAires,
            'equipements' => $resultEquipements,
            'points' => $resultPoints
        ];
    }
}

// Si appelé directement en ligne de commande
if (php_sapi_name() === 'cli') {
    DecodeApi::syncAll();
}
?>

