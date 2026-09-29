var HRIS = function () {
    PECO.getHighlightsPlugin();
    PECO.getiCheckPlugin();
    PECO.getSweetAlert();
    PECO.getSelect2Plugins();


    var base_url = PECO.base_url();
    var dt = $('#emptable').dataTable();
    var filedropzone = $(document).find('#accompfiledrop');
    var workhistorytbl= $(document).find('#workhistorytbl');
    var scheduletable = $(document).find('#scheduletable');
    var tbl_premiums = $(document).find('#tbl_premiums');
    var tbl_loans = $(document).find('#tbl_loans');
    var tbl_deductions = $(document).find('#tbl_deductions');
    var prempaytype = $(document).find('#prempaytype');
    var dedtype  = $(document).find('#dedtype');
    var workshiftpending = $(document).find('#workshiftpending');
    var empdependentstable= $(document).find('#empdependentstable');
    var payrollfixamttable = $(document).find('#payrollfixamttable');
    var dt_logs = $(document).find('#dt_logs');

    var attachment_explorer_container = $('#attachement_container', document);

    var init_send_payslip_form = function () {
        var updatePeriodUI = function () {
            if (!$('#send_payslip_payclass').length) {
                return;
            }
            var payclass = parseInt($('#send_payslip_payclass').val(), 10);
            if ($.inArray(payclass, [128, 3077, 3078]) !== -1) {
                $('#group_period').show();
                $('#send_payslip_period').val('');
            } else {
                $('#group_period').hide();
                $('#send_payslip_period').val(1);
            }
        };

        updatePeriodUI();

        $(document)
            .off('submit.send_payslip', '#form_send_payslip')
            .on('submit.send_payslip', '#form_send_payslip', function (e) {
                e.preventDefault();
                var this_ = $(this);
                var monthVal = $('#send_payslip_month').val();
                var yearVal = $('#send_payslip_year').val();
                var payclassVal = $('#send_payslip_payclass').val();

                if (!monthVal || !yearVal) {
                    swal("Missing", "Please select Month and Year before sending the payslip.", "warning");
                    return;
                }

                if ($.inArray(parseInt(payclassVal), [128, 3077, 3078]) !== -1) {
                    var periodVal = $('#send_payslip_period').val();
                    if (!periodVal) {
                        swal("Missing", "Please select Period (1st/2nd Half) for semi-monthly payslip.", "warning");
                        return;
                    }
                }

                var submitBtn = $('#btn_send_payslip_submit');
                var originalBtnHtml = submitBtn.html();
                submitBtn.prop('disabled', true);
                submitBtn.html('Sending...');
                $.ajax({
                    url: this_.data('action') || this_.attr('action'),
                    type: this_.attr('method'),
                    data: this_.serialize(),
                    dataType: 'json'
                }).done(function (d) {
                    if (d && d.qry) {
                        swal("Success", "Payslip sent to " + (d.email ? d.email : "employee email") + ".", "success");
                    } else {
                        var t = (d && d.title) ? d.title : "Info";
                        var m = (d && d.msg) ? d.msg : "Unable to send payslip.";
                        swal(t, m, "warning");
                    }
                }).fail(function (xhr) {
                    var title = 'Error';
                    var msg = 'Unable to send payslip.';
                    if (xhr && xhr.responseText) {
                        try {
                            var parsed = JSON.parse(xhr.responseText);
                            title = parsed.title || title;
                            msg = parsed.msg || msg;
                        } catch (e) {
                            msg = xhr.responseText;
                        }
                    }
                    swal(title, msg, "error");
                }).always(function () {
                    submitBtn.prop('disabled', false);
                    submitBtn.html(originalBtnHtml);
                });
            });
    };

    $(function () {
        init_send_payslip_form();
    });

    var init_attachements_explorer = function(dataid) {
        $.ajax({
            url: PECO.base_url() + 'upload/employeeattachments',
            type: 'post',
            data: {empid: dataid},
            dataType: 'json',
            beforeSend: function() {
                attachment_explorer_container.html('<h4><i class="fa fa-spinner fa-pulse fa-spin text-info"></i> Fetching attachments...</h4>');
            }
        }).done(function(d) {
            attachment_explorer_container.html(d.html);
        });
    };


    var init_attachedments = function(dataid) {


        $(document).on('click','.kv-file-remove',function (e) {
            e.preventDefault();
            var this_ = $(this);


            var parent = this_.closest('div.file-preview-frame');
            var src = parent.children('.kv-file-content').find('img').attr('src');



            $.SmartMessageBox({
                title: "<i class='fa fa-question fa-fw fa-lg txt-color-yellow'></i> Are you sure you want to delete this file?</span>",
                content: 'Please confirm action taken!',
                buttons: '[No][Yes]'
            }, function (ButtonPressed) {
                if (ButtonPressed === "Yes") {
                    $.ajax({
                        url:PECO.base_url()+'hris/deleteaccomp',
                        type:'post',
                        data:{"src":src , "dataid":dataid},
                        dataType:'json'
                    }).done(function (d) {
                        PECO.initAlerts(d.msg , "PECO.net" , d.func);
                        //  parent.addClass('hidden');
                        parent.remove();
                    }).fail(function () {
                        PECO.phpError();
                    });
                }
            });
        });

        /*   */

    };

    var get_init_accompfile = function(dataid, callback) {
        var safeDataId = dataid;
        if (safeDataId && typeof safeDataId === 'object' && safeDataId.target) {
            safeDataId = $(safeDataId.target).val();
        }
        if (safeDataId === undefined || safeDataId === null) {
            safeDataId = '';
        }
        safeDataId = String(safeDataId);
        $.ajax({
            url:PECO.base_url()+'hris/getuploadedaccomplishments',
            type:'post',
            data:'dataid=' + encodeURIComponent(safeDataId),
            dataType:'json',
            cache: false
        }).done(function (d) {
            if (typeof callback === 'function') {
                callback(d.images);
            }
        }).fail(function () {
            PECO.phpError();
        });
    };

    $('#daterange').html(function () {
        var dateObj = new Date();
        var t2 = new Date(dateObj.getFullYear(), dateObj.getMonth(), 1);
        var t1 = new Date();
        var diff = new Date(t1 - t2);
        var days = parseInt(diff / 1000 / 60 / 60 / 24 + 1);
        return days;
    });

    var fetchmonthlysched = function(userid,month,year){

        $.ajax({
            url:PECO.base_url()+'hris/getemployeecalendar',
            type:'post',
            data:{"empid":userid,"month":month,"year":year},
            dataType:"json"
        }).done(function (d) {
            $('#empcalendar').html(d.html);
        }).fail(function () {
            PECO.phpError();
        });
    };


    var fetchschedule = function(userid){

        $.ajax({
            url:PECO.base_url()+"hris/gettimeshift",
            type:"post",
            data:{"empid" : userid},
            dataType:'json'
        }).done(function (d) {
            scheduletable.dataTable().empty();
            scheduletable.dataTable({
                bDestroy: true,
                bPaginate: false,
                bFilter: false,
                bInfo: false,
                bStateSave: true,
                bProcessing: true,
                aaData: d.timeshiftdata,
                aoColumns: [
                    {"data":"num" , sWidth:"10%"},
                    {"data":"week"},
                    {"data":"amtime"},
                    {"data":"pmtime"},
                    {"data":"status"}
                ],
                searchHighlight: true

            });
        }).fail(function () {
            PECO.phpError();
        });
    };

    /* var getemploymenthistory = function(dataid){

     };
             */


    //for premiums
    var getpremiums = function(dataid, tabid){
        $.ajax({
            url:PECO.base_url()+"hris/getemployeecont",
            type:"post",
            data:{"empid" : dataid,"tabid":tabid},
            dataType:'json'
        }).done(function (d) {

            $(document).find('#totalpaidpremium').text(d.totalpaidamount.toFixed(2));
            $(document).find('#totalunpaidpremium').text(d.totalunpaidamount.toFixed(2));

            tbl_premiums.dataTable().empty();
            tbl_premiums.dataTable({
                bDestroy: true,
                bPaginate: false,
                bFilter: false,
                bInfo: false,
                bStateSave: true,
                bProcessing: true,
                aaData: d.employeecontdata,
                aoColumns: [
                    {"data":"expand", sClass:'expand',sWidth:'5%'},
                    {"data":"amount",sClass:'text-danger number'},
                    {"data":"for"},
                    {"data":"amtpermonth",sClass:'text-danger number'},
                    {"data":"datecreated"},
                    {"data":"control"}
                ],
                searchHighlight: true,
                fnRowCallback: function(nRow, data, iDisplayIndex) {
                    PECO.dtExpandBtn(nRow, data.expand);
                },
                language: PECO.DTEmptyMessage('No record yet!')
            });
        }).fail(function () {
            PECO.phpError();
        });
    };



    var getloans = function(dataid, tabid){

        $.ajax({
            url:PECO.base_url()+"hris/getloans",
            type:"post",
            data:{"empid" : dataid,"tabid":tabid},
            dataType:'json'
        }).done(function (d) {

            $(document).find('#totalpaidloans').text(d.totalpaidamount.toFixed(2));
            $(document).find('#totalunpaidloans').text(d.totalunpaidamount.toFixed(2));

            tbl_loans.dataTable().empty();
            tbl_loans.dataTable({
                bDestroy: true,
                bPaginate: false,
                bFilter: false,
                bInfo: false,
                bStateSave: true,
                bProcessing: true,
                aaData: d.emploansdata,
                aoColumns: [
                    {"data":"expand", sClass:'expand',sWidth:'5%'},
                    {"data":"amount",sClass:'number text-danger'},
                    {"data":"for"},
                    {"data":"amtpermonth",sClass:'number text-danger'},
                    {"data":"loantype",sClass:'number text-danger'},
                    {"data":"datecreated"},
                    {"data":"control",sWidth:'5px'}
                ],
                searchHighlight: true,
                fnRowCallback: function(nRow, data, iDisplayIndex) {
                    PECO.dtExpandBtn(nRow, data.expand);
                },
                language: PECO.DTEmptyMessage('No record yet!')
            });
        }).fail(function () {
            PECO.phpError();
        });
    };

    //for deductions
    var getdeductions = function(dataid){
        $.ajax({
            url:PECO.base_url()+"hris/getdeductions",
            type:"post",
            data:{"dataid":dataid},
            dataType:'json'
        }).done(function (d) {

            $(document).find('#totalpaiddeductions').text(d.totalpaidamount.toFixed(2));
            $(document).find('#totalunpaiddeductions').text(d.totalunpaidamount.toFixed(2));

            tbl_deductions.dataTable().empty();
            tbl_deductions.dataTable({
                bDestroy: true,
                bPaginate: false,
                bFilter: false,
                bInfo: false,
                bStateSave: true,
                bProcessing: true,
                aaData: d.deductionsdata,
                aoColumns: [
                    {"data":"expand", sClass:'expand'},
                    {"data":"type"},
                    {"data":"amount"},
                    {"data":"for"},
                    {"data":"permonth"},
                    {"data":"control",sWidth:'5px'}
                ],
                searchHighlight: true,
                fnRowCallback: function(nRow, data, iDisplayIndex) {
                    PECO.dtExpandBtn(nRow, data.expand);
                },
                language: PECO.DTEmptyMessage('No record yet!')
            });
        }).fail(function () {
            PECO.phpError();
        });
    };

    var fetchdependents = function(dataid){
          $.ajax({
              url:PECO.base_url()+'hris/fetchdependents',
              type:'post',
              data:{"userid":dataid},
              dataType:'json'
          }).done(function (d) {
              empdependentstable.dataTable().empty();
              empdependentstable.dataTable({
                  bDestroy: true,
                  bPaginate: false,
                  bFilter: false,
                  bInfo: false,
                  bStateSave: true,
                  bProcessing: true,
                  aaData: d.dependentsdata,
                  aoColumns: [
                      {"data":"num"},
                      {"data":"name"},
                      {"data":"birthdate"},
                      {"data":"relation"}
                  ],
                  searchHighlight: true,
                  language: PECO.DTEmptyMessage('No record yet!')
              });
          }).fail(function () {
              PECO.phpError();
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
            //   win.print(); // blocking - so close will not
            //   win.close(); // execute until this is done
        }, 250);

    };

    var fetchfixamttable  = function (dataid){

        $.ajax({
            url:PECO.base_url()+'payroll/getemppayrollfixamt',
            type:'post',
            data:{"empid" : dataid},
            dataType:'json'
        }).done(function (data) {
            payrollfixamttable.dataTable().empty();
            payrollfixamttable.dataTable({
                bDestroy: true,
                bPaginate: true,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                bProcessing: true,
                aaData: data.payrollfixamtdata,
                aoColumns: [
                    {"data":"num"},
                    {"data":"types"},
                    {"data":"amt"},
                    {"data":"datecreated"},
                    {"data":"control"}
                ],
                searchHighlight: true
            });
        }).fail(function () {
            PECO.phpError();
        });

    };

    var dt_employeelogs = function (dataid) {
        $.ajax({
            url: PECO.base_url()+'hris/getemployeelogs',
            type: 'post',
            data: {'empid':dataid},
            dataType: 'json',
        }).done(function (data) {
            dt_logs.dataTable().empty();
            dt_logs.dataTable({
                bDestroy: true,
                bPaginate: true,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                bProcessing: true,
                aaData: data.listlogs,
                aoColumns: [
                    {"data": "num"},
                    {"data": "datecreated",sWidth: '140px'},
                    {"data": "specificdate"},
                    {"data": "remarks"},
                    {"data": "status"},
                    {"data": "updated"},
                    {"data": "controls",sWidth: '10px'}
                ],
                searchHighlight: true
            })
        })
    };

    var initHR_view = function (modulehash, dataid) {

        fetchdependents(dataid);
        var d = new Date();
        var nowmonth = d.getMonth() + 1;
        var year = d.getFullYear();

        var empid = $('#select2empstatus',document).attr('data-id');
        PECO.select2Basic($('#relationdependents', document),'hris/getrelations','Select Relation', false,false,false);
        PECO.select2Basic($('#schedmonth', document),'systems/select2month','Select Month', false,false,nowmonth);
        PECO.select2Basic($('#monthselect2', document),'systems/select2month','Select Month', false,false,nowmonth);
        PECO.select2Basic($('#yearselect2', document),'hris/select2year','Select Year', false,false,year);
        PECO.select2Basic($('#select2empstatus', document),'hris/select2empstatus','Select Status..', true,false,false, false, true,empid);

        $('#typeselect2').select2({
            allowClear:true
        });

        $('#input_specificdate',document).datepicker({
            format: 'yyyy-mm-dd'
        });

        $('li a[href="#loans"]').click(function () {
            $('html, body').animate({scrollTop : 0},800);
        });

        $(document).on('click' , '#deletefixamt' , function () {
              var this_ = $(this);
              var datasysid = this_.attr("data-id");

            swal({
                title: "Are you sure?",
                text: "Transaction will be deleted to this employee.",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes, Delete!",
                closeOnConfirm: false,
                closeOnCancel: false,
                showLoaderOnConfirm: true
            }, function(isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url:PECO.base_url()+"hris/deletefixamt",
                        type:'post',
                        data:{"dataid" :datasysid},
                        dataType:'json'
                    }).done(function (data) {
                        swal("PECO" , data.msg , data.func);
                        if(data.qry == true){
                            fetchfixamttable(dataid);
                        }
                    }).fail(function () {
                        PECO.phpError();
                    });
                }else{
                    swal.close();
                }
            });


        });

        $(document).on('submit','#submitpayrollfixamt' , function (e) {
            e.preventDefault();
            var this_ = $(this);

            swal({
                title: "Are you sure?",
                text: "Amount will be addded to this employee.",
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
                        url:this_.attr("action"),
                        type:this_.attr("method"),
                        data:this_.serialize(),
                        dataType:'json'
                    }).done(function (data) {
                        swal.close();
                        PECO.initAlerts(data.msg , "PECO" , data.func);
                        if(data.qry == true){
                            $('#submitpayrollfixamt')[0].reset();
                            $('#types' , document).select2('val' , '');
                            fetchfixamttable(data.empid);
                        }

                    }).fail(function () {
                        PECO.phpError();
                    });

                } else {
                    swal("Cancelled", "Processing canceled", "error");
                }
            });


        });

        $(document).on('submit','#submitpayslip',function (e) {
            e.preventDefault();
            var this_ = $(this);
           $.ajax({
               url:this_.attr("action"),
               type:this_.attr("method"),
               data:this_.serialize(),
               dataType:'json'
           }).done(function (d) {
               var html = '';
               var i = 0;
               var numbering = 1;
               for(i = 0;i < d.count - 1; i++){


                   html+= '<div class="row">';
                   html+= '<div class="col-md-9 col-sm-9 col-xs-9">';
                   html+= '<div class="form-group">';
                   html+= '<h6  style="font-size: 9px!important;">'+numbering+' PANAY ELECTRIC COMPANY, INC.</h6>';
                   html+= '</div>';
                   html+= '</div>';

                   html+= '<div class="col-md-3 col-sm-3 col-xs-3">';
                   if(payclass == 128){
                       html+= '<h6  style="font-size: 9px!important;">PAYSLIP '+d.month+' '+d.period+', '+d.year+'</h6>';
                   }else{
                       html+= '<h6  style="font-size: 9px!important;">PAYSLIP FOR THE MONTH '+d.month+' '+d.year+'</h6>';
                   }

                   html+= '</div>';
                   html+= '</div>';

                   html+= '<div class="row">';

                   html+= '<div class="col-md-2 col-sm-2 col-xs-2">';
                   html += '<label  style="font-size: 9px!important;">NAME</label>';
                   html+= '</div>';

                   html+= '<div class="col-md-2 col-sm-2 col-xs-2">';
                   html += '<label   style="font-size: 9px!important;" class="bold">'+d.payrollreportdata[i].name+'</label>';
                   html+= '</div>';

                   html+= '<div class="col-md-2 col-sm-2 col-xs-2">';
                   html += '<label   style="font-size: 9px!important;">EMPLOYEE#</label>';
                   html+= '</div>';

                   html+= '<div class="col-md-2 col-sm-2 col-xs-2">';
                   html += '<label   style="font-size: 9px!important;" class="bold">N/A</label>';
                   html+= '</div>';

                   html+= '<div class="col-md-2 col-sm-2 col-xs-2">';
                   html += '<label   style="font-size: 9px!important;">DEPT CODE</label>';
                   html+= '</div>';

                   html+= '<div class="col-md-2 col-sm-2 col-xs-2">';
                   html += '<label   style="font-size: 9px!important;" class="bold">'+d.payrollreportdata[i].department+'</label>';
                   html+= '</div>';

                   html+= '</div>';

                   html+= '<div class="row">';

                   html += '<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">';
                   html += '<h6 style="font-size: 9px!important;">EARNINGS</h6>';
                   html += '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';

                   html += '<span class="col-md-8 col-sm-8 col-xs-8"  style="font-size: 8px!important;">Basic</span>';
                   html += '<span class="col-md-4 col-sm-4 col-xs-4" style="text-align: right;font-size: 8px!important;">'+d.payrollreportdata[i].basic+'</span>';

                   html += '<span class="col-md-8 col-sm-8 col-xs-8" style="font-size: 8px!important;">COLA</span>';
                   html += '<span class="col-md-4 col-sm-4 col-xs-4" style="text-align: right;font-size: 8px!important;">'+d.payrollreportdata[i].cola+'</span>';

                   html += '<span class="col-md-8 col-sm-8 col-xs-8" style="font-size: 8px!important;">Others</span>';
                   html += '<span class="col-md-4 col-sm-4 col-xs-4" style="text-align: right;font-size: 8px!important;">'+d.payrollreportdata[i].others+'</span>';

                   html += '<span class="col-md-8 col-sm-8 col-xs-8" style="font-size: 8px!important;">Holiday Pay</span>';
                   html += '<span class="col-md-4 col-sm-4 col-xs-4" style="text-align: right;font-size: 8px!important;">'+d.payrollreportdata[i].holidays+'</span>';

                   html += '<span class="col-md-8 col-sm-8 col-xs-8" style="font-size: 8px!important;">Overtime Pay</span>';
                   html += '<span class="col-md-4 col-sm-4 col-xs-4" style="text-align: right;font-size: 8px!important;">'+d.payrollreportdata[i].otpay+'</span>';

                   html += '<span class="col-md-8 col-sm-8 col-xs-8" style="font-size: 8px!important;">Night Differential</span>';
                   html += '<span class="col-md-4 col-sm-4 col-xs-4" style="text-align: right;font-size: 8px!important;">'+d.payrollreportdata[i].nitediff+'</span>';

                   html += '<span class="col-md-8 col-sm-8 col-xs-8" style="font-size: 8px!important;">Adjustments</span>';
                   html += '<span class="col-md-4 col-sm-4 col-xs-4" style="text-align: right;font-size: 8px!important;">'+d.payrollreportdata[i].adjustments+'</span>';

                   html += '</div>';

                   html+= '</div>';

                   //deductions

                   html += '<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">';
                   html += '<h6 style="font-size: 9px!important;">DEDUCTIONS</h6>';
                   html += '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';

                   html += '<span class="col-md-6 col-sm-6 col-xs-6" style="font-size: 8px!important;">SSS</span>';
                   html += '<span class="col-md-3 col-sm-3 col-xs-3" style="text-align: right;font-size: 8px!important;">'+d.payrollreportdata[i].ssscont+'</span>';
                   html += '<span class="col-md-3 col-sm-3 col-xs-3" style="text-align: right;font-size: 8px!important;">'+d.payrollreportdata[i].sssloan+'</span>';

                   html += '<span class="col-md-6 col-sm-6 col-xs-6" style="font-size: 8px!important;">PAG-IBIG</span>';
                   html += '<span class="col-md-3 col-sm-3 col-xs-3" style="text-align: right;font-size: 8px!important;">'+d.payrollreportdata[i].hdmfcont+'</span>';
                   html += '<span class="col-md-3 col-sm-3 col-xs-3" style="text-align: right;font-size: 8px!important;">'+d.payrollreportdata[i].hdmfloan+'</span>';

                   html += '<span class="col-md-6 col-sm-6 col-xs-6" style="font-size: 8px!important;">PECEWA</span>';
                   html += '<span class="col-md-3 col-sm-3 col-xs-3" style="text-align: right;font-size: 8px!important;">'+d.payrollreportdata[i].agencyfee+'</span>';
                   html += '<span class="col-md-3 col-sm-3 col-xs-3" style="text-align: right;font-size: 8px!important;">'+d.payrollreportdata[i].pecewa+'</span>';
                   html += '<span class="col-md-6 col-sm-6 col-xs-6" style="font-size: 8px!important;">Cooperative</span>';
                   html += '<span class="col-md-3 col-sm-3 col-xs-3" style="text-align: right;font-size: 8px!important;">0.00</span>';
                   html += '<span class="col-md-3 col-sm-3 col-xs-3" style="text-align: right;font-size: 8px!important;">'+d.payrollreportdata[i].corporate+'</span>';


                   html += '<span class="col-md-6 col-sm-6 col-xs-6" style="font-size: 8px!important;">HMO Deduction</span>';
                   html += '<span class="col-md-3 col-sm-3 col-xs-3" style="text-align: right;font-size: 8px!important;">'+d.payrollreportdata[i].hmodedn+'</span>';

                   html += '<span class="col-md-6 col-sm-6 col-xs-6" style="font-size: 8px!important;">Electric Bills</span>';
                   html += '<span class="col-md-3 col-sm-3 col-xs-3" style="text-align: right;font-size: 8px!important;">'+d.payrollreportdata[i].electbills+'</span>';

                   html += '<span class="col-md-6 col-sm-6 col-xs-6" style="font-size: 8px!important;">Others</span>';
                   html += '<span class="col-md-3 col-sm-3 col-xs-3" style="text-align: right;font-size: 8px!important;">'+d.payrollreportdata[i].othersdedn+'</span>';

                   html += '<span class="col-md-6 col-sm-6 col-xs-6" style="font-size: 8px!important;">Leave Without Pay</span>';
                   html += '<span class="col-md-3 col-sm-3 col-xs-3" style="text-align: right;font-size: 8px!important;">'+d.payrollreportdata[i].leavewithoutpay+'</span>';

                   html += '<span class="col-md-6 col-sm-6 col-xs-6" style="font-size: 8px!important;">Withholding Tax</span>';
                   html += '<span class="col-md-3 col-sm-3 col-xs-3" style="text-align: right;font-size: 8px!important;">'+d.payrollreportdata[i].withholdingtax+'</span>';

                   html += '</div>';

                   html+= '</div>';

                   html+= '<div class="col-md-4 col-sm-4 col-xs-4">';
                   html += '<h6 style="font-size: 9px!important;">SUMMARY</h6>';
                   html+= '<div class="row">';
                   html+= '<div class="col-md-8 col-sm-8 col-xs-8" style="font-size: 9px!important;">TOTAL EARNINGS</div>';
                   html+= '<div class="col-md-4 col-sm-4 col-xs-4" style="font-size: 9px!important;">'+d.payrollreportdata[i].earnings+'</div>';
                   html+= '<div class="col-md-8 col-sm-8 col-xs-8" style="font-size: 9px!important;">TOTAL DEDUCTIONS</div>';
                   html+= '<div class="col-md-4 col-sm-4 col-xs-4" style="font-size: 9px!important;">'+d.payrollreportdata[i].deductions+'</div>';
                   html+= '<div class="col-md-8 col-sm-8 col-xs-8" style="font-size: 9px!important;">NET PAY</div>';
                   html+= '<div class="col-md-4 col-sm-4 col-xs-4 bold" style="font-size: 9px!important;">'+d.payrollreportdata[i].netpay+'</div>';
                   if(payclass == 1){

                       var totalnetpay = parseFloat(d.payrollreportdata[i].netpay.replace(/,/g, ''));
                       var nethalf = totalnetpay / 2;

                       html+= '<div class="col-md-8 col-sm-8 col-xs-8" style="font-size: 9px!important;">NET 15</div>';
                       html+= '<div class="col-md-4 col-sm-4 col-xs-4" style="font-size: 9px!important;">'+nethalf.toLocaleString(undefined, {
                           minimumFractionDigits: 2,
                           maximumFractionDigits: 2
                       })+'</div>';
                       html+= '<div class="col-md-8 col-sm-8 col-xs-8" style="font-size: 9px!important;">NET 30</div>';
                       html+= '<div class="col-md-4 col-sm-4 col-xs-4" style="font-size: 9px!important;">'+nethalf.toLocaleString(undefined, {
                           minimumFractionDigits: 2,
                           maximumFractionDigits: 2
                       })+'</div>';
                       totalnetpay = 0;
                       nethalf = 0;

                   }

                   html+= '</div>';
                   html+= '</div>';

                   html+= '</div>';//end row
                   html+= '<div style="border-bottom: 1px solid gray !important;"></div>';//end row
                   numbering++;
               }
               pecoRepPrint("Payroll Payslip" , html);
           }).fail(function () {
               PECO.phpError();
           });

        });


        $(document).on('submit','#submitempdependents',function (e) {
            e.preventDefault();
            var this_ = $(this);
            $.ajax({
                url:this_.attr("action"),
                type:this_.attr("method"),
                data:this_.serialize(),
                dataType:'json'
            }).done(function (d) {
                PECO.initAlerts(d.msg,"PECO.net",d.func);
                if(d.qry == true){
                    $('#submitempdependents')[0].reset();
                    $('#relationdependents').select2('val','');
                    fetchdependents(dataid);
                }

            }).fail(function () {
                PECO.phpError();
            });
        });
        $(document).on('click','#deletedeductionbtn',function () {
            var this_ = $(this);
            var this_empid = this_.attr("data-empid");
            var this_ded_id = this_.attr("data-id");

            swal({
                title: "Are you sure?",
                text: "Deduction will be deleted",
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
                        url:PECO.base_url()+'hris/deletededuction',
                        type:'post',
                        data:{"empid":this_empid , "deductionid":this_ded_id},
                        dataType:'json'
                    }).done(function (d) {
                        swal("Delete", d.msg, d.func);
                        if(d.qry == true){
                            getdeductions(this_empid);
                        }
                    }).fail(function () {
                        PECO.phpError();
                    });
                } else {
                    swal("Cancelled", "Processing canceled", "error");
                }
            });
        });

        $(document).on('click','#deletecontributionbtn',function () {
            var this_ = $(this);
            var this_empid = this_.attr("data-empid");
            var this_premium_id = this_.attr("data-id");
            var ref_this = $("#tab_premiums li.active a");
            var tabid = ref_this.attr("data-id");

            swal({
                title: "Are you sure?",
                text: "Premiums will be deleted",
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
                        url:PECO.base_url()+'hris/deletepremiums',
                        type:'post',
                        data:{"empid":this_empid,"premiumid":this_premium_id},
                        dataType:'json'
                    }).done(function (d) {
                        swal("Delete", d.msg, d.func);
                        if(d.qry == true){
                            getpremiums(this_empid, tabid);
                        }
                    }).fail(function () {
                        PECO.phpError();
                    });
                } else {
                    swal("Cancelled", "Processing canceled", "error");
                }
            });

        });
        $(document).on('click','#deleteloans',function () {
            var this_ = $(this);
            var this_empid = this_.attr("data-empid");
            var this_loan_id = this_.attr("data-id");
            var ref_this = $("#tab_loans li.active a");
            var tabid = ref_this.attr("data-id");

            swal({
                title: "Are you sure?",
                text: "Loans will be deleted",
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
                        url:PECO.base_url()+'hris/deleteloans',
                        type:'post',
                        data:{"empid":this_empid,"loanid":this_loan_id},
                        dataType:'json'
                    }).done(function (d) {
                        swal("Delete", d.msg, d.func);
                        if(d.qry == true){
                            getloans(this_empid, tabid)
                        }
                    }).fail(function () {
                        PECO.phpError();
                    });
                } else {
                    swal("Cancelled", "Processing canceled", "error");
                }
            });
        });

        $(document).on('click','#exportbtn',function () {
            $.ajax({
                url:PECO.base_url()+'hris/export201file',
                type:'post',
                data:{"empid":dataid},
                dataType:'json'
            }).done(function (d) {
              PECO.pecoRepPrint("Employee 201 File" , d.html);
            }).fail(function () {
                PECO.phpError();
            });
        });

        $(document).on('click','#sendbtn',function (e) {
            PECO.initAlerts("This feature is under contruction",  "PECO.net" , "info");
        });

        init_send_payslip_form();


        var inputarrs = {dataid: dataid};
        PECO.dtSubDetails(tbl_premiums , 'hris/getempbreakdowns' , inputarrs , 'sub-table');
        PECO.dtSubDetails(tbl_deductions , 'hris/getempbreakdowns' , inputarrs, 'sub-table');
        PECO.dtSubDetails(tbl_loans , 'hris/getempbreakdowns' , inputarrs, 'sub-table');
        PECO.select2Basic($('#teamassign',document), 'ts/gettsteamno' ,'Please select team');
        PECO.select2Basic($('#branch',document), 'hris/getbranches' ,'Please select branch');

        //  getemploymenthistory(dataid);
        getdeductions(dataid);
        getloans(dataid , 257);



        $(document).on('submit','#submitdeductions',function (e) {
            e.preventDefault();
            var this_ = $(this);
            $.ajax({
                url:this_.attr("action"),
                method:this_.attr("method"),
                data:this_.serialize(),
                dataType:'json'
            }).done(function (d) {
                PECO.initAlerts(d.msg , "PECO.net" , d.func);
                $('#submitdeductions')[0].reset();
                $('#deducttype').select2('val','');
                $('#monthded').select2('val','');
                $('#yearded').select2('val','');
                $('#dedpaytype').select2('val','');
                $('#modal_ajax').modal('hide');
                getdeductions(dataid);
            }).fail(function () {
                PECO.phpError();
            });
        });

        $(document).on('submit','#submitadditionalloans',function (e) {
            e.preventDefault();
            var this_ = $(this);
            $.ajax({
                url:this_.attr("action"),
                method:this_.attr("method"),
                data:this_.serialize(),
                dataType:'json'
            }).done(function (d) {
                PECO.initAlerts(d.msg , "PECO.net" , d.func);
                $('#submitadditionalloans')[0].reset();
                $('#loanspaytype').select2('val','');
                $('#yearloans').select2('val','');
                $('#monthloans').select2('val','');
                getloans(dataid ,d.tabid);

                $('#modal_ajax').modal('hide');
            }).fail(function () {
                PECO.phpError();
            });
        });

        $(document).on('submit','#submitpremiums',function (e) {
            e.preventDefault();
            var this_ = $(this);
            $.ajax({
                url:this_.attr("action"),
                method:this_.attr("method"),
                data:this_.serialize(),
                dataType:'json'
            }).done(function (d) {
                PECO.initAlerts(d.msg , "PECO.net" , d.func);
                $('#submitpremiums')[0].reset();
                $('#prempaytype').select2('val','');
                $('#yearprem').select2('val','');
                $('#monthprem').select2('val','');
                getpremiums(dataid ,d.tabid);

                $('#modal_ajax').modal('hide');
            }).fail(function () {
                PECO.phpError();
            });
        });

        $(document).on('click','#tab_premiums a',function (e) {
            e.preventDefault();
            var this_ = $(this);
            var tabid = this_.attr("data-id");
            $(document).find('#conttitle').text(this_.text());
            $('#btn_add_premium').attr('data-view', tabid);
            $('#btn_add_premium').text("Add "+this_.text());
            getpremiums(dataid, tabid);
            return false;
        });

        $(document).on('click','#tab_loans a',function (e) {
            e.preventDefault();
            var this_ = $(this);
            var tabid = this_.attr("data-id");


            $(document).find('#loanstitle').text(this_.text());
            $('#btn_add_loans').attr('data-view', tabid);
            $('#btn_add_loans').text("Add "+this_.text());
            getloans(dataid, tabid);
            return false;
        });


        $(document).on('submit','#submitemploymenthistory',function (e) {
            e.preventDefault();
            var this_ = $(this);
            $.ajax({
                url:this_.attr("action"),
                type:this_.attr("method"),
                data:this_.serialize(),
                dataType:'json'
            }).done(function (d) {
                PECO.initAlerts(d.msg,"PECO.net",d.func);
                if(d.qry == true){
                    $('#submitemploymenthistory')[0].reset();
                    getemploymenthistory(dataid);
                }
            }).fail(function () {
                PECO.phpError();
            });
        });

        /*
                   */
        $(".timepicker-default").each(function(){
            $(this).timepicker({
                autoclose: !0,
                showSeconds: !0,
                minuteStep: 1
            });
        });

        $(document).on('click','#backbtn',function (e) {
            window.location.href = base_url+"module/f1f836cb4ea6efb2a0b1b99f41ad8b103eff4b59/list";
        });

        $(document).on('click','#printtimelogs',function (e) {
            res_summary();
            var d1 = $('div.date-picker-start').datepicker('getDate');
            var d2 = $('div.date-picker-end').datepicker('getDate');
            var d1 = $.datepicker.formatDate('dd-mm-yy', d1);
            var d2 = $.datepicker.formatDate('dd-mm-yy', d2);
            e.preventDefault();
            $.ajax({
                url: base_url + 'hris/gettimelogs',
                type: "post",
                data: {'empid': dataid, 'datestart': d1, 'dateend': d2},
                dataType:"json"
            }).done(function (d) {
                var loggeduser = $(document).find('#loggeduser').val();
                var count = d.iTotalDisplayRecords;
                var index =0;
                var html = '';
                html += '<div class="row">';
                html += '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';
                html += '<h3>'+loggeduser+'</h3>';

                html += '<table class="table table-responsive table-bordered table condensed tbl-xs">';
                html += '<thead>';

                html +=  '<tr>';
                html += '<th rowspan="2" valign="middle"><center>';
                html +=    'LOG DATE';
                html += '</center></th>';
                html +='<th colspan="3">AM</th>';
                html +=    '<th colspan="3">PM</th>';
                html +=    '<th colspan="2">OT</th>';
                html +=    '<th colspan="2">LOCATOR</th>';
                html +=    '<th rowspan="2"><center>';
                html +=    'STATUS';
                html +=    '</center></th>';
                html +='<th rowspan="2"><center>';
                html +=    'LATE / UT';
                html +=   '</center></th>';
                html +='</tr>';
                html +='<th>IN</th>';
                html +='<th>OUT</th>';
                html +='<th>Late</th>';
                html +='<th>IN</th>';
                html +='<th>OUT</th>';
                html += '<th>Late</th>';
                html +='<th>IN</th>';
                html += '<th>OUT</th>';
                html +='<th>IN</th>';
                html += '<th>OUT</th>';
                html +='</tr>';
                html += '</thead>';

                html += '<tbody>';

                for(index=0;index<count;index++){
                    html +='<tr>';
                    html +='<td>'+d.aaData[index].day+'</td>';
                    html +='<td>'+d.aaData[index].amin+'</td>';
                    html +='<td>'+d.aaData[index].amout+'</td>';
                    html +='<td>'+d.aaData[index].amlate+'</td>';
                    html +='<td>'+d.aaData[index].pmin+'</td>';
                    html +='<td>'+d.aaData[index].pmout+'</td>';
                    html +='<td>'+d.aaData[index].pmlate+'</td>';

                    html +='<td></td>';
                    html +='<td></td>';
                    html +='<td></td>';
                    html +='<td></td>';

                    html +='<td>'+d.aaData[index].stat+'</td>';
                    html +='<td>'+d.aaData[index].totallate+'</td>';

                    html +='</tr>';
                }
                html += '</tbody>';
                html += '</table>';

                html += '</div>';
                html += '</div>';
                PECO.pecoRepPrint("Time Logs Report" , html);
            });
        });

        $(document).on('click','#printbtn',function (e) {
            e.preventDefault();
            var dataid = $(document).find('#dataid').val();
            $.ajax({
                url:base_url+"hris/printempdetails",
                type:"post",
                data:{"dataid":dataid},
                dataType:"json"
            }).done(function (d) {
                PECO.pecoRepPrint("201 File" , d.html);
            }).fail(function () {
                PECO.phpError();
            });
        });

        $('body').find(".draggable-modal").draggable({
            handle: ".modal-header"
        });

        $('.date-picker-start').datepicker({
            orientation: "left",
            autoclose: true,
            format: 'yyyy-mm-dd',
            setDate: '2016-08-01'
        });

        $('.date-picker-end').datepicker({
            orientation: "left",
            autoclose: true,
            format: 'yyyy-mm-dd',
            setDate: new Date()
        });

        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {

            var target = $(e.target).attr("href");
            if (target == '#attendance') {
                res_summary();
                var d1 = $('div.date-picker-start').datepicker('getDate');
                var d2 = $('div.date-picker-end').datepicker('getDate');
                var d1 = $.datepicker.formatDate('dd-mm-yy', d1);
                var d2 = $.datepicker.formatDate('dd-mm-yy', d2);
                setTimeout(function () {
                    draw_dtr(d1, d2, dataid);
                }, 100);
                init_employee_leave_credits($('#list_leave_credits'), dataid);
                $(".page-content").animate({ scrollTop: 0 }, "slow");
                return false;
            }
            if (target == '#dtr') {
                res_summary();
                var d1 = $('div.date-picker-start').datepicker('getDate');
                var d2 = $('div.date-picker-end').datepicker('getDate');
                var d1 = $.datepicker.formatDate('dd-mm-yy', d1);
                var d2 = $.datepicker.formatDate('dd-mm-yy', d2);
                draw_dtr(d1, d2, dataid );
            }


            if (target == '#leavehistory') {
                init_employee_leave_credits($('#list_leave_credits'), dataid);
                init_employee_leave_history(dataid);
            }

            if (target == '#premiums') {
                getpremiums(dataid, 72);
                $(".page-content").animate({ scrollTop: 0 }, "slow");
                return false;
            }
            if (target == '#otherstab') {
                PECO.select2Basic($('#fixtype' , document) , 'payroll/getfixtypesid' , 'Select Type' , false,false,false );
                fetchfixamttable(dataid);
                $(".page-content").animate({ scrollTop: 0 }, "slow");
                return false;
            }
            if (target == '#loans') {
                $(".page-content").animate({ scrollTop: 0 }, "slow");
                return false;
            }
            if (target == '#deductions') {
                $(".page-content").animate({ scrollTop: 0 }, "slow");
                return false;
            }
            if (target == '#scheduletab') {
                $(".page-content").animate({ scrollTop: 0 }, "slow");
                return false;
            }
            if (target == '#logs') {
                dt_employeelogs(dataid);
            }
        });

        $('#btn-dtr-generate').click(function (e) {
            var this_= $(this);
            var data_id = this_.attr('data-id');
            e.preventDefault();
            var d1 = $('div.date-picker-start').datepicker('getDate');
            var d2 = $('div.date-picker-end').datepicker('getDate');
            var d1 = $.datepicker.formatDate('dd-mm-yy', d1);
            var d2 = $.datepicker.formatDate('dd-mm-yy', d2);
            draw_dtr(d1, d2,data_id );
        });

        fetchschedule(dataid);
        fetchmonthlysched(dataid,$('#schedmonth').val() , $('#schedyear').val());

        $(document).on('click','#btn_filter_monthly_sched',function (e) {
            e.preventDefault();
            fetchmonthlysched(dataid, $('#schedmonth').val() , $('#schedyear').val());
        });

        $(document).on('click', '.tab-pane#monthly #empcalendar table td.calendar-day', function(e) {
            e.preventDefault();
            var this_ = $(this);
            var this_day = this_.$('.day-number').text();
            var this_month = $('#schedmonth').val();
            var this_year = $('#schedyear').val();

            // show popover
            // send data day, mont, year post get query "getemployeedayscheduledetails"
            // Leave/Locator/Absent
            // Birthday
        });

        $(document).on('submit','#submitreqsched',function (e) {
            e.preventDefault();
            var this_ = $(this);
            $.ajax({
                url:this_.attr("action"),
                type:this_.attr("method"),
                data:this_.serialize(),
                dataType:'json'
            }).done(function (d) {
                PECO.initAlerts(d.msg , "PECO.net" , d.func);
                $('#submitreqsched')[0].reset();
                $('#branch').select2('val','');
                $('#teamassign').select2('val','');
            }).fail(function () {
                PECO.phpError();
            });
        });

        $(document).on('submit','#addemployeelog',function (e) {
            e.preventDefault();
            var this_ = $(this);
            $.ajax({
                url: this_.attr('action'),
                type: this_.attr('method'),
                dataType: 'json',
                data: this_.serialize(),
            }).done(function (d) {
                PECO.initAlerts(d.msg,d.title,d.func);
                dt_employeelogs(dataid);
            }).fail(function () {
               PECO.phpError();
            })
        });

        $(document).on('click','#btn_delete_emplogs',function (e) {
            e.preventDefault();
            var this_ = $(this);
            var logs_id = this_.attr('data-id');
            $.ajax({
                url: this_.attr('href'),
                type: 'post',
                data: {'emplogid': logs_id},
                dataType: 'json'
            }).done(function (d) {
                dt_employeelogs(dataid);
            }).fail(function () {
                PECO.phpError();
            })
        })
    };

    var init_employee_leave_history = function(dataid) {
        $('#tbl_leave_history').dataTable();
        $.ajax({
            url:PECO.base_url()+"hris/fetchleavecredits",
            type:"post",
            dataType:"json",
            data: {'dataid': dataid},
            beforeSend: function(){
                $('#tbl_leave_history').dataTable().empty();
                PECO.DTphpLoading($('#tbl_leave_history'), 'Loading... ');
            }
        }).done(function (d) {
            $('#tbl_leave_history').dataTable().empty();
            $('#tbl_leave_history').dataTable({
                bDestroy: true,
                bPaginate: true,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                bProcessing: true,
                aaData: d.leavecreditsprofile,
                aoColumns: [
                    {"data":"num"},
                    {"data":"dateapplication"},
                    {"data":"fromdate"},
                    {"data":"todate"},
                    {"data":"fromtime"},
                    {"data":"totime"},
                    {"data":"total"},
                    {"data":"status"}
                ],
                searchHighlight: true
            });
        }).fail(function () {
            PECO.phpError();
        });
    };

    var init_employee_leave_credits = function(el, empid, year) {
        var year = (year) ? year : false;
        $.ajax({
            url: PECO.base_url() + 'hris/getemployeeleavecredits',
            type: 'post',
            dataType: 'json',
            data: {'empid': empid, 'year': year}
        }).done(function(d){
            el.html(d.html);
            $('.popovers').each(function(){
                $(this).popover({
                    html: true,
                    animation: true,
                    template: '<div class="popover popover-info"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>'
                });
            });
        }).fail(function(){
            PECO.phpError();
        });

    };


    var pecoRepPrint =  function (reptitle, content) {
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
            '<style>body{margin: 0px 0px !important; margin-top: 100px; font-family: arial; background: #fff;}</style>' +
            '</head>' +
            '<img  style="display: inline-block; height: 80px; float: left; z-index: 2 !important; position: absolute; left: 0px;" src="' + PECO.base_url() + 'assets/global/img/PECO_LEFT_HEAD.png" />' +
            '<h4 style="position: absolute; top: 50px; right: 0px; width: auto; text-align: right; padding-right: 10px">' + reptitle + '</h4>' +
            '<div style="position: absolute; top: 90px; left: 0px; width: 100%;">' + content + '</div>';
        setTimeout(function () {
            //  win.print(); // blocking - so close will not
            //  win.close(); // execute until this is done
        }, 250);
    };

    var init_employee_list = function(modulehash) {

        PECO.select2Basic($('#departmentview' , document) , 'hris/select2department' , 'Department' ,false,false,false);
        PECO.select2Basic($('#empjobcat' , document) , 'hris/select2jobcat' , 'Job Category' ,false,false,false);
        PECO.select2Basic($('#emppayclass' , document) , 'hris/select2payclass' , 'Payclass' ,false,false,false);

        var statusemp = 1;
        init_employee_tbl(1, $('#emptable'), modulehash);
        
        $(document).on('change','#empcontrolfilter' , function () {
            var empjobcat = $(document).find('#empjobcat').val();
            var emppayclass = $(document).find('#emppayclass').val();
            var departmentview = $(document).find('#departmentview').val();
            var stat = $('.emp-stat-btn').find('li.active').data("stat");
            init_employee_tbl(stat, $('#emptable',document), modulehash , departmentview , false , emppayclass , empjobcat);

        });

        $(document).on('click','#reportempsbtn' , function () {
            var empjobcat = $(document).find('#empjobcat').val();
            var emppayclass = $(document).find('#emppayclass').val();
            var departmentview = $(document).find('#departmentview').val();
            var stat = $('.emp-stat-btn').find('li.active').data("stat");
            init_employee_tbl(stat, $('#emptable',document), modulehash , departmentview , false , emppayclass , empjobcat , 1);
        });
        
        $('body').on('click', '.emp-stat-btn li', function(e) {
            e.preventDefault();
            var this_ = $(this);
            var data_stat = this_.attr('data-stat');

            var empjobcat = $(document).find('#empjobcat').val();
            var emppayclass = $(document).find('#emppayclass').val();
            var departmentview = $(document).find('#departmentview').val();
            init_employee_tbl(data_stat, $('#emptable',document), modulehash , departmentview , false , emppayclass , empjobcat);
            statusemp = data_stat;
        });

        $(document).on('click','#regular',function () {
            var typestat = 1;
            init_employee_tbl(1, $('#emptable'), modulehash ,false, typestat);
            statusemp = 1;
        });
        $(document).on('click','#contractual',function () {
            var typestat = 3;
            init_employee_tbl(1, $('#emptable'), modulehash,false,typestat);
            statusemp = 1;
        });

        $('body').on('click','#depart_list .selected_val',function(e){
            e.preventDefault();
            var this_ = $(this);
            var selected_val = $(this).val();
            init_employee_tbl(statusemp, $('#emptable'), modulehash , selected_val);
        });
        $(document).on('click','#activateemployee',function () {
                var this_ = $(this);
                var dataid = this_.attr("data-id");
                $.ajax({
                    url:PECO.base_url()+'hris/activateemployee',
                    type:'post',
                    data:{"dataid" : dataid},
                    dataType:'json'
                }).done(function (d) {
                    init_employee_tbl(0, $('#emptable'), modulehash);
                    PECO.initAlerts(d.msg, "PECO.net", d.func);
                }).fail(function () {
                    PECO.phpError();
                });
        });

        init_dt_subdetails($('#emptable'), 'hris/empinfo', 'false', 'sub-details');


        $(document).on('submit', '#frm_upload_pic', function(e) {
            e.preventDefault();
            var form = $(this);
            $.ajax({
                url:PECO.base_url()+'hris/uploadprofilepic',
                data: new FormData(form[0]),
                dataType: 'json',
                type: 'post',
                contentType: false,       // The content type used when sending data to the server.
                cache: false,             // To unable request pages to be cached
                processData: false,        // To send DOMDocument or non processed data file it is set to false
            }).done(function(d){
                PECO.initAlerts(d.msg, 'Picture Upload', d.func);
            }).fail(function(){
                alert("ERROR PHP");
            });
        });

        $(document).on('click', '#btn_upload_pic', function(e) {
            e.preventDefault();
            $('#frm_upload_pic', document).trigger('submit');

            /*
            var form = $('#frm_upload_pic', document);
            var form_data = new FormData(form);
            $.ajax({
                url:PECO.base_url()+'hris/uploadpic',
                data: form_data,
                dataType: 'json',
                type: 'post',
                contentType: false,       // The content type used when sending data to the server.
                cache: false,             // To unable request pages to be cached
                processData: false,        // To send DOMDocument or non processed data file it is set to false
            }).done(function(d){
                console.log(d);
            }).fail(function(){
                alert("ERROR PHP");
            });
            */
        });



        $('body').find(".draggable-modal").draggable({
            handle: ".modal-header"
        });

        $(document).on('click', '#btn_status', function(e){
            var this_dataid = $(this).attr('data-id');
            var this_datastat = $(this).attr('data-stat');
            if(this_datastat == 1) {
                changeuserstatus(this_dataid , 0);
            }else {
                changeuserstatus(this_dataid, 1);
            }
        });

        var changeuserstatus = function(sysid , stat){
            var this_ = $(this);
            var message = (stat == 0) ? 'Deactivate Employee Records' : 'Activate Employee Records';
            var message_btn = (stat == 0) ? 'Deactivate' : 'Activate';
            swal({
                title: "Are you sure?",
                text: message,
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes, " + message_btn + "!",
                closeOnConfirm: false,
                closeOnCancel: false,
                showLoaderOnConfirm: true
            }, function(isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: PECO.base_url() + "hris/changeuserstatus",
                        type: 'post',
                        data: {"sysid": sysid, "stat": stat},
                        dataType: 'json'
                    }).done(function (d) {
                        PECO.initAlerts(d.msg, "PECO.net", d.func);
                        var empjobcat = $(document).find('#empjobcat').val();
                        var emppayclass = $(document).find('#emppayclass').val();
                        var departmentview = $(document).find('#departmentview').val();
                        var currentStat = $('.emp-stat-btn').find('li.active').data("stat");
                        init_employee_tbl(currentStat, $('#emptable',document), modulehash , departmentview , false , emppayclass , empjobcat);
                        swal.close();
                    }).fail(function () {
                        PECO.phpError();
                        swal.close();
                    });
                }else{
                    swal.close();
                }
            });
        };
    };

    var init_dt_subdetails = function(tbl, url, inputs_arr, clss) {
        var inputs_arr = (inputs_arr) ? inputs_arr : false;
        var clss_ = (clss) ? clss : '';
        tbl.on('click', '#btn-expand', function () {
            var this_ = $(this);
            var thisTr = this_.closest('tr');
            var thisTr_child = thisTr.children('td').length;
            var data_id = this_.attr('data-id');
            if (this_.hasClass('expanded') == false) {
                thisTr.next('#error').remove();
                this_.removeClass('fa-plus-square-o').addClass('fa-minus-square-o');
                $.ajax({
                    url: PECO.base_url()+url,
                    type: 'post',
                    data: {'id': data_id, 'inputs': inputs_arr},
                    dataType: 'json',
                    beforeSend: function () {
                        thisTr.after('<tr id="loading" class="info " ><td colspan="' + thisTr_child + '" class=""><i class="fa fa-spinner fa-spin fa-pulse"></i> Loading..</td></tr>');

                    }
                }).done(function(d){
                    thisTr.after('<tr class="animated fadeIn fast compact '+d.func+'" id="details"><td colspan="' + thisTr_child + '" class="'+clss_+'">' + d.html + '</td></tr>');
                    tbl.find('#loading').remove();




                }).fail(function(){
                    thisTr.after('<tr class="animated fadeIn fast compact danger" id="error"><td colspan="' + thisTr_child + '"><i class="fa fa-times"></i> Error PHP</td></tr>');
                    tbl.find('#loading').remove();
                });
            } else {
                thisTr.next('#details').remove();
                thisTr.next('#error').remove();
                tbl.find('#loading').remove();
                this_.removeClass('fa-minus-square-o').addClass('fa-plus-square-o');
            }
            this_.toggleClass('expanded');
            this_.closest('tr').toggleClass('expand-show');
        });

    };

    var init_employee_tbl = function(stat, tbl, modulehash , dept , typestat , payclass , jobcat , report) {
        //

        $.extend(true, $.fn.DataTable.TableTools.classes, {
            "container": "btn-group tabletools-dropdown-on-portlet",
            "buttons": {
                "normal": "btn btn-sm default",
                "disabled": "btn btn-sm default disabled"
            },

            "collection": {
                "container": "DTTT_dropdown dropdown-menu tabletools-dropdown-menu"
            }
        });


        var emp_tbl = tbl;
        var emp_stat = stat;
        var emp_dept  =  (dept) ? dept : false;
        var emp_typestat  =  (typestat) ? typestat : false;

        $.ajax({
            url: PECO.base_url() + 'hris/emplist',
            type: 'post',
            dataType: 'json',
            data: {'status': emp_stat, 'modulehash': modulehash, 'viewtype': 0 , "dept":emp_dept,"typestat":emp_typestat , "payclass" : payclass , "jobcat" : jobcat , "report" : report},
            beforeSend: function(){
                PECO.DTphpLoading(emp_tbl, 'Loading employees...');
            }
        }).done(function (data) {
            if(data.report > 0){
                PECO.pecoRepPrint("Employees" , data.html , false);
            }
            // LUCKY WAS HERE 4-20-2017
            // LOAD SEARCH HIGHLIGHTS PLUGINS
            PECO.getHighlightsPlugin();
            emp_tbl.dataTable().empty();
            dt = emp_tbl.dataTable({
                bDestroy: true,
                bPaginate: true,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                bProcessing: true,
                "iDisplayLength": 100,
                "lengthMenu": [[100, 125, 150], [100, 125, 150]],

                aaData: data.list,
                aoColumns: [
                    {"data": "expand", sWidth: '15px', sClass: 'expand'},
                    {"data": "empid", sWidth: '110px'},
                    {"data": "lastname"},
                    {"data": "firstname"},
                    {"data": "middlename"},
                    {"data": "depname", sWidth: '120px'},
                    {"data": "datecreated", sWidth: '150px'},
                    {"data": "createdby", sWidth: '110px'},
                    {"data": "control", sWidth: '60px', sClass: "controls text-align-center hidden-print"}
                ],
                searchHighlight: true,
                fnRowCallback: function(nRow, data) {
                    PECO.dtExpandBtn(nRow, data.expand);
                    PECO.popOverRow($(nRow).find('.popovers'), true, true, 'popover-info');
                    PECO.iCheckRow($(nRow).find('input.icheck'), 'minimal', 'blue');
                    $('.tooltips', nRow).tooltip();
                }
            });
          //  init_employee_tbl(emp_stat, $('#emptable'), modulehash);
        }).fail(function(){
            PECO.DTphpError(emp_tbl);
        });
    };



    var res_summary = function () {
        $('#totallatesum').html('00:00:00');
        $('#totalutsum').html('00:00:00');
        $('#totalotsum').html('00:00:00');
        $('#daterangesum').html('0');
    };

    var draw_dtr = function (date_start, date_end, empid) {

        $.extend(true, $.fn.DataTable.TableTools.classes, {
            "container": "btn-group tabletools-dropdown-on-portlet",
            "buttons": {
                "normal": "btn btn-sm default",
                "disabled": "btn btn-sm default disabled"
            },
            "collection": {
                "container": "DTTT_dropdown dropdown-menu tabletools-dropdown-menu"
            }
        });

        $('#tbl_dtr').dataTable().empty();
        $('#tbl_dtr').dataTable({
            bDestroy: true,
            bPaginate: false,
            bFilter: false,
            bInfo: true,
            bStateSave: true,
            scrollY: false,
            bProcessing: true,
            bServerSide: true,
            //"order": [[ 0, "desc" ], [ 1, "asc" ]],
            oLanguage: {
                sProcessing: '<p class="text-info">Loading time logs... <br> <br> <i class="fa fa-spinner fa-spin fa-pulse fa-2x"></i> </p.'
            },
            ajax: {
                url: base_url + 'hris/gettimelogs',
                type: "POST",
                data: {'empid': empid, 'datestart': date_start, 'dateend': date_end},
            },

            aoColumns: [
                {"data": "day", sWidth: '90px', sClass: 'text-info'},
                {"data": "amin"},
                {"data": "amout"},
                {"data": "amlate",sClass:'font-red'},
                {"data": "pmin"},
                {"data": "pmout"},
                {"data": "pmlate",sClass:'font-red'},
                {"data": "otin"},
                {"data": "otout"},
                {"data": "locin"},
                {"data": "locout"},
                {"data": "stat"},
                {"data": "totallate",sClass:'font-red', sWidth: '100px'}
            ],
            columnDefs: [
                {"targets": '_all', "orderable": false, "searchable": false},
            ],
            //
            "dom": "<'row' <'col-md-12'T>><'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>", // horizobtal scrollable datatable

            // Uncomment below line("dom" parameter) to fix the dropdown overflow issue in the datatable cells. The default datatable layout
            // setup uses scrollable div(table-scrollable) with overflow:auto to enable vertical scroll(see: assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js).
            // So when dropdowns used the scrollable div should be removed.
            //"dom": "<'row' <'col-md-12'T>><'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r>t<'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>",

            "tableTools": {
                "sSwfPath": PECO.base_url()+"assets/global/plugins/datatables/extensions/TableTools/swf/copy_csv_xls_pdf.swf",
                "aButtons": [{
                    "sExtends": "pdf",
                    "sButtonText": "PDF"
                }, {
                    "sExtends": "csv",
                    "sButtonText": "CSV"
                }, {
                    "sExtends": "xls",
                    "sButtonText": "Excel"
                }]
            },
            //
            fnRowCallback: function (nRow, data, iDisplayIndex, iDisplayIndexFull) {
                if (data.weekend == true) {
                    $(nRow).addClass('danger');
                }
            },
            drawCallback: function (data, textStatus, jqXHR) {
                $('#totallatesum').html(data.json.overalllate);
                $('#totalutsum').html(data.json.totalut);
                $('#totalotsum').html(data.json.totalot);
                $('#daterangesum').html(data.json.daterange);
            }
        });

        PECO.initDTNicescroller();
    };

    var init_employee_attendance_daily = function() {



        $(document).on('submit','#addtimelogssubmit',function (e) {
            e.preventDefault();
            var this_ = $(this);
            swal({
                title: "Are you sure?",
                text: "Attendance will be modified saving.",
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
                        url:this_.attr("action"),
                        type:this_.attr("method"),
                        data:this_.serialize(),
                        dataType:'json'
                    }).done(function (d) {
                        swal("Saved!",d.msg, d.func);
                        if(d.qry == true){
                            $('#addtimelogssubmit')[0].reset();
                        }
                    }).fail(function () {
                        PECO.phpError();
                    });
                } else {
                    swal("Cancelled", "Processing canceled", "error");
                }
            });



        });

        $(document).on('change','#timetype',function () {
            var this_ = $(this);
            $.ajax({
                url:PECO.base_url()+'hris/gettimelogsformodify',
                type:'post',
                data:{"userid":$('#userid').val(), "timetype" : this_.val() , "todate":$('#to_date').val()},
                dataType:'json'
            }).done(function (d) {
                $('#oldtimelog').val(d.logtime);
            }).fail(function () {
                PECO.phpError();
            });
        });

        $(".timepicker-default").each(function(){
            $(this).timepicker({
                autoclose: !0,
                showSeconds: !0,
                minuteStep: 1
            });
        });

        $(document).on('submit','#submittimemodify',function (e) {
            e.preventDefault();
            var this_ = $(this);
            swal({
                title: "Are you sure?",
                text: "Attendance will be modified upon approval.",
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
                        url:this_.attr("action"),
                        type:this_.attr("method"),
                        data: this_.serialize(),
                        dataType:'json'
                    }).done(function (d) {
                        swal("Generated!", "Request has been sent!", "success");
                        $("#submittimemodify")[0].reset();
                        $('#attendancemodal').modal('hide');
                    }).fail(function () {
                        PECO.phpError();
                    });
                } else {
                    swal("Cancelled", "Processing canceled", "error");
                }
            });
        });

        $(document).on('click','#generateattendbtn',function (e) {

            var to_date = $('#to_date', document);
            var to_date_val = to_date.val();
            var payclass = $('#pay_class', document).find('li.type.active').attr('data-id');
            var ccid = $('#dept', document).val();
            var datearr = to_date_val.split("-");
            var daterange = datearr[2];
            var paytype = 1;
            if(daterange > 15){
                paytype = 2;
            }

            swal({
                title: "Are you sure?",
                text: "Attendance will be generated.",
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
                        url:PECO.base_url()+'hris/generateattendance',
                        type:'post',
                        data: {'today': to_date_val, 'payclass': payclass, 'ccid': ccid , 'paytype':paytype},
                        dataType:'json'
                    }).done(function (d) {
                        swal("Generated!", "Attendace has been generated!", "success");
                        //   PECO.pecoRepPrint("Daily Attendance Report" , d.html);
                    }).fail(function () {
                        PECO.phpError();
                    });
                } else {
                    swal("Cancelled", "Generate attendance processing canceled", "error");
                }
            });

        });

        var tbl = $('#tbl_attendance');
        PECO.DTDefault(tbl);

        PECO.select2Basic($('#dept'), 'hris/select2dept', 'Select Department..', true, true);
        tbl_employee_attendance_daily(tbl);


        $(document).on('change', '#to_date', function(e){
            tbl_employee_attendance_daily(tbl);
        });

        $(document).on('change', '#dept', function(e){
            tbl_employee_attendance_daily(tbl);
        });

        $(document).on('click', '#pay_class li.type', function(e){
            tbl_employee_attendance_daily(tbl);
        });

        setInterval(function(){
            // tbl_employee_attendance_daily(tbl);
        }, 10000); // 10 SEC Refresh

        tbl.on('click', '#btn-edit', function(e) {
            e.preventDefault();
            var this_ = $(this);
            var this_id = this_.attr('data-id');
            $("#timetype").val("selectime").change();
            $(document).find('#userid').val(this_id);
            $('#datetoday').val($('#to_date').val());
            $('#attendancemodal').modal('show');
        });


        PECO.dtSubDetails(tbl, 'hris/getattendancedetails' , $('#to_date' , document).val());
    };



    var tbl_employee_attendance_daily = function(tbl) {

        var to_date = $('#to_date', document);
        var to_date_val = to_date.val();
        var payclass = $('#pay_class', document).find('li.type.active').attr('data-id');
        var ccid = $('#dept', document).val();

        $.ajax({
            url: PECO.base_url() + 'hris/gettimelogsdaily',
            type: 'post',
            dataType: 'json',
            data: {'today': to_date_val, 'payclass': payclass, 'ccid': ccid},
            beforeSend: function() {
                //   tbl.dataTable().empty();
                //  PECO.DTphpLoading(tbl, "Loading today's attendance..");
            }
        }).done(function (data) {
            tbl.dataTable().empty();
            
            // Check if data is available and has the expected structure
            if (data && data.data && data.data.length > 0) {
                tbl.dataTable({
                    bDestroy: true,
                    bPaginate: true,
                    bFilter: true,
                    bInfo: true,
                    bStateSave: true,
                    aaData: data.data,
                    aoColumns: [
                        {"data": "expand", sWidth: '10px'},
                        {"data": "empid", sWidth: '90px', sClass: 'text-danger text-bold'},
                        {"data": "empname", sWidth: '200px', sClass: 'text-primary'},
                        {"data": "amin", sWidth: '', sClass: ''},
                        {"data": "amout", sWidth: '', sClass: ''},
                        {"data": "amlate", sWidth: '', sClass: 'amlate'},
                        {"data": "pmin", sWidth: '', sClass: ''},
                        {"data": "pmout", sWidth: '', sClass: ''},
                        {"data": "pmlate", sWidth: '', sClass: 'pmlate'},
                        {"data": "irrin", sWidth: '', sClass: ''},
                        {"data": "irrout", sWidth: '', sClass: ''},
                        {"data": "irrlate", sWidth: '', sClass: ''},
                        {"data": "otin", sWidth: '', sClass: ''},
                        {"data": "otout", sWidth: '', sClass: ''},
                        {"data": "locatorout", sWidth: '', sClass: ''},
                        {"data": "locatorin", sWidth: '', sClass: ''},
                        {"data": "latetotal", sWidth: '', sClass: 'latetotal'},
                        {"data": "status", sWidth: '', sClass: ''},
                        {"data": "control", sWidth: '', sClass: ''}
                    ],
                    columnDefs: [
                        // Hide columns that don't exist in the data but are in the table header
                        {"targets": [15, 16, 17, 18], "visible": false, "searchable": false}
                    ],
                    "aLengthMenu": [[25, 50, 75, -1], [25, 50, 75, "All"]],
                    "iDisplayLength": 100,
                    "drawCallback": function (settings) {
                    },
                    "fnRowCallback": function (nRow, data) {
                        $(nRow).addClass(data.rowclass);
                        
                        // Handle late styling
                        if (data.amlate && data.amlate.includes('text-danger')) {
                            $(nRow).find('td.amlate').addClass('text-danger');
                        }
                        if (data.pmlate && data.pmlate.includes('text-danger')) {
                            $(nRow).find('td.pmlate').addClass('text-danger');
                        }
                        if (data.latetotal && data.latetotal.includes('text-danger')) {
                            $(nRow).find('td.latetotal').addClass('text-danger text-bold');
                        }

                        $(nRow).find('[data-toggle="popover"]').each(function(){
                         PECO.popOverRow($(this), true, true, 'popover-info');
                     });
                        $(nRow).find('.tooltips').each(function(){
                            $(this).tooltip();
                        });
                    }
                ,searchHighlight: true,
                language: {
                    "emptyTable": "No attendance data found for the selected criteria"
                }
                });
        } else {
            // Handle case where no data is returned
            tbl.dataTable({
                bDestroy: true,
                bPaginate: true,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                aaData: [],
                aoColumns: [],
                "aLengthMenu": [[25, 50, 75, -1], [25, 50, 75, "All"]],
                "iDisplayLength": 100,
                "language": {
                    "emptyTable": "No attendance data found for the selected criteria"
                }
            });
        }
        $('.date-stat').html(data.date);
        }).fail(function(){
            PECO.DTDefault(tbl, 'Error Loading PHP query..');
        });
    };

    /*
    var fetchemployeeaccomp = function(sysid){
            $.ajax({
                url:PECO.base_url()+'hris/fetchaccomplishments',
                type:'post',
                data:{"dataid":sysid},
                dataType:'json'
            }).done(function (d) {
                $("#accomplishments").fileinput({
                    'initialPreview':d.files,
                    'initialPreviewAsData':true
                });
            }).fail(function () {
                PECO.phpError();
            });
    };
    */

    var init_hris_data = function(sysid){


        $('.nav-tabs a').on('shown.bs.tab', function(event){
            var target_ = $(event.target).attr('href');         // active tab
            if(target_ == '#accomplishements') {
                init_attachements_explorer(sysid);
            }
        });

        $(document).on('click', '#btn_del_attachement', function(e) {
           e.preventDefault();
           var this_ = $(this);
           var this_tile = this_.closest('.tile');
           var this_id = this_.attr('data-id');
           var this_file = this_.attr('data-file');
           $.ajax({
               url: PECO.base_url() + 'upload/deleteemployeeattachement',
               type: 'post',
               data: {id: this_id, 'file': this_file},
               dataType: 'json'
           }).done(function(d) {
               if(d.qry==true) {
                   this_tile.fadeOut('fast');
               }
           });
        });

        $('#workshift').editable({
            success: function (response, newValue) {


                fetchschedule(sysid);
                fetchmonthlysched(sysid,$('#schedmonth').val() , $('#schedyear').val());
                if (!response.success)
                    return response.msg;
            },
            error: function (response, newValue) {
                if (response.status === 500) {
                    return 'Service unavailable. Please try later.';
                } else {
                    return response.responseText;
                }
            },
            select2: {
                //tags: [],
                allowClear: true,
                width: '200px',
                id: function (item) {
                    return item.id;
                },
                ajax: {
                    url: PECO.base_url() + 'hris/select2workshift',
                    type: 'post',
                    dataType: 'json',
                    data: function (term) {
                        return {
                            term: term,
                        };
                    },
                    results: function (data) {

                        return {
                            results: $.map(data.list, function (item) {
                                return {
                                    text: item.text,
                                    id: item.id,
                                };
                            })
                        };
                    }
                },
                //formatResult: PECO.formatState, // omitted for brevity, see the source of this page
                // formatSelection: PECO.formatDataSelection, // omitted for brevity, see the source of this page
            },
            url: PECO.base_url() + 'hris/editinfo',
            title: 'Modify Workshift',
            placeholder: 'Modify Workshift',
            inputclass: 'form-control',
            emptytext: 'Enter Workshift',
            placement: 'right'
        }).on('click', function () {
            PECO.select2_scroller();
        });


        // Initialize file input after loading accomplishments
        get_init_accompfile(sysid, function(files) {
            filedropzone.fileinput({
                uploadAsync: true,
                showBrowse: true,
                browseOnZoneClick: true,
                showPreview: true,
                overwriteInitial: false,
                initialPreviewAsData: true, // defaults markup
                initialPreviewFileType: 'image', // image is the default and can be overridden in config below
                uploadExtraData: function () {
                    return {
                        dataid: sysid
                    };
                },
                initialPreview: files
            });
        });
        $.ajax({
            url:PECO.base_url()+"hris/getemploymenthistory",
            type:"post",
            data:{"empid" : dataid},
            dataType:'json'
        }).done(function (d) {
            workhistorytbl.dataTable().empty();
            workhistorytbl.dataTable({
                bDestroy: true,
                bPaginate: false,
                bFilter: false,
                bInfo: false,
                bStateSave: true,
                bProcessing: true,
                aaData: d.employmenthistorydata,
                aoColumns: [
                    {"data":"num" , sWidth:"10%"},
                    {"data":"position"},
                    {"data":"company"},
                    {"data":"year"}
                ],
                searchHighlight: true
            });
        }).fail(function () {
            PECO.phpError();
        });
    };

    var init_emplogs = function(bioid){



        var dateval  = $('#emplogdateval',document).val();
        var timelogstable = $('#timelogstable' , document);
        $.ajax({
            url:PECO.base_url()+'hris/getempattendancelogs',
            type:'post',
            data:{"bioid":bioid,"dateval":dateval},
            dataType:'json'
        }).done(function (d) {
            timelogstable.dataTable().empty();
            timelogstable.dataTable({
                bDestroy: true,
                bPaginate: false,
                bFilter: false,
                bInfo: false,
                bStateSave: true,
                bProcessing: true,
                aaData: d.emplogstime,
                aoColumns: [
                    {"data":"num" , sWidth:"10%"},
                    {"data":"bioid",sClass:'bioidclass'},
                    {"data":"logtime",sClass:'logtime'},
                    {"data":"remarks",sClass:'remarks'},
                    {"data":"desc"}

                ],
                searchHighlight: true,
                fnRowCallback: function(nRow, data, iDisplayIndex) {
                    PECO.select2Basic($('#timedesc' , nRow),'hris/gettimelogsselect2','Select type',false , false , data.logtype);
                }
            });
        }).fail(function () {
            PECO.phpError();
        });

        $(document).on('change','#timedesc',function (e) {

            var logdate  = $('#emplogdateval',document).val();
            var this_ = $(this);
            var this_tr = this_.closest('tr');
            var logtime = this_tr.find('td.logtime').text();
            var bioid = this_tr.find('td.bioidclass').text();
            var remarks = this_tr.find('td #remarks').val();

            $.ajax({
                url:PECO.base_url()+'hris/updateattendancetimelogs',
                type:'post',
                data:{"bioid":bioid,"logdate":logdate,"logtype":this_.val(),"logtime":logtime,"remarks":remarks},
                dataType:'json'
            }).done(function (d) {
                PECO.initAlerts(d.msg,"PECO.net",d.func);

            }).fail(function () {
                PECO.phpError();
            });
            e.stopImmediatePropagation();
        });
    };

    var init_pending_workshift = function(idgroup){

        $(document).on('click','#approveallbtn',function () {
            var this_ = $(this);
            var dataid = this_.attr("data-id");

            if (workshiftpending.fnSettings().aoData.length===0) {
               PECO.initAlerts("No data to approve.","PECO.net","info");
            }else{
                swal({
                    title: "Are you sure?",
                    text: "Employees Workshift will be saved.",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Yes",
                    closeOnConfirm: false,
                    closeOnCancel: false,
                    showLoaderOnConfirm: true
                }, function(isConfirm){
                    if (isConfirm) {
                        $.ajax({
                            url:PECO.base_url()+'hris/approveallempworkshift',
                            type:'post',
                            data:{"groupid" : dataid},
                            dataType:'json'
                        }).done(function (d) {
                            swal("Approved", d.msg, d.func);
                            if(d.qry == true){
                                populatependingworkshift(idgroup);
                                this_.hide();
                            }
                        }).fail(function () {
                            PECO.phpError();
                        });
                    } else {
                        swal("Cancelled", "Processing canceled", "error");
                    }
                });
            }

        });

        $(document).on('click','#workshiftdisapprovebtn',function () {
            var this_ = $(this);
            var dataid  = this_.attr("data-id");
            var groupid  = this_.attr("data-groupid");

            swal({
                title: "Are you sure?",
                text: "Workshift will be disapproved.",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes",
                closeOnConfirm: false,
                closeOnCancel: false,
                showLoaderOnConfirm: true
            }, function(isConfirm){
                if (isConfirm) {
                    $.ajax({
                        url:PECO.base_url()+'hris/disapprovependingworkshift',
                        type:'post',
                        data:{"dataid" : dataid , "groupid" : groupid},
                        dataType:'json'
                    }).done(function (d) {
                        swal("Disapproved", d.msg, d.func);
                        if(d.qry == true){
                            populatependingworkshift(idgroup);
                        }
                    }).fail(function () {
                        PECO.phpError();
                    });
                } else {
                    swal("Cancelled", "Processing canceled", "error");
                }
            });

        });
        populatependingworkshift(idgroup);
    };

    var populatependingworkshift = function(dataid){

        $.ajax({
            url:PECO.base_url()+'hris/getpendingworkshift',
            type:'post',
            data:{"dataid":dataid},
            dataType:'json'
        }).done(function (d) {
            workshiftpending.dataTable().empty();
            workshiftpending.dataTable({
                bDestroy: true,
                bPaginate: false,
                bFilter: false,
                bInfo: false,
                bStateSave: true,
                bProcessing: true,
                aaData: d.emppendingworkshift,
                aoColumns: [
                    {"data":"num",sWidth:'5px'},
                    {"data":"empid",sWidth:'20px'},
                    {"data":"name",sWidth:'80px'},
                    {"data":"shift"},
                    {"data":"control"}
                ],
                searchHighlight: true
            });
        }).fail(function () {
            PECO.phpError();
        });
    };

    return {
        init: function (modulehash, dataid) {
            initHR_view(modulehash, dataid);
        },
        list: function(modulehash) {
            init_employee_list(modulehash);
        },
        attendancedaily: function() {
            init_employee_attendance_daily();
        },
        leavecredits: function(el, empid) {
            init_employee_leave_credits(el, empid);
        },
        accomplishments: function (dataid) {
            init_attachedments(dataid);
        },
        init_data: function(dataid){
            init_hris_data(dataid);
        },
        initemptattendancetable:function(bioid){
            init_emplogs(bioid);
        },
        initpendingworkshift:function(dataid){
            init_pending_workshift(dataid);
        },
        attachementexplorer: function(dataid) {
            init_attachements_explorer(dataid);
        }
    }
}();
