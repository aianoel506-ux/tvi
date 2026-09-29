<?php
$ids = $this->input->post('ids');
?>
<div class="modal-body">

    <div class="col-md-6 pull-left" style="margin-left: -15px;">
        <button class="btn btn-default btn-sm"><i class="fa fa-print"></i> Print</button>
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

<script src="<?php echo base_url(); ?>assets/pages/cad/newaccount.js" type="text/javascript"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $("a.iframe").fancybox({
            'width': 640, // or whatever you wantc
            'height': 480, // or whatever you want
            'type': 'iframe'
        });
    });
    CAD.requirements(<?php echo $ids; ?>);
</script>