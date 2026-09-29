<?php
?>

<div class="portlet grey box margin-top-20">
    <div class="portlet-title" style="position: relative">
        <div class="caption">
            <i class="fa fa-map-o"></i>
            <span class="caption-subject font-green-sharp bold uppercase">Inspection Logs</span>
            <span class="caption-helper">Load Inspection Logs</span>
        </div>
    </div>
    <div class="portlet-body">
        <table width="100%" class="table table-condensed table-xs table-striped" id="tbl_inspection_logs">
            <thead>
            <tr>
                <th></th>
                <th>Remarks</th>
                <th>Date</th>
                <th>Items</th>
                <th>Load</th>
            </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>
</div>

<script src="<?php echo base_url(); ?>assets/pages/inspection/main.js" type="text/javascript"></script>
<script type="text/javascript">
    INSPECTION.logs(<?php echo $dataid;?>)
</script>
