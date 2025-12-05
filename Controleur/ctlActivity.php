<?php
require_once __DIR__ . '/../Model/MysqlDB.php';
require_once __DIR__ . '/../Model/ActivityModel.php';
require_once __DIR__ . '/../API/DecodeApi.php';

// Gérer les requêtes AJAX pour synchroniser les activités
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'sync') {
    header('Content-Type: application/json');
    
    // Utiliser DecodeApi pour synchroniser
    $result = DecodeApi::syncAll();
    
    echo json_encode([
        'success' => $result['success'],
        'aires' => $result['aires'],
        'equipements' => $result['equipements'],
        'manifestations' => $result['manifestations'],
        'agenda' => $result['agenda'],
        'points' => $result['points'],
        'total_saved' => $result['aires']['saved'] + $result['equipements']['saved'] + $result['manifestations']['saved'] + $result['agenda']['saved'] + $result['points']['saved'],
        'total_updated' => $result['aires']['updated'] + $result['equipements']['updated'] + $result['manifestations']['updated'] + $result['agenda']['updated'] + $result['points']['updated'],
        'total_errors' => $result['aires']['errors'] + $result['equipements']['errors'] + $result['manifestations']['errors'] + $result['agenda']['errors'] + $result['points']['errors']
    ]);
    exit();
}

// Gérer les requêtes pour récupérer les activités depuis la base de données
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_activities') {
    header('Content-Type: application/json');
    
    try {
        $db = MySqlDb::getPdoDb();
        $activities = [];
        $today = new DateTime('today');
        $now = new DateTime();
        
        // Récupérer les aires de jeux
        $stmt = $db->query("SELECT id, libelle as name, adresse as address, commune, latitude, longitude, famille_eqpt as description FROM aires_jeux");
        $aires = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($aires as $aire) {
            if ($aire['latitude'] && $aire['longitude']) {
                $activities[] = [
                    'id' => $aire['id'],
                    'type' => 'aires_jeux',
                    'name' => $aire['name'],
                    'address' => $aire['address'],
                    'commune' => $aire['commune'],
                    'lat' => floatval($aire['latitude']),
                    'lon' => floatval($aire['longitude']),
                    'description' => $aire['description'] ?? '',
                    'participants' => [] // Initialiser la liste des participants
                ];
            }
        }
        
        // Récupérer les équipements sportifs
        $stmt = $db->query("SELECT id, equip_nom as name, adr_num_et_rue as address, adr_commune as commune, latitude, longitude, equip_type as description FROM equipements_sportifs");
        $equipements = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($equipements as $equip) {
            if ($equip['latitude'] && $equip['longitude']) {
                $activities[] = [
                    'id' => $equip['id'],
                    'type' => 'equipements_sportifs',
                    'name' => $equip['name'],
                    'address' => $equip['address'],
                    'commune' => $equip['commune'],
                    'lat' => floatval($equip['latitude']),
                    'lon' => floatval($equip['longitude']),
                    'description' => $equip['description'] ?? '',
                    'participants' => [] // Initialiser la liste des participants
                ];
            }
        }
        
        // Récupérer les points d'intérêt
        $stmt = $db->query("SELECT id, libelle as name, adresse as address, commune, latitude, longitude, thematique as description FROM points_interets");
        $points = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($points as $point) {
            if ($point['latitude'] && $point['longitude']) {
                $activities[] = [
                    'id' => $point['id'],
                    'type' => 'points_interets',
                    'name' => $point['name'],
                    'address' => $point['address'],
                    'commune' => $point['commune'],
                    'lat' => floatval($point['latitude']),
                    'lon' => floatval($point['longitude']),
                    'description' => $point['description'] ?? '',
                    'participants' => []
                ];
            }
        }
        
        echo json_encode([
            'success' => true,
            'activities' => $activities
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
    exit();
}
?>
