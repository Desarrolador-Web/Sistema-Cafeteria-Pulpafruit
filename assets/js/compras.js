document.addEventListener('DOMContentLoaded', function () {
    const table_productos = document.querySelector('#table_productos tbody');
    const btn_Recibido = document.querySelector('#btn-Recibido');
    const btn_pendiente = document.querySelector('#btn-pendiente');
    const formProductos = document.querySelector('#frmProductos');
    const selectEmpresa = document.querySelector('#id_empresa');

    // Cargar la tabla de productos si existe
    if (table_productos) {
        cargarProductos();
    }

    // Configurar botones Recibido y Pendiente
    configurarBotonCompra(btn_Recibido, 1); // Estado 1: Recibido
    configurarBotonCompra(btn_pendiente, 0); // Estado 0: Pendiente

    // Función para configurar botones
    function configurarBotonCompra(boton, estado) {
        if (boton) {
            boton.onclick = function () {
                if (rolUsuario === 1 || rolUsuario === 2) {
                    // Mostrar el modal si el rol es 1 o 2
                    $('#modalCompra').modal('show');
                    document.getElementById('estadoCompra').value = estado; // Guardar estado en modal
                } else {
                    // Mostrar SweetAlert si el rol es diferente a 1 o 2
                    Swal.fire({
                        title: '¿De dónde viene el dinero?',
                        showCancelButton: true,
                        cancelButtonText: 'Cancelar',
                        confirmButtonText: 'Caja',
                        showDenyButton: true,
                        denyButtonText: 'Socio',
                    }).then((result) => {
                        let metodo_compra = 0;
                        if (result.isConfirmed) {
                            metodo_compra = 2; // Caja
                        } else if (result.isDenied) {
                            metodo_compra = 1; // Socio
                        }
                        if (metodo_compra !== 0) {
                            registrarCompra(estado, metodo_compra);
                        }
                    });
                }
            };
        }
    }

    // Función para registrar compra desde el modal
    function registrarCompraDesdeModal() {
        const metodo_compra = document.getElementById('metodoCompra').value;
        const estado = document.getElementById('estadoCompra').value;

        if (metodo_compra) {
            registrarCompra(estado, metodo_compra);
            $('#modalCompra').modal('hide');
        } else {
            Swal.fire('Error', 'Seleccione un método de compra válido', 'error');
        }
    }

    // Función para registrar una compra
    function registrarCompra(estado, metodo_compra) {
        const formData = new FormData(formProductos);
        formData.append('estado', estado);
        formData.append('metodo_compra', metodo_compra);
    
        axios.post('controllers/comprasController.php?option=registrarCompra', formData)
            .then(function (response) {
                const info = response.data;
                Swal.fire({
                    icon: info.tipo === 'success' ? 'success' : 'error',
                    title: info.tipo === 'success' ? 'Éxito' : 'Error',
                    text: info.mensaje
                });
                if (info.tipo === 'success') {
                    cargarProductos();
                    formProductos.reset();
                }
            })
            .catch(function (error) {
                console.error(error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ocurrió un error inesperado. Por favor, inténtelo de nuevo.'
                });
            });
    }

    // Función para cargar productos
    function cargarProductos() {
        axios.get('controllers/comprasController.php?option=listarProductos')
            .then(function (response) {
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
                        language: {
                            url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
                        }
                    });
                } else {
                    console.warn('La respuesta no es un arreglo:', productos);
                }
            })
            .catch(function (error) {
                console.error(error);
            });
    }

    // Cargar empresas si existe el select
    if (selectEmpresa) {
        cargarEmpresas();
    }

    // Función para cargar empresas
    function cargarEmpresas() {
        axios.get('controllers/comprasController.php?option=listarEmpresas')
            .then(function (response) {
                const empresas = response.data;
                const select = document.querySelector('#id_empresa');
                select.innerHTML = '<option value="">Seleccione una empresa</option>';

                if (Array.isArray(empresas)) {
                    empresas.forEach(function (empresa) {
                        const option = document.createElement('option');
                        option.value = empresa.id_empresa;
                        option.textContent = empresa.razon_social;
                        select.appendChild(option);
                    });
                } else {
                    console.warn('La respuesta no es un arreglo:', empresas);
                }
            })
            .catch(function (error) {
                console.error(error);
            });
    }

    // Función para cambiar estado de un producto
    window.cambiarEstado = function (id, nuevoEstado) {
        Swal.fire({
            title: 'Ingrese el código de barras para actualizar el producto pendiente:',
            input: 'text',
            inputAttributes: {
                autocapitalize: 'off'
            },
            showCancelButton: true,
            confirmButtonText: 'Actualizar',
            cancelButtonText: 'Cancelar',
            preConfirm: (barcode) => {
                if (!/^\d+$/.test(barcode) || barcode.trim() === '') {
                    Swal.showValidationMessage('El código de barras debe ser una serie de números y no puede estar vacío.');
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
                    .then((response) => {
                        const info = response.data;
                        Swal.fire({
                            icon: info.tipo === 'success' ? 'success' : 'error',
                            title: info.tipo === 'success' ? 'Éxito' : 'Error',
                            text: info.mensaje
                        });
                        if (info.tipo === 'success') {
                            cargarProductos();
                        }
                    })
                    .catch((error) => {
                        console.error(error);
                        Swal.fire('Error inesperado', '', 'error');
                    });
            }
        });
    };
});
