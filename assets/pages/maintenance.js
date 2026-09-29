var MAIN = function () {
    PECO.getSweetAlert();
    PECO.getHighlightsPlugin();
    PECO.getiCheckPlugin();

    var tbl_parameters = $('#tbl_parameters', document);
    var tbl_attributes = $('#tbl_attributes', document);
    var tbl_roles = $('#roles_list', document);
    var tbl_nav = $('#nav_list', document);

    var main_init = function () {
        var action_option = $('#action_option', document);
        var parameter_pages = $('#parameter_pages', document);

        tbl_parameters_init();

        $(document).on('click', '#filter_btn a', function(e) {
            tbl_parameters_init();
        });


        $(document).on('submit', '#frm_add_parementer', function(e) {
            e.preventDefault();
            var form = $(this);
            $.ajax({
                url: form.attr('action'),
                type: form.attr('method'),
                data: form.serialize(),
                dataType: 'json'
            }).done(function(d){
                if(d.qry==true) {
                    tbl_parameters_init();
                }
                PECO.initAlerts(d.msg, d.title, d.func);
            }).fail(function(){
                PECO.phpError();
            });
        });

        PECO.select2icons($('#add_icon', document));
        $('tbody #add_bgcolor, tbody #add_txtcolor', document).minicolors({
            control: "hue",
            letterCase: "uppercase",
            theme: "bootstrap",
            position: "top"
        });

        $(document).on('shown.bs.tab', 'a[data-toggle="tab"]', function (e) {
            var target = $(e.target).attr("href");
            if (target == '#parameters') {
                tbl_parameters_init();
            }
        });

        tbl_parameters.on('change', 'tbody tr td.colortxt input, tbody tr td.colorbg input, tbody tr td.icons input', function(e) {
           e.preventDefault();
           var this_ = $(this);
           var this_val = this_.val();
           var this_tr = this_.closest('tr');
           var this_id = $('td', this_tr).eq(0).text();
           var this_class = this_.closest('td').attr('class');
           $.ajax({
               url: PECO.base_url() + 'systems/updateparameterrow',
               type: 'post',
               data: {'col': this_class, 'val': this_val, 'id': this_id},
               dataType: 'json'
           });
        });

        tbl_parameters.on('keyup', 'tbody tr td.codes input, tbody tr td.names input, tbody tr td.desc input', function(e) {
           e.preventDefault();
           var this_ = $(this);
           var this_val = this_.val();
           var this_tr = this_.closest('tr');
           var this_id = $('td', this_tr).eq(0).text();
           var this_class = this_.closest('td').attr('class');
           $.ajax({
               url: PECO.base_url() + 'systems/updateparameterrow',
               type: 'post',
               data: {'col': this_class, 'val': this_val, 'id': this_id},
               dataType: 'json'
           });
        });

        tbl_parameters.on('click', '#btn_delete', function(e) {
           e.preventDefault();
           var this_ = $(this);

            $.ajax({
                url: PECO.base_url() + 'systems/deleteparameters',
                type: 'post',
                data: {'id': this_.attr('data-id')},
                dataType: 'json'
            }).done(function(d){
               if(d.qry==true) {
                   this_.closest('tr td').addClass('danger');
                   this_.closest('tr').fadeOut();
               }
            });
        });

        tbl_parameters.on('click', '#btn_delete_permanent', function(e) {
           e.preventDefault();
           var this_ = $(this);

            $.ajax({
                url: PECO.base_url() + 'systems/deleteparameterspermanent',
                type: 'post',
                data: {'id': this_.attr('data-id')},
                dataType: 'json'
            }).done(function(d){
               if(d.qry==true) {
                   this_.closest('tr td').addClass('danger');
                   this_.closest('tr').fadeOut('fast');
               }
            });
        });


        tbl_parameters.on('click', '#btn_edit', function(e) {
            e.preventDefault();
            var this_ = $(this);
            var this_tr = this_.closest('tr');

            var codes_text = $('td.codes', this_tr).text();
            var names_text = $('td.names', this_tr).text();
            var desc_text = $('td.desc', this_tr).text();

            if(this_.hasClass("open")==false) {

                $('td.colortxt .minicolors, td.colorbg .minicolors', this_tr).removeClass('hidden');

                $('td', this_tr).css('background', 'transparent');
                $('td.colortxt input,td.colorbg input', this_tr).minicolors({
                    control: "hue",
                    letterCase: "uppercase",
                    theme: "bootstrap"
                }).attr('type', 'text');

                $('.icons .icheck', this_tr).attr('type', 'text');

                PECO.select2icons($('.icons .icheck', this_tr));

                $('.colorbox-main', this_tr).addClass('hidden');
                $('#icon-view', this_tr).addClass('hidden');

                // TEXTS
                $('td.codes', this_tr).html('<input class="form-control inline" value="'+codes_text+'" />');
                $('td.names', this_tr).html('<input class="form-control inline" value="'+names_text+'" />');
                $('td.desc', this_tr).html('<input class="form-control inline" value="'+desc_text+'" />');



            }else{
                $('td.colortxt input,td.colorbg input', this_tr).attr('type', 'hidden');
                $('.icons .icheck', this_tr).select2('destroy').attr('type', 'hidden');
                $('td.colortxt .minicolors, td.colorbg .minicolors', this_tr).addClass('hidden');

                $('.colorbox-main', this_tr).removeClass('hidden');
                $('#icon-view', this_tr).removeClass('hidden');

                // TEXTS
                var new_codes_text = $('td.codes input', this_tr).val();
                $('td.codes input', this_tr).remove()
                $('td.codes', this_tr).text(new_codes_text);
                var new_names_text = $('td.names input', this_tr).val();
                $('td.names input', this_tr).remove()
                $('td.names', this_tr).text(new_names_text);
                var new_desc_text = $('td.desc input', this_tr).val();
                $('td.desc input', this_tr).remove()
                $('td.desc', this_tr).text(new_desc_text);

            }

            this_.toggleClass("open");
        });

    };

    var tbl_parameters_init = function (codes) {


        var codes_ = (codes) ? codes : false;
        var status_ = $('#filter_btn li.active a', document).attr('data-id');

        $.ajax({
            url: PECO.base_url() + 'systems/getparameters',
            type: 'post',
            data: {'codes': codes_, 'status': status_},
            dataType: 'json',
            beforeSend: function () {
                PECO.DTphpLoading(tbl_parameters, 'Loading paremeters..');
            }
        }).done(function (d) {
            tbl_parameters.dataTable().empty();
            tbl_parameters.dataTable({
                bDestroy: true,
                bPaginate: false,
                bFilter: true,
                bInfo: false,
                aaData: d.data,
                bSort: true,
                bStateSave: true,
                bProcessing: true,
                scrollY: '460px',
                aoColumns: [
                    {"data": "sysid", sWidth: '', sClass: 'number'},
                    {"data": "codes", sWidth: '', sClass: 'codes'},
                    {"data": "names", sWidth: '', sClass: 'names'},
                    {"data": "desc", sWidth: '', sClass: 'desc'},
                    {"data": "colortxt", sWidth: '', sClass: 'colortxt'},
                    {"data": "colorbg", sWidth: '', sClass: 'colorbg'},
                    {"data": "icons", sWidth: '15%', sClass: 'icons'},
                    {"data": "control", sWidth: '', sClass: 'control'}
                ],
                "searchHighlight": true,
                "language": PECO.DTEmptyMessage(),

                "order": [0, 'asc'],
                fnRowCallback: function (nRow, aData, Index) {
                    //$('.colortxt', nRow).css('background-color', aData.colortxt);
                    //$('.colorbg', nRow).css('background-color', aData.colorbg);
                }
            });


            PECO.initDTNicescroller();

            /*
                $('td.colortxt input,td.colorbg input', tbl_parameters).minicolors({
                    control: "hue",
                    letterCase: "uppercase",
                    theme: "bootstrap"
                });
            */
        });

    };


    var roles_init = function() {
        tbl_roles_list();

        tbl_roles.on('ifChecked', 'tr td #radio_select_role', function () {
            var this_ = $(this);
            var id = this_.val();
            tbl_nav_list(id);
        });

        tbl_nav.on('ifChanged', 'tr td #select_navigation', function () {
            var this_ = $(this);
            var navid = this_.val();
            var roleid = this_.attr('data-id');
            var checked = (this_.is(':checked')) ? 'uncheck' : 'check';
            //alert(checked);
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
                        url:PECO.base_url()+'systems/updatenavmatrix',
                        type:'post',
                        data:{
                            navid:navid,
                            roleid:roleid,
                        },
                        dataType:'json'
                    }).done(function (d) {
                        swal(d.msg, "Navigation!", d.func);
                        //tbl_nav_list(roleid);
                    }).fail(function () {
                        PECO.phpError();
                    });
                }else{
                    this_.iCheck(checked);
                }
            });
        });


        tbl_roles.on('blur', 'tr td #input_role_color', function() {
            var this_ = $(this);
            var this_val = this_.val();
            var this_id = this_.attr('data-id');
            //ajax update....
            $.ajax({
                url: PECO.base_url() + 'systems/updaterolescolor',
                dataType: 'json',
                type: 'post',
                data: {
                    sysid: this_id,
                    color: this_val
                }
            }).done(function (d) {
                if (d.update == true) {
                    tbl_roles_list();
                }else{
                    tbl_roles_list();
                    alert('Error updating role color.');
                }
            });
        });
    };

    /*
    var ComponentsColorPickers = function () {
        var t = function () {
            jQuery().colorpicker && ($(".colorpicker-default").colorpicker({format: "hex"}), $(".colorpicker-rgba").colorpicker())
        }, o = function () {
            $("td input.inline" document).each(function () {
                $(this).minicolors({
                    control: $(this).attr("data-control") || "hue",
                    defaultValue: $(this).attr("data-defaultValue") || "",
                    inline: "true" === $(this).attr("data-inline"),
                    letterCase: $(this).attr("data-letterCase") || "lowercase",
                    opacity: $(this).attr("data-opacity"),
                    position: $(this).attr("data-position") || "bottom left",
                    change: function (t, o) {
                        t && (o && (t += ", " + o), "object" == typeof console && console.log(t))
                    },
                    theme: "bootstrap"
                })
            })
        };
    };
    */

    var tbl_roles_list = function () {
        $.ajax({
            url: PECO.base_url() + 'systems/tblroleslist',
            dataType: 'json',
            type: 'post',
            beforeSend: function () {
                PECO.DTphpLoading(tbl_roles, 'Loading roles...');
            }
        }).done(function (d) {
            tbl_roles.dataTable().empty();
            tbl_roles.dataTable({
                bDestroy: true,
                bPaginate: true,
                bFilter: true,
                bInfo: false,
                aaData: d.tblroles,
                bSort: true,
                bStateSave: true,
                bProcessing: true,
                aoColumns: [
                    {"data": "radio", sWidth: '5%', sClass: 'center'},
                    {"data": "code", sWidth: '5%', sClass: 'codes'},
                    {"data": "desc", sWidth: '25%', sClass: 'desc'},
                    {"data": "colorbox", sWidth: '5%', sClass: 'color'},
                ],
                "searchHighlight": true,
                fnRowCallback: function (nRow, data, index) {
                    $('#input_role_color',nRow).minicolors({
                        control: "hue",
                        letterCase: "uppercase",
                        theme: "bootstrap"
                    });
                    PECO.iCheckRow($('.icheck',nRow),'square','blue')
                }
            });
        });
    };

    var tbl_nav_list = function (id) {
        $.ajax({
            url: PECO.base_url() + 'systems/tblnavlist',
            dataType: 'json',
            type: 'post',
            data: {
                id : id
            },
            beforeSend: function () {
                PECO.DTphpLoading(tbl_nav, 'Loading navigation list...');
            },
        }).done(function (d) {
            tbl_nav.dataTable().empty();
            tbl_nav.dataTable({
                bDestroy: true,
                bPaginate: true,
                bFilter: true,
                bInfo: true,
                aaData: d.nav_list,
                bSort: false,
                bStateSave: false,
                bProcessing: true,
                aoColumns: [
                    {"data": "control", sWidth: '1%', sClass: 'center'},
                    {"data": "modid", sWidth: '3%', sClass: 'center'},
                    {"data": "code", sWidth: '5%', sClass: 'codes'},
                    {"data": "name", sWidth: '15%', sClass: 'desc'},
                ],
                "searchHighlight": true,
                fnRowCallback: function (nRow, data, index) {
                    PECO.iCheckRow($('.icheck',nRow),'flat','blue')
                }
            });
        });

    };

    var table_init = function() {

    };

    return {
        init: function () {
            main_init();
        },

        roles: function () {
            roles_init();
        },

        tables: function() {
            table_init();
        }
    }
}();
