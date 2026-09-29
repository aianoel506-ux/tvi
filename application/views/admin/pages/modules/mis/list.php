<?php


$monday = date( 'Y-m-d', strtotime( 'monday this week' ) );
$friday = date( 'Y-m-d', strtotime( 'friday this week' ) );

?>

<link href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/css/fileinput.css" media="all" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/themes/explorer/theme.css" media="all" rel="stylesheet" type="text/css"/>

<style>
    #btn_types_group button {
        margin-bottom: 5px;
    }
    #btn_types_group button.active {
        border-bottom: 5px solid #000;
        margin-bottom: 0px;
    }
    .mtr-entry-row .form-control{
        font-size: 18px !important;
        padding: 2px 2px !important;
        height: 22px !important;
    }
</style>
<!-- START PAGE CONTENT-->
<div class="row">
    <form action="<?php echo base_url(); ?>assets/addnewmtr" method="post" id="frm_add_new_mtr">

        <div class="col-md-12">
            <div class="portlet light">
                <div class="portlet-title tabbable-line">

                    <div class="col-md-7 pull-left" style="margin-left: -15px !important;">
                        <div class="input-group">
                        <span class="input-group-btn">
                            <a href="#form_add_new_meter" title="New Meter" data-toggle="ajax-modal" class="btn btn-primary"><i class="fa fa-plus"></i> Add New</a>
                        </span>
                            <span class="input-group-addon">Limit</span>
                            <input type="text" id="limit" name="limit" class="form-control " style="width: 50px;"  value="50" />
                            <span class="input-group-addon">From</span>
                            <input type="date" id="datestart" name="datestart" class="form-control "  value="<?php echo $monday; ?>" />
                            <span class="input-group-addon">To</span>
                            <input type="date" id="dateend" name="dateend" class="form-control "  value="<?php echo $friday; ?>" />
                            <span class="input-group-btn">
                                <button id="submitfiltersearch" type="button" class="btn btn-default"> <i class="fa fa-search"></i> Search </button>
                            </span>
                        </div>
                    </div>

                    <a class="btn btn-primary pull-right" title="Meter Releasing" href="#frm_mis_releasing" data-toggle="ajax-modal" aria-expanded="false"> <i class="fa fa-sign-out"></i> Meter Releasing </a>


                    <!--
                    -->
                </div>

                <div class="portlet-body">
                    <div class="col-md-4 pull-left" style="padding-left: 0px;">
                        <div class="input-group">
                            <input onkeypress="return noenter()" id="itemsearch" class="form-control" placeholder="Search: Name/Meter Number/Serial" />
                            <span class="input-group-btn">
                            <button type="button" id="searchbtn" class="btn btn-default"><i class="fa fa-search"></i></button>
                        </span>

                        </div>
                    </div>

                    <ul class="nav nav-tabs meterstat">
                        <li class="active">
                            <a href="javascript:;" data-id="1" data-toggle="tab" aria-expanded="true"> Active </a>
                        </li>
                        <li class="">
                            <a href="javascript:;" data-id="2" data-toggle="tab" aria-expanded="false"> Issued </a>
                        </li>
                    </ul>
                    <div class="pull-right activestats">
                        <!--
                        <div class="btn-group pull-right" id="btn_types_group">
                            <?php
                            $buttons =  $this->db->select("asm.typesid , ptp.names , ptp.colortxt , ptp.colorbg")->from("assets_status_matrix as asm")
                                ->join("prime_types_parameter as ptp" , "ptp.sysid = asm.typesid && asm.codes = 'METER'" )
                                ->get();
                            if($buttons->num_rows() > 0){
                                foreach ($buttons->result() as $row){
                                    echo '<button type="button" data-id="'.$row->typesid.'" style="color:'.$row->colortxt.'; background-color:'.$row->colorbg.'" class="btn">'.$row->names.'</button> ';
                                }
                            }
                            ?>
                        </div>
                        -->
                    </div>

                    <table id="tbl_meter_list" class="table table-hover table-condensed table-striped tbl-sm">
                        <thead>
                        <th><i class="fa fa-reorder"></i></th>
                        <th>Meter#</th>
                        <th>Serial</th>
                        <th>Type</th>
                        <th>Make</th>
                        <th>ERC Seal</th>
                        <th>PECO Seal</th>
                        <th>Ampere</th>
                        <th>Volts</th>
                        <th>Reading</th>
                        <th>Mult</th>
                        <th>W.Size</th>
                        <th>Modified</th>
                        <th>Status</th>
                        <th>Control</th>
                        </thead>
                        <tbody>
                        </tbody>

                        <tfoot class="row-info mtr-entry-row">
                            <td><i class="fa fa-plus"></i></td>
                            <td>
                                <div class="input-icon left">
                                    <i class="fa fa-pencil"></i>
                                    <input name="mtrno" id="mtrno" placeholder="Mtr No." class="form-control inline" style="width: 100%;" />
                                </div>
                            </td>
                            <td>
                                <div class="input-icon left">
                                    <i class="fa fa-pencil"></i>
                                    <input name="serial" id="serial" placeholder="Serial.." class="form-control inline" style="width: 100%;" />
                                </div>
                            </td>
                            <td>
                                <div class="input-icon left">
                                    <i class="fa fa-pencil"></i>
                                    <input name="type" id="type" placeholder="Type.." class="form-control inline" style="width: 100%;" />
                                </div>
                            </td>
                            <td>
                                <div class="input-icon left">
                                    <i class="fa fa-pencil"></i>
                                    <input name="brand" id="brand" placeholder="Brand.." class="form-control inline" style="width: 100%;" />
                                </div>
                            </td>
                            <td>
                                <div class="input-icon left">
                                    <i class="fa fa-pencil"></i>
                                    <input name="ercseal" id="ercseal" placeholder="ERC Seal" class="form-control inline" style="width: 100%;" />
                                </div>
                            </td>
                            <td>
                                <div class="input-icon left">
                                    <i class="fa fa-pencil"></i>
                                    <input name="epcoseal" id="pecoseal" placeholder="PECO Seal.." class="form-control inline" style="width: 100%;" />
                                </div>
                            </td>
                            <td>
                                <div class="input-icon left">
                                    <i class="fa fa-pencil"></i>
                                    <input name="amps" id="amps" placeholder="Ampere.." class="form-control inline" style="width: 100%;" />
                                </div>
                            </td>
                            <td>
                                <div class="input-icon left">
                                    <i class="fa fa-pencil"></i>
                                    <input name="volts" id="volts" placeholder="Volts.." class="form-control inline" style="width: 100%;" />
                                </div>
                            </td>
                            <td>
                                <div class="input-icon left">
                                    <i class="fa fa-pencil"></i>
                                    <input name="reading" id="reading" placeholder="Reading.." class="form-control inline" style="width: 100%;" />
                                </div>
                            </td>
                            <td>
                                <div class="input-icon left">
                                    <i class="fa fa-pencil"></i>
                                    <input name="mult" id="mult" placeholder="Mult.." class="form-control inline" style="width: 100%;" />
                                </div>
                            </td>
                            <td>
                                <div class="input-icon left">
                                    <i class="fa fa-pencil"></i>
                                    <input name="wiresize" id="wiresize" placeholder="Wire Size.." class="form-control inline" style="width: 100%;" />
                                </div>
                            </td>
                            <td>
                                <div class="input-icon left">
                                    <i class="fa fa-pencil"></i>
                                    <input value="<?php echo date('Y-m-d');?>" name="dateissued" id="dateissued" placeholder="Date Issued.." class="form-control inline" style="width: 100%;" />
                                </div>
                            </td>
                            <td>
                                <div class="input-icon left">
                                    <i class="fa fa-pencil"></i>
                                    <input name="status" id="status" placeholder="Status.." class="form-control inline" style="width: 100%;" />
                                </div>
                            </td>
                            <td>
                                <div class="btn-group">
                                <button type="reset" class="btn btn-default inline btn-xs"><i class="fa fa-refresh"></i></button>
                                <button type="submit" class="btn btn-primary inline btn-xs pull-right"><i class="fa fa-save"></i> Add</button>
                                </div>
                            </td>
                        </tfoot>
                    </table>

                </div>
            </div>
        </div>

    </form>


</div>

<hr>

<div class="row" style="">

    <div class="col-md-4">

        <div class="form-group">
            <input id="datafile" name="datafile" class="file" type="file" data-preview-file-type="any" data-upload-url="<?php echo base_url('assets/uploadmisdata');?>">
        </div>
    </div>
    <div class="col-md-8 pull-left text-align-right">
        <button id="btn_sync_asset" class="btn btn-danger">Synchronize</button>
    </div>
</div>

<hr>
<hr>

<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/js/fileinput.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/js/locales/fr.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/js/locales/es.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/themes/explorer/theme.js" type="text/javascript"></script>


<script type="text/javascript" src="<?php echo base_url(); ?>assets/pages/assets/mtr.js"></script>
<script type="text/javascript">
    MTR.init();
    MTR.newmi();
    function noenter() {
        return !(window.event && window.event.keyCode == 13);
    }
</script>
