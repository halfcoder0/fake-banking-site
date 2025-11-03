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
//    Column Rendering for Transactions Table  //
//=============================================//
$(window).on('load', function () {
  const $t = $('#col_render');
  if (!$t.length) return;

  // Destroy existing DataTable instance (prevent reinit)
  if ($.fn.DataTable.isDataTable($t)) {
    $t.DataTable().clear().destroy();
  }

  $t.DataTable({
    processing: true,
    ajax: {
      url: '/transactions',    // <-- Your controller endpoint
      type: 'POST',
      dataSrc: '',
      error: function (xhr) {
        console.error('[DT] Transactions AJAX Error:', xhr.status, xhr.responseText);
        if (xhr.status === 401) {
          alert('Your session has expired. Please log in again.');
          window.location = '/login';
        }
      }
    },
    columns: [
      { data: 'transaction_date', title: 'Date' },
      { data: 'reference_number', title: 'Reference Number' },
      { data: 'from_customer', title: 'From (Customer)' },
      { data: 'from_account_type', title: 'From (Account Type)' },
      { data: 'to_customer', title: 'To (Customer)' },
      { data: 'to_account_type', title: 'To (Account Type)' },
      {
        data: 'amount',
        title: 'Amount (SGD)',
        render: function (data, type) {
          if (type !== 'display' && type !== 'filter') return data;
          const value = Number(String(data).replace(/[^0-9.-]/g, ''));
          return new Intl.NumberFormat('en-SG', { style: 'currency', currency: 'SGD' }).format(value);
        }
      }
    ],
    order: [[0, 'desc']],
    lengthMenu: [[5, 10, 25, 50, 100, -1], [5, 10, 25, 50, 100, 'All']],
    pageLength: 5,
    responsive: true,
    dom: 'lfrtip',
    language: {
      emptyTable: 'No transactions available.',
      lengthMenu: 'Show _MENU_ entries',
      search: 'Filter:',
      zeroRecords: 'No matching records found.'
    }
  });
});

//=============================================//
//    Column Rendering for Account Balances     //
//=============================================//
$(window).on('load', function () {
  const $b = $('#balances_table');
  if (!$b.length) return;

  // Destroy existing instance
  if ($.fn.DataTable.isDataTable($b)) {
    $b.DataTable().clear().destroy();
  }

  $b.DataTable({
    processing: true,
    ajax: {
      url: '/accounts',   // <-- Your controller endpoint
      type: 'POST',
      dataSrc: '',
      error: function (xhr) {
        console.error('[DT] Accounts AJAX Error:', xhr.status, xhr.responseText);
        if (xhr.status === 401) {
          alert('Your session has expired. Please log in again.');
          window.location = '/login';
        }
      }
    },
    columns: [
      { data: 'customer_name', title: 'Customer Name' },
      { data: 'account_id', title: 'Account ID' },
      { data: 'account_type', title: 'Account Type' },
      {
        data: 'balance',
        title: 'Balance (SGD)',
        render: function (data, type) {
          if (type !== 'display' && type !== 'filter') return data;
          const numeric = Number(String(data).replace(/[^0-9.-]/g, ''));
          return new Intl.NumberFormat('en-SG', { style: 'currency', currency: 'SGD' }).format(numeric);
        }
      }
    ],
    order: [[0, 'asc'], [2, 'asc'], [1, 'asc']],
    lengthMenu: [[5, 10, 25, 50, 100, -1], [5, 10, 25, 50, 100, 'All']],
    pageLength: 5,
    dom: 'lfrtip',
    responsive: true,
    language: {
      emptyTable: 'No account balances available.',
      lengthMenu: 'Show _MENU_ entries',
      search: 'Filter:',
      zeroRecords: 'No matching records found.'
    }
  });
});