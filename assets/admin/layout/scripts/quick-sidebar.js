/**
Core script to handle the entire theme and core functions
**/
var QuickSidebar = function () {

    var echo_voice = new Audio();

	//this function can remove a array element.
	Array.remove = function(array, from, to) {
		var rest = array.slice((to || from) + 1 || array.length);
		array.length = from < 0 ? array.length + from : from;
		return array.push.apply(array, rest);
	};

	//this variable represents the total number of popups can be displayed according to the viewport width
	var total_popups = 0;

	//arrays of popups ids
	var popups = [];

	// Handles tab click ajax
    var hadleTabclick = function() {
        $('#right_bar_options a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            var target = $(e.target).attr("data-id")
            if (target === 'users') {
                handlerUserLists();
            }
        });
    };

    var handlerUserLists = function() {
        var search = $('#input_user_search', document).val();
        $.ajax({
            url: PECO.base_url() + 'user/chatuserlist',
            type: 'post',
            data: {'search': search},
            dataType: 'json',
            beforeSend: function() {
                $('#users_list').html('<h3 class="list-heading"><i class="fa fa-circle-o-notch fa-spin"></i> Loading users..</h3>');
            }
        }).done(function(d){
            if(!$('#users_list').find('.media-list').length ) {
                $('#users_list').html(d.html);
                $('#user_list_num', document).text(d.usernum);
            }
            $('body').find('.tooltips').each(function(){
                $(this).tooltip();
            });
        }).fail(function(){
            $('#users_list').html('<h3 class="list-heading text-danger">PHP Error!</h3>');
        });
    };
	
    // Handles quick sidebar toggler
    var handleQuickSidebarToggler = function () {
        // quick sidebar toggler
        $('.top-menu .dropdown-quick-sidebar-toggler a, .page-quick-sidebar-toggler').click(function (e) {
            $('body').toggleClass('page-quick-sidebar-open');
            handlerUserLists();
        });
    };

    // Handles quick sidebar chats
    var handleQuickSidebarChat = function () {
        var wrapper = $('.page-quick-sidebar-wrapper');
        var wrapperChat = wrapper.find('.page-quick-sidebar-chat .page-quick-sidebar-item');
        var wrapperChatBar = $('.users-chat-bar-wrapper', document);



        $(document).on('keyup', '#input_user_search', function(e) {
            var this_ = $(this);
            if(this_.val().length == '') {
                handlerUserLists();
            }
        });

        $(document).on('keypress', '#input_user_search', function(e) {
            var code = (e.keyCode) ? e.keyCode : e.which;
            if(code == 13) {
                handlerUserLists();
            }
        });

        var initChatSlimScroll = function () {
            var chatUsers = wrapper.find('.page-quick-sidebar-chat-users #users_list');
            var chatUsersHeight;

            chatUsersHeight = wrapper.height() - wrapper.find('.nav-justified > .nav-tabs').outerHeight();

            // chat user list 
            PECO.destroySlimScroll(chatUsers);
            chatUsers.attr("data-height", chatUsersHeight);
            PECO.initSlimScroll(chatUsers);

            var chatMessages = wrapperChat.find('.page-quick-sidebar-chat-user-messages');
            var chatMessagesHeight = chatUsersHeight - wrapperChat.find('.page-quick-sidebar-chat-user-form').outerHeight() - wrapperChat.find('.page-quick-sidebar-nav').outerHeight();

            // user chat messages 
            PECO.destroySlimScroll(chatMessages);
            chatMessages.attr("data-height", chatMessagesHeight);
            PECO.initSlimScroll(chatMessages);
        };

        initChatSlimScroll();
        PECO.addResizeHandler(initChatSlimScroll); // reinitialize on window resize

        $('body').on('click', '#chat_user_lists',  function () {
            var this_ = $(this);
            var chat_bar_cnt = $('.users-chat-bar-wrapper .users-chat-bar-lists').find('.chat-bar').length;
            if(chat_bar_cnt>=3){
                $('.users-chat-bar-wrapper .users-chat-bar-lists .chat-bar:first-child').remove();
                chat_bar_html(this_);
            }else {
                chat_bar_html(this_);
            }
        });

        $('body').on('click', '.chat-bar-name span', function(e) {
            e.preventDefault();
            var this_ = $(this);
            this_.closest('.chat-bar').toggleClass('open');
        });

        /*
        @TODO CLICK IN NAME LIST TOGGLE CHAT BAR OPEN
        $('body').on('click', '#chat_user_lists', function(e) {
            e.preventDefault();
            var this_ = $(this);
            $('body').find('.chat-bar#' + this_.attr('data-id')).toggleClass('open');
        });
        */


        $('body').on('click', '.chat-btn-remove', function(e) {
            e.preventDefault();
            var this_ = $(this);
            this_.closest('.chat-bar').remove();
        });

        var chat_in = function(data_id) {
            var msg = '';
            $.ajax({
                url: PECO.base_url() + 'messenger/getconversations',
                type: 'post',
                data: {'userid': data_id},
                dataType: 'json',
                async: false,
                cache: false,
            }).done(function(d) {
                msg = d.msg;
            });
            return msg;
        };



        var echo_person_voice = function(words){
            var TTS_URL = "http://api.ispeech.org/api/rest?apikey=ispeech-listenbutton-betauserkey&action=convert&voice=usenglishmale&text="+words
            echo_voice.src = TTS_URL;
            echo_voice.volume = 1;
            echo_voice.load();
            echo_voice.play();
        };

        var chat_echo_intro = function() {
            var echo_intro = '';
            echo_intro += '<li class="in post">';
            echo_intro += '<span class="body">';
            $.ajax({
                url: PECO.base_url() + 'messenger/getechointro',
                dataType: 'json',
                async: false,
                cache: false,
            }).done(function(d) {
                if(d.msg != '') {
                    if(d.online == true) {
                        echo_person_voice(d.txt);
                    }
                    echo_intro += '<h5 class="text-primary" style="margin: 0px 5px;">'+d.msg+'</h5>';
                }else{
                    echo_intro += '<h5 class="text-warning" style="margin: 0px 5px;">Query: Error 404!</h5>';
                }
            }).fail(function() {
                echo_intro += '<h5 class="text-danger" style="margin: 0px 5px;">PHP: Error 404!</h5>';
            });
            echo_intro += '</span>';
            echo_intro += '</li>';
            return echo_intro;
        };


        var  chat_bar_html = function(this_) {
            var chat_bar = '';
            var username = this_.find('h4').text();
            var data_id = this_.attr('data-id');
            var img_src = this_.find('.media-object').attr('src');
            var userstat = this_.attr('data-stat');

            var others = $('.users-chat-bar-wrapper .users-chat-bar-lists');
            if (!$('.users-chat-bar-wrapper .users-chat-bar-lists').find('#' + data_id).length) {

                var chat_history = (typeof data_id != "undefined") ? chat_in(data_id) : chat_echo_intro();

                chat_bar += '<div class="chat-bar open '+userstat+' animated slideInUp fast" id="' + data_id + '">' +
                            '<a href="javascript:;" class="chat-bar-name">' +
                            '<img class="chat-bar-image tooltips" title="Away" src="'+img_src+'" />' +
                            '<span><i class="fa fa-circle fa-online-stat"></i> ' + username +'</span>'+
                            '<i class="fa  fa-times chat-btn-remove pull-right btn-close"></i>' +
                            '</a>' +
                            '<div class="chat-contents " >' +
                            '<ul class="chats scroller" style="height: 220px;" data-always-visible="1" data-rail-visible1="1">' +
                            chat_history+
                            '</ul>' +
                            '<div class="send-message-form">' +
                            '<div class="input-group send-message-form-input">' +
                            '<span class="input-group-btn">' +
                            '<button id="bth_add" class="btn btn-default btn-sm"><i class="fa fa-plus"></i></button>' +
                            '</span>' +
                            '<input class="form-control input-xs" placeholder="Type a message.."/>' +
                            '</div>' +
                            '</div>' +
                            '</div>';


                $('.users-chat-bar-wrapper .users-chat-bar-lists').append(chat_bar);
            }
            var chat_container = $('.scroller');
            PECO.initSlimScroll(chat_container);

            var getLastPostPos = function() {
                var height = 0;
                chat_container.find(".post").each(function() {
                    height = height + $(this).outerHeight();
                });
                return height;
            };

            chat_container.slimScroll({
                scrollTo: getLastPostPos(chat_container)
            });

            $('.tooltips').tooltip();

            handlerDraggableChatBar();
        };


        var handleChatMessagePost = function (this_) {
            var wrapper_chat_bar = this_.closest('.chat-contents');
            var wrapper_chat_box = wrapper_chat_bar.closest('.chat-bar');
            var chat_user_id = wrapper_chat_box.attr('id');


            var chatContainer = $('.chats', wrapper_chat_bar);

            var text = this_.val();
            if (text.length === 0) {
                return;
            }

            $('li#empty_chat', wrapper_chat_bar).remove();

            $.ajax({
                url: PECO.base_url() + "messenger/postmessage",
                type: 'POST',
                dataType: 'JSON',
                data: {'messages': text, 'userid': chat_user_id}
            }).done(function (d) {
                // @TODO create delivered message icon
                // handle post
                var time = new Date();
                var message = preparePost('out', d.time, d.name, d.avatar, d.text);
                message = $(message);
                chatContainer.append(message);
                chatContainer.slimScroll({
                    scrollTo: getLastPostPos(chatContainer)
                });
                this_.val("");

                if(d.echo == true) {
                    handleEchoResponse(chatContainer, text, getLastPostPos);
                }
            });



        };

        wrapperChatBar.on('keypress', '.send-message-form-input input', function (e) {
            var this_ = $(this);
            if (e.which == 13) {
                handleChatMessagePost(this_);
                return false;
            }
        });
    };

    // Handle Echo response
    var handleEchoResponse = function(chatContainer, text, getLastPostPos)  {

        // ECHO RESPONSE ONLY
        // simulate reply
        setTimeout(function () {
            $.ajax({
                url: PECO.base_url() + "messenger/echo_response",
                type: 'POST',
                dataType: 'JSON',
                data: {'messages': text}
            }).done(function (data) {
                PECO.initPlaySound('pling');
                var time = new Date();
                if (data['response'] == "logout_user") {
                    var message = preparePost('in', (time.getHours() + ':' + time.getMinutes()), "ECHO", PECO.base_url() + 'assets/global/img/admin_pic.png', 'You will be logout, good bye!');
                    message = $(message);
                    chatContainer.append(message);
                    chatContainer.slimScroll({
                        scrollTo: getLastPostPos(chatContainer)
                    });
                    setTimeout(function () {
                        $("#btn-logout").trigger("click");
                    }, 2000);
                } else {
                    var message = preparePost('in', (time.getHours() + ':' + time.getMinutes()), "ECHO", PECO.base_url() + 'assets/global/img/admin_pic.png', data['response']);
                    message = $(message);
                    chatContainer.append(message);
                    chatContainer.slimScroll({
                        scrollTo: getLastPostPos(chatContainer)
                    });
                }
            }).fail(function () {
                console.log('Unable to find the php file. Message: ' + text);
            });

        }, 3000);
    };


    var handlerDraggableChatBar = function() {
        var userChatBar = $('.chat-bar.open', document);
        userChatBar.draggable({
            handle: ".chat-bar-image",
            start: function(e, ui) {
                ui.helper.addClass("chat-bar-dragged");
            },
            drop: function(e, ui) {
                ui.helper.removeClass("chat-bar-dragged");
            },
        });

        $(document).on('click', '.chat-bar-image', function(i, e) {
            var $div = $(this).parent();
            revertDraggable($div);
            alert('clicked!');
        });
    };

    var revertDraggable = function($selector) {
        $selector.each(function() {
            var $this = $(this),
                position = $this.data("originalPosition");

            if (position) {
                $this.animate({
                    left: position.left,
                    top: position.top
                }, 500, function() {
                    $this.data("originalPosition", null);
                });
            }
        });
    };


    var preparePost = function(dir, time, name, avatar, message) {
        var tpl = '';
        tpl += '<li class="'+dir+' post">';
        tpl += '<img class="avatar" alt="" src="' + avatar +'"/>';
        tpl += '<div class="message">';
        tpl += '<span class="arrow"></span>';
        tpl += '<a href="#" class="name">'+name+'</a><br>';
        tpl += '<span class="datetime">' + time + '</span>';
        tpl += '<span class="body">' + message + '</span>';
        tpl += '</div>';
        tpl += '</li>';
        return tpl;
    };


    var getLastPostPos = function(chatContainer) {
        var height = 0;
        chatContainer.find(".post").each(function() {
            height = height + $(this).outerHeight();
        });
        return height;
    };

    // Handles quick sidebar tasks
    var handleQuickSidebarAlerts = function () {
        var wrapper = $('.page-quick-sidebar-wrapper');
        var wrapperAlerts = wrapper.find('.page-quick-sidebar-alerts');

        var initAlertsSlimScroll = function () {
            var alertList = wrapper.find('.page-quick-sidebar-alerts-list');
            var alertListHeight;

            alertListHeight = wrapper.height() - wrapper.find('.nav-justified > .nav-tabs').outerHeight();

            // alerts list 
            PECO.destroySlimScroll(alertList);
            alertList.attr("data-height", alertListHeight);
            PECO.initSlimScroll(alertList);
        };

        initAlertsSlimScroll();
        PECO.addResizeHandler(initAlertsSlimScroll); // reinitialize on window resize
    };

    // Handles quick sidebar settings
    var handleQuickSidebarSettings = function () {
        var wrapper = $('.page-quick-sidebar-wrapper');
        var wrapperAlerts = wrapper.find('.page-quick-sidebar-settings');

        var initSettingsSlimScroll = function () {
            var settingsList = wrapper.find('.page-quick-sidebar-settings-list');
            var settingsListHeight;

            settingsListHeight = wrapper.height() - wrapper.find('.nav-justified > .nav-tabs').outerHeight();

            // alerts list 
            PECO.destroySlimScroll(settingsList);
            settingsList.attr("data-height", settingsListHeight);
            PECO.initSlimScroll(settingsList);
        };

        initSettingsSlimScroll();
        PECO.addResizeHandler(initSettingsSlimScroll); // reinitialize on window resize
    };

    var handleChatAdd = function() {
        $(document).on('click', '.chat-bar #bth_add', function(e) {

        });
    };
	


    return {

        init: function () {
            //layout handlers
            handleQuickSidebarToggler(); // handles quick sidebar's toggler
            handleQuickSidebarChat(); // handles quick sidebar's chats
            handleQuickSidebarAlerts(); // handles quick sidebar's alerts
            handleQuickSidebarSettings(); // handles quick sidebar's setting
            hadleTabclick();
            handleChatAdd();
            handlerDraggableChatBar();
        },
		popUp: function(id, name) {
			register_popup(id, name);
		}
    };

}();