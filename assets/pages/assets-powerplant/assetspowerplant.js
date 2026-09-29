
var POWERPLANT = function () {

    PECO.getHighlightsPlugin();
    PECO.getSweetAlert();

    var tbl_releasing = $('#customer_list');

    var init_assets_powerplant = function () {
        //PECO.DTDefault(tbl_releasing, 'No asset found!');
        tbl_meter_releasing(1);

        $('#btn_filter').on('click', 'button', function(e){
           e.preventDefault();
           var this_ = $(this);
           var this_val = this_.attr('data-id');
           if(this_val) {
               tbl_meter_releasing(this_val);
           }
        });

        tbl_releasing.on('click', '#btn_unrelease', function(e) {
            e.preventDefault();
            var sysid = $(this).attr("data-id");
            swal({
                title: "Are you sure?",
                text: 'Unrelease Meter',
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
                        url: PECO.base_url() + "cad/unreleasemeter",
                        method: "post",
                        dataType: "json",
                        data: {'sysid': sysid}
                    }).done(function (d) {
                        tbl_meter_releasing(300);
                        swal("Success!", "Meter release unreleased!", "success");
                    });
                }
            });

        });

        tbl_releasing.on('click', '#btn_releasethis', function(e) {
            e.preventDefault();
            var sysid = $(this).attr("data-id");
            swal({
                title: "Are you sure?",
                text: 'Release Meter',
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
                        url: PECO.base_url() + "cad/releasethismeter",
                        method: "post",
                        dataType: "json",
                        data: {'sysid': sysid}
                    }).done(function (d) {
                        tbl_meter_releasing(361);
                        swal("Success!", "Meter release released!", "success");
                    });
                }
            });
        });

        $('#btn_print_list', document).click(function(e) {
            e.preventDefault();
            $.ajax({
                url: PECO.base_url() + "cad/getappmeterconn",
                method:"post",
                dataType:"json",
                data: {'status': 1, 'viewtype': 1}
            }).done(function (d) {
                PECO.pecoRepPrint('Meter Releasing', d.html);
            });
        });

        $('#btn_release_list', document).click(function(e) {
            e.preventDefault();
            swal({
                title: "Are you sure?",
                text: 'Did you print the list first?',
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
                        url: PECO.base_url() + "cad/getappmeterconn",
                        method: "post",
                        dataType: "json",
                        data: {'status': 1, 'viewtype': 2}
                    }).done(function (d) {
                        tbl_meter_releasing(361);
                        swal("Success!", "List has been release", "success");
                    });
                }
            });
        });
    };


    var init_mtr_realease = function (dataid) {
        $.ajax({
            url:base_url+"assets/getmeterassignment",
            method:"post",
            data:{"dataid":dataid},
            dataType:"json"
        }).done(function (d) {
            $(document).find('#assetcode').text(d.mtrserial);
            $(document).find('#brand').text(d.brand);
            $(document).find('#amp').text(d.amps);
            $(document).find('#volts').text(d.volts);
            $(document).find('#desc').text(d.desc);

        }).fail(function () {
            alert("Failed to fetch data for Meter Assignment");
        });


    };

    var tbl_meter_releasing = function(status) {
        var status = (status) ? status : false;
        $.ajax({
            url: PECO.base_url() + "cad/getappmeterconn",
            method:"post",
            dataType:"json",
            data: {'status': status}
        }).done(function (d) {
            tbl_releasing.dataTable().empty()
            tbl_releasing.dataTable({
                bDestroy: true,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                Order: [[1, "asc"]],
                aaData: d.list,
                oLanguage: {
                    sProcessing: PECO.datatableLoading('Loading items..', 'text-danger'),
                },
                aoColumns: [
                    {"data": "num", sWidth: '80px'},
                    {"data": "custname", sWidth: ''},
                    {"data": "address", sWidth: ''},
                    {"data": "mtrserial", sWidth: ''},
                    {"data": "gdlb", sWidth: ''},
                    {"data": "date", sWidth: ''},
                    {"data": "status", sWidth: ''},
                    {"data": "reading", sWidth: '', sClass: 'input text-danger'},
                    {"data": "check", sWidth: ''}
                ],
                searchHighlight: true,
            });
        }).fail(function () {
            PECO.DTphpError(tbl_releasing);
        });
    };

    var handler_set_status = function(dataid, status, this_) {
        $.ajax({
            url: PECO.base_url() + "cad/setstatus",
            method:"post",
            dataType:"json",
            data: {'dataid': dataid, 'status': status}
        }).done(function (d) {
            var this_title = this_.attr('title');
            var this_func = this_.attr('data-func');
            PECO.initAlerts(this_title, 'PAE.sys Alert', this_func);
        });
    };

    var init_installations = function(dataid, moduleid) {

        handler_team_assignment_tbl(dataid, moduleid);

        $(document).on('click', '.install-btns .install-function', function(e) {
            e.preventDefault();
            var this_ = $(this);
            handler_set_status(dataid, this_.attr('data-id'), this_);
        });

        /*
        $(document).on('click', '#send_out_installation', function(e) {
            e.preventDefault();
            PECO.initAlerts('Status set to send out for installation!', 'Send out Installation', 'warning');
        });
        $(document).on('click', '#send_ongoing_installation', function(e) {
            e.preventDefault();
            PECO.initAlerts('Status set to on-going installation!', 'On-Going Installation', 'info');
        });
        $(document).on('click', '#send_done_installation', function(e) {
            e.preventDefault();
            PECO.initAlerts('Status set to finish installation!', 'Finish Installation', 'success');
        });

         */

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


    };


    return{
        init:function () {
            init_assets_powerplant();
        },
        meterrealease:function(dataid){
            init_mtr_realease(dataid);
        },
        installation: function(dataid, moduleid){
            init_installations(dataid, moduleid);
        }
    };
}();
