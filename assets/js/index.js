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
        const valorCierreNumerico = parseFloat(valorCierre);

        console.log('Valor de cierre capturado:', valorCierre); 

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

                console.log('ID de caja abierta:', idInfoCaja); 

                const formData = new FormData();
                formData.append('id_info_caja', idInfoCaja);
                formData.append('valorCierre', valorCierre);

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
                    console.log('Respuesta del servidor:', data); 
                    if (data.tipo === 'success') {
                        Swal.fire({
                            title: 'Caja cerrada exitosamente',
                            text: data.mensaje,
                            icon: 'success',
                            confirmButtonText: 'Aceptar'
                        }).then(() => {
                            window.location.reload();
                        });
                    } else if (data.tipo === 'error' && data.resultado !== undefined) {
                        // Mostrar SweetAlert para ingresar observación
                        Swal.fire({
                            title: 'Valores no coinciden',
                            text: 'Los valores no coinciden. Por favor, ingresa una observación.',
                            input: 'text',
                            inputPlaceholder: 'Escribe tu observación aquí',
                            showCancelButton: true,
                            confirmButtonText: 'Guardar observación',
                            cancelButtonText: 'Cancelar'
                        }).then((result) => {
                            if (result.isConfirmed && result.value) {
                                cerrarCajaConObservacion(idInfoCaja, valorCierre, result.value);
                            }
                        });
                    } else {
                        Swal.fire({
                            title: 'Error al cerrar caja',
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


function cerrarCajaConObservacion(idInfoCaja, valorCierre, observacion) {
    const formDataObservacion = new FormData();
    formDataObservacion.append('id_info_caja', idInfoCaja);
    formDataObservacion.append('valorCierre', valorCierre);
    formDataObservacion.append('observacion', observacion);

    fetch(ruta + 'controllers/adminController.php?option=cerrarCaja', {
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
            Swal.fire({
                title: 'Caja cerrada exitosamente',
                icon: 'success',
                confirmButtonText: 'Aceptar'
            }).then(() => {
                window.location.reload();
            });
        } else {
            Swal.fire({
                title: 'Error al cerrar caja',
                text: data.mensaje,
                icon: 'error',
                confirmButtonText: 'Aceptar'
            });
        }
    })
    .catch(error => {
        console.error('Error al cerrar caja:', error);
        Swal.fire({
            title: 'Error',
            text: 'Error al cerrar caja: ' + error,
            icon: 'error',
            confirmButtonText: 'Aceptar'
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