var REPORTS = function() {

    var init_user_transactions = function(userid) {

        var userid = (userid) ? userid : false;
        var tbl_trn_list = $('#tbl_trn_list');
        PECO.DTDefault(tbl_trn_list, 'No transaction yet!');
        PECO.dtSubDetails(tbl_trn_list, 'tellering/ordetails');

        $.ajax({
            url: PECO.base_url() + 'tellering/trnlist',
            type: 'post',
            data: {'userid': userid},
            dataType: 'json',
            beforeSend: function() {
                PECO.DTphpLoading(tbl_trn_list, 'Loading your transactions...');
            }
        }).done(function(d){
            $(document).find('#total_amt').html(d.totalamt);
            $(document).find('#total_chk').html(d.totalamtchk);
            $(document).find('#total_cash').html(d.totalamtcash);

            //tbl_trn_list.DataTable().empty();
            tbl_trn_list.DataTable({
                // Internationalisation. For more info refer to http://datatables.net/manual/i18n
                bDestroy: true,
                bPaginate: false,
                bFilter: false,
                bInfo: false,
                bStateSave: true,
                bProcessing: true,
                scrollY: '300px',
                aaData: d.list,
                aoColumns: [
                    {"data": "expand"},
                    {"data": "trnno"},
                    {"data": "orno", sWidth: '100px', sClass: '' },
                    {"data": "servno", sWidth: '', sClass: '' },
                    {"data": "amt", sWidth: '', sClass: 'number text-danger text-bold' },
                    {"data": "mode", sWidth: '', sClass: '' },
                    {"data": "payfor", sWidth: '', sClass: '' },
                    {"data": "control", sWidth: '', sClass: 'control' },
                    {"data": "select", sWidth: '20px', sClass: 'checkcontrol' },
                ],
                fnRowCallback: function(nRow, data) {
                    // RE-INITIALIZE TOOLTIPS
                    $(nRow).find('[data-toggle="popover"]').popover({animate: true, html: true});
                    $(nRow).find('td').addClass(data.rowclass);
                    $(nRow).find('.icheck').iCheck({
                        checkboxClass: 'icheckbox_minimal',
                        radioClass: 'iradio_minimal',
                        increaseArea: '20%' // optional
                    });
                }
            });
            PECO.initDTNicescroller();
        }).fail(function(){
            PECO.phpError();
        });

        tbl_trn_list.on('click', 'tr td, tr input', function(e) {
            var this_ = $(this);
            var input_checkbox = this_.closest("tr").find('.checkcontrol input');

            if(input_checkbox.is(':checked')) {
                input_checkbox.attr('checked', false);
            }else{
                input_checkbox.attr('checked', true);
            }
        });

        $(document).on('click','#orvoidbtn',function () {
            var ids = [];
            tbl_trn_list.find('.checkcontrol input:checked').each(function () {
                var this_val = $(this).val();
                ids.push(this_val);
            });
            console.log(ids);


            if (ids.length > 0) {
                $.ajax({
                    url: PECO.base_url() + 'tellering/getpaymentdetails',
                    type: 'post',
                    data: {'ids': ids},
                    dataType: 'json'
                }).done(function(d) {
                    $(document).find('#amtpd').text(d.totalamount);
                    $(document).find('#amtvat').text(d.vattaxamt);
                    $(document).find('#amtfrtx').text(d.franchisetax);
                    $(document).find('#amtnovat').text(d.nonvatt);
                    $('#void_window').modal('show');
                    return false;
                });
            }else{
                PECO.initAlerts("Please select item to void.","Void Transations","info",3000);
            }

        });
    };



    return {
        usertrn: function() {
            init_user_transactions();
        }
    }
}();