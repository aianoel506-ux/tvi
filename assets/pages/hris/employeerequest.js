
var EMPLOYEEREQ = function(empid) {

    PECO.getDatePickerPlugins();
    PECO.getPulsate();

    var tbl_leave_draft = $('#tbl_employee_leave_draft', document);
    var tbl_leave_credits = $('#tbl_leave_credits_status', document);

    var fn_employee_request = function(empid) {
        init_employee_leave_credits(tbl_leave_credits, empid);
        var year = $('#input_year', document).val();

        tbl_leave_transactions_draft(empid, year);

        $('.tooltips', document).tooltip();

        // $('#input_time_start, #input_time_start', document);

        setTimeout(function() {

            $('#input_date_from, #input_date_end', document).datepicker({
                format: 'yyyy-mm-dd'
            });
            $(".timepicker-default").timepicker({autoclose:!0,showSeconds:!0,minuteStep:1});
        }, 500);

        PECO.DTDefault(tbl_leave_draft, 'Create leave first...');


        $('#typeofleave').select2();

        $('#remarks', document).maxlength({
            limitReachedClass: "label label-danger",
            alwaysShow: false,
            placement: 'bottom'
        });

        $(document).on('click' , '#deleteleavedraft' , function () {
            var this_ = $(this);
            var dataid = this_.attr("data-id");

            if(empid == '' || year == ''){
                PECO.initAlerts("Employee / Year is empty" , "PECO" , "info");
            }else{
                swal({
                    title: "Are you sure?",
                    text: 'Draft item will be deleted.',
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
                            url:PECO.base_url()+'request/deletependingrequest',
                            type:'post',
                            data:{"dataid" : dataid},
                            dataType:'json'
                        }).done(function (data) {
                            swal("PECO" , data.msg , data.func);
                            tbl_leave_transactions_draft(empid , year)
                        }).fail(function () {
                            PECO.phpError();
                            swal.close();
                        });
                    }else{
                        swal.close();
                    }
                });
            }
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
                $('input.radio-leave-credits', this_).attr('checked', true);
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
            var selectleavetype = $('#typeofleave', document);
            if(selectleavetype.select2('val') > 0) {
                if((date_from_val != '' && date_to_val != '') || nohours != ''){
                    $(document).find('.leave-input input, .leave-input textarea').each(function () {
                        $(this).attr('disabled', false);
                    });
                    var numofdays = 0;
                    /*
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

                     */

                }
            }
        }).fail(function(){
            PECO.phpError();
        });
    };


    var tbl_leave_transactions_draft = function(empid , year){
        $.ajax({
            url:PECO.base_url()+'request/tblleaveitem' ,
            type:'post',
            data:{empid : empid, year : year},
            dataType:'json'
        }).done(function (data) {
            tbl_leave_draft.dataTable().empty();
            tbl_leave_draft.dataTable({
                bDestroy: true,
                bPaginate: false,
                bFilter: false,
                bInfo: false,
                bStateSave: true,
                bProcessing: true,
                aaData: data.leaverequestedlist,
                aoColumns: [
                    {"data":"num"},
                    {"data":"leavetype"},
                    {"data":"from"},
                    {"data":"to"},
                    {"data":"fromtime"},
                    {"data":"totime"},
                    {"data":"type"},
                    {"data":"control"}
                ],
                searchHighlight: true,
                language: PECO.DTEmptyMessage('No temporary transaction created!')
            });
        });
    };

    return {
        init: function(empid) {
            fn_employee_request(empid);
        },
        trntemp: function(empid, year) {
            tbl_leave_transactions_draft(empid, year);
        }
    }
}();