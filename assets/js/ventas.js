let table_temp = document.querySelector('#table_temp tbody');
const totalVenta = document.querySelector('#total-venta'); 
const nombre_cliente = document.querySelector('#nombre-cliente');
const area_cliente = document.querySelector('#area-cliente');
const id_cliente = document.querySelector('#id-cliente');
const capacidad_cliente = document.querySelector('#capacidad-cliente');
const search = document.querySelector('#search'); 
let btn_save;
let table_clientes; 

document.addEventListener('DOMContentLoaded', function () {
    btn_save = document.querySelector('#btn-guardar');
    $('#table_venta').DataTable({
        ajax: {
            url: ruta + 'controllers/ventasController.php?option=listar',
            dataSrc: ''
        },
        columns: [
            { data: 'codigo_producto' },
            { data: 'descripcion' },
            {
                data: 'existencia',
                render: function (data, type, row) {
                    let colorClass = 'badge-info'; // Azul por defecto
                    if (row.porcentajeStock <= 10) {
                        colorClass = 'badge-danger'; // Rojo
                    } else if (row.porcentajeStock <= 30) {
                        colorClass = 'badge-warning'; // Amarillo
                    }
                    return `<span class="badge ${colorClass}">${data}</span>`;
                }
            },
            { data: 'precio_venta' },
            { data: 'addcart' }
        ],
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.1/i18n/es-ES.json'
        }
    });
    
    temp(); // Inicializa la tabla temporal y el total

    document.addEventListener('DOMContentLoaded', function () {
        let table_clientes = $('#table_clientes').DataTable({
            ajax: {
                url: ruta + 'controllers/ventasController.php?option=listar-clientes', 
                dataSrc: ''
            },
            columns: [
                {
                    data: null,
                    render: function (data) {
                        return `${data.nombre_completo}`;
                    }
                },
                { data: 'id_cliente' },
                { data: 'area' },
                { data: 'capacidad' }
            ],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.1/i18n/es-ES.json'
            },
            createdRow: function (row, data) {
                if (data.capacidad == 0) {
                    $(row).addClass('table-danger'); // Resalta filas con capacidad 0
                }
            }
        });
    
        // Doble clic en una fila para seleccionar un cliente
        $('#table_clientes tbody').on('dblclick', 'tr', function () {
            const datos = table_clientes.row(this).data();
            document.querySelector('#id-cliente').value = datos.id_cliente;
            document.querySelector('#nombre-cliente').value = datos.nombre_completo;
            document.querySelector('#area-cliente').value = datos.area;
            document.querySelector('#capacidad-cliente').value = datos.capacidad;
            $('#modal-cliente').modal('hide');
        });
    });
    
    search.addEventListener('keyup', function (e) {
        if (e.key === "Enter") {
            if (e.target.value.trim() === '') {
                message('error', 'INGRESE CÓDIGO DE BARRAS');
            } else {
                axios.get(ruta + 'controllers/ventasController.php?option=searchbarcode&barcode=' + e.target.value.trim())
                    .then(function (response) {
                        // Suponemos que si hay una respuesta, el producto se encontró
                        console.log("Respuesta del servidor: ", response.data); // Depuración
                        message('success', 'Producto agregado correctamente al carrito');
    
                        // Limpieza y actualizaciones
                        search.value = ''; // Limpia el campo de búsqueda
                        updateStock(); // Actualiza stock
                        temp(); // Actualiza carrito
                    })
                    .catch(function (error) {
                        console.error("Error en la solicitud: ", error); // Depuración
                        message('error', 'Ocurrió un error al procesar la solicitud');
                    });
            }
        }
    });
    
    
            
    btn_save.onclick = function () {
        axios.post(ruta + 'controllers/ventasController.php?option=saveventa', {
            idCliente: id_cliente.value,
            metodo: metodo.value
        })
        .then(function (response) {
            const info = response.data;
            message(info.tipo, info.mensaje);
            if (info.tipo === 'success') {
                updateStock();
                temp(); // Actualiza la tabla temporal y el total

                // Reinicia los formularios
                resetFormularios();
            }
        })
        .catch(function (error) {
            console.log(error);
        });
    };
});

function resetFormularios() {
    // Reiniciar campos del cliente
    id_cliente.value = '';
    nombre_cliente.value = '';
    area_cliente.value = '';
    capacidad_cliente.value = '';

    // Reiniciar el método de pago al valor predeterminado
    metodo.value = 'Efectivo';

    // Vaciar la tabla temporal
    table_temp.innerHTML = '';

    // Reiniciar el total a 0
    totalVenta.textContent = '0';
}

function addCart(codProducto) {
    axios.get(ruta + 'controllers/ventasController.php?option=addcart&id=' + codProducto)
        .then(function (response) {
            const info = response.data;
            message(info.tipo, info.mensaje);
            updateStock();
            temp(); // Actualiza la tabla temporal y el total
        })
        .catch(function (error) {
            console.log(error);
        });
}

function temp() {
    axios.get(ruta + 'controllers/ventasController.php?option=listarTemp')
        .then(function (response) {
            const info = response.data;
            let tempProductos = '';
            let total = 0; // Inicializa el total

            info.forEach(pro => {
                const subtotal = parseFloat(pro.precio_venta) * parseInt(pro.cantidad);
                tempProductos += `<tr>
                    <td>${pro.descripcion}</td>
                    <td><input class="form-control" type="number" value="${pro.precio_venta}" onchange="addPrecio(event, ${pro.id_producto})" /></td>
                    <td><input class="form-control" type="number" value="${pro.cantidad}" onchange="addCantidad(event, ${pro.id_producto})" /></td>
                    <td>${subtotal.toFixed(2)}</td>
                    <td><i class="fas fa-eraser text-danger" onclick="deleteProducto(${pro.id_producto})"></i></td>
                </tr>`;
                total += subtotal; // Suma el subtotal al total
            });

            table_temp.innerHTML = tempProductos;
            totalVenta.textContent = total.toFixed(2); // Actualiza el total en la vista
        })
        .catch(function (error) {
            console.log(error);
        });
}

function addCantidad(e, idTemp) {
    axios.post(ruta + 'controllers/ventasController.php?option=addcantidad', {
        id: idTemp,
        cantidad: e.target.value
    })
    .then(function (response) {
        const info = response.data;
        if (info.tipo == 'error') {
            message(info.tipo, info.mensaje);
            return;
        }
        updateStock();
        temp(); // Actualiza la tabla temporal y el total
    })
    .catch(function (error) {
        console.log(error);
    });
}

function addPrecio(e, idTemp) {
    axios.post(ruta + 'controllers/ventasController.php?option=addprecio', {
        id: idTemp,
        precio: e.target.value
    })
    .then(function (response) {
        const info = response.data;
        if (info.tipo == 'error') {
            message(info.tipo, info.mensaje);
            return;
        }
        updateStock();
        temp(); // Actualiza la tabla temporal y el total
    })
    .catch(function (error) {
        console.log(error);
    });
}

function deleteProducto(idTemp) {
    axios.get(ruta + 'controllers/ventasController.php?option=delete&id=' + idTemp)
    .then(function (response) {
        const info = response.data;
        message(info.tipo, info.mensaje);
        updateStock();
        temp(); // Actualiza la tabla temporal y el total
    })
    .catch(function (error) {
        console.log(error);
    });
}

function updateStock() {
    $('#table_venta').DataTable().ajax.reload();
}
