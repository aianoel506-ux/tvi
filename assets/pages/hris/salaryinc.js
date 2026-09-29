var SALARYINC = function(){

    PECO.getNumberFormatPlugin();
    PECO.getSweetAlert();
    PECO.getSelect2Plugins();
    PECO.getHighlightsPlugin();

    var salaryinctbl = $(document).find('#salaryinctbl');
    var pceosalaryinc = $(document).find("#pceosalaryinc");



    var init_main = function(dataid){

        PECO.select2Basic($('#payclasstype' , document) , 'hris/getpayclasslist' , 'Select payclass',false,false,false);


        fetchempsalaryinc();
        init_events(dataid);
        input_navigation();
    };

    var fetchpceoempsalaryinc = function(dataid){

        $.ajax({
            url:PECO.base_url()+'hris/pceoempsalaryinc',
            type:'post',
            data:{"dataid":dataid},
            dataType:'json'
        }).done(function (d) {
            populatepceoempsalaryinc(d)
        }).fail(function () {
            PECO.phpError();
        });
    };

    var populatepceoempsalaryinc = function(data){
        pceosalaryinc.dataTable().empty();
        pceosalaryinc.dataTable({
            bDestroy: true,
            bPaginate: false,
            bFilter: true,
            bInfo: true,
            bStateSave: true,
            bProcessing: true,
            aaData: data.pceoempsalaryincdata,
            aoColumns: [
                {"data":"num"},
                {"data":"empcode"},
                {"data":"lastname"},
                {"data":"firstname"},
                {"data":"department"},
                {"data":"basic" , sClass:'number'},
                {"data":"increase", sClass:'number'},
                {"data":"total", sClass:'number bold text-info',sWidth:'20%'},
                {"data":"control"}
            ],
            searchHighlight: true
        });

    };

    var fetchempsalaryinc = function(payclass){
        $.ajax({
            url:PECO.base_url()+'hris/empsalaryinc',
            type:'post',
            data:{"payclasstype" : payclass},
            dataType:'json'
        }).done(function (d) {
            populateempsalaryinc(d)
        }).fail(function () {
            PECO.phpError();
        });
    };

    var populateempsalaryinc = function(data){
        salaryinctbl.dataTable().empty();
        salaryinctbl.dataTable({
            bDestroy: true,
            bPaginate: false,
            bFilter: true,
            bInfo: true,
            bStateSave: true,
            bProcessing: true,
            aaData: data.empsalaryincdata,
            dom:'<"top"i>',
            aoColumns: [
                {"data":"num"},
                {"data":"empcode"},
                {"data":"name", sClass: 'name', sWidth: '15%'},
                {"data":"department" , sClass:'text-info'},
                {"data":"basic", sClass: 'number text-danger basic'},
                {"data":"inputs", sClass: 'number input increase', sWidth: '15%'},
                {"data":"purpose" , sClass: 'purpose', sWidth: '15%'},
                {"data":"remarks" , sClass: 'input remarks', sWidth: '16%'},
                {"data":"pendingamt", sClass: 'number text-success input', sWidth: '8%'},
            ],
            searchHighlight: true,
            fnRowCallback: function (nRow, data) {
                PECO.select2Basic($('#incpurpose', nRow), 'hris/getpurposeofsalaryinc','Select purpose',false,false,data.purposeval);
            }
        });
    };

    var input_navigation = function() {


        salaryinctbl.on('keyup', '#input_increase', function(e) {
            var this_ = $(this);
            var this_row = this_.closest('tr');
            var this_val = this_.val();
            if(this_val>0) {
                $('#incpurpose', this_row).attr('disabled', false);
                PECO.select2Basic($('#incpurpose', this_row), 'hris/getpurposeofsalaryinc','Select purpose',false,false,false);
            }else{
                $('#incpurpose', this_row).select2('distroy').attr('disabled', false);
            }
        });
        salaryinctbl.on('blur', '#input_increase', function(e) {
            var this_ = $(this);
            var this_row = this_.closest('tr');
            var this_val = this_.val();
            if(this_val>0) {
                $('#incpurpose', this_row).attr('disabled', false);
                $('#incpurpose', this_row).select2('open');
            }
        });

        salaryinctbl.on('keyup', '#remarks', function(e) {
            var this_ = $(this);
            var this_row = this_.closest('tr');

            var this_salaryinc = $('#input_increase', this_row).val();
            var this_empid = $('#empid', this_row).val();
            var this_basic =  $('#basic', this_row).val();
            var this_basic_text =  $('#basictext', this_row);
            var this_new_pending_amt = $('#pendingnewamt' , this_row);
            var this_purpose = $('#incpurpose' , this_row).val();
            var this_remarks = $('#remarks' , this_row).val();
            if (e.keyCode == 13) {
                if(this_salaryinc == ''){
                    PECO.initAlerts("Salary increase is empty","PECO.net","info");
                }else if(this_purpose == ''){
                    PECO.initAlerts("Purpose is empty","PECO.net","info");
                }else{
                    $.ajax({
                        url:PECO.base_url()+'hris/addemployeesalarytrn',
                        type:'post',
                        data:{"empid":this_empid , "curamt" : this_basic,"salinc":this_salaryinc,"purpose":this_purpose,"remarks":this_remarks},
                        dataType:'json'
                    }).done(function (d) {
                        PECO.initAlerts(d.msg,"PECO.net",d.func);

                        this_new_pending_amt.text(d.pendingnewamt);

                        var index = this_.closest("tr").index() + 1
                        var this_input = $('input#input_increase').eq(index).focus();

                        setTimeout(function() {
                            this_input.select();
                        },100);
                        salaryinctbl.find('tr.row-info').removeClass('row-info');
                        this_input.closest('tr').addClass('row-info');

                    }).fail(function () {
                        PECO.phpError();
                    });
                }
            }
        });

    };

    var init_events = function(dataid){

        $(document).on('change','#payclasstype',function () {
            var this_ = $(this);
            fetchempsalaryinc(this_.val());
        });

        $(document).on('click','#saveallsalaries',function () {
            swal({
                title: "Are you sure?",
                text: "Salary increase save.",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes, Process!",
                closeOnConfirm: false,
                closeOnCancel: false,
                showLoaderOnConfirm: true
            }, function(isConfirm){
                if (isConfirm) {
                    $.ajax({
                        url:PECO.base_url()+'hris/savesalaryincreasetrn',
                        type:'post',
                        dataType:'json',
                        beforeSend: function(){
                            salaryinctbl.dataTable().empty();
                            PECO.DTphpLoading(salaryinctbl, 'Loading... ');
                        }
                    }).done(function (d) {
                        swal("Sent!", d.msg, d.func);
                        if(d.qry == true){
                            fetchempsalaryinc();
                        }
                    }).fail(function () {
                        PECO.phpError();
                    });
                } else {
                    swal("Cancelled", "Processing canceled", "error");
                }
            });
        });
        $(document).on('click','#disapproveempsalinc',function () {
            var this_ = $(this);
            var empid  = this_.attr("data-id");
            $.ajax({
                url:PECO.base_url()+'hris/disapproveempincsal',
                type:'post',
                data:{"empid":empid , "groupid" : dataid},
                dataType:'json'
            }).done(function (d) {
                PECO.initAlerts(d.msg,"PECO.net",d.func);
                if(d.qry == true){
                    fetchpceoempsalaryinc(dataid);
                }

            }).fail(function () {
                PECO.phpError();
            });

        });
        $(document).on('click','#pceosaveempsalaryinc',function () {

            swal({
                title: "Are you sure?",
                text: "Salary increase will be applied.",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes, Process!",
                closeOnConfirm: false,
                closeOnCancel: false,
                showLoaderOnConfirm: true
            }, function(isConfirm){
                if (isConfirm) {
                    $.ajax({
                        url:PECO.base_url()+'hris/pceoapprovesalaryinc',
                        type:'post',
                        data:{"groupid":dataid},
                        dataType:'json'
                    }).done(function (d) {
                        swal("Save!", d.msg, d.func);
                        if(d.qry == true){
                            fetchpceoempsalaryinc(dataid);
                        }
                    }).fail(function () {
                        PECO.phpError();
                    });
                } else {
                    swal("Cancelled", "Processing canceled", "error");
                }
            });


        });
    };

    var pceoview = function(dataid){
        fetchpceoempsalaryinc(dataid);
        init_events(dataid);
    };

    return{
        init:function(dataid){
            init_main(dataid);
        },
        pceoview:function(dataid){
            pceoview(dataid);
        }
    }
}();
