var SHIFTSCHED = function(){

    PECO.getSelect2Plugins();
    PECO.getHighlightsPlugin();
    PECO.getiCheckPlugin();
    PECO.getSweetAlert();

    var tstableshift = $(document).find('#tstableshift');
    var shiftassignmenttable = $(document).find('#shiftassignmenttable');
    var loaded = false;
    //Team Shifting Select 2
    var typehalfassignment = $('#typehalfassignment',document);
    var monthassignment = $('#monthassignment',document);
    var typehalfshift = $('#typehalfshift',document);
    var monthshift = $('#monthshift',document);

    //Assignment Select 2
    var teamshifttype = $('#teamshifttype',document);
    var teamshiftmonth = $('#teamshiftmonth',document);
    var getdataschedsubmit = $('#getdatasched',document);

    var teamtable = $('#teamtable',document);
    var teamemp = $('#teamemp',document);
    var groupsched = $('#groupsched' , document);

    var branchestable = $('#branchestable',document);

    var operationemptbl = $('#operationemptbl',document);




    var fetchbranches = function(){
        $.ajax({
            url:PECO.base_url()+'ts/getbranches',
            type:'post',
            dataType:'json'
        }).done(function (data) {
            branchestable.dataTable().empty();
            branchestable.dataTable({
                bDestroy: true,
                bPaginate: false,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                bProcessing: true,
                aaData: data.brancheslist,
                searchHighlight: true,
                aoColumns:[
                    {"data":'num'},
                    {"data":'code'},
                    {"data":'desc'},
                    {"data":'address'},
                    {"data":'contactno'},
                    {"data":'control'}
                ]
            });
        }).fail(function () {
            PECO.phpError();
        });
    };

    var fetchdefaultschedofthemonth = function(month  , types){


        var d = new Date();
        var monthdata = (month > 0)? month : d.getMonth() + 1;
        var typedata = (types > 0 )? types : $('#typehalfshift',document).val();


        $.ajax({
            url:PECO.base_url()+'ts/getdatasched',
            type:"post",
            data:{"typedata":typedata,"month":monthdata},
            dataType:'json'
        }).done(function (d) {

            $('#tabledata',document).html(d.tabledata);

        }).fail(function () {
            PECO.phpError();
        });

    };

    var fetchtsreport  = function(){

        var monthts = $('#monthts' , document).val();
        var yearts = $('#yearts' , document).val();
        var typets = $('#typets' , document).val();

        var d = new Date();
        var defaultmonth = d.getMonth()+ 1;
        var defaultyear = d.getFullYear();
        var defaulttoday = d.getDate();

        if(monthts == '' || monthts == null){
            monthts = defaultmonth;
        }
        if(yearts == '' || yearts == null){
            yearts = defaultyear;
        }
        if(typets == '' || typets == null){
            if(defaulttoday > 15){
                typets  = 2;
            }else{
                typets = 1;
            }
        }


        $.ajax({
            url:PECO.base_url()+'ts/gettsreport',
            type:'post',
            data:{"month" : monthts , "year" : yearts , "type" : typets},
            dataType:'json'
        }).done(function (data) {
            $('#tstable' , document).html(data.html);
        }).fail(function () {
            PECO.phpError();
        });
    };


    var fetchemployee = function(dataid){

        $(document).on('click','#deleteemp' , function () {
            var this_ = $(this);
            var empdataid = this_.attr("data-id");
            $.ajax({
                url:PECO.base_url()+'hris/deletesbtsemp',
                type:'post',
                data:{"dataid" : empdataid},
                dataType:'json'
            }).done(function (data) {
                PECO.initAlerts(data.msg , "PECO" , data.func);
                if(data.qry == true){
                    fetchemployee(dataid);
                }
            }).fail(function () {
                PECO.phpError();
            });
            e.stopImmediatePropagation();
        });

        $(document).on('shown.bs.tab', 'a[data-toggle="tab"]', function (e) {
            var target = $(e.target).attr("href");
            if (target == '#ts') {
                PECO.select2Basic($('#monthts') , 'systems/select2month' , 'Month' , false, false,false);
                PECO.select2Basic($('#dayts') , 'ts/select2day' , 'Day' , false, false,false);
                PECO.select2Basic($('#yearts') , 'systems/select2year' , 'Year' , false, false,false);
                PECO.select2Basic($('#typets') , 'ts/gettypesched' , 'Type' , false, false,false);
                fetchtsreport();

                $(document).on('click' , '#deletesched' , function () {
                    var this_ = $(this);
                    var dataid = this_.attr("data-id");

                    swal({
                        title: "Are you sure?",
                        text: 'Schedule will be deleted',
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
                                url:PECO.base_url()+'ts/deletetssched',
                                type:'post',
                                data:{"schedid" : dataid},
                                dataType:'json'
                            }).done(function (data) {
                                swal("Success!", data.msg, data.func);
                            }).fail(function () {
                                PECO.phpError();
                            });

                        }
                    });


                });
            }
        });


        fetchsbtsemp(dataid);

    };

    var fetchsbtsemp = function (dataid) {
        $.ajax({
            url:PECO.base_url()+'ts/fetchoperationemp',
            type:'post',
            data:{"dataid":dataid},
            dataType:'json'
        }).done(function (data) {
            operationemptbl.dataTable().empty();
            operationemptbl.dataTable({
                bDestroy: true,
                bPaginate: false,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                bProcessing: true,
                aaData: data.empdata,
                searchHighlight: true,
                aoColumns:[
                    {"data":'num'},
                    {"data":'emp'},
                    {"data":'control'}
                ]
            });
        }).fail(function () {
            PECO.phpError();
        });
    };

    var main = function(branchid , shiftid  , yearassign , monthassign , dayassign){
        fetchdefaultschedofthemonth();
        fetchbranches();

        var dateobject = new Date();
        var monthdata = dateobject.getMonth() + 1;
        var day = new Date();
        var daydata = day.getDate();
        var daydatadefault = 1;
        if(daydata > 15){
            daydatadefault = 2;
        }else{
            daydatadefault = 1;
        }
        typehalfshift.select2({
            "allowClear":true
        });
        $('#emptype').select2({
            "allowClear":true
        });

        typehalfassignment.select2({
            "allowClear":true
        });
        typehalfassignment.select2("val",daydatadefault);
        teamshifttype.select2({
            "allowClear":true
        });
        teamshifttype.select2("val",daydatadefault);

        fetchcompanybrach();
        fetchassignteam();
        events(branchid , shiftid  , yearassign , monthassign , dayassign);
        PECO.select2Basic(monthassignment , 'ts/getmonth' , 'Month' , false,false, monthdata);
        PECO.select2Basic(monthshift , 'ts/getmonth' , 'Month' , false,false, monthdata);
        PECO.select2Basic(teamshiftmonth , 'ts/getmonth' , 'Month' , false,false, monthdata);
        PECO.select2Basic($('#employeelist') , 'hris/getemployees' , 'Select Employee' , false,false,false);
        PECO.select2Basic($('#typebranch') , 'ts/getbranchtype' , 'Select Type' , false,false,false);
        PECO.select2Basic($('#typeshift') , 'ts/getshiftype' , 'Select Shift' , false,false,false);
        PECO.select2Basic($('#weekday') , 'ts/getweekday' , 'Select Weekday' , false,false,false);
        fetchteamtableassign(branchid , shiftid  ,yearassign , monthassign , dayassign);
        fetchemptableassign(branchid , shiftid  ,yearassign , monthassign , dayassign);

    };

    var loadshiftingschedule = function(){
        var fromdate = $('#fromdate',document).val();
        var todate = $('#todate',document).val();
        $.ajax({
            url:PECO.base_url()+'ts/getdatasched',
            type:'post',
            data:{"fromdate":fromdate,"todate":todate},
            dataType:'json'
        }).done(function (d) {
            $('#tabledata',document).html = '';
            $('#tabledata',document).html(d.tabledata);
        }).fail(function () {
            PECO.phpError();
        });
    };


    var fetchemptableassign = function(branchid , shiftid  ,yearassign , monthassign , dayassign){
        $.ajax({
            url:PECO.base_url()+'hris/fetchemptableassign',
            type:'post',
            data:{"branchid":branchid,"shiftid":shiftid,"year":yearassign,"month":monthassign,"day":dayassign},
            dataType:'json'
        }).done(function (data) {
            teamemp.dataTable().empty();
            teamemp.dataTable({
                bDestroy: true,
                bPaginate: false,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                bProcessing: true,
                aaData: data.empdata,
                searchHighlight: true,
                aoColumns:[
                    {"data":'id'},
                    {"data":'empname'},
                    {"data":'control'}
                ],
                  fnRowCallback: function(nRow, data) {
                       PECO.iCheckRow($(nRow).find('input.icheck'), 'minimal', 'blue');
                }
            });
        }).fail(function () {
            PECO.phpError();
        });
    };

    var fetchteamtableassign = function(branchid , shiftid  ,yearassign , monthassign , dayassign){
        $.ajax({
            url:PECO.base_url()+'hris/fetchteamtableassign',
            type:'post',
            data:{"branchid":branchid,"shiftid":shiftid,"year":yearassign,"month":monthassign,"day":dayassign},
            dataType:'json'
        }).done(function (data) {
            teamtable.dataTable().empty();
            teamtable.dataTable({
                bDestroy: true,
                bPaginate: false,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                bProcessing: true,
                aaData: data.branchesdata,
                searchHighlight: true,
                aoColumns:[
                    {"data":'id'},
                    {"data":'team'},
                    {"data":'control'}
                ],
                fnRowCallback: function(nRow, data) {
                    PECO.iCheckRow($(nRow).find('input.icheck'), 'minimal', 'blue');
                }
            });
        }).fail(function () {
            PECO.phpError();
        });
    };

    var fetchassignteam = function(monthparam , typeparam, yearparam , typeshiftparam , weekdayparam){

        var dateobject = new Date();
        var month = dateobject.getMonth() + 1;
        var year = dateobject.getFullYear();
        var day = new Date();
        var daydata = day.getDate();
        if(daydata > 15){
            typehalf = 2;
        }else{
            typehalf = 1;
        }

        var  typeshiftval = $(document).find('#typeshift').val();
        var  weekdayval = $(document).find('#weekday').val();

        //get the current date
        var monthdata = (month) ? month : false;
        var typedata = (typehalf) ? typehalf : false;
        var yeardata = (year) ? year : false;
        var typeshift = (typeshiftparam) ? typeshiftparam : typeshiftval;
        var weekday = (weekdayparam) ? weekdayparam : weekdayval;

        if(monthparam > 0 && typeparam > 0 && yearparam > 0 && typeshiftparam > 0 && weekdayparam > 0){
            monthdata = monthparam;
            typedata = typeparam;
            yeardata = yearparam;
            typeshift = typeshiftparam;
            weekday = weekdayparam;
        }


    };
    var print = function(reptitle, content){
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
            '<style>body{margin: 0px 0px !important; font-family: arial; background: #fff;}</style>' +
            '</head>' +
            /*  '<img  style="display: inline-block; height: 80px; float: left; z-index: 2 !important; position: absolute; left: 0px;" src="' + PECO.base_url() + 'assets/global/img/PECO_LEFT_HEAD.png" /><img style="display: inline-block; height: 80px; width: 100%; position: absolute; top 0px; right: 0px; z-index: 0;" src="' + PECO.base_url() + 'assets/global/img/PECO_REP_HEAD.png" />' +
             '<h4 style="position: absolute; top: 50px; right: 0px; width: auto; text-align: right; padding-right: 10px">' + reptitle + '</h4>' +*/
            '<div style="position: absolute;  left: 0px; width: 100%;">' + content + '</div>';
        setTimeout(function () {
            //  win.print(); // blocking - so close will not
            //  win.close(); // execute until this is done
        }, 250);
    };

    var events = function(branchid , shiftid  , yearassign , monthassign , dayassign){

        $(document).on('submit' , '#updatetssched' , function (e) {
            e.preventDefault();
            var this_ = $(this);
            $.ajax({
                url:this_.attr("action"),
                type:this_.attr("method"),
                data:this_.serialize(),
                dataType:'json'
            }).done(function (data) {
                PECO.initAlerts(data.msg , "Trouble Shooter" , data.func);
                if(data.qry == true){
                    fetchtsreport();
                }
            }).fail(function () {
                PECO.phpError();
            });
        });

        $(document).on('submit','#submittssched' , function (e) {
            e.preventDefault();
            var this_ = $(this);
            swal({
                title: "Are you sure?",
                text: 'TS Schedule will be saved.',
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
                        swal("Success!", data.msg,  data.func);
                        if(data.qry == true){
                            fetchtsreport();
                        }
                    }).fail(function () {
                        PECO.phpError();
                    });
                }
            });



        });

        $(document).on('click','#filtertssched' , function () {
            var monthts = $('#monthts' , document).val();
            var yearts = $('#yearts' , document).val();
            var typets = $('#typets' , document).val();
            $.ajax({
                url:PECO.base_url()+'ts/gettsreport',
                type:'post',
                data:{"month" : monthts , "year" : yearts , "type" : typets},
                dataType:'json'
            }).done(function (data) {
                $('#tstable' , document).html(data.html);
            }).fail(function () {
                PECO.phpError();
            });
        });

        $(document).on('click','#tsreportbtn' , function () {
            var monthts = $('#monthts' , document).val();
            var yearts = $('#yearts' , document).val();
            var typets = $('#typets' , document).val();
            if(monthts != '' && yearts != '' && typets != ''){
                $.ajax({
                    url:PECO.base_url()+'ts/gettsreport',
                    type:'post',
                    data:{"month" : monthts , "year" : yearts , "type" : typets , "report" : true},
                    dataType:'json'
                }).done(function (data) {
                    print("Trouble Shooters Schedule" , data.html);
                }).fail(function () {
                    PECO.phpError();
                });
            }else{
                PECO.initAlerts("Please fill up Month/Year/Type" , "PECO" , "info");
            }

        });

        $(document).on('submit','#submitemptosched',function (e) {
            e.preventDefault();
            var this_ = $(this);

            swal({
                title: "Are you sure?",
                text: "you want to add this employee?.",
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
                        swal("PECO", data.msg, data.func);
                        $('#employeelist').select2('val' , '');
                        if(data.qry == true){
                            fetchemployee(data.userid);
                        }
                    }).fail(function () {
                        PECO.phpError();
                    });

                } else {
                    swal("Cancelled", "Processing canceled", "error");
                }
            });
        });
        $(document).on('click','#deletebranchbtn',function () {
            var this_ = $(this);
            var dataid = this_.attr("data-id");
            swal({
                title: "Are you sure?",
                text: "Branch will be deleted.",
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
                        url:PECO.base_url()+'ts/removebranch',
                        type:'post',
                        data:{"dataid" : dataid},
                        dataType:'json'
                    }).done(function (data) {
                        swal("PECO", data.msg, data.func);
                        if(data.qry == true){
                            fetchbranches();
                        }
                    }).fail(function () {
                        PECO.phpError();
                    });
                } else {
                    swal("Cancelled", "Processing canceled", "error");
                }
            });
        });

        $(document).on('click','#removeonschedbtn',function () {
            var this_  = $(this);
            var dataid = this_.attr("data-id");
            var month = $(document).find('#monthshift').val();
            var types =  $(document).find('#typehalfshift').val();

            swal({
                title: "Are you sure?",
                text: "Employee will be remove from the schedule.",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes, Remove!",
                closeOnConfirm: false,
                closeOnCancel: false,
                showLoaderOnConfirm: true
            }, function(isConfirm){
                if (isConfirm) {
                    $.ajax({
                        url:PECO.base_url()+'ts/removeonsched',
                        type:'post',
                        data:{"dataid" :dataid},
                        dataType:'json'
                    }).done(function (data) {
                        if(data.qry == true){
                            fetchdefaultschedofthemonth(month , types);
                        }
                        swal("PECO",data.msg, data.func);
                    }).fail(function () {
                        PECO.phpError();
                    });
                } else {
                    swal("Cancelled", "Processing canceled", "error");
                }
            });

        });

        $(document).on('click','#reportschedbtn',function () {
            var month = $(document).find('#monthshift').val();
            var types = $(document).find('#typehalfshift').val();
            $.ajax({
                url:PECO.base_url()+'ts/getdatasched',
                type:'post',
                data:{"month":month , "typedata":types ,"report":true},
                dataType:'json'
            }).done(function (data) {
                PECO.pecoRepPrint("SUBSTATION OPERATION SCHEDULE FROM "+data.fromdate+" TO "+data.todate , data.tabledata , false);
            }).fail(function () {
                PECO.phpError();
            });
        });
        
        $(document).on('submit','#submitbranch',function (e) {
            e.preventDefault();
            var this_ = $(this);

            swal({
                title: "Are you sure?",
                text: "Branch will be added.",
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
                        swal("PECO", data.msg, data.func);
                        if(data.qry == true){
                            fetchbranches();
                            $('#submitbranch')[0].reset();
                        }
                    }).fail(function () {
                        PECO.phpError();
                    });
                } else {
                    swal("Cancelled", "Processing canceled", "error");
                }
            });

        });

        teamtable .on('ifChecked', '.icheck', function(e){

            var this_ = $(this);
            var teamid = this_.attr("data-id");
            $.ajax({
                url:PECO.base_url()+'hris/assignteamsched',
                type:'post',
                data:{"teamid":teamid , "branchid":branchid, "shiftid":shiftid,"year":yearassign,"month":monthassign,"day":dayassign},
                dataType:'json'
            }).done(function (d) {
                PECO.initAlerts(d.msg,"PECO.net",d.func);
                if(d.qry== true){
                    loadshiftingschedule();
                }
            }).fail(function () {
                PECO.phpError();
            });

        }).on('ifUnchecked','.icheck',function (e) {
            var this_ = $(this);
            var teamid = this_.attr("data-id");
            $.ajax({
                url:PECO.base_url()+'hris/unassignteamsched',
                type:'post',
                data:{"teamid":teamid , "branchid":branchid, "shiftid":shiftid,"year":yearassign,"month":monthassign,"day":dayassign},
                dataType:'json'
            }).done(function (d) {
                PECO.initAlerts(d.msg , "PECO.net",d.func);
                if(d.qry == true){
                    loadshiftingschedule();
                }
            }).fail(function () {
                PECO.phpError();
            });
        });

        teamemp.on('ifChecked', '.icheck', function(){
            var this_ = $(this);
            var empid = this_.attr("data-id");
            $.ajax({
                url:PECO.base_url()+'hris/assignempsched',
                type:'post',
                data:{"empid":empid , "branchid":branchid, "shiftid":shiftid,"year":yearassign,"month":monthassign,"day":dayassign},
                dataType:'json'
            }).done(function (d) {
                PECO.initAlerts(d.msg,"PECO.net",d.func);
                loadshiftingschedule();
            }).fail(function () {
                PECO.phpError();
            });
        }).on('ifUnchecked','.icheck',function () {
            var this_ = $(this);
            var empid = this_.attr("data-id");
            $.ajax({
                url:PECO.base_url()+'hris/unassignempsched',
                type:'post',
                data:{"empid":empid,"year":yearassign,"month":monthassign,"day":dayassign},
                dataType:'json'
            }).done(function (d) {
                PECO.initAlerts(d.msg,"PECO.net",d.func);
                loadshiftingschedule();
            }).fail(function () {
                PECO.phpError();
            });
        });


        $(document).keypress(function(e) {
            var key = (e.keyCode ? e.keyCode : e.which);
            if(key==27) {

                $('td.days input').each(function(){
                    $(this).select2('destroy');
                    $(this).attr('type', 'hidden');
                });
                var dateobject = new Date();
                var teamshiftmonth = $(document).find('#teamshiftmonth').val();
                var teamshifttype = $(document).find('#teamshifttype').val();
                var year = dateobject.getFullYear();
                fetchcompanybrach(teamshiftmonth,teamshifttype,year);
            }
        });
        shiftassignmenttable.on('ifChecked', '.icheck', function(e){
            var this_ = $(this);
            var data_id = this_.attr("data-id");
            var data_team = this_.attr("data-team");

            swal({
                title: "Are you sure?",
                text: "Employee will be assign to this team.",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes, Process!",
                closeOnConfirm: false,
                closeOnCancel: false,
                showLoaderOnConfirm: true
            }, function(isConfirm){
                if (isConfirm) {
                    //press to check
                    updateteamassignment(data_id , data_team , 1);
                } else {
                    this_.iCheck('uncheck');
                    swal.close();
                }
            });
        }).on('ifUnchecked', '.icheck', function(e) {
            var this_ = $(this);
            var data_id = this_.attr("data-id");
            var data_team = this_.attr("data-team");

           swal({
                title: "Are you sure?",
                text: "Employee will be remove in this team.",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes, Process!",
                closeOnConfirm: false,
                closeOnCancel: false,
                showLoaderOnConfirm: true
            }, function(isConfirm){
                if (isConfirm) {
                    //press to uncheck
                    updateteamassignment(data_id , data_team , 0);
                } else {
                    this_.iCheck('check');
                    swal.close();
                }
            });

        });

        $(document).on('submit','#frm_save_shifts',function (e) {
            e.preventDefault();
            var form = $(this);
            $.ajax({
                url:form.attr('action'),
                type:form.attr('method'),
                data:form.serialize(),
                dataType:'json'
            }).done(function (d) {
                alert("test");
            }).fail(function () {
                PECO.phpError();
            });
        });

        $(document).on('click','ul li a[href="#assignment"]',function (e) {
            var dateobject = new Date();
            var teamshiftmonth = $(document).find('#monthassignment').val();
            var teamshifttype = $(document).find('#typehalfassignment').val();
            var year = dateobject.getFullYear();
            fetchassignteam(teamshiftmonth , teamshifttype , year);
        });

        $(document).on('click','ul li a[href="#shifting"]',function (e) {
            var dateobject = new Date();
            var teamshiftmonth = $(document).find('#teamshiftmonth').val();
            var teamshifttype = $(document).find('#teamshifttype').val();
            var year = dateobject.getFullYear();
            //fetchcompanybrach(teamshiftmonth,teamshifttype,year);
            fetchdefaultschedofthemonth(4 , 2);
        });

        $(document).on('change','#teamshifttype',function () {
            var dateobject = new Date();
            var teamshiftmonth = $(document).find('#teamshiftmonth').val();
            var teamshifttype = $(document).find('#teamshifttype').val();
            var year = dateobject.getFullYear();
            fetchcompanybrach(teamshiftmonth,teamshifttype,year);
        });
        $(document).on('change','#teamshiftmonth',function () {
            var dateobject = new Date();
            var teamshiftmonth = $(document).find('#teamshiftmonth').val();
            var teamshifttype = $(document).find('#teamshifttype').val();
            var year = dateobject.getFullYear();
            fetchcompanybrach(teamshiftmonth,teamshifttype,year);
        });

        $(document).on('change','#monthassignment',function () {
            var dateobject = new Date();
            var teamshiftmonth = $(document).find('#monthassignment').val();
            var teamshifttype = $(document).find('#typehalfassignment').val();
            var typeshift = $(document).find('#typeshift').val();
            var weekday = $(document).find('#weekday').val();
            var year = dateobject.getFullYear();
            fetchassignteam(teamshiftmonth , teamshifttype , year , typeshift , weekday);
        });
        $(document).on('change','#typehalfassignment',function () {
            var dateobject = new Date();
            var teamshiftmonth = $(document).find('#monthassignment').val();
            var teamshifttype = $(document).find('#typehalfassignment').val();
            var typeshift = $(document).find('#typeshift').val();
            var weekday = $(document).find('#weekday').val();
            var year = dateobject.getFullYear();
            fetchassignteam(teamshiftmonth , teamshifttype , year, typeshift, weekday);
        });

        $(document).on('change','#typeshift' , function () {
            var dateobject = new Date();
            var teamshiftmonth = $(document).find('#monthassignment').val();
            var teamshifttype = $(document).find('#typehalfassignment').val();
            var typeshift = $(document).find('#typeshift').val();
            var weekday = $(document).find('#weekday').val();
            var year = dateobject.getFullYear();
            fetchassignteam(teamshiftmonth , teamshifttype , year , typeshift, weekday);
        });

        $(document).on('change','#weekday' , function () {
            var dateobject = new Date();
            var teamshiftmonth = $(document).find('#monthassignment').val();
            var teamshifttype = $(document).find('#typehalfassignment').val();
            var typeshift = $(document).find('#typeshift').val();
            var weekday = $(document).find('#weekday').val();
            var year = dateobject.getFullYear();
            fetchassignteam(teamshiftmonth , teamshifttype , year , typeshift, weekday);
        });


        $(document).on('click','#removeteamshift',function () {
            var this_ = $(this);
            var teamid = this_.attr("data-teamid");
            var day = this_.attr("data-day");
            var branch = this_.attr("data-branch");
            var typehalf = this_.attr("data-type");

            swal({
                title: "Are you sure you want to removed this team?",
                text: 'Remove Team',
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
                        url:PECO.base_url()+'ts/removeteamshift',
                        type:'post',
                        data:{"teamid":teamid,"day":day,"branch":branch,"typehalf":typehalf},
                        dataType:'json'
                    }).done(function () {
                        PECO.initAlerts("Team has been removed.","PECO.net","success");
                        var dateobject = new Date();
                        var teamshiftmonth = $(document).find('#teamshiftmonth').val();
                        var teamshifttype = $(document).find('#teamshifttype').val();
                        var year = dateobject.getFullYear();
                        fetchcompanybrach(teamshiftmonth,teamshifttype,year);
                        swal.close();
                    }).fail(function () {
                        PECO.phpError();
                    });
                }else{
                    swal.close();
                }
            });
        });
        getdataschedsubmit.submit(function (e) {
            e.preventDefault();
            var this_ = $(this);
            $.ajax({
                url:this_.attr("action"),
                type:this_.attr("method"),
                data:this_.serialize(),
                dataType:'json',
                beforeSend: function(){
                  //  $('#tabledata',document).dataTable().empty();
                 //   PECO.DTphpLoading($('#tabledata',document), 'Loading... ');
                }
            }).done(function (d) {
                $('#tabledata',document).html(d.tabledata);
            }).fail(function () {
                PECO.phpError();
            });
        });
    };

    var updateteamassignment = function (dataid , datateam , status) {
        var dateobject = new Date();
        var teamshiftmonth = $(document).find('#monthassignment').val();
        var teamshifttype = $(document).find('#typehalfassignment').val();
        var typeshift = $(document).find('#typeshift').val();
        var weekday = $(document).find('#weekday').val();

        var year = dateobject.getFullYear();
        var day = dateobject.getDate();

        $.ajax({
            url:PECO.base_url()+'hris/updateteamassignment',
            type:'post',
            data:{"dataid":dataid,"datateam":datateam,"status":status , "month":teamshiftmonth , "type":teamshifttype , "year":year,"day":day , "typeshift" : typeshift , "weekday" : weekday},
            dataType:'json'
        }).done(function (d) {
            if(status == 0){
               swal("PECO.net", "Employee has been removed to the group.", "success");
            }else{
                swal("PECO.net",d.msg,d.func);
            }
        }).fail(function () {
            PECO.phpError();
        });
    };



    var fetchcompanybrach = function(monthparam,typeparam , yearparam){
        var dateobject = new Date();
        var month = dateobject.getMonth() + 1;
        var year = dateobject.getFullYear();
        var day = new Date();
        var daydata = day.getDate();
        var defaulthalf = 1;
        if(daydata > 15){
            defaulthalf = 2;
        }else{
            defaulthalf = 1;
        }
        var monthdata = (month) ? month : false;
        var typedata = (defaulthalf) ? defaulthalf : false;
        var yeardata = (year) ? year : false;
        if(monthparam > 0 && typeparam > 0 && yearparam > 0){
            monthdata = monthparam;
            typedata =typeparam;
            yeardata = yearparam;
        }

        $.ajax({
            url:PECO.base_url()+'hris/getcompanybranches',
            type:'post',
            data:{"month":monthdata , "type":typedata,"year":yeardata},
            dataType:'json',
            beforeSend: function(){
                tstableshift.dataTable().empty();
                PECO.DTphpLoading(tstableshift, 'Loading... ');
            }
        }).done(function (d) {
            populatebranch(d);
            loaded = true;
        }).fail(function () {
            PECO.phpError();
        });
    };

    var populatebranch = function (d) {
        tstableshift.dataTable().empty();
        tstableshift.dataTable({
            bDestroy: true,
            bPaginate: false,
            bFilter: true,
            bInfo: true,
            bStateSave: true,
            bProcessing: true,
            aaData: d.shiftdata,
            language: {
                searchPlaceholder: "Search records"
            },
            aoColumns: [
                {"data":"num",sClass:'5px!important'},
                {"data":"brach"},
                {"data":"time"},
                {"data":"mon", sClass:"mon days"},
                {"data":"tue", sClass:"tue days"},
                {"data":"wed", sClass:"wed days"},
                {"data":"thu", sClass:"thu days"},
                {"data":"fri", sClass:"fri days"},
                {"data":"sat", sClass:"sat days"},
                {"data":"sun", sClass:"sun days"},
                {"data":"control"}
            ],
            searchHighlight: true
        }).on('click', 'tr #btn_edit_shift', function(e) {

           var this_ = $(this);
           var this_tr = this_.closest('tr');

            $('td.days input').each(function(){
                $(this).select2('destroy');
                $(this).attr('type', 'hidden');
            });

            ('#mondayselect', this_tr).attr('type', 'text');
            ('#tuesdayselect', this_tr).attr('type', 'text');
            ('#wednesdayselect', this_tr).attr('type', 'text');
            ('#thursdayselect', this_tr).attr('type', 'text');
            ('#fridayselect', this_tr).attr('type', 'text');
            ('#saturdayselect', this_tr).attr('type', 'text');
            ('#sundayselect', this_tr).attr('type', 'text');
            var teamshiftmonth = $(document).find('#teamshiftmonth').val();
            var teamshifttype = $(document).find('#teamshifttype').val();
            PECO.select2BasicMult($('#mondayselect', this_tr), 'hris/getteamassign', false , teamshiftmonth , teamshifttype);
            PECO.select2BasicMult($('#tuesdayselect', this_tr), 'hris/getteamassign', false, teamshiftmonth , teamshifttype);
            PECO.select2BasicMult($('#wednesdayselect', this_tr), 'hris/getteamassign', false, teamshiftmonth , teamshifttype);
            PECO.select2BasicMult($('#thursdayselect', this_tr), 'hris/getteamassign', false, teamshiftmonth , teamshifttype);
            PECO.select2BasicMult($('#fridayselect', this_tr), 'hris/getteamassign', false, teamshiftmonth , teamshifttype);
            PECO.select2BasicMult($('#saturdayselect', this_tr), 'hris/getteamassign', false, teamshiftmonth , teamshifttype);
            PECO.select2BasicMult($('#sundayselect', this_tr), 'hris/getteamassign', false, teamshiftmonth , teamshifttype);
            $('.select2-container' , this_tr).css("width","90px");
            $('.select2-container' , this_tr).css("width","90px");
            $('.select2-container' , this_tr).css("width","90px");
            $('.select2-container' , this_tr).css("width","90px");
            $('.select2-container' , this_tr).css("width","90px");
            $('.select2-container' , this_tr).css("width","90px");
            $('.select2-container' , this_tr).css("width","90px");
            this_tr.css("height", "30px");
        }).on('keypress', 'tr td.days', function(e){
            var this_ = $(this);
            var this_input = $('input.input-days', this_);
            var this_input_val = this_input.select2('val');
            var datashift = this_input.attr("data-shift");
            var databranch = this_input.attr("data-branch");
            var dataclass = this_input.attr("data-class");
            var key = (e.keyCode ? e.keyCode : e.which);
            var month = $(document).find('#teamshiftmonth').val();
            var type = $(document).find('#teamshifttype').val();
            var dateobject = new Date();
            var year = dateobject.getFullYear();
            var day = new Date();
            var daydata = day.getDate();
            if(key==9) {
                if(month == '' || type == ''){
                    PECO.initAlerts("Month/Type is empty","PECO.net","warning");
                    d.stopImmediatePropagation();
                    d.preventDefault();
                }else{
                    if(!this_input_val.length == 0){
                        save_team_assignment(this_input_val , datashift , databranch  , dataclass , month , type,year,daydata);
                        d.stopImmediatePropagation();
                        d.preventDefault();
                        d.stopPropagation();
                    }else{
                        PECO.initAlerts("Team is empty,please assign employee to proceed","PECO.net","warning");
                        d.stopImmediatePropagation();
                        d.preventDefault();
                    }
                }
            }
        });
    };

    var save_team_assignment = function(teamarr , timeshift , branchid , dataclass , month , type,year,daydata) {
            $.ajax({
                url:PECO.base_url()+"ts/saveteamshiftingassignment",
                type:'post',
                data:{"teamarr":teamarr,"teamshift":timeshift,"branchid":branchid ,"day":dataclass,"month":month,"type":type,"year":year,"daydata":daydata},
                dataType:'json'
            }).done(function (d) {
                PECO.initAlerts(d.msg, "PECO.net" , d.func);
            }).fail(function () {
                PECO.phpError()
            });
    };

    var populatebranches = function(dayofweek , fromdate , todate){
        $.ajax({
            url:PECO.base_url()+'hris/getbranchesandworkshift',
            type:'post',
            data:{"dayofweek":dayofweek,"fromdate":fromdate , "todate":todate},
            dataType:'json'
        }).done(function (d) {
            groupsched.dataTable().empty();
            groupsched.dataTable({
                bDestroy: true,
                bPaginate: false,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                bProcessing: true,
                aaData: d.branchdata,
                language: {
                    searchPlaceholder: "Search branch"
                },
                aoColumns: [
                    {"data":"num",sClass:'5px!important'},
                    {"data":"branch",sClass:'branchclass'},
                    {"data":"control"}
                ],
                searchHighlight: true,
                fnRowCallback: function(nRow, aData) {
                    $(nRow).find('.icheck').iCheck({
                        checkboxClass: 'icheckbox_flat-blue',
                        radioClass: 'iradio_flat-blue',
                        increaseArea: '20%' // optional
                    });
                }
            }).on('ifChecked', '.icheck', function (e) {
                var this_ = $(this);
                var databranchid = this_.attr("data-branchid");
                var dataworkshiftid = this_.attr("data-workshiftid");
                var this_tr = this_.closest('tr');
                var itemdesc = this_tr.find('td.branchclass').text();
                $('#itemdesc' , document).text(itemdesc);
                $(document).find('#branchidhidden').val(databranchid);
                $(document).find('#workshiftidhidden').val(dataworkshiftid);
            });
        }).fail(function () {
            PECO.phpError();
        });
    };

    var init_empschedgroup = function(dayofweek , fromdate , todate){
        populatebranches(dayofweek , fromdate , todate);
        $(document).on('submit','#submitschedule',function (e) {
            e.preventDefault();
            if($('#branchidhidden').val() == '' || $('#workshiftidhidden').val() == ''){
                PECO.initAlerts("No selected Branch","PECO.net","error");
                e.stopImmediatePropagation();
            }else{
                if($('#sbtsemployee').val() == '' && $('#sbtsteam').val() == ''){
                    PECO.initAlerts("No selected Team/Employee","PECO.net","error");
                    e.stopImmediatePropagation();
                }else{
                    var this_  = $(this);
                    $.ajax({
                        url:this_.attr("action"),
                        type:this_.attr("method"),
                        data:this_.serialize(),
                        dataType:'json'
                    }).done(function (d) {
                        PECO.initAlerts(d.msg , "PECO.net" , d.func);

                        $('#sbtsemployee').select2('val','');
                        fetchdefaultschedofthemonth();
                    }).fail(function () {
                        PECO.phpError();
                    });
                    e.stopImmediatePropagation();
                }
            }
        });


    };

    return{
        init:function(branchid , shiftid , year , month , day){
            main(branchid , shiftid , year , month , day);
        },
        groupsched:function(dayofweek , fromdate , todate){
            init_empschedgroup(dayofweek , fromdate , todate);
        },
        empoperationtbl:function(dataid){
            fetchemployee(dataid);
        }
    }
}();