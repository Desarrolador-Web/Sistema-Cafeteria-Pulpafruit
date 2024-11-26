document.addEventListener('DOMContentLoaded', function () {
  $('#table_clientes').DataTable({
      ajax: {
          url: ruta + 'controllers/clientesController.php?option=listar',
          dataSrc: ''
      },
      columns: [
          { data: 'id_cliente' },
          { data: 'nombres' },
          { data: 'apellidos'},
          { data: 'area' },
          { data: 'sueldo' }
      ],
      language: {
          url: 'https://cdn.datatables.net/plug-ins/1.13.1/i18n/es-ES.json'
      },
      "order": [[0, 'desc']]
  });
});
