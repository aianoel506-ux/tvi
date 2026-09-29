<?php
$trnid = $this->uri->segment(4);
$qry_trn = $this->db->select()->from('transaction_request_main_trails')->where('sysid', $trnid)->get()->row();
$qry_stg = $this->db->select()->from('prime_transaction_flow_main_stages')->where(array('sysid' => $qry_trn->stageid))->get()->row();
$flowid = $qry_stg->flowid;
?>
<!-- DATEPICKER CSS START!-->
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-datepicker/css/datepicker3.css">
<!-- DATEPICKER CSS END!-->
<style>
    /* Chrome, Safari, Edge, Opera */
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    /* Firefox */
    input[type=number] {
        -moz-appearance: textfield;
    }
</style>


<div class="row tab-pane fade in">
    <div class="col-md-6">
        <?php
        $appdetails = get_application_details($dataid);
        $rateclassid = (isset($appdetails->rateclassid)) ? $appdetails->rateclassid : false;
        customer_application_basicinfo($dataid, true, false);
        ?>

        <div class="portlet grey box margin-top-20 <?php echo ($flowid == 2) ? 'hidden' : ''; ?>" id="recom_setup">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-file"></i>
                    <span class="caption-subject font-red-flamingo bold uppercase"><span class="label label-danger">4</span> Recommended Installation Setup
                </div>
                <div class="btn-group pull-right" style="margin-top: 5px;">
                    <!--Load Buttons if published-->
                    <a href="javascript:" id="btn_reload_setup" class="btn btn-primary btn-sm inline"><i class="fa fa-refresh"></i> </a>
                    <!--Load Preview if not published on save remove all icons-->
                    <a href="javascript:" id="btn_setup_preview" class="btn btn-primary btn-sm inline preview"><i class="fa fa-search"></i> Preview</a>
                    <a href="javascript:" id="btn_setup_delete" class="btn btn-danger btn-sm inline"><i class="fa fa-trash"></i></a>
                </div>
            </div>
            <div class="portlet-body">
                <form id="frm_sps_setup" action="<?php echo base_url();?>inspection/createspssetup">
                    <div class="form-group row">
                        <div class="col-md-4">
                            <label class="control-label font-red-flamingo bold uppercase">Panel Type</label>
                            <input class="form-control" id="select2_paneltypes" name="sptypeid" placeholder="Panel Type...">
                        </div>
                        <div class="col-md-4">
                            <label class="control-label font-red-flamingo bold uppercase">Number of Panels</label>
                            <input type="number" class="form-control" id="input_nop" name="nop" placeholder="Number of panels...">
                        </div>
                        <div class="col-md-4">
                            <label class="control-label font-red-flamingo bold uppercase">Number of Strings</label>
                            <input type="number" class="form-control" id="input_nos" name="nos" placeholder="Number of strings...">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-6">
                            <!--<label class="control-label font-red-flamingo bold uppercase">Number of Panels per String</label>
                            <input class="form-control" id="input_panelsperstring" name="panelsperstring" placeholder="Panels per string...">-->
                            <label class="control-label font-red-flamingo bold uppercase" for="input_panelsperstring">Number of Panels per String</label>
                            <div class="input-icon right">
                                <input class="form-control input-reset" id="input_panelsperstring" placeholder="Panels per string..." name="panelsperstring" />
                                <i class="fa fa-search"></i>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="control-label font-red-flamingo bold uppercase" for="input_invertersize">Inverter Size(s)</label>
                            <div class="input-icon right">
                                <input class="form-control input-reset" id="input_invertersize" name="invertersize" placeholder="Inverter Size...">
                                <i class="fa fa-search"></i>
                            </div>
                        </div>
                    </div>
                </form>
                <hr>
                <div id="template_search_result"></div>

                <div class="tabbable-line">
                    <ul class="nav nav-tabs ">
                        <li class="active">
                            <a href="#sps_components" data-toggle="tab" aria-expanded="true" data-id="1"> Components </a>
                        </li>
                        <li class="">
                            <a href="#sps_accessories" data-toggle="tab" aria-expanded="true" data-id="2"> Accessories </a>
                        </li>
                        <li class="">
                            <a href="#sps_consumables" data-toggle="tab" aria-expanded="true" data-id="3"> Consumables </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade in active" id="sps_items">
                            <form id="frm_add_spsitem" action="<?php echo base_url()?>inspection/addspsitem" method="post">
                                <table class="table table-striped table-condensed table-bordered table-responsive" id="tbl_add_components">
                                    <thead>
                                    <th style="width: 50% !important;">Select Item</th>
                                    <th style="width: 10% !important;">Qty</th>
                                    <th style="width: 10% !important;">Unit</th>
                                    <!--<th style="width: 15% !important;">Price</th>
                                    <th style="width: 15% !important;">Total</th>-->
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>
                                            <input class="form-control input-sm " id="select2_newitem" placeholder="Select item...">
                                        </td>
                                        <td class="number">
                                            <input class="form-control input-sm " id="input_new_item_qty" placeholder="Qty...">
                                        </td>
                                        <td>
                                            <input type="hidden" id="input_new_item_type">
                                            <span id="item_unit"></span>
                                        </td>
                                        <!--<td class="number">
                                            <input class="form-control input-sm " id="input_new_item_price" placeholder="Price...">
                                        </td>
                                        <td class="number">
                                            <span id="item_total">0.00</span>
                                        </td>-->
                                    </tr>

                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <td colspan="5">
                                            <div class="btn-group" id="item_controls">
                                            </div>
                                            <button type="submit" class="btn btn-sm btn-primary pull-left" id="btn_save_new_item"><i class="fa fa-plus"></i> Add </button>
                                            <button type="reset" href="javascript:;" class="btn btn-sm btn-danger pull-right" id="btn_reset_new_item"><i class="fa fa-rotate-left"></i> Reset </button>
                                        </td>
                                    </tr>
                                    </tfoot>
                                </table>
                            </form>
                            <hr>
                            <table class="table table-striped table-hover table-bordered table-condensed types" id="tbl_sps_components" width="100%">
                                <thead>
                                <th>#</th>
                                <th>Item</th>
                                <th>Qty</th>
                                <th>Unit</th>
                                <!--<th>Price</th>
                                <th>Total</th>-->
                                <th><i class="fa fa-check-square-o"></i></th>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!--<div class="portlet-footer">
                    <div class="row">
                        <div class="col-md-12">
                            <ul class="list-group summary column list-group-lg">
                                <li class="list-group-item">
                                    <span class="col-md-6 label-name" style="padding: 10px 20px !important;">Total 10-Year Plan </span>
                                    <span class="col-md-6 font-red-flamingo bold number" id="total_10years" style="padding: 10px 20px !important;"></span>
                                </li>
                                <li class="list-group-item">
                                    <span class="col-md-6 label-name" style="padding: 10px 20px !important;">Total 5-Year Plan </span>
                                    <span class="col-md-6 font-red-flamingo bold number" id="total_5years" style="padding: 10px 20px !important;"></span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>-->
            </div>
        </div>

        <div class="portlet grey box margin-top-20" id="assessment_docs">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-file-text"></i>
                    <span class="caption-subject font-red-flamingo bold uppercase"><span class="label label-danger"><?php echo ($flowid == 2) ? 4 : 5; ?></span> Documents</span>
                </div>
                <div class="tools"></div>
            </div>
            <div class="portlet-body">
                <div class="row">
                    <div class="col-md-12">
                        <a href="javascript:" id="btn_reload_documents" class="btn btn-default inline pull-right"><i class="fa fa-refresh"></i> Reload</a>
                    </div>
                </div>
                <table id="tbl_assessment_docs" class="table table-condensed table-bordered table-hover" width="100%" data-folder="<?php echo 'cad/applications/' . str_pad($dataid, 6, "0", STR_PAD_LEFT) . "/".get_stage_specific(92)->desc."/Docs/"; ?>">
                    <thead>
                        <th>#</th>
                        <th width="80%">File</th>
                        <th class="center"><i class="fa fa-search"></i> </th>
                    </thead>
                    <tbody>
                    
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="row  margin-bottom-10">
            <div class="col-md-12">
                <form action="<?php echo base_url(); ?>inspection/saveinspectionreport" method="post" id="form_inspection_report">
                    <input type="hidden" id="selected_inspection" name="surveyid">
                    <div class="portlet  grey box">
                        <div class="portlet-title" style="position: relative">
                            <div class="caption">
                                <i class="fa fa-bolt"></i>
                                <span class="caption-subject font-red-flamingo bold uppercase"><span class="label label-danger">2</span> Technical Site Survey Report</span>
                            </div>
                            <div class="tools" id="inspection_tools">

                            </div>
                        </div>
                        <div class="portlet-body" id="tech_report_input">
                            <div class="tabbable-line pull-right" style="padding-top: 0px !important; padding-bottom: 15px !important;">
                                <ul class="nav nav-tabs ">
                                    <li class="active">
                                        <a href="#tssr_va_reading" data-toggle="tab" aria-expanded="true" data-id="2"> Readings  </a>
                                    </li>
                                    <li class="">
                                        <a href="#tssr_info" data-toggle="tab" aria-expanded="true" data-id="1"> Details  </a>
                                    </li>
                                    <li class="">
                                        <a href="#tssr_measurements" data-toggle="tab" aria-expanded="true" data-id="3"> Measurements and Remarks </a>
                                    </li>
                                    <li class="">
                                        <a href="#tssr_other_info" data-toggle="tab" aria-expanded="true" data-id="4"> Additional Info </a>
                                    </li>
                                    <li class="">
                                        <a href="#tssr_pics" data-toggle="tab" aria-expanded="true" data-id="5"> Uploaded Pics </a>
                                    </li>
                                </ul>

                            </div>
                            <div class="tab-content">
                                <div class="tab-pane fade in " id="tssr_info">
                                    <ul class="list-group summary column no-border">
                                        <li class="list-group-item">
                                            <span class="col-md-3 label-name">Roof Orientation</span>
                                            <span class="col-md-9 label-default">
                                            <input class="form-control" placeholder="Roof Orientation..." name="rooforientation" id="rooforientation"/>
                                        </span>
                                        </li>
                                        <li class="list-group-item">
                                            <span class="col-md-3 label-name">Kind of Roof</span>
                                            <span class="col-md-9 label-default">
                                            <input class="form-control" id="select2_rooftype" name="roofing" placeholder="Kind of Roof...">
                                        </span>
                                        </li>
                                        <li class="list-group-item">
                                            <span class="col-md-3 label-name">Roof Inclination</span>
                                            <span class="col-md-9 label-default">
                                            <input required="required" class="form-control" id="roof_inclination" name="roofinclination" value="" placeholder="Inclination.. " />
                                        </span>
                                        </li>
                                        <li class="list-group-item">
                                            <span class="col-md-3 label-name">Voltage Drop Condition</span>
                                            <span class="col-md-9 label-default">
                                            <input class="form-control" id="vd_condition" name="vdcondition" value="" placeholder="Voltage Drop Condition.. " />
                                        </span>
                                        </li>
                                        <li class="list-group-item">
                                            <span class="col-md-3 label-name">Generator Rating</span>
                                            <span class="col-md-9 label-default">
                                            <input name="gensetrate" id="genset_rate" class="form-control" placeholder="Generator Rating...">
                                        </span>
                                        </li>
                                    </ul>
                                </div>
                                <div class="tab-pane fade in active" id="tssr_va_reading">
                                    <div class="form-group row">
                                        <div class="col-md-4 l1l2">
                                            <label class="control-label font-red-flamingo bold uppercase">L1-L2</label>
                                            <div class="form-control disabled">0</div>
                                            <input tabindex="1" class="form-control" placeholder="Volts.." name="l1l2" id="l1_l2" />
                                        </div>
                                        <div class="col-md-4 l1l3">
                                            <label class="control-label font-red-flamingo bold uppercase">L1-L3</label>
                                            <div class="form-control disabled">0</div>
                                            <input tabindex="2" class="form-control" placeholder="Volts.." name="l1l3" id="l1_l3" />
                                        </div>
                                        <div class="col-md-4 l2l3">
                                            <label class="control-label font-red-flamingo bold uppercase">L2-L3</label>
                                            <div class="form-control disabled">0</div>
                                            <input tabindex="3" class="form-control" placeholder="Volts.." name="l2l3" id="l2_l3" />
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-md-4 l1g">
                                            <label class="control-label font-red-flamingo bold uppercase">L1-G</label>
                                            <div class="form-control disabled">0</div>
                                            <input tabindex="4" class="form-control" placeholder="Volts.." name="l1g"/>
                                        </div>
                                        <div class="col-md-4 l2g">
                                            <label class="control-label font-red-flamingo bold uppercase">L2-G</label>
                                            <div class="form-control disabled">0</div>
                                            <input tabindex="5" class="form-control" placeholder="Volts.." name="l2g"/>
                                        </div>
                                        <div class="col-md-4 l3g">
                                            <label class="control-label font-red-flamingo bold uppercase">L3-G</label>
                                            <div class="form-control disabled">0</div>
                                            <input tabindex="6" class="form-control" placeholder="Volts.." name="l3g"/>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-md-4 l1l2a">
                                            <label class="control-label font-red-flamingo bold ">L1-L2(A)</label>
                                            <div class="form-control disabled">0</div>
                                            <input tabindex="7" class="form-control" placeholder="Ampere.." name="l1l2a"/>
                                        </div>
                                        <div class="col-md-4 l1l3a">
                                            <label class="control-label font-red-flamingo bold ">L1-L3(A)</label>
                                            <div class="form-control disabled">0</div>
                                            <input tabindex="8" class="form-control" placeholder="Ampere.." name="l1l3a"/>
                                        </div>
                                        <div class="col-md-4 l2l3a">
                                            <label class="control-label font-red-flamingo bold ">L2-L3(A)</label>
                                            <div class="form-control disabled">0</div>
                                            <input tabindex="9" class="form-control" placeholder="Ampere.." name="l2l3a"/>
                                        </div>
                                    </div>


                                    <div class="well well-sm" style="margin-bottom: 5px;">
                                        <div class="form-group row" style="margin-bottom: 0px;">
                                            <div class="col-md-4">
                                                <span class="caption-subject font-red-flamingo bold uppercase">Grid Service</span>
                                                <input tabindex="14" required="required" class="form-control" id="rate_class_select" name="rateclass" value="<?php echo $rateclassid;?>" />
                                                <div class="form-control disabled">0</div>
                                            </div>
                                            <div class="col-md-4">
                                                <span class="caption-subject font-red-flamingo bold uppercase">Panel Type</span>
                                                <input tabindex="15" required="required" class="form-control" id="select2_panel_type" name="paneltype" value="" placeholder="Select panel.. " />
                                                <div class="form-control disabled">0</div>
                                            </div>
                                            <div class="col-md-4">
                                                <span class="caption-subject font-red-flamingo bold uppercase">Inspection Date</span>
                                                <input tabindex="17" name="inspectiondate" id="inspectiondate" class="form-control border-blue" placeholder="Date...">
                                                <div class="form-control disabled">0</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-md-12">
                                            <span class="caption-subject font-red-flamingo bold uppercase">Remarks</span>
                                            <textarea class="form-control" rows="2" name="remarks" placeholder="Remarks..."></textarea>
                                            <div class="form-control disabled" style="height: 60px;">0</div>
                                        </div>
                                    </div>

                                    <div class="row margin-bottom-10">
                                        <div class="col-md-12">
                                            <ul class="list-group summary column list-group-lg">
                                                <li class="list-group-item">
                                                    <span class="col-md-6 label-name" style="padding: 10px 20px !important;">Number of Panel <span class="small">(s)</span></span>
                                                    <span class="col-md-6 font-red-flamingo bold number" id="text_nop" style="padding: 10px 20px !important;"></span>
                                                </li>
                                                <li class="list-group-item">
                                                    <span class="col-md-6 label-name" style="padding: 10px 20px !important;">Total Watt <span class="small">(s)</span></span>
                                                    <span class="col-md-6 font-red-flamingo bold number" id="text_power" style="padding: 10px 20px !important;"></span>
                                                </li>
                                                <li class="list-group-item">
                                                    <span class="col-md-6 label-name" style="padding: 10px 20px !important;">System Size <span class="small">Recommendation</span></span>
                                                    <span class="col-md-6 font-red-flamingo bold number" id="text_system_size" style="padding: 10px 20px !important;"></span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade in" id="tssr_measurements">
                                    <?php
                                    $surveydetails_qry = $this->db->select()
                                        ->from('prime_types_parameter')
                                        ->where(array('codes' => 'INSFILETYPES','status' => 1))
                                        ->get();

                                    if ($surveydetails_qry->num_rows() > 0) {
                                        foreach ($surveydetails_qry->result() AS $surveydetail) {?>
                                            <div class="form-group row">
                                                <div class="col-md-12">
                                                    <span class="caption-subject font-red-flamingo bold uppercase"><?php echo $surveydetail->names;?></span>
                                                    <input class="form-control" name="surveydetail[<?php echo $surveydetail->sysid;?>][measurements]" placeholder="Measurements...">
                                                    <textarea class="form-control" rows="2" name="surveydetail[<?php echo $surveydetail->sysid;?>][remarks]" placeholder="Remarks..."></textarea>
                                                </div>
                                            </div>
                                        <?php }
                                    }
                                    ?>
                                </div>
                                <div class="tab-pane fade in" id="tssr_other_info">
                                    <div class="form-group row">
                                        <div class="col-md-12">
                                            <span class="caption-subject font-red-flamingo bold uppercase">Roof Dimensions</span>
                                            <textarea class="form-control" rows="2" name="roofdimension" placeholder="Roof Dimensions..."></textarea>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-md-12">
                                            <span class="caption-subject font-red-flamingo bold uppercase">Electrical/Structural Plans</span>
                                            <textarea class="form-control" rows="2" name="esplans" placeholder="Electrical/Structural Plans..."></textarea>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-md-12">
                                            <span class="caption-subject font-red-flamingo bold uppercase">Normal Loads or for Clamping</span>
                                            <textarea class="form-control" rows="2" name="forclamping" placeholder="Normal Loads or for Clamping..."></textarea>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-md-12">
                                            <span class="caption-subject font-red-flamingo bold uppercase">Meter # / Billing Details</span>
                                            <textarea class="form-control" rows="2" name="billingdetails" placeholder="Meter # / Billing Details..."></textarea>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-md-12">
                                            <span class="caption-subject font-red-flamingo bold uppercase">Daytime Appliances</span>
                                            <textarea class="form-control" rows="2" name="dtappliances" placeholder="Daytime Appliances..."></textarea>
                                        </div>
                                    </div>

                                </div>
                                <div class="tab-pane fade in" id="tssr_pics">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <a href="javascript:" id="btn_reload_attachments" class="btn btn-default inline pull-right"><i class="fa fa-refresh"></i> Reload</a>
                                        </div>
                                    </div>
                                    <div id="box_file_explorer" class="well mt-element-card mt-element-overlay" style="display: inline-block; width: 100%; min-height: 300px; border: 4px dashed #ccc; text-align: left;" data-folder="<?php echo get_stage_specific(92)->desc;?>/Survey" >
                                        <h3><i class="fa fa-warning text-warning"></i> No file uploaded yet!</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="portlet-footer">
                                <button type="button" id="save_inspection_load" class="btn btn-info"><i class="fa fa-save"></i> Save</button>
                                <button type="button" id="publish_inspection_load" class="btn btn-success"><i class="fa fa-send"></i> Publish</button>
                                <button type="button" id="preview_inspection_load" class="btn btn-primary pull-right"><i class="fa fa-search"></i> Preview</button>
                            </div>
                        </div>
                        </div>
                </form>
            </div>
        </div>

        <div class="portlet grey box margin-top-20">
            <div class="portlet-title" style="position: relative">
                <div class="caption">
                    <i class="fa fa-users"></i>
                    <span class="caption-subject font-red-flamingo bold uppercase"><span class="label label-danger">3</span> Inspection Team</span>
                    <span class="caption-helper">employee that executes the inspection</span>
                </div>
                <div class="btn-group pull-right" style="margin-top: 5px;">
                    <a href="#frm_add_team_member" title="Add Team" modal-class="modal-sm" data-arr="<?php echo $dataid . ',' . $moduleid; ?>" data-toggle="ajax-modal" class="btn btn-primary btn-sm inline"><i class="fa fa-plus"></i> Add</a>
                    <a href="javascript:" id="btn_refresh_team_list" class="btn btn-default btn-sm inline"><i class="fa fa-refresh"></i></a>
                </div>
            </div>
            <div class="portlet-body ">
                <table class="table table-striped table-hover" id="tbl_inspection_team">
                    <thead>
                    <th>Emp ID</th>
                    <th>Employee Name</th>
                    <th>Date Added</th>
                    <th><i class="fa fa-wrench"></i></th>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

        <div class="portlet grey box margin-top-20">
            <div class="portlet-title" style="position: relative">
                <div class="caption">
                    <i class="fa fa-map-o"></i>
                    <span class="caption-subject font-red-flamingo bold uppercase">Survey Logs</span>
                    <span class="caption-helper">Load Survey Logs</span>
                </div>
            </div>
            <div class="portlet-body hidden">
                <table class="table table-condensed table-xs table-striped" id="tbl_gdr_logs">
                    <thead>
                    <tr>
                        <th></th>
                        <th>#</th>
                        <th>Rate Class</th>
                        <th>Monthly</th>
                        <th>Daily</th>
                        <th>Demand</th>
                        <th>Rate</th>
                        <th>Total Load</th>
                        <th>GDR Amount</th>
                        <th>Active</th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            <div class="portlet-body">
                <table width="100%" class="table table-condensed table-xs table-striped" id="tbl_inspection_logs">
                    <thead>
                    <tr>
                        <th></th>
                        <th>#</th>
                        <th>Remarks</th>
                        <th>Date</th>
                        <th>NOP</th>
                        <th>Power</th>
                        <th>Inspector</th>
                        <th>Entered</th>
                        <th>Control</th>
                        <th>Select</th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<div class="modal fade draggable-modal" id="draggable" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form role="form" class="form-horizontal">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Asset Entry</h4>
                </div>
                <div class="modal-body">

                    <div class="form-body">
                        <div class="form-group form-md-line-input">
                            <label class="col-md-2 control-label" for="form_control_1">Description</label>
                            <div class="col-md-10">
                                <input type="text" class="form-control" id="form_control_1" placeholder="Enter asset description">
                                <div class="form-control-focus">
                                </div>
                                <span class="help-block">Asset Description</span>
                            </div>
                        </div>
                        <div class="form-group form-md-line-input">
                            <label class="col-md-2 control-label" for="form_control_1">Description</label>
                            <div class="col-md-10">
                                <input type="text" class="form-control" id="form_control_1" placeholder="Enter asset description">
                                <div class="form-control-focus">
                                </div>
                                <span class="help-block">Asset Description</span>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn blue">Add Asset</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>



<!--<script src="<?php echo base_url(); ?>assets/global/plugins/datatables/jquery.dataTables.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/datatables/dataTables.bootstrap.js"></script>-->

<!-- DATE PICKER!-->
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>

<script src="<?php echo base_url(); ?>assets/global/plugins/fuelux/js/spinner.min.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery.input-ip-address-control-1.0.min.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-pwstrength/pwstrength-bootstrap.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery-tags-input/jquery.tagsinput.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-touchspin/bootstrap.touchspin.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/typeahead/handlebars.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/typeahead/typeahead.bundle.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/ckeditor/ckeditor.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-select/bootstrap-select.min.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/select2/select2.min.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js"></script>

<!-- BEGIN PAGE LEVEL PLUGINS -->
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/jquery-validation/js/jquery.validate.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/jquery-validation/js/additional-methods.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-wizard/jquery.bootstrap.wizard.min.js"></script>
<!-- END PAGE LEVEL PLUGINS -->
<!-- DATE PICKER END!-->
<!-- GOOGLE MAPS LIBS START !-->
<script src="<?php echo base_url(); ?>assets/global/plugins/gmaps/gmaps.js" type="text/javascript"></script>

<script src="<?php echo base_url(); ?>assets/pages/cad/newaccount.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/pages/inspection/main.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/pages/maps/main.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/pages/attachements/main.js" type="text/javascript"></script>

<script type="text/javascript">
    //GMAPSMAIN.mapping(<?php echo $dataid; ?>, '#gmap_geocoding', true, <?php echo $moduleid; ?>);
    INSPECTION.application(<?php echo $dataid; ?>);
    INSPECTION.team(<?php echo $dataid; ?>, <?php echo $moduleid; ?>);
    CAD.profile(<?php echo $dataid; ?>, <?php echo $flowid;?>);
    ATTACHEMENTS.init(<?php echo $dataid; ?>);
</script>