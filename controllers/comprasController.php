<?php
$option = (empty($_GET['option'])) ? '' : $_GET['option'];
require_once '../models/productos.php';
$productos = new Productos();
switch ($option) {

    case 'listar':
        // Verifica si el id_sede está en la sesión
        if (isset($_SESSION['id_sede'])) {
            $id_sede = $_SESSION['id_sede'];
            $data = $productos->getProductsBySede($id_sede);  // Filtra los productos por sede
            echo json_encode($data);
        } else {
            echo json_encode(['tipo' => 'error', 'mensaje' => 'No se ha seleccionado ninguna sede.']);
        }
        break;
    

    case 'save':
        $barcode = $_POST['barcode'];
        $nombre = $_POST['nombre'];
        $precio_compra = $_POST['precio_compra'];
        $precio_venta = $_POST['precio_venta'];
        $stock = $_POST['stock'];
        $imagen = $_FILES['imagen']['name'];
        $id_proveedor = $_POST['id_proveedor'];
        $id_product = $_POST['id_product'];
        if ($id_product == '') {
            $consult = $productos->comprobarBarcode($barcode);
            if (empty($consult)) {
                $result = $productos->saveProduct($barcode, $nombre, $precio_compra, $precio_venta, $stock, $imagen, $id_proveedor);
                if ($result) {
                    $res = array('tipo' => 'success', 'mensaje' => 'PRODUCTO REGISTRADO');
                } else {
                    $res = array('tipo' => 'error', 'mensaje' => 'ERROR AL AGREGAR');
                }
            } else {
                $res = array('tipo' => 'error', 'mensaje' => 'EL BARCODE YA EXISTE');
            }
        } else {
            $result = $productos->updateProduct($barcode, $nombre, $precio_compra, $precio_venta, $stock, $imagen, $id_proveedor, $id_product);
            if ($result) {
                $res = array('tipo' => 'success', 'mensaje' => 'PRODUCTO MODIFICADO');
            } else {
                $res = array('tipo' => 'error', 'mensaje' => 'ERROR AL MODIFICAR');
            }
        }
        echo json_encode($res);
        break;

    case 'delete':
        $id = $_GET['id'];
        $data = $productos->deleteProducto($id);
        if ($data) {
            $res = array('tipo' => 'success', 'mensaje' => 'PRODUCTO ELIMINADO');
        } else {
            $res = array('tipo' => 'error', 'mensaje' => 'ERROR AL ELIMINAR');
        }
        echo json_encode($res);
        break;
        
    case 'edit':
        $id = $_GET['id'];
        $data = $productos->getProduct($id);
        echo json_encode($data);
        break;
    default:
        # code...
        break;
}

