/**
 * Created by SE on 0004, May 4, 2017.
 */
var MODULES = function(){
    PECO.getHighlightsPlugin();
    var tbl_modulemain = $('#tbl_module_main');
    var tbn_levels = $('#btn_levels');
    var init_modulemain = function() {
        init_modulemain_tbl(1);
        //init_nav_sub();

        tbn_levels.on('click', 'button', function(e) {
            $('button', tbn_levels).removeClass('active');
            e.preventDefault();
            var this_ = $(this);
            var level = this_.attr('data-id');
            this_.addClass('active');
            init_modulemain_tbl(level);
            //init_nav_sub();
        });

        PECO.dtSubDetails(tbl_modulemain, 'settings/moduleinfo');


        /*$('body').on('submit', '.frm_add_nav', function(e){
            e.preventDefault();
            var form = $(this);
            $.ajax({
                url: form.attr('action'),
                type: form.attr('method'),
                data: form.serialize(),
                dataType: 'json',
            }).done(function(data) {
                console.log(data);
                PECO.initAlerts(data.msg, 'Adding Navigation', data.func);
            }).fail(function(){
               PECO.phpError();
            });
        });*/

    };

    /*var init_nav_sub = function() {
        tbl_modulemain.on('click', '#btn-expand', function() {
            var this_ = $(this);
            var id = this_.attr('data-id');
            $.ajax({
                url: PECO.base_url() + 'settings/getsubnavs',
                type: 'post',
                data: {'id': id},
                dataType: 'json',
            }).done(function(data) {
                console.log(data);
                setTimeout(function(){
                    //wait_select2_parent($('#this_parent_'+id, document), data);

                    $('#this_class_'+id, document).select2({
                        formatResult: formatSelect2result, // omitted for brevity, see the source of this page
                        formatSelection: formatSelect2result, // omitted for brevity, see the source of this page
                    });

                    PECO.select2icons($('#this_icons_'+id, document));
                    PECO.select2icons($('#module_icon', document));

                    PECO.select2_scroller();
                }, 200);
            });
        });
    };*/

    var formatSelect2result = function (data) {
        var d = data.text.split(' - ');
        var text2 = (d[1]) ? d[1] : '';
        return '<p style="display: inline-block; width: 100%; margin: 0px 0px;">' +
            '<span style="display: inline-block; width: 10%;">' +
            '<i class="fa fa-circle text-'+d[0]+'"></i>' +
            '</span>' +
            '<span style="display: inline-block; width: 90%; padding-left: 10px;">'+ text2 +'</span>' +
            '</p>';
    };

    var wait_select2_parent = function(el, data){
        if(el.length){
            el.select2({
                placeholder: "Select...",
                allowClear: true,
                data: data
            });
            PECO.select2_scroller();
        }else{
            wait_select2_parent(el, data);
        }
    };

    var init_modulemain_tbl = function(level) {
        var level = (level) ? level : false;
        $.ajax({
            url: PECO.base_url() + 'settings/getmodulesmain',
            type: 'POST',
            dataType: 'json',
            data: {'level': level}
        }).done(function (data) {
            console.log(data);
            tbl_modulemain.dataTable().empty();
            tbl_modulemain.dataTable({
                bDestroy: true,
                bPaginate: true,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                bProcessing: true,
                aaData: data.list,
                aoColumns: [
                    { "data": "expand", sWidth: '10px', sClass: 'hidden-print' },
                    { "data": "codes", sWidth: '', sClass: 'text-bold' },
                    { "data": "names", sWidth: '', sClass: 'text-info' },
                    { "data": "descs", sWidth: '', sClass: '' },
                    { "data": "icons", sWidth: '', sClass: '' },
                    { "data": "stats", sWidth: '', sClass: '' },
                    { "data": "control", sWidth: '', sClass: '' },
                ],
                searchHighlight: true,
            });
        });
    };

    var module_maintenance = function (moduleid) {
        $('#module_details input',document).attr('disabled',true);

        $('#btn_update_module',document).on('click',function () {
            var this_ = $(this);
            if (this_.hasClass('update')) {
                $('#btn_update_module_cancel',document).removeClass('hidden');
                this_.removeClass('update').addClass('save btn-primary');
                this_.html('<i class="fa fa-save"></i> Save');
                $('#module_details input',document).attr('disabled',false);
            } else {
                $('#btn_update_module_cancel',document).addClass('hidden');
                this_.html('<i class="far fa-edit"></i> Update');
                this_.removeClass('save btn-primary').addClass('update');
                $('#module_details input',document).attr('disabled',true);
            }
        });

        $('#btn_update_module_cancel',document).on('click',function () {
            var this_ = $(this);
            this_.addClass('hidden');
            $('#btn_update_module',document).html('<i class="fa fa-edit"></i> Update');
            $('#btn_update_module',document).removeClass('save btn-primary').addClass('update');
            $('#module_details input',document).attr('disabled',true);
        });

        $('#select_module_class_' + moduleid, document).select2({
            formatResult: formatSelect2result, // omitted for brevity, see the source of this page
            formatSelection: formatSelect2result, // omitted for brevity, see the source of this page
        });


        $('#frm_add_nav',document).on('submit',function (e) {
            e.preventDefault();
            var this_ = $(this);

            $.ajax({
                url: this_.attr('action'),
                type: this_.attr('method'),
                dataType: 'json',
                data: this_.serialize(),
            }).done(function (d) {
                PECO.initAlerts(d.msg,'Add New Sub Menu',d.func);
                dt_sub_modules(moduleid);
            }).fail(function () {

            });
        });

        PECO.select2icons($('#select_module_icon_' + moduleid,document));
    };
    var dt_sub_modules = function (moduleid) {
        var tbl_sub_modules = $('#tbl_sub_modules',document);
        $.ajax({
            url:  PECO.base_url() + 'settings/dtmoduleslist',
            type: 'post',
            dataType: 'json',
            data: {
                moduleid : moduleid,
            }
        }).done(function (d) {
            tbl_sub_modules.dataTable().empty();
            tbl_sub_modules.dataTable({
                bDestroy: true,
                bPaginate: true,
                bFilter: false,
                bInfo: false,
                bStateSave: true,
                bProcessing: true,
                aaData: d.list,
                aoColumns: [
                    {"data": "num", sClass: 'number', sWidth: ''},
                    {"data": "codes", sClass: "text-center", sWidth: '15%'},
                    {"data": "names", sClass: "", sWidth: ''},
                    {"data": "desc", sClass: "", sWidth: '25%'},
                    {"data": "icon", sClass: "text-center", sWidth: ''},
                    {"data": "status", sClass: "", sWidth: ''},
                ],
                language: {
                    "emptyTable": '<i class="far fa-dizzy fa-2x text-danger"></i> No record found.'
                },
            });
        }).fail(function () {
            PECO.DTphpError(tbl_sub_modules,'PHP Error!');
        });
    };


    return {
        init: function(){
            init_modulemain();
        },
        maintenance: function (moduleid) {
            dt_sub_modules(moduleid);
            //alert('test');
            module_maintenance(moduleid);
        }
    }
}();