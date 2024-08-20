<?php
class Conexion {
    public function conectar() {
        $pdo = null;
        try {
            $pdo = new PDO('sqlsrv:Server='.DB_HOST.';Database='.DB_NAME, DB_USER, DB_PASS);
            return $pdo;
        } catch (PDOException $e) {
            print "¡Error!: " . $e->getMessage() . "<br/>";
            die();
        }
    }
}