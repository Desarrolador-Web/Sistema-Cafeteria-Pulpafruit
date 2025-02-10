document.addEventListener('DOMContentLoaded', function () {
    // Elementos del DOM
    const formProductos = document.getElementById('frmProductos');
    const btnRecibido = document.getElementById('btn-Recibido');
    const selectSede = document.getElementById('selectSede');
    const modalMensajeRol = document.getElementById('modalMensajeRol');
    const guardarModal = document.getElementById('guardarModal');
    const cerrarModal = document.getElementById('cerrarModal');
    const table_productos = document.querySelector('#table_productos tbody');

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

    // Función para cargar los proveedores en el select
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

    // Manejar el evento de envío del formulario principal
    formProductos.addEventListener('submit', function (e) {
        e.preventDefault(); // Evitar que el formulario recargue la página

        const formData = new FormData(formProductos);

        // Enviar los datos al backend para registrar el producto
        axios.post('controllers/productosController.php?option=registrarProducto', formData)
            .then(response => {
                const data = response.data;

                if (data.tipo === 'success') {
                    Swal.fire('Éxito', data.mensaje, 'success');
                    cargarProductos(); // Recargar los productos para mostrar el nuevo
                } else {
                    Swal.fire('Error', data.mensaje, 'error');
                }
            })
            .catch(error => {
                console.error('Error al registrar el producto:', error);
                Swal.fire('Error', 'Hubo un problema al guardar el producto.', 'error');
            });
    });

    // Abrir el modal al presionar "Recibido"
    btnRecibido.addEventListener('click', function () {
        modalMensajeRol.style.display = 'flex'; // Muestra el modal
    });

    // Manejar el evento "Guardar" en el modal
    guardarModal.addEventListener('click', function () {
        const idCaja = selectSede.value; // Valor seleccionado en el modal
        if (!idCaja) {
            alert('Por favor seleccione una sede antes de continuar.');
            return;
        }

        // Crear FormData a partir del formulario
        const formData = new FormData(formProductos);

        // Agregar el valor de `id_caja` del modal al FormData
        formData.append('id_caja', idCaja);

        // Verificar los datos enviados (opcional para depuración)
        for (let [key, value] of formData.entries()) {
            console.log(`${key}: ${value}`);
        }

        // Enviar datos al backend
        axios.post('controllers/productosController.php?option=registrarProducto', formData)
            .then(response => {
                const data = response.data;

                if (data.tipo === 'success') {
                    Swal.fire('Éxito', data.mensaje, 'success');
                    modalMensajeRol.style.display = 'none'; // Oculta el modal
                    formProductos.reset(); // Limpia el formulario
                    cargarProductos(); // Recarga la tabla de productos
                } else {
                    Swal.fire('Error', data.mensaje, 'error');
                }
            })
            .catch(error => {
                console.error('Error al guardar el producto:', error);
                Swal.fire('Error', 'Hubo un problema al guardar el producto.', 'error');
            });
    });

    // Cerrar el modal
    cerrarModal.addEventListener('click', function () {
        modalMensajeRol.style.display = 'none';
    });

    // Cargar productos y proveedores al cargar la página
    if (table_productos) {
        cargarProductos();
    }
    cargarProveedores();
});
