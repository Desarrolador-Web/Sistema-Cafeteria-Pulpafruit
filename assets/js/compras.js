document.addEventListener('DOMContentLoaded', function () {
    const table_productos = document.querySelector('#table_productos tbody');
    const btnGuardarModal = document.getElementById('guardarModal');
    const btnCerrarModal = document.getElementById('cerrarModal');
    const selectSede = document.getElementById('selectSede');
    const selectEmpresa = document.getElementById('id_empresa');
    const modalElement = document.getElementById('modalMensajeRol');
    const modalMensajeTexto = document.getElementById('modalMensajeTexto');
    const rol = parseInt(rolUsuario, 10);
    const idSedeSesion = idSede;

    if (isNaN(rol)) {
        console.error('El rol del usuario no es válido:', rolUsuario);
        return;
    }

    if (table_productos) {
        cargarProductos();
    }

    if (btnGuardarModal) {
        btnGuardarModal.addEventListener('click', function () {
            const estado = modalElement.getAttribute('data-estado');
            const sedeSeleccionada = rol === 3 ? idSedeSesion : 4; 
            const metodoSeleccionado = document.getElementById('selectMetodo')?.value || '';
            
            if (!sedeSeleccionada || !metodoSeleccionado) {
                Swal.fire('Error', 'Faltan datos del modal (sede o método) para guardar.', 'error');
                return;
            }

            enviarDatosModal(sedeSeleccionada, metodoSeleccionado, estado);
        });
    }

    if (btnCerrarModal) {
        btnCerrarModal.addEventListener('click', cerrarModal);
    }

    document.querySelectorAll('[data-estado]').forEach(button => {
        button.addEventListener('click', () => {
            const estado = button.getAttribute('data-estado');
            modalElement.setAttribute('data-estado', estado);

            if (rol === 1 || rol === 2) {
                mostrarModal("Complete los datos antes de guardar");
            } else if (rol === 3) {
                mostrarSweetAlert(estado);
            } else {
                console.error('Rol no válido:', rol);
            }
        });
    });

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
        axios.get(`controllers/comprasController.php?option=listarProductos&id_caja=${rol === 3 ? idSedeSesion : 4}&rolUsuario=${rolUsuario}`)
            .then(response => {
                const data = response.data;
                if (typeof data !== 'object' || !data.productos) {
                    console.error('La respuesta no es válida:', data);
                    return;
                }
                mostrarProductos(data.productos);
            })
            .catch(error => console.error('Error al cargar productos:', error));
    }

    function mostrarProductos(productos) {
        table_productos.innerHTML = ''; 
        productos.forEach(producto => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${producto.Id}</td>
                <td>${producto.Barcode}</td>
                <td>${producto.Descripción}</td>
                <td>${producto.Precio_Compra}</td>
                <td>${producto.Precio_Venta}</td>
                <td>${producto.Proveedor}</td>
                <td><img src="${producto.Imagen}" alt="Imagen del Producto" width="50" /></td>
                <td>${producto.Cantidad}</td>
                <td>
                    ${producto.status == 1 
                        ? '<button class="btn btn-success btn-sm" disabled>Recibido</button>' 
                        : `<button class="btn btn-warning btn-sm" onclick="cambiarEstado(${producto.id_producto}, 1)">Pendiente</button>`}
                </td>
            `;
            table_productos.appendChild(row);
        });
    }

    if (selectEmpresa) {
        cargarEmpresas();
    }

    function cargarEmpresas() {
        axios.get('controllers/comprasController.php?option=listarEmpresas')
            .then(response => {
                const empresas = response.data;
                if (!Array.isArray(empresas)) {
                    console.warn('La respuesta no es un arreglo:', empresas);
                    return;
                }

                selectEmpresa.innerHTML = '<option value="">Seleccione una empresa</option>';
                empresas.forEach(empresa => {
                    const option = document.createElement('option');
                    option.value = empresa.id_empresa;
                    option.textContent = empresa.razon_social;
                    selectEmpresa.appendChild(option);
                });
            })
            .catch(error => console.error('Error al cargar empresas:', error));
    }

    function mostrarSweetAlert(estado) {
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
            confirmButtonText: 'Guardar',
            cancelButtonText: 'Cancelar',
        }).then(result => {
            if (result.isConfirmed) {
                const metodoSeleccionado = result.value;
                enviarDatosModal(idSedeSesion, metodoSeleccionado, estado); // Usar idSedeSesion para rol 3
            }
        });
    }

    function enviarDatosModal(sede, metodo, estado) {
        const formData = new FormData(document.getElementById('frmProductos'));
        formData.append('sede', sede);
        formData.append('metodo', metodo);
        formData.append('estado', estado);

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
            .catch(error => console.error('Error al guardar datos del modal:', error));
    }

    window.cambiarEstado = function (id, nuevoEstado) {
        Swal.fire({
            title: 'Ingrese el código de barras para actualizar el producto pendiente:',
            input: 'text',
            showCancelButton: true,
            confirmButtonText: 'Actualizar',
            cancelButtonText: 'Cancelar',
            preConfirm: barcode => {
                if (!/^\d+$/.test(barcode)) {
                    Swal.showValidationMessage('El código de barras debe ser numérico.');
                    return false;
                }
                return barcode;
            }
        }).then(result => {
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
                        console.error('Error al cambiar el estado:', error);
                        Swal.fire('Error', 'Error inesperado.', 'error');
                    });
            }
        });
    };
});
