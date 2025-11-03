/*************************************************************************************/
// -->Template Name: Bootstrap Press Admin
// -->Author: Themedesigner
// -->Email: niravjoshi87@gmail.com
// -->File: datatable_advanced_init
/*************************************************************************************/

//=============================================//
//    File export                              //
//=============================================//
$('#file_export').DataTable({
    dom: 'Bfrtip',
    buttons: [
        'copy', 'csv', 'excel', 'pdf', 'print'
    ]
});
$('.buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel').addClass('btn btn-primary mr-1');

//==================================================//
//  Show / hide columns dynamically                 //
//==================================================//

var table = $('#show_hide_col').DataTable({
    "scrollY": "200px",
    "paging": false
});

$('a.toggle-vis').on('click', function(e) {
    e.preventDefault();

    // Get the column API object
    /*var column = table.column($(this).attr('data-column'));*/
    var column = $('#show_hide_col').dataTable().api().column($(this).attr('data-column'));
    // Toggle the visibility
    column.visible(!column.visible());
});

//=============================================//
//    Column rendering                         //
//=============================================//

$(window).on('load', function () {
  var $t = $('#col_render');
  if (!$t.length) return;

  if ($.fn.DataTable.isDataTable($t)) {
    $t.DataTable().clear().destroy();
  }

  $t.DataTable({
    ajax: {
      url: '/transactions', 
      type: 'POST',
      dataSrc: '',
      error: function (xhr) {
        console.error('[DT-init] AJAX error', xhr.status, xhr.responseText);
      }
    },
    columns: [
      { data: 'transaction_date' },
      { data: 'reference_number' },
      { data: 'from_customer' },
      { data: 'from_account_type' },
      { data: 'to_customer' },
      { data: 'to_account_type' },
      {
        data: 'amount',
        render: function (data, type) {
          if (type === 'display' || type === 'filter') {
            return '$' + parseFloat(data).toFixed(2);
          }
          return data;
        }
      }
    ],
    order: [[0, 'desc']],  // sort by newest date
    lengthMenu: [[5,10, 25, 50, 100,-1], [5,10, 25, 50, 100,'All']],
    pageLength: 5,
    responsive: true
  });
});

//=============================================//
//    Column rendering for balances            //
//=============================================//
$(window).on('load', function () {
  const $b = $('#balances_table');
  if (!$b.length) return;

  if ($.fn.DataTable.isDataTable($b)) $b.DataTable().clear().destroy();

  $b.DataTable({
    processing: true,
    ajax: {
      url: '/accounts',  
      type: 'POST',
      dataSrc: '',                       
      error: function (xhr) {
        console.error('[DT] ACCT AJAX error', xhr.status, xhr.responseText);
      }
    },
    columns: [
      { data: 'customer_name' },
      { data: 'account_id' },
      { data: 'account_type' },
      {
        data: 'balance',
        render: function (d, type) {
          if (type !== 'display' && type !== 'filter') return d;
          const n = Number(String(d).replace(/[^0-9.-]/g, ''));
          return new Intl.NumberFormat('en-SG', { style: 'currency', currency: 'SGD' }).format(n);
        }
      }
    ],
    order: [[0,'asc'], [2,'asc'], [1,'asc']],
    lengthMenu: [[5,10,25,50,100,-1],[5,10,25,50,100,'All']],
    pageLength: 5,
    dom: 'lfrtip',
    responsive: true
  });
});