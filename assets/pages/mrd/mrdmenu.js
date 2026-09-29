

var MRDMENU = function(){

    // initialize plugins
    PECO.getHighlightsPlugin();
    PECO.getSelect2Plugins();

    //Accomp Entry
    var get_employee = $('#get_employee');
    var select_employee = $('#select_employee');
    var select_gdlb = $('#select_gdlb');
    var accomplishment_table = $('#accomplishment_table');
    var frm_add_accomplishment = $('#frm_add_accomplishment');

    //Accomplishment
    var meter_reader_table = $('#meter_reader_table');

    var accomplishment_entry = function(){

        $(document).on("keydown",'#reading_count',function (e) {
            var this_ = $(this);
            if(e.keyCode==13){
                 var employee = $('#select_employee').val();
                 var gdlb = $('#select_gdlb').val();
                 var daterearding = $('#date_reading').val();
                 var readcount = this_.val();

                $.ajax({
                    url:base_url+'hris/addreadingaccomplishment',
                    type:'post',
                    data:{"select_employee":employee,  "select_gdlb":gdlb , "reading_count":readcount , "date_reading":daterearding},
                    dataType:'json'
                }).done(function (d) {

                    if(d.qry === true){
                        PECO.initAlerts(d.msg, 'Registered', d.func);
                        tbl_accomplishment_refresh();
                        select_gdlb.select2("val","");
                        $(document).find('#reading_count').val('');
                        select_gdlb.select2('open');
                    }else{
                        PECO.initAlerts(d.msg, 'PECO.net', d.func);
                    }
                }).fail(function () {
                    PECO.phpError();
                });

            }
        });

        PECO.select2Basic(get_employee,'hris/get_employee_username/164' , 'Get Employee' , false , false,false);
        PECO.select2Basic(select_employee, 'hris/get_employee_username/164', 'Select Employee..', false, false, false);
        PECO.select2Basic(select_gdlb, 'hris/get_gdlb', '', false, false, false);

        //preventing non numeric inputs
        $("#reading_count").keypress(function (e) {
            if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
                return false;
            }
        });

        $(document).on('click' , '#submit_payroll_btn' , function (e) {
            e.preventDefault();
                var payrollstart = $(document).find('#payrollstart').val();
                var payrollend = $('#payrollend').val();
                //not valid date
                if(payrollstart > payrollend){
                    PECO.initAlerts("Invalid From - To Date", 'Invalid', 'info');
                }else{
                    if(payrollstart==='' || payrollend===''){
                        PECO.initAlerts("Please provide date for payroll start and payroll end", 'Empty', 'info');
                    }else{
                            $.SmartMessageBox({
                                title: "<i class='fa fa-question fa-fw fa-lg txt-color-yellow'></i>Are you sure you want to submit all the accomplishments?</span>",
                                content: 'Note! Payroll can be reverted via administrators process! Please confirm action taken!',
                                buttons: '[Yes][No]'
                            }, function (ButtonPressed) {
                                if (ButtonPressed === "Yes") {
                                    $.ajax({
                                        url:PECO.base_url()+ "hris/updateaccomplishment",
                                        type: "post",
                                        data: {"payrollstart":payrollstart,"payrollend":payrollend},
                                        dataType: "json"
                                    }).done(function (d) {
                                        PECO.initAlerts(d.msg, 'Updated', d.func);
                                        tbl_accomplishment_refresh();
                                        $('#payrolldate')[0].reset();
                                    });
                                }
                            });
                    }
                }
        });

        $(document).on('click','#delete_btn' , function (e) {
            e.preventDefault();
            var this_ = $(this);
            var this_val = this_.attr("data-id");
            $.SmartMessageBox({
                title: "<i class='fa fa-question fa-fw fa-lg txt-color-yellow'></i> Are you sure you want to delete this entry?</span>",
                content: 'Please confirm action taken!',
                buttons: '[Yes][No]'
            }, function (ButtonPressed) {
                if (ButtonPressed === "Yes") {
                    $.ajax({
                        url:PECO.base_url()+ "hris/deleteaccomplishment",
                        type: "post",
                        data: {"accomid":this_val},
                        dataType: "json"
                    }).done(function (d) {
                        PECO.initAlerts(d.msg, 'Message', d.func);
                        if(d.qry === true){
                            tbl_accomplishment_refresh();
                        }
                    });
                }
            });
        });

        select_employee.change(function () {
                var this_ = $(this);

                if(this_.val() == ''){
                  //  tbl_accomplishment_refresh();
                }else{
                    $.ajax({
                        url:PECO.base_url()+"hris/showmeterreadingaccomplishment",
                        type:"post",
                        dataType:"json",
                        data:{'empid':this_.val()},
                        beforeSend: function(){
                            accomplishment_table.dataTable().empty();
                            PECO.DTphpLoading(accomplishment_table, 'Loading accomplishment... ');
                        }
                    }).done(function (d) {
                        tbl_accomplishment(d);
                    });
                }
        });

        frm_add_accomplishment.submit(function (e) {
            e.preventDefault();
            var this_ = $(this);
            $.ajax({
                url: this_.attr('action'),
                type: this_.attr('method'),
                data: this_.serialize(),
                dataType: 'json'
            }).done(function (d) {
                PECO.initAlerts(d.msg, 'Registered', d.func);
                if(d.qry === true){
                    tbl_accomplishment_refresh();
                    select_gdlb.select2("val","");
                    $(document).find('#reading_count').val('');
                }
            });
        });

        accomplishment_table.on('keydown','input' , function (e) {
            var this_ = $(this);
            var key = (e.keyCode ? e.keyCode : e.which);
            if(e.keyCode===13){
                var this_tr = this_.closest('tr');
                var id = this_tr.find('.errorid').val();
                updateerrorreading(id , this_.val());
                var this_index = this_.closest('td.errorinput').index();
                var next_tr = this_.closest('tr').next();
                var next_input = next_tr.find('td.errorinput').find('input');
                next_input.focus();
            }
            if(e.keyCode===40){
                var this_index = this_.closest('td').index();
                var next_tr = this_.closest('tr').next();
                var next_input = next_tr.find('td.errorinput').find('input');
                next_input.focus();
            }
            if(e.keyCode===38){
                var this_index = this_.closest('td').index();
                var next_tr = this_.closest('tr').prev() + 1;
                var next_input = next_tr.find('td.errorinput').find('input');
                next_input.focus();
            }
        });

    };

    var updateerrorreading = function (id , value) {
            $.ajax({
                url: PECO.base_url() + "hris/updateerrorreading",
                type:"post",
                data:{"id":id, "value":value},
                dataType:"json"
            }).done(function (d) {
                if(d.qry === true){
                    PECO.initAlerts(d.msg , "Success" , d.func);
                }else{
                    PECO.initAlerts(d.msg , "Failed" , d.func);
                }
            });
    };

    var tbl_accomplishment_refresh = function(){
        $.ajax({
            url:PECO.base_url()+"hris/showmeterreadingaccomplishment",
            type:"post",
            dataType:"json",
            data:{'empid':$('#select_employee').val()},
            beforeSend: function(){
                accomplishment_table.dataTable().empty();
                PECO.DTphpLoading(accomplishment_table, 'Loading accomplishment... ');
            }
        }).done(function (d) {
            tbl_accomplishment(d);
        });
    };

    var tbl_accomplishment = function(data) {
        accomplishment_table.dataTable().empty();
        accomplishment_table.dataTable({
            bDestroy: true,
            bPaginate: true,
            bFilter: true,
            bInfo: true,
            bStateSave: true,
            bProcessing: true,
            aaData: data.accomplishments,
            aoColumns: [
                {"data":"num"},
                {"data":"gdlbid",sClass:"text-info"},
                {"data":"readingdte"},
                {"data":"readingcnt" ,sClass:"text-info"},
                {"data":"errors" , sClass: 'number errorinput' , sWidth: ''},
                {"data":"status"},
                {"data":"buttons"}
            ],
            searchHighlight: true
        });
    };

    //main function
    var accomplishment_report = function(){
        getlatestdate();
        tbl_meter_reader_table();
        init_meter_reading_chart();
        init_show_accomplishment();
    };

    var  getlatestdate = function(){
        $.ajax({
            url: PECO.base_url() + "hris/getlatestdate",
            type:"post",
            dataType:"json"
        }).done(function (d) {
            $(document).find('#fromaccomp').text(d.latestfrom);
            $(document).find('#toaccomp').text(d.latestto);
        });
    };
    
    var init_show_accomplishment = function () {

        var completed = true;
        var fromaccomp = $(document).find('#fromaccomp').val();
        $('#showaccomplishmentreport').click(function () {

            var fromaccomp = $(document).find('#fromaccomp').text();
            var toaccomp = $(document).find('#toaccomp').text();
            $.ajax({
                url:PECO.base_url()+"hris/getaccomplishmentreport",
                type:"post",
                data:{"fromaccomp":fromaccomp , "toaccomp":toaccomp},
                dataType:"json"
            }).done(function (d) {
                print("Meter Reader Accomplishment Report",d.html);
            });
        });
    };

    var print = function(reptitle, content){

        // Open a new window for the printable table
        var win = window.open('', '');
        win.document.body.innerHTML =
            '<head>' +
            '<title>'+reptitle+'</title>'+
            '<link href="' + PECO.base_url() + 'assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>' +
            '<link href="' + PECO.base_url() + 'assets/global/css/components.css" rel="stylesheet" type="text/css"/>' +
            '<link href="' + PECO.base_url() + 'assets/global/css/plugins.css" rel="stylesheet" type="text/css"/>' +
            '<link href="' + PECO.base_url() + 'assets/admin/layout/css/layout.css" rel="stylesheet" type="text/css"/>' +
            '<link href="' + PECO.base_url() + 'assets/admin/layout/css/themes/default.css" rel="stylesheet" type="text/css"/>' +
            '<link href="' + PECO.base_url() + 'assets/admin/layout/css/custom.css" rel="stylesheet" type="text/css"/>' +
            '<style>body{margin: 0px 0px !important; font-family: arial; background: #fff;}</style>' +
            '</head>' +
            '<div style="position: absolute; left: 0px; width: 100%;">' + content + '</div>';
        setTimeout(function () {
          //  win.print(); // blocking - so close will not
          //  win.close(); // execute until this is done
        }, 250);

    };
    
    var init_meter_reading_chart = function(){
        $.ajax({
            url:PECO.base_url()+"hris/meterreadingchart",
            type:"post",
            dataType:"json",
        }).done(function (d) {
            var chart = AmCharts.makeChart("materreaderbarchart", {
                "type": "serial",
                "theme": "light",
                "marginRight": 70,
                "dataProvider": d.meterreadingarr,
                "valueAxes": [{
                    "axisAlpha": 0,
                    "position": "left",
                    "title": "Meter Reading Report"
                }],
                "startDuration": 1,
                "graphs": [
                    {
                        "balloonText": "Regular <b>[[value]]</b>",
                        "fillColorsField": "color",
                        "fillAlphas": 0.9,
                        "lineAlpha": 0.2,
                        "type": "column",
                        "valueField": "regtotal"
                    },
                    {
                        "balloonText": "Special <b>[[value]]</b>",
                        "fillColorsField": "color",
                        "fillAlphas": 0.9,
                        "lineAlpha": 0.2,
                        "type": "column",
                        "valueField": "sptotal"
                    },
                    {
                        "balloonText": "Total <b>[[value]]</b>",
                        "fillColorsField": "color",
                        "fillAlphas": 0.9,
                        "lineAlpha": 0.2,
                        "type": "column",
                        "valueField": "total"
                    }

                ],
                "chartCursor": {
                    "categoryBalloonEnabled": true,
                    "cursorAlpha": 0,
                    "zoomable": false
                },
                "categoryField": "fullname",
                "categoryAxis": {
                    "gridPosition": "start",
                    "labelRotation": 0
                },
                "export": {
                    "enabled": true
                }

            });
            //init pie chart

            var chart = AmCharts.makeChart("meterreaderpiechart", {
                "type": "pie",
                "startDuration": 0,
                "theme": "none",
                "addClassNames": true,
                "legend":{
                    "position":"right",
                    "marginRight":100,
                    "autoMargins":false
                },
                "innerRadius": "30%",
                "defs": {
                    "filter": [{
                        "id": "shadow",
                        "width": "200%",
                        "height": "200%",
                        "feOffset": {
                            "result": "offOut",
                            "in": "SourceAlpha",
                            "dx": 0,
                            "dy": 0
                        },
                        "feGaussianBlur": {
                            "result": "blurOut",
                            "in": "offOut",
                            "stdDeviation": 5
                        },
                        "feBlend": {
                            "in": "SourceGraphic",
                            "in2": "blurOut",
                            "mode": "normal"
                        }
                    }]
                },
                "dataProvider": d.meterreadingarr,
                "valueField": "total",
                "titleField": "fullname",
                "export": {
                    "enabled": true
                }
            });
            chart.addListener("init", handleInit);
            chart.addListener("rollOverSlice", function(e) {
                handleRollOver(e);
            });

            function handleInit(){
                chart.legend.addListener("rollOverItem", handleRollOver);
            }
            function handleRollOver(e){
                var wedge = e.dataItem.wedge.node;
                wedge.parentNode.appendChild(wedge);
            }
        });
    };

    var tbl_meter_reader_table = function(){
        $.ajax({
            url:PECO.base_url()+"hris/showmeterreaderemployees",
            type:"post",
            dataType:"json",
            beforeSend: function(){
                meter_reader_table.dataTable().empty();
                PECO.DTphpLoading(meter_reader_table, 'Loading accomplishment... ');
            }
        }).done(function (d) {
            populate_meter_reader_table(d);
        });
    };

    var populate_meter_reader_table = function (data) {
        meter_reader_table.dataTable().empty();
        meter_reader_table.dataTable({
            bDestroy: true,
            bPaginate: true,
            bFilter: true,
            bInfo: true,
            bStateSave: true,
            bProcessing: true,
            aaData: data.meter_reader_table,
            aoColumns: [
                {"data":"num", sWidth: ''},
                {"data":"empid" , sClass: 'text-primary', sWidth: ''},
                {"data":"fullname", sWidth: '30%'},
                {"data":"gdlb"  , sClass: 'number gdlb', sWidth: ''},
                {"data":"regtotal", sClass: 'text-info number regtotal', sWidth: ''},
                {"data":"sptotal", sClass: 'text-info number sptotal', sWidth: ''},
                {"data":"regrate" ,  sClass: 'regrate', sWidth: ''},
                {"data":"sprate", sClass: 'sprate', sWidth: ''},
                {"data":"regdeduct", sClass: 'number regdeduct', sWidth: ''},
                {"data":"spdeduct", sClass: 'number spdeduct', sWidth: ''},
                {"data":"total", sClass: 'text-info number total' , sWidth: '20%'}
            ],
            searchHighlight: true,
            fnRowCallback: function(nRow, data) {
                if(data.done===true) {
                    $(nRow).find('td').addClass('success');
                }
            }
        });
    };

    return {
        accomplishmentreport:function(){
            accomplishment_report();
        },
        accomplishment:function(){
            accomplishment_entry();
        }
    };
}();