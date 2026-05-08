$(document).ready(function() {
    setTimeout(function() {
        function getTableButtons($table) {
            var exportColumns = $table.data('export-skip-first') ? ':not(:first-child)' : ':visible';
            var exportOptions = {
                columns: exportColumns
            };

            return [
                {
                    extend: 'csv',
                    exportOptions: exportOptions
                },
                {
                    extend: 'excel',
                    exportOptions: exportOptions
                },
                {
                    extend: 'pdf',
                    exportOptions: exportOptions
                },
                {
                    extend: 'print',
                    exportOptions: exportOptions
                }
            ];
        }

        function initBasicTable(selector) {
            var $table = $(selector);

            if (!$table.length || $.fn.DataTable.isDataTable(selector)) {
                return;
            }

            $table.DataTable({
                layout: {
                    topStart: {
                        buttons: getTableButtons($table)
                    }
                },
                "pageLength": 50, // Default 50 records per page
            });
        }

        // [ Zero Configuration ] start
        // const dataTable = new simpleDatatables.DataTable("#simpletable", {
        //     searchable: true,
        //     fixedHeight: true,
        // });
        initBasicTable('#simpletable');
        initBasicTable('#simpletable2');
        initBasicTable('#simpletable3');
        // let dataTable = new DataTable("#myTable");

        // [ Default Ordering ] start
        $('#order-table').DataTable({
            "order": [
                [3, "desc"]
            ]
        });

        // [ Multi-Column Ordering ]
        $('#multi-colum-dt').DataTable({
            columnDefs: [{
                targets: [0],
                orderData: [0, 1]
            }, {
                targets: [1],
                orderData: [1, 0]
            }, {
                targets: [4],
                orderData: [4, 0]
            }]
        });

        // [ Complex Headers ]
        $('#complex-dt').DataTable();

        // [ DOM Positioning ]
        $('#DOM-dt').DataTable({
            "dom": '<"top"i>rt<"bottom"flp><"clear">'
        });

        // [ Alternative Pagination ]
        $('#alt-pg-dt').DataTable({
            "pagingType": "full_numbers"
        });

        // [ Scroll - Vertical ]
        $('#scr-vrt-dt').DataTable({
            "scrollY": "200px",
            "scrollCollapse": true,
            "paging": false
        });

        // [ Scroll - Vertical, Dynamic Height ]
        $('#scr-vtr-dynamic').DataTable({
            scrollY: '50vh',
            scrollCollapse: true,
            paging: false
        });

        // [ Language - Comma Decimal Place ]
        $('#lang-dt').DataTable({
            "language": {
                "decimal": ",",
                "thousands": "."
            }
        });

    }, 100);
});
