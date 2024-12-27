// Al cargar el DOM, inicializamos las funciones
document.addEventListener('DOMContentLoaded', function () {
    console.log('Rol del usuario:', rolUsuario);
    console.log('Caja abierta:', cajaAbierta);
 
    // Validación para mostrar el modal solo si el rol es 3 y cajaAbierta es false
    if (parseInt(rolUsuario) === 3) {
        fetch(`${ruta}controllers/adminController.php?option=verificarCaja`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error al verificar el estado de la caja');
                }
                return response.json();
            })
            .then(data => {
                console.log('Respuesta de verificación de caja:', data);
                if (data.cajaAbierta === false) {
                    console.log('Rol 3 detectado y caja no abierta. Mostrando modal de apertura de caja.');
                    mostrarModalAbrirCaja();
                } else {
                    console.log('Rol 3 detectado y caja abierta. No se mostrará el modal.');
                }
            })
            .catch(error => {
                console.error('Error al verificar caja:', error);
            });
    }
 
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

        // Obtener el id_info_caja desde el servidor antes de continuar
        fetch(ruta + 'controllers/adminController.php?option=obtenerIdCajaAbierta', {
            method: 'GET'
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor al obtener id_info_caja');
            }
            return response.json();
        })
        .then(data => {
            if (data.id_info_caja) {
                const idInfoCaja = data.id_info_caja;

                const formData = new FormData();
                formData.append('valorCierre', valorCierre);
                formData.append('id_info_caja', idInfoCaja); // Agregar id_info_caja al formData

                fetch(ruta + 'controllers/adminController.php?option=cerrarCaja', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error en la respuesta del servidor');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.resultado !== undefined) {
                        const resultadoConsultaNumerico = parseFloat(data.resultado);

                        if (Number(valorCierreNumerico) !== Number(resultadoConsultaNumerico)) {
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
                                    formDataObservacion.append('id_info_caja', idInfoCaja);

                                    // Guardar observación en la base de datos y enviar el código de autorización
                                    fetch(ruta + 'controllers/adminController.php?option=guardarObservacion', {
                                        method: 'POST',
                                        body: formDataObservacion
                                    })
                                    .then(response => {
                                        if (!response.ok) {
                                            throw new Error('Error en la respuesta del servidor');
                                        }
                                        return response.json();
                                    })
                                    .then(data => {
                                        if (data.tipo === 'success') {
                                            // Enviar el código de autorización cada vez que se presiona "Agregar código"
                                            enviarCodigoAutorizacion(idInfoCaja);
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
                        } else {
                            // Cerrar caja exitosamente si los valores coinciden
                            Swal.fire({
                                title: 'Caja cerrada exitosamente',
                                icon: 'success',
                                confirmButtonText: 'Aceptar'
                            }).then(() => {
                                window.location.reload();
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
            } else {
                console.error('No se recibió un id_info_caja válido');
                Swal.fire({
                    title: 'Error',
                    text: 'No se pudo obtener el identificador de caja.',
                    icon: 'error',
                    confirmButtonText: 'Aceptar'
                });
            }
        })
        .catch(error => {
            console.error('Error al obtener id_info_caja:', error);
            Swal.fire({
                title: 'Error',
                text: 'Error al obtener el identificador de caja: ' + error,
                icon: 'error',
                confirmButtonText: 'Aceptar'
            });
        });
    });
}

// Función para enviar el código de autorización
function enviarCodigoAutorizacion(idInfoCaja) {
    fetch(ruta + 'controllers/adminController.php?option=enviarCodigoAutorizacion', {
        method: 'POST',
        body: new URLSearchParams({ 'id_info_caja': idInfoCaja })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Error en la respuesta del servidor al enviar código');
        }
        return response.json();
    })
    .then(data => {
        if (data.tipo === 'success') {
            Swal.fire({
                title: 'Código enviado exitosamente',
                text: data.mensaje,
                icon: 'success',
                confirmButtonText: 'Aceptar'
            }).then(() => {
                // Solicitar el código de autorización al usuario después de enviarlo
                Swal.fire({
                    title: 'Ingresa el código de autorización',
                    input: 'text',
                    inputPlaceholder: 'Escribe el código enviado a tu correo',
                    showCancelButton: true,
                    confirmButtonText: 'Validar código',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed && result.value) {
                        validarCodigoAutorizacion(idInfoCaja, result.value);
                    }
                });
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
}

// Función para validar el código de autorización y cerrar la caja si es correcto
function validarCodigoAutorizacion(idInfoCaja, codigoAutorizacion) {
    const valorCierre = document.querySelector('#valorCierre').value; // Obtén el valor de cierre

    const formDataValidacion = new FormData();
    formDataValidacion.append('id_info_caja', idInfoCaja);
    formDataValidacion.append('codigoAutorizacion', codigoAutorizacion);
    formDataValidacion.append('valorCierre', valorCierre); // Agrega el valor de cierre

    fetch(ruta + 'controllers/adminController.php?option=validarCodigoAutorizacion', {
        method: 'POST',
        body: formDataValidacion
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Error en la respuesta del servidor al validar código');
        }
        return response.json();
    })
    .then(data => {
        if (data.tipo === 'success') {
            Swal.fire({
                title: 'Caja cerrada exitosamente',
                icon: 'success',
                confirmButtonText: 'Aceptar'
            }).then(() => {
                window.location.reload();
            });
        } else {
            Swal.fire({
                title: 'Código incorrecto',
                text: data.mensaje,
                icon: 'error',
                confirmButtonText: 'Intentar nuevamente'
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
}
