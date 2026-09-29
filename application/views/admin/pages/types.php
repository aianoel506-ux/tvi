
<link href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-colorpicker/css/colorpicker.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>assets/global/plugins/jquery-minicolors/jquery.minicolors.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>assets/global/plugins/fullcalendar/fullcalendar.min.css" rel="stylesheet" type="text/css"/>

<style>
    .table tbody td {
        position: relative;
    }
    .color-picker {
        position: absolute;
        top: 0px; bottom: 0px; right: 0px;
        width: 15%;
    }
    .full-block {
        position: absolute;
        top: 0px; bottom: 0px; right: 0px;
        width: 15%;
    }


    .table tbody td input.inline{
        padding: 0px 0px;
        font-size: 14px !important;
        width: 100%;
        margin: 0px 0px;
    }
</style>


<div class="page-content-wrapper animated fadeIn fast">
    <div class="page-content">

				<div class="page-bar">
                    <?php echo create_breadcrumb(); ?>
					<div class="page-toolbar">

					</div>
				</div>
				<!-- END PAGE HEADER-->
				<!-- BEGIN PAGE CONTENT-->
				
				
                
                <div id="parameter_pages" class="tab-content">
                    <div class="tab-pane active fade in" id="parameters">


                        <h3><i class="fa fa-road"></i> System Parameters</h3>
                        <div class="col-md-6 pull-left">
                            <div class="tabbable-line">
                                <ul class="nav nav-tabs " id="filter_btn">
                                    <li class="active">
                                        <a data-id="1" href="#" data-toggle="tab"> <i class="fa fa-check text-success"></i> Active </a>
                                    </li>
                                    <li>
                                        <a data-id="0" href="#" data-toggle="tab"> <i class="fa fa-times text-danger"></i> In-Active </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <table class="table table-hover table-bordered table-condensed table-striped" id="tbl_parameters">
                            <thead>
                                <th>#</th>
                                <th>CODES</th>
                                <th>NAMES</th>
                                <th>DESCRIPTIONS</th>
                                <th>TXT COLOR</th>
                                <th>BG COLOR</th>
                                <th>ICON</th>
                                <th></th>
                            </thead>
                            <tbody></tbody>
                        </table>
                        <form id="frm_add_parementer" action="<?php echo base_url('systems/addparementer'); ?>" method="post">
                            <table style="width: 100%;">
                                <tbody>

                                <tr>
                                    <td width="5%">ADD</td>
                                    <td width="12%"><input name="codes" required id="" class="form-control inline" placeholder="Codes.." /></td>
                                    <td width=""><input name="names" required class="form-control inline" placeholder="Names.." /></td>
                                    <td width=""><input name="desc" class="form-control inline" placeholder="Descriptions.." /></td>
                                    <td width="15%"><input name="colortxt" id="add_txtcolor" class="form-control inline" placeholder="Text Color" /></td>
                                    <td width="15%"><input name="colorbg" id="add_bgcolor" class="form-control inline" placeholder="BG Color" /></td>
                                    <td width="15%"><input name="icons" id="add_icon" class="form-control inline" placeholder="Icon" /></td>
                                    <td width="6%"><button type="submit" class="btn btn-default inline">Save</button></td>
                                </tr>
                                </tbody>
                            </table>
                        </form>
                        <hr>
                    </div>
                    <div class="tab-pane fade in" id="icons">
                        <h3>Icons</h3>
                    </div>
                </div>
                
				<!-- END PAGE CONTENT-->
			</div>
            
</div>

<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery-minicolors/jquery.minicolors.min.js" type="text/javascript"></script>

<script src="<?php echo base_url(); ?>assets/pages/maintenance.js"></script>
<script>

    MAIN.init();
</script>