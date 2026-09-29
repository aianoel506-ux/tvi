<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.6/css/buttons.dataTables.min.css"/>

<div class="portlet light bordered">
    <div class="portlet-title">
        <div class="caption  tabbable-line">

            <ul class="nav nav-tabs">
                <li class=""><a><i class="fa fa-user"></i>User's Report</a></li>
                <li class="active"><a data-toggle="tab" href="#users_list"><i class="fa fa-user"></i> User</a></li>
                <li class=""><a data-toggle="tab" href="#users_access"><i class="fa fa-user-secret"></i> User Access</a></li>
            </ul>
        </div>
        <div class="tools">
        </div>
    </div>
    <div class="portlet-body tab-content">
        <div id="users_list" class="tab-pane fade in active">
            <table class="table table-condensed table-bordered table-hover" id="usersreporttbl">
                <thead>
                <th></th>
                <th></th>
                <th>Firstname</th>
                <th>Lastname</th>
                <th>Session Date Time</th>
                <th>Logcount</th>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>

        <div id="users_access" class="tab-pane fade in">
            <div class="col-md-6 pull-left">
                <button id="btn_copy_access_table" class="btn btn-default">Copy Table</button>
            </div>
            <table class="table table-hover table-condensed table-striped" id="tbl_user_access_group">
                <thead>
                    <th>#</th>
                    <th>User Name</th>
                    <th>Group</th>
                    <th>Modules</th>
                </thead>
                <tbody>

                </tbody>
            </table>


mo
        </div>
    </div>
</div>

<!--
<script src="https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js" type="text/javascript"></script>
<script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.flash.min.js" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js" type="text/javascript"></script>
<script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js" type="text/javascript"></script>
<script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.print.min.js" type="text/javascript"></script>
-->

<script src="<?php echo base_url() ?>assets/global/plugins/datatables/dataTables.buttons.min.js" type="text/javascript"></script>
<script src="<?php echo base_url() ?>assets/global/plugins/datatables/extensions/buttons/buttons.flash.min.js" type="text/javascript"></script>
<script src="<?php echo base_url() ?>assets/global/plugins/datatables/extensions/buttons/buttons.html5.min.js" type="text/javascript"></script>
<script src="<?php echo base_url() ?>assets/global/plugins/datatables/extensions/buttons/jszip.min.js" type="text/javascript"></script>
<script src="<?php echo base_url() ?>assets/global/plugins/datatables/extensions/buttons/vfs_fonts.js" type="text/javascript"></script>
<script src="<?php echo base_url() ?>assets/global/plugins/datatables/extensions/buttons/pdfmake.min.js" type="text/javascript"></script>
<script src="<?php echo base_url() ?>assets/global/plugins/datatables/extensions/buttons/buttons.print.min.js" type="text/javascript"></script>

<script src="<?php echo base_url() ?>assets/pages/reportusers/usersreport.js"></script>

<script type="text/javascript">
    USERSREPORT.init();
</script>