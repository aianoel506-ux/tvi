<?php
$ids = $this->input->post('ids');
?>

<div class="modal-body">
    <div class="row">
        <div class="col-md-12">

            <table id="tbl_ecales_templates" class="table table-bordered table-hover table-striped table-condensed table-striped">
                <thead>
                    <th><i class="fa fa-bars"></i></th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Controls</th>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="modal-footer">

</div>
<script src="<?php echo base_url(); ?>/assets/pages/ecales.js" type="text/javascript"></script>
<script>
    ECALES.templates(<?php echo $ids;?>);
</script>