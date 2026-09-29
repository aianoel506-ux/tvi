/**
 * Created by SE on 5/29/2017.
 */

var CRMVIEW = function() {
    var init_view = function() {
        $('#frm_basic_info').submit(function(e){
            var form = $(this);
            e.preventDefault();
            $.ajax({
                url: form.attr('action'),
                type: form.attr('method'),
                data: form.serialize(),
                dataType: 'json',
                beforeSend: function() {
                    $('#query_loading_basic').html('<i class="fa fa-spinner fa-spin fa-pulse text-info"></i> Processing...');
                }
            }).done(function(d) {
                PECO.initAlerts(d.msg, d.title, d.func);
                $('#query_loading_basic').html('');
            }).fail(function(){
                PECO.phpError();
                $('#query_loading_basic').html('');
            });
        });

        $('#frm_other_info').submit(function(e){
            var form = $(this);
            e.preventDefault();
            $.ajax({
                url: form.attr('action'),
                type: form.attr('method'),
                data: form.serialize(),
                dataType: 'json',
                beforeSend: function() {
                    $('#query_loading_other').html('<i class="fa fa-spinner fa-spin fa-pulse text-info"></i> Processing...');
                }
            }).done(function(d) {
                $('#input_multcodedate').val(d.date);
                PECO.initAlerts(d.msg, d.title, d.func);
                $('#query_loading_other').html('');
                $('#btn_next_cust').trigger('click');
            }).fail(function(){
                PECO.phpError();
                $('#query_loading_other').html('');
            });

        })
    };

    return {
        init: function() {
            init_view();
        }
    }
}();
