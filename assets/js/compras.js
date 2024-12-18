document.addEventListener('DOMContentLoaded', function () {
    const btnRecibido = document.querySelector('#btn-Recibido');
    const btnPendiente = document.querySelector('#btn-pendiente');
    const btnGuardarMetodo = document.querySelector('#btnGuardarMetodo');
    const table_productos = document.querySelector('#table_productos tbody');
    const formProductos = document.querySelector('#frmProductos');
    let estadoCompra; // Variable para el estado actual (Recibido o Pendiente)

    // Evento para el botón "Recibido"
    if (btnRecibido) {
        btnRecibido.onclick = function () {
            estadoCompra = 1; // Recibido
            abrirModalMetodoPago();
        };
    }

    // Evento para el botón "Pendiente"
    if (btnPendiente) {
        btnPendiente.onclick = function () {
            estadoCompra = 0; // Pendiente
            abrirModalMetodoPago();
        };
    }

    // Abre el modal para seleccionar método de pago y sede
    function abrirModalMetodoPago() {
        const modal = new bootstrap.Modal(document.getElementById('modalMetodoPago'));
        modal.show();
    }

    // Evento para guardar método de pago y sede desde el modal
    btnGuardarMetodo.onclick = function () {
        const metodo_compra = document.querySelector('#metodo_compra').value;
        const id_caja = document.querySelector('#id_caja').value;

        if (!metodo_compra || !id_caja) {
            Swal.fire('Error', 'Debe seleccionar el método de pago y la sede.', 'error');
            return;
        }

        // Cierra el modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('modalMetodoPago'));
        modal.hide();

        // Envía los datos al servidor
        registrarCompra(estadoCompra, metodo_compra, id_caja);
    };

    // Función para registrar la compra
    function registrarCompra(estado, metodo_compra, id_caja) {
        const formData = new FormData(formProductos);
        formData.append('estado', estado); // Estado (Recibido o Pendiente)
        formData.append('metodo_compra', metodo_compra); // Método de pago
        formData.append('id_caja', id_caja); // Sede seleccionada

        axios.post('controllers/comprasController.php?option=registrarCompra', formData)
            .then(response => {
                const info = response.data;
                if (info.tipo === 'success') {
                    Swal.fire('Éxito', info.mensaje, 'success');
                    cargarProductos();
                    formProductos.reset();
                } else {
                    Swal.fire('Error', info.mensaje, 'error');
                }
            })
            .catch(error => {
                console.error(error);
                Swal.fire('Error', 'Ocurrió un error inesperado.', 'error');
            });
    }

    // Función para cargar productos
    function cargarProductos() {
        axios.get('controllers/comprasController.php?option=listarProductos')
            .then(response => {
                const productos = response.data;
                const tbody = document.querySelector('#table_productos tbody');

                if ($.fn.DataTable.isDataTable('#table_productos')) {
                    $('#table_productos').DataTable().destroy();
                }

                tbody.innerHTML = '';

                if (Array.isArray(productos)) {
                    productos.forEach(function (producto) {
                        const row = document.createElement('tr');

                        row.innerHTML = `
                            <td>${producto.idcompra}</td>
                            <td>${producto.codigo}</td>
                            <td>${producto.descripcion}</td>
                            <td>${producto.precio_compra}</td>
                            <td>${producto.precio_venta}</td>
                            <td>${producto.empresa}</td>
                            <td><img src="${producto.imagen}" alt="Imagen del Producto" width="50" /></td>
                            <td>${producto.existencia}</td>
                            <td>
                                ${producto.status == 1
                                    ? '<button class="btn btn-success btn-sm" disabled>Recibido</button>'
                                    : `<button class="btn btn-warning btn-sm" onclick="cambiarEstado(${producto.id_producto}, 1)">Pendiente</button>`}
                            </td>
                        `;
                        tbody.appendChild(row);
                    });

                    $('#table_productos').DataTable({
                        dom: 'Bfrtip',
                        buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
                        language: { url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json' }
                    });
                } else {
                    console.log('La respuesta no es un arreglo:', productos);
                }
            })
            .catch(error => {
                console.error(error);
            });
    }

    // Función para cambiar el estado del producto
    window.cambiarEstado = function (id, nuevoEstado) {
        Swal.fire({
            title: 'Ingrese el código de barras para actualizar el producto pendiente:',
            input: 'text',
            showCancelButton: true,
            confirmButtonText: 'Actualizar',
            cancelButtonText: 'Cancelar',
            preConfirm: (barcode) => {
                if (!/^\d+$/.test(barcode)) {
                    Swal.showValidationMessage('El código de barras debe ser numérico.');
                    return false;
                }
                return barcode;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('id', id);
                formData.append('estado', nuevoEstado);
                formData.append('barcode', result.value);

                axios.post('controllers/comprasController.php?option=cambiarEstado', formData)
                    .then(response => {
                        const info = response.data;
                        if (info.tipo === 'success') {
                            Swal.fire('Éxito', info.mensaje, 'success');
                            cargarProductos();
                        } else {
                            Swal.fire('Error', info.mensaje, 'error');
                        }
                    })
                    .catch(error => {
                        console.error(error);
                        Swal.fire('Error', 'Error inesperado.', 'error');
                    });
            }
        });
    };

    // Cargar empresas en el select
    function cargarEmpresas() {
        axios.get('controllers/comprasController.php?option=listarEmpresas')
            .then(response => {
                const empresas = response.data;
                const select = document.querySelector('#id_empresa');
                select.innerHTML = '<option value="">Seleccione una empresa</option>';

                if (Array.isArray(empresas)) {
                    empresas.forEach(empresa => {
                        const option = document.createElement('option');
                        option.value = empresa.id_empresa;
                        option.textContent = empresa.razon_social;
                        select.appendChild(option);
                    });
                }
            })
            .catch(error => console.error(error));
    }

    if (table_productos) {
        cargarProductos();
    }

    if (document.querySelector('#id_empresa')) {
        cargarEmpresas();
    }
});
