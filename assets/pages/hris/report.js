var HRISREP = function () {

    var emplistreptbl = $(document).find('#emplistreptbl');

    var init_emp_list = function () {
        $.ajax({
            url:PECO.base_url()+'reports/getemplistreport',
            type:'post',
            dataType:'json'
        }).done(function (data) {
            emplistreptbl.dataTable().empty();
            emplistreptbl.dataTable({
                bDestroy: true,
                bPaginate: true,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                bProcessing: true,
                aaData: data.emplist,
                aoColumns: [
                    {"data":"num"},
                    {"data":"name" , sWidth:'200px'},
                    {"data":"dob" },
                    {"data":"age", sClass:'number'},
                    {"data":"dh", sClass:'number'},
                    {"data":"yos", sClass:'number', sWidth:'15px'},
                    {"data":"pos"},
                    {"data":"cc"},
                    {"data":"payclass"},
                    {"data":"mf"},
                    {"data":"idno"},
                    {"data":"hdmf"},
                    {"data":"tin"},
                    {"data":"sss"},
                    {"data":"phil"},
                    {"data":"addr"}
                ],
                searchHighlight: true
            });
        }).fail(function () {
            PECO.phpError();
        });
    };

    var init_print_emp_list = function () {
        PECO.select2Basic($('#input_jobcat',document),'reports/getjobcat','Select Job Category...');
        $(document).on('click','#print_registers',function () {
            var jobcat = $(document).find('#input_jobcat').val();
            $.ajax({
                url:PECO.base_url()+'reports/getemplistreport',
                type:'post',
                dataType:'json',
                data: {jobcat:jobcat}
            }).done(function (data) {
                // Open a new window for the printable table
                var win = window.open('', '_blank');
                var cnt = data.emplist.length;
                var html = '';
                html += '<title>Employee Masterfile</title>';
                html +=
                    '<head>' +
                    //'<title>'+reptitle+'</title>'+
                    '<link href="' + PECO.base_url() + 'assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>' +
                    '<link href="' + PECO.base_url() + 'assets/global/css/components.css" rel="stylesheet" type="text/css"/>' +
                    '<link href="' + PECO.base_url() + 'assets/global/css/plugins.css" rel="stylesheet" type="text/css"/>' +
                    '<link href="' + PECO.base_url() + 'assets/admin/layout/css/layout.css" rel="stylesheet" type="text/css"/>' +
                    '<link href="' + PECO.base_url() + 'assets/admin/layout/css/themes/default.css" rel="stylesheet" type="text/css"/>' +
                    '<link href="' + PECO.base_url() + 'assets/admin/layout/css/custom.css" rel="stylesheet" type="text/css"/>' +
                    '<style>body{margin: 0px 0px !important;  font-family: arial; background: #fff;}</style>' +
                    '</head>';

                html += '<body>';
                html += '<table class="table tbl-sm print-table-standard" id="emplistreptbl">';
                html += '<thead>';
                html += '<tr>';
                html += '<th colspan="16">';
                html += data.printheader;
                html += '<br>';
                html += '</th>';
                html += '</tr>';
                html += '<tr>';
                html += '<th>No.</th>';
                html += '<th>Name</th>';
                html += '<th>D.O.B</th>';
                html += '<th>Age</th>';
                html += '<th>Date Hired</th>';
                html += '<th>Yrs of Srvc</th>';
                html += '<th>Position</th>';
                html += '<th>CC</th>';
                html += '<th>Payclass</th>';
                html += '<th>M/F</th>';
                html += '<th>ID No.</th>';
                html += '<th>PAG-IBIG No.</th>';
                html += '<th>TIN No.</th>';
                html += '<th>SSS No.</th>';
                html += '<th>PHILHEALTH</th>';
                html += '<th>ADDRESS</th>';
                html += '</tr>';
                html += '</thead>';
                html += '<tbody>';
                for (var i=0; i<cnt; i++) {
                    html += '<tr>';
                    html += '<td style="padding-right: 25px">'+data.emplist[i].num+'</td>';
                    html += '<td style="padding-right: 25px">'+data.emplist[i].name+'</td>';
                    html += '<td style="padding-right: 25px">'+data.emplist[i].dob+'</td>';
                    html += '<td style="padding-right: 25px" class="number">'+data.emplist[i].age+'</td>';
                    html += '<td style="padding-right: 25px">'+data.emplist[i].dh+'</td>';
                    html += '<td style="padding-right: 25px" class="number">'+data.emplist[i].yos+'</td>';
                    html += '<td style="padding-right: 25px">'+data.emplist[i].pos+'</td>';
                    html += '<td style="padding-right: 25px">'+data.emplist[i].cc+'</td>';
                    html += '<td style="padding-right: 25px">'+data.emplist[i].payclass+'</td>';
                    html += '<td style="padding-right: 25px">'+data.emplist[i].mf+'</td>';
                    html += '<td style="padding-right: 25px">'+data.emplist[i].idno+'</td>';
                    html += '<td style="padding-right: 25px">'+data.emplist[i].hdmf+'</td>';
                    html += '<td style="padding-right: 25px">'+data.emplist[i].tin+'</td>';
                    html += '<td style="padding-right: 25px">'+data.emplist[i].sss+'</td>';
                    html += '<td style="padding-right: 25px">'+data.emplist[i].phil+'</td>';
                    html += '<td style="padding-right: 25px">'+data.emplist[i].addr+'</td>';
                    html += '</tr>';
                }
                html += '</tbody>';
                html += '</table>';
                html += '</body>';
                $(win.document.body).html(html);
            }).fail(function () {
                PECO.phpError();
            });
        });
    };

    var init_print_emp_list_summ = function () {
        $(document).on('click','#print_registers_summ',function () {
            var this_ = $(this);
            var command = this_.attr('command');
            $.ajax({
                url:PECO.base_url()+'reports/getemplistreport',
                type:'post',
                dataType:'json',
                data: {command:command}
            }).done(function (data) {
                //parse all response data as INT
                var mprob = parseInt(data.totals.mprob);
                var mreg = parseInt(data.totals.mreg);
                var fprob = parseInt(data.totals.fprob);
                var freg = parseInt(data.totals.freg);
                var regtotal = freg+mreg;
                var probtotal = mprob+fprob;
                var emptotal = regtotal+probtotal;
                var execcnt = parseInt(data.totals.execcnt);
                var sacnt = parseInt(data.totals.sacnt);
                var conficnt = parseInt(data.totals.conficnt);
                var rfcnt = parseInt(data.totals.rfcnt);
                var tieredcnt = parseInt(data.totals.tieredcnt);
                var subtotal = parseInt(data.totals.subt_reg);
                var ret_res = parseInt(data.totals.ret_res);

                // Open a new window for the printable table
                var win = window.open('', '_blank');
                var html = '';
                html += '<title>Employee Masterfile</title>';
                html +=
                    '<head>' +
                    //'<title>'+reptitle+'</title>'+
                    '<link href="' + PECO.base_url() + 'assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>' +
                    '<link href="' + PECO.base_url() + 'assets/global/css/components.css" rel="stylesheet" type="text/css"/>' +
                    '<link href="' + PECO.base_url() + 'assets/global/css/plugins.css" rel="stylesheet" type="text/css"/>' +
                    '<link href="' + PECO.base_url() + 'assets/admin/layout/css/layout.css" rel="stylesheet" type="text/css"/>' +
                    '<link href="' + PECO.base_url() + 'assets/admin/layout/css/themes/default.css" rel="stylesheet" type="text/css"/>' +
                    '<link href="' + PECO.base_url() + 'assets/admin/layout/css/custom.css" rel="stylesheet" type="text/css"/>' +
                    '<style>body{margin: 0px 0px !important;  font-family: arial; background: #fff;}</style>' +
                    '</head>';
                html += '<body>';
                html += data.printheader;
                html += 'Human Resource Department';
                html += '<br>';
                html += '<br>';
                html += 'SUMMARY OF EMPLOYEE';
                html += '<hr style="border: 1px solid">';
                html += '<div class="row">';
                html += '<div class="col-md-1"></div>';
                html += '<div class="col-md-3 center">';
                html += '<table class="table tbl-sm print-table-standard">';
                html += '<tr>';
                html += '<td></td>';
                html += '<td class="number">Probationary</td>';
                html += '<td class="number">Regular</td>';
                html += '</tr>';
                html += '<tr>';
                html += '<td>Male</td>';
                html += '<td class="number">'+mprob+'</td>';
                html += '<td class="number">'+mreg+'</td>';
                html += '</tr>';
                html += '<tr>';
                html += '<td>Female</td>';
                html += '<td class="number">'+fprob+'</td>';
                html += '<td class="number">'+freg+'</td>';
                html += '</tr>';
                html += '<tr class="border-bottom border-top bold">';
                html += '<td>Total :</td>';
                html += '<td class="number">'+probtotal+'</td>';
                html += '<td class="number">'+regtotal+'</td>';
                html += '</tr>';
                html += '<tr>';
                html += '<td>Total Employees :</td>';
                html += '<td class="number">'+emptotal+'</td>';
                html += '<td></td>';
                html += '</tr>';
                html += '</table>';
                html += '</div>';
                html += '<div class="col-md-2">';
                html += '</div>';
                html += '<div class="col-md-2">';
                html += '<table class="table tbl-sm print-table-standard">';
                html += '<tr>';
                html += '<td>EXECUTIVE</td>';
                html += '<td class="number">'+execcnt+'</td>';
                html += '</tr>';
                html += '<tr>';
                html += '<td>SUPERVISOR</td>';
                html += '<td class="number">'+sacnt+'</td>';
                html += '</tr>';
                html += '<tr>';
                html += '<td>CONFIDENTIAL</td>';
                html += '<td class="number">'+conficnt+'</td>';
                html += '</tr>';
                html += '<tr>';
                html += '<td>RANK IN FILE</td>';
                html += '<td class="number">'+rfcnt+'</td>';
                html += '</tr>';
                html += '<tr>';
                html += '<td>TIERED</td>';
                html += '<td class="number">'+tieredcnt+'</td>';
                html += '</tr>';
                html += '<tr class="bold">';
                html += '<td>SUB - TOTAL</td>';
                html += '<td class="number">'+subtotal+'</td>';
                html += '</tr>';
                html += '<tr><td colspan="2" class="border-left-0 border-right-0"> <br> </td></tr>';
                html += '<tr>';
                html += '<td>PROBATIONARY</td>';
                html += '<td class="number">'+probtotal+'</td>';
                html += '</tr>';
                html += '<tr>';
                html += '<td>RETIRED/RESIGNED</td>';
                html += '<td class="number">'+ret_res+'</td>';
                html += '</tr>';
                html += '</table>';
                html += '</div>';
                html += '</div>';
                html += '</body>';
                $(win.document.body).html(html);
            }).fail(function () {
                PECO.phpError();
            });
        });
    };

    return{
        init:function () {
            init_emp_list();
            init_print_emp_list();
            init_print_emp_list_summ();
        }
    }
}();