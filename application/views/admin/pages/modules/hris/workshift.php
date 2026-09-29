<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" />

<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/datatables/dataTables.bootstrap.css">


<style>
    .form-md-line-input {
        posiotn: relative !important;	
    }
    .form-md-line-input .fileinput .input-group-addon{
        background: rgba(177,176,176,0.47) !important;
        z-index: 3000 !important;	
    }
    .form-md-line-input .fileinput .input-group-addon .btn.red-intense {
        background: rgba(251,124,126,0.77) !important;
    }
    .form-md-line-input .select2-container{
        margin-bottom: 0px !important;
    }
    .select2-drop{
        margin-top: -15px !important;
    }
    .portlet.table {
        padding: 0px 0px !important;	
    }

</style>
<style>
    .tabletools-dropdown-on-portlet
    {
        margin-top: 10px;
        margin-right: 10px;
    }


</style>


        <h3 class="page-title">
            <?php echo $pagename->pname; ?> <small><?php echo $pagename->desc; ?></small>
        </h3>
        <div class="row">
            <form role="form" class="form-horizontal asset-entry-form" id="entry-form-ajaxify">	

                <div class="col-md-12">
                    <div class="portlet light table ">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-edit"></i>
                                <span class="caption-subject font-green-sharp bold uppercase">Employee List</span>
                                <span class="caption-helper"></span>


                            </div>
                            <div class="tabbable-line pull-right col-md-6">
                                <ul class="nav nav-tabs pull-right">
                                    <li class="dropdown ">
                                        <a href="javascript:;" id="myTabDrop1" class="dropdown-toggle" data-toggle="dropdown">
                                            Department <i class="fa fa-angle-down"></i>
                                        </a>
                                        <ul class="dropdown-menu" role="menu" aria-labelledby="myTabDrop1">
                                            <?php
                                            if (select_department()) {
                                               foreach (select_department() as $row) {
                                                  $default = '';
                                                  if ($row->sysid == 1) {
                                                     $default = 'selected="selected"';
                                                  }
                                                  echo '<option> ' . $row->desc . '</option>';
                                               }
                                            }
                                            ?>
                                            <!-- <li>
                                                 <a href="#tab_2_3" tabindex="-1" data-toggle="tab">
                                                     Option 1 </a>
                                             </li> -->

                                        </ul>
                                    </li>
                                </ul>

                                <ul class="nav nav-tabs emp-stat-btn pull-right" style="margin-left: 50px;">
                                    <li data-stat="1" type="button" class="active"><a href="#active" data-toggle="tab"><i class="fa fa-check"></i> Active</a></li>
                                    <li data-stat="0" type="button"><a href="#inactive" data-toggle="tab"><i class="fa fa-times"></i> In-Active</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="portlet-body ">

                            <table class="table table-responsive table-hover table-striped table-condensed table-bordered" id="emptable">
                                <thead>
                                <th class=""></th>

                                <th>Emp. Code</th>
                                <th><i class="fa fa-venus-mars fa-fw text-info"></i> Last Name</th>
                                <th><i class="fa fa-user fa-fw text-info"></i> First Name</th>
                                <th><i class="fa fa-user fa-fw text-info"></i> Middle Name</th>
                                <th><i class="fa fa-user fa-fw text-info"></i> Department</th>
                                <th class="no-print"><i class="fa fa-gears fa-fw text-info"></i> </th>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>

                        </div>		

                    </div>
                </div>

            </form>

        </div>
        <!-- END PAGE HEADER-->
        <!-- BEGIN PAGE CONTENT-->



<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/datatables/extensions/TableTools/js/dataTables.tableTools.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/datatables/extensions/ColReorder/js/dataTables.colReorder.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/datatables/extensions/Scroller/js/dataTables.scroller.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/datatables/jquery.dataTables.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/datatables/dataTables.bootstrap.js"></script>
<script src="<?php echo base_url(); ?>assets/pages/hris/view.js"></script>


<script type="text/javascript">
   HRIS.list('<?php echo $this->uri->segment(2) ?>');

</script>
