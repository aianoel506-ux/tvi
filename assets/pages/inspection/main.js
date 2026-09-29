var INSPECTION = function () {
    PECO.getSweetAlert();
    PECO.getNumberFormatPlugin();
    var tbl_inspection_logs = $('#tbl_inspection_logs',document);
    var tbl_sps_components = $('#tbl_sps_components',document);


    var init_inspection_application = function (dataid) {
        $("#draggable").draggable({
            handle: ".modal-header"
        });


        $('#inspectiondate', document).datepicker({
            format: 'yyyy-mm-dd',
            placeholder: 'Date Inspected...'
        });



        $(document).on('change', '#rate_class_select', function (e) {
            e.preventDefault();

        });

        $(document).on("change", "[name='city'], [name='inspection_date'], [name='district'], [name='specific_address']", function () {
            //save data on change.
            var name = $(this).attr("name");
            var value = $(this).val();
            $.ajax({
                type: 'POST',
                url: PECO.base_url() + "query/ajaxInputSave",
                data: {
                    formName: name,
                    inputValue: value,
                    dataid: dataid
                },
                dataType: "json",
                success: function (queryReturn) {
                    if (queryReturn['query']) {
                        console.log('queryValue:' + queryReturn['query']);
                    } else {
                        console.log('queryValue:' + queryReturn['query']);
                    }
                }
            });
            //alert(name+' '+value); //this is for debugging purposes only.

        });
        $("#inspection_date").datepicker();



        //$("#latitude").val(latitude);
        // $("#longitude").val(longitude);
        $('#capture').click(function (e) {
            e.preventDefault();
            capture_map_data();
            $.ajax({
                url: PECO.base_url() + 'inspection/updategeodata',
                data: {
                    'x': latitude,
                    'y': longitude,
                    'i': $('#owner_addr_id').val()
                },
                type: 'post',
                dataType: 'json'
            }).done(function (data) {
                PECO.initAlerts(data.msg, 'Map Update', data.func);
            }).fail(function () {
                PECO.initAlerts('Error PHP', 'ERROR', 'error');
            });
        });


        // SELECT 2 INIT
        PECO.select2Basic($('#rate_class_select', document), 'inspection/initrateclasslist', 'Rate Class..');
        PECO.select2Basic($('#select2_panel_type', document), 'inspection/select2paneltype', 'Panel type..');
        PECO.select2Basic($('#select2_rooftype',document),'inspection/select2rooftypes','Roofing...');



        init_inspection_logs(dataid);

        PECO.dtSubDetails(tbl_inspection_logs, 'inspection/getgdrsubdetails');


        $('#tbl_inspection_logs',document).on('click','#btn_del_insp', function () {
            var this_ = $(this);
            var logid = this_.attr('data-id');

            delete_inspection(dataid,logid);
        });

        $('#btn_tag_ecales',document).on('click', function () {
            var this_ = $(this);
            var dataid = this_.attr('data-id');
            var flowid = this_.attr('flow-id');

            swal({
                title: "Inventory",
                text: 'This will tag the application as Inventory.',
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Tag as Inventory!",
                closeOnConfirm: false,
                closeOnCancel: true,
                showLoaderOnConfirm: true
            }, function(isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: base_url + 'inspection/tagecales',
                        type: 'post',
                        dataType: 'json',
                        data: {
                            dataid : dataid,
                            flowid : flowid
                        }
                    }).done(function (d) {
                        swal('Inventory',d.msg,d.func);
                    });
                } else {
                    swal('Canceled!','Account was not tagged as Inventory.','error');
                }
            });
        });

        /*
        handler_inspection_compute(dataid, 0);
        $(document).on('change', '#tech_report_input input', function(e) {
            handler_inspection_compute(dataid, 0);
        });
         */

        tbl_inspection_logs.on('change','#active_insp',function () {
            var this_ = $(this);
            var surveyid = this_.attr('data-id');
            select_survey_report(dataid,surveyid);
        });

        PECO.DTDefault(tbl_sps_components,"No system components loaded!");
        //PECO.DTDefault(tbl_sps_accessories,"No system accessories loaded!");
        //PECO.DTDefault(tbl_sps_consumables,"No installation consumables loaded!");

        load_on_tab(dataid);
        load_installation_setup(dataid);
        init_installation_setup(dataid);

        $(document).on('click','#btn_reload_setup',function () {
            init_installation_setup(dataid);
        });
        installation_setup_handler(dataid);
        dt_assessment_docs();
    };



    var init_inspection_logs = function (dataid) {
        var form_add_eq = $('#form_add_eq',document);
        var form = $('#form_inspection_report', document);
        $.ajax({
            url: PECO.base_url() + 'inspection/dtinspectionlogs',
            type: 'post',
            dataType: 'json',
            data: {dataid : dataid}
        }).done(function (d) {
            //tbl_inspection_logs.DataTable().empty();
            tbl_inspection_logs.DataTable({
                bDestroy: true,
                bPaginate: false,
                bFilter: false,
                bInfo: false,
                bStateSave: true,
                bProcessing: true,
                aaData: d.list,
                aoColumns: [
                    {"data": "expand", sClass: 'withsub' , sWidth: '5px '},
                    {"data": "num", sClass: 'number'},
                    {"data": "remarks", sClass: "", },
                    {"data": "inspectiondate", sClass: ""},
                    {"data": "nop", sClass: "number"},
                    {"data": "power", sClass: "number"},
                    {"data": "created", sClass: "", },
                    {"data": "datecreated", sClass: "", },
                    {"data": "control", sClass: "control text-align-center"},
                    {"data": "active", "sClass": "text-align-center", "sWidth": "60px"}, ],
                language: {
                    "emptyTable": '<i class="fa fa-warning text-warning"></i> No record found.'
                },
                fnRowCallback: function (nRow,aData,i) {
                    //$('.totalwatt', nRow).number(true, 0);
                    //$('.totalcost', nRow).number(true, 2);

                    //PECO.iCheckRow($('.icheck',nRow),'square','orange');
                }
            });

            //var list = d.list;
            var sysid = 0;
            var published = d.published;
            if (published) {
                sysid = published.sysid;
                if (published.result === true) {
                    select_survey_report(dataid,sysid);
                    form.find(':input').each(function () {
                        $(this).attr('readonly',true);
                    });
                }
            }
            //console.log('Sysid: ' + sysid);
            /*if (sysid !== 0) {

                //IF 305 LOAD BUTTONS
                var html = '';

            }*/
        })
    };


    var delete_inspection = function (dataid,logid) {
        var form_inspection_report  = $('#form_inspection_report', document);
        swal({
            title: "Are you sure?",
            text: 'This will delete the corresponding survey log.',
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
                    url: base_url + 'inspection/deleteinspection',
                    type: 'post',
                    dataType: 'json',
                    data: {
                        appid: dataid,
                        logid: logid
                    },
                }).done(function (d) {
                    swal(d.title,d.msg,d.func);
                    if (d.active > 0) {
                        setTimeout(function () {
                            init_inspection_logs(dataid);
                        }, 700);
                    } else {
                        init_inspection_logs(dataid);
                    }
                }).fail(function () {
                    swal('Fail!','Failed to delete record.','error');
                })
            }
        });
    };

    var inspection_logs = function (dataid) {
        var tbl_inspection_logs = $('#tbl_inspection_logs',document);
        PECO.dtSubDetails(tbl_inspection_logs, 'inspection/getgdrsubdetails');
        //alert('inspection_logs');
        var form_add_eq = $('#form_add_eq',document);
        $.ajax({
            url: PECO.base_url() + 'inspection/dtinspectionlogs',
            type: 'post',
            dataType: 'json',
            data: {dataid : dataid}
        }).done(function (d) {
            //tbl_inspection_logs.dataTable().empty();
            tbl_inspection_logs.DataTable({
                bDestroy: true,
                bPaginate: true,
                bFilter: true,
                bInfo: false,
                bStateSave: true,
                bProcessing: true,
                aaData: d.list,
                aoColumns: [
                    {"data": "expand", sClass: 'withsub' , sWidth: '5px '},
                    {"data": "num", sClass: 'number'},
                    {"data": "comment", sClass: "", },
                    {"data": "inspectiondate", sClass: ""},
                    {"data": "nop", sClass: "number"},
                    {"data": "power", sClass: "number"},
                    {"data": "created", sClass: "", },
                    {"data": "datecreated", sClass: "", },
                    {"data": "stat", sClass: "text-align-center", }, ],
                language: {
                    "emptyTable": '<i class="fa fa-warning text-warning"></i> No record found.'
                },
            });
        })
    };

    var handler_team_assignment = function(dataid, moduleid) {


        handler_team_assignment_tbl(dataid, moduleid);

        $(document).on('click', '#btn_refresh_team_list', function(e) {
            e.preventDefault();
            handler_team_assignment_tbl(dataid, moduleid);
        });

        $('#tbl_inspection_team', document).on('click', '#btn_delete_team', function(e) {
            e.preventDefault();
            var this_ = $(this);
            var this_tr = this_.closest('tr');
            $.ajax({
                url: PECO.base_url() + 'inspection/deleteteam',
                type: 'post',
                data: {'id' : this_.attr('data-id')},
                dataType: 'json',
            }).done(function(d) {
                if(d.qry) {
                    this_tr.fadeOut();
                }else{
                    alert('error adding employee!');
                }
            }).fail(function() {
                alert('PHP Error!');
            });
        });
        $(document).on('submit', '#frm_add_team_member', function(e) {
            e.preventDefault();
            var form = $(this);
            var modal = form.closest('div.modal');
            $.ajax({
                url: form.attr('action'),
                type: 'post',
                data: form.serialize(),
                dataType: 'json',
            }).done(function(d) {
                if(d.qry) {
                    handler_team_assignment_tbl(dataid, moduleid);
                }else{
                    alert('error adding employee!');
                }
            }).fail(function() {
                alert('PHP Error!');
            });
        });
    };

    var handler_team_assignment_tbl = function(appid, moduleid) {
        var tbl_inspection_team = $('#tbl_inspection_team', document);
        $.ajax({
            url: PECO.base_url() + 'inspection/getteamassignment',
            type: 'post',
            dataType: 'json',
            data: {'appid': appid, 'moduleid': moduleid},
            beforeSend: function() {
                PECO.DTphpLoading(tbl_inspection_team, 'Loading team member list...');
            }
        }).done(function(d) {
            tbl_inspection_team.DataTable({
                bDestroy: true,
                bPaginate: false,
                bFilter: false,
                bInfo: false,
                bStateSave: true,
                bProcessing: true,
                aaData: d.list,
                aoColumns: [
                    {"data": "empid", sClass: '' , sWidth: ''},
                    {"data": "name", sClass: 'text-primary bold'},
                    {"data": "date", sClass: "", },
                    {"data": "control", sClass: "controls text-align-center", sWidth: '30px'}
                ],
                language: {
                    "emptyTable": '<i class="fa fa-warning text-warning"></i> No record found.'
                },
            });

        }).fail(function() {
            PECO.DTphpError(tbl_inspection_team);
        });

        handler_inspection_compute(appid, 0);
        $(document).on('change', '#tech_report_input input', function() {
            handler_inspection_compute(appid, 0);
        });
        $(document).on('blur', '#tech_report_input input', function() {
            handler_inspection_compute(appid, 0);
        });



        var tech_report_input = $('#tssr_va_reading');

        $('div.form-control', tech_report_input).each(function() {
            $(this).hide();
        });

        $('input.form-control, textarea.form-control', tech_report_input).each(function() {
            $(this).show();
        });

        tech_report_input.on('change', 'input, select', function(e) {
            handler_inspection_compute(appid, 0)
        });


        $(document).on('click', '#save_inspection_load', function(e) {
            handler_inspection_compute(appid, 1);
        });

        $(document).on('click', '#publish_inspection_load', function(e) {
            swal({
                title: "Confirm Action",
                text: 'Please confirm action, publish computation',
                type: "info",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes",
                closeOnConfirm: false,
                closeOnCancel: true,
                showLoaderOnConfirm: true
            }, function(isConfirm) {
                if (isConfirm) {
                    handler_inspection_compute(appid, 2);
                    swal.close();
                }
            });
        });

    };

    var handler_inspection_compute = function(dataid, posttype) {

        var form_inspection_report  = $('#form_inspection_report', document);
        var tech_report_input       = $('#tech_report_input', document);

        var div_l1l2    = $('.l1l2', document);
        var div_l1l3    = $('.l1l3', document);
        var div_l2l3    = $('.l2l3', document);
        var div_l1g     = $('.l1g', document);
        var div_l2g     = $('.l2g', document);
        var div_l3g     = $('.l3g', document);
        var div_l1l2a   = $('.l1l2a', document);
        var div_l1l3a   = $('.l1l3a', document);
        var div_l2l3a   = $('.l2l3a', document);

        var data_ar = {
            l1l2        : $('input.form-control', div_l1l2).val(),
            l1l3        : $('input.form-control', div_l1l3).val(),
            l2l3        : $('input.form-control', div_l2l3).val(),
            l1g         : $('input.form-control', div_l1g).val(),
            l2g         : $('input.form-control', div_l2g).val(),
            l3g         : $('input.form-control', div_l3g).val(),
            l1l2a       : $('input.form-control', div_l1l2a).val(),
            l1l3a       : $('input.form-control', div_l1l3a).val(),
            l2l3a       : $('input.form-control', div_l2l3a).val(),
            posttype    : posttype
        };
        $('#text_nop', tech_report_input).html('0');
        $('#text_power', tech_report_input).html('0');
        $('#text_system_size', tech_report_input).html('0');

        var data_serialized = form_inspection_report.serialize();
        var data_arr_add = data_serialized + '&posttype=' + posttype + '&appid=' + dataid;

        var post_data = $('input, textarea', tech_report_input).serialize();
        $.ajax({
            url: PECO.base_url() + 'inspection/computeinspection',
            type: 'post',
            dataType: 'json',
            data: data_arr_add
        }).done(function(d) {
            $('#text_nop', document).text(d.nop);
            $('#text_power', document).text(d.power_text);
            $('#text_system_size', document).text(d.system_size_text);
            if(posttype>0) {
                PECO.initAlerts(d.msg, 'Inspection Alert', d.func);
                init_inspection_logs(dataid);
                if (d.surveyid) {
                    form_inspection_report.find('#selected_inspection').val(d.surveyid);
                }
            }
        }).fail(function() {
            alert('PHP Fail!');
        });
    };

    var select_survey_report = function (dataid,surveyid) {
        var form_inspection_report = $('#form_inspection_report',document);
        $.ajax({
            url: PECO.base_url() + 'inspection/getactivesurvey',
            type: 'post',
            dataType: 'json',
            data: {
                dataid: surveyid
            }
        }).done(function (d) {
            var selected = d.selected;
            //console.log(selected);
            form_inspection_report.find('input').each(function () {
                var this_ = $(this);
                var name = this_.attr('name');
                var id = this_.attr('id');
                if (selected.hasOwnProperty(name)) {
                    var value = selected[name];
                    var val = 0;
                    if(typeof value === 'number'){
                        val = parseFloat(value);
                    } else{
                        val = value
                    }
                    this_.val(val).trigger('change');
                }
            });

            form_inspection_report.find('textarea').each(function () {
                var this_ = $(this);
                var name = this_.attr('name');
                if (selected.hasOwnProperty(name)) {
                    this_.text(selected[name]);
                }
            });

            form_inspection_report.find('#selected_inspection').val(surveyid);

            handler_inspection_compute(dataid,0)
        }).fail(function () {

        });
    };

    var load_on_tab = function (dataid) {
        var recom_setup = $('#recom_setup',document);
        var tab = $('a[data-toggle="tab"]',recom_setup);
        var id = 1;
        PECO.select2Basic($('#select2_newitem',document),'inspection/select2applicationspsitems','Select item...',false,false,false,false,false,id + ',' + dataid);
        tab.on('shown.bs.tab', function (e) {
            var target = $(e.target).attr('href');
            id = $(e.target).attr('data-id');
            var msg = '';
            var load = '';

            if (target == '#sps_components') {
                msg = "No system components loaded!";
            }
            if (target == '#sps_accessories') {
                msg = "No system Accessories loaded!";
            }
            if (target == '#sps_consumables') {
                msg = "No installation consumables loaded!";
            }
            PECO.DTDefault(tbl_sps_components,msg);
            load_installation_setup(dataid,id);
            PECO.select2Basic($('#select2_newitem',document),'inspection/select2applicationspsitems','Select item...',false,false,'',false,false,id + ',' + dataid);
        });

        $(document).on('click','#btn_reload_setup',function () {
            load_installation_setup(dataid,id);
        });
    };

    var load_installation_setup = function (dataid,itemtype) {
        var itemType = (itemtype) ? itemtype : 1;
        var select2_paneltypes = $('#select2_paneltypes',document);
        var input_nop = $('#input_nop',document);
        var input_nos = $('#input_nos',document);
        var input_panelsperstring = $('#input_panelsperstring',document);
        var input_invertersize = $('#input_invertersize',document);



        var load = ['','Loading system components...','Loading system accessories...','Loading installation consumables...'];
        $.ajax({
            url: PECO.base_url() + 'inspection/getspsitemslist',
            type: 'post',
            dataType: 'json',
            data: {
                appid : dataid,
                itemtype : itemType
            },
            beforeSend: function() {
                PECO.DTphpLoading(tbl_sps_components, load[itemType]);
            }
        }).done(function (d) {
            tbl_sps_components.DataTable({
                bDestroy: true,
                bPaginate: false,
                bFilter: false,
                bInfo: false,
                bStateSave: true,
                bProcessing: true,
                aaData: d.parts,
                language: PECO.DTEmptyMessage(d.msg),
                aoColumns: [
                    {"data": "num", sClass: 'number' , sWidth: '10%'},
                    {"data": "item", sClass: 'text-primary bold', sWidth: '50%'},
                    {"data": "qty", sClass: "number", sWidth: '10%' },
                    {"data": "unit", sClass: "", sWidth: '10px' },
                    //{"data": "price", sClass: "number", sWidth: '15%' },
                    //{"data": "total", sClass: "number total", sWidth: '15%' },
                    {"data": "control", sClass: "controls text-align-center", sWidth: '10%'}
                ],
            });

            $('#total_10years').html(d.total10yrplan);
            $('#total_5years').html(d.total5yrplan);
        }).fail(function () {
            PECO.DTphpError(tbl_sps_components);
        });
    };

    var init_installation_setup = function (dataid) {
        // Find if existing installation setup.
        // If yes, do not search. Otherwise, search for template.
        // Load saved setup information.
        // Edit/Delete information.
        // Ask to add as template if not exist in templates.
        var select2_paneltypes = $('#select2_paneltypes',document);
        var input_nop = $('#input_nop',document);
        var input_nos = $('#input_nos',document);
        var input_panelsperstring = $('#input_panelsperstring',document);
        var input_invertersize = $('#input_invertersize',document);
        $.ajax({
            url: PECO.base_url() + 'inspection/getsavedsystemsize',
            type: 'post',
            dataType: 'json',
            data: {
                appid : dataid
            }
        }).done(function (d) {
            PECO.select2Basic(select2_paneltypes,'inspection/select2paneltype','Panel Type...',false,false,d.sizeid);

            if (d.details) {
                //select2_paneltypes.val(d.details['sptypeid']).trigger('change');
                PECO.select2Basic(select2_paneltypes,'inspection/select2paneltype','Panel Type...',false,false,d.details['sptypeid']);
                input_nop.val(d.details['nop']);
                input_nos.val(d.details['nos']);
                input_panelsperstring.val(d.details['panelsperstring']);
                input_invertersize.val(d.details['invertersize']);
            } else {
                setup_parameters_lookup(dataid);
            }

        }).fail(function () {

        });

        /*$('#tbl_add_components',document).DataTable({
            bDestroy: true,
            bPaginate: false,
            bFilter: false,
            bInfo: false,
            bStateSave: true,
            bProcessing: true
        });*/

        load_installation_setup(dataid,1);
    };

    var setup_parameters_lookup = function (dataid) {
        var form = $('#frm_sps_setup',document);
        var select2_paneltypes = $('#select2_paneltypes',form);
        var input_nop = $('#input_nop',form);
        var input_nos = $('#input_nos',form);
        var input_panelsperstring = $('#input_panelsperstring',form);
        var input_invertersize = $('#input_invertersize',form);

        var ppsparams = {sptypeid : select2_paneltypes.val(),nop : input_nop.val() , nos : input_nos.val()};

        var isparams = $.extend({},ppsparams,{panelsperstring: input_panelsperstring.val()});

        $(document).on('change keyup blur','#select2_paneltypes,#input_nop,#input_nos,#input_panelsperstring,#input_invertersize',function (e) {
            var form = $('#frm_sps_setup',document);
            var template_search_result = $('#template_search_result',document);
            $.ajax({
                url: PECO.base_url() + 'inspection/searchsetuptemplate',
                type: 'post',
                dataType: 'json',
                data: form.serialize() + '&' + $.param({appid : dataid})
            }).done(function (d) {
                template_search_result.html(d.html);
            }).fail(function () {

            });

            ppsparams = {sptypeid : select2_paneltypes.val(),nop : input_nop.val() , nos : input_nos.val()};
            isparams = $.extend({},ppsparams,{panelsperstring: input_panelsperstring.val()});
        });

        /*$(document).on('change','#select2_paneltypes,#input_nop,#input_nos,#input_panelsperstring,#input_invertersize',function () {

            //console.log(isparams);
        });*/

        //change DB values to
        PECO.searchToolTip(input_panelsperstring,'inspection/panelsperstringlookup',ppsparams);
        PECO.searchToolTip(input_invertersize,'inspection/invertersizelookup',isparams);
    };

    var load_template_list = function (ids) {
        var ids = ids.split(',');
        var tbl_sps_template_list = $('#tbl_sps_template_list',document);
        PECO.dtSubDetails(tbl_sps_template_list,'inspection/templatedetails');
        $.ajax({
            url : PECO.base_url() + 'inspection/templatelist',
            type : 'post',
            dataType : 'json',
            data : {
                ids : ids
            }
        }).done(function (d) {
            tbl_sps_template_list.DataTable({
                bDestroy: true,
                bPaginate: true,
                bFilter: false,
                bInfo: true,
                bStateSave: true,
                bProcessing: true,
                aaData: d.list,
                language: PECO.DTEmptyMessage(d.msg),
                aoColumns: [
                    {"data": "num", sClass: 'number' , sWidth: ''},
                    {"data": "name", sClass: 'text-primary bold name', sWidth: '20%'},
                    {"data": "systemtype", sClass: "", },
                    {"data": "paneltype", sClass: "", },
                    {"data": "nop", sClass: "number",},
                    {"data": "nos", sClass: "number",},
                    {"data": "panelsperstring", sClass: "", },
                    {"data": "invertersize", sClass: "", },
                    {"data": "select", sClass: "controls text-align-center", sWidth: '30px'}
                ],
                columnDefs: [ {
                    targets: 1,
                    render: function ( data, type, row ) {
                        return type === 'display' && data.length > 10 ?
                            data.substr( 0, 15 ) +'…' :
                            data;
                    }
                }, {
                    targets: 2,
                    render: function ( data, type, row ) {
                        return type === 'display' && data.length > 10 ?
                            data.substr( 0, 10 ) +'…' :
                            data;
                    }
                }, {
                    targets: 3,
                    render: function ( data, type, row ) {
                        return type === 'display' && data.length > 10 ?
                            data.substr( 0, 10 ) +'…' :
                            data;
                    }
                }, ]
            });
        }).fail(function () {

        });

        $(document).on('click','#btn_select_template',function (e) {
            e.preventDefault();
            var this_ = $(this);
            var appid = $('#input_app_id',document).val();
            swal({
                title: "Load template?",
                text: 'This will apply the selected template for the application.',
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-primary",
                confirmButtonText: "Yes",
                closeOnConfirm: false,
                closeOnCancel: true,
                showLoaderOnConfirm: true
            }, function(isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: this_.attr('href'),
                        type: 'post',
                        dataType: 'json',
                        data: {
                            id : this_.attr('data-id'),
                            appid : appid
                        },
                    }).done(function (d) {
                        swal(d.title,d.msg,d.func);
                        if (d.qry) {
                            setTimeout(function () {
                                init_installation_setup(appid);
                            }, 700);
                        }
                    }).fail(function () {
                        swal('Fail!','Failed to apply template!','error');
                    })
                }
            });
        });
    };

    var template_details = function (id) {
        var tab = $('a[data-toggle="tab"]');
        var tbl_template_components = $('#tbl_template_components',document);
        PECO.DTDefault(tbl_template_components,'No system components loaded!');
        $.ajax({
            url : PECO.base_url() + 'inspection/templatelist',
            type : 'post',
            dataType : 'json',
            data : {
                ids : id
            }
        }).done(function (d) {
            $('#name_template',document).text(d.details['name']);
            $('#paneltypes_template',document).text(d.details['paneltype']);
            $('#numberofpanels_template',document).text(d.details['nop']);
            $('#numberofstrings_template',document).text(d.details['nos']);
            $('#panelsperstring_template',document).text(d.details['panelsperstring']);
            $('#invertersize_template',document).text(d.details['invertersize']);
        }).fail(function () {

        });
        load_template_components(1,id);
        tab.on('shown.bs.tab', function (e) {
            var target = $(e.target).attr('href');
            var itemtype = $(e.target).attr('data-id');
            var msg = '';
            var load = '';

            if (itemtype == 1) {
                msg = "No system components loaded!";
            }
            if (itemtype == 2) {
                msg = "No system Accessories loaded!";
            }
            if (itemtype == 3) {
                msg = "No installation consumables loaded!";
            }
            PECO.DTDefault(tbl_template_components,msg);
            load_template_components(itemtype,id);
        });
    };

    var load_template_components = function (itemtype,id) {
        var tbl_template_components = $('#tbl_template_components',document);

        $.ajax({
            url: PECO.base_url() + 'inspection/getspsitemslisttemplate',
            type : 'post',
            dataType: 'json',
            data : {
                groupid : id,
                type : itemtype
            }
        }).done(function (d) {
            tbl_template_components.DataTable({
                bDestroy: true,
                bPaginate: true,
                bFilter: false,
                bInfo: true,
                bStateSave: true,
                bProcessing: true,
                aaData: d.parts,
                language: PECO.DTEmptyMessage(d.msg),
                aoColumns: [
                    {"data": "num", sClass: 'number' , sWidth: '10px'},
                    {"data": "item", sClass: 'text-primary bold'},
                    {"data": "qty", sClass: "number",  sWidth: '10px'},
                    {"data": "unit", sClass: "",  sWidth: '10px'},
                    {"data": "price", sClass: "number",  sWidth: '50px'},
                    {"data": "total", sClass: "number",  sWidth: '50px'}
                ],
            });

            $('#total_10years_template').html(d.total10yrplan);
            $('#total_5years_template').html(d.total5yrplan);
        }).fail(function () {
            PECO.DTphpError(tbl_template_components);
        });
    };

    var installation_setup_handler = function (dataid) {

        var data = {};
        var total_str = '';
        var frm_add_spsitem = $('#frm_add_spsitem',document);

        tbl_sps_components.on('click','#btn_edit_item',function () {
            var this_ = $(this);

            var this_tr = this_.closest('tr');
            var this_td = this_.closest('td');

            var controls = $('#item_controls',this_td);
            var controls_html = controls.html();

            total_str = $('.total',this_tr).html();

            var edit = '';
            edit += '<a href="#save" class="btn btn-sm btn-primary inline" id="btn_save_item"><i class="fa fa-save"></i> </a>';
            edit += '<a href="#cancel" class="btn btn-sm btn-danger inline" id="btn_cancel_edit"><i class="fa fa-times"></i> </a>';

            controls.html(edit);

            this_tr.find('input').each(function () {
                $(this).attr('disabled',false);
                var style = $(this).attr('style');
                $(this).attr('style',style + 'border-bottom: 1px solid #333333 !important');
                $(this).val( $(this).val().replace(/,/g, "") );
                data[$(this).attr('name')] = $(this).val();
            })
        });

        tbl_sps_components.on('click','#btn_cancel_edit, #btn_save_item',function (e) {
            e.preventDefault();
            var this_ = $(this);
            var target = this_.attr('href');

            var this_tr = this_.closest('tr');
            var this_td = this_.closest('td');

            var controls = $('#item_controls',this_td);

            var edit = '';
            edit += '<a href="javascript:;" class="btn btn-sm btn-primary inline" id="btn_edit_item"><i class="fa fa-edit"></i> </a>';
            edit += '<a href="javascript:;" class="btn btn-sm btn-danger inline" id="btn_remove_item"><i class="fa fa-times"></i> </a>';

            if (target === '#save') {
                this_tr.find('input').each(function () {
                    data[$(this).attr('name')] = $(this).val();
                });

                var total10yrs = $('#total_10years',document);
                var total5yrs = $('#total_5years',document);

                var old10yrs = parseFloat(total10yrs.html().replace(/\,/g, ''),10);
                var old5yrs = parseFloat(total5yrs.html().replace(/\,/g, ''),10);

                var new10yrs = 0;
                var new5yrs = 0;

                $.ajax({
                    url : PECO.base_url() + 'inspection/updateinstallationitem',
                    type : 'post',
                    dataType : 'json',
                    data : data
                }).done(function (d) {

                    this_tr.find('input').each(function () {
                        $(this).attr('disabled',true);
                        var style = $(this).attr('style');
                        var css = style.split(';');
                        $(this).attr('style',css[0]+';');
                        $(this).val(d.values[$(this).attr('name')]);
                    });
                    $('.total',this_tr).text(d.total);
                    controls.html(edit);

                    new10yrs = old10yrs - parseFloat(d.oldtotal) + parseFloat(d.total);
                    new5yrs = old5yrs - parseFloat(d.oldtotal) + parseFloat(d.total);

                    total10yrs.html(new10yrs.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',').toLocaleString());
                    total5yrs.html(new5yrs.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',').toLocaleString());

                    PECO.initAlerts('Item properties updated!','Update Success!','success');


                }).fail(function () {

                });
            }

            if (target === '#cancel') {
                this_tr.find('input').each(function () {
                    $(this).val(data[$(this).attr('name')]);
                    var val = $(this).val();
                    $(this).attr('disabled',true);
                    var style = $(this).attr('style');
                    var css = style.split(';');
                    $(this).attr('style',css[0]+';');
                    if (val.indexOf('.') > -1) {
                        PECO.formatNumber($(this),true);
                    } else {
                        PECO.formatNumber($(this),false);
                    }
                });

                $('.total',this_tr).text(total_str);
                controls.html(edit);
            }
        });

        tbl_sps_components.on('blur keyup change','input',function (event) {
            var this_ = $(this);
            var this_tr = this_.closest('tr');
            var qty = $('#input_qty',this_tr).val();
            var price = parseFloat($('#input_unitprice',this_tr).val(),10);
            var total = qty*price;
            $('.total',this_tr).text(total.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',').toLocaleString());
        });

        frm_add_spsitem.on('change','#select2_newitem',function () {
            var this_ = $(this);
            var this_id = this_.val();
            var new_item_qty = $('#input_new_item_qty',frm_add_spsitem).val();
            $.ajax({
                url: PECO.base_url() + 'inspection/getspsitemdefaults',
                type : 'post',
                dataType: 'json',
                data : {
                    itemid : this_id
                }
            }).done(function (d) {
                $('#item_unit',frm_add_spsitem).text(d.unit);
                $('#input_new_item_price',frm_add_spsitem).val(d.unitprice);
                $('#input_new_item_type',frm_add_spsitem).val(d.type);
                var total_price = new_item_qty * d.unitprice;
                $('#item_total',frm_add_spsitem).text(total_price.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ','));
            }).fail(function () {

            });
        });

        frm_add_spsitem.on('change blur keyup','#input_new_item_qty,#input_new_item_price',function () {
            var new_item_qty = $('#input_new_item_qty',frm_add_spsitem).val();
            var new_item_price = $('#input_new_item_price',frm_add_spsitem).val();
            var total_price = new_item_qty * new_item_price;
            $('#item_total',frm_add_spsitem).text(total_price.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ','));
        });

        frm_add_spsitem.on('submit',function (e) {
            e.preventDefault();
            var this_ = $(this);

            $.ajax({
                url: this_.attr('action'),
                type: this_.attr('method'),
                dataType: 'json',
                data: this_.serialize()
            }).done(function (d) {
                load_installation_setup(dataid,d.type)
            }).fail(function () {

            });
        });

        $(document).on('click','#preview_inspection_load',function () {
            $.ajax({
                url: PECO.base_url() + 'printer/docspreview',
                type: 'post',
                dataType: 'json',
                data: {
                    id: dataid,
                    doctype: 3436,
                    print: true
                }
            }).done(function (d) {
                var win = window.open('','');
                PECO.pdfPreview(win,d.title,d.html);
            }).fail(function () {

            });
        });

        $(document).on('click','#btn_setup_preview',function () {
            $.ajax({
                url: PECO.base_url() + 'inspection/printinstallationsetup',
                type: 'post',
                dataType: 'json',
                data: {
                    id: dataid
                }
            }).done(function (d) {
                var win = window.open('','');
                PECO.pdfPreview(win,d.title,d.html);
            }).fail(function () {

            });
        });

    };

    var dt_assessment_docs = function () {
        var tbl_assessment_docs = $('#tbl_assessment_docs',document);
        PECO.dtDocsList(tbl_assessment_docs);
    };

    return {
        application: function (dataid) {
            init_inspection_application(dataid);
        }, logs: function (dataid) {
            inspection_logs(dataid);
        }, team: function(dataid, moduleid) {
            handler_team_assignment(dataid, moduleid);
        }, templates: function (ids) {
            load_template_list(ids);
        }, templatedetails: function (id) {
            template_details(id);
        }
    }
}();
