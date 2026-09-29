var ACCOMPLISHMENT = function() {
    PECO.getSelect2Plugins();
    PECO.getHighlightsPlugin();

    var select_employee = $('#conby');
    var select_multcode = $('#multcode');
    var tbl_near_mtr = $('#tbl_nearmeter');
    var frm_accomplishments = $('#frm_execute_accomplishments');
    var init_accomplishment = function() {
        PECO.select2Basic(select_employee, 'hris/get_employee_username', 'Select Employee..', true, false, false);
        PECO.select2Basic(select_multcode, 'billing/getselect2multcode', 'Select Multcode..', true, false, false);
        PECO.DTDefault(tbl_near_mtr, 'No near meter encoded!');

        frm_accomplishments.submit(function(e) {
            e.preventDefault();
            var this_ = $(this);
            $.SmartMessageBox({
                    title: "<i class='fa fa-question fa-fw fa-lg txt-color-yellow'></i> Confirm: Execute Accomplishemnts</span>",
                    content: 'Please confirm action taken',
                    buttons: '[Yes][No]',
                    buttonClass: 'btn-primary, btn-default',
                    buttonsIcon: 'fa-angle-double-right, fa-times',
                },
                function (ButtonPressed) {
                    if (ButtonPressed === "Yes") {
                        $.ajax({
                            url: this_.attr('action'),
                            type: this_.attr('method'),
                            data: this_.serialize(),
                            beforeSend: function () {
                                PECO.blockUI({
                                    target: this_,
                                    animate: true,
                                    overlayColor: false
                                });
                            },
                            cache: false,
                            dataType: 'json'
                        }).done(function (d) {
                            PECO.unblockUI(this_);
                            // msg, title, func, timeout, box, shake, number
                            PECO.initAlerts(d.msg, 'Execute Accomplishment', d.func, 5000, 'small', true, 0);
                            if (d.qry == true) {
                                setTimeout(function(){
                                    PECO.pecoRepPrint('Customer Name', '');
                                }, 2000);
                            }
                        }).fail(function () {
                            PECO.unblockUI(this_);
                        });
                    }
                });
        });
    };

    return {
        init: function() {
            init_accomplishment();
        }
    }
}();