/**
 * Created by IT on 2/27/2018.
 */

var  REQUEST = function () {

    PECO.getPulsate();

    var selectleavetype = $(document).find('#select2_leave_credit');
    var empid = $(document).find('#hiddenempid');
    var leavetype = $(document).find('#selectleavetype');
    var hiddenleavedays = $(document).find('#hiddenleavedays');
    var leaverequesttbl = $(document).find('#leaverequesttbl');
    var moduleid = $(document).find('#moduleid');
    var dataid = $(document).find('#dataid');
    var approvedleaverequest = $(document).find('#approvedleaverequest');

    var tbl_leave_requests = $(document).find('#tbl_leave_requests');



    var draft_leave_request = function(empid, stat){

        $.ajax({url:PECO.base_url()+"request/fetchpendingleaverequested",
            type:"post",
            dataType:"json",
            data: {'empid': empid, 'stat': stat},
            beforeSend: function(){
                tbl_leave_requests.dataTable().empty();
                PECO.DTphpLoading(tbl_leave_requests, 'Loading... ');
            }
        }).done(function (d) {

            $(document).find('#total_no_days').text(d.totalnodays);
            $(document).find('#total_no_hours').text(d.totalnohours);

            tbl_leave_requests.dataTable().empty();
            tbl_leave_requests.dataTable({
                bDestroy: true,
                bPaginate: true,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                bProcessing: true,
                aaData: d.pendingleaverequestedlist,
                aoColumns: [
                    {"data":"num"},
                    {"data":"leavetype"},
                    {"data":"fromdate"},
                    {"data":"todate"},
                    {"data":"hours"},
                    {"data":"status"},
                    {"data":"control"}
                ],
                searchHighlight: true
            });
        }).fail(function () {
            PECO.phpError();
        });
    };


    var init_request_fn = function(empid) {
        $(document).on('click','#del_btn',function (e) {
            e.preventDefault();
            var this_ = $(this);
            var dataid = this_.attr("data-id");
            var dataval = this_.attr("data-type-value");

            $.SmartMessageBox({
                title: "<i class='fa fa-question fa-fw fa-lg txt-color-yellow'></i>Are you sure you want to delete this request?</span>",
                content: 'Please confirm action taken!',
                buttons: '[Yes][No]'
            }, function (ButtonPressed) {
                if (ButtonPressed === "Yes") {
                    $.ajax({
                        url:PECO.base_url()+"request/deletependingrequest",
                        type:"post",
                        data:{"dataid":dataid,"status":dataval},
                        dataType:"json"
                    }).done(function (d) {

                        PECO.initAlerts(d.msg , "PECO.net" , d.func);

                        if(dataval == 303){
                            draft_leave_request(307);
                            init_employee_leave_credits($(document).find('#list_leave_credits'), empid.val());
                        }else if(dataval == 302){
                            draft_leave_request(empid,300);
                             init_employee_leave_credits($(document).find('#list_leave_credits'),$(document).find('#hiddenempid').val());
                        }

                    }).fail(function () {
                        PECO.phpError();
                    });
                }
            });
        });

        $(document).on('click','#approved_btn',function (e) {
            e.preventDefault();
            var this_ = $(this);
            var dataid = this_.attr('data-id');


            if(tbl_leave_requests.fnSettings().aoData.length===0) {
                PECO.initAlerts("No request to approved", 'Empty', 'info', false, false);
            } else {
                $.SmartMessageBox({
                    title: "<i class='fa fa-question fa-fw fa-lg txt-color-yellow'></i>Are you sure you want to approved this request?</span>",
                    content: 'Please confirm action taken!',
                    buttons: '[Yes][No]'
                }, function (ButtonPressed) {
                    if (ButtonPressed === "Yes") {
                        $.ajax({
                            url:PECO.base_url()+"request/approvedrequest",
                            type:"post",
                            data:{"dataid":dataid},
                            dataType:"json"
                        }).done(function (d) {
                            PECO.initAlerts(d.msg , "PECO.net" , d.func);
                            if(d.qry == true){
                                init_employee_leave_credits($(document).find('#list_leave_credits'), empid);
                            }
                            draft_leave_request(empid,300);
                        }).fail(function () {
                            PECO.phpError();
                        });
                    }
                });
            }
        });

        $(document).on('click','#disapproved_btn',function (e) {
            e.preventDefault();
            var this_ = $(this);
            var dataid = this_.attr('data-id');

            if(tbl_leave_requests.fnSettings().aoData.length===0) {
                PECO.initAlerts("No request to approved", 'Empty', 'info', false, false);
            } else {
                $.SmartMessageBox({
                    title: "<i class='fa fa-question fa-fw fa-lg txt-color-yellow'></i>Are you sure you want to disapproved this request?</span>",
                    content: 'Please confirm action taken!',
                    buttons: '[Yes][No]'
                }, function (ButtonPressed) {
                    if (ButtonPressed === "Yes") {
                        $.ajax({
                            url:PECO.base_url()+"request/disapprovedrequest",
                            type:"post",
                            data:{"dataid":dataid},
                            dataType:"json"
                        }).done(function (d) {
                            PECO.initAlerts(d.msg , "PECO.net" , d.func);
                            draft_leave_request(300);

                                $(document).find('#total_no_days').text("0");
                                $(document).find('#total_no_hours').text("0");
                                init_employee_leave_credits($(document).find('#list_leave_credits'),$(document).find('#hiddenempid').val());

                        }).fail(function () {
                            PECO.phpError();
                        });
                    }
                });
            }
        });
    };

    var init_approved_request = function(empid){
        //table approvedleaverequest
        $.ajax({
            url:PECO.base_url()+'hris/getapprovedrequest',
            type:'post',
            data:{"empid":empid},
            dataType:'json'
        }).done(function (d) {
            approvedleaverequest.dataTable().empty();
            approvedleaverequest.dataTable({
                bDestroy: true,
                bPaginate: true,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                bProcessing: true,
                aaData: d.approvereqdata,
                aoColumns: [
                    {"data":"num"},
                    {"data":"leavetype" ,sClass:'text-info'},
                    {"data":"from"},
                    {"data":"to"},
                    {"data":"reason"},
                    {"data":"status"}
                ],
                searchHighlight: true
            });
        }).fail(function () {
            PECO.phpError();
        });
    };

    var request_init = function (empid , year) {

        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            var target = $(e.target).attr("href");
            if (target == '#new') {
                init_employee_leave_credits($('#list_leave_credits'), empid);
            }else if(target == '#draft'){
                fetchdraftrequestleavetbl(empid , year);
            }else if(target == '#approved_disapproved'){

            }
        });

        $(document).on('click','#submitrequest',function (e) {
            e.preventDefault();

            if(tbl_leave_requests.fnSettings().aoData.length===0) {
                PECO.initAlerts("No request to submit", 'Empty', 'info', false, false);
            } else {
                $.SmartMessageBox({
                    title: "<i class='fa fa-question fa-fw fa-lg txt-color-yellow'></i>Submit Request?</span>",
                    content: 'Please confirm action taken!',
                    buttons: '[Yes][No]'
                }, function (ButtonPressed) {
                    if (ButtonPressed === "Yes") {

                        $.ajax({
                            url:PECO.base_url()+"request/submiteleaverequest",
                            type:"post",
                            data:{"empid":empid.val() , "moduleid":moduleid.val() , "dataid":dataid.val()},
                            dataType:"json"
                        }).done(function (d) {
                            draft_leave_request(307);

                            if(d.qry === true){
                                init_approved_request(empid.val());
                            }
                            PECO.initAlerts(d.msg,  "PECO.net" , d.func);

                            init_employee_leave_credits($(document).find('#list_leave_credits'), empid.val());
                        }).fail(function () {
                            PECO.phpError();
                        });
                    }
                });
            }
        });

        $(document).on('click','#resetbtn',function (e) {
            e.preventDefault();
            $(document).find('#totaldayleave').text("");
            $(document).find('#hiddenleavedays').val('');
            $(document).find('#reason').val('');
            $(document).find('#fromdate').val('mm/dd/yyyy');
            $(document).find('#todate').val('mm/dd/yyyy');
            selectleavetype.select2("val","");
            $('#nodays' , document).val('0');
            init_employee_leave_credits($(document).find('#list_leave_credits'), empid.val());
        });

        $('#submitleaveform').submit(function (e) {
            e.preventDefault();
            var this_ = $(this);
            var selectleavetype2 = $('#selectleavetype',document).val();
            var reason = $('#reason',document).val();
            var fromdate = $('#fromdate',document).val();
            var todate = $('#todate',document).val();
            var nohours = $('#nohours',document).val();

            if(selectleavetype2 == ''){
                PECO.initAlerts("Please select leave type","PECO","info");
            }else if(reason == ''){
                PECO.initAlerts("Please provide reason of leaving","PECO","info");
            }else if((fromdate == '' || todate == '') && nohours == ''){
                PECO.initAlerts("Please provide date or hours of leaving","PECO","info");
            }else{
                $.SmartMessageBox({
                    title: "<i class='fa fa-question fa-fw fa-lg txt-color-yellow'></i>Submit Leave Form?</span>",
                    content: 'Please confirm action taken!',
                    buttons: '[Yes][No]'
                }, function (ButtonPressed) {
                    if (ButtonPressed === "Yes") {
                        $.ajax({
                            url: this_.attr("action"),
                            type: this_.attr("method"),
                            data: this_.serialize(),
                            dataType:"json"
                        }).done(function (d) {
                            PECO.initAlerts(d.msg, "Leave Form",d.func);
                            $(document).find('#totaldayleave').text("");
                            $(document).find('#hiddenleavedays').val('');
                            $(document).find('#nohours').val('');
                            $(document).find('#nominutes').val('');
                            $(document).find('#reason').val('');
                            $(document).find('#fromdate').val('mm/dd/yyyy');
                            $(document).find('#todate').val('mm/dd/yyyy');
                            selectleavetype.select2("val","");
                            $('#nodays' , document).val('0');
                            init_employee_leave_credits($(document).find('#list_leave_credits'), empid);
                            $('.nav-tabs a[href="#list"]').trigger('click');
                            REQUEST.initreqtbl(d.empid, 307);
                        }).fail(function () {
                            PECO.phpError();
                        });
                    }
                });
            }
        });

        $(document).on('change','#fromdate',function () {
           /* if($('#todate',document).val() != ''){
                init_employee_leave_credits($(document).find('#list_leave_credits'), empid.val());
            } */
        });
        $(document).on('change','#todate',function () {
          /*  if($('#fromdate',document).val() != ''){
                init_employee_leave_credits($(document).find('#list_leave_credits'), empid.val());
            } */
        });
        $(document).on('keyup','#nohours',function () {
           // init_employee_leave_credits($(document).find('#list_leave_credits'), empid.val());
        });

        // PECO.select2Basic(selectleavetype, 'request/getleavetype', 'Select Leave Type..', false, false, false,false,false,empid);

        selectleavetype.change(function () {
            /*   $('#nodays' , document).val('0');
            var this_ = $(this);
            init_employee_leave_credits($(document).find('#list_leave_credits'), empid.val());
             $.ajax({
                    url:PECO.base_url()+"request/getleavenames",
                    type:"post",
                    data:{"id":this_.val()},
                    dataType:"json"
                }).done(function (d) {

                }).fail(function () {
                    PECO.phpError();
                }); */
        });


        $('#reason').maxlength({
            limitReachedClass: "label label-danger",
            alwaysShow: true,
            placement: 'bottom'
        });


        leave_requested_tbl();
    };

    var leave_requested_tbl = function(){
        var data_id = $(document).find('#data_id').val();
        $.ajax({
            url:PECO.base_url()+"request/tblleaveitem",
            type:"post",
            data:{"empid":empid.val(),"data_id" : data_id},
            dataType:"json",
            beforeSend: function(){
                leaverequesttbl.dataTable().empty();
                PECO.DTphpLoading(leaverequesttbl, 'Loading... ');
            }
        }).done(function (d) {
            populate_leave_requested_tbl(d);
        });
    };

    //populate data to table
    var populate_leave_requested_tbl = function (data) {
        leaverequesttbl.dataTable().empty();
        leaverequesttbl.dataTable({
            bDestroy: true,
            bPaginate: true,
            bFilter: true,
            bInfo: true,
            bStateSave: true,
            bProcessing: true,
            aaData: data.leaverequestedlist,
            aoColumns: [
                {"data":"num"},
                {"data":"leavetype" ,sClass:'text-info'},
                {"data":"from"},
                {"data":"to"},
                {"data":"status" ,sClass:'text-danger'},
            ],
            searchHighlight: true
        });
    };

    var init_employee_leave_credits = function(el, empid) {


            $.ajax({
                url: PECO.base_url() + 'hris/getemployeeleavecredits',
                type: 'post',
                dataType: 'json',
                data: {'empid': empid}
            }).done(function(d){
                el.html(d.html);

                el.on('click', 'li.list-group-item', function(e) {
                    var this_ = $(this);
                    $('.list-group-item', el).removeClass('list-group-item-danger');
                    this_.addClass('list-group-item-danger');
                    $('input.radio-leave-credits', this_).click();
                });

                $('.popovers').each(function(){
                    $(this).popover({
                        html: true,
                        animation: true,
                        template: '<div class="popover popover-info"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>'
                    });
                });

                var leavetype_val = 0;

                var date_from_val = $('#fromdate').val();
                var date_to_val = $('#todate').val();
                var nohours = $('#nohours').val();
                var this_balance = $('#'+leavetype_val).find('.balance');
                var this_balance_val = this_balance.text();
                var this_spent = $('#'+leavetype_val).find('.spent');
                var this_spent_val = this_spent.text();
                if(selectleavetype.select2('val') > 0) {
                    if((date_from_val != '' && date_to_val != '') || nohours != ''){
                        if ($('#' + leavetype_val).length > 0) {

                            $(document).find('.leave-input input, .leave-input textarea').each(function () {
                                $(this).attr('disabled', false);
                            });
                            var numofdays = 0;
                            $.ajax({
                                cache: false,
                                url: PECO.base_url() + 'hris/computenumdays',
                                data: {'datefrom': date_from_val, 'dateto': date_to_val},
                                dataType: 'json',
                                type: 'post',
                                async: false,
                            }).done(function (d) {
                                numofdays = d.numdays;
                                $('#nodays', document).val(numofdays);
                            });

                            if (this_balance_val > 0 ) {
                                    if (this_balance_val >= numofdays) {

                                            var numofdaysperhours = numofdays * 24;
                                            var numberofbalanceperhours = 0;

                                            if(this_balance_val.contains(".")){
                                                var balancearray = this_balance_val.split(".");
                                                numberofbalanceperhours = Number((balancearray[0] * 24)) + Number(balancearray[1]);
                                            }else{
                                                numberofbalanceperhours =Number((this_balance_val * 24))
                                            }

                                            var finaltotalspent = Number(nohours) + Number(numofdaysperhours);
                                            var new_balance = Number(numberofbalanceperhours - finaltotalspent);
                                            if(new_balance < 0 || nohours > numberofbalanceperhours){

                                                $(document).find('#nodays').val('');
                                                $(document).find('#fromdate').val('');
                                                $(document).find('#todate').val('');
                                                $(document).find('#nohours').val('');
                                                PECO.initAlerts("Insufficient leave credit balance", "PECO.net", "warning");
                                            }else{
                                                if(new_balance >= 24){

                                                    var remainder = new_balance % 24;
                                                    var fixbalance = new_balance / 24;
                                                    new_balance =  Math.floor(fixbalance) + '.'+remainder;
                                                }else{
                                                    if(new_balance <= 0){
                                                        new_balance = 0;
                                                    }else{
                                                        new_balance = '.'+new_balance
                                                    }

                                                }
                                                this_balance.text(new_balance);

                                                var spentarray = this_spent_val.split(".");
                                                var daysspent = Number(spentarray[0])+Number(numofdays);
                                                if(spentarray[1]){
                                                    var hoursspent = Number(spentarray[1])+Number(nohours);
                                                }else{
                                                    var hoursspent = 0+Number(nohours);
                                                }

                                                if(hoursspent >= 24){
                                                    daysspent++;
                                                    hoursspent = hoursspent - 24;
                                                }

                                                var newspent = daysspent +'.'+ hoursspent;
                                                if(nohours > 0){
                                                    if(hoursspent == 24){
                                                        this_spent.text(newspent);
                                                    }else{
                                                        this_spent.text(newspent);
                                                    }
                                                }else{
                                                    this_spent.text(newspent);
                                                }

                                            }
                                    } else {
                                        $(document).find('#nodays').val('');
                                        $(document).find('#fromdate').val('');
                                        $(document).find('#todate').val('');
                                        $(document).find('#nohours').val('');
                                        PECO.initAlerts("Insufficient leave credit balance", "PECO.net", "warning");
                                    }

                            } else {
                                $(document).find('#nohours').val('');
                                $(document).find('#nodays').val('');
                                $(document).find('#fromdate').val('');
                                $(document).find('#todate').val('');
                                PECO.initAlerts("Insufficient leave credit balance", "PECO.net", "warning");
                            }


                        } else {
                            PECO.initAlerts('Employee has no leave credit', 'PECO.net', 'info');
                            $(document).find('.leave-input input, .leave-input textarea').each(function () {
                                $(this).attr('disabled', true);
                            });
                        }

                    }
                }
            }).fail(function(){
                PECO.phpError();
            });
    };


    var init_components = function () {
        PECO.select2Basic($('#yearleave', document) , 'systems/select2year' , 'Select Year' , false,false,false);
        $('#typeofleave').select2({
            "allowClear" : true
        });
        $('#trnofleave').select2({
            "allowClear" : true
        });

        $(document).on('change' , '#trnofleave' , function () {
            var this_ = $(this);
            if(this_.val() == 1){
                $('#daystype', document).removeClass('hidden');
                $('#hourstype' , document).addClass('hidden');
            }else if(this_.val() == 2){
                $('#daystype', document).addClass('hidden');
                $('#hourstype' , document).removeClass('hidden');
            }
        });
        $(document).on('submit' , '#submitleavereq' , function (e) {
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
                          $('#submitleavereq' , document)[0].reset();
                          $('#selectleavetype' , document).select2('val' , '');
                          $('#yearleave' , document).select2('val' , '');
                          $('#typeofleave' , document).select2('val' , '');
                          $('#trnofleave' , document).select2('val' , '');
                      }
                }).fail(function () {
                    PECO.phpError();
                });
        });
        $(document).on('submit' , '#submitleavetransaction' , function (e) {
            e.preventDefault();
            alert("test");
        });

    };
    var fetchdraftrequestleavetbl = function(empid , year){
        $.ajax({
            url:PECO.base_url()+'hris/fetchdraftrequested' ,
            type:'post',
            data:{"empid" : empid , "year" : year},
            dataType:'json'
        }).done(function (data) {

            $('#draftrequestleavetbl' , document).dataTable().empty();
            $('#draftrequestleavetbl' , document).dataTable({
                bDestroy: true,
                bPaginate: false,
                bFilter: false,
                bInfo: false,
                bStateSave: true,
                bProcessing: true,
                aaData: data.draftrequesteddata,
                aoColumns: [
                    {"data":"num"},
                    {"data":"leavetype"},
                    {"data":"fromdate"},
                    {"data":"todate"},
                    {"data":"fromtime"},
                    {"data":"totime"},
                    {"data":"type"},
                    {"data":"datecreated"}
                ],
                searchHighlight: true
            });
        });
    };
    return{
        init:function(empid , year){

            request_init(empid , year);
            init_request_fn(empid);
        },
        leavecredits: function(el, empid) {
            init_employee_leave_credits(el, empid);
        },

        initreqtbl: function(empid, stat) {
            draft_leave_request(empid, stat);
            init_approved_request(empid);
        },
        initcomponents:function () {
            init_components();
        }

    }
}();