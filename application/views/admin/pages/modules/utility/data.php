<style>
    .form-control[type=date] {
        padding: 0px 10px !important;
    }
</style>

<?php


$qry = get_joborder_info($dataid);

if($qry) {
    $acct_name = '';
    $remarks = '';
    $dateaccomp = '';
    $accompby = '';
    $dateencode = '';

    $ownerid = $qry->acctid;

    if ($qry->tickettype == 322) {
        $ownerid = 0;
        $qry_acct = get_application_details($qry->acctid)->info;
        $middlename = (isset($qry_acct->middlename[0])) ? $qry_acct->middlename[0].'.' : '';
        $ownername = ($qry_acct) ? $qry_acct->lastname.', '.$qry_acct->firstname.' '.$middlename : 'N/A';
    } else {
        if ($qry->types == 5) {
            $qry_acctname = $this->db->select()->from('customer_accounts_name_legacy')
                ->where(array('sysid' => $qry->ownerid))
                ->get()->row();
            $ownername = ($qry_acctname) ? $qry_acctname->name : 'N/A';
        } else {
            $qry_acctname = $this->db->select()->from('person')
                ->where(array('sysid' => $qry->ownerid))
                ->get()->row();
            $ownername = ($qry_acctname) ? $qry_acctname->lastname . ', ' . $qry_acctname->firstname : 'N/A';
        }
    }


    $subjectprofile = base_url('peco/customer/' . $ownerid);

    $qry_trnid = $this->db->query("
        SELECT
        rmt.sysid,
        rmt.trnid,
        rmt.stageid,
        rmt.dataid,
        rm.flowid
        FROM
        transaction_request_main_trails AS rmt
        INNER JOIN transaction_request_main AS rm ON rmt.trnid = rm.sysid
        WHERE rmt.sysid = $trailid
            ")->row();

    $trnid = ($qry_trnid) ? $qry_trnid->trnid : 0;

    $issued = false;
    $dateissued = '';
    $issuedby = '';
    $check_issued = $this->db->select()->from('joborders_details_trails')
        ->where(array('codes' => 'ISSUED', 'joid' => $dataid))
        ->order_by('sysid', 'desc')
        ->get()->row();
    if($check_issued) {
        $check_cancel = $this->db->select()->from('joborders_details_trails')
            ->where(array('codes' => 'CANCELED', 'joid' => $dataid, 'sysid > ' => $check_issued->sysid))
            ->get()->row();
        if( $check_cancel == false ) {
            $issued = true;
            $dateissued = $check_issued->datecreated;
            $issuedby = get_users_info($check_issued->createdby)->lastname;
        }
        $remarks = $check_issued->remarks;
        $dateaccomp = $check_issued->dateaccomp;
        $accompby = get_employee_info($check_issued->accompby)->lastname . ', ' .get_employee_info($check_issued->accompby)->firstname;
        $dateencode = $check_issued->datecreated;
    }

    ?>
    <hr>
    <div class="row">
        <div class="col-md-4">
            <div class="portlet light portlet-fit bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <i class=" icon-layers font-green"></i>
                        <span class="caption-subject font-green bold uppercase"><?php echo $ownername; ?> </span>
                        <div class="caption-desc font-grey-cascade"></div>
                    </div>
                    <div class="actions">
                        <div class="btn-group btn-group-devided">
                            <?php if ($qry->tickettype == 322) {?>
                                <span class="caption-subject font-green bold uppercase"><i class="fa fa-tag"></i> New</span>
                            <?php }else{?>
                                <a href="<?php echo $subjectprofile; ?>" class="btn btn-default"><i class="fa fa-search"></i></a>
                            <?php }?>
                        </div>
                    </div>
                </div>
                <div class="portlet-body ">
                    <ul class="list-group summary column">
                        <li class="list-group-item">
                            <span class="col-md-4 label-name">Service No.</span>
                            <span class="col-md-8 label-default number"><a href="javascript:;" class="label tooltips" data-placement="" title="" style="background: #FFF; color: #000" data-original-title=""><i class="fa "></i> <?php echo ($qry->tickettype == 322) ? $qry_acct->servno : $qry->servicenumber; ?> </a></span>
                        </li>
                        <li class="list-group-item">
                            <span class="col-md-4 label-name">Mtr No.</span>
                            <span class="col-md-8 label-default number"><a href="javascript:;" class="label tooltips" data-placement="" title="" style="background: #FFF; color: #000" data-original-title=""><i class="fa "></i> <?php echo ($qry->tickettype == 322) ? 'N/A' : $qry->mtrno ?> </a></span>
                        </li>
                        <li class="list-group-item">
                            <span class="col-md-4 label-name">Mtr Serial</span>
                            <span class="col-md-8 label-default number"><a href="javascript:;" class="label tooltips" data-placement="" title="" style="background: #FFF; color: #000" data-original-title=""><i class="fa "></i> <?php echo ($qry->tickettype == 322) ? 'N/A' : $qry->mtrserial ?> </a></span>
                        </li>
                        <li class="list-group-item">
                            <span class="col-md-4 label-name">GDLB</span>
                            <span class="col-md-8 label-default number"><a href="javascript:;" class="label tooltips" data-placement="" title="" style="background: #FFF; color: #000" data-original-title=""><i class="fa "></i> <?php echo ($qry->tickettype == 322) ? get_gdlb_name($qry_acct->gdlbid) : $qry->gdlbcode ?> </a></span>
                        </li>
                        <li class="list-group-item">
                            <span class="col-md-4 label-name">Date Contract</span>
                            <span class="col-md-8 label-default number"><a href="javascript:;" class="label tooltips" data-placement="" title="" style="background: #FFF; color: #000" data-original-title=""><i class="fa "></i> <?php echo ($qry->tickettype == 322) ? 'N/A' : $qry->datecontract ?> </a></span>
                        </li>
                        <li class="list-group-item">
                            <span class="col-md-4 label-name">Date Connected</span>
                            <span class="col-md-8 label-default number"><a href="javascript:;" class="label tooltips" data-placement="" title="" style="background: #FFF; color: #000" data-original-title=""><i class="fa "></i> <?php echo ($qry->tickettype == 322) ? 'N/A' : $qry->dateconnected ?> </a></span>
                        </li>
                        <li class="list-group-item">
                            <span class="col-md-4 label-name">Source</span>
                            <span class="col-md-8 label-default number"><a href="javascript:;" class="label tooltips" data-placement="" title="" style="background: #FFF; color: #000" data-original-title=""><i class="fa "></i> <?php echo get_types_label_format($qry->repsource)  ?> </a></span>
                        </li>
                        <li class="list-group-item">
                            <span class="col-md-4 label-name">Requester</span>
                            <span class="col-md-8 label-default number"> <?php echo $ownername ?> </span>
                        </li>
                        <li class="list-group-item">
                            <span class="col-md-4 label-name">District</span>
                            <span class="col-md-8 label-default number"> <?php echo ($qry->tickettype == 322) ? get_district_name($qry_acct->distid) : $qry->distname; ?></span>
                        </li>
                        <li class="list-group-item">
                            <span class="col-md-4 label-name">Specific Address</span>
                            <span class="col-md-8 label-default number"> <?php echo ($qry->tickettype == 322) ? $qry_acct->addrspec : $qry->addrspecific; ?></span>
                        </li>
                        <li class="list-group-item">
                            <span class="col-md-4 label-name">Date Created</span>
                            <span class="col-md-8 label-default number"><a href="javascript:;" class="label tooltips" data-placement="" title="" style="background: #FFF; color: #000" data-original-title=""><i class="fa "></i> <?php echo ($qry->tickettype == 322) ? date_format(date_create($qry_acct->datecreated),"F d, Y h:i:s A") : $qry->datecreated ?> </a></span>
                        </li>
                        <li class="list-group-item">
                            <span class="col-md-4 label-name">Date Updated</span>
                            <span class="col-md-8 label-default number"><a href="javascript:;" class="label tooltips" data-placement="" title="" style="background: #FFF; color: #000" data-original-title=""><i class="fa "></i> <?php echo ($qry->tickettype == 322) ? date_format(date_create($qry_acct->dateupdated),"F d, Y h:i:s A") : $qry->dateupdated ?> </a></span>
                        </li>
                        <li class="list-group-item">
                            <span class="col-md-4 label-name">Status</span>
                            <span class="col-md-8 label-default number"><a href="javascript:;" class="label tooltips" data-placement="" title="" style="background: #FFF; color: #000" data-original-title=""><i class="fa "></i> <?php echo $qry->desc ?> </a></span>
                        </li>
                    </ul>


                </div>
            </div>
        </div>


        <div class="col-md-8">
            <?php
            if($flowid == 18) {
                if($qry->status == 314) {
                    ?>
                    <div class="portlet light portlet-fit bordered">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class=" icon-layers font-green"></i>
                                <span class="caption-subject font-green bold uppercase">Job Order</span>
                                <span class="caption-desc font-grey-cascade"><?php echo str_pad($dataid, 6, '0', STR_PAD_LEFT); ?></span>
                            </div>
                            <div class="tools">
                                <button id="btn_print_order" data-id="<?php echo $dataid;?>" class="btn btn-default pull-right"><i class="fa fa-print"></i> Print Order</button>
                            </div>
                        </div>
                        <div class="portlet-body">
                            <h3><i class="fa fa-times font-red-haze"></i> Account has been disconnected!</h3>

                        </div>
                    </div>
                    <?php
                }else {
                    ?>
                    <div class="portlet light portlet-fit bordered">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class=" icon-layers font-green"></i>
                                <span class="caption-subject font-green bold uppercase">Job Order</span>
                                <span class="caption-desc font-grey-cascade"><?php echo str_pad($dataid, 6, '0', STR_PAD_LEFT); ?></span>
                            </div>
                        </div>
                        <div class="portlet-body">
                            <form id="frm_utility_accomplishment_fdo" method="post"
                                  action="<?php echo base_url('jo/accomplishfdo'); ?>">
                                <input type="hidden" name="joid" value="<?php echo $dataid; ?>"/>
                                <input type="hidden" name="trailid" class="form-control"
                                       value="<?php echo $trailid; ?>"/>
                                <div class="form-group ">
                                    <div style="width: 30%; display: inline-block; vertical-align: top">
                                        <label class="small">
                                            Accomplished By
                                        </label>
                                        <input required id="empid" class="form-control" name="empid"
                                               placeholder="Lastname.. "/>
                                    </div>
                                    <div style="width: 20%; display: inline-block; vertical-align: top">
                                        <label class="small">
                                            Accomplished Date
                                        </label>
                                        <input required type="date" class="form-control"
                                               placeholder="Date accomplished.. " name="date" />
                                    </div>
                                    <div style="width: 25%; display: inline-block; vertical-align: top">
                                        <label class="small">
                                            Remarks
                                        </label>
                                        <input class="form-control" placeholder="Remarks.. " name="remarks"/>
                                    </div>
                                    <div style="width: 5%; display: inline-block; vertical-align: top">
                                        <label class="small">
                                            Seq#
                                        </label>
                                        <input class="form-control" placeholder="Seq.. " name="sequence"/>
                                    </div>
                                    <div style="width: 18%; display: inline-block; vertical-align: top;">

                                        <label class="small">
                                            Old Reading
                                        </label>
                                        <input class="form-control" placeholder="reading" name="reading"/>
                                    </div>
                                </div>
                                <hr>

                                <div class="btn-group">
                                    <button type="submit" class="btn btn-primary"><i class="fa fa-save fa-fw"></i>
                                        Accomplish
                                    </button>
                                </div>
                            </form>

                        </div>
                    </div>
                    <?php
                }
                ?>

                <?php
            } else {

                if ($issued == true) {


                    $qry_asset_owner = $this->db->select('am.sysid, am.labels, am.serials, am.brand')
                        ->from('assets_main_owner_history moh')
                        ->join('assets_main AS am', 'am.sysid = moh.assetid')
                        ->where(array(
                                'moh.ownerid' => $ownerid,
                                'moh.ownertype' => 3,
                                'moh.status' => 1)
                        )
                        ->get()->row();

                    $new_asset_type = '';
                    $new_asset_volts = '';
                    $new_asset_amps = '';
                    $new_asset_brand = '';
                    $new_asset_pecoseal = '';
                    $new_asset_ercseal = '';
                    $new_asset_reading = '';
                    if($qry_asset_owner) {
                        $new_asset_info = json_decode($this->model_search->get_meter_info($qry_asset_owner->sysid));
                        $new_asset_type = ($new_asset_info) ? $new_asset_info->type : 'N/A';
                        $new_asset_amps = ($new_asset_info) ? $new_asset_info->ampere : 'N/A';
                        $new_asset_volts = ($new_asset_info) ? $new_asset_info->volts : 'N/A';
                        $new_asset_brand = ($new_asset_info) ? $new_asset_info->brand : 'N/A';
                        $new_asset_pecoseal = ($new_asset_info) ? $new_asset_info->pecoseal : 'N/A';
                        $new_asset_ercseal = ($new_asset_info) ? $new_asset_info->ercseal : 'N/A';
                        $new_asset_reading = ($new_asset_info) ? $new_asset_info->reading : 'N/A';
                    }

                    $new_asset_no = ($qry_asset_owner) ? $qry_asset_owner->labels : 'N/A';
                    $new_asset_serial = ($qry_asset_owner) ? $qry_asset_owner->serials : 'N/A';

                    ?>

                    <div class="portlet light bordered">
                        <div class="portlet-body">

                            <div class="row">
                                <div class="col-md-6">
                                    <h4 class=""><i class="fa fa-link font-green-haze"></i> Asset Installation</h4>
                                    <ul class="list-group summary column no-border list-group-sm">
                                        <li class="list-group">
                                            <span class="col-md-5 label-name">Meter#.</span>
                                            <span class="col-md-7 label-default"><?php echo $new_asset_no; ?></span>
                                        </li>
                                        <li class="list-group">
                                            <span class="col-md-5 label-name">Serial</span>
                                            <span class="col-md-7 label-default"><?php echo $new_asset_serial; ?></span>
                                        </li>
                                        <li class="list-group">
                                            <span class="col-md-5 label-name">Type</span>
                                            <span class="col-md-7 label-default"><?php echo $new_asset_type; ?></span>
                                        </li>
                                        <li class="list-group">
                                            <span class="col-md-5 label-name">Volts</span>
                                            <span class="col-md-7 label-default"><?php echo $new_asset_volts; ?></span>
                                        </li>
                                        <li class="list-group">
                                            <span class="col-md-5 label-name">Amps</span>
                                            <span class="col-md-7 label-default"><?php echo $new_asset_amps; ?></span>
                                        </li>
                                        <li class="list-group">
                                            <span class="col-md-5 label-name">Brand</span>
                                            <span class="col-md-7 label-default"><?php echo $new_asset_brand; ?></span>
                                        </li>

                                        <li class="list-group">
                                            <span class="col-md-5 label-name">PECO Seal</span>
                                            <span class="col-md-7 label-default"><?php echo $new_asset_pecoseal; ?></span>
                                        </li>

                                        <li class="list-group">
                                            <span class="col-md-5 label-name">ERC Seal</span>
                                            <span class="col-md-7 label-default"><?php echo $new_asset_ercseal; ?></span>
                                        </li>

                                        <li class="list-group">
                                            <span class="col-md-5 label-name">Reading</span>
                                            <span class="col-md-7 label-default"><?php echo $new_asset_reading; ?></span>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-md-6">

                                    <h4 class=""><i class="fa fa-check font-green-haze"></i> Accomplishment</h4>
                                    <div class="well">

                                        <ul class="list-group summary column no-border list-group-sm">
                                            <li class="list-group">
                                                <span class="col-md-5 label-name">Date Accomplished</span>
                                                <span class="col-md-7 label-default"><?php echo $dateaccomp; ?></span>
                                            </li>

                                            <li class="list-group">
                                                <span class="col-md-5 label-name">Accomplished By</span>
                                                <span class="col-md-7 label-default"><?php echo $accompby; ?></span>
                                            </li>

                                            <li class="list-group">
                                                <span class="col-md-5 label-name">Date Encoded</span>
                                                <span class="col-md-7 label-default"><?php echo $dateencode; ?></span>
                                            </li>

                                            <li class="list-group">
                                                <span class="col-md-5 label-name">Remarks</span>
                                                <span class="col-md-7 label-default"><?php echo $remarks; ?></span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php

                } else {
                    ?>


                    <form id="frm_submit_mtr_assignment" action="<?php echo base_url(); ?>jo/submitmtrassignment"
                          method="post">

                        <div class="portlet light portlet-fit bordered">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class=" icon-layers font-green"></i>
                                    <span class="caption-subject font-green bold uppercase">Issuance</span>
                                    <span class="caption-desc font-grey-cascade">issue a meter</span>
                                </div>
                                <div class="pull-right">

                                    <button type="submit" class="btn btn-default"><i class="fa fa-save fa-fw"></i> Save
                                    </button>
                                </div>
                            </div>
                            <div class="portlet-body">
                                <?php
                                $qry_asset_owner = $this->db->select('am.sysid, am.labels, am.serials, am.brand')
                                    ->from('assets_main_owner_history moh')
                                    ->join('assets_main AS am', 'am.sysid = moh.assetid')
                                    ->where(array(
                                            'moh.ownerid' => $ownerid,
                                            'moh.ownertype' => 3,
                                            'moh.status' => 300)
                                    )
                                    ->get()->row();

                                $new_asset_no = ($qry_asset_owner) ? $qry_asset_owner->labels : 'N/A';
                                $new_asset_serial = ($qry_asset_owner) ? $qry_asset_owner->serials : 'N/A';
                                $new_asset_brand = ($qry_asset_owner) ? (($qry_asset_owner->brand == '') ? 'N/A' : $qry_asset_owner->brand) : 'N/A';

                                if ($qry->status == 314) {
                                    echo '<h3><i class="fa fa-check font-green-haze"></i> Transaction has been Accomplished!</h3>';
                                } else {
                                    $get_trail_level = $this->db->select('levels')
                                        ->from('prime_transaction_flow_main_stages')
                                        ->where(array('sysid' => $stageid))
                                        ->get()->row();


                                    $get_trail_down = $this->db->select('sysid')
                                        ->from('prime_transaction_flow_main_stages')
                                        ->where(array('flowid' => $flowid, 'levels < ' => $get_trail_level->levels))
                                        ->order_by('levels', 'desc')
                                        ->get()->row();

                                    $trnmainid = $this->db->select()->from('transaction_request_main')->where(array('sysid' => $trnid))->get()->row();

                                    if ($trnmainid) {
                                        $trn_title = $trnmainid->descs;
                                    }

                                    ?>


                                    <input type="hidden" name="trailid" value="<?php echo $trailid; ?>"/>
                                    <input type="hidden" name="acctid" value="<?php echo $ownerid; ?>"/>
                                    <input type="hidden" name="joid" value="<?php echo $dataid; ?>"/>
                                    <input type="hidden" name="flowid" id="flowid" value="<?php echo $flowid; ?>"/>
                                    <input type="hidden" name="trnid" id="trnid" value="<?php echo $trnid; ?>"/>
                                    <input type="hidden" name="stageid" id="stageid" value="<?php echo $stageid; ?>"/>
                                    <input type="hidden" name="dataid" id="dataid" value="<?php echo $dataid; ?>"/>
                                    <input type="hidden" name="moduleid" id="moduleid" value="<?php echo $origin; ?>"/>
                                    <input type="hidden" name="routeto" id="routeto" value="<?php echo $get_trail_down->sysid; ?>"/>
                                    <input type="hidden" name="trntitle" id="trntitle" value="<?php echo $trn_title ?>"/>
                                    <input type="hidden" name="status" id="status" value="0"/>

                                    <div class="row">
                                        <div class="col-md-7">
                                            <h4 class="font-green-haze">Asset Installation</h4>
                                            <div data-toggle="metersearchform"><i
                                                        class="fa fa-spinner fa-spin fa-pulse"></i> Initializing form...
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <h4 class="font-green-haze">Accomplishment</h4>
                                            <div class="form-group">
                                                <label>Old Reading</label>
                                                <input type="text" name="oldreading" class="form-control" placeholder="Enter Old Reading"/>
                                            </div>
                                            <div class="form-group">
                                                <textarea class="form-control" value="" name="remarks"
                                                          placeholder="Remarks.."></textarea>
                                            </div>

                                            <div class="form-group ">
                                                <label class="small">
                                                    Accomplished By
                                                </label>
                                                <input required id="empid" class="form-control" name="empid"
                                                       placeholder="Lastname.. "/>
                                            </div>
                                            <div class="form-group ">
                                                <label class="small">
                                                    Accomplished Date
                                                </label>
                                                <input required type="date" class="form-control"
                                                       placeholder="Date accomplished.. " name="date"/>
                                            </div>

                                        </div>
                                    </div>

                                    <!--
                            <form id="frm_utility_accomplishment" method="post"
                                  action="<?php echo base_url('jo/accomplishtrans'); ?>">
                                <input type="hidden" name="joid" value="<?php echo $dataid; ?>"/>
                                <input type="hidden" name="trailid" class="form-control"
                                       value="<?php echo $trailid; ?>"/>
                                <input type="hidden" name="assetid"
                                       value="<?php echo $qry_asset_owner->sysid; ?>"/>
                                <hr>

                                <hr>

                                <div class="btn-group">
                                    <button type="submit" class="btn btn-primary"><i
                                                class="fa fa-save fa-fw"></i> Accomplish
                                    </button>
                                    <button data-type="1" data-ownerid="<?php echo $ownerid; ?>"
                                            data-joid="<?php echo $dataid; ?>"
                                            data-trialid="<?php echo $trailid; ?>" data class="btn btn-danger"
                                            id="btn_cancel_issuance"><i class="fa fa-refresh"></i> Cancel
                                        Issuance
                                    </button>
                                </div>
                            </form>
                            -->
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                    </form>
                <?php }
            }
            ?>


            <div class="portlet light portlet-fit bordered table">
                <div class="portlet-title tabbable-line">
                    <div class="caption">
                        <i class=" icon-layers font-green"></i>
                        <span class="caption-subject font-green bold uppercase">Transaction History</span>


                    </div>

                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a href="#trails" data-toggle="tab" aria-expanded="true"> Trails </a>
                        </li>
                        <li class="">
                            <a href="#logs" data-toggle="tab" aria-expanded="false"> Logs</a>
                        </li>
                    </ul>

                </div>
                <div class="portlet-body tab-content">
                    <div class="tab-pane fade in active" id="trails">

                        <table class="table table-bordered table-condensed" id="jotrntrailtbl">
                            <thead>
                            <th></th>
                            <th>Descriptions</th>
                            <th>Date Created</th>
                            <th>Date Updated</th>
                            <th>Created by</th>
                            <th>Updated by</th>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>


                    <div class="tab-pane fade in" id="logs">
                        <table class="table table-bordered table-condensed" id="jotrnlogstbl">
                            <thead>
                            <th></th>
                            <th>Descriptions</th>
                            <th>Date Created</th>
                            <th>Created by</th>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <hr>
<?php }else{
    page_data_notfound('Job Order not found!');
} ?>



<!-- BEGIN PAGE LEVEL PLUGINS -->
<script src="<?php echo base_url() ?>assets/global/plugins/moment.min.js" type="text/javascript"></script>
<script src="<?php echo base_url() ?>assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js" type="text/javascript"></script>
<script src="<?php echo base_url() ?>assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
<script src="<?php echo base_url() ?>assets/global/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js" type="text/javascript"></script>
<script src="<?php echo base_url() ?>assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
<script src="<?php echo base_url() ?>assets/global/plugins/clockface/js/clockface.js" type="text/javascript"></script>
<!-- END PAGE LEVEL PLUGINS -->
<script src="<?php echo base_url() ?>assets/pages/jo/main.js"></script>

<script>
    JO.init();
    JO.mistrn(<?php echo $trnid; ?>, <?php echo $dataid; ?>);
</script>
