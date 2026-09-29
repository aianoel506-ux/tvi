<input type="hidden" id="con_test_input" value="0" />
<!-- START PAGE CONTENT-->
<div class="page-content-wrapper animated fadeIn fast">
    <div class="page-content">

        <div class="page-bar">
            <ul class="page-breadcrumb">
                <li>System <small class="text-danger">Father Update</small></li>
            </ul>
        </div>

        <div class="portlet light bordered" style="margin-top: 20px;">
            <div class="portlet-title">
                <div class="caption">
                    <h3><i class="fa fa-warning text-danger"></i> <b class="font-yellow-casablanca">PECO</b> - Father Update (migrate data from father to ERP System database.</h3>
                </div>
            </div>
            <div class="portlet-body">


                <div class="row">
                    <div class="col-md-12">
                        <h4>Father Records: <span id="text_father_records" class="text-info text-bold">0</span></h4>
                    </div>
                </div>
                <div class="progress progress-striped active">
                    <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" id="stat_bar">
                    </div>
                </div>
                <div class="input-group">
            <span class="input-group-btn">
                <button id="btn_triggger" class="btn btn-success"><i class="fa fa-refresh"></i> Start</button>
            </span>
                    <span class="input-group-addon bg-red-flamingo bg-font-red-flamingo" id="conn_test" style="min-width: 100px;">
                        <i class="fa fa-wifi"></i>
                    </span>
                    <input class="form-control" id="input_last_servno" placeholder="Servno.." value="" style="width: 10%" />
                    <input class="form-control" id="input_last_num" placeholder="Count.." value="" style="width: 10%" />
                    <input class="form-control" disabled id="test_stat" value="" style="width: 70%" />
                    <input class="form-control bg-blue-chambray bg-font-blue-chambray" disabled id="test_per" value="" style="width: 10%; text-align: right;" />
                </div>
            </div>

        </div>
    </div>
</div>
<!-- END PAGE CONTENT-->

<script src="<?php echo base_url(); ?>assets/pages/settings.js"></script>
<script type="text/javascript">
    SETTINGS.fatherupdate();
</script>