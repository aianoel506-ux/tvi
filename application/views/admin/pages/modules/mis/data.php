<?php

$qry = get_joborder_info($dataid);

if($qry) {
    $acct_name = '';

    $ownerid = $qry->acctid;

    if($qry->types == 5) {
        $qry_acctname = $this->db->select()->from('customer_accounts_name_legacy')
            ->where(array('sysid' => $qry->ownerid))
            ->get()->row();
        $ownername = ($qry_acctname) ? $qry_acctname->name : 'N/A';
    }else{

        $qry_acctname = $this->db->select()->from('person')
            ->where(array('sysid' => $qry->ownerid))
            ->get()->row();
        $ownername = ($qry_acctname) ? $qry_acctname->lastname.', '.$qry_acctname->firstname : 'N/A';
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
        if($check_cancel==false) {
            $issued = true;
            $dateissued = $check_issued->datecreated;
            $issuedby = get_users_info($check_issued->createdby)->lastname;
        }
    }

    ?>

    <div class="row">
        <div class="col-md-4">
            <div class="portlet light portlet-fit bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <i class=" icon-layers font-green"></i>
                        <span class="caption-subject font-green bold uppercase">Current Account Details</span>
                        <div class="caption-desc font-grey-cascade"></div>
                    </div>
                    <div class="actions">
                        <div class="btn-group btn-group-devided">
                            <a target="_blank" href="<?php echo $subjectprofile; ?>" class="btn btn-default"><i class="fa fa-search"></i></a>

                        </div>
                    </div>
                </div>
                <div class="portlet-body ">
                    <ul class="list-group summary column">
                        <li class="list-group-item">
                            <span class="col-md-4 label-name">Service No.</span>
                            <span class="col-md-8 label-default number"><a href="javascript:;" class="label tooltips" data-placement="" title="" style="background: #FFF; color: #000" data-original-title=""><i class="fa "></i> <?php echo $qry->servicenumber ?> </a></span>
                        </li>
                        <li class="list-group-item">
                            <span class="col-md-4 label-name">Mtr No.</span>
                            <span class="col-md-8 label-default number"><a href="javascript:;" class="label tooltips" data-placement="" title="" style="background: #FFF; color: #000" data-original-title=""><i class="fa "></i> <?php echo $qry->mtrno ?> </a></span>
                        </li>
                        <li class="list-group-item">
                            <span class="col-md-4 label-name">Mtr Serial</span>
                            <span class="col-md-8 label-default number"><a href="javascript:;" class="label tooltips" data-placement="" title="" style="background: #FFF; color: #000" data-original-title=""><i class="fa "></i> <?php echo $qry->mtrserial ?> </a></span>
                        </li>
                        <li class="list-group-item">
                            <span class="col-md-4 label-name">GDLB</span>
                            <span class="col-md-8 label-default number"><a href="javascript:;" class="label tooltips" data-placement="" title="" style="background: #FFF; color: #000" data-original-title=""><i class="fa "></i> <?php echo $qry->gdlbcode ?> </a></span>
                        </li>
                        <li class="list-group-item">
                            <span class="col-md-4 label-name">Date Contract</span>
                            <span class="col-md-8 label-default number"><a href="javascript:;" class="label tooltips" data-placement="" title="" style="background: #FFF; color: #000" data-original-title=""><i class="fa "></i> <?php echo $qry->datecontract ?> </a></span>
                        </li>
                        <li class="list-group-item">
                            <span class="col-md-4 label-name">Date Connected</span>
                            <span class="col-md-8 label-default number"><a href="javascript:;" class="label tooltips" data-placement="" title="" style="background: #FFF; color: #000" data-original-title=""><i class="fa "></i> <?php echo $qry->dateconnected ?> </a></span>
                        </li>

                        <li class="list-group-item">
                            <span class="col-md-4 label-name">Owner</span>
                            <span class="col-md-8 label-default number"><a href="javascript:;" class="label tooltips" data-placement="" title="" style="background: #FFF; color: #000" data-original-title=""><i class="fa "></i> <?php echo $ownername; ?> </a></span>
                        </li>
                        <li class="list-group-item">
                            <span class="col-md-4 label-name">Source</span>
                            <span class="col-md-8 label-default number"><a href="javascript:;" class="label tooltips" data-placement="" title="" style="background: #FFF; color: #000" data-original-title=""><i class="fa "></i> <?php echo get_types_label_format($qry->repsource)  ?> </a></span>
                        </li>
                        <li class="list-group-item">
                            <span class="col-md-4 label-name">Requester</span>
                            <span class="col-md-8 label-default number"> <?php echo get_person_info($qry->complainants)->info->lastname.', '.get_person_info($qry->complainants)->info->firstname; ?> </span>
                        </li>
                        <li class="list-group-item">
                            <span class="col-md-4 label-name">District</span>
                            <span class="col-md-8 label-default number"> <?php echo $qry->distname; ?></span>
                        </li>
                        <li class="list-group-item">
                            <span class="col-md-4 label-name">Specific Address</span>
                            <span class="col-md-8 label-default number"> <?php echo $qry->addrspecific; ?></span>
                        </li>
                        <li class="list-group-item">
                            <span class="col-md-4 label-name">Date Created</span>
                            <span class="col-md-8 label-default number"><a href="javascript:;" class="label tooltips" data-placement="" title="" style="background: #FFF; color: #000" data-original-title=""><i class="fa "></i> <?php echo $qry->datecreated ?> </a></span>
                        </li>
                        <li class="list-group-item">
                            <span class="col-md-4 label-name">Date Updated</span>
                            <span class="col-md-8 label-default number"><a href="javascript:;" class="label tooltips" data-placement="" title="" style="background: #FFF; color: #000" data-original-title=""><i class="fa "></i> <?php echo $qry->dateupdated ?> </a></span>
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
            <div class="portlet light portlet-fit bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <i class=" icon-layers font-green"></i>
                        <span class="caption-subject font-green bold uppercase">Issuance</span>
                        <span class="caption-desc font-grey-cascade">issue a meter</span>
                    </div>
                    <div class="tools">
                        <button id="btn_print_order" data-id="<?php echo $dataid; ?>" class="btn btn-default"><i class="fa fa-print"></i> Print Order</button>

                        <?php
                        if($issued==true) {
                        ?>
                        <button style="margin-left: 10px;" data-type="0" data-ownerid="<?php echo $ownerid;?>" data-joid="<?php echo $dataid;?>" data-trialid="<?php echo $trailid;?>" data class="btn red-mint btn-outline sbold  pull-right" id="btn_cancel_issuance"><i class="fa fa-refresh"></i> Cancel Issuance</button>

                        <?php } ?>
                    </div>
                </div>
                <div class="portlet-body">

                    <?php


                    $get_trail_level = $this->db->select('levels')
                        ->from('prime_transaction_flow_main_stages')
                        ->where(array('sysid' => $stageid))
                        ->get()->row();


                    $get_trail_down = $this->db->select('sysid')
                        ->from('prime_transaction_flow_main_stages')
                        ->where(array('flowid' => $flowid, 'levels > ' => $get_trail_level->levels))
                        ->order_by('levels', 'desc')
                        ->get()->row();

                    $trnmainid = $this->db->select()->from('transaction_request_main')->where(array('sysid' => $trnid))->get()->row();

                    if($trnmainid) {
                        $trn_title = $trnmainid->descs;
                    }

                    ?>

                    <input type="hidden" name="flowid" id="flowid" value="<?php echo $flowid; ?>" />
                    <input type="hidden" name="trnid" id="trnid" value="<?php echo $trnid; ?>" />
                    <input type="hidden" name="stageid" id="stageid" value="<?php echo $stageid; ?>" />
                    <input type="hidden" name="dataid" id="dataid" value="<?php echo $dataid; ?>" />
                    <input type="hidden" name="moduleid" id="moduleid" value="<?php echo $origin; ?>" />
                    <input type="hidden" name="routeto" id="routeto" value="<?php echo $get_trail_down->sysid; ?>" />
                    <input type="hidden" name="trntitle" id="trntitle" value="<?php echo $trn_title ?>" />
                    <input type="hidden" name="status" id="status" value="0" />

                    <?php
                    if($issued==true) {
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
                        $new_asset_brand = ($qry_asset_owner) ? (($qry_asset_owner->brand=='') ? 'N/A' : $qry_asset_owner->brand) : 'N/A';


                        if($qry->status == 314) {
                            echo '<h3><i class="fa fa-check font-green-haze"></i> Transaction has been Accomplished!</h3>';
                        }else {

                            ?>
                            <div class="row">

                                <div class="col-md-12">
                                    <?php


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

                                    <input type="hidden" name="flowid" id="flowid" value="<?php echo $flowid; ?>"/>
                                    <input type="hidden" name="trnid" id="trnid" value="<?php echo $trnid; ?>"/>
                                    <input type="hidden" name="stageid" id="stageid" value="<?php echo $stageid; ?>"/>
                                    <input type="hidden" name="dataid" id="dataid" value="<?php echo $dataid; ?>"/>
                                    <input type="hidden" name="moduleid" id="moduleid" value="<?php echo $origin; ?>"/>
                                    <input type="hidden" name="routeto" id="routeto"
                                           value="<?php echo $get_trail_down->sysid; ?>"/>
                                    <input type="hidden" name="trntitle" id="trntitle"
                                           value="<?php echo $trn_title ?>"/>
                                    <input type="hidden" name="status" id="status" value="0"/>

                                    <h4 style="margin-left: 10px;"><i class="fa fa-tag"></i> New Assaet Issuance

                                        <span class="pull-right">
                                        <i class="fa fa-check"></i> Issued&nbsp; on <?php echo $dateissued; ?>
                                            , by <?php echo $issuedby; ?>

                                    </span>

                                    </h4>
                                    <hr style="margin: 0px 5px;">
                                    <ul class="list-group summary column table">
                                        <li class="list-group">
                                            <span class="col-md-5 label-name">Meter#.</span>
                                            <span class="col-md-7 label-default"><?php echo $new_asset_no; ?></span>
                                        </li>
                                        <li class="list-group">
                                            <span class="col-md-5 label-name">Serial</span>
                                            <span class="col-md-7 label-default"><?php echo $new_asset_serial; ?></span>
                                        </li>
                                        <li class="list-group">
                                            <span class="col-md-5 label-name">Brand</span>
                                            <span class="col-md-7 label-default"><?php echo $new_asset_brand; ?></span>
                                        </li>
                                        <li class="list-group">
                                            <span class="col-md-5 label-name">Type</span>
                                            <span class="col-md-7 label-default">N/A</span>
                                        </li>
                                        <li class="list-group">
                                            <span class="col-md-5 label-name">Volts</span>
                                            <span class="col-md-7 label-default">N/A</span>
                                        </li>
                                        <li class="list-group">
                                            <span class="col-md-5 label-name">Amps</span>
                                            <span class="col-md-7 label-default">N/A</span>
                                        </li>
                                    </ul>

                                    <hr style="margin: 0px 0px;">
                                    <ul class="list-group summary column table">
                                        <li class="list-group">
                                            <span class="col-md-5 label-name">PECO Seal</span>
                                            <span class="col-md-7 label-default"><?php echo $new_asset_no; ?></span>
                                        </li>

                                        <li class="list-group">
                                            <span class="col-md-5 label-name">ERC Seal</span>
                                            <span class="col-md-7 label-default"><?php echo $new_asset_serial; ?></span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <?php
                        }
                    }else{
                    ?>
                    <form id="frm_submit_mtr_assignment" action="<?php echo base_url(); ?>jo/submitmtrassignment" method="post">
                        <input type="hidden" name="trailid" class="form-control" value="<?php echo $trailid; ?>" />
                        <input type="hidden" name="acctid" class="form-control" value="<?php echo $ownerid; ?>" />
                        <input type="hidden" name="joid" class="form-control" value="<?php echo $dataid; ?>" />
                        <div class="row">
                            <div class="col-md-7"><div data-toggle="metersearchform"></div></div>
                            <div class="col-md-5">

                                <div class="form-group">
                                    <label>Remarks:</label>
                                    <textarea class="form-control" value="" id="remarks" name="remarks" placeholder="Remarks.." ></textarea>
                                </div>
                                <div class="form-group">
                                    <button class="btn btn-default"><i class="fa fa-save fa-fw"></i> Save</button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <?php  } ?>
                </div>
            </div>
        </div>


        <div class="col-md-8">
            <div class="portlet light portlet-fit bordered table">
                <div class="portlet-title tabbable-line">
                    <div class="caption">
                        <i class=" icon-layers font-green"></i>
                        <span class="caption-subject font-green bold uppercase">Transaction</span>


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


<?php }else{
    page_data_notfound('Job Order not found!');
} ?>

<script src="<?php echo base_url() ?>assets/pages/jo/main.js"></script>

<script>
    JO.init();
    JO.mistrn(<?php echo $trnid; ?>, <?php echo $dataid; ?>);
</script>
