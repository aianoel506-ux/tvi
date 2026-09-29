var USERSREPORT = function () {

    PECO.getHighlightsPlugin();
    PECO.getSelect2Plugins();


    var usersreporttbl = $('#usersreporttbl', document);
    var tbl_user_access_group = $('#tbl_user_access_group', document);

    var init_userreport = function () {
        init_datausers();
        init_events();
    };
    var init_events = function () {
        PECO.dtSubDetails(usersreporttbl, 'reports/usersessions', false);

        $(document).on('click', '#btn_copy_access_table', function(e) {
            e.preventDefault();
            selectElementContents(document.getElementById('tbl_user_access_group'));
        });

        $(document).on('shown.bs.tab', 'a[data-toggle=tab]', function (e) {
            var target = e.target.href;
            var href = $(this).attr('href').replace('#', '');

            if (href == 'users_access') {
                $.ajax({
                    url: PECO.base_url() + 'systems/tblusersaccess',
                    dataType: 'json'
                }).done(function (d) {
                    tbl_user_access_group.DataTable({
                        bDestroy: true,
                        bPaginate: false,
                        bFilter: true,
                        bInfo: true,
                        bStateSave: true,
                        bProcessing: true,
                        aaData: d.list,
                        aoColumns: [
                            {"data": "username"},
                            {"data": "name"},
                            {"data": "roles"},
                            {"data": "modules"},
                        ],
                        fnDrawCallback: function () {
                            MergeGridCells(tbl_user_access_group);
                        },
                        dom: 'Bfrtip',
                        //dom: "<'row' <'col-md-12'B>><'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>",
                        buttons: [
                            'copy', 'csv', 'excel', 'pdf', 'print'
                        ]
                    });
                });
            }
        });
    };

    function MergeGridCells(tbl) {
        var dimension_cells = new Array();
        var dimension_col = null;
        var columnCount = $("tr:first th", tbl).length;
        for (dimension_col = 0; dimension_col <= columnCount; dimension_col++) {
            // first_instance holds the first instance of identical td
            var first_instance = null;
            var rowspan = 1;
            // iterate through rows
            tbl.find('tr').each(function () {

                // find the td of the correct column (determined by the dimension_col set above)
                var dimension_td = $(this).find('td:nth-child(' + dimension_col + ')');


                if (first_instance === null) {
                    // must be the first row
                    first_instance = dimension_td;
                } else if (dimension_td.text() === first_instance.text()) {
                    // the current td is identical to the previous
                    // remove the current td
                    // dimension_td.remove();
                    dimension_td.attr('hidden', true);
                    ++rowspan;
                    // increment the rowspan attribute of the first instance
                    first_instance.attr('rowspan', rowspan);
                } else {
                    // this cell is different from the last
                    first_instance = dimension_td;
                    rowspan = 1;
                }
            });
        }
    }

    var init_datausers = function () {
        $.ajax({
            url: PECO.base_url() + 'reports/getuserreports',
            type: 'post',
            dataType: 'json'
        }).done(function (d) {

            usersreporttbl.dataTable().empty();
            usersreporttbl.dataTable({
                bDestroy: true,
                bPaginate: true,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                bProcessing: true,
                aaData: d.usersreports,
                aoColumns: [
                    {"data": "num"},
                    {"data": "expand", sClass: 'expand'},
                    {"data": "fname"},
                    {"data": "lname"},
                    {"data": "sessdatetime"},
                    {"data": "logcount"}
                ],
                searchHighlight: true,

                fnRowCallback: function (nRow, data, iDisplayIndex) {
                    $(nRow).addClass(data.rowcolor);
                    var index = iDisplayIndex + 1;
                    $('td:eq(0)', nRow).html(index);
                    PECO.dtExpandBtn(nRow, data.expand)
                }
            });
        }).fail(function () {
            PECO.phpError();
        });
    };

    var selectElementContents = function(el) {
        var body = document.body, range, sel;
        if (document.createRange && window.getSelection) {
            range = document.createRange();
            sel = window.getSelection();
            sel.removeAllRanges();
            try {
                range.selectNodeContents(el);
                sel.addRange(range);
            } catch (e) {
                range.selectNode(el);
                sel.addRange(range);
            }
        } else if (body.createTextRange) {
            range = body.createTextRange();
            range.moveToElementText(el);
            range.select();
        }
    };

    return {
        init: function () {
            init_userreport();
        }
    }
}();
