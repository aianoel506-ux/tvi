var EVALUATION  = function () {
    
    var executivetbl = $(document).find('#executivetbl');
    var employeestable = $(document).find('#employeestable');

    var init_employees = function () {
        PECO.select2Basic($('#select2execforemp',document),'hris/getselect2exec' , 'Select Executives',false,false,false);
        PECO.select2Basic($('#select2execjforemp',document),'hris/getselect2execj' , 'Select Junior Executives',false,false,false);
        PECO.select2Basic($('#select2deptforemp',document),'user/getdepartments' , 'Select Department',false,false,false);
    };

    var init_executives = function () {
        $.ajax({
            url:PECO.base_url()+'hris/getexecutiveslist',
            type:'post',
            dataType:'json'
        }).done(function (data) {
            executivetbl.dataTable().empty();
            executivetbl.dataTable({
                bDestroy: true,
                bPaginate: true,
                bFilter: true,
                bInfo: true,
                bStateSave: false,
                bProcessing: true,
                aaData: data.executives,
                aoColumns: [
                    {"data":"num"},
                    {"data":"name"},
                    {"data":"position"},
                    {"data":"coexec"},
                    {"data":"self"},
                    {"data":"comm"},
                    {"data":"pceo"}
                ],
                searchHighlight: true
            });
        }).fail(function () {
            PECO.phpError();
        });
    };

    var init_main = function () {
        init_executives();
        init_employees();
        init_events();
    };

    var init_events = function () {
        $(document).on('submit','#sumitemployeefilter' , function (e) {
            e.preventDefault();
            var this_ = $(this);
            $.ajax({
                url:this_.attr("action"),
                type:this_.attr("method"),
                data:this_.serialize(),
                dataType:'json'
            }).done(function (data) {
                employeestable.dataTable().empty();
                employeestable.dataTable({
                    bDestroy: true,
                    bPaginate: true,
                    bFilter: true,
                    bInfo: true,
                    bStateSave: false,
                    bProcessing: true,
                    aaData: data.employees,
                    aoColumns: [
                        {"data":"num"},
                        {"data":"name"},
                        {"data":"position"},
                        {"data":"self"},
                        {"data":"exec"},
                        {"data":"head"},
                        {"data":"comm"},
                        {"data":"pceo"}
                    ],
                    searchHighlight: true
                });
            }).fail(function () {
                PECO.phpError();
            });
        });
    };

    return{
        init:function () {
            init_main();
        }
    }
}();