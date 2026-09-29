var HR_PAYROLL_DATA_ENTRY = function(){

    var  confidentialtable = $('#confidentialtable',document);
    var rankandfiletable  = $('#rankandfiletable',document);
    var tierd1table  = $('#tierd1table',document);
    var tierd2table  = $('#tierd2table',document);
    var encodedtable  = $('#encodedtable',document);
    var payrollemployeetbl  = $('#payrollemployeetbl',document);

    var init_main = function(){

        PECO.select2Basic($('#payrollemployee',document) , 'hris/getemployees' , 'Employee' , false,false,false);

        $(document).on('change','#tierd2month',function () {
            fetchtierd2();
        });
        $(document).on('change','#tierd2year',function () {
            fetchtierd2();
        });
        $(document).on('change','#tierd2typehalf',function () {
            fetchtierd2();
        });
        $(document).on('change','#tierd1month',function () {
            fetchtierd1();
        });
        $(document).on('change','#tierd1year',function () {
            fetchtierd1();
        });
        $(document).on('change','#tierd1typehalf',function () {
            fetchtierd1();
        });
        $(document).on('change','#ranknfilemonth',function () {
            fetchrankandfile();
        });
        $(document).on('change','#ranknfileyear',function () {
            fetchrankandfile();
        });
        $(document).on('change','#typehalf',function () {
            fetchrankandfile();
        });

        $(document).on('change','#confidentialmonth',function () {
            fetchconfidential();
        });
        $(document).on('change','#confidentialyear',function () {
            fetchconfidential();
        });

        $(document).on('submit','#submitpayrollnewemployee' , function (e) {
            e.preventDefault();
            var this_ = $(this);

            swal({
                title: "Are you sure you want to add this employee?",
                text: 'Add Payroll Employee',
                type: "info",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes",
                closeOnConfirm: false,
                closeOnCancel: true,
                showLoaderOnConfirm: true
            }, function(isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url:this_.attr("action"),
                        type:this_.attr("method"),
                        data:this_.serialize(),
                        dataType:'json'
                    }).done(function (data) {
                        swal("Payroll Employee" , data.msg , data.func);
                        if(data.qry == true){
                            loadpayrollemployee();
                        }
                    }).fail(function () {
                        swal.close();
                        PECO.phpError();
                    });
                }
            });


        });

        $(document).on('click','#payrollempremovebtn' , function () {
            var this_ = $(this);
            var dataid = this_.attr("data-id");


            swal({
                title: "Are you sure?",
                text: "Remove employee from the payroll.",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes, Remove!",
                closeOnConfirm: false,
                closeOnCancel: false,
                showLoaderOnConfirm: true
            }, function(isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url:PECO.base_url()+'payroll/removepayrollemp',
                        type:'post',
                        data:{"dataid" : dataid},
                        dataType:'json'
                    }).done(function (data) {
                        swal("PECO" , data.msg , data.func);
                        if(data.qry == true){
                            loadpayrollemployee();
                        }
                    }).fail(function () {
                        PECO.phpError();
                    });
                }else{
                    swal.close();
                }
            });

        });

        $(document).on('submit','#submitencoded' , function (e) {
            e.preventDefault();
            var this_ = $(this);
            $.ajax({
                url:this_.attr("action"),
                type:this_.attr("method"),
                data:this_.serialize(),
                dataType:'json'
            }).done(function (data) {
                encodedtable.dataTable().empty();
                encodedtable.dataTable({
                    bDestroy: true,
                    bPaginate: true,
                    bFilter: true,
                    bInfo: true,
                    bStateSave: true,
                    bProcessing: true,
                    aaData: data.encodeddata,
                    aoColumns: [
                        {"data":"num"},
                        {"data":"name" , sWidth:'200px'},
                        {"data":"type" },
                        {"data":"inserted", sClass:'number'},
                        {"data":"computed", sClass:'number'},
                        {"data":"createdby"},
                        {"data":"datecreated"},
                        {"data":"stat"}
                    ],
                    searchHighlight: true
                });
            }).fail(function () {
                PECO.phpError();
            });
        });

        $(document).on('shown.bs.tab', 'a[data-toggle="tab"]', function (e) {
            var target = $(e.target).attr("href");
            if (target == '#encoded') {
               PECO.select2Basic($('#monthencoded',document) , 'systems/select2month' , 'Select Month' , false,false,false);
               PECO.select2Basic($('#yearencoded',document) , 'systems/select2year' , 'Select Year' , false,false,false);
               $('#payclassencoded',document).select2({"allowClear":true});
               $('#paytypeencoded',document).select2({"allowClear":true});
            }
        });

        $(document).on('click','#savepayrollbtn' , function () {
            var typehalf = $(document).find('#typehalf').val();

            swal({
                title: "Are you sure you want to save this payroll?",
                text: 'Payroll can be reverted via administrator\'s process!',
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
                       url:PECO.base_url()+'payroll/savepayrollentry',
                       type:'post',
                       data:{"typehalf" : typehalf},
                       dataType:'json'
                   }).done(function (data) {
                       swal.close();
                       PECO.initAlerts(data.msg , "PECO" , data.func);
                   }).fail(function () {
                       PECO.phpError();
                   });
                }else{
                    swal.close();
                }
            });
        });

        $(document).on('shown.bs.tab', 'a[data-toggle="tab"]', function (e) {
            var target = $(e.target).attr("href");
            if (target == '#confidential') {
                fetchconfidential();
            }else if(target == '#rankandfile'){
                fetchrankandfile();
            }else if(target == '#tierd1'){
                fetchtierd1();
            }else if(target == '#tierd2'){
                fetchtierd2();
            }
        });

        $(document).on('click','#fetchrankandfiletransactions' , function (e) {
            e.preventDefault();
            var  ranknfilemonth= $(document).find('#ranknfilemonth').val();
            var ranknfileyear= $(document).find('#ranknfileyear').val();
            var typehalf= $(document).find('#typehalf').val();
            var d  = new Date();
            var today = d.getDate();

            if(ranknfilemonth == ''){
                PECO.initAlerts("Select Month","PECO","info");
            }else if(ranknfileyear == ''){
                PECO.initAlerts("Select Year","PECO","info");
            }else if(typehalf == ''){
                PECO.initAlerts("Select Type Half","PECO","info");
            }else{
                fetchrankandfile();
            }
        });

        $(document).on('click','#fetchtransactionbtn' , function () {


            if(confidentialmonth == ''){
                PECO.initAlerts("Select Month","PECO","info");
            }else if(confidentialyear == ''){
                PECO.initAlerts("Select Year","PECO","info");
            }else{
                fetchconfidential();
            }
        });

        var d  = new Date();
        var defaultmonth = d.getMonth() + 1;
        var defaultyear = d.getFullYear();
        var today = d.getDate();

        if(today > 15){
            $(document).find('#typehalf').val(2);
            $(document).find('#tierd1typehalf').val(2);
            $(document).find('#tierd2typehalf').val(2);
        }else{
            $(document).find('#typehalf').val(1);
            $(document).find('#tierd1typehalf').val(1);
            $(document).find('#tierd2typehalf').val(1);
        }

        $(document).find('#ranknfilemonth').val(defaultmonth);
        $(document).find('#ranknfileyear').val(defaultyear);
        $(document).find('#confidentialmonth').val(defaultmonth);
        $(document).find('#confidentialyear').val(defaultyear);
        $(document).find('#tierd1month').val(defaultmonth);
        $(document).find('#tierd1year').val(defaultyear);
        $(document).find('#tierd2month').val(defaultmonth);
        $(document).find('#tierd2year').val(defaultyear);


        loadpayrollemployee();
        fetchrankandfile();
        fetchconfidential();
        fetchtierd1();
        fetchtierd2();
        init_events(defaultmonth , defaultyear);

    };

    var loadpayrollemployee = function () {
        $.ajax({
            url:PECO.base_url()+'hris/getpayrollemployee',
            type:'post',
            dataType:'json'
        }).done(function (data) {
            payrollemployeetbl.dataTable().empty();
            payrollemployeetbl.dataTable({
                bDestroy: true,
                bPaginate: true,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                bProcessing: true,
                aaData: data.payrollemplist,
                aoColumns: [
                    {"data":"num"},
                    {"data":"lastname"},
                    {"data":"firstname"},
                    {"data":"accntno"},
                    {"data":"payclass"},
                    {"data":"group"},
                    {"data":"status"},
                    {"data":"control"},
                ],
                searchHighlight: true
            });
        }).fail(function () {
            PECO.phpError();
        });
    };

    var appliholiday  = function(noofydays,payclass,month,year,typehalf){
        var halftype = (typehalf) ? typehalf : false;
        
        // Validate inputs before sending
        if(!noofydays || noofydays <= 0) {
            PECO.initAlerts('Please enter a valid number of days.', 'Validation Error', 'warning');
            return;
        }
        
        if(!payclass) {
            PECO.initAlerts('Payclass is required.', 'Validation Error', 'warning');
            return;
        }
        
        if(!month || !year) {
            PECO.initAlerts('Month and year are required.', 'Validation Error', 'warning');
            return;
        }
        
        $.ajax({
            url:PECO.base_url()+'payroll/applyholiday',
            type:'post',
            data:{"noofdays":noofydays,"payclass":payclass,"month":month,"year":year,"half":halftype},
            dataType:'json'
        }).done(function (d) {
            if(d && d.qry !== undefined) {
                swal("PECO", d.msg, d.func);
                if(d.qry == true){
                    $(document).find('#noofholidaysranknfile').val('');
                    $(document).find('#noofholidaysconfidential').val('');
                    $(document).find('#noofholidaystierd1').val('');
                    $(document).find('#noofholidaystierd2').val('');
                }
            } else {
                PECO.initAlerts('Invalid response from server.', 'Error: PHP / Ajax', 'error');
            }
        }).fail(function (xhr, status, error) {
            console.log('AJAX Error:', xhr.responseText);
            console.log('Status:', status);
            console.log('Error:', error);
            
            var errorMsg = 'There was an error processing the holiday application.';
            if(xhr.responseText) {
                try {
                    var response = JSON.parse(xhr.responseText);
                    if(response.msg) {
                        errorMsg = response.msg;
                    }
                } catch(e) {
                    errorMsg += ' Server response: ' + xhr.responseText.substring(0, 200);
                }
            }
            
            PECO.initAlerts(errorMsg, "Error: PHP / Ajax", "error");
        });
    };

    var init_events = function(defaultmonth , defaultyear){


        $(document).on('submit','#submitannualtax',function (e) {
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
                    $('#submitannualtax')[0].reset();
                    $('#month').select2('val','');
                    $('#year').select2('val','');
                    $('#employees').select2('val','');
                }
            }).fail(function () {
                PECO.phpError();
            });
        });

        $(document).on('click','#applyholidaybtn',function () {
            var this_ = $(this);

            var datapayclass = this_.attr("data-payclass");
            var noofdays = '';
            var month, year, half;



            if(datapayclass == 129){
                var confidentialmonth = $('#confidentialmonth',document).val();
                var confidentialyear = $('#confidentialyear',document).val();
                noofdays = $(document).find('#noofholidaysconfidential').val();
                month = confidentialmonth;
                year = confidentialyear;
                half = 1; // Set default half for confidential

            }else if(datapayclass == 128){
                var ranknfilemonth = $('#ranknfilemonth',document).val();
                var ranknfileyear = $('#ranknfileyear',document).val();
                var typehalf = $('#typehalf',document).val();
                noofdays = $(document).find('#noofholidaysranknfile').val();
                month = ranknfilemonth;
                year = ranknfileyear;
                half = typehalf;
            }else if(datapayclass == 3077){
                var tierd1month = $(document).find('#tierd1month').val();
                var tierd1year = $(document).find('#tierd1year').val();
                var tierd1typehalf = $(document).find('#tierd1typehalf').val();
                noofdays = $(document).find('#noofholidaystierd1').val();
                month = tierd1month;
                year = tierd1year;
                half = tierd1typehalf;
            }else if(datapayclass == 3078){
                var tierd2month = $(document).find('#tierd2month').val();
                var tierd2year = $(document).find('#tierd2year').val();
                var tierd2typehalf = $(document).find('#tierd2typehalf').val();
                noofdays = $(document).find('#noofholidaystierd2').val();
                month = tierd2month;
                year = tierd2year;
                half = tierd2typehalf;
            }

           if(noofdays == ''){
               PECO.initAlerts("Enter No. of days.","PECO","info");
           }else if(month == '' || year == ''){
               PECO.initAlerts("Please select month and year.","PECO","info");
           }else if((datapayclass == 128 || datapayclass == 3077 || datapayclass == 3078) && (half == '' || half == undefined)){
               PECO.initAlerts("Please select half period.","PECO","info");
           }else{
               swal({
                   title: "Are you sure?",
                   text: "Holidays will be applied to all employees in this payclass.",
                   type: "warning",
                   showCancelButton: true,
                   confirmButtonClass: "btn-danger",
                   confirmButtonText: "Yes, Process!",
                   closeOnConfirm: false,
                   closeOnCancel: false,
                   showLoaderOnConfirm: true
               }, function(isConfirm){
                   if (isConfirm) {
                       appliholiday(noofdays, datapayclass, month, year, half);
                   } else {
                       swal("Cancelled", "Processing canceled", "error");
                   }
               });

           }
        });

        $('a[href="#rankandfile"][data-toggle="tab"]').on('shown.bs.tab', function (e) {

        });
        $('a[href="#confidential"][data-toggle="tab"]').on('shown.bs.tab', function (e) {

        });
        confidentialtable.on('keypress','#confidentialinput',function (e) {
            var keycode = (e.keyCode ? e.keyCode : e.which);
            if(keycode == '13'){
                var this_ = $(this);
                var this_val = this_.val();
                if(this_val == ''){
                    PECO.initAlerts("No value","PECO","info");
                }else{
                    var this_dataid = this_.attr("data-id");
                    var amount  = this_.val();
                    var empid = this_.attr("data-empid");
                    var month = $(document).find('#confidentialmonth').val();
                    var year = $(document).find('#confidentialyear').val();
                    var paytype = $(document).find('#confipaytype').val();

                    var payclass = this_.attr("data-payclass");


                    $.ajax({
                        url:PECO.base_url()+'payroll/addpayrolltransactions',
                        type:'post',
                        data:{"amt":amount,"empid":empid,"month":month,"payclass":payclass,"payspec":paytype,"paytype":paytype,
                            "type":this_dataid,"year":year},
                        dataType:'json'
                    }).done(function () {
                        PECO.initAlerts("Transaction has been saved","Payroll Transaction","success");
                    }).fail(function () {
                        PECO.phpError();
                    });
                }

            }
        });

        rankandfiletable.on('keypress','#ranknfileinput',function (e) {
            var keycode = (e.keyCode ? e.keyCode : e.which);
            if(keycode == '13'){
               var this_ = $(this);
                var this_val = this_.val();
                if(this_val == ''){
                    PECO.initAlerts("No value","PECO","info");
                }else{
                    var this_dataid = this_.attr("data-id");
                    var amount  = this_.val();
                    var empid = this_.attr("data-empid");
                    var month = $(document).find('#ranknfilemonth').val();
                    var year = $(document).find('#ranknfileyear').val();
                    var paytype = $(document).find('#typehalf').val();

                    var payclass = this_.attr("data-payclass");

                    $.ajax({
                        url:PECO.base_url()+'payroll/addpayrolltransactions',
                        type:'post',
                        data:{"amt":amount,"empid":empid,"month":month,"payclass":payclass,"payspec":paytype,"paytype":paytype,
                            "type":this_dataid,"year":year},
                        dataType:'json'
                    }).done(function () {
                        PECO.initAlerts("Transaction has been saved","Payroll Transaction","success");
                    }).fail(function () {
                        PECO.phpError();
                    });
                }

            }
        });
        tierd1table.on('keypress','#tierd1input',function (e) {
            var keycode = (e.keyCode ? e.keyCode : e.which);
            if(keycode == '13'){
               var this_ = $(this);
                var this_val = this_.val();
                if(this_val == ''){
                    PECO.initAlerts("No value","PECO","info");
                }else{
                    var this_dataid = this_.attr("data-id");
                    var amount  = this_.val();
                    var empid = this_.attr("data-empid");
                    var month = $(document).find('#tierd1month').val();
                    var year = $(document).find('#tierd1year').val();
                    var paytype = $(document).find('#tierd1typehalf').val();

                    var payclass = this_.attr("data-payclass");

                    $.ajax({
                        url:PECO.base_url()+'payroll/addpayrolltransactions',
                        type:'post',
                        data:{"amt":amount,"empid":empid,"month":month,"payclass":payclass,"payspec":paytype,"paytype":paytype,
                            "type":this_dataid,"year":year},
                        dataType:'json'
                    }).done(function () {
                        PECO.initAlerts("Transaction has been saved","Payroll Transaction","success");
                    }).fail(function () {
                        PECO.phpError();
                    });
                }

            }
        });
        tierd2table.on('keypress','#tierd2input',function (e) {
            var keycode = (e.keyCode ? e.keyCode : e.which);
            if(keycode == '13'){
               var this_ = $(this);
                var this_val = this_.val();
                if(this_val == ''){
                    PECO.initAlerts("No value","PECO","info");
                }else{
                    var this_dataid = this_.attr("data-id");
                    var amount  = this_.val();
                    var empid = this_.attr("data-empid");
                    var month = $(document).find('#tierd2month').val();
                    var year = $(document).find('#tierd2year').val();
                    var paytype = $(document).find('#tierd2typehalf').val();

                    var payclass = this_.attr("data-payclass");

                    $.ajax({
                        url:PECO.base_url()+'payroll/addpayrolltransactions',
                        type:'post',
                        data:{"amt":amount,"empid":empid,"month":month,"payclass":payclass,"payspec":paytype,"paytype":paytype,
                            "type":this_dataid,"year":year},
                        dataType:'json'
                    }).done(function () {
                        PECO.initAlerts("Transaction has been saved","Payroll Transaction","success");
                    }).fail(function () {
                        PECO.phpError();
                    });
                }

            }
        });
        PECO.select2Basic($('#confidentialmonth',document) , 'payroll/getconfidentialmonth' , 'Select Month',false,false , defaultmonth);
        PECO.select2Basic($('#ranknfilemonth',document) , 'payroll/getranknfilemonth' , 'Select Month',false,false , defaultmonth);
        PECO.select2Basic($('#confidentialyear',document) , 'payroll/getconfidentialyear' , 'Select Year',false,false , defaultyear);
        PECO.select2Basic($('#ranknfileyear',document) , 'payroll/getranknfileyear' , 'Select Year',false,false,defaultyear);
        PECO.select2Basic($('#tierd1year',document) , 'payroll/getranknfileyear' , 'Select Year',false,false,defaultyear);
        PECO.select2Basic($('#tierd2year',document) , 'payroll/getranknfileyear' , 'Select Year',false,false,defaultyear);
        PECO.select2Basic($('#tierd1month',document) , 'payroll/getranknfilemonth' , 'Select Month',false,false , defaultmonth);
        PECO.select2Basic($('#tierd2month',document) , 'payroll/getranknfilemonth' , 'Select Month',false,false , defaultmonth);

        $('#typehalf',document).select2({
            "allowClear":true
        });
        $('#tierd2typehalf',document).select2({
            "allowClear":true
        });
        $('#tierd1typehalf',document).select2({
            "allowClear":true
        });

    };

    var capitalizeWord = function capitalizeFirstLetter(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    };
    var fetchconfidential = function(){

        var confidentialmonth = $(document).find('#confidentialmonth').val();
        var confidentialyear = $(document).find('#confidentialyear').val();
        var confipaytype = $(document).find('#confipaytype').val();

        $.ajax({
            url:PECO.base_url()+'payroll/fetchconfidential',
            type:'post',
            data:{"month":confidentialmonth,"year":confidentialyear , "paytype" : confipaytype},
            dataType:'json'
        }).done(function (d) {

            if(d.approve == true){
              //  $(document).find('#confipayrollencodestatus').html("Status : <span class=\"label label-sm label-success\"> Approved </span>");
            }

            $('#confidentialtable tbody', document).html('');
            $('#confidentialtable tr', document).html('');
            if(d.columns.length>0){
                for(th = 0; th<d.columns.length; th++) {
                    $('#confidentialtable thead tr', document).append('<th class="dynamic '+d.columns[th]['sClass']+'">'+d.columns[th]['text']+'</th>');
                }
            }
            var tables = $('#confidentialtable').DataTable({
                bDestroy: true,
                info: true,
                scrollY: "250px",
                scrollX: true,
                scrollCollapse: true,
                paging: false,
                saveState: true,
                fixedColumns: {
                    leftColumns: 1
                },
                searchHighlight: true,
                aoColumns: d.columns,
                bStateSave: true,
                bProcessing: true,
                aaData:d.confidentialdata,
                fnRowCallback: function(nRow, Data) {
                    $('.tooltips', nRow).tooltip();
                }
            });

        }).fail(function () {
            PECO.phpError();
        });
    };
    var fetchrankandfile = function(){


        var defaultmonth = $(document).find('#ranknfilemonth').val();
        var defaultyear = $(document).find('#ranknfileyear').val();
        var typehalf = $(document).find('#typehalf').val();

        $.ajax({
            url:PECO.base_url()+'payroll/fetchrankandfile',
            type:'post',
            data:{"month":defaultmonth,"year":defaultyear , "typehalf":typehalf},
            dataType:'json'
        }).done(function (d) {
            if(d.approve == true){
                $(document).find('#rnfpayrollencodestatus').html("Status : <span class=\"label label-sm label-success\"> Approved </span>");
            }else{
                $(document).find('#rnfpayrollencodestatus').html("Status : <span class=\"label label-sm label-info\"> Draft </span>");
            }
            $('#rankandfiletable tbody', document).html('');
            $('#rankandfiletable tr', document).html('');
            if(d.columns.length>0){
                var th= 0;
                for(th = 0; th<d.columns.length; th++) {
                    $('#rankandfiletable thead tr', document).append('<th class="dynamic '+d.columns[th]['sClass']+'">'+d.columns[th]['text']+'</th>');
                }
            }
            var tables = $('#rankandfiletable').DataTable({
                bDestroy: true,
                info: true,
                scrollY: "42vh",
                scrollX: true,
                scrollCollapse: true,
                paging: false,
                saveState: true,
                fixedColumns: {
                    leftColumns: 1
                },
                searchHighlight: true,
                aoColumns: d.columns,
                bStateSave: true,
                bProcessing: true,
                aaData:d.rankandfiledata,
                fnRowCallback: function(nRow, Data) {
                    $('.tooltips', nRow).tooltip();
                }
            });
        }).fail(function () {
            PECO.phpError();
        });
    };
    var fetchtierd1 = function(){

        var tierd1month = $(document).find('#tierd1month').val();
        var tierd1year = $(document).find('#tierd1year').val();
        var tierd1typehalf = $(document).find('#tierd1typehalf').val();

        $.ajax({
            url:PECO.base_url()+'payroll/fetchtierd1',
            type:'post',
            data:{"month":tierd1month,"year":tierd1year , "tierd1typehalf":tierd1typehalf},
            dataType:'json'
        }).done(function (d) {
            // Debug: Log the response to console
            console.log('Tier 1 Response:', d);
            
            if(d.dataempcount > 0){
                if(d.approve == true){
                    // $(document).find('#rnfpayrollencodestatus').html("Status : <span class=\"label label-sm label-success\"> Approved </span>");
                }
                $('#tierd1table tbody', document).html('');
                $('#tierd1table tr', document).html('');
                if(d.columns.length>0){
                    var th= 0;
                    for(th = 0; th<d.columns.length; th++) {
                        $('#tierd1table thead tr', document).append('<th class="dynamic '+d.columns[th]['sClass']+'">'+d.columns[th]['text']+'</th>');
                    }
                }

                var tables = $('#tierd1table').DataTable({
                    bDestroy: true,
                    info: true,
                    scrollY: "250px",
                    scrollX: true,
                    scrollCollapse: true,
                    paging: false,
                    saveState: true,
                    fixedColumns: {
                        leftColumns: 1
                    },
                    searchHighlight: true,
                    aoColumns: d.columns,
                    bStateSave: true,
                    bProcessing: true,
                    aaData:d.tierd1data,
                    fnRowCallback: function(nRow, Data) {
                        $('.tooltips', nRow).tooltip();
                    }
                });
            } else {
                console.log('No Tier 1 employees found. Debug info:', d.debug_final);
                // Show a message to the user
                $('#tierd1table tbody', document).html('<tr><td colspan="100%">No Tier 1 employees found for the selected criteria. Check console for debug information.</td></tr>');
            }

        }).fail(function (xhr, status, error) {
            console.log('Tier 1 AJAX Error:', xhr.responseText);
            PECO.phpError();
        });
    };
    var fetchtierd2 = function(){

        var tierd2month = $(document).find('#tierd2month').val();
        var tierd2year = $(document).find('#tierd2year').val();
        var tierd2typehalf = $(document).find('#tierd2typehalf').val();

        $.ajax({
            url:PECO.base_url()+'payroll/fetchtierd2',
            type:'post',
            data:{"month":tierd2month,"year":tierd2year , "tierd2typehalf":tierd2typehalf},
            dataType:'json'
        }).done(function (d) {
            if(d.empcount > 0){
                if(d.approve == true){
                    // $(document).find('#rnfpayrollencodestatus').html("Status : <span class=\"label label-sm label-success\"> Approved </span>");
                }
                $('#tierd2table tbody', document).html('');
                $('#tierd2table tr', document).html('');
                if(d.columns.length>0){
                    var th= 0;
                    for(th = 0; th<d.columns.length; th++) {
                        $('#tierd2table thead tr', document).append('<th class="dynamic '+d.columns[th]['sClass']+'">'+d.columns[th]['text']+'</th>');
                    }
                }
                var tables = $('#tierd2table').DataTable({
                    bDestroy: true,
                    info: true,
                    scrollY: "250px",
                    scrollX: true,
                    scrollCollapse: true,
                    paging: false,
                    saveState: true,
                    fixedColumns: {
                        leftColumns: 1
                    },
                    searchHighlight: true,
                    aoColumns: d.columns,
                    bStateSave: true,
                    bProcessing: true,
                    aaData:d.tierd2data,
                    fnRowCallback: function(nRow, Data) {
                        $('.tooltips', nRow).tooltip();
                    }
                });
            }

        }).fail(function () {
            PECO.phpError();
        });
    };

    return{
        init:function(){
            init_main();
        }
    }
}();