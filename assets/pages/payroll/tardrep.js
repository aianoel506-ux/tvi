var TARDINESS = function () {
    PECO.getHighlightsPlugin();
    PECO.getiCheckPlugin();

    var base_url = PECO.base_url();
    var dt = $('#emptable').dataTable();


    $('#daterange').html(function () {
        var dateObj = new Date();
        var t2 = new Date(dateObj.getFullYear(), dateObj.getMonth(), 1);
        var t1 = new Date();
        var diff = new Date(t1 - t2);
        var days = parseInt(diff / 1000 / 60 / 60 / 24 + 1);
        return days;
    });



    var init_employee_attendance_daily = function() {
        events();
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
            tbl_employee_attendance_daily(tbl);
        }, 10000); // 10 SEC Refresh

        tbl.on('click', '#btn-edit', function(e) {
            e.preventDefault();
            var this_ = $(this);
            var this_id = this_.attr('data-id');
            $('#attendancemodal').modal('show');
        });


        PECO.dtSubDetails(tbl, 'hris/getattendancedetails');
    };

    var events = function(){
        $(document).on('click','#printtardinessreport',function () {
            var to_date = $('#to_date', document);
            var to_date_val = to_date.val();
            var payclass = $('#pay_class', document).find('li.type.active').attr('data-id');
            var ccid = $('#dept', document).val();
            $.ajax({
                url: PECO.base_url() + 'hris/fetchtardinessdata',
                type: 'post',
                dataType: 'json',
                data: {'today': to_date_val, 'payclass': payclass, 'ccid': ccid},
            }).done(function (data) {

                var count = (data.data.length);
                var html = '';
                html += '<table class="table table-bordered table-responsive tbl-xs">';
                html += '<thead>';
                html += '<tr>';
                html += '<th>Emp ID</th>';
                html += '<th>Name</th>';
                html += '<th>AM Late</th>';
                html += '<th>PM Late</th>';
                html += '<th>Total Late</th>';
                html += '</tr>';
                html += '</thead>';
                html += '<tbody>';
                var i = 0;
                for(i=0;i<count;i++){
                    html += '<tr>';
                    html += '<td>'+data.data[i].empid+'</td>';
                    html += '<td>'+data.data[i].empname+'</td>';

                    if(data.data[i].amlate === '0:00:00'){
                        html += '<td></td>';
                    }else{
                        html += '<td>'+data.data[i].amlate+'</td>';
                    }
                    if(data.data[i].pmlate === '0:00:00'){
                        html += '<td></td>';
                    }else{
                        html += '<td>'+data.data[i].pmlate+'</td>';
                    }




                    html += '<td>'+data.data[i].latetotal+'</td>';
                    html += '</tr>';
                }


                html += '</tbody>';
                html += '</table>';
                PECO.pecoRepPrint("Tardiness Report" , html);
            }).fail(function () {
                PECO.phpError();
            });
        });
    };

    var tbl_employee_attendance_daily = function(tbl) {

        var to_date = $('#to_date', document);
        var to_date_val = to_date.val();
        var payclass = $('#pay_class', document).find('li.type.active').attr('data-id');
        var ccid = $('#dept', document).val();

        $.ajax({
            url: PECO.base_url() + 'hris/fetchtardinessdata',
            type: 'post',
            dataType: 'json',
            data: {'today': to_date_val, 'payclass': payclass, 'ccid': ccid},
            beforeSend: function() {
                //tbl.dataTable().empty();
                //PECO.DTphpLoading(tbl, "Loading today's attendance..");
            }
        }).done(function (data) {
            tbl.dataTable().empty();
            tbl.dataTable({
                bDestroy: true,
                bPaginate: true,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                aaData: data.data,
                aoColumns: [
                    {"data": "expand", sWidth: '10px', sClass: 'expand'},
                    {"data": "empid", sWidth: '90px', sClass: 'text-danger text-bold'},
                    {"data": "empname", sWidth: '200px', sClass: 'text-primary'},
                    {"data": "amlate", sWidth: '', sClass: 'amlate'},
                    {"data": "pmlate", sWidth: '', sClass: 'pmlate'},
                    {"data": "latetotal", sWidth: '', sClass: 'latetotal'},
                ],
                columnDefs: [
                    //{"targets": '_all', "orderable": false, "searchable": false},
                ],
                "drawCallback": function (settings) {
                },
                "fnRowCallback": function (nRow, data) {
                    if(data.complete==false){ $(nRow).addClass('warning'); }
                    if(data.lateam==true){$(nRow).find('td.amlate').addClass('danger'); }else{$(nRow).find('td.amlate').addClass('text-liquefied');}
                    if(data.latpm==true){$(nRow).find('td.pmlate').addClass('danger'); }else{$(nRow).find('td.pmlate').addClass('text-liquefied');}
                    if(data.lateam==true || data.latpm==true) {$(nRow).find('td.latetotal').addClass('text-primary text-bold');}else{$(nRow).find('td.latetotal').addClass('text-liquefied');}

                    $(nRow).find('[data-toggle="popover"]').each(function(){
                        PECO.popOverRow($(this), true, true, 'popover-info');
                    });

                    $(nRow).find('.tooltips').each(function(){
                        $(this).tooltip();
                    });
                },

                searchHighlight: true,

                language: {
                    "emptyTable": '<h4><i class="fa fa-warning text-warning"></i> No record found.</h4>'
                },
            });

            $('.date-stat').html(data.date);
        }).fail(function(){
            PECO.DTDefault(tbl, 'Error Loading PHP query..');
        });
    };


    return {
        attendancedaily: function() {
            init_employee_attendance_daily();
        }

    }
}();
