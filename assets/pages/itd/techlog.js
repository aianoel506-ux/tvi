var TECHLOG = function() {

    PECO.getDatePickerPlugins();

    var tbl_ticket_list = $('#tbl_ticket_list', document);
    var hanlderTechLogs = function() {

        PECO.select2Types($('#select2issuetype', document), 'TECHLOGTYPE', 'Select issue type..', false, false, false, false, true);

        setTimeout(function() {

            $("#techlogdatetime", document).datetimepicker({
                isRTL: PECO.isRTL(),
                format: "yyyy-mm-dd hh:ii",
                showMeridian: !0,
                autoclose: !0,
                fontAwesome: !0,
                pickerPosition: 'bottom-right',
                todayBtn: !0
            });
        },300);

        PECO.handlerComplaintsInputBasic();

    };


    var handlerTechLogView = function(dataid) {
        var input_employee_assign = $('#input_employee_assign', document);
        PECO.employeeSelectTagging(input_employee_assign, true);
    };


    var handlerTechLogList = function(int) {
        init_list_table(1, int);
        $('#btn_refresh_list', document).click(function(e){
            init_list_table(1, int);
        });

        PECO.dtSubDetails(tbl_ticket_list, 'itd/getlogdetails');
    };


    var init_list_table = function(status, int) {
        $.ajax({
            url: PECO.base_url() + 'itd/gettechloglist',
            type: 'post',
            dataType: 'json',
            data: {'status': status, 'complaints': 'it', 'int': int},
            beforeSend: function() {
                PECO.DTphpLoading(tbl_ticket_list, 'Loading ticket history...');
            }
        }).done(function (d) {
            tbl_ticket_list.DataTable({
                bDestroy: true,
                bPaginate: false,
                bFilter: true,
                bInfo: true,
                aaData: d.list,
                bSort: false,
                //scrollY: '',
                aoColumns: [
                    {"data": "expand", sWidth: '', sClass: 'text-align-center'},
                    {"data": "num", sWidth: '', sClass: 'text-align-center'},
                    {"data": "ticketno", sWidth: '10%', sClass: 'text-primary tcno'},
                    {"data": "name", sWidth: '20%', sClass: 'text-danger tcname'},
                    {"data": "department", sWidth: '10%', sClass: ''},
                    {"data": "time", sWidth: '', sClass: ''},
                    {"data": "complaints", sWidth: '', sClass: 'text-info'},
                    {"data": "remarks", sWidth: '10%', sClass: 'remarks'},
                    {"data": "findings", sWidth: '10%', sClass: 'remarks'},
                    {"data": "status", sWidth: '10%', sClass: 'status editable'}
                ],
                "language": PECO.DTEmptyMessage(),
                fnRowCallback: function(nRow, aData, Index) {
                    PECO.dtExpandBtn(nRow, aData.expand);

                    // CREATE SORT NUMBER
                    var index = Index +1;
                    $('td:eq(1)',nRow).html(index);
                }
            });
        }).fail(function(){
            PECO.DTphpError(tbl_ticket_list, 'Error loading ticket: PHP error!');
        });
    };

    return  {
        init: function() {
            hanlderTechLogs();
        },
        list: function(int) {
            handlerTechLogList(int);
        },
        view: function(dataid) {
            handlerTechLogView(dataid);
        }
    }

}();