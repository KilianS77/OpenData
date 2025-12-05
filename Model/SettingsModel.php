<?php
require_once __DIR__ . '/MysqlDB.php';

class SettingsModel {
    
    /**
     * Récupérer les paramètres d'un utilisateur
     */
    public static function getSettings($userId) {
        try {
            $db = MySqlDb::getPdoDb();
            $stmt = $db->prepare("SELECT * FROM user_settings WHERE user_id = ?");
            $stmt->execute([$userId]);
            $settings = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Si aucun paramètre n'existe, créer avec les valeurs par défaut
            if (!$settings) {
                return self::createDefaultSettings($userId);
            }
            
            return $settings;
        } catch (PDOException $e) {
            return null;
        }
    }
    
    /**
     * Créer les paramètres par défaut pour un utilisateur
     */
    public static function createDefaultSettings($userId) {
        try {
            $db = MySqlDb::getPdoDb();
            
            // Vérifier si les paramètres existent déjà
            $stmt = $db->prepare("SELECT id FROM user_settings WHERE user_id = ?");
            $stmt->execute([$userId]);
            if ($stmt->fetch()) {
                // Les paramètres existent déjà, les récupérer
                return self::getSettings($userId);
            }
            
            // Vérifier si la colonne view_participations existe
            try {
                $stmt = $db->prepare("
                    INSERT INTO user_settings (user_id, participation_visibility, view_participations) 
                    VALUES (?, 'friends_only', 'friends_only')
                ");
                $stmt->execute([$userId]);
            } catch (PDOException $e) {
                // Si la colonne view_participations n'existe pas, utiliser INSERT sans cette colonne
                // puis faire un ALTER TABLE pour l'ajouter
                if (strpos($e->getMessage(), 'view_participations') !== false || 
                    strpos($e->getMessage(), 'Unknown column') !== false) {
                    // Insérer sans view_participations
                    $stmt = $db->prepare("
                        INSERT INTO user_settings (user_id, participation_visibility) 
                        VALUES (?, 'friends_only')
                    ");
                    $stmt->execute([$userId]);
                    
                    // Essayer d'ajouter la colonne
                    try {
                        $db->exec("
                            ALTER TABLE user_settings 
                            ADD COLUMN view_participations ENUM('public', 'friends_only') 
                            DEFAULT 'friends_only' AFTER participation_visibility
                        ");
                        
                        // Mettre à jour la valeur
                        $stmt = $db->prepare("
                            UPDATE user_settings 
                            SET view_participations = 'friends_only' 
                            WHERE user_id = ?
                        ");
                        $stmt->execute([$userId]);
                    } catch (PDOException $e2) {
                        // Si l'ALTER échoue (colonne existe déjà), continuer
                    }
                } else {
                    throw $e;
                }
            }
            
            return [
                'user_id' => $userId,
                'participation_visibility' => 'friends_only',
                'view_participations' => 'friends_only',
                'notifications_enabled' => true,
                'email_notifications' => true
            ];
        } catch (PDOException $e) {
            error_log("Erreur création paramètres: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Mettre à jour la visibilité des participations
     */
    public static function updateParticipationVisibility($userId, $visibility) {
        try {
            $db = MySqlDb::getPdoDb();
            
            // Vérifier si les paramètres existent
            $settings = self::getSettings($userId);
            if (!$settings) {
                $settings = self::createDefaultSettings($userId);
                if (!$settings) {
                    return false;
                }
            }
            
            $stmt = $db->prepare("
                UPDATE user_settings 
                SET participation_visibility = ?, updated_at = NOW() 
                WHERE user_id = ?
            ");
            $result = $stmt->execute([$visibility, $userId]);
            
            return $result;
        } catch (PDOException $e) {
            error_log("Erreur updateParticipationVisibility: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Mettre à jour la visibilité des participations à voir
     */
    public static function updateViewParticipations($userId, $viewMode) {
        try {
            $db = MySqlDb::getPdoDb();
            
            // Vérifier si les paramètres existent
            $settings = self::getSettings($userId);
            if (!$settings) {
                $settings = self::createDefaultSettings($userId);
                if (!$settings) {
                    return false;
                }
            }
            
            // Vérifier si la colonne existe, sinon l'ajouter
            try {
                $stmt = $db->prepare("
                    UPDATE user_settings 
                    SET view_participations = ?, updated_at = NOW() 
                    WHERE user_id = ?
                ");
                $result = $stmt->execute([$viewMode, $userId]);
                return $result;
            } catch (PDOException $e) {
                // Si la colonne n'existe pas, l'ajouter
                if (strpos($e->getMessage(), 'view_participations') !== false || 
                    strpos($e->getMessage(), 'Unknown column') !== false) {
                    try {
                        $db->exec("
                            ALTER TABLE user_settings 
                            ADD COLUMN view_participations ENUM('public', 'friends_only') 
                            DEFAULT 'friends_only' AFTER participation_visibility
                        ");
                        
                        // Réessayer la mise à jour
                        $stmt = $db->prepare("
                            UPDATE user_settings 
                            SET view_participations = ?, updated_at = NOW() 
                            WHERE user_id = ?
                        ");
                        $result = $stmt->execute([$viewMode, $userId]);
                        return $result;
                    } catch (PDOException $e2) {
                        error_log("Erreur updateViewParticipations: " . $e2->getMessage());
                        return false;
                    }
                } else {
                    error_log("Erreur updateViewParticipations: " . $e->getMessage());
                    return false;
                }
            }
        } catch (PDOException $e) {
            error_log("Erreur updateViewParticipations: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Mettre à jour les deux paramètres en une seule fois
     */
    public static function updateSettings($userId, $participationVisibility, $viewParticipations) {
        try {
            $db = MySqlDb::getPdoDb();
            
            // Vérifier si les paramètres existent
            $settings = self::getSettings($userId);
            if (!$settings) {
                self::createDefaultSettings($userId);
            }
            
            $stmt = $db->prepare("
                UPDATE user_settings 
                SET participation_visibility = ?, view_participations = ?, updated_at = NOW() 
                WHERE user_id = ?
            ");
            $result = $stmt->execute([$participationVisibility, $viewParticipations, $userId]);
            
            return $result;
        } catch (PDOException $e) {
            return false;
        }
    }
}
?>

