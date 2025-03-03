let table_temp = document.querySelector('#table_temp tbody');
const totalVenta = document.querySelector('#total-venta'); 
const nombre_personal = document.querySelector('#nombre-personal');
const area_personal = document.querySelector('#area-personal');
const id_personal = document.querySelector('#id-personal');
const capacidad_personal = document.querySelector('#capacidad-personal');
const search = document.querySelector('#search'); 
let btn_save;

// Validar caja y sesión antes de ejecutar cualquier función
document.addEventListener('DOMContentLoaded', function () {
    console.log("ID Usuario en JS:", idUsuario); // Depuración

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

    verificarCajaYSesion(idUsuario);
});

function verificarCajaYSesion(idUsuario) {
    console.log("Enviando ID Usuario:", idUsuario); // Depuración antes de enviar la petición

    axios.post(ruta + 'controllers/ventasController.php?option=verificarCajaSesion', new URLSearchParams({
        id_usuario: idUsuario
    }))
    .then(function (response) {
        console.log("Respuesta del servidor:", response.data); // Depuración

        if (response.data.caja_abierta === true) {
            console.log("Acceso permitido a ventas");
            inicializarFuncionesVentas();
        } else {
            console.log("Acceso restringido a ventas");
            Swal.fire({
                icon: 'error',
                title: 'Acceso denegado',
                text: 'Debe tener abierta una caja para poder vender',
                confirmButtonText: 'Ir a abrir caja'
            }).then(() => {
                window.location.href = '?pagina=configuracion';
            });
        }
    })
    .catch(function (error) {
        console.error("Error al verificar la sesión:", error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Ocurrió un error al verificar la sesión. Intente nuevamente.',
            confirmButtonText: 'Aceptar'
        });
    });
}


// Inicializar funciones solo si la caja está abierta
function inicializarFuncionesVentas() {
    let table_personal = $('#table_personal').DataTable({
        ajax: { 
            url: ruta + 'controllers/ventasController.php?option=listarPersonal',
            dataSrc: ''
        },
        columns: [
            { data: 'id', title: 'Cédula' },   
            { data: 'nombre', title: 'Nombre' },
            { data: 'area', title: 'Área' },
            { data: 'capacidad', title: 'Capacidad' }
        ],
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.1/i18n/es-ES.json'
        },
        createdRow: function (row, data) {
            if (data.capacidad == 0) {
                $(row).addClass('table-danger');
            }
        }
    });

    $('#table_personal tbody').on('dblclick', 'tr', function () {
        const datos = table_personal.row(this).data();
        if (datos) {
            if (id_personal) id_personal.value = datos.id || '';
            if (nombre_personal) nombre_personal.value = datos.nombre || '';
            if (area_personal) area_personal.value = datos.area || '';
            if (capacidad_personal) capacidad_personal.value = datos.capacidad || '';

            $('#modal-personal').modal('hide');
        } else {
            message('error', 'Error al seleccionar el personal. Intente nuevamente.');
        }
    });

    // Inicializar el botón de guardar
    btn_save = document.querySelector('#btn-guardar'); 

    if (btn_save) {
        btn_save.onclick = function () {
            if (!id_personal.value || !metodo.value) {
                message('error', 'Debe seleccionar un personal');
                return;
            }
        
            axios.post(ruta + 'controllers/ventasController.php?option=saveventa', {
                cedula: id_personal.value, // Enviar el ID del personal
                metodo: metodo.value
            })
            .then(function (response) {
                const info = response.data;
                message(info.tipo, info.mensaje);
                if (info.tipo === 'success') {
                    updateStock();
                    temp();
                    resetFormularios();
                }
            })
            .catch(function (error) {
                console.error("Error al guardar la venta:", error);
                message('error', 'Ocurrió un error al guardar la venta');
            });
        };
    } else {
        console.error('El botón #btn-guardar no se encontró en el DOM.');
    }

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

    // Configuración de la tabla de ventas
    $('#table_venta').DataTable({
        ajax: {
            url: ruta + 'controllers/ventasController.php?option=listar',
            dataSrc: ''
        },
        columns: [
            { data: 'codigo_producto', title: 'Código' },
            { data: 'descripcion', title: 'Descripción' },
            {
                data: 'existencia',
                title: 'Existencia',
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
            { data: 'precio_venta', title: 'Precio' },
            { data: 'addcart', title: 'Acción', orderable: false, searchable: false }
        ],
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.1/i18n/es-ES.json'
        }
    });    

    temp(); // Inicializa la tabla temporal y el total
}


// Reiniciar formularios después de guardar la venta
function resetFormularios() {
    if (id_personal) id_personal.value = '';
    if (nombre_personal) nombre_personal.value = '';
    if (area_personal) area_personal.value = '';
    if (capacidad_personal) capacidad_personal.value = '';
    if (metodo) metodo.value = 'Efectivo';
    if (table_temp) table_temp.innerHTML = '';
    if (totalVenta) totalVenta.textContent = '0';
}

function addCart(codProducto) {
    axios.get(ruta + 'controllers/ventasController.php?option=addcart&id=' + codProducto)
        .then(function (response) {
            const info = response.data;
            message(info.tipo, info.mensaje);
            updateStock();
            temp();
        })
        .catch(function (error) {
            console.log(error);
        });
}

// Función para actualizar la tabla temporal
function temp() {
    axios.get(ruta + 'controllers/ventasController.php?option=listarTemp')
        .then(function (response) {
            if (!Array.isArray(response.data)) {
                console.error('Error: response.data no es un array', response.data);
                message('error', 'Error al cargar la tabla temporal. Datos no válidos.');
                return;
            }

            let tempProductos = '';
            let total = 0;

            response.data.forEach(pro => {
                const precio = parseFloat(pro.precio_venta) || 0; // Asegura que precio_venta tenga un valor válido
                const cantidad = parseInt(pro.cantidad) || 0; // Asegura que cantidad sea válida
                const subtotal = precio * cantidad;

                tempProductos += `<tr>
                    <td>${pro.descripcion}</td>
                    <td><input class="form-control" type="number" value="${precio}" onchange="addPrecio(event, ${pro.id_producto})" /></td>
                    <td><input class="form-control" type="number" value="${cantidad}" onchange="addCantidad(event, ${pro.id_producto})" /></td>
                    <td>${subtotal.toFixed(2)}</td>
                    <td><i class="fas fa-eraser text-danger" onclick="deleteProducto(${pro.id_producto})"></i></td>
                </tr>`;
                total += subtotal;
            });

            table_temp.innerHTML = tempProductos;
            totalVenta.textContent = total.toFixed(2);
        })
        .catch(function (error) {
            console.error("Error al cargar la tabla temporal:", error);
        });
}

function cargarProductos() {
    let endpoint = mostrarTodosRegistros 
        ? ruta + 'controllers/ventasController.php?option=listar'
        : ruta + 'controllers/ventasController.php?option=listar';

    axios.get(endpoint)
        .then(response => {
            if (Array.isArray(response.data)) {
                actualizarTablaProductos(response.data);
            } else {
                message('error', response.data.mensaje);
            }
        })
        .catch(error => {
            console.error('Error al cargar productos:', error);
            message('error', 'Error al cargar productos');
        });
}



function addCantidad(e, idTemp) {
    axios.post(ruta + 'controllers/ventasController.php?option=addcantidad', {
        id: idTemp,
        cantidad: e.target.value
    })
    .then(() => {
        updateStock();
        temp();
    });
}

function addPrecio(e, idTemp) {
    axios.post(ruta + 'controllers/ventasController.php?option=addprecio', {
        id: idTemp,
        precio: e.target.value
    })
    .then(() => {
        updateStock();
        temp();
    });
}

function deleteProducto(idTemp) {
    axios.get(ruta + 'controllers/ventasController.php?option=delete&id=' + idTemp)
    .then(() => {
        updateStock();
        temp();
    });
}

function updateStock() {
    $('#table_venta').DataTable().ajax.reload();
}
