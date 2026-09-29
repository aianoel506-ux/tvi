/**
 Core script to handle the entire theme and core functions
 **/
var Layout = function () {

    var layoutImgPath = 'admin/layout/img/';

    var layoutCssPath = 'admin/layout/css/';

    var resBreakpointMd = PECO.getResponsiveBreakpoint('md');



    //* BEGIN:CORE HANDLERS *//
    // this function handles responsive layout on screen size resize or mobile device rotate.

    // Set proper height for sidebar and content. The content and sidebar height must be synced always.
    var handleSidebarAndContentHeight = function () {
        var content = $('.page-content');
        var sidebar = $('.page-sidebar');
        var body = $('body');
        var height;

        if (body.hasClass("page-footer-fixed") === true && body.hasClass("page-sidebar-fixed") === false) {
            var available_height = PECO.getViewPort().height - $('.page-footer').outerHeight() - $('.page-header').outerHeight();
            if (content.height() < available_height) {
                content.attr('style', 'min-height:' + available_height + 'px');
            }
        } else {
            if (body.hasClass('page-sidebar-fixed')) {
                height = _calculateFixedSidebarViewportHeight();
                if (body.hasClass('page-footer-fixed') === false) {
                    height = height - $('.page-footer').outerHeight();
                }
            } else {
                var headerHeight = $('.page-header').outerHeight();
                var footerHeight = $('.page-footer').outerHeight();

                if (PECO.getViewPort().width < resBreakpointMd) {
                    height = PECO.getViewPort().height - headerHeight - footerHeight;
                } else {
                    height = sidebar.outerHeight() + 10;
                }

                if ((height + headerHeight + footerHeight) <= PECO.getViewPort().height) {
                    height = PECO.getViewPort().height - headerHeight - footerHeight;
                }
            }
            content.attr('style', 'min-height:' + height + 'px');
        }
    };

    // Handle sidebar menu links
    var handleSidebarMenuActiveLink = function (mode, el) {
        var url = location.hash.toLowerCase();

        var menu = $('.page-sidebar-menu');

        if (mode === 'click' || mode === 'set') {
            el = $(el);
        } else if (mode === 'match') {
            menu.find("li > a").each(function () {
                var path = $(this).attr("href").toLowerCase();
                // url match condition         
                if (path.length > 1 && url.substr(1, path.length - 1) == path.substr(1)) {
                    el = $(this);
                    return;
                }
            });
        }

        if (!el || el.size() == 0) {
            return;
        }

        if (el.attr('href').toLowerCase() === 'javascript:;' || el.attr('href').toLowerCase() === '#') {
            return;
        }

        var slideSpeed = parseInt(menu.data("slide-speed"));
        var keepExpand = menu.data("keep-expanded");

        // disable active states
        menu.find('li.active').removeClass('active');
        menu.find('li > a > .selected').remove();

        if (menu.hasClass('page-sidebar-menu-hover-submenu') === false) {
            menu.find('li.open').each(function () {
                if ($(this).children('.sub-menu').size() === 0) {
                    $(this).removeClass('open');
                    $(this).find('> a > .arrow.open').removeClass('open');
                }
            });
        } else {
            menu.find('li.open').removeClass('open');
        }

        el.parents('li').each(function () {
            $(this).addClass('active');
            $(this).find('> a > span.arrow').addClass('open');

            if ($(this).parent('ul.page-sidebar-menu').size() === 1) {
                $(this).find('> a').append('<span class="selected"></span>');
            }

            if ($(this).children('ul.sub-menu').size() === 1) {
                $(this).addClass('open');
            }
        });

        if (mode === 'click') {
            if (PECO.getViewPort().width < resBreakpointMd && $('.page-sidebar').hasClass("in")) { // close the menu on mobile view while laoding a page 
                $('.page-header .responsive-toggler').click();
            }
        }
    };

    // Handle sidebar menu
    var handleSidebarMenu = function () {
        $('.page-sidebar').on('click', 'li > a', function (e) {

            if (PECO.getViewPort().width >= resBreakpointMd && $(this).parents('.page-sidebar-menu-hover-submenu').size() === 1) { // exit of hover sidebar menu
                return;
            }

            if ($(this).next().hasClass('sub-menu') === false) {
                if (PECO.getViewPort().width < resBreakpointMd && $('.page-sidebar').hasClass("in")) { // close the menu on mobile view while laoding a page 
                    $('.page-header .responsive-toggler').click();
                }
                return;
            }

            if ($(this).next().hasClass('sub-menu always-open')) {
                return;
            }

            var parent = $(this).parent().parent();
            var the = $(this);
            var menu = $('.page-sidebar-menu');
            var sub = $(this).next();

            var autoScroll = menu.data("auto-scroll");
            var slideSpeed = parseInt(menu.data("slide-speed"));
            var keepExpand = menu.data("keep-expanded");

            if (keepExpand !== true) {
                parent.children('li.open').children('a').children('.arrow').removeClass('open');
                parent.children('li.open').children('.sub-menu:not(.always-open)').slideUp(slideSpeed);
                parent.children('li.open').removeClass('open');
            }

            var slideOffeset = -200;

            if (sub.is(":visible")) {
                $('.arrow', $(this)).removeClass("open");
                $(this).parent().removeClass("open");
                sub.slideUp(slideSpeed, function () {
                    if (autoScroll === true && $('body').hasClass('page-sidebar-closed') === false) {
                        if ($('body').hasClass('page-sidebar-fixed')) {
                            menu.slimScroll({
                                'scrollTo': (the.position()).top
                            });
                        } else {
                            PECO.scrollTo(the, slideOffeset);
                        }
                    }
                    handleSidebarAndContentHeight();
                });
            } else {
                $('.arrow', $(this)).addClass("open");
                $(this).parent().addClass("open");
                sub.slideDown(slideSpeed, function () {
                    if (autoScroll === true && $('body').hasClass('page-sidebar-closed') === false) {
                        if ($('body').hasClass('page-sidebar-fixed')) {
                            menu.slimScroll({
                                'scrollTo': (the.position()).top
                            });
                        } else {
                            PECO.scrollTo(the, slideOffeset);
                        }
                    }
                    handleSidebarAndContentHeight();
                });
            }

            e.preventDefault();
        });

        // handle ajax links within sidebar menu
        $('.page-sidebar').on('click', ' li > a.ajaxify', function (e) {
            e.preventDefault();
            PECO.scrollTop();

            var url = $(this).attr("href");
            var menuContainer = $('.page-sidebar ul');
            var pageContent = $('.page-content');
            var pageContentBody = $('.page-content .page-content-body');

            menuContainer.children('li.active').removeClass('active');
            menuContainer.children('arrow.open').removeClass('open');

            $(this).parents('li').each(function () {
                $(this).addClass('active');
                $(this).children('a > span.arrow').addClass('open');
            });
            $(this).parents('li').addClass('active');

            if (PECO.getViewPort().width < resBreakpointMd && $('.page-sidebar').hasClass("in")) { // close the menu on mobile view while laoding a page 
                $('.page-header .responsive-toggler').click();
            }

            PECO.startPageLoading();

            var the = $(this);

            $.ajax({
                type: "GET",
                cache: false,
                url: url,
                dataType: "html",
                success: function (res) {

                    if (the.parents('li.open').size() === 0) {
                        $('.page-sidebar-menu > li.open > a').click();
                    }

                    PECO.stopPageLoading();
                    pageContentBody.html(res);
                    Layout.fixContentHeight(); // fix content height
                    PECO.initAjax(); // initialize core stuff
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    PECO.stopPageLoading();
                    pageContentBody.html('<h4>Could not load the requested content.</h4>');
                }
            });
        });

        // handle ajax link within main content
        $('.page-content').on('click', '.ajaxify', function (e) {
            e.preventDefault();
            PECO.scrollTop();

            var url = $(this).attr("href");
            var pageContent = $('.page-content');
            var pageContentBody = $('.page-content .page-content-body');

            PECO.startPageLoading();

            if (PECO.getViewPort().width < resBreakpointMd && $('.page-sidebar').hasClass("in")) { // close the menu on mobile view while laoding a page 
                $('.page-header .responsive-toggler').click();
            }

            $.ajax({
                type: "GET",
                cache: false,
                url: url,
                dataType: "html",
                success: function (res) {
                    PECO.stopPageLoading();
                    pageContentBody.html(res);
                    Layout.fixContentHeight(); // fix content height
                    PECO.initAjax(); // initialize core stuff
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    pageContentBody.html('<h4>Could not load the requested content.</h4>');
                    PECO.stopPageLoading();
                }
            });
        });

        // handle scrolling to top on responsive menu toggler click when header is fixed for mobile view
        $(document).on('click', '.page-header-fixed-mobile .page-header .responsive-toggler', function () {
            PECO.scrollTop();
        });
    };

    // Helper function to calculate sidebar height for fixed sidebar layout.
    var _calculateFixedSidebarViewportHeight = function () {
        var sidebarHeight = PECO.getViewPort().height - $('.page-header').outerHeight();
        if ($('body').hasClass("page-footer-fixed")) {
            sidebarHeight = sidebarHeight - $('.page-footer').outerHeight();
        }

        return sidebarHeight;
    };

    // Handles fixed sidebar
    var handleFixedSidebar = function () {
        var menu = $('.page-sidebar-menu');

        PECO.destroySlimScroll(menu);

        if ($('.page-sidebar-fixed').size() === 0) {
            handleSidebarAndContentHeight();
            return;
        }

        if (PECO.getViewPort().width >= resBreakpointMd) {
            menu.attr("data-height", _calculateFixedSidebarViewportHeight());
            PECO.initSlimScroll(menu);
            handleSidebarAndContentHeight();
        }
    };

    // Handles sidebar toggler to close/hide the sidebar.
    var handleFixedSidebarHoverEffect = function () {
        var body = $('body');
        if (body.hasClass('page-sidebar-fixed')) {
            $('.page-sidebar').on('mouseenter', function () {
                if (body.hasClass('page-sidebar-closed')) {
                    $(this).find('.page-sidebar-menu').removeClass('page-sidebar-menu-closed');
                }
            }).on('mouseleave', function () {
                if (body.hasClass('page-sidebar-closed')) {
                    $(this).find('.page-sidebar-menu').addClass('page-sidebar-menu-closed');
                }
            });
        }
    };

    // Hanles sidebar toggler
    var handleSidebarToggler = function () {
        var body = $('body');
        if ($.cookie && $.cookie('sidebar_closed') === '1' && PECO.getViewPort().width >= resBreakpointMd) {
            $('body').addClass('page-sidebar-closed');
            $('.page-sidebar-menu').addClass('page-sidebar-menu-closed');
        }

        // handle sidebar show/hide
        $('body').on('click', '.sidebar-toggler', function (e) {
            var sidebar = $('.page-sidebar');
            var sidebarMenu = $('.page-sidebar-menu');
            $(".sidebar-search", sidebar).removeClass("open");

            if (body.hasClass("page-sidebar-closed")) {
                body.removeClass("page-sidebar-closed");
                sidebarMenu.removeClass("page-sidebar-menu-closed");
                if ($.cookie) {
                    $.cookie('sidebar_closed', '0');
                }
            } else {
                body.addClass("page-sidebar-closed");
                sidebarMenu.addClass("page-sidebar-menu-closed");
                if (body.hasClass("page-sidebar-fixed")) {
                    sidebarMenu.trigger("mouseleave");
                }
                if ($.cookie) {
                    $.cookie('sidebar_closed', '1');
                }
            }

            $(window).trigger('resize');
        });

        handleFixedSidebarHoverEffect();

        // handle the search bar close
        $('.page-sidebar').on('click', '.sidebar-search .remove', function (e) {
            e.preventDefault();
            $('.sidebar-search').removeClass("open");
        });

        // handle the search query submit on enter press
        $('.page-sidebar .sidebar-search').on('keypress', 'input.form-control', function (e) {
            if (e.which == 13) {
                $('.sidebar-search').submit();
                return false; //<---- Add this line
            }
        });

        // handle the search submit(for sidebar search and responsive mode of the header search)
        $('.sidebar-search .submit').on('click', function (e) {
            e.preventDefault();
            if ($('body').hasClass("page-sidebar-closed")) {
                if ($('.sidebar-search').hasClass('open') === false) {
                    if ($('.page-sidebar-fixed').size() === 1) {
                        $('.page-sidebar .sidebar-toggler').click(); //trigger sidebar toggle button
                    }
                    $('.sidebar-search').addClass("open");
                } else {
                    $('.sidebar-search').submit();
                }
            } else {
                $('.sidebar-search').submit();
            }
        });

        // handle close on body click
        if ($('.sidebar-search').size() !== 0) {
            $('.sidebar-search .input-group').on('click', function (e) {
                e.stopPropagation();
            });

            $('body').on('click', function () {
                if ($('.sidebar-search').hasClass('open')) {
                    $('.sidebar-search').removeClass("open");
                }
            });
        }
    };

    // Handles the horizontal menu
    var handleHeader = function () {
        // handle search box expand/collapse        
        $('.page-header').on('click', '.search-form', function (e) {
            $(this).addClass("open");
            $(this).find('.form-control').focus();

            $('.page-header .search-form .form-control').on('blur', function (e) {
                $(this).closest('.search-form').removeClass("open");
                $(this).unbind("blur");
            });
        });

        // handle hor menu search form on enter press
        $('.page-header').on('keypress', '.hor-menu .search-form .form-control', function (e) {
            if (e.which == 13) {
                $(this).closest('.search-form').submit();
                return false;
            }
        });

        // handle header search button click
        $('.page-header').on('mousedown', '.search-form.open .submit', function (e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).closest('.search-form').submit();
        });
    };

    // Handles Bootstrap Tabs.
    var handleTabs = function () {
        // fix content height on tab click
        $('body').on('shown.bs.tab', 'a[data-toggle="tab"]', function () {
            handleSidebarAndContentHeight();
        });
    };

    // Handles the go to top button at the footer
    var handleGoTop = function () {
        var offset = 300;
        var duration = 500;

        if (navigator.userAgent.match(/iPhone|iPad|iPod/i)) { // ios supported
            $(window).bind("touchend touchcancel touchleave", function (e) {
                if ($(this).scrollTop() > offset) {
                    $('.scroll-to-top').fadeIn(duration);
                } else {
                    $('.scroll-to-top').fadeOut(duration);
                }
            });
        } else { // general 
            $(window).scroll(function () {
                if ($(this).scrollTop() > offset) {
                    $('.scroll-to-top').fadeIn(duration);
                } else {
                    $('.scroll-to-top').fadeOut(duration);
                }
            });
        }

        $('.scroll-to-top').click(function (e) {
            e.preventDefault();
            $('html, body').animate({
                scrollTop: 0
            }, duration);
            return false;
        });
    };

    // Hanlde 100% height elements(block, portlet, etc)
    var handle100HeightContent = function () {

        var target = $('.full-height-content');
        var height;

        if (!target.hasClass('portlet')) {
            return;
        }

        height = PECO.getViewPort().height -
            $('.page-header').outerHeight(true) -
            $('.page-footer').outerHeight(true) -
            $('.page-title').outerHeight(true) -
            $('.page-bar').outerHeight(true);

        if ($('body').hasClass('page-header-fixed')) {
            height = height - $('.page-header').outerHeight(true);
        }

        var portletBody = target.find('.portlet-body');

        if (PECO.getViewPort().width < resBreakpointMd) {
            PECO.destroySlimScroll(portletBody.find('.full-height-content-body')); // destroy slimscroll 
            return;
        }

        if (target.find('.portlet-title')) {
            height = height - target.find('.portlet-title').outerHeight(true);
        }

        height = height - parseInt(portletBody.css("padding-top"));
        height = height - parseInt(portletBody.css("padding-bottom"));

        if (target.hasClass("full-height-content-scrollable")) {
            portletBody.find('.full-height-content-body').css('height', height);
            PECO.initSlimScroll(portletBody.find('.full-height-content-body'));
        } else {
            portletBody.css('min-height', height);
        }
    };
    var handleSessionCheck = function () {
    }
    var handleLogoutBtn = function () {
        $('body').on('click', '#btn-logout', function (e) {
            $.this_form = $(this);
            e.preventDefault();
            $.SmartMessageBox({
                    title: "<i class='fa fa-question fa-fw fa-lg txt-color-yellow'></i> Confirm: " + $.this_form.attr('title') + "</span>",
                    content: 'Please confirm action taken',
                    buttons: '[Yes][No]',
                    buttonClass: "btn-success, btn-danger",
                    buttonsIcon: "fa-check, fa-times",
                },
                function (ButtonPressed) {
                    if (ButtonPressed === "Yes") {

                        $.ajax({
                            url: $.this_form.attr('href').replace('#', ''),
                            type: $.this_form.attr('data-method'),
                            data: {'segs': $.this_form.attr('data-segs'), 'navid': $.this_form.attr('data-module')},
                            dataType: "json",
                            beforeSend: function () {
                                PECO.start_pageLogin_loading({animate: true, message: 'Working...', messageSize: '20px'});
                            }
                        }).done(function (data) {
                            if(data.qry==true) {
                                PECO.stop_pageLogin_loading();
                                PECO.start_pageLogin_loading({animate: true, message: '<span class="text-success animated fadeInUp fast">' + data.msg + '</span>', messageSize: '35px'});
                                PECO.initPlaySound('skypeout');
                                setTimeout(function () {
                                    window.location = PECO.base_url() + data.landing;
                                }, 1000);
                            }else{
                                $.smallBox({
                                    title: $.this_form.attr('title'),
                                    content: 'Logout attempt error',
                                    color: "rgba(238, 71, 24, 0.5)",
                                    icon: "fa fa-times fa-2x fadeInRight animated",
                                    timeout: PECO.notification_timeout()
                                });
                            }
                        }).fail(function () {
                            PECO.stop_pageLogin_loading();
                            console.log('Unable to find the PHP file');
                        });

                    }
                });
        });

        $('body').on('click', '#btn-lock', function (e) {
            $.this_form = $(this);
            e.preventDefault();
            $.SmartMessageBox({
                    title: "<i class='fa fa-question fa-fw fa-lg txt-color-yellow'></i> Confirm: " + $.this_form.attr('title') + "</span>",
                    content: 'Please confirm action taken',
                    buttons: '[Yes][No]',
                    buttonClass: "btn-success, btn-danger",
                    buttonsIcon: "fa-check, fa-times",
                },
                function (ButtonPressed) {
                    if (ButtonPressed === "Yes") {
                        $.ajax({
                            url: $.this_form.attr('href').replace('#', ''),
                            type: $.this_form.attr('data-method'),
                            data: {'segs': $.this_form.attr('data-segs'), 'navid': $.this_form.attr('data-module')},
                            dataType: "json",
                            beforeSend: function () {
                                PECO.start_pageLogin_loading({animate: true, message: 'Working...', messageSize: '20px'});
                            }
                        }).done(function (data) {
                            if (data['num'] == 0) {
                                $.smallBox({
                                    title: $.this_form.attr('title'),
                                    content: 'Logout attempt error',
                                    color: "rgba(238, 71, 24, 0.5)",
                                    icon: "fa fa-times fa-2x fadeInRight animated",
                                    timeout: PECO.notification_timeout()
                                });
                            } else {
                                PECO.stop_pageLogin_loading();
                                PECO.start_pageLogin_loading({animate: true, message: '<span class="text-success animated fadeInUp fast">' + data.msg + '</span>', messageSize: '35px'});
                                PECO.initPlaySound('skypeout');
                                setTimeout(function () {
                                    window.location = base_url;
                                }, 1000);
                            }
                        }).fail(function () {
                            PECO.stop_pageLogin_loading();
                            console.log('Unable to find the PHP file');
                        });
                    }
                });
        });
    };

    var handleCheckDisk = function () {
        //getServerDiskStat();
        //setInterval(function () {
        //    getServerDiskStat();
        //}, 120000);
    };

    var getServerDiskStat = function () {

        $.get(base_url + 'admin/checkdisck', function (data) {
            $('#diskspace').html('<span style="color: ' + data.color + '">' + data.size + ' ' + data.name + '</span> / ' + data.total + ' ' + data.name);
        }, 'json');
    };

    var browserNotify = function(title, text, icon, url) {
        document.addEventListener('DOMContentLoaded', function () {
            if (!Notification) {
                alert('Desktop notifications not available in your browser. Try Chromium.');
                return;
            }

            if (Notification.permission !== "granted")
                Notification.requestPermission();
        });

        if (Notification.permission !== "granted")
            Notification.requestPermission();
        else {
            var notification = new Notification(title, {
                icon: icon,
                body: text,
            });

            notification.onclick = function () {
                window.open(url);
            };

        }
    };
    var init_notification_list = function(this_) {
        $.this_ = this_;
        var loading_message_list = '<li>' +
            '<a href="javascript:;">' +
            '<span class="time"></span>' +
            '<span class="">' +
            '<span class="">' +
            '' +
            '</span>' +
            '<i class="fa fa-spiner fa-spin fa-fw"></i> Loading content..</span>' +
            '</a>' +
            '</li>';
        $.ajax({
            url: base_url + 'notification/get_notification_lists', //$.this_.attr('href').replace('#',''),
            type: 'POST',
            data: 'html',
            dataType: "json",
            beforeSend: function () {
                $.this_.find('#ajaxify-list').html(loading_message_list);
            },
        }).done(function (data) {
            if (data['query'] == 0) {
                $('#total-notifications-log').val(data['query']);

                $('.top-menu').find('li#' + data['dataid']).find('span.badge').html('');
            } else {
                $('#total-notifications-log').val(data['query']);
                $('.top-menu').find('li#' + data['dataid']).find('span.badge').html(data['query']);
            }
            $.this_.find('#ajaxify-list').html(data['html']);
            PECO.initDTNicescroller();
        }).fail(function () {
            console.log('fail to load file...');
        });
    }

    var handleNotification = function () {


        $('.top-menu').on('click', '.ajaxify', function (e) {
            //e.preventDefault();
            $.this_ = $(this);
            init_notification_list($.this_);
        });
        setInterval(function () {
            var notification_on = $('#notification-switch').bootstrapSwitch('state');
            if (notification_on == true) {
                $.post(base_url + "notification/notificationcheck", function (q) {
                    if (q) {
                        // NOTIFICATIONS : ACTIVITIES //
                        var notification_id 	= q.notify[0].dataid;
                        var notification_count 	= q.notify[0].datacount;
                        var notifications_log 	= q.notify[0].notifycount;
                        var notification_mode 	= q.notify[0].notifymode;
                        var notifycation_cont	= q.notify[0].notifycontent;
                        var notification_pic	= q.notify[0].pic;
                        var notification_url 	= q.notify[0].url;

                        // NOTIFICATIONS : USER LOGS //
                        var userlog_id 			= q.notify[3].dataid;
                        var userlog_hist 		= q.notify[3].loghist;
                        var userlog_lastid 		= q.notify[3].logid;
                        var userlog_mode 		= q.notify[3].notifymode;
                        var userlog_cont		= q.notify[3].notifycontent;
                        var userlog_person		= q.notify[3].notifyperson;
                        var userlog_notify		= q.notify[3].notify;
                        var userlog_pic			= q.notify[3].pic;


                        var message_id = q['notify'][1]['dataid'];
                        var message_count = q['notify'][1]['datacount'];

                        var task_id = q['notify'][2]['dataid'];
                        var task_count = q['notify'][2]['datacount'];


                        var messages_log = $('#total-messages-log').val();
                        var task_log = $('#total-tasks-log').val();

                        var browser_total_notification = 0;
                        var browser_notify = false;

                        // console.log('USERS STATS: ' + userlog_notify + ' | LASTID: '+userlog_hist+' | NOTIFYID: '+userlog_lastid+' LOG TYPE: '+userlog_cont);

                        if( userlog_notify==true) {
                            PECO.initPlaySound('smallbox');
                            PECO.initAlerts('<b>'+userlog_person+'</b> is '+userlog_cont, 'User', userlog_mode);
                            browserNotify('User', userlog_person+' is '+userlog_cont, base_url+userlog_pic, base_url);
                            $.post(base_url + "notification/updateuserlog", function (e) {}, 'json');

                        }

                        if (Number(notification_count) > Number(notifications_log)) {
                            PECO.initPlaySound('bigbox');
                            if(notification_count>0) {
                                $('.top-menu').find('li#' + notification_id).find('span.badge').html(notification_count).addClass('animated bounce fast').fadeTo(200, 1, function () {
                                    $(this).removeClass('animated bounce fast');
                                });
                            }
                            $('#total-notifications-log').val(notification_count);
                            PECO.initAlerts('you have ' + notification_count + ' new notification!<br>'+notifycation_cont+'', 'Alert', notification_mode);
                            browser_notify = true;
                            browser_total_notification += notification_count;
                            browserNotify('Activity Notification', "You have "+browser_total_notification+" unread notifications \n "+notifycation_cont, notification_pic, notification_url);
                            // console.log('NOTIFY TRUE: Data Count: '+notification_count+' / Notify Count: '+notifications_log);
                            // RELOAD NOTIFICATION LIST
                            init_notification_list($('#notifications'));
                            console.log(notification_url);
                        }else{
                            // SHOW NOTIFICATION COUNT WITHOUT ALERT
                            if(notification_count>0) {
                                $('.top-menu').find('li#' + notification_id).find('span.badge').html(notification_count);
                            }
                        }


                        if (Number(message_count > messages_log)) {
                            PECO.initPlaySound('pling');
                            $('.top-menu').find('li#' + message_id).find('span.badge').html(message_count).addClass('animated bounce fast').fadeTo(200, 1, function () {
                                $(this).removeClass('animated bounce fast');
                            });
                            $('#total-messages-log').val(message_count);
                            PECO.initAlerts('you have ' + message_count + ' new messages..', 'Alert', 'info');
                        }
                        if (Number(task_count > task_log)) {
                            PECO.initPlaySound('pling');
                            $('.top-menu').find('li#' + task_id).find('span.badge').html(task_count).addClass('animated bounce fast').fadeTo(200, 1, function () {
                                $(this).removeClass('animated bounce fast');
                            });
                            $('#total-tasks-log').val(task_count);
                            PECO.initAlerts('you have ' + task_count + ' new task pending', 'Alert', 'info');
                        }

                    }
                }, 'json');
            }
        }, 1000);
    };

    var init_search_customer = function() {
        // PECO.customerSelectTagging($('#global_search'), true, false);
        $('body').on('submit', '.search-form', function(e){
            var form = $(this);
            if(form.attr('is-submit')=='false') {
                e.preventDefault();
            }
        });

        var lastname = $('#global_search');

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
                suggestion: Handlebars.compile(['<div class="media">', '<div class="pull-left">', '<div class="media-object">', '<img src="{{img}}" width="50" height="50"/>', "</div>", "</div>", '<div class="media-body">', '<h5 class="media-heading text-primary"><a href="'+PECO.base_url()+'person/{{url}}.html"><b class="text-glow-yellow">{{lastname}}</b>, {{firstname}} {{middlename}}</h5>', "<p>{{district}} - {{addr}}</p>", "</a></div>", "</div>"].join("")),
            },
        }).on('typeahead:selected', function(event, selection) {
            $('.search-form', document).addClass('open');
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
        });
    };



    //* END:CORE HANDLERS *//

    return {
        // Main init methods to initialize the layout
        // IMPORTANT!!!: Do not modify the core handlers call order.

        initHeader: function () {
            handleHeader(); // handles horizontal menu    
            init_search_customer();
        },
        setSidebarMenuActiveLink: function (mode, el) {
            handleSidebarMenuActiveLink(mode, el);
        },
        initSidebar: function () {
            //layout handlers
            handleFixedSidebar(); // handles fixed sidebar menu
            handleSidebarMenu(); // handles main menu
            handleSidebarToggler(); // handles sidebar hide/show

            if (PECO.isAngularJsApp()) {
                handleSidebarMenuActiveLink('match'); // init sidebar active links 
            }

            PECO.addResizeHandler(handleFixedSidebar); // reinitialize fixed sidebar on window resize
        },
        initContent: function () {
            handle100HeightContent(); // handles 100% height elements(block, portlet, etc)
            handleTabs(); // handle bootstrah tabs

            PECO.addResizeHandler(handleSidebarAndContentHeight); // recalculate sidebar & content height on window resize
            PECO.addResizeHandler(handle100HeightContent); // reinitialize content height on window resize 
        },
        initFooter: function () {
            handleGoTop(); //handles scroll to top functionality in the footer
        },
        initEmployeeList: function (loadingmsg, loadingclass, depid, scrolling, paging, filter, info, statesave, modulehash) {

            //filter default active employees
            var depid = (depid) ? depid : '';
            $('#emptable').dataTable().empty();
            $('#emptable').dataTable({
               
                bDestroy: true,
                bPaginate: paging,
                bFilter: filter,
                bInfo: info,
                bStateSave: statesave,
                scrollY: scrolling,
                bProcessing: true,
                bServerSide: true,
                Order: [[1, "asc"]],
                oLanguage: {
                    sProcessing: PECO.datatableLoading(loadingmsg, loadingclass),
                },
                ajax: {
                    url: base_url + 'hris/emplist',
                    type: "POST",
                    data: {'depid': depid, 'modulehash': modulehash},
                },
                aoColumns: [
                    {"data": "empid", sWidth: '80px'},
                    {"data": "lastname", sWidth: ''},
                    {"data": "firstname", sWidth: ''},
                    {"data": "middlename", sWidth: ''},
                    {"data": "department", sWidth: ''},
                    {"data": "position", sWidth: ''},
                    {"data": "empstat", sWidth: ''},
                    {"data": "controls", sWidth: ''}
                ],
                columnDefs: [
                    {"targets": -4, "orderable": false, "searchable": false},
                    {"targets": -3, "orderable": false, "searchable": false},
                    {"targets": -2, "orderable": false, "searchable": false},
                    {"targets": -1, "orderable": false, "searchable": false},
                ]
            });
            PECO.initDTNicescroller();
        },
        initEmployeePerClassList: function (tblId, classId, loadingmsg, loadingclass, depid, scrolling, paging, filter, info, statesave, modulehash)
        {
            var depid = (depid) ? depid : '';
            var classId = (classId) ? classId : '';
            $(tblId).dataTable().empty();
            $(tblId).dataTable({
                bDestroy: true,
                bPaginate: paging,
                bFilter: filter,
                bInfo: info,
                bStateSave: statesave,
                scrollY: scrolling,
                bProcessing: true,
                bServerSide: true,
                Order: [[1, "asc"]],
                oLanguage: {
                    sProcessing: PECO.datatableLoading(loadingmsg, loadingclass),
                },
                ajax: {
                    url: base_url + 'hris/emplist',
                    type: "POST",
                    data: {'depid': depid, 'modulehash': modulehash, 'classid': classId},
                },
                aoColumns: [
                    {"data": "empid", sWidth: '80px'},
                    {"data": "lastname", sWidth: ''},
                    {"data": "firstname", sWidth: ''},
                    {"data": "middlename", sWidth: ''},
                    {"data": "department", sWidth: ''},
                    {"data": "position", sWidth: ''},
                    {"data": "empstat", sWidth: ''},
                    {"data": "controls", sWidth: ''}
                ],
                columnDefs: [
                    {"targets": -4, "orderable": false, "searchable": false},
                    {"targets": -3, "orderable": false, "searchable": false},
                    {"targets": -2, "orderable": false, "searchable": false},
                    {"targets": -1, "orderable": false, "searchable": false},
                ]
            });
            PECO.initDTNicescroller();
        },
        init: function () {
            this.initHeader();
            this.initSidebar();
            this.initContent();
            this.initFooter();
            handleLogoutBtn();
            handleSessionCheck();
            //handleNotification();
            handleCheckDisk();
        },
        initSessionIdle: function () {
            handleSessionCheck();
        },
        //public function to fix the sidebar and content height accordingly
        fixContentHeight: function () {
            handleSidebarAndContentHeight();
        },
        initFixedSidebarHoverEffect: function () {
            handleFixedSidebarHoverEffect();
        },
        initFixedSidebar: function () {
            handleFixedSidebar();
        },
        getLayoutImgPath: function () {
            return PECO.getAssetsPath() + layoutImgPath;
        },
        getLayoutCssPath: function () {
            return PECO.getAssetsPath() + layoutCssPath;
        },
    };

}();
