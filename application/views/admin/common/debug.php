<link href="<?php echo base_url(); ?>assets/global/plugins/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
<div class="page-content-wrapper">
    <div class="page-content  animated fadeInUp fast no-padding">

        <div class="row">
            <div class="col-md-12">
                <div class="portlet blue box table margin-top-20">
                    <div class="portlet-title margin-top-20">
                        <h1 class="page-title" style="color: #fff; padding-top: 15px;"> Billing </h1>
                    </div>
                    <div class="portlet-body">
                        <div class="row">
                            <div class="col-md-12 margin-top-10">
                                <table class="table table-advance table-condensed table-hover table-bordered table-hover table-striped tbl-sm" id="tbl_billing_list">
                                    <thead>
                                    <th width="20px"></th>
                                    <th width="30px"><i class="fa fa-reorder"></i></th>
                                    <th width="18px">Servno</th>
                                    <th width="10px">MTR</th>
                                    <th>Name</th>
                                    <th>Address</th>
                                    <th width="65px">Due</th>
                                    <th width="65px">Previous</th>
                                    <th width="65px">Interest</th>
                                    <th width="65px">Current</th>
                                    <th width="75px">Total</th>
                                    <th width="35px">Status</th>
                                    <th width="50px"><i class="fa fa-wrench"></i></th>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-select/bootstrap-select.min.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/select2/select2.min.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/datatables/jquery.dataTables.min.js" type="text/javascript"></script> 
<script src="<?php echo base_url(); ?>assets/global/plugins/datatables/dataTables.bootstrap.min.js" type="text/javascript"></script> 
<script>
    var BILLING = function () {
        var table = $('#tbl_billing_list');


        var init_billing_table = function () {
            $.ajax({
                url: PECO.base_url() + 'settings/person',
                type: 'POST',
                dataType: 'json',
            }).done(function (data) {
                table.dataTable().empty();
                var dt = table.dataTable({
                    // Internationalisation. For more info refer to http://datatables.net/manual/i18n
                    bDestroy: true,
                    bPaginate: true,
                    bFilter: true,
                    bInfo: true,
                    bStateSave: true,
                    bProcessing: true,
                    bServerSide: true,
                    oLanguage: {
                        sProcessing: '<p class="text-info">Loading time logs... </p.'
                    },
                    ajax: {
                        url: PECO.base_url() + 'settings/person',
                        type: "POST",
                        //data: {},

                    },
                    aoColumns: [
                        {"data": "expand"},
                        {"data": "sysid"},
                        {"data": "servno", sClass: "text-info"},
                        {"data": "mtr"},
                        {"data": "name"},
                        {"data": "addrspec"},
                        {"data": "due", sClass: "number"},
                        {"data": "prevamt", sClass: "number"},
                        {"data": "interest", sClass: "number"},
                        {"data": "current", sClass: "number"},
                        {"data": "total", sClass: "number text-danger"},
                        {"data": "status", sClass: "center"},
                        {"data": "control", sClass: 'center'},
                    ],
                    "columnDefs": [
                        {"orderable": false, searchable: false, "targets": 0},
                        {"orderable": false, searchable: false, "targets": 2},
                        {"orderable": false, searchable: false, "targets": 3},
                        {"orderable": false, searchable: false, "targets": 6},
                        {"orderable": false, searchable: false, "targets": 7},
                        {"orderable": false, searchable: false, "targets": 8},
                        {"orderable": false, searchable: false, "targets": 9},
                        {"orderable": false, searchable: false, "targets": 10},
                        {"orderable": false, searchable: false, "targets": 11},
                        {"orderable": false, searchable: false, "targets": 12}
                    ],
                     "order": [[ 1, "asc" ]],
                    
                    "lengthMenu": [
                        [5, 15, 20, -1],
                        [5, 15, 20, "All"] // change per page values here
                    ],
                    // set the initial value
                    "pageLength": 10,
                    fnDrawCallback: function () {
                        console.log('Table drawn..');

                    },
                });


                $('#tbl_billing_list tbody').on('click', '#btn-expand', function () {
                    console.log('details clicked!');
                    var this_ = $(this);
                    var thisTr = this_.closest('tr');
                    var thisTr_child = thisTr.children('td').length;
                    if (this_.hasClass('expanded') == false) {
                        //$('<tr><td colspan="'+thisTr_child+'">Details HERE</td></tr>').insertAfter(thisTr);

                        $.ajax({
                            url: PECO.base_url() + 'settings/getinfo',
                            type: 'post',
                            dataType: 'json',
                            data: {'id': this_.attr('data-id')},
                            beforeSend: function () {
                                thisTr.after('<tr id="loading"><td colspan="' + thisTr_child + '">Loading..</td></tr>');
                            }
                        }).done(function (data) {
                            if (data.qry === true) {
                                var data_details = '<div class="row">';
                                data_details += '<div class="col-md-3">';
                                data_details += '<ul class="list-group">';
                                data_details += '<li class="list-group-item">KWH Used: <span class="data pull-right">' + data.kwh + '</span></li>';
                                data_details += '<li class="list-group-item">Generation Amount: <span class="data pull-right">' + data.genamt + '</span></li>';
                                data_details += '<li class="list-group-item">Generation Charge: <span class="data pull-right">' + data.genchrg + '</span></li>';
                                data_details += '</ul>';
                                data_details += '</div>';

                                data_details += '<div class="col-md-3">';
                                data_details += '<ul class="list-group">';
                                data_details += '<li class="list-group-item">Previous Balance (total): <span class="data pull-right">' + data.prevbill + '</span></li>';
                                data_details += '<li class="list-group-item">Previous Interest: <span class="data pull-right">' + data.prevint + '</span></li>';
                                data_details += '<li class="list-group-item">Previous Vat: <span class="data pull-right">' + data.prevvat + '</span></li>';
                                data_details += '<li class="list-group-item">Total: <span class="data pull-right">' + data.prevtotal + '</span></li>';
                                data_details += '</ul>';
                                data_details += '</div>';

                                data_details += '<div class="col-md-3">';
                                data_details += '<ul class="list-group">';
                                data_details += '<li class="list-group-item">Current Bill: <span class="data pull-right">' + data.curamt + '</span></li>';
                                data_details += '<li class="list-group-item">Interest: <span class="data pull-right">' + data.curint + '</span></li>';
                                data_details += '<li class="list-group-item">Current Vat: <span class="data pull-right">' + data.curvat + '</span></li>';
                                data_details += '<li class="list-group-item">Total: <span class="data pull-right">' + data.curtotal + '</span></li>';
                                data_details += '</ul>';
                                data_details += '</div>';

                                data_details += '<div class="col-md-3">';
                                data_details += '<ul class="list-group">';
                                data_details += '<li class="list-group-item">Grand Total.: <span class="data pull-right">' + data.total + '</span></li>';
                                data_details += '<li class="list-group-item">Bill Date: <span class="data pull-right">' + data.billdate + '</span></li>';
                                data_details += '<li class="list-group-item">Bill Count: <span class="data pull-right">' + data.billcount + '</span></li>';
                                data_details += '<li class="list-group-item">Bill #: <span class="data pull-right">' + data.billno + '</span></li>';
                                data_details += '</ul>';
                                data_details += '</div>';

                                data_details += '</div>';
                                data_details += '<div class="row footer">';
                                data_details += '<div class="col-md-12">';
                                data_details += '<i class="fa fa-info"></i> <span class="label label-warning"><i class="fa fa-check"></i> Delevered</span> <span class="label label-danger"><i class="fa fa-plus"></i> With Add-Bill</span> <span class="label label-success"><i class="fa fa-print"></i> Printed</span>';
                                data_details += '<div class="btn-group pull-right"><button class="btn btn-primary btn-xs"><i class="fa fa-print"></i> Print Account</button><button class="btn btn-default btn-xs"><i class="fa fa-print"></i> Re-print Bill</button></div>';
                                data_details += '</div>';
                                data_details += '</div>';
                                thisTr.after('<tr class="animated fadeIn fast compact" id="details"><td colspan="' + thisTr_child + '">' + data_details + '</td></tr>');
                            } else {
                                thisTr.after('<tr class="animated fadeIn fast compact"  id="details"><td colspan="' + thisTr_child + '"><i class="fa fa-warning text-warning"></i> No Record Found!</td></tr>');
                            }
                        });

                        this_.removeClass('fa-plus-square-o').addClass('fa-minus-square-o');
                        thisTr.next('#loading').remove();
                    } else {
                        thisTr.next('#details').remove();
                        thisTr.next('#loading').remove();
                        this_.removeClass('fa-minus-square-o').addClass('fa-plus-square-o');
                    }
                    this_.toggleClass('expanded');
                });

                /*
                 // Array to track the ids of the details displayed rows
                 var detailRows = [];
                 
                 
                 // On each draw, loop over the `detailRows` array and show any child rows
                 dt.on('draw', function () {
                 $.each(detailRows, function (i, id) {
                 $('#' + id + ' td.details-control').trigger('click');
                 });
                 });
                 */
            })
        };
        var init_billing = function () {
            init_billing_table();
        };
        return {
            init: function () {
                init_billing();
            }
        };
    }();
    BILLING.init();
</script>