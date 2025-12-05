<?php
require_once __DIR__ . '/MysqlDB.php';

class ActivityModel {
    
    /**
     * Récupérer une activité par son activity_id (API ID)
     */
    public static function getActivityByApiId($activityId) {
        try {
            $db = MySqlDb::getPdoDb();
            $stmt = $db->prepare("SELECT * FROM activities WHERE activity_id = ?");
            $stmt->execute([$activityId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getActivityByApiId: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Créer ou mettre à jour une activité
     */
    public static function createOrUpdateActivity($activityId, $type, $name, $address, $commune, $lat, $lon, $description, $dataJson) {
        try {
            $db = MySqlDb::getPdoDb();
            
            // Vérifier si l'activité existe déjà (évite les doublons)
            $existing = self::getActivityByApiId($activityId);
            
            if ($existing) {
                // Mettre à jour l'activité existante
                $stmt = $db->prepare("
                    UPDATE activities 
                    SET type = ?, name = ?, address = ?, commune = ?, 
                        latitude = ?, longitude = ?, description = ?, data_json = ?
                    WHERE activity_id = ?
                ");
                $result = $stmt->execute([
                    $type,
                    $name,
                    $address,
                    $commune,
                    $lat,
                    $lon,
                    $description,
                    $dataJson,
                    $activityId
                ]);
                
                if (!$result) {
                    error_log("Erreur UPDATE activité {$activityId}: " . implode(', ', $stmt->errorInfo()));
                }
                
                return $result;
            } else {
                // Créer une nouvelle activité (pas de doublon)
                // Valider les données avant insertion
                if (empty($activityId) || empty($type) || empty($name)) {
                    error_log("Données invalides pour activité: activityId={$activityId}, type={$type}, name={$name}");
                    return false;
                }
                
                // Vérifier que les coordonnées sont valides
                if (!is_numeric($lat) || !is_numeric($lon)) {
                    error_log("Coordonnées invalides pour activité {$activityId}: lat={$lat}, lon={$lon}");
                    return false;
                }
                
                $stmt = $db->prepare("
                    INSERT INTO activities (activity_id, type, name, address, commune, latitude, longitude, description, data_json)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                
                try {
                    $result = $stmt->execute([
                        $activityId,
                        $type,
                        $name,
                        $address ?? null,
                        $commune ?? null,
                        $lat,
                        $lon,
                        $description ?? null,
                        $dataJson ?? null
                    ]);
                    
                    if ($result) {
                        error_log("✅ Activité créée avec succès: {$activityId} - {$name}");
                    } else {
                        $errorInfo = $stmt->errorInfo();
                        error_log("❌ Erreur INSERT activité {$activityId}: " . implode(' | ', $errorInfo));
                    }
                    
                    return $result;
                } catch (PDOException $e) {
                    error_log("❌ Exception PDO lors de l'INSERT activité {$activityId}: " . $e->getMessage());
                    return false;
                }
            }
        } catch (PDOException $e) {
            error_log("Erreur PDO createOrUpdateActivity pour {$activityId}: " . $e->getMessage());
            return false;
        } catch (Exception $e) {
            error_log("Erreur createOrUpdateActivity pour {$activityId}: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Récupérer toutes les activités
     */
    public static function getAllActivities() {
        try {
            $db = MySqlDb::getPdoDb();
            $stmt = $db->query("SELECT * FROM activities ORDER BY name");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getAllActivities: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Récupérer une activité par son ID (id de la table)
     */
    public static function getActivityById($id) {
        try {
            $db = MySqlDb::getPdoDb();
            $stmt = $db->prepare("SELECT * FROM activities WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getActivityById: " . $e->getMessage());
            return null;
        }
    }
}
?>

