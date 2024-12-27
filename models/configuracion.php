<?php
require_once __DIR__ . '/../config.php';
require_once 'conexion.php';

class ConfiguracionModel {
    private $pdo;

    public function __construct() {
        $this->pdo = new Conexion();
        $this->pdo = $this->pdo->conectar();
    }

    // Validar si hay una caja sin cerrar en la sede
    public function checkCajaSinCerrar($id_sede) {
        $sql = "SELECT * FROM cf_informacion_cajas WHERE id_sede = ? AND valor_cierre IS NULL AND fecha_cierre IS NULL";
        $query = $this->pdo->prepare($sql);
        $query->execute([$id_sede]);
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    // Abrir una nueva caja
    public function abrirCaja($id_usuario, $valorApertura, $id_sede, $fechaApertura) {
        $sql = "INSERT INTO cf_informacion_cajas (id_usuario, valor_apertura, id_sede, fecha_apertura) VALUES (?, ?, ?, ?)";
        $query = $this->pdo->prepare($sql);
        return $query->execute([$id_usuario, $valorApertura, $id_sede, $fechaApertura]);
    }
}
