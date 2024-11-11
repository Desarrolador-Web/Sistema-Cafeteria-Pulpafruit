document.addEventListener('DOMContentLoaded', function () {
    // Inicializar las funciones al cargar el DOM
    verificarCajaAbierta();
    manejarAperturaCaja();
    manejarCierreCaja();
});

// Función para verificar si el usuario tiene una caja abierta hoy
function verificarCajaAbierta() {
    fetch(ruta + 'controllers/adminController.php?option=verificarCaja')
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor');
            }
            return response.json();
        })
        .then(data => {
            if (!data.cajaAbierta) {
                mostrarModalAbrirCaja();
            }
        })
        .catch(error => {
            console.error('Error al verificar la caja:', error);
        });
}


// Función para mostrar el modal de apertura de caja
function mostrarModalAbrirCaja() {
    $('#modalAbrirCaja').modal({
        backdrop: 'static',  // Evita cerrar el modal al hacer clic fuera
        keyboard: false      // Evita cerrar el modal con la tecla Esc
    });
    $('#modalAbrirCaja').modal('show');
}

// Función para manejar la apertura de caja
function manejarAperturaCaja() {
    document.querySelector('#formAperturaCaja').addEventListener('submit', function (e) {
        e.preventDefault();

        const valorApertura = document.querySelector('#valorApertura').value;
        const sede = document.querySelector('#sede').value;

        const formData = new FormData();
        formData.append('valorApertura', valorApertura);
        formData.append('id_sede', sede);

        fetch(ruta + 'controllers/adminController.php?option=abrirCaja', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.tipo === 'success') {
                $('#modalAbrirCaja').modal('hide');
                Swal.fire({
                    title: '¡Caja abierta!',
                    text: data.mensaje,
                    icon: 'success',
                    confirmButtonText: 'Aceptar'
                });
            } else {
                Swal.fire({
                    title: 'Error',
                    text: 'Error: ' + data.mensaje,
                    icon: 'error',
                    confirmButtonText: 'Aceptar'
                });
            }
        })
        .catch(error => {
            console.error('Error en la solicitud:', error);
            Swal.fire({
                title: 'Error',
                text: 'Error en la solicitud: ' + error,
                icon: 'error',
                confirmButtonText: 'Aceptar'
            });
        });
    });
}

// Función para manejar el cierre de caja
function manejarCierreCaja() {
    document.querySelector('#formCerrarCaja').addEventListener('submit', function (e) {
        e.preventDefault();

        const valorCierre = document.querySelector('#valorCierre').value;
        const valorCierreNumerico = parseInt(valorCierre);

        const formData = new FormData();
        formData.append('valorCierre', valorCierre);

        fetch(ruta + 'controllers/adminController.php?option=cerrarCaja', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.resultado !== undefined) {
                const resultadoConsultaNumerico = parseFloat(data.resultado);

                if (Number(valorCierreNumerico) === Number(resultadoConsultaNumerico)) {
                    Swal.fire({
                        title: 'Caja cerrada exitosamente',
                        icon: 'success',
                        confirmButtonText: 'Aceptar'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Valores no coinciden',
                        text: `El valor calculado es ${data.resultado}. Por favor ingresa una observación.`,
                        input: 'textarea',
                        inputPlaceholder: 'Escribe tu observación aquí...',
                        showCancelButton: true,
                        confirmButtonText: 'Agregar código',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed && result.value) {
                            const observacion = result.value;

                            // Crear FormData para enviar la observación y el id_info_caja
                            const formDataObservacion = new FormData();
                            formDataObservacion.append('observacion', observacion);
                            formDataObservacion.append('id_info_caja', idInfoCaja); // Asegúrate de definir `idInfoCaja`

                            // Guardar observación en la base de datos
                            fetch(ruta + 'controllers/adminController.php?option=guardarObservacion', {
                                method: 'POST',
                                body: formDataObservacion
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.tipo === 'success') {
                                    // Observación guardada, ahora solicitar el código de autorización
                                    fetch(ruta + 'controllers/adminController.php?option=enviarCodigoAutorizacion', {
                                        method: 'POST'
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.tipo === 'success') {
                                            Swal.fire({
                                                title: 'Código enviado exitosamente',
                                                text: data.mensaje,
                                                icon: 'success',
                                                confirmButtonText: 'Aceptar'
                                            });
                                        } else {
                                            Swal.fire({
                                                title: 'Error al enviar código',
                                                text: data.mensaje,
                                                icon: 'error',
                                                confirmButtonText: 'Aceptar'
                                            });
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Error en la solicitud:', error);
                                        Swal.fire({
                                            title: 'Error',
                                            text: 'Error en la solicitud: ' + error,
                                            icon: 'error',
                                            confirmButtonText: 'Aceptar'
                                        });
                                    });
                                } else {
                                    Swal.fire({
                                        title: 'Error al guardar observación',
                                        text: data.mensaje,
                                        icon: 'error',
                                        confirmButtonText: 'Aceptar'
                                    });
                                }
                            })
                            .catch(error => {
                                console.error('Error al guardar la observación:', error);
                                Swal.fire({
                                    title: 'Error',
                                    text: 'Error al guardar la observación: ' + error,
                                    icon: 'error',
                                    confirmButtonText: 'Aceptar'
                                });
                            });
                        }
                    });
                }
            } else {
                Swal.fire({
                    title: 'Error',
                    text: 'Error: El valor de la consulta no se ha recibido correctamente.',
                    icon: 'error',
                    confirmButtonText: 'Aceptar'
                });
            }
        })
        .catch(error => {
            console.error('Error en la solicitud:', error);
            Swal.fire({
                title: 'Error',
                text: 'Error en la solicitud: ' + error,
                icon: 'error',
                confirmButtonText: 'Aceptar'
            });
        });
    });
}
