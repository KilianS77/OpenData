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
        'total_saved' => $result['aires']['saved'] + $result['equipements']['saved'],
        'total_updated' => $result['aires']['updated'] + $result['equipements']['updated'],
        'total_errors' => $result['aires']['errors'] + $result['equipements']['errors']
    ]);
    exit();
}

// Gérer les requêtes pour récupérer les activités depuis la base de données
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_activities') {
    header('Content-Type: application/json');
    
    try {
        $db = MySqlDb::getPdoDb();
        $activities = [];
        
        // Récupérer les aires de jeux
        $stmt = $db->query("SELECT id, libelle as name, adresse as address, commune, latitude, longitude, famille_eqpt as description FROM aires_jeux");
        $aires = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($aires as $aire) {
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
        
        // Récupérer les équipements sportifs
        $stmt = $db->query("SELECT id, equip_nom as name, adr_num_et_rue as address, adr_commune as commune, latitude, longitude, equip_type as description FROM equipements_sportifs");
        $equipements = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($equipements as $equip) {
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
