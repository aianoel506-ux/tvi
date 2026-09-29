var CONTRIBUTION =  function () {

    PECO.getHighlightsPlugin();
    PECO.getSelect2Plugins();

    var conttable = $(document).find('#conttable');
    var employeeconttbl = $(document).find('#employeeconttbl');
    var contribs_tbl = $(document).find('#tbl_contribs');
    var earnings_tbl = $(document).find('#tbl_earnings');

    var init_contribution = function(){

        var defaultconttype = $('#tabber').find('li.active a').attr('data-id');
        PECO.select2Basic($('#conttype' , document) , 'payroll/getpayrollcontributions' , 'Select Contributions' , false,false,false);
        PECO.select2Basic($('#deletiontype' , document) , 'payroll/getpayrollcontributions' , 'Select Contributions' , false,false,false);
        PECO.select2Basic($('#deletionyear' , document) , 'systems/select2year' , 'Select Year' , false,false,false);
        PECO.select2Basic($('#monthcont' , document) , 'systems/select2month' , 'Select Month' , false,false,false);
        PECO.select2Basic($('#monthdeletion' , document) , 'systems/select2month' , 'Select Month' , false,false,false);
        PECO.select2Basic($('#yearcont' , document) , 'systems/select2year' , 'Select Year' , false,false,false);

        init_conttable(defaultconttype);
        loademployeeconttbl();
        init_events();
        tbl_contribs();
        PECO.DTDefault(earnings_tbl, "Please select from contribution list...");
    };

    var loademployeeconttbl = function () {
        $.ajax({
            url:PECO.base_url()+'payroll/getempconttbl',
            type:'post',
            dataType:'json'
        }).done(function (data) {
            employeeconttbl.dataTable().empty();
            employeeconttbl.dataTable({
                bDestroy: true,
                bPaginate: true,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                bProcessing: true,
                aaData: data.empcontdata,
                aoColumns: [
                    {"data":"num"},
                    {"data":"emp" },
                    {"data":"type"}
                ],
                searchHighlight: true
            });
        }).fail(function () {
            PECO.phpError();
        });
    };

    var init_events = function(){
        $(document).on('submit','#submitempcont' , function (e) {
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
                  //  $(document).find('#employeecont').select2('val','');
                    $(document).find('#conttype').select2('val','');
                    loademployeeconttbl();
                }
            }).fail(function () {
                PECO.phpError();
            });
        });
        $(document).on('click','#deleteratesbtn' , function () {
            var deletiontype = $(document).find('#deletiontype').val();
            var deletionyear = $(document).find('#deletionyear').val();
            var monthdeletion = $(document).find('#monthdeletion').val();
            if(deletiontype != '' && deletionyear != ''){

                swal({
                    title: "Are you sure?",
                    text: "Rates will be deleted",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Yes, Delete!",
                    closeOnConfirm: false,
                    closeOnCancel: false,
                    showLoaderOnConfirm: true
                }, function(isConfirm) {
                    if (isConfirm) {
                        $.ajax({
                            url:PECO.base_url()+'payroll/deletecontrates',
                            type:'post',
                            data:{"type" : deletiontype , "year" : deletionyear , "months" : monthdeletion},
                            dataType:'json'
                        }).done(function (data) {
                            swal("PECO" , data.msg , data.func);
                            if(data.qry == true){
                                $(document).find('#deletiontype').select2('val' , '');
                                $(document).find('#deletionyear').select2('val' , '');
                                $(document).find('#monthdeletion').select2('val' , '');
                            }
                        }).fail(function () {
                            PECO.phpError();
                        });
                    }else{
                        swal.close();
                    }
                });
            }else{
                PECO.initAlerts("Please select type/year","Empty","info")
            }
        });

        $(document).on('submit','#submitcontribution',function (e) {
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
                    $('#fromrange').val('');
                    $('#torange').val('');
                    $('#monthlysalcredit').val('');
                    $('#ercont').val('');
                    $('#eecont').val('');
                    $('#totalcont').val('');
                }
            }).fail(function () {
                PECO.phpError();
            });
        });

        $(document).on('submit','#submitcontribrates',function (e) {
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
                    var defaultconttype = $('#tabber').find('li.active a').attr('data-id');
                    init_conttable(defaultconttype);
                }
            }).fail(function () {
                PECO.phpError();
            });
        });

        $(document).on('click','.conttype',function () {
            var this_ = $(this);
            var conttype = this_.attr('data-id');
            init_conttable(conttype);

        });
        $(document).on('click','#delbtn',function (e) {
            e.preventDefault();
            var this_ = $(this);
            var dataid = this_.attr('data-id');

            $.SmartMessageBox({
                title: "<i class='fa fa-question fa-fw fa-lg txt-color-yellow'></i> Are you sure you want to delete this item?</span>",
                content: 'Please confirm action taken!',
                buttons: '[No][Yes]'
            }, function (ButtonPressed) {
                if (ButtonPressed === "Yes") {
                    $.ajax({
                        url:PECO.base_url()+'payroll/deletecontribution',
                        type:"post",
                        data:{"dataid":dataid},
                        dataType:"json"
                    }).done(function (d) {
                        if(d.qry == true){
                            PECO.initAlerts(d.msg , 'PECO.net' , d.func);
                            var defaultconttype = $('#tabber').find('li.active a').attr('data-id');
                            init_conttable(defaultconttype);
                        }
                    }).fail(function () {
                        PECO.phpError();
                    });
                }
            });

        });
        
        contribs_tbl.on('click','tr td #contrib_radio',function () {
            var this_ = $(this);
            var typesid = this_.val();
            tbl_earnings(typesid);
        });

        earnings_tbl.on('change','tr td #earnings_',function () {
            var this_ = $(this);
            var typesid = this_.attr('data-id');
            var earningid = this_.val();
            var status = this_.attr('data-status');
            swal({
                title: "Are you sure?",
                text: 'Affected Earnings will be updated',
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes",
                closeOnConfirm: false,
                closeOnCancel: true,
                showLoaderOnConfirm: true
            }, function(isConfirm) {
                if (isConfirm) {

                    $.ajax({
                        url:PECO.base_url()+'payroll/updateaddmatrix',
                        type:'post',
                        data:{
                            typesid : typesid,
                            earningid : earningid,
                            status : status,
                        },
                        dataType:'json'
                    }).done(function (d) {
                        swal(d.msg, "Earnings Matrix!", d.func);
                        tbl_earnings(typesid);
                    }).fail(function () {
                        PECO.phpError();
                    });
                }else{
                    tbl_earnings(typesid);
                }
            });
        })
        
    };
    var init_conttable = function(conttype){

        $.ajax({
            url:PECO.base_url()+"payroll/getconttypetable",
            type:"post",
            data:{"conttype":conttype},
            dataType:"json",
            beforeSend: function(){
                conttable.dataTable().empty();
                PECO.DTphpLoading(conttable, 'Loading... ');
            }
        }).done(function (data) {
            conttable.dataTable().empty();
            conttable.dataTable({
                bDestroy: true,
                bPaginate: true,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                bProcessing: true,
                aaData: data.contributiondata,
                aoColumns: [
                    {"data":"num"},
                    {"data":"base" , sClass:'number'},
                    {"data":"min" , sClass:'number'},
                    {"data":"max" , sClass:'number'},
                    {"data":"amtcont", sClass:'number'},
                    {"data":"rateemployee", sClass:'number'},
                    {"data":"rateemployer" , sClass:'number'},
                    {"data":"var"},
                    {"data":"types"},
                    {"data":"datecreated"},
                    {"data":"createdby"},
                    {"data":"control", sWidth: '50px'}
                ],
                searchHighlight: true
            });
        });
    };

    var tbl_contribs = function () {
        $.ajax({
            url: PECO.base_url() + 'payroll/tblcontribs',
            type: 'post',
            dataType: 'json',
        }).done(function (d) {
            contribs_tbl.dataTable().empty();
            contribs_tbl.dataTable({
                bDestroy: true,
                bPaginate: false,
                bFilter: false,
                bInfo: true,
                bStateSave: true,
                bProcessing: true,
                aaData: d.contribs_list,
                aoColumns: [
                    {"data":"name"},
                    {"data":"select", sClass: 'center'},
                ],
                searchHighlight: false
            });
        });
    };

    var tbl_earnings = function (id) {
        $.ajax({
            url: PECO.base_url() + 'payroll/tblearnings',
            type: 'post',
            dataType: 'json',
            data: {id: id},
            beforeSend: function () {
                PECO.DTphpLoading(earnings_tbl, 'Fetching earnings table..');
            }
        }).done(function (d) {
            earnings_tbl.dataTable().empty();
            earnings_tbl.dataTable({
                bDestroy: true,
                bPaginate: false,
                bFilter: false,
                bInfo: false,
                bSort: true,
                bStateSave: true,
                bProcessing: true,
                aaData: d.earnings_list,
                aoColumns: [
                    {"data":"select", sClass: 'center'},
                    {"data":"name"},
                    {"data":"desc"},
                ],
                searchHighlight: false
            });
        });
    };

    return{
        init:function(){
            init_contribution();
        }
    }
}();