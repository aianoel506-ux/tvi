/**
/**
 * Created by IT on 3/5/2018.
 */

var MAINTENACE = function(){
    //PECO.getiCheckPlugin();
    PECO.getHighlightsPlugin();
    PECO.getSelect2Plugins();
    PECO.getSweetAlert();

    var holidaytableentry = $('#holidaytableentry',document);
    var leavecreditstype = $('#leavecreditstype');
    var empschedtbl= $('#empschedtbl' , document);
    var holidaystbl = $('#holidaystbl');
    var workshifttable = $('#workshifttable');
    var timeshifttabl = $('#timeshifttabl');
    var attendancerequesttbl = $('#attendancerequesttbl');
    var payrollmatrixtable = $('#payrollmatrixtable');
    var typesnames = $(document).find("#typesnames");
    var groupworkshift= $(document).find('#groupworkshift');
    var regularemployeeschedtable = $(document).find('#regularemployeeschedtable');
    var departmenttable = $(document).find('#departmenttable');
    var positionstable = $(document).find('#positionstable');
    var empcreditstbl = $(document).find('#empcreditstbl');
    var printleavetbl = $(document).find('#printleavetbl');
    var empcreditstbl_tiered = $(document).find('#empcreditstbl_tiered');
    var printleavetbl_tiered = $(document).find('#printleavetbl_tiered');
    var cancelledleavetbl = $(document).find('#cancelledleavetbl');
    var myTable = $('#printleavetbl').DataTable();
    var employeedept = $(document).find('#employeedept');
    var flexibalancetable = $(document).find('#flexibalancetable');
    var pendingflexitable = $(document).find('#pendingflexitable');
    var employeeworkshift = $(document).find('#employeeworkshift');
    var totalflexicredits = $(document).find('#totalflexicredits');
    var credistlistemp = $(document).find('#credistlistemp');
    var totalincurredtbl = $(document).find('#totalincurredtbl');
    var unionbalancetbl = $(document).find('#unionbalancetbl');
    var unionpendingtrntbl = $(document).find('#unionpendingtrntbl');
    var unioncreditstbl = $(document).find('#unioncreditstbl');
    var availedunion = $(document).find('#availedunion');
    var personalinfotbl = $(document).find('#personalinfotbl');

    var values = [];

    var events = function(){

        $(document).on('click','#refreshsched',function () {
            fetchempsched();
        });
        $(document).on('submit','#fetchschedbydate',function (e) {
            e.preventDefault();
            var this_ = $(this);
            $.ajax({
                url:this_.attr("action"),
                type:this_.attr("method"),
                data:this_.serialize(),
                dataType:'json'
            }).done(function (d) {
                populate_sched(d);
            }).fail(function () {
                PECO.phpError();
            });
        });
        $(document).on('click','#printschedlist',function () {
            $.ajax({
                url:PECO.base_url()+'hris/fetchempsched',
                type:'post',
                dataType:'json'
            }).done(function (d) {

                var count = d.schedlist.length;

                var html = '';
                html += '<table class="table table-bordered table-responsive">';
                html += '<thead>';
                html += '<tr>';
                html += '<th></th>';
                html += '<th>Emp ID</th>';
                html += '<th>Workshift</th>';
                html += '<th>From</th>';
                html += '<th>To</th>';
                html += '<th>Date</th>';
                html += '</tr>';
                html += '</thead>';
                html += '<tbody>';

                var i = 0;

                for(i=0;i<count;i++){
                    html += '<tr>';
                    html += '<td>'+d.schedlist[i].num+'</td>';
                    html += '<td>'+d.schedlist[i].name+'</td>';
                    html += '<td>'+d.schedlist[i].workshift+'</td>';
                    html += '<td>'+d.schedlist[i].start+'</td>';
                    html += '<td>'+d.schedlist[i].end+'</td>';
                    html += '<td>'+d.schedlist[i].tdate+'</td>';
                    html += '</tr>';
                }

                html += '</tbody>';
                html += '</table>';
                 PECO.pecoRepPrint("Schedule Report",html);
            });
        });
    };

    var fetchpayrollmatrix = function () {
        $.ajax({
            url:PECO.base_url()+'hris/getpayrollmatrix',
            type:'post',
            dataType:'json'
        }).done(function (d) {
            populatepayrollmatrix(d);
        }).fail(function () {
            PECO.phpError();
        });
    };

    var populatepayrollmatrix = function(data){

        payrollmatrixtable.dataTable().empty();
        payrollmatrixtable.dataTable({
            bDestroy: true,
            bPaginate: true,
            bFilter: true,
            bInfo: true,
            bStateSave: true,
            bProcessing: true,
            aaData: data.payrollmatrixdata,
            aoColumns: [
                {"data":"num"},
                {"data":"codes"},
                {"data":"types"},
                {"data":"functions"},
                {"data":"effects"},
                {"data":"notax"},
                {"data":"capping"}
            ],
            searchHighlight: true
        });
    };

    var fetchrequestattendance = function(){
        $.ajax({
            url:PECO.base_url()+'hris/fetchrequestattendance',
            type:'post',
            dataType:'json'
        }).done(function (d) {
            populaterequestattendance(d);
        }).fail(function () {
            PECO.phpError();
        });
    };
    var populaterequestattendance = function (data) {
        attendancerequesttbl.dataTable().empty();
        attendancerequesttbl.dataTable({
            bDestroy: true,
            bPaginate: true,
            bFilter: true,
            bInfo: true,
            bStateSave: true,
            bProcessing: true,
            aaData: data.requestlogsdata,
            aoColumns: [
                {"data":"num"},
                {"data":"empid"},
                {"data":"name"},
                {"data":"attdate"},
                {"data":"type"},
                {"data":"timelogs"},
                {"data":"reason"},
                {"data":"status"},
                {"data":"control"}
            ],
            searchHighlight: true
        });
    };

    var fetchdepartments = function(){
        $.ajax({
            url:PECO.base_url()+'hris/getalldepartments',
            type:'post',
            dataType:'json'
        }).done(function (d) {

            departmenttable.dataTable().empty();
            departmenttable.dataTable({
                bDestroy: true,
                bPaginate: true,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                bProcessing: true,
                aaData: d.departmentsdata,
                aoColumns: [
                    {"data":"num"},
                    {"data":"codes"},
                    {"data":"names"},
                    {"data":"desc"},
                    {"data":"address"}
                ],
                searchHighlight: true
            });
        }).fail(function () {
            PECO.phpError();
        });
    };
    
    var fetchpositions = function(){
        $.ajax({
            url:PECO.base_url()+'hris/fetchpositions',
            type:'json',
            dataType:'json'
        }).done(function (d) {
            positionstable.dataTable().empty();
            positionstable.dataTable({
                bDestroy: true,
                bPaginate: true,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                bProcessing: true,
                aaData: d.positionsdata,
                aoColumns: [
                    {"data":"num"},
                    {"data":"codes"},
                    {"data":"names"},
                    {"data":"desc"},
                    {"data":"control"}
                ],
                searchHighlight: true
            });
        }).fail(function () {
            PECO.phpError();
        });
    };

    var fetchholidays = function(){
        $.ajax({
            url:PECO.base_url()+'hris/fetchholidaysentry',
            type:'post',
            dataType:'json'
        }).done(function (d) {
            holidaytableentry.dataTable().empty();
            holidaytableentry.dataTable({
                bDestroy: true,
                bPaginate: true,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                bProcessing: true,
                aaData: d.holidaydata,
                aoColumns: [
                    {"data":'num'},
                    {"data": 'date'},
                    {"data":'desc'},
                    {"data":'datecreated'},
                    {"data":'createdby'},
                    {"data":'control'}
                ],
                searchHighlight: true
            });
        }).fail(function () {
            PECO.phpError();
        });
    };

    var getempcredits = function(print , year , ccid, jobcat){
        $.ajax({
            url:PECO.base_url()+'hris/getempcredits',
            data:{"print" : print , "year" : year , "ccid" : ccid , "jobcat" : jobcat},
            type:'post',
            dataType:'json'
        }).done(function (data) {
            if(data.print > 0){
                PECO.pecoRepPrint("Leave Credits Report" , data.html);
            }else{
                empcreditstbl.dataTable().empty();
                empcreditstbl.dataTable({
                    bDestroy: true,
                    bPaginate: true,
                    bFilter: true,
                    bInfo: true,
                    bStateSave: true,
                    bProcessing: true,
                    aaData: data.empcredits,
                    aoColumns:data.datacolumns,
                    searchHighlight: true
                });
            }

        }).fail(function () {
            PECO.phpError();
        });
    };

    var  leaveprint = function (reptitle, content) {
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
            '<div style="position:left: 0px; width: 100%;">' + content + '</div>';
        setTimeout(function () {
            //  win.print(); // blocking - so close will not
            //  win.close(); // execute until this is done
        }, 250);
    };

    var fetchempdepartment = function(){
        $.ajax({
            url:PECO.base_url()+'hris/getemployeesdept' ,
            type:'post',
            dataType:'json'
        }).done(function (data) {
            employeedept.dataTable().empty();
            employeedept.dataTable({
                bDestroy: true,
                bPaginate: false,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                bProcessing: true,
                aaData: data.empdept,
                aoColumns: [
                    {"data":"num"},
                    {"data":"name"},
                    {"data":"dept"}
                ],
                searchHighlight: true,
                fnRowCallback: function(nRow, data, iDisplayIndex) {
                    PECO.select2Basic($('#empdeptselect2' , nRow) , 'hris/getempdept','Select Department',true,false,data.ccid);
                }
            });
        }).fail(function () {
            PECO.phpError();
        });

    };

    var employeeleavetrn = function(){
        $.ajax({
            url:PECO.base_url()+'hris/printleaveform',
            type:'post',
            data:{}
        });
    };

    var init_hrmaintenance = function (month , year) {

        fetchdepartments();
      //  fetchempdepartment();
        fetchpositions();

        $(document).on('click','#deleteposbtn',function () {
            var this_ = $(this);
            var dataid = this_.attr("data-id");

            swal({
                title: "Are you sure?",
                text: "Position will be remove.",
                type: "error",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes, Remove!",
                closeOnConfirm: false,
                closeOnCancel: false,
                showLoaderOnConfirm: true
            }, function(isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url:PECO.base_url()+'hris/removepos',
                        type:'post',
                        data:{"dataid" : dataid},
                        dataType:'json'
                    }).done(function (data) {
                        swal("PECO" , data.msg , data.func);
                        if(data.qry == true){
                            fetchpositions();
                        }
                    }).fail(function () {
                        PECO.phpError();
                    });
                }else{
                    swal.close();
                }
            });


        });

        $(document).on('change' , '#empdeptselect2' , function () {
            var this_ = $(this);
            var empid = this_.attr("data-empid");
            if(this_.val() != '' || this_.val() != null){
                $.ajax({
                    url:PECO.base_url()+'hris/updateempdept',
                    type:'post',
                    data:{"id" :this_.val() , "empid" : empid},
                    dataType:'json'
                }).done(function (data) {
                    PECO.initAlerts(data.msg , "PECO" , data.func);
                }).fail(function () {
                    PECO.phpError();
                });
            }else{
                PECO.initAlerts("Please select department" , "PECO" , "info");
            }

        });


       // PECO.select2Basic($('#employee',document),'hris/getallemployees','Select Employee',false,false,false);


       // fetchholidays();
        var monthdata = (month) ? month : false;
        var year = (year) ? year : false;
        var d = new Date();
        var defaultyear = d.getFullYear();
        var monthdefault = d.getMonth() + 1;
        if(monthdata > 0){
            monthdefault = monthdata;
        }
        if(year > 0){
            defaultyear = year;
        }
        $(document).on('click','#deleteholidays',function () {
            var this_ = $(this);
            var dataid = this_.attr("data-id");
            swal({
                title: "Are you sure?",
                text: "Holiday will be removed.",
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
                        url:PECO.base_url()+'hris/removeholiday',
                        type:'post',
                        data:{"dataid":dataid},
                        dataType:'json'
                    }).done(function (d) {
                        swal("PECO.net", d.msg, d.func);
                        if(d.qry == true){
                            fetchholidays();
                        }
                    }).fail(function () {
                        PECO.phpError();
                    });
                } else {
                    swal("Cancelled", "Processing canceled", "error");
                }
            });
        });
        $(document).on('submit','#submitholidayentry',function (e) {
            e.preventDefault();
            var this_ = $(this);
            swal({
                title: "Are you sure?",
                text: "Holiday will be saved.",
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
                        swal("PECO.net", d.msg, d.func);
                        if(d.qry == true){
                            fetchholidays();
                            this_[0].reset();
                        }
                    }).fail(function () {
                        PECO.phpError();
                    });
                } else {
                    swal("Cancelled", "Processing canceled", "error");
                }
            });

        });
        PECO.select2Basic($('#workshiftmonth'),'systems/select2month', 'Select Month...', false, false, monthdefault);
        PECO.select2Basic($('#workshiftyear'),'hris/select2year','Select Year',false,false,defaultyear);



        $(document).on('click','#searchworkshift',function (e) {
            var month = $(document).find('#workshiftmonth').val();
            var year = $(document).find('#workshiftyear').val();
            fetchregularemployeesched (month , year);
        });
        $(document).on('submit','#submitposition',function (e) {
            e.preventDefault();
            var this_  = $(this);
            $.ajax({
                url:this_.attr("action"),
                type:this_.attr("method"),
                data:this_.serialize(),
                dataType:'json'
            }).done(function (d) {
                PECO.initAlerts(d.msg,"PECO.net",d.func);
                if(d.qry == true){
                    $('#submitposition')[0].reset();
                    fetchpositions();
                }
            }).fail(function () {
                PECO.phpError();
            });
        });
        $(document).on('submit','#submitdepartment',function (e) {
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
                    $('#submitdepartment')[0].reset();
                    fetchdepartments();
                }
            }).fail(function () {
                PECO.phpError();
            });
        });



        $(document).on('submit','#submitmatrixform',function (e) {
            e.preventDefault();
            var this_ = $(this);
            $.ajax({
                url:this_.attr("action"),
                method:this_.attr("method"),
                data:this_.serialize(),
                dataType:'json'
            }).done(function (d) {
                PECO.initAlerts(d.msg,"PECO.net",d.func);
                $('#submitmatrixform')[0].reset();
                if(d.qry == true){
                    fetchpayrollmatrix();
                }
            }).fail(function () {
                PECO.phpError();
            });
        });

        $(document).on('click','#approve',function () {
            var this_ = $(this);
            var dataid = this_.attr("data-id");
            swal({
                title: "Are you sure?",
                text: "Approve timelogs request.",
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
                        url:PECO.base_url() + 'hris/approvetimelogsrequest',
                        type:"post",
                        data:{"dataid":dataid},
                        dataType:'json'
                    }).done(function (d) {
                        swal("Approved!", d.msg, d.func);
                    }).fail(function () {
                        PECO.phpError();
                    });
                } else {
                    swal("Cancelled", "Processing canceled", "error");
                }
            });
        });
        $(document).on('click','#disapprove',function () {
            var this_ = $(this);
            var dataid = this_.attr("data-id");
            swal({
                title: "Are you sure?",
                text: "Disapprove timelogs request.",
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
                        url:PECO.base_url() + 'hris/disapprovetimelogsrequest',
                        type:"post",
                        data:{"dataid":dataid},
                        dataType:'json'
                    }).done(function (d) {
                        swal("Disapproved!", d.msg, d.func);
                    }).fail(function () {
                        PECO.phpError();
                    });
                } else {
                    swal("Cancelled", "Processing canceled", "error");
                }
            });
        });

        $(".timepicker-default").each(function(){
            $(this).timepicker({
                autoclose: !0,
                showSeconds: !0,
                minuteStep: 1
            });
        });

        events();
        $('#calendarid').calendar({
            "contextMenuItems":true,

            clickDay: function(e) {
                var this_ = $(this);
              //  alert(this_.find('.day .day-content').text());
                var d = e.date;
                var dateclick = $.datepicker.formatDate('yy-mm-dd', d);
                $(document).find('#dateofholiday').val(dateclick);

            }
        });
        $(document).on('submit','#submitholidays',function (e) {
            e.preventDefault();
            var this_ = $(this);
           var date = $(document).find('#dateofholiday').val();

           if(date === ''){
                PECO.init("Please select date","PECO.net","info");
           }else{

           }
        });




        init_events();


        fetchworkshiftlist();
        fetchtimeshift();
    //
      //  fetchpayrollmatrix();
        fetchregularemployeesched();

        PECO.select2Basic(typesnames , "hris/getparametersname",'Please select type',false,false,false);
    };

    var fetchregularemployeesched = function(month , year){
        var monthdata = (month) ? month : false;
        var year = (year) ? year : false;
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
                url:PECO.base_url()+'hris/getallemployeeschedule',
                type:'post',
                data:{"monthdata":monthdefault,"yeardata":defaultyear},
                dataType:'json',
                beforeSend: function(){
                    regularemployeeschedtable.dataTable().empty();
                    PECO.DTphpLoading(regularemployeeschedtable, 'Loading... ');
                }
            }).done(function (d) {
                regularemployeeschedtable.dataTable().empty();
                regularemployeeschedtable.dataTable({
                    bDestroy: true,
                    bPaginate: true,
                    bFilter: true,
                    bInfo: true,
                    bStateSave: true,
                    bProcessing: true,
                    aaData: d.regularempscheddata,
                    aoColumns: [
                        {"data":"num"},
                        {"data":"name"},
                        {"data":"shift"},
                        {"data":"fromdate"},
                        {"data":"todate"},
                        {"data":"datecreated"}
                    ],
                    searchHighlight: true
                });
            }).fail(function () {
                PECO.phpError();
            });
    };

    var init_empschedule = function () {
        PECO.select2Basic($('#employee',document) , 'hris/empschedlist','Select Employee',true,false,false);
        PECO.select2Basic($('#workshift',document) , 'hris/empschedworkshift','Select Workshift',true,false,false);

    };

    var fetchtimeshift = function(){
        $.ajax({
            url:PECO.base_url()+'hris/fetchtimeshift',
            type:'post',
            dataType:'json'
        }).done(function (d) {
            populatetimeshift(d);
        }).fail(function () {
            PECO.phpError();
        });
    };
    
    var populatetimeshift = function (d) {
        timeshifttabl.dataTable().empty();
        timeshifttabl.dataTable({
            bDestroy: true,
            bPaginate: true,
            bFilter: true,
            bInfo: true,
            bStateSave: true,
            bProcessing: true,
            aaData: d.timeshiftdata,
            aoColumns: [
                {"data":"num"},
                {"data":"shiftid"},
                {"data":"days"},
                {"data":"amtimein"},
                {"data":"amtimeout"},
                {"data":"pmtimein"},
                {"data":"pmtimeout"}
            ],
            searchHighlight: true
        });
    };




    var populate_sched = function(d){
        empschedtbl.dataTable().empty();
        empschedtbl.dataTable({
            bDestroy: true,
            bPaginate: true,
            bFilter: true,
            bInfo: true,
            bStateSave: true,
            bProcessing: true,
            aaData: d.schedlist,
            aoColumns: [
                {"data":"num", sWidth: '10%'},
                {"data":"name", sWidth: '10%'},
                {"data":"workshift", sWidth: '50%'},
                {"data":"start", sWidth: '10%', sClass:'text-info'},
                {"data":"end", sWidth: '10%', sClass:'text-info'},
                {"data":"tdate", sWidth: '10%'},
                {"data":"status", sWidth: '10%'}
            ],
            searchHighlight: true
        });
    };
    var init_events = function () {

        $(document).on('submit','#submitlocatorslip2',function (e) {
            e.preventDefault();
            var this_ = $(this);
            $.ajax({
                url:this_.attr("action"),
                type:this_.attr("method"),
                data:this_.serialize(),
                dataType:'json'
            }).done(function (d) {
                if(d.qry == true){
                    $('#submitlocatorslip2')[0].reset();
                }
            }).fail(function () {
                PECO.phpError();
            });
        });

        $(document).on('submit','#submitschedule',function (e) {
            var this_ = $(this);
            e.preventDefault();
           swal({
                title: "Are you sure you want to add schedule to this employee?",
                text: 'Employee Scheduling',
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
                        url:this_.attr("action"),
                        type:this_.attr("method"),
                        data:this_.serialize(),
                        dataType:'json'
                    }).done(function (d) {
                        fetchempsched();
                        swal(d.title, d.msg, d.func);
                    }).fail(function () {
                        PECO.phpError();
                    });
                }
            });
        });

        $(document).on('click','#updateempbtn',function (e) {
            e.preventDefault();
            var this_  = $(this);
            var dataid = this_.attr('data-id');

            $.SmartMessageBox({
                title: "<i class='fa fa-question fa-fw fa-lg txt-color-yellow'></i>Are you sure you want to update the status of this employee?</span>",
                content: 'Please confirm action taken!',
                buttons: '[Yes][No]'
            }, function (ButtonPressed) {
                if (ButtonPressed === "Yes") {
                    $.ajax({
                        url:PECO.base_url()+"hris/updatestatusemp",
                        type:"post",
                        data:{"dataid":dataid},
                        dataType:"json"
                    }).done(function (d) {
                        PECO.initAlerts(d.msg , "Employee Maintenance" , d.func);
                        fetchempmaintenance();
                    }).fail(function () {
                        PECO.phpError();
                    });
                }
            });
        });


        $(document).on('click','#copyyearbtn',function (e) {
            e.preventDefault();
            var types = $(document).find('#creditsysid').val();
            if(types == ''){
                PECO.initAlerts("Please select leave type credits","PECO.net","info");
            }else{
                $.SmartMessageBox({
                    title: "<i class='fa fa-question fa-fw fa-lg txt-color-yellow'></i>Are you sure you want to apply the past credits of all employees?</span>",
                    content: 'Please confirm action taken!',
                    buttons: '[Yes][No]'
                }, function (ButtonPressed) {
                    if (ButtonPressed === "Yes") {
                        var year = $(document).find('#yeartxt').val();
                        $.ajax({
                            url:PECO.base_url()+"hris/copytonextyearcredits",
                            type:"post",
                            data:{"year":year},
                            dataType:"json"
                        }).done(function (d) {
                            PECO.initAlerts(d.msg , "PECO.net" , d.func);
                            $(document).find('#nodays').val("");
                            $(document).find('#yeartxt').val("");
                            $(document).find('#creditsysid').val("");
                            $(document).find('#creditsselected').text("");
                        }).fail(function () {
                            PECO.phpError();
                        });
                    }
                });
            }

        });
    };


    var loademployees = function (payclass=false) {
        var payclass = (payclass) ? payclass : false;
        var employeelist = $('#employeelist');
        $.ajax({
            url:PECO.base_url()+"hris/fetchemployees",
            type:"post",
            dataType:"json",
            data: {payclass: payclass},
            beforeSend: function(){
                employeelist.dataTable().empty();
                PECO.DTphpLoading(employeelist, 'Loading... ');
            }
        }).done(function (d) {

            employeelist.dataTable().empty();
            employeelist.dataTable({
                bDestroy: true,
                bPaginate: false,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                bProcessing: true,
                aaData: d.emplist,
                scrollY: '300px',
                aoColumns: [
                    {"data":"num" , sWidth:"10%"},
                    {"data":"lastname"},
                    {"data":"firstname"},
                    {"data":"middlename"},
                    {"data":"control"}
                ],
                searchHighlight: true,
                fnRowCallback: function(nRow, aData) {
                    //PECO.iCheckRow($('.icheck', nRow), 'square', 'blue');
                }
            }).on('click', 'tr .icheck', function (e) {
                var this_ = $(this);
                var this_tr = this.closest('tr');
                this_tr.toggleClass('active');
                //var id = this_.attr("data-id");
                //values.push(id);

            });
        }).fail(function () {
            PECO.phpError();
        });
    };


    var handler_leave_credits = function() {

        PECO.select2Basic($('#yearcredits')  , 'systems/select2year' , 'Year' , false,false,false);
        PECO.select2Basic($('#costcentercredits')  , 'hris/get_cost_centers' , 'Costcenter' , false,false,false);
        $('#jobcat',document).select2({
            "allowClear":true,
            "placeholder" : 'Job Category...'
        });

        var yearcredits= $(document).find('#yearcredits').val();
        var costcentercredits= $(document).find('#costcentercredits').val();
        var jobcat = $(document).find('#jobcat').val();

        getempcredits(false,yearcredits,costcentercredits,jobcat);

        $(document).on('click','#printleavecredits' , function () {
            getempcredits(1 ,yearcredits,costcentercredits,jobcat);
        });

        $(document).on('change','#yearcredits' , function () {
            var d = new Date();
            var yeardefault = d.getFullYear();

            if ($(this).val() != '') {
                yearcredits = $(this).val();
            } else {
                yearcredits = yeardefault;
            }
            getempcredits(false,yearcredits,costcentercredits,jobcat);
        });
        $(document).on('change','#costcentercredits',function () {
            costcentercredits = $(this).val();
            getempcredits(false , yearcredits , costcentercredits,jobcat);
        });
        $(document).on('change','#jobcat',function () {
            jobcat = $(this).val();
            getempcredits(false , yearcredits , costcentercredits,jobcat);
        });
    };

    var handler_leave_credits_entry_modal = function() {
        var payclass = $('#select_payclass',document);
        loademployees();

        PECO.select2Basic(payclass,'admin/select2payclass','Payclass',false);
        payclass.on('change',function () {
            loademployees(payclass.val());
        });

        $(document).on('submit','#frm_submit_selected_employee_leave_credits',function (e) {
            e.preventDefault();
            var form_ = $(this);

            $.ajax({
                url: form_.attr('action'),
                type:"post",
                data: form_.serialize(),
                dataType:"json"
            }).done(function (d) {
                PECO.initAlerts(d.msg , "PECO.net" , d.func);
                values = null;
                //loademployees();
                //$('#creditsselectedmodal').modal('hide');
                //$(document).find('#yeartxt').val('');
                //$(document).find('#nodays').val('');
            }).fail(function () {
                PECO.phpError();
            });
            e.stopImmediatePropagation();
        });

    };

    var fetch_emp_encoded_credits = function (year) {
        $.ajax({
            url:PECO.base_url()+'hris/getempencodedcredits',
            type:'post',
            data:{"year" : year},
            dataType:'json'
        }).done(function (data) {
            credistlistemp.dataTable().empty();
            credistlistemp.dataTable({
                bDestroy: true,
                bPaginate: true,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                bProcessing: true,
                aaData: data.listdata,
                aoColumns: [
                    {"data":"num" , sWidth: '10%'},
                    {"data":"empid" },
                    {"data":"lastname" },
                    {"data":"firstname" },
                    {"data":"vl"},
                    {"data":"sl"},
                    {"data":"el"},
                ],
                searchHighlight: true
            });
        }).fail(function () {
            PECO.phpError();
        });

    };

    var unionleavecredits = function(){
        $.ajax({
            url:PECO.base_url()+'hris/getunioncredits',
            type:'post',
            dataType:'json'
        }).done(function (data) {
            unioncreditstbl.dataTable().empty();
            unioncreditstbl.dataTable({
                bDestroy: true,
                bPaginate: true,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                bProcessing: true,
                aaData: data.unioncreditsdata,
                aoColumns: [
                    {"data":"num" , sWidth: '10%'},
                    {"data":"credit" },
                    {"data":"year" },
                    {"data":"status" },
                    {"data":"control" }
                ],
                searchHighlight: true
            });
        }).fail(function () {
            PECO.phpError();
        });

    }

    var handler_leave_credits_entry = function(){
        loadleavecredits();
        fetch_emp_encoded_credits();
        unionleavecredits();

        $(document).on('submit' , '#submitunion' , function (e) {
            e.preventDefault();
            var this_ = $(this);

            swal({
                title: "Are you sure?",
                text: "Union credits will be added",
                type: "info",
                showCancelButton: true,
                confirmButtonClass: "btn-primary",
                confirmButtonText: "Yes, Add!",
                closeOnConfirm: false,
                closeOnCancel: false,
                showLoaderOnConfirm: true
            }, function(isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url:this_.attr("action"),
                        type:this_.attr("method"),
                        data:this_.serialize(),
                        dataType:'json'
                    }).done(function (data) {
                        swal("PECO" , data.msg , data.func);
                        if(data.qry == true){
                            unionleavecredits();
                            $('#submitunion')[0].reset();
                        }
                    }).fail(function () {
                        PECO.phpError();
                    });
                }else{
                    swal.close();
                }
            });


        });

        $('a[href="#portlet_tab3"][data-toggle="tab"]').on('shown.bs.tab', function (e) {
            var creditsyears = $(document).find('#creditsyears').val();
            if(creditsyears == ''){
                fetch_emp_encoded_credits(creditsyears);
            }else{
                var d = new Date();
                var year = d.getFullYear();
                fetch_emp_encoded_credits(year);
            }
        });

        $(document).on('change' , '#creditsyears' , function () {
                var this_ = $(this);
                if(this_.val() != ''){
                    fetch_emp_encoded_credits(this_.val());
                }else{
                    fetch_emp_encoded_credits();
                }

        });

        $(document).on('click' , '#form_select_employee_for_leave_credits' , function () {

            var id = $('#creditsysid', document).val();
            var year = $('#yeartxt', document).val();
            var days = $('#nodays', document).val();
            var hours = $('#nohours', document).val();
            $('#btn_selected' , document).attr("data-arr" , id + ',' + year + ',' + days+ ',' + hours);
        });
        $(document).on('submit','#submitflexi' , function (e) {
            e.preventDefault();
            var this_ = $(this);

            swal({
                title: "Are you sure?",
                text: 'Add flexi credit',
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
                        url:this_.attr("action"),
                        type:this_.attr("method"),
                        data:this_.serialize(),
                        dataType:'json'
                    }).done(function (data) {
                        swal("PECO", data.msg, data.func);
                        if(data.qry == true){
                            $('#submitflexi')[0].reset();
                            $('#trntype').select2('val' , '');
                            $('#employee').select2('val' , '');
                            $('#expiration').select2('val' , '');
                        }
                    }).fail(function () {
                        PECO.phpError();
                        swal.close();
                    });
                }else{
                    swal.close();
                }
            });

        });
        $(document).on('change' , '#trntype' ,function () {
            var this_ = $(this);
            if(this_.val() == 1){
                $('#hourly',document).addClass('hidden');
                $('#day',document).removeClass('hidden');
            }else if(this_.val() == 2){
                $('#hourly',document).removeClass('hidden');
                $('#day',document).addClass('hidden');
            }
        });

        $(document).on('click' , '#saveleavetypebtn' , function (e) {
            e.preventDefault();
            var names = $(document).find('#names').val();
            var desc = $(document).find('#desc').val();
            if(names == '' || desc == ''){
                PECO.initAlerts("Please fill up the required fields" , "PECO" ,"info");
            }else{
                $.ajax({
                    url:PECO.base_url()+'hris/addeavetype',
                    type:"post",
                    data:{"names":names,"desc":desc},
                    dataType:"json"
                }).done(function (d) {
                    $('#leavetype').popover('hide');
                    PECO.initAlerts(d.msg , "PECO.net" , d.func);
                    loadleavecredits();
                }).fail(function () {
                    PECO.phpError();
                });
            }
        });
        $(document).on('click','#closepopover',function (e) {
            e.preventDefault();
            $('#leavetype').popover('hide');
        });
        $(document).on('click','#btn_applyall',function (e) {
            e.preventDefault();
            var selected = $(document).find('#creditsysid').val();
            var credits = $(document).find('#nodays').val();
            var year = $(document).find('#yeartxt').val();
            if(selected === '') {
                PECO.initAlerts("Please select credit types", "PECO.net", "info");
            }else if (credits === ''){
                PECO.initAlerts("Please specify No. of days","PECO.net","info");
            }else{
                $.SmartMessageBox({
                    title: "<i class='fa fa-question fa-fw fa-lg txt-color-yellow'></i>Are you sure you want to apply this credits to all employees?</span>",
                    content: 'Please confirm action taken!',
                    buttons: '[Yes][No]'
                }, function (ButtonPressed) {
                    if (ButtonPressed === "Yes") {
                        var credits = $(document).find('#nodays').val();
                        var year = $(document).find('#yeartxt').val();
                        var types = $(document).find('#creditsysid').val();
                        $.ajax({
                            url:PECO.base_url()+"hris/applycredits",
                            type:"post",
                            data:{"credits":credits , "year":year,"types":types},
                            dataType:"json"
                        }).done(function (d) {
                            PECO.initAlerts(d.msg , "PECO.net" , d.func);
                            $(document).find('#nodays').val("");
                            $(document).find('#yeartxt').val("");
                            $(document).find('#creditsysid').val("");
                            $(document).find('#creditsselected').text("");
                        }).fail(function () {
                            PECO.phpError();
                        });
                    }
                });
            }
        });

        $(document).on('click','#selectedbtn',function (e) {
            e.preventDefault();
            var credits = $(document).find('#nodays').val();
            var year = $(document).find('#yeartxt').val();
            var types = $(document).find('#creditsysid').val();
            if(types == ''){
                PECO.initAlerts("Please select leave type credits","PECO.net","info");
            }else if(year == ''){
                PECO.initAlerts("Year field is empty","PECO.net","info");
            }else if (credits == ''){
                PECO.initAlerts("No. of days field is empty","PECO.net","info");
            }else{
                $('#creditsselectedmodal').modal('show');
            }
        });
    };


    var loadleavecredits = function () {


        $.ajax({
            url:PECO.base_url()+"hris/fetchcredittypes",
            type:"post",
            dataType:"json",
            beforeSend: function(){
                leavecreditstype.dataTable().empty();
                PECO.DTphpLoading(leavecreditstype, 'Loading... ');
            }
        }).done(function (d) {
            leavecreditstype.dataTable().empty();
            leavecreditstype.dataTable({
                bDestroy: true,
                bPaginate: true,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                bProcessing: true,
                aaData: d.creditlist,
                aoColumns: [
                    {"data":"num" , sWidth:"7%"},
                    {"data":"types" , sWidth:"90%" , sClass:"types"},
                    {"data":"radio" , sWidth:"3%"}
                ],
                searchHighlight: true,
                fnRowCallback: function(nRow, aData) {
                  /*  $(nRow).find('.icheck').iCheck({
                        checkboxClass: 'icheckbox_flat-blue',
                        radioClass: 'iradio_flat-blue',
                        increaseArea: '20%' // optional
                    }); */
                }
            }).on('click', 'tr .radioselected', function (e) {
                var this_ = $(this);
                this_.attr('checked', true);

                var id = this_.attr("data-id");
                var year = $('#yeartxt', document).val();
                var days = $('#nodays', document).val();
                var hours = $('#nohours', document).val();
                $('#btn_selected' , document).attr("data-arr" , id + ',' + year + ',' + days+ ',' + hours);

                var creditsselected = this_.closest('tr').find('td.types').text();
                $(document).find('#creditsselected').text(creditsselected);
                $(document).find('#creditsysid').val(id);
            });
        }).fail(function () {
            PECO.phpError();
        });
    };

    var loadflexispent = function (empid) {
        $.ajax({
            url:PECO.base_url()+'hris/getflexispent',
            type:'post',
            data:{"empid" : empid},
            dataType:'json'
        }).done(function (data) {
            //totalincurredtbl
            $(document).find('#totalspent').text(data.total);
            totalincurredtbl.dataTable().empty();
            totalincurredtbl.dataTable({
                bDestroy: true,
                bPaginate: true,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                bProcessing: true,
                aaData: data.flexiincurreddata,
                aoColumns: [
                    {"data":"num" },
                    {"data":"fromdate"},
                    {"data":"todate"},
                    {"data":"fromtime"},
                    {"data":"totime"},
                    {"data":"leavedate"},
                    {"data":"datecreated"}
                ],
                searchHighlight: true,
            });
        }).fail(function () {
            PECO.phpError();
        });

    };

    var loadunionleavebalance = function (year) {
        $.ajax({
            url:PECO.base_url()+'hris/getunionbalance',
            type:'post',
            data:{"year" : year},
            dataType:'json'
        }).done(function (data) {

            unionbalancetbl.dataTable().empty();
            unionbalancetbl.dataTable({
                bDestroy: true,
                bPaginate: true,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                bProcessing: true,
                aaData: data.unionleavedata,
                aoColumns: [
                    {"data":"num" },
                    {"data":"credit"},
                    {"data":"year"},
                    {"data":"datecreated"},
                    {"data":"createdby"}
                ],
                searchHighlight: true,
            });
        }).fail(function () {
            PECO.phpError();
        });

    };

    var loadpendinguniontrn = function (empid , year) {
        $.ajax({
            url:PECO.base_url()+'hris/getpendinguniontrn',
            type:'post',
            data:{"empid" : empid , "year" : year},
            dataType:'json'
        }).done(function (data) {

            unionpendingtrntbl.dataTable().empty();
            unionpendingtrntbl.dataTable({
                bDestroy: true,
                bPaginate: true,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                bProcessing: true,
                aaData: data.unionpendingtrndata,
                aoColumns: [
                    {"data":"num" },
                    {"data":"fromdate"},
                    {"data":"todate"},
                    {"data":"fromtime"},
                    {"data":"totime"},
                    {"data":"total"},
                    {"data":"control"},
                ],
                searchHighlight: true,
            });
        }).fail(function () {
            PECO.phpError();
        });


    };

    var loaduniontrn  = function (year) {
        $.ajax({
            url:PECO.base_url()+'hris/getuniontrn',
            type:'post',
            data:{"year" : year},
            dataType:'json'
        }).done(function (data) {
            availedunion.dataTable().empty();
            availedunion.dataTable({
                bDestroy: true,
                bPaginate: true,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                bProcessing: true,
                aaData: data.uniondatatrn,
                aoColumns: [
                    {"data":"emp" },
                    {"data":"fromdate" },
                    {"data":"todate"},
                    {"data":"fromtime"},
                    {"data":"totime"},
                    {"data":"total"},
                    {"data":"leavedate"},
                    {"data":"year"},
                    {"data":"datecreated"},
                    {"data":"createdby"},
                ],
                searchHighlight: true,
            });
        }).fail(function () {
            PECO.phpError();
        });
    };

    var handler_leave_request = function(){
        var d = new Date();
        var yearnow = d.getFullYear();

        //PECO.select2Basic($('#yearleave',document),'systems/select2year','Select Year',false,false,false);
        PECO.select2Basic($('#yearprint',document),'systems/select2year','Select Year',false,false,yearnow);
        PECO.select2Basic($('#unionyear',document),'systems/select2year','Select Year',false,false,false);
        PECO.select2Basic($('#employeeselect2',document),'hris/leaveemployee','Select Employee',false,false,false);
        PECO.select2Basic($('#employeeprint',document),'hris/leaveemployee','Select Employee',false,false,false);
        PECO.select2Basic($('#leavetype',document),'hris/getleavetype','Select Leave Type',false,false,false);
        PECO.select2Basic($('#employee',document),'hris/getemployees','Select Employee',false,false,false);
        PECO.select2Basic($('#unionempname',document),'hris/getemployees','Select Employee',false,false,false);

        $(document).on('change','#unionyearsummary' , function () {
            var this_ = $(this).val();
            loaduniontrn(this_);
        });

        $(document).on('change','#employeeprint' , function () {
            var this_ = $(this);
            if(this_.val() != ''){
                fetchsupexec(this_.val());
            }
        });
        loaduniontrn();
        var fetchsupexec = function(empid){
            $.ajax({
                url:PECO.base_url()+'hris/getsupexec',
                type:'post',
                data:{"empid" : empid},
                dataType:'json'
            }).done(function (data) {

                if(data.head != '' || data.head != null){
                    PECO.select2Basic($('#supervisor',document),'hris/leaveemployee','Select Employee',false,false,data.head);
                }else{
                    alert("test");
                    PECO.select2Basic($('#supervisor',document),'hris/leaveemployee','Select Employee',false,false,false);
                }
                if(data.executive != '' || data.executive != null){
                    PECO.select2Basic($('#executive',document),'hris/leaveemployee','Select Employee',false,false,data.executive);
                }else{
                    alert("test");
                    PECO.select2Basic($('#executive',document),'hris/leaveemployee','Select Employee',false,false,false);
                }
            }).fail(function () {
                PECO.phpError();
            });
        };

        $(document).on('click','#saveunionpendingbtn' , function () {
            var this_ = $(this);
            var empid = this_.attr("data-empid");
            var year = this_.attr("data-year");

            if(empid != '' && year != ''){
                $.ajax({
                    url:PECO.base_url()+"hris/saveunionpendingtrn",
                    type:'post',
                    data:{"empid" : empid , "year" : year},
                    dataType:'json'
                }).done(function (data) {
                    swal("PECO" , data.msg , data.func);
                    if(data.qry == true){
                        loadunionleavebalance(data.year);
                        loadpendinguniontrn(data.empid , data.year);
                    }
                }).fail(function () {
                    PECO.phpError();
                });
            }else{

            }
        });

        $(document).on('click' , '#deletependinguniontrn' , function () {
            var this_ = $(this);
            var dataid = this_.attr("data-id");
            var unionempname = $(document).find('#unionempname').val();
            var unionyear  = $(document).find('#unionyear').val();
            if(unionempname != '' && unionyear != ''){
                $.ajax({
                    url:PECO.base_url()+'hris/deletependinguniontrn',
                    type:'post',
                    data:{"dataid" : dataid},
                    dataType:'json'
                }).done(function (data) {
                    PECO.initAlerts(data.msg , "PECO" , data.func);
                    if(data.qry == true){
                        loadunionleavebalance(unionyear);
                        loadpendinguniontrn(unionempname , unionyear);
                    }

                }).fail(function () {
                    PECO.phpError();
                });
            }else{
                PECO.initAlerts("Please select Employee/Year" , "PECO" , "info");
            }

        });

        $(document).on('change' , '#unionevent input' , function () {
            var unionempname = $(document).find('#unionempname').val();
            var unionyear  = $(document).find('#unionyear').val();
            if(unionempname != '' && unionyear != ''){
                $(document).find('#saveunionpendingbtn').attr("data-empid" , unionempname);
                $(document).find('#saveunionpendingbtn').attr("data-year" , unionyear);
                loadunionleavebalance(unionyear);
                loadpendinguniontrn(unionempname , unionyear);
            }
        });

        $(document).on('submit' , '#submitunionleave' , function (e) {
            e.preventDefault();
            var this_ = $(this);
            $.ajax({
                url:this_.attr("action"),
                type:this_.attr("method"),
                data:this_.serialize(),
                dataType:'json'
            }).done(function (data) {
                PECO.initAlerts(data.msg , "PECO" , data.func);
                if(data.qry == true){
                    loadunionleavebalance(data.year);
                    loadpendinguniontrn(data.empid , data.year);
                }
            }).fail(function () {
                PECO.phpError();
            });
        });

        $(document).on('click' , '#deleteleavedraft' , function () {
            var this_ = $(this);
            var dataid = this_.attr("data-id");
            var empid = $('#employeeselect2' , document).val();
            var year = $('#yearleave' , document).val();

            if(empid == '' || year == ''){
                PECO.initAlerts("Employee / Year is empty" , "PECO" , "info");
            }else{

                swal({
                    title: "Are you sure?",
                    text: 'Draft item will be deleted.',
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
                            url:PECO.base_url()+'hris/deleteleavedraft',
                            type:'post',
                            data:{"dataid" : dataid},
                            dataType:'json'
                        }).done(function (data) {
                            swal("PECO" , data.msg , data.func);
                            fetchdraftrequestleavetbl(empid , year)
                        }).fail(function () {
                            PECO.phpError();
                            swal.close();
                        });
                    }else{
                        swal.close();
                    }
                });



            }
        });

        $(document).on('click', '#deleteflexibtn' , function () {
                var this_ = $(this);
                var dataid = this_.attr("data-id");
            swal({
                title: "Are you sure?",
                text: 'Delete Transaction',
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
                        url:PECO.base_url()+'hris/deleteflexitrn',
                        type:'post',
                        data:{"dataid" : dataid},
                        dataType:'json'
                    }).done(function (data) {
                         swal("PECO" , data.msg , data.func);
                         if(data.qry == true){
                             loadflexipendingtrntable(dataid);
                         }
                    }).fail(function () {
                        PECO.phpError();
                        swal.close();
                    });
                }else{
                    swal.close();
                }
            });
        });

        $(document).on('click','#savependingflexitrn',function () {
            var this_ = $(this);
            var dataid = this_.attr("data-id");

            swal({
                title: "Are you sure?",
                text: "Flexi transaction will be save.",
                type: "error",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes, Save!",
                closeOnConfirm: false,
                closeOnCancel: false,
                showLoaderOnConfirm: true
            }, function(isConfirm) {
                if(isConfirm){
                    $.ajax({
                        url:PECO.base_url()+'hris/savependingflexitrn',
                        type:'post',
                        data:{"dataid" : dataid},
                        dataType:'json'
                    }).done(function (data) {
                        swal("PECO" , data.msg , data.func);
                        if(data.qry == true){
                            loadflexibalancetable(data.empid);
                            loadflexipendingtrntable(data.empid);
                            loadflexispent(data.empid);
                        }
                    }).fail(function () {
                        PECO.phpError();
                        swal.close();
                    });
                }else{
                    swal.close();
                }

            });



        });

        $(document).on('submit','#submitflexitrn',function (e) {
            e.preventDefault();
            var this_ = $(this);

            swal({
                title: "Are you sure?",
                text: "Add transaction.",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-primary",
                confirmButtonText: "Yes, Add!",
                closeOnConfirm: false,
                closeOnCancel: false,
                showLoaderOnConfirm: true
            }, function(isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url:this_.attr("action"),
                        type:this_.attr("method"),
                        data:this_.serialize(),
                        dataType:'json'
                    }).done(function (data) {
                       swal("PECO" , data.msg , data.func);
                        if(data.qry == true){
                            $('#submitflexitrn')[0].reset();
                            $('#employee').select2('val','');
                            $('#flexitype').select2('val','');
                            loadflexipendingtrntable(data.empid);
                            loadflexibalancetable(data.empid);
                        }
                    }).fail(function () {
                        PECO.phpError();
                    });
                }else{
                    swal.close();
                }
            });

        });

        $('#flexitype',document).select2({
            "allowClear":true
        });
        $(document).on('change','#uniontype',function () {
            var this_ = $(this);
            if(this_.val() == 1){
                $(document).find('#uniondate').removeClass('hidden');
                $(document).find('#uniontime').addClass('hidden');
            }else if(this_.val() == 2){
                $(document).find('#uniondate').addClass('hidden');
                $(document).find('#uniontime').removeClass('hidden');
            }
        });
        $(document).on('change','#flexitype',function () {
            var this_ = $(this);
            if(this_.val() == 1){
                $(document).find('#flexidate').removeClass('hidden');
                $(document).find('#flexitime').addClass('hidden');
            }else if(this_.val() == 2){
                $(document).find('#flexidate').addClass('hidden');
                $(document).find('#flexitime').removeClass('hidden');
            }
        });
        $(document).on('change','#employee' , function () {
            var this_ = $(this);
            if(this_.val() != ''){
                loadflexibalancetable(this_.val());
                loadflexipendingtrntable(this_.val());
                loadflexispent(this_.val())
                $(document).find('#savependingflexitrn').attr("data-id" , this_.val());
                $('#empflexibalance' , document).removeClass('hidden');
            }else{
                $('#empflexibalance' , document).addClass('hidden');
            }
        });

        /*

        $(document).on('change','#yearleave',function () {
            var this_ = $(this);
            var empval = $(document).find("#employeeselect2").val();

            if(this_.val() != ''){
                if(empval != ''){
                    $('#hiddenempid', document).val(empval);
                    PECO.select2Basic($('#selectleavetype2',document), 'request/getleavetype', 'Select Leave Type..', false, false, false , false,false ,  empval);
                    init_employee_leave_credits($('#list_leave_credits'), empval , this_.val());
                    $('#list_leave_credits').show();

                    fetchdraftrequestleavetbl( empval , this_.val());
                }else{
                    PECO.initAlerts("Please select employee" , "Employee" , "info");
                    $('#list_leave_credits').hide();
                }
            }else{
                $('#list_leave_credits').hide();
            }
        });
        */
        $(document).on('change','.filter-empyr input',function () {
             var this_ = $(this);
             var this_emp_val = $('#employeeselect2' , document).val();
             var this_year_val = $('#yearleave' , document).val();
             if(this_emp_val != '' && this_year_val != '') {
                 $('#hiddenempid', document).val(this_emp_val);
                 init_employee_leave_credits($('#list_leave_credits', document), this_emp_val, this_year_val);
                 $('#list_leave_credits', document).show();
                 fetchdraftrequestleavetbl(this_emp_val, this_year_val);
                 $('#draftpanel' , document).show();
                 PECO.select2Basic($('#selectleavetype2', document), 'request/getleavetype', 'Select Leave Type..', false, false, false, false, false, this_emp_val);
             }else{
                 $('#list_leave_credits', document).hide();
                 $('#hiddenempid', document).val('');
                 $('#draftpanel' , document).hide();
             }
        });

        $('#submitleaveform2').submit(function (e) {
            e.preventDefault();
            var this_ = $(this);
            var selectleavetype2 = $('#selectleavetype2',document).val();
            var reason = $('#reason',document).val();
            var fromdate = $('#fromdate2',document).val();
            var todate = $('#todate2',document).val();
            var nohours = $('#nohours2',document).val();
            var nodays = $('#nodays2',document).val();

            if(selectleavetype2 == ''){
                PECO.initAlerts("Please select leave type","PECO","info");
            }else if(reason == ''){
                PECO.initAlerts("Please provide reason of leaving","PECO","info");
            }else if((fromdate == '' || todate == '') && nohours == ''){
                PECO.initAlerts("Please provide date or hours of leaving","PECO","info");
            }else{
                $.SmartMessageBox({
                    title: "<i class='fa fa-question fa-fw fa-lg txt-color-yellow'></i>Add Transaction?</span>",
                    content: 'Please confirm action taken!',
                    buttons: '[Yes][No]'
                }, function (ButtonPressed) {
                    if (ButtonPressed === "Yes") {
                        $.ajax({
                            url: this_.attr("action"),
                            type: this_.attr("method"),
                            data: this_.serialize(),
                            dataType:"json"
                        }).done(function (d) {
                            PECO.initAlerts(d.msg, "Leave Form",d.func);
                            if(d.qry == true){

                                $('#selectleavetype2' , document).select2('val' , '');
                                $('#fromdate2' , document).val('');
                                $('#todate2', document).val('');
                                $('#fromhours', document).val('');
                                $('#fromminutes', document).val('');
                                $('#tohours', document).val('');
                                $('#tominutes', document).val('');
                                $('#leavetype' , document).select2('val' , '');

                                fetchdraftrequestleavetbl(d.empid , d.year);

                            }

                        }).fail(function () {
                            PECO.phpError();
                        });
                    }
                });
            }
        });
        $(document).on('click','#submitform' , function () {
            var employeeselect2 = $(document).find('#employeeselect2').val();
            var year = $(document).find('#yearleave').val();
            var remarks = $(document).find('#remarks').val();
            if(employeeselect2 == ''){
                PECO.initAlerts("Employee is empty" , "PECO" , "info");
            }else if(year == ''){
                PECO.initAlerts("Year is empty" , "PECO" , "info");
            }else{
                swal({
                    title: "Are you sure?",
                    text: "Leave form will be submitted",
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
                            url:PECO.base_url()+'hris/submitleaveform',
                            type:'post',
                            data:{"empid" : employeeselect2 , "year" : year ,"remarks" : remarks},
                            dataType:'json'
                        }).done(function (data) {
                            swal("PECO", data.msg, data.func);
                            if(data.qry  == true){
                                fetchdraftrequestleavetbl(employeeselect2 , year);
                                $('#yearleave').select2('val' , '');
                                init_employee_leave_credits($('#list_leave_credits', document), employeeselect2, year);
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

        var fetchdraftrequestleavetbl = function(empid , year){
            $.ajax({
                url:PECO.base_url()+'hris/fetchdraftrequested' ,
                type:'post',
                data:{"empid" : empid , "year" : year},
                dataType:'json'
            }).done(function (data) {

                $('#draftrequestleavetbl' , document).dataTable().empty();
                $('#draftrequestleavetbl' , document).dataTable({
                    bDestroy: true,
                    bPaginate: false,
                    bFilter: false,
                    bInfo: false,
                    bStateSave: true,
                    bProcessing: true,
                    aaData: data.draftrequesteddata,
                    aoColumns: [
                        {"data":"num"},
                        {"data":"leavetype"},
                        {"data":"fromdate"},
                        {"data":"todate"},
                        {"data":"fromtime"},
                        {"data":"totime"},
                        {"data":"type"},
                        {"data":"control"}
                    ],
                    searchHighlight: true
                });
            });
        };

        var init_employee_leave_credits = function(el, empid , year) {
            var totalminutes = 0;


            $.ajax({
                url: PECO.base_url() + 'hris/getemployeeleavecredits',
                type: 'post',
                dataType: 'json',
                data: {'empid': empid , "year" : year}
            }).done(function(d){

                el.html(d.html);
                $('.popovers').each(function(){
                    $(this).popover({
                        html: true,
                        animation: true,
                        template: '<div class="popover popover-info"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>'
                    });
                });
                var leavetype_val = $('#selectleavetype2',document).val();
                el.find('li').removeClass('.list-group-item-danger');
                $('#' + leavetype_val).addClass('list-group-item-danger');
                PECO.pulsateTarget($('#' + leavetype_val), 50, 3, 0.5, true, '#ff3838');
                if($('#selectleavetype2',document).val() == ''){
                    PECO.pulsateTarget($('#selectleavetype2').closest('div'), 50, 3, 0.5, true, '#62cbfc');
                }else{
                    if ($('#' + leavetype_val).length == 0) {
                        // alert("test");
                        //   PECO.initAlerts('Employee has no leave credit', 'PECO.net', 'info');
                        $(document).find('.leave-input input, .leave-input textarea').each(function () {
                            $(this).attr('disabled', true);
                        });
                    }else{
                        $(document).find('.leave-input input, .leave-input textarea').each(function () {
                            $(this).attr('disabled', false);
                        });
                    }
                }


                var date_from_val = $('#fromdate2').val();
                var date_to_val = $('#todate2').val();
                var nohours = $('#nohours2').val();
                var nominutes = $('#nominutes').val();
                var this_balance = $('#'+leavetype_val).find('.balance');
                var this_balance_val = this_balance.text();
                var this_spent = $('#'+leavetype_val).find('.spent');
                var this_spent_val = this_spent.text();
                if($('#selectleavetype2').select2('val') > 0) {

                    // if ($('#' + leavetype_val).length > 0) {

                    $(document).find('.leave-input input, .leave-input textarea').each(function () {
                        $(this).attr('disabled', false);
                    });
                    var numofdays = 0;
                    $.ajax({
                        cache: false,
                        url: PECO.base_url() + 'hris/computenumdays',
                        data: {'datefrom': date_from_val, 'dateto': date_to_val},
                        dataType: 'json',
                        type: 'post',
                        async: false
                    }).done(function (d) {
                        numofdays = d.numdays;
                        $('#nodays2', document).val(numofdays);
                    });



                    if (this_balance_val >= numofdays) {

                        var numofdaysperhours = numofdays * 24;

                        var numberofbalanceperhours =  0;
                        if(this_balance_val.indexOf('.') !== -1){

                            var balancearray = this_balance_val.split(".");
                            numberofbalanceperhours = Number((balancearray[0] * 24)) + Number(balancearray[1]);
                        }else{
                            numberofbalanceperhours =Number((this_balance_val * 24))
                        }

                        var finaltotalspent = Number(nohours) + Number(numofdaysperhours);
                        var new_balance = Number(numberofbalanceperhours - finaltotalspent);

                        if(new_balance < 0 || nohours > numberofbalanceperhours){


                            $(document).find('#nodays2').val('');
                            $(document).find('#fromdate2').val('');
                            $(document).find('#todate2').val('');
                            $(document).find('#nohours2').val('');
                            PECO.initAlerts("Insufficient leave credit balance", "PECO.net", "warning");
                        }else{

                            if(new_balance >= 24){

                                var remainder = new_balance % 24;
                                var fixbalance = new_balance / 24;
                                new_balance =  Math.floor(fixbalance) + '.'+remainder;
                            }else{
                                if(new_balance <= 0){
                                    new_balance = 0;
                                }else{
                                    new_balance = '.'+new_balance
                                }

                            }
                            this_balance.text(new_balance);

                            var spentarray = this_spent_val.split(".");
                            var daysspent = Number(spentarray[0])+Number(numofdays);
                            if(spentarray[1]){
                                var hoursspent = Number(spentarray[1])+Number(nohours);
                            }else{
                                var hoursspent = 0+Number(nohours);
                            }

                            var dayspentmin = daysspent * 24 * 60;
                            var hourspentmin  = hoursspent * 60;
                            var minutespent = 0;

                            var dayspenttext = 0;
                            var hoursspenttext = 0;
                            var minutespenttext = 0;
                            $.ajax({
                                url:PECO.base_url()+'hris/getleaveminutes',
                                type:'post',
                                data:{"empid" : empid},
                                dataType:'json'
                            }).done(function (data) {
                                minutespent = data.minutes;

                                var totalminutes = Number(dayspentmin)  + Number(hourspentmin) + Number(minutespent);
                                var totalinhours = totalminutes / 60;

                                dayspenttext  = totalinhours / 24;
                                hoursspenttext = totalinhours % 24;
                                minutespenttext = totalminutes % 60;


                                var newspent = Math.floor(dayspenttext) +'.'+ Math.floor(hoursspenttext) +'.'+minutespenttext ;
                                //  alert(newspent);
                                this_spent.text(newspent);
                            }).fail(function () {
                                PECO.phpError();
                            });


                        }
                    } else {
                        $(document).find('#nodays2').val('');
                        $(document).find('#fromdate2').val('');
                        $(document).find('#todate2').val('');
                        $(document).find('#nohours2').val('');
                        PECO.initAlerts("Insufficient leave credit balance", "PECO.net", "warning");
                    }




                    /*    } else {
                            PECO.initAlerts('Employee has no leave credit', 'PECO.net', 'info');
                            $(document).find('.leave-input input, .leave-input textarea').each(function () {
                                $(this).attr('disabled', true);
                            });
                        } */


                }
            }).fail(function(){
                PECO.phpError();
            });
        };
        $(document).on('submit','#printleaveform' , function (e) {
            e.preventDefault();
            var this_ = $(this);

            $.ajax({
                url:this_.attr("action"),
                type:this_.attr("method"),
                data:this_.serialize(),
                dataType:'json'
            }).done(function (data) {
                $(document).find('#viewallleavehist').attr("data-view" , data.empid);
                $(document).find('#viewallleavehist').attr("data-arr" , data.year+'-'+data.trntype);
                cancelledleavetbl.dataTable().empty();
                cancelledleavetbl.dataTable({
                    bDestroy: true,
                    bPaginate: true,
                    bFilter: true,
                    bInfo: true,
                    bStateSave: false,
                    bProcessing: true,
                    aaData: data.cancelledleave,
                    aoColumns: [
                        {"data":"expand"},
                        {"data":"createdby"},
                        {"data":"updatedby"},
                        {"data":"datecreated"},
                        {"data":"dateupdated"},
                        {"data":"print"},
                        {"data":"cancel"}
                    ],
                    searchHighlight: true
                });

                printleavetbl.dataTable().empty();
                printleavetbl.dataTable({
                    bDestroy: true,
                    bPaginate: true,
                    bFilter: true,
                    bInfo: true,
                    bStateSave: false,
                    bProcessing: true,
                    aaData: data.leaveempdata,
                    aoColumns: [
                        {"data":"expand"},
                        {"data":"createdby"},
                        {"data":"updatedby"},
                        {"data":"datecreated"},
                        {"data":"dateupdated"},
                        {"data":"print"},
                        {"data":"cancel"}
                    ],
                    searchHighlight: true,
                    fnRowCallback:function (nRow) {
                        $('.tooltips' , nRow).tooltip();
                    }
                });

            }).fail(function () {
                PECO.phpError();
            });
        });


        printleavetbl.on('click', '#btn-expand', function () {
            var this_ = $(this);
            var thisTr = this_.closest('tr');
            var thisTr_child = thisTr.children('td').length;
            var data_id = this_.attr('data-id');
            var trntype =  this_.closest('td').find('input').val();
            var clss_ = 'sub-table';
            var stat = 301;

            if (this_.hasClass('expanded') == false) {

                thisTr.next('#error').remove();
                this_.removeClass('fa-plus-square-o').addClass('fa-minus-square-o');
                $.ajax({
                    url: PECO.base_url()+'hris/getleavesub',
                    type: 'post',
                    data: {'id': data_id, 'inputs': trntype,"stat" :stat},
                    dataType: 'json',
                    beforeSend: function () {
                        thisTr.after('<tr id="loading" class="info " ><td colspan="' + thisTr_child + '" class=""><i class="fa fa-spinner fa-spin fa-pulse"></i> Loading..</td></tr>');

                    }
                }).done(function(d){
                    thisTr.after('<tr class="animated fadeIn fast compact '+d.func+'" id="details"><td colspan="' + thisTr_child + '" class="'+clss_+'">' + d.html + '</td></tr>');
                    printleavetbl.find('#loading').remove();
                }).fail(function(){
                    thisTr.after('<tr class="animated fadeIn fast compact danger" id="error"><td colspan="' + thisTr_child + '"><i class="fa fa-times"></i> Error PHP</td></tr>');
                    printleavetbl.find('#loading').remove();
                });
            } else {
                thisTr.next('#details').remove();
                thisTr.next('#error').remove();
                printleavetbl.find('#loading').remove();
                this_.removeClass('fa-minus-square-o').addClass('fa-plus-square-o');
            }
            this_.toggleClass('expanded');
            this_.closest('tr').toggleClass('expand-show');
        });



        cancelledleavetbl.on('click', '#btn-expand', function () {
            var this_ = $(this);
            var thisTr = this_.closest('tr');
            var thisTr_child = thisTr.children('td').length;
            var data_id = this_.attr('data-id');
            var trntype =  this_.closest('td').find('input').val();
            var clss_ = 'sub-table';
            var stat = 0;

            if (this_.hasClass('expanded') == false) {

                thisTr.next('#error').remove();
                this_.removeClass('fa-plus-square-o').addClass('fa-minus-square-o');
                $.ajax({
                    url: PECO.base_url()+'hris/getleavesub',
                    type: 'post',
                    data: {'id': data_id, 'inputs': trntype , "stat" :stat},
                    dataType: 'json',
                    beforeSend: function () {
                        thisTr.after('<tr id="loading" class="info " ><td colspan="' + thisTr_child + '" class=""><i class="fa fa-spinner fa-spin fa-pulse"></i> Loading..</td></tr>');

                    }
                }).done(function(d){
                    thisTr.after('<tr class="animated fadeIn fast compact '+d.func+'" id="details"><td colspan="' + thisTr_child + '" class="'+clss_+'">' + d.html + '</td></tr>');
                    cancelledleavetbl.find('#loading').remove();
                }).fail(function(){
                    thisTr.after('<tr class="animated fadeIn fast compact danger" id="error"><td colspan="' + thisTr_child + '"><i class="fa fa-times"></i> Error PHP</td></tr>');
                    cancelledleavetbl.find('#loading').remove();
                });
            } else {
                thisTr.next('#details').remove();
                thisTr.next('#error').remove();
                cancelledleavetbl.find('#loading').remove();
                this_.removeClass('fa-minus-square-o').addClass('fa-plus-square-o');
            }
            this_.toggleClass('expanded');
            this_.closest('tr').toggleClass('expand-show');
        });

        $(document).on('click','#printleaveformbtn' , function () {
            var this_ = $(this);
            var dataid = this_.attr("data-id");
            var empid = this_.attr("data-emp");
            var trntype = this_.attr("data-trntype");
            var printtype = this_.attr('data-printype');

            var supervisor = $('#supervisor', document).val();
            var executive = $('#executive', document).val();
            var year = $('#yearprint', document).val();
            var check = 0;
            if (document.getElementById('tempsupp').checked) {
                check = 1;
            } else {
                check = 0;
            }
            var colsultant = 0;
            if (document.getElementById('consultant').checked) {
                colsultant = 1;
            } else {
                colsultant = 0;
            }


                $.ajax({
                    url:PECO.base_url()+'hris/printleave',
                    type:'post',
                    data:{"id":dataid , "sup" : supervisor , "exec" : executive , "empid" :empid , "year" : year , "trntype" : trntype, "printtype": printtype , "check" : check,"consultant" : colsultant},
                    dataType:'json'
                }).done(function (data) {
                    leaveprint("Leave Form" , data.html);
                }).fail(function () {
                    PECO.phpError();
                });

        });

        $(document).on('click' , '#cancelleaveformbtn' , function () {
            var this_ = $(this);
            var dataempid = this_.attr("data-emp");
            var datatrnid = this_.attr("data-id");
            var trntype = this_.attr("data-trntype");
            swal({
                title: "Are you sure?",
                text: "Leave will be cancelled!",
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
                        url:PECO.base_url()+'hris/cancelleavetrn',
                        type:'post',
                        data:{"empid" : dataempid , "trnid" : datatrnid , "trntype" : trntype},
                        dataType:'json'
                    }).done(function (data) {
                        swal("PECO!", data.msg, data.func);
                        if(data.qry == true){
                            this_.parents("tr").remove();
                        }
                    }).fail(function () {
                        PECO.phpError();
                    });

                } else {
                    swal("Cancelled", "Leave cancellation canceled", "error");
                }
            });
        });
    };

    var loadflexipendingtrntable = function(empid){
        $.ajax({
            url:PECO.base_url()+'hris/getflexipendingtrn',
            type:'post',
            data:{"dataid" : empid},
            dataType:'json'
        }).done(function (data) {
            $(document).find('#totalpendingcredits').text(data.total);
            pendingflexitable.dataTable().empty();
            pendingflexitable.dataTable({
                bDestroy: true,
                bPaginate: true,
                bFilter: false,
                bInfo: false,
                bStateSave: true,
                bProcessing: true,
                aaData: data.flexipendingtrndata,
                aoColumns: [
                    {"data":"num"},
                    {"data":"fromdate"},
                    {"data":"todate"},
                    {"data":"fromtime"},
                    {"data":"totime"},
                    {"data":"total"},
                    {"data":"control"}
                ],
                searchHighlight: true
            });
        }).fail(function () {
            PECO.phpError();
        });
    };

    var loadflexibalancetable = function(empid){
        $.ajax({
            url:PECO.base_url()+'hris/getflexibalancerecord',
            type:'post',
            data:{"dataid" : empid},
            dataType:'json'
        }).done(function (data) {
            $(document).find('#totalflexicreditslabel').text(data.totalbalance);
            totalflexicredits.dataTable().empty();
            totalflexicredits.dataTable({
                bDestroy: true,
                bPaginate: true,
                bFilter: false,
                bInfo: false,
                bStateSave: true,
                bProcessing: true,
                aaData: data.empflexibalancedata,
                aoColumns: [
                    {"data":"num"},
                    {"data":"totalinminutes"},
                    {"data":"purpose"},
                    {"data":"expiry"},
                    {"data":"datecreated"}
                ],
                searchHighlight: true
            });
        }).fail(function () {
            PECO.phpError();
        });
    };

    var handler_workshift_list= function (){
        fetchworkshiftlist();
        fetchempworkshift();

        $(document).on('change','#empcurrentworkshift' , function () {
            var this_ = $(this);
            var dataid = this_.attr("data-id");
            if(this_.val() != ''){
                $.ajax({
                    url:PECO.base_url()+'hris/updateworkshift',
                    type:'post',
                    data:{"val" : this_.val(), "empid" : dataid},
                    dataType:'json'
                }).done(function (data) {
                    PECO.initAlerts(data.msg , "PECO" , data.func);
                    if(data.qry == true){
                        fetchempworkshift();
                    }
                }).fail(function () {
                    PECO.phpError();
                });
            }
        });

        $(".timepicker-default").each(function(){
            $(this).timepicker({
                autoclose: !0,
                showSeconds: !0,
                minuteStep: 1
            });
        });

        $(document).on('submit','#submitworkshift',function (e) {
            e.preventDefault();
            var this_ = $(this);
            $.ajax({
                url:this_.attr("action"),
                type:this_.attr("method"),
                data:this_.serialize(),
                dataType:'json'
            }).done(function (d) {
                PECO.initAlerts(d.msg, "PECO.net",d.func);
                if(d.qry === true){
                    fetchworkshiftlist();
                    $('#submitworkshift')[0].reset();
                }
            }).fail(function () {
                PECO.phpError();
            });
        });
    };

    var fetchempworkshift = function(){
        $.ajax({
            url:PECO.base_url()+'hris/getemployeeworkshift',
            type:'post',
            dataType:'json'
        }).done(function (data) {
            employeeworkshift.dataTable().empty();
            employeeworkshift.dataTable({
                bDestroy: true,
                bPaginate: true,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                bProcessing: true,
                aaData: data.empworkshiftdata,
                aoColumns: [
                    {"data":"num"},
                    {"data":"empname"},
                    {"data":"amstart"},
                    {"data":"amend"},
                    {"data":"pmstart"},
                    {"data":"pmend"},
                    {"data":"control"}
                ],
                searchHighlight: true,
                fnRowCallback: function (nRow, data) {
                    PECO.select2Basic($('#empcurrentworkshift', nRow), 'hris/getworkshifts','Select workshift',false,false,data.workshiftid);
                }
            });

        }).fail(function () {
            PECO.phpError();
        });
    };

    var fetchworkshiftlist = function () {
        $.ajax({
            url:PECO.base_url()+'hris/fetchworkshiftlist',
            type:'post',
            dataType:'json'
        }).done(function (d) {
            populate_workshiftlist(d);
        }).fail(function () {
            PECO.phpError();
        });
    };

    var populate_workshiftlist = function(d){
        workshifttable.dataTable().empty();
        workshifttable.dataTable({
            bDestroy: true,
            bPaginate: true,
            bFilter: true,
            bInfo: true,
            bStateSave: true,
            bProcessing: true,
            aaData: d.schedlist,
            aoColumns: [
                {"data":"num"},
                {"data":"codes"},
                {"data":"desc"},
                {"data":"logcnt"},
                {"data":"amstart"},
                {"data":"amend"},
                {"data":"pmstart"},
                {"data":"pmend"},
                {"data":"status"},
                {"data":"datecreated"}
            ],
            searchHighlight: true
        });
    };

    var handler_workshift_req = function(){
        PECO.select2Basic($('#monthdate',document), 'systems/select2month', 'Select Month...', false, false, false);
        $('#typedate',document).select2({
            'allowClear':true
        });
        fetchempcurrentworkshift();
    };

    var fetchempcurrentworkshift = function(){
        $.ajax({
            url:PECO.base_url()+'hris/getempcurrentworkshift',
            type:'post',
            dataType:'json'
        }).done(function (d) {
            groupworkshift.dataTable().empty();
            groupworkshift.dataTable({
                bDestroy: true,
                bPaginate: true,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                bProcessing: true,
                aaData: d.currentworkshiftdata,
                aoColumns: [
                    {"data":"num", sWidth:'5px'},
                    {"data":"name"},
                    {"data":"workshift" , sWidth:'60% !important'}
                ],
                searchHighlight: true,
                fnRowCallback: function(nRow, data, iDisplayIndex) {
                    PECO.select2Basic($('#currentworkshift' , nRow) , 'hris/getselect2workshift','Select Workshift',true,false,false);
                }
            });
        }).fail(function () {
            PECO.phpError();
        });

        $(document).on('change','#currentworkshift',function () {
            var this_ = $(this);
            var this_val = this_.val();
            var this_dataid = this_.attr("data-id");
            $.ajax({
                url:PECO.base_url()+'hris/insertworkshiftrequest',
                type:'post',
                data:{"workshiftid":this_val,"empid":this_dataid},
                dataType:'json'
            }).done(function (d) {
                PECO.initAlerts(d.msg,'PECO.net',d.func);
            }).fail(function () {
                PECO.phpError();
            });
        });

        $(document).on('click','#workshiftapprovalbtn',function () {
            var dt = new Date();
            var fromdate =   '';
            var todate =   '';
            var monthdate = $('#monthdate',document).val();
            var typdate = $('#typedate',document).val();
            var lastdayofmonth =  new Date(dt.getFullYear(), monthdate, 0).getDate();
            if( typdate== 1){
                fromdate = dt.getFullYear()+"-"+monthdate+"-"+1;
                todate = dt.getFullYear()+"-"+monthdate+"-"+15;

            }else if(typdate == 2){
                fromdate = dt.getFullYear()+"-"+monthdate+"-"+16;
                todate = dt.getFullYear()+"-"+monthdate+"-"+lastdayofmonth;
            }


            if(monthdate == '' || typdate == ''){
                PECO.initAlerts("No date provided.","PECO.net","info");
            }else{
                swal({
                    title: "Are you sure?",
                    text: "Workshift will be sent for approval.",
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
                            url:PECO.base_url()+'hris/sentworkshiftforapproval',
                            data:{"fromdate":fromdate , "todate":todate},
                            type:'post',
                            dataType:'json'
                        }).done(function (d) {
                            swal("Sent!", d.msg, d.func);
                        }).fail(function () {
                            PECO.phpError();
                        });

                    } else {
                        swal("Cancelled", "Processing canceled", "error");
                    }
                });
            }
        });


    };

    var personinfo = function () {

        $.ajax({
            url: PECO.base_url() + 'test/getpersondata',
            type: 'post',
            dataType: 'json'
        }).done(function (data) {
            console.log(data.persondata2);
            personalinfotbl.dataTable().empty();
            personalinfotbl.dataTable({
                bDestroy: true,
                bPaginate: true,
                bFilter: true,
                bInfo: true,
                bStateSave: false,
                bProcessing: true,
                aaData: data.persondata2,
                aoColumns: [
                    {"data":"num"},
                    {"data":"lastname"},
                    {"data":"firstname"},
                    {"data":"middlename"},
                    {"data":"address"},
                    {"data":"birthdate"},
                    {"data":"control",sWidth:'10px' }
                ],
                searchHighlight: true
            });
        });
    };

    var handler_attendance_approval = function(){
        fetchrequestattendance();
        personinfo();

        $(document).on('submit','#personentry',function (e) {
            e.preventDefault();
            var this_ = $(this);
          /*  ajax({
                url: this_.attr('action'),
                type: this_.attr('method'),
                data: this_.serialize(),
                dataType: 'json'
            }).done(function (data) {
                alert(data.msg);
            }); */
          $.ajax({
              url:this_.attr("action"),
              type:this_.attr("method"),
              data:this_.serialize(),
              dataType: 'json'
          }).done(function (data) {
              personinfo();
              alert(data.msg);
          }).fail(function () {
              alert("ERROR")
          });
        });

        $(document).on('click','#deleteinfo',function () {
           var this_ = $(this);
           var dataid = this_.attr("data-id");
           $.ajax({
               url: PECO.base_url()+'test/removeinfo',
               type: 'post',
               data:{"dataid":dataid},
               dataType: 'json'
           }).done(function (data) {
               personinfo();
               alert(data.msg);
           }).fail(function () {
               alert('Something went wrong.');
           });
        });


        $(document).on('click','#approveattbtn',function () {
            var this_ = $(this);
            var dataid = this_.attr("data-id");

            swal({
                title: "Are you sure?",
                text: "Attendance will be modified.",
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
                        url:PECO.base_url()+'hris/approveattendancereq',
                        type:'post',
                        data:{"dataid":dataid},
                        dataType:'json'
                    }).done(function (d) {
                        swal("Approved",d.msg, d.func);
                        if(d.qry == true){
                            fetchrequestattendance();
                        }
                    }).fail(function () {
                        PECO.phpError();
                    });
                } else {
                    swal("Cancelled", "Processing canceled", "error");
                }
            });
        });
        $(document).on('click','#disapproveattbtn',function () {
            var this_ = $(this);
            var dataid = this_.attr("data-id");

            swal({
                title: "Are you sure?",
                text: "Attendance request will be disapprove.",
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
                        url:PECO.base_url()+'hris/disapproveattendancereq',
                        type:'post',
                        data:{"dataid":dataid},
                        dataType:'json'
                    }).done(function (d) {
                        swal("Approved",d.msg, d.func);
                        if(d.qry == true){
                            fetchrequestattendance();
                        }
                    }).fail(function () {
                        PECO.phpError();
                    });
                } else {
                    swal("Cancelled", "Processing canceled", "error");
                }
            });
        });
    };

    var init_employee_payclass = function () {
        var employeepayclasstable = $(document).find('#employeepayclasstable');
        $.ajax({
            url:PECO.base_url()+'hris/getemployeepayclass',
            type:'post',
            dataType:'json'
        }).done(function (data) {
            employeepayclasstable.dataTable().empty();
            employeepayclasstable.dataTable({
                bDestroy: true,
                bPaginate: true,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                bProcessing: true,
                aaData: data.emppayclassdata,
                aoColumns: [
                    {"data":"num", sWidth:'5px'},
                    {"data":"lastname"},
                    {"data":"firstname"},
                    {"data":"dept"},
                    {"data":"payclass"},
                    {"data":"dateupdated"},
                ],
                searchHighlight: true,
                fnRowCallback: function(nRow, data, iDisplayIndex) {
                    //PECO.select2Basic($('#currentworkshift' , nRow) , 'hris/getselect2workshift','Select Workshift',true,false,false);
                }
            });
        }).fail(function () {
            PECO.phpError();
        });
    };

    return{
        init:function () {
            init_hrmaintenance();
        },
        initcurrentworkshift:function(){
            fetchempcurrentworkshift();
        },

        initleavecredits: function() {
            handler_leave_credits();
            handler_leave_credits_tiered();
        },
        initleavecreditsentry:function(){
            handler_leave_credits_entry();
        },
        initleavecreditsentrymodal:function(){
            handler_leave_credits_entry_modal();
        },
        initleaverequest:function(){
            handler_leave_request();
        },
        initworkshiftlist:function(){
            handler_workshift_list();
        },
        initworkshiftrequest:function(){
            handler_workshift_req();
        },
        init_attendance_approval:function(){
            handler_attendance_approval();
        },
        init_employee_payclass:function () {
            init_employee_payclass();
        },
    }
}();