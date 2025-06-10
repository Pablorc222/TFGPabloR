<?php
// Verificar si la clase ya existe antes de declararla
if (!class_exists('Database')) {
    class Database {
        public static function connect() {
            $host = 'mariadb';
            $dbname = 'FootStore';
            
            try {
                $dsn = 'mysql:host=' . $host . ';dbname=' . $dbname . ';charset=UTF8';
                $pdo = new PDO($dsn, 'root', 'changepassword');
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                return $pdo;
            } catch (PDOException $e) {
                die("Error de conexión: " . $e->getMessage());
            }
        }
    }
}
?>