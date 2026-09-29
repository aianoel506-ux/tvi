<?php
$trans = $this->db->select('trmt.*,	tfms.`desc`')
    ->from('transaction_request_main_trails as trmt')
    ->join('prime_transaction_flow_main_stages as tfms','trmt.stageid = tfms.sysid','left')
    ->where('trmt.dataid',$dataid)
    ->order_by('trmt.datecreated','DESC')
    ->get()->row();
?>


<div class="portlet light bordered">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-edit"></i>
            <span class="caption-subject font-green-sharp bold uppercase">Requirements</span>
            <span class="caption-helper">
                <a href="javascript:;" id="btn_reload_req" class="btn btn-default btn-xs inline text-align-right"><i class="fa fa-retweet"></i> Reload</a>
            </span>
        </div>
        <div class="tools btn-group">
            <?php if ($trans->stageid != 6) {?>
                <a class="btn btn-primary btn-xs inline" href="<?php echo base_url();?>cad/printrequirements/<?php echo $dataid; ?>" target="_blank" id="btn_reprint_requirements" data-id="<?php echo $dataid; ?>"><i class="fa fa-print"></i> Print</a>
                <a class="btn btn-primary btn-xs inline" href="javascript:;" id="btn_email_requirements" data-id="<?php echo $dataid; ?>"><i class="fa fa-envelope"></i> List</a>
            <?php }?>
        </div>
    </div>
    <div class="portlet-body">

        <div class="col-md-6 pull-left" style="margin-left: -15px;">
            <a class="btn btn-default inline" id='btn_add_app_req' data-toggle="ajax-modal" href="#frm_cad_add_requirements" data-arr="<?php echo $dataid;?>"><i class="fa fa-plus"></i> Add Requirements</a>
        </div>

        <table class="table table-hover table-striped table-condensed" id="tbl_requirements_list">
            <thead>
            <th>#</th>
            <th>Name</th>
            <th>Complied</th>
            <th><i class="fa fa-wrench"></i></th>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<script src="<?php echo base_url(); ?>assets/pages/cad/newaccount.js" type="text/javascript"></script>
<script type="text/javascript">
    CAD.requirements(<?php echo $dataid; ?>);

    $(document).ready(function () {
        $("a.iframe").fancybox({
            'width': 640, // or whatever you want
            'height': 480, // or whatever you want
            'type': 'iframe'
        });
    });
</script>
