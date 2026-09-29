<?php
/**
 * Created by PhpStorm.
 * User: FADERON
 * Date: 3/26/2018
 * Time: 1:54 PM
 */

$moduleid = $this->input->post('moduleid');
$dataid = $this->input->post('acctid');

$qry_ref = $this->db->select()->from('prime_types_parameter')
    ->where(array('codes' => 'REFERRALS', 'status' => 1))
    ->get();
$qry_acct = $this->db->select()
    ->from('customer_accounts_main')
    ->where(array('sysid' => $dataid))
    ->get()->row();
if($qry_acct) {
$gdlb = get_acct_gdlb($dataid);
$mtrno = $qry_acct->mtrno;
$mtrser = $qry_acct->mtrserial;
?>
<style>
    .referrals label {
        font-size: 11px !important;
    }
</style>
<div class="row">
    <div class="col-md-3">

        <div class="form-group">
            <ul class="list-group summary column no-border">
                <li class="list-group-item">
                    <span class="label-name col-md-4">GDLB</span>
                    <span class="label-default col-md-8 number"><?php echo ($gdlb) ? $gdlb->GDLB : ''; ?></span>
                </li>
                <li class="list-group-item">
                    <span class="label-name col-md-4">Meter #</span>
                    <span class="label-default col-md-8 number"><?php echo $mtrno; ?></span>
                </li>
                <li class="list-group-item">
                    <span class="label-name col-md-4">Meter Serial</span>
                    <span class="label-default col-md-8 number"><?php echo $mtrser; ?></span>
                </li>
            </ul>
        </div>

        <div class="form-group form-md-radios">
            <label>Referrals</label>
            <div class="md-radio-list referrals bg-grey-steel bg-font-grey-steel" style="padding: 10px 10px;">
                <?php
                if($qry_ref->num_rows()>0) {
                    foreach($qry_ref->result() as $row) {
                       echo '<div class="md-radio">
                                <input id="radio1_'.$row->sysid.'" name="ref" value="'.$row->sysid.'" class="md-radiobtn" type="radio">
                                <label for="radio1_'.$row->sysid.'">
                                    <span class="inc"></span>
                                    <span class="check"></span>
                                    <span class="box"></span> ['.$row->names.'] '.$row->desc.'</label>
                            </div>';
                    }
                }
                ?>

            </div>
        </div>

    </div>
    <div class="col-md-5">
        <div class="tabbable-custom ">
            <ul class="nav nav-tabs reftrn">
                <li class="active">
                    <a href="#artbl" data-toggle="tab" aria-expanded="true"> Account Receivable / Billing </a>
                </li>
                <li class="">
                    <a href="#reftbl" data-toggle="tab" aria-expanded="false"> Referral Logs </a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="artbl">

                    <table class="table table-hover table-bordered tbl-sm" id="tbl_billing_hist_ref">
                        <thead>
                        <th>Month</th>
                        <th>Year</th>
                        <th>KWH Used</th>
                        <th>Billed Amount</th>
                        <th>Ref</th>
                        <th></th>
                        </thead>

                        <tbody>

                        </tbody>
                    </table>
                </div>
                <div class="tab-pane" id="reftbl">
                    <table class="table table-hover table-bordered tbl-sm" id="tbl_referrals_logs">
                        <thead>
                        <th>Month</th>
                        <th>Year</th>
                        <th>Billno</th>
                        <th>Codes</th>
                        <th></th>
                        </thead>

                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <input type="hidden" class="form-control" name="moduleid" value="<?php echo $moduleid;?>" />
        <input type="hidden" class="form-control" name="dataid" value="<?php echo $dataid;?>" />
        <ul class="list-group summary column no-border md-checkbox-list">
            <li class="list-group-item">
                <span class="has-success col-md-9 label-name">Date of Last Payments</span>
                <span class="col-md-3 label-default">0000-00-00</span>
            </li>
        </ul>
        <hr>
        <ul class="list-group summary column no-border md-checkbox-list">
            <li class="list-group-item">
                <span class="md-checkbox has-success col-md-9 label-default">Findings</span>
                <span class="col-md-3 label-default">#MO</span>
            </li>
            <li class="list-group-item">
                <span class="md-checkbox has-success col-md-9 label-name">
                    <input name="accomulated" id="checkbox_accomulated" class="md-check" type="checkbox">
                    <label for="checkbox_accomulated">
                        <span class="inc"></span>
                        <span class="check"></span>
                        <span class="box"></span> Accomulated </label>
                </span>

                <span class="col-md-3 label-default">
                    <input class="form-control input-sm" name="accomulatedmonths" />
                </span>
            </li>
            <li class="list-group-item">
                <span class="md-checkbox has-error col-md-9 label-name">
                    <input name="rvrequest"  id="checkbox10" class="md-check" type="checkbox">
                    <label for="checkbox10">
                        <span></span>
                        <span class="check"></span>
                        <span class="box"></span> With Request For Verification </label>
                </span>

            </li>
            <li class="list-group-item">
                <span class="md-checkbox has-warning col-md-9 label-name">
                    <input name="addbill"  id="checkbox11" class="md-check" type="checkbox">
                    <label for="checkbox11">
                        <span></span>
                        <span class="check"></span>
                        <span class="box"></span> Additional / Late / Estimated Bill </label>
                </span>
                <span class="col-md-3 label-default">
                    <input class="form-control input-sm" name="addbillmonths" />
                </span>
            </li>
        </ul>

        <div class="form-group form-md-line-input has-success">
            <textarea name="remarks" class="form-control" placeholder="Remarks..."></textarea>
            <label for="form_control_1">Remarks</label>
            <span class="help-block">State your remarks why referrals was made, (optional)!</span>
        </div>
    </div>
</div>

<?php
} else {
    page_construction();
}
?>



<script src="<?php echo base_url();?>assets/pages/inquiry/referrals.js" type="text/javascript"></script>
<script>
    REFERRALS.init();
</script>
