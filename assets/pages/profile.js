/**
 * Created by SE on 0004, May 4, 2017.
 */
var PROFILE = function() {

    PECO.getSweetAlert();
    PECO.getHighlightsPlugin();
    PECO.getSelect2Plugins();
    PECO.getiCheckPlugin();

    var userroles = $(document).find('#userroles');
    var evaluationtable = $(document).find('#evaluationtable');

    var init_user_roles = function(dataid){

        $.ajax({
            url:PECO.base_url()+'settings/getuserroles',
            type:'post',
            data:{"dataid":dataid},
            dataType:'json'
        }).done(function (d) {
            userroles.dataTable().empty();
            userroles.dataTable({
                bDestroy: true,
                bPaginate: true,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                bProcessing: true,
                aaData: d.roleslist,
                aoColumns: [
                    {data: 'num'},
                    {data: 'code'},
                    {data: 'descs'},
                    {data: 'control',sClass:'checkboxrole'}
                ],
                // language: PECO.DTEmptyMessage(),
                searchHighlight: true,
                fnRowCallback: function(nRow, aData) {
                    PECO.iCheckRow($('.icheck', nRow),'minimal', 'blue');

                }
            });
            // PECO.initDTNicescroller();
        }).fail(function () {
            PECO.phpError();
        });
    };

    var query_roles_update = function(userid, roleid, types) {
        swal({
            title: "Are you sure?",
            text: 'This role will updated',
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
                    url:PECO.base_url()+'settings/updaterole',
                    type:'post',
                    data:{"userid":userid,"roleid":roleid,"types":types},
                    dataType:'json'
                }).done(function (d) {
                    swal(d.msg, "Role!", d.func);
                    init_user_roles(userid);
                }).fail(function () {
                    PECO.phpError();
                });
            }else{
                init_user_roles(userid);
            }
        });
    };


    var getevaluationselection = function(userid){
        $.ajax({
            url:PECO.base_url()+'hris/getevaluationselections',
            type:'post',
            data:{"empid" : userid , "evaltype" : 1,"ratedby" : userid},
            dataType:"json"
        }).done(function (data) {

            var class1 = data.sClass2 + 'rate_0';
            var class2 = data.sClass2 + 'rate_1';
            var class3 = data.sClass2 + 'rate_2';
            var class4 = data.sClass2 + 'rate_3';
            var class5 = data.sClass2 + 'rate_4';

            evaluationtable.dataTable().empty();
            evaluationtable.dataTable({
                bDestroy: true,
                bPaginate: false,
                bFilter: false,
                bInfo: true,
                bStateSave: true,
                bProcessing: true,
                aaData: data.selectionsdata,
                aoColumns: [
                    {data: 'num', sClass:'text-info'},
                    {data: 'persontraits', sWidth:'18%' , sClass:'text-info'},
                    {data: 'desc', sWidth:'20%' , sClass:'text-info'},
                    {data: 'unsatisfactory', sClass: class1, sWidth:'15%'},
                    {data: 'somedeficiencies', sClass: class2, sWidth:'15%'},
                    {data: 'satisfactory', sClass: class3, sWidth:'15%'},
                    {data: 'exceptional', sClass: class4, sWidth:'15%'},
                    {data: 'clearlyoutstanding', sClass: class5},
                    {data: 'comments' , sWidth:'2%'}
                ],
                // language: PECO.DTEmptyMessage(),
                searchHighlight: true,
                fnRowCallback: function(nRow, aData, Index) {
                    $('a.popovers', nRow).popover({
                        'html': true,
                        'animate': true,
                        'title' : '<span class="text-info"><strong>Justification</strong></span>'+
                            '<button type="button" id="close" class="close" onclick="$(&quot;a.popovers&quot;).popover(&quot;hide&quot;);">&times;</button>',
                    });

                    if(aData.voterate == 0){
                        $('td:eq(3)', nRow).addClass(aData.colors);
                    }else if(aData.voterate == 1){
                        $('td:eq(4)', nRow).addClass(aData.colors);
                    }else if(aData.voterate == 2){
                        $('td:eq(5)', nRow).addClass(aData.colors);
                    }else if(aData.voterate == 3){
                        $('td:eq(6)', nRow).addClass(aData.colors);
                    }else if(aData.voterate == 4){
                        $('td:eq(7)', nRow).addClass(aData.colors);
                    }


                }
            });
        }).fail(function () {
            PECO.phpError();
        });
    };

    var init_profile = function(userid) {

        getevaluationselection(userid);

        $('.popovers').popover({ placement: 'bottom', trigger: 'hover', html: true, animate: true });

        userroles.on('ifChecked', '.icheck', function(e){
            var this_dataid = $(this).attr('data-id');
            query_roles_update(userid, this_dataid, 1);
        }).on('ifUnchecked', '.icheck', function(e) {
            var this_dataid = $(this).attr('data-id');
            query_roles_update(userid, this_dataid, 0);
        });

        $('#user_stat').on('click', 'a', function(e){
            e.preventDefault();
            var this_ = $(this);
            var this_id = this_.attr('data-id');
            init_users(this_id);
        });

        $('#frm_upd_acct').submit(function(e){
            e.preventDefault();
            var form = $(this);
            $.ajax({
                url: form.attr('action'),
                type: form.attr('method'),
                data: form.serialize(),
                dataType: 'json'
            }).done(function(d){
                console.log(d);
                PECO.initAlerts(d.msg, 'Account Update', d.func);
            }).fail(function(){
                PECO.phpError();
            });
        });

        $('body').find('[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            var target = $(e.target).attr("href");
            if (target == '#message') {
                init_inbox();
            }

            if (target == '#all_notifications') {
                init_notification();
            }

            if (target == '#activities') {
                init_notification();
            }
        });
    };

    var init_notification = function() {
        var tbl = $('#tbl_notification_all', document);
        $.ajax({
            url: PECO.base_url() + 'systems/getusernotifications',
            type: 'post',
            data: {},
            dataType: 'json',
            beforeSend: function() {
                PECO.DTphpLoading(tbl, 'Loading all notifications....');
            }
        }).done(function(d) {
            tbl.DataTable({
                bDestroy: true,
                bPaginate: true,
                bFilter: true,
                bInfo: true,
                aaData: d.list,
                bSort: true,
                scrollY: '',
                aoColumns: [
                    {"data": "seen", sWidth: '', sClass: 'text-default text-align-center'},
                    {"data": "title", sWidth: '', sClass: 'text-primary'},
                    {"data": "code", sWidth: '', sClass: 'bold'},
                    {"data": "desc", sWidth: '', sClass: ''},
                    {"data": "from", sWidth: '', sClass: ''},
                    {"data": "remarks", sWidth: '', sClass: ''},
                    {"data": "control", sWidth: '70px', sClass: ''},
                ]
            });
        }).fail(function() {
            PECO.DTphpError(tbl, 'Error Notification PHP');
        });
    };



    var init_inbox = function() {
        $('.inbox-nav').on('click', 'li', function(e){
            $('.inbox-nav').find('li').removeClass('active');
            e.preventDefault();
            var this_ = $(this);
            this_.addClass('active');
            var inbox_nav_title = $('a', this_).attr('data-title');
            $('.inbox-header').find('h1').html(inbox_nav_title);
        });

        $('#tbl_inbox').dataTable();
    };

    var updatescoreboard = function (dataid) {
        var empid = dataid;
        var eval = 1;
        $.ajax({
            url:PECO.base_url()+'hris/fetchempscore',
            type:'post',
            data:{"empid":empid , "eval":eval},
            dataType:'json'
        }).done(function (d) {
            $(document).find('#scoreboardhtml').html(d.html);
            $(document).find('#score').html(d.score);
            $('.your-score').tooltip();
        }).fail(function () {
            PECO.phpError();
        });
    };

    var cast_vote = function(questid, vote, this_, dataid) {
        var empid = dataid;
        var eval = 1;

        $.ajax({
            url:PECO.base_url()+'hris/castvote',
            type:'post',
            data:{"empid" : empid , "evaluationtype" : eval , "questionaireid" : questid , "voterate" : vote},
            dataType: 'json',
            async: false,
            cache: false
        }).done(function (data) {
            this_.closest('tr').find('td.rate').removeClass("danger");
            this_.addClass("danger");
        }).fail(function () {
            PECO.phpError();
        });

    };

    var init_events = function (dataid) {

        $(document).on('submit','#submitevaluation',function (e) {
            e.preventDefault();
            var this_ = $(this);
            $.ajax({
                url:this_.attr("action"),
                type:this_.attr("method"),
                data:this_.serialize(),
                dataType:'json'
            }).done(function (data) {
                PECO.initAlerts(data.msg , "PECO" , data.func);
                if(data.qry == true){
                    //location.reload();
                    getevaluationselection(dataid);
                }
            });
        });

        $(document).on('submit','#frm_justification_entry',function (e) {
            e.preventDefault();
            var this_ = $(this);
            $.ajax({
                url:this_.attr("action"),
                type:this_.attr("method"),
                data:this_.serialize(),
                dataType:'json'
            }).done(function (data) {
                PECO.initAlerts(data.msg , "PECO" , data.func);
            });
        });

        $(document).on('click' , '.popovers' , function (e) {
            $(document).find('.popovers').not($(this)).popover('hide');
        });

        evaluationtable.on('click', 'td.rate', function(e){
            var this_ = $(this);
            var quest_id = this_.closest('tr').find('input.questid').val();
            if(this_.hasClass('rate_0')) {
                var vote = 0;
                this_.addClass(cast_vote(quest_id, vote, this_ , dataid));
            }

            if(this_.hasClass('rate_1')) {
                var vote = 1;
                cast_vote(quest_id, vote, this_, dataid);
            }

            if(this_.hasClass('rate_2')) {
                var vote = 2;
                cast_vote(quest_id, vote, this_, dataid);
            }

            if(this_.hasClass('rate_3')) {
                var vote = 3;
                cast_vote(quest_id, vote, this_, dataid);
            }

            if(this_.hasClass('rate_4')) {
                var vote = 4;
                cast_vote(quest_id, vote, this_, dataid);
            }
            updatescoreboard(dataid);
        });
    };

    var accountSignature = function () {
        var filedropzone = $(document).find('input[type=file]');

        filedropzone.fileinput({
            uploadAsync: true,
            showBrowse: true,
            barowseOnZoneClick: true,
            showPreview: false,
            uploadExtraData: function (d) {

            },
        }).on('fileuploaded' , function (event, data, previewId, index) {

            handler_file_explorer(dataid);
            filedropzone.fileinput('clear');

        }).on('fileerror' , function (event, data, previewId, index) {

            var form = data.form, files = data.files, extra = data.extra,
                response = data.response, reader = data.reader;
            PECO.initAlerts(response.msg, 'Upload File', 'error', false, false);
            filedropzone.fileinput('clear');

        });

        filedropzone.on('filebatchuploadsuccess', function(event, data, previewId, index) {
            alert("test");
            var form = data.form, files = data.files, extra = data.extra,
                response = data.response, reader = data.reader;
            PECO.initAlerts(response.msg, 'Upload File', 'error', false, false);
            filedropzone.fileinput('clear');
        });
    };

    return {
        init: function(dataid) {
            init_profile(dataid);
            init_user_roles(dataid);
            init_events(dataid);
        }
    }
}();
