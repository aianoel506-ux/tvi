
var COSTMAIN = function () {


    var costmaintbl = $('#costmaintbl',document);

    var init_events = function () {

        $(document).on('submit','#submitcostheadexec' , function (e) {
            e.preventDefault();
            var this_ = $(this);
            $.ajax({
                url:this_.attr("action"),
                type:this_.attr("method"),
                data:this_.serialize(),
                dataType:'json'
            }).done(function (data) {
                PECO.initAlerts(data.msg , "PECO" , data.func);
                if(data.qry == true){
                    init_costmain();
                }
            }).fail(function () {
                PECO.phpError();
            });
            e.stopImmediatePropagation();
        });
    };

    var init_costmain = function () {

        $.ajax({
            url:PECO.base_url()+'hris/getcostmain',
            type:'post',
            dataType:'json'
        }).done(function (data) {
            costmaintbl.dataTable().empty();
            costmaintbl.dataTable({
                bDestroy: true,
                bPaginate: true,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                aaData: data.costcenterdata,
                aoColumns: [
                    {"data": "num"},
                    {"data": "code"},
                    {"data": "desc"},
                    {"data": "head"},
                    {"data": "exec"},
                    {"data": "control"}
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

    return{
        init:function () {
            init_costmain();
            init_events();
        }
    }
}();

