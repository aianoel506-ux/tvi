<style>
    .mt-list-item {
        margin-bottom: 0px !important;
        padding-bottom: 10px !important;
    }
    .mt-element-list, .mt-list-container, .list-trails{
        border: none !important;
    }
    .mt-list-container {
        padding-left: 0px !important;
        padding-right: 0px !important;
    }
    .mt-list-item {
        margin-top: 0px !important;
        padding-top: 10px !important;
    }

    .form-md-line-input {
        margin-bottom: 10px !important;
        margin-top: 0px !important;
        padding: 0px 0px !important;
    }
</style>

<?php

$refid =  $this->uri->segment(4);

/**
 * Created by PhpStorm.
 * User: ITD-SE
 * Date: 5/30/2018
 * Time: 4:24 PM
 */
$ci = &get_instance();
$ci->load->model('model_ts');

$ts_qry = $ci->model_ts->get_ticket_details($dataid);
$ts_decode = json_decode($ts_qry);

if($ts_decode) {
$ts_info = $ts_decode->qry;
$ts_traill = $ts_decode->trail;
$ts_trail_cnt = $ts_decode->trailno;
$ts_team = $ts_decode->team;

$user_fullname = get_users_info($ts_info->createdby)->firstname. ' ' . get_users_info($ts_info->createdby)->lastname;

$middlename = (isset($ts_info->middlename[0])) ? strtoupper($ts_info->middlename[0]) .'.' : '';
$complainants = ($ts_info->firstname!='') ? $ts_info->lastname . ', '.$ts_info->firstname. ' ' . $middlename  : $ts_info->compname;
$tcsource = get_types_label_format($ts_info->repsource, false, false, false, false, false);
$createdby = '('. get_users_info($ts_info->createdby)->username . ') ' . $user_fullname;

if($ts_info->status!=305) {
    $ticket_trail_arr = array(
        'ticketid' => $ts_info->sysid,
        'codes' => 'READ',
        'descs' => 'By: ' . user_info(user_id())->username,
        'createdby' => user_id()
    );
    $this->db->insert('ticketing_details_trails', $ticket_trail_arr);
}

$qry_trail_lastread = $this->db->select()->from('ticketing_details_trails')
    ->where(array('ticketid' => $ts_info->sysid, 'codes' => 'READ'))
    ->order_by('datecreated', 'desc')
    ->get()->row();

$qry_trail_accomp = $this->db->select()->from('ticketing_details_trails')
    ->where(array('ticketid' => $ts_info->sysid, 'codes' => 'TSACCOMPLISHMENT'))
    ->order_by('datecreated', 'desc')
    ->get()->row();

$lastt_action = 'No Accomplishment Remarks';

if($ts_info->status == 314) {
    $qry_accomplish = $this->db->select('remarks')->from('ticketing_details_trails')
        ->where(array('ticketid' => $ts_info->sysid, 'remarks != ' => ''))
        ->order_by('datecreated', 'desc')
        ->get()->row();
    if($qry_accomplish) {
        $lastt_action = $qry_accomplish->remarks;
    }
}

$qry_trail_findings = $this->db->select()->from('ticketing_details_logs_findings')
    ->where(array('ticketid' => $ts_info->sysid, 'status' => 1))
    ->get()->row();

$qry_trail_equipments = $this->db->select()->from('ticketing_details_logs_equipments')
    ->where(array('ticketid' => $ts_info->sysid, 'status' => 1))
    ->get()->row();

$check_ticket_type = $this->db->select("tickettype")->from("ticketing_details_logs")
    ->where(array("sysid" => $refid))->get()->row();

$gettransactioninfo = $this->db->select("")->from("legal_transactions")
    ->where(array("refid" => $refid))->get()->row();

$start_month = (date('m') == 12) ? 1 : (date('m') + 1);
$start_year = (date('m') == 12) ? (date('Y') + 1) : (date('Y'));
?>
<div class="row">

    <div class="col-md-3">
        <h3 class="bold"><?php echo $complainants;?></h3>
        <hr>
        <ul class="list-group summary column no-border">
            <li class="list-group-item"><span class="col-md-5 label-name">Source</span><span class="col-md-7 label-default" id="" style="color: red;"><?php echo $tcsource;?></span> </li>
            <li class="list-group-item"><span class="col-md-5 label-name">District</span><span class="col-md-7 label-default" id="amt_paid"><?php echo get_district_name($ts_info->district); ?></span> </li>
            <li class="list-group-item"><span class="col-md-5 label-name">Barangay</span><span class="col-md-7 label-default" id="nobills"><?php echo $ts_info->barangay; ?></span> </li>

            <li class="list-group-item"><span class="col-md-5 label-name">Landmarks</span><span class="col-md-7 label-default" id="amt_interest"><?php echo $ts_info->landmarks; ?></span> </li>
            <li class="list-group-item"><span class="col-md-5 label-name">Report Stated</span><span class="col-md-7 label-default" id="lastpay"><?php echo $ts_info->remarks; ?></span> </li>
            <li class="list-group-item"><span class="col-md-5 label-name">Date Created</span><span class="col-md-7 label-default" id="mtrno"><?php echo $ts_info->datecreated; ?></span> </li>

            <li class="list-group-item"><span class="col-md-5 label-name">Created By</span><span class="col-md-7 label-default" id="amt_overdue"><?php echo $createdby; ?></span> </li>
            <li class="list-group-item"><span class="col-md-5 label-name">Status</span><span class="col-md-7 label-default" id="amt_current"><?php echo get_types_label_format($ts_info->status); ?></span> </li>
            <li class="list-group-item"><span class="col-md-5 label-name">Last Read</span><span class="col-md-7 label-default" id="kwh_ave"><?php echo ($qry_trail_lastread) ? $qry_trail_lastread->descs : ''; ?></span> </li>
        </ul>


        <hr>
        <div class="btn-group">
            <a href="<?php echo base_url() ?>module/667be543b02294b7624119adc3a725473df39885/list" style="margin-right: 10px;" class="btn btn-danger inline">Back <i class="fa fa-arrow-left"></i></a>

            <button class="btn btn-primary inline"><i class="fa fa-print"></i> Print Statement</button>
            <button class="btn btn-default inline"><i class="fa fa-sign-in"></i> Queue</button>
        </div>
    </div>

    <div class="col-md-9">

        <div class="portlet light bordered">
            <div class="portlet-title tabbable-line">
                <div class="caption">

                    <span class="caption-subject font-green bold uppercase">
                            App Number: <?php echo $dataid; ?>

                        </span>
                    <div class="caption-desc font-grey-cascade"></div>
                </div>

                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#ledger" data-toggle="tab" aria-expanded="true"><i class="fa fa-file-text-o"></i> Ledger</a>
                    </li>
                    <li class="">
                        <a href="#payments" data-toggle="tab" aria-expanded="false"><i class="fa fa-file-text-o"></i> Payments</a>
                    </li>

                    <?php
                    if($check_ticket_type->tickettype == 150){
                    ?>
                    <li class="">
                        <a href="#reports" data-toggle="tab" aria-expanded="false"><i class="fa fa-file-text-o"></i> IR Reports</a>
                    </li>
                    <?php } ?>
                    <li class="">
                        <a href="#logs" data-toggle="tab" aria-expanded="false"><i class="fa fa-file-text-o"></i> Logs</a>
                    </li>
                </ul>
            </div>
            <div class="portlet-body ">
                <div class="row">
                    <div class="col-md-12">

                        <div class="tab-content">

                            <div class="row">
                                <div class="col-md-4">
                                    <p style="margin: 3px 0px;" class="font-green"><i class="fa fa-tag"></i> Connection Status</p>

                                    <div class="well" style="padding: 10px 10px;">
                                        <ul class="list-group summary column no-border list-group-sm">
                                            <li class="list-group-item">
                                                <span class="col-md-4 label-name">Servno</span>
                                                <span class="col-md-6 label-default">M13049</span>
                                                <span class="col-md-2 label-default">1</span>
                                            </li>
                                            <li class="list-group-item">
                                                <span class="col-md-4 label-name">Name</span>
                                                <span class="col-md-8 label-default">N/A</span>
                                            </li>
                                            <li class="list-group-item">
                                                <span class="col-md-4 label-name">Address</span>
                                                <span class="col-md-8 label-default">N/A</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <p style="margin: 3px 0px;" class="font-green"><i class="fa fa-tag"></i> Accounts</p>
                                    <div class="well" style="padding: 10px 10px;">
                                        <ul class="list-group summary column no-border list-group-sm">
                                            <li class="list-group-item">
                                                <span class="col-md-4 label-name">Total Amount</span>
                                                <span class="col-md-8 label-default number" id="total_amt">0.00</span>
                                            </li>
                                            <li class="list-group-item">
                                                <span class="col-md-4 label-name">Total Paid</span>
                                                <span class="col-md-8 label-default number" id="total_paid">0.00</span>
                                            </li>
                                            <li class="list-group-item">
                                                <span class="col-md-4 label-name">Total Balance</span>
                                                <span class="col-md-8 label-default number" id="total_balance">0.00</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <p style="margin: 3px 0px;" class="font-green"><i class="fa fa-tag"></i> Updates</p>
                                    <div class="well" style="padding: 10px 10px;">
                                        <ul class="list-group summary column no-border list-group-sm">
                                            <li class="list-group-item">
                                                <span class="col-md-4 label-name">Date of Last Pay</span>
                                                <span class="col-md-8 label-default number">0.00</span>
                                            </li>
                                            <li class="list-group-item">
                                                <span class="col-md-4 label-name">Amount Paid</span>
                                                <span class="col-md-8 label-default number">0.00</span>
                                            </li>
                                            <li class="list-group-item">
                                                <span class="col-md-4 label-name">Paid to</span>
                                                <span class="col-md-8 label-default number">0.00</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade in active" id="ledger">

                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <a id="btn_ledger_entry" style="width: 100%; display: inline-block" class="accordion-toggle accordion-toggle-styled collapsed" data-toggle="collapse" data-parent="#accordion3" href="#ledger_entry_form"> <i class="fa fa-edit"></i> Ledger Entry </a>
                                        </h4>
                                    </div>
                                <?php
                                if($check_ticket_type->tickettype == 150){
                                ?>

                                    <div id="ledger_entry_form" class="panel-collapse collapse" style="padding: 10px 10px;">
                                    <form id="frm_ledger_entry" title="Apprehension Transaction Entry"  action="<?php echo base_url('legal/addratrans'); ?>" method="post">
                                        <input value="<?php echo $check_ticket_type->tickettype; ?>" name="reftype" type="hidden"/>
                                        <input value="<?php echo $refid; ?>" name="refid" type="hidden"/>
                                        <div class="form-group row">
                                            <div class="col-md-3">
                                                Trn.Type
                                                <input required name="trntype"class="form-control" id="input_entry_type" placeholder="Entry.."/>
                                                <label>Trn.Amount</label>
                                                <input required name="trnamt" class="form-control" id="input_entry_amt" placeholder="Entry.." />
                                            </div>

                                            <div class="col-md-3">
                                                Month to pay
                                                <input required value="0" name="payablecnt" class="form-control" id="input_entry_monthpay" placeholder="Entry.." />
                                                <label>Bill Month Start</label>
                                                <input required value="<?php echo $start_month;?>" name="billmonth" class="form-control" id="input_entry_billyear" placeholder="Entry month.." />
                                            </div>

                                            <div class="col-md-3">
                                                Start Due
                                                <input required value="<?php echo date('Y-m-d', strtotime("+30 days"));?>" name="payablestartdue" class="form-control" id="input_entry_startdue" placeholder="Entry amount.." />
                                                <label>Bill Year</label>
                                                <input required value="<?php echo $start_year;?>" name="billyear" class="form-control" id="input_entry_billyear" placeholder="Entry month.." />
                                            </div>

                                            <div class="input-group col-md-3" style="padding-top: 20px;">

                                                <label for="input_check_payable" style="margin: 0px 0px;">Payable</label>
                                                <input name="payable" type="checkbox" class="icheck" id="input_check_payable" value="1">

                                                <hr style="margin: 19px 0px;">
                                                <div class="btn-group">
                                                    <button class="btn btn-default" type="reset">Reset</button>

                                                    <button type="submit" style="width: 150px;" class="btn btn-default"><i class="fa fa-save"></i> Save Entry</button>
                                                </div>

                                            </div>
                                        </div>
                                    </form>
                                    </div>
                                <?php
                                }
                                if($check_ticket_type->tickettype == 151) {
                                ?>
                                    <div id="ledger_entry_form" class="panel-collapse collapse">
                                    <form class="" id="frm_ledger_entry" title="Bounche Cheque Entry" style="margin-top: ;" action="<?php echo base_url('legal/addratrans'); ?>" method="post">
                                        <input value="<?php echo $check_ticket_type->tickettype; ?>" name="reftype" type="hidden"/>
                                        <input value="<?php echo $refid; ?>" name="refid" type="hidden"/>
                                        <input type="hidden" name="servno" id="radpservno">
                                        <input type="hidden" name="mtr" id="radpmtr">
                                        <div class="panel-body">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group form-md-line-input" style="">
                                                        <label>OR No.</label>
                                                        <div class="input-icon">
                                                            <i class="fa fa-file-text font-blue"></i>
                                                            <input type="text" class="form-control entry" required="" name="orno" id="orno" placeholder="0000" maxlength="">
                                                        </div>
                                                    </div>

                                                    <div class="form-group form-md-line-input" style="">
                                                        <label>Amount</label>
                                                        <div class="input-icon">
                                                            <i class="fa fa-file-text font-blue"></i>
                                                            <input type="text" class="form-control entry" required="" name="amt" id="bpamt" placeholder="0000" maxlength="">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group form-md-line-input" style="">
                                                        <label>Check No.</label>
                                                        <div class="input-icon">
                                                            <i class="fa fa-file-text font-blue"></i>
                                                            <input type="text" class="form-control entry" required="" name="checkno" id="checkno" placeholder="0000" maxlength="">
                                                        </div>
                                                    </div>
                                                    <div class="form-group form-md-line-input" style="">
                                                        <label>Interest</label>
                                                        <div class="input-icon">
                                                            <i class="fa fa-file-text font-blue"></i>
                                                            <input type="text" class="form-control entry" required="" name="int" id="int" placeholder="0000" maxlength="">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">

                                                    <div class="form-group form-md-line-input" style="">
                                                        <label>Bank Name</label>
                                                        <div class="input-group input-icon">
                                                            <i class="fa fa-file-text font-blue"></i>
                                                            <select name="bankname" id="input_bank_list" class="form-control">
                                                                <option value="">Select..</option>
                                                                <option value="1">BDO</option><option value="2">BPI</option><option value="3">METROBANK</option><option value="4">SECURITY BANK</option><option value="5">EAST WEST</option><option value="6">RCBC</option><option value="7">BANK OF COMMERCE</option><option value="8">UNIONBANK</option><option value="9">PSBank</option><option value="10">Stearling Bank of Asia</option><option value="11">UCPB</option><option value="12">ChinaBank</option><option value="13">AUB</option><option value="14">MayBank</option><option value="15">Landbank</option><option value="16">Equicum</option><option value="17">Queen Bank</option><option value="18">PNB</option><option value="19">PBB</option><option value="20">Malayan Bank</option><option value="21">PLANTERS BANK</option><option value="22">WEALTH BANK</option><option value="23">ROBINSONS BANK</option><option value="24">PBCOM</option>
                                                            </select>
                                                            <span class="input-group-btn">
                                                                <button class="btn btn-default"><i class="fa fa-plus"></i></button>
                                                            </span>
                                                        </div>
                                                    </div>



                                                    <div class="form-group form-md-line-input" style="">
                                                        <label>B.P. Date: </label>
                                                        <div class="input-group input-icon">
                                                            <i class="fa fa-file-text font-blue"></i>
                                                            <input type="text" id="input_date_spec" class="form-control entry" required="" name="bpdate">
                                                            <div class="input-group-btn">
                                                                <button type="reset" class="btn btn-danger inline" id=""><i class="fa fa-refresh"></i> Reset</button>
                                                                <button type="submit" class="btn btn-primary inline" id="" form-ajax-btn="true" label-loading="Wait.." label-default="Save Data" label-icon="fa-save"><i class="fa fa-save"></i> Save Data</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                    </div>
                                <?php
                                }
                                ?>

                                </div>

                                <hr style="margin: 3px 0px;">
                                <table style="margin-top: 10px; width: 100%" id="tbl_ledger" class="table table-hover table-bordered">
                                    <thead>
                                    <th>#</th>
                                    <th>Date Posted</th>
                                    <th>Type</th>
                                    <th>Acctno</th>
                                    <th>Amount</th>
                                    <th>Paid</th>
                                    <th>Due</th>
                                    <th><span title="Type of Payment" class="tooltips">ToP</span></th>
                                    <th>Monthly</th>
                                    <th>Status</th>
                                    <th>AR</th>
                                    <th><i class="fa fa-wrench"></i></th>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>

                            <div class="tab-pane fade in" id="payments">
                                <h4 class="bold">
                                    Flexible Payment Plan Table
                                </h4>
                                <hr>
                                <table class="table table-bordered table-condensed table-hover" id="tbl_flexible_payment_plan">
                                    <thead>
                                    <th>#</th>
                                    <th>Year</th>
                                    <th>Month</th>
                                    <th>Duedate</th>
                                    <th>Amount</th>
                                    <th>Date Paid</th>
                                    <th>Status</th>
                                    <th><i class="fa fa-wrench"></i></th>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                            <div class="tab-pane fade in" id="reports">
                                <form class="" action="<?php echo base_url('legal/submitirdata');?>" method="post" id="frm_legalraic">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group form-md-line-input">
                                                <label>Violation</label>
                                                <div class="input-icon">
                                                    <i class="fa fa-tag font-blue"></i>
                                                    <select required="" class="form-control" name="violation" id="select2violation">
                                                        <option value="">Violation</option>
                                                        <option value="1">1. Bypassing</option>
                                                        <option selected="" value="2">2. Illegal Connection</option>
                                                        <option value="3">3. Violation of Contract</option>
                                                        <option value="4">4. Tampering of Meter</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group form-md-line-input">
                                                <label>Inspector</label>
                                                <div class="input-icon">
                                                    <i class="fa fa-user font-blue"></i>
                                                    <input type="text" class="form-control" required="" name="empid" id="empid" placeholder="" maxlength="">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group form-md-line-input" style="">
                                                <label>IR No.</label>

                                                <div class="input-icon">
                                                    <i class="fa fa-tag font-blue"></i>
                                                    <input type="text" class="form-control" required="" name="irno" placeholder="IR No." maxlength="6">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group form-md-line-input" style="">
                                                <label>IR Date</label>

                                                <div class="input-icon">
                                                    <i class="fa fa-calendar font-blue"></i>
                                                    <input type="text" class="form-control" required="" name="irdate" id="irdate" placeholder="Select date..">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group  form-md-line-input" style="">
                                                <label>Remarks</label>
                                                <div class="input-group input-icon">
                                                    <i class="fa fa-comment-o font-blue"></i>
                                                    <input type="text" class="form-control" required="" name="rem" id="rem" placeholder="Enter remarks here..." maxlength="">
                                                    <span class="input-group-btn">
                                                        <button type="submit" class="btn btn-primary inline" form-ajax-btn="true" label-loading="Wait.." label-default="SAVE" label-icon="fa-save"><i class="fa fa-save fa-fw"></i> Save</button>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </form>

                                <hr>
                                <table class="table table-hover table-striped" id="tbl_ir_reports">
                                    <thead>
                                    <th>IR No.</th>
                                    <th>IR Date</th>
                                    <th>Violation</th>
                                    <th>Inspector</th>
                                    <th>Control</th>
                                    </thead>
                                </table>
                            </div>
                            <div class="tab-pane fade in" id="logs">

                                <div class="col-md-12">
                                    <div class="mt-element-list">
                                        <div class="mt-list-container list-default">
                                            <ul class="list-trails">
                                                <?php if($ts_traill) {
                                                    foreach ($ts_traill as $trow) {
                                                        $user_fullname = get_users_info($trow->createdby)->firstname.' '.get_users_info($trow->createdby)->lastname;
                                                        ?>
                                                        <li class="mt-list-item done">
                                                            <div class="list-icon-container">
                                                                <a href="javascript:;">
                                                                    <i class="icon-check"></i>
                                                                </a>
                                                            </div>
                                                            <div class="list-datetime" style="margin-right: 10px;"><?php echo $trow->datecreated; ?></div>
                                                            <div class="list-item-content">
                                                                <h3 class="uppercase">
                                                                    <?php if($trow->statusid == '') { ?>
                                                                        <b><a href="javascript:;"><?php echo $trow->codes; ?></a></b>
                                                                    <?php }else { ?>
                                                                        <b><a href="javascript:;"><?php echo get_types_label_format($trow->statusid, false, false, false, 'javascript:;', false, true)->text; ?></a></b>
                                                                    <?php } ?>
                                                                </h3>
                                                                <p><?php echo $trow->descs; ?></p>
                                                                <small class="font-green-haze">By: <?php echo $user_fullname; ?></small>
                                                            </div>
                                                        </li>
                                                        <?php
                                                    }
                                                    if($ts_trail_cnt>5) {
                                                        echo '<li class="mt-list-item done">';
                                                        echo '<a class="pull-right" href="'.base_url('module/d321d6f7ccf98b51540ec9d933f20898af3bd71e/data/'.$dataid.'/logs').'">View All ('.$ts_trail_cnt.')</a>';
                                                        echo '</li>';
                                                    }

                                                }
                                                ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <?php
    } else {
        echo page_construction();
    }
    ?>

    <script src="<?php echo base_url(); ?>assets/pages/legal/app.js" type="text/javascript"></script>
    <script>
        LEGAL.app(<?php echo $dataid; ?>);
    </script>
