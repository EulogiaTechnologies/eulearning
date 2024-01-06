let table = $('#lms_table').DataTable({
    bLengthChange: true,
    "lengthChange": true,
    "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
    "bDestroy": true,
    processing: true,
    serverSide: true,
    // stateSave: true,
    order: [[1, "desc"]],
    "ajax": $.fn.dataTable.pipeline({
        url: $('#my_refund_route').val(),
        data: function (d) {
            d.f_date = $('.date_range_input').val();
        },
        pages: 5 // number of pages to cache
    }),
    columns: [
        {data: 'DT_RowIndex', name: 'id'},
        {data: 'created_at', name: 'created_at'},
        {data: 'course', name: 'course.title'},
        {data: 'purchase_price', name: 'purchase_price'},
        {data: 'request_from', name: 'request_from'},
        {data: 'type', name: 'type', orderable: false, searchable: false},
        {data: 'status', name: 'status', orderable: false, searchable: false},
        {data: 'action', name: 'action', orderable: false, searchable: false},

    ],
    language: {
        search: "<i class='ti-search'></i>",
        paginate: {
            next: "<i class='ti-arrow-right'></i>",
            previous: "<i class='ti-arrow-left'></i>"
        }
    },
    dom: 'Blfrtip',
    buttons: [
        {
            extend: 'copyHtml5',
            text: '<i class="far fa-copy"></i>',
            title: $("#logo_title").val(),
            exportOptions: {
                columns: ':visible',
                columns: ':not(:last-child)',
            }
        },
        {
            extend: 'excelHtml5',
            text: '<i class="far fa-file-excel"></i>',
            title: $("#logo_title").val(),
            margin: [10, 10, 10, 0],
            exportOptions: {
                columns: ':visible',
                columns: ':not(:last-child)',
            },

        },
        {
            extend: 'csvHtml5',
            text: '<i class="far fa-file-alt"></i>',
            exportOptions: {
                columns: ':visible',
                columns: ':not(:last-child)',
            }
        },
        {
            extend: 'pdfHtml5',
            text: '<i class="far fa-file-pdf"></i>',
            title: $("#logo_title").val(),
            exportOptions: {
                columns: ':visible',
                columns: ':not(:last-child)',
            },
            orientation: 'landscape',
            pageSize: 'A4',
            margin: [0, 0, 0, 12],
            alignment: 'center',
            header: true,
            customize: function (doc) {
                doc.content[1].table.widths =
                    Array(doc.content[1].table.body[0].length + 1).join('*').split('');
            }

        },
        {
            extend: 'print',
            text: '<i class="fa fa-print"></i>',
            title: $("#logo_title").val(),
            exportOptions: {
                columns: ':not(:last-child)',
            }
        },
        {
            extend: 'colvis',
            text: '<i class="fa fa-columns"></i>',
            postfixButtons: ['colvisRestore']
        }
    ],
    columnDefs: [{
        visible: false
    }],
    responsive: true,
});
(function ($) {
    "use strict";
    $(document).ready(function () {

        $(document).on('click', '.reset_btn', function (event) {
            event.preventDefault();
            $('.date_range_input').val('');
            resetAfterChange();
        });


        $(document).on('click', '.show_reason', function (event) {
            event.preventDefault();
            let reason = $(this).data('reason');
            let heading = $(this).data('heading');
            $('#reason_body').html(reason);
            $('#reason_heading').html(heading);
            $("#reasonShowModal").modal('show');
        });

        function resetAfterChange() {
            let table = $('#lms_table').DataTable();
            table.clearPipeline();
            table.ajax.reload();
        }

    });

})(jQuery);
