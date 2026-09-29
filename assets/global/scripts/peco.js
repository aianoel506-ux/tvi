/**
 Core script to handle the entire theme and core functions
 **/
var PECO = function () {
    var NotifyTimeOut = 30000;
    var SessionTimeCheck = 5000;
    var max_rate = 50;
    // IE mode
    var isRTL = false;
    var isIE8 = false;
    var isIE9 = false;
    var isIE10 = false;
    var resizeHandlers = [];
    var assetsPath = base_url + 'assets/';
    var globalImgPath = 'global/img/';
    var globalPluginsPath = 'global/plugins/';
    var globalCssPath = 'global/css/';

    // * NOTIFICATION HANDLER * //
    var handlerNotifications = function() {
        $('#header_notification_bar').on('click change', function(){
            var this_ = $(this);
            handlerAjaxTopMenu(this_, 'systems/getusernotifications', 'Loading notification..', true);

            $('#top_menu_context').remove();
        });
        $('#header_inbox_bar').on('click change', function(){
            var this_ = $(this);
            handlerAjaxTopMenu(this_, 'systems/getuserinbox', 'Loading inbox..', true);

            $('#top_menu_context').remove();
        });
        $('#header_task_bar').on('click change', function(){
            var this_ = $(this);
            handlerAjaxTopMenu(this_, 'systems/getusertask', 'Loading tasks..', true);


            $('#top_menu_context').remove();
        });


        handlerContectMenuTopMenu($('.top-menu', document));
    };

    var handlerContectMenuTopMenu = function(el) {
        el.on('contextmenu', '.notification-list > a', function(e) {
            var this_ = $(this);
            e.preventDefault();

            $('#top_menu_context', document).remove();
            var data_text = $('.title', this_).text();
            var this_dataid = this_.attr('data-id');
            var this_datatrnid = this_.attr('data-trnid');
            var this_datarow = this_.attr('data-row');

            var context_menu_list = '<ul id="top_menu_context" class="custom-menu">' +
                '<li style="background: #00A8FF; color: #fff; font-weight: bold;"><i class="fa fa-calendar"></i> '+data_text+'</li>' +
                '<li>' +
                '<a title="'+data_text+'" data-row="'+this_datarow+'" id="btn_mark_read" data-id="'+this_dataid+'" data-trnid="'+this_datatrnid+'" class="btn btn-primary inline" href="javascript:;"><i class="fa fa-search"></i> Mark As Read</a>' +
                '<a title="'+data_text+'" data-row="'+this_datarow+'" id="btn_send_trn_next" data-id="'+this_dataid+'" data-trnid="'+this_datatrnid+'" data-original-title="Send to next route." class="btn btn-info inline" href="javascript:;"><i class="fa fa-send"></i> Send to</a>' +
                '<a title="'+data_text+'" data-row="'+this_datarow+'" id="btn_remove_notification" data-id="'+this_dataid+'" data-trnid="'+this_datatrnid+'" class="btn btn-danger inline" href="javascript:;"><i class="fa fa-times"></i> Remove</a>' +
                '</li>' +
                '</ul>';

            $('body').append(context_menu_list);
            // Show contextmenu
            $(".custom-menu").finish().toggle(100).// In the right position (the mouse)
                css({top: e.pageY + "px", left: e.pageX + "px"});

            var windowHeight = $(window).height()/2;
            var windowWidth = $(window).width()/2;

            if(e.clientY > windowHeight && e.clientX <= windowWidth) {
                $(".custom-menu").css("left", e.clientX);
                $(".custom-menu").css("bottom", $(window).height() - e.clientY);
                $(".custom-menu").css("right", "auto");
                $(".custom-menu").css("top", "auto");
            }

        });

        $(document).click(function(e){
            if ($(".custom-menu").has(e.target).length === 0) {
                $(".custom-menu").hide(100);
                $('#top_menu_context').remove();
                $('.top-menu').find(' ul.sub-menu li').removeClass('info');
            }
        });

        $('#modal_transaction').on('shown.bs.modal', function() {
            $('#top_menu_context').remove();
        });

        $(document).on('click', '.top-nav #btn_mark_read', function(e) {
            e.preventDefault();
            var this_ = $(this);
            $('#top_menu_context').remove();
        });

        $(document).on('click', '.top-nav #btn_remove_notification', function(e) {
            e.preventDefault();
            var this_ = $(this);
            var this_datarow = this_.attr('data-row');
            var this_li_row = this_.closest('li.' + this_datarow);
            this_li_row.remove();
            $('#top_menu_context').remove();
        });
    };

    var handlerNotificationsCnt = function() {

        var this_ = $('#header_notification_bar');
        handlerAjaxTopMenu(this_, 'systems/getusernotifications', 'Loading notification..', false);

        setInterval(function() {
            handlerAjaxTopMenu(this_, 'systems/getusernotifications', 'Loading notification..', false);
        }, NotifyTimeOut);
    };

    var handlerAjaxTopMenu = function(el, url, msg, cont, table) {
        var container_ = $('.dropdown-menu-list', el);
        var cnt = $('#cnt', el);
        var drop_msg = $('.external #msg', el);
        $.ajax({
            url: PECO.base_url() + url,
            type: 'post',
            data: {},
            dataType: 'json',
            beforeSend: function() {
                if(cont == true) {
                    container_.html(PECO.topMenuLoading(container_, msg));
                }
            }
        }).done(function(d) {
            if(cont == true) {
                container_.html(d.html);
            }
            if(d.unread > 0) {
                cnt.html(d.unread);
                drop_msg.html(d.unread + ' pending');
            }else{
                cnt.html('');
                drop_msg.html('no pending');
            }
        }).fail(function(){
            container_.html(PECO.topMenuErrorPHP(container_));
        });
    };


    // * BEGIN: ADITIONAL PLUGINS *//
    var init_fancybox = function() {

        $("<link/>", {
            rel: "stylesheet",
            type: "text/css",
            href: PECO.base_url() + "assets/global/plugins/fancybox/source/jquery.fancybox.css"
        }).appendTo("head");

        var script_arr = [
            "assets/global/plugins/jquery-mixitup/jquery.mixitup.min.js",
            "assets/global/plugins/fancybox/source/jquery.fancybox.pack.js",
        ];
        pluginPatchArrHandle(script_arr, PECO.base_url()).done(function () {
            if(PECO.sysCheckMode()==true) {
                console.log('FancyBox loaded...');
            }
        });
    };
    var init_sweetbootstrap_alert = function() {

        $("<link/>", {
            rel: "stylesheet",
            type: "text/css",
            href: PECO.base_url() + "assets/global/plugins/bootstrap-sweetalert/sweetalert.css"
        }).appendTo("head");

        var script_arr = [
            "assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js",
        ];
        pluginPatchArrHandle(script_arr, PECO.base_url()).done(function () {
            if(PECO.sysCheckMode()==true) {
                console.log('FancyBox loaded...');
            }
        });
    };

    var init_swal_notif = function (title,message,func) {
        var title_ = (title) ? title : 'Notification!';

        switch (func) {
            case 'success':
                sound = 'successful';
                break;
            case 'error':
                sound = 'smallbox';
                break;
            default:
                sound = 'messagebox';
        }

        // Removed playSound call to prevent autoplay errors
        // playSound(sound);
        swal(title_ ,  message, func);
    };

    var init_datepicker_plugins = function() {

        var css_arr = [
            "assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css",
            "assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css",
            "assets/global/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css",
            "assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css",
            "assets/global/plugins/clockface/css/clockface.css",
        ];
        pluginCSSArrHandler(css_arr);

        var script_arr = [
            "assets/global/plugins/moment.min.js",
            "assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js",
            "assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js",
            "assets/global/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js",
            "assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js",
            "assets/global/plugins/clockface/js/clockface.js",
        ];
        pluginPatchArrHandle(script_arr, PECO.base_url()).done(function () {
            if(PECO.sysCheckMode() == true) {
                console.log('FancyBox loaded...');
            }
        });
    };

    var init_input_maxlength = function() {
        var script_arr = [
            "assets/global/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js"
        ];
        pluginPatchArrHandle(script_arr, PECO.base_url()).done(function () {
            if(PECO.sysCheckMode()==true) {
                console.log('Input Maxlength loaded...');
            }
        });
    };

    var init_pulsate = function() {
        var script_arr = [
            "assets/global/plugins/jquery.pulsate.min.js"
        ];
        pluginPatchArrHandle(script_arr, PECO.base_url()).done(function () {
            if(PECO.sysCheckMode()==true) {
                console.log('Pulsate Plugin loaded...');
            }
        });
    };

    var init_icheck = function() {
        $("<link/>", {
            rel: "stylesheet",
            type: "text/css",
            href: PECO.base_url() + "assets/global/plugins/icheck/skins/all.css"
        }).appendTo("head");

        var script_arr = [
            "assets/global/plugins/icheck/icheck.min.js"
        ];
        pluginPatchArrHandle(script_arr, PECO.base_url()).done(function () {
            if(PECO.sysCheckMode()==true) {
                console.log('iCheck loaded...');
            }
        });
    };

    var init_number_format = function() {
        var script_arr = [
            "assets/global/plugins/jquery-number/jquery.number.min.js"
        ];
        pluginPatchArrHandle(script_arr, PECO.base_url()).done(function () {
            if(PECO.sysCheckMode()==true) {
                console.log('jQuery Number loaded...');
            }
        });
    };

    var init_fileinput = function() {
        $("<link/>", {
            rel: "stylesheet",
            type: "text/css",
            href: PECO.base_url() + "assets/global/plugins/bootstrap-fileinput/css/fileinput.css"
        }).appendTo("head");

        $("<link/>", {
            rel: "stylesheet",
            type: "text/css",
            href: PECO.base_url() + "assets/global/plugins/bootstrap-fileinput/themes/explorer/theme.css"
        }).appendTo("head");

        var script_arr = [
            "assets/global/plugins/bootstrap-fileinput/js/fileinput.js",
            "assets/global/plugins/bootstrap-fileinput/js/locales/fr.js",
            "assets/global/plugins/bootstrap-fileinput/themes/explorer/theme.js"
        ];
        pluginPatchArrHandle(script_arr, PECO.base_url()).done(function () {
            if(PECO.sysCheckMode()==true) {
                console.log('FileInput loaded...');
            }
        });
    };
    var init_digital_clock = function() {
        var script_arr = [
            "assets/global/plugins/jquery-clock/js/jquery.MyDigitClock.js"
        ];
        pluginPatchArrHandle(script_arr, PECO.base_url()).done(function () {
            if(PECO.sysCheckMode()==true) {
                console.log('Digital Clock loaded...');
            }
        });
    };

    var init_sessions = function() {
        /*
      setInterval(function(){
          $.ajax({
              url: PECO.base_url()+'admin/checksession',
              type: 'post',
              dataType: 'json',
              data: {'userid': $('#user').val(), 'segs': $('#segs').val()},
          }).done(function(d){
              if(d && d.qry==false) {
                  setTimeout(function() {
                      if (!$('.MessageBoxContainer').length) {
                          $.SmartMessageBox({
                              title: '<span class="session-img"></span> Session Timeout</span>',
                              content: 'Your account has been sign out!',
                              buttons: '[Okay]',
                              buttonsPosition: 'right',
                              buttonClass: 'btn-lg btn-danger',
                              buttonsIcon: 'fa-check',
                          }, function (ButtonPressed) {
                              if (ButtonPressed === "Okay") {
                                    window.location = PECO.base_url();
                              }
                          });
                          setTimeout(function () {
                              $("li.dropdown-user > a > img.img-circle").clone().prependTo('.MsgTitle');
                          }, 300)
                      }
                  },2000);
              }else{
                  var console_arr = {
                      'message': 'Checkking session...',
                      'res': d,
                  };
                  if(PECO.sysCheckMode()==true) {
                      console.log(console_arr);
                  }
              }
          });
      }, SessionTimeCheck);
      */
    };

    var init_alerts_message_box = function (msg, title) {
        setTimeout(function () {
            $.smallBox({
                title: 'Success',
                content: '<b>' + title + '</b>',
                color: "rgba(0,204,0,0.8)",
                icon: "fa fa-check fa-2x fa-fw",
                timeout: NotifyTimeOut
            });
        }, 1000);
    };
    var init_dt_default = function(tbl, msg, options) {
        var msg = (msg) ? '<h4><i class="fa fa-warning text-warning"></i> ' + msg + '</h4>': '<h4><i class="fa fa-warning text warning"></i> No record found! </h4>';
        var dt_options = {};
        if(options == true) {
            dt_options = options;
            dt_options.push(
                {'language': msg}
            );
        }else{
            dt_options = {
                bDestroy: true,
                bPaginate: false,
                bFilter: false,
                bInfo: false,
                bStateSave: true,
                language: {"emptyTable": msg},
            };
        }
        tbl.DataTable(dt_options).clear();
        tbl.DataTable().clear();
    };
    var init_dt_alert = function(tbl, msg, func) {
        var msg = (msg) ? '<h4><i class="fa fa-warning text-warning"></i> ' + msg + '</h4>': '<h4><i class="fa fa-warning text warning"></i> No record found! </h4>';
        tbl.DataTable().clear();
        tbl.DataTable({
            bDestroy: true,
            bPaginate: false,
            bFilter: false,
            bInfo: false,
            bStateSave: true,
            language: {"emptyTable": msg}
        }).clear();

        $('.dataTables_empty', tbl).addClass(func);
    };
    var init_loading_dt = function(tbl, msg) {
        var msg = (msg) ? '<h4 style="margin: 0px 20px;"><i class="fa fa-spinner fa-spin fa-pulse text-info"></i> ' + msg + '</h4>' : '<h4 style="margin: 0px 20px;"><i class="fa fa-spinner fa-spin fa-pulse text-info"></i> Loading...</h4>';
        tbl.DataTable().clear();
        tbl.DataTable({
            bDestroy: true,
            bPaginate: false,
            bFilter: false,
            bInfo: false,
            bStateSave: false,
            language: {"emptyTable": msg},
        }).clear();
    };

    var init_alerts_dt = function (tbl, msg) {
        var msg = (msg) ? msg : '<i class="fa fa-warning text-danger"></i> PHP Error.';
        tbl.DataTable({
            bDestroy: true,
            bPaginate: false,
            bFilter: false,
            bInfo: false,
            bStateSave: true,
            language: {"emptyTable": msg},
        }).clear();
    };

    var init_alerts_toastr = function (msg, title, func, timeout, box, shake, number) {
        // msg,
        // title,
        // func,
        // timeout == MILISECONDS,
        // box == "big" / "small",
        // shake == BOOLEAN,
        // number == NUMBER(INT) / zero default;
        var timeout = (timeout) ? timeout : 10000;
        var shake = (shake) ? 'shake animated' : '';
        var number = (number>0) ? number : '';
        var sound;

        switch (func) {
            case 'error':
                color = "rgba(158, 37, 10, 0.7)";
                icon = "fa fa-times";
                break;
            case 'warning':
                color = "rgba(201, 107, 6, 0.7)";
                icon = "fa fa-warning";
                break;
            case 'info':
                color = "rgba(10, 121, 158, 0.7)";
                icon = "fa fa-info";
                break;
            case 'success':
                color = "rgba(79, 158, 11, 0.7)";
                icon = "fa fa-check";
                sound = 'successful';
                break;
            default:
                color = "rgba(55, 5, 201, 0.7)";
                icon = "fa fa-bell";
                sound = 'messagebox';
        }
        if(box=='big') {
            $.bigBox({
                title: '<b>' + title + '</b>',
                content: msg,
                color: color,
                icon : icon + ' ' + shake,
                number : number,
                timeout: timeout,
                soundurl: sound,
            });
        }else {
            $.smallBox({
                title: '<b>' + title + '</b>',
                content: msg,
                color: color,
                timeout: timeout,
                icon: icon + ' ' + shake,
                number: number,
                soundurl: sound,
            });
        }
        /*
        if (onclick == true) {
            toastr.options = {
                "onclick": null,
                "closeButton": true,
                "debug": false,
                "positionClass": "toast-top-right",
                "showDuration": "1000",
                "hideDuration": "1000",
                "timeOut": "5000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut",
                "preventDuplicates": dup,
            }
        } else {
            toastr.options = {
                "closeButton": true,
                "debug": false,
                "positionClass": "toast-top-right",
                "showDuration": "1000",
                "hideDuration": "1000",
                "timeOut": "5000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut",
                "preventDuplicates": dup,
            }
        }
        toastr[func](msg, title);
        var sound_src;
        if(func=='success') {
            sound_src = "successful";
        }else{
            sound_src = "successful";
        }

        if (isIE8orlower() == 0) {
            var audioElement = document.createElement('audio');
            if (navigator.userAgent.match('Firefox/'))
                audioElement.setAttribute('src', PECO.base_url()+'assets/global/sound/'+sound_src+'.ogg');
            else
                audioElement.setAttribute('src', PECO.base_url()+'assets/global/sound/'+sound_src+'.mp3');

            $.get();
            audioElement.addEventListener("load", function () {
                audioElement.play();
            }, true);
            audioElement.pause();
            audioElement.play();
        }
        */
    };

    var isIE8orlower = function () {
        var msg = "0";
        var ver = getInternetExplorerVersion();
        if (ver > -1) {
            if (ver >= 9.0)
                msg = 0;
            else
                msg = 1;
        }
        return msg;
    };

    var playSound = function (filename) {
        if (isIE8orlower() == 0) {
            var audioElement = document.createElement('audio');
            if (navigator.userAgent.match('Firefox/'))
                audioElement.setAttribute('src', PECO.base_url()+'assets/global/sounds/'+filename+'.ogg');
            else
                audioElement.setAttribute('src', PECO.base_url()+'assets/global/sounds/'+filename+'.mp3');

            // Add error handling for autoplay policy
            audioElement.addEventListener("load", function () {
                var playPromise = audioElement.play();
                if (playPromise !== undefined) {
                    playPromise.catch(function(error) {
                        // Auto-play was prevented, silently ignore
                        console.log('Audio autoplay prevented:', error);
                    });
                }
            }, true);

            // Try to play with error handling
            var playPromise = audioElement.play();
            if (playPromise !== undefined) {
                playPromise.catch(function(error) {
                    // Auto-play was prevented, silently ignore
                    console.log('Audio autoplay prevented:', error);
                });
            }
        }
    };

    // theme layout color set

    var brandColors = {
        'blue': '#89C4F4',
        'red': '#F3565D',
        'green': '#1bbc9b',
        'purple': '#9b59b6',
        'grey': '#95a5a6',
        'yellow': '#F8CB00'
    };

    // initializes main settings
    var handleInit = function () {


        $('#modal_ajax').modal('hide');
        $('#modal_transaction').modal('hide');

        if (jQuery().select2) {
            $('body').find('input').each(function () {
                $(this).change(function(){
                    $('#search_highlights').removeClass('bg-blue');
                });
            });
        }
        $("button[type='reset']").on("click", function(event){
            if($('input').select2) {
                $('input').select2('val', '');
            }
        });

        if ($('body').css('direction') === 'rtl') {
            isRTL = true;
        }

        isIE8 = !!navigator.userAgent.match(/MSIE 8.0/);
        isIE9 = !!navigator.userAgent.match(/MSIE 9.0/);
        isIE10 = !!navigator.userAgent.match(/MSIE 10.0/);

        if (isIE10) {
            $('html').addClass('ie10'); // detect IE10 version
        }

        if (isIE10 || isIE9 || isIE8) {
            $('html').addClass('ie'); // detect IE10 version
        }

        if (jQuery().popover) {
            $(document).on('click', '.close', function () {
                $(this).closest('div.popover').popover('hide');
            });
        }
    };


    var handlerAjaxModal = function() {
        $(document).on('click', '[data-toggle="ajax-modal"]', function(e) {

            var el = $('body', document);

            PECO.blockUI({
                target: el,
                animate: true,
                overlayColor: '#64A8C8'
            });

            var this_ = $(this);
            var this_menu = this_.closest('.top-menu');
            var this_page = this_.attr('href').replace('#', '');
            var this_title_orig = this_.attr('title');
            var this_title = this_.text();
            var this_data_arr = this_.attr('data-arr'); // MUST BE STRING ARRAY FORMAT EX: 1,2,3
            var data_arr = (this_data_arr) ? this_data_arr : false;
            var this_modal_full = this_.attr('modal-full');
            var this_modal_add_class = this_.attr('modal-class'); // ex: modal-sm user-custom-class



            var this_data_view = this_.attr('data-view');
            var data_view = (this_data_view) ? this_data_view :false;

            // ##############################
            // ARRAY SAMPLE
            // ##############################

            var this_modal = $('#modal_ajax', document);
            var this_modal_content = $('#modal_content', this_modal);
            var this_modal_title = $('#modal_title', this_modal);
            if(this_modal_full==1) {
                this_modal.addClass('modal-full');
            }

            if(this_modal_add_class == 'modal-sm') {
                $('.modal-dialog', this_modal).removeClass('modal-lg').addClass('modal-sm');
            }else{
                $('.modal-dialog', this_modal).removeClass('modal-sm').addClass('modal-lg');
            }
            this_modal.addClass(this_modal_add_class);
            this_modal_content.load(PECO.base_url() + 'ajax/' + this_page,
                {'ids': data_arr, 'view': data_view},
                function(d){
                    this_modal.appendTo("body").modal('show');
                    if(this_title_orig != '') {
                        this_modal_title.html(this_title_orig);
                    }else{
                        this_modal_title.html(this_title);
                    }

                    $('.dropdown.open .dropdown-toggle', this_menu).dropdown('toggle');

                    PECO.unblockUI(el);
                });


            $("#modal_ajax", document).draggable({
                handle: ".modal-header"
            });

            $('#modal_ajax').on('hidden', function () {
                this_modal_content.html('');
            });

            setTimeout(function() {
                PECO.unblockUI(el);
            }, 10000);
            return false;
        });

    };

    var handlerCorporationSearch = function() {
        var corpname = $('#corpname', document);
        var brancname = $('#corpbranch', document);
        var lastname = $('.rep #lastname', document);
        var firstname = $('.rep #firstname', document);
        var middlename = $('.rep #middlename', document);

        var a = new Bloodhound({
            datumTokenizer: function (e) {
                return e.tokens
            },
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: {url: PECO.base_url() + "search/corpsearch?query=%QUERY", wildcard: "%QUERY"}
        });

        a.initialize(), corpname.typeahead(null, {
            hint: false,
            highlight: true,
            minLength: 1,
            displayKey: "corpname",
            source: a.ttAdapter(),
            cache: false,
            templates: {
                suggestion: Handlebars.compile(['<div class="media">', '<div class="pull-left">', '<div class="media-object">', '<img src="{{img}}" width="50" height="50"/>', "</div>", "</div>", '<div class="media-body">', '<h5 class="media-heading text-primary"><b class="text-glow-yellow">{{corpname}}</b></h5>', "<p>{{corpbranch}} - {{corpaddr}}</p>", "</div>", "</div>"].join("")),
            },
        }).on('typeahead:selected', function(event, selection) {
            brancname.val(selection.corpbranch);
            lastname.val(selection.replname);
            firstname.val(selection.repfname);
            middlename.val(selection.repmname);
        }).click(function() {
            PECO.initElScroller($('.tt-dropdown-menu', document));
        });
    };

    var handlerGovernmentSearch = function() {
        var corpname = $('#corpname', document);
        var brancname = $('#corpbranch', document);
        var lastname = $('.rep #lastname', document);
        var firstname = $('.rep #firstname', document);
        var middlename = $('.rep #middlename', document);

        var a = new Bloodhound({
            datumTokenizer: function (e) {
                return e.tokens
            },
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: {url: PECO.base_url() + "search/govsearch?query=%QUERY", wildcard: "%QUERY"}
        });

        a.initialize(), corpname.typeahead(null, {
            hint: false,
            highlight: true,
            minLength: 1,
            displayKey: "govname",
            source: a.ttAdapter(),
            cache: false,
            templates: {
                suggestion: Handlebars.compile(['<div class="media">', '<div class="pull-left">', '<div class="media-object">', '<img src="{{img}}" width="50" height="50"/>', "</div>", "</div>", '<div class="media-body">', '<h5 class="media-heading text-primary"><b class="text-glow-yellow">{{govname}}</b></h5>', "<p>{{govbranch}} - {{corpaddr}}</p>", "</div>", "</div>"].join("")),
            }
        }).on('typeahead:selected', function(event, selection) {
            brancname.val(selection.govbranch);
            lastname.val(selection.replname);
            firstname.val(selection.repfname);
            middlename.val(selection.repmname);
        }).click(function() {
            PECO.initElScroller($('.tt-dropdown-menu', document));
        });
    };

    var init_handler_complaints_input_basic = function() {

        var select_district = $('#select_district', document);
        var select_priority = $('#select_priority', document);
        var select_sm_info = $('#select_sm_info', document);
        var select_landmark = $('#select_landmark', document);
        var select_barangay = $('#select_barangay', document);
        var re_acctid = $('#re_acctid', document);
        var lastname = $('#lastname', document);
        var firstname = $('#firstname', document);
        var middlename = $('#middlename', document);
        var address = $('#address', document);
        var contact = $('#contactno', document);


        PECO.select2Basic(select_district, 'user/getdistrictselect', 'Select District..', true, false, false);
        PECO.select2Basic(select_priority, 'user/getpriorityselect', 'Select Priority..', false, false, false);
        PECO.customerSelectTagging(re_acctid, 'Tag account..'); // WITH LABEL

        var a = new Bloodhound({
            datumTokenizer: function (e) {
                return e.tokens
            },
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: {url: PECO.base_url() + "search/personsearch?query=%QUERY", wildcard: "%QUERY"}
        });

        a.initialize(), lastname.typeahead(null, {
            hint: false,
            highlight: true,
            minLength: 1,
            displayKey: "lastname",
            source: a.ttAdapter(),
            cache: false,
            templates: {
                suggestion: Handlebars.compile(['<div class="media">', '<div class="pull-left">', '<div class="media-object">', '<img src="{{img}}" width="50" height="50"/>', "</div>", "</div>", '<div class="media-body">', '<h5 class="media-heading text-primary"><b class="text-glow-yellow">{{lastname}}</b>, {{firstname}} {{middlename}}</h5>', "<p>{{district}} - {{addr}}</p>", "</div>", "</div>"].join("")),
            },
        }).on('typeahead:selected', function(event, selection) {
            firstname.val(selection.firstname);
            middlename.val(selection.middlename);
            address.val(selection.addr);
            contact.val(selection.contact);
            select_district.val(selection.distid).trigger('change');
            if(selection.distid>0) {
                brgyHandler(select_barangay, selection.distid, selection.brgyid, 'close');
                landMarkSelectHandler(select_landmark, selection.distid, selection.brgyid, selection.landarr, 'close');
            }
            // clearing the selection requires a typeahead method
            // $(this).typeahead('setQuery', '');

            $('.complainants .form-group', document).each(function(){
                $(this).addClass('has-success');
            });
        }).click(function() {
            PECO.initElScroller($('.tt-dropdown-menu', document));
        });


        select_district.change(function(e){
            var this_ = $(this);
            var this_dist_val = this_.val();
            if(this_dist_val != '') {

                brgyHandler(select_barangay, this_dist_val, false, true);
                // PECO.select2Basic(select_barangay, "user/getbarangay",'Select Brgy..');

                select_barangay.on('change', function(){
                    var this_barangay = $(this);
                    var this_barangay_vals = this_barangay.val();
                    if(this_barangay_vals!='') {
                        landMarkSelectHandler(select_landmark, this_dist_val, this_barangay_vals, false);
                    }else{
                        select_landmark.val('').select2('destroy');
                        select_landmark.attr('readonly', true);
                    }
                });

            }else{
                select_barangay.val('').select2('destroy');
                select_barangay.attr('readonly', true);

                select_landmark.val('').select2('destroy');
                select_landmark.attr('readonly', true);
            }
        });
    };

    var init_handler_joborder_inputs = function() {

        var jo_entry = $('#frm_jo_entry', document);
        var select_district = $('#select_district', document);
        var select_priority = $('#select_priority', document);
        var select_landmark = $('#select_landmark', document);
        var select_barangay = $('#select_barangay', document);

        var lastname = $('#lastname', jo_entry);
        var firstname = $('#firstname', jo_entry);
        var middlename = $('#middlename', jo_entry);
        var contact = $('#contactno', document);

        var acctsearch = $('#acctsearch', document);

        PECO.select2Basic(select_priority, 'user/getpriorityselect', 'Select Priority..', false, false, false);

        //PECO.customerSelectTagging(re_acctid, 'Tag account..'); // WITH LABEL


        var a = new Bloodhound({
            datumTokenizer: function (e) {
                return e.tokens
            },
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: {url: PECO.base_url() + "search/personsearch?query=%QUERY", wildcard: "%QUERY"}
        });

        a.initialize(), lastname.typeahead(null, {
            hint: false,
            highlight: true,
            minLength: 1,
            displayKey: "lastname",
            source: a.ttAdapter(),
            cache: false,
            templates: {
                suggestion: Handlebars.compile(['<div class="media">', '<div class="pull-left">', '<div class="media-object">', '<img src="{{img}}" width="50" height="50"/>', "</div>", "</div>", '<div class="media-body">', '<h5 class="media-heading text-primary"><b class="text-glow-yellow">{{lastname}}</b>, {{firstname}} {{middlename}}</h5>', "<p>{{district}} - {{addr}}</p>", "</div>", "</div>"].join("")),
            },
        }).on('typeahead:selected', function(event, selection) {
            firstname.val(selection.firstname);
            middlename.val(selection.middlename);
            //address.val(selection.addr);
            contact.val(selection.contact);
            //select_district.val(selection.distid).trigger('change');
            if(selection.distid>0) {
                brgyHandler(select_barangay, selection.distid, selection.brgyid, 'close');
                landMarkSelectHandler(select_landmark, selection.distid, selection.brgyid, selection.landarr, 'close');
            }
            // clearing the selection requires a typeahead method
            // $(this).typeahead('setQuery', '');

            $('.complainants .form-group', document).each(function(){
                $(this).addClass('has-success');
            });
        });


        var acct = new Bloodhound({
            datumTokenizer: function (e) {
                return e.tokens
            },
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: {url: PECO.base_url() + "search/accountsearch?query=%QUERY", wildcard: "%QUERY"}
        });

        acct.initialize(), acctsearch.typeahead(null, {
            hint: false,
            highlight: true,
            minLength: 1,
            displayKey: "text",
            source: acct.ttAdapter(),
            cache: false,
            templates: {
                suggestion: Handlebars.compile(['<div class="media">', '<div class="pull-left">', '<div class="media-object">', '<img src="{{pics}}" width="50" height="50"/>', "</div>", "</div>", '<div class="media-body">', '<h5 class="media-heading text-primary"><b class="text-glow-yellow">{{text}}</b>, {{name}}</h5>', "<p>{{addr}}</p>", "</div>", "</div>"].join("")),
            },
        }).on('typeahead:selected', function(event, selection) {

            $('#acctid', document).val(selection.id);
            $.ajax({
                url: PECO.base_url() + 'peco/getaccountinfo',
                type: 'post',
                data: {'id': selection.id},
                dataType: 'json',
            }).done(function(d) {

                $('#jo_acct_name', document).html(d.name);
                $('#jo_acct_addr', document).html(d.addr);
                $('#jo_acct_mtrno', document).html(d.mtrno);
                $('#jo_acct_serial', document).html(d.serial);

            });
        });
    };



    var handlerMeterSearchForm = function() {

        var html = '';

        html += '<input required id="assetid" value="" type="hidden" name="assetid" />';
        html += '<ul class="list-group summary column no-border">';
        html += '<li class="list-group-item">';
        html += '<span class="col-md-4 label-name">Meter Number</span>';
        html += '<span class="col-md-8" style="margin: 0px 0px; padding: 0px 0px;">';
        html += '<div class="input-group">';
        html += '<input class="form-control input-sm" id="mtrsearch" name="label" placeholder="Search meter number.."/>';
        html += '<span class="input-group-btn" >';
        html += '<a id="btn_asset_mtrview" class="btn btn-default" href="#">View</a>';
        html += '<a title="Search Meter" data-arr="1" href="#tbl_utility_meterlist" data-toggle="ajax-modal" class="btn btn-default"><i class="fa fa-search"></i></a>';
        html += '</span>';
        html += '</div>';
        html += '</span>';
        html += '</li>';
        html += '<li class="list-group-item">';
        html += '<span class="col-md-4 label-name">Serial</span>';
        html += '<span class="col-md-8 label-default display-text" id="mis_serial">N/A</span>';
        html += '</li>';
        html += '<li class="list-group-item">';
        html += '<span class="col-md-4 label-name">Type</span>';
        html += '<span class="col-md-8 label-default display-text" id="mis_type">N/A</span>';
        html += '</li>';
        html += '<li class="list-group-item">';
        html += '<span class="col-md-4 label-name">Brand</span>';
        html += '<span class="col-md-8 label-default display-text" id="mis_brand">N/A</span>';
        html += '</li>';
        html += '<li class="list-group-item">';
        html += '<span class="col-md-4 label-name">Volts</span>';
        html += '<span class="col-md-8 label-default display-text" id="mis_volts">N/A</span>';
        html += '</li>';
        html += '<li class="list-group-item">';
        html += '<span class="col-md-4 label-name">Ampere</span>';
        html += '<span class="col-md-8 label-default display-text" id="mis_ampere">N/A</span>';
        html += '</li>';
        html += '<li class="list-group-item">';
        html += '<span class="col-md-4 label-name">PECO Seal</span>';
        html += '<span class="col-md-8 label-default display-text" id="mis_pecoseal">N/A</span>';
        html += '</li>';
        html += '<li class="list-group-item">';
        html += '<span class="col-md-4 label-name">ERC Seal</span>';
        html += '<span class="col-md-8 label-default display-text" id="mis_ercseal">N/A</span>';
        html += '</li>';
        html += '<li class="list-group-item">';
        html += '<span class="col-md-4 label-name">Wire Size</span>';
        html += '<span class="col-md-8 label-default display-text" id="mis_wiresize">N/A</span>';
        html += '</li>';
        html += '<li class="list-group-item">';
        html += '<span class="col-md-4 label-name">KH</span>';
        html += '<span class="col-md-8 label-default display-text" id="mis_kh">N/A</span>';
        html += '</li>';
        html += '<li class="list-group-item">';
        html += '<span class="col-md-4 label-name">Reading</span>';
        html += '<span class="col-md-8 label-default display-text" id="mis_reading">N/A</span>';
        html += '</li>';
        html += '<li class="list-group-item">';
        html += '<span class="col-md-4 label-name">Status</span>';
        html += '<span class="col-md-8 label-default display-text" id="mis_status">N/A</span>';
        html += '</li>';
        html += '</ul>';

        $('[data-toggle=metersearchform]', document).html(html);


        var mtr_search = $('#mtrsearch', document);

        var m = new Bloodhound({
            datumTokenizer: function (e) {
                return e.tokens
            },
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: {url: PECO.base_url() + "search/metersearch?query=%QUERY", wildcard: "%QUERY"}
        });

        m.initialize(), mtr_search.typeahead(null, {
            hint: false,
            highlight: true,
            minLength: 1,
            displayKey: "label",
            source: m.ttAdapter(),
            cache: false,
            templates: {
                suggestion: Handlebars.compile(['<div class="media">', '<div class="pull-left">', '<div class="media-object">', '<img src="{{pics}}" width="50" height="50"/>', "</div>", "</div>", '<div class="media-body">', '<h5 class="media-heading text-primary"><b class="text-glow-yellow">{{label}}</b>, {{serial}}</h5>', "<p>{{status}}</p>", "</div>", "</div>"].join("")),
            },
        }).on('typeahead:selected', function(event, selection) {
            $('#assetid', document).val(selection.id);
            $('#mis_serial', document).html(selection.serial);
            $('#mis_type', document).html(selection.type);
            $('#mis_brand', document).html(selection.brand);
            $('#mis_volts', document).html(selection.volts);
            $('#mis_ampere', document).html(selection.ampere);
            $('#mis_pecoseal', document).html(selection.pecoseal);
            $('#mis_ercseal', document).html(selection.ercseal);
            $('#mis_wiresize', document).html(selection.wiresize);
            $('#mis_kh', document).html(selection.kh);
            $('#mis_reading', document).html(selection.reading);
            $('#mis_status', document).html(selection.status);
            $('#btn_asset_mtrview', document).attr('href', PECO.base_url()+'module/6052521b7625e31d4ee9cc706732484fcf850877/view/' + selection.id);
        });

        mtr_search.keyup(function(e) {
            var this_ = $(this);
            if(this_.val() == '') {
                $('#assetid', document).val('');
                $('#btn_asset_mtrview', document).attr('href', '#');

                $('.display-text', document).each(function() {
                    $(this).html('N/A');
                });
            }
        });
    };


    var handler_complaints_entry = function() {
        var frm_ticket_entry = $('#frm_ticket_entry', document);
        var select_complaints = $('#select_complaints', document);

        init_handler_complaints_input_basic();

        PECO.handleriCheckForm(frm_ticket_entry);

        PECO.select2Basic(select_complaints, 'user/getcomplaints', 'Select Complaints..', true, false, false);



        frm_ticket_entry.submit(function(e) {
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
                        if(d.qry==true) {
                            if(typeof TS !== 'undefined') {
                                TS.list(false, 1, false);
                            }
                        }
                        swal(d.title, d.msg, d.func);
                    }).fail(function(){
                        swal("Error404: PHP", "Server side error!", "error");
                    });
                }else{
                    swal.close();
                }
            });

        });

    };

    var handlerTroubleCall = function(int) {

        var frm_ticket_entry = $('#frm_ticket_entry', document);
        var select_outage = $('#select_outage', document);
        var select_complaints = $('#select_complaints', document);

        PECO.handleriCheckForm(frm_ticket_entry);
        PECO.select2Basic(select_outage, 'user/getoutage', 'Select Concerns..', true, false, false);
        PECO.select2Basic(select_complaints, 'user/getcomplaints', 'Select Complaints..', true, false, false);

        init_handler_complaints_input_basic();

        frm_ticket_entry.submit(function(e) {
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
                        if(d.qry==true) {
                            if(typeof TS !== 'undefined') {
                                TS.list(false, 1, false);
                            }
                        }
                        swal(d.title, d.msg, d.func);
                    }).fail(function(){
                        swal("Error404: PHP", "Server side error!", "error");
                    });
                }else{
                    swal.close();
                }
            });
        });


    };

    var handlerCustomerDataEntry = function(int) {

        var frm_ticket_entry = $('#frm_ticket_entry', document);
        var select_concern = $('#select_concern', document);
        var select_complaints = $('#select_complaints', document);
        var select_sm_info = $('#select_sm_info', document);

        // PECO.handleriCheckForm(frm_ticket_entry);
        PECO.select2Basic(select_concern, 'user/select2concerns', 'Select Concerns..', true, false, false);
        PECO.select2Basic(select_complaints, 'user/getcomplaints', 'Select Complaints..', true, false, false);

        init_handler_complaints_input_basic();

        $('.icheck-inline .icheck', frm_ticket_entry).each(function(){
            $(this).iCheck({
                checkboxClass: 'icheckbox_square-red', // minimal / square / polaris / futurico // red / green / blue
                radioClass: 'iradio_square-red',
                increaseArea: '20%' // optional
            }).on('ifChecked', function(){
                var this_ = $(this);
                this_.attr('checked', true);
                // facebook 393
                // @TODO Create query of sub select2 for info.
                if(this_.val() == 393) {
                    var sm_info_arr = [
                        {id: 1, text: 'Referrals'},
                        {id: 2, text: 'Boosted Post'},
                        {id: 3, text: 'Personal Messages'}
                    ];

                    select_sm_info.attr('disabled', false);
                    select_sm_info.select2({
                        data: sm_info_arr,
                        allowClear: true,
                        placeholder: 'Select FB info..'
                    });
                }else{
                    select_sm_info.val('').trigger('change');
                    select_sm_info.attr('disabled', true);
                }
            }).on('ifUnchecked', function(){
                var this_ = $(this);
                this_.attr('checked', false);
            });
        });

        frm_ticket_entry.submit(function(e) {
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
                        if(d.qry==true) {
                            if(typeof TS !== 'undefined') {
                                TS.list(false, 1, false);
                            }
                        }
                        swal(d.title, d.msg, d.func);
                    }).fail(function(){
                        swal("Error404: PHP", "Server side error!", "error");
                    });
                }else{
                    swal.close();
                }
            });
        });


    };

    var handlerJobOrder = function(view) {

        var frm_ticket_entry = $('#frm_jo_entry', document);
        var select_joborder = $('#select2joborder', document);

        PECO.handleriCheckForm(frm_ticket_entry);
        PECO.select2Basic(select_joborder, 'jo/select2joborders', 'Select Joborder..', true);

        init_handler_joborder_inputs();

        frm_ticket_entry.submit(function(e) {
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
                        if(d.qry == true) {
                            if(typeof JO !== 'undefined') {
                                JO.table(d.view)
                            } else {
                                console.log('JO Function not found!');
                            }
                        }
                        swal(d.title, d.msg, d.func);
                    }).fail(function(){
                        swal("Error404: PHP", "Server side error!", "error");
                    });
                }else{
                    swal.close();
                }
            });

        });


    };

    var init_email_submit = function() {
        $(document).on('submit', '#frm_submit_email', function(e) {
            var form = $(this);
            e.preventDefault();
            swal({
                title: "Are you sure?",
                text: 'Send email.',
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Send",
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
    };


    var handlerTechlogEntry = function() {
        $(document).on('submit', '#frm_techlog_entry', function(e) {
            var form = $(this);
            e.preventDefault();
            swal({
                title: "Are you sure?",
                text: 'Adding new Tech Issue Report',
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
                        if(d.qry==true) {
                            if(typeof TECHLOG !== 'undefined') {
                                TECHLOG.list(false, 1, false);
                            }
                        }
                        swal(d.title, d.msg, d.func);
                    }).fail(function(){
                        swal("Error404: PHP", "Server side error!", "error");
                    });
                }else{
                    swal.close();
                }
            });
        });
    };

    var handlerApprehensionEntry = function() {
        var frm_apprehension_entry = $('#frm_apprehension_entry', document);
        var select2apprehension = $('#select2apprehension', document);
        PECO.select2Basic(select2apprehension, "query/apprehensions", 'Apprehension Type..', false, false);

        PECO.handleriCheckForm(frm_apprehension_entry);

        init_handler_complaints_input_basic();

        frm_apprehension_entry.submit(function(e) {
            var form = $(this);
            e.preventDefault();
            swal({
                title: "Are you sure?",
                text: 'Adding Apprehension Record',
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
                        if(d.qry==true) {
                            if(typeof TS !== 'undefined') {
                                TS.list(false, 1, false);
                            }
                        }
                        swal(d.title, d.msg, d.func);
                    }).fail(function(){
                        swal("Error404: PHP", "Server side error!", "error");
                    });
                }else{
                    swal.close();
                }
            });

        });
    };

    var brgyHandler = function(el, distid, initdata, mode, focus) {
        var mode_ = (mode) ? mode : 'open';
        var focus_ = (focus) ? focus : false;
        $.ajax({
            url: PECO.base_url() + 'user/getbarangay',
            dataType: 'json',
            data: {'dist': distid},
            type: "POST",
        }).done(function (d) {
            if(d) {
                el.val('').trigger('change.select2');
                el.attr('readonly', false);
                el.select2({
                    allowClear: true,
                    placeholder: 'Select Brgy..',
                    data: d.list,
                    formatResult: formatDataListBasic, // omitted for brevity, see the source of this page
                    formatSelection: formatDataSelectionBasic, // omitted for brevity, see the source of this page
                    width: 'resolve', // 100% or resolve
                });
                if(focus_) {
                    el.select2(mode_).focus();
                }
                if (initdata) {
                    el.val(initdata).trigger('change.select2');
                }
                PECO.select2_slimscroller();
            }else{
                el.attr('readonly', true);
                el.select2({
                    allowClear: true,
                    placeholder: 'No data found!',
                    width: 'resolve'
                });
            }
        }).fail(function() {
            el.attr('readonly', true);
            el.select2({
                allowClear: true,
                placeholder: 'PHP Error',
            });
        });
    };

    var landMarkSelectHandler = function(select_landmark, distid, brgyid, initdata, mode) {

        var mode_ = (mode) ? mode : 'open';
        select_landmark.attr('readonly', false);
        select_landmark.select2({
            placeholder: 'Landmark..',
            tags: false,
            multiple: false,
            minimumInputLength: 3,
            //tags: [],
            ajax: {
                url: base_url + "user/getbarangay",
                dataType: 'json',
                type: "POST",
                quietMillis: 50,
                data: function (term) {
                    return {
                        term: term,
                        dist: distid,
                        bargy: brgyid
                    };
                },
                results: function (data) {
                    return {
                        results: $.map(data.list, function (item) {
                            return {
                                text: item.text,
                                id: item.id
                            };
                        })
                    };
                }
            },
            initSelection: function (element, callback) {
                if (initdata) {
                    callback(initdata);
                }
            },
            createSearchChoice: function (term, data) {
                if ($(data.list).filter(function () {
                    return this.text.localeCompare(term) === 0;
                }).length === 0) {
                    return {id: term, text: term + ' - <i class="fa fa-plus text-primary"></i> Add'};
                }
            },
            escapeMarkup: function (markup) {
                return markup;
            }, // let our custom formatter work
            formatResult: formatDataListBasic, // omitted for brevity, see the source of this page
            formatSelection: formatDataSelectionFull, // omitted for brevity, see the source of this page
        }).select2("val", []);
        if(mode) {
            select_landmark.select2(mode_).focus();
        }
    };

    // iCHECK SETUP
    var init_handler_icheck_form = function(this_content) {
        $('.icheck-inline .icheck', this_content).each(function(){
            $(this).iCheck({
                checkboxClass: 'icheckbox_square-red', // minimal / square / polaris / futurico // red / green / blue
                radioClass: 'iradio_square-red',
                increaseArea: '20%' // optional
            }).on('ifChecked', function(){
                var this_ = $(this);
                this_.attr('checked', true);
            }).on('ifUnchecked', function(){
                var this_ = $(this);
                this_.attr('checked', false);
            });
        });
    };


    // runs callback functions set by PECO.addResponsiveHandler().
    var _runResizeHandlers = function () {
        // reinitialize other subscribed elements
        for (var i = 0; i < resizeHandlers.length; i++) {
            var each = resizeHandlers[i];
            each.call();
        }
    };

    // handle the layout reinitialization on window resize
    var handleOnResize = function () {
        var resize;
        if (isIE8) {
            var currheight;
            $(window).resize(function () {
                if (currheight == document.documentElement.clientHeight) {
                    return; //quite event since only body resized not window.
                }
                if (resize) {
                    clearTimeout(resize);
                }
                resize = setTimeout(function () {
                    _runResizeHandlers();
                }, 50); // wait 50ms until window resize finishes.                
                currheight = document.documentElement.clientHeight; // store last body client height
            });
        } else {
            $(window).resize(function () {
                if (resize) {
                    clearTimeout(resize);
                }
                resize = setTimeout(function () {
                    _runResizeHandlers();
                }, 50); // wait 50ms until window resize finishes.
            });
        }
    };

    // Handles portlet tools & actions
    var handlePortletTools = function () {
        // handle portlet remove
        $('body').on('click', '.portlet > .portlet-title > .tools > a.remove', function (e) {
            e.preventDefault();
            var portlet = $(this).closest(".portlet");

            if ($('body').hasClass('page-portlet-fullscreen')) {
                $('body').removeClass('page-portlet-fullscreen');
            }

            portlet.find('.portlet-title .fullscreen').tooltip('destroy');
            portlet.find('.portlet-title > .tools > .reload').tooltip('destroy');
            portlet.find('.portlet-title > .tools > .remove').tooltip('destroy');
            portlet.find('.portlet-title > .tools > .config').tooltip('destroy');
            portlet.find('.portlet-title > .tools > .collapse, .portlet > .portlet-title > .tools > .expand').tooltip('destroy');

            portlet.remove();
        });

        // handle portlet fullscreen
        $('body').on('click', '.portlet > .portlet-title .fullscreen', function (e) {
            e.preventDefault();
            var portlet = $(this).closest(".portlet");
            if (portlet.hasClass('portlet-fullscreen')) {
                $(this).removeClass('on');
                portlet.removeClass('portlet-fullscreen');
                $('body').removeClass('page-portlet-fullscreen');
                portlet.children('.portlet-body').css('height', 'auto');
            } else {
                var height = PECO.getViewPort().height -
                    portlet.children('.portlet-title').outerHeight() -
                    parseInt(portlet.children('.portlet-body').css('padding-top')) -
                    parseInt(portlet.children('.portlet-body').css('padding-bottom'));

                $(this).addClass('on');
                portlet.addClass('portlet-fullscreen');
                $('body').addClass('page-portlet-fullscreen');
                portlet.children('.portlet-body').css('height', height);
            }
        });

        $('body').on('click', '.portlet > .portlet-title > .tools > a.reload', function (e) {
            e.preventDefault();
            var el = $(this).closest(".portlet").children(".portlet-body");
            var url = $(this).attr("data-url");
            var error = $(this).attr("data-error-display");
            if (url) {
                PECO.blockUI({
                    target: el,
                    animate: true,
                    overlayColor: 'none'
                });
                $.ajax({
                    type: "GET",
                    cache: false,
                    url: url,
                    dataType: "html",
                    success: function (res) {
                        PECO.unblockUI(el);
                        el.html(res);
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        PECO.unblockUI(el);
                        var msg = 'Error on reloading the content. Please check your connection and try again.';
                        if (error == "toastr" && toastr) {
                            toastr.error(msg);
                        } else if (error == "notific8" && $.notific8) {
                            $.notific8('zindex', 11500);
                            $.notific8(msg, {
                                theme: 'ruby',
                                life: 3000
                            });
                        } else {
                            alert(msg);
                        }
                    }
                });
            } else {
                // for demo purpose
                PECO.blockUI({
                    target: el,
                    animate: true,
                    overlayColor: 'none'
                });
                window.setTimeout(function () {
                    PECO.unblockUI(el);
                }, 1000);
            }
        });

        // load ajax data on page init
        $('.portlet .portlet-title a.reload[data-load="true"]').click();

        $('body').on('click', '.portlet > .portlet-title > .tools > .collapse, .portlet .portlet-title > .tools > .expand', function (e) {
            e.preventDefault();
            var el = $(this).closest(".portlet").children(".portlet-body");
            if ($(this).hasClass("collapse")) {
                $(this).removeClass("collapse").addClass("expand");
                el.slideUp(200);
            } else {
                $(this).removeClass("expand").addClass("collapse");
                el.slideDown(200);
            }
        });
    };

    // Handles custom checkboxes & radios using jQuery Uniform plugin
    var handleUniform = function () {
        if (!$().uniform) {
            return;
        }
        var test = $("input[type=checkbox]:not(.toggle, .md-check, .md-radiobtn, .make-switch, .icheck), input[type=radio]:not(.toggle, .md-check, .md-radiobtn, .star, .make-switch, .icheck)");
        if (test.size() > 0) {
            test.each(function () {
                if ($(this).parents(".checker").size() === 0) {
                    $(this).show();
                    $(this).uniform();
                }
            });
        }
    };

    // Handlesmaterial design checkboxes
    var handleMaterialDesign = function () {

        // Material design ckeckbox and radio effects
        $('body').on('click', '.md-checkbox > label, .md-radio > label', function () {
            var the = $(this);
            // find the first span which is our circle/bubble
            var el = $(this).children('span:first-child');

            // add the bubble class (we do this so it doesnt show on page load)
            el.addClass('inc');

            // clone it
            var newone = el.clone(true);

            // add the cloned version before our original
            el.before(newone);

            // remove the original so that it is ready to run on next click
            $("." + el.attr("class") + ":last", the).remove();
        });

        if ($('body').hasClass('page-md')) {
            // Material design click effect
            // credit where credit's due; http://thecodeplayer.com/walkthrough/ripple-click-effect-google-material-design       
            $('body').on('click', 'a.btn, button.btn, input.btn, label.btn', function (e) {
                var element, circle, d, x, y;

                element = $(this);

                if (element.find(".md-click-circle").length == 0) {
                    element.prepend("<span class='md-click-circle'></span>");
                }

                circle = element.find(".md-click-circle");
                circle.removeClass("md-click-animate");

                if (!circle.height() && !circle.width()) {
                    d = Math.max(element.outerWidth(), element.outerHeight());
                    circle.css({height: d, width: d});
                }

                x = e.pageX - element.offset().left - circle.width() / 2;
                y = e.pageY - element.offset().top - circle.height() / 2;

                circle.css({top: y + 'px', left: x + 'px'}).addClass("md-click-animate");
            });
        }

        // Floating labels
        var handleInput = function (el) {
            if (el.val() != "") {
                el.addClass('edited');
            } else {
                el.removeClass('edited');
            }
        };

        $('body').on('keydown', '.form-md-floating-label > .form-control', function (e) {
            handleInput($(this));
        });
        $('body').on('blur', '.form-md-floating-label > .form-control', function (e) {
            handleInput($(this));
        });
    };

    // Handles custom checkboxes & radios using jQuery iCheck plugin
    var handleiCheck = function () {
        if (!$().iCheck) {
            return;
        }

        $('.icheck').each(function () {
            var checkboxClass = $(this).attr('data-checkbox') ? $(this).attr('data-checkbox') : 'icheckbox_minimal-grey';
            var radioClass = $(this).attr('data-radio') ? $(this).attr('data-radio') : 'iradio_minimal-grey';

            if (checkboxClass.indexOf('_line') > -1 || radioClass.indexOf('_line') > -1) {
                $(this).iCheck({
                    checkboxClass: checkboxClass,
                    radioClass: radioClass,
                    insert: '<div class="icheck_line-icon"></div>' + $(this).attr("data-label")
                });
            } else {
                $(this).iCheck({
                    checkboxClass: checkboxClass,
                    radioClass: radioClass
                });
            }
        });
    };

    // Handles Bootstrap switches
    var handleBootstrapSwitch = function () {
        if (!$().bootstrapSwitch) {
            return;
        }
        $('.make-switch').bootstrapSwitch();
    };

    // Handles Bootstrap confirmations
    var handleBootstrapConfirmation = function () {
        if (!$().confirmation) {
            return;
        }
        $('[data-toggle=confirmation]').confirmation({container: 'body', btnOkClass: 'btn-xs btn-success', btnCancelClass: 'btn-xs btn-danger'});
    };

    // Handles Bootstrap Accordions.
    var handleAccordions = function () {
        $('body').on('shown.bs.collapse', '.accordion.scrollable', function (e) {
            PECO.scrollTo($(e.target));
        });
    };

    // Handles Bootstrap Tabs.
    var handleTabs = function () {
        //activate tab if tab id provided in the URL
        if (location.hash) {
            var tabid = location.hash.substr(1);
            $('a[href="#' + tabid + '"]').parents('.tab-pane:hidden').each(function () {
                var tabid = $(this).attr("id");
                $('a[href="#' + tabid + '"]').click();
            });
            $('a[href="#' + tabid + '"]').click();
        }

        if ($().tabdrop) {
            $('.tabbable-tabdrop .nav-pills, .tabbable-tabdrop .nav-tabs').tabdrop({
                text: '<i class="fa fa-ellipsis-v"></i> <i class="fa fa-angle-down"></i>'
            });
        }
    };

    // Handles Bootstrap Modals.
    var handleModals = function () {
        // fix stackable modal issue: when 2 or more modals opened, closing one of modal will remove .modal-open class. 
        $('body').on('hide.bs.modal', function () {
            if ($('.modal:visible').size() > 1 && $('html').hasClass('modal-open') === false) {
                $('html').addClass('modal-open');
            } else if ($('.modal:visible').size() <= 1) {
                $('html').removeClass('modal-open');
            }
        });

        // fix page scrollbars issue
        $('body').on('show.bs.modal', '.modal', function () {
            if ($(this).hasClass("modal-scroll")) {
                $('body').addClass("modal-open-noscroll");
            }
        });

        // fix page scrollbars issue
        $('body').on('hide.bs.modal', '.modal', function () {
            $('body').removeClass("modal-open-noscroll");
        });

        // remove ajax content and remove cache on modal closed 
        $('body').on('hidden.bs.modal', '.modal:not(.modal-cached)', function () {
            $(this).removeData('bs.modal');
        });
    };

    // Handles Bootstrap Tooltips.
    var handleTooltips = function () {
        // global tooltips
        $('.tooltips').tooltip();
        $('[data-toggle="tooltips"]').tooltip();
        $(document).find('[data-toggle="tooltips"]').each(function(){
            $(this).tooltip();
        });

        // portlet tooltips
        $('.portlet > .portlet-title .fullscreen').tooltip({
            container: 'body',
            title: 'Fullscreen'
        });
        $('.portlet > .portlet-title > .tools > .reload').tooltip({
            container: 'body',
            title: 'Reload'
        });
        $('.portlet > .portlet-title > .tools > .remove').tooltip({
            container: 'body',
            title: 'Remove'
        });
        $('.portlet > .portlet-title > .tools > .config').tooltip({
            container: 'body',
            title: 'Settings'
        });
        $('.portlet > .portlet-title > .tools > .collapse, .portlet > .portlet-title > .tools > .expand').tooltip({
            container: 'body',
            title: 'Collapse/Expand'
        });
    };

    // Handles Bootstrap Dropdowns
    var handleDropdowns = function () {
        /*
         Hold dropdown on click  
         */
        $('body').on('click', '.dropdown-menu.hold-on-click', function (e) {
            e.stopPropagation();
        });
    };

    var handleAlerts = function () {
        $('body').on('click', '[data-close="alert"]', function (e) {
            $(this).parent('.alert').hide();
            $(this).closest('.note').hide();
            e.preventDefault();
        });

        $('body').on('click', '[data-close="note"]', function (e) {
            $(this).closest('.note').hide();
            e.preventDefault();
        });

        $('body').on('click', '[data-remove="note"]', function (e) {
            $(this).closest('.note').remove();
            e.preventDefault();
        });
    };

    // Handle Hower Dropdowns
    var handleDropdownHover = function () {
        $('[data-hover="dropdown"]').not('.hover-initialized').each(function () {
            $(this).dropdownHover();
            $(this).addClass('hover-initialized');
        });
    };

    // Handles Bootstrap Popovers

    // last popep popover
    var lastPopedPopover;

    var handlePopovers = function () {
        $('.popovers').popover({
            html: true,
            animate: true
        });

        // close last displayed popover

        $(document).on('click.bs.popover.data-api', function (e) {
            if (lastPopedPopover) {
                //lastPopedPopover.popover('hide');
            }
        });
    };

    // Handles scrollable contents using jQuery SlimScroll plugin.
    var handleScrollers = function () {
        PECO.initSlimScroll('.scroller');
    };

    // Handles Image Preview using jQuery Fancybox plugin
    var handleFancybox = function () {
        /*if (!jQuery.fancybox) {
         return;
         }
         */
        $(".fancybox-fast-view").click(function (e) {
            $(this).fancybox();
        });
        if ($(".fancybox-button").size() > 0) {
            $(".fancybox-button").fancybox({
                groupAttr: 'data-rel',
                prevEffect: 'none',
                nextEffect: 'none',
                closeBtn: true,
                helpers: {
                    title: {
                        type: 'inside'
                    }
                }
            });
        }
    };

    // Fix input placeholder issue for IE8 and IE9
    var handleFixInputPlaceholderForIE = function () {
        //fix html5 placeholder attribute for ie7 & ie8
        if (isIE8 || isIE9) { // ie8 & ie9
            // this is html5 placeholder fix for inputs, inputs with placeholder-no-fix class will be skipped(e.g: we need this for password fields)
            $('input[placeholder]:not(.placeholder-no-fix), textarea[placeholder]:not(.placeholder-no-fix)').each(function () {
                var input = $(this);

                if (input.val() === '' && input.attr("placeholder") !== '') {
                    input.addClass("placeholder").val(input.attr('placeholder'));
                }

                input.focus(function () {
                    if (input.val() == input.attr('placeholder')) {
                        input.val('');
                    }
                });

                input.blur(function () {
                    if (input.val() === '' || input.val() == input.attr('placeholder')) {
                        input.val(input.attr('placeholder'));
                    }
                });
            });
        }
    };

    // Handle Select2 Dropdowns
    var handleSelect2 = function () {
        if ($().select2) {
            $('.select2me').select2({
                placeholder: "Select",
                allowClear: true
            });
        }

    };

    // TRIGGER LOGIN PAGE LOADING SCRIPT
    var pageLogin_loading = function (options) {
        $('body').append('<div class="page-main-loader">' +
            '<div style="font-size: ' + (options && options.messageSize ? options.messageSize : '') + '" class="loader-message ' + (options && options.messageClass ? options.messageClass : '') + '">' +
            (options && options.message ? options.message : 'Loading...') +
            '</div>' +
            '<div class="page-main-loader-anim"><div class="circle-anim"></div></div></div>');
    };

    // UNLOAD LOGIN PAGE LOADING SCRIPT
    var unloadPageLogin_loading = function () {
        $('.page-main-loader').remove();
    };

    var datatable_scroller = function () {
        $('.dataTables_scrollBody').niceScroll({
            styler: "fb",
            cursorcolor: "rgba(215, 98, 44, 0.6)",
            cursorwidth: '5',
            cursorborderradius: '1px',
            background: 'transparent',
            cursorborder: '',
            zindex: '1000'
        });

        $('.dataTables_scrollBody').css('margin-right', '-15px');
        $('.dataTables_scrollBody').css('margin-bottom', '-15px');
    };

    var dt_niceScroller = function (visible) {
        var opacity = (visible == false) ?  0 : 0.05;
        /*
        $('.dataTables_scrollBody').slimScroll({
            size: '4px',
            position: 'right',
            color: '#ff6026',
            railColor: '#222',
            railVisible: true,
            railOpacity: opacity,
        });
        */
        /*
        $('.dataTables_scrollBody').niceScroll({
            styler: "fb",
            cursorcolor: "rgba(215, 98, 44, 0.6)",
            cursorwidth: '5',
            cursorborderradius: '1px',
            background: 'transparent',
            cursorborder: '',
            zindex: '1000'
        });
        */


        $('.dataTables_scrollBody').css('margin-right', '-15px');
        $('.DTFC_LeftBodyWrapper').css('margin-bottom', '-15px').css('bottom', '-15px');
    };


    var dt_select2SlimScroller = function () {
        $('.select2-drop ul').niceScroll({
            styler: "fb",
            cursorcolor: "rgba(215, 98, 44, 0.6)",
            cursorwidth: '3',
            cursorborderradius: '0px',
            background: 'transparent',
            cursorborder: ''
        });
    };


    var select2_niceScroller = function () {
        $('.select2-drop ul').niceScroll({
            styler: "fb",
            cursorcolor: "rgba(215, 98, 44, 0.6)",
            cursorwidth: '6',
            cursorborderradius: '0px',
            background: 'transparent',
            cursorborder: ''
        });
    };

    var select2_niceScroller_tbl = function (tbl) {
        tbl.find('.select2-drop ul').each(function(){
            $(this).niceScroll({
                styler: "fb",
                cursorcolor: "rgba(215, 98, 44, 0.6)",
                cursorwidth: '5',
                cursorborderradius: '0px',
                background: 'transparent',
                cursorborder: ''
            });
        });
    };


    var body_niceScroller = function () {
        /*
        $("html").niceScroll({
            styler: "fb",
            cursorcolor: "rgba(215, 98, 44, 0.6)",
            cursorwidth: '8',
            cursorborderradius: '0px',
            background: 'transparent',
            cursorborder: '',
            zindex: '1000'
        });
        */

    };

    var el_niceScroller = function (el) {
        el.niceScroll({
            styler: "fb",
            cursorcolor: "rgba(215, 98, 44, 0.6)",
            cursorwidth: '5',
            cursorborderradius: '0px',
            background: 'transparent',
            cursorborder: '',
            zindex: '1000'
        });
    };


    var html_niceScroller = function () {
        /*
        var isWindows = navigator.platform.indexOf('Win') > -1 ? true : false;
        if (isWindows) {
            // if we are on windows OS we activate the perfectScrollbar function
            $('.page-content').perfectScrollbar();

            $('.page-content').addClass('perfect-scrollbar-on');
        } else {
            $('.page-content').addClass('perfect-scrollbar-off');
        }
        */


        $(".page-quick-sidebar-wrapper").hover(
            function () {
                $("html").getNiceScroll().hide();
            }, function () {
                $("html").getNiceScroll().show();
            }
        );

        $("body").find(".slimScrollDiv").hover(
            function () {
                $("html").getNiceScroll().hide();
            }, function () {
                $("html").getNiceScroll().show();
            }
        );

        $(".page-sidebar").hover(
            function () {
                $("html").getNiceScroll().hide();
            }, function () {
                $("html").getNiceScroll().show();
            }
        );
    };

    var ajax_stats = function (btn) {
        var url = btn.attr('href');
        var id = btn.attr('data-id');
        var title = (btn.attr('title')) ? btn.attr('title') : '';
        $.SmartMessageBox({
                title: "<i class='fa fa-question fa-fw fa-lg txt-color-yellow'></i> Confirm: " + title + "</span>",
                content: 'Please confirm action taken',
                buttons: '[Yes][No]',
                buttonClass: 'btn-primary, btn-default',
                buttonsIcon: 'fa-angle-double-right, fa-times',
            },
            function (ButtonPressed) {
                if (ButtonPressed === "Yes") {
                    return $.ajax({
                        url: url,
                        type: 'POST',
                        data: {'id': id},
                        dataType: 'json',
                        beforeSend: function () {
                            btn.removeClass(btn.attr('btn-default')).addClass('btn-default').find('.fa').addClass('fa-spinner fa-spin fa-pulse');
                        }
                    }).done(function (data) {
                        if (data.qry == true) {
                            PECO.initAlerts(data.msg, btn.attr('title'), 'success');
                            btn.removeClass('btn-default').addClass(btn.attr('btn-success')).find('.fa').removeClass('fa-spinner fa-spin fa-pulse').addClass('fa-check');

                        } else {
                            PECO.initAlerts(data.msg, btn.attr('title'), 'warning');
                            btn.removeClass('btn-default').addClass(btn.attr('btn-warning')).find('.fa').removeClass('fa-spinner fa-spin fa-palse').addClass('fa-warning');

                        }
                    }).fail(function () {
                        PECO.phpError();
                        btn.removeClass('btn-default').find('.fa').removeClass('fa-spinner fa-spin fa-pulse');
                    });
                }
            });
    };

    var ajax_confirm = function (url, msgtitle) {
        $.SmartMessageBox({
                title: "<i class='fa fa-question fa-fw fa-lg txt-color-yellow'></i> Confirm: " + msgtitle['title'] + "</span>",
                content: 'Please confirm action taken',
                buttons: '[Yes][No]',
                buttonClass: 'btn-primary, btn-default',
                buttonsIcon: 'fa-angle-double-right, fa-times',
            },
            function (ButtonPressed) {
                if (ButtonPressed === "Yes") {
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: 'html'
                    }).done(function (data) {
                        if (data == 0) {
                            $.smallBox({
                                title: msgtitle['dataname'],
                                content: 'Data has been deactivated!',
                                color: "rgba(238, 71, 24, 0.5)",
                                icon: "fa fa-times fa-2x fadeInRight animated",
                                timeout: NotifyTimeOut
                            });
                        } else {
                            $.smallBox({
                                title: msgtitle['dataname'],
                                content: 'Data has been activated!',
                                color: "rgba(63, 190, 0, 0.6)",
                                icon: "fa fa-check fa-2x fadeInRight animated",
                                timeout: NotifyTimeOut
                            });
                        }
                    });
                }
            });
    };

    var btn_confirm_approval = function (url, data_arr, msgtitle, obj) {

        $.SmartMessageBox({
                title: "<i class='fa fa-question fa-fw fa-lg txt-color-yellow'></i> Confirm: " + msgtitle + "</span>",
                content: 'Please confirm action taken',
                buttons: '[Yes][No]',
                buttonClass: 'btn-primary, btn-default',
                buttonsIcon: 'fa-angle-double-right, fa-times',
            },
            function (ButtonPressed) {
                if (ButtonPressed === "Yes") {
                    $.ajax({
                        url: url,
                        type: 'post',
                        data: data_arr,
                        dataType: 'json'
                    }).done(function (data) {
                        //console.log(data);
                        if (data.qry == true) {
                            PECO.initAlerts(data.msg, data.title, data.func);
                            var task_stat = (data.approval == 'true') ? '<h3 class="text-success"><i class="fa fa-check fa-fw"></i> Approved</h3>' : '<h3 class="text-danger"><i class="fa fa-times fa-fw"></i> Disapproved</h3>';
                            obj.closest('#btn-approval-group').html(task_stat);
                            $('#btn_submit_group', document).remove();
                        } else {
                            PECO.initAlerts('<b>Query: </b> Error', 'Warning', 'warning');
                        }
                    }).fail(function () {
                        PECO.phpError();
                    });
                } else {
                    PECO.initAlerts('User canceled approval process', 'Warning', 'info');
                }
            });
    };
    var validate_username = function(username) {
        var response = {'qry': false};
        $.ajax({
            url: PECO.base_url()+'admin/validateusername',
            type: 'post',
            dataType: 'json',
            data: {'username': username},
            async: false,
        }).done(function(d){
            response = d;
        });
        return response;
    };

    var validate_login = function(username, password) {
        var response = false;
        $.ajax({
            url: PECO.base_url()+'admin/validatelogin',
            type: 'post',
            dataType: 'json',
            data: {'username': username, 'password': password},
            async: false,
        }).done(function(d){
            response = d;
        });
        return response;
    };

    var ajax_confirm_alerts = function (url, id, title) {
        var ButtonPressed;
        $.SmartMessageBox({
            title: "<i class='fa fa-question fa-fw fa-lg txt-color-yellow'></i> Confirm: " + title + "</span>",
            content: 'Please confirm action taken!',
            buttons: '[Yes][No]',
            buttonsPosition: 'right',
            buttonClass: 'btn-lg btn-success, btn-lg btn-danger',
            buttonsIcon: 'fa-check, fa-times',
        }, function (ButtonPressed) {
            if (ButtonPressed === "Yes") {
                $.ajax({
                    url: url,
                    type: 'post',
                    data: {'id': id},
                    dataType: 'json',
                    async: false,
                    beforeSend: function () {
                    }
                }).done(function (data) {
                    PECO.initAlerts(data.msg, data.title, data.func);
                    response = true;
                    ret = 'TEST';
                }).fail(function(){
                    PECO.phpError();
                });
            }
        });

    };

    var ajax_confirm_editable = function (callback, btngroup) {
        $.editable_portlet = $('[data-toggle=editable]');
        $.editable_portlet_btn = '';
        $.editable_portlet_btn += '<div class="md-checkbox has-error">';
        $.editable_portlet_btn += '<input type="checkbox"  class="md-check" id="enable" />';
        $.editable_portlet_btn += '<label for="enable">';
        $.editable_portlet_btn += '<span></span>';
        $.editable_portlet_btn += '<span class="check"></span>';
        $.editable_portlet_btn += '<span class="box"></span>';
        $.editable_portlet_btn += '<i class="fa fa-pencil"></i> Editable</label>';
        $.editable_portlet_btn += '</div>';
        $.editable_portlet.find('.tools').html($.editable_portlet_btn);
        $.this_ = $('#enable');


        $.this_.click(function (e) {
            $.this_ = $(this);
            $.this_.closest('.md-checkbox').removeClass('has-error').addClass('has-success');
            e.stopPropagation();
            if (e.target.checked == false) {
                $('.editable').editable('toggleDisabled');
                $.this_.attr('checked', false).closest('.md-checkbox').removeClass('has-success').addClass('has-error');
                $('.editable').each(function () {
                    $(this).closest('span').removeClass('label-editable');
                });
                btngroup.each(function(){
                    $(this).addClass('hidden');
                });
                callback();
            } else {
                $.SmartMessageBox({
                    title: "<i class='fa fa-user fa-fw fa-lg txt-color-yellow'></i> Allow Edit Information</span>",
                    content: 'Please confirm action taken!',
                    input: 'text',
                    placeholder: 'Username',
                    inputValue: "",
                    buttons: '[Proceed][Cancel]',
                    buttonsPosition: 'right',
                    buttonClass: 'btn-lg btn-primary, btn-lg btn-warning',
                    buttonsIcon: 'fa-angle-double-right, fa-times',
                    inputIcon: 'fa fa-user',
                    inputIconPosition: 'left',
                    cornerType: ''
                }, function (ButtonPressed, Value) {
                    if (ButtonPressed == "Cancel") {
                        $.this_.attr('checked', false).closest('.md-checkbox').addClass('has-error').removeClass('has-success');
                        return false;
                    }
                    var username_validation = validate_username(Value);
                    if(username_validation.qry==true) {

                        Value1 = Value.toUpperCase();
                        var username = Value;
                        $.SmartMessageBox({
                            title: "<i class='fa fa-key fa-fw fa-lg txt-color-red'></i> Hey! <strong>" + Value1 + ",</strong></span>",
                            content: "And now please provide your password:",
                            buttons: "[Login][Cancel]",
                            buttonsPosition: 'right',
                            buttonClass: 'btn-primary, btn-warning',
                            buttonsIcon: 'fa-angle-double-right, fa-times',
                            input: "password",
                            inputIcon: 'fa fa-key',
                            inputIconPosition: 'left',
                            placeholder: "Password",
                            inputValue: "",
                        }, function (ButtonPress, Value) {
                            var password = Value;
                            if (ButtonPress === "Login") {
                                var login_validaton = validate_login(username, password);
                                console.log(login_validaton);
                                if(login_validaton.qry==true) {
                                    $('.editable').editable('toggleDisabled');
                                    callback();
                                    $.this_.attr('checked', true).closest('.md-checkbox').removeClass('has-error').addClass('has-success');
                                    $.smallBox({
                                        title : "PECO.net",
                                        content : "Editable content enabled!",
                                        color : "#5384AF",
                                        timeout: 5000,
                                        icon : "fa fa-bell"
                                    });
                                    $('.editable').each(function () {
                                        $(this).closest('span').addClass('label-editable');
                                    });
                                    btngroup.each(function(){
                                        $(this).removeClass('hidden');
                                    });
                                }else{
                                    $.this_.attr('checked', false).closest('.md-checkbox').addClass('has-error').removeClass('has-success');
                                    PECO.initAlerts(login_validaton.msg, 'Login Validation', 'error');
                                }
                            } else {
                                $.this_.attr('checked', false).closest('.md-checkbox').addClass('has-error').removeClass('has-success');
                            }
                        });
                    }else{
                        $.this_.attr('checked', false).closest('.md-checkbox').addClass('has-error').removeClass('has-success');
                        PECO.initAlerts(username_validation.msg, 'Username Validation', 'warning');
                    }
                });
            }
        });

    };

    var ajax_confirm_form = function (form, msgtitle) {
        $.SmartMessageBox({
            title: "<i class='fa fa-question fa-fw fa-lg txt-color-yellow'></i> Confirm: " + msgtitle + "</span>",
            content: 'Please confirm action taken',
            buttons: '[Yes][No]',
            buttonsPosition: 'right',
            buttonClass: 'btn-primary, btn-danger',
            buttonsIcon: 'fa-angle-double-right, fa-times',
            inputIcon: 'fa fa-user',
            inputIconPosition: 'left',
        },function (ButtonPressed) {
            if (ButtonPressed === "Yes") {
                $.ajax({
                    url: form.attr('action'),
                    type: form.attr('method'),
                    data: form.serialize(),
                    dataType: 'json'
                }).done(function (data) {
                    if (data.qry == true) {
                        var func = (data.func) ? data.func : 'success';
                        PECO.initAlerts(data.msg, msgtitle.dataname, func);
                    } else {
                        var func = (data.func) ? data.func : 'warning';
                        PECO.initAlerts(data.msg, msgtitle.dataname, func);
                    }
                    if(typeof data.table !== 'undefined'){
                        $('#' + data.table).closest('.btn-refresh').trigger('click');
                    }
                }).fail(function () {
                    PECO.phpError();
                });
            }
        });
    };


    var ajax_btn_submit = function (btn) {
        var id = btn.attr('data-id');
        return $.ajax({
            url: btn.attr('href'),
            type: 'post',
            data: {'id': id},
            dataType: 'json',
            async: false,
            beforeSend: function () {
            }
        }).done(function (data) {
            if (data.qry == true) {
                console.log(data);
                return true;
            } else {
                return false;
            }
        }).fail(function () {
            return false;
        });
    };

    var ajax_submit = function (frm, msg_arr) {
        var msg_arr_def = {
            'error': {'title': 'Error', 'msg': 'Error PHP file!', 'func': 'error'},
            'warning': {'title': 'Warning', 'msg': 'Query fail!', 'func': 'warning'},
            'success': {'title': 'Success', 'msg': 'Query Success!', 'func': 'success'}
        };
        var msg_arr = (msg_arr) ? msg_arr : msg_arr_def;
        var res;
        $.form_main = frm;
        $.ajax({
            url: $.form_main.attr('action'),
            type: $.form_main.attr('method'),
            data: new FormData($.form_main[0]),
            dataType: 'json',
            contentType: false,
            cache: false,
            processData: false,
            async: false, // <--- DEMN THIS IS IMPORTANT IF YOU WANT TO RETURN RESPONSE FROM INSIDE AJAX STRING
            beforeSend: function () {
                PECO.blockUI({
                    target: $.form_main,
                    animate: true,
                    overlayColor: 'none'
                });
            }
        }).done(function (data) {
            if (data && data.qry == true) {
                init_alerts_toastr(msg_arr.success.msg, msg_arr.success.title, msg_arr.success.func);
                PECO.unblockUI($.form_main);
                $.form_main.each(function (e) {
                    $('input.input-reset, textarea.input-reset').val('');
                    $('input.input-reset').select2('val', '');
                });
                res = true;
            } else {
                init_alerts_toastr(msg_arr.warning.msg, msg_arr.warning.title, msg_arr.warning.func);
                PECO.unblockUI($.form_main);
                res = false;
            }
        }).fail(function () {
            PECO.phpError();
            PECO.unblockUI($.form_main);
            res = false;
        });
        return res;
    };


    var btnProcess = function () {
        $('#process-btn').on('click', 'button', function () {
            $('#process-btn button').removeClass('active');
            $(this).addClass('active');
        });
    };

    var formatDataCorp = function (data) {
        if (data.loading)
            var addr;
        var rep;
        if (data.details == true) {
            addr = (data.address) ? '<li style="font-size: 11px; margin: 1px 1px !important; padding: 0px 0px !important; line-height: 12px;"><span class="text-info">' + data.address + '<span></li>' : '';
            rep = (data.rep) ? '<li style="font-size: 11px; margin: 1px 1px !important; padding: 0px 0px !important; line-height: 12px; padding-bottom: 10px !important"><span class="text-danger">' + data.rep + '<span></li>' : '';
        } else {
            addr = '';
            rep = '';
        }
        pics = (data.pic) ? '<img src="' + PECO.base_url() + data.pic + '" width="100%"/>' : '';
        markup = '<div style="position: relative;">' +
            '<div style="float: left; width: 20%; height: 100%; position: absolute;">' + pics + '</div>' +
            '<ul style="margin: 0px 0px; padding: 0px 0px; background: transparent; position: relative; left: 20%; width: 78%; margin-left: 5px;"><li><span><b>' + data.text + '</b></span></li>' +
            addr +
            rep +
            '</div>';
        return markup;
    };

    var formatDataSelectionCorp = function (data) {
        return data.text.split(',', 1);
    };

    // SELECT 2 METER MENU FORMATING
    var formatMeterData = function (data) {
        var text_arr = data.text.split(' - ');
        var markup = '<div class="row"><div class="col-md-3">' +
            '<img src="' + PECO.base_url() + '/assets/global/img/barcode_default.gif" width="100%" height="100%"/>' +
            '</div>' +
            '<div class="col-md-9">' +
            '<span class="text-danger" style="font-size: 15px"><i class="fa fa-tag"></i> <b>' + text_arr[0] + '</b></span> - ' + text_arr[1] + '</span><br>' +
            '<span class="text-info"><i class="fa fa-user"></i> ' + data.owner + '</span><br>' +
            '<span class="text-warning"><i class="fa fa-map-marker"></i>  ' + data.addr + '</span>' +
            '</div></div>';
        return markup;
    };
    var formatDataSelectionMeter = function (data) {
        return data.text.split(' - ', 1);
    };


    // SELECT 2 PERSON MENU FORMATING
    var formatDataCustomers = function(data) {
        var name = data.name;
        var addr = data.addr;
        var serv = data.text;
        var pics = data.pics;
        markup = '<div style="position: relative; display: inline-block; width: 100%; padding-left: 15%;">' +
            '<span class="img-circle" style="position: absolute; left: 0px; width: 15%; padding-top: 5px;">' +
            '<img src="'+pics+'" style="width: 100%" />' +
            '</span>' +
            '<ul style="position: absolute; left: 15%; margin: 0px 0px; padding: 0px 0px; background: transparent; position: relative; left: 0; width: 100%; margin-left: 5px;">' +
            '<li><span><b>' + serv + '</b> - '+name+'</span></li>' +
            '<li><span class="text-danger">' + addr + '</span></li>' +
            '</div>';
        return markup;
    };

    var formatData = function (data) {
        if (data.loading)
            return data.name;
        /*
         markup = '<li class="media select-2">'+
         '<a class="pull-left" href="javascript:;">'+
         '<img class="media-object" src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI2NCIgaGVpZ2h0PSI2NCI+PHJlY3Qgd2lkdGg9IjY0IiBoZWlnaHQ9IjY0IiBmaWxsPSIjZWVlIi8+PHRleHQgdGV4dC1hbmNob3I9Im1pZGRsZSIgeD0iMzIiIHk9IjMyIiBzdHlsZT0iZmlsbDojYWFhO2ZvbnQtd2VpZ2h0OmJvbGQ7Zm9udC1zaXplOjEycHg7Zm9udC1mYW1pbHk6QXJpYWwsSGVsdmV0aWNhLHNhbnMtc2VyaWY7ZG9taW5hbnQtYmFzZWxpbmU6Y2VudHJhbCI+NjR4NjQ8L3RleHQ+PC9zdmc+" alt="32x32" data-src="holder.js/32x32" style="width: 32px; height: 32px;">'+
         '</a>'+
         '<div class="media-body">';
         '<p><i class="fa fa-tag"></i><span><b>' + data.text + '</b></span></p>'+ 
         '<p>'+data.gender+' ' + data.birthday + '<p>'+
         '<p><i class="fa fa-map-marker"></i> <span>'+data.address+'<span></p>'+
         '</div></li>';
         */
        var gender;
        var bday;
        var addr;
        var pics;
        if (data.details == true) {
            gender = (data.gender) ? data.gender : '';
            bday = (data.birthday) ? '<li style="font-size: 11px; margin: 1px 1px !important; padding: 0px 0px !important; line-height: 12px;"> ' + data.birthday + '<li>' : '';
            addr = (data.address) ? '<li style="font-size: 11px; margin: 1px 1px !important; padding: 0px 0px !important; line-height: 12px;"><span>' + data.address + '<span></li>' : '';
        } else {
            gender = '';
            bday = '';
            addr = '';
        }
        pics = (data.pic) ? '<img src="' + PECO.base_url() + data.pic + '" width="100%"/>' : '';
        markup = '<div style="position: relative; margin-bottom: 5px; display: inline-block; width: 100%">' +
            '<div style="float: left; width: 10%; height: 100%; position: absolute; padding: 5px 5px;">' + pics + '</div>' +
            '<ul style="margin: 0px 0px; padding: 0px 0px; background: transparent; position: relative; left: 10%; width: 90%; margin-left: 5px;"><li><span><span style="float: right">' + gender + '</span><b>' + data.text + '</b></span></li>' +
            bday +
            addr +
            '</div>';
        return markup;
    };


    var select2result = function (data) {
        if (data.loading)
            return data.name;
        var text = data.text.split(' - ');
        var markup = '<span class="text-info">' + text[0] + '</span>' + ' - ' + text[1];
        return markup;
    };

    var select2selection = function (data) {
        if (data.loading)
            return data.name;
        var text = data.text.split(' - ');
        var markup = '<span class="text-info">' + text[0] + '</span>' + ' - ' + text[1];
        return markup;
    };

    var formatStateDefault = function (data) {
        var text_arr = data.text.split(' - ');
        if(text_arr.length > 1) {
            if (!data.id) {
                return data.text;
            }
            var text_color = (data.color) ? data.color : '';
            var text_icon = '<i class="fa fa-circle-o"></i>';
            var code_color = '#ef582d';
            return '<p style="color: ' + text_color + '"  style="display: inline-block; width: 100%; margin:3px 0px; position: relative; height: auto; word-wrap: normal;"><b style="display: inline-block; width: 25%; vertical-align: top; color: ' + code_color + '">' + text_arr[0] + '</b><span style="display: inline-block; width: 75%; padding-left: 10px; border-left: 1px solid #ccc;">' + text_arr[1] + '<span class="pull-right">' + text_icon + '</span></span></p>';
        }else{
            return data.text;
        }
    };

    var formatStateEditable = function (data) {
        var text_arr = data.text.split(' - ');
        if (!data.id) {
            return data.text;
        }
        var text_color = (data.color) ? data.color : '';
        var text_icon = (data.icon) ? '<i class="fa '+data.icon+'"></i>' : '';
        var code_color = (data.color) ? data.color : '#ef582d';
        return '<p style="color: '+text_color+'"  style="display: inline-block; width: 100%; margin:3px 0px; position: relative; height: auto; word-wrap: normal;"><b style="display: inline-block; width: 25%; vertical-align: top; color: '+code_color+'">' + text_arr[0] + '</b><span style="display: inline-block; width: 75%; padding-left: 10px; border-left: 1px solid #ccc;">' + text_arr[1]+'<span class="pull-right">'+text_icon+'</span></span></p>';
    };

    var formatDataSelectionDefault = function (data) {
        return data.text;
    };


    var formatDataSelectionEditable = function (data) {
        if (!data.id) {
            return data.text;
        }
        return data.text.split(' - ', 1);
    };

    var formatDataSelectionBasic = function (data) {
        return '<i class="fa fa-check text-success"></i> ' + data.text.split(' - ', 1);
    };
    var formatDataSelectionBasicLabel = function (data) {
        var b = data.text.split(' - ');
        return  '<span class="pull-right label label-info" style="margin-top: 3px; margin-right: 35px !important;">'+ b[2] +'</span><i class="fa fa-check text-success"></i> ' + b[0] ;
    };
    var formatDataSelectionFullLabel = function (data) {
        var b = data.text.split(' - ');
        var d = data.text.split(' - ');
        return '<i class="fa fa-check text-success"></i> ' + '<b>'+b[0]+'</b> ' + d[1] + '<span class="pull-right label label-info" style="margin-right: 35px !important;">'+d[2]+'</span>';
    };
    var formatDataSelectionFull = function (data) {
        var d = data.text.split(' - ');
        var text2 = (d[1]) ? ' - ' + d[1] : '';
        return '<i class="fa fa-check text-success"></i> ' + '<b>'+d[0]+'</b> ' + text2;
    };

    var formatDataListBasic = function (data) {
        if (data.loading)
            return data.name;
        var text = data.text.split(' - ');
        var text2 = (text[1]) ? text[1] : '';
        var markup = '<span class="select2-list"><i class="fa fa-circle-o text-info"></i> <b>' + text[0] + '</b> ' + text2 + '</span>';
        return markup;
    };

    var formatDataListBasicLabel = function (data) {
        if (data.loading)
            return data.name;
        var text = data.text.split(' - ');
        var text2 = (text[1]) ? ' - ' + text[1] : '';
        var markup = '<span class="select2-list"><i class="fa fa-circle-o"></i> <b>' + text[0] + '</b> ' + text2 + '<span class="pull-right label label-info">'+text[2]+'</span></span>';
        return markup;
    };

    var formatDataSelection = function (data) {
        return data.text.split(',', 1);
    };

    var pluginPatchArrHandle = function (arr, path) {
        var _arr = $.map(arr, function (scr) {
            return $.getScript((path || "") + scr);
        });
        _arr.push($.Deferred(function (deferred) {
            $(deferred.resolve);
        }));
        return $.when.apply($, _arr);
    };

    var pluginCSSArrHandler = function(arr) {
        if(arr && arr.length > 0) {
            for(i = 0; i <= arr.length; i++) {
                var href = arr[i];
                if (!$("link[href='"+PECO.base_url() +href+"']").length) {
                    $("<link/>", {
                        rel: "stylesheet",
                        type: "text/css",
                        href: PECO.base_url() + href,
                    }).insertAfter("#head_plugins_marker");
                }
            }
        }
    };

    var amsChartHandle = function () {
        $("<link/>", {
            rel: "stylesheet",
            type: "text/css",
            href: PECO.base_url() + "assets/global/plugins/amcharts_v3/plugins/export/export.css"
        }).appendTo("head");

        var script_arr = [
            "assets/global/plugins/amcharts/amcharts/amcharts.js",
        ];
        pluginPatchArrHandle(script_arr, PECO.base_url()).done(function () {
            console.log('Scripts AmChart, loaded!..');
            var script_arr = [
                "assets/global/plugins/amcharts/amcharts/serial.js",
                "assets/global/plugins/amcharts/amcharts/pie.js",
                "assets/global/plugins/amcharts/amcharts/radar.js",
                "assets/global/plugins/amcharts/amcharts/themes/light.js",
                "assets/global/plugins/amcharts/amcharts/themes/patterns.js",
                "assets/global/plugins/amcharts/amcharts/themes/chalk.js",
                "assets/global/plugins/amcharts/ammap/ammap.js",
                "assets/global/plugins/amcharts/ammap/maps/js/worldLow.js",
                "assets/global/plugins/amcharts/amstockcharts/amstock.js",
                "assets/global/plugins/amcharts_v3/plugins/export/export.min.js",
            ];
            pluginPatchArrHandle(script_arr, PECO.base_url()).done(function () {
                console.log('Scripts AmChart Ext., loaded!..');
            });
        });
    };

    var typeHeadHandle = function () {


        $("<link/>", {
            rel: "stylesheet",
            type: "text/css",
            href: PECO.base_url() + "assets/global/plugins/typeahead/typeahead.css"
        }).appendTo("head");

        var script_arr = [
            "assets/global/plugins/typeahead/handlebars.min.js",
            "assets/global/plugins/typeahead/typeahead.bundle.min.js"
        ];

        pluginPatchArrHandle(script_arr, PECO.base_url()).done(function () {
            console.log('Type heads loaded!...');
        });
    };

    var select2Handle = function () {

        var script_arr = [
            "assets/global/plugins/bootstrap-select/bootstrap-select.min.js",
            //"assets/global/plugins/select2/select2.min.js",
            "assets/global/plugins/select2/select2.full.min.js",
            "assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js"
        ];

        pluginPatchArrHandle(script_arr, PECO.base_url()).done(function () {
            console.log('Scripts Select 2, loaded!..');
        });
    };

    var tblSelectHandle = function () {
        /*
         * LOAD Multiple Javascript Extentions
         * Highlight Search
         */
        var script_arr = [
            "assets/global/plugins/datatables/dataTables.keyTable.min.js",
        ];
        pluginPatchArrHandle(script_arr, PECO.base_url()).done(function () {
            console.log('Scripts TBL SELECT, loaded!..');
        });
    };


    var dtResizableColumn = function () {
        /*
         * LOAD Multiple Javascript Extentions
         * Highlight Search
         */
        var script_arr = [
            "assets/global/plugins/datatables/ColReorderWithResize.js",
        ];
        pluginPatchArrHandle(script_arr, PECO.base_url()).done(function () {
            console.log('Scripts TBL Resizable Column, loaded!..');
        });
    };

    var dataTablePluginHandle = function () {

        $("<link/>", {
            rel: "stylesheet",
            type: "text/css",
            href: PECO.base_url() + "assets/global/plugins/datatables/dataTables.bootstrap.css"
        }).appendTo("head");

        var script_arr = [
            "assets/global/plugins/datatables/jquery.dataTables.js",
            "assets/global/plugins/datatables/dataTables.bootstrap.js"
        ];
        pluginPatchArrHandle(script_arr, PECO.base_url()).done(function () {
            console.log('Scripts DataTable, loaded!..');
        });
    };

    var DataTablePluginHandle1 = function () {

        $("<link/>", {
            rel: "stylesheet",
            type: "text/css",
            href: PECO.base_url() + "assets/global/plugins/datatables/dataTables.bootstrap.css"
        }).appendTo("head");

        var script_arr = [
            "assets/global/plugins/datatables/jquery.dataTables.js",
            "assets/global/plugins/datatables/dataTables.bootstrap.js"
        ];
        pluginPatchArrHandle(script_arr, PECO.base_url()).done(function () {
            console.log('Scripts DataTable, loaded!..');
        });
    };

    var handleGoogleKey = function () {
        var script_arr = [
            //"http://maps.google.com/maps/api/js?v=3&key=" + gkey +"&sensor=false&callback=initializeMap&libraries=places"
            //"http://maps.google.com/maps/api/js?v=3&sensor=false&callback=initializeMap&libraries=places&key=" + gkey
            //"http://maps.google.com/maps/api/js?libraries=places&sensor=false"
            //"http://maps.googleapis.com/maps/api/js?v=3&key="+PECO.google_api()+"&sensor=false&libraries=places",
            //"http://maps.googleapis.com/maps/api/js?v=3&key="+ PECO.google_api() +"&sensor=false&libraries=places"
            "https://maps.googleapis.com/maps/api/js?key=" + PECO.google_api() + "&callback=initMap"
        ];

        /*pluginPatchArrHandle(script_arr, PECO.base_url()).done(function () {
            console.log('Scripts Google Map API, loaded!..');
        });*/

    $('<script async defer src="https://maps.googleapis.com/maps/api/js?key=' + PECO.google_api() + '&callback=initMap"></script>').appendTo("head");

    };


    var handler_editable_plugins = function() {

        $("<link/>", {
            rel: "stylesheet",
            type: "text/css",
            href: PECO.base_url() + "assets/global/plugins/bootstrap-editable/bootstrap-editable/css/bootstrap-editable.css"
        }).appendTo("head");

        $("<link/>", {
            rel: "stylesheet",
            type: "text/css",
            href: PECO.base_url() + "assets/global/plugins/bootstrap-editable/inputs-ext/address/address.css"
        }).appendTo("head");

        var script_arr = [
            "assets/global/plugins/bootstrap-editable/bootstrap-editable/js/bootstrap-editable.js",
            "assets/global/plugins/bootstrap-editable/bootstrap-editable/js/radiobutton.js",
            "assets/global/plugins/bootstrap-editable/inputs-ext/address/address.js",
            "assets/global/plugins/bootstrap-editable/inputs-ext/wysihtml5/wysihtml5.js"
        ];
        pluginPatchArrHandle(script_arr, PECO.base_url()).done(function () {
            console.log('Scripts Editable Plugins, loaded!..');
        });
    };

    var tblHighlightsHandle = function () {
        /*
         * Include Datatable Highlights CSS
         * Instead adding to html view Link Rel CSS external
         */
        $("<link/>", {
            rel: "stylesheet",
            type: "text/css",
            href: PECO.base_url() + "assets/global/plugins/datatables/dataTables.searchHighlight.css"
        }).appendTo("head");

        /*
         * LOAD Multiple Javascript Extentions
         * Highlight Search
         */
        var script_arr = [
            "assets/global/plugins/datatables/dataTables.searchHighlight.min.js",
            "assets/global/plugins/datatables/jquery.highlight.js"
        ];
        pluginPatchArrHandle(script_arr, PECO.base_url()).done(function () {
            console.log('Scripts TBL HIGHLIGHT, loaded!..');
        });
    };



    var init_mapstyle = function(map) {
        var styles = [{
            "featureType": "water",
            "elementType": "geometry",
            "stylers": [{
                "color": "#91CFEE"
            }, {
                "lightness": 17
            }]
        }, {
            "featureType": "landscape",
            "elementType": "geometry",
            "stylers": [{
                "color": "#FFFFFF"
            }, {
                "lightness": 20
            }]
        }, {
            "featureType": "road.highway",
            "elementType": "geometry.fill",
            "stylers": [{
                "color": "#F3FB60"
            }, {
                "lightness": 17
            }]
        }, {
            "featureType": "road.highway",
            "elementType": "geometry.stroke",
            "stylers": [{
                "color": "#F0CC9B"
            }, {
                "lightness": 29
            }, {
                "weight": 0.2
            }]
        }, {
            "featureType": "road.arterial",
            "elementType": "geometry",
            "stylers": [{
                "color": "#cccccc"
            }, {
                "lightness": 18
            }]
        }, {
            "featureType": "road.local",
            "elementType": "geometry",
            "stylers": [{
                "color": "#cccccc"
            }, {
                "lightness": 16
            }]
        }, {
            "featureType": "poi",
            "elementType": "geometry",
            "stylers": [{
                "color": "#cccccc"
            }, {
                "lightness": 21
            }]
        }, {
            "elementType": "labels.text.stroke",
            "stylers": [{
                "visibility": "on"
            }, {
                "color": "#30CEFF"
            }, {
                "lightness": 1
            }]
        }, {
            "elementType": "labels.text.fill",
            "stylers": [{
                "saturation": 36
            }, {
                "color": "#DF5F09"
            }, {
                "lightness": 40
            },{
                "visibility": "off"
            }]
        }, {
            "elementType": "labels.icon",
            "stylers": [{
                "visibility": "off"
            }]
        }, {
            "featureType": "transit",
            "elementType": "geometry",
            "stylers": [{
                "color": "#cccccc"
            }, {
                "lightness": 19
            }]
        }, {
            "featureType": "administrative",
            "elementType": "geometry.fill",
            "stylers": [{
                "color": "#FFFFFF"
            }, {
                "lightness": 20
            }, {
                "visibility": "off"
            }]
        }, {
            "featureType": "administrative",
            "elementType": "geometry.stroke",
            "stylers": [{
                "color": "#FFFFFF"
            }, {
                "lightness": 17
            }, {
                "weight": 1.2
            }]
        }];
        map.addStyle({
            styledMapName:"Styled Map",
            styles: styles,
            mapTypeId: "map_style"
        });

        map.setStyle("map_style");
    };

    //* END:CORE HANDLERS *//
    //* ############################################### *//
    //* COMPANY OPERATIONS HANDLE HANDLES * //
    var init_crm = function (div, gkey) {
        var gkey_ = (gkey) ? gkey : '';


        PECO.getGoogleKey(gkey_);
        PECO.getDataTablePlugin();
        PECO.getHighlightsPlugin();
        PECO.getAmsChartPlugins();
        PECO.getFileInputPlugin();
        PECO.getSelectPlugins();



        $(div).load(PECO.base_url() + 'mrd/custstats',
            {'test': 'testing..'},
            function () {

                PECO.select2Basic($('#rate_class'), 'inspection/initrateclasslist', 'Rate Class..', true, true);
                PECO.select2Basic($('#dist_list'), 'inspection/initdistrictlist', 'District..', true, true);

                $('#disp_type').select2({
                    placeholder: 'Select Display',
                    allowClear: true,
                });
                $('#disp_type_input').select2({
                    placeholder: 'Select Display',
                    allowClear: true,
                });


                var filedropzone = $(document).find('#reqfiledrop');

                filedropzone.fileinput({
                    uploadAsync: true,
                    showBrowse: true,
                    browseOnZoneClick: true,
                    showPreview: true,
                    uploadExtraData: function (d) {
                        return {
                            display: $('#disp_type_input').select2('val')
                        };
                    },
                });


                $('#map').html('<div class="col-md-12">' +
                    '<span class="text-info"><i class="fa fa-spinner fa-spin fa-pulse"></i> Loading mapping table..' +
                    '</span></div>');
                var div = '#map';
                var form = '#frm_filter_customers';
                var table = $('#tbl_cust_list');
                var map = new GMaps({
                    div: div,
                    lat: 10.7133503,
                    lng: 122.5580168,
                });


                //google.maps.event.addDomListener(window, 'load', initialise);

                PECO.scrollTo($(div));
                // GET LIST OF CUSTOMERS GEOTAG
                function get_customers_locations(form) {
                    form = $(form);
                    var customers_arr = [];
                    $.ajax({
                        url: PECO.base_url() + 'query/getcustomersinmap',
                        type: 'post',
                        async: false,
                        dataType: 'json',
                        data: form.serialize(),
                        beforeSend: function() {
                            table.dataTable().empty();
                            PECO.DTphpLoading(table, 'Loading list...');
                            $('#search_btn', document).html('<i class="fa fa-spinner fa-pulse fa-spin"></i> Loading.. ');
                        }
                    }).done(function (data) {
                        console.log(data);
                        customers_arr = data;
                        $('#search_btn', document).html('Search');

                    }).fail(function () {
                        PECO.phpError();
                        $('#search_btn', document).html('Search');

                    });
                    return customers_arr;
                }
                function drawListTable(data) {
                    table.dataTable().empty();
                    table.dataTable({
                        bDestroy: true,
                        bPaginate: true,
                        bFilter: true,
                        bInfo: true,
                        bStateSave: true,
                        bProcessing: true,
                        aaData: data,
                        aoColumns: [
                            {data: 'num', sClass: 'number text-danger', sWidth: '5%'},
                            {data: 'servno', sClass: ' text-info', sWidth: '10%'},
                            {data: 'mtrno', sClass: ' text-bold', sWidth: '5%'},
                            {data: 'name', sClass: 'text-primary', sWidth: '40%'},
                            {data: 'addrspec', sClass: '', sWidth: '40%'},
                            {data: 'gdlb', sClass: '', sWidth: '10%'},
                        ],
                        searchHighlight: true,
                        "order": [[0, "desc"]],
                        language: {
                            "emptyTable": '<h4><i class="fa fa-warning text-warning"></i> No transaction related records yet!</h4>'
                        },
                        searchHighlight: true,
                    });
                }
                $(form).on('submit', function (e) {
                    e.preventDefault();
                    var customers_arr = get_customers_locations(form);
                    if (customers_arr.qry == true) {
                        loadResults(customers_arr, map);
                        drawListTable(customers_arr.list);
                        amsChart(customers_arr);
                        map.fitZoom();
                    } else {
                        map.removeMarkers();
                        PECO.initAlerts("No Customer(s) Found!", 'Result Null', 'warning');
                    }
                });

                // LOAD CUSTOMERS MARKERS
                var customers_arr = get_customers_locations(form);
                drawListTable(customers_arr.list);
                loadResults(customers_arr, map);
                amsChart(customers_arr);
                map.fitZoom();

                function loadResults(data, map) {
                    map.removeMarkers();
                    var items, markers_data = [];
                    if (data.customers.length > 0) {
                        items = data.customers;

                        for (var i = 0; i < items.length; i++) {
                            var item = items[i];

                            if (item.location.lat != undefined && item.location.lng != undefined) {
                                markers_data.push({
                                    lat: item.location.lat,
                                    lng: item.location.lng,
                                    title: item.name,
                                    /*
                                    icon: {
                                        //size: new google.maps.Size(64, 64),
                                        //url: item.icon
                                    },
                                    */
                                    infoWindow: {
                                        content: item.content
                                    }
                                });
                            }
                        }
                    }

                    map.addMarkers(markers_data);
                    createInput(map);
                }

                function createInput(map) {
                    var input = document.getElementById('pac-input');
                    var searchBox = new google.maps.places.SearchBox(input);
                    map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);


                    // Bias the SearchBox results towards current map's viewport.
                    map.addListener('bounds_changed', function() {
                        searchBox.setBounds(map.getBounds());
                    });

                    var markers = [];
                    // Listen for the event fired when the user selects a prediction and retrieve
                    // more details for that place.
                    searchBox.addListener('places_changed', function() {
                        var places = searchBox.getPlaces();

                        if (places.length == 0) {
                            return;
                        }

                        // Clear out the old markers.
                        markers.forEach(function(marker) {
                            marker.setMap(null);
                        });
                        markers = [];

                        // For each place, get the icon, name and location.
                        var bounds = new google.maps.LatLngBounds();
                        places.forEach(function(place) {
                            if (!place.geometry) {
                                console.log("Returned place contains no geometry");
                                return;
                            }
                            var icon = {
                                url: place.icon,
                                size: new google.maps.Size(71, 71),
                                origin: new google.maps.Point(0, 0),
                                anchor: new google.maps.Point(17, 34),
                                scaledSize: new google.maps.Size(25, 25)
                            };

                            // Create a marker for each place.
                            markers.push(new google.maps.Marker({
                                map: map,
                                icon: icon,
                                title: place.name,
                                position: place.geometry.location
                            }));

                            if (place.geometry.viewport) {
                                // Only geocodes have viewport.
                                bounds.union(place.geometry.viewport);
                            } else {
                                bounds.extend(place.geometry.location);
                            }
                        });
                        map.fitBounds(bounds);
                    });
                }

                // WORKING WITH TABS
                $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                    var target = $(e.target).attr("href");
                    if (target === '#tab_map') {
                        console.log('tabs shown!');
                        map.fitZoom();
                    }
                    $('#view_title').html($(this).text());
                });
                var active_li = $("ul.nav-tabs li.active");
                var active_tx = active_li.find('a').text();
                $('#view_title').html(active_tx);


                function amsChart(chart_arr) {
                    var chart1 = AmCharts.makeChart("chart1", {
                        "type": "pie",
                        "startDuration": 1,
                        //"startEffect": "elastic",
                        "theme": "light",
                        "addClassNames": true,
                        "titles": [{
                            "text": chart_arr.charttitle,
                            "position": "left"
                        }],
                        "valueField": "size",
                        "pullOutRadius": 20,
                        "depth3D": 2,
                        "legend": {
                            "position": "right",
                            "marginRight": 100,
                            "autoMargins": true
                        },
                        "innerRadius": "20%",
                        "defs": {
                            "filter": [{
                                "id": "shadow",
                                "width": "250%",
                                "height": "250%",
                                "feOffset": {
                                    "result": "offOut",
                                    "in": "SourceAlpha",
                                    "dx": 0,
                                    "dy": 0
                                },
                                "feGaussianBlur": {
                                    "result": "blurOut",
                                    "in": "offOut",
                                    "stdDeviation": 5
                                },
                                "feBlend": {
                                    "in": "SourceGraphic",
                                    "in2": "blurOut",
                                    "mode": "normal"
                                }
                            }]
                        },
                        "dataProvider": chart_arr.chartarr,
                        "valueField": "CNT",
                        "titleField": "TITLE",
                        //"colorField": "color",
                        //"labelColorField": "color",
                        "export": {
                            "enabled": true
                        }
                    });
                    chart1.addListener("init", handleInit);
                    chart1.addListener("rollOverSlice", function (e) {
                        handleRollOver(e);
                    });
                    function handleInit() {
                        chart1.legend.addListener("rollOverItem", handleRollOver);
                    }
                    function handleRollOver(e) {
                        var wedge = e.dataItem.wedge.node;
                        wedge.parentNode.appendChild(wedge);
                    }

                }

                return map;


            });




    };

    var init_map_drawer = function(div, lat, lon, z) {
        var div_id = div.replace('#', '');
        var element =  document.getElementById(div_id);
        if (typeof(element) != 'undefined' && element != null) {
            var map = new GMaps({
                div: div,
                lat: lat,
                lng: lon,
                zoom: z,
                zoomControl: false,
                zoomControlOpt: {
                    style: "SMALL",
                    position: "TOP_LEFT"
                },
                panControl: false,
                streetViewControl: true,
                mapTypeControl: true,
                overviewMapControl: false
            });


            init_mapstyle(map);
            set_marker_specific(map, lat, lon, z);
        } else {
            alert('Map element not found! : ' + div_id) ;
        }
    };

    var init_map_specific = function(div, dataid, type) {
        var lat, lon, alt, spec, title, text;
        $.ajax({
            url: PECO.base_url() + 'inspection/getaccountmap',
            type: 'post',
            async: false,
            dataType: 'json',
            data: {'id': dataid, 'type': type},
            beforeSend: function() {
                $(div, document).html('<h3><i class="fa fa-refresh fa-spin"></i> Loading map...</h3>');
            }
        }).done(function (data) {
            if(data.qry==true) {
                console.log(data);

                $(div).css('height', '55vh');

                lat = data.lat;
                lon = data.lon;
                alt = data.alt;
                //spec = data.spec;
                //title = data.servno;
                //text = data.name;
                var map = new GMaps({
                    div: div,
                    lat: lat,
                    lng: lon,
                    zoom: alt,
                    zoomControl : false,
                    zoomControlOpt: {
                        style : "SMALL",
                        position: "TOP_LEFT"
                    },
                    panControl : false,
                    streetViewControl : true,
                    mapTypeControl: true,
                    overviewMapControl: false
                });


                //init_mapstyle(map);
                set_marker_specific(map, lat, lon, alt);
                $(div).after(
                    '<div class="row">' +
                    '<div class="col-md-12">'+
                    '<code class="" style="font-size: 10px; display: block">'+
                    '<span id="map_lat">'+lat+'</span>'+
                    ' / ' +
                    '<span id="map_lon">'+lon+'</span>'+
                    '</code>' +
                    '</div>'+
                    '</div>'
                );
                //                     '<div class="col-md-12"><div class="well" style="margin-bottom: 0px; padding: 5px 10px;"><span class="text-info text-color-blue">'+get_map_address(lat, lon).responseJSON.results[0].formatted_address+'</span></div></div>'+

            }else{
                var html = '';
                html += '<div style="margin: 20px 20px; display: inline-block; width: 100%;" class="text-align-center">';
                html += '<i class="fa fa-map-marker font-red-flamingo" style="font-size: 6em; margin-top: 20px;"></i>';
                html += '<h3><b> Map Data Not Found</b></h3>';
                html += '<p>Map geodata is not yet encoded!</p>';
                html += '</div>';
                $(div).html(html);
            }
        });
    };

    var init_mapping = function (dataid, div, editable, moduleid, gkey) {
        PECO.getGoogleKey();

        $(div).html('<span class="text-info"><i class="fa fa-spinner fa-spin fa-pulse"></i> Loading mapping table..</span>');
        setTimeout(function () {
            var lat, lon, alt, spec;
            // GET MAPPING INFO
            $.ajax({
                url: PECO.base_url() + 'inspection/getaccountmapping',
                type: 'post',
                async: false,
                dataType: 'json',
                data: {'id': dataid},
            }).done(function (data) {
                console.log(data);
                lat = data.lat;
                lon = data.lon;
                alt = data.alt;
                spec = data.spec;
            });




            if (editable) {
                $(div).before(
                    '<input type="hidden" value="' + dataid + '" name="dataid" />' +
                    '<input type="hidden" id="input_lat" name="lon" readonly="" value="' + lat + '">' +
                    '<input type="hidden" id="input_lon" name="lat" readonly="" value="' + lon + '">' +
                    '<div class="form-group well" style="padding: 10px 10px; margin-bottom: 0px;">' +
                    '<div class="row">' +
                    '<div class="col-md-6">' +
                    '<div class="form-group">' +
                    '<label for="latitude">Latitude</label>' +
                    '<h3 class="text-info" style="padding: 0px 0px !important; margin: 0px 0px;"><i class="fa fa-location-arrow"></i> <span id="text_lat">' + lat + '</span></h3>' +
                    '</div>' +
                    '</div>' +
                    '<div class="col-md-6">' +
                    '<div class="form-group">' +
                    '<label for="longitude">Longitude</label>' +
                    '<h3 class="text-info" style="padding: 0px 0px !important;; margin: 0px 0px;"><i class="fa fa-location-arrow"></i> <span id="text_lon">' + lon + '</span></h3>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '<div class="form-group has-success"><div class="input-group input-icon">' +
                    '<i class="fa fa-search"></i><input type="text" class="form-control tooltips" data-container="body" data-placement="top" data-original-title="Google Maps Address"  id="search_address" value="" placeholder="Search address...">' +
                    '<span class="input-group-btn">' +
                    '<button class="btn green-turquoise fa fa-search" id="btn_search_address"></button>' +
                    '<button class="btn blue-madison fa fa-save tooltips" data-container="body" data-placement="top" data-original-title="Capture map coordinates" id="capture" name="capture"></button>' +
                    '<label for="capture" class="tooltip">Capture</label>' +
                    '</span>' +
                    '</div></div>' +
                    '<div class="form-group has-success margin-top-10">' +
                    '<div class=" input-icon ">' +
                    '<i class="fa fa-map-o"></i><input type="text" class="form-control tooltips" data-container="body" data-placement="right" data-original-title="Specific Address / Landmarks" id="spec_address" value="' + spec + '" placeholder="Specific Address">' +
                    '</div></div>'
                );
            }
            $(div).after(
                '<div class="input-icon well" style="padding: 5px 5px;">' +
                '<i class="fa fa-map-marker" style="margin-top: 10px !important; margin-left: 0px !important; color: red !important;"></i>' +
                '<h4 class="text-info"> ' +
                '<span id="details_address" style="margin-left: 20px; display: inline-block;">' + spec + '</span>' +
                '</h4>' +
                '</div>'
            );
            $('.tooltips').tooltip();

            var map = new GMaps({
                div: div,
                lat: lat,
                lng: lon,
            });

            set_marker(map, lat, lon, 'Location', 'Text', 18);


            if (editable == true) {
                map.addListener('click', function (event) {
                    latitude = event.latLng.lat();
                    longitude = event.latLng.lng();
                    map.removeMarkers();
                    set_marker(map, latitude, longitude, 'map click location', 'coordinate: (' + latitude + ', ' + longitude + ')', map.getZoom());
                    get_map_address(latitude, longitude);
                });
            }

            $('#btn_search_address').click(function (e) {
                e.preventDefault();
                handle_search(map);
            });


            $('#capture').click(function (e) {
                e.preventDefault();
                $.ajax({
                    url: PECO.base_url() + 'inspection/updategeodata',
                    data: {
                        'a': $('#spec_address', document).val(),
                        'x': $('#input_lat', document).val(),
                        'y': $('#input_lon', document).val(),
                        'i': dataid,
                        'moduleid': moduleid,
                        'inspdate': $('#inspection_date', document).val(),
                        'remarks': $('#inspection_remarks', document).val()
                    },
                    type: 'post',
                    dataType: 'json'
                }).done(function (data) {

                    if (data.qry == true) {
                        PECO.initAlerts('Geo Location has been updated!', 'Success', 'success');
                    } else {
                        PECO.initAlerts('Geo Location has not updated!', 'Warning', 'warning');
                    }
                }).fail(function () {
                    PECO.initAlerts('Error PHP', 'ERROR', 'error');
                });
            });

            if (lat == 0 || lon == 0) {
                GMaps.geolocate({
                    success: function (position) {
                        map.setCenter(position.coords.latitude, position.coords.longitude);
                        lat = position.coords.latitude;
                        lon = position.coords.longitude;
                        get_map_address(lat, lon);
                    },
                    error: function (error) {
                        alert('Geolocation failed: ' + error.message);
                    },
                    not_supported: function () {
                        alert("Your browser does not support geolocation");
                    }
                });
            } else {
                get_map_address(lat, lon);
            }

            /*



            if (editable == true) {
                map.addListener('click', function (event) {
                    latitude = event.latLng.lat();
                    longitude = event.latLng.lng();
                    map.removeMarkers();
                    set_marker(latitude, longitude, 'map click location', 'coordinate: (' + latitude + ', ' + longitude + ')', map.getZoom());
                    get_map_address(latitude, longitude);
                });
            }

            set_marker(lat, lon, 'Location', 'Text', 18)
            PECO.scrollTo($(div));



            $('body').on('keyup', "#search_address", function (e) {
                if (!e.metaKey) {
                    var code = (e.keyCode ? e.keyCode : e.which);
                    if (code == 13) {
                        handle_search();
                        e.preventDefault();
                    }
                }
            });






            */

        }, 1000);
    };


    var handle_search = function(map) {
        var text = $.trim($('#search_address', document).val());
        GMaps.geocode({
            address: text,
            callback: function (results, status) {
                if (status == 'OK') {
                    var latlng = results[0].geometry.location;
                    latitude = latlng.lat();
                    longitude = latlng.lng();
                    set_marker(map, latitude, longitude, "Inspection location", text, 17);
                    get_map_address(latitude, longitude);
                }
            },
        });
    };


    var set_marker = function(map, lat, lon, title, text, zoom) {

        $('#input_lat', document).val(lat);
        $('#text_lat', document).text(lat);
        $('#input_lon', document).val(lon);
        $('#text_lon', document).text(lon);

        map.setCenter(lat, lon);
        map.addMarker({
            lat: lat,
            lng: lon,
            title: title,
            infoWindow: {
                content: '<span style="color:#000"><i class="fa fa-map-pin"></i> ' + text + '</span>'
            }
        });
        map.setZoom(zoom);
    };

    var init_dt_expandbtn = function(nRow, id) {
        return $(nRow).find('td').eq(0).html('<i id="btn-expand" class="fa fa-angle-right" data-id="'+id+'"></i>');
    };

    var init_customer_map = function(dataid) {
        $.ajax({
            url: PECO.base_url() + 'inspection/getaccountmap',
            type: 'post',
            async: false,
            dataType: 'json',
            data: {'id': dataid, 'type': 0},
        }).done(function (data) {
            if (data.qry == true) {

                if(typeof data.lat != 'undefined' && typeof data.lon != 'undefined') {

                    var lat = data.lat;
                    var lon = data.lon;
                    var latLng = new google.maps.LatLng(lat, lon);
                    var mapProp = {
                        center: latLng,
                        zoom: 16,
                        mapTypeId: google.maps.MapTypeId.ROADMAP
                    };
                    var map = new google.maps.Map(document.getElementById("googleMap"), mapProp);
                    var marker = new google.maps.Marker({
                        position: latLng,
                        visible: true
                    });
                    marker.setMap(map);
                }else{
                    alert('map not set yet!');
                }
            }
        });
    };


    var init_dt_subdetails = function(tbl, url, inputs_arr, clss) {
        var inputs_arr = (inputs_arr) ? inputs_arr : false;
        var clss_ = (clss) ? clss : '';
        tbl.on('click', '#btn-expand', function () {
            var this_ = $(this);
            var thisTr = this_.closest('tr');
            var thisTr_child = thisTr.children('td').length;
            var data_id = this_.attr('data-id');
            if (this_.hasClass('expanded') == false) {

                thisTr.next('#error').remove();
                this_.removeClass('fa-angle-right').addClass('fa-angle-down');
                $.ajax({
                    url: PECO.base_url()+url,
                    type: 'post',
                    data: {'id': data_id, 'inputs': inputs_arr},
                    dataType: 'json',
                    beforeSend: function () {
                        thisTr.after('<tr id="loading" class="info " ><td colspan="' + thisTr_child + '" class=""><i class="fa fa-spinner fa-spin fa-pulse"></i> Loading..</td></tr>');

                    }
                }).done(function(d){
                    thisTr.after('<tr class="animated fadeIn fast compact '+d.func+'" id="details"><td colspan="' + thisTr_child + '" class="'+clss_+'">' + d.html + '</td></tr>');
                    tbl.find('#loading').remove();
                }).fail(function(){
                    thisTr.after('<tr class="animated fadeIn fast compact danger" id="error"><td colspan="' + thisTr_child + '"><i class="fa fa-times"></i> Error PHP</td></tr>');
                    tbl.find('#loading').remove();
                });
            } else {
                thisTr.next('#details').remove();
                thisTr.next('#error').remove();
                tbl.find('#loading').remove();
                this_.removeClass('fa-angle-down').addClass('fa-angle-right');
            }
            this_.toggleClass('expanded');
            this_.closest('tr').toggleClass('expand-show');
        });

    };

    var set_marker_specific = function(map, lat, lon, alt) {
        map.setCenter(lat, lon);
        map.addMarker({
            lat: lat,
            lng: lon
        });
        map.setZoom(14);
    };

    var get_map_address = function(lat, lon) {
        // GET MAP ADDRESS NAME
        var mapinfo;
        return $.ajax({
            url: "https://maps.googleapis.com/maps/api/geocode/json?key="+PECO.google_api()+"&latlng=" + lat + "," + lon + "&sensor=true",
            async: false,
            dataType: 'json',
            data: 'text',
        }).done(function (data) {
            mapinfo = data;
            console.log(data);


            if(typeof data.results[0] !== "undefined") {
                var address_full = data.results[0].formatted_address;
                $('#search_address', document).val(address_full);
            }

        });
        return mapinfo;
    };

    var handleHeaderFixedScroll = function() {
        $(window).scroll(function() {
            if($(window).scrollTop() > 0) {
                $('body').addClass('content-scrolls');
            } else {
                $('body').removeClass('content-scrolls');
            }
        });
        /*
        $('.page-content', document).niceScroll({
            styler: "fb",
            cursorcolor: "rgba(215, 98, 44, 0.6)",
            cursorwidth: '5',
            cursorborderradius: '0px',
            background: 'transparent',
            cursorborder: '',
            zindex: '1',
            emulatetouch: false,
        });
        */
    };
    var handleDigitalClock = function() {

        PECO.getDigitalClock();

        setTimeout(function() {
            if(jQuery().MyDigitClock) {
                if($("#clock").length>0) {
                    $("#clock").MyDigitClock(
                        {
                            fontSize: 14,
                            fontFamily: "arial",
                            fontColor: "#fff",
                            fontWeight: "bold",
                            background: 'transparent',
                            bAmPm: true,
                            bShowHeartBeat: true
                        }
                    );
                }

            }
        }, 1000);

        /*
        var $dOut = $('#date'),
            $hOut = $('#hours'),
            $mOut = $('#minutes'),
            $sOut = $('#seconds'),
            $ampmOut = $('#ampm');
        var months = [
            'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'
        ];

        var days = [
            'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'
        ];

        function update(){
            var date = new Date();

            var ampm = date.getHours() < 12
                ? 'AM'
                : 'PM';

            var hours = date.getHours() == 0
                ? 12
                : date.getHours() > 12
                    ? date.getHours() - 12
                    : date.getHours();

            var minutes = date.getMinutes() < 10
                ? '0' + date.getMinutes()
                : date.getMinutes();

            var seconds = date.getSeconds() < 10
                ? '0' + date.getSeconds()
                : date.getSeconds();

            var dayOfWeek = days[date.getDay()];
            var month = months[date.getMonth()];
            var day = date.getDate();
            var year = date.getFullYear();

            var dateString = dayOfWeek + ', ' + month + ' ' + day + ', ' + year;

            $dOut.text(dateString);
            $hOut.text(hours);
            $mOut.text(minutes);
            $sOut.text(seconds);
            $ampmOut.text(ampm);

            if((hours==12 && minutes <= 5) || (hours==11 && minutes >= 55)) {
                $('.page-sidebar-menu li.heading ', document).removeClass('bg-blue-soft').addClass('bg-red-flamingo');
                setTimeout(function(){
                    $('.page-sidebar-menu li.heading ', document).removeClass('bg-red-flamingo').addClass('bg-blue-soft');
                }, 300);
            }else{
                $('.page-sidebar-menu li.heading ', document).removeClass('bg-red-flamingo').addClass('bg-blue-soft');
            }
        }

        update();
        window.setInterval(update, 1000);
        */
    };

    var init_fancybox_all = function() {
        $("a[href$='.jpg'],a[href$='.png'],a[href$='.gif']").attr('rel', 'gallery').fancybox(
            {

                padding: 0,
                openEffect : 'elastic',
                closeEffect : 'elastic',
                closeSpeed  : 150,
                openSpeed  : 150,
                closeClick : true,
                autoScale    : true,
                helpers : {
                    overlay : null
                }
            }
        );
    };

    var init_map = function(ids, type) {

        var directionsService = new google.maps.DirectionsService();
        var directionsDisplay = new google.maps.DirectionsRenderer();

        setTimeout(function(){
            var origLatlng = {lat: 10.7019954, lng: 122.5634341}; // PECO
            var map = new google.maps.Map(document.getElementById('map'), {
                zoom: 15,
                center: origLatlng
            });
            $.ajax({
                url: PECO.base_url() + 'mrd/getacctmaparr',
                type: 'post',
                data: {'id': ids, 'type': type},
                dataType: 'json',
                beforeSend: function() {
                    $('#loading_box').removeClass('hidden').html('<h4 class="text-info" style="margin: 10px 10px"><i class="fa fa-spinner fa-spin fa-pulse"></i> Getting location data...</h4>');
                }
            }).done(function(d) {
                if(d.qry==true) {
                    var distLatLng = d.latlngarr; // PECO
                    init_map_direction(map, origLatlng, distLatLng, directionsService, directionsDisplay);
                    $('#lat', document).text(d.latlngarr['lat']);
                    $('#lng', document).text(d.latlngarr['lng']);
                    $('#loading_box').addClass('hidden');
                }else{
                    $('#loading_box').removeClass('hidden').html('<h4 class="text-danger" style="margin: 10px 10px"><i class="fa fa-times"></i> No map data found!</h4>');
                }
            });
        }, 500);

        $('#print_map', document).on('click', function printAnyMaps() {
            const $body = $('body');
            const $mapContainer = $('#map');
            const $mapContainerParent = $mapContainer.parent();
            const $printContainer = $('<div style="position:relative;">');

            $printContainer
                .height($mapContainer.height())
                .append($mapContainer)
                .prependTo($body);

            const $content = $body
                .children()
                .not($printContainer)
                .not('script')
                .detach();

            /**
             * Needed for those who use Bootstrap 3.x, because some of
             * its `@media print` styles ain't play nicely when printing.
             */
            const $patchedStyle = $('<style media="print">')
                .text('img { max-width: none !important; } a[href]:after { content: ""; }')
                .appendTo('head');

            window.print();

            $body.prepend($content);
            $mapContainerParent.prepend($mapContainer);

            $printContainer.remove();
            $patchedStyle.remove();
        });

    };

    var init_map_direction = function(map, start, end, directionsService, directionsDisplay) {
        var request = {
            origin: start,
            destination: end,
            travelMode: 'DRIVING'
        };
        directionsService.route(request, function(response, status) {
            console.log(response);
            if (status == 'OK') {
                directionsDisplay.setDirections(response);
            }
        });
        directionsDisplay.setMap(map);
    };

    var init_shortcut_button = function() {
        $(document).on('click', '[data-toggle="shortcut"]', function(e){
            e.preventDefault();
            var this_ = $(this);
            var this_moduleid = this_.attr('data-id');
            $.ajax({
                url: PECO.base_url() + 'user/addshortuct',
                type: 'post',
                data: {'moduleid': this_moduleid},
                dataType: 'json',
                beforeSend: function() {

                }
            }).done(function(d) {
                this_.removeClass('default').addClass('btn-success');
                $('.fa', this_).removeClass('fa-plus').addClass('fa-map-pin');
                PECO.initAlerts('Page has been added to your cad shortcuts!', 'PECO.net', 'success');
            });
        });
    };

    var init_employee_search = function() {
        var lastname = $('#lastname', document);
        var firstname = $('#firstname', document);
        var middlename = $('#middlename', document);

        var a = new Bloodhound({
            datumTokenizer: function (e) {
                return e.tokens
            },
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: {url: PECO.base_url() + "search/employeesearch?query=%QUERY", wildcard: "%QUERY"}
        });

        a.initialize(), lastname.typeahead(null, {
            hint: false,
            highlight: true,
            minLength: 1,
            displayKey: "lastname",
            source: a.ttAdapter(),
            cache: false,
            templates: {
                suggestion: Handlebars.compile(['<div class="media">', '<div class="pull-left">', '<div class="media-object">', '<img src="{{img}}" width="50" height="50"/>', "</div>", "</div>", '<div class="media-body">', '<h5 class="media-heading text-primary"><b class="text-glow-yellow">{{lastname}}</b>, {{firstname}} {{middlename}}</h5>', "<p><b>{{dept}}</b></p><p>{{district}} - {{addr}}</p>", "</div>", "</div>"].join("")),
            },
        }).on('typeahead:selected', function(event, selection) {
            firstname.val(selection.firstname);
            middlename.val(selection.middlename);
        });
    };


    var preOutput = function(inp) {
        document.getElementById('console_res').innerHTML = inp;
    };

    var syntaxHighlight = function (json) {
        json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
        return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function (match) {
            var cls = 'number';
            if (/^"/.test(match)) {
                if (/:$/.test(match)) {
                    cls = 'key';
                } else {
                    cls = 'string';
                }
            } else if (/true|false/.test(match)) {
                cls = 'boolean';
            } else if (/null/.test(match)) {
                cls = 'null';
            }
            return '<span class="' + cls + '">' + match + '</span>';
        });
    };

    var init_quick_launch = function() {
        $(document).on('click', 'a#quick_launch', function(e) {
            $('#quick_launch_item .items', document).remove();
            var html = '';
            $.ajax({
                url: PECO.base_url() + 'systems/quicklaunchlist',
                type: 'post',
                data: {},
                dataType: 'json',
                beforeSend: function() {
                    $('#quick_launch_item', document).append('<li class="item" id="loading"><a><i class="fa fa-spinner fa-spin fa-pulse"></i> Loading...</a></li>');
                }
            }).done(function(d){
                $('#quick_launch_item #loading', document).remove();
                $('#quick_launch_item', document).append(d.html);

                $('#quick_launch_item li a', document).each(function() {
                    $(this).tooltip();
                });
                return false;
            });
        });
    };

    var init_leave_submit = function() {
        $(document).on('submit', '#frm_add_employee_leave_draft', function(e) {
            e.preventDefault();
            var form = $(this);
            $.ajax({
                url: form.attr('action'),
                type: form.attr('method'),
                data: form.serialize(),
                dataType: 'json',
            }).done(function(d){
                if(typeof EMPLOYEEREQ != "undefined") {
                    var empid = d.empid;
                    var year = d.year;
                    EMPLOYEEREQ.trntemp(empid, year);
                }
            });
        });

        $(document).on('click', '#btn_submit_leave', function(e) {
            e.preventDefault();
            var year = $('#input_leave_year', document).val();
            var remarks = $('#input_leave_remarks', document).val();
            if(year == ''){
                PECO.initAlerts("Year is empty" , "PECO" , "info");
            }else{
                swal({
                    title: "Are you sure?",
                    text: "Leave form will be submitted",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Yes, Process!",
                    closeOnConfirm: false,
                    closeOnCancel: false,
                    showLoaderOnConfirm: true
                }, function(isConfirm){
                    if (isConfirm) {
                        $.ajax({
                            url:PECO.base_url()+'hris/submitleaveformpersonal',
                            type:'post',
                            data:{"year" : year ,"remarks" : remarks},
                            dataType:'json'
                        }).done(function (data) {
                            swal("PECO", data.msg, data.func);
                            if(data.qry  == true){
                                $('#list_leave_credits').hide();
                                fetchdraftrequestleavetbl(employeeselect2 , year);
                                $('#yearleave').select2('val' , '');
                            }
                        }).fail(function () {
                            PECO.phpError();
                        });

                    } else {
                        swal("Cancelled", "Processing canceled", "error");
                    }
                });
            }
        });
    };

    var handlerTransactionRouteDirect = function() {

        // #######################################
        // ROUTE TO
        $(document).on('click', '#btn_send_trn_next', function(e){
            e.preventDefault();
            var btn = $(this);
            var data_id = btn.attr('data-id');
            var data_trnid = btn.attr('data-trnid');
            var data_view = {dataid: data_id, trnid: data_trnid, process: 1};
            var data_submit = {dataid: data_id, trnid: data_trnid, types: 1, process: 1};
            var data_message = 'Please confirm send to next route.';
            handlerProcessTransactionModal(btn, data_view, data_submit);
        });

        // #######################################
        // SEND BACK
        $(document).on('click', '#btn_send_trn_back', function(e){
            e.preventDefault();
            var btn = $(this);
            var data_id = btn.attr('data-id');
            var data_trnid = btn.attr('data-trnid');
            var data_view = {dataid: data_id, trnid: data_trnid, process: 0};
            var data_submit = {dataid: data_id, trnid: data_trnid, types: 1, process: 0};
            var data_message = 'Please confirm send back.';
            handlerProcessTransactionModal(btn, data_view, data_submit);
        });
    };

    var handlerModalMap = function() {

        $(document).on('click', '[data-toggle="ajax-modal-map"]', function(e) {

            var el = $('body', document);
            var this_ = $(this);
            var this_id = this_.attr('data-id');

            PECO.blockUI({
                target: el,
                animate: true,
                overlayColor: '#64A8C8'
            });


            var modal_html = '';
            modal_html += '<div class="modal fade bs-modal-lg"  id="modal_map" tabindex="-1" role="dialog" aria-hidden="true">';
            modal_html += '<div class="modal-dialog modal-lg">';
            modal_html += '<div class="modal-content">';
            modal_html += '<div class="modal-header" id="">';
            modal_html += '<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>';
            modal_html += '<h4 class="modal-title" id="modal_title"><i class="fa fa-edit"></i> <span id="modal_title"></span></h4>';
            modal_html += '</div>';
            modal_html += '<div class="modal-body" id="map_lookup_' + this_id + '"></div>';
            modal_html += '</div>';
            modal_html += '</div>';
            modal_html += '</div>';


            el.append(modal_html);




            setTimeout(function() {

                var this_modal = $('#modal_map', document);
                this_modal.modal('show');
                this_modal.on('shown.bs.modal', function () {
                    var this_modal_title = $('#modal_title', this_modal);
                    this_modal_title.html('<i class="fa fa-map-marker fa-fw"></i>' + this_.attr('title'));


                    PECO.initMapSpec('#map_lookup_' + this_id, this_id, 0);

                    PECO.unblockUI(el);
                });


                this_modal.draggable({
                    handle: ".modal-header"
                });

                this_modal.on('hidden.bs.modal', function () {
                    this_modal.remove();
                });



            }, 500);


        });

    };

    var handlerProcessTransactionModal = function(btn, vdata, sdata) {
        var btn_row = btn.closest('tr');

        var el = $('body', document);
        var this_modal = $('#modal_transaction', document);
        $('#trn_process_direct_routeto', this_modal).val();
        $('#trn_process_direct_routeto', this_modal).select2('destroy');

        $.ajax({
            url: PECO.base_url() + 'query/requestprocessdirect',
            type: 'post',
            data: vdata,
            dataType: 'json',
            beforeSend: function() {
                PECO.blockUI({
                    target: el,
                    animate: true,
                    overlayColor: '#64A8C8'
                });
            }
        }).done(function(d){
            var this_modal_title = $('#modal_title', this_modal);
            this_modal.modal('show');
            this_modal.on('shown.bs.modal', function () {
                PECO.unblockUI(el);
                this_modal_title.text(d.trnname);
                $('#text_route_curr', this_modal).html(d.route_curr);
                $('#text_route_next', this_modal).html(d.route_next);
                $('#trn_process_direct_trnid', this_modal).val(d.current_trnid);
                $('#trn_process_direct_flowid', this_modal).val(d.current_flowid);
                $('#trn_process_direct_stageid', this_modal).val(d.current_stageid);
                $('#trn_process_direct_moduleid', this_modal).val(d.current_moduleid);
                $('#trn_process_direct_dataid', this_modal).val(d.current_dataid);
                $('#trn_process_direct_routeto', this_modal).val(d.next_route_id);
                $('#frm_trn_process_direct_submit', this_modal).attr('title', d.title);
                setTimeout(function() {
                    PECO.select2Basic($('#trn_process_direct_routeto', this_modal), 'query/gettrnflowstages', 'Select route...', false, false, false, false, false, {'flowid': d.current_flowid});
                }, 200);
            });
            this_modal.draggable({
                handle: ".modal-header"
            });
        }).fail(function(xhr, textStatus, errorThrown) {
            PECO.initAlerts('Error PHP: ' +textStatus, 'PA Energy', 'error');
            PECO.unblockUI(el);
        });


        /*
        $.ajax({
            url: PECO.base_url() + 'query/requestprocessdirect',
            type: 'post',
            data: vdata,
            dataType: 'json',
        }).done(function(d){
            swal({
                title: title,
                text: d.msg,
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Send",
                closeOnConfirm: false,
                closeOnCancel: false,
                showLoaderOnConfirm: true
            }, function(isConfirm) {
                if(isConfirm) {
                    $.ajax({
                        url: PECO.base_url() + 'query/requestprocessdirect',
                        type: 'post',
                        data: sdata,
                        dataType: 'json',
                    }).done(function (d) {
                        swal(d.title, d.msg, d.func);
                        if(d.func == 'success'){
                            btn_row.remove();
                        }
                    }).fail(function () {
                        swal.close();
                        PECO.phpError();
                    });
                } else {
                    swal.close();
                }
            });
        }).fail(function() {
            PECO.phpError();
        });

         */
    };

    var handlerTrnDirectSubmit = function() {
        $(document).on('submit', '#frm_trn_process_direct_submit', function(e) {
            e.preventDefault();
            var form = $(this);

            confirm_arr = {title: 'Task Update', 'dataname': 'New Account'};
            PECO.ajaxConfirmForm(form, confirm_arr);
        });
    };

    var handlerItemDataEntry = function() {
        $(document).on('submit', '#frm_submit_items', function(e) {
            e.preventDefault();
            var form = $(this);
            $.ajax({
                url: form.attr('action'),
                type: form.attr('method'),
                data: form.serialize(),
                dataType: 'json',
            }).done(function(d) {
                swal(d.title, d.msg, d.func);
            }).fail(function() {
                PECO.phpError();
            });
        });

    };

    var handler_search_item_category = function() {

        var item_category = $('#item_category_search', document);
        var supp_category = $('#item_supplier_search', document);
        var supplier_branch = $('#supplier_branch', document);

        var a = new Bloodhound({
            datumTokenizer: function (e) {
                return e.tokens
            },
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: {url: PECO.base_url() + "search/itemcategory?query=%QUERY", wildcard: "%QUERY"}
        });


        a.initialize(), item_category.typeahead(null, {
            hint: false,
            highlight: true,
            minLength: 1,
            displayKey: "names",
            source: a.ttAdapter(),
            cache: false,
            templates: {
                suggestion: Handlebars.compile(['<div class="media">', '<div class="pull-left">', '<div class="media-object">', '<i class="fa fa-tag"></i>', "</div>", "</div>", '<div class="media-body">', '<h5 class="media-heading text-primary"><b class="text-glow-yellow">{{codes}}</b>, {{names}}</h5>', "</div>", "</div>"].join("")),
            },
        }).on('typeahead:selected', function(event, selection) {

        }).click(function() {
            PECO.initElScroller($('.tt-dropdown-menu', document));
        });


        handlerItemSearchComponents();
        handlerCorporationSearch();
    };

    var handlerItemSearchComponents = function() {
        var item_component = $('#item_comp_search', document);
        var item_category = $('#item_category_search', document);
        var ics = new Bloodhound({
            datumTokenizer: function (e) {
                return e.tokens
            },
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            // remote: {url: PECO.base_url() + "search/itemcomponentsearch?query=%QUERY", wildcard: "%QUERY"}
            remote: {
                url: PECO.base_url() + "search/itemcomponentsearch/",
                replace: function (url, query) {
                    return url + query.toUpperCase() + '/' + item_category.val()
                },
                wildcard: "%QUERY",
            }
        });
        ics.initialize(), item_component.typeahead(null, {
            hint: false,
            highlight: true,
            minLength: 1,
            displayKey: "names",
            source: ics.ttAdapter(),
            cache: false,
            templates: {
                suggestion: Handlebars.compile(['<div class="media">', '<div class="pull-left">', '<div class="media-object">', '<i class="fa fa-tag"></i>', "</div>", "</div>", '<div class="media-body">', '<h5 class="media-heading text-primary"><b class="text-glow-yellow">{{codes}}</b>, {{names}}</h5>', "</div>", "</div>"].join("")),
            },
        }).on('typeahead:selected', function(event, selection) {

        }).click(function() {
            PECO.initElScroller($('.tt-dropdown-menu', document));
        });
    };

    var handler_internet_notify = function() {
        setInterval(function() {
            if(navigator.onLine){
                $('#internet_connect', document)
                    .removeClass('icon-footer-offline')
                    .addClass('icon-footer-online')
                    .attr('title', 'You are connected to network!');
                $('#internet_connect .internet-text', document).text('Online');
                $('#internet_connect .fa', document);
            } else {
                $('#internet_connect', document)
                    .removeClass('icon-footer-online')
                    .addClass('icon-footer-offline')
                    .attr('title', 'You may not connected to the internet!');

                $('#internet_connect .internet-text', document).text('Offline');
                $('#internet_connect .fa', document);
            }

            $('#internet_connect', document).tooltip();
        },1000);
    };

    var searchtooltip = function(el,url,otherParams) {

        //var item_category = $('#item_category_search', document);
        //var supp_category = $('#item_supplier_search', document);
        //var supplier_branch = $('#supplier_branch', document);
        var params_str = [];
        var params = '';
        //console.log(otherParams);
        if (otherParams && (otherParams.constructor === Object || otherParams.constructor === Array)){
            $.each(otherParams,function (key,value) {
                var str = key + '=' + value;
                params_str.push(str);
            });
            params = '&' + params_str.join('&');
        }

        var a = new Bloodhound({
            datumTokenizer: function (e) {
                return e.tokens
            },
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: {url: PECO.base_url() + url + "?query=%QUERY" + params, wildcard: "%QUERY"}
        });


        a.initialize(), el.typeahead(null, {
            hint: false,
            highlight: true,
            minLength: 1,
            displayKey: "names",
            source: a.ttAdapter(),
            cache: false,
            templates: {
                suggestion: Handlebars.compile(['<div class="media">', '<div class="pull-left">', '<div class="media-object">', '<i class="fa fa-tag"></i>', "</div>", "</div>", '<div class="media-body">', '<h5 class="media-heading text-primary"><b class="text-glow-yellow">{{codes}}</b>, {{names}}</h5>', "</div>", "</div>"].join("")),
            },
        }).on('typeahead:selected', function(event, selection) {
            console.log(event);
            console.log(selection);
        }).click(function() {
            PECO.initElScroller($('.tt-dropdown-menu', document));
        });
    };


    return {
        //main function to initiate the theme
        init: function () {
            init_shortcut_button();
            init_fancybox_all();
            init_quick_launch();
            init_leave_submit();
            init_email_submit();

            handleDigitalClock();
            //IMPORTANT!!!: Do not modify the core handlers call order.
            handlerTransactionRouteDirect();
            handlerTrnDirectSubmit();

            handlerModalMap();


            // NICESCROLLER FOR ALL PAGES
            html_niceScroller();
            handler_internet_notify();
            // TROUBLE CALL
            handlerTroubleCall();
            handlerJobOrder();

            // TECH SUPPORT
            handlerTechlogEntry();

            // AJAX HANDLER
            handlerAjaxModal();

            // ITEM DATA ENTRY HANDLER
            handlerItemDataEntry();

            //Core handlers
            handleInit(); // initialize core variables
            handleOnResize(); // set and handle responsive

            //UI Component handlers     
            handleMaterialDesign(); // handle material design       
            handleUniform(); // hanfle custom radio & checkboxes
            handleiCheck(); // handles custom icheck radio and checkboxes
            handleBootstrapSwitch(); // handle bootstrap switch plugin
            handleScrollers(); // handles slim scrolling contents 
            handleFancybox(); // handle fancy box
            handleSelect2(); // handle custom Select2 dropdowns
            handlePortletTools(); // handles portlet action bar functionality(refresh, configure, toggle, remove)
            handleAlerts(); //handle closabled alerts
            handleDropdowns(); // handle dropdowns
            handleTabs(); // handle tabs
            handleTooltips(); // handle bootstrap tooltips
            handlePopovers(); // handles bootstrap popovers
            handleAccordions(); //handles accordions 
            handleModals(); // handle modals
            handleBootstrapConfirmation(); // handle bootstrap confirmations

            // Hacks
            handleFixInputPlaceholderForIE(); //IE8 & IE9 input placeholder issue fix
            btnProcess();
            // html_niceScroller();
            // select2_niceScroller();

            handleHeaderFixedScroll();

            handlerNotifications();
            //handlerNotificationsCnt();
            init_sweetbootstrap_alert();

        },
        corporationSearch: function() {
            handlerCorporationSearch();
        },
        governmentSearch: function() {
            handlerGovernmentSearch();
        },
        handlerEmployeeSearchBasic: function() {
            init_employee_search();
        },
        handlerSearchItemCategory: function() {
            handler_search_item_category();
        },
        handlerAccntMap: function(ids, type) {
            init_map(ids, type);
        },
        handlerBarangay: function(el, distid, initdata, mode, focus) {
            brgyHandler(el, distid, initdata, mode, focus);
        },
        handleriCheckForm: function(container) {
            init_handler_icheck_form(container);
        },
        handlerComplaintsEntry: function() {
            handler_complaints_entry();
        },

        handlerLandmark: function(el, distid, brgyid, initdata, mode) {
            landMarkSelectHandler(el, distid, brgyid, initdata, mode);
        },
        tcinit: function(int) {
            handlerTroubleCall(int);
        },
        cdeinit: function(int) {
            handlerCustomerDataEntry(int);
        },
        joinit: function(view){
            handlerJobOrder(view);
        },
        apprehension: function() {
            handlerApprehensionEntry();
        },
        techlog: function() {
            handlerTechlogEntry();
        },
        session: function() {
            init_sessions();
        },
        notification_timeout: function () {
            return NotifyTimeOut;
        },
        start_pageLogin_loading: function (options) {
            return pageLogin_loading(options);
        },
        stop_pageLogin_loading: function () {
            return 	unloadPageLogin_loading();
        },
        //main function to initiate core javascript after ajax complete
        initAjax: function () {
            handleUniform(); // handles custom radio & checkboxes     
            handleiCheck(); // handles custom icheck radio and checkboxes
            handleBootstrapSwitch(); // handle bootstrap switch plugin
            handleDropdownHover(); // handles dropdown hover       
            handleScrollers(); // handles slim scrolling contents 
            handleSelect2(); // handle custom Select2 dropdowns
            handleFancybox(); // handle fancy box
            handleDropdowns(); // handle dropdowns
            handleTooltips(); // handle bootstrap tooltips
            handlePopovers(); // handles bootstrap popovers
            handleAccordions(); //handles accordions 
            handleBootstrapConfirmation(); // handle bootstrap confirmations
        },
        //init main components 
        initComponents: function () {
            this.initAjax();
        },
        //public function to remember last opened popover that needs to be closed on click
        setLastPopedPopover: function (el) {
            lastPopedPopover = el;
        },
        //public function to add callback a function which will be called on window resize
        addResizeHandler: function (func) {
            resizeHandlers.push(func);
        },
        //public functon to call _runresizeHandlers
        runResizeHandlers: function () {
            _runResizeHandlers();
        },
        // wrPECOer function to scroll(focus) to an element
        scrollTo: function (el, offeset) {
            var pos = (el && el.size() > 0) ? el.offset().top : 0;

            if (el) {
                if ($('body').hasClass('page-header-fixed')) {
                    pos = pos - $('.page-header').height();
                }
                pos = pos + (offeset ? offeset : -1 * el.height());
            }

            $('html,body').animate({
                scrollTop: pos
            }, 'slow');
        },
        initMapStyle: function(map) {
            return init_mapstyle(map);
        },
        initSlimScroll: function (el) {
            $(el).each(function () {
                if ($(this).attr("data-initialized")) {
                    return; // exit
                }

                var height;

                if ($(this).attr("data-height")) {
                    height = $(this).attr("data-height");
                } else {
                    height = $(this).css('height');
                }

                $(this).slimScroll({
                    allowPageScroll: true, // allow page scroll when the element scroll is ended
                    size: '7px',
                    color: ($(this).attr("data-handle-color") ? $(this).attr("data-handle-color") : '#bbb'),
                    wrapperClass: ($(this).attr("data-wrapper-class") ? $(this).attr("data-wrapper-class") : 'slimScrollDiv'),
                    railColor: ($(this).attr("data-rail-color") ? $(this).attr("data-rail-color") : '#eaeaea'),
                    position: isRTL ? 'left' : 'right',
                    height: height,
                    alwaysVisible: ($(this).attr("data-always-visible") == "1" ? true : false),
                    railVisible: ($(this).attr("data-rail-visible") == "1" ? true : false),
                    disableFadeOut: true
                });

                $(this).attr("data-initialized", "1");
            });
        },
        initDTSlimScroll: function (elID, color, alwaysvis, railvis, fadout) {
            var color_ = (color) ? color : '#eb7a34';
            var alwaysvis_ = (alwaysvis) ? true : false;
            var railvis_ = (railvis) ? true : false;
            var fadout_ = (fadout) ? true : false;

            var elem_wrapper = $('#'+elID+'_wrapper', document);

            var elem = $('.dataTables_scrollBody', elem_wrapper);
            if (elem.attr("data-initialized")) {
                return; // exit
            }

            var height;

            if (elem.attr("data-height")) {
                height = elem.attr("data-height");
            } else {
                height = elem.css('height');
            }

            elem.slimScroll({
                allowPageScroll: true, // allow page scroll when the element scroll is ended
                size: '5px',
                color: color_,
                wrapperClass: 'slimScrollDiv',
                // wrapperClass: (elem.attr("data-wrapper-class") ? $(this).attr("data-wrapper-class") : 'slimScrollDiv'),
                railColor: '#eaeaea',
                position: isRTL ? 'left' : 'right',
                height: height,
                alwaysVisible: alwaysvis_,
                railVisible: railvis_,
                disableFadeOut: fadout_
            });

            elem.attr("data-initialized", "1");
        },
        destroySlimScroll: function (el) {
            $(el).each(function () {
                if ($(this).attr("data-initialized") === "1") { // destroy existing instance before updating the height
                    $(this).removeAttr("data-initialized");
                    $(this).removeAttr("style");

                    var attrList = {};

                    // store the custom attribures so later we will reassign.
                    if ($(this).attr("data-handle-color")) {
                        attrList["data-handle-color"] = $(this).attr("data-handle-color");
                    }
                    if ($(this).attr("data-wrapper-class")) {
                        attrList["data-wrapper-class"] = $(this).attr("data-wrapper-class");
                    }
                    if ($(this).attr("data-rail-color")) {
                        attrList["data-rail-color"] = $(this).attr("data-rail-color");
                    }
                    if ($(this).attr("data-always-visible")) {
                        attrList["data-always-visible"] = $(this).attr("data-always-visible");
                    }
                    if ($(this).attr("data-rail-visible")) {
                        attrList["data-rail-visible"] = $(this).attr("data-rail-visible");
                    }

                    $(this).slimScroll({
                        wrapperClass: ($(this).attr("data-wrapper-class") ? $(this).attr("data-wrapper-class") : 'slimScrollDiv'),
                        destroy: true
                    });

                    var the = $(this);

                    // reassign custom attributes
                    $.each(attrList, function (key, value) {
                        the.attr(key, value);
                    });

                }
            });
        },
        // function to scroll to the top
        scrollTop: function () {
            PECO.scrollTo();
        },
        // PECO function to  block element(indicate loading)
        blockUIRipple: function(options) {
            options = $.extend(true, {}, options);
            var html = '<div class="lds-ripple"><div></div><div></div></div>';
            if (options.target) { // element blocking
                var el = $(options.target);
                if (el.height() <= ($(window).height())) {
                    options.cenrerY = true;
                }
                el.block({
                    message: html,
                    baseZ: options.zIndex ? options.zIndex : 1000,
                    centerY: options.cenrerY !== undefined ? options.cenrerY : false,
                    css: {
                        top: '10%',
                        border: '0',
                        padding: '0',
                        backgroundColor: 'none'
                    },
                    overlayCSS: {
                        backgroundColor: options.overlayColor ? options.overlayColor : '#555',
                        opacity: options.boxed ? 0.05 : 0.1,
                        cursor: 'wait'
                    }
                });
            } else { // page blocking
                $.blockUI({
                    message: html,
                    baseZ: options.zIndex ? options.zIndex : 1000,
                    css: {
                        border: '0',
                        padding: '0',
                        backgroundColor: 'none'
                    },
                    overlayCSS: {
                        backgroundColor: options.overlayColor ? options.overlayColor : '#555',
                        opacity: options.boxed ? 0.05 : 0.1,
                        cursor: 'wait'
                    }
                });
            }
        },

        blockUI: function (options) {
            options = $.extend(true, {}, options);
            var html = '';
            if (options.animate) {
                html = '<div class="loading-message ' + (options.boxed ? 'loading-message-boxed' : '') + '">' + '<div class="block-spinner-bar"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div>' + '</div>';
            } else if (options.iconOnly) {
                html = '<div class="loading-message ' + (options.boxed ? 'loading-message-boxed' : '') + '"><img src="' + this.getGlobalImgPath() + 'loading-spinner-grey.gif" align=""></div>';
            } else if (options.textOnly) {
                html = '<div class="loading-message ' + (options.boxed ? 'loading-message-boxed' : '') + '"><span>  ' + (options.message ? options.message : 'LOADING...') + '</span></div>';
            } else {
                html = '<div class="loading-message ' + (options.boxed ? 'loading-message-boxed' : '') + '"><img src="' + this.getGlobalImgPath() + 'loading-spinner-grey.gif" align=""><span>  ' + (options.message ? options.message : 'LOADING...') + '</span></div>';
            }

            if (options.target) { // element blocking
                var el = $(options.target);
                if (el.height() <= ($(window).height())) {
                    options.cenrerY = true;
                }
                el.block({
                    message: html,
                    baseZ: options.zIndex ? options.zIndex : 1000,
                    centerY: options.cenrerY !== undefined ? options.cenrerY : false,
                    css: {
                        top: '10%',
                        border: '0',
                        padding: '0',
                        backgroundColor: 'none'
                    },
                    overlayCSS: {
                        backgroundColor: options.overlayColor ? options.overlayColor : '#555',
                        opacity: options.boxed ? 0.05 : 0.1,
                        cursor: 'wait'
                    }
                });
            } else { // page blocking
                $.blockUI({
                    message: html,
                    baseZ: options.zIndex ? options.zIndex : 1000,
                    css: {
                        border: '0',
                        padding: '0',
                        backgroundColor: 'none'
                    },
                    overlayCSS: {
                        backgroundColor: options.overlayColor ? options.overlayColor : '#555',
                        opacity: options.boxed ? 0.05 : 0.1,
                        cursor: 'wait'
                    }
                });
            }
        },
        // wrPECOer function to  un-block element(finish loading)
        unblockUI: function (target) {
            if (target) {
                $(target).unblock({
                    onUnblock: function () {
                        $(target).css('position', '');
                        $(target).css('zoom', '');
                    }
                });
            } else {
                $.unblockUI();
            }
        },
        startPageLoading: function (options) {
            if (options && options.animate) {
                $('.page-spinner-bar').remove();
                $('body').append('<div class="page-spinner-bar"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div>');
            } else {
                $('.page-loading').remove();
                $('body').append('<div class="page-loading"><img src="' + this.getGlobalImgPath() + 'loading-spinner-grey.gif"/>  <span>' + (options && options.message ? options.message : 'Loading...') + '</span></div>');
            }
        },
        stopPageLoading: function () {
            $('.page-loading, .page-spinner-bar').remove();
        },
        alert: function (options) {

            options = $.extend(true, {
                container: "", // alerts parent container(by default placed after the page breadcrumbs)
                place: "append", // "append" or "prepend" in container 
                type: 'success', // alert's type
                message: "", // alert's message
                close: true, // make alert closable
                reset: true, // close all previouse alerts first
                focus: true, // auto scroll to the alert after shown
                closeInSeconds: 0, // auto close after defined seconds
                icon: "" // put icon before the message
            }, options);

            var id = PECO.getUniqueID("PECO_alert");

            var html = '<div id="' + id + '" class="PECO-alerts alert alert-' + options.type + ' fade in">' + (options.close ? '<button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>' : '') + (options.icon !== "" ? '<i class="fa-lg fa fa-' + options.icon + '"></i>  ' : '') + options.message + '</div>';

            if (options.reset) {
                $('.PECO-alerts').remove();
            }

            if (!options.container) {
                if ($('body').hasClass("page-container-bg-solid")) {
                    $('.page-title').after(html);
                } else {
                    if ($('.page-bar').size() > 0) {
                        $('.page-bar').after(html);
                    } else {
                        $('.page-breadcrumb').after(html);
                    }
                }
            } else {
                if (options.place == "append") {
                    $(options.container).append(html);
                } else {
                    $(options.container).prepend(html);
                }
            }

            if (options.focus) {
                PECO.scrollTo($('#' + id));
            }

            if (options.closeInSeconds > 0) {
                setTimeout(function () {
                    $('#' + id).remove();
                }, options.closeInSeconds * 1000);
            }

            return id;
        },
        // initializes uniform elements
        initUniform: function (els) {
            if (els) {
                $(els).each(function () {
                    if ($(this).parents(".checker").size() === 0) {
                        $(this).show();
                        $(this).uniform();
                    }
                });
            } else {
                handleUniform();
            }
        },
        // function to update/sync jquery uniform checkbox & radios
        updateUniform: function (els) {
            $.uniform.update(els); // update the uniform checkbox & radios UI after the actual input control state changed
        },
        //public function to initialize the fancybox plugin
        initFancybox: function () {
            handleFancybox();
        },
        //public helper function to get actual input value(used in IE9 and IE8 due to placeholder attribute not supported)
        getActualVal: function (el) {
            el = $(el);
            if (el.val() === el.attr("placeholder")) {
                return "";
            }
            return el.val();
        },
        //public function to get a paremeter by name from URL
        getURLParameter: function (paramName) {
            var searchString = window.location.search.substring(1),
                i, val, params = searchString.split("&");

            for (i = 0; i < params.length; i++) {
                val = params[i].split("=");
                if (val[0] == paramName) {
                    return unescape(val[1]);
                }
            }
            return null;
        },
        // check for device touch support
        isTouchDevice: function () {
            try {
                document.createEvent("TouchEvent");
                return true;
            } catch (e) {
                return false;
            }
        },
        // To get the correct viewport width based on  http://andylangton.co.uk/articles/javascript/get-viewport-size-javascript/
        getViewPort: function () {
            var e = window,
                a = 'inner';
            if (!('innerWidth' in window)) {
                a = 'client';
                e = document.documentElement || document.body;
            }

            return {
                width: e[a + 'Width'],
                height: e[a + 'Height']
            };
        },
        getUniqueID: function (prefix) {
            return 'prefix_' + Math.floor(Math.random() * (new Date()).getTime());
        },
        // check IE8 mode
        isIE8: function () {
            return isIE8;
        },
        // check IE9 mode
        isIE9: function () {
            return isIE9;
        },
        //check RTL mode
        isRTL: function () {
            return isRTL;
        },
        // check IE8 mode
        isAngularJsApp: function () {
            return (typeof angular == 'undefined') ? false : true;
        },
        getAssetsPath: function () {
            return assetsPath;
        },
        setAssetsPath: function (path) {
            assetsPath = path;
        },
        setGlobalImgPath: function (path) {
            globalImgPath = path;
        },
        getGlobalImgPath: function () {
            return assetsPath + globalImgPath;
        },
        setGlobalPluginsPath: function (path) {
            globalPluginsPath = path;
        },
        getGlobalPluginsPath: function () {
            return assetsPath + globalPluginsPath;
        },
        getGlobalCssPath: function () {
            return assetsPath + globalCssPath;
        },
        // get layout color code by color name
        getBrandColor: function (name) {
            if (brandColors[name]) {
                return brandColors[name];
            } else {
                return '';
            }
        },
        usersSelectTagging: function (elem, detailed, initdata) {
            var initdata = (initdata) ? initdata : false;
            elem.select2({
                tags: false,
                multiple: false,
                minimumInputLength: 3,
                //tags: [],
                ajax: {
                    url: base_url + "query/select2getusers",
                    dataType: 'json',
                    type: "post",
                    quietMillis: 50,
                    data: function (term) {
                        return {
                            term: term,
                        };
                    },
                    results: function (data) {
                        return {
                            results: $.map(data, function (item) {
                                return {
                                    text: item.text,
                                    birthday: item.birthday,
                                    gender: item.gender,
                                    address: item.address,
                                    pic: item.pic,
                                    id: item.id,
                                    details: detailed,
                                };

                            })

                        };

                    }
                },
                initSelection: function (element, callback) {
                    if (initdata) {
                        callback(initdata);
                    }

                },
                escapeMarkup: function (markup) {
                    return markup;
                }, // let our custom formatter work
                formatResult: formatData, // omitted for brevity, see the source of this page
                formatSelection: formatDataSelection, // omitted for brevity, see the source of this page

            }).select2("val", []);
        },
        customerSelectTagging: function (elem, placeholder, initval) {
            var placeholder = (placeholder) ? placeholder : 'Select customers..';
            var initdata = (initdata) ? initdata : '';
            if($.fn.select2) {
                elem.select2({
                    tags: false,
                    multiple: false,
                    minimumInputLength: 3,
                    placeholder: placeholder,
                    //tags: [],
                    ajax: {
                        url: base_url + "query/select2customers",
                        dataType: 'json',
                        type: "post",
                        quietMillis: 50,
                        data: function (term) {
                            return {
                                term: term,
                            };
                        },
                        results: function (data) {
                            return {
                                results: $.map(data, function (item) {
                                    return {
                                        text: item.text,
                                        name: item.name,
                                        addr: item.addr,
                                        pics: item.pics,
                                        id: item.id,
                                    };

                                })

                            };

                        }
                    },

                    initSelection: function (element, callback) {
                        if (initdata) {
                            callback(initdata);
                        }

                    },
                    escapeMarkup: function (markup) {
                        return markup;
                    }, // let our custom formatter work
                    formatResult: formatDataCustomers, // omitted for brevity, see the source of this page
                    formatSelection: formatDataSelection, // omitted for brevity, see the source of this page

                });
                select2_niceScroller();
            }
        },



        servicesTagging: function (elem, placeholder, type) {
            var placeholder = (placeholder) ? placeholder : 'Select customers..';
            var initdata = (initdata) ? initdata : '';

            elem.select2({
                tags: false,
                multiple: false,
                minimumInputLength: 3,
                placeholder: placeholder,
                //tags: [],
                ajax: {
                    url: base_url + "query/searchaccountservices",
                    dataType: 'json',
                    type: "post",
                    quietMillis: 50,
                    data: function (term) {
                        return {
                            term: term,
                            type: type
                        };
                    },
                    results: function (data) {
                        return {
                            results: $.map(data, function (item) {
                                return {
                                    text: item.text,
                                    address: item.address,
                                    id: item.id,
                                };

                            })

                        };

                    }
                },

                createSearchChoice: function (term, data) {
                    if ($(data).filter(function () {
                        return this.text.localeCompare(term) === 0;
                    }).length === 0) {
                        return {id: term, text: term};
                    }
                },
                escapeMarkup: function (markup) {
                    return markup;
                }, // let our custom formatter work
                formatResult: formatDataCustomers, // omitted for brevity, see the source of this page
                formatSelection: formatDataSelection, // omitted for brevity, see the source of this page
            });
            select2_niceScroller();
        },

        select2BasicId: function (elem, url, id, placeholder, full, allowall, selectedval) {

            if(!$.fn.select2) {
                return false;
            }

            // Initialize immediately to prevent "Uncaught query function not defined" for input elements
            // But skip 'data' for select elements as it's not allowed
            var initOptions = {
                allowClear: true,
                placeholder: placeholder,
                width: 'resolve'
            };
            if (!elem.is('select')) {
                initOptions.data = [];
            }
            elem.select2(initOptions);

            var selection = (full) ? formatDataSelectionFull : formatDataSelectionBasic;
            var allow_all = (allowall) ? true : false;
            var selected_val = (selectedval) ? selectedval : false;
            $.ajax({
                url: base_url + url,
                data: {'id': id},
                dataType: 'json',
                type: "post"
            }).done(function (d) {
                if(d.num > 0) {

                    var dataArr = d.list;
                    if (allow_all == true) {
                        var allow_all_arr = {'id': '0', 'text': 'All - records'};
                        dataArr.unshift(allow_all_arr);
                    }
                    var successOptions = {
                        allowClear: true,
                        placeholder: placeholder,
                        formatResult: formatDataListBasic, // omitted for brevity, see the source of this page
                        formatSelection: selection, // omitted for brevity, see the source of this page
                        width: 'resolve'
                    };
                    if (!elem.is('select')) {
                        successOptions.data = dataArr;
                    }
                    elem.select2(successOptions);

                    if (selected_val) {
                        elem.val(selected_val).trigger('change.select2');
                    }
                    PECO.select2_slimscroller();
                }else{
                    elem.val("").select2('destroy').attr('readonly', true);
                    var failOptions = {
                        allowClear: true,
                        placeholder: 'No data found!'
                    };
                    if (!elem.is('select')) {
                        failOptions.data = [];
                    }
                    elem.select2(failOptions);
                }
            }).fail(function() {
                var errorOptions = {
                    allowClear: true,
                    placeholder: 'PHP Error'
                };
                if (!elem.is('select')) {
                    errorOptions.data = [];
                }
                elem.select2(errorOptions);
            });
        },

        select2Basic: function (elem, url, placeholder, full, allowall, selectedval, labeled, bigdrop , data) {
            if(!$.fn.select2) {
                return false;
            }

            var bigdrop_ = (bigdrop) ? 'select2-bigdrop' : '';

            // Initialize immediately to prevent "Uncaught query function not defined" for input elements
            // But skip 'data' for select elements as it's not allowed
            var initOptions = {
                allowClear: true,
                placeholder: placeholder,
                width: '100%',
                dropdownCssClass : bigdrop_
            };
            if (!elem.is('select')) {
                initOptions.data = [];
            }
            elem.select2(initOptions);

            if(labeled) {
                var selection = (full) ? formatDataSelectionFullLabel : formatDataSelectionBasicLabel;
            }else{
                var selection = (full) ? formatDataSelectionFull : formatDataSelectionBasic;
            }
            var allow_all = (allowall) ? true : false;
            var options = (labeled) ? formatDataListBasicLabel : formatDataListBasic;
            if(elem.val() != '') {
                var selected_val = elem.val();
            }else{
                var selected_val = (selectedval) ? selectedval : false;
            }
            var data =  (data) ? data : false;

            $.ajax({
                url: base_url + url,
                dataType: 'json',
                type: "POST",
                data:{"data":data}
            }).done(function (d) {
                if(typeof d.list != 'undefined' && d.list.length > 0) {
                    var dataArr = d.list;
                    if (allow_all == true) {
                        var allow_all_arr = {'id': '0', 'text': 'All - records'};
                        dataArr.unshift(allow_all_arr);
                    }
                    if  ($.fn.select2) {
                        var successOptions = {
                            allowClear: true,
                            placeholder: placeholder,
                            formatResult: options, // omitted for brevity, see the source of this page
                            formatSelection: selection, // omitted for brevity, see the source of this page
                            width: '100%', // 100% or resolve
                            dropdownCssClass : bigdrop_,

                        };
                        if (!elem.is('select')) {
                            successOptions.data = dataArr;
                        }
                        elem.select2(successOptions);

                        if (selected_val) {
                            elem.val(selected_val).trigger('change.select2');
                        }
                        PECO.select2_slimscroller();
                    }
                }else{
                    var failOptions = {
                        allowClear: true,
                        placeholder: 'No data found!',
                        width: '100%'
                    };
                    if (!elem.is('select')) {
                        failOptions.data = [];
                    }
                    elem.select2(failOptions);
                }
            }).fail(function() {
                var errorOptions = {
                    allowClear: true,
                    placeholder: 'PHP Error',
                    width: '100%'
                };
                if (!elem.is('select')) {
                    errorOptions.data = [];
                }
                elem.select2(errorOptions);
            });

        },

        select2Types: function (elem, codes, placeholder, full, allowall, selectedval, labeled, bigdrop) {

            if(!$.fn.select2) {
                return false;
            }

            var bigdrop_ = (bigdrop) ? 'select2-bigdrop' : '';

            // Initialize immediately to prevent "Uncaught query function not defined" for input elements
            // But skip 'data' for select elements as it's not allowed
            var initOptions = {
                allowClear: true,
                placeholder: placeholder,
                width: 'resolve',
                dropdownCssClass : bigdrop_
            };
            if (!elem.is('select')) {
                initOptions.data = [];
            }
            elem.select2(initOptions);

            if(labeled) {
                var selection = (full) ? formatDataSelectionFullLabel : formatDataSelectionBasicLabel;
            }else{
                var selection = (full) ? formatDataSelectionFull : formatDataSelectionBasic;
            }

            var allow_all = (allowall) ? true : false;
            var options = (labeled) ? formatDataListBasicLabel : formatDataListBasic;
            if(elem.val() != '') {
                var selected_val = elem.val();
            }else{
                var selected_val = (selectedval) ? selectedval : false;
            }
            var data =  (data) ? data : false;

            $.ajax({
                url: base_url + 'systems/select2types',
                dataType: 'json',
                type: "POST",
                data:{'codes': codes}
            }).done(function (d) {
                if(d) {

                    var dataArr = d.list;
                    if (allow_all == true) {
                        var allow_all_arr = {'id': '0', 'text': 'All - records'};
                        dataArr.unshift(allow_all_arr);
                    }
                    if  ($.fn.select2) {
                        var successOptions = {
                            allowClear: true,
                            placeholder: placeholder,
                            formatResult: options, // omitted for brevity, see the source of this page
                            formatSelection: selection, // omitted for brevity, see the source of this page
                            width: 'resolve', // 100% or resolve
                            dropdownCssClass : bigdrop_,

                        };
                        if (!elem.is('select')) {
                            successOptions.data = dataArr;
                        }
                        elem.select2(successOptions);

                        if (selected_val) {
                            elem.val(selected_val).trigger('change.select2');
                        }
                        PECO.select2_slimscroller();
                    }
                }else{
                    var failOptions = {
                        allowClear: true,
                        placeholder: 'No data found!',
                        width: 'resolve'
                    };
                    if (!elem.is('select')) {
                        failOptions.data = [];
                    }
                    elem.select2(failOptions);
                }
            }).fail(function() {
                var errorOptions = {
                    allowClear: true,
                    placeholder: 'PHP Error',
                    width: 'resolve'
                };
                if (!elem.is('select')) {
                    errorOptions.data = [];
                }
                elem.select2(errorOptions);
            });

        },


        select2BasicMult: function (elem, url, initdata, minimized, allowadd) {
            var formatResult_ = (minimized) ? formatStateEditable : formatStateDefault;
            var formatSelection_ = (minimized) ? formatDataSelectionEditable : formatDataSelectionDefault;
            elem.select2({
                multiple: true,
                minimumInputLength: 1,
                //tags: [],
                ajax: {
                    url: base_url + url,
                    dataType: 'json',
                    type: "POST",
                    quietMillis: 50,
                    minimumInputLength: 3,
                    data: function (term) {
                        return {
                            term: term,
                        };
                    },
                    results: function (data) {
                        return {
                            results: $.map(data.list, function (item) {
                                return {
                                    id: item.id,
                                    text: item.text
                                };
                            })
                        };
                    }
                },

                initSelection: function (element, callback) {
                    if (initdata) {
                        callback(initdata);
                    }
                },

                createSearchChoice: function (term, data) {
                    if(allowadd == true) {
                        if ($(data.list).filter(function () {
                            return this.text.localeCompare(term) === 0;
                        }).length === 0) {
                            return {id: term, text: term + ' - <i class="fa fa-plus text-primary"></i> Add'};
                        }
                    }
                },

                escapeMarkup: function (markup) {
                    return markup;
                }, // let our custom formatter work

                formatResult: formatResult_, // omitted for brevity, see the source of this page
                formatSelection: formatSelection_, // omitted for brevity, see the source of this page
            });
        },

        /*
        select2BasicMult: function (elem, url, initdata) {
            elem.select2({
                tags: false,
                multiple: true,
                minimumInputLength: 1,
                //tags: [],
                ajax: {
                    url: base_url + url,
                    dataType: 'json',
                    type: "post",
                    quietMillis: 50,
                    data: function (term) {
                        return {
                            term: term,
                        };
                    },
                    results: function (data) {
                        return {
                            results: $.map(data.list, function (item) {
                                return {
                                    id: item.id,
                                    text: item.text
                                };
                            })
                        };
                    }
                },
                initSelection: function (element, callback) {
                    if (initdata) {
                        callback(initdata);
                    }
                },
                escapeMarkup: function (markup) {
                    return markup;
                }, // let our custom formatter work
                //formatResult: formatDataCorp, // omitted for brevity, see the source of this page
                //formatSelection: formatDataSelectionCorp, // omitted for brevity, see the source of this page
            }).select2("val", []);
        },
        */

        corpSelectTagging: function (elem, detailed) {
            elem.select2({
                tags: false,
                multiple: false,
                minimumInputLength: 3,
                //tags: [],
                ajax: {
                    url: base_url + "query/getcorpinfo/",
                    dataType: 'json',
                    type: "POST",
                    quietMillis: 50,
                    data: function (term) {
                        return {
                            term: term,
                        };
                    },
                    results: function (data) {
                        return {
                            results: $.map(data, function (item) {
                                return {
                                    text: item.text,
                                    rep: item.rep,
                                    address: item.address,
                                    pic: item.pic,
                                    id: item.id,
                                    details: detailed,
                                };

                            })

                        };

                    }
                },
                createSearchChoice: function (term, data) {
                    if ($(data).filter(function () {
                        return this.text.localeCompare(term) === 0;
                    }).length === 0) {
                        return {id: term, text: term};
                    }
                },
                escapeMarkup: function (markup) {
                    return markup;
                }, // let our custom formatter work
                formatResult: formatDataCorp, // omitted for brevity, see the source of this page
                formatSelection: formatDataSelectionCorp, // omitted for brevity, see the source of this page
            });
        },

        employeeSelectTagging: function (elem, detailed, initdata) {
            var initdata = (initdata) ? initdata : false;
            if($.fn.select2) {
                elem.select2({
                    tags: true,
                    multiple: true,
                    minimumInputLength: 3,
                    //tags: [],
                    ajax: {
                        url: base_url + "hris/getpersoninfo/",
                        dataType: 'json',
                        type: "POST",
                        quietMillis: 50,
                        data: function (term) {
                            return {
                                term: term,
                            };
                        },
                        results: function (data) {
                            return {
                                results: $.map(data, function (item) {
                                    return {
                                        text: item.text,
                                        birthday: item.birthday,
                                        gender: item.gender,
                                        address: item.address,
                                        pic: item.pic,
                                        id: item.id,
                                        details: detailed,
                                    };

                                })

                            };

                        }
                    },
                    initSelection: function (element, callback) {
                        if (initdata) {
                            callback(initdata);
                        }
                    },
                    /*
                    createSearchChoice: function (term, data) {
                        if ($(data).filter(function () {
                                return this.text.localeCompare(term) === 0;
                            }).length === 0) {
                            return {id: term, text: term};
                        }
                    },
                    escapeMarkup: function (markup) {
                        return markup;
                    }, // let our custom formatter work
                    */
                    formatResult: formatData, // omitted for brevity, see the source of this page
                    formatSelection: formatDataSelection, // omitted for brevity, see the source of this page

                }).select2("val", []);

                PECO.select2_slimscroller();
            }
        },

        personSelectTagging: function (elem, detailed, initdata) {
            var initdata = (initdata) ? initdata : false;
            elem.select2({
                tags: false,
                multiple: false,
                minimumInputLength: 3,
                //tags: [],
                ajax: {
                    url: base_url + "query/getpersoninfo/",
                    dataType: 'json',
                    type: "POST",
                    quietMillis: 50,
                    data: function (term) {
                        return {
                            term: term,
                        };
                    },
                    results: function (data) {
                        return {
                            results: $.map(data, function (item) {
                                return {
                                    text: item.text,
                                    birthday: item.birthday,
                                    gender: item.gender,
                                    address: item.address,
                                    pic: item.pic,
                                    id: item.id,
                                    details: detailed,
                                };

                            })

                        };

                    }
                },
                initSelection: function (element, callback) {
                    if (initdata) {
                        callback(initdata);
                    }

                },
                createSearchChoice: function (term, data) {
                    if ($(data).filter(function () {
                        return this.text.localeCompare(term) === 0;
                    }).length === 0) {
                        return {id: term, text: term};
                    }
                },
                escapeMarkup: function (markup) {
                    return markup;
                }, // let our custom formatter work
                formatResult: formatData, // omitted for brevity, see the source of this page
                formatSelection: formatDataSelection, // omitted for brevity, see the source of this page

            }).select2("val", []);
            select2_niceScroller();
        },
        meterSelectTagging: function (elem, detailed, initdata) {
            var initdata = (initdata) ? initdata : false;
            elem.select2({
                tags: false,
                multiple: false,
                minimumInputLength: 3,
                //tags: [],
                ajax: {
                    url: base_url + "query/searchmeter",
                    dataType: 'json',
                    type: "POST",
                    quietMillis: 50,
                    data: function (term) {
                        return {
                            term: term,
                        };
                    },
                    results: function (data) {
                        return {
                            results: $.map(data, function (item) {
                                return {
                                    text: item.text,
                                    id: item.id,
                                    owner: item.owner,
                                    addr: item.addr,
                                };

                            })

                        };

                    }
                },
                initSelection: function (element, callback) {
                    if (initdata) {
                        callback(initdata);
                    }

                },
                escapeMarkup: function (markup) {
                    return markup;
                }, // let our custom formatter work
                formatResult: formatMeterData, // omitted for brevity, see the source of this page
                formatSelection: formatDataSelectionMeter, // omitted for brevity, see the source of this page

            }).select2("val", []);
            select2_niceScroller();
        },

        getResponsiveBreakpoint: function (size) {
            // bootstrap responsive breakpoints
            var sizes = {
                'xs': 480, // extra small
                'sm': 768, // small
                'md': 992, // medium
                'lg': 1200     // large
            };

            return sizes[size] ? sizes[size] : 0;
        },
        base_url: function () {
            return base_url;
        },
        google_api: function() {
            return $('[data-toggle=api]').attr('data-val');
        },
        initAlerts: function (msg, title, func, timeout, box, shake, number) {
            return init_alerts_toastr(msg, title, func, timeout, box, shake, number);
        },
        phpError: function () {
            init_alerts_toastr('There is an error in PHP server script!', 'Error: PHP / Ajax', 'error');
        },
        DTphpError: function (tbl, msg) {
            init_alerts_dt(tbl, msg);
        },
        DTDefault: function(tbl, msg) {
            init_dt_default(tbl, msg);
        },
        DTAlert: function(tbl, msg, func) {
            init_dt_alert(tbl, msg, func);
        },
        DTphpLoading: function (tbl, msg) {
            init_loading_dt(tbl, msg);
        },
        DTEmptyMessage: function (msg) {
            var msg = (msg) ? '<h4 style="margin: 0px 10px;"><i class="fa fa-warning font-yellow"></i> ' + msg + '</h4>': '<h4 style="margin: 0px 10px;"><i class="fa fa-warning font-blue"></i> No data available in table!</h4>';
            return {'emptyTable': msg};
        },
        checkIE8lower: function () {
            return isIE8orlower();
        },
        initPlaySound: function (filename) {
            return playSound(filename);
        },
        ajaxContentLoad: function (object, container) {
            $.ajax({
                url: object.attr('href').replace('#', ''),
                type: 'post',
                beforeSend: function () {
                    $('.page-breadcrumb li#ajax-breadcrumb').remove();
                    container.html('<h4><i class="fa fa-circle-o-notch fa-spin fa-fw"></i> Loading content, please wait...</h4>');
                    $('.page-breadcrumb').append('<li id="ajax-breadcrumb"><i class="fa fa-angle-right"></i> <a href="javascript:;"><b class="">Loading...</b></a></li>');
                }
            }).done(function (data) {
                container.html(data);
                $('.page-breadcrumb li#ajax-breadcrumb').remove();
                $('.page-breadcrumb').append('<li id="ajax-breadcrumb"><i class="fa fa-angle-right"></i> <a href="javascript:;"><b class="">' + object.text() + '</b></a></li>');
                PECO.initNicescroll();
            }).fail(function () {
                container.html('<h4 class="text-danger"><i class="fa fa-times fa-fw"></i> Fail to load content</h4>');
                PECO.initAlerts('Fail to load HTML content from <strong>' + object.attr('href').replace('#', '') + '</strong>', 'ERROR URL', 'error');
            });
        },
        confirmEditable: function (callback, btngroup) {
            ajax_confirm_editable (callback, btngroup);
        },
        confirmAlerts: function (url, id, title) {
            return ajax_confirm_alerts(url, id, title);
        },
        confirmApproval: function (url, data, msgtitle, obj) {
            return btn_confirm_approval(url, data, msgtitle, obj);
        },
        ajaxConfirmForm: function (form, msgtitle) {
            return ajax_confirm_form(form, msgtitle);
        },
        ajaxConfirmStat: function (btn) {
            return ajax_stats(btn);
        },
        initNicescroll: function () {
            return body_niceScroller();
        },
        initElScroller: function (el) {
            return el_niceScroller(el);
        },
        initDTNicescroller: function () {
            return dt_niceScroller();
        },
        dataTableScroller: function () {
            return datatable_scroller();
        },
        select2_scroller: function () {
            return select2_niceScroller();
        },
        select2_scrollertbl: function (tbl) {
            return select2_niceScroller_tbl(tbl);
        },
        select2_slimscroller: function() {
            return dt_select2SlimScroller();
        },
        datatableLoading: function (message, htmlclass) {
            return '<span class="text-' + htmlclass + '"><i class="fa fa-spinner fa-spinfa-pulse fa-fw"></i> ' + message + '</span>';
        },
        getAmsChartPlugins: function () {
            amsChartHandle();
        },
        getSelect2Plugins: function () {
            select2Handle();
        },
        getTypeHeadPlugins: function () {
            typeHeadHandle();
        },
        getNumberFormatPlugin: function () {
            init_number_format();
        },
        getFileInputPlugin: function () {
            init_fileinput();
        },
        getDigitalClock: function () {
            init_digital_clock();
        },
        getiCheckPlugin: function () {
            init_icheck();
        },
        getInputMaxlength: function () {
            init_input_maxlength();
        },
        getSweetAlert: function () {
            init_sweetbootstrap_alert();
        },
        getDatePickerPlugins: function () {
            init_datepicker_plugins();
        },
        getPulsate: function () {
            init_pulsate();
        },
        getSelectPlugins: function () {
            tblSelectHandle();
        },
        getDTResizableColumn: function () {
            dtResizableColumn();
        },
        getDataTablePlugin: function () {
            dataTablePluginHandle();
        },
        getGoogleKey: function () {
            handleGoogleKey();
        },
        getHighlightsPlugin: function () {
            tblHighlightsHandle();
        },
        getEditablePlugins: function () {
            handler_editable_plugins();
        },
        ajaxFormSubmit: function (frm, msgtitle) {
            return ajax_submit(frm, msgtitle);
        },
        ajaxBtnSubmit: function (btn) {
            return ajax_btn_submit(btn);
        },
        numberString: function(str) {
            return Number(str.replace(/[^0-9\.-]+/g,""));
        },

        pulsateTarget: function (elem, reach, repeat, speed, glow, color) {
            elem.pulsate({
                color: color, // STR
                reach: reach, // INT
                repeat: repeat, // INT
                speed: speed, // INT
                glow: glow      // BOOLEAN
            }).find('a').focus();
        },
        formatDataSelection: function (data) {

            if (!data.id) {
                return data.text;
            }
            var $data = $('<span><i class="fa fa-check text-success"></i> ' + data.text.split('-', 1) + '</span>');

            return $data;
        },
        formatState: function (data) {
            var text_arr = data.text.split('-');
            if (!data.id) {
                return data.text;
            }
            var row_stat = '';
            if (text_arr[2]) {
                row_stat = '<span class="label label-danger pull-right"> ' + text_arr[2] + ' </span>';
            }
            var $data = $(
                '<p><b>' + text_arr[0] + '</b> - ' + text_arr[1] + row_stat + ' </p>'
            );
            return $data;
        },

        formatStateEditable: function (data) {
            var text_arr = data.text.split('-');
            if (!data.id) {
                return data.text;
            }
            return '<p style="display: inline-block; width: 100%; margin:3px 0px; position: relative; height: auto; word-wrap: normal;"><b style="display: inline-block; width: 25%; vertical-align: top; color: #ef582d">' + text_arr[0] + '</b><span style="display: inline-block; width: 75%; padding-left: 10px; border-left: 1px solid #ccc;">' + text_arr[1]+'</span></p>';

        },

        formatDataSelectionEditable: function (data) {
            if (!data.id) {
                return data.text;
            }

            var text_arr = data.text.split('-');
            return text_arr[1];
        },

        formatDataSelectionEditableFull: function (data) {
            if (!data.id) {
                return data.text;
            }
            var text_arr = data.text.split('-');
            return '<i class="fa fa-check font-green-haze"></i> <span class="badge badge-info" style="float: right; position: absolute; right: 40px; top: 5px;">' +text_arr[0] + '</span>' + text_arr[1];
        },


        formatDataListNationality: function (data) {
            if (data.loading)
                return data.name;
            var markup;
            var text = data.text.split(' - ');
            if(data.icon!='') {
                markup = '<img src="' + PECO.base_url() + 'assets/global/img/flags/' + data.icon + '.png" /> ' + '<b class="text-info">' + text[0] + '</b> - ' + text[1];
            }else{
                markup = '<b class="text-info">' + text[0] + '</b> - ' + text[1];
            }
            return markup;
        },

        formatDataListCountry: function (data) {
            if (data.loading)
                return data.name;
            var markup;
            if(data.icon!='') {
                markup = '<img src="' + PECO.base_url() + 'assets/global/img/flags/' + data.icon + '.png" /> ' + '<b class="">' + data.text + '</b>';
            }else{
                markup = '<b class="text-info">' + data.text + '</b>';
            }
            return markup;
        },

        formatDataSelectionCountry: function (data) {
            return data.text.split(' - ', 1);
        },

        initAssetSelect: function (query, dataid, callback) {
            var callback = (callback) ? callback : false;
            var el = $(".asset-details").find(".portlet-body");
            $.ajax({
                url: PECO.base_url() + 'search/loadasset',
                dataType: 'json',
                data: {'dataid': dataid, 'query': query},
                type: 'post',
                bofereSend: function () {
                    PECO.blockUI({
                        target: el,
                        animate: true,
                        overlayColor: false
                    });
                }
            }).done(function (d) {
                if (d) {
                    console.log(dataid);
                    console.log(d);
                    selections = {'id': d.id, 'text': d.text};
                    $('#assetcode').html(d.assetcode);
                    $('#brand').html(d.brand);
                    $('#desc').html(d.desc);
                    $('#amp').html(d.amps);
                    $('#volts').html(d.volts);
                    $('#desc').html(d.desc);
                    if (callback) {
                        callback(selections);
                    }
                } else {
                    $('#assetcode').html('N/A');
                    $('#brand').html('N/A');
                    $('#desc').html('N/A');
                    $('#amp').html('N/A');
                    $('#volts').html('N/A');
                    $('#desc').html('N/A');
                }
                PECO.unblockUI(el);
            }).fail(function () {
                PECO.phpError();
                PECO.unblockUI(el);
            });
        },
        meterPopUp: function () {
            $('body').find('#map-pop-up').remove();
            $('body').prepend('<div id="map-pop-up" style="display: none; width: 800px; "><h3><span id="map_zoom_gdlb"></span><span id="map_zoom_custname" class="pull-right"></span></h3><div style="width: 800px; height: 360px" id="map_zoom"></div><span id="directions-panel"></span></div>');
            $('body').find('#product-pop-up').remove();
            $('body').prepend('<div id="product-pop-up" style="display: none; width: 850px; ">' +
                '<div class="product-page product-pop-up">' +
                '<div class="row">' +
                '<div class="col-md-6 col-sm-6 col-xs-3">' +
                '<div class="product-other-images"></div>' +
                '<div class="product-main-image" style="min-height: 450px; display: inline-block;"><img src="" alt="" class="img-responsive"></div>' +
                '</div>' +
                '<div class="col-md-6 col-sm-6 col-xs-9">' +
                '<h2 id="ownername"></h2>' +
                '<div class="price-availability-block clearfix">' +
                '<div class="price">' +
                '<strong id="mtrno"></strong>' +
                '<em></em>' +
                '</div>' +
                '<div class="availability" >' +
                '</div>' +
                '</div>' +
                ' <div class="description">' +
                '<h4 class="text-info">Account Meter Details</h4>' +
                '<ul class="list-group summary">' +
                '<li class="list-group-item">Asset Serial: <span class="label label-default pull-right" id="assetserial"></span></li>' +
                '<li class="list-group-item">Asset Desc.: <span class="label label-default pull-right" id="assetdesc"></span></li>' +
                '<li class="list-group-item">Asset Acquired.: <span class="label label-default pull-right" id="assetacq"></span></li>' +
                '</ul>' +
                '<h4 class="text-info">Picture Details</h4>' +
                '<ul class="list-group summary">' +
                '<li class="list-group-item">Picture taken: <span class="label label-default pull-right" id="picdate"></span></li>' +
                '<li class="list-group-item">Taken by: <span class="label label-default pull-right" id="picby"></span></li>' +
                '</ul>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '</div>');
        },
        animatePopUpFancy: function (this_) {
            this_.width = ($('.fancybox-iframe').contents().find('html').width()) + 50;
            this_.height = ($('.fancybox-iframe').contents().find('html').height()) + 50;
        },
        pecoRepPrint: function (reptitle, content, header = true) {
            // Open a new window for the printable table
            var win = window.open('', '');
            var head = '<title>' + reptitle + '</title>';
            var topval = '0px';
            var header_html = '';
            if(header == true) {
                topval = '80px';
                header_html = '<img  style="display: inline-block; height: 80px; float: left; z-index: 2 !important; position: absolute; left: 0px;" src="' + PECO.base_url() + 'assets/global/img/PECO_LEFT_HEAD.png" /><img style="display: inline-block; height: 80px; width: 100%; position: absolute; top 0px; right: 0px; z-index: 0;" src="' + PECO.base_url() + 'assets/global/img/PECO_REP_HEAD.png" />';
            }
            win.document.title = reptitle;
            win.document.body.innerHTML = '' +
                '<head>' +
                '<title>'+reptitle+'</title>'+
                '<link href="' + PECO.base_url() + 'assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>' +
                '<link href="' + PECO.base_url() + 'assets/global/css/components.css" rel="stylesheet" type="text/css"/>' +
                '<link href="' + PECO.base_url() + 'assets/global/css/plugins.css" rel="stylesheet" type="text/css"/>' +
                '<link href="' + PECO.base_url() + 'assets/admin/layout/css/layout.css" rel="stylesheet" type="text/css"/>' +
                '<link href="' + PECO.base_url() + 'assets/admin/layout/css/themes/default.css" rel="stylesheet" type="text/css"/>' +
                '<link href="' + PECO.base_url() + 'assets/admin/layout/css/custom.css" rel="stylesheet" type="text/css"/>' +
                '<style>body{margin: 0px 0px !important; font-family: arial; background: #fff;}</style>' +
                '</head>' +
                '<div style="position: absolute; top: '+topval+'; left: 0px; width: 100%;">' + content + '</div>';
            setTimeout(function () {
                //  win.print(); // blocking - so close will not
                //  win.close(); // execute until this is done
            }, 250);
        },
        pecoBill: function (reptitle, content) {
            // Open a new window for the printable table
            var reptitle = 'BILL FORMS';
            var win = window.open('', reptitle);
            win.document.body.innerHTML =
                '<head>' +
                '<link href="' + PECO.base_url() + 'assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>' +
                '<link href="' + PECO.base_url() + 'assets/global/css/components.css" rel="stylesheet" type="text/css"/>' +
                '<link href="' + PECO.base_url() + 'assets/global/css/plugins.css" rel="stylesheet" type="text/css"/>' +
                '<link href="' + PECO.base_url() + 'assets/admin/layout/css/layout.css" rel="stylesheet" type="text/css"/>' +
                '<link href="' + PECO.base_url() + 'assets/admin/layout/css/themes/default.css" rel="stylesheet" type="text/css"/>' +
                '<link href="' + PECO.base_url() + 'assets/admin/layout/css/custom.css" rel="stylesheet" type="text/css"/>' +
                '<link href="' + PECO.base_url() + 'assets/pages/billing/billform.css" rel="stylesheet" type="text/css"/>' +
                '<style>body{margin: 0px 0px !important; margin-top: 100px; font-family: arial; background: #fff;}</style>' +
                '</head><body>' + content + '</body>';

            setTimeout(function () {
                win.print(); // blocking - so close will not
                //win.close(); // execute until this is done
            }, 250);
        },
        printPayslip: function (html) {
            var win = window.open('', 'Print Payslip');
            win.document.body.innerHTML =
                '<head>' +
                '<link href="' + PECO.base_url() + 'assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>' +
                '<link href="' + PECO.base_url() + 'assets/global/css/components.css" rel="stylesheet" type="text/css"/>' +
                '<link href="' + PECO.base_url() + 'assets/global/css/plugins.css" rel="stylesheet" type="text/css"/>' +
                '<link href="' + PECO.base_url() + 'assets/admin/layout/css/layout.css" rel="stylesheet" type="text/css"/>' +
                '<link href="' + PECO.base_url() + 'assets/admin/layout/css/themes/default.css" rel="stylesheet" type="text/css"/>' +
                '<link href="' + PECO.base_url() + 'assets/admin/layout/css/custom.css" rel="stylesheet" type="text/css"/>' +
                '<link href="' + PECO.base_url() + 'assets/pages/billing/billform.css" rel="stylesheet" type="text/css"/>' +
                '<style>body{margin: 0px 0px !important; margin-top: 100px; font-family: arial; background: #fff;}</style>' +
                '</head><body>' + html + '</body>';

            setTimeout(function () {
                win.print(); // blocking - so close will not
                //win.close(); // execute until this is done
            }, 250);
        },
        row_validation: function (input) {
            var value = input.val();
            var tr = input.closest('tr');
            var stat = tr.find('#readstat');
            if (stat.val() == '' && value != '') {
                stat.closest('td').addClass('danger');
                tr.addClass('has-success');
            } else {
                if ((value != '' || value > 0) && stat.val() != '') {
                    tr.addClass('success');
                } else {
                    tr.removeClass('success');
                }
                stat.closest('td').removeClass('danger');
                if (stat.val() != '' && value != '') {
                    tr.addClass('has-success');
                } else {
                    tr.removeClass('has-success');
                }
            }
        },
        crmstat: function (div, gkey) {
            init_crm(div, gkey);
        },
        mapping: function (dataid, div, editable, moduleid) {
            init_mapping(dataid, div, editable, moduleid);
        },
        dtExpandBtn: function(nRow, id) {
            return init_dt_expandbtn(nRow, id);
        },
        dtSubDetails: function(tbl, url, inputs_arr, clss) {
            init_dt_subdetails(tbl, url, inputs_arr, clss);
        },
        initMapSpec: function(div, dataid, type) {
            init_map_specific(div, dataid, type);
        },
        sysCheckMode: function(){
            var ret;
            $.ajax({
                url: PECO.base_url() + 'settings/systemcheck/mode',
                type: 'post',
                data: 'html',
                async: false,
                dataType: 'json'
            }).done(function(d){
                if(d.qry==true) {
                    ret = d.dev;
                }else{
                    PECO.initAlerts('System Mode is not set!', 'PECO.net', 'warning');
                }
            }).fail(function(){
                PECO.phpError();
            });
            return ret;
        },
        print_acct_requirements: function(dataid) {
            var html = '';
            $.ajax({
                url: PECO.base_url() + 'cad/getacctrequirements',
                type: 'post',
                data: {'id': dataid},
                dataType: 'json',
            }).done(function (data) {
                console.log(data.list);
                var req_num = data.list.length;
                html += '<ul class="list-group summary column">';
                for (req = 0; req < req_num; req++) {
                    //var req_text = data[req].text.substring(0, 45);
                    var req_text = data.list[req].text;
                    var req_stat = data.list[req].stat;
                    html += '<li class="list-group-item"><span class="label label-default">' + req_text + '</span>' +
                        '<span class="pull-right">'+req_stat+'</span>' +
                        '</li>';
                }
                html += '</ul>';
                PECO.pecoRepPrint('Application Requirements', data.html , false);
            });
        },
        fancybox: function() {
            init_fancybox();
        },
        iCheckRow: function(el, type, color) {
            var el_color = (color) ? '-' + color : '';

            if  ($.fn.iCheck) {
                el.iCheck({
                    checkboxClass: 'icheckbox_' + type + el_color, // minimal / square / polaris / futurico // red / green / blue
                    radioClass: 'iradio_' + type + el_color,
                    increaseArea: '20%' // optional
                }).on('ifChecked', function () {
                    var this_ = $(this);
                    this_.attr('checked', true);
                }).on('ifUnchecked', function () {
                    var this_ = $(this);
                    this_.attr('checked', false);
                });
            }
        },
        popOverRow: function(el, html, aniamte, clss) {
            el.popover({
                html: html,
                animation: aniamte,
                template: '<div class="popover '+ clss +'"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>',
            });
            return false;
        },

        meterSearchForm: function() {
            handlerMeterSearchForm();
        },

        topMenuLoading: function(el, msg) {
            var msg = (msg!='') ? msg : 'content..';
            var html = '';
            html += '<li>';
            html += '<a href="javascript:;">';
            html += '<span class="task">';
            html += '<span class="desc"><i class="fa fa-spinner fa-pulse fa-spin fa-fast"></i> '+msg+'</span>';
            html += '</span>';
            html += '</span>';
            html += '</a>';
            html += '</li>';
            return html;
        },

        topMenuDefault: function(el, msg) {
            var msg = (msg!='') ? msg : 'No content found!';
            var html = '';
            html += '<li>';
            html += '<a href="javascript:;">';
            html += '<span class="task">';
            html += '<span class="desc text-warning"><i class="fa fa-question"></i> '+msg+'</span>';
            html += '</span>';
            html += '</span>';
            html += '</a>';
            html += '</li>';
            return html;
        },

        topMenuErrorPHP: function(el) {
            var html = '';
            html += '<li>';
            html += '<a href="javascript:;">';
            html += '<span class="task">';
            html += '<span class="desc" style="color: red !important; text-align: center !important;"><i class="fa fa-warning"></i> ERROR PHP</span>';
            html += '</span>';
            html += '</span>';
            html += '</a>';
            html += '</li>';
            return html;
        },

        btnLoading: function (this_btn, msg) {
            var html_default = (msg != '') ? '<i class="fa fa-spinner fa-spin fa-pulse"></i> ' + msg : '<i class="fa fa-spinner fa-spin fa-pulse"></i> Getting data...';
            this_btn.html(html_default);
            this_btn.removeClass('btn-primary btn-success btn-warning btn-danger').addClass('btn-info');
        },

        btnSuccess: function (this_btn, msg, btn_orig_html, btn_orig_class) {
            var btn_message = (msg != '') ? '<i class="fa fa-info"></i> ' + msg : '<i class="fa fa-check"></i> Query success!';
            this_btn.removeClass('btn-danger btn-primary btn-info btn-warning').addClass('btn-success');
            this_btn.html(btn_message);

            setTimeout(function(){
                this_btn.html(btn_orig_html);
                this_btn.removeClass('btn-success btn-danger btn-primary btn-info').addClass(btn_orig_class);
            }, 2000);
        },

        btnErrorPHP: function (this_btn, this_btn_orig_html, btn_orig_class) {
            this_btn.html('<i class="fa fa-times"> PHP Error!');
            this_btn.removeClass('btn-success btn-primary btn-info btn-warning').addClass('btn-danger');
            setTimeout(function(){
                this_btn.html(this_btn_orig_html);
                this_btn.removeClass('btn-success btn-danger btn-primary btn-info').addClass(btn_orig_class);
            }, 1000);
        },

        btnClearTrans: function(btn) {
            btn.click(function(e) {
                e.preventDefault();
                swal({
                    title: "Are you sure?",
                    text: btn.attr('title'),
                    type: "error",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Yes, Clear!",
                    closeOnConfirm: false,
                    closeOnCancel: false,
                    showLoaderOnConfirm: true
                }, function(isConfirm) {
                    if (isConfirm) {
                        $.ajax({
                            url: PECO.base_url() + btn.attr('href'),
                            type: 'post',
                            data: {},
                            dataType: 'json',
                        }).done(function(d) {
                            swal.close();
                            PECO.initAlerts(d.msg, 'PECO.net', d.func);
                        }).fail(function() {
                            swal.close();
                            PECO.phpError();
                        });
                    }else{
                        swal.close();
                    }
                });
            });
        },

        handlerComplaintsInputBasic: function() {
            init_handler_complaints_input_basic();
        },

        handleCustomerMap: function(dataid) {
            return init_customer_map(dataid);
        },

        select2icons: function(el) {
            var this_tr = el.closest('tr');
            var this_val = this_tr.find('#icon-view').attr('data-id');
            $.ajax({
                url: PECO.base_url() + 'systems/select2icons',
                dataType: 'json',
                type: "POST",
                beforeSend: function() {
                    el.val('Loading icons...');
                }
            }).done(function (d) {
                if (d) {
                    el.val('');
                    el.select2({
                        allowClear: true,
                        placeholder: 'Icons..',
                        data: d.list,
                        formatResult: select2result, // omitted for brevity, see the source of this page
                        formatSelection: select2selection, // omitted for brevity, see the source of this page
                        width: 'resolve', // 100% or resolve
                    }).val(this_val).trigger('change.select2');

                    PECO.select2_slimscroller();
                }
            });
        },

        initMapDrawer: function(div, lat, lon, z) {
            init_map_drawer(div, lat, lon, z);
        },

        initCustomerReadingData: function(acctid) {
            var html = '';
            html += '<style>.dataTables_length { display: inline-block; width: 20% !important; float: left !important;} </style>' +
                '<table class="table table-bordered table-hover table-bordered table-striped tbl-zoom" id="tbl_data_reading_history">\n' +
                ' <thead>' +
                '<th></th>' +
                '<th>Year</th>' +
                '<th>Month</th>' +
                '<th>Kwh</th>' +
                '<th>Reading</th>' +
                ' <th>Amount</th>' +
                '<th>Duedate</th>' +
                '<th>Ref</th>' +
                '<th>Payments</th>' +
                '<th></th>' +
                '</thead>' +
                '<tbody></tbody>' +
                '</table>';

            $('[data-toggle=readingdata]', document).html(html);
            var tbl = $('#tbl_data_reading_history', document);
            $.ajax({
                url: PECO.base_url() + 'peco/getcustomerreadingdata',
                type: 'post',
                data: {'id': acctid},
                dataType: 'json',
                beforeSend: function() {
                    PECO.DTphpLoading(tbl, 'Loading reading data..');
                }
            }).done(function(d) {
                tbl.DataTable({
                    bDestroy: true,
                    bPaginate: true,
                    bFilter: true,
                    bInfo: true,
                    aaData: d.list,
                    bSort: true,
                    pageLength: 12,
                    "aLengthMenu": [[12, 24, 48, -1], [12, 24, 48, "All"]],
                    saveState: true,
                    aoColumns: [
                        {"data": "expand", sWidth: '', sClass: 'text-align-center'},
                        {"data": "year", sWidth: '', sClass: ''},
                        {"data": "months", sWidth: '', sClass: ''},
                        {"data": "kwh", sWidth: '20%', sClass: 'number'},
                        {"data": "reading", sWidth: '35%', sClass: 'number'},
                        {"data": "amt", sWidth: '12%', sClass: 'number'},
                        {"data": "duedate", sWidth: '12%', sClass: ''},
                        {"data": "ref", sWidth: '', sClass: ''},
                        {"data": "payment", sWidth: '', sClass: ''},
                        {"data": "control", sWidth: '10%', sClass: 'contols'},
                    ],

                    "searchHighlight": true,
                    "language": PECO.DTEmptyMessage(),
                    "sDom": "Rlfrtip",
                    order: [[1, 'desc'], [2, 'desc']],
                    fnRowCallback: function (nRow, aData, index) {

                    }
                });
            }).fail(function() {
                PECO.DTphpError(tbl);
            });

            tbl.on('click', 'tr #btn-expand', function () {
                var this_ = $(this);
                var thisTr = this_.closest('tr');
                var thisTr_child = thisTr.children('td').length;
                var data_id = this_.attr('data-id');
                if (this_.hasClass('expanded') == false) {
                    thisTr.next('#error').remove();
                    this_.removeClass('fa-angle-right').addClass('fa-angle-down');
                    $.ajax({
                        url: PECO.base_url() + 'peco/getcustomerreadinginfo',
                        type: 'post',
                        data: {'id': data_id},
                        dataType: 'json',
                        beforeSend: function () {
                            thisTr.after('<tr id="loading" class="info " ><td colspan="' + thisTr_child + '" class=""><i class="fa fa-spinner fa-spin fa-pulse"></i> Loading..</td></tr>');

                        }
                    }).done(function(d){
                        thisTr.after('<tr class="animated fadeIn fast compact '+d.func+'" id="details"><td colspan="' + thisTr_child + '" class="">' + d.html + '</td></tr>');
                        tbl.find('#loading').remove();
                    }).fail(function(){
                        thisTr.after('<tr class="animated fadeIn fast compact danger" id="error"><td colspan="' + thisTr_child + '"><i class="fa fa-times"></i> Error PHP</td></tr>');
                        tbl.find('#loading').remove();
                    });
                } else {
                    thisTr.next('#details').remove();
                    thisTr.next('#error').remove();
                    tbl.find('#loading').remove();
                    this_.removeClass('fa-angle-down').addClass('fa-angle-right');
                }
                this_.toggleClass('expanded');
                this_.closest('tr').toggleClass('expand-show');
            });
        },

        sweetNotif: function (title,message,func) {
            init_swal_notif(title,message,func);
        },

        pdfPreview: function (win,title,html) {

            win.document.title = (title) ? title : 'Print Preview';

            const form = document.createElement('form');
            form.method = 'post';
            form.action = PECO.base_url() + 'printer/PDFview';

            const titleField = document.createElement('input');
            titleField.type = 'hidden';
            titleField.name = 'title';
            titleField.value = title;

            form.appendChild(titleField);

            const filenameField = document.createElement('input');
            filenameField.type = 'hidden';
            filenameField.name = 'filename';
            filenameField.value = title;

            form.appendChild(filenameField);

            const htmlField = document.createElement('input');
            htmlField.type = 'hidden';
            htmlField.name = 'html';
            htmlField.value = html;

            form.appendChild(htmlField);

            win.document.body.appendChild(form);
            form.submit();
        },

        searchToolTip: function (el,url,otherParams) {
            searchtooltip(el,url,otherParams);
        },

        formatNumberOnChange: function (el,decimal) {
            if (decimal === true) {
                el.on('keyup', function (evt) {
                    if (evt.which != 110) {//not a fullstop
                        var n = parseFloat($(this).val().replace(/\,/g, ''),10);
                        $(this).val(n.toLocaleString());
                    }
                });
            } else {
                el.on('keyup', function(){
                    var n = parseInt($(this).val().replace(/\D/g,''),10);
                    $(this).val(n.toLocaleString());
                });
            }
        },

        formatNumber: function (el,decimal) {
            if (decimal === true) {
                var n = el.val().replace(/\,/g, '');
                var x = n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                el.val(x.toLocaleString());
            } else {
                var n = parseInt(el.val().replace(/\D/g,''),10);
                el.val(n.toLocaleString());
            }
        },

        dtDocsList: function (el) {
            var folder = el.attr('data-folder');

            $.ajax({
                url : PECO.base_url() + 'admin/dtdocslist',
                type : 'post',
                dataType : 'json',
                data : {folder : folder},
                beforeSend: function() {
                    PECO.DTphpLoading(el, 'Loading file list...');
                }
            }).done(function (d) {
                el.DataTable({
                    bDestroy: true,
                    bPaginate: false,
                    bFilter: false,
                    bInfo: false,
                    bStateSave: true,
                    bProcessing: true,
                    aaData: d.list,
                    aoColumns: [
                        {"data": "count", sClass: '' , sWidth: ''},
                        {"data": "name", sClass: 'text-primary bold'},
                        {"data": "control", sClass: "controls text-align-center", sWidth: '30px'}
                    ],
                    language: {
                        "emptyTable": '<i class="fa fa-warning text-warning"></i> No record found.'
                    },
                });
            }).fail(function () {
                PECO.DTphpError(el);
            });
        }

    };

}();
