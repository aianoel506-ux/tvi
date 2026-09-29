
var SCHEDULE = function(){
    PECO.getHighlightsPlugin();
    PECO.getSweetAlert();
    PECO.getSelect2Plugins();
    PECO.getiCheckPlugin();

    var empworkshifttbl = $('#empworkshifttbl');
    var workschedtbl = $('#workschedtbl');
    var regularemployeeschedtable = $('#regularemployeeschedtable');

    var main = function(){
        init();
        events();
    };

    var fetchempschedworkshift = function(){
        $.ajax({
            url:PECO.base_url()+'hris/fetchempschedworkshift',
            type:'post',
            dataType:'json'
        }).done(function (d) {
            populatempschedworkshift(d);
        }).fail(function () {
            PECO.phpError();
        });
    };

    var populatempschedworkshift = function (data) {
        workschedtbl.dataTable().empty();
        workschedtbl.dataTable({
            bDestroy: true,
            bPaginate: true,
            bFilter: true,
            bInfo: true,
            bStateSave: true,
            bProcessing: true,
            aaData: data.empscheddata,
            aoColumns: [
                {data: 'num'},
                {data: 'empid'},
                {data: 'name'},
                {data: 'date'},
                {data: 'amin'},
                {data: 'amout'},
                {data: 'pmin'},
                {data: 'pmout'},
                {data: 'control'}
            ],
            language: PECO.DTEmptyMessage(),
            searchHighlight: true
        });
        PECO.initDTNicescroller();
    };

    var fetchempworkshift = function () {
        $.ajax({
            url:PECO.base_url()+'hris/fetchempworkshft',
            type:'post',
            dataType:'json'
        }).done(function (d) {
            populateempworkshift(d);
        }).fail(function () {
            PECO.phpError();
        });
    };

    var populateempworkshift = function (data) {
        empworkshifttbl.dataTable().empty();
        empworkshifttbl.dataTable({
            bDestroy: true,
            bPaginate: true,
            bFilter: true,
            bInfo: true,
            bStateSave: true,
            bProcessing: true,
            aaData: data.empworkshiftdata,
            aoColumns: [
                {data: 'num'},
                {data: 'empid'},
                {data: 'name', sWidth:'25%'},
                {data: 'workshift', sWidth:'45%'},
                {data: 'control'}
            ],
            language: PECO.DTEmptyMessage(),
            searchHighlight: true
        });
        PECO.initDTNicescroller();
    };

    var init = function () {
        fetchempworkshift();
        fetchempschedworkshift();
    };

    var events = function () {


        $(document).on('click','#printworkshift',function () {

           var monthdefault = $('#workshiftmonth',document).val();
           var defaultyear = $('#workshiftyear',document).val();
            $.ajax({
                url:PECO.base_url()+'hris/getallemployeeschedule',
                type:'post',
                data:{"monthdata":monthdefault,"yeardata":defaultyear},
                dataType:'json'
            }).done(function (d) {
                    PECO.pecoRepPrint("Approved Schedule as of "+monthdefault+"/"+defaultyear,d.html);
            }).fail(function () {
                PECO.phpError();
            });
        });

        $(document).on('click','#searchworkshift',function (e) {
            var month = $(document).find('#workshiftmonth').val();
            var year = $(document).find('#workshiftyear').val();
            loadapprovedworkshift(month , year);
        });
        $(document).on('click','#approveschedreq',function (e) {

            e.preventDefault();
            var this_ = $(this);
            swal({
                title: "Are you sure?",
                text: "Approve schedule!",
                type: "error",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes, Approve!",
                closeOnConfirm: false,
                closeOnCancel: true,
                showLoaderOnConfirm: true
            }, function(isConfirm){
                if (isConfirm) {
                    var dataid = this_.attr("data-id");
                    $.ajax({
                        url: PECO.base_url() + 'hris/approvesched',
                        type: 'post',
                        data: {'dataid': dataid},
                        dataType: 'json'
                    }).done(function (d) {
                        if(d.qry == true){
                            fetchempschedworkshift();
                        }
                        swal(d.title,d.msg,d.func);
                    }).fail(function(){
                        swal("Failed", "Approving schedule.", "warning");
                    });
                }
            });
        });

        $(document).on('click','#disapproveschedreq',function (e) {
            e.preventDefault();
            var this_ = $(this);
            swal({
                title: "Are you sure?",
                text: "Disapprove schedule!",
                type: "error",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes, Dispprove!",
                closeOnConfirm: false,
                closeOnCancel: true,
                showLoaderOnConfirm: true
            }, function(isConfirm){
                if (isConfirm) {

                    var dataid = this_.attr("data-id");
                    $.ajax({
                        url: PECO.base_url() + 'hris/disapprovesched',
                        type: 'post',
                        data: {'dataid': dataid},
                        dataType: 'json'
                    }).done(function (d) {
                        fetchempschedworkshift();
                        swal("Dispproved!", "Schedule has been disapproved.", "success");
                    }).fail(function(){
                        swal("Failed", "Disapproving schedule.", "warning");
                    });
                }
            });
        });
    };

    var loadapprovedworkshift = function(month , year){


        var monthdata = (month) ? month : false;
        var year = (year) ? year : false;
        var d = new Date();
        var defaultyear = d.getFullYear();
        var monthdefault = d.getMonth() + 1;
        if(monthdata > 0){
            monthdefault = monthdata;
        }
        if(year > 0){
            defaultyear = year;
        }
        $.ajax({
            url:PECO.base_url()+'hris/getallemployeeschedule',
            type:'post',
            data:{"monthdata":monthdefault,"yeardata":defaultyear},
            dataType:'json',
            beforeSend: function(){
                regularemployeeschedtable.dataTable().empty();
                PECO.DTphpLoading(regularemployeeschedtable, 'Loading... ');
            }
        }).done(function (d) {
            regularemployeeschedtable.dataTable().empty();
            regularemployeeschedtable.dataTable({
                bDestroy: true,
                bPaginate: true,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                bProcessing: true,
                aaData: d.regularempscheddata,
                aoColumns: [
                    {"data":"num"},
                    {"data":"name"},
                    {"data":"shift"},
                    {"data":"fromdate"},
                    {"data":"todate"},
                    {"data":"datecreated"}
                ],
                searchHighlight: true
            });
        }).fail(function () {
            PECO.phpError();
        });
    };

    var initmainapproveworkshift = function(month , year){
        var monthdata = (month) ? month : false;
        var year = (year) ? year : false;
        var d = new Date();
        var defaultyear = d.getFullYear();
        var monthdefault = d.getMonth() + 1;
        if(monthdata > 0){
            monthdefault = monthdata;
        }
        if(year > 0){
            defaultyear = year;
        }

        PECO.select2Basic($('#workshiftmonth'),'systems/select2month', 'Select Month...', false, false, monthdefault);
        PECO.select2Basic($('#workshiftyear'),'hris/select2year','Select Year',false,false,defaultyear);
        loadapprovedworkshift();
        events();
    };

    return {
        init:function () {
            main();
        },
        initapprovedworkshift:function(){
            initmainapproveworkshift();
        }
    }
}();