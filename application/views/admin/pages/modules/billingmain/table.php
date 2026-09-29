<style type="text/css">
    #tbl_charges_main tr td:hover {
         cursor: pointer !important;
    }
</style>
 

        <h3 class="page-title">
            <i class="fa <?php echo $pageicon; ?> fa-fw text-<?php echo $pageclass; ?>"></i><span class="text-<?php echo $pageclass; ?>"><?php echo $pagetitle; ?></span>
            <small> Billing Charges Matrix Table</small>
        </h3>

		<!-- START PAGE CONTENT-->
        <div class="row">
            <div class="col-md-7">
                <div class="portlet light table">
                    <div class="portlet-title">
                        <div class="caption"><i class="fa fa-tag fa-fw"></i> Charges Main</div>
                        <div class="tools">
                            <div class="input-group">
                            </div>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <table class="table table-striped table-hover table-bordered" id="tbl_charges_main">
                            <thead>
                            <th>#</th>
                            <th>Codes</th>
                            <th>Descriptions</th>
                            <th>Year</th>
                            <th>Month</th>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-5">
                <div class="portlet blue box table">
                    <div class="portlet-title">
                        <div class="caption" id="comp_title"><i class="fa fa-tag fa-fw"></i> Composition</div>
                    </div>
                    <div class="portlet-body" >
                        <table style="width: 100%" class="table table-condensed table-bordered" id="comp_container">
                            <thead>
                            <th>Rate Name</th>
                            <th>Current Rate</th>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

<script type="text/javascript" src="<?php echo base_url();?>assets/pages/billing/maintenance.js"></script>
<script type="text/javascript">
    BILLMAIN.init();
</script>

