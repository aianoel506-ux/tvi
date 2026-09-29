<?php

?>
<div class="portlet light bordered table">
    <div class="portlet-title">
        <div class="caption">
            <h4><i class="fa fa-wifi"></i> Application <span id="refresh_table" class="font-green-haze">Online</span></h4>
        </div>
        <div class="tools">
            <a href="javascript:;" id="btn_download_all"><i class="fa fa-download text-info"></i> Download Visible</a>
        </div>
    </div>
    <div class="portlet-body">
        <hr style="margin-top: 0px;">
        <table width="100%" class="table table-hover table-striped table-bordered table-condensed" id="tbl_online_list">
            <thead>
            <th>Date</th>
            <th>Ticket#</th>
            <th>Name</th>
            <th>Address</th>
            <th>District</th>
            <th>Class</th>
            <th>Connection</th>
            <th>Ownertype</th>
            <th>Property</th>
            <th>Status</th>
            </thead>
        </table>
    </div>
</div>


<script type="text/javascript">
    var CADSYNC = function() {
        PECO.getHighlightsPlugin();

        var online = true;

        if( online == true ) {
            //var url_row = 'http://www.panayelectric.com/index.php/customer/getrowticket';
            var url = 'https://www.panayelectric.com/index.php/customer/getapplicationslist';
        } else {
            //var url_row = 'http://localhost/pecoweb/index.php/customer/getrowticket';
            var url = 'http://localhost/pecoweb/index.php/customer/getapplicationslist';
        }

        var tbl_online_list = $('#tbl_online_list', document);
        var handler_sync_fn = function() {

            handler_tbl_online();
            $(document).on('click', '#btn_download_all', function(e) {
                e.preventDefault();
                var length = $('tbody tr.available', tbl_online_list).length; // get class that are not sync yet.
                handler_row_submit(0, length);
            });

            init_reading_entry_keyboard();

            $(document).on('click', '#refresh_table', function(e) {
                e.preventDefault();

                handler_tbl_online();
            });
        };

        var handler_row_submit = function(index, totalindex) {
            async function submit() {

                var tr = $('tr.available', tbl_online_list).eq(index);
                var ticketno = $('#ticketnum', tr).val();


                var new_index  = Number(index) + 1;
                if(index < totalindex) {
                    let response = await fetch(url, {
                        method: 'post',
                        //headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                        //body: 'index=' + index + '&ticketno=' +ticketno,
                        headers: {'Content-Type':'application/x-www-form-urlencoded'},
                        body: 'password=P3C02019&ticketno=' +ticketno
                    });

                    // the server responds with confirmation and the image size
                    let result = await response.json();

                    handler_download_online_row(result, tr, new_index, totalindex)

                }else{
                    var length = $('tbody tr.available', tbl_online_list).length;
                    if(length>0) {
                        PECO.initAlerts('Sync finished!', 'PECO.net', 'info');
                    }else{
                        PECO.initAlerts('No data to sync!', 'PECO.net', 'warning');
                    }
                }
            }
            submit();
        };

        var handler_download_online_row = function(res, nRow, rowIndex, cnt) {
            var td_status = $('td.controls', nRow);
            var i_status = $('.fa', td_status);
            var input_arr = {};


            $.each(res.res, function (key, value) {
                input_arr[key] = value;
            });


            $.ajax({
                url: PECO.base_url() + 'cad/submitonlinerowdata',
                type: 'post',
                data: input_arr,
                dataType: 'json',
                async: false,
                cache: false,
                beforeSend: function () {
                    i_status.removeClass('fa-download fa-check fa-times text-danger').addClass('fa-spinner fa-spin fa-pulse');
                }
            }).done(function (d) {
                handler_row_submit(rowIndex, cnt);
                nRow.addClass('row-success').removeClass('available')
                i_status.removeClass('fa-spinner fa-spin fa-pulse fa-download fa-times text-danger').addClass('fa-check text-success');
            }).fail(function () {
                handler_row_submit(rowIndex, cnt);
                i_status.removeClass('fa-spinner fa-spin fa-pulse fa-download fa-check text-success').addClass('fa-times text-danger');
            });

        };

        var handler_tbl_online = function() {
            var select2_conntype_arr = [
                {'id': 1, 'text': 'R - Residential'},
                {'id': 2, 'text': 'C - Commercial - Business/Commercial Entity'},
                {'id': 3, 'text': 'P - Power - Over 5,000 KWH'},
                {'id': 10, 'text': 'G - Government - Government Entity'},
            ];



            async function table() {
                let response = await fetch(url, {
                    method: 'post',
                    headers: {'Content-Type':'application/x-www-form-urlencoded'},
                    body: 'password=P3C02019'
                });

                let result = await response.json();

                tbl_online_list.DataTable({
                    bDestroy: true,
                    bPaginate: true,
                    bFilter: true,
                    bInfo: true,
                    bStateSave: true,
                    bProcessing: true,
                    "iDisplayLength": 100,
                    "lengthMenu": [[100, 125, 150], [100, 125, 150]],
                    aaData: result.list,
                    aoColumns: [
                        {"data": "datecreated", sWidth: '200px', sClass: ''},
                        {"data": "ticketno", sWidth: '30px', sClass: 'text-danger bold'},
                        {"data": "ticketno", sWidth: '20%', sClass: 'text-primary', mRender: function(value, disp, data) {
                                return data.lastname + ', ' + data.firstname + ' ' + data.middlename;
                            }
                        },
                        {"data": "addressfull", sWidth: '25%', sClass: ''},
                        {"data": "addressdistrict", sWidth: '15px', sClass: '',
                            mRender: function(data) {
                                return '<code>' +  handlerDistName(data) + '</code>';
                            }
                        },
                        {"data": "connpurpose", sWidth: '15px', sClass: '',
                            mRender: function(data) {
                                var text;
                                select2_conntype_arr.forEach(function (item, index) {
                                    var id = item.id;
                                    if(id == data) {
                                        text = item.text;
                                    }
                                });
                                return text;
                            }

                        },
                        {"data": "conntype", sWidth: '15px', sClass: '',
                            mRender: function(data) {
                                return handlerAppParam(data);
                            }
                        },
                        {"data": "ownertype", sWidth: '15px', sClass: '',
                            mRender: function(data) {
                                return handlerAppParam(data);
                            }
                        },
                        {"data": "propertyowner", sWidth: '15px', sClass: '',
                            mRender: function(data) {
                                return handlerAppParam(data);
                            }
                        },
                        {"data": "ticketno", sClass: 'controls tet-align-center', mRender: function(value, disp, data) {
                                var row_form = '';
                                row_form += '<form action="#" id="frm_row_submit" method="post">';
                                row_form += '<div class="btn-group">';
                                row_form += '<input type="hidden" id="row_index" value="0" name="ticketindex" />';
                                row_form += '<input type="hidden" value="'+value+'" name="ticketnum" id="ticketnum" />';
                                row_form += '<button type="submit" class="btn btn-primary btn-xs inline"><i class="fa fa-download"></i></button>';
                                row_form += '</div>';
                                row_form += '</form>';
                                return row_form;
                            },
                            sortable: false,
                            searchable: false
                        }
                    ],
                    fnRowCallback: function(nRow, aData, iDisplayIndex) {
                        var index = iDisplayIndex + 1;
                        $('#row_index', nRow).val(index);
                        handler_check_row_sync(aData.ticketno, nRow);
                    },
                    searchHighlight: true,
                    language: PECO.DTEmptyMessage('No online applicant entry yet!')
                });
            }

            table();
        };


        var init_reading_entry_keyboard = function() {

            // ARROW DOWN NEXT READING INPUT
            tbl_online_list.on('keydown', '#input_row_essr', function(e) {
                var code = (e.keyCode ? e.keyCode : e.which);
                if (code === 13) {
                    var index = $('input#input_row_essr').index(this) + 1;
                    var this_input = $('input#input_row_essr').eq(index).focus();
                    setTimeout(function() {
                        this_input.select();
                    },100);
                    tbl_online_list.find('tr.row-info').removeClass('row-info');
                    this_input.closest('tr').addClass('row-info');
                }
            });

            // ARROW DOWN NEXT READING INPUT
            tbl_online_list.on('keydown', '#input_row_essr', function(e) {
                var code = (e.keyCode ? e.keyCode : e.which);
                if (code === 40) {
                    var index = $('input#input_row_essr').index(this) + 1;
                    var this_input = $('input#input_row_essr').eq(index).focus();
                    setTimeout(function() {
                        this_input.select();
                    },100);
                    tbl_online_list.find('tr.row-info').removeClass('row-info');
                    this_input.closest('tr').addClass('row-info');
                }
            });

            // ARROW UP PREVIOUS READING INPUT
            tbl_online_list.on('keydown', '#input_row_essr', function(e) {
                var code = (e.keyCode ? e.keyCode : e.which);
                if (code === 38) {
                    var index = $('input#input_row_essr').index(this) - 1;
                    var this_input = $('input#input_row_essr').eq(index).focus();
                    setTimeout(function() {
                        this_input.select();
                    },100);
                    tbl_online_list.find('tr.row-info').removeClass('row-info');
                    this_input.closest('tr').addClass('row-info');
                }
            });

        };

        var handler_check_row_sync = function(ticketno, nRow) {
            $.ajax({
                url: PECO.base_url() + 'cad/checkonlineticketstatus',
                type: 'post',
                dataType: 'json',
                data: {ticketno: ticketno},
            }).done(function(d) {
                if(d.qry == false) {
                    $(nRow).addClass('available');
                } else {
                    $(nRow).addClass('row-success');
                    $('.controls .fa', nRow).removeClass('fa-download').addClass('fa-check');
                }
            }).fail(function() {
                $(nRow).addClass('row-danger');
            });
        };

        var handlerDistName = function(distid) {
            var codes;
            $.ajax({
                url: PECO.base_url() + 'query/getdistrictcodes',
                type: 'post',
                data: {'id': distid},
                async: false,
                cache: false,
            }).done(function(d) {
                codes = d;
            });
            return codes;
        };

        var handlerAppParam = function(distid) {
            var codes;
            $.ajax({
                url: PECO.base_url() + 'query/getapplicationparamname',
                type: 'post',
                data: {'id': distid},
                async: false,
                cache: false,
            }).done(function(d) {
                codes = d;
            });
            return codes;
        };

        return {
            init: function() {
                handler_sync_fn();
            }
        }
    }();

    CADSYNC.init();
</script>
