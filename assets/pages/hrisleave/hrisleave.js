var HRISLEAVE = function(){
    PECO.getSweetAlert();

    var leavereportstable = $('#leavereportstable',document);


    var init_hrisleave = function(){
        fetchleavereports(301);
        events();
    };

    var fetchleavereports = function(status) {
        $.ajax({
            url:PECO.base_url()+'hris/fetchleavereports',
            type:'post',
            data:{"status":status},
            dataType:'json'
        }).done(function (d) {
            leavereportstable.dataTable().empty();
            leavereportstable.dataTable({
                bDestroy: true,
                bPaginate: true,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                aaData: d.hrisleavedata,
                aoColumns: [
                    //  {"data": "expand", sWidth: '10px', sClass: 'expand'},
                    {"data": "num", sWidth: '10px'},
                    {"data": "empid", sWidth: '90px', sClass: 'text-danger text-bold'},
                    {"data": "leave", sWidth: '200px', sClass: 'text-primary'},
                    {"data": "fromdate", sWidth: '', sClass: ''},
                    {"data": "todate", sWidth: '', sClass: ''},
                    {"data": "hours", sWidth: '', sClass: 'amlate'},
                    {"data": "datecreated", sWidth: '', sClass: 'amlate'},
                    {"data": "status", sWidth: ''}
                ],
                searchHighlight: true,
                language: {
                    "emptyTable": '<h4><i class="fa fa-warning text-warning"></i> No record found.</h4>'
                },
            });

        }).fail(function () {
            PECO.phpError();
        });
    };

    var events = function (){

        $(document).on('click','#leavereportsbtn',function () {
             $.ajax({
                 url:PECO.base_url()+'hris/getleavereports',
                 type:'post',
                 dataType:'json'
             }).done(function (d) {

             }).fail(function () {
                 PECO.phpError();
             });
        });

        $('#btn_clear_cadtrans', document).click(function(e) {
            e.preventDefault();
            swal({
                title: "Are you sure?",
                text: "Clear C.A.D. Transactions, will delete all temporary transactions. Note: this is for development porpuses only.",
                type: "error",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes, Clear!",
                closeOnConfirm: false,
                closeOnCancel: false,
                showLoaderOnConfirm: true
            }, function(isConfirm) {
                if (isConfirm) {
                    $.post(PECO.base_url() + 'hris/clearleaverequests', function (d) {
                        swal.close();
                        PECO.initAlerts(d.msg, 'PECO.net', d.func);
                    }, 'json');
                }else{
                    swal.close();
                }
            });
        });
    };
    return{
        init:function(){
            init_hrisleave();
        }
    }
}();