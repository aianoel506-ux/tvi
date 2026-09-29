<?php
$trnid = $this->uri->segment(4);
$qry_trn = $this->db->select()->from('transaction_request_main_trails')->where('sysid', $trnid)->get()->row();
$qry_stg = $this->db->select()->from('prime_transaction_flow_main_stages')->where(array('sysid' => $qry_trn->stageid))->get()->row();
$flowid = $qry_stg->flowid;
?>
<!-- DATEPICKER CSS START!-->
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-datepicker/css/datepicker3.css">
<!-- DATEPICKER CSS END!-->
<style>
    /* Chrome, Safari, Edge, Opera */
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    /* Firefox */
    input[type=number] {
        -moz-appearance: textfield;
    }
</style>

<div class="row tab-pane fade in">
    <div class="col-md-6">
        <?php
        $appdetails = get_application_details($dataid);
        $rateclassid = (isset($appdetails->rateclassid)) ? $appdetails->rateclassid : false;
        customer_application_basicinfo($dataid, true, false);
        ?>
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    <span class="caption-subject font-red-flamingo bold uppercase"> Distribution Utility</span>
                </div>
            </div>
            <div class="portlet-body">
                <form id="frm_du_update" method="post" action="<?php echo base_url();?>cad/updatedistutility">
                <div class="form-group row">
                    <div class="col-md-8">
                        <label class="control-label bold uppercase">DU Name</label>
                        <input class="form-control" id="select2_du" name="distutility" placeholder="Distribution Utility..." value="<?php echo $appdetails->info->duid;?>">
                    </div>
                    <div class="col-md-4">
                        <label class="control-label bold uppercase">DU Rate</label>
                        <input type="number" step="any" class="form-control" id="durate" name="durate" placeholder="DU Rate..." value="<?php echo $appdetails->info->durate;?>">
                    </div>
                </div>
                <div class="portlet-footer">
                    <button type="submit" class="btn btn-primary pull-right" style="padding-top: 10px"><i class="fa fa-save"></i> Save</button>
                </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    <span class="caption-subject font-red-flamingo bold uppercase"> Preview</span>
                </div>
                <div class="tools">
                    <div class="btn-group">
                        <a href="javascript:" id="btn_reload_preview" class="btn btn-primary btn-sm inline"><i class="fa fa-refresh"></i> Refresh</a>
                        <a href="javascript:" id="btn_open_preview" class="btn btn-primary btn-sm inline"><i class="fa fa-search"></i> Open in Tab</a>
                    </div>
                </div>
            </div>
            <div class="portlet-body">
                <!--<embed src="<?php echo base_url();?>cad/getproposalpdf/<?php echo $dataid;?>" width="100%" height="500"
                       type="application/pdf">-->
                <iframe id="iframe_prop_preview" src="" style="width:100%; height:500px;" frameborder="0"></iframe>
                <div class="portlet-footer btn-group" id="preview_actions">


                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url(); ?>assets/pages/cad/newaccount.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/pages/sales/main.js" type="text/javascript"></script>

<script type="text/javascript">
    //CAD.profile(<?php echo $dataid; ?>, <?php echo $flowid;?>);
    SALES.init(<?php echo $dataid; ?>);
</script>