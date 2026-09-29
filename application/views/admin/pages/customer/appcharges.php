<?php
$trans = $this->db->select('trmt.*,	tfms.`desc`')
    ->from('transaction_request_main_trails as trmt')
    ->join('prime_transaction_flow_main_stages as tfms','trmt.stageid = tfms.sysid','left')
    ->where('trmt.dataid',$dataid)
    ->order_by('trmt.datecreated','DESC')
    ->get()->row();

$user = get_user_info_main_role();
?>
<div class="portlet light bordered">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-edit"></i>
            <span class="caption-subject font-green-sharp bold uppercase">Charges</span>
            <span class="caption-helper">

            </span>
        </div>
        <div class="tools btn-group">
            <?php if (super_admin() || $user->roleid == 8) {?>
                <a href="#frm_overide_amt" data-toggle="ajax-modal" data-arr="3" class="btn btn-primary inline btn-xs tooltips" id="overide_amt"><i class="fa fa-edit"></i> Override</a>
            <?php }?>
            <a href="javascript:;" id="btn_reload_charges" class="btn btn-default btn-xs inline text-align-right"><i class="fa fa-refresh"></i> Reload</a>
        </div>
    </div>
    <div class="portlet-body">
        <!--<ul class="list-group summary column no-border" id="charges_list">
        </ul>-->
        <table width="100%" class="table table-condensed table-hover table-bordered table-striped" id="tbl_charges_list">
            <thead>
                <th>Name/Desc</th>
                <th>Amount</th>
                <th>Status</th>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>
    <div class="portlet-footer">
        <div class="row" style="margin: 0px 0px">
            <div class="col-md-9">
                <span class="text-info bold" style="font-size: 20px !important;">Total Charges</span>
                <span class="pull-right">:</span>
            </div>
            <div class="col-md-3">
                <span class="number text-danger bold" id="total_charges" style="font-size: 20px !important;">0.00</span>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url(); ?>assets/pages/cad/newaccount.js" type="text/javascript"></script>
<script type="text/javascript">
    CAD.charges(<?php echo $dataid;?>,<?php echo $origin;?>);
</script>
