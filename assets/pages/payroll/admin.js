var FINANCEADMIN  = function () {

    var annualtable = $(document).find('#annualtable');

    var saverow = function (this_tr , typesid , empid) {

        var gross = $('td.gross input', this_tr).val();
        var tax = $('td.tax input', this_tr).val();
        var deduction = $('td.deduction input', this_tr).val();

        var month = $(document).find('#month').val();
        var year = $(document).find('#year').val();
        var paytype = $(document).find('#paytype').val();

        if(month != '' && year != ''  && paytype != ''){
            $.ajax({
                url:PECO.base_url()+'admin/savemanualearnings',
                type:'post',
                data:{"gross" : gross , "tax" : tax,
                    "typesid" : typesid , "empid" : empid , "month" : month , "year" : year , "paytype" : paytype,
                "deduction" : deduction},
                dataType:'json',
                async:false
            }).done(function (data) {
                PECO.initAlerts(data.msg , "PECO" , data.func);
                var this_stat = $('td.stat', this_tr);
                this_stat.html('<span class="label label-sm label-primary"> Draft </span>');
                return true;
            }).fail(function () {
                return false;
            });
        }else{
            PECO.initAlerts("Please fill upp all the fields" , "PECO" , "info");
        }


    };

    var init_events = function () {

        var viewtype = $('#viewtype', document);
        var typesid = $('#typesid', document);
        var year = $('#year', document);
        var month = $('#month', document);
        var paytype = $('#paytype', document);

        fetchemployees(typesid.val() , month.val() , year.val() , paytype.val() , viewtype.val());

        $(document).on('change' ,'#viewtype, #year, #month, #paytype ' , function () {
            fetchemployees(typesid.val() , month.val() , year.val() , paytype.val() , viewtype.val());
                        //(typesid , month , year , paytype , payclass, viewtype)
        });

        $(document).on('click' ,'#getbasic' , function () {
            swal({
                title: "Are you sure?",
                text: 'Basic salary will be saved.',
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes",
                closeOnConfirm: false,
                closeOnCancel: true,
                showLoaderOnConfirm: true
            }, function(isConfirm) {
                if (isConfirm) {
                    var typesid = $('#typesid', document).val();
                    var year = $('#year', document).val();
                    var month = $('#month', document).val();
                    var paytype = $('#paytype', document).val();
                    var viewtype = $('#viewtype', document).val();
                    fetchemployees(typesid , month , year , paytype , viewtype, 1);
                    swal.close();
                }else{
                    swal.close();
                }
            });
        });
        $(document).on('submit','#submitpstransactions' , function (e) {
            e.preventDefault();
            var this_ = $(this);

            swal({
                title: "Are you sure?",
                text: "Profit share will be save.",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes, save!",
                closeOnConfirm: false,
                closeOnCancel: false,
                showLoaderOnConfirm: true
            }, function(isConfirm){
                if (isConfirm) {
                    $.ajax({
                        url:this_.attr("action"),
                        type:this_.attr("method"),
                        data:this_.serialize(),
                        dataType:'json'
                    }).done(function (data) {
                        swal("PECO" , data.msg , data.func);
                        if(data.qry == true){
                            fetchemployees(data.typesid , data.month , data.year , data.paytype);
                        }
                    }).fail(function () {
                        swal.close();
                    });
                } else {
                    swal("Cancelled", "Transaction processing canceled", "error");
                }
            });

        });

        $(document).on('keyup' , '#annualtable input' , function (e) {
            e.preventDefault();
            var this_ = $(this);
            var this_tr = this_.closest('tr');
            var this_gross = $('td.gross input', this_tr).val();
            var this_tax = $('td.tax input', this_tr).val();
            var this_deduction = $('td.deduction input', this_tr).val();
            var this_total_ded = Number(this_tax) + Number(this_deduction);
            var this_net = Number(this_gross) - Number(this_total_ded);
            var this_net_input = $('td.net', this_tr);
            this_net_input.text(this_net);


        });
        $(document).on('keypress' , '#annualtable input' , function (e) {
            var this_ = $(this);

            var this_tr = this_.closest('tr');



            var keycode = (e.keyCode ? e.keyCode : e.which);

            if(keycode == '13'){
                e.preventDefault();
                var typesid = this_.attr("data-type");
                var empid = this_.attr("data-empid");

                if(this_.closest('td').hasClass('gross') == true) {
                    $('td.tax input', this_tr).focus();
                }else {

                    if(saverow(this_tr , typesid , empid) == true){
                        var next_row = this_tr.next();
                        var next_gross = next_row.find('td.gross input');
                        next_gross.focus();
                    }

                }
            }
        });
        $(document).on('click','#btn_send_toemail',function (e) {
            e.preventDefault();
            var typesid = $('#typesid', document).val();
            var year = $('#year', document).val();
            var month = $('#month', document).val();
            var paytype = $('#paytype', document).val();
            $.ajax({
                url:PECO.base_url()+'reports/sendannualpayslips',
                type:'post',
                data:{"typesid" : typesid ,"year" : year,"month":month , "paytype" : paytype},
                dataType:'json'
            }).done(function (data) {
                
            }).fail(function () {
                
            });

        });
        $(document).on('click', '#btn_export_bankfile', function(e) {
            e.preventDefault();
            var typesid = $('#typesid', document).val();
            var year = $('#year', document).val();
            var month = $('#month', document).val();
            var paytype = $('#paytype', document).val();
            var payclass = $('#viewtype', document).val();
            if(payclass == ''){
                payclass = 0;
            }
            window.open(PECO.base_url() + 'reports/downloademployeesannualbankfile/' + typesid + '/' + year + '/' + month + '/' + paytype + '/' + payclass, '_blank');
        });

        $(document).on('click','#btn_print_payslip',function (e) {
            e.preventDefault();
            var typesid = $('#typesid', document).val();
            var year = $('#year', document).val();
            var month = $('#month', document).val();
            var paytype = $('#paytype', document).val();
            var viewtype = $('#viewtype', document).val();

            window.open(PECO.base_url() + 'reports/printannualpayslip/' + typesid + '/' + year + '/' + month+ '/' + paytype+ '/' + viewtype);
        });

        $(document).on('click', '#btn_print_report', function(e) {
            e.preventDefault();
            var this_ = $(this);
            var this_html = this_.html();
            var typesid = $('#typesid', document).val();
            var year = $('#year', document).val();
            var month = $('#month', document).val();
            var paytype = $('#paytype', document).val();
            var viewtype = $('#viewtype',document).val();
            $.ajax({
                url: PECO.base_url() + 'reports/printannualreport',
                type: 'post',
                data: {'typesid': typesid, 'year': year, 'month': month, 'paytype': paytype, 'viewtype': viewtype},
                dataType: 'json'
            }).done(function(d) {
               PECO.pecoRepPrint("" , d.html,false);
            }).fail(function() {
                PECO.btnErrorPHP(this_, this_html, 'btn-default');
            });
        });
    };

    var fetchemployees = function (typesid , month , year , paytype , payclass, viewtype) {
        var viewtype_ = (viewtype) ? 1 : 0;
        $.ajax({
            url:PECO.base_url()+'payroll/fetchemployeeforbonuses',
            type:'post',
            data:{"typesid" : typesid , "month":month , "year" : year , "paytype" : paytype , "payclass" : payclass, "viewtype": viewtype_},
            dataType:'json',
            beforeSend: function () {
                annualtable.dataTable().empty();
                PECO.DTphpLoading(annualtable, 'Please wait, loading data ...');
            }
        }).done(function (data) {

                annualtable.dataTable().empty();
                annualtable.dataTable({
                    bDestroy: true,
                    bPaginate: false,
                    bFilter: true,
                    bInfo: true,
                    bStateSave: true,
                    bProcessing: true,
                    aaData: data.databonuses,
                    aoColumns: [
                        {"data":"num"},
                        {"data":"accntno" , sWidth:'40px'},
                        {"data":"name"},
                        {"data":"gross", sClass:'gross number'},
                        {"data":"deduction", sClass:'deduction text-danger number' },
                        {"data":"tax", sClass:'tax text-danger number'},
                        {"data":"net" , sClass:'net text-primary text-bold number'},
                        {"data":"type" , sClass:'type'},
                        {"data":"status" , sClass:'stat'}
                    ],
                    searchHighlight: true,
                    fnRowCallback: function (nRow, aData) {
                          $(nRow).addClass(aData.rowcolor);
                    }
                });
                if(data.getbasic == true){
                    fetchemployees(typesid , month , year , paytype , viewtype, 0);
                }
        }).fail(function () {
            PECO.phpError();
        });
    };

    return{
        init:function (typesid , month , year , paytype) {
            fetchemployees(typesid , month , year , paytype );
            init_events();
        }
    }
}();