var TICKETING = function() {
    var frm_new_ticket = $('#frm_new_ticket', document);
    var search_acct = $('#search_acct', document);

    var select_ticket = $('#select_ticket');
    var select_ticketpart = $('#select_ticketpart');
    var select_district = $('#select_district');
    var select_priority = $('#select_priority');
    var select_department = $('#ticketdepart');
    var select_status = $('#select_status');
    var select_selected_priority_ticket = $('#select_selected_priority_ticket');


    var init_new_ticket = function() {

        PECO.select2Basic(select_department, 'user/getdepartments', 'Select Department..', false, false, false);
        PECO.select2Basic(select_ticket, 'user/getticketselect', 'Select Ticket..', false, false, false);
        PECO.select2Basic(select_district, 'user/getdistrictselect', 'Select District..', false, false, false);
        PECO.select2Basic(select_priority, 'user/getpriorityselect', 'Select Priority..', false, false, false);

        //change event of drop down
        select_ticket.on('change', function(e){
            var this_ = $(this);
            var this_val = this_.val();
            if(this_val>0) {
                select_ticketpart.attr('readonly', false);
                PECO.select2BasicId(select_ticketpart, 'user/getticketpartselect', this_val, false, false, false, false);
            }else{
                select_ticketpart.attr('readonly', true).val('').select2('destroy');
            }

            if(this_val==278) {
                $('.billing').removeClass('hidden');
                $('.services, .payments').addClass('hidden');
                PECO.DTDefault($('#tbl_billhist'), 'No Billing history..');
            }else if(this_val==279) {
                $('.payments').removeClass('hidden');
                $('.services, .billing').addClass('hidden');
            }else if(this_val==280) {
                $('.services').removeClass('hidden');
                $('.payments, .billing').addClass('hidden');

                PECO.employeeSelectTagging($('#empid', document), true);
            }else{
                $('.payments, .billing, .services').addClass('hidden');
            }
        });

        frm_new_ticket.submit(function(e) {
            var form = $(this);
            e.preventDefault();
            swal({
                title: "Are you sure?",
                text: 'Adding new ticket',
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
                        url: form.attr('action'),
                        method: form.attr('method'),
                        dataType: "json",
                        data: new FormData(form[0]),
                        processData: false,
                        contentType: false,
                    }).done(function (d) {
                        swal(d.title, d.msg, d.func);
                    }).fail(function(){
                        swal("Error404: PHP", "Server side error!", "error");
                    });
                }else{
                    swal.close();
                }
            });
        });

        PECO.customerSelectTagging(search_acct, 'Search account..');

        $('#reset').click(function(){
            acct_res_default();
        });

        search_acct.change(function(e){
            var this_ = $(this);
            acct_res_default();

            $.ajax({
                url: PECO.base_url() + 'cwdo/acctsearch',
                type: 'post',
                data: {'acctid': this_.val()},
                dataType: 'json'
            }).done(function(d) {
                $('#res_servno', document).html(d.servno);
                $('#res_name', document).html(d.name);
                $('#res_address', document).html(d.address);
            });
        });
    };

    var acct_res_default = function() {
        $('#res_servno', document).html('N/A');
        $('#res_name', document).html('N/A');
        $('#res_address', document).html('N/A');
    };

    return {
        init: function() {
            init_new_ticket();
        }
    }
}();