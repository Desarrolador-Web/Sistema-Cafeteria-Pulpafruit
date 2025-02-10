document.addEventListener('DOMContentLoaded', function () {
    // Elementos del DOM
    const formProductos = document.getElementById('frmProductos');
    const barcodeInput = document.getElementById('barcode');
    const btnRecibido = document.getElementById('btn-Recibido');
    const selectSede = document.getElementById('selectSede');
    const modalMensajeRol = document.getElementById('modalMensajeRol');
    const guardarModal = document.getElementById('guardarModal');
    const cerrarModal = document.getElementById('cerrarModal');
    const table_productos = document.querySelector('#table_productos tbody');

    // Validar si el código de barras ya existe
    barcodeInput.addEventListener('blur', function () {
        const barcode = barcodeInput.value.trim();

        if (barcode) {
            axios.get(`controllers/productosController.php?option=verificarBarcode&barcode=${barcode}`)
                .then(response => {
                    const data = response.data;

                    if (data.existe) {
                        Swal.fire({
                            title: 'Producto existente',
                            text: 'El producto con este código de barras ya existe. Si desea agregar más cantidad, debe realizar una nueva compra.',
                            icon: 'warning',
                            confirmButtonText: 'Entendido'
                        });
                    }
                })
                .catch(error => console.error('Error al verificar el código de barras:', error));
        }
    });

    // Función para cargar los productos desde el backend
    function cargarProductos() {
        axios.get(`controllers/productosController.php?option=listarProductos&id_caja=${idSede}&rolUsuario=${rolUsuario}`)
            .then(response => {
                const data = response.data;
                if (data.productos) {
                    mostrarProductos(data.productos);
                } else {
                    console.error('Error al obtener los productos:', data.error);
                }
            })
            .catch(error => console.error('Error al cargar productos:', error));
    }

    // Mostrar productos en la tabla
    function mostrarProductos(productos) {
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

    // Cargar proveedores al inicio
    function cargarProveedores() {
        axios.get('controllers/productosController.php?option=listarProveedores')
            .then(response => {
                const data = response.data;
                const selectProveedor = document.getElementById('id_empresa');

                if (data.proveedores) {
                    selectProveedor.innerHTML = '<option value="">Seleccione un proveedor</option>';
                    data.proveedores.forEach(proveedor => {
                        selectProveedor.innerHTML += `<option value="${proveedor.id_empresa}">${proveedor.razon_social}</option>`;
                    });
                } else {
                    console.error('Error al obtener proveedores:', data.error);
                }
            })
            .catch(error => console.error('Error al cargar proveedores:', error));
    }

    // Manejo del modal "Recibido"
    btnRecibido.addEventListener('click', function () {
        modalMensajeRol.style.display = 'flex'; // Mostrar el modal
    });

    guardarModal.addEventListener('click', function () {
        const idCaja = selectSede.value; // Obtener la sede seleccionada
        if (!idCaja) {
            alert('Por favor seleccione una sede antes de continuar.');
            return;
        }

        const formData = new FormData(formProductos);
        formData.append('id_caja', idCaja); // Agregar la sede al FormData

        axios.post('controllers/productosController.php?option=registrarProducto', formData)
            .then(response => {
                const data = response.data;

                if (data.tipo === 'success') {
                    Swal.fire('Éxito', data.mensaje, 'success');
                    modalMensajeRol.style.display = 'none'; // Ocultar modal
                    formProductos.reset(); // Limpiar el formulario
                    cargarProductos(); // Recargar la tabla de productos
                } else {
                    Swal.fire('Error', data.mensaje, 'error');
                }
            })
            .catch(error => {
                console.error('Error al guardar el producto:', error);
                Swal.fire('Error', 'Hubo un problema al guardar el producto.', 'error');
            });
    });

    cerrarModal.addEventListener('click', function () {
        modalMensajeRol.style.display = 'none'; // Cerrar el modal
    });

    // Cargar los productos y proveedores al iniciar la página
    if (table_productos) {
        cargarProductos();
    }
    cargarProveedores();
});
