<?php


class MySqlDb {
    public static function getEnvVariables() {
        // Charger les variables d'environnement à partir du fichier .env
        $env = parse_ini_file(__DIR__ . '/../.env', false, INI_SCANNER_TYPED);
        return $env ?: []; // Retourne un tableau vide si le fichier n'est pas trouvé
    }

    private static $objPdoDb;
    
    

    public static function getPdoDb() {
        if (!self::$objPdoDb) {
            // Charger les variables d'environnement
            $env = self::getEnvVariables();
            
            // Vérifier que les variables nécessaires sont présentes
            if (empty($env['DB_NAME']) || empty($env['DB_HOST']) || empty($env['DB_USER'])) {
                throw new Exception("Configuration de base de données manquante. Vérifiez le fichier .env");
            }
            
            $dsn = sprintf(
                'mysql:dbname=%s;host=%s',
                $env['DB_NAME'],
                $env['DB_HOST']
            );
            
            try {
                self::$objPdoDb = new PDO(
                    $dsn,
                    $env['DB_USER'],
                    $env['DB_PASSWORD'] ?? '',
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                    ]
                );
            } catch (PDOException $e) {
                error_log("Erreur de connexion à la base de données: " . $e->getMessage());
                throw new Exception("Impossible de se connecter à la base de données: " . $e->getMessage());
            }
        }
        return self::$objPdoDb;
    }
}
?>