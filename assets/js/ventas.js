let table_temp = document.querySelector('#table_temp tbody');
<<<<<<< HEAD
const totalVenta = document.querySelector('#total-venta'); 
const nombre_personal = document.querySelector('#nombre-personal');
const area_personal = document.querySelector('#area-personal');
const id_personal = document.querySelector('#id-personal');
const capacidad_personal = document.querySelector('#capacidad-personal');
const search = document.querySelector('#search'); 
=======
const totalVenta = document.querySelector('#total-venta');
const nombre_cliente = document.querySelector('#nombre-cliente');
const area_cliente = document.querySelector('#area-cliente');
const id_cliente = document.querySelector('#id-cliente');
const capacidad_cliente = document.querySelector('#capacidad-cliente');
const search = document.querySelector('#search');
const idBio = document.querySelector('#idBio');
const metodo = document.querySelector('#metodo');
>>>>>>> eae77a48f863dceaf7a9bfa932cd8e5b7113d14f
let btn_save;

document.addEventListener('DOMContentLoaded', function () {
    // Inicializar tabla de personal
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

    // Evento para seleccionar un personal
    $('#table_personal tbody').on('dblclick', 'tr', function () {
        const datos = table_personal.row(this).data(); // Obtener datos de la fila seleccionada

        if (datos) {
            console.log("Datos seleccionados:", datos); // Depuración
            
            if (id_personal) id_personal.value = datos.id || '';
            if (nombre_personal) nombre_personal.value = datos.nombre || '';
            if (area_personal) area_personal.value = datos.area || '';
            if (capacidad_personal) capacidad_personal.value = datos.capacidad || '';
            
            $('#modal-personal').modal('hide'); // Cerrar modal después de seleccionar
        } else {
            console.error('No se pudieron obtener los datos del personal seleccionado.');
            message('error', 'Error al seleccionar el personal. Intente nuevamente.');
        }
    });

    // Inicializar el botón de guardar
    btn_save = document.querySelector('#btn-guardar'); 

    if (btn_save) {
        btn_save.onclick = function () {
            if (!id_personal.value || !metodo.value) {
                message('error', 'Debe seleccionar un personal y un método de pago');
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
<<<<<<< HEAD
});

// Reiniciar formularios después de guardar la venta
=======

    table_clientes = $('#table_clientes').DataTable({
        ajax: {
            url: ruta + 'controllers/ventasController.php?option=listar-clientes',
            dataSrc: ''
        },
        columns: [
            {
                data: null, render: function (data, type, row) {
                    return data.nombres + ' ' + data.apellidos;
                }
            },
            { data: 'id_cliente' },
            { data: 'area' },
            { data: 'capacidad' } // Mostrar capacidad directamente desde la tabla cf_cliente
        ],
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.1/i18n/es-ES.json'
        },
        createdRow: function (row, data, dataIndex) {
            // Verificar si la capacidad es 0 y agregar una clase a la fila
            if (data.capacidad == 0) {
                $(row).addClass('table-danger');
            }
        }
    });

    $('#table_clientes tbody').on('dblclick', 'tr', function () {
        let datos = table_clientes.row(this).data();
        id_cliente.value = datos.id_cliente;
        nombre_cliente.value = datos.nombres + ' ' + datos.apellidos;
        area_cliente.value = datos.area;
        capacidad_cliente.value = datos.capacidad;
        $('#modal-cliente').modal('hide');
    });

    search.onkeyup = function (e) {
        if (e.key === "Enter") {
            if (e.target.value == '') {
                message('error', 'INGRESE CÓDIGO DE BARRAS');
            } else {
                axios.get(ruta + 'controllers/ventasController.php?option=searchbarcode&barcode=' + e.target.value)
                    .then(function (response) {
                        const info = response.data;
                        search.value = '';
                        message(info.tipo, info.mensaje);
                        if (info.tipo === 'success') {
                            const producto = info.producto;
                            addCart(producto.id_producto);
                        }
                        updateStock();
                        temp(); // Actualiza la tabla temporal y el total
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            }
        }
    };

    btn_save.onclick = function () {
        datos();

        // if (id_cliente && metodo === "Credito") {
        //     biometric();
        // } else {
        //     console.error(Error);
        // }

        axios.post(ruta + 'controllers/ventasController.php?option=saveventa', {
            idCliente: id_cliente.value,
            metodo: metodo.value

<<<<<<< HEAD
=======
                // Reinicia los formularios
                resetFormularios();
            }
>>>>>>> 2b805a3ebe61d2e689b0c73d184ebad9a5bf5fa5
        })
        // .then(function (response) {
        //     const info = response.data;
        //     message(info.tipo, info.mensaje);
        //     if (info.tipo === 'success') {
        //         updateStock();
        //         temp(); // Actualiza la tabla temporal y el total

        //         // Reiniciar los formularios
        //         resetFormularios();
        //     }
        // })
        // .catch(function (error) {
        //     console.log(error);
        // });

    };
});

//Funcion para ocultar elementos
function hideElement(elementId) {
    $('#' + elementId).hide();
}

//Funcion para mostrar elementos
function showElement(elementId) {
    $('#' + elementId).show();
}

function canva(video, canvas) {
    let context = canvas.getContext('2d');
    context.drawImage(video, 0, 0, canvas.width, canvas.height);
    return canvas.toDataURL('image/png');
}

//funcion para simular biometrico
function biometric() {

    const creditModal = new bootstrap.Modal(document.getElementById('sales-modal'));
    creditModal.show();

    showElement('biometricc');
    showElement('cancel');
    showElement('bio');
    hideElement('can');
    hideElement('refresh');
    hideElement('accept');

    let video = document.getElementById('bio');
    let stream;
    navigator.mediaDevices.getUserMedia({ video: true })
        .then(function (mediaStream) {
            stream = mediaStream
            video.srcObject = mediaStream;
            video.play();
        })
        .catch(function (err) {
            console.error('Error al activar biometrico: ', err);
        });
    let canvasVideo = document.getElementById('can');

    $("#biometricc").on('click', function (e) {
        e.preventDefault();

        let imageDataURL = canva(video, canvasVideo);
        //idBio es donde se almacena la foto tomada este es un input type hidden,bio es la etiqueta video,
        hideElement('bio');
        hideElement('biometricc')
        showElement('idBio');
        showElement('can');
        showElement('cancel');
        showElement('refresh');
        showElement('accept');

        $('#idBio').val(imageDataURL);

        if (stream) {
            let tracks = stream.getTracks();
            tracks.forEach(track => track.stop());
        }
    })

    $("#refresh").on('click', function (e) {
        e.preventDefault();
        let d = document.getElementById("can");
        let lienzo = d.getContext("2d");
        function dibujar(color, x_incial, y_incial, x_final, y_final) {
            lienzo.beginPath();
            lienzo.strokeStyle = color;
            lienzo.moveTo(x_incial, y_incial);
            lienzo.lineTo(x_final, y_final);
            lienzo.stroke();
            lienzo.closePath();
        }

        function limpiarCanvas() {
            lienzo.clearRect(0, 0, d.width, d.height);
            dibujar("black", 1, 0, 0, 300);
            dibujar("black", 1, 300, 300, 300);
        }

        let id;
        function limpiarHidden() {
            id = document.getElementById("idBio").value = "";
        }

        limpiarHidden(id);
        limpiarCanvas();

        showElement('bio');
        showElement('biometricc')
        showElement('idBio');
        hideElement('can');
        showElement('cancel');
        hideElement('refresh');
        hideElement('accept');

        let video = document.getElementById('bio');
        let stream;
        const metodo = document.getElementById('metodo');

        // Obtén el valor del select
        const metodoSeleccionado = metodo.value;

        // Verifica si el valor seleccionado es "Credito"
        if (metodoSeleccionado === 'Credito') {
            navigator.mediaDevices.getUserMedia({ video: true })
                .then(function (mediaStream) {
                    stream = mediaStream
                    video.srcObject = mediaStream;
                    video.play();
                })
                .catch(function (err) {
                    console.error('Error al activar biometrico: ', err);
                });
        }
    })

    $("#accept").on("click", function (e) {
        e.preventDefault();
        axios.post(ruta + 'controllers/ventasController.php?option=saveventa', {
            idCliente: id_cliente.value,
            metodo: metodo.value,
            idBio: idBio.value
        })
            .then(function (response) {
                const info = response.data;
                message(info.tipo, info.mensaje);
                if (info.tipo === 'success') {
                    updateStock();
                    temp(); // Actualiza la tabla temporal y el total

                    // Reiniciar los formularios
                    resetFormularios();
                }
                window.location.reload();

            })
            .catch(function (error) {
                console.log(error);
            });
    });

}

//Funcionalidad para quemar los datos ingresados por el usuario en una 
function datos() {
    //tabla de donde se va a extraer los datos de la venta
    let tabla = document.querySelector("#table_temp");
    let productos = [];//array en donde se van a almacenar los productos que desee comprar el cliente
    let rows = tabla.querySelectorAll("tr");//se seleccionaran todas las filas de la tabla
    let cantidad = document.querySelector("#cantidad");

    //recorre los elementos que se encuentran en la variable rows
    rows.forEach((row) => {
        let columns = row.querySelectorAll("td");//se seleccionan todas las celdas
        //se asegura que se tenga mas de dos celdas en la fila de la tabla
        if (columns.length >= 2) {
            let producto = columns[0].textContent.trim();//se obtiene los datos de la primera celda
            let precio = document.querySelector("#precio");

            // let cantidadProducto = cantidad ? cantidad.value.trim() : "0";//trim me sirve para eliminar espacios en blanco al principio y al final del texto
            productos.push(producto + '- Precio:' + precio.value + '- Cantidad:' + cantidad.value); //se añade a una cadena los datos que tendra el array
        }
    });

    //array para almacenar errores
    let errores = [];

    let Max = 100;

    //se validan si los campos estan llenos de no ser así me bota un mensaje
    if (!nombre_cliente.value) errores.push("Nombre del cliente");//push agrega elementos a un array
    if (!productos || productos.length === 0) errores.push("Productos");
    if (!metodo.value) errores.push("Método de pago");
    if (cantidad && cantidad.value > Max) errores.push("La maxima cantidad que puede agregar al carrito es de " + Max);
    if (cantidad && cantidad.value < 1) errores.push("La minima cantidad que puede agregar al carrito es de 1");

    //valida si existen errores de no ser asi ejecuta el mensaje succes, 
    //si hay errores me va a salir un mensaje de error indicandome cual es el campo que me falta
    if (errores.length === 0) {
        message('success', "Nombre:" + " " + nombre_cliente.value + "\n" + "Productos:" + "\n" + productos.join("\n")
            + "\n" + "Metodo de pago: " + " " + metodo.value);

        // updateStock();

        // temp(); // Actualiza la tabla temporal y el total

        // // Reiniciar los formularios
        resetFormularios();
        // window.location.reload();

    } else {

        message('error', 'ERROR: ' + ' ' + errores.join(', '));
        return;
    }
}


>>>>>>> eae77a48f863dceaf7a9bfa932cd8e5b7113d14f
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
<<<<<<< HEAD
                    <td><input class="form-control" type="number" value="${precio}" onchange="addPrecio(event, ${pro.id_producto})" /></td>
                    <td><input class="form-control" type="number" value="${cantidad}" onchange="addCantidad(event, ${pro.id_producto})" /></td>
=======
                    <td><input class="form-control" id="precio" type="number" value="${pro.precio_venta}" readonly onchange="addPrecio(event, ${pro.id_producto})" /></td>
                    <td><input class="form-control" id="cantidad" type="number" value="${pro.cantidad}" MIN="1" MAX="100" onchange="addCantidad(event, ${pro.id_producto})" /></td>
>>>>>>> eae77a48f863dceaf7a9bfa932cd8e5b7113d14f
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
<<<<<<< HEAD
    .then(() => {
        updateStock();
        temp();
    });
=======
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
>>>>>>> eae77a48f863dceaf7a9bfa932cd8e5b7113d14f
}

function addPrecio(e, idTemp) {
    axios.post(ruta + 'controllers/ventasController.php?option=addprecio', {
        id: idTemp,
        precio: e.target.value
    })
<<<<<<< HEAD
    .then(() => {
        updateStock();
        temp();
    });
=======
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
>>>>>>> eae77a48f863dceaf7a9bfa932cd8e5b7113d14f
}

function deleteProducto(idTemp) {
    axios.get(ruta + 'controllers/ventasController.php?option=delete&id=' + idTemp)
<<<<<<< HEAD
    .then(() => {
        updateStock();
        temp();
    });
=======
        .then(function (response) {
            const info = response.data;
            message(info.tipo, info.mensaje);
            updateStock();
            temp(); // Actualiza la tabla temporal y el total
        })
        .catch(function (error) {
            console.log(error);
        });
>>>>>>> eae77a48f863dceaf7a9bfa932cd8e5b7113d14f
}

function updateStock() {
    $('#table_venta').DataTable().ajax.reload();
}
