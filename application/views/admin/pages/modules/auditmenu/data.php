<?php
//  echo $dataid;
?>
<div class="row">
    <input type="hidden" id="idhidden" value="<?php echo $dataid ?>"/>
    <div class="col-md-9">
        <div class="portlet light table">
            <div class="portlet-title">
                <ul class="list-group summary column ">
                    <li class="list-group-item">
                        <span class="label-name col-md-2">Reason</span>
                        <span class="label-default col-md-10"><?php echo $trnqry->descs; ?></span>
                    </li>
                </ul>
            </div>
            <div class="portlet-body">
                <hr>
                <input type="hidden" id="moduleid" value="<?php echo $moduleid; ?>" />
                <table class="table table-bordered table-hover table-condensed" id="auditpaymenttable">
                    <thead>
                    <th>OR ID</th>
                    <th>Type</th>
                    <th>OR #</th>
                    <th>Amount Total</th>
                    <th>Amount Vat</th>
                    <th>Amount FR Tx</th>
                    <th>Desc</th>
                    <th>Pay Form</th>
                    <th><i class="fa fa-wrench"></i></th>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>

    </div>
    <div class="col-md-3">
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption">Summary</div>
            </div>
            <div class="portlet-body">
                <ul class="list-group summary column ">
                    <li class="list-group-item">
                        <span class="label-name col-md-6">Amount Total</span>
                        <span class="label-default col-md-6 number" id="auditamounttotal">0.00</span>
                    </li>
                    <li class="list-group-item">
                        <span class="label-name col-md-6">Amount Vat</span>
                        <span class="label-default col-md-6 number" id="auditamountvat">0.00</span>
                    </li>
                    <li class="list-group-item">
                        <span class="label-name col-md-6">Amount FR Tx</span>
                        <span class="label-default col-md-6 number" id="auditfrtx">0.00</span>
                    </li>
                </ul>
                <button  style="margin-top: 20px!important;" class="btn btn-primary pull-right" id="accomplishbtn"><i class="fa fa-check"></i> Accomplish</button>
            </div>
        </div>

    </div>
</div>

<script src="<?php echo base_url() ?>assets/pages/cnc/main.js"></script>
<script>
    CNC.orvoidaudit();
</script>