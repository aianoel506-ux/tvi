var SETTINGS = function() {

    var con_test_stat = $('#conn_test', document);
    var con_test_input = $('#con_test_input', document);

    var init_testing_fn = function() {
        $(document).on('click', '#btn_triggger', function() {
            init_form_loop(0, 0);
        });
    };

    var init_server_check = function() {

        $.ajax({
            url: PECO.base_url() + 'settings/servercheck',
            type: 'post',
            data: {},
            dataType: 'json',
        }).done(function(d){
            if(d.conn == true) {
                con_test_input.val(1);
                con_test_stat.removeClass('bg-red-flamingo bg-font-red-flamingo').addClass('bg-green-jungle bg-font-green-jungle');
            }else{
                con_test_input.val(0);
                con_test_stat.removeClass('bg-green-jungle bg-font-green-jungle').addClass('bg-red-flamingo bg-font-red-flamingo');
            }
        }).fail(function() {
            con_test_input.val(0);
            con_test_stat.removeClass('bg-green-jungle bg-font-green-jungle').addClass('bg-red-flamingo bg-font-red-flamingo');
        });
    };

    var init_form_loop = function(num, sysid) {
        $.ajax({
            url: PECO.base_url() + 'settings/testquery',
            type: 'post',
            data: {'num': num, 'sysid': sysid},
            dataType: 'json',
        }).done(function(d){
            if(d.end==false) {
                init_form_loop(d.num, d.sysid);

                $('#stat_bar', document).closest('.progress').addClass('progress-striped active');
                $('#stat_bar', document).css('width', d.per + '%');
                $('#test_stat', document).val('Item queried: ' + d.empname);
                $('#test_per', document).val(d.per + '%');
                $('#test_num', document).html(d.num);
                if(d.per < 30) {
                    $('#stat_bar', document).removeClass('progress-bar-success progress-bar-info').addClass('progress-bar-danger');
                }else{
                    if(d.per < 70) {
                        $('#stat_bar', document).removeClass('progress-bar-success progress-bar-danger').addClass('progress-bar-info');
                    }else{
                        $('#stat_bar', document).removeClass('progress-bar-info progress-bar-danger').addClass('progress-bar-success');
                    }
                }
            }else{
                $('#stat_bar', document).css('width', d.per + '%');
                $('#stat_bar', document).closest('.progress').removeClass('progress-striped active');
                $('#test_stat', document).val(d.empname);
                $('#test_per', document).val(d.per + '%');
            }
        }).fail(function() {
            $('#test_stat', document).html('<i class="fa fa-times text-danger"></i> PHP Error');
        });
    };

    var init_father_update = function() {
        var cnt = 0;
        $.ajax({
            url: PECO.base_url() + 'settings/getfatherrecordscount',
            type: 'post',
            dataType: 'json',
            data: {}
        }).done(function(d) {
            if(d != null && d.qry == true ) {
                cnt = d.cnt;

                $('#input_last_servno', document).val(d.servno);
                $('#input_last_num', document).val(d.num);
                $('#text_father_records', document).text(cnt);
                $(document).on('click', '#btn_triggger', function () {
                    var last_servno = $('#input_last_servno', document).val();
                    var last_num = $('#input_last_num', document).val();
                    init_father_loop(last_num, last_servno, cnt);
                });
            }else{
                $('#text_father_records', document).text('Error PHP');
            }
        }).fail(function() {
            $('#text_father_records', document).text('Error PHP');
        });


        // INITIALIZE SERVER CHECK
        init_server_check();

        // INTERVAL CHECK 5 MIN SERVER CHECK
        setInterval(function() {
            init_server_check();
        }, 300000);
    };

    var init_query_billtrn = function(year, month) {
        var cnt = 0;
        $.ajax({
            url: PECO.base_url() + 'settings/getbilltrncount',
            type: 'post',
            dataType: 'json',
            data: {'year': year, 'month': month},
            async: false,
            cashe: false,
        }).done(function(d) {
            if (d != null && d.qry == true) {
                cnt = d.cnt;
            }
        });
        return cnt;
    };

    var init_billtrn_update = function() {
        $(document).on('click', '#btn_get_date', function(e) {
            e.preventDefault();
            var year = $('#input_year', document).val();
            var month = $('#input_year', document).val();
            var inputcnt = $('#data_count');
            var q_count = init_query_billtrn(year, month);
            if(q_count > 0) {
                $('#btn_triggger', document).attr('disabled', false);
                inputcnt.val(q_count);
                var num = 0;
                init_billtrn_loop(num, year, month, q_count);
            }else {
                $('#btn_triggger', document).attr('disabled', true);
                inputcnt.val(0);
            }
        });


        // INITIALIZE SERVER CHECK
        init_server_check();

        // INTERVAL CHECK 5 MIN SERVER CHECK
        setInterval(function() {
            init_server_check();
        }, 300000);
    };


    var init_father_loop = function(num, servno, cnt) {
        $.ajax({
            url: PECO.base_url() + 'settings/fahterupdatedloop',
            type: 'post',
            data: {'num': num, 'servno': servno, 'cnt': cnt},
            dataType: 'json',
        }).done(function(d){
            if(d.end==false) {

                $('#input_last_servno').val(d.servno);
                $('#input_last_num').val(d.num);

                init_father_loop(d.num, d.servno, cnt);

                $('#stat_bar', document).closest('.progress').addClass('progress-striped active');
                $('#stat_bar', document).css('width', d.per + '%');
                $('#test_stat', document).val('Item queried: ' + d.custname);
                $('#test_per', document).val(d.per + '%');
                if(d.per < 30) {
                    $('#stat_bar', document).removeClass('progress-bar-success progress-bar-info').addClass('progress-bar-danger');
                }else{
                    if(d.per < 70) {
                        $('#stat_bar', document).removeClass('progress-bar-success progress-bar-danger').addClass('progress-bar-info');
                    }else{
                        $('#stat_bar', document).removeClass('progress-bar-info progress-bar-danger').addClass('progress-bar-success');
                    }
                }
            }else{
                $('#stat_bar', document).css('width', d.per + '%');
                $('#stat_bar', document).closest('.progress').removeClass('progress-striped active');
                $('#test_stat', document).val(d.custname);
                $('#test_per', document).val(d.per + '%');
            }
        }).fail(function() {
            $('#test_stat', document).html('<i class="fa fa-times text-danger"></i> PHP Error');
        });
    };

    var init_billtrn_loop = function(num, year, month, cnt) {
        $.ajax({
            url: PECO.base_url() + 'settings/billtrnupdateloop',
            type: 'post',
            data: {'num': num, 'year': year, 'month': month, 'cnt': cnt},
            dataType: 'json',
        }).done(function(d){
            if(d.end==false) {

                $('#input_last_servno').val(d.servno);
                $('#input_last_num').val(d.num);

                init_father_loop(d.num, d.servno, cnt);

                $('#stat_bar', document).closest('.progress').addClass('progress-striped active');
                $('#stat_bar', document).css('width', d.per + '%');
                $('#test_stat', document).val('Item queried: ' + d.custname);
                $('#test_per', document).val(d.per + '%');
                if(d.per < 30) {
                    $('#stat_bar', document).removeClass('progress-bar-success progress-bar-info').addClass('progress-bar-danger');
                }else{
                    if(d.per < 70) {
                        $('#stat_bar', document).removeClass('progress-bar-success progress-bar-danger').addClass('progress-bar-info');
                    }else{
                        $('#stat_bar', document).removeClass('progress-bar-info progress-bar-danger').addClass('progress-bar-success');
                    }
                }
            }else{
                $('#stat_bar', document).css('width', d.per + '%');
                $('#stat_bar', document).closest('.progress').removeClass('progress-striped active');
                $('#test_stat', document).val(d.custname);
                $('#test_per', document).val(d.per + '%');
            }
        }).fail(function() {
            $('#test_stat', document).html('<i class="fa fa-times text-danger"></i> PHP Error');
        });
    };

    return {
        init: function() {
            init_testing_fn();
        },
        fatherupdate: function() {
            init_father_update();
        },
        billtrnupdate: function() {
            init_billtrn_update();
        }
    }
}();