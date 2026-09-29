<div class="portlet light bordered">
    <div class="portlet-title tabbable-line" bis_skin_checked="1">
        <div class="caption" bis_skin_checked="1">
            <i class="icon-bubbles font-dark hide"></i>
            <span class="caption-subject font-dark bold uppercase">AR Monitoring</span>
        </div>
        <ul class="nav nav-tabs">
            <li class="active">
                <a href="#ar_billing" data-toggle="tab" aria-expanded="true"> Billing </a>
            </li>
            <li class="">
                <a href="#ar_payments" data-toggle="tab" aria-expanded="false"> Payments </a>
            </li>
        </ul>
    </div>

    <div class="portlet-body">
        <div class="tab-content">
            <div id="ar_billing" class="tab-pane active fade in">
                <div class="btn-group">
                    <a class="btn btn-primary inline" href="javascript:;" id="btn_process_billing"><i class="fa fa-refresh"></i> Process Billing</a>
                    <a class="btn btn-default inline" href="javascript:;" id="btn_process_billing"><i class="fa fa-print"></i> Print Billing</a>
                </div>
                <hr>
                <table class="table table-striped table-bordered table-hover table-condensed" id="tbl_billing">
                    <thead>
                    <th>#</th>
                    <th>Bill #</th>
                    <th>Year</th>
                    <th>Month</th>
                    <th>Name</th>
                    <th>Address</th>
                    <th>Due</th>
                    <th>Due Date</th>
                    <th>Control</th>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            <div id="ar_payments" class="tab-pane fade in">
                <h2>Payments</h2>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript" src="<?php echo base_url(); ?>assets/pages/paebilling/billing.js"></script>
<script type="text/javascript">
    BILLING.init();
</script>