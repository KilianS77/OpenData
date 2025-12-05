<?php
require_once __DIR__ . '/MysqlDB.php';

class UserModel {
    
    /**
     * Créer un nouvel utilisateur avec mot de passe hashé en bcrypt
     */
    public static function createUser($email, $password, $name) {
        try {
            $db = MySqlDb::getPdoDb();
            
            // Vérifier si l'email existe déjà
            $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                return ['success' => false, 'message' => 'Cet email est déjà utilisé'];
            }
            
            // Hasher le mot de passe avec bcrypt (génère automatiquement un sel unique)
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            
            // Démarrer une transaction
            $db->beginTransaction();
            
            try {
                // Insérer l'utilisateur
                $stmt = $db->prepare("INSERT INTO users (email, password, name) VALUES (?, ?, ?)");
                $result = $stmt->execute([$email, $hashedPassword, $name]);
                
                if (!$result) {
                    $db->rollBack();
                    return ['success' => false, 'message' => 'Erreur lors de la création du compte'];
                }
                
                $userId = $db->lastInsertId();
                
                // Créer les paramètres par défaut pour l'utilisateur
                require_once __DIR__ . '/SettingsModel.php';
                $settingsCreated = SettingsModel::createDefaultSettings($userId);
                
                if (!$settingsCreated) {
                    // Si la création des paramètres échoue, on continue quand même
                    // Les paramètres seront créés à la première connexion
                }
                
                // Valider la transaction
                $db->commit();
                
                return ['success' => true, 'message' => 'Compte créé avec succès', 'userId' => $userId];
                
            } catch (PDOException $e) {
                $db->rollBack();
                throw $e;
            }
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erreur base de données: ' . $e->getMessage()];
        }
    }
    
    /**
     * Vérifier les identifiants de connexion
     */
    public static function login($email, $password) {
        try {
            $db = MySqlDb::getPdoDb();
            
            // Récupérer l'utilisateur par email
            $stmt = $db->prepare("SELECT id, email, password, name FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$user) {
                return ['success' => false, 'message' => 'Email ou mot de passe incorrect'];
            }
            
            // Vérifier le mot de passe avec password_verify (compare avec le hash bcrypt)
            if (password_verify($password, $user['password'])) {
                return [
                    'success' => true,
                    'user' => [
                        'id' => $user['id'],
                        'email' => $user['email'],
                        'name' => $user['name']
                    ]
                ];
            } else {
                return ['success' => false, 'message' => 'Email ou mot de passe incorrect'];
            }
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erreur base de données: ' . $e->getMessage()];
        }
    }
    
    /**
     * Récupérer un utilisateur par son ID
     */
    public static function getUserById($userId) {
        try {
            $db = MySqlDb::getPdoDb();
            $stmt = $db->prepare("SELECT id, email, name FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }
}
?>

