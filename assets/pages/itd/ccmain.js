
var CCMAIN = function() {
    var tbl_cc_list = $('#tbl_cc_list', document);
    var tbl_cc_employee = $('#tbl_cc_employee', document);

    var init_cc_main = function() {

        init_tbl_cc_list();

        $('#input_designation', document).select2();
        $('#input_type', document).select2();

        PECO.DTDefault(tbl_cc_employee, 'Select CC first');

        tbl_cc_list.on('click', 'tbody tr td', function(e) {
            var this_ = $(this);
            var this_tr = this_.closest('tr');
            var this_ccid = $('td', this_tr).eq(0).text();
            init_tbl_cc_employee(this_ccid);
            $('tbody tr', tbl_cc_list).removeClass('row-info');
            this_tr.addClass('row-info');
            $('#frm_assign_employee', document).removeClass('hidden');
            $('#input_cc_id', document).val(this_ccid);
            $('#lastname', document).val('');
            $('#firstname', document).val('');
        });

        tbl_cc_employee.on('click', '#btn_del_emp', function(e) {
            var this_ = $(this);
            var this_tr = this_.closest('tr');
            var this_id = this_.attr('data-id');
            var this_ccid = this_.attr('data-ccid');
            $.ajax({
                url: PECO.base_url() + 'itd/deleteccemployee',
                type: 'post',
                data: {'empid': this_id, 'ccid': this_ccid},
                befreSend: function() {
                    this_tr.addClass('row-danger');
                }
            }).done(function() {
                this_tr.fadeOut('fast');
            });
        });

        $(document).on('submit', '#frm_assign_employee', function(e){
            e.preventDefault();
            var this_ = $(this);
            var this_ccid = $('#input_cc_id', this_).val();
            swal({
                title: "Are you sure you want to assign this employee?",
                text: 'Assign User',
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
                        url: this_.attr('action'),
                        type: this_.attr('method'),
                        data: this_.serialize(),
                        dataType: 'json',
                        beforeSend: function () {

                        }
                    }).done(function (d) {
                        if(d.qry==true) {
                            init_tbl_cc_employee(this_ccid);
                        }
                        PECO.initAlerts(d.msg, d.title, d.func);
                        swal.close();
                    }).fail(function () {
                        PECO.phpError();
                        swal.close();
                    });
                }else{
                    swal.close();
                }
            });
        });

        $(document).on('shown.bs.tab', '.nav-tabs a', function(e) {
           var this_ = $(this);
           var this_target = this_.attr('href');
           if(this_target == '#charts') {
               init_org_chart();
           }
        });
    };

    var init_org_chart = function() {
        var ds = {};
        $.ajax({
            url: PECO.base_url() + 'reports/getcompanyorgchart',
            type: 'post',
            data: {},
            dataType: 'json'
        }).done(function(d) {
            ds = d;
            /*
            var ds = {
                'name': 'Luis Miguel Cacho',
                'title': 'President - CEO',
                'children': [
                    { 'name': 'Bo Miao', 'title': 'department manager' },
                    { 'name': 'Su Miao', 'title': 'department manager',
                        'children': [
                            { 'name': 'Tie Hua', 'title': 'senior engineer' },
                            { 'name': 'Hei Hei', 'title': 'senior engineer',
                                'children': [
                                    { 'name': 'Pang Pang', 'title': 'engineer' },
                                    { 'name': 'Xiang Xiang', 'title': 'UE engineer' }
                                ]
                            }
                        ]
                    },
                    { 'name': 'Hong Miao', 'title': 'department manager' },
                    { 'name': 'Chun Miao', 'title': 'department manager' }
                ]
            };

             */

            $('#chart-container', document).html('');
            var oc = $('#chart-container').orgchart({
                'data' : ds,
                'nodeContent': 'title'
            });
        });




    };

    var init_tbl_cc_employee = function(ccid) {
        $.ajax({
            url: PECO.base_url() + 'itd/getccemployees',
            type: 'post',
            data: {'ccid': ccid},
            dataType: 'json',
            beforeSend: function() {
                PECO.DTphpLoading(tbl_cc_employee, 'Loading employee list...');
            }
        }).done(function(d) {

            $('#cc_text_head', document).text(d.headname);
            $('#cc_text_exec', document).text(d.execname);

            tbl_cc_employee.DataTable({
                bDestroy: true,
                bPaginate: true,
                bFilter: true,
                bInfo: true,
                aaData: d.list,
                bSort: true,
                pageLength: 10,
                saveState: true,
                "order": [[0, "desc"]],
                aoColumns: [
                    {"data": "id", sWidth: '', sClass: ''},
                    {"data": "code", sWidth: '', sClass: ''},
                    {"data": "name", sWidth: '', sClass: ''},
                    {"data": "pos", sWidth: '', sClass: ''},
                    {"data": "status", sWidth: '', sClass: ''},
                ]
            });
        });
    };


    var init_tbl_cc_list = function() {
        $.ajax({
            url: PECO.base_url() + 'itd/getcclist',
            type: 'post',
            data: {},
            dataType: 'json',
            beforeSend: function() {
                PECO.DTphpLoading(tbl_cc_list, 'Loading cost center list...');
            }
        }).done(function(d) {
            tbl_cc_list.DataTable({
                bDestroy: true,
                bPaginate: true,
                bFilter: true,
                bInfo: true,
                aaData: d.list,
                bSort: true,
                pageLength: 10,
                saveState: true,
                "order": [[0, "asc"]],
                aoColumns: [
                    {"data": "id", sWidth: '', sClass: ''},
                    {"data": "ccid", sWidth: '', sClass: 'text-warning bold'},
                    {"data": "code", sWidth: '', sClass: 'text-primary bold'},
                    {"data": "name", sWidth: '', sClass: ''},
                    {"data": "head", sWidth: '', sClass: ''},
                    {"data": "exec", sWidth: '', sClass: ''},
                ]
            });
        });
    };

    return {
        init: function() {
            init_cc_main();
        }
    }
}();
