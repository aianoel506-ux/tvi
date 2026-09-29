<style>
    input.empty {
        font-family: FontAwesome;
        font-style: normal;
        font-weight: normal;
        text-decoration: inherit;
    }
</style>
<div class="portlet light">
    <div class="portlet-title">
        <div class="caption">
            Meter Reader Payroll
        </div>
    </div>
    <div class="portlet-body">
        <div class="col-lg-12 col-md-12 col-xs-12">
            <table class="table table-responsive table-hover table-striped table-condensed table-bordered tbl-xs" id="meter_reader_payroll">
                <thead>
                <th>#</th>
                <th>Emp ID</th>
                <th>Fullname</th>
                <th>GDLB</th>
                <th>Regular</th>
                <th>Special</th>
                <th>[R]Rate</th>
                <th>[S]Rate</th>
                <th>[R]Deduction</th>
                <th>[S]Deduction</th>
                <th>Amount</th>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>


<div class="portlet light">
    <div class="portlet-title">
        <div class="caption">
            <i class="icon-bar-chart font-green-sharp hide"></i>
            <i class="fa fa-file-o" aria-hidden="true"></i> Summary
        </div>
    </div>
    <div class="portlet-body">
        <div class="row">
            <div class="col-md-3">
                <ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span class="caption-subject font-green-sharp bold uppercase">Total REG[R] :</span>
                        <span class="caption-helper pull-right text-danger bold" id="total_regval">0</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span class="caption-subject font-green-sharp bold uppercase">Total SP[S] :</span>
                        <span class="caption-helper pull-right text-danger bold" id="total_spval">0</span>
                    </li>
                </ul>
            </div>

            <div class="col-md-3">
                <ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span class="caption-subject font-green-sharp bold uppercase">Total GDLB :</span>
                        <span class="caption-helper pull-right text-danger bold" id="total_gdlbval">0</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span class="caption-subject font-green-sharp bold uppercase">Total Amount :</span>
                        <span class="caption-helper pull-right text-danger bold" id="total_amountval">0</span>
                        <input type="hidden" id="hiddentotal_amountval" />
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>


<!--<button class="btn btn-default mt-sweetalert" data-title="Do you agree to the Terms and Conditions?" data-message="Duis mollis, est non commodo luctus, nisi erat porttitor ligula, mattis consectetur purus sit amet eget lacinia odio sem nec elit. Cras mattis consectetur purus sit amet fermentum." data-type="info" data-show-confirm-button="true" data-confirm-button-class="btn-success" data-show-cancel-button="true" data-cancel-button-class="btn-default" data-close-on-confirm="false" data-close-on-cancel="false" data-confirm-button-text="Yes, I agree" data-cancel-button-text="No, I disagree" data-popup-title-success="Thank you" data-popup-message-success="You have agreed to our Terms and Conditions" data-popup-title-cancel="Cancelled" data-popup-message-cancel="You have disagreed to our Terms and Conditions">Agree to Terms &amp; Conditions</button>-->

<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-sweetalert/ui-sweetalert.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/pages/payroll/main.js"></script>
<script src="<?php echo base_url(); ?>assets/pages/payroll/list.js"></script>


<script type="text/javascript">
    PAYROLL.init(128);
    LIST.init();
</script>
