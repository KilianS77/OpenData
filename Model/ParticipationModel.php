<?php
require_once __DIR__ . '/MysqlDB.php';

class ParticipationModel {
    
    /**
     * Vérifier si une participation existe déjà pour un utilisateur, une activité et une date
     */
    public static function verifierParticipationDejaExistante($userId, $activityId, $activityType, $datePresence) {
        try {
            $db = MySqlDb::getPdoDb();
            $stmt = $db->prepare("
                SELECT * FROM participations 
                WHERE user_id = :userid 
                AND activity_id = :activityid 
                AND activity_type = :activitytype 
                AND date_presence = :datepresence
            ");
            $stmt->bindParam(':userid', $userId);
            $stmt->bindParam(':activityid', $activityId);
            $stmt->bindParam(':activitytype', $activityType);
            $stmt->bindParam(':datepresence', $datePresence);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
        } catch (PDOException $e) {
            error_log("Erreur verifierParticipationDejaExistante: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Créer une nouvelle participation
     */
    public static function createParticipation($userId, $activityType, $activityId, $datePresence, $heurePresence, $activityDescription) {
        // Fonction pour logger dans un fichier personnalisé
        $logToFile = function($message) {
            $logFile = __DIR__ . '/../logs/participation.log';
            $logDir = dirname($logFile);
            if (!file_exists($logDir)) {
                mkdir($logDir, 0755, true);
            }
            $timestamp = date('Y-m-d H:i:s');
            file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND);
        };
        
        try {
            // Log des paramètres reçus
            $logMsg = "=== CREATE PARTICIPATION ===\n";
            $logMsg .= "userId: " . $userId . "\n";
            $logMsg .= "activityType: " . $activityType . "\n";
            $logMsg .= "activityId: " . $activityId . " (type: " . gettype($activityId) . ")\n";
            $logMsg .= "datePresence: " . $datePresence . "\n";
            $logMsg .= "heurePresence: " . ($heurePresence ?? 'NULL') . "\n";
            $logMsg .= "activityDescription: " . $activityDescription;
            
            error_log($logMsg);
            $logToFile($logMsg);
            
            $db = MySqlDb::getPdoDb();
            $stmt = $db->prepare("
                INSERT INTO participations 
                (user_id, activity_type, activity_id, date_presence, heure_presence, activity_description) 
                VALUES (:userid, :activitytype, :activityid, :datepresence, :heurepresence, :activitydescription)
            ");
            
            // Convertir activityId en entier si c'est une chaîne
            $activityId = (int)$activityId;
            $userId = (int)$userId;
            
            $stmt->bindParam(':userid', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':activitytype', $activityType, PDO::PARAM_STR);
            $stmt->bindParam(':activityid', $activityId, PDO::PARAM_INT);
            $stmt->bindParam(':datepresence', $datePresence, PDO::PARAM_STR);
            $stmt->bindParam(':heurepresence', $heurePresence, PDO::PARAM_STR);
            $stmt->bindParam(':activitydescription', $activityDescription, PDO::PARAM_STR);
            
            $result = $stmt->execute();
            $rowCount = $stmt->rowCount();
            
            $logMsg = "execute() result: " . ($result ? 'true' : 'false') . "\n";
            $logMsg .= "rowCount(): " . $rowCount;
            error_log($logMsg);
            $logToFile($logMsg);
            
            if (!$result) {
                $errorInfo = $stmt->errorInfo();
                $errorMsg = "Erreur SQL createParticipation: " . print_r($errorInfo, true);
                error_log($errorMsg);
                $logToFile($errorMsg);
                return false;
            }
            
            // Vérifier que des lignes ont été insérées
            if ($rowCount === 0) {
                $errorInfo = $stmt->errorInfo();
                $errorMsg = "Aucune ligne insérée - ErrorInfo: " . print_r($errorInfo, true);
                error_log($errorMsg);
                $logToFile($errorMsg);
                
                // Vérifier si c'est une erreur de contrainte unique
                if (isset($errorInfo[1]) && $errorInfo[1] == 1062) {
                    $dupMsg = "Tentative de création d'un doublon détectée (code 1062)";
                    error_log($dupMsg);
                    $logToFile($dupMsg);
                }
                return false;
            }
            
            $successMsg = "✅ Participation créée avec succès (ID: " . $db->lastInsertId() . ")";
            error_log($successMsg);
            $logToFile($successMsg);
            return true;
            
        } catch (PDOException $e) {
            $logFile = __DIR__ . '/../logs/participation.log';
            $logDir = dirname($logFile);
            if (!file_exists($logDir)) {
                mkdir($logDir, 0755, true);
            }
            $logToFile = function($message) use ($logFile) {
                $timestamp = date('Y-m-d H:i:s');
                file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND);
            };
            
            $errorMsg = "❌ Erreur PDO createParticipation: " . $e->getMessage() . "\n";
            $errorMsg .= "Code erreur: " . $e->getCode() . "\n";
            $errorMsg .= "SQLSTATE: " . $e->getCode();
            error_log($errorMsg);
            $logToFile($errorMsg);
            
            // Si c'est une erreur de contrainte unique, on la gère spécifiquement
            if ($e->getCode() == 23000) { // SQLSTATE 23000 = Integrity constraint violation
                $constraintMsg = "Violation de contrainte d'intégrité (probablement doublon)";
                error_log($constraintMsg);
                $logToFile($constraintMsg);
            }
            return false;
        }
    }
    
    /**
     * Récupérer toutes les participations d'un utilisateur avec les détails des activités
     */
    public static function getParticipationByUserId($userId) {
        try {
            $db = MySqlDb::getPdoDb();
            $stmt = $db->prepare("
                SELECT p.*, 
                       CASE 
                           WHEN p.activity_type = 'aires_jeux' THEN aj.libelle
                           WHEN p.activity_type = 'equipements_sportifs' THEN es.equip_nom
                           ELSE 'Activité inconnue'
                       END as activity_name,
                       CASE 
                           WHEN p.activity_type = 'aires_jeux' THEN aj.adresse
                           WHEN p.activity_type = 'equipements_sportifs' THEN es.adr_num_et_rue
                           ELSE ''
                       END as activity_address
                FROM participations p
                LEFT JOIN aires_jeux aj ON p.activity_type = 'aires_jeux' AND p.activity_id = aj.id
                LEFT JOIN equipements_sportifs es ON p.activity_type = 'equipements_sportifs' AND p.activity_id = es.id
                WHERE p.user_id = :userid
                ORDER BY p.date_presence DESC, p.heure_presence DESC
            ");
            $stmt->bindParam(':userid', $userId);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getParticipationByUserId: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Récupérer une participation par son ID
     */
    public static function getParticipationById($participationId) {
        try {
            $db = MySqlDb::getPdoDb();
            $stmt = $db->prepare("SELECT * FROM participations WHERE id = :id");
            $stmt->bindParam(':id', $participationId);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getParticipationById: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Supprimer une participation
     */
    public static function deleteParticipation($participationId, $userId) {
        try {
            $db = MySqlDb::getPdoDb();
            $stmt = $db->prepare("DELETE FROM participations WHERE id = :id AND user_id = :userid");
            $stmt->bindParam(':id', $participationId);
            $stmt->bindParam(':userid', $userId);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erreur deleteParticipation: " . $e->getMessage());
            return false;
        }
    }
}
?>
