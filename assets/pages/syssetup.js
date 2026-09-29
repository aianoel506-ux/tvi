var SYSSETUP = function() {
    var init_sys_setup = function() {
        init_logout_btn();
    };

    var init_db = function() {
        $(document).on('submit', '#frm_command_trans', function(e) {
            e.preventDefault();
            var form = $(this);
            var btn = $('button[type=submit]', form);
            var btn_html = btn.html();

            $.ajax({
                url: form.attr('action'),
                type: form.attr('method'),
                data: form.serialize(),
                dataType: 'json',
                beforeSend: function() {
                    PECO.btnLoading(btn, ' ');
                }
            }).done(function(d) {
                var exec_html = '';

                exec_html += '\n' +
                    '<span class="col-md-6"><input class="form-control input-lg inline code" name="killcode" id="killcode" placeholder="CODE"/></span>\n' +
                    '<span class="col-md-6">\n' +
                    '\n' +
                    '<button href="javascript:;" data-code="'+d.code+'" id="trigger_command_trans" class="btn btn-danger btn-lg">\n' +
                    '<i class="fa fa-warning"></i>\n' +
                    'Execute Command\n' +
                    '</button>\n' +
                    '</span>' +
                    '';

                $('#exec_command', document).html(exec_html);
                PECO.btnSuccess(btn, 'Sent!', btn_html, 'btn-default');
            });


        });

        $(document).on('click', '#trigger_command_trans', function(d) {
            d.preventDefault();
            var this_btn = $(this);
            var this_html = this_btn.html();

            var kill_code = $('#killcode', document).val();
            var this_code = this_btn.attr('data-code');

            $.ajax({
                url: PECO.base_url() + 'setup/executetransreset',
                type: 'post',
                data: {'code': this_code, 'killcode': kill_code},
                dataType: 'json',
                beforeSend: function() {
                    PECO.btnLoading(this_btn, 'Processing..');
                }
            }).done(function(d) {
                if(d.qry == true) {
                    PECO.btnSuccess(this_btn, 'Done!', this_html);
                }else{
                    PECO.btnErrorPHP(this_btn, 'Error Code!', this_html);
                }
            }).fail(function() {
                PECO.btnErrorPHP(this_btn, 'PHP error', this_html);
            });
        });
    };

    var init_login_frm = function() {
        $(document).on('submit', '#form_login', function (e) {
            var form = $(this);
            e.preventDefault();

            $.ajax({
                url: form.attr('action'),
                type: form.attr('method'),
                data: form.serialize(),
                dataType: "json",
                beforeSend: function () {
                    PECO.start_pageLogin_loading({
                        animate: true,
                        message: '<span class="text-info animated fadeInDown fast"><i class="fa fa-circle-o-notch fa-spin"></i> Authenticating....</span>',
                        messageSize: '18px'
                    });
                }
            }).done(function (data) {
                if (data) {
                    if (data.num > 0) {
                        PECO.stop_pageLogin_loading();
                        setTimeout(function () {
                            window.location = PECO.base_url() + 'setup/db';
                        }, 1500);
                    } else {
                        PECO.stop_pageLogin_loading();
                        $('.login-box-body').find('.form-group').addClass('has-error');
                        $('.login-box-body').removeClass('fadeInUp').addClass('shake').fadeTo(1000, 1, function () {
                            $(this).removeClass('shake');
                        });
                    }
                } else {
                    //$('.query-stats').html(data.message);
                    PECO.stop_pageLogin_loading();
                }
            }).fail(function () {
                PECO.stop_pageLogin_loading();
                console.log('Unable to find the PHP file');
            });

        });
    };

    var init_logout_btn = function() {
        $(document).on('click', '#btn_logout', function (e) {
            e.preventDefault();
            swal({
                title: "Are you sure?",
                text: 'Logout setup...',
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes",
                closeOnConfirm: false,
                closeOnCancel: true,
                showLoaderOnConfirm: true
            }, function (isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: PECO.base_url() + 'setup/slogout',
                        type: 'post',
                        dataType: 'json',
                    }).done(function () {
                        swal.close();
                        window.location = PECO.base_url() + 'setup/db';
                    });
                }
            });
        });
    };

    return {
        init: function() {
            init_sys_setup();
        },
        db: function() {
            init_db();
        },
        login: function() {
            init_login_frm();
        }
    }
}();