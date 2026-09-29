<?php
/**
 * Created by PhpStorm.
 * User: ITD-SE
 * Date: 1/22/2019
 * Time: 5:32 PM
 */

$qry_billing_rates_group = $this->db->select()
    ->from('trn_billing_rates_requests_group')
    ->where(array('sysid' => $dataid))
    ->get()->row();


$check_trails = $this->db->select('stageid')
    ->from('transaction_request_main_trails')
    ->where(array('sysid' => $trailsid))
    ->get()->row();
if($check_trails && $qry_billing_rates_group) {
    $year = $qry_billing_rates_group->years;
    $month = $qry_billing_rates_group->months;
    $stats = $qry_billing_rates_group->status;
    $rates_details = '<span class="bold">'.$year.' - '.date_formating($month, '!m', 'M').'</span>';
    ?>

    <form id="frm_rates_approval" method="post" action="<?php echo base_url('billing/billingratesapproval'); ?>">

        <input type="hidden" class="form-control" placeholder="Year" value="<?php echo $year ?>" name="filteryear" id="filteryear"/>
        <input type="hidden" class="form-control" placeholder="Year" value="<?php echo $month ?>" name="filtermonth" id="filtermonth"/>

        <div class="portlet light box">
            <div class="portlet-body table">
                <div class="row">
                    <div class="col-md-12 margin-bottom-20">

                        <div class="col-md-6 margin-top-10">
                            <i class="fa fa-warning text-danger"></i>
                            <span class="caption-subject font-green-sharp uppercase"> Rate Charges <?php echo $rates_details; ?></span>
                        </div>
                        <table class="table table-bordered table-hover table-condensed tbl-xs" id="tbl_bill_rate">
                            <thead>
                            <th>#</th>
                            </thead>

                        </table>
                    </div>
                </div>
                <hr>

                <?php if($stats==364) {
                    if($check_trails->stageid != 46) {
                        ?>

                        <div class="row" id="confirm_button">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="input-group">
                                        <input class="form-control" name="remarks" placeholder="Remarks.."/>
                                        <span class="input-group-btn">
                            <button type="submit" class="btn btn-success"><i class="fa fa-check"></i> Lock</button>
                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php
                    }
                } else {
                ?>
                        <div class="row" id="confirm_button">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="input-group">
                                        <input class="form-control" name="remarks" placeholder="Remarks.." />
                                        <span class="input-group-btn">
                                    <button type="submit" class="btn btn-danger"><i class="fa fa-check"></i> Confirm Rates</button>
                                </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                <?php } ?>
            </div>
        </div>
    </form>

    <?php
} else {
    page_data_notfound();
}
?>

<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-select/bootstrap-select.min.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/select2/select2.min.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/datatables/jquery.dataTables.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/datatables/dataTables.bootstrap.min.js" type="text/javascript"></script>



<script src="<?php echo base_url(); ?>assets/pages/billing/main.js" type="text/javascript"></script>
<script>
    BILLING.rateapproval(true);
</script>
