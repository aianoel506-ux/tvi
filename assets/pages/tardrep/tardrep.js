var TARDREP = function(){


    var tardinesstable = $('#tardinesstable',document);

    var init_tardrep = function(flexi){
        init_tardreptable(false,false,flexi);
        events(flexi);
    };

    var events = function (flexi){
        $(document).on('click','#printmonthlytard',function () {
            var monthpass = $(document).find('#month').val();
            var yearpass = $(document).find('#year').val();
            
            // Validate that both month and year are selected
            if (!monthpass || !yearpass) {
                PECO.showAlert('Please select both month and year before printing.', 'warning');
                return false;
            }
            
            if (flexi) {
                dt_flexi_tardrep(monthpass,yearpass,false,'summary');
            } else {
                dt_tardrep(monthpass,yearpass,false,'summary');
            }
        });

        $(document).on('click','#searchtardibtn',function (e) {
            e.preventDefault();
            var month = $(document).find('#month').val();
            var year = $(document).find('#year').val();
            
            // Validate that both month and year are selected
            if (!month || !year) {
                PECO.showAlert('Please select both month and year before searching.', 'warning');
                return false;
            }
            
            if (flexi) {
                dt_flexi_tardrep(month , year);
            } else {
                dt_tardrep(month , year);
            }
        });

        $(document).on('click','#printmonthlytarddetails',function () {
            var monthpass = $(document).find('#month').val();
            var yearpass = $(document).find('#year').val();
            
            // Validate that both month and year are selected
            if (!monthpass || !yearpass) {
                PECO.showAlert('Please select both month and year before printing.', 'warning');
                return false;
            }

            if (flexi) {
                dt_flexi_tardrep(monthpass,yearpass,false,'details');
            } else {
                dt_tardrep(monthpass,yearpass,false,'details');
            }
        });
    };
    var init_tardreptable = function(month , year, flexi){
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

        // Set default values for dropdowns
        $('#month').val(monthdefault);
        $('#year').val(defaultyear);
        
        // Initialize select2 if available, otherwise use regular dropdowns
        if (typeof PECO !== 'undefined' && PECO.select2Basic) {
            PECO.select2Basic($('#month'),'systems/select2month', 'Select Month...', false, false, monthdefault);
            PECO.select2Basic($('#year'),'hris/select2year','Select Year',false,false,defaultyear);
        }
        
        // Load initial data
        if (flexi) {
            dt_flexi_tardrep(monthdefault,defaultyear)
        } else {
            dt_tardrep(monthdefault,defaultyear);
        }
        /*$.ajax({
            url:PECO.base_url()+'hris/getalltardinessrep',
            type:'post',
            data:{"monthdata":monthdefault,"yeardata":defaultyear},
            dataType:'json',
            beforeSend: function(){
                tardinesstable.dataTable().empty();
                PECO.DTphpLoading(tardinesstable, 'Loading tardiness... ');
            }
        }).done(function (d) {
            tardinesstable.dataTable().empty();
            tardinesstable.dataTable({
                bDestroy: true,
                bPaginate: true,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                aaData: d.tardinessdata,
                aoColumns: [
                    //  {"data": "expand", sWidth: '10px', sClass: 'expand'},
                    {"data": "num", sWidth: '20px'},
                    {"data": "empid", sWidth: '30px', sClass: 'text-danger text-bold'},
                    {"data": "workshiftass", sWidth: '80px', sClass: 'text-success text-bold'},
                    {"data": "datelog", sWidth: '20px', sClass: 'text-primary'},
                    {"data": "amin", sWidth: '20px', sClass: ''},
                    {"data": "amout", sWidth: '20px', sClass: ''},
                    {"data": "amlate", sWidth: '20px', sClass: ''},
                    {"data": "pmin", sWidth: '20px'},
                    {"data": "pmout", sWidth: '20px'},
                    {"data": "pmlate", sWidth: '20px'},
                    {"data": "total", sWidth: '20px'}
                ],
                searchHighlight: true,
                language: {
                    "emptyTable": '<h4><i class="fa fa-warning text-warning"></i> No record found.</h4>'
                },
            });
        }).fail(function () {
            PECO.phpError();
        });*/
    };

    var dt_tardrep = function (month,year,id,print) {
        PECO.dtSubDetails(tardinesstable,'hris/generatetardiness',{month: month,year: year,print:'details'});
        $.ajax({
            url : PECO.base_url() + 'hris/generatetardiness',
            type : 'post',
            dataType : 'json',
            data : {
                month : month,
                year : year,
                id : id,
                print : print,
                fetch_time_data: true // Add parameter to fetch time data
            },
            beforeSend: function(){
                if (print === '') {
                    tardinesstable.dataTable().empty();
                    PECO.DTphpLoading(tardinesstable, 'Loading tardiness... ');
                }
            }
        }).done(function (d) {
            if (print && d.html.length) {
                var win = window.open('', '');
                PECO.pdfPreview(win, "Monthly Tardiness Report as of "+month+"/"+year, d.html);
            }

            if (d.list) {
                tardinesstable.dataTable().empty();
                tardinesstable.dataTable({
                    bDestroy: true,
                    bPaginate: true,
                    bFilter: true,
                    bInfo: true,
                    bStateSave: true,
                    aaData: d.list,
                    aoColumns: [
                        //  {"data": "expand", sWidth: '10px', sClass: 'expand'},
                        {"data": "num", sWidth: '20px'},
                        {"data": "name", sWidth: '30px', sClass: 'text-danger text-bold'},
                        {"data": "bioid", sWidth: '20px', sClass: 'text-primary'},
                        {"data": "position", sWidth: '30px', sClass: 'text-danger text-bold'},
                        {"data": "sched", sWidth: '80px', sClass: 'text-success text-bold'},
                        {"data": "latecount", sWidth: '20px', sClass: ''},
                        {"data": "totallates", sWidth: '20px', sClass: ''},
                        {"data": "totalLates", sWidth: '20px', sClass: 'text-danger'},
                        {"data": "totalUndertime", sWidth: '20px', sClass: 'text-warning'},
                        {"data": "totalDeduction", sWidth: '20px', sClass: 'text-danger text-bold'},
                    ],
                    searchHighlight: true,
                    language: {
                        "emptyTable": '<h4><i class="fa fa-warning text-warning"></i> No record found.</h4>'
                    },
                });
            } else {
                // Show debug information when no data is returned
                console.log('No tardiness data returned for month:', month, 'year:', year);
                console.log('Response data:', d);
                
                // Show user-friendly message
                tardinesstable.dataTable().empty();
                tardinesstable.dataTable({
                    bDestroy: true,
                    bPaginate: false,
                    bFilter: false,
                    bInfo: false,
                    aaData: [{
                        num: '',
                        name: '<h4><i class="fa fa-info-circle text-info"></i> No tardiness data found for ' + month + '/' + year + '</h4><p>This could be due to:</p><ul><li>No attendance logs for this month</li><li>No employees with tardiness records</li><li>No schedule assignments for this period</li></ul>',
                        bioid: '',
                        position: '',
                        sched: '',
                        latecount: '',
                        totallates: ''
                    }],
                    aoColumns: [
                    {"data": "num", sWidth: '20px'},
                    {"data": "name", sWidth: '100%', sClass: 'text-center'},
                    {"data": "bioid", sWidth: '0px', sClass: 'hidden'},
                    {"data": "position", sWidth: '0px', sClass: 'hidden'},
                    {"data": "sched", sWidth: '0px', sClass: 'hidden'},
                    {"data": "latecount", sWidth: '0px', sClass: 'hidden'},
                    {"data": "totallates", sWidth: '0px', sClass: 'hidden'},
                    {"data": "totalLates", sWidth: '0px', sClass: 'hidden'},
                    {"data": "totalUndertime", sWidth: '0px', sClass: 'hidden'},
                    {"data": "totalDeduction", sWidth: '0px', sClass: 'hidden'},
                ],
                    language: {
                        "emptyTable": ''
                    },
                });
            }
        }).fail(function (xhr, status, error) {
            console.error('AJAX Error:', status, error);
            console.error('Response:', xhr.responseText);
            
            // Show error message to user
            tardinesstable.dataTable().empty();
            tardinesstable.dataTable({
                bDestroy: true,
                bPaginate: false,
                bFilter: false,
                bInfo: false,
                aaData: [{
                    num: '',
                    name: '<h4><i class="fa fa-exclamation-triangle text-danger"></i> Error loading tardiness data</h4><p>Please try again or contact the administrator.</p>',
                    bioid: '',
                    position: '',
                    sched: '',
                    latecount: '',
                    totallates: '',
                    totalLates: '',
                    totalUndertime: '',
                    totalDeduction: ''
                }],
                aoColumns: [
                    {"data": "num", sWidth: '20px'},
                    {"data": "name", sWidth: '100%', sClass: 'text-center'},
                    {"data": "bioid", sWidth: '0px', sClass: 'hidden'},
                    {"data": "position", sWidth: '0px', sClass: 'hidden'},
                    {"data": "sched", sWidth: '0px', sClass: 'hidden'},
                    {"data": "latecount", sWidth: '0px', sClass: 'hidden'},
                    {"data": "totallates", sWidth: '0px', sClass: 'hidden'},
                ],
                language: {
                    "emptyTable": ''
                },
            });
        });
    };

    var dt_flexi_tardrep = function (month,year,id,print) {
        PECO.dtSubDetails(tardinesstable,'hris/generateflexibletardiness',{month: month,year: year,print:'details'});
        $.ajax({
            url : PECO.base_url() + 'hris/generateflexibletardiness',
            type : 'post',
            dataType : 'json',
            data : {
                month : month,
                year : year,
                id : id,
                print : print,
                inputs: {
                    graceLateSeconds: 60,
                    fallbackWorkHours: 8
                }
            },
            beforeSend: function(){
                if (print === '') {
                    tardinesstable.dataTable().empty();
                    PECO.DTphpLoading(tardinesstable, 'Loading tardiness... ');
                }
            }
        }).done(function (d) {
            if (print && d.html.length) {
                var win = window.open('', '');
                PECO.pdfPreview(win, "Monthly Tardiness Report as of "+month+"/"+year, d.html);
            }

            if (d.list && d.list.length) {
                tardinesstable.dataTable().empty();
                tardinesstable.dataTable({
                    bDestroy: true,
                    bPaginate: true,
                    bFilter: true,
                    bInfo: true,
                    bStateSave: true,
                    aaData: d.list,
                    aoColumns: d.columns,
                    searchHighlight: true,
                    language: {
                        "emptyTable": '<h4><i class="fa fa-warning text-warning"></i> No record found.</h4>'
                    },
                });
            } else {
                tardinesstable.dataTable().empty();
                tardinesstable.dataTable({
                    bDestroy: true,
                    bPaginate: false,
                    bFilter: false,
                    bInfo: false,
                    aaData: [],
                    aoColumns: d.columns || [
                        {"data":"num","sWidth":"10px"},
                        {"data":"name","sWidth":"30px","sClass":"text-danger text-bold"},
                        {"data":"bioid","sWidth":"20px","sClass":"text-primary"},
                        {"data":"totallates","sWidth":"20px"},
                        {"data":"totalundertime","sWidth":"20px"},
                        {"data":"fordeds","sWidth":"20px","sClass":"text-danger text-bold"}
                    ],
                    language: {
                        "emptyTable": '<h4><i class="fa fa-warning text-warning"></i> No record found.</h4>'
                    },
                });
            }
        }).fail(function () {

        });
    };

    return{
        init:function(flexi){
            init_tardrep(flexi);
        }
    }
}();
