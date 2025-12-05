<?php
/**
 * Script pour synchroniser les activités depuis l'API et mettre à jour les participations
 * Ce script met à jour les activités dans la base de données avec les vraies informations depuis l'API
 */

require_once __DIR__ . '/M/MysqlDB.php';
require_once __DIR__ . '/M/ActivityModel.php';

echo "Synchronisation des activités depuis l'API...\n\n";

$apis = [
    [
        'url' => 'https://data.melunvaldeseine.fr/api/explore/v2.1/catalog/datasets/equipements-sportifs/records?limit=-1',
        'type' => 'equipements'
    ],
    [
        'url' => 'https://data.melunvaldeseine.fr/api/explore/v2.1/catalog/datasets/aires-de-jeux/records?limit=-1',
        'type' => 'aires'
    ]
];

$totalSaved = 0;
$totalUpdated = 0;
$errors = 0;

foreach ($apis as $api) {
    echo "Chargement de {$api['type']}...\n";
    
    try {
        $ch = curl_init($api['url']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200 || !$response) {
            echo "  Erreur: Impossible de charger les données (HTTP $httpCode)\n";
            $errors++;
            continue;
        }
        
        $data = json_decode($response, true);
        
        if (!isset($data['results']) || !is_array($data['results'])) {
            echo "  Aucune donnée trouvée\n";
            continue;
        }
        
        $count = 0;
        foreach ($data['results'] as $resultItem) {
            try {
                // La structure de l'API: { record: { id: ..., fields: { ... } } }
                $record = $resultItem['record'] ?? [];
                $fields = $record['fields'] ?? $resultItem; // Fallback si pas de structure record
                $recordId = $record['id'] ?? $resultItem['recordid'] ?? null;
                
                if ($api['type'] === 'equipements') {
                    if (!$recordId) {
                        // Essayer de créer un ID stable basé sur le nom et l'adresse
                        $recordId = md5(($fields['equip_nom'] ?? '') . ($fields['adr_num_et_rue'] ?? ''));
                    }
                    
                    $activityId = "equipements_{$recordId}";
                    $name = $fields['equip_nom'] ?? 'Nom non disponible';
                    $address = $fields['adr_num_et_rue'] ?? '';
                    $commune = $fields['adr_commune'] ?? 'Melun';
                    
                    // Gérer les coordonnées (peuvent être dans différents formats)
                    $lat = 48.54;
                    $lon = 2.66;
                    if (isset($fields['equip_lat'])) {
                        $lat = floatval($fields['equip_lat']);
                    } elseif (isset($fields['point_geo']['lat'])) {
                        $lat = floatval($fields['point_geo']['lat']);
                    } elseif (isset($fields['point_geo'][0])) {
                        $lat = floatval($fields['point_geo'][0]);
                    }
                    
                    if (isset($fields['equip_long'])) {
                        $lon = floatval($fields['equip_long']);
                    } elseif (isset($fields['point_geo']['lon'])) {
                        $lon = floatval($fields['point_geo']['lon']);
                    } elseif (isset($fields['point_geo'][1])) {
                        $lon = floatval($fields['point_geo'][1]);
                    }
                    
                    $description = $fields['equip_type'] ?? null;
                    
                } elseif ($api['type'] === 'aires') {
                    if (!$recordId) {
                        $recordId = md5(($fields['libelle'] ?? '') . ($fields['adresse'] ?? ''));
                    }
                    
                    $activityId = "aires_{$recordId}";
                    $name = $fields['libelle'] ?? 'Nom non disponible';
                    $address = $fields['adresse'] ?? '';
                    $commune = $fields['commune'] ?? 'Melun';
                    
                    // Gérer les coordonnées
                    $lat = 48.54;
                    $lon = 2.66;
                    if (isset($fields['geo_point_2d']['lat'])) {
                        $lat = floatval($fields['geo_point_2d']['lat']);
                    } elseif (isset($fields['geo_point_2d'][0])) {
                        $lat = floatval($fields['geo_point_2d'][0]);
                    }
                    
                    if (isset($fields['geo_point_2d']['lon'])) {
                        $lon = floatval($fields['geo_point_2d']['lon']);
                    } elseif (isset($fields['geo_point_2d'][1])) {
                        $lon = floatval($fields['geo_point_2d'][1]);
                    }
                    
                    $description = $fields['famille_eqpt'] ?? null;
                } else {
                    continue;
                }
                
                // Vérifier si l'activité existe déjà
                $existing = ActivityModel::getActivityByApiId($activityId);
                
                $result = ActivityModel::createOrUpdateActivity(
                    $activityId,
                    $api['type'],
                    $name,
                    $address,
                    $commune,
                    $lat,
                    $lon,
                    $description,
                    json_encode($resultItem)
                );
                
                if ($result) {
                    if ($existing) {
                        $totalUpdated++;
                    } else {
                        $totalSaved++;
                    }
                    $count++;
                } else {
                    $errors++;
                }
            } catch (Exception $e) {
                $errors++;
                error_log("Erreur activité: " . $e->getMessage());
            }
        }
        
        echo "  {$api['type']}: $count activités traitées\n";
        
    } catch (Exception $e) {
        echo "  Erreur: " . $e->getMessage() . "\n";
        $errors++;
    }
}

echo "\n=== Résumé ===\n";
echo "Activités créées: $totalSaved\n";
echo "Activités mises à jour: $totalUpdated\n";
echo "Erreurs: $errors\n";
echo "\nSynchronisation terminée!\n";
?>

