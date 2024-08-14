let minDate, maxDate, table;
document.addEventListener('DOMContentLoaded', function () {
  table = $('#table_compras').DataTable({
    ajax: {
      url: ruta + 'controllers/historialComprasController.php?option=historial',
      dataSrc: ''
    },
    columns: [
      { data: 'id' },
      { data: 'nombre' },
      { data: 'producto' },
      { data: 'total' },
      { data: 'fecha' }
    ],
    language: {
      url: 'https://cdn.datatables.net/plug-ins/1.13.1/i18n/es-ES.json'
    }
  });

  minDate = new DateTime($('#desde'), {
    format: 'YYYY-MM-DD'
  });
  maxDate = new DateTime($('#hasta'), {
    format: 'YYYY-MM-DD'
  });

  $('#desde, #hasta').on('change', function () {
    table.draw();
  });

  $.fn.dataTable.ext.search.push(
    function(settings, data, dataIndex) {
      let min = minDate.val();
      let max = maxDate.val();
      let date = new Date(data[4]);

      if (
        (min === null && max === null) ||
        (min === null && date <= new Date(max)) ||
        (min <= date && max === null) ||
        (min <= date && date <= new Date(max))
      ) {
        return true;
      }
      return false;
    }
  );
});
