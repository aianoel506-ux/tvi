<?php
$ids = $this->input->post('ids');

$sys_size = '';

$qry_customer_system_size = $this->db->select()
    ->from('application_customers_system_size')
    ->where(array('status > ' => 0, 'appid' => $ids))
    ->order_by('sysid', 'desc')
    ->get()->row();
$total_power = ($qry_customer_system_size) ? $qry_customer_system_size->power : 0;

if($qry_customer_system_size) {
    $get_power_size = $this->db->select()->from('customer_system_size')
        ->where(array('sysid' => $qry_customer_system_size->sysize))
        ->get()->row();
    $sys_size = ($get_power_size) ? $get_power_size->descs : '';
}

?>
<style type="text/css">
    #scrollbox {
        overflow-y: auto;
        max-height: calc(90vh - 150px);
    }
</style>

<div class="row">
    <div class="col-md-6">
        <h4 class="bold" style="margin: 10px 15px;"><i class="fa fa-tag"></i> Total Power:
            <span id="total_watt" class="font-red-flamingo"><?php echo number_format($total_power);?></span> /
            <span class="font-blue"><?php echo $sys_size; ?></span>
        </h4>
    </div>
    <div class="col-md-6">
        <div class="tabbable-line pull-right" style="margin: 0px 10px;">
            <ul class="nav nav-tabs ">

                <li class="active ">
                    <a href="#inspection_log" data-toggle="tab" aria-expanded="true">
                        Inspection Logs
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
<hr style="margin: 0px 0px;">
<div class="modal-body tab-content">
    <div class="tab-pane " id="inspection_log_active">
        <table width="100%" class="table table-striped table-hover table-condensed tbl-sm" id="tbl_load_equipments">
            <thead>
            <tr>
                <th></th>
                <th>Codes</th>
                <th>Descriptions</th>
                <th>Power</th>
                <th align="center">NOP</th>
                <th align="center">Total</th>
            </tr>
            </thead>
            <tbody></tbody>
            <tfoot>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="text-align-center"><span class="text-danger" id="load_total_qty">0</span></td>
                <td><span class="pull-right text-danger" id="load_total_watts">0</span></td>
            </tr>
            </tfoot>
        </table>
    </div>

    <div class="tab-pane active" id="inspection_log">
        <table width="100%" class="table table-condensed table-xs table-striped" id="tbl_inspection_logs">
            <thead>
            <tr>
                <th></th>
                <th>#</th>
                <th>Remarks</th>
                <th>Date</th>
                <th>NOP</th>
                <th>Power</th>
                <th>Inspector</th>
                <th>Entered</th>
                <th>Active</th>
            </tr>
            </thead>
            <tbody>

            </tbody>
        </table>

    </div>
</div>

<script src="<?php echo base_url(); ?>assets/pages/inspection/main.js" type="text/javascript"></script>

<script type="text/javascript">
    INSPECTION.logs(<?php echo $ids; ?>);
</script>