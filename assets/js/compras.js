document.addEventListener('DOMContentLoaded', function () {
    const table_productos = document.querySelector('#table_productos tbody');
    const btn_despachado = document.querySelector('#btn-despachado');
    const btn_pendiente = document.querySelector('#btn-pendiente');
    const formProductos = document.querySelector('#frmProductos');
    const selectEmpresa = document.querySelector('#id_empresa'); 

    if (table_productos) {
        cargarProductos();
    }

    if (btn_despachado) {
        btn_despachado.onclick = function () {
            registrarCompra(1); // Despachado
        };
    }

    if (btn_pendiente) {
        btn_pendiente.onclick = function () {
            registrarCompra(0); // Pendiente
        };
    }

    if (selectEmpresa) {
        cargarEmpresas(); 
    }

    function registrarCompra(estado) {
        const formData = new FormData(formProductos);
        formData.append('estado', estado);

        const id_empresa = document.querySelector('#id_empresa').value; 
        if (!id_empresa) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Por favor seleccione una empresa.'
            });
            return;
        }

        axios.post('controllers/comprasController.php?option=registrarCompra', formData)
            .then(function (response) {
                const info = response.data;
                Swal.fire({
                    icon: info.tipo === 'success' ? 'success' : 'error',
                    title: info.tipo === 'success' ? 'Éxito' : 'Error',
                    text: info.mensaje
                });
                cargarProductos();
                formProductos.reset();
            })
            .catch(function (error) {
                console.log(error);
            });
    }

    function cargarProductos() {
        axios.get('controllers/comprasController.php?option=listarProductos')
            .then(function (response) {
                const productos = response.data;
                const tbody = document.querySelector('#table_productos tbody');
                
                // Destruye la instancia existente de DataTable si existe
                if ($.fn.DataTable.isDataTable('#table_productos')) {
                    $('#table_productos').DataTable().destroy();
                }

                tbody.innerHTML = ''; // Limpia el contenido de la tabla

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
                                    ? '<button class="btn btn-success btn-sm" disabled>Despachado</button>' 
                                    : `<button class="btn btn-warning btn-sm" onclick="cambiarEstado(${producto.id_producto}, 1)">Pendiente</button>`}
                            </td>
                        `;
                        tbody.appendChild(row);
                    });

                    // Reinicia el DataTable después de cargar los nuevos datos
                    $('#table_productos').DataTable({
                        dom: 'Bfrtip',
                        buttons: [
                            'copy', 'csv', 'excel', 'pdf', 'print'
                        ],
                        language: {
                            url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
                        }
                    });
                } else {
                    console.log('La respuesta no es un arreglo:', productos);
                }
            })
            .catch(function (error) {
                console.log(error);
            });
    }

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
                    console.log('La respuesta no es un arreglo:', empresas);
                }
            })
            .catch(function (error) {
                console.log(error);
            });
    }

    window.cambiarEstado = function (id, nuevoEstado) {
        Swal.fire({
            title: 'Ingrese el código de barras para despachar el producto:',
            input: 'text',
            inputAttributes: {
                autocapitalize: 'off'
            },
            showCancelButton: true,
            confirmButtonText: 'Despachar',
            cancelButtonText: 'Cancelar',
            showLoaderOnConfirm: true,
            preConfirm: (barcode) => {
                if (!/^\d+$/.test(barcode) || barcode.trim() === '') {
                    Swal.showValidationMessage(
                        'El código de barras debe ser una serie de números y no puede estar vacío.'
                    );
                }
                return barcode;
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed) {
                axios.post('controllers/comprasController.php?option=cambiarEstado', {
                    id: id,
                    estado: nuevoEstado,
                    barcode: result.value
                })
                .then(function (response) {
                    const info = response.data;
                    Swal.fire({
                        icon: info.tipo === 'success' ? 'success' : 'error',
                        title: info.tipo === 'success' ? 'Éxito' : 'Error',
                        text: info.mensaje
                    });
                    cargarProductos();
                })
                .catch(function (error) {
                    console.log(error);
                });
            }
        });
    }
});
