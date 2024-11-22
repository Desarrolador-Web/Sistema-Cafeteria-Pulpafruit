let table_temp = document.querySelector('#table_temp tbody');
const totalVenta = document.querySelector('#total-venta');
const nombre_cliente = document.querySelector('#nombre-cliente');
const area_cliente = document.querySelector('#area-cliente');
const id_cliente = document.querySelector('#id-cliente');
const capacidad_cliente = document.querySelector('#capacidad-cliente');
const search = document.querySelector('#search');
const idBio = document.querySelector('#idBio');
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
            { data: 'cantidad' },
            { data: 'precio_venta' },
            { data: 'addcart' }
        ],
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.1/i18n/es-ES.json'
        }
    });

    temp(); // Inicializa la tabla temporal y el total

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
        // Selecciona los elementos necesarios
        biometric();
        const metodo = document.getElementById('metodo');

        // Obtén el valor del select
        const metodoSeleccionado = metodo.value;

        // Verifica si el valor seleccionado es "Credito"
        if (metodoSeleccionado === 'Credito') {
            // Abre el modal si es "Crédito"
            const creditModal = new bootstrap.Modal(document.getElementById('sales-modal'));
            creditModal.show();
            return; // Detén la ejecución aquí para no continuar con el flujo normal
        }

        // Si no es "Crédito", ejecuta el flujo normal
        console.log("Método seleccionado no es crédito. Continuando con el flujo normal...");
        realizarAccionNormal(metodoSeleccionado);

        // Simula una función para las otras opciones
        function realizarAccionNormal(metodo) {
            console.log(`Realizando acción para el método: ${metodo}`);
            // Aquí puedes agregar el código que quieras para el flujo normal
        }

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

                    // Reiniciar los formularios
                    resetFormularios();
                }
            })
            .catch(function (error) {
                console.log(error);
            });
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
let video = document.getElementById('bio');
let stream;
const metodo = document.getElementById('metodo');

// Obtén el valor del select
const metodoSeleccionado = metodo.value;

//funcion para simular biometrico
function biometric() {
    credito();
    showElement('biometricc');
    showElement('cancel');
    showElement('bio');
    hideElement('can');
    hideElement('refresh');
    hideElement('accept');

    // Verifica si el valor seleccionado es "Credito"
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

        return; // Detén la ejecución aquí para no continuar con el flujo normal
    }
}

function credito() {
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
        // $('#sales-modal').modal({backdrop: 'static', keyboard: false});
    });

}

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
