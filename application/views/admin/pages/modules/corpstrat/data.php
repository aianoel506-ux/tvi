<?php
$trnid = $this->uri->segment(4);
$qry_trn = $this->db->select()->from('transaction_request_main_trails')->where('sysid', $trnid)->get()->row();
$qry_stg = $this->db->select()->from('prime_transaction_flow_main_stages')->where(array('sysid' => $qry_trn->stageid))->get()->row();
$flowid = $qry_stg->flowid;

$docs_qry = $this->db->select('d.doctype,t.names,t.desc,d.signed')
    ->from('prime_documents_main as d')
    ->join('prime_types_parameter as t','d.doctype = t.sysid','left')
    ->where(array('d.status' => 1))->get();
$documents = array();
if ($docs_qry->num_rows() > 0) {
    $documents = $docs_qry->result();
}
?>
<!-- DATEPICKER CSS START!-->
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-datepicker/css/datepicker3.css">
<!-- DATEPICKER CSS END!-->
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/css/fileinput.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/themes/explorer/theme.css">
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

    </div>

    <div class="col-md-6">
        <div class="row  margin-bottom-10">
            <div class="col-md-12">
                    <div class="portlet light bordered">
                        <div class="portlet-title" style="position: relative">
                            <div class="caption">
                                <i class="fa fa-bolt"></i>
                                <span class="caption-subject font-red-flamingo bold uppercase"><span class="label label-danger">2</span> Documents Preview</span>
                            </div>
                            <div class="tools" id="inspection_tools">

                            </div>
                            <div class="tabbable-line pull-right" id="doc_preview_tabs">
                                <ul class="nav nav-tabs ">
                                    <li class="active">
                                        <a href="#doc_tssr" data-toggle="tab" aria-expanded="true" data-id="3436"> TSSR  </a>
                                    </li>
                                    <?php
                                    foreach ($documents as $dox) {
                                        echo '<li class="">';
                                        echo '<a href="#doc_'.strtolower($dox->names).'" data-toggle="tab" aria-expanded="true" data-id="'.$dox->doctype.'"> '.$dox->names.' </a>';
                                        echo '</li>';
                                    }
                                    ?>
                                    <li class="">
                                        <a href="#doc_others" data-toggle="tab" aria-expanded="true" data-id="3436"> Other Documents </a>
                                    </li>
                                </ul>

                            </div>
                        </div>
                        <div class="portlet-body">
                            <div class="tab-content" id="doc_preview_pane">
                                <div class="tab-pane fade in active" id="doc_tssr">
                                    <div class="row">
                                        <div class="btn-group col-md-12 pull-right">
                                            <a href="javascript:" id="btn_open_preview" class="btn btn-primary btn-sm inline" data-type="3436"><i class="fa fa-search"></i> Open in Tab</a>
                                        </div>
                                    </div>
                                    <!--<iframe id="iframe_tssr_preview" data-type="3436" src="" style="width:100%; height:500px;" frameborder="0"></iframe>-->
                                </div>
                                <?php
                                foreach ($documents as $dox) {
                                    echo '<div class="tab-pane fade in " id="doc_'.strtolower($dox->names).'">';
                                    echo '<div class="row">';
                                    echo '<div class="btn-group">';
                                    if (!$dox->signed) {
                                        echo '<a href="javascript:" id="btn_sign_doc" class="btn btn-primary btn-sm inline" data-name="Proposal" data-type="' . $dox->doctype . '"><i class="fa fa-pencil"></i> Sign</a>';
                                    }
                                    echo '<a href="javascript:" id="btn_open_preview" class="btn btn-primary btn-sm inline" data-type="'.$dox->doctype.'"><i class="fa fa-search"></i> Open in Tab</a>';
                                    echo '</div></div>';
                                    //echo '<iframe id="iframe_'.strtolower($dox->names).'_preview" data-type="'.$dox->doctype.'" src="" style="width:100%; height:500px;" frameborder="0"></iframe>';
                                    echo '</div>';
                                }
                                ?>
                                <div class="tab-pane fade in" id="doc_others">
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
                            <div class="portlet-footer">

                            </div>
                        </div>
                    </div>
            </div>
        </div>
    </div>

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
    ATTACHEMENTS.docs(<?php echo $dataid; ?>);
</script>