document.addEventListener('DOMContentLoaded', function () {
    // Inicializar las funciones al cargar el DOM
    verificarCajaAbierta();
    manejarAperturaCaja();
    manejarCierreCaja();
});

// Función para verificar si el usuario tiene una caja abierta hoy
function verificarCajaAbierta() {
    fetch(ruta + 'controllers/adminController.php?option=verificarCaja')
        .then(response => response.json())
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

        // Convertir ambos valores a números con parseFloat
        const valorCierreNumerico = parseFloat(valorCierre);

        const formData = new FormData();
        formData.append('valorCierre', valorCierre);

        fetch(ruta + 'controllers/adminController.php?option=cerrarCaja', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            // Verificar si el valor de 'resultado' está definido en el JSON
            if (data.resultado !== undefined) {
                // Convertir el valor del resultado de la consulta a número
                const resultadoConsultaNumerico = parseFloat(data.resultado);

                // Comparar los valores numéricos para evitar problemas de tipo de datos
                if (valorCierreNumerico === resultadoConsultaNumerico) {
                    // Los valores coinciden, cerrar caja exitosamente
                    Swal.fire({
                        title: 'Caja cerrada exitosamente',
                        icon: 'success',
                        confirmButtonText: 'Aceptar'
                    }).then(() => {
                        window.location.reload(); // O cualquier otra acción después de cerrar la caja
                    });
                } else {
                    // Los valores no coinciden, mostrar el valor de la consulta y pedir observación
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
                            Swal.fire({
                                title: 'Ingrese un código',
                                input: 'number',
                                inputPlaceholder: 'Código numérico',
                                showCancelButton: true,
                                confirmButtonText: 'Validar código',
                                cancelButtonText: 'Cancelar'
                            }).then((codigoResult) => {
                                if (codigoResult.isConfirmed && codigoResult.value) {
                                    const observacion = result.value;
                                    const codigo = codigoResult.value;

                                    formData.append('observacion', observacion);
                                    formData.append('codigo', codigo);

                                    // Enviar los datos para cerrar la caja con la observación y código
                                    fetch(ruta + 'controllers/adminController.php?option=cerrarCaja', {
                                        method: 'POST',
                                        body: formData
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.tipo === 'success') {
                                            Swal.fire({
                                                title: 'Caja cerrada con observación',
                                                icon: 'success',
                                                confirmButtonText: 'Aceptar'
                                            }).then(() => {
                                                window.location.reload(); // O cualquier otra acción después de cerrar la caja
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
                                }
                            });
                        }
                    });
                }
            } else {
                // Si data.resultado no está definido, mostrar error
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
