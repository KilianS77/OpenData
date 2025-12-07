<?php
require_once __DIR__ . '/MysqlDB.php';
require_once __DIR__ . '/ParticipationModel.php';

class InvitationModel {
    
    /**
     * Envoyer une invitation
     */
    public static function sendInvitation($fromUserId, $toUserId, $activityType, $activityId, $datePresence, $heurePresence, $message = null) {
        try {
            $db = MySqlDb::getPdoDb();
            
            // Vérifier qu'il n'y a pas déjà une invitation en attente
            $stmt = $db->prepare("
                SELECT id FROM invitations 
                WHERE from_user_id = ? 
                AND to_user_id = ? 
                AND activity_type = ? 
                AND activity_id = ? 
                AND date_presence = ?
                AND status = 'pending'
            ");
            $stmt->execute([$fromUserId, $toUserId, $activityType, $activityId, $datePresence]);
            if ($stmt->fetch()) {
                return false; // Invitation déjà envoyée
            }
            
            $stmt = $db->prepare("
                INSERT INTO invitations 
                (from_user_id, to_user_id, activity_type, activity_id, date_presence, heure_presence, message, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')
            ");
            return $stmt->execute([$fromUserId, $toUserId, $activityType, $activityId, $datePresence, $heurePresence, $message]);
        } catch (PDOException $e) {
            error_log("Erreur sendInvitation: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Récupérer les invitations reçues par un utilisateur
     */
    public static function getReceivedInvitations($userId) {
        try {
            $db = MySqlDb::getPdoDb();
            
            // Supprimer automatiquement les invitations expirées
            self::deleteExpiredInvitations($db);
            
            $stmt = $db->prepare("
                SELECT i.*, 
                       u.name as from_user_name,
                       u.email as from_user_email,
                       CASE 
                           WHEN i.activity_type = 'aires_jeux' THEN aj.libelle
                           WHEN i.activity_type = 'equipements_sportifs' THEN es.equip_nom
                           WHEN i.activity_type = 'manifestations_sportives' THEN ms.manifestation
                           WHEN i.activity_type = 'agenda_culturel' THEN ac.nom_du_spectacle
                           WHEN i.activity_type = 'points_interets' THEN pi.libelle
                           ELSE 'Activité inconnue'
                       END as activity_name,
                       CASE 
                           WHEN i.activity_type = 'aires_jeux' THEN aj.adresse
                           WHEN i.activity_type = 'equipements_sportifs' THEN es.adr_num_et_rue
                           WHEN i.activity_type = 'manifestations_sportives' THEN ms.lieu
                           WHEN i.activity_type = 'agenda_culturel' THEN ac.lieu_de_representation
                           WHEN i.activity_type = 'points_interets' THEN pi.adresse
                           ELSE ''
                       END as activity_address
                FROM invitations i
                INNER JOIN users u ON i.from_user_id = u.id
                LEFT JOIN aires_jeux aj ON i.activity_type = 'aires_jeux' AND i.activity_id = aj.id
                LEFT JOIN equipements_sportifs es ON i.activity_type = 'equipements_sportifs' AND i.activity_id = es.id
                LEFT JOIN manifestations_sportives ms ON i.activity_type = 'manifestations_sportives' AND i.activity_id = ms.id
                LEFT JOIN agenda_culturel ac ON i.activity_type = 'agenda_culturel' AND i.activity_id = ac.id
                LEFT JOIN points_interets pi ON i.activity_type = 'points_interets' AND i.activity_id = pi.id
                WHERE i.to_user_id = ? 
                AND i.status = 'pending'
                ORDER BY i.created_at DESC
            ");
            $stmt->execute([$userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getReceivedInvitations: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Accepter une invitation
     */
    public static function acceptInvitation($invitationId, $userId) {
        try {
            $db = MySqlDb::getPdoDb();
            $db->beginTransaction();
            
            // Récupérer l'invitation
            $stmt = $db->prepare("
                SELECT * FROM invitations 
                WHERE id = ? AND to_user_id = ? AND status = 'pending'
            ");
            $stmt->execute([$invitationId, $userId]);
            $invitation = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$invitation) {
                $db->rollBack();
                return false;
            }
            
            // Vérifier que la date/heure n'est pas passée
            $now = new DateTime();
            $datePresence = new DateTime($invitation['date_presence']);
            
            if ($invitation['heure_presence']) {
                $heurePresence = DateTime::createFromFormat('H:i:s', $invitation['heure_presence']);
                if ($heurePresence) {
                    $datePresence->setTime($heurePresence->format('H'), $heurePresence->format('i'), $heurePresence->format('s'));
                }
            }
            
            if ($datePresence < $now) {
                // Date/heure passée, supprimer l'invitation
                $stmt = $db->prepare("DELETE FROM invitations WHERE id = ?");
                $stmt->execute([$invitationId]);
                $db->commit();
                return false;
            }
            
            // Vérifier si l'utilisateur participe déjà à cette activité pour ce jour
            $alreadyParticipates = ParticipationModel::verifierParticipationDejaExistante(
                $userId,
                $invitation['activity_id'],
                $invitation['activity_type'],
                $invitation['date_presence']
            );
            
            if ($alreadyParticipates) {
                // L'utilisateur participe déjà, on marque l'invitation comme acceptée mais on ne crée pas de doublon
                $stmt = $db->prepare("UPDATE invitations SET status = 'accepted' WHERE id = ?");
                $stmt->execute([$invitationId]);
                $db->commit();
                // Retourner un code spécial pour indiquer que c'était un doublon
                return 'already_participates';
            }
            
            // Récupérer la description de l'activité
            $activityDescription = self::getActivityDescription($db, $invitation['activity_type'], $invitation['activity_id']);
            
            // Créer la participation pour l'utilisateur invité
            $participationCreated = ParticipationModel::createParticipation(
                $userId,
                $invitation['activity_type'],
                $invitation['activity_id'],
                $invitation['date_presence'],
                $invitation['heure_presence'],
                $activityDescription
            );
            
            if (!$participationCreated) {
                $db->rollBack();
                return false;
            }
            
            // Mettre à jour le statut de l'invitation
            $stmt = $db->prepare("UPDATE invitations SET status = 'accepted' WHERE id = ?");
            $stmt->execute([$invitationId]);
            
            $db->commit();
            return true;
        } catch (PDOException $e) {
            error_log("Erreur acceptInvitation: " . $e->getMessage());
            if (isset($db)) {
                $db->rollBack();
            }
            return false;
        }
    }
    
    /**
     * Refuser une invitation
     */
    public static function declineInvitation($invitationId, $userId) {
        try {
            $db = MySqlDb::getPdoDb();
            $stmt = $db->prepare("
                UPDATE invitations 
                SET status = 'declined' 
                WHERE id = ? AND to_user_id = ? AND status = 'pending'
            ");
            return $stmt->execute([$invitationId, $userId]);
        } catch (PDOException $e) {
            error_log("Erreur declineInvitation: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Supprimer les invitations expirées
     */
    private static function deleteExpiredInvitations($db) {
        try {
            $now = new DateTime();
            $nowDate = $now->format('Y-m-d');
            $nowTime = $now->format('H:i:s');
            
            // Supprimer les invitations avec date passée
            $stmt = $db->prepare("
                DELETE FROM invitations 
                WHERE status = 'pending' 
                AND (
                    date_presence < ? 
                    OR (date_presence = ? AND heure_presence IS NOT NULL AND heure_presence < ?)
                )
            ");
            $stmt->execute([$nowDate, $nowDate, $nowTime]);
        } catch (PDOException $e) {
            error_log("Erreur deleteExpiredInvitations: " . $e->getMessage());
        }
    }
    
    /**
     * Récupérer la description d'une activité
     */
    private static function getActivityDescription($db, $activityType, $activityId) {
        try {
            $description = '';
            
            switch ($activityType) {
                case 'aires_jeux':
                    $stmt = $db->prepare("SELECT libelle, adresse FROM aires_jeux WHERE id = ?");
                    $stmt->execute([$activityId]);
                    $activity = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($activity) {
                        $description = ($activity['libelle'] ?? '') . ' - ' . ($activity['adresse'] ?? '');
                    }
                    break;
                case 'equipements_sportifs':
                    $stmt = $db->prepare("SELECT equip_nom, adr_num_et_rue FROM equipements_sportifs WHERE id = ?");
                    $stmt->execute([$activityId]);
                    $activity = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($activity) {
                        $description = ($activity['equip_nom'] ?? '') . ' - ' . ($activity['adr_num_et_rue'] ?? '');
                    }
                    break;
                case 'manifestations_sportives':
                    $stmt = $db->prepare("SELECT manifestation, lieu FROM manifestations_sportives WHERE id = ?");
                    $stmt->execute([$activityId]);
                    $activity = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($activity) {
                        $description = ($activity['manifestation'] ?? '') . ' - ' . ($activity['lieu'] ?? '');
                    }
                    break;
                case 'agenda_culturel':
                    $stmt = $db->prepare("SELECT nom_du_spectacle, lieu_de_representation FROM agenda_culturel WHERE id = ?");
                    $stmt->execute([$activityId]);
                    $activity = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($activity) {
                        $description = ($activity['nom_du_spectacle'] ?? '') . ' - ' . ($activity['lieu_de_representation'] ?? '');
                    }
                    break;
                case 'points_interets':
                    $stmt = $db->prepare("SELECT libelle, adresse FROM points_interets WHERE id = ?");
                    $stmt->execute([$activityId]);
                    $activity = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($activity) {
                        $description = ($activity['libelle'] ?? '') . ' - ' . ($activity['adresse'] ?? '');
                    }
                    break;
            }
            
            return $description ?: 'Activité';
        } catch (PDOException $e) {
            error_log("Erreur getActivityDescription: " . $e->getMessage());
            return 'Activité';
        }
    }
}
?>

