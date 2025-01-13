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
    const selectEmpresa = document.getElementById('id_empresa');
    const rol = parseInt(rolUsuario, 10);

    if (isNaN(rol)) {
        console.error('El rol del usuario no es válido:', rolUsuario);
        return;
    }

    if (table_productos) {
        cargarProductos();
    }

    configurarBotonCompra(btn_Recibido, 1);
    configurarBotonCompra(btn_pendiente, 0);

    if (btnGuardarModal) {
        btnGuardarModal.addEventListener('click', function () {
            const sedeSeleccionada = selectSede?.value || '';
            const metodoSeleccionado = selectMetodo?.value || '';

            enviarDatosModal(sedeSeleccionada, metodoSeleccionado);
            cerrarModal();
        });
    }

    if (btnCerrarModal) {
        btnCerrarModal.addEventListener('click', cerrarModal);
    }

    function configurarBotonCompra(boton, estado) {
        if (boton) {
            boton.onclick = function () {
                if (rol === 1 || rol === 2) {
                    mostrarModal("Este es un mensaje para roles 1 o 2");
                } else if (rol === 3) {
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
                            enviarDatosCompra(estado, result.value);
                        }
                    });
                } else {
                    console.error('Rol no válido:', rol);
                }
            };
        }
    }

    function mostrarModal(mensaje) {
        if (modalElement && modalMensajeTexto) {
            modalMensajeTexto.textContent = mensaje;
            modalElement.style.display = 'flex';
        }
    }

    function cerrarModal() {
        if (modalElement) {
            modalElement.style.display = 'none';
        }
    }

    function cargarProductos() {
        axios.get(`controllers/comprasController.php?option=listarProductos&id_caja=${idSede}&rolUsuario=${rolUsuario}`)
            .then(function (response) {
                const data = response.data;

                if (typeof data !== 'object' || !data.productos) {
                    console.error('La respuesta no es válida:', data);
                    return;
                }
                const productos = data.productos;
                Object.values(productos).forEach(function (producto) {
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
                    table_productos.appendChild(row);
                });
            })
            .catch(function (error) {
                console.error('Error al cargar productos:', error);
            });
    }

    if (selectEmpresa) {
        cargarEmpresas();
    }

    function cargarEmpresas() {
        axios.get('controllers/comprasController.php?option=listarEmpresas')
            .then(function (response) {
                const empresas = response.data;

                if (!Array.isArray(empresas)) {
                    console.warn('La respuesta no es un arreglo:', empresas);
                    return;
                }

                selectEmpresa.innerHTML = '<option value="">Seleccione una empresa</option>';

                empresas.forEach(function (empresa) {
                    const option = document.createElement('option');
                    option.value = empresa.id_empresa;
                    option.textContent = empresa.razon_social;
                    selectEmpresa.appendChild(option);
                });
            })
            .catch(function (error) {
                console.error('Error al cargar empresas:', error);
            });
    }

    function enviarDatosCompra(estado, metodo_compra) {
        const formData = new FormData(document.getElementById('frmProductos'));
        formData.append('estado', estado);
        formData.append('metodo_compra', metodo_compra);

        axios.post('controllers/comprasController.php?option=registrarCompra', formData)
            .then(response => {
                const data = response.data;
                if (data.tipo === 'success') {
                    Swal.fire('Éxito', data.mensaje, 'success');
                    cargarProductos();
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
        formData.append('sede', sede);
        formData.append('metodo', metodo);

        axios.post('controllers/comprasController.php?option=guardarDesdeModal', formData)
            .then(response => {
                const data = response.data;
                if (data.tipo === 'success') {
                    Swal.fire('Éxito', data.mensaje, 'success');
                    cargarProductos();
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
