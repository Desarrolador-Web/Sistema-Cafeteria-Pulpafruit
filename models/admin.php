<?php
require_once __DIR__ . '/../config.php';
require_once 'conexion.php';

class AdminModel {
    private $pdo, $con;
    public function __construct() {
        $this->con = new Conexion();
        $this->pdo = $this->con->conectar();
    }

    public function getDatos($table)
    {
        if ($table === 'cf_usuario') {
            $consult = $this->pdo->prepare("SELECT COUNT(*) AS total FROM $table WHERE estado_usuario = ?");
            $consult->execute([1]);
        } else {
            $consult = $this->pdo->prepare("SELECT COUNT(*) AS total FROM $table");
            $consult->execute();
        }
        return $consult->fetch(PDO::FETCH_ASSOC);
    }

    public function getVentas($id_user)
    {
        $consult = $this->pdo->prepare("SELECT COUNT(*) AS total FROM cf_ventas WHERE id_usuario = ?");
        $consult->execute([$id_user]);
        return $consult->fetch(PDO::FETCH_ASSOC);
    }

    public function ventasSemana($fechaInicio, $fechaFin, $idUsuario) {
        $consult = $this->pdo->prepare("SELECT * FROM cf_ventas WHERE fecha BETWEEN ? AND ? AND id_usuario = ?");
        $consult->execute([$fechaInicio, $fechaFin, $idUsuario]);
        return $consult->fetchAll(PDO::FETCH_ASSOC);
    }

    public function topClientes($idUsuario) {
        $consult = $this->pdo->prepare("
            SELECT TOP 10 id_cliente, SUM(total) as total
            FROM cf_ventas
            WHERE id_usuario = ?
            GROUP BY id_cliente
            ORDER BY total DESC
        ");
        $consult->execute([$idUsuario]);
        return $consult->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDato()
    {
        $consult = $this->pdo->prepare("SELECT * FROM configuracion");
        $consult->execute();
        return $consult->fetch(PDO::FETCH_ASSOC);
    }

    public function saveDatos($nombre, $telefono, $correo, $direccion, $id)
    {
        $consult = $this->pdo->prepare("UPDATE configuracion SET nombre=?, telefono=?, email=?, direccion=? WHERE id = ?");
        return $consult->execute([$nombre, $telefono, $correo, $direccion, $id]);
    }
}

