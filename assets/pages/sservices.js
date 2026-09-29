// LUCKY JOHN FADERON
// 4/12/2018
// ####################

var SServcies = function() {

    var tbl_assessments = $('#tbl_assesstments', document);


    var init_fn = function() {
        var lastname = $('#lastname', document);
        var firstname = $('input#firstname', document);
        var middlename = $('input#middlename', document);
        var address = $('input#address', document);
        var personid = $('input#personid', document);
        var types = $('input#types', document);
        PECO.select2Basic($('#select2services'), 'admin/select2getservices', 'Select service', true, false);
        PECO.DTDefault(tbl_assessments, 'Start Assessement first!');

        var pecocheck = $('#icheck', document);

        pecocheck.iCheck('uncheck');
        // DEFULT SELECT TAGGING


        //PECO.servicesTagging(lastname, 'Person Lastname..', 1);

        $(document).on('ifChecked', '#icheck', function(){
            var this_ = $(this);
            this_.attr('checked', true);
            PECO.servicesTagging(lastname, 'Service Number..', 2);
        }).on('ifUnchecked', '#icheck', function(){
            var this_ = $(this);
            this_.attr('checked', false);
            PECO.servicesTagging(lastname, 'Person Lastname..', 1);
        });




        lastname.change(function(e) {
            e.preventDefault();
            var this_ = $(this);
            var this_val = this_.select2('val');
            if(this_val.length <= 1) {
                var pecocheck = $('#icheck:checked', document).length;
                $.ajax({
                    url: PECO.base_url() + 'cad/serviceslastname',
                    type: 'post',
                    dataType: 'json',
                    data: {'selectarr': this_val},
                }).done(function (d) {
                    if (d.exists == true) {
                        firstname.val(d.firstname).attr('disabled', true);
                        middlename.val(d.middlename).attr('disabled', true);
                        address.val(d.address).attr('disabled', true);
                        personid.val(d.sysid);
                        types.val(d.type);
                    } else {
                        firstname.val('').attr('disabled', false);
                        middlename.val('').attr('disabled', false);
                        address.val('').attr('disabled', false);
                        personid.val(0);
                        types.val(0);
                    }
                });
            }else{
                PECO.initAlerts('Single Person only!', 'PECO.net', 'info');
                this_.select2('val', '');
                firstname.val('').attr('disabled', false);
                middlename.val('').attr('disabled', false);
                address.val('').attr('disabled', false);
                personid.val(0).attr('disabled', false);
                types.val(0).attr('disabled', false);
            }
        });

        $('#frm_start_assessment', document).submit(function(e) {
            e.preventDefault();
            var form = $(this);
            $.ajax({
                url: form.attr('action'),
                type: form.attr('method'),
                dataType: 'json',
                data: form.serialize(),
            }).done(function(d){
                PECO.initAlerts(d.msg, 'PECO.net', d.func);
                if(d.qry==true) {
                    $('#servustdataid').val(d.dataid);
                    $('#servcustname').text(d.custname);
                    $('#servcustaddr').text(d.address);
                    $('#servcustservno').text(d.servno);
                    init_tbl_assesstments(d.dataid, d.moduleid);
                    lastname.val(null).trigger("change");
                    $('.select2-search-choice', document).remove();
                }
            }).fail(function(){
                PECO.phpError();
            });
        });
        $('#frm_add_services', document).submit(function(e) {
            e.preventDefault();
            var form = $(this);
            $.ajax({
                url: form.attr('action'),
                type: form.attr('method'),
                dataType: 'json',
                data: form.serialize(),
            }).done(function(d){
                PECO.initAlerts(d.msg, 'PECO.net', d.func);
                if(d.qry==true) {
                    init_tbl_assesstments(d.dataid, d.moduleid);
                }
            }).fail(function(){
                PECO.phpError();
            });
        });


        $(document).on('click','#print_application_cost_assesment',function (e) {
            e.preventDefault();
            var this_ = $(this);
            var html = '';
            var i = 0;
            var title = "Application Cost Assessment";

            var dataid = this_.attr('data-id');
            var moduleid = 106;

            $.ajax({
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

            });

        });

    };

    var init_tbl_assesstments = function(dataid, moduleid) {
        $.ajax({
            url: PECO.base_url() + 'cad/getcustomerservices',
            type: 'post',
            data: {'appid': dataid, 'moduleid': moduleid},
            dataType: 'json',
        }).done(function(data){
                $('#print_application_cost_assesment').attr('data-id', dataid);
                $('#servcnt').html(data.qty);
                $('#servamt').html(data.servamt);
                $('#assesstmentvat').html(data.totalvat);
                $('#assesstmentnovat').html(data.totalnvat);
                $('#assessmenttotalamt').html(data.total);
                $('#assessmenttotalamtpaid').html(data.totalpaid);
                $('#assessmenttotalamtbal').html(data.balance);
                $('#servgrandtotal').html(data.total);
                tbl_assessments.dataTable().empty();
                tbl_assessments.dataTable({
                    // Internationalisation. For more info refer to http://datatables.net/manual/i18n
                    bDestroy: true,
                    bPaginate: false,
                    bFilter: true,
                    bInfo: false,
                    bStateSave: true,
                    bProcessing: true,
                    scrollY: '300px',
                    aaData: data.list,
                    aoColumns: [
                        {"data": "num"},
                        {"data": "acctno", sWidth: '100px', sClass: '' },
                        {"data": "acctname", sWidth: '', sClass: '' },
                        {"data": "vat", sWidth: '', sClass: 'text-danger number' },
                        {"data": "nonvat", sWidth: '', sClass: 'number' },
                        {"data": "total", sWidth: '150px', sClass: 'number text-bold' },
                        {"data": "control", sWidth: '20px', sClass: 'hidden-print text-align-center' },
                    ],
                    fnRowCallback: function(nRow, data) {
                        // RE-INITIALIZE TOOLTIPS
                        $(nRow).find('.tooltips').tooltip();
                        $(nRow).find('td').addClass(data.rowclass);
                    },
                    "language": PECO.DTEmptyMessage(),
                });
                PECO.initDTNicescroller();
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
                    init_tbl_assesstments(dataid, moduleid);
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
        init: function() {
            init_fn();
        }
    }

}();
