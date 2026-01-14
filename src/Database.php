<?php

class Database {
    private static $pdo;

    public static function getConnect() {
        try {
            self::$pdo = new PDO(
                "pgsql:host=ep-fragrant-poetry-a11ds5hw-pooler.ap-southeast-1.aws.neon.tech;dbname=neondb; sslmode=require;channel_binding=require",
                "neondb_owner",
                "npg_XdR2sn7lroSG",
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }

        return self::$pdo;
    }
}


// // TEMP: quick connection check; .
// try {
//     $db = Database::getConnect();
//     echo "Connected OK";
// } catch (Throwable $e) {
//     echo "Failed: " . $e->getMessage();
// }

