var NBOS = function () {
    var table = $('#bos_creation_tbl');
    var init_nBOS = function () {
        table.on('click', 'tbody td #copythis', function (e) {
            e.preventDefault();
            console.log('Row added!');
            var this_tr_html = $(this).closest('tr').html();
            $('tbody', table).append('<tr>' + this_tr_html + '</tr>');
            droawSelect2Tbl_entry();
        });
        table.on('click', 'tbody td #delthis', function (e) {
            e.preventDefault();
            console.log('Row added!');
            var this_tr = $(this).closest('tr');
            var id = $(this).attr('data-id');
            this_tr.fadeOut('fast');
            $.ajax({
                url: PECO.base_url() + 'bos/trnbudgetdelrow',
                type: 'POST',
                data: {'id': id},
                dataType: "json",

            }).done(function (d) {
                if (d.qry == true)
                {
                    drawDT_list();
                }
            });

        });

        $('#btn_add_row').click(function (e) {
            e.preventDefault();
            console.log('Row added!');
            $('tbody', table).append(tr_html);
            $('tbody', table).find('select').select2('destroy');
            droawSelect2Tbl_entry();
        });


    };
    var tr_html = function () {
        $.ajax({
            url: PECO.base_url() + 'bos/trnbudgetaddrow',
            type: 'POST',
            data: {},
            dataType: "json",

        }).done(function (d) {
            if (d.qry == true)
            {
                drawDT_list();
            }
        });
    };
    var formatDataSelection = function (route) {

        if (!route.id) {
            return route.text;
        }

        var $route = $('<span><i class="fa fa-check text-success"></i> ' + route.text.split('-', 1) + '</span>');
        return $route;
    };
    var formatState = function (route) {

        if (!route.id) {
            return route.text;
        }
        var route_arr = route.text.split('-');
        var $route = $(
                '<p><b>' + route_arr[0] + '</b><br><em>' + route_arr[1] + '</em></p>'
                );
        return $route;
    };
    var droawSelect2Tbl_entry = function () {
        table.each(function (e) {
            $(this).find('select').select2({
                placeholder: 'Select..',
                allowClear: true,
                formatResult: formatState,
                formatSelection: formatDataSelection
            });
        });
        PECO.select2_scroller();
    };
    var fnFormatDetails = function (oTable, nTr) {
        var aData = oTable.fnGetData(nTr);
        var sOut = '<table class="table table-condensed table-striped table-hover">';
        sOut += '<tr><td width="150px">Budget Title:</td><td >' + aData[2] + '</td></tr>';
        sOut += '<tr><td width="150px">Prepared By:</td><td>Lucky John Faderon</td></tr>';
        sOut += '<tr><td width="150px">Date Created:</td><td >2016-09-08 08:00 AM</td></tr>';
        sOut += '</table>';

        return sOut;
    };

    var drawDT_list = function () {

        table.dataTable().empty();
        var oTable = table.dataTable({
            bDestroy: true,
            bPaginate: false,
            bFilter: false,
            bInfo: true,
            bStateSave: true,
            scrollY: false,
            bProcessing: true,
            bServerSide: true,
            //"order": [[ 0, "desc" ], [ 1, "asc" ]],
            oLanguage: {
                sProcessing: '<p class="text-info">Loading time logs... </p.'
            },
            ajax: {
                url: PECO.base_url() + 'bos/trnbudgetcreation',
                type: "POST",
                //data: {},

            },
            aoColumns: [
                {"data": "btype"},
                {"data": "ccid"},
                {"data": "joborder"},
                {"data": "terms"},
                {"data": "desc"},
                {"data": "amt", sClass: 'number text-danger'},
                {"data": "control"},
            ],
            columnDefs: [
                {"targets": '_all', "orderable": false, "searchable": false},
            ],
            "columnDefs": [{
                    "orderable": false,
                    "targets": [0]
                }],
            "drawCallback": function (settings) {
                droawSelect2Tbl_entry();
            },
            "language": {
                "aria": {
                    "sortAscending": ": activate to sort column ascending",
                    "sortDescending": ": activate to sort column descending"
                },
                "emptyTable": "No data available in table",
                "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                "infoEmpty": "No entries found",
                "infoFiltered": "(filtered1 from _MAX_ total entries)",
                "lengthMenu": "Show _MENU_ entries",
                "search": "Search:",
                "zeroRecords": "No matching records found"
            },

            "columnDefs": [{
                    "orderable": false,
                    "targets": [0]
                }],
            "order": [
                [1, 'asc']
            ],
            "lengthMenu": [
                [5, 15, 20, -1],
                [5, 15, 20, "All"] // change per page values here
            ],
            // set the initial value
            "pageLength": 10,

        });
        var tableWrapper = $('#bos_creation_tbl_wrapper'); // datatable creates the table wrapper by adding with id {your_table_jd}_wrapper

        tableWrapper.find('.dataTables_length select').select2(); // initialize select2 dropdown

        /* Add event listener for opening and closing details
         * Note that the indicator for showing which row is open is not controlled by DataTables,
         * rather it is done here
         */
        table.on('click', ' tbody td .row-details', function () {
            var nTr = $(this).parents('tr')[0];
            if (oTable.fnIsOpen(nTr)) {
                /* This row is already open - close it */
                $(this).addClass("row-details-close").removeClass("row-details-open");
                oTable.fnClose(nTr);
            } else {
                /* Open this row */
                $(this).addClass("row-details-open").removeClass("row-details-close");
                oTable.fnOpen(nTr, fnFormatDetails(oTable, nTr), 'details');
            }
        });



        PECO.initDTNicescroller();

    };
    return {
        init: function () {
            init_nBOS();
            drawDT_list();
        }
    };

}();