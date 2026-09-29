<?php
$servno = $this->input->post('servno');
if($servno) {
    $qry_search = $this->db->select()
        ->from('customer_accounts_main')
        ->where('servicenumber', $servno)
        ->get()->row();
    $id = ($qry_search) ? $qry_search->sysid : 101;
}else{
    $id = $this->uri->segment(4);
}


$next_page_disabled = 'disabled';
$next_page_id = '';
$next_servno = 'None';

$frm_basic_info = $this->input->post('basicinfosubmit');
$frm_other_info = $this->input->post('otherinfosubmit');
$input = $this->input->post();
$msg = '';



// UPDATE DATA

if($frm_other_info) {
    $msg .= '<div class="alert alert-success">';
    $msg .= '<button type="button" class="close" data-dismiss="alert"></button>';
    $msg .= '<h4><i class="fa fa-check"></i> Other info submited!</h4>';
    $msg .= $output;
    $msg .= '</div>';
}

// DISPLAY DATA
$info = $this->model_query->get_active_owner($id);
$qry_next = $this->db->select('sysid, servicenumber')->from('customer_accounts_main')->where('sysid > ', $id)->get()->row();

if($qry_next) {
    $next_page_disabled = '';
    $next_page_id = $qry_next->sysid;
    $next_servno = $qry_next->servicenumber;
}

$prev_page_disabled = 'disabled';
$prev_page_id = '';
$prev_servno = 'None';
$qry_prev= $this->db->select('sysid, servicenumber')->from('customer_accounts_main')->where('sysid < ', $id)->order_by('sysid', 'desc')->get()->row();

if($qry_prev){
    $prev_page_disabled = '';
    $prev_page_id = $qry_prev->sysid;
    $prev_servno = $qry_prev->servicenumber;
}




?>

<style>
    .control-label {
        color: #8a8a8a;
        font-size: 13px;
    }
</style>

        <?php if($info) { ?>
        <div class="row">
            <div class="col-md-12">
                <h3 class="page-title col-md-10">
                    <span class="caption text-primary text-bold"><?php echo $info->SERVNO; ?></span>
                    <span class="caption-helper"> -
                        <?php
                        if($info->TYPES==1) {
                            echo $info->LASTNAME. ', '.$info->FIRSTNAME .' '. $info->MIDDLENAME;
                        }

                        if($info->TYPES==5) {
                            echo $info->FIRSTNAME;
                        }
                        ?>
                        </span>

                </h3>
                <div class="input-group input-icon">
                    <i class="fa fa-search"></i>
                    <input class="form-control" placeholder="Jump to.." />
                    <span class="input-group-btn"><button class="btn btn-default">Go</button> </span>
                </div>
            </div>
        </div>

        <?php echo $msg; ?>


        <div class="row">
            <div class="col-md-4">
                <div class="portlet blue box">
                    <div class="portlet-title">
                        <div class="caption">
                           <i class="fa fa-tag"></i> Basic Info
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <form class="form-horizontal form-bordered form-row-stripped" action="<?php echo base_url('cad/updatecustomerinfo'); ?>" method="post" id="frm_basic_info">
                            <input type="hidden" name="basicinfosubmit" value="1"/>
                            <input type="hidden" name="mode" value="1"/>
                            <input type="hidden" name="id" value="<?php echo $id; ?>"/>
                            <div class="form-body">
                            <div class="form-group">
                                <label class="control-label col-md-3">
                                    City
                                </label>
                                <div class="col-md-9">
                                    <input id="input_city" class="form-control" name="city" placeholder="City" value="<?php echo $info->CITY; ?>" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3">
                                    District
                                </label>
                                <div class="col-md-9">
                                    <input id="input_dist" class="form-control" name="district" placeholder="District" value="" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3">
                                    Rate Class
                                </label>
                                <div class="col-md-9">
                                    <input id="input_rate" class="form-control" name="rate" placeholder="Rate Class" value="" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3">
                                    GDLB
                                </label>
                                <div class="col-md-9">
                                    <input id="input_gdlb" class="form-control" name="gdlb" placeholder="GDLB" value="" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3">
                                    Landmark
                                </label>
                                <div class="col-md-9">
                                    <input id="input_addrspec" class="form-control" name="addrspec" placeholder="Specific Address" value="<?php echo $info->STREET; ?>" />
                                </div>
                            </div>

                            <div class="form-actions">
                                <span id="query_loading_basic"></span>
                                <div class="btn-group pull-right"><button class="btn btn-primary">Update</button></div>

                            </div>
                            </div>



                        </form>
                    </div>
                </div>

            </div>

            <div class="col-md-8">
                <div class="portlet light">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-tag"></i> <?php echo $info->RATE; ?>
                        </div>
                    </div>
                    <div class="portlet-body form" style="min-height: 300px;">

                        <form class="form-horizontal form-bordered form-row-stripped" action="<?php echo base_url('cad/updatecustomerinfo'); ?>" method="post" id="frm_other_info">
                            <input type="hidden" name="otherinfosubmit" value="1"/>
                            <input type="hidden" name="mode" value="2"/>
                            <input type="hidden" name="id" value="<?php echo $id; ?>"/>
                            <input type="hidden" name="mtrsysid" value="<?php echo $info->MTRSYSID; ?>"/>
                            <div class="form-body">
                                <div class="form-group">
                                    <label class="control-label col-md-3">
                                        Meter No.
                                    </label>
                                    <div class="col-md-3">
                                        <input id="input_city" class="form-control" name="mtrno" placeholder="MTR Number" value="<?php echo $info->MTRNO; ?>" />
                                    </div>

                                    <label class="control-label col-md-2">
                                        Serial No.
                                    </label>
                                    <div class="col-md-4">
                                        <input id="input_city" class="form-control" name="mtrser" placeholder="MTR Serial" value="<?php echo $info->MTRSER; ?>" />
                                    </div>
                                </div>


                                <div class="form-group">
                                    <label class="control-label col-md-3">
                                        Contract Date
                                    </label>
                                    <div class="col-md-3">
                                        <input id="input_contract" class="form-control" name="contractdate" placeholder="Contract Date" value="<?php echo $info->CONTRACTDATE; ?>" type="date" />
                                    </div>

                                    <label class="control-label col-md-2">
                                        Connection Date
                                    </label>
                                    <div class="col-md-4">
                                        <input id="input_contract" class="form-control" name="conndate" placeholder="Status Date" value="<?php echo $info->CONNDATE; ?>" type="date" />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-md-3">
                                        RGD #
                                    </label>
                                    <div class="col-md-3">
                                        <input id="input_rgdno" class="form-control" name="rgdno" placeholder="RGD No." value="<?php echo $info->RGDNO; ?>" />
                                    </div>

                                    <label class="control-label col-md-2">
                                        RGD Amt.
                                    </label>
                                    <div class="col-md-4">
                                        <input id="input_rgdamt" class="form-control" name="rgdamt" placeholder="RGD Amt." value="<?php echo $info->RGDAMT; ?>" />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-md-3">
                                        Present Read
                                    </label>
                                    <div class="col-md-3">
                                        <input id="input_presread" class="form-control" name="presread" placeholder="Present Reading" value="<?php echo $info->PRESREADING; ?>" />
                                    </div>

                                    <label class="control-label col-md-2">
                                        Present Read Date
                                    </label>
                                    <div class="col-md-4">
                                        <input id="input_presread_date" class="form-control" name="presreaddate" placeholder="Present Reading Date" value="<?php echo $info->PRESREADDATE; ?>" type="date" />
                                    </div>
                                </div>

                                <div class="form-group">

                                    <label class="control-label col-md-3">
                                        Previous Read
                                    </label>
                                    <div class="col-md-3">
                                        <input id="input_prevread" class="form-control" name="prevread" placeholder="Preavious Reading" value="<?php echo $info->PREVREADING; ?>" />
                                    </div>
                                    <label class="control-label col-md-2">
                                        Previous Read Date
                                    </label>
                                    <div class="col-md-4">
                                        <input id="input_prevread_date" class="form-control" name="prevreaddate" placeholder="Preavious Reading Date" value="<?php echo $info->PREVREADDATE; ?>" type="date" />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-md-3">
                                        Current Load
                                    </label>
                                    <div class="col-md-3">
                                        <input id="input_load" class="form-control" name="load" placeholder="Load" value="<?php echo $info->LOAD; ?>" />
                                    </div>

                                    <label class="control-label col-md-2">
                                        Date Updated
                                    </label>
                                    <div class="col-md-4">
                                        <input id="input_loaddate" class="form-control" name="loaddate" placeholder="Load Date" value="<?php echo $info->LOADDATE; ?>" type="text"/>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-md-3">
                                        Mult Code
                                    </label>
                                    <div class="col-md-3">
                                        <input id="input_multcode" class="form-control" name="nultcode" placeholder="Mult Code" value="<?php echo $info->MULTCODE; ?>" />
                                    </div>

                                    <label class="control-label col-md-2">
                                        Date Updated
                                    </label>
                                    <div class="col-md-4">
                                        <input id="input_multcodedate" class="form-control" name="multcodedate" placeholder="Date Updated" value="<?php echo $info->MULTCODEDATE; ?>" type="text"/>
                                    </div>
                                </div>

                                <div class="form-actions">
                                    <span id="query_loading_other"></span>
                                    <div class="btn-group pull-right"><button type="submit" class="btn btn-primary">Update</button> </div>
                                </div>
                            </div>



                        </form>

                    </div>
                    <hr>
                    <ul class="pager">
                        <li class="previous <?php echo $prev_page_disabled; ?>">
                            <a href="<?php echo base_url().'module/'.$this->uri->segment(2); ?>/view/<?php echo $prev_page_id; ?>">
                                <i class="fa fa-angle-double-left"></i> <?php echo $prev_servno; ?>
                            </a>
                        </li>
                        <li class="next <?php echo $next_page_disabled; ?>">
                            <a id="btn_next_cust" href="<?php echo base_url().'module/'.$this->uri->segment(2); ?>/view/<?php echo $next_page_id; ?>">
                                <?php echo $next_servno; ?> <i class="fa fa-angle-double-right"></i>
                            </a>
                        </li>
                    </ul>
                </div>

            </div>

        </div>





    <?php
    } else {
        echo "Error searching data!";
    }
    ?>


<script src="<?php echo base_url(); ?>assets/pages/crm/view.js" type="text/javascript"></script>
<script>
    PECO.select2Basic ($('#input_city'), 'query/select2city', 'City', false, false, 1);                                     // 1 Selected Value for Iloilo
    PECO.select2Basic ($('#input_dist'), 'query/select2district', 'District', false, false, <?php echo $info->DIST; ?>);    //
    PECO.select2Basic ($('#input_gdlb'), 'query/select2gdlb', 'GDLB', false, false, <?php echo $info->GDLB; ?>);            //
    PECO.select2Basic ($('#input_rate'), 'query/select2rate', 'Rate Class', false, false, <?php echo $info->RATEID; ?>);    //
    PECO.select2Basic ($('#input_multcode'), 'query/select2multcode', 'Mult Code', false, false, <?php echo $info->MULTCODE; ?>);    //

    CRMVIEW.init();
</script>