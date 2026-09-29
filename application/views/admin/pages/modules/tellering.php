
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/jquery-tags-input/jquery.tagsinput.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-markdown/css/bootstrap-markdown.min.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/typeahead/typeahead.css">
<script src="<?php echo base_url(); ?>assets/global/plugins/datatables/jquery.dataTables.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/datatables/dataTables.bootstrap.js"></script>


<style>

    body{
        background: #eaeaea
    }

    #search_txt, #search_mtr {
        text-transform: uppercase;
        padding: 2px 4px !important;
    }
    .scan-stat{
        text-align: center;
    }
    .scan-stat .fa-big{
        opacity: 0.2;
        font-size: 70pt;
        width: 100%;
        margin-top: 50px !important;
    }
    .transactions .portlet{
        display: none;
    }
    .transactions .portlet.active{
        display: block;
    }
    .input-group.input-lg .fa {
        font-size: 25px !important;
        top: 5px !important;
    }
    .input-group.input-lg .form-control {
        padding: 5px 10px !important;
        font-size: 20px !important;
    }
    .input-group.input-lg .input-icon .form-control {
        padding-left: 40px !important;
    }
    .icon-btn.active {
        background: rgba(128, 204, 255, 0.50);
    }
    .table tr.row-warn td:first-child:before {
        font-family: FontAwesome;
        content: '\f12a';
        color: red;
        position: absolute;
        left: 10px;
    }

    #tbl_bill_sp input, #tbl_bill_sp td {
        font-size: 15px !important;
        padding: 2px 2px !important;
    }

    .pay-input label {
        color: #0a6aa1;
        font-size: 11px;
    }

    .pay-input #spamtchange {
        font-weight: bold;
    }
    .pay-input {
        margin-bottom: 10px !important;
    }
    .pay-input input{
        font-size: 20px !important;
        padding: 2px 4px !important;
        text-align: right;
    }
    .pay-input input.input-lg{
        font-size: 28px !important;
        padding: 2px 4px;
    }

    .table .btn-remove{
        cursor: pointer;
    }

    .search-type .icon-btn{
        margin-right: 0.2em !important;
        height: 80px !important;
    }

    .search-type .icon-btn small{
        color: #2b9bff;
    }

    .table.tbl-zoom th {
        font-size: 15px !important;
    }
    .table.tbl-zoom, .table.tbl-zoom td, .table.tbl-zoom th, .table.tbl-zoom tr {
        border-left: rgba(0,0,0,0.02) 1px solid !important;
        border-right: rgba(0,0,0,0.02) 1px solid !important;
    }
    .shortcuts code {
        font-size: 10px;
    }

    .footer
    {
        width: 100%;
        position: fixed;
        bottom: 0px;
        height: 25px;
        background: rgba(38,132,255,0.50);
        display: inline-block;
        padding: 3px 5px;
        color: #fff;
    }

    .footer.page-footer {
        margin-left: 0px !important;
        left: 0px !important;
    }

    .shortcut-legend .label:last-child{
        width: auto !important;
    }

    .shortcut-legend {
        display: flex !important;
        flex-wrap: wrap !important;
    }

    .shortcut-legend .label {
        flex-grow: 1;
        min-width: 10%;
        color: #000;
        border: 1px solid #c9cccf;
        margin: 2px 2px;
    }

    tr button:active, tr button:focus  {
        border-color: #27d3ff !important;
        background: rgba(39,190,255,0.20) !important;
        color: #fff !important;
    }
    tr button:focus .fa {
        color: #fff !important;
    }


</style>




    <div class="row" style="padding: 20px 20px !important;">
        <div class="col-md-3">
            <div class="portlet blue box tabbed">
                <div class="portlet-title">
                    <div class="caption">
                    </div>
                    <div class="tools">
                        <button data-toggle="tooltips" data-placement="bottom" title="Print Test" class="btn btn-default" id="btn_transaction_testprint"><i class="fa fa-print"></i></button>
                        <button data-toggle="tooltips" data-placement="bottom" title="Print Transactions" class="btn btn-default" id="btn_transaction_reports"><i class="fa fa-file-text"></i></button>
                        <button data-toggle="tooltips" data-placement="bottom" title="Transaction List" class="btn btn-default" id="btn_transaction_table"><i class="fa fa-reorder"></i></button>
                        <button data-toggle="tooltips" data-placement="bottom" title="Close Transactions" class="btn btn-danger" id="btn_transaction_close"><i class="fa fa-sign-out"></i></button>
                    </div>
                </div>
                <div class="portlet-body form">

                    <form class="form-horizontal" id="search_trn" action="<?php echo base_url('tellering/getbillingreg'); ?>" method="post">
                        <div class="form-body">
                            <div class="form-group form-md-line-input" style="padding: 0px 10px;">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-search"></i></span>
                                    <!-- 43: Tellering, 35: CAD, 56: Legal, 0: Other -->
                                    <input style="width: 80%; display: inline-block;" id="search_module" name="moduleid" type="hidden" value="43" class="form-control input-lg servno" placeholder="Queue Code..">
                                    <input style="width: 80%; display: inline-block;" id="search_txt" name="servno" type="text" class="form-control input-lg servno" placeholder="Queue Code..">
                                    <input style="width: 20%; display: inline-block;" id="search_mtr" name="mtr" type="text" class="form-control input-lg mtr" placeholder="1" value="1">
                                    <span class="input-group-btn"><button type="submit" class="btn btn-primary btn-lg"><i class="fa fa-search"></i></button></span>

                                    <label style="display: inline-block; width: 100%; left: 0px;" class="" for="search_txt"></label>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions ">

                            <div class="col-md-12"><h4>Queueing</h4> </div>

                            <div class="btn-broup col-md-8">
                                <div class="input-group">
                                    <input id="queue_last_num" class="form-control" placeholder="0" />
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-primary"><i class="fa fa-angle-right"></i></button>
                                        <button type="button" class="btn btn-danger"><i class="fa fa-plus"></i></button>
                                        <button type="button" class="btn btn-default"><i class="fa fa-refresh"></i></button>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <h4>Serving: <b id="queue_num">0</b></h4>
                            </div>

                        </div>
                    </form>

                </div>

            </div>


            <div class="portlet box grey">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-warning"></i> Payment Types
                    </div>
                    <div class="tools">

                    </div>
                </div>
                <div class="portlet-body form">
                    <div class="form-body search-type" id="pay_type_button" style="text-align: center;">

                        <?php

                        $qry_module_withpay = $this->db->select('sysid, pagefile, icon, name, code, parent, htmlclass')
                            ->from('prime_module_navigations_main')
                            ->where('withpay', 1)
                            ->group_by('sysid, pagefile, icon, name, code, parent, htmlclass')
                            ->get();
                        $num_rows = $qry_module_withpay->num_rows();
                        if($num_rows>0) {
                            foreach($qry_module_withpay->result() as $row) {
                                $folder_arr = explode('/', $row->pagefile);
                                $folder = $folder_arr[0];
                                if(file_exists(FCPATH . 'application/views/admin/pages/modules/'.$folder.'/payments.php')) {
                                    $get_parent_dept = $this->db->select('cm.names, cm.icon, cm.colorclass, nd.descs')
                                        ->from('prime_module_navigations_departments AS nd')
                                        ->join('prime_costcenter_main AS cm', 'cm.sysid = nd.ccid')
                                        ->where('nd.navid', $row->sysid)
                                        ->get()->row();

                                    $button_name = ($get_parent_dept) ? $get_parent_dept->names : $row->name;
                                    $button_icon = ($get_parent_dept) ? $get_parent_dept->icon : $row->icon;
                                    $button_class = $row->htmlclass;
                                    $button_tooltip = ($get_parent_dept) ? $get_parent_dept->descs : '';
                                    $button_active = ($row->sysid==43) ? 'active' : '';
                                    echo '<a style="width: 24% !important;" title="'.$button_tooltip.'" data-placement="right" data-val="'.$row->sysid.'" href="javascript:;" class="icon-btn '.$button_active.' text-'.$button_class.' tooltips">
                                                <i class="fa '.$button_icon.'"></i>
                                                <div>'.$row->code.'<br><small style="font-size: 10px;">'.$button_name.'</small></div>
                                                <span id="" class="badge badge-danger"></span>
                                          </a>';
                                }
                            }
                        }
                        ?>

                        <!--

                        <a data-val="ra" href="javascript:;" class="icon-btn">
                            <i class="fa fa-warning"></i>
                            <div>
                                R.A.7832
                            </div>
                            <span class="badge badge-danger">
                                <i class="fa fa-info"></i> </span>
                        </a>

                        <a data-val="bp" href="javascript:;" class="icon-btn">
                            <i class="fa fa-money"></i>
                            <div>
                                B.P.
                            </div>
                            <span class="badge badge-danger">
                                <i class="fa fa-info"></i> </span>
                        </a>


                        <a data-val="cad" href="javascript:;" class="icon-btn">
                            <i class="fa fa-history"></i>
                            <div>
                                CAD
                            </div>
                        </a>
                        -->
                    </div>
                    <div class="form-actions shortcut-legend">
                        <span class="label">[F2] Cash Entry</span>
                        <span class="label">[F3] Check Entry</span>
                        <span class="label">[Arrow Up/Down] Navigation up and down</span>
                        <span class="label">[Tab] Navigation right</span>
                        <span class="label">[Shift-Tab] Navigation left</span>
                        <br>
                        <span class="label">[F4] Select Row</span>
                        <span class="label">[F5] Select Row (Check)</span>
                        <span class="label">[F6] Transaction Void</span>
                    </div>

                </div>

            </div>
        </div>

        <div class="col-md-9 transactions">
            <div class="portlet light active" id="trn-list" style="min-height: 550px;">

            </div>
        </div>

        </div>


<div class="footer page-footer">
    PECO.apps Version 2 Beta

    <span class="pull-right" id="clock"></span>
</div>



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
<script src="<?php echo base_url(); ?>assets/global/plugins/input-mask/jquery.inputmask.bundle.js"></script>
<script src="<?php echo base_url(); ?>assets/pages/tellering/main.js"></script>

<script type="text/javascript">
   TELLERING.init();
</script>