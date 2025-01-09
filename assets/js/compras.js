document.addEventListener('DOMContentLoaded', function () {
    const table_productos = document.querySelector('#table_productos tbody');
    const btn_Recibido = document.querySelector('#btn-Recibido');
    const btn_pendiente = document.querySelector('#btn-pendiente');
    const modalElement = document.getElementById('modalMensajeRol');
    const modalMensajeTexto = document.getElementById('modalMensajeTexto');
    const selectSede = document.getElementById('selectSede');
    const selectMetodo = document.getElementById('selectMetodo');
    const btnGuardarModal = document.getElementById('guardarModal');
    const btnCerrarModal = document.getElementById('cerrarModal');

    // Intentar obtener el elemento de selectEmpresa solo si existe
    const selectEmpresa = document.getElementById('id_empresa');

    // Convertir rolUsuario a entero
    const rol = parseInt(rolUsuario, 10);

    // Validar que el rol sea válido
    if (isNaN(rol)) {
        console.error('rolUsuario no está definido o no es un número válido:', rolUsuario);
        return;
    }

    console.log('Rol del usuario:', rol);

    // Cargar tabla de productos si existe
    if (table_productos) {
        cargarProductos();
    }

    // Configurar botones Recibido y Pendiente
    configurarBotonCompra(btn_Recibido, 1); // Estado 1: Recibido
    configurarBotonCompra(btn_pendiente, 0); // Estado 0: Pendiente

    // Configurar eventos para el modal
    if (btnGuardarModal) {
        btnGuardarModal.addEventListener('click', function () {
            const sedeSeleccionada = selectSede.value;
            const metodoSeleccionado = selectMetodo.value;
        
            console.log(`Sede seleccionada: ${sedeSeleccionada}`);
            console.log(`Método seleccionado: ${metodoSeleccionado}`);
        
            // Enviar datos del modal al backend
            enviarDatosModal(sedeSeleccionada, metodoSeleccionado);
        
            cerrarModal();
        });        
    }

    if (btnCerrarModal) {
        btnCerrarModal.addEventListener('click', cerrarModal);
    }

    // Función para configurar botones de compra
    function configurarBotonCompra(boton, estado) {
        if (boton) {
            boton.onclick = function () {
                console.log('Rol del usuario al presionar el botón:', rol);

                if (rol === 1 || rol === 2) {
                    // Mostrar modal si el rol es 1 o 2
                    mostrarModal("Este es un mensaje para roles 1 o 2");
                } else if (rol === 3) {
                    // Mostrar SweetAlert si el rol es 3
                    Swal.fire({
                        title: '¿De dónde viene el dinero?',
                        input: 'select',
                        inputOptions: {
                            1: 'Socio',
                            2: 'Caja',
                            3: 'Bancaria',
                        },
                        inputPlaceholder: 'Seleccione el método',
                        showCancelButton: true,
                        cancelButtonText: 'Cancelar',
                        confirmButtonText: 'Guardar',
                    }).then((result) => {
                        if (result.isConfirmed && result.value) {
                            const metodo_compra = result.value;
                            enviarDatosCompra(estado, metodo_compra); // Incluye el método
                        } else {
                            console.log("Operación cancelada o método no seleccionado");
                        }
                    });
                } else {
                    console.error('Rol no válido:', rol);
                }
            };
        }
    }

    // Función para mostrar el modal
    function mostrarModal(mensaje) {
        if (modalElement && modalMensajeTexto) {
            modalMensajeTexto.textContent = mensaje;
            modalElement.style.display = 'flex';
        }
    }

    // Función para cerrar el modal
    function cerrarModal() {
        if (modalElement) {
            modalElement.style.display = 'none';
        }
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

    function cargarEmpresas() {
        axios.get('controllers/comprasController.php?option=listarEmpresas')
            .then(function (response) {
                const empresas = response.data;
                selectEmpresa.innerHTML = '<option value="">Seleccione una empresa</option>';

                if (Array.isArray(empresas)) {
                    empresas.forEach(function (empresa) {
                        const option = document.createElement('option');
                        option.value = empresa.id_empresa;
                        option.textContent = empresa.razon_social;
                        selectEmpresa.appendChild(option);
                    });
                } else {
                    console.warn('La respuesta no es un arreglo:', empresas);
                }
            })
            .catch(function (error) {
                console.error(error);
            });
    }

    function enviarDatosCompra(estado, metodo_compra) {
        const formData = new FormData(document.getElementById('frmProductos'));

        // Agrega el estado y método de compra al formulario
        formData.append('estado', estado);
        formData.append('metodo_compra', metodo_compra);

        axios.post('controllers/comprasController.php?option=registrarCompra', formData)
            .then(response => {
                const data = response.data;
                if (data.tipo === 'success') {
                    Swal.fire('Éxito', data.mensaje, 'success');
                    cargarProductos(); // Recarga la tabla
                } else {
                    Swal.fire('Error', data.mensaje, 'error');
                }
            })
            .catch(error => {
                console.error('Error al registrar la compra:', error);
                Swal.fire('Error', 'No se pudo registrar la compra.', 'error');
            });
    }

    function enviarDatosModal(sede, metodo) {
        const formData = new FormData(document.getElementById('frmProductos'));
    
        // Añadir datos del modal
        formData.append('sede', sede);
        formData.append('metodo', metodo);
    
        axios.post('controllers/comprasController.php?option=guardarDesdeModal', formData)
            .then(response => {
                const data = response.data;
                if (data.tipo === 'success') {
                    Swal.fire('Éxito', data.mensaje, 'success');
                    cargarProductos(); // Recarga la tabla
                } else {
                    Swal.fire('Error', data.mensaje, 'error');
                }
            })
            .catch(error => {
                console.error('Error al guardar desde el modal:', error);
                Swal.fire('Error', 'No se pudo guardar la información del modal.', 'error');
            });
    }
    
});
