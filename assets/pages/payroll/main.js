var PAYROLL = function() {

    PECO.getHighlightsPlugin();
    PECO.getNumberFormatPlugin();
    PECO.getiCheckPlugin();
    PECO.getNumberFormatPlugin();
    PECO.getSweetAlert();
    PECO.getSelect2Plugins();

    var payroll_tbl = $('#payroll_table', document);
    var payroll_tbldt = $('#payroll_table', document).DataTable();
    var frm_process_payroll = $('#frm_process_payroll', document);
    var payrollreportstbl = $('#payrollreportstbl' , document);

    var confidentialtabletax = $('#confidentialtabletax',document);
    var rankandfiletabletax = $('#rankandfiletabletax',document);

    var rankandfiletablehdmf = $('#rankandfiletablehdmf',document);
    var confidentialtablehdmf = $('#confidentialtablehdmf',document);

    var rankandfiletablesssloan = $('#rankandfiletablesssloan',document);
    var confidentialtablesssloan = $('#confidentialtablesssloan',document);

    var rankandfiletablessscont = $('#rankandfiletablessscont',document);
    var confidentialtablessscont = $('#confidentialtablessscont',document);

    var rankandfiletablepecewa = $('#rankandfiletablepecewa',document);
    var confidentialtablepecewa = $('#confidentialtablepecewa',document);

    var rankandfiletablecoop = $('#rankandfiletablecoop',document);
    var confidentialtablecoop = $('#confidentialtablecoop',document);

    //recheck tables
    var payrollbreakdownloan = $('#payrollbreakdownloan',document);
    var payrolltransactionval = $('#payrolltransactionval',document);

    var payrollregistertable = $(document).find('#payrollregistertable2');
    var earningregistertable = $(document).find('#earningregistertable2');
    var deductionsregistertable = $(document).find('#deductionsregistertable2');
    var overtimeregistertable = $(document).find('#overtimeregistertable2');
    var annualregtbl = $(document).find('#annualregtbl');
    var net15table = $(document).find('#net15table');
    var net1530table = $(document).find('#net1530table');

    //for preview printing
    var payrollregpreviewtrn = $(document).find('#payrollregpreviewtrn');
    var earningregpreviewtrn = $(document).find('#earningregpreviewtrn');
    var deductionsregpreviewtrn = $(document).find('#deductionsregpreviewtrn');
    var overtimeregpreviewtrn = $(document).find('#overtimeregpreviewtrn');

    // var dataid = $(document).find('#payrolldataid').val();
    var getpayrollpreviewtrn = function (payclass) {
        var year = $(document).find('#periodyear').val();
        var month = $(document).find('#select2month').val();
        var paytype = $(document).find('#select2paytype').val();

        $.ajax({
            url:PECO.base_url()+'payroll/getpreviewtrn',
            type:'post',
            data:{"year":year,"month" : month,"paytype":paytype,"payclass" : payclass},
            dataType:'json'
        }).done(function (data) {
            payrollregpreviewtrn.dataTable().empty();
            payrollregpreviewtrn.dataTable({
                bDestroy: true,
                bPaginate: false,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                bProcessing: true,
                aaData: data.payrollreg,
                aoColumns: [
                    {"data":"expand"},
                    {"data":"codes" , sClass:'text-info'},
                    {"data":"grossear", sClass:'number'},
                    {"data":"totalded", sClass:'number'},
                    {"data":"totalnet", sClass:'number'},
                    {"data":"ssscont", sClass:'number'},
                    {"data":"sssloan", sClass:'number'},
                    {"data":"hdmfcont", sClass:'number'},
                    {"data":"hdmfloan", sClass:'number'},
                    {"data":"pecewaloan", sClass:'number'},
                    {"data":"cooploan", sClass:'number'},
                    {"data":"pagibigadd", sClass:'number'},
                    {"data":"otherded", sClass:'number'},
                    {"data":"hmoded", sClass:'number'},
                    {"data":"deda", sClass:'number'},
                    {"data":"electbill", sClass:'number'},
                    {"data":"memins", sClass:'number'},
                    {"data":"lwop", sClass:'number'},
                    {"data":"tax", sClass:'number'}
                ],
                searchHighlight: true
            });
            earningregpreviewtrn.dataTable().empty();
            earningregpreviewtrn.dataTable({
                bDestroy: true,
                bPaginate: false,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                bProcessing: true,
                aaData: data.earningreg,
                aoColumns: [
                    {"data":"expand"},
                    {"data":"codes" , sClass:'text-info'},
                    {"data":"basicrate", sClass:'number'},
                    {"data":"cola", sClass:'number'},
                    {"data":"transallw", sClass:'number'},
                    {"data":"ricesubsi", sClass:'number'},
                    {"data":"holiday", sClass:'number'},
                    {"data":"nitediff", sClass:'number'},
                    {"data":"otpay", sClass:'number'},
                    {"data":"actingallw", sClass:'number'},
                    {"data":"otheradd", sClass:'number'}
                ],
                searchHighlight: true
            });
            deductionsregpreviewtrn.dataTable().empty();
            deductionsregpreviewtrn.dataTable({
                bDestroy: true,
                bPaginate: false,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                bProcessing: true,
                aaData: data.deductionreg,
                aoColumns: [
                    {"data":"expand"},
                    {"data":"codes" , sClass:'text-info'},
                    {"data":"ssscont", sClass:'number'},
                    {"data":"sssloan", sClass:'number'},
                    {"data":"hdmfcont", sClass:'number'},
                    {"data":"hdmfloan", sClass:'number'},
                    {"data":"pecewaloan", sClass:'number'},
                    {"data":"cooploan", sClass:'number'},
                    {"data":"pagibigadd", sClass:'number'},
                    {"data":"otherded", sClass:'number'},
                    {"data":"hmodedn", sClass:'number'},
                    {"data":"deda", sClass:'number'},
                    {"data":"electbill", sClass:'number'},
                    {"data":"memins", sClass:'number'},
                    {"data":"lwop", sClass:'number'},
                    {"data":"basetax", sClass:'number'},
                ],
                searchHighlight: true
            });

            overtimeregpreviewtrn.dataTable().empty();
            overtimeregpreviewtrn.dataTable({
                bDestroy: true,
                bPaginate: false,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                bProcessing: true,
                aaData: data.overtimereg,
                aoColumns: [
                    {"data":"expand"},
                    {"data":"codes" , sClass:'text-info'},
                    {"data":"ndot8hrs", sClass:'number'},
                    {"data":"ndot8pay", sClass:'number'},
                    {"data":"othrs", sClass:'number'},
                    {"data":"125%", sClass:'number'},
                    {"data":"130%", sClass:'number'},
                    {"data":"150%", sClass:'number'},
                    {"data":"160%", sClass:'number'},
                    {"data":"180%", sClass:'number'},
                    {"data":"210%", sClass:'number'},
                    {"data":"230%", sClass:'number'},
                    {"data":"260%", sClass:'number'}
                ],
                searchHighlight: true
            });
            'use strict';
            const object = data.payrollres[0];
            for (const [key, value] of Object.entries(object)) {
                console.log(key, value);
                $(document).find('#'+key).text(value.toFixed(2));
            }

        }).fail(function () {
            PECO.phpError();
        });
    };

    var init_payroll = function(payclass) {

        PECO.DTDefault(payrollregistertable, "Click <b>Get</b> to load data.");
        PECO.DTDefault(earningregistertable, "Click <b>Get</b> to load data.");
        PECO.DTDefault(deductionsregistertable, "Click <b>Get</b> to load data.");
        PECO.DTDefault(overtimeregistertable, "Click <b>Get</b> to load data.");
        //by department totals

        $(document).on('change','#select2paytype,#select2month,#periodyear',function () {
            getpayrollpreviewtrn(payclass);
        });

        $(document).on('click','#printpayreg',function (e) {
            e.preventDefault();

            var this_ = $(this);
            var this_html = this_.html();

            var payrollyear  = $(document).find('#payrollyear').val();
            var payrollmonth = $(document).find('#payrollmonth').val();
            var payrollpayclass = $(document).find('#payrollpayclass').val();
            var payrollpaytype = $(document).find('#payrollpaytype').val();
            $.ajax({
                url:PECO.base_url()+'payroll/getpayrollregisterdata',
                data:{"payrollyear": payrollyear ,"payrollmonth":payrollmonth , "payrollpayclass":payrollpayclass , "payrollpaytype":payrollpaytype},
                type:'post',
                dataType:'json',
                beforeSend: function() {
                    PECO.btnLoading(this_, 'Processing...');
                }
            }).done(function (d) {
                PECO.btnSuccess(this_, 'Done', this_html, 'btn-primary');
                var count = 0;
                var html = '';
                var index = 0;
                count = d.datacount;

                html+= d.header;

                html+= '<div class="row">';
                html+= '<div class="col-md-12 cold-sm-12 col-xs-12 col-lg-12">';

                html+= '<table class="table table-condensed tbl-xs print-table-standard">';
                html+= '<thead>';

                html+= '<th>DEPT CODE</th>';
                html+= '<th>GROSS EARNINGS</th>';
                html+= '<th>TOTAL DEDN</th>';
                html+= '<th>TOTAL NET</th>';
                html+= '<th>SSS CONT</th>';
                html+= '<th>SSS LOAN</th>';
                html+= '<th>HDMF CONT</th>';
                html+= '<th>HDMF LOAN</th>';
                html+= '<th>PECEWA LOAN</th>';
                html+= '<th>COOP LOAN</th>';
                html+= '<th>PAGIBIG ADD</th>';
                html+= '<th>OTHER DEDN</th>';
                html+= '<th>HMO DEDN</th>';
                html+= '<th>DED A</th>';
                html+= '<th>ELECTRIC BILL</th>';
                html+= '<th>MEM INS</th>';
                html+= '<th>LWOP</th>';
                html+= '<th>BASE TAX</th>';

                html+= '</thead>';



                html+= '<tbody>';

                for(index=0;index<count;index++) {
                    html += '<tr>';
                    html += '<td>' + d.payrollregisterdata[index].deptcode + '</td>';
                    html += '<td class="number">' + d.payrollregisterdata[index].grossearnings + '</td>';
                    html += '<td class="number">' + d.payrollregisterdata[index].totaldedn + '</td>';
                    html += '<td class="number">' + d.payrollregisterdata[index].totalnet + '</td>';
                    html += '<td class="number">' + d.payrollregisterdata[index].ssscont + '</td>';
                    html += '<td class="number">' + d.payrollregisterdata[index].sssloan + '</td>';
                    html += '<td class="number">' + d.payrollregisterdata[index].hdmfcont + '</td>';
                    html += '<td class="number">' + d.payrollregisterdata[index].hdmfloan + '</td>';
                    html += '<td class="number">' + d.payrollregisterdata[index].pecewaloan + '</td>';
                    html += '<td class="number">' + d.payrollregisterdata[index].cooploan + '</td>';
                    html += '<td class="number">' + d.payrollregisterdata[index].pagibigadd + '</td>';
                    html += '<td class="number">' + d.payrollregisterdata[index].otherdeductions + '</td>';
                    html += '<td class="number">' + d.payrollregisterdata[index].hmodedn + '</td>';
                    html += '<td class="number">' + d.payrollregisterdata[index].deda + '</td>';
                    html += '<td class="number">' + d.payrollregisterdata[index].electricbill + '</td>';
                    html += '<td class="number">' + d.payrollregisterdata[index].memins + '</td>';
                    html += '<td class="number">' + d.payrollregisterdata[index].lwop + '</td>';
                    html += '<td class="number">' + d.payrollregisterdata[index].basetax + '</td>';
                    html += '</tr>';
                }

                html+= '</tbody>';

                html+= '<tfoot>';
                html+= '<tr>';
                html+= '<td>Total Result</td>';
                html+= '<td class="number bold">'+d.resultdeptgrossearnings+'</td>';
                html+= '<td class="number bold">'+d.resultdepttotaldedn+'</td>';
                html+= '<td class="number bold">'+d.resultdeptnet+'</td>';
                html+= '<td class="number bold">'+d.resultdeptssscont+'</td>';
                html+= '<td class="number bold">'+d.resultdeptsssloan+'</td>';
                html+= '<td class="number bold">'+d.resultdepthdmfcont+'</td>';
                html+= '<td class="number bold">'+d.resultdepthdmfloan+'</td>';
                html+= '<td class="number bold">'+d.resultdeptpecewaloan+'</td>';
                html+= '<td class="number bold">'+d.resultdeptcooploan+'</td>';
                html+= '<td class="number bold">'+d.resultpagibigadd+'</td>';
                html+= '<td class="number bold">'+d.resultotherdeduction+'</td>';
                html+= '<td class="number bold">'+d.resultdepthmodedn+'</td>';
                html+= '<td class="number bold">'+d.resultdeptdeda+'</td>';
                html+= '<td class="number bold">'+d.resultdeptelectricbill+'</td>';
                html+= '<td class="number bold">'+d.resultdeptmemins+'</td>';
                html+= '<td class="number bold">'+d.resultdeptlwop+'</td>';
                html+= '<td class="number bold">'+d.resultdeptbasetax+'</td>';
                html+= '</tr>';
                html+= '</tfoot>';

                html+= '</table>';
                html+= '</div>';
                html+= '</div>';

                html+= '<div class="row">';
                html+= '<div class="col-md-2  col-sm-2 col-xs-2 col-lg-2">';
                html+= '<div>Encoded by:</div>';
                html+= '<div>____________</div>';
                html+= '<div>HRDH</div>';
                html+= '</div>';
                html+= '<div class="col-md-2  col-sm-2 col-xs-2 col-lg-2">';
                html+= '<div>Checked by:</div>';
                html+= '<div>____________</div>';
                html+= '<div>GA</div>';
                html+= '</div>';
                html+= '<div class="col-md-2 col-sm-2 col-xs-2 col-lg-2">';
                html+= '<div>Noted by:</div>';
                html+= '<div>____________</div>';
                html+= '<div>FM</div>';
                html+= '</div>';
                html+= '<div class="col-md-3  col-sm-3 col-xs-3 col-lg-3">';
                html+= '</div>';
                html+= '<div class="col-md-3  col-sm-3 col-xs-3 col-lg-3">';
                html+= '<div>Approved by:</div>';
                html+= '<div>____________</div>';
                html+= '<div>P-CEO</div>';
                html+= '</div>';
                html+= '</div>';

                pecoRepPrint("Payroll Register" , html);
            }).fail(function () {
                PECO.phpError();
            });

        });
        frm_process_payroll.submit(function (e) {
            e.preventDefault();
            var form = $(this);
            swal({
                title: "Are you sure?",
                text: "Payroll can be reverted via administrator's process!",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes, Process!",
                closeOnConfirm: false,
                closeOnCancel: false,
                showLoaderOnConfirm: true
            }, function(isConfirm){
                if (isConfirm) {
                    $.ajax({
                        url: form.attr('action'),
                        type: form.attr('method'),
                        data: form.serialize(),
                        dataType: 'json'
                    }).done(function (d) {
                        init_payroll_table(payclass , d.viewtype);
                        swal("Generated!", "Payroll has been generated!", "success");
                    });
                } else {
                    swal("Cancelled", "Payroll processing canceled", "error");
                }
            });
        });
        $(document).on('click','#printdednregbyemp',function (e) {
            e.preventDefault();

            var this_ = $(this);
            var this_html = this_.html();

            var payrollyear  = $(document).find('#payrollyear').val();
            var payrollmonth = $(document).find('#payrollmonth').val();
            var payrollpayclass = $(document).find('#payrollpayclass').val();
            var payrollpaytype = $(document).find('#payrollpaytype').val();
            $.ajax({
                url:PECO.base_url()+'payroll/getprintdednregbyemp',
                data:{"payrollyear": payrollyear ,"payrollmonth":payrollmonth , "payrollpayclass":payrollpayclass , "payrollpaytype":payrollpaytype},
                type:'post',
                dataType:'json',
                beforeSend: function() {
                    PECO.btnLoading(this_, 'Processing...');
                }
            }).done(function (d) {
                pecoRepPrint("Deductions Register By Employee" , d.html);
                PECO.btnSuccess(this_, 'Done', this_html, 'btn-primary');
            }).fail(function () {
                PECO.phpError();
            });
        });
        $(document).on('click','#printearregbyemp',function (e) {
            e.preventDefault();

            var this_ = $(this);
            var this_html = this_.html();

            var payrollyear  = $(document).find('#payrollyear').val();
            var payrollmonth = $(document).find('#payrollmonth').val();
            var payrollpayclass = $(document).find('#payrollpayclass').val();
            var payrollpaytype = $(document).find('#payrollpaytype').val();
            $.ajax({
                url:PECO.base_url()+'payroll/getprintearregbyemp',
                data:{"payrollyear": payrollyear ,"payrollmonth":payrollmonth , "payrollpayclass":payrollpayclass , "payrollpaytype":payrollpaytype},
                type:'post',
                dataType:'json',
                beforeSend: function() {
                    PECO.btnLoading(this_, 'Processing...');
                }
            }).done(function (d) {
                pecoRepPrint("Eearning Register By Employee" , d.html);
                PECO.btnSuccess(this_, 'Done', this_html, 'btn-primary');
            }).fail(function () {
                PECO.phpError();
            });
        });
        $(document).on('click','#printpayregbyemp',function (e) {
            e.preventDefault();

            var this_ = $(this);
            var this_html = this_.html();

            var payrollyear  = $(document).find('#payrollyear').val();
            var payrollmonth = $(document).find('#payrollmonth').val();
            var payrollpayclass = $(document).find('#payrollpayclass').val();
            var payrollpaytype = $(document).find('#payrollpaytype').val();
            $.ajax({
                url:PECO.base_url()+'payroll/getprintpayregbyemp',
                type:'post',
                data:{"payrollyear": payrollyear ,"payrollmonth":payrollmonth , "payrollpayclass":payrollpayclass , "payrollpaytype":payrollpaytype},
                dataType:'json' ,
                beforeSend: function() {
                    PECO.btnLoading(this_, 'Processing...');
                }
            }).done(function (data) {
                pecoRepPrint("Payroll Register By Employee" , data.html);
                PECO.btnSuccess(this_, 'Done', this_html, 'btn-primary');
            }).fail(function () {
                PECO.phpError();
            });
        });
        $(document).on('click','#printearregdepttotals',function (e) {
            e.preventDefault();

            var this_ = $(this);
            var this_html = this_.html();

            var payrollyear  = $(document).find('#payrollyear').val();
            var payrollmonth = $(document).find('#payrollmonth').val();
            var payrollpayclass = $(document).find('#payrollpayclass').val();
            var payrollpaytype = $(document).find('#payrollpaytype').val();
            $.ajax({
                url:PECO.base_url()+'payroll/getearningsreport',
                data:{"payrollyear": payrollyear ,"payrollmonth":payrollmonth , "payrollpayclass":payrollpayclass , "payrollpaytype":payrollpaytype},
                type:'post',
                dataType:'json',
                beforeSend: function() {
                    PECO.btnLoading(this_, 'Processing...');
                }
            }).done(function (d) {
                PECO.btnSuccess(this_, 'Done', this_html, 'btn-primary');

                var count = 0;
                var html = '';
                var index = 0;
                var code = 401;

                if(d.payclass == 128){
                    code = 402;
                }else if(d.payclass == 1 || d.payclass == 129){
                    code = 401;
                }

                count = d.datacount;

                html+= d.header;
                html+= '<div class="row">';
                html+= '<div class="col-md-12 cold-sm-12 col-xs-12 col-lg-12">';



                html+= '<table class="table table-condensed tbl-xs print-table-standard" id="earningregistertable">';
                html+= '<thead>';
                html+= '<tr>';

                html+= '<th></th>';
                html+= '<th>'+code+'</th>';
                html+= '<th>410</th>';
                html+= '<th>'+code+'</th>';
                html+= '<th>410</th>';
                html+= '<th>'+code+'</th>';
                html+= '<th>'+code+'</th>';
                html+= '<th>'+code+'</th>';
                html+= '<th>'+code+'</th>';
                html+= '<th>'+code+'</th>';
                html+= '</tr>';
                html+= '<tr>';

                html+= '<th>DEPT CODE</th>';
                html+= '<th>BASIC RATE</th>';
                html+= '<th>COLA</th>';
                html+= '<th>TRANS ALLW</th>';
                html+= '<th>RICE SUBSI</th>';
                html+= '<th>HOLIDAY PAY</th>';
                html+= '<th>NITE DIFF</th>';
                html+= '<th>OT PAY</th>';
                html+= '<th>ACTING ALLW</th>';
                html+= '<th>OTHERADD</th>';
                html+= '</tr>';
                html+= '</thead>';
                html+= '<tbody>';

                for(index=0;index<count;index++){
                    html+= '<tr>';
                    html+= '<td>'+d.earningsdata[index].deptcode+'</td>';
                    html+= '<td class="number">'+d.earningsdata[index].basicrate+'</td>';
                    html+= '<td class="number">'+d.earningsdata[index].cola+'</td>';
                    html+= '<td class="number">'+d.earningsdata[index].transallw+'</td>';
                    html+= '<td class="number">'+d.earningsdata[index].ricesubsi+'</td>';
                    html+= '<td class="number">'+d.earningsdata[index].holidaypay+'</td>';
                    html+= '<td class="number">'+d.earningsdata[index].nitediff+'</td>';
                    html+= '<td class="number">'+d.earningsdata[index].otpay+'</td>';
                    html+= '<td class="number">'+d.earningsdata[index].actingallw+'</td>';
                    html+= '<td class="number">'+d.earningsdata[index].otheradd+'</td>';
                    html+= '</tr>';
                }

                html+= '</tbody>';
                html+= '<tfoot>';
                html+= '<tr>';
                html+= '<td>Total Result</td>';
                html+= '<td class="number bold">'+d.totalbasicrate+'</td>';
                html+= '<td class="number bold">'+d.totalcola+'</td>';
                html+= '<td class="number bold">'+d.totaltransallw+'</td>';
                html+= '<td class="number bold">'+d.totalricesubsi+'</td>';
                html+= '<td class="number bold">'+d.totalholidaypay+'</td>';
                html+= '<td class="number bold">'+d.totalnitediff+'</td>';
                html+= '<td class="number bold">'+d.totalotpay+'</td>';
                html+= '<td class="number bold">'+d.totalactingallw+'</td>';
                html+= '<td class="number bold">'+d.totalotheradd+'</td>';
                html+= '</tr>';
                html+= '</tfoot>';
                html+= '</table>';

                html+= '</div>';
                html+= '</div>';

                html+= '<div class="row">';
                html+= '<div class="col-md-2  col-sm-2 col-xs-2 col-lg-2">';
                html+= '<div>Encoded by:</div>';
                html+= '<div>____________</div>';
                html+= '<div>HRDH</div>';
                html+= '</div>';
                html+= '<div class="col-md-2  col-sm-2 col-xs-2 col-lg-2">';
                html+= '<div>Checked by:</div>';
                html+= '<div>____________</div>';
                html+= '<div>GA</div>';
                html+= '</div>';
                html+= '<div class="col-md-2  col-sm-2 col-xs-2 col-lg-2">';
                html+= '<div>Noted by:</div>';
                html+= '<div>____________</div>';
                html+= '<div>FM</div>';
                html+= '</div>';
                html+= '<div class="col-md-3  col-sm-3 col-xs-3 col-lg-3">';

                html+= '</div>';
                html+= '<div class="col-md-3 col-sm-3 col-xs-3 col-lg-3">';
                html+= '<div>Approved by:</div>';
                html+= '<div>____________</div>';
                html+= '<div>P-CEO</div>';
                html+= '</div>';
                html+= '</div>';

                pecoRepPrint("Earnings Register" , html);
            }).fail(function () {
                PECO.phpError();
            });

        });
        $(document).on('click' , '#printotregdepttotals' , function () {

            var this_ = $(this);
            var this_html = this_.html();

            var payrollyear  = $(document).find('#payrollyear').val();
            var payrollmonth = $(document).find('#payrollmonth').val();
            var payrollpayclass = $(document).find('#payrollpayclass').val();
            var payrollpaytype = $(document).find('#payrollpaytype').val();
            $.ajax({
                url:PECO.base_url()+"payroll/getovertimereport",
                type:"post",
                data:{"payrollyear": payrollyear ,"payrollmonth":payrollmonth , "payrollpayclass":payrollpayclass , "payrollpaytype":payrollpaytype},
                dataType:"json",
                beforeSend: function() {
                    PECO.btnLoading(this_, 'Processing...');
                }
            }).done(function (data) {
                pecoRepPrint("OVERTIME" , data.html);
                PECO.btnSuccess(this_, 'Done', this_html, 'btn-primary');
            });
        });
        $(document).on('click' , '#printotregbyemp' , function () {

            var this_ = $(this);
            var this_html = this_.html();

            var payrollyear  = $(document).find('#payrollyear').val();
            var payrollmonth = $(document).find('#payrollmonth').val();
            var payrollpayclass = $(document).find('#payrollpayclass').val();
            var payrollpaytype = $(document).find('#payrollpaytype').val();
            $.ajax({
                url:PECO.base_url()+"payroll/printotregisterbyemp",
                type:"post",
                data:{"payrollyear": payrollyear ,"payrollmonth":payrollmonth , "payrollpayclass":payrollpayclass , "payrollpaytype":payrollpaytype},
                dataType:"json",
                beforeSend: function() {
                    PECO.btnLoading(this_, 'Processing...');
                }
            }).done(function (data) {
                pecoRepPrint("OVERTIME" , data.html);
                PECO.btnSuccess(this_, 'Done', this_html, 'btn-primary');
            });
        });


        $(document).on('click','#printdednregdepttotals',function (e) {
            e.preventDefault();

            var this_ = $(this);
            var this_html = this_.html();

            var payrollyear  = $(document).find('#payrollyear').val();
            var payrollmonth = $(document).find('#payrollmonth').val();
            var payrollpayclass = $(document).find('#payrollpayclass').val();
            var payrollpaytype = $(document).find('#payrollpaytype').val();
            $.ajax({
                url:PECO.base_url()+'payroll/getdeductionsreport',
                data:{"payrollyear": payrollyear ,"payrollmonth":payrollmonth , "payrollpayclass":payrollpayclass , "payrollpaytype":payrollpaytype},
                type:'post',
                dataType:'json' ,
                beforeSend: function() {
                    PECO.btnLoading(this_, 'Processing...');
                }
            }).done(function (d) {

                PECO.btnSuccess(this_, 'Done', this_html, 'btn-primary');

                var code = 401;
                if(d.payclass == 128){
                    code = 402;
                }else if(d.payclass == 1 || d.payclass == 129){
                    code = 401;
                }
                var count = 0;
                var html = '';
                var index = 0;
                count = d.datacount;

                html += d.header;
                html += '<div class="row">';
                html += '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';

                html += '<table class="table table-condensed tbl-xs print-table-standard">';
                html += '<thead>';
                html += '<tr>';
                html += '<th></th>';
                html += '<th>256</th>';
                html += '<th>262</th>';
                html += '<th>260/408/261</th>';
                html += '<th>274</th>';
                html += '<th>264</th>';
                html += '<th>265</th>';
                html += '<th>261</th>';
                html += '<th></th>';
                html += '<th>405</th>';
                html += '<th>178</th>';
                html += '<th>175</th>';
                html += '<th>MEM INS</th>';
                html += '<th>'+code+'</th>';
                html += '<th>245</th>';
                html += '</tr>';
                html += '<tr>';
                html += '<th>DEPT CODE</th>';
                html += '<th>SSS CONT</th>';
                html += '<th>SSS LOAN</th>';
                html += '<th>HDMF CONT</th>';
                html += '<th>HDMF LOAN</th>';
                html += '<th>PECEWA LOAN</th>';
                html += '<th>COOP LOAN</th>';
                html += '<th>PAGIBIG AD</th>';
                html += '<th>OTHER DEDN</th>';
                html += '<th>HMO DEDN</th>';
                html += '<th>DED A</th>';
                html += '<th>ELECT BILL</th>';
                html += '<th>MEM INS</th>';
                html += '<th>LWOP</th>';
                html += '<th>BASE TAX</th>';
                html += '</tr>';
                html += '</thead>';
                html += '<tbody>';

                for(index=0;index<count;index++){
                    html+= '<tr>';
                    html+= '<td>'+d.deductionsdata[index].deptcode+'</td>';
                    html+= '<td class="number">'+d.deductionsdata[index].ssscont+'</td>';
                    html+= '<td class="number">'+d.deductionsdata[index].sssloan+'</td>';
                    html+= '<td class="number">'+d.deductionsdata[index].hdmfcont+'</td>';
                    html+= '<td class="number">'+d.deductionsdata[index].hdmfloan+'</td>';
                    html+= '<td class="number">'+d.deductionsdata[index].pecewaloan+'</td>';
                    html+= '<td class="number">'+d.deductionsdata[index].cooploan+'</td>';
                    html+= '<td class="number">'+d.deductionsdata[index].pagibigad+'</td>';
                    html+= '<td class="number">'+d.deductionsdata[index].otherdeduct+'</td>';
                    html+= '<td class="number">'+d.deductionsdata[index].hmodeduct+'</td>';
                    html+= '<td class="number">'+d.deductionsdata[index].deda+'</td>';
                    html+= '<td class="number">'+d.deductionsdata[index].electbill+'</td>';
                    html+= '<td class="number">'+d.deductionsdata[index].memins+'</td>';
                    html+= '<td class="number">'+d.deductionsdata[index].lwop+'</td>';
                    html+= '<td class="number">'+d.deductionsdata[index].basetax+'</td>';
                    html+= '</tr>';
                }


                html += '</tbody>';
                html += '<tfoot>';
                html += '<tr>';
                html += '<td >TOTAL RESULT</td>';
                html += '<td class="number">'+d.totalssscont+'</td>';
                html += '<td class="number">'+d.totalsssloan+'</td>';
                html += '<td class="number">'+d.totalhdmfcont+'</td>';
                html += '<td class="number">'+d.totalhdmfloan+'</td>';
                html += '<td class="number">'+d.totalpecewaloan+'</td>';
                html += '<td class="number">'+d.totalcooploan+'</td>';
                html += '<td class="number">'+d.totalpagibigad+'</td>';
                html += '<td class="number">'+d.totalotherdedn+'</td>';
                html += '<td class="number">'+d.totalhmodedn+'</td>';
                html += '<td class="number">'+d.totaldeda+'</td>';
                html += '<td class="number">'+d.totalelectbill+'</td>';
                html += '<td class="number">'+d.totalmemins+'</td>';
                html += '<td class="number">'+d.totallwop+'</td>';
                html += '<td class="number">'+d.totalbasetax+'</td>';
                html += '</tr>';
                html += '</tfoot>';
                html += '</table>';


                html += '</div>';
                html += '</div>';

                html+= '<div class="row">';
                html+= '<div class="col-md-2 col-sm-2 col-xs-2 col-lg-2">';
                html+= '<div>TOTAL DEDUCTIONS: </div>';
                html+= '<br />';
                html+= '<div>NET TOTAL: </div>';
                html+= '</div>';
                html+= '<div class="col-md-2 col-sm-2 col-xs-2 col-lg-2">';
                html+= '<div>'+d.totaldeductionamt+'</div>';
                html+= '<br />';
                html+= '<div>'+d.totalnetamt+'</div>';
                html+= '</div>';
                html += '</div>';
                html+= '<br />';
                html+= '<div class="row">';
                html+= '<div class="col-md-2 col-sm-2 col-xs-2 col-lg-2">';
                html+= '<div>Encoded by:</div>';
                html+= '<div>____________</div>';
                html+= '<div>HRDH</div>';
                html+= '</div>';
                html+= '<div class="col-md-2 col-sm-2 col-xs-2 col-lg-2">';
                html+= '<div>Checked by:</div>';
                html+= '<div>____________</div>';
                html+= '<div>GA</div>';
                html+= '</div>';
                html+= '<div class="col-md-2 col-sm-2 col-xs-2 col-lg-2">';
                html+= '<div>Noted by:</div>';
                html+= '<div>____________</div>';
                html+= '<div>FM</div>';
                html+= '</div>';
                html+= '<div class="col-md-3 col-sm-3 col-xs-3 col-lg-3">';

                html+= '</div>';
                html+= '<div class="col-md-3 col-sm-3 col-xs-3 col-lg-3">';
                html+= '<div>Approved by:</div>';
                html+= '<div>____________</div>';
                html+= '<div>P-CEO</div>';
                html+= '</div>';
                html+= '</div>';

                pecoRepPrint("Deductions Register" , html);
            });

        });
    };

    var draw_tbl_payroll_registers = function(payclass, viewtype, container) {

        var month       = $('#select2month', document).val();
        var year        = $('#periodyear', document).val();
        var paytype     = $('#select2paytype', document).val();
        $.ajax({
            url: PECO.base_url() + 'reports/payrollreportdata',
            type: 'post',
            data: {'month': month, 'year': year, 'payclass': payclass, 'viewtype': viewtype, 'paytype': paytype },
            dataType: 'json',
        }).done(function(d) {
            var tableid = '';
            if(d.tableview == 1){
                tableid = '#reg_payroll';
            }else if(d.tableview == 2){
                tableid = '#reg_earnigs';
            }else if(d.tableview == 3){
                tableid = '#reg_deduction';
            }else if(d.tableview == 4){
                tableid = '#reg_overtime';
            }
            if(d.cols.length>0){
                var th;
                $(tableid+ ' #tbl_registers th', document).html('');
                for(th = 0; th<d.cols.length; th++) {
                    alert(d.cols[th]);
                    $('#tbl_registers', container).append('<th>'+d.cols[th]+'</th>');
                    // $(tableid+ ' #tbl_registers', document).append('<th>'+d.cols[th]+'</th>');
                    // $(container+ '#tbl_registers', document).append('<th  '+d.cols[th]+'">'+d.cols[th]+'</th>');
                }
            }
        });
    };

    var init_payroll_event = function(payclass) {

        $(document).on('submit','#submitnet1530report',function (e) {
            e.preventDefault();
            var this_ = $(this);
            $.ajax({
                url:this_.attr("action"),
                type:this_.attr("method"),
                data:this_.serialize(),
                dataType:'json'
            }).done(function (data) {
                net1530table.dataTable().empty();
                net1530table.dataTable({
                    bDestroy: true,
                    bPaginate: false,
                    bFilter: false,
                    bInfo: true,
                    bStateSave: false,
                    bProcessing: true,
                    aaData: data.netpaylist,
                    aoColumns: [
                        {"data":"name"},
                        {"data":"accntno"},
                        {"data":"net15",sClass:'number'},
                        {"data":"net30",sClass:'number'},
                        {"data":"totalnet",sClass:'number'},
                    ],
                    "columnDefs": [
                        {"targets": 0, "orderable": false},
                        {"targets": -1, "orderable": false}
                    ],
                    searchHighlight: true
                });
                $(document).find('#net15f').text(data.total15);
                $(document).find('#net30f').text(data.total30);
                $(document).find('#total1530f').text(data.total1530);
            }).fail(function () {
                PECO.phpError();
            });
        });

        $(document).on('click','#printnet1530',function () {

            var  net1530payclass = $(document).find('#net1530payclass').val();
            var  net1530month = $(document).find('#net1530month').val();
            var  net1530year = $(document).find('#net1530year').val();
            var  payrolldate = $(document).find('#payrolldate1530').val();
            $.ajax({
                url:PECO.base_url()+'payroll/submitnet1530report',
                type:'post',
                data:{"net1530payclass" : net1530payclass
                    , "net1530month" : net1530month , "net1530year" : net1530year , "report" : 1,"payrolldate" : payrolldate},
                dataType:'json'
            }).done(function (data) {
                PECO.pecoRepPrint("",data.html,false);
            }).fail(function () {
                PECO.phpError();
            });
        });

        $(document).on('click','#printnet15',function () {
            var  net15paytype = $(document).find('#net15paytype').val();
            var  net15payclass = $(document).find('#net15payclass').val();
            var  net15month = $(document).find('#net15month').val();
            var  net15year = $(document).find('#net15year').val();
            var  payrolldate = $(document).find('#payrolldate15').val();
            var  namesig = $(document).find('#namesig').val();
            var  possig = $(document).find('#possig').val();
            $.ajax({
                url:PECO.base_url()+'payroll/submitnet15report',
                type:'post',
                data:{"net15paytype":net15paytype , "net15payclass" : net15payclass
                    , "net15month" : net15month , "net15year" : net15year , "report" : 1,"payrolldate" : payrolldate , "namesig" : namesig , "possig" : possig},
                dataType:'json'
            }).done(function (data) {
                PECO.pecoRepPrint("",data.html,false);
            }).fail(function () {
                PECO.phpError();
            });
        });

        $(document).on('submit','#submitnet15report',function (e) {
            e.preventDefault();
            var this_ = $(this);
            $.ajax({
                url:this_.attr("action"),
                type:this_.attr("method"),
                data:this_.serialize(),
                dataType:'json'
            }).done(function (data) {
                net15table.dataTable().empty();
                net15table.dataTable({
                    bDestroy: true,
                    bPaginate: false,
                    bFilter: false,
                    bInfo: true,
                    bStateSave: false,
                    bProcessing: true,
                    aaData: data.netpaylist,
                    aoColumns: [
                        {"data":"name"},
                        {"data":"accntno"},
                        {"data":"netpay",sClass:'number'}
                    ],
                    "columnDefs": [
                        {"targets": 0, "orderable": false},
                        {"targets": -1, "orderable": false}
                    ],
                    searchHighlight: true
                });
            }).fail(function () {
                PECO.phpError();
            });
        });

        $('#payroll_data_tab a').on('shown.bs.tab', function(event){
            var x = $(event.target).attr('href');         // active tab
            if(x == '#payroll') {
                init_payroll_table(payclass, 1);
            }
            if(x == '#reg_payroll') {
                var container = $(x);
                draw_tbl_payroll_registers(payclass, 1, container);
            }
            if(x == '#reg_earnigs') {
                var container = $(x);
                draw_tbl_payroll_registers(payclass, 2, container);
            }
            if(x == '#reg_deduction') {
                var container = $(x);
                draw_tbl_payroll_registers(payclass, 3, container);
            }
            if(x == '#reg_overtime') {
                var container = $(x);
                draw_tbl_payroll_registers(payclass, 4, container);
            }
        });

        payrollregistertable.on('click', '#btn-expand', function () {
            var this_ = $(this);
            var thisTr = this_.closest('tr');
            var thisTr_child = thisTr.children('td').length;
            var data_id = this_.attr('data-id');
            var payrollyear  = $(document).find('#payrollyear').val();
            var payrollmonth = $(document).find('#payrollmonth').val();
            var payrollpayclass = $(document).find('#payrollpayclass').val();
            var payrollpaytype = $(document).find('#payrollpaytype').val();
            var clss_ = 'sub-table';

            if (this_.hasClass('expanded') == false) {

                thisTr.next('#error').remove();
                this_.removeClass('fa-angle-right').addClass('fa-angle-down');
                $.ajax({
                    url: PECO.base_url()+'payroll/getempdeptpayrollregister',
                    type: 'post',
                    data:{"payrollyear": payrollyear ,"payrollmonth":payrollmonth , "payrollpayclass":payrollpayclass , "payrollpaytype":payrollpaytype , "id" : data_id },
                    dataType: 'json',
                    beforeSend: function () {
                        thisTr.after('<tr id="loading" class="info " ><td colspan="' + thisTr_child + '" class=""><i class="fa fa-spinner fa-spin fa-pulse"></i> Loading..</td></tr>');

                    }
                }).done(function(d){
                    thisTr.after('<tr class="animated fadeIn fast compact '+d.func+'" id="details"><td colspan="' + thisTr_child + '" class="'+clss_+'">' + d.html + '</td></tr>');
                    payrollregistertable.find('#loading').remove();
                }).fail(function(){
                    thisTr.after('<tr class="animated fadeIn fast compact danger" id="error"><td colspan="' + thisTr_child + '"><i class="fa fa-times"></i> Error PHP</td></tr>');
                    payrollregistertable.find('#loading').remove();
                });
            } else {
                thisTr.next('#details').remove();
                thisTr.next('#error').remove();
                payrollregistertable.find('#loading').remove();
                this_.removeClass('fa-angle-down').addClass('fa-angle-right');
            }
            this_.toggleClass('expanded');
            this_.closest('tr').toggleClass('expand-show');
        });
        earningregistertable.on('click', '#btn-expand', function () {
            var this_ = $(this);
            var thisTr = this_.closest('tr');
            var thisTr_child = thisTr.children('td').length;
            var data_id = this_.attr('data-id');
            var payrollyear  = $(document).find('#payrollyear').val();
            var payrollmonth = $(document).find('#payrollmonth').val();
            var payrollpayclass = $(document).find('#payrollpayclass').val();
            var payrollpaytype = $(document).find('#payrollpaytype').val();
            var clss_ = 'sub-table';

            if (this_.hasClass('expanded') == false) {

                thisTr.next('#error').remove();
                this_.removeClass('fa-angle-right').addClass('fa-angle-down');
                $.ajax({
                    url: PECO.base_url()+'payroll/getempdeptearnings',
                    type: 'post',
                    data:{"payrollyear": payrollyear ,"payrollmonth":payrollmonth , "payrollpayclass":payrollpayclass , "payrollpaytype":payrollpaytype , "id" : data_id },
                    dataType: 'json',
                    beforeSend: function () {
                        thisTr.after('<tr id="loading" class="info " ><td colspan="' + thisTr_child + '" class=""><i class="fa fa-spinner fa-spin fa-pulse"></i> Loading..</td></tr>');

                    }
                }).done(function(d){
                    thisTr.after('<tr class="animated fadeIn fast compact '+d.func+'" id="details"><td colspan="' + thisTr_child + '" class="'+clss_+'">' + d.html + '</td></tr>');
                    earningregistertable.find('#loading').remove();
                }).fail(function(){
                    thisTr.after('<tr class="animated fadeIn fast compact danger" id="error"><td colspan="' + thisTr_child + '"><i class="fa fa-times"></i> Error PHP</td></tr>');
                    earningregistertable.find('#loading').remove();
                });
            } else {
                thisTr.next('#details').remove();
                thisTr.next('#error').remove();
                earningregistertable.find('#loading').remove();
                this_.removeClass('fa-angle-down').addClass('fa-angle-right');
            }
            this_.toggleClass('expanded');
            this_.closest('tr').toggleClass('expand-show');
        });
        deductionsregistertable.on('click', '#btn-expand', function () {
            var this_ = $(this);
            var thisTr = this_.closest('tr');
            var thisTr_child = thisTr.children('td').length;
            var data_id = this_.attr('data-id');
            var payrollyear  = $(document).find('#payrollyear').val();
            var payrollmonth = $(document).find('#payrollmonth').val();
            var payrollpayclass = $(document).find('#payrollpayclass').val();
            var payrollpaytype = $(document).find('#payrollpaytype').val();
            var clss_ = 'sub-table';

            if (this_.hasClass('expanded') == false) {

                thisTr.next('#error').remove();
                this_.removeClass('fa-angle-right').addClass('fa-angle-down');
                $.ajax({
                    url: PECO.base_url()+'payroll/getempdeptdeductions',
                    type: 'post',
                    data:{"payrollyear": payrollyear ,"payrollmonth":payrollmonth , "payrollpayclass":payrollpayclass , "payrollpaytype":payrollpaytype , "id" : data_id },
                    dataType: 'json',
                    beforeSend: function () {
                        thisTr.after('<tr id="loading" class="info " ><td colspan="' + thisTr_child + '" class=""><i class="fa fa-spinner fa-spin fa-pulse"></i> Loading..</td></tr>');

                    }
                }).done(function(d){
                    thisTr.after('<tr class="animated fadeIn fast compact '+d.func+'" id="details"><td colspan="' + thisTr_child + '" class="'+clss_+'">' + d.html + '</td></tr>');
                    deductionsregistertable.find('#loading').remove();
                }).fail(function(){
                    thisTr.after('<tr class="animated fadeIn fast compact danger" id="error"><td colspan="' + thisTr_child + '"><i class="fa fa-times"></i> Error PHP</td></tr>');
                    deductionsregistertable.find('#loading').remove();
                });
            } else {
                thisTr.next('#details').remove();
                thisTr.next('#error').remove();
                deductionsregistertable.find('#loading').remove();
                this_.removeClass('fa-angle-down').addClass('fa-angle-right');
            }
            this_.toggleClass('expanded');
            this_.closest('tr').toggleClass('expand-show');
        });
        overtimeregistertable.on('click', '#btn-expand', function () {
            var this_ = $(this);
            var thisTr = this_.closest('tr');
            var thisTr_child = thisTr.children('td').length;
            var data_id = this_.attr('data-id');
            var payrollyear  = $(document).find('#payrollyear').val();
            var payrollmonth = $(document).find('#payrollmonth').val();
            var payrollpayclass = $(document).find('#payrollpayclass').val();
            var payrollpaytype = $(document).find('#payrollpaytype').val();
            var clss_ = 'sub-table';

            if (this_.hasClass('expanded') == false) {

                thisTr.next('#error').remove();
                this_.removeClass('fa-angle-right').addClass('fa-angle-down');
                $.ajax({
                    url: PECO.base_url()+'payroll/getempdeptovertime',
                    type: 'post',
                    data:{"payrollyear": payrollyear ,"payrollmonth":payrollmonth , "payrollpayclass":payrollpayclass , "payrollpaytype":payrollpaytype , "id" : data_id },
                    dataType: 'json',
                    beforeSend: function () {
                        thisTr.after('<tr id="loading" class="info " ><td colspan="' + thisTr_child + '" class=""><i class="fa fa-spinner fa-spin fa-pulse"></i> Loading..</td></tr>');

                    }
                }).done(function(d){
                    thisTr.after('<tr class="animated fadeIn fast compact '+d.func+'" id="details"><td colspan="' + thisTr_child + '" class="'+clss_+'">' + d.html + '</td></tr>');
                    overtimeregistertable.find('#loading').remove();
                }).fail(function(){
                    thisTr.after('<tr class="animated fadeIn fast compact danger" id="error"><td colspan="' + thisTr_child + '"><i class="fa fa-times"></i> Error PHP</td></tr>');
                    overtimeregistertable.find('#loading').remove();
                });
            } else {
                thisTr.next('#details').remove();
                thisTr.next('#error').remove();
                overtimeregistertable.find('#loading').remove();
                this_.removeClass('fa-angle-down').addClass('fa-angle-right');
            }
            this_.toggleClass('expanded');
            this_.closest('tr').toggleClass('expand-show');
        });


        $(document).on('submit' , '#submitgetreports' , function (e) {
            e.preventDefault();
            var this_ = $(this);
            $.ajax({
                url:this_.attr("action"),
                type:this_.attr("method"),
                data:this_.serialize(),
                dataType:'json',
                beforeSend: function() {
                    PECO.DTphpLoading(payrollregistertable, 'Loading payroll register, Please wait...');
                    PECO.DTphpLoading(earningregistertable, 'Loading earnings register, Please wait...');
                    PECO.DTphpLoading(deductionsregistertable, 'Loading decuction registers, Please wait...');
                    PECO.DTphpLoading(overtimeregistertable, 'Loading overtime, Please wait...');
                }
            }).done(function (data) {
                if(data.payclass == 1){
                    $(document).find('.changablecol').text('401');
                }else{
                    $(document).find('.changablecol').text('402');
                }

                if(data.qry == true){
                    $(document).find('#payrolldataid').val(data.groupid);
                    $(document).find('#inputgroupid').val(data.groupid);
                    $(document).find('#get_value').val(1);
                    loadevents();
                    //fetchpayrollregistertable();

                    payroll_register_table(data.registers);
                    payroll_earnings_table(data.earnings);
                    payroll_deduction_table(data.deductions);
                    payroll_overtime_table(data.overtimes);

                    //payrollearningstable();
                    //payrolldeductionstable();
                    //payrollovertimetable();
                    //payrollannualtable();

                    if (data.access == true){
                        $("#approval_buttons", document).html(data.button);
                    } else {
                        $("#approval_buttons", document).html('');
                    }
                }else{
                    $(document).find('#pceobtn').html("");
                    $(document).find('#get_value').val(0);

                    PECO.DTDefault(payrollregistertable, data.msg);
                    PECO.DTDefault(earningregistertable, data.msg);
                    PECO.DTDefault(deductionsregistertable, data.msg);
                    PECO.DTDefault(overtimeregistertable, data.msg);
                }
            }).fail(function () {
                PECO.phpError();
            });
        });

        var loadevents = function(){

        };

        $(document).on('click','#exportexcelbtn',function (e) {
            e.preventDefault();
            var this_ = $(this);
            var this_html = this_.html();
            var payclasscombo = $(document).find('#payclasscombo').val();
            var month = $(document).find('#month').val();
            var year = $(document).find('#year').val();
            var payrollperiod = $(document).find('#payrollperiod').val();

            window.open(PECO.base_url()+'payroll/exportexceldata/' + payclasscombo + '/' + month +'/'+year+'/'+payrollperiod)

        });

        $(document).on('click','#exportbankbtn',function (e) {
            e.preventDefault();
            var this_ = $(this);
            var this_html = this_.html();
            var payclasscombo= $(document).find('#payclasscombo').val();
            var month = $(document).find('#month').val();
            var year= $(document).find('#year').val();
            var payrollperiod= $(document).find('#payrollperiod').val();



            if(payclasscombo == 128 || payclasscombo == 3077 || payclasscombo == 3078){
                payrollperiod = payrollperiod;
            }else{
                payrollperiod = 0;
            }

            if (payclasscombo == '' || month == '' || year == ''){
                PECO.initAlerts("Please fill up all the fields","Empty","info");
            }else{
                swal({
                    title: "Are you sure?",
                    text: "Bank file will be exported",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Yes, Export!",
                    closeOnConfirm: false,
                    closeOnCancel: false,
                    showLoaderOnConfirm: true
                }, function(isConfirm){
                    if (isConfirm) {
                        $.ajax({
                            //url:PECO.base_url()+'payroll/getreportsdata',
                            url:PECO.base_url()+'payroll/exportexceldata',
                            type:'post',
                            data:{"payclass":payclasscombo,"month":month,"year":year,"payrollperiod":payrollperiod,"exporttxt":true},
                            dataType:'json',
                            beforeSend: function() {
                                PECO.btnLoading(this_, 'Exporting bank file...');
                            }
                        }).done(function (d) {
                            swal.close();
                            this_.html(this_html);
                            window.open(d.filenamebank, '_blank');
                            //window.open("file:///"+d.systempath, '_blank');
                        }).fail(function () {
                            PECO.phpError();
                            PECO.btnErrorPHP(this_, this_html);
                            swal.close();
                        });


                    } else {
                        swal("Cancelled", "Bank file export cancelled!", "error");
                    }
                });



            }

        });



        $(document).on('click','#disapprovebtn',function (e) {
            e.preventDefault();

            swal({
                title: "Are you sure you want to disapprove this payroll?",
                text: 'Dispprove Payroll',
                type: "info",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes",
                closeOnConfirm: false,
                closeOnCancel: true,
                showLoaderOnConfirm: true
            }, function(isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: PECO.base_url() + "payroll/disapprovepayroll",
                        method: "post",
                        dataType: "json",
                        data: {'dataid': $(document).find('#inputgroupid').val()}
                    }).done(function (d) {
                        $(document).find('.pceopayrollbtn').hide();
                        swal(d.title, d.msg, d.func);
                        window.location = '';
                    });

                }
            });
            e.stopImmediatePropagation();
        });

        $(document).on('click','#approvebtn',function (e) {
            e.preventDefault();

            swal({
                title: "Are you sure you want to approve this payroll?",
                text: 'Approve Payroll',
                type: "info",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes",
                closeOnConfirm: false,
                closeOnCancel: true,
                showLoaderOnConfirm: true
            }, function(isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: PECO.base_url() + "payroll/approvepayroll",
                        method: "post",
                        dataType: "json",
                        data: {'dataid': $(document).find('#inputgroupid').val()}
                    }).done(function (d) {
                        $(document).find('.pceopayrollbtn').hide();
                        swal(d.title, d.msg, d.func);
                        window.location = '';
                    });
                }
            });
            e.stopImmediatePropagation();
        });

        $(document).on('submit','#submitrecheck',function (e) {
            e.preventDefault();
            var this_ = $(this);
            $.ajax({
                url:this_.attr("action"),
                type:this_.attr("method"),
                data:this_.serialize(),
                dataType:'json'
            }).done(function (d) {
                payrollbreakdownloan.dataTable().empty();
                payrollbreakdownloan.dataTable({
                    bDestroy: true,
                    bPaginate: false,
                    bFilter: false,
                    bInfo: true,
                    bStateSave: false,
                    bProcessing: true,
                    aaData: d.breakdownloan,
                    aoColumns: [
                        {"data":"num"},
                        {"data":"type"},
                        {"data":"amount"}
                    ],
                    "order": [[ 1, "asc" ]],
                    "columnDefs": [
                        {"targets": 0, "orderable": false},
                        {"targets": -1, "orderable": false}
                    ],
                    searchHighlight: true
                });
                payrolltransactionval.dataTable().empty();
                payrolltransactionval.dataTable({
                    bDestroy: true,
                    bPaginate: false,
                    bFilter: false,
                    bInfo: true,
                    bStateSave: false,
                    bProcessing: true,
                    aaData: d.payrolltransactiondata,
                    aoColumns: [
                        {"data":"num"},
                        {"data":"type"},
                        {"data":"amount"}
                    ],
                    "order": [[ 1, "asc" ]],
                    "columnDefs": [
                        {"targets": 0, "orderable": false},
                        {"targets": -1, "orderable": false}
                    ],
                    searchHighlight: true
                });
            }).fail(function () {
                PECO.phpError();
            });
        });
        PECO.select2Basic($('#employeesearch',document),'payroll/getpayrollemployee','Please select employee',false,false,false);
        $(document).on('change','#type',function () {
            var this_ = $(this);
            var this_val = this_.val();
            var empid = this_.closest('tr').find('td.empid').text();
            var periodyear = $('#periodyear',document).val();
            var select2month = $('#select2month',document).val();
            var select2paytype = ($('#select2paytype',document).val())? $('#select2paytype',document).val()  : false ;
            $.ajax({
                url:PECO.base_url()+'payroll/getexistingvalue',
                type:'post',
                data:{"trntype":this_val,"empid":empid,"year":periodyear,"month":select2month,"paytype":select2paytype},
                dataType:'json'
            }).done(function (data) {
                $('#amt').val(data.amt);
            }).fail(function () {
                PECO.phpError();
            });
        });
        // Handle click on "Expand All" button
        $('#expandall').on('click', function(){
            alert("test");
            $('#payroll_table > tbody  > tr').each(function() {
                var this_ =  $(this);
                this_.addClass('expanded');
            });
        });
        var period_month_default_val = $('#select2month').val();
        var tbl_draw;
        tbl_draw = init_payroll_table(payclass , 1);
        PECO.select2Basic($('#deptselect'), 'hris/select2dept', 'Select Department...', true);
        PECO.select2Basic($('#select2month',document), 'systems/select2month', 'Select Month...', false, false, period_month_default_val);
        init_dt_subdetails(payclass);
        $(document).on('change', '#select2month', function (e) {
            tbl_draw = init_payroll_table(payclass , 1);
            init_handler_row_form(tbl_draw);
        });
        $(document).on('change', '#deptselect', function (e) {
            tbl_draw = init_payroll_table(payclass, 1);
            init_handler_row_form(tbl_draw);
        });
        $(document).on('change', '#select2paytype', function (e) {
            tbl_draw = init_payroll_table(payclass , 1);
            init_handler_row_form(tbl_draw);
        });
        init_handler_row_select2(tbl_draw);
        init_handler_row_form(tbl_draw);
        init_handler_clearbtn(payclass);
        /*  tbl_draw.on('click','tbody tr #btn_print_payslip_temp',function (e) {
              e.preventDefault();
              var this_ = $(this);

              var this_val = this_.attr("data-id");
              var month       = $('#select2month', document).select2('val');
              var year        = $('#periodyear', document).val();
              if(payclass==128) {
                  var paytype = $('#select2paytype', document).val();
              }else{
                  var paytype = 1;
              }

              $.ajax({
                  url:PECO.base_url()+'payroll/printtemppayslip',
                  type:'post',
                  data:{'id': this_val, 'year': year, 'month': month, 'paytype': paytype, 'payclass': payclass},
                  dataType:'json'
              }).done(function (d) {
                  PECO.pecoRepPrint("Temporary Payslip",d.html);
              }).fail(function () {
                  PECO.phpError();
              });
          }); */

    };

    var init_handler_clearbtn = function(payclass) {
        var btn_clear = $('#clearpayroll');
        btn_clear.click(function(e) {
            e.preventDefault();
            var module_id = btn_clear.attr('data-module-id');
            swal({
                title: "Are you sure?",
                text: "Generated payroll will be cleared!",
                type: "error",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes, Process!",
                closeOnConfirm: false,
                closeOnCancel: true,
                showLoaderOnConfirm: true
            }, function(isConfirm){
                if (isConfirm) {
                    $.ajax({
                        url: PECO.base_url() + 'payroll/cleartrans',
                        type: 'post',
                        data: {'viewtype': 1, 'clear': 1, 'moduleid': module_id, 'payclass': payclass},
                        dataType: 'json'
                    }).done(function (d) {
                        init_payroll_table(payclass , 1);
                        swal("Cleared!", "Payroll has been cleared!", "success");
                    }).fail(function(){
                        swal("Failed", "Clearing failed", "warning");
                    });
                }
            });
        });
    };

    var init_handler_row_form = function(tbl_draw) {
        $('tbody tr', tbl_draw).each(function(){
            $(this).on('submit', '.popover #frm_transaction_deduction_entry', function(e) {
                e.preventDefault();
                var this_ = $(this);
                var this_row_ = this_.closest('tr');
                var this_pop = $('.popovers#deductionpopover', this_row_);
                handler_ajax_submit_row(this_, this_pop);
                e.stopImmediatePropagation();
            });
        });

        $('tbody tr', tbl_draw).each(function(e){
            $(this).on('submit', '.popover #frm_transaction_earnings_entry', function(e) {
                e.preventDefault();
                var this_ = $(this);
                var this_row_ = this_.closest('tr');
                var this_pop = $('.popovers#earningpopover', this_row_);
                handler_ajax_submit_row(this_, this_pop);
                e.stopImmediatePropagation();
            });
        });

        $('tbody tr', tbl_draw).each(function(){
            $(this).on('submit', '.popover #frm_transaction_loan_entry', function(e) {
                e.preventDefault();
                var this_ = $(this);
                var this_row_ = this_.closest('tr');
                var this_pop = $('.popovers#loanpopover', this_row_);
                handler_ajax_submit_row(this_, this_pop);
                e.stopImmediatePropagation();
            });
        });
    };

    var init_handler_row_select2 = function(tbl_draw) {
        $('.popovers#earningpopover', tbl_draw).click(function() {
            var this_pop = $(this);
            var this_row = this_pop.closest('tr');
            var this_form_earnings = $('.popover #frm_transaction_earnings_entry', this_row);
            PECO.select2Basic($('input#type', this_form_earnings), 'hris/select2earnings', 'Select Earning..');
            $('#payspec',this_form_earnings).select2({'placeholder': 'Select specific'});
            return false;
        });
        $('.popovers#deductionpopover', tbl_draw).click(function() {
            var this_pop = $(this);
            var this_row = this_pop.closest('tr');
            var this_form_deduction = $('.popover #frm_transaction_deduction_entry', this_row);
            PECO.select2Basic($('input#type', this_form_deduction), 'hris/select2deductions', 'Select Deduction..');
            $('#payspec',this_form_deduction).select2({'placeholder': 'Select specific'});
            return false;
        });
        $('.popovers#loanpopover', tbl_draw).click(function() {
            var this_pop = $(this);
            var this_row = this_pop.closest('tr');
            var this_form_loan = $('.popover #frm_transaction_loan_entry', this_row);
            PECO.select2Basic($('input#type', this_form_loan), 'hris/select2loans', 'Select loan..');
            $('#payspec',this_form_loan).select2({'placeholder': 'Select specific'});
            return false;
        });
    };

    var init_dt_subdetails = function(payclass) {

        payroll_tbl.on('click', '#btn-expand', function () {

            var month       = $('#select2month', document).select2('val');
            var year        = $('#periodyear', document).val();
            var expand      = false;
            if(payclass==128 || payclass == 3077 || payclass == 3078) {
                var paytype = $('#select2paytype', document).val();
            }else{
                var paytype = 1;
            }

            var this_ = $(this);
            var thisTr = this_.closest('tr');
            var thisTr_child = thisTr.children('td').length;
            var data_id = this_.attr('data-id');
            if (this_.hasClass('expanded') == false) {
                thisTr.next('#error').remove();
                this_.removeClass('fa-angle-right').addClass('fa-angle-down');
                $.ajax({
                    url: PECO.base_url() + 'payroll/payrollinfo',
                    type: 'post',
                    data: {'id': data_id, 'year': year, 'month': month, 'paytype': paytype, 'payclass': payclass},
                    dataType: 'json',
                    beforeSend: function () {
                        thisTr.after('<tr id="loading" class="info"><td colspan="' + thisTr_child + '"><i class="fa fa-spinner fa-spin fa-pulse"></i> Loading..</td></tr>');
                    }
                }).done(function (d) {
                    thisTr.after('<tr class="animated fadeIn fast compact ' + d.func + '" id="details"><td colspan="' + thisTr_child + '">' + d.html + '</td></tr>');
                    payroll_tbl.find('#loading').remove();
                }).fail(function () {
                    thisTr.after('<tr class="animated fadeIn fast compact danger" id="error"><td colspan="' + thisTr_child + '"><i class="fa fa-times"></i> Error PHP</td></tr>');
                    payroll_tbl.find('#loading').remove();
                });
            } else {
                thisTr.next('#details').remove();
                thisTr.next('#error').remove();
                payroll_tbl.find('#loading').remove();
                this_.removeClass('fa-angle-down').addClass('fa-angle-right');
            }
            this_.toggleClass('expanded');

        });

    };

    var init_payroll_table = function(payclass , viewtype) {
        var tbl;
        var ccid        = $('#deptselect', document).val();
        var month       = $('#select2month', document).val();
        var year        = $('#periodyear', document).val();
        var paytype     = $('#select2paytype', document).val();
        var moduleid    = $('#moduleid', document).val();



        if(payclass==128 || payclass == 3077 || payclass == 3078) {
            $('#select2paytype').attr('disabled', false).select2({'placeholder': 'Select..'});
        }else{
            $('#select2paytype').attr('disabled', true).select2('val', '');
        }

        $.ajax({
            url: PECO.base_url() + 'payroll/emplist',
            type: 'post',
            dataType: 'json',
            data: {
                'stat': 1,
                'modulehash': '',
                'dept': ccid,
                'class': payclass,
                'viewtype': viewtype,
                'year': year,
                'month': month,
                'paytype': paytype,
                'moduleid': moduleid
            },
            beforeSend: function() {
                // payroll_tbl.dataTable().empty();
                // PECO.DTphpLoading(payroll_tbl, 'Generating payroll...');
            },
            cache: false,
            async: false
        }).done(function (data) {
            $(document).find('#totalearningssum' , document).text(data.totaleaningssum);
            $(document).find('#totalloanssum' , document).text(data.totalloanssum);
            $(document).find('#totalpremiumssum' , document).text(data.totalpremiumssum);
            $(document).find('#totaltaxsum' , document).text(data.totaltaxsum);
            $(document).find('#totaldeductionssum' , document).text(data.totaldeductionssum);
            $(document).find('#totalnetsum' , document).text(data.totalnetsums);

            if (parseInt(data.groupstatus, 10) === 301) {
                $('#processpayroll',document).attr('disabled',true).html(data.payrollstat);
            } else {
                $('#processpayroll',document).attr('disabled',false).html('<i class="fa fa-forward fa-fw"></i> Process Payroll');
            }
            PECO.getHighlightsPlugin();
            payroll_tbl.dataTable().empty();
            tbl = payroll_tbl.dataTable({
                bDestroy: true,
                bPaginate: false,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                bProcessing: true,
                "iDisplayLength": 20,
                "lengthMenu": [[20, 30, 50, 100, 125, 150, -1], [20, 30, 50, 100, 125, 150, 'All']],
                aaData: data.list,
                aoColumns: [
                    {"data": "expand", sWidth: 'width: 5px;', sClass: 'text-align-center', sortable: false},
                    {"data": "num", sWidth: '', sClass: ''},
                    {"data": "empid", sWidth: '', sClass: 'empid'},
                    {"data": "lastname", sWidth: '', sClass: ''},
                    {"data": "firstname", sWidth: '', sClass: ''},
                    {"data": "middlename", sWidth: '', sClass: ''},
                    {"data": "department", sWidth: '', sClass: ''},
                    {"data": "basic", sWidth: '', sClass: 'number text-bold'},
                    {"data": "earnings", sWidth: '', sClass: 'number font-blue-hoki'},
                    {"data": "loans", sWidth: '', sClass: 'number'},
                    {"data": "premiums", sWidth: '', sClass: 'number'},
                    {"data": "tax", sWidth: '', sClass: 'number tax font-yellow-gold'},
                    //   {"data": "lateminutes", sWidth: '', sClass: 'font-red'},
                    // {"data": "latecharge", sWidth: '', sClass: 'font-red'},
                    {"data": "deductions", sWidth: '', sClass: 'number font-red'},
                    {"data": "netpay", sWidth: '', sClass: 'number netpay text-primary text-bold'},
                    {"data": "status", sWidth: '', sClass: 'text-align-center'}
                    //{"data": "control", sClass: "controls text-align-center hidden-print"},
                ],
                //   "order": [[ 6, "asc" ]],
                searchHighlight: true,
                fnRowCallback: function(nRow, data, iDisplayIndex) {
                    PECO.dtExpandBtn(nRow, data.expand);
                    PECO.popOverRow($(nRow).find('.popovers'), true, true, 'popover-danger');
                    PECO.iCheckRow($(nRow).find('input.icheck'), 'minimal', 'blue');

                    // CREATE SORT NUMBER
                    $(nRow).addClass(data.rowcolor);
                    var index = iDisplayIndex +1;
                    $('td:eq(1)',nRow).html(index);
                },
                drawCallback: function() {
                    init_handler_row_select2(tbl);
                    init_handler_row_form(tbl);
                }
            });

        }).fail(function(){
            PECO.DTphpError(payroll_tbl);
        });
        return tbl;
    };

    var handler_ajax_submit_row = function(this_, container) {
        // this = form
        //container = popover
        var this_tr = container.closest('tr');
        $.ajax({
            url: this_.attr("action"),
            type: this_.attr("method"),
            data: this_.serialize(),
            dataType: 'json',
            beforeSend: function () {
                PECO.blockUI({
                    animate: true,
                    overlayColor: false,
                    target: payroll_tbl
                });
            }
        }).done(function (d) {
            var this_net = this_tr.find('td.netpay');
            var this_tax = this_tr.find('td.tax');


            if(d.transactiontype==0) {

                container.text(d.loans).number(true, 2);
                container.closest('td').addClass('danger bg-fade');
                setTimeout(function(){
                    container.closest('td').removeClass('danger');
                },1000);
            }
            if(d.transactiontype==1) {

                container.text(d.earnings).number(true, 2);
                container.closest('td').addClass('danger bg-fade');
                setTimeout(function(){
                    container.closest('td').removeClass('danger');
                },1000);
            }
            if(d.transactiontype==2) {

                container.text(d.deductions).number(true, 2);
                container.closest('td').addClass('danger bg-fade');
                setTimeout(function(){
                    container.closest('td').removeClass('danger');
                },1000);
            }
            var this_decutions = this_tr.find('#deductionpopover');
            this_decutions.text(d.deductions).number(true, 2);
            this_decutions.closest('td').addClass('danger bg-fade');
            setTimeout(function(){
                this_decutions.closest('td').removeClass('danger');
            },1000);
            this_net.text(d.netpay).number(true, 2);
            this_tax.text(d.taxamt).number(true,2);
            container.popover('hide');
            PECO.unblockUI(payroll_tbl);
        }).fail(function () {
            PECO.phpError();
            PECO.unblockUI(payroll_tbl);
        });
        return false;
    };

    var init_payroll_report = function(){

        $(document).on('submit','#submitapprovereports' , function (e) {
            e.preventDefault();
            var this_ = $(this);
            $.ajax({
                url:this_.attr("action"),
                type:this_.attr("method"),
                data:this_.serialize(),
                dataType:'json'
            }).done(function (data) {

            }).fail(function () {
                PECO.phpError();
            });
        });

        $('#payclasscombo').change(function () {
            var this_ = $(this).val();

            if(this_ == 128){
                $(document).find('#payrollperiod').attr("disabled" , false);
            }else if(this_ == 129){
                $(document).find('#payrollperiod').attr("disabled" , true);
                //   $(document).find('#payrollperiod').select2("val" , "");
            }else if(this_ == ''){
                // $(document).find('#payrollperiod').select2("val" , "");
                $(document).find('#payrollperiod').attr("disabled" , false);
            }

        });


        PECO.select2Basic($('#payclasscombo' , document) , 'admin/select2payclass' , 'Payclass' , true, false,false);
        PECO.select2Basic($('#month' , document) , 'systems/select2month' , 'Month' , true, false,false);

        $('#payrollperiod').select2({
            "allowClear": true,
            "placeholder": 'Select Period'
        });
        var d = new Date();


        report_events();

        $(document).on('submit', '#frm_get_payslip_data', function(e) {
            e.preventDefault();
            var this_ = $(this);
            $.ajax({
                url:this_.attr("action"),
                type:this_.attr("method"),
                data:this_.serialize(),
                dataType:'json',
                beforeSend: function() {
                    PECO.DTphpLoading(payrollreportstbl, 'Generating payslip preview...');
                }
            }).done(function (d) {
                if(d.qry==true) {
                    payrollreportstbl.dataTable().empty();
                    payrollreportstbl.dataTable({
                        bDestroy: true,
                        bPaginate: false,
                        bFilter: true,
                        bInfo: true,
                        bStateSave: true,
                        bProcessing: true,
                        language: PECO.DTEmptyMessage('<b><p>No approved payroll transactions for this payroll period, month and pay class.</p><p>Please ask GA to approve transaction.</p></b>'),
                        aaData: d.payrollreportdata,
                        aoColumns: [
                            {"data":"expand", sClass: 'expand'},
                            {"data":"num"},
                            {"data":"empcode"},
                            {"data":"name" ,sWidth:'40%'},
                            {"data":"department"},
                            {"data":"basic" , sClass:'number'},
                            {"data":"deductions" , sClass:'text-danger number'},
                            {"data":"earnings" , sClass:'number'},
                            {"data":"tax" , sClass:'number'},
                            {"data":"netpay" , sClass:'text-info number'},
                            {"data":"print", sClass: 'controls'}
                        ],
                        searchHighlight: true,
                        fnRowCallback: function(nRow, aData, aIndex) {
                            // PECO.dtExpandBtn(nRow, data.expand);
                            // CREATE SORT NUMBER
                            $(nRow).addClass(aData.rowcolor);
                            var index = aIndex +1;
                            $('td:eq(1)',nRow).html(index);
                        }
                    });
                }else {
                    PECO.DTAlert(payrollreportstbl, d.msg, d.func);
                }
            }).fail(function () {
                PECO.phpError();
            });
        });

        var emppayrollreportstbl = $('#emppayrollreportstbl',document);
        var empid = $('#empid',document);

        PECO.DTDefault(emppayrollreportstbl,'Click <b>GET DATA</b> to proceed.');

        PECO.select2Basic(empid,'hris/getemployees','Select Employee...',true,false,false);

        $('#frm_get_emp_payslip_data',document).on('submit',function (e) {
            e.preventDefault();
            var this_ = $(this);
            $.ajax({
                url: this_.attr('action'),
                type: this_.attr('method'),
                dataType: 'json',
                data: this_.serialize(),
                beforeSend: function() {
                    PECO.DTphpLoading(emppayrollreportstbl, 'Generating payslip preview...');
                }
            }).done(function (d) {
                emppayrollreportstbl.dataTable().empty();
                emppayrollreportstbl.dataTable({
                    bDestroy: true,
                    bPaginate: false,
                    bFilter: true,
                    bInfo: true,
                    bStateSave: true,
                    bProcessing: true,
                    aaData: d.list,
                    aoColumns: [
                        //{"data":"expand", sClass: 'expand'},
                        {"data":"num"},
                        {"data":"year"},
                        {"data":"month"},
                        {"data":"paytype" , sClass:'text-align-center'},
                        {"data":"basic" , sClass:'number'},
                        {"data":"deductions" , sClass:'text-danger number'},
                        {"data":"earnings" , sClass:'number'},
                        {"data":"tax" , sClass:'number'},
                        {"data":"net" , sClass:'text-info number'}
                    ],
                    searchHighlight: true,
                });
            }).fail(function () {
                PECO.phpError();
            });
        });

        $(document).on('click','#print_emp_payslips',function () {
            var empid = $('#empid',document).val();
            var datefrom = $('#datefrom',document).val();
            var dateto = $('#dateto',document).val();
            window.open(PECO.base_url() + 'reports/printemppayslip/' + empid + '/' + datefrom + '/' + dateto);
        });
    };

    var report_events = function(){
        $(document).on('submit','#generaterepdata' , function (e) {
            e.preventDefault();
            var this_ = $(this);
            $.ajax({
                url:this_.attr("action"),
                type:this_.attr("method"),
                data:this_.serialize(),
                dataType:'json',
                beforeSend: function() {
                    PECO.DTphpLoading(payrollreportstbl, 'Generating payslip preview...');
                }
            }).done(function (d) {
                if(d.qry==true) {
                    payrollreportstbl.dataTable().empty();
                    payrollreportstbl.dataTable({
                        bDestroy: true,
                        bPaginate: false,
                        bFilter: true,
                        bInfo: true,
                        bStateSave: true,
                        bProcessing: true,
                        aaData: d.payrollreportdata,
                        aoColumns: [
                            {"data":"expand", sClass: 'expand'},
                            {"data":"num"},
                            {"data":"empcode"},
                            {"data":"name" ,sWidth:'40%'},
                            {"data":"department"},
                            {"data":"basic" , sClass:'number'},
                            {"data":"deductions" , sClass:'text-danger number'},
                            {"data":"earnings" , sClass:'number'},
                            {"data":"tax" , sClass:'number'},
                            {"data":"netpay" , sClass:'text-info number'}
                        ],
                        searchHighlight: true,
                        /*
                        fnRowCallback: function(nRow, aData, aIndex) {
                            // PECO.dtExpandBtn(nRow, data.expand);
                            // CREATE SORT NUMBER
                            $(nRow).addClass(data.rowcolor);
                            var index = aIndex +1;
                            $('td:eq(1)',nRow).html(index);
                        }*/
                    });
                }else {
                    PECO.DTAlert(payrollreportstbl, d.msg, d.func);
                }
            }).fail(function () {
                PECO.phpError();
            });
        });
        $(document).on('click','#prinreportbtn',function (e) {
            e.preventDefault();
            var payclass = $(document).find('#payclasscombo').val();
            var month = $(document).find('#month').val();
            var year = $(document).find('#year').val();
            var period = $(document).find('#payrollperiod').val();
            $.ajax({
                url:PECO.base_url()+"payroll/getreportsdata",
                type:"post",
                data:{'payclass':payclass , 'month':month , 'year':year , 'payrollperiod':period},
                dataType:"json"
            }).done(function (d) {

                var html = '';
                html += '<div class="row">';
                html += '<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">';
                html += '<table class="table table-bordered table-condensed table-hover tbl-xs">';
                html += '<thead>';
                html += '<tr>';
                html += '<th>Emp Code</th>';
                html += '<th>Name</th>';
                html += '<th>Department</th>';
                html += '<th>Basic</th>';
                html += '<th>Deductions</th>';
                html += '<th>Earnings</th>';
                html += '<th>Tax</th>';
                html += '<th>Net Pay</th>';
                html += '</tr>';
                html += '</thead>';
                html += '<tbody>';


                var i = 0;

                for(i = 0;i < d.count - 1; i++){
                    html += '<tr>';
                    html += '<td>'+d.payrollreportdata[i].empcode+'</td>';
                    html += '<td>'+d.payrollreportdata[i].name+'</td>';
                    html += '<td>'+d.payrollreportdata[i].department+'</td>';
                    html += '<td>'+d.payrollreportdata[i].basic+'</td>';
                    html += '<td>'+d.payrollreportdata[i].deductions+'</td>';
                    html += '<td>'+d.payrollreportdata[i].earnings+'</td>';
                    html += '<td>'+d.payrollreportdata[i].tax+'</td>';
                    html += '<td>'+d.payrollreportdata[i].netpay+'</td>';

                    html += '</tr>';
                }

                html += '</tbody>';
                html += '</table>';
                html += '</div>';
                html += '</div>';
                PECO.pecoRepPrint("Payroll Report" ,html);
            });
        });
        $(document).on('click','#payslip',function (e) {
            e.preventDefault();
            var payclass = $(document).find('#payclasscombo').val();
            var month = $(document).find('#month').val();
            var year = $(document).find('#year').val();
            var period = $(document).find('#payrollperiod').val();
            var btn = $(this);

            print_payslip(payclass,month,year , period);
        });

        $(document).on('click', '#sendpayslip', function(e) {
            var payclass = $(document).find('#payclasscombo').val();
            var month = $(document).find('#month').val();
            var year = $(document).find('#year').val();
            var paytype = $(document).find('#payrollperiod').val();
            var btn = $(this);

            $.SmartMessageBox({
                    title: "<i class='fa fa-question fa-fw fa-lg txt-color-yellow'></i> Confirm: Send Payslip</span>",
                    content: 'Please confirm action taken',
                    buttons: '[Yes][No]',
                    buttonClass: "btn-success, btn-danger",
                    buttonsIcon: "fa-check, fa-times",
                },
                function (ButtonPressed) {
                    if (ButtonPressed === "Yes") {
                        send_payslip(payclass,month,year,btn , paytype);
                    }
                });
        });


        var send_payslip = function (payclass,month,year,btn , paytype) {
            var payclass_ = (payclass) ? payclass : false;
            var month_= (month) ? month : false;
            var year_ = (year) ? year : false;
            var paytype_ = (paytype) ? paytype : false;
            var btn_html = btn.html();

            if(payclass == '' || month == '' || year ==''){
                PECO.initAlerts("Please select Pay Class / Month / Year","Empty fields" ,"error");
            }else{
                $.ajax({
                    url: PECO.base_url() + 'reports/sendpayslips',
                    type: 'post',
                    data: {'payclass': payclass_, 'month': month_, 'year': year_ , 'paytype' : paytype_},
                    dataType: 'json',
                    beforeSend: function() {
                        PECO.btnLoading(btn, 'Sending...');
                    }
                }).done(function(d) {
                    if(d.qry==true) {
                        PECO.btnSuccess(btn, 'Payslips sent!');
                        setTimeout(function() {
                            btn.html(btn_html);
                            btn.removeClass('btn-danger btn-success btn-info btn-warning').addClass('btn-primary');
                        }, 2000);
                    }
                }).fail(function() {
                    PECO.btnErrorPHP(btn, btn_html, 'btn-primary');
                });
            }
        };

        var print_payslip = function (payclassparam,monthparam,yearparam , paytypeparam){
            var payclass = (payclassparam) ? payclassparam : false;
            var month = (monthparam) ? monthparam : false;
            var year = (yearparam) ? yearparam : false;
            var paytype = (paytypeparam) ? paytypeparam : false;


            if(payclass == '' || month == '' || year ==''){
                PECO.initAlerts("Please select Pay Class / Month / Year","Empty fields" ,"error");
            }else{
                if(payclass == 128 || payclass == 3077 || payclass == 3078){
                    if(paytype == ''){
                        PECO.initAlerts("Please select Paytype","Empty field" ,"error");
                    }else{
                        window.open(PECO.base_url() + 'reports/payslips/' + payclass + '/' + year + '/' + month+ '/' + paytype);
                    }
                }else{
                    window.open(PECO.base_url() + 'reports/payslips/' + payclass + '/' + year + '/' + month+ '/' + paytype);
                }
            }
        };

    };

    var pecoRepPrint = function (reptitle, content) {
        // Open a new window for the printable table
        var win = window.open('', '');
        var head = '<title>' + reptitle + '</title>';
        win.document.title = reptitle;
        win.document.body.innerHTML =
            '<head>' +
            //'<title>'+reptitle+'</title>'+
            '<link href="' + PECO.base_url() + 'assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>' +
            '<link href="' + PECO.base_url() + 'assets/global/css/components.css" rel="stylesheet" type="text/css"/>' +
            '<link href="' + PECO.base_url() + 'assets/global/css/plugins.css" rel="stylesheet" type="text/css"/>' +
            '<link href="' + PECO.base_url() + 'assets/admin/layout/css/layout.css" rel="stylesheet" type="text/css"/>' +
            '<link href="' + PECO.base_url() + 'assets/admin/layout/css/themes/default.css" rel="stylesheet" type="text/css"/>' +
            '<link href="' + PECO.base_url() + 'assets/admin/layout/css/custom.css" rel="stylesheet" type="text/css"/>' +
            '<style>body{margin: 0px 0px !important;  font-family: arial; background: #fff;}</style>' +
            '</head>' +
            '<div style="position: absolute; left: 0px; width: 100%;">' + content + '</div>';
        setTimeout(function () {
            //   win.print(); // blocking - so close will not
            //   win.close(); // execute until this is done
        }, 250);

    };
    //populate data to table
    var populate_payroll_report = function (data) {
        payrollreportstbl.dataTable().empty();
        payrollreportstbl.dataTable({
            bDestroy: true,
            bPaginate: false,
            bFilter: true,
            bInfo: true,
            bStateSave: true,
            bProcessing: true,
            aaData: data.payrollreportdata,
            aoColumns: [
                {"data":"expand", sClass: 'expand'},
                {"data":"num"},
                {"data":"empcode"},
                {"data":"name" ,sWidth:'40%'},
                {"data":"department"},
                {"data":"basic" , sClass:'number'},
                {"data":"deductions" , sClass:'text-danger number'},
                {"data":"earnings" , sClass:'number'},
                {"data":"tax" , sClass:'number'},
                {"data":"netpay" , sClass:'text-info number'}
            ],
            searchHighlight: true,
            fnRowCallback: function(nRow, aData, aIndex) {
                // PECO.dtExpandBtn(nRow, data.expand);
                // CREATE SORT NUMBER
                $(nRow).addClass(data.rowcolor);
                var index = aIndex +1;
                $('td:eq(1)',nRow).html(index);
            }
        });
    };

    var reporttable = function(payclass , month , year , period){
        $.ajax({
            url:PECO.base_url()+"payroll/getreportsdata",
            type:"post",
            data:{'payclass':payclass , 'month':month , 'year':year , 'payrollperiod':period},
            dataType:"json",
            beforeSend: function(){
                payrollreportstbl.dataTable().empty();
                PECO.DTphpLoading(payrollreportstbl, 'Loading... ');
            }
        }).done(function (d) {
            populate_payroll_report(d);
        });
    };




    var pecoRepPrint = function (reptitle, content) {
        // Open a new window for the printable table
        var win = window.open('', '');
        var head = '<title>' + reptitle + '</title>';
        win.document.title = reptitle;
        win.document.body.innerHTML =
            '<head>' +
            //'<title>'+reptitle+'</title>'+
            '<link href="' + PECO.base_url() + 'assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>' +
            '<link href="' + PECO.base_url() + 'assets/global/css/components.css" rel="stylesheet" type="text/css"/>' +
            '<link href="' + PECO.base_url() + 'assets/global/css/plugins.css" rel="stylesheet" type="text/css"/>' +
            '<link href="' + PECO.base_url() + 'assets/admin/layout/css/layout.css" rel="stylesheet" type="text/css"/>' +
            '<link href="' + PECO.base_url() + 'assets/admin/layout/css/themes/default.css" rel="stylesheet" type="text/css"/>' +
            '<link href="' + PECO.base_url() + 'assets/admin/layout/css/custom.css" rel="stylesheet" type="text/css"/>' +
            '<style>body{margin: 0px 0px !important;  font-family: arial; background: #fff;}</style>' +
            '</head>' +
            '<div style="position: absolute; left: 0px; width: 100%;">' + content + '</div>';
        setTimeout(function () {
            // win.print(); // blocking - so close will not
            // win.close(); // execute until this is done
        }, 250);

    };


    //payroll register
    var payroll_register_table = function(d){
        populatepayrollregisterstable(d);
        $(document).find('#resultgrossearnings2').text(d.resultdeptgrossearnings);
        $(document).find('#resulttotaldedn2').text(d.resultdepttotaldedn);
        $(document).find('#resulttotalnet2').text(d.resultdeptnet);
        $(document).find('#resultssscont2').text(d.resultdeptssscont);
        $(document).find('#resultsssloan2').text(d.resultdeptsssloan);
        $(document).find('#resulthdmfcont2').text(d.resultdepthdmfcont);
        $(document).find('#resulthdmfloan2').text(d.resultdepthdmfloan);
        $(document).find('#resultpecewaloan2').text(d.resultdeptpecewaloan);
        $(document).find('#resultcooploan2').text(d.resultdeptcooploan);
        $(document).find('#resultpagibigadd2').text(d.resultpagibigadd);
        $(document).find('#resultotherdeduction2').text(d.resultotherdeduction);
        $(document).find('#resulthmodedn2').text(d.resultdepthmodedn);
        $(document).find('#resultdeda2').text(d.resultdeptdeda);
        $(document).find('#resultelectricbill2').text(d.resultdeptelectricbill);
        $(document).find('#resultmemins2').text(d.resultdeptmemins);
        $(document).find('#resultlwop2').text(d.resultdeptlwop);
        $(document).find('#resultbasetax2').text(d.resultdeptbasetax);
    };


    var fetchpayrollregistertable = function(){


        var payrollyear  = $(document).find('#payrollyear').val();
        var payrollmonth = $(document).find('#payrollmonth').val();
        var payrollpayclass = $(document).find('#payrollpayclass').val();
        var payrollpaytype = $(document).find('#payrollpaytype').val();

        $.ajax({
            url:PECO.base_url()+"payroll/getpayrollregisterdata",
            type:"post",
            data:{"payrollyear": payrollyear ,"payrollmonth":payrollmonth , "payrollpayclass":payrollpayclass , "payrollpaytype":payrollpaytype},
            dataType:"json",
            beforeSend: function(){
                payrollregistertable.dataTable().empty();
                PECO.DTphpLoading(payrollregistertable, 'Loading... ');
            }
        }).done(function (d) {
            populatepayrollregisterstable(d);
            $(document).find('#resultgrossearnings2').text(d.resultdeptgrossearnings);
            $(document).find('#resulttotaldedn2').text(d.resultdepttotaldedn);
            $(document).find('#resulttotalnet2').text(d.resultdeptnet);
            $(document).find('#resultssscont2').text(d.resultdeptssscont);
            $(document).find('#resultsssloan2').text(d.resultdeptsssloan);
            $(document).find('#resulthdmfcont2').text(d.resultdepthdmfcont);
            $(document).find('#resulthdmfloan2').text(d.resultdepthdmfloan);
            $(document).find('#resultpecewaloan2').text(d.resultdeptpecewaloan);
            $(document).find('#resultcooploan2').text(d.resultdeptcooploan);
            $(document).find('#resultpagibigadd2').text(d.resultpagibigadd);
            $(document).find('#resultotherdeduction2').text(d.resultotherdeduction);
            $(document).find('#resulthmodedn2').text(d.resultdepthmodedn);
            $(document).find('#resultdeda2').text(d.resultdeptdeda);
            $(document).find('#resultelectricbill2').text(d.resultdeptelectricbill);
            $(document).find('#resultmemins2').text(d.resultdeptmemins);
            $(document).find('#resultlwop2').text(d.resultdeptlwop);
            $(document).find('#resultbasetax2').text(d.resultdeptbasetax);

        });
    };

    var populatepayrollregisterstable = function (data) {
        payrollregistertable.dataTable().empty();
        payrollregistertable.dataTable({
            bDestroy: true,
            bPaginate: false,
            bFilter: false,
            bInfo: true,
            bStateSave: false,
            bProcessing: true,
            aaData: data.payrollregisterdata,
            aoColumns: [
                {"data":"expand", sClass:'expand'},
                {"data":"deptcode"},
                {"data":"grossearnings" , sClass:'number'},
                {"data":"totaldedn" , sClass:'number'},
                {"data":"totalnet" , sClass:'number'},
                {"data":"ssscont" , sClass:'number'},
                {"data":"sssloan" , sClass:'number'},
                {"data":"hdmfcont" , sClass:'number'},
                {"data":"hdmfloan" , sClass:'number'},
                {"data":"pecewaloan" , sClass:'number'},
                {"data":"cooploan" , sClass:'number'},
                {"data":"pagibigadd" , sClass:'number'},
                {"data":"otherdeductions" , sClass:'number'},
                {"data":"hmodedn" , sClass:'number'},
                {"data":"deda" , sClass:'number'},
                {"data":"electricbill" , sClass:'number'},
                {"data":"memins" , sClass:'number'},
                {"data":"lwop" , sClass:'number'},
                {"data":"basetax" , sClass:'number'}
            ],
            "order": [[ 1, "asc" ]],
            "columnDefs": [
                {"targets": 0, "orderable": false},
                {"targets": -1, "orderable": false}
            ],
            searchHighlight: true,
            fnRowCallback: function(nRow, data, iDisplayIndex) {
                PECO.dtExpandBtn(nRow, data.expand);
            }
        });
    };


    //earnings
    var payroll_earnings_table = function(d){
        $(document).find("#totaleaningbasicrate2").text(d.totalbasicrate);
        $(document).find("#totalearningcola2").text(d.totalcola);
        $(document).find("#totalearningtransallw2").text(d.totaltransallw);
        $(document).find("#totalearningricesubsi2").text(d.totalricesubsi);
        $(document).find("#totalearningholidaypay2").text(d.totalholidaypay);
        $(document).find("#totalearningnitediff2").text(d.totalnitediff);
        $(document).find("#totalearningotpay2").text(d.totalotpay);
        $(document).find("#totalearningactingallw2").text(d.totalactingallw);
        $(document).find("#totalearningotheradd2").text(d.totalotheradd);
        populateearningstable(d);
    };

    var payrollearningstable = function(dataid){
        var payrollyear  = $(document).find('#payrollyear').val();
        var payrollmonth = $(document).find('#payrollmonth').val();
        var payrollpayclass = $(document).find('#payrollpayclass').val();
        var payrollpaytype = $(document).find('#payrollpaytype').val();
        $.ajax({
            url:PECO.base_url()+"payroll/getearningsreport",
            type:"post",
            data:{"payrollyear": payrollyear ,"payrollmonth":payrollmonth , "payrollpayclass":payrollpayclass , "payrollpaytype":payrollpaytype},
            dataType:"json",
            beforeSend: function(){
                earningregistertable.dataTable().empty();
                PECO.DTphpLoading(earningregistertable, 'Loading... ');
            }
        }).done(function (d) {
            $(document).find("#totaleaningbasicrate2").text(d.totalbasicrate);
            $(document).find("#totalearningcola2").text(d.totalcola);
            $(document).find("#totalearningtransallw2").text(d.totaltransallw);
            $(document).find("#totalearningricesubsi2").text(d.totalricesubsi);
            $(document).find("#totalearningholidaypay2").text(d.totalholidaypay);
            $(document).find("#totalearningnitediff2").text(d.totalnitediff);
            $(document).find("#totalearningotpay2").text(d.totalotpay);
            $(document).find("#totalearningactingallw2").text(d.totalactingallw);
            $(document).find("#totalearningotheradd2").text(d.totalotheradd);
            populateearningstable(d);
        });
    };

    var populateearningstable = function (data) {
        earningregistertable.dataTable().empty();
        earningregistertable.dataTable({
            bDestroy: true,
            bPaginate: false,
            bFilter: false,
            bInfo: true,
            bStateSave: false,
            bProcessing: true,
            aaData: data.earningsdata,
            aoColumns: [
                {"data":"expand", sClass:'expand'},
                {"data":"deptcode"},
                {"data":"basicrate" , sClass:'number'},
                {"data":"cola" , sClass:'number'},
                {"data":"transallw" , sClass:'number'},
                {"data":"ricesubsi" , sClass:'number'},
                {"data":"holidaypay" , sClass:'number'},
                {"data":"nitediff" , sClass:'number'},
                {"data":"otpay" , sClass:'number'},
                {"data":"actingallw" , sClass:'number'},
                {"data":"otheradd" , sClass:'number'}
            ],
            "order": [[ 1, "asc" ]],
            "columnDefs": [
                {"targets": 0, "orderable": false},
                {"targets": -1, "orderable": false}
            ],
            searchHighlight: true,
            fnRowCallback: function(nRow, data, iDisplayIndex) {
                PECO.dtExpandBtn(nRow, data.expand);
            }
        });
    };

    //deductions

    var payroll_deduction_table = function(d){
        $(document).find('#totaldeductionssscont2').text(d.totalssscont);
        $(document).find('#totaldeductionsssloan2').text(d.totalsssloan);
        $(document).find('#totaldeductionhdmfcont2').text(d.totalhdmfcont);
        $(document).find('#totaldeductionhdmfloan2').text(d.totalhdmfloan);
        $(document).find('#totaldeductionpecewaloan2').text(d.totalpecewaloan);
        $(document).find('#totaldeductioncooploan2').text(d.totalcooploan);
        $(document).find('#totaldeductionpagibigad2').text(d.totalpagibigad);
        $(document).find('#totaldeductionotherdedn2').text(d.totalotherdedn);
        $(document).find('#totaldeductionhmodedn2').text(d.totalhmodedn);
        $(document).find('#totaldeductiondeda2').text(d.totaldeda);
        $(document).find('#totaldeductionelectbill2').text(d.totalelectbill);
        $(document).find('#totaldeductionmemins2').text(d.totalmemins);
        $(document).find('#totaldeductionlwop2').text(d.totallwop);
        $(document).find('#totaldeductionbasetax2').text(d.totalbasetax);


        deductionsregistertable.dataTable().empty();
        deductionsregistertable.dataTable({
            bDestroy: true,
            bPaginate: false,
            bFilter: false,
            bInfo: true,
            bStateSave: false,
            bProcessing: true,
            aaData: d.deductionsdata,
            aoColumns: [
                {"data":"expand"},
                {"data":"deptcode"},
                {"data":"ssscont" , sClass:'number'},
                {"data":"sssloan" , sClass:'number'},
                {"data":"hdmfcont" , sClass:'number'},
                {"data":"hdmfloan" , sClass:'number'},
                {"data":"pecewaloan" , sClass:'number'},
                {"data":"cooploan" , sClass:'number'},
                {"data":"pagibigad" , sClass:'number'},
                {"data":"otherdeduct" , sClass:'number'},
                {"data":"hmodeduct" , sClass:'number'},
                {"data":"deda" , sClass:'number'},
                {"data":"electbill" , sClass:'number'},
                {"data":"memins" , sClass:'number'},
                {"data":"lwop" , sClass:'number'},
                {"data":"basetax" , sClass:'number'}
            ],
            "order": [[ 1, "asc" ]],
            "columnDefs": [
                {"targets": 0, "orderable": false},
                {"targets": -1, "orderable": false}
            ],
            searchHighlight: true,
            fnRowCallback: function(nRow, data, iDisplayIndex) {
                PECO.dtExpandBtn(nRow, data.expand);
            }
        });
    };

    /*
    var payrolldeductionstable = function(dataid){

        var payrollyear  = $(document).find('#payrollyear').val();
        var payrollmonth = $(document).find('#payrollmonth').val();
        var payrollpayclass = $(document).find('#payrollpayclass').val();
        var payrollpaytype = $(document).find('#payrollpaytype').val();
        $.ajax({
            url:PECO.base_url()+"payroll/getdeductionsreport",
            type:"post",
            data:{"payrollyear": payrollyear ,"payrollmonth":payrollmonth , "payrollpayclass":payrollpayclass , "payrollpaytype":payrollpaytype},
            dataType:"json",
            beforeSend: function(){
                deductionsregistertable.dataTable().empty();
                PECO.DTphpLoading(deductionsregistertable, 'Loading... ');
            }
        }).done(function (d) {

            $(document).find('#totaldeductionssscont2').text(d.totalssscont);
            $(document).find('#totaldeductionsssloan2').text(d.totalsssloan);
            $(document).find('#totaldeductionhdmfcont2').text(d.totalhdmfcont);
            $(document).find('#totaldeductionhdmfloan2').text(d.totalhdmfloan);
            $(document).find('#totaldeductionpecewaloan2').text(d.totalpecewaloan);
            $(document).find('#totaldeductioncooploan2').text(d.totalcooploan);
            $(document).find('#totaldeductionpagibigad2').text(d.totalpagibigad);
            $(document).find('#totaldeductionotherdedn2').text(d.totalotherdedn);
            $(document).find('#totaldeductionhmodedn2').text(d.totalhmodedn);
            $(document).find('#totaldeductiondeda2').text(d.totaldeda);
            $(document).find('#totaldeductionelectbill2').text(d.totalelectbill);
            $(document).find('#totaldeductionmemins2').text(d.totalmemins);
            $(document).find('#totaldeductionlwop2').text(d.totallwop);
            $(document).find('#totaldeductionbasetax2').text(d.totalbasetax);

            populatedeductionstable(d);
        });
    };
    */

    var payrollannualtable = function () {
        var payrollyear  = $(document).find('#payrollyear').val();
        var payrollmonth = $(document).find('#payrollmonth').val();
        var payrollpayclass = $(document).find('#payrollpayclass').val();
        var payrollpaytype = $(document).find('#payrollpaytype').val();
        $.ajax({
            url:PECO.base_url()+"payroll/getannualreport",
            type:"post",
            data:{"payrollyear": payrollyear ,"payrollmonth":payrollmonth , "payrollpayclass":payrollpayclass , "payrollpaytype":payrollpaytype},
            dataType:"json",
            beforeSend: function(){
                annualregtbl.dataTable().empty();
                PECO.DTphpLoading(annualregtbl, 'Loading... ');
            }
        }).done(function (data) {
            annualregtbl.dataTable().empty();
            annualregtbl.dataTable({
                bDestroy: true,
                bPaginate: false,
                bFilter: false,
                bInfo: true,
                bStateSave: true,
                bProcessing: true,
                aaData: data.annualregdata,
                aoColumns: [
                    {"data":"expand"},
                    {"data":"code" , sClass:'text-info'},
                    {"data":"basic"},
                    {"data":"gross"},
                    {"data":"tax"},
                    {"data":"deduction"},
                    {"data":"net"},
                ],
                searchHighlight: true
            });
        });

    };

    //overtime
    /*
    var payrollovertimetable = function(dataid){
        var payrollyear  = $(document).find('#payrollyear').val();
        var payrollmonth = $(document).find('#payrollmonth').val();
        var payrollpayclass = $(document).find('#payrollpayclass').val();
        var payrollpaytype = $(document).find('#payrollpaytype').val();
        $.ajax({
            url:PECO.base_url()+"payroll/getovertimereport",
            type:"post",
            data:{"payrollyear": payrollyear ,"payrollmonth":payrollmonth , "payrollpayclass":payrollpayclass , "payrollpaytype":payrollpaytype},
            dataType:"json",
            beforeSend: function(){
                overtimeregistertable.dataTable().empty();
                PECO.DTphpLoading(overtimeregistertable, 'Loading... ');
            }
        }).done(function (d) {
            populateovertimetable(d);
            $(document).find('#totalndothrs2').text(d.totalndothrs);
            $(document).find('#totalndotpay2').text(d.totalndotpay);
            $(document).find('#totalothrs2').text(d.totalothrs);
            $(document).find('#totalone252').text(d.totalone25);
            $(document).find('#totalone302').text(d.totalone30);
            $(document).find('#totalone502').text(d.totalone50);
            $(document).find('#totalone602').text(d.totalone60);
            $(document).find('#totalone802').text(d.totalone80);
            $(document).find('#totaltwo102').text(d.totaltwo10);
            $(document).find('#totaltwo302').text(d.totaltwo30);
            $(document).find('#totaltwo602').text(d.totaltwo60);

        });
    };
    */

    var payroll_overtime_table = function(d){
        $(document).find('#totalndothrs2').text(d.totalndothrs);
        $(document).find('#totalndotpay2').text(d.totalndotpay);
        $(document).find('#totalothrs2').text(d.totalothrs);
        $(document).find('#totalone252').text(d.totalone25);
        $(document).find('#totalone302').text(d.totalone30);
        $(document).find('#totalone502').text(d.totalone50);
        $(document).find('#totalone602').text(d.totalone60);
        $(document).find('#totalone802').text(d.totalone80);
        $(document).find('#totaltwo102').text(d.totaltwo10);
        $(document).find('#totaltwo302').text(d.totaltwo30);
        $(document).find('#totaltwo602').text(d.totaltwo60);
        overtimeregistertable.dataTable().empty();
        overtimeregistertable.dataTable({
            bDestroy: true,
            bPaginate: false,
            bFilter: false,
            bInfo: true,
            bStateSave: true,
            bProcessing: true,
            aaData: d.overtimedata,
            aoColumns: [
                {"data":"expand"},
                {"data":"deptcode" , sClass:'text-info'},
                {"data":"ndot8hrs"},
                {"data":"ndotpay"},
                {"data":"othrs"},
                {"data":"125%"},
                {"data":"130%"},
                {"data":"150%"},
                {"data":"160%"},
                {"data":"180%"},
                {"data":"210%"},
                {"data":"230%"},
                {"data":"260%"}
            ],
            searchHighlight: true
        });
    };

    var init_taxreport = function(){
        load_taxreport();
        events_taxreports();
    };

    var load_taxreport = function(payclassparam,month,yearparam,print){
        var payclass =  (payclassparam) ? payclassparam : false;
        var monthdata = (month) ? month : false;
        var year = (yearparam) ? yearparam : false;
        var print = (print) ? print : false;
        var d = new Date();
        var defaultyear = d.getFullYear();
        var monthdefault = d.getMonth() + 1;
        if(monthdata > 0){
            monthdefault = monthdata;
        }
        if(year > 0){
            defaultyear = year;
        }

        $.ajax({
            url:PECO.base_url()+'payroll/getemployeetaxes',
            type:'post',
            data:{"payclass":payclass,"month":monthdefault ,"year" : defaultyear,"print":print},
            dataType:'json'
        }).done(function (data) {
            if(data.print == true){
                PECO.pecoRepPrint(data.title,data.html);
            }
            confidentialtabletax.dataTable().empty();
            confidentialtabletax.dataTable({
                bDestroy: true,
                bPaginate: false,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                bProcessing: true,
                aaData: data.confidentialtaxdata,
                aoColumns: [
                    {"data":"id"},
                    {"data":"empname" , sClass:'text-info'},
                    {"data":"tax"}
                ],
                searchHighlight: true
            });
            rankandfiletabletax.dataTable().empty();
            rankandfiletabletax.dataTable({
                bDestroy: true,
                bPaginate: false,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                bProcessing: true,
                aaData: data.rankandfiletaxdata,
                aoColumns: [
                    {"data":"id"},
                    {"data":"empname" , sClass:'text-info'},
                    {"data":"tax"}
                ],
                searchHighlight: true
            });
        }).fail(function () {
            PECO.phpError();
        });
    };

    var events_taxreports = function(){
        $('#confisearchtax',document).click(function () {
            var configetmonth = $('#configetmonth',document).val();
            var yeardata = $('#yearconfi',document).val();
            load_taxreport(129,configetmonth,yeardata);
        });
        $('#rankandfilesearchtax',document).click(function () {
            var rankandfilegetmonth = $('#rankandfilegetmonth',document).val();
            var yeardata = $('#rankandfileyear',document).val();
            load_taxreport(128,rankandfilegetmonth,yeardata);
        });
        $('#confiprinttax',document).click(function () {
            var configetmonth = $('#configetmonth',document).val();
            var yeardata = $('#yearconfi',document).val();
            load_taxreport(129,configetmonth,yeardata,129);
        });
        $('#rankandfileprinttax',document).click(function () {
            var rankandfilegetmonth = $('#rankandfilegetmonth',document).val();
            var yeardata = $('#rankandfileyear',document).val();
            load_taxreport(128,rankandfilegetmonth,yeardata,128);
        });
    };

    var init_hdmfreport = function(){
        $(document).on('click','#rankandfilesearchhdmf',function () {
            var rankandfilegetmonth = $('#rankandfilegetmonthhdmf',document).val();
            var yeardata = $('#rankandfileyearhdmf',document).val();
            loadhdmfreport(128 , rankandfilegetmonth , yeardata , false);
        });
        $(document).on('click','#rankandfileprinthdmf',function () {
            var rankandfilegetmonth = $('#rankandfilegetmonthhdmf',document).val();
            var yeardata = $('#rankandfileyearhdmf',document).val();
            loadhdmfreport(128 , rankandfilegetmonth , yeardata , 128);
        });
        $(document).on('click','#confisearchhdmf',function () {
            var configetmonthhdmf = $('#configetmonthhdmf',document).val();
            var yearconfihdmf = $('#yearconfihdmf',document).val();
            loadhdmfreport(129 , configetmonthhdmf , yearconfihdmf , false);
        });
        $(document).on('click','#confiprinthdmf',function () {
            var configetmonthhdmf = $('#configetmonthhdmf',document).val();
            var yearconfihdmf = $('#yearconfihdmf',document).val();
            loadhdmfreport(129 , configetmonthhdmf , yearconfihdmf , 129);
        });
    };

    var loadhdmfreport = function(payclassparam,month,yearparam,print){
        var payclass =  (payclassparam) ? payclassparam : false;
        var monthdata = (month) ? month : false;
        var year = (yearparam) ? yearparam : false;
        var print = (print) ? print : false;
        var d = new Date();
        var defaultyear = d.getFullYear();
        var monthdefault = d.getMonth() + 1;
        if(monthdata > 0){
            monthdefault = monthdata;
        }
        if(year > 0){
            defaultyear = year;
        }

        $.ajax({
            url:PECO.base_url()+'payroll/gethdmfloan',
            type:'post',
            data:{"payclass":payclass,"month":monthdefault , "year":defaultyear , "print":print},
            dataType:'json'
        }).done(function (data) {
            if(data.print > 0){
                PECO.pecoRepPrint("CONFIDENTIAL & SUPERVISOR DEDUCTIONS",data.html);
            }
            if(data.payclass == 128){
                rankandfiletablehdmf.dataTable().empty();
                rankandfiletablehdmf.dataTable({
                    bDestroy: true,
                    bPaginate: false,
                    bFilter: true,
                    bInfo: true,
                    bStateSave: true,
                    bProcessing: true,
                    aaData: data.rankandfilehdmfloan,
                    aoColumns: [
                        {"data":"num"},
                        {"data":"name" , sClass:'text-info'},
                        {"data":"amount"}
                    ],
                    searchHighlight: true
                });
            }else{
                confidentialtablehdmf.dataTable().empty();
                confidentialtablehdmf.dataTable({
                    bDestroy: true,
                    bPaginate: false,
                    bFilter: true,
                    bInfo: true,
                    bStateSave: true,
                    bProcessing: true,
                    aaData: data.confidentialhdmfloan,
                    aoColumns: [
                        {"data":"num"},
                        {"data":"name" , sClass:'text-info'},
                        {"data":"amount"}
                    ],
                    searchHighlight: true
                });
            }
        }).fail(function () {
            PECO.phpError();
        });

    };

    var init_sssloanreport = function () {
        $(document).on('click','#confisearchsssloan',function () {
            var configetmonthsssloan = $('#configetmonthsssloan',document).val();
            var yearconfisssloan = $('#yearconfisssloan',document).val();
            loadsssloanreport(129 , configetmonthsssloan , yearconfisssloan , false);
        });
        $(document).on('click','#confiprintsssloan',function () {
            var configetmonthsssloan = $('#configetmonthsssloan',document).val();
            var yearconfisssloan = $('#yearconfisssloan',document).val();
            loadsssloanreport(129 , configetmonthsssloan , yearconfisssloan , 129);
        });
        $(document).on('click','#rankandfilesearchsssloan',function () {
            var rankandfilegetmonthsssloan = $('#rankandfilegetmonthsssloan',document).val();
            var yeardata = $('#rankandfileyearhdmf',document).val();
            loadsssloanreport(128 , rankandfilegetmonthsssloan , yeardata , false);
        });
        $(document).on('click','#rankandfileprintsssloan',function () {
            var rankandfilegetmonthsssloan = $('#rankandfilegetmonthsssloan',document).val();
            var rankandfileyearsssloan = $('#rankandfileyearsssloan',document).val();
            loadsssloanreport(128 , rankandfilegetmonthsssloan , rankandfileyearsssloan , 128);
        });
    };

    var loadsssloanreport = function(payclassparam,month,yearparam,print){
        var payclass =  (payclassparam) ? payclassparam : false;
        var monthdata = (month) ? month : false;
        var year = (yearparam) ? yearparam : false;
        var print = (print) ? print : false;
        var d = new Date();
        var defaultyear = d.getFullYear();
        var monthdefault = d.getMonth() + 1;
        if(monthdata > 0){
            monthdefault = monthdata;
        }
        if(year > 0){
            defaultyear = year;
        }

        $.ajax({
            url:PECO.base_url()+'payroll/getsssloan',
            type:'post',
            data:{"payclass":payclass,"month":monthdefault , "year":defaultyear , "print":print},
            dataType:'json'
        }).done(function (data) {
            if(data.print > 0){
                PECO.pecoRepPrint("CONFIDENTIAL & SUPERVISOR DEDUCTIONS",data.html);
            }
            if(data.payclass == 128){
                rankandfiletablesssloan.dataTable().empty();
                rankandfiletablesssloan.dataTable({
                    bDestroy: true,
                    bPaginate: false,
                    bFilter: true,
                    bInfo: true,
                    bStateSave: true,
                    bProcessing: true,
                    aaData: data.rankandfilesssloan,
                    aoColumns: [
                        {"data":"num"},
                        {"data":"name" , sClass:'text-info'},
                        {"data":"amount"}
                    ],
                    searchHighlight: true
                });
            }else{
                confidentialtablesssloan.dataTable().empty();
                confidentialtablesssloan.dataTable({
                    bDestroy: true,
                    bPaginate: false,
                    bFilter: true,
                    bInfo: true,
                    bStateSave: true,
                    bProcessing: true,
                    aaData: data.confidentialsssloan,
                    aoColumns: [
                        {"data":"num"},
                        {"data":"name" , sClass:'text-info'},
                        {"data":"amount"}
                    ],
                    searchHighlight: true
                });
            }
        }).fail(function () {
            PECO.phpError();
        });
    };

    var init_ssscontreport = function(){
        $(document).on('click','#confisearchssscont',function () {
            var configetmonthssscont = $('#configetmonthssscont',document).val();
            var yearconfissscont = $('#yearconfissscont',document).val();
            loadssscontreport(129 , configetmonthssscont , yearconfissscont , false);
        });
        $(document).on('click','#confiprintssscont',function () {
            var configetmonthssscont = $('#configetmonthssscont',document).val();
            var yearconfissscont = $('#yearconfissscont',document).val();
            loadssscontreport(129 , configetmonthssscont , yearconfissscont , 129);
        });
        $(document).on('click','#rankandfilesearchssscont',function () {
            var rankandfilegetmonthssscont = $('#rankandfilegetmonthssscont',document).val();
            var yeardata = $('#rankandfileyearssscont',document).val();
            loadssscontreport(128 , rankandfilegetmonthssscont , yeardata , false);
        });
        $(document).on('click','#rankandfileprintssscont',function () {
            var rankandfilegetmonthssscont = $('#rankandfilegetmonthssscont',document).val();
            var rankandfileyearssscont = $('#rankandfileyearssscont',document).val();
            loadssscontreport(128 , rankandfilegetmonthssscont , rankandfileyearssscont , 128);
        });
    };


    var loadssscontreport = function(payclassparam,month,yearparam,print){
        var payclass =  (payclassparam) ? payclassparam : false;
        var monthdata = (month) ? month : false;
        var year = (yearparam) ? yearparam : false;
        var print = (print) ? print : false;
        var d = new Date();
        var defaultyear = d.getFullYear();
        var monthdefault = d.getMonth() + 1;
        if(monthdata > 0){
            monthdefault = monthdata;
        }
        if(year > 0){
            defaultyear = year;
        }

        $.ajax({
            url:PECO.base_url()+'payroll/getssscont',
            type:'post',
            data:{"payclass":payclass,"month":monthdefault , "year":defaultyear , "print":print},
            dataType:'json'
        }).done(function (data) {
            if(data.print > 0){
                PECO.pecoRepPrint("CONFIDENTIAL & SUPERVISOR DEDUCTIONS",data.html);
            }
            if(data.payclass == 128){
                rankandfiletablessscont.dataTable().empty();
                rankandfiletablessscont.dataTable({
                    bDestroy: true,
                    bPaginate: false,
                    bFilter: true,
                    bInfo: true,
                    bStateSave: true,
                    bProcessing: true,
                    aaData: data.rankandfilessscont,
                    aoColumns: [
                        {"data":"num"},
                        {"data":"name" , sClass:'text-info'},
                        {"data":"amount"}
                    ],
                    searchHighlight: true
                });
            }else{
                confidentialtablessscont.dataTable().empty();
                confidentialtablessscont.dataTable({
                    bDestroy: true,
                    bPaginate: false,
                    bFilter: true,
                    bInfo: true,
                    bStateSave: true,
                    bProcessing: true,
                    aaData: data.confidentialssscont,
                    aoColumns: [
                        {"data":"num"},
                        {"data":"name" , sClass:'text-info'},
                        {"data":"amount"}
                    ],
                    searchHighlight: true
                });
            }
        }).fail(function () {
            PECO.phpError();
        });
    };

    var init_pecewareport = function(){
        $(document).on('click','#confisearchpecewa',function () {
            var configetmonthpecewa = $('#configetmonthpecewa',document).val();
            var yearconfipecewa = $('#yearconfipecewa',document).val();
            loadpecewareport(129 , configetmonthpecewa , yearconfipecewa , false);
        });
        $(document).on('click','#confiprintpecewa',function () {
            var configetmonthpecewa = $('#configetmonthpecewa',document).val();
            var yearconfipecewa = $('#yearconfipecewa',document).val();
            loadpecewareport(129 , configetmonthpecewa , yearconfipecewa , 129);
        });
        $(document).on('click','#rankandfilesearchpecewa',function () {
            var rankandfilegetmonthpecewa = $('#rankandfilegetmonthpecewa',document).val();
            var yeardata = $('#rankandfileyearpecewa',document).val();
            loadpecewareport(128 , rankandfilegetmonthpecewa , yeardata , false);
        });
        $(document).on('click','#rankandfileprintpecewa',function () {
            var rankandfilegetmonthpecewa = $('#rankandfilegetmonthpecewa',document).val();
            var rankandfileyearpecewa = $('#rankandfileyearpecewa',document).val();
            loadpecewareport(128 , rankandfilegetmonthpecewa , rankandfileyearpecewa , 128);
        });
    };

    var loadpecewareport = function(payclassparam,month,yearparam,print){
        var payclass =  (payclassparam) ? payclassparam : false;
        var monthdata = (month) ? month : false;
        var year = (yearparam) ? yearparam : false;
        var print = (print) ? print : false;
        var d = new Date();
        var defaultyear = d.getFullYear();
        var monthdefault = d.getMonth() + 1;
        if(monthdata > 0){
            monthdefault = monthdata;
        }
        if(year > 0){
            defaultyear = year;
        }

        $.ajax({
            url:PECO.base_url()+'payroll/getpecewa',
            type:'post',
            data:{"payclass":payclass,"month":monthdefault , "year":defaultyear , "print":print},
            dataType:'json'
        }).done(function (data) {
            if(data.print > 0){
                PECO.pecoRepPrint("CONFIDENTIAL & SUPERVISOR DEDUCTIONS",data.html);
            }
            if(data.payclass == 128){
                rankandfiletablepecewa.dataTable().empty();
                rankandfiletablepecewa.dataTable({
                    bDestroy: true,
                    bPaginate: false,
                    bFilter: true,
                    bInfo: true,
                    bStateSave: true,
                    bProcessing: true,
                    aaData: data.rankandfilepecewa,
                    aoColumns: [
                        {"data":"num"},
                        {"data":"name" , sClass:'text-info'},
                        {"data":"amount"}
                    ],
                    searchHighlight: true
                });
            }else{
                confidentialtablepecewa.dataTable().empty();
                confidentialtablepecewa.dataTable({
                    bDestroy: true,
                    bPaginate: false,
                    bFilter: true,
                    bInfo: true,
                    bStateSave: true,
                    bProcessing: true,
                    aaData: data.confidentialpecewa,
                    aoColumns: [
                        {"data":"num"},
                        {"data":"name" , sClass:'text-info'},
                        {"data":"amount"}
                    ],
                    searchHighlight: true
                });
            }
        }).fail(function () {
            PECO.phpError();
        });
    };

    var init_coopreport = function(){
        $(document).on('click','#confisearchcoop',function () {
            var configetmonthcoop = $('#configetmonthcoop',document).val();
            var yearconficoop = $('#yearconficoop',document).val();
            loadcoopreport(129 , configetmonthcoop , yearconficoop , false);
        });
        $(document).on('click','#confiprintcoop',function () {
            var configetmonthcoop = $('#configetmonthcoop',document).val();
            var yearconficoop = $('#yearconficoop',document).val();
            loadcoopreport(129 , configetmonthcoop , yearconficoop , 129);
        });
        $(document).on('click','#rankandfilesearchcoop',function () {
            var rankandfilegetmonthcoop = $('#rankandfilegetmonthcoop',document).val();
            var yeardata = $('#rankandfileyearcoop',document).val();
            loadcoopreport(128 , rankandfilegetmonthcoop , yeardata , false);
        });
        $(document).on('click','#rankandfileprintcoop',function () {
            var rankandfilegetmonthcoop = $('#rankandfilegetmonthcoop',document).val();
            var rankandfileyearcoop = $('#rankandfileyearcoop',document).val();
            loadcoopreport(128 , rankandfilegetmonthcoop , rankandfileyearcoop , 128);
        });
    };

    var loadcoopreport = function(payclassparam,month,yearparam,print){
        var payclass =  (payclassparam) ? payclassparam : false;
        var monthdata = (month) ? month : false;
        var year = (yearparam) ? yearparam : false;
        var print = (print) ? print : false;
        var d = new Date();
        var defaultyear = d.getFullYear();
        var monthdefault = d.getMonth() + 1;
        if(monthdata > 0){
            monthdefault = monthdata;
        }
        if(year > 0){
            defaultyear = year;
        }

        $.ajax({
            url:PECO.base_url()+'payroll/getcoop',
            type:'post',
            data:{"payclass":payclass,"month":monthdefault , "year":defaultyear , "print":print},
            dataType:'json'
        }).done(function (data) {
            if(data.print > 0){
                PECO.pecoRepPrint("CONFIDENTIAL & SUPERVISOR DEDUCTIONS",data.html);
            }
            if(data.payclass == 128){
                rankandfiletablecoop.dataTable().empty();
                rankandfiletablecoop.dataTable({
                    bDestroy: true,
                    bPaginate: false,
                    bFilter: true,
                    bInfo: true,
                    bStateSave: true,
                    bProcessing: true,
                    aaData: data.rankandfilecoop,
                    aoColumns: [
                        {"data":"num"},
                        {"data":"name" , sClass:'text-info'},
                        {"data":"amount"}
                    ],
                    searchHighlight: true
                });
            }else{
                confidentialtablecoop.dataTable().empty();
                confidentialtablecoop.dataTable({
                    bDestroy: true,
                    bPaginate: false,
                    bFilter: true,
                    bInfo: true,
                    bStateSave: true,
                    bProcessing: true,
                    aaData: data.confidentialcoop,
                    aoColumns: [
                        {"data":"num"},
                        {"data":"name" , sClass:'text-info'},
                        {"data":"amount"}
                    ],
                    searchHighlight: true
                });
            }
        }).fail(function () {
            PECO.phpError();
        });
    };

    return {
        init: function(payclass) {
            init_payroll(payclass , 1);
            init_payroll_event(payclass);
        },
        report:function(dataid){
            init_payroll_report();
            // init_payroll_reports(dataid);
        },
        taxreport:function(){
            init_taxreport();
        },
        hdmfreport:function () {
            init_hdmfreport();
        },
        sssloanreport:function(){
            init_sssloanreport();
        },
        ssscontreport:function () {
            init_ssscontreport();
        },
        pecewareport:function(){
            init_pecewareport();
        },
        coopreport:function(){
            init_coopreport();
        },
        initpreviewtrn:function (payclass) {
            getpayrollpreviewtrn(payclass);
        }
    }
}();
