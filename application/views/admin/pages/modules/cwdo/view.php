
<style>
    .gallery-thumb {
        height: 70px;
        width: 70px;
        margin: 5px 2px;
    }
    #tbl_view_ar td {
        cursor: pointer;
    }
    .referrals label {
        font-size: 11px !important;
    }
</style>


<?php
/**
 * Created by PhpStorm.
 * User: SE
 * Date: 5/10/2018
 * Time: 5:50 PM
 */

$info = $this->model_cwdo->get_ticket_info($dataid);


$folder = 'uploads/attachments/cwdo/'.$dataid.'/';
$filetype = '*.*';
$files = glob($folder.$filetype);
$file_count = count($files);
if($info) {
    $acct_info = get_active_account_info($info->acctid);
    /**
     * Created by PhpStorm.
     * User: FADERON
     * Date: 3/26/2018
     * Time: 1:54 PM
     */

    $moduleid = 41;

    $qry_ref = $this->db->select()->from('prime_types_parameter')
        ->where(array('codes' => 'REFERRALS', 'status' => 1))
        ->get();
    $gdlb = get_acct_gdlb($info->acctid);
    $mtrno = $acct_info->mtrno;
    $mtrser = $acct_info->mtrserial;
    ?>
    <form id="frm_tagging" method="post" action="<?php echo base_url('cwdo/savetagging'); ?>" >

        <input id="input_acct_id" type="hidden" value="<?php echo $info->acctid; ?>"/>
        <input id="input_ticket_id" type="hidden" value="<?php echo $dataid; ?>" name="ticketid"/>

        <div class="row">
            <div class="col-md-8">
                <div class="portlet light">

                    <div class="portlet-body">
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="list-group summary column no-border">
                                    <li class="list-group-item">
                                        <span class="label-name col-md-7">GDLB</span>
                                        <span class="label-default col-md-5 number"><?php echo ($gdlb) ? $gdlb->GDLB : ''; ?></span>
                                    </li>
                                    <li class="list-group-item">
                                        <span class="label-name col-md-7">Meter #</span>
                                        <span class="label-default col-md-5 number"><?php echo $mtrno; ?></span>
                                    </li>
                                    <li class="list-group-item">
                                        <span class="label-name col-md-7">Meter Serial</span>
                                        <span class="label-default col-md-5 number"><?php echo $mtrser; ?></span>
                                    </li>
                                    <li class="list-group-item">
                                        <span class="col-md-7 label-name">Complaints Ticket Created</span>
                                        <span class="col-md-5 label-default number"><?php echo $info->datecreated; ?></span>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="list-group summary column no-border">
                                    <li class="list-group-item">
                                        <span class="col-md-4 label-name">Service #</span>
                                        <span class="col-md-8 label-default"><?php echo $acct_info->servicenumber; ?></span>
                                    </li>
                                    <li class="list-group-item">
                                        <span class="col-md-4 label-name">Name</span>
                                        <span class="col-md-8 label-default"><?php echo $acct_info->name; ?></span>
                                    </li>
                                    <li class="list-group-item">
                                        <span class="col-md-4 label-name">Address</span>
                                        <span class="col-md-8 label-default"><?php echo $acct_info->address; ?></span>
                                    </li>
                                    <li class="list-group-item">
                                        <span class="col-md-4 label-name">Rate Class</span>
                                        <span class="col-md-8 label-default"><?php echo $acct_info->classcode; ?></span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
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
                            <table id="tbl_view_ar" class="table table-hover table-bordered table-condensed table-striped ">
                                <thead>
                                <th>MO</th>
                                <th>YR</th>
                                <th>Current</th>
                                <th>KWH</th>
                                <th>Reading</th>
                                <th>R.Date</th>
                                <th>Reff</th>
                                <th></th>
                                </thead>
                                <tbody></tbody>
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
                <div class="portlet light">
                    <div class="portlet-body">
                        <input type="hidden" class="form-control" name="moduleid" value="<?php echo $moduleid;?>" />
                        <input type="hidden" class="form-control" name="dataid" value="<?php echo $info->acctid;?>" />

                        <ul class="list-group summary column no-border md-checkbox-list">
                            <li class="list-group-item">
                                <span class="has-success col-md-6 label-name">Date of Last Payments</span>
                                <span class="col-md-6 label-default">0000-00-00</span>
                            </li>
                        </ul>
                        <hr>
                        <div class="form-group">
                            <label class="form-label">Referrals:</label>
                            <input name="ref" id="select2_ref" class="form-control" />
                        </div>
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

                        <div class="form-actions">
                            <button type="submit" id="" class="btn btn-primary">Save</button>
                            <button type="reset" id="" class="btn btn-default">Reset</button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </form>
    <?php
} else {
    page_construction();
}
?>



<script src="<?php echo base_url();?>assets/pages/inquiry/referrals.js" type="text/javascript"></script>
<script>
    REFERRALS.init();
</script>