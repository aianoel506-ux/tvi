<?php
/**
 * Created by PhpStorm.
 * User: SE
 * Date: 5/16/2018
 * Time: 10:08 AM
 */

?>
<div class="well">
    <form class="" id="frm_rep_filter" method="post" action="">
        <div class="row">
            <div class="form-group col-md-3">
                <div class="input-group">
                    <span class="input-group-addon">
                        From
                    </span>
                    <input class="form-control" type="date" id="filter_from" name="from" />
                </div>
            </div>
            <div class="form-group  col-md-3">
                <div class="input-group">
                    <span class="input-group-addon">
                        To
                    </span>
                    <input class="form-control" type="date" id="filter_to" name="to" />
                </div>
            </div>

            <div class="form-group col-md-6">
                <div class="input-group">
                    <span class="input-group-addon">
                        Type
                    </span>
                    <select id="select2_types" name="types" class="form-control">
                        <option></option>
                        <option value="1">Accomplished</option>
                        <option value="2">Unaccomplished</option>
                        <option value="3">Pending</option>
                    </select>

                    <span class="input-group-btn">
                        <button class="btn btn-default" type="reset"><i class="fa fa-refresh"></i> Reset</button>
                        <button class="btn btn-default" type="button"><i class="fa fa-print"></i> Print</button>
                        <button class="btn btn-success" type="button"><i class="fa fa-file"></i> Export</button>
                        <button class="btn btn-primary" type="submit"><i class="fa fa-filter"></i> Filter</button>
                    </span>
                </div>
            </div>
        </div>
    </form>
</div>
<table class="table table-hover table-striped" id="tbl_cwd_tickets">
    <thead>
    <th></th>
    <th><i class="fa fa-reorder"></i></th>
    <th>Ticket #</th>
    <th>Date Created</th>
    <th>Date Updated</th>
    <th>Status</th>
    <th>Account Details</th>
    <th>Complaints</th>
    <th>Particular</th>
    <th>Verification</th>
    <th>Created By</th>
    <th></th>
    </thead>
    <tbody>
    </tbody>
</table>


<script>
    var CWDOREP = function() {

        PECO.getHighlightsPlugin();
        PECO.getSelect2Plugins();
        PECO.getSweetAlert();

        var tbl_cwd_tickets = $('#tbl_cwd_tickets');

        var init_cwd_rep = function() {
            $('#select2_types', document).select2({
                'allowClear': true,
                'placeholder': 'Select type...'
            });

            init_tbl_tickets();

            PECO.dtSubDetails(tbl_cwd_tickets, 'cwdo/getticketdetailsbasic');
        };

        var init_tbl_tickets = function() {
            var status = false;
            $.ajax({
                url: PECO.base_url() + 'cwdo/getticketlist',
                type: 'post',
                dataType: 'json',
                data: {'status': status, 'complaints': 'cwd'},
                beforeSend: function() {
                    PECO.DTphpLoading(tbl_cwd_tickets, 'Loading ticket history...');
                }
            }).done(function (d) {
                tbl_cwd_tickets.dataTable().empty();
                tbl_cwd_tickets.dataTable({
                    bDestroy: true,
                    bPaginate: false,
                    bFilter: true,
                    bInfo: false,
                    aaData: d.list,
                    bSort: false,
                    //scrollY: '',
                    aoColumns: [
                        {"data": "expand", sWidth: '', sClass: 'text-align-center'},
                        {"data": "num", sWidth: '', sClass: 'number'},
                        {"data": "ticketno", sWidth: '', sClass: 'text-primary'},
                        {"data": "datecreated", sWidth: '', sClass: 'text-danger'},
                        {"data": "dateupdated", sWidth: '', sClass: ''},
                        {"data": "status", sWidth: '', sClass: 'number'},
                        {"data": "accountinfo", sWidth: '', sClass: ''},
                        {"data": "complaints", sWidth: '', sClass: ''},
                        {"data": "particular", sWidth: '', sClass: ''},
                        {"data": "verifications", sWidth: '', sClass: ''},
                        {"data": "createdby", sWidth: '', sClass: ''},
                        {"data": "control", sWidth: '', sClass: 'control'}
                    ],
                    "language": PECO.DTEmptyMessage(),
                    fnRowCallback: function(nRow, aData, Index) {
                        PECO.dtExpandBtn(nRow, aData.expand);

                        // CREATE SORT NUMBER
                        var index = Index +1;
                        $('td:eq(1)',nRow).html(index);
                    }
                });
            });
        };

        return {
            init: function() {
                init_cwd_rep();
            }
        }
    }();

    CWDOREP.init();
</script>