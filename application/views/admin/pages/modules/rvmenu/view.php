
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

    $qry_rv_remarks = $this->db->select('remarks, createdby')
        ->from('ticketing_details_trails')
        ->where(array('ticketid' => $dataid, 'codes' => 'CWDRV'))
        ->order_by('datecreated', 'desc')
        ->get()->row();

    $rvremarks = ($qry_rv_remarks && $qry_rv_remarks->remarks != '') ? $qry_rv_remarks->remarks : 'None';
    $rvby = ($qry_rv_remarks) ? get_users_info($qry_rv_remarks->createdby)->lastname : 'Unknown';
    ?>
    <form id="frm_tagging" method="post" action="<?php echo base_url('cwdo/savervfindings'); ?>" >

        <input id="input_acct_id" type="hidden" value="<?php echo $info->acctid; ?>"/>
        <input id="input_ticket_id" type="hidden" value="<?php echo $dataid; ?>" name="ticketid"/>

        <div class="row">
            <div class="col-md-6">
                <div class="portlet light">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-edit"></i>
                            <span class="caption-subject font-green-sharp bold uppercase">Account Information</span>
                            <span class="caption-helper"></span>
                        </div>
                    </div>

                    <div class="portlet-body">
                        <ul class="list-group summary column no-border">

                            <li class="list-group-item">
                                <span class="col-md-6 label-name">Service #</span>
                                <span class="col-md-6 label-default"><?php echo $acct_info->servicenumber; ?></span>
                            </li>

                            <li class="list-group-item">
                                <span class="col-md-6 label-name">Name</span>
                                <span class="col-md-6 label-default"><?php echo $acct_info->name; ?></span>
                            </li>

                            <li class="list-group-item">
                                <span class="col-md-6 label-name">Address</span>
                                <span class="col-md-6 label-default"><?php echo $acct_info->address; ?></span>
                            </li>

                            <li class="list-group-item">
                                <span class="label-name col-md-6">GDLB</span>
                                <span class="label-default col-md-6"><?php echo ($gdlb) ? $gdlb->GDLB : ''; ?></span>
                            </li>

                            <li class="list-group-item">
                                <span class="label-name col-md-6">Meter #</span>
                                <span class="label-default col-md-6"><?php echo $mtrno; ?></span>
                            </li>

                            <li class="list-group-item">
                                <span class="label-name col-md-6">Meter Serial</span>
                                <span class="label-default col-md-6"><?php echo $mtrser; ?></span>
                            </li>

                            <li class="list-group-item">
                                <span class="col-md-6 label-name">Complaints Ticket Created</span>
                                <span class="col-md-6 label-default"><?php echo $info->datecreated; ?></span>
                            </li>

                            <li class="list-group-item">
                                <span class="col-md-6 label-name">Rate Class</span>
                                <span class="col-md-6 label-default"><?php echo $acct_info->classcode; ?></span>
                            </li>

                            <li class="list-group-item">
                                <span class="col-md-6 label-name">RV No.</span>
                                <span class="col-md-6 label-default"><?php echo str_pad($info->rvno, 6, '0', STR_PAD_LEFT); ?></span>
                            </li>

                            <li class="list-group-item">
                                <span class="col-md-6 label-name">Request By</span>
                                <span class="col-md-6 label-default"><?php echo $rvby; ?></span>
                            </li>

                            <li class="list-group-item">
                                <span class="col-md-6 label-name">RV Remarks</span>
                                <span class="col-md-6 label-default"><?php echo $rvremarks; ?></span>
                            </li>

                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="portlet light">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-edit"></i>
                            <span class="caption-subject font-green-sharp bold uppercase">Verification</span>
                            <span class="caption-helper"></span>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <input type="hidden" class="form-control" name="moduleid" value="<?php echo $moduleid;?>" />
                        <input type="hidden" class="form-control" name="dataid" value="<?php echo $info->acctid;?>" />

                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon">Mapping</span>
                                <input class="form-control" placeholder="Search Google location.." />
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-default"><i class="fa fa-search"></i></button>
                                </span>
                            </div>
                        </div>
                        <div id="map"style="width:100%; height: 150px">
                            <pre>
                                Error: Map API Failed to load.
                                Sub: API Key access is exceeded!
                            </pre>
                        </div>
                        <hr>
                        <div class="form-group">
                            <label class="form-label">Findings:</label>
                            <input name="ref" id="select2_ref" class="form-control" />
                        </div>
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