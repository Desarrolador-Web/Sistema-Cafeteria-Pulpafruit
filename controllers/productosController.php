<?php
require_once '../models/productos.php';

class ProductoController {
    private $productoModel;

    public function __construct() {
        $this->productoModel = new Compras();
    }

    private function jsonResponse($data) {
        echo json_encode($data);
        exit;
    }

    public function listarProductos() {
        if (!isset($_GET['id_caja'], $_GET['rolUsuario'])) {
            $this->jsonResponse(['error' => 'Faltan parámetros necesarios']);
        }

        $id_caja = intval($_GET['id_caja']);
        $rolUsuario = intval($_GET['rolUsuario']);

        $productos = $this->productoModel->getProducts($id_caja, $rolUsuario);
        $this->jsonResponse(['productos' => $productos ?: []]);
    }

    public function listarProveedores() {
        $this->jsonResponse(['proveedores' => $this->productoModel->getProveedores()]);
    }

    public function verificarBarcode() {
        if (!isset($_GET['barcode'])) {
            $this->jsonResponse(['error' => 'Falta el código de barras']);
        }

        $existe = $this->productoModel->verificarProductoPorBarcode(trim($_GET['barcode']));
        $this->jsonResponse(['existe' => $existe, 'mensaje' => $existe ? 
            'El producto ya existe. Debe comprarlo para añadir existencias.' 
            : 'El producto no existe.']);
    }

    public function registrarProducto() {
        $camposRequeridos = ['barcode', 'descripcion', 'precio_compra', 'precio_venta', 'cantidad', 'id_empresa', 'id_caja'];

        foreach ($camposRequeridos as $campo) {
            if (empty($_POST[$campo])) {
                $this->jsonResponse(['tipo' => 'error', 'mensaje' => 'Todos los campos son obligatorios.']);
            }
        }

        $imagen = $this->subirImagen();
        $resultado = $this->productoModel->saveProduct(
            trim($_POST['barcode']),
            trim($_POST['descripcion']),
            (float)$_POST['precio_compra'],
            (float)$_POST['precio_venta'],
            (int)$_POST['cantidad'],
            (int)$_POST['id_empresa'],
            (int)$_POST['id_caja'],
            $imagen
        );

        $this->jsonResponse([
            'tipo' => $resultado ? 'success' : 'error',
            'mensaje' => $resultado ? 'Producto registrado con éxito.' : 'Error al registrar el producto.'
        ]);
    }

    private function subirImagen() {
        if (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] !== 0) {
            return '';
        }

        $directorio = '../uploads/';
        if (!file_exists($directorio)) {
            mkdir($directorio, 0777, true);
        }

        $ruta = 'uploads/' . basename($_FILES['imagen']['name']);
        move_uploaded_file($_FILES['imagen']['tmp_name'], '../' . $ruta);
        return $ruta;
    }
}

// Instancia del controlador y ejecución de acción
$controller = new ProductoController();
$option = $_GET['option'] ?? '';

$acciones = [
    'listarProductos' => 'listarProductos',
    'listarProveedores' => 'listarProveedores',
    'verificarBarcode' => 'verificarBarcode',
    'registrarProducto' => 'registrarProducto'
];

if (array_key_exists($option, $acciones)) {
    $controller->{$acciones[$option]}();
} else {
    echo json_encode(['tipo' => 'error', 'mensaje' => 'Opción no válida.']);
}
