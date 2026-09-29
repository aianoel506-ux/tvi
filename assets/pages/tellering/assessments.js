var ASSESSMENTS = function() {
    PECO.getSelect2Plugins();
    PECO.getNumberFormatPlugin();
    var tbl_assessments = $('#tbl_assesstments');
    var frm_assessments = $('#frm_add_services');

    var init_payments_assessments = function(dataid, moduleid) {

        $(document).on('click','#print_application_cost_assesment',function (e) {
            e.preventDefault();
            var html = '';
            var i = 0;
            var title = "Application Cost Assessment";


            /*$.ajax({
                url: PECO.base_url() + "cad/getcustomerservices",
                type: 'post',
                data: {appid: dataid, moduleid: moduleid},
                dataType: 'json'
            }).done(function (d) {
                var cnt = d.list.length;

                html += d.printhead;
                html += '<div class="container">';
                html += '<div class="row">';
                html += '<h4 class="col-md-6 col-xs-6">Summary of Cost</h4>';
                html += '<h4 class="col-md-6 col-xs-6 text-align-right pull-right">'+d.essrno+'</h4>';
                html += '</div>';
                html += '<table class="table table-bordered table-condensed">';
                html += '<thead>';
                    html += '<th></th>';
                    html += '<th>Account Code</th>';
                    html += '<th width="30%">Account Name</th>';
                    html += '<th>Amount No-VAT</th>';
                    html += '<th>VAT Amount</th>';
                    html += '<th>Total</th>';
                    html += '<th>CWT</th>';
                    html += '<th>New Total</th>';

                html += '</thead>';
                html += '<tbody>';

                for(i=0;i<cnt;i++){
                    html += '<tr><td>'+d.list[i]["num"]+'</td>';
                    html += '<td>'+d.list[i]["acctno"]+'</td>';
                    html += '<td>'+d.list[i]["acctname"]+'</td>';
                    html += '<td align="right">'+d.list[i]["vat"]+'</td>';
                    html += '<td align="right">'+d.list[i]["nonvat"]+'</td>';
                    html += '<td align="right">'+d.list[i]["total"]+'</td>';
                    html += '<td align="right"></td>';
                    html += '<td align="right"></td>';
                    html += '</tr>';
                }
                html += '</tbody>';
                html += '</table>';

                html += '<div class="row">';
                    html += '<div class="col-md-6 col-xs-6">';
                            html += '<h5>Printed by:</h5>';

                            html += '<h5 style="margin-top: 50px !important;">'+d.printedby+'</h5>';
                            html += '<h5>'+d.dateprinted +'</h5>';
                    html += '</div>';

                    html += '<div class="col-md-6 col-xs-6 pull-right">';
                            html += '<table class="table table-condensed tbl-xs tbl-zoom">';
                            html += '<thead>';

                            html += '</thead>';
                            html += '<tbody>';

                            html += '<tr>';
                            html += '<td>Amount (no-vat)</td>';
                            html += '<td>:</td>';
                            html += '<td align="right">'+d.totalnvat+'</td>';
                            html += '</tr>';

                            html += '<tr>';
                            html += '<td>Total Vat</td>';
                            html += '<td>:</td>';
                            html += '<td align="right">'+d.totalvat+'</td>';
                            html += '</tr>';

                            html += '<tr>';
                            html += '<td>Total Amount</td>';
                            html += '<td>:</td>';
                            html += '<td align="right">'+d.total+'</td>';
                            html += '</tr>';

                            html += '<tr>';
                            html += '<td width="70%">Total Amount (Paid)</td>';
                            html += '<td>:</td>';
                            html += '<td align="right">('+d.totalpaid+')</td>';
                            html += '</tr>';

                            html += '<tr>';
                            html += '<td>Total Balance</td>';
                            html += '<td>:</td>';
                            html += '<td align="right">'+d.balance+'</td>';
                            html += '</tr>';

                            html += '</tbody>';
                            html += '</table>';
                    html += '</div>';
                html += '</div>';




                html += '</div>';
                 PECO.pecoRepPrint(title , html, false);

            });*/

            $.ajax({
                url: base_url + 'cad/summaryofcost',
                dataType: 'json',
                type: 'post',
                data: {
                    appid : dataid
                }
            }).done(function (d) {
                var win = window.open('','_blank');
                win.document.write(d.html);

            }).fail(function () {
                var win = window.open('','');
                win.document.write('<p>Error on script.</p>');
            });
        });

        $(document).on('change','#acctcode',function () {
            //alert('changed!');
            $('#acctamt').removeAttr('value');
        });

        PECO.DTDefault(tbl_assessments, 'No services / materials added yet!');
        $(document).ready(function () {
            //elem, url, placeholder, full, allowall, selectedval
            PECO.select2Basic($('#acctcode'), 'query/getselect2chartofaccounts', 'Account Code', true, false, 0);
            setTimeout(function () {
                $('#acctamt').number(true, 2);
            }, 500);
        });
        init_tbl_assessments(dataid, moduleid);

        frm_assessments.submit(function(e) {
            e.preventDefault();
            var this_ = $(this);
            $.ajax({
                url: this_.attr('action'),
                type: this_.attr('method'),
                data: this_.serialize(),
                dataType: 'json',
                beforeSend: function() {

                }
            }).done(function(d){
                PECO.initAlerts(d.msg, 'Add Services/Materials', d.func, false, false);
                $('#acctamt').removeAttr('value');
                init_tbl_assessments(dataid, moduleid);
            }).fail(function(){
                PECO.error();
            });
        });
    };

    var init_assessment_list = function(dataid, moduleid) {
        init_tbl_assessments(dataid, moduleid, 2);
    };

    var init_tbl_assessments = function(dataid, moduleid, viewtype) {
        var viewtype_ = (viewtype) ? viewtype : false;
        var view_control = (viewtype_ == 2) ? 'hidden' : '';
        $.ajax({
            url: PECO.base_url() + 'cad/getcustomerservices',
            type: 'post',
            data: {
                appid: dataid,
                moduleid: moduleid,
                viewtype: viewtype_
            },
            dataType: 'json',
            beforeSend: function() {
                PECO.DTphpLoading(tbl_assessments, 'Loading assessments data... ');
            }
        }).done(function(data){
            if(data.qry==true) {
                $('#servcnt', document).html(data.qty);
                $('#servamt', document).html(data.servamt);
                $('#assesstmentvat', document).html(data.totalvat);
                $('#assesstmentinitamt', document).html(data.initdepamt);
                $('#assesstmentgdramt', document).html(data.gdrdepamt);
                $('#assesstmentservicelaboramt', document).html(data.laborservamt);
                $('#assesstmentnovat', document).html(data.totalnvat);
                $('#assessmenttotalamt', document).html(data.total);
                $('#assessmenttotalamtpaid', document).html(data.totalpaid);
                $('#assessmenttotalamtbal', document).html(data.balance);
                $('#servgrandtotal', document).html(data.total);
                $('#ar_btn', document).html(data.arbtn);
                // tbl_assessments.dataTable().empty();
                tbl_assessments.DataTable({
                    // Internationalisation. For more info refer to http://datatables.net/manual/i18n
                    bDestroy: true,
                    bPaginate: false,
                    bFilter: (viewtype_ == 2) ? false : true,
                    bInfo: false,
                    bStateSave: true,
                    bProcessing: true,
                    scrollY: '300px',
                    aaData: data.list,
                    aoColumns: [
                        {"data": "num", sWidth: '10px'},
                        {"data": "acctno", sWidth: '100px', sClass: '' },
                        {"data": "acctname", sWidth: '200', sClass: 'text-primary' },
                        {"data": "nonvat", sWidth: '80px', sClass: 'number' },
                        {"data": "vat", sWidth: '70px', sClass: 'text-danger number' },
                        {"data": "total", sWidth: '80px', sClass: 'number text-bold' },
                        {"data": "control", sWidth: '20px', sClass: 'hidden-print text-align-center ' + view_control },
                    ],
                    fnRowCallback: function(nRow, data) {
                        // RE-INITIALIZE TOOLTIPS
                        $(nRow).find('.tooltips').tooltip();
                        $(nRow).find('td').addClass(data.rowclass);
                    }
                });
                PECO.initDTNicescroller();
            }else{
                PECO.DTDefault(tbl_assessments, 'No assessment yet!');
            }

        });

        tbl_assessments.on('click', '.btn_del', function(e){
            e.preventDefault();
            var this_ = $(this);
            $.ajax({
                url: PECO.base_url()+'cad/delservicesfee',
                type: 'post',
                data: {'id': this_.attr('data-id')},
                dataType: 'json',
            }).done(function(d){
                if(d.qry==true){
                    this_.closest('tr').fadeOut('fast');
                    init_tbl_assessments(dataid, moduleid);
                }
            }).fail(function(){
                PECO.phpError();
            });
        });

        if(PECO.sysCheckMode()==true) {
            console.log('Services Table Loaded!');
        }
    };

    return {
        init: function(dataid, moduleid) {
            init_payments_assessments(dataid, moduleid);
        },
        list: function(dataid, moduleid) {
            init_assessment_list(dataid, moduleid);
        }
    }
}();
