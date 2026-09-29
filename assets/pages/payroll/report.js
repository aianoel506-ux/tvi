var PAYROLLREPORTS = function(){

    PECO.getSelect2Plugins();
    PECO.getHighlightsPlugin();
    PECO.getSweetAlert();

    var payrollregistertable = $(document).find('#payrollregistertable');
    var earningregistertable = $(document).find('#earningregistertable');
    var deductionsregistertable = $(document).find('#deductionsregistertable');
    var overtimeregistertable = $(document).find('#overtimeregistertable');

    var init_payroll_reports = function (dataid) {

        var inputarrs = {dataid: dataid};
        PECO.dtSubDetails(payrollregistertable , 'payroll/getempdeptpayrollregister' , inputarrs);
        PECO.dtSubDetails(earningregistertable , 'payroll/getempdeptearnings' , inputarrs);
        PECO.dtSubDetails(deductionsregistertable , 'payroll/getempdeptdeductions' , inputarrs);
        PECO.dtSubDetails(overtimeregistertable , 'payroll/getempdeptovertime' , inputarrs);


        fetchpayrollregistertable(dataid);
        payrollearningstable(dataid);
        payrolldeductionstable(dataid);
        payrollovertimetable(dataid);
        handleevents(dataid);
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
             //  win.close(); // execute until this is done
        }, 250);

    };

    var handleevents = function(dataid){

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
                        data: {'dataid': dataid}
                    }).done(function (d) {
                        $(document).find('.pceopayrollbtn').hide();
                        swal(d.title, d.msg, d.func);
                    });

                }
            });
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
                        data: {'dataid': dataid}
                    }).done(function (d) {
                            $(document).find('.pceopayrollbtn').hide();
                            swal(d.title, d.msg, d.func);
                    });
                }
            });
        });

        $(document).on('click','#printdednregbyemp',function (e) {
                e.preventDefault();
                $.ajax({
                    url:PECO.base_url()+'payroll/getprintdednregbyemp',
                    data:{"dataid":dataid},
                    type:'post',
                    dataType:'json'
                }).done(function (d) {
                    pecoRepPrint("Deductions Register By Employee" , d.html);
                }).fail(function () {
                    PECO.phpError();
                });
        });

        $(document).on('click','#printearregbyemp',function (e) {
            e.preventDefault();
            $.ajax({
                url:PECO.base_url()+'payroll/getprintearregbyemp',
                data:{"dataid":dataid},
                type:'post',
                dataType:'json'
            }).done(function (d) {
                pecoRepPrint("Eearning Register By Employee" , d.html);
            }).fail(function () {
                PECO.phpError();
            });
        });

        $(document).on('click','#printpayregbyemp',function (e) {
           e.preventDefault();
           $.ajax({
               url:PECO.base_url()+'payroll/getprintpayregbyemp',
               type:'post',
               data:{"dataid":dataid},
               dataType:'json'
           }).done(function (d) {
               pecoRepPrint("Payroll Register By Employee" , d.html);
           }).fail(function () {
               PECO.phpError();
           });
        });

        //by department totals
        $(document).on('click','#printpayreg',function (e) {
            e.preventDefault();
            $.ajax({
                url:PECO.base_url()+'payroll/getpayrollregisterdata',
                data:{"dataid":dataid},
                type:'post',
                dataType:'json'
            }).done(function (d) {
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



        $(document).on('click','#printearregdepttotals',function (e) {
            e.preventDefault();
            $.ajax({
                url:PECO.base_url()+'payroll/getearningsreport',
                data:{"dataid":dataid},
                type:'post',
                dataType:'json'
            }).done(function (d) {
                var today = new Date();
                var dd = today.getDate();
                var mm = today.getMonth()+1; //January is 0!
                var yyyy = today.getFullYear();

                if(dd<10) {
                    dd = '0'+dd
                }

                if(mm<10) {
                    mm = '0'+mm
                }

                today = mm + '/' + dd + '/' + yyyy;

                var count = 0;
                var html = '';
                var index = 0;
                count = d.datacount;

                html+= '<div class="row">';
                html+= '<div class="col-md-12 cold-sm-12 col-xs-12 col-lg-12">';
                html+= d.header;

                html+= '<table class="table table-condensed tbl-xs print-table-standard" id="earningregistertable">';
                html+= '<thead>';
                html+= '<tr>';

                html+= '<th></th>';
                html+= '<th>401</th>';
                html+= '<th>410</th>';
                html+= '<th>401</th>';
                html+= '<th>410</th>';
                html+= '<th>401</th>';
                html+= '<th>401</th>';
                html+= '<th>401</th>';
                html+= '<th>401</th>';
                html+= '<th>401</th>';
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

        $(document).on('click','#printdednregdepttotals',function (e) {
           e.preventDefault();
            $.ajax({
                url:PECO.base_url()+'payroll/getdeductionsreport',
                data:{"dataid":dataid},
                type:'post',
                dataType:'json'
            }).done(function (d) {

                var today = new Date();
                var dd = today.getDate();
                var mm = today.getMonth()+1; //January is 0!
                var yyyy = today.getFullYear();

                if(dd<10) {
                    dd = '0'+dd
                }

                if(mm<10) {
                    mm = '0'+mm
                }

                today = mm + '/' + dd + '/' + yyyy;


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
                html += '<th>401</th>';
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

        $(document).on('click' , '#printotregdepttotals' , function () {
            $.ajax({
                url:PECO.base_url()+"payroll/getovertimereport",
                type:"post",
                data:{"dataid":dataid},
                dataType:"json"
            }).done(function (data) {
                pecoRepPrint("OVERTIME" , data.html);
            });
        });

        $(document).on('click' , '#printotregbyemp' , function () {

            $.ajax({
                url:PECO.base_url()+"payroll/printotregisterbyemp",
                type:"post",
                data:{"dataid":dataid},
                dataType:"json"
            }).done(function (data) {
                pecoRepPrint("OVERTIME" , data.html);
            });
        });

    };

    //payroll register
    var fetchpayrollregistertable = function(dataid){
        $.ajax({
            url:PECO.base_url()+"payroll/getpayrollregisterdata",
            type:"post",
            data:{"dataid":dataid},
            dataType:"json",
            beforeSend: function(){
                payrollregistertable.dataTable().empty();
                PECO.DTphpLoading(payrollregistertable, 'Loading... ');
            }
        }).done(function (d) {
            $(document).find('#resultgrossearnings').text(d.resultdeptgrossearnings);
            $(document).find('#resulttotaldedn').text(d.resultdepttotaldedn);
            $(document).find('#resulttotalnet').text(d.resultdeptnet);
            $(document).find('#resultssscont').text(d.resultdeptssscont);
            $(document).find('#resultsssloan').text(d.resultdeptsssloan);
            $(document).find('#resulthdmfcont').text(d.resultdepthdmfcont);
            $(document).find('#resulthdmfloan').text(d.resultdepthdmfloan);
            $(document).find('#resultpecewaloan').text(d.resultdeptpecewaloan);
            $(document).find('#resultcooploan').text(d.resultdeptcooploan);
            $(document).find('#resultpagibigadd').text(d.resultpagibigadd);
            $(document).find('#resultotherdeduction').text(d.resultotherdeduction);
            $(document).find('#resulthmodedn').text(d.resultdepthmodedn);
            $(document).find('#resultdeda').text(d.resultdeptdeda);
            $(document).find('#resultelectricbill').text(d.resultdeptelectricbill);
            $(document).find('#resultmemins').text(d.resultdeptmemins);
            $(document).find('#resultlwop').text(d.resultdeptlwop);
            $(document).find('#resultbasetax').text(d.resultdeptbasetax);
            populatepayrollregisterstable(d);
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
    var payrollearningstable = function(dataid){
        $.ajax({
            url:PECO.base_url()+"payroll/getearningsreport",
            type:"post",
            data:{"dataid":dataid},
            dataType:"json",
            beforeSend: function(){
                earningregistertable.dataTable().empty();
                PECO.DTphpLoading(earningregistertable, 'Loading... ');
            }
        }).done(function (d) {
            $(document).find("#totaleaningbasicrate").text(d.totalbasicrate);
            $(document).find("#totalearningcola").text(d.totalcola);
            $(document).find("#totalearningtransallw").text(d.totaltransallw);
            $(document).find("#totalearningricesubsi").text(d.totalricesubsi);
            $(document).find("#totalearningholidaypay").text(d.totalholidaypay);
            $(document).find("#totalearningnitediff").text(d.totalnitediff);
            $(document).find("#totalearningotpay").text(d.totalotpay);
            $(document).find("#totalearningactingallw").text(d.totalactingallw);
            $(document).find("#totalearningotheradd").text(d.totalotheradd);
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

    var payrolldeductionstable = function(dataid){
        $.ajax({
            url:PECO.base_url()+"payroll/getdeductionsreport",
            type:"post",
            data:{"dataid":dataid},
            dataType:"json",
            beforeSend: function(){
                deductionsregistertable.dataTable().empty();
                PECO.DTphpLoading(deductionsregistertable, 'Loading... ');
            }
        }).done(function (d) {

            $(document).find('#totaldeductionssscont').text(d.totalssscont);
            $(document).find('#totaldeductionsssloan').text(d.totalsssloan);
            $(document).find('#totaldeductionhdmfcont').text(d.totalhdmfcont);
            $(document).find('#totaldeductionhdmfloan').text(d.totalhdmfloan);
            $(document).find('#totaldeductionpecewaloan').text(d.totalpecewaloan);
            $(document).find('#totaldeductioncooploan').text(d.totalcooploan);
            $(document).find('#totaldeductionpagibigad').text(d.totalpagibigad);
            $(document).find('#totaldeductionotherdedn').text(d.totalotherdedn);
            $(document).find('#totaldeductionhmodedn').text(d.totalhmodedn);
            $(document).find('#totaldeductiondeda').text(d.totaldeda);
            $(document).find('#totaldeductionelectbill').text(d.totalelectbill);
            $(document).find('#totaldeductionmemins').text(d.totalmemins);
            $(document).find('#totaldeductionlwop').text(d.totallwop);
            $(document).find('#totaldeductionbasetax').text(d.totalbasetax);

            populatedeductionstable(d);
        });
    };

    var populatedeductionstable = function (data) {
        deductionsregistertable.dataTable().empty();
        deductionsregistertable.dataTable({
            bDestroy: true,
            bPaginate: false,
            bFilter: false,
            bInfo: true,
            bStateSave: false,
            bProcessing: true,
            aaData: data.deductionsdata,
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

    //overtime

    var payrollovertimetable = function(dataid){
        $.ajax({
            url:PECO.base_url()+"payroll/getovertimereport",
            type:"post",
            data:{"dataid":dataid},
            dataType:"json",
            beforeSend: function(){
                overtimeregistertable.dataTable().empty();
                PECO.DTphpLoading(overtimeregistertable, 'Loading... ');
            }
        }).done(function (d) {
            populateovertimetable(d);

            $(document).find('#totalndothrs').text(d.totalndothrs);
            $(document).find('#totalndotpay').text(d.totalndotpay);
            $(document).find('#totalothrs').text(d.totalothrs);
            $(document).find('#totalone25').text(d.totalone25);
            $(document).find('#totalone30').text(d.totalone30);
            $(document).find('#totalone50').text(d.totalone50);
            $(document).find('#totalone60').text(d.totalone60);
            $(document).find('#totalone80').text(d.totalone80);
            $(document).find('#totaltwo10').text(d.totaltwo10);
            $(document).find('#totaltwo30').text(d.totaltwo30);
            $(document).find('#totaltwo60').text(d.totaltwo60);

        });
    };

    var populateovertimetable = function (data) {
        overtimeregistertable.dataTable().empty();
        overtimeregistertable.dataTable({
            bDestroy: true,
            bPaginate: false,
            bFilter: false,
            bInfo: true,
            bStateSave: true,
            bProcessing: true,
            aaData: data.overtimedata,
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
            searchHighlight: true,
            fnRowCallback: function(nRow, data, iDisplayIndex) {
                PECO.dtExpandBtn(nRow, data.expand);
            }
        });
    };
    return{
        init:function(dataid){
            init_payroll_reports(dataid);
        }
    }
}();