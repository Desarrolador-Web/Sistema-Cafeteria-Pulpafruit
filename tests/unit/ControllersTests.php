<?php

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

require_once __DIR__ . '/../../../models/compras.php';

class ControllersTests extends TestCase
{
    /**
     * @var MockObject|Compras
     */
    private $comprasMock;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear un mock del modelo Compras
        $this->comprasMock = $this->createMock(Compras::class);

        // Iniciar sesión simulado
        $_SESSION['idusuario'] = 1;
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // Limpiar las variables globales
        unset($_SESSION['idusuario']);
    }

    // Prueba para la opción 'listarProductos'
    public function testListarProductos()
    {
        $_GET['option'] = 'listarProductos';

        // Configurar el mock para devolver un array de productos
        $expectedProducts = [
            ['idcompra' => 1, 'codproducto' => '123', 'codigo' => 'ABC123', 'descripcion' => 'Product 1', 'existencia' => 10, 'status' => 1, 'precio_compra' => 50, 'precio_venta' => 100, 'imagen' => 'image.jpg', 'proveedor' => 'Provider 1']
        ];
        $this->comprasMock->expects($this->once())
            ->method('getProducts')
            ->willReturn($expectedProducts);

        // Incluir el controlador
        include __DIR__ . '/../../../controllers/comprasController.php';

        // Verificar el resultado
        $this->expectOutputString(json_encode($expectedProducts));
    }

    // Prueba para la opción 'listarProveedores'
    public function testListarProveedores()
    {
        $_GET['option'] = 'listarProveedores';

        // Configurar el mock para devolver un array de proveedores
        $expectedProveedores = [
            ['id_proveedor' => 1, 'razon_social' => 'Proveedor 1']
        ];
        $this->comprasMock->expects($this->once())
            ->method('getProveedores')
            ->willReturn($expectedProveedores);

        // Incluir el controlador
        include __DIR__ . '/../../../controllers/comprasController.php';

        // Verificar el resultado
        $this->expectOutputString(json_encode($expectedProveedores));
    }

    // Prueba para la opción 'registrarProducto'
    public function testRegistrarProducto()
    {
        $_GET['option'] = 'registrarProducto';
        $_POST = [
            'barcode' => '1234567890',
            'descripcion' => 'Test Product',
            'id_proveedor' => 1,
            'precio_compra' => 50.0,
            'precio_venta' => 100.0,
            'cantidad' => 10,
            'estado' => 1,
            'id_product' => ''
        ];
        $_FILES = [
            'imagen' => [
                'name' => 'test.jpg',
                'type' => 'image/jpeg',
                'tmp_name' => '/tmp/test.jpg',
                'error' => 0,
                'size' => 123
            ]
        ];

        // Configurar el mock para las operaciones del modelo
        $this->comprasMock->expects($this->once())
            ->method('getProveedor')
            ->with($this->equalTo(1))
            ->willReturn(['id_proveedor' => 1, 'razon_social' => 'Proveedor 1']);

        $this->comprasMock->expects($this->once())
            ->method('saveCompra')
            ->willReturn(1);

        $this->comprasMock->expects($this->once())
            ->method('saveProduct')
            ->willReturn(1);

        $this->comprasMock->expects($this->once())
            ->method('saveDetalle')
            ->willReturn(true);

        // Incluir el controlador
        include __DIR__ . '/../../../controllers/comprasController.php';

        // Verificar el resultado
        $expectedResult = ['tipo' => 'success', 'mensaje' => 'PRODUCTO REGISTRADO'];
        $this->expectOutputString(json_encode($expectedResult));
    }

    // Prueba para la opción 'cambiarEstado'
    public function testCambiarEstado()
    {
        $_GET['option'] = 'cambiarEstado';
        $accion = [
            'estado' => 1,
            'barcode' => '1234567890',
            'id' => 1
        ];
        $GLOBALS['HTTP_RAW_POST_DATA'] = json_encode($accion);

        // Configurar el mock para las operaciones del modelo
        $this->comprasMock->expects($this->once())
            ->method('getProduct')
            ->with($this->equalTo(1))
            ->willReturn(['id_producto' => 1, 'precio_compra' => 50, 'existencia' => 10, 'id_proveedor' => 1]);

        $this->comprasMock->expects($this->once())
            ->method('updateEstadoYBarcodeProducto')
            ->with($this->equalTo(1), $this->equalTo(1), $this->equalTo('1234567890'))
            ->willReturn(true);

        $this->comprasMock->expects($this->once())
            ->method('saveCompra')
            ->willReturn(1);

        $this->comprasMock->expects($this->once())
            ->method('saveDetalle')
            ->willReturn(true);

        // Incluir el controlador
        include __DIR__ . '/../../../controllers/comprasController.php';

        // Verificar el resultado
        $expectedResult = ['tipo' => 'success', 'mensaje' => 'ESTADO ACTUALIZADO'];
        $this->expectOutputString(json_encode($expectedResult));
    }

    // Prueba para la opción 'opción no válida'
    public function testOpcionNoValida()
    {
        $_GET['option'] = 'opcionNoValida';

        // Incluir el controlador
        include __DIR__ . '/../../../controllers/comprasController.php';

        // Verificar el resultado
        $expectedResult = ['tipo' => 'error', 'mensaje' => 'OPCIÓN NO VÁLIDA'];
        $this->expectOutputString(json_encode($expectedResult));
    }
}
