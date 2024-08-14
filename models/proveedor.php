<?php
require_once '../config.php';
require_once 'conexion.php';

class ProveedorModel {
    private $pdo, $con;

    public function __construct() {
        $this->con = new Conexion();
        $this->pdo = $this->con->conectar();
    }

    public function getProveedores() {
        $consult = $this->pdo->prepare("SELECT p.*, e.razon_social as razon_social_empresa FROM cf_proveedor p JOIN cf_empresa e ON p.id_empresa = e.id_empresa WHERE p.estado_proveedor = 1");
        $consult->execute();
        return $consult->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProveedor($id) {
        $consult = $this->pdo->prepare("SELECT * FROM cf_proveedor WHERE id_proveedor = ?");
        $consult->execute([$id]);
        return $consult->fetch(PDO::FETCH_ASSOC);
    }

    public function getEmpresas() {
        $consult = $this->pdo->prepare("SELECT * FROM cf_empresa");
        $consult->execute();
        return $consult->fetchAll(PDO::FETCH_ASSOC);
    }

    public function saveEmpresa($nit, $razon_social, $telefono, $correo, $direccion) {
        $consult = $this->pdo->prepare("INSERT INTO cf_empresa (nit, razon_social, telefono_empresa, correo_empresa, direccion) VALUES (?, ?, ?, ?, ?)");
        return $consult->execute([$nit, $razon_social, $telefono, $correo, $direccion]);
    }

    public function updateEmpresa($nit, $razon_social, $telefono, $correo, $direccion, $id_empresa) {
        $consult = $this->pdo->prepare("UPDATE cf_empresa SET nit=?, razon_social=?, telefono_empresa=?, correo_empresa=?, direccion=? WHERE id_empresa=?");
        return $consult->execute([$nit, $razon_social, $telefono, $correo, $direccion, $id_empresa]);
    }

    public function saveProveedor($id_empresa, $nombres, $apellidos, $celular, $correo) {
        $consult = $this->pdo->prepare("INSERT INTO cf_proveedor (id_empresa, nombres, apellidos, celular, correo) VALUES (?, ?, ?, ?, ?)");
        return $consult->execute([$id_empresa, $nombres, $apellidos, $celular, $correo]);
    }

    public function updateProveedor($id_empresa, $nombres, $apellidos, $celular, $correo, $id_proveedor) {
        $consult = $this->pdo->prepare("UPDATE cf_proveedor SET id_empresa=?, nombres=?, apellidos=?, celular=?, correo=? WHERE id_proveedor=?");
        return $consult->execute([$id_empresa, $nombres, $apellidos, $celular, $correo, $id_proveedor]);
    }

    public function deleteProveedor($id) {
        $consult = $this->pdo->prepare("UPDATE cf_proveedor SET estado_proveedor = 0 WHERE id_proveedor = ?");
        return $consult->execute([$id]);
    }

    public function getEmpresa($id_empresa) {
        $consult = $this->pdo->prepare("SELECT * FROM cf_empresa WHERE id_empresa = ?");
        $consult->execute([$id_empresa]);
        return $consult->fetch(PDO::FETCH_ASSOC);
    }
    

    public function getProveedoresByEmpresa($id_empresa) {
        $consult = $this->pdo->prepare("SELECT * FROM cf_proveedor WHERE id_empresa = ? AND estado_proveedor = 1");
        $consult->execute([$id_empresa]);
        return $consult->fetchAll(PDO::FETCH_ASSOC);
    }
}
