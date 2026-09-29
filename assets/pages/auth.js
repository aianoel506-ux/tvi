/**
 * Created by SE on 0004, May 4, 2017.
 */
var AUTH = function(){
    var init_loginscreen = function() {

        init_autoceomplete($('#loginusername', document));

        $('body').on('submit', '#form_login', function(e){
            var form = $(this);
            e.preventDefault();

            $.ajax({
                url: form.attr('action'),
                type: form.attr('method'),
                data: form.serialize(),
                dataType: "json",
                beforeSend: function(){
                    PECO.start_pageLogin_loading({animate: true, message: '<span class="text-info animated fadeInDown fast"><i class="fa fa-circle-o-notch fa-spin"></i> Authenticating....</span>', messageSize: '18px'});
                }
            }).done(function(data){
                if(data) {
                    if (data.num > 0) {
                        $('.query-stats').html(data['message']);
                        PECO.stop_pageLogin_loading();
                        PECO.start_pageLogin_loading({
                            animate: true,
                            message: '<span class="text-success animated fadeInUp fast">Hello, ' + data['username'] + '!</span>',
                            messageSize: '35px'
                        });
                        setTimeout(function () {
                            window.location = PECO.base_url() + data.segs;
                        }, 1500);
                    } else {
                        $('.query-stats').html(data['message']);
                        PECO.stop_pageLogin_loading();
                        $('.login-box-body').find('.form-group').addClass('has-error');
                        $('.login-box-body').removeClass('flipInY').addClass('shake').fadeTo(1000, 1, function () {
                            $(this).removeClass('shake');
                        });
                    }
                }else{
                    //$('.query-stats').html(data.message);
                    PECO.stop_pageLogin_loading();
                }
            }).fail(function(){
                PECO.stop_pageLogin_loading();
                console.log('Unable to find the PHP file');
            });

        });
    };

    var init_lockscreen = function() {

        $('body').on('submit', '#form_unlock', function(e){
            var form = $(this);
            e.preventDefault();
            $.ajax({
                url: form.attr('action'),
                type: form.attr('method'),
                data: form.serialize(),
                dataType: "json",
                beforeSend: function(){
                    PECO.start_pageLogin_loading({animate: true, message: '<span class="text-info animated fadeInDown fast"><i class="fa fa-circle-o-notch fa-spin"></i> Authenticating....</span>', messageSize: '18px'});
                }
            }).done(function(data){
                if(data) {
                    if (data.num > 0) {
                        $('.query-stats').html(data.msg);
                        PECO.stop_pageLogin_loading();
                        PECO.start_pageLogin_loading({
                            animate: true,
                            message: '<span class="text-success animated fadeInUp fast">Hello, ' + data.username + '!</span>',
                            messageSize: '35px'
                        });
                        setTimeout(function () {
                            window.location = PECO.base_url() + data.segs;
                        }, 1500);
                    } else {
                        $('.query-stats').html(data.msg);
                        PECO.stop_pageLogin_loading();
                        $('.login-box-body').find('.form-group').addClass('has-error');
                        $('.login-box-body').removeClass('flipInY').addClass('shake').fadeTo(1000, 1, function () {
                            $(this).removeClass('shake');
                        });
                    }
                }else{
                    //$('.query-stats').html(data.message);
                    PECO.stop_pageLogin_loading();
                }
            }).fail(function(){
                PECO.stop_pageLogin_loading();
                console.log('Unable to find the PHP file');
            });
        });
    };

    var init_backstretch = function() {
        $.backstretch([
            PECO.base_url()+"assets/global/img/bg/2.jpg",
            PECO.base_url()+"assets/global/img/bg/3.jpg",
            PECO.base_url()+"assets/global/img/bg/4.jpg",
            PECO.base_url()+"assets/global/img/bg/5.jpg",
        ], {
            fade: 1000,
            duration: 8000
        });
    };

    var init_autoceomplete = function(el) {
        var r = new Bloodhound({
            datumTokenizer: function (e) {
                return e.tokens
            },
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: {url: PECO.base_url() + "search/employeesearchid?query=%QUERY", wildcard: "%QUERY"}
        });
        r.initialize(), el.typeahead(null, {
            hint: false,
            highlight: true,
            minLength: 1,
            displayKey: "empid",
            source: r.ttAdapter(),
            cache: false,
            templates: {
                suggestion: Handlebars.compile(
                    ['<div class="media">',
                        '<div class="pull-left">',
                        '<div class="media-object">',
                        '<img src="{{img}}" width="50" height="50"/>',
                        '</div>',
                        '</div>',
                        '<div class="media-body">',
                        '<h5 class="media-heading text-primary">' +
                        '<b class="text-glow-yellow">{{name}}</b><br>' +
                        '<p class="small font-green-haze" style="margin-top: 6px;">{{position}}</p>' +
                        '<p class="small">{{department}}</p></h5>',
                        '</div>',
                        '</div>'].join("")),
            },
        }).on('typeahead:selected', function(event, selection) {

        });
    };

    var init_reload_captcha = function() {
        var input_captcha = $('#input_captcha', document);
        var unique = $.now();
        input_captcha.val('');
        input_captcha.focus();
        $('#img_captcha', document).attr('src', PECO.base_url() + 'peco/gencaptcha?' + unique);
    };

    var init_registration = function() {
        PECO.getiCheckPlugin();
        PECO.getSweetAlert();
        var form_register = $('#form_register', document);
        var empid = $('#empid', document);
        var qrystat = $('#qry_stat', document);

        init_autoceomplete(empid);

        // SUBMIT REGISTRATION
        form_register.submit(function(e) {
            qrystat.html('');
            e.preventDefault();
            var form = $(this);
            var this_btn = $('button[type=submit]', form);
            var this_btn_html = this_btn.html();
            $.ajax({
                url: form.attr('action'),
                type: form.attr('method'),
                data: form.serialize(),
                dataType: 'json',
                beforeSend: function () {
                    PECO.btnLoading(this_btn, "Processing...");
                }
            }).done(function(d) {
                if(d.qry==true) {
                    init_reload_captcha();
                    PECO.btnSuccess(this_btn, 'Done', this_btn_html, 'btn-default')
                }else{
                    PECO.btnErrorPHP(this_btn, this_btn_html, 'btn-default');
                }
                qrystat.html('<div class="note note-' + d.func + '">'+d.msg+'</div>');
            }).fail(function() {
                PECO.btnErrorPHP(this_btn, this_btn_html, 'btn-default');
            });
        })

        // RELOAD ANOTHER CAPTCHA CODE
        $(document).on('click', '#btn_captcha_refresh', function(e) {
            e.preventDefault();
            var this_ = $(this);
            $('.fa',this_).addClass('fa-spin fast');
            setTimeout(function() {

                init_reload_captcha();
                $('.fa',this_).removeClass('fa-spin fast');
            }, 1000);
        });
    };

    var init_forgot_password = function() {
        form_forgotpassword

        PECO.getiCheckPlugin();
        PECO.getSweetAlert();
        var form_forgotpassword = $('#form_forgotpassword', document);
        var empid = $('#empid', document);
        var qrystat = $('#qry_stat', document);
        // SUBMIT REGISTRATION
        form_forgotpassword.submit(function(e) {
            qrystat.html('');
            e.preventDefault();
            var form = $(this);
            var this_btn = $('button[type=submit]', form);
            var this_btn_html = this_btn.html();
            $.ajax({
                url: form.attr('action'),
                type: form.attr('method'),
                data: form.serialize(),
                dataType: 'json',
                beforeSend: function () {
                    PECO.btnLoading(this_btn, "Processing...");
                }
            }).done(function(d) {
                if(d.qry==true) {
                    init_reload_captcha();
                    PECO.btnSuccess(this_btn, 'Done', this_btn_html, 'btn-default')
                }else{
                    PECO.btnErrorPHP(this_btn, this_btn_html, 'btn-default');
                }
                qrystat.html('<div class="note note-' + d.func + '">'+d.msg+'</div>');
            }).fail(function() {
                PECO.btnErrorPHP(this_btn, this_btn_html, 'btn-default');
            });
        })

        // RELOAD ANOTHER CAPTCHA CODE
        $(document).on('click', '#btn_captcha_refresh', function(e) {
            e.preventDefault();
            var this_ = $(this);
            $('.fa',this_).addClass('fa-spin fast');
            setTimeout(function() {

                init_reload_captcha();
                $('.fa',this_).removeClass('fa-spin fast');
            }, 1000);
        });
    };


    return {
        lockscreen: function() {
            init_backstretch();
            init_lockscreen();
        },
        loginscreen: function() {
            //init_backstretch();
            init_loginscreen();
        },
        registration: function(){
            init_registration();
        },
        forgotpassword: function(){
            init_forgot_password();
        }
    }
}();