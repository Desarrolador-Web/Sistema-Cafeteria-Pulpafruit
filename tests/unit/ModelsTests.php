<?php

use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../../../models/admin.php';
require_once __DIR__ . '/../../../models/clientes.php';


class ModelsTests extends TestCase
{

//-----------------------------------------------------------------------------------------------------------------

    // Prueba para el método getDatos - admin.php
    public function testGetDatos()
    {
        $model = new AdminModel();
        $table = 'cf_usuario';
        $result = $model->getDatos($table);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('total', $result);
    }

    // Prueba para el método getVentas - admin.php
    public function testGetVentas()
    {
        $model = new AdminModel();
        $id_user = 1; 
        $result = $model->getVentas($id_user);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('total', $result);
    }

    // Prueba para el método ventasSemana - admin.php
    public function testVentasSemana()
    {
        $model = new AdminModel();
        $fechaInicio = '2023-01-01';
        $fechaFin = '2023-01-07';
        $idUsuario = 1; 
        $result = $model->ventasSemana($fechaInicio, $fechaFin, $idUsuario);
        $this->assertIsArray($result);
    }

    // Prueba para el método topClientes - admin.php
    public function testTopClientes()
    {
        $model = new AdminModel();
        $idUsuario = 1; 
        $result = $model->topClientes($idUsuario);
        $this->assertIsArray($result);
    }

    // Prueba para el método getDato - admin.php
    public function testGetDato()
    {
        $model = new AdminModel();
        $result = $model->getDato();
        $this->assertIsArray($result);
    }

    // Prueba para el método saveDatos - admin.php
    public function testSaveDatos()
    {
        $model = new AdminModel();
        $nombre = 'Test Name';
        $telefono = '123456789';
        $correo = 'test@example.com';
        $direccion = 'Test Address';
        $id = 1; 
        $result = $model->saveDatos($nombre, $telefono, $correo, $direccion, $id);
        $this->assertTrue($result);
    }

//-----------------------------------------------------------------------------------------------------------------

    // Prueba para el método getClientes - clientes.php
    public function testGetClientes()
    {
        $model = new ClientesModel();
        $result = $model->getClientes();
        $this->assertIsArray($result);
        if (!empty($result)) {
            $this->assertArrayHasKey('id', $result[0]);
            $this->assertArrayHasKey('nombres', $result[0]);
            $this->assertArrayHasKey('apellidos', $result[0]);
            $this->assertArrayHasKey('area', $result[0]);
            $this->assertArrayHasKey('sueldo', $result[0]);
        }
    }

    // Prueba para el método getClienteById - clientes.php
    public function testGetClienteById()
    {
        $model = new ClientesModel();
        $id = 1;
        $result = $model->getClienteById($id);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('nombres', $result);
        $this->assertArrayHasKey('apellidos', $result);
        $this->assertArrayHasKey('area', $result);
        $this->assertArrayHasKey('sueldo', $result);
        $this->assertArrayHasKey('capacidad', $result);
    }

    // Prueba para el método updateCapacidad - clientes.php
    public function testUpdateCapacidad()
    {
        $model = new ClientesModel();
        $id = 1; 
        $nueva_capacidad = 100; 
        $result = $model->updateCapacidad($id, $nueva_capacidad);
        $this->assertTrue($result);
    }

//--------------------------------------------------------------------------------------------------------------------

    // Prueba para el método getProducts - compras.php
    public function testGetProducts()
    {
        $model = new Compras();
        $result = $model->getProducts();
        $this->assertIsArray($result);
        if (!empty($result)) {
            $this->assertArrayHasKey('idcompra', $result[0]);
            $this->assertArrayHasKey('codproducto', $result[0]);
            $this->assertArrayHasKey('codigo', $result[0]);
            $this->assertArrayHasKey('descripcion', $result[0]);
            $this->assertArrayHasKey('existencia', $result[0]);
            $this->assertArrayHasKey('status', $result[0]);
            $this->assertArrayHasKey('precio_compra', $result[0]);
            $this->assertArrayHasKey('precio_venta', $result[0]);
            $this->assertArrayHasKey('imagen', $result[0]);
            $this->assertArrayHasKey('proveedor', $result[0]);
        }
    }

    // Prueba para el método saveProduct - compras.php
    public function testSaveProduct()
    {
        $model = new Compras();
        $barcode = '1234567890';
        $descripcion = 'Test Product';
        $id_proveedor = 1;
        $precio_compra = 50.0;
        $precio_venta = 100.0;
        $imagen = 'test.jpg';
        $cantidad = 10;
        $estado = 1;
        $result = $model->saveProduct($barcode, $descripcion, $id_proveedor, $precio_compra, $precio_venta, $imagen, $cantidad, $estado);
        $this->assertIsInt($result); 
    }

    // Prueba para el método getProductByBarcode - compras.php
    public function testGetProductByBarcode()
    {
        $model = new Compras();
        $barcode = '1234567890'; // Usar un código de producto válido
        $result = $model->getProductByBarcode($barcode);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('id_producto', $result);
        $this->assertArrayHasKey('codigo_producto', $result);
        $this->assertArrayHasKey('descripcion', $result);
        $this->assertArrayHasKey('id_proveedor', $result);
        $this->assertArrayHasKey('precio_compra', $result);
        $this->assertArrayHasKey('precio_venta', $result);
        $this->assertArrayHasKey('imagen', $result);
        $this->assertArrayHasKey('existencia', $result);
        $this->assertArrayHasKey('estado_producto', $result);
    }

    // Prueba para el método getProveedores - compras.php
    public function testGetProveedores()
    {
        $model = new Compras();
        $result = $model->getProveedores();
        $this->assertIsArray($result);
        if (!empty($result)) {
            $this->assertArrayHasKey('id_proveedor', $result[0]);
            $this->assertArrayHasKey('razon_social', $result[0]);
        }
    }

    // Prueba para el método getProveedor - compras.php
    public function testGetProveedor()
    {
        $model = new Compras();
        $id_proveedor = 1;
        $result = $model->getProveedor($id_proveedor);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('id_proveedor', $result);
        $this->assertArrayHasKey('razon_social', $result);
    }

    // Prueba para el método saveCompra - compras.php
    public function testSaveCompra()
    {
        $model = new Compras();
        $id_proveedor = 1;
        $total = 1000.0;
        $fecha = '2023-01-01';
        $id_user = 1;
        $estado = 1;
        $result = $model->saveCompra($id_proveedor, $total, $fecha, $id_user, $estado);
        $this->assertIsInt($result); 
    }

    // Prueba para el método saveDetalle - compras.php
    public function testSaveDetalle()
    {
        $model = new Compras();
        $id_producto = 1;
        $id_compra = 1;
        $cantidad = 10;
        $precio = 50.0;
        $result = $model->saveDetalle($id_producto, $id_compra, $cantidad, $precio);
        $this->assertTrue($result); 
    }

    // Prueba para el método updateEstadoYBarcodeProducto - compras.php
    public function testUpdateEstadoYBarcodeProducto()
    {
        $model = new Compras();
        $id_producto = 1;
        $estado = 1;
        $barcode = '1234567890';
        $result = $model->updateEstadoYBarcodeProducto($id_producto, $estado, $barcode);
        $this->assertTrue($result); // Verifica que la operación de actualización fue exitosa
    }

    // Prueba para el método updateCompraEstado - compras.php
    public function testUpdateCompraEstado()
    {
        $model = new Compras();
        $id_compra = 1;
        $estado = 1;
        $result = $model->updateCompraEstado($id_compra, $estado);
        $this->assertTrue($result); // Verifica que la operación de actualización fue exitosa
    }

//---------------------------------------------------------------------------------------------------------------



}
