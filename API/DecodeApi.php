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
     * Logique simple : si existe déjà, on ignore. Sinon, on ajoute.
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
        $errors = 0;
        
        foreach ($data['results'] as $record) {
            try {
                // Extraire les données
                $item = $record['fields'] ?? $record['record']['fields'] ?? $record;
                
                // Normaliser les valeurs
                $libelle = trim($item['libelle'] ?? '');
                $adresse = trim($item['adresse'] ?? '');
                
                if (empty($libelle) && empty($adresse)) {
                    continue; // Ignorer si pas de libelle ni d'adresse
                }
                
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
                
                // Vérifier si l'aire existe déjà
                // Clé unique : libelle + adresse
                if ($adresse) {
                $stmt = $db->prepare("SELECT id FROM aires_jeux WHERE libelle = ? AND adresse = ?");
                    $stmt->execute([$libelle, $adresse]);
                } else {
                    $stmt = $db->prepare("SELECT id FROM aires_jeux WHERE libelle = ? AND (adresse IS NULL OR adresse = '')");
                    $stmt->execute([$libelle]);
                }
                $existing = $stmt->fetch();
                
                if ($existing) {
                    // Existe déjà, on ignore
                    continue;
                }
                
                // N'existe pas, on ajoute
                    $stmt = $db->prepare("
                        INSERT INTO aires_jeux 
                        (famille_eqpt, public_autorise, libelle, tranches_age, pmr, 
                         acces_entree_pmr, acces_sol_pmr, acces_modules_pmr, adresse, 
                         commune, codeinsee, latitude, longitude, photo, data_json)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ");
                    $result = $stmt->execute([
                    trim($item['famille_eqpt'] ?? '') ?: null,
                    trim($item['public_autorise'] ?? '') ?: null,
                    $libelle ?: 'Nom non disponible',
                    trim($item['tranches_age'] ?? '') ?: null,
                    trim($item['pmr'] ?? '') ?: null,
                    trim($item['acces_entree_pmr'] ?? '') ?: null,
                    trim($item['acces_sol_pmr'] ?? '') ?: null,
                    trim($item['acces_modules_pmr'] ?? '') ?: null,
                    $adresse ?: null,
                    trim($item['commune'] ?? '') ?: null,
                    trim($item['codeinsee'] ?? '') ?: null,
                        $lat,
                        $lon,
                    trim($item['photo'] ?? '') ?: null,
                        json_encode($item)
                    ]);
                
                if ($result) {
                    $saved++;
                } else {
                    $errors++;
                }
            } catch (Exception $e) {
                $errors++;
                error_log("Erreur aires de jeux: " . $e->getMessage());
            }
        }
        
        echo "✅ Aires de jeux: $saved créées, $errors erreurs\n";
        return ['success' => true, 'saved' => $saved, 'updated' => 0, 'errors' => $errors];
    }
    
    /**
     * Synchroniser les équipements sportifs
     * Logique simple : si existe déjà, on ignore. Sinon, on ajoute.
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
        $errors = 0;
        
        foreach ($data['results'] as $record) {
            try {
                // Extraire les données
                $item = $record['fields'] ?? $record['record']['fields'] ?? $record;
                
                // Normaliser les valeurs
                $equipNom = trim($item['equip_nom'] ?? '');
                $adresse = trim($item['adr_num_et_rue'] ?? '');
                
                if (empty($equipNom) && empty($adresse)) {
                    continue; // Ignorer si pas de nom ni d'adresse
                }
                
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
                
                // Vérifier si l'équipement existe déjà
                // Clé unique : equip_nom + adr_num_et_rue
                if ($adresse) {
                $stmt = $db->prepare("SELECT id FROM equipements_sportifs WHERE equip_nom = ? AND adr_num_et_rue = ?");
                    $stmt->execute([$equipNom, $adresse]);
                } else {
                    $stmt = $db->prepare("SELECT id FROM equipements_sportifs WHERE equip_nom = ? AND (adr_num_et_rue IS NULL OR adr_num_et_rue = '')");
                    $stmt->execute([$equipNom]);
                }
                $existing = $stmt->fetch();
                
                if ($existing) {
                    // Existe déjà, on ignore
                    continue;
                }
                
                // N'existe pas, on ajoute
                    $stmt = $db->prepare("
                        INSERT INTO equipements_sportifs 
                        (equip_theme, equip_type, equip_nom, adr_codepostal, adr_commune, 
                         adr_code_insee_com, adr_num_et_rue, latitude, longitude, data_json)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ");
                    $result = $stmt->execute([
                    trim($item['equip_theme'] ?? '') ?: null,
                    trim($item['equip_type'] ?? '') ?: null,
                    $equipNom ?: 'Nom non disponible',
                    trim($item['adr_codepostal'] ?? '') ?: null,
                    trim($item['adr_commune'] ?? '') ?: null,
                    trim($item['adr_code_insee_com'] ?? '') ?: null,
                    $adresse ?: null,
                        $lat,
                        $lon,
                        json_encode($item)
                    ]);
                
                if ($result) {
                    $saved++;
                } else {
                    $errors++;
                }
            } catch (Exception $e) {
                $errors++;
                error_log("Erreur équipements sportifs: " . $e->getMessage());
            }
        }
        
        echo "✅ Équipements sportifs: $saved créés, $errors erreurs\n";
        return ['success' => true, 'saved' => $saved, 'updated' => 0, 'errors' => $errors];
    }
    
    /**
     * Synchroniser les manifestations sportives
     * Logique simple : si existe déjà, on ignore. Sinon, on ajoute.
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
        $errors = 0;
        
        foreach ($data['results'] as $record) {
            try {
                // Extraire les données - l'API OpenData peut avoir les données dans 'fields' ou directement
                $item = $record['fields'] ?? $record['record']['fields'] ?? $record;
                
                // Normaliser les valeurs
                $manifestation = trim($item['manifestation'] ?? '');
                if (empty($manifestation)) {
                    continue; // Ignorer si pas de nom
                }
                
                // Convertir les dates
                $dateDebut = null;
                $dateFin = null;
                if (isset($item['date_debut']) && !empty($item['date_debut'])) {
                    $dateDebut = date('Y-m-d', strtotime($item['date_debut']));
                }
                if (isset($item['date_de_fin']) && !empty($item['date_de_fin'])) {
                    $dateFin = date('Y-m-d', strtotime($item['date_de_fin']));
                }
                
                // Vérifier si la manifestation existe déjà
                // Clé unique : manifestation + date_debut + lieu
                $lieu = trim($item['lieu'] ?? '');
                
                if ($dateDebut) {
                    // Si on a une date, chercher avec date
                    if ($lieu) {
                        $stmt = $db->prepare("SELECT id FROM manifestations_sportives WHERE manifestation = ? AND date_debut = ? AND lieu = ?");
                        $stmt->execute([$manifestation, $dateDebut, $lieu]);
                    } else {
                        $stmt = $db->prepare("SELECT id FROM manifestations_sportives WHERE manifestation = ? AND date_debut = ? AND (lieu IS NULL OR lieu = '')");
                        $stmt->execute([$manifestation, $dateDebut]);
                    }
                } else {
                    // Si pas de date, chercher par nom et lieu uniquement
                    if ($lieu) {
                        $stmt = $db->prepare("SELECT id FROM manifestations_sportives WHERE manifestation = ? AND lieu = ? AND date_debut IS NULL");
                        $stmt->execute([$manifestation, $lieu]);
                    } else {
                        $stmt = $db->prepare("SELECT id FROM manifestations_sportives WHERE manifestation = ? AND date_debut IS NULL AND (lieu IS NULL OR lieu = '')");
                        $stmt->execute([$manifestation]);
                    }
                }
                $existing = $stmt->fetch();
                
                if ($existing) {
                    // Existe déjà, on ignore
                    continue;
                }
                
                // N'existe pas, on ajoute
                $lat = null;
                $lon = null;
                
                    $stmt = $db->prepare("
                        INSERT INTO manifestations_sportives 
                        (association_ou_service, manifestation, date_debut, date_de_fin, lieu, 
                         adresse, commune, latitude, longitude, data_json)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ");
                    $result = $stmt->execute([
                    trim($item['association_ou_service'] ?? ''),
                    $manifestation,
                        $dateDebut,
                        $dateFin,
                    $lieu,
                    null,
                    null,
                        $lat,
                        $lon,
                        json_encode($item)
                    ]);
                
                if ($result) {
                    $saved++;
                } else {
                    $errors++;
                }
            } catch (Exception $e) {
                $errors++;
                error_log("Erreur manifestations sportives: " . $e->getMessage());
            }
        }
        
        echo "✅ Manifestations sportives: $saved créées, $errors erreurs\n";
        return ['success' => true, 'saved' => $saved, 'updated' => 0, 'errors' => $errors];
    }
    
    /**
     * Synchroniser l'agenda culturel
     * Logique simple : si existe déjà, on ignore. Sinon, on ajoute.
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
        $errors = 0;
        
        foreach ($data['results'] as $record) {
            try {
                // Extraire les données - l'API OpenData peut avoir les données dans 'fields' ou directement
                $item = $record['fields'] ?? $record['record']['fields'] ?? $record;
                
                // Normaliser les valeurs
                $nomSpectacle = trim($item['nom_du_spectacle'] ?? '');
                if (empty($nomSpectacle)) {
                    continue; // Ignorer si pas de nom
                }
                
                // Convertir la date
                $date = null;
                if (isset($item['date']) && !empty($item['date'])) {
                    $date = date('Y-m-d', strtotime($item['date']));
                }
                
                $lieu = trim($item['lieu_de_representation'] ?? '');
                $horaire = trim($item['horaire'] ?? '');
                
                // Vérifier si l'événement existe déjà
                // Clé unique : nom_du_spectacle + date + lieu + horaire
                if ($date && $nomSpectacle) {
                    if ($lieu && $horaire) {
                        $stmt = $db->prepare("SELECT id FROM agenda_culturel WHERE nom_du_spectacle = ? AND date = ? AND lieu_de_representation = ? AND horaire = ?");
                        $stmt->execute([$nomSpectacle, $date, $lieu, $horaire]);
                    } elseif ($lieu) {
                        $stmt = $db->prepare("SELECT id FROM agenda_culturel WHERE nom_du_spectacle = ? AND date = ? AND lieu_de_representation = ? AND (horaire IS NULL OR horaire = '')");
                        $stmt->execute([$nomSpectacle, $date, $lieu]);
                    } elseif ($horaire) {
                        $stmt = $db->prepare("SELECT id FROM agenda_culturel WHERE nom_du_spectacle = ? AND date = ? AND (lieu_de_representation IS NULL OR lieu_de_representation = '') AND horaire = ?");
                        $stmt->execute([$nomSpectacle, $date, $horaire]);
                    } else {
                        $stmt = $db->prepare("SELECT id FROM agenda_culturel WHERE nom_du_spectacle = ? AND date = ? AND (lieu_de_representation IS NULL OR lieu_de_representation = '') AND (horaire IS NULL OR horaire = '')");
                        $stmt->execute([$nomSpectacle, $date]);
                    }
                } elseif ($nomSpectacle) {
                    // Pas de date, chercher par nom uniquement
                    $stmt = $db->prepare("SELECT id FROM agenda_culturel WHERE nom_du_spectacle = ? AND date IS NULL");
                    $stmt->execute([$nomSpectacle]);
                } else {
                    $existing = false;
                }
                
                if (isset($stmt)) {
                $existing = $stmt->fetch();
                } else {
                    $existing = false;
                }
                
                if ($existing) {
                    // Existe déjà, on ignore
                    continue;
                }
                
                // N'existe pas, on ajoute
                $lat = null;
                $lon = null;
                
                    $stmt = $db->prepare("
                        INSERT INTO agenda_culturel 
                        (date, horaire, commune, thematique, nom_du_spectacle, lieu_de_representation, 
                         adresse, latitude, longitude, data_json)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ");
                    $result = $stmt->execute([
                        $date,
                    $horaire ?: null,
                    trim($item['commune'] ?? '') ?: null,
                    trim($item['thematique'] ?? '') ?: null,
                    $nomSpectacle,
                    $lieu ?: null,
                    null,
                        $lat,
                        $lon,
                        json_encode($item)
                    ]);
                
                if ($result) {
                    $saved++;
                } else {
                    $errors++;
                }
            } catch (Exception $e) {
                $errors++;
                error_log("Erreur agenda culturel: " . $e->getMessage());
            }
        }
        
        echo "✅ Agenda culturel: $saved créés, $errors erreurs\n";
        return ['success' => true, 'saved' => $saved, 'updated' => 0, 'errors' => $errors];
    }
    
    /**
     * Synchroniser les points d'intérêt
     * Logique simple : si existe déjà, on ignore. Sinon, on ajoute.
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
        $errors = 0;
        
        foreach ($data['results'] as $record) {
            try {
                // Extraire les données
                $item = $record['fields'] ?? $record['record']['fields'] ?? $record;
                
                // Normaliser les valeurs
                $libelle = trim($item['libelle'] ?? '');
                $adresse = trim($item['adresse'] ?? '');
                
                if (empty($libelle) && empty($adresse)) {
                    continue; // Ignorer si pas de libelle ni d'adresse
                }
                
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
                
                // Vérifier si le point existe déjà
                // Clé unique : libelle + adresse
                if ($adresse) {
                $stmt = $db->prepare("SELECT id FROM points_interets WHERE libelle = ? AND adresse = ?");
                    $stmt->execute([$libelle, $adresse]);
                } else {
                    $stmt = $db->prepare("SELECT id FROM points_interets WHERE libelle = ? AND (adresse IS NULL OR adresse = '')");
                    $stmt->execute([$libelle]);
                }
                $existing = $stmt->fetch();
                
                if ($existing) {
                    // Existe déjà, on ignore
                    continue;
                }
                
                // N'existe pas, on ajoute
                    $stmt = $db->prepare("
                        INSERT INTO points_interets 
                        (libelle, thematique, descriptio, liens_vers, photo, credit_photo, 
                         adresse, commune, code_insee, latitude, longitude, data_json)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ");
                    $result = $stmt->execute([
                    $libelle ?: 'Nom non disponible',
                    trim($item['thematique'] ?? '') ?: null,
                    trim($item['descriptio'] ?? '') ?: null,
                    trim($item['liens_vers'] ?? '') ?: null,
                    trim($item['photo'] ?? '') ?: null,
                    trim($item['credit_photo'] ?? '') ?: null,
                    $adresse ?: null,
                    trim($item['commune'] ?? '') ?: null,
                    trim($item['code_insee'] ?? '') ?: null,
                        $lat,
                        $lon,
                        json_encode($item)
                    ]);
                
                if ($result) {
                    $saved++;
                } else {
                    $errors++;
                }
            } catch (Exception $e) {
                $errors++;
                error_log("Erreur points d'intérêt: " . $e->getMessage());
            }
        }
        
        echo "✅ Points d'intérêt: $saved créés, $errors erreurs\n";
        return ['success' => true, 'saved' => $saved, 'updated' => 0, 'errors' => $errors];
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
        echo "Manifestations sportives: {$resultManifestations['saved']} créées\n";
        echo "Agenda culturel: {$resultAgenda['saved']} créés\n";
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
        echo "Aires de jeux: {$resultAires['saved']} créées\n";
        echo "Équipements sportifs: {$resultEquipements['saved']} créés\n";
        echo "Points d'intérêt: {$resultPoints['saved']} créés\n";
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

