
var ASSETREPORT = function(){

    var assettable = $(document).find('#assettable');
    var init_reports = function (){
        assetreport(1);
    };

    var assetreport = function(status){
        $.ajax({
            url:PECO.base_url()+"assets/getassetreports",
            type:"post",
            data:{"status":status},
            dataType:"json",
            beforeSend: function(){
                assettable.dataTable().empty();
                PECO.DTphpLoading(assettable, 'Loading... ');
            }
        }).done(function (d) {
            populateassetreport(d);
        });
    };

    var populateassetreport = function (data) {
        assettable.dataTable().empty();
        assettable.dataTable({
            bDestroy: true,
            bPaginate: true,
            bFilter: true,
            bInfo: true,
            bStateSave: true,
            bProcessing: true,
            aaData: data.assetreportdata,
            aoColumns: [
                {"data":"num"},
                {"data":"assetcode"},
                {"data":"assetdesc"},
                {"data":"assetowner"},
                {"data":"assetloc"},
                {"data":"assetstat"},
                {"data":"assettype"},
                {"data":"control"}
            ],
            searchHighlight: true
        });
    };


    return{
        init:function(){
            init_reports();
        }
    }
}();