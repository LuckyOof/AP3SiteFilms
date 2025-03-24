<?php
abstract class PDOModel {
    private static $pdo;

    private function setBdd() {
        self::$pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
        self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
    }

    protected function getBdd() {
        if(self::$pdo === null) {
            $this->setBdd();
        }
        return self::$pdo;
    }
}
