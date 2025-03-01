document.addEventListener('DOMContentLoaded', function () {
    class ProductoService {
        static verificarBarcode(barcode) {
            return axios.get(`controllers/productosController.php?option=verificarBarcode&barcode=${barcode}`);
        }

        static listarProductos(idSede, rolUsuario) {
            return axios.get(`controllers/productosController.php?option=listarProductos&id_caja=${idSede}&rolUsuario=${rolUsuario}`);
        }

        static listarProveedores() {
            return axios.get('controllers/productosController.php?option=listarProveedores');
        }

        static registrarProducto(formData) {
            return axios.post('controllers/productosController.php?option=registrarProducto', formData);
        }

        static verificarCajaSesion(idUsuario, callback) {
            axios.post('controllers/ventasController.php?option=verificarCajaSesion', new URLSearchParams({
                id_usuario: idUsuario
            }))
            .then(response => {
                console.log("Verificación de caja abierta:", response.data);
                if (response.data.caja_abierta === true) {
                    callback(true);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Acceso denegado',
                        text: 'Debe tener una caja abierta para gestionar productos.',
                        confirmButtonText: 'Ir a abrir caja'
                    }).then(() => {
                        window.location.href = '?pagina=configuracion';
                    });
                }
            })
            .catch(error => {
                console.error("Error al verificar la caja:", error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ocurrió un error al verificar la caja. Intente nuevamente.',
                    confirmButtonText: 'Aceptar'
                });
            });
        }
    }

    class UIHandler {
        static mostrarAlerta(titulo, mensaje, tipo) {
            Swal.fire({ title: titulo, text: mensaje, icon: tipo, confirmButtonText: 'Entendido' });
        }

        static mostrarProductos(productos) {
            const table_productos = document.querySelector('#table_productos tbody');
            table_productos.innerHTML = '';

            productos.forEach(producto => {
                const imagenProducto = producto.Imagen ? producto.Imagen : 'uploads/default.png';

                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${producto.Id}</td>
                    <td>${producto.Barcode}</td>
                    <td>${producto.Descripción}</td>
                    <td>${producto.Precio_Compra}</td>
                    <td>${producto.Precio_Venta}</td>
                    <td>${producto.Proveedor}</td>
                    <td>
                        <img src="${imagenProducto}" alt="Imagen del Producto" width="50" 
                        onerror="this.src='uploads/default.png';" />
                    </td>
                    <td>${producto.Cantidad}</td>
                `;
                table_productos.appendChild(row);
            });
        }

        static cargarProveedores(proveedores) {
            const selectProveedor = document.getElementById('id_empresa');
            selectProveedor.innerHTML = '<option value="">Seleccione un proveedor</option>';
            proveedores.forEach(proveedor => {
                selectProveedor.innerHTML += `<option value="${proveedor.id_empresa}">${proveedor.razon_social}</option>`;
            });
        }
    }

    console.log("ID Usuario en JS:", idUsuario);

    if (!idUsuario) {
        console.error("Error: idUsuario no está definido en la sesión.");
        Swal.fire({
            icon: 'error',
            title: 'Error de sesión',
            text: 'No se pudo obtener el usuario. Por favor, inicie sesión nuevamente.',
            confirmButtonText: 'Aceptar'
        }).then(() => {
            window.location.href = 'login.php';
        });
        return;
    }

    let idCaja = idSede;
    console.log("Sede obtenida desde plantilla.php:", idCaja);

    ProductoService.verificarCajaSesion(idUsuario, function (cajaAbierta) {
        if (cajaAbierta) {
            console.log("Acceso permitido a productos");
            cargarDatosProductos();
        }
    });

    function cargarDatosProductos() {
        ProductoService.listarProductos(idCaja, rolUsuario)
            .then(response => {
                if (response.data.productos) UIHandler.mostrarProductos(response.data.productos);
            })
            .catch(error => console.error('Error al cargar productos:', error));

        ProductoService.listarProveedores()
            .then(response => {
                if (response.data.proveedores) UIHandler.cargarProveedores(response.data.proveedores);
            })
            .catch(error => console.error('Error al cargar proveedores:', error));
    }

    // Elementos del DOM
    const barcodeInput = document.getElementById('barcode');
    const formProductos = document.getElementById('frmProductos');
    const btnRecibido = document.getElementById('btn-Recibido');

    barcodeInput.addEventListener('blur', function () {
        const barcode = barcodeInput.value.trim();
        if (!barcode) return;

        ProductoService.verificarBarcode(barcode)
            .then(response => {
                if (response.data.existe) {
                    UIHandler.mostrarAlerta('Producto existente', 'El producto con este código de barras ya existe. Si desea agregar más cantidad, debe realizar una nueva compra.', 'warning');
                    barcodeInput.value = '';
                }
            })
            .catch(error => console.error('Error al verificar el código de barras:', error));
    });

    btnRecibido.addEventListener('click', function () {
        if (!idCaja) {
            UIHandler.mostrarAlerta('Error', 'No se ha podido obtener la sede del usuario.', 'error');
            return;
        }

        const formData = new FormData(formProductos);
        formData.append('id_caja', idCaja);

        ProductoService.registrarProducto(formData)
            .then(response => {
                if (response.data.tipo === 'success') {
                    UIHandler.mostrarAlerta('Éxito', response.data.mensaje, 'success');
                    formProductos.reset();
                    ProductoService.listarProductos(idCaja, rolUsuario)
                        .then(response => {
                            if (response.data.productos) UIHandler.mostrarProductos(response.data.productos);
                        });

                    ProductoService.listarProveedores()
                        .then(response => {
                            if (response.data.proveedores) UIHandler.cargarProveedores(response.data.proveedores);
                        });

                } else {
                    UIHandler.mostrarAlerta('Error', response.data.mensaje, 'error');
                }
            })
            .catch(error => {
                console.error('Error al guardar el producto:', error);
                UIHandler.mostrarAlerta('Error', 'Hubo un problema al guardar el producto.', 'error');
            });
    });
});