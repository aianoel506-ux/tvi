
<style type="text/css">
    .dataTables_scrollBody{
        scrollbar-width: none !important;
    }

    ::-webkit-scrollbar {
        width: 0px;
    }
</style>

<div class="modal-body">
    <div class="row">
        <div class="col-md-8">
            <div class="portlet light">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-exchange"></i>
                        <span class="caption-subject bold uppercase">Rearrange Transaction Flow</span>
                    </div>
                </div>
                <div class="portlet-body">
                    <table class="table table-condensed table-bordered table-hover table-hover tbl-sm" id="trnflowstagestbl_details">
                        <thead>
                        <th>Level</th>
                        <th>Descriptions</th>
                        <th>Module ID</th>
                        <th>Move</th>
                        <th>Control</th>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="portlet light">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-exchange"></i>
                        <span class="caption-subject bold uppercase">Rearrange Transaction Flow</span>
                    </div>
                </div>
                <div class="portlet-body">

                </div>
            </div>
    </div>
</div>
<script src="<?php echo base_url(); ?>assets/pages/settings/trnflow.js"></script>
<script type="text/javascript">
    TRANSACTIONSFLOW.stages($('#trnflowstagestbl_details',document),<?php echo $id;?>);
</script>