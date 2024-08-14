<?php
require_once '../models/proveedor.php';
$option = (empty($_GET['option'])) ? '' : $_GET['option'];
$proveedor = new ProveedorModel();

switch ($option) {
    case 'listar':
        $data = $proveedor->getProveedores();
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['nombre_completo'] = $data[$i]['nombres'] . ' ' . $data[$i]['apellidos']; 
            $data[$i]['accion'] = '<div class="d-flex">
                <a class="btn btn-danger btn-sm" onclick="deleteProveedor(' . $data[$i]['id_proveedor'] . ')"><i class="fas fa-eraser"></i></a>
                <a class="btn btn-primary btn-sm" onclick="editProveedor(' . $data[$i]['id_proveedor'] . ')"><i class="fas fa-edit"></i></a>
                </div>';
        }
        echo json_encode($data);
        break;

    case 'listarEmpresas':
        $data = $proveedor->getEmpresas();
        echo json_encode($data);
        break;

    case 'saveEmpresa':
        $nit_empresa = $_POST['nit_empresa'];
        $razon_social_empresa = $_POST['razon_social_empresa'];
        $telefono_empresa = $_POST['telefono_empresa'];
        $correo_empresa = $_POST['correo_empresa'];
        $direccion_empresa = $_POST['direccion_empresa'];
        $id_empresa = $_POST['id_empresa'];

        if ($id_empresa == '') {
            $result = $proveedor->saveEmpresa($nit_empresa, $razon_social_empresa, $telefono_empresa, $correo_empresa, $direccion_empresa);
            if ($result) {
                $res = array('tipo' => 'success', 'mensaje' => 'EMPRESA REGISTRADA');
            } else {
                $res = array('tipo' => 'error', 'mensaje' => 'ERROR AL AGREGAR EMPRESA');
            }
        } else {
            $result = $proveedor->updateEmpresa($nit_empresa, $razon_social_empresa, $telefono_empresa, $correo_empresa, $direccion_empresa, $id_empresa);
            if ($result) {
                $res = array('tipo' => 'success', 'mensaje' => 'EMPRESA MODIFICADA');
            } else {
                $res = array('tipo' => 'error', 'mensaje' => 'ERROR AL MODIFICAR EMPRESA');
            }
        }
        echo json_encode($res);
        break;

    case 'saveProveedor':
        $empresa_id = $_POST['empresa_id'];
        $nombres = $_POST['nombres'];
        $apellidos = $_POST['apellidos'];
        $celular = $_POST['celular'];
        $correo = $_POST['correo'];
        $id_proveedor = $_POST['id_proveedor'];

        if ($id_proveedor == '') {
            $result = $proveedor->saveProveedor($empresa_id, $nombres, $apellidos, $celular, $correo);
            if ($result) {
                $res = array('tipo' => 'success', 'mensaje' => 'PROVEEDOR REGISTRADO');
            } else {
                $res = array('tipo' => 'error', 'mensaje' => 'ERROR AL AGREGAR PROVEEDOR');
            }
        } else {
            $result = $proveedor->updateProveedor($empresa_id, $nombres, $apellidos, $celular, $correo, $id_proveedor);
            if ($result) {
                $res = array('tipo' => 'success', 'mensaje' => 'PROVEEDOR MODIFICADO');
            } else {
                $res = array('tipo' => 'error', 'mensaje' => 'ERROR AL MODIFICAR PROVEEDOR');
            }
        }
        echo json_encode($res);
        break;

    case 'delete':
        $id = $_GET['id'];
        $data = $proveedor->deleteProveedor($id);
        if ($data) {
            $res = array('tipo' => 'success', 'mensaje' => 'PROVEEDOR ELIMINADO');
        } else {
            $res = array('tipo' => 'error', 'mensaje' => 'ERROR AL ELIMINAR PROVEEDOR');
        }
        echo json_encode($res);
        break;

    case 'edit':
        $id = $_GET['id'];
        $data = $proveedor->getProveedor($id);
        echo json_encode($data);
        break;

    case 'viewEmpresa':
        $id_empresa = $_GET['id'];
        $empresa = $proveedor->getEmpresa($id_empresa);
        $proveedores = $proveedor->getProveedoresByEmpresa($id_empresa);
        echo json_encode(['empresa' => $empresa, 'proveedores' => $proveedores]);
        break;
        

    default:
        echo json_encode(['tipo' => 'error', 'mensaje' => 'OPCIÓN NO VÁLIDA']);
        break;
}
