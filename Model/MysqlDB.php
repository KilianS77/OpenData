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
            // Vérifier si les variables d'environnement sont déjà chargées
            if (empty($_ENV['DB_NAME']) || empty($_ENV['DB_HOST'])) {
                $env = self::getEnvVariables();
            }
            
            $dsn = sprintf(
                'mysql:dbname=%s;host=%s',
                $env['DB_NAME'],
                $env['DB_HOST']
            );
            
            self::$objPdoDb = new PDO(
                $dsn,
                $env['DB_USER'],
                $env['DB_PASSWORD'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
        }
        return self::$objPdoDb;
    }
}
?>