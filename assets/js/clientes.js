// Declaración de variables y constantes
let cliente;

// Definición de la clase Clientes
class Clientes {
    // Método para inicializar DataTable con datos iniciales
    inicializarTablaClientes() {
        $('#table_clientes').DataTable({
            ajax: {
                url: 'http://localhost/Sistema-Cafeteria-Pulpafruit/controllers/clientesController.php?option=listar',
                dataSrc: ''
            },
            columns: [
                { data: 'id_cliente' },
                { data: 'nombres' },
                { data: 'apellidos' },
                { data: 'area' },
                { data: 'sueldo' }
            ],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.1/i18n/es-ES.json'
            },
            order: [[0, 'desc']]
        });
    }

    // Método para cargar datos y reinicializar DataTable
    cargarTablaClientes() {
        axios.get('http://localhost/Sistema-Cafeteria-Pulpafruit/controllers/clientesController.php?option=listar')
            .then((response) => {
                const datos = response.data;
                if (datos.tipo === 'success') {
                    $('#tablaClientes').DataTable({
                        destroy: true, // Permite reinicializar la tabla
                        data: datos.data,
                        columns: [
                            { data: 'nombre_completo', title: 'Nombre Completo' },
                            { data: 'nombre_sede', title: 'Sede' },
                            { data: 'fecha_apertura', title: 'Fecha Apertura' },
                            {
                                data: 'fecha_cierre',
                                render: function (data) {
                                    return data ? data : 'Sesión en curso';
                                },
                                title: 'Fecha Cierre'
                            },
                            {
                                data: 'valor_cierre',
                                render: function (data) {
                                    return data ? data : 'Sesión en curso';
                                },
                                title: 'Valor Cierre'
                            }
                        ],
                        language: {
                            sProcessing: "Procesando...",
                            sLengthMenu: "Mostrar _MENU_ registros",
                            sZeroRecords: "No se encontraron resultados",
                            sEmptyTable: "Ningún dato disponible en esta tabla",
                            sInfo: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                            sInfoEmpty: "Mostrando registros del 0 al 0 de un total de 0 registros",
                            sInfoFiltered: "(filtrado de un total de _MAX_ registros)",
                            sSearch: "Buscar:",
                            oPaginate: {
                                sFirst: "Primero",
                                sLast: "Último",
                                sNext: "Siguiente",
                                sPrevious: "Anterior"
                            }
                        }
                    });
                } else {
                    console.error('Error al cargar datos:', datos.mensaje);
                    alert('Hubo un error al cargar los datos.');
                }
            })
            .catch((error) => {
                console.error('Error:', error);
                alert('Hubo un error al conectar con el servidor.');
            });
    }

    // Método para mostrar el modal de "Inicios de Sesión"
    mostrarIniciosSesion() {
        axios.get('http://localhost/Sistema-Cafeteria-Pulpafruit/controllers/clientesController.php?option=iniciosSesion')
            .then((response) => {
                const datos = response.data.data;

                if (response.data.tipo === 'success') {
                    let contenidoTabla = '';
                    datos.forEach((fila) => {
                        contenidoTabla += `
                            <tr>
                                <td>${fila.nombre_completo}</td>
                                <td>${fila.nombre_sede}</td>
                                <td>${fila.fecha_apertura}</td>
                                <td>${fila.hora_apertura}</td>
                                <td>${fila.fecha_cierre}</td>
                                <td>${fila.hora_cierre}</td>
                            </tr>
                        `;
                    });

                    document.getElementById('tablaIniciosSesionBody').innerHTML = contenidoTabla;
                    new bootstrap.Modal(document.getElementById('modalIniciosSesion')).show();
                } else {
                    alert('Error al cargar los datos: ' + response.data.mensaje);
                }
            })
            .catch((error) => {
                console.error('Error:', error);
                alert('Hubo un error al conectar con el servidor.');
            });
    }

    // Método para mostrar el modal de "Usuarios Registrados"
    mostrarUsuariosRegistrados() {
        axios.get('http://localhost/Sistema-Cafeteria-Pulpafruit/controllers/clientesController.php?option=usuariosRegistrados')
            .then((response) => {
                if (response.data.tipo === 'success') {
                    let contenidoTabla = '';
                    response.data.data.forEach((usuario) => {
                        contenidoTabla += `
                            <tr>
                                <td>${usuario.nombre_completo}</td>
                                <td>${usuario.rol}</td>
                            </tr>
                        `;
                    });

                    // Insertar los datos en la tabla
                    document.getElementById('tablaUsuariosBody').innerHTML = contenidoTabla;

                    // Mostrar el modal
                    new bootstrap.Modal(document.getElementById('modalUsuariosRegistrados')).show();
                } else {
                    alert('Error al cargar los datos: ' + response.data.mensaje);
                }
            })
            .catch((error) => {
                console.error('Error:', error);
                alert('Hubo un error al conectar con el servidor.');
            });
    }
}


// Inicialización de cliente y eventos
document.addEventListener('DOMContentLoaded', function () {
    // Crear la instancia de Clientes
    cliente = new Clientes();

    // Inicializar tabla y cargar datos
    cliente.inicializarTablaClientes();
    cliente.cargarTablaClientes();

    // Agregar evento para abrir el modal de inicios de sesión
    document.getElementById('metric-inicios-sesion').addEventListener('click', function () {
        cliente.mostrarIniciosSesion(); // Solo abre el modal de Inicios de Sesión
    });

    // Agregar evento para abrir el modal de usuarios registrados
    document.getElementById('metric-usuarios-registrados').addEventListener('click', function () {
        cliente.mostrarUsuariosRegistrados(); // Solo abre el modal de Usuarios Registrados
    });

    // Vincular eventos de cierre manual al modal
    document.getElementById('closeModal')?.addEventListener('click', () => {
        const modal = bootstrap.Modal.getInstance(document.getElementById('modalIniciosSesion'));
        modal.hide();
    });

    document.getElementById('closeModalFooter')?.addEventListener('click', () => {
        const modal = bootstrap.Modal.getInstance(document.getElementById('modalIniciosSesion'));
        modal.hide();
    });
});

