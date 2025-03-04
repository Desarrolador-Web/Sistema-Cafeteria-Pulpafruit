class Clientes {
    // Método para mostrar el modal de "Inicios de Sesión"
    mostrarIniciosSesion() {
        axios.get('http://localhost/Sistema-Cafeteria-Pulpafruit/controllers/clientesController.php?option=iniciosSesion')
            .then((response) => {
                if (response.data.tipo === 'success') {
                    const datos = response.data.data;
                    const contenidoTabla = datos.map(fila => `
                        <tr>
                            <td>${fila.nombre_completo}</td>
                            <td>${fila.nombre_sede}</td>
                            <td>${fila.fecha_apertura}</td>
                            <td>${fila.hora_apertura}</td>
                            <td>${fila.fecha_cierre}</td>
                            <td>${fila.hora_cierre}</td>
                        </tr>
                    `).join('');

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
                    const contenidoTabla = response.data.data.map(usuario => `
                        <tr>
                            <td>${usuario.nombre_completo}</td>
                            <td>${usuario.rol}</td>
                        </tr>
                    `).join('');

                    document.getElementById('tablaUsuariosBody').innerHTML = contenidoTabla;
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

    // Método para mostrar el modal de "Productos Agotados"
    mostrarProductosAgotados() {
        axios.get('http://localhost/Sistema-Cafeteria-Pulpafruit/controllers/clientesController.php?option=productosAgotados')
            .then((response) => {
                if (response.data.tipo === 'success') {
                    const contenidoTabla = response.data.data.map(producto => `
                        <tr>
                            <td>${producto.producto}</td>
                            <td>${producto.ultima_fecha_compra || 'N/A'}</td>
                            <td>${producto.ultima_fecha_venta || 'N/A'}</td>
                        </tr>
                    `).join('');

                    document.getElementById('tablaProductosAgotadosBody').innerHTML = contenidoTabla;
                    new bootstrap.Modal(document.getElementById('modalProductosAgotados')).show();
                } else {
                    alert('Error al cargar los datos: ' + response.data.mensaje);
                }
            })
            .catch((error) => {
                console.error('Error:', error);
                alert('Hubo un error al conectar con el servidor.');
            });
    }

    // Método auxiliar para los textos de DataTable
    getDataTableLanguage() {
        return {
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
        };
    }
}

// Inicialización de cliente y eventos
document.addEventListener('DOMContentLoaded', function () {
    const cliente = new Clientes();

    // Inicializar tabla y cargar datos
    cliente.inicializarTablaClientes();

    // Eventos para los botones
    document.getElementById('metric-inicios-sesion').addEventListener('click', () => cliente.mostrarIniciosSesion());
    document.getElementById('metric-usuarios-registrados').addEventListener('click', () => cliente.mostrarUsuariosRegistrados());
    document.getElementById('metric-productos-agotados').addEventListener('click', () => cliente.mostrarProductosAgotados());
});
