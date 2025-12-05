<?php
require_once __DIR__ . '/MysqlDB.php';

class FriendModel {
    
    /**
     * Récupérer tous les amis acceptés d'un utilisateur
     */
    public static function getFriends($userId) {
        try {
            $db = MySqlDb::getPdoDb();
            $stmt = $db->prepare("
                SELECT f.*, 
                       u1.id as friend_user_id,
                       u1.name as friend_name,
                       u1.email as friend_email
                FROM friends f
                INNER JOIN users u1 ON (
                    (f.user_id = :userid AND f.friend_id = u1.id) OR
                    (f.friend_id = :userid AND f.user_id = u1.id)
                )
                WHERE (f.user_id = :userid OR f.friend_id = :userid)
                AND f.status = 'accepted'
                ORDER BY u1.name ASC
            ");
            $stmt->bindParam(':userid', $userId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getFriends: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Récupérer les demandes d'amitié en attente (reçues)
     */
    public static function getPendingRequests($userId) {
        try {
            $db = MySqlDb::getPdoDb();
            $stmt = $db->prepare("
                SELECT f.*, 
                       u.name as sender_name,
                       u.email as sender_email
                FROM friends f
                INNER JOIN users u ON f.user_id = u.id
                WHERE f.friend_id = :userid
                AND f.status = 'pending'
                ORDER BY f.created_at DESC
            ");
            $stmt->bindParam(':userid', $userId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getPendingRequests: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Récupérer les demandes d'amitié envoyées en attente
     */
    public static function getSentRequests($userId) {
        try {
            $db = MySqlDb::getPdoDb();
            $stmt = $db->prepare("
                SELECT f.*, 
                       u.name as friend_name,
                       u.email as friend_email
                FROM friends f
                INNER JOIN users u ON f.friend_id = u.id
                WHERE f.user_id = :userid
                AND f.status = 'pending'
                ORDER BY f.created_at DESC
            ");
            $stmt->bindParam(':userid', $userId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getSentRequests: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Trouver un utilisateur par son email
     */
    public static function findUserByEmail($email) {
        try {
            $db = MySqlDb::getPdoDb();
            $stmt = $db->prepare("SELECT id, name, email FROM users WHERE email = ?");
            $stmt->execute([$email]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur findUserByEmail: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Vérifier si une relation d'amitié existe déjà
     */
    public static function friendshipExists($userId, $friendId) {
        try {
            $db = MySqlDb::getPdoDb();
            $stmt = $db->prepare("
                SELECT * FROM friends 
                WHERE (user_id = :userid AND friend_id = :friendid) 
                   OR (user_id = :friendid AND friend_id = :userid)
            ");
            $stmt->bindParam(':userid', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':friendid', $friendId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
        } catch (PDOException $e) {
            error_log("Erreur friendshipExists: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Envoyer une demande d'amitié
     */
    public static function sendFriendRequest($userId, $friendId) {
        try {
            $db = MySqlDb::getPdoDb();
            $stmt = $db->prepare("
                INSERT INTO friends (user_id, friend_id, status) 
                VALUES (:userid, :friendid, 'pending')
            ");
            $stmt->bindParam(':userid', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':friendid', $friendId, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erreur sendFriendRequest: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Accepter une demande d'amitié
     */
    public static function acceptFriendRequest($userId, $friendId) {
        try {
            $db = MySqlDb::getPdoDb();
            $stmt = $db->prepare("
                UPDATE friends 
                SET status = 'accepted', updated_at = NOW()
                WHERE user_id = :friendid AND friend_id = :userid AND status = 'pending'
            ");
            $stmt->bindParam(':userid', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':friendid', $friendId, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erreur acceptFriendRequest: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Refuser/Supprimer une demande d'amitié
     */
    public static function declineFriendRequest($userId, $friendId) {
        try {
            $db = MySqlDb::getPdoDb();
            $stmt = $db->prepare("
                DELETE FROM friends 
                WHERE (user_id = :friendid AND friend_id = :userid) 
                   OR (user_id = :userid AND friend_id = :friendid)
            ");
            $stmt->bindParam(':userid', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':friendid', $friendId, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erreur declineFriendRequest: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Supprimer un ami (retirer de la liste d'amis)
     */
    public static function removeFriend($userId, $friendId) {
        // Même logique que declineFriendRequest
        return self::declineFriendRequest($userId, $friendId);
    }
}
?>


