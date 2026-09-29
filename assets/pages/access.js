var ACCESS = function() {
    PECO.getSweetAlert();
    PECO.getHighlightsPlugin();
    PECO.getSelect2Plugins();

    var tbl_users_list = $('#users_list');
    var usersgrouptable = $('#usersgrouptable' , document);

    var init_access = function(){
        $(document).on('change','#leguname' , function () {
            var this_ = $(this);
            var dataid = this_.attr("data-id");
            var telcode = this_.val();
            $.ajax({
                url:PECO.base_url()+'query/updatelegacyusername',
                type: 'post',
                data: {
                    'dataid' : dataid,
                    'telcode' : telcode
                },
                dataType: 'json'
            }).done(function(d){
                PECO.initAlerts('Legacy Username Updated.' , 'PECO.net' , 'success');
            }).fail(function () {
                PECO.phpError();
            });
        });
        $(document).on('click','#deletebtn',function(e){
            e.preventDefault();
            var this_ = $(this);
            var dataid = this_.attr('data-id');
            swal({
                title: "Are you sure you want to delete this user?",
                text: 'Delete User',
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
                        url: PECO.base_url() + "settings/deleteuser",
                        method: "post",
                        dataType: "json",
                        data: {'dataid': dataid}
                    }).done(function (d) {
                        swal(d.title, d.msg, d.func);
                    });
                }
            });
        });

        PECO.select2Basic($("#select2rolesupload"), 'admin/getuserrole', 'Select Role', true, false);

        $('#datafile', document).fileinput({
            //uploadUrl: url, // server upload action

            uploadAsync: true,
            showBrowse: false,
            browseOnZoneClick: true,
            uploadExtraData: function (d) {
                return {
                    roleid: $('#select2rolesupload', document).val(),
                };
            },

            previewFileIcon: '<i class="fa fa-file"></i>',
            allowedPreviewTypes: null, // set to empty, null or false to disable preview for all types
            previewFileIconSettings: {
                'docx': '<i class="fa fa-file-word-o text-primary"></i>',
                'xlsx': '<i class="fa fa-file-excel-o text-success"></i>',
                'pptx': '<i class="fa fa-file-powerpoint-o text-danger"></i>',
                'jpg': '<i class="fa fa-file-photo-o text-warning"></i>',
                'pdf': '<i class="fa fa-file-pdf-o text-danger"></i>',
                'zip': '<i class="fa fa-file-archive-o text-muted"></i>',
                'dbf': '<i class="fa fa-database"></i>',
            }

        }).on('fileuploaded', function(event, data, previewId, index) {
            if(data.response.list) {
                setTimeout(function() {
                    tbl_upload_users_result(data.response.list);
                }, 500);
            }
        });



        $('#user_stat').on('click', 'a', function(e){
            e.preventDefault();
            var this_ = $(this);
            var this_id = this_.attr('data-id');
            init_users(this_id);
        });

        $('body').on('click', '.roles-pop li a', function(e){
            e.preventDefault();
            $.this_ = $(this);
            $.ajax({
                url: $.this_.attr('href'),
                type: 'POST',
                data: {'id': $.this_.attr('data-id') },
                dataType:"json",
            }).done(function(data){
                if(data['q']==true){
                    $.this_.closest('li').fadeOut('fast');
                    PECO.initAlerts(data['msg'], 'Update Roles', 'success');
                }
            }).fail(function(){
                console.log('PHP NOT FOUND!');
            });
        });

        $('#username').keyup(function(e){
            e.preventDefault();
            $.this_ = $(this);
            if($.this_.val().length>=4){
                $.ajax({
                    url: PECO.base_url() + 'query/checkusername',
                    data: {'username': $.this_.val()},
                    type: 'post',
                    dataType: 'json'
                }).done(function(data){
                    if(data['q']==true){
                        $.this_.closest('.form-group').addClass('has-error').append('<span class="help-block">'+data['m']+'</span>');
                        setTimeout(function(){
                            $.this_.closest('.form-group').find('.help-block').fadeOut();
                            $.this_.val('');
                        },4000);
                        $.this_.closest('form').find('#add_user').addClass('disabled');
                    }else{
                        $.this_.closest('.form-group').removeClass('has-error').addClass('has-success').find('.help-block').remove();
                        $.this_.closest('form').find('#add_user').removeClass('disabled');
                    }
                }).fail(function(){
                    console.log('Cant find specific URL');
                });
            }
        });

        $('#register-form').submit(function(e){
            e.preventDefault();
            $.this_ = $(this);
            $.ajax({
                url: $.this_.attr('action'),
                type: $.this_.attr('method'),
                data: $.this_.serialize(),
                dataType: 'json',
            }).done(function(data){
                if(data['qry']==true){
                    PECO.initAlerts(data.msg, 'Register User', 'success');
                    clearInputs();
                    init_users();
                }else{
                    PECO.initAlerts(data.msg, 'Register User', 'warning');
                }
            }).fail(function(){
                console.log('Cant find specific URL');
            });
        });

        $('.userrole-form').submit(function(e){
            e.preventDefault();
            $.this_ = $(this);
            $.ajax({
                url: $.this_.attr('action'),
                type: $.this_.attr('method'),
                data: $.this_.serialize(),
                dataType: 'json',
            }).done(function(data){
                PECO.initAlerts(data['msg'], 'Alert', 'success');
                init_users();
            }).fail(function(){
                console.log('Cant find specific URL');
            });
        });

        PECO.select2BasicMult($("#selectroles", document), 'admin/getuserrole', false, true);

        $("#cancel").click(function(){
            clearInputs();
        });

        $('ul.dropdown-menu.mega-dropdown-menu').on('click', function(event) {
            event.stopPropagation();
        });
    };

    var clearInputs = function(){
        $("#firstname").val('');
        $("#lastname").val('');
        $("#username").val('');
        $("#register_password").val('');
        $("#rpassword").val('');
        $("#status").val('');
    };


    var init_users = function(stat) {
        var stat = (stat) ? stat : false;
        var dept = $('#select2dept', document).val();



        $.ajax({
            url: PECO.base_url() + 'settings/userlists',
            type: 'post',
            dataType: 'json',
            data: {'stat': stat, 'dept': dept},
            beforeSend: function() {
                PECO.DTphpLoading(tbl_users_list, 'Loading users...');
            }
        }).done(function(d) {
            var a = 0;
            PECO.getViewPort().width < PECO.getResponsiveBreakpoint("md") ? $(".page-header").hasClass("page-header-fixed-mobile") && (a = $(".page-header").outerHeight(!0)) : $(".page-header").hasClass("navbar-fixed-top") ? a = $(".page-header").outerHeight(!0) : $("body").hasClass("page-header-fixed") && (a = 64);

            //tbl_users_list.dataTable().empty();
            var oTable = tbl_users_list.DataTable({
                bDestroy: true,
                bPaginate: false,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                bProcessing: true,
                aaData: d.list,
                fixedHeader: {header: true, footer: true, headerOffset: 100},
                aoColumns: [
                    {data: 'sysid', sClass: 'number text-danger', sWidth: ''},
                    {data: 'username', sClass: ' text-info', sWidth: ''},
                    {data: 'legacyuname', sClass: ' text-info', sWidth: ''},
                    {data: 'firstname', sClass: ' text-bold', sWidth: ''},
                    {data: 'roles', sClass: '', sWidth: ''},
                    {data: 'activity', sClass: '', sWidth: ''},
                    {data: 'created', sClass: '', sWidth: ''},
                    {data: 'updated', sClass: '', sWidth: ''},
                    {data: 'status', sClass: 'text-primary', sWidth: ''},
                    {data: 'control', sClass: '', sWidth: ''},
                ],
                "order": [[0, "desc"]],
                language: PECO.DTEmptyMessage(),
                //searchHighlight: true,
                fnRowCallback: function(nRow, data) {
                    PECO.popOverRow($('.popovers', nRow), true, true, 'popover-info');
                    PECO.select2Basic($('#leguname' , nRow) , 'query/select2pecoappsusers' , 'Legacy Username...',false,false,data.telcode);
                }
            });

            $(document).on( 'click', '#enable', function () {
                alert('enable');
                oTable.fixedHeader.enable();
            } );

            $(document).on( 'click', '#disable', function () {
                oTable.fixedHeader.disable();
                alert('disable');
            } );

            PECO.initDTNicescroller();
        });

        $(window).scroll(function() {
            if($(window).scrollTop() > 0) {
                console.log('Scrolling top')
            } else {
                console.log('Scrolling down')
            }
        });
    };

    var tbl_upload_users_result = function(data) {
        var tbl_users_uploads = $('#tbl_users_uploads', document);
        tbl_users_uploads.dataTable().empty();
        tbl_users_uploads.dataTable({
            bDestroy: true,
            bPaginate: false,
            bFilter: true,
            bInfo: true,
            bStateSave: true,
            bProcessing: true,
            aaData: data,
            aoColumns: [
                {data: 'userid'},
                {data: 'name'},
                {data: 'username'},
                {data: 'password'}
            ]
        });
        PECO.initDTNicescroller();
    };

    var init_usersgroup = function(){
        fetchusergroupdate();
    };
    var fetchusergroupdate = function(){
        $.ajax({
            url:PECO.base_url()+'settings/fetchusergroupdata',
            type:'post',
            dataType:'json'
        }).done(function (d) {
            populateusergrouptable(d);
        }).fail(function () {
            PECO.phpError();
        });
    };
    var populateusergrouptable = function(d){
        usersgrouptable.dataTable().empty();
        usersgrouptable.dataTable({
            bDestroy: true,
            bPaginate: true,
            bFilter: true,
            bInfo: true,
            bStateSave: true,
            bProcessing: true,
            aaData: d.usergroupdata,
            aoColumns: [
                {data: 'id'},
                {data: 'firstname'},
                {data: 'lastname'}
            ]
        });
        PECO.initDTNicescroller();
    };

    return {
        init: function() {
            init_access();
            init_users();
            init_usersgroup();
        }
    }
}();
