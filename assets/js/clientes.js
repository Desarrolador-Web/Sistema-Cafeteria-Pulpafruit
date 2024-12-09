document.addEventListener('DOMContentLoaded', function () {
    cargarTablaClientes();
});

// Función para inicializar DataTable y cargar datos
function cargarTablaClientes() {
    axios.get(ruta + 'controllers/clientesController.php?option=listar')
        .then(function (response) {
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
        .catch(function (error) {
            console.error('Error:', error);
            alert('Hubo un error al conectar con el servidor.');
        });
}
