<?php
$qry_account_info = $this->model_query->get_active_owner($dataid);

$firstname = $qry_account_info->FIRSTNAME;



$total_load = acct_total_loads($dataid);

$qry_account_main = $this->db->select()->from("customer_accounts_main")->where('sysid', $dataid)->get()->row();
$qry_account_owner = $this->db->select()->from("customer_accounts_owners")->where(array('accountid' => $dataid, 'status' => 1))->get()->row();
$qry_account_owner_addr = $this->db->select()->from("customer_accounts_address")->where(array('acctid' => $qry_account_owner->sysid, 'status' => 1))->get()->row();
$qry_account_owner_geo = $this->db->select()->from("customer_accounts_subscription_geodata")->where(array('addressid' => $qry_account_owner_addr->sysid, 'status' => 1))->get()->row();


$acctrate = ($this->model_query->get_active_owner($dataid)) ? $this->model_query->get_active_owner($dataid)->ACCTRATE : "";

// GET SEQUENC NO
$query_seq = $this->db->select('sequence')->from('customer_accounts_servsequence')->where('classid', $acctrate)->order_by('sequence', 'desc')->get()->row();
$last_seq = ($query_seq) ? $query_seq->sequence : "N/A";

// GET TRN ID
// TRAIL ID 
$trail_id = $this->uri->segment(4);
$query_trn = $this->db->select()->from('transaction_request_main_trails')->where(array('sysid' => $trail_id, 'dataid' => $dataid))->get()->row();
$trn_id = ($query_trn) ? $query_trn->trnid : "";

$account_type = 'Regular';
if ($total_load >= 15000) {
    $account_type = 'Spacial';
}
?>

<style>
    .label-default, .list-group-item, .list-group-item h3{
        dispaly: inline-block !important;
        word-break: break-all !important;
        word-wrap: wrap !important;
    }
</style>

        <div class="tab-pane fade in" id="data">


            <div class="row">
                <div class="col-md-4">
                    <div class="portlet light">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-edit"></i>
                                <span class="caption-subject font-green-sharp bold uppercase">Information</span>
                                <span class="caption-helper">General</span>
                            </div>
                            <div class="tools">
                                <a href="javascript:;" class="collapse" data-original-title="" title="">
                                </a>

                                <a href="#portlet-config" data-toggle="modal" class="config" data-original-title="" title="">
                                </a>

                                <a href="javascript:;" class="reload" data-original-title="" title="">
                                </a>

                                <a href="javascript:;" class="fullscreen" data-original-title="" title="">
                                </a>

                                <a href="javascript:;" class="remove" data-original-title="" title="">
                                </a>

                            </div>
                        </div>
                        <div class="portlet-body">

                                    <ul class="list-group summary column no-border" style="margin: 5px 15px;">
                                            <li class="list-group-item">
                                                <div class="row">
                                                    <span class=" label-name col-md-4">Servno </span><span class="label label-default col-md-8 pull-right"><span id="name"><?php echo $this->model_query->get_active_owner($dataid)->SERVNO; ?></span></span></div>
                                            </li>

                                        <?php if ($this->model_query->get_active_owner($dataid)->TYPES == 5) { ?>
                                            <li class="list-group-item">
                                                <div class="row">
                                                    <span class=" label-name col-md-4">Name </span><span
                                                            class="label label-default col-md-8 pull-right"><span
                                                                id="name"><?php echo $this->model_query->get_active_owner($dataid)->FIRSTNAME; ?><?php echo $this->model_query->get_active_owner($dataid)->MIDDLENAME; ?><?php echo $this->model_query->get_active_owner($dataid)->LASTNAME; ?></span></span>
                                                </div>
                                            </li>
                                        <?php }  else { ?>
                                            <?php if ($this->model_query->get_active_owner($dataid)->TYPES == 1) { ?>
                                                <li class="list-group-item">
                                                    <div class="row">
                                                        <span class=" label-name col-md-4">Name </span><span
                                                                class="label label-default col-md-8 pull-right"><span
                                                                    id="name"><?php echo $this->model_query->get_active_owner($dataid)->FIRSTNAME; ?><?php echo $this->model_query->get_active_owner($dataid)->MIDDLENAME; ?><?php echo $this->model_query->get_active_owner($dataid)->LASTNAME; ?></span></span>
                                                    </div>
                                                </li>
                                                <li class="list-group-item">
                                                    <div class="row"><span
                                                                class=" label-name col-md-4">Contact Number </span><span
                                                                class="label label-default col-md-8 pull-right"><span
                                                                    id="name">+639284450000</span></span>
                                                    </div>
                                                </li>
                                            <?php } else { ?>
                                                <li class="list-group-item">
                                                    <div class="row">
                                                        <span class=" label-name col-md-4">Corp. Name </span><span
                                                                class="label label-default col-md-8 pull-right"><span
                                                                    id="name"><?php echo $this->model_query->get_active_owner($dataid)->CORPNAME; ?>
                                                                - <?php echo $this->model_query->get_active_owner($dataid)->CORPDESC; ?></span></span>
                                                    </div>
                                                </li>
                                                <li class="list-group-item">
                                                    <div class="row"><span
                                                                class=" label-name col-md-4">Contact Number </span><span
                                                                class="label label-default col-md-8 pull-right"><span
                                                                    id="name">+639284450000</span></span>
                                                    </div>
                                                </li>
                                                <li class="list-group-item">
                                                    <div class="row"><span
                                                                class=" label-name col-md-4">Address </span><span
                                                                class="label label-default col-md-8 pull-right"><span
                                                                    id="name"><?php
                                                                echo $this->model_query->get_active_owner($dataid)->STREET . ', ';
                                                                echo get_district_name($this->model_query->get_active_owner($dataid)->DIST) . ', ';
                                                                echo get_city_name($this->model_query->get_active_owner($dataid)->CITY);
                                                                ?></span></span>
                                                    </div>
                                                </li>
                                                <li class="list-group-item">
                                                    <hr style="margin: 3px 0px;">
                                                </li>
                                                <li class="list-group-item">
                                                    <div class="row">
                                                        <h4 style="margin: 0px 0px;"><span class=" label-name col-md-4">Representative </span><span
                                                                    class="label label-default col-md-8 pull-right"><span
                                                                        id="name"><?php echo $this->model_query->get_active_owner($dataid)->LASTNAME; ?>
                                                                    , <?php echo $this->model_query->get_active_owner($dataid)->FIRSTNAME; ?></span></span>
                                                    </div>
                                                    </h4>
                                                </li>
                                            <?php }
                                        }
                                        ?>
                                        <li class="list-group-item">
                                            <div class="row"><span class=" label-name col-md-4">Brgy / Street </span><span class="label label-default col-md-8 pull-right"><span id="name"><?php echo $this->model_query->get_active_owner($dataid)->STREET; ?></span></span>
                                            </div>
                                        </li>
                                        <li class="list-group-item">
                                            <div class="row"><span class=" label-name col-md-4">District </span><span class="label label-default col-md-8 pull-right"><span id="name"><?php echo (acct_gdlb($dataid)) ? acct_gdlb($dataid)->DISTNAME : "N/A"; ?></span></span>
                                            </div>
                                        </li>
                                        <li class="list-group-item">
                                            <div class="row"><span class=" label-name col-md-4">Lot & Book </span><span class="label label-default col-md-8 pull-right"><span id="name"><?php echo (acct_gdlb($dataid)) ? acct_gdlb($dataid)->GDLB : "N/A"; ?></span></span>
                                            </div>
                                        </li>
                                    </ul>
                                    <hr>
                                    
                                    <a href="javascript:;" class="icon-btn">
                                    <i class="fa fa-bar-chart-o"></i>
                                    <div>
                                         Reports
                                    </div>
                                    </a>
                                    
                                    <a href="javascript:;" class="icon-btn">
                                    <i class="fa fa-bullhorn"></i>
                                    <div>Complaints</div>
                                    </a>
                            <hr>
                            <ul class="pager">
                                <li class="previous disabled">
                                    <a href="javascript:;">
                                        <i class="fa fa-angle-left"></i> Previous </a>
                                </li>
                                <li class="next">
                                    <a href="javascript:;">
                                        Next <i class="fa fa-angle-right"></i> </a>
                                </li>
                            </ul>
                        </div>

                    </div>






                </div>

                <div class="col-md-8">
                    <div class="portlet box blue">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-edit"></i>
                                <span class="caption-subject bold "><?php echo date('Y'); ?></span>
                            </div>
                            <ul class="nav nav-tabs">
                                <li class="active">
                                    <a href="#billar" data-toggle="tab" aria-expanded="true">
                                    Billing AR
                                    </a>
                                </li>
                                <li class="">
                                    <a href="#acctdetails" data-toggle="tab" aria-expanded="false">
                                    Details
                                    </a>
                                </li>
                                <li class="">
                                    <a href="#payments" data-toggle="tab" aria-expanded="false">
                                    Payments
                                    </a>
                                </li>
                            </ul>

                        </div>
                        <div class="portlet-body tab-content">
                            <div class="tab-pane active fade in" id="billar">
                                <div class="row">
                                <div class="col-md-12">
                                <h3 style="width: 30%; float: left; margin-top: 0px;">AR/Billing</h3>
                                <form id="frm_acct_rec" action="" method="post" class="pull-right">
                                    <div class="input-group" style="width: 200px;">
                                        <input class="form-control input-sm" id="acct_year" placeholder="Year.." />
                                        <span class="input-group-btn">
                                                <button type="submit" id="btn_acct_rec" class="btn btn-sm">Exec</button>
                                            </span>
                                    </div>
                                </form>
                                </div>
                                </div>
                                <hr>
                                <table id="tbl_acct_rec" class="table table-hover table-stripped table-condensed table-bordered tbl-sm"  style="margin-top: -5px !important;">
                                <thead>
                                <th>Month</th>
                                <th>Bill No.</th>
                                <th>KWH</th>
                                <th>Amount Due</th>
                                <th>Interest</th>
                                <th>Duedate</th>
                                <th>Amount Paid</th>
                                <th>Date Paid</th>
                                <th>Rem</th>
                                <th></th>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                            </div>

                            <div class="tab-pane fade in" id="acctdetails">
                                <h3>Details</h3>
                            </div>

                            <div class="tab-pane fade in" id="payments">
                                <h3>Payments</h3>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>

<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-select/bootstrap-select.min.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/select2/select2.min.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/datatables/jquery.dataTables.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/datatables/dataTables.bootstrap.js"></script>
<script src="<?php echo base_url(); ?>assets/pages/tellering/main.js"></script>
<script type="text/javascript">
    TELLERING.acctrec(<?php echo $dataid; ?>);
</script>
