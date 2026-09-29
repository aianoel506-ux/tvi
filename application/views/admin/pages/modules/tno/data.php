<?php

$qry = get_joborder_info($dataid);
//print_r($qry);

if($qry) {

    $apply = get_application_details($qry->acctid)->info;
    $acct_name = '';

    $ownerid = $qry->acctid;

    $middlename = (isset($apply->middlename[0])) ? $apply->middlename[0].'.' : '';
    $ownername = $apply->lastname.', '.$apply->firstname.' '.$middlename;

    $subjectprofile = base_url('peco/customer/' . $ownerid);

    $conntype = get_acct_type($apply->connid);
    $ownertype = get_acct_type($apply->owntypeid);
    $rateclass = get_rateclass_name($apply->rateclassid);
    $landtype = get_acct_type($apply->loctypeid);

    $check_gdr = check_acct_gdr($qry->acctid);

    ?>

    <hr>
    <div class="row">
        <div class="col-md-4">
            <div class="portlet light portlet-fit bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <i class=" icon-layers font-green"></i>
                        <span class="caption-subject font-green bold uppercase"></i> <?php echo $ownername; ?> </span>
                        <div class="caption-desc font-grey-cascade"></div>
                    </div>
                    <div class="actions">
                        <div class="btn-group btn-group-devided">
                            <button id="btn_print_order" data-id="<?php echo $dataid; ?>" class="btn btn-default"><i class="fa fa-print"></i> Print Order</button>

                            <a target="_blank" href="<?php echo $subjectprofile; ?>" class="btn btn-default"><i class="fa fa-search"></i></a>
                        </div>
                    </div>
                </div>
                <div class="portlet-body ">
                    <ul class="list-group summary column">
                        <li class="list-group-item">
                            <span class="col-md-4 label-name">Service No.</span>
                            <span class="col-md-8 label-default number"><a href="javascript:;" class="label tooltips" data-placement="" title="" style="background: #FFF; color: #000" data-original-title=""><i class="fa "></i> <?php echo $apply->servno ?> </a></span>
                        </li>
                        <li class="list-group-item">
                            <span class="col-md-4 label-name">GDLB</span>
                            <span class="col-md-8 label-default number"><a href="javascript:;" class="label tooltips" data-placement="" title="" style="background: #FFF; color: #000" data-original-title=""><i class="fa "></i> <?php echo get_gdlb_name($apply->gdlbid);?> </a></span>
                        </li>
                        <li class="list-group-item">
                            <span class="col-md-4 label-name">Source</span>
                            <span class="col-md-8 label-default number"><a href="javascript:;" class="label tooltips" data-placement="" title="" style="background: #FFF; color: #000" data-original-title=""><i class="fa "></i> <?php echo get_types_label_format($qry->repsource, false, false)  ?> </a></span>
                        </li>
                        <li class="list-group-item">
                            <span class="col-md-4 label-name">Requester</span>
                            <span class="col-md-8 label-default number"> <?php echo $ownername; ?> </span>
                        </li>
                        <li class="list-group-item">
                            <span class="col-md-4 label-name">District</span>
                            <span class="col-md-8 label-default number"> <?php echo get_district_name($apply->distid); ?></span>
                        </li>
                        <li class="list-group-item">
                            <span class="col-md-4 label-name">Specific Address</span>
                            <span class="col-md-8 label-default number"> <?php echo $apply->addrspec; ?></span>
                        </li>
                        <li class="list-group-item">
                            <span class="col-md-4 label-name">Date Created</span>
                            <span class="col-md-8 label-default number"><a href="javascript:;" class="label tooltips" data-placement="" title="" style="background: #FFF; color: #000" data-original-title=""><i class="fa "></i> <?php echo date_format(date_create($apply->datecreated),"F d, Y h:i:s A");?> </a></span>
                        </li>
                        <li class="list-group-item">
                            <span class="col-md-4 label-name">Date Updated</span>
                            <span class="col-md-8 label-default number"><a href="javascript:;" class="label tooltips" data-placement="" title="" style="background: #FFF; color: #000" data-original-title=""><i class="fa "></i> <?php echo date_format(date_create($apply->dateupdated),"F d, Y h:i:s A");?> </a></span>
                        </li>
                        <li class="list-group-item">
                            <span class="col-md-4 label-name">Status</span>
                            <span class="col-md-8 label-default number"><a href="javascript:;" class="label tooltips" data-placement="" title="" style="background: #FFF; color: #000" data-original-title=""><i class="fa "></i> <?php echo $qry->desc ?> </a></span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="row">
                <div class="col-md-12">
                    <div class="portlet light">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-edit"></i>
                                <span class="caption-subject font-green-sharp bold uppercase">Account Details</span>
                                <span class="caption-helper"></span>
                            </div>
                        </div>
                        <span class="portlet-body">
                            <ul class="list-group summary row no-border">
                                <li class="list-group-item">
                                    <span class="label-name col-md-5">Rate </span>
                                    <span class="label-default col-md-7"><?php echo $rateclass; ?></span>
                                </li>

                                <li class="list-group-item">
                                    <span class="label-name col-md-5">Connection </span>
                                    <span class="label-default col-md-7"><?php echo $conntype; ?></span>
                                </li>

                                <li class="list-group-item">
                                    <span class="label-name col-md-5">Ownership </span>
                                    <span class="label-default col-md-7"><?php echo $ownertype; ?></span>
                                </li>

                                <li class="list-group-item">
                                    <span class="label-name col-md-5">Land </span>
                                    <span class="label-default col-md-7"><?php echo $landtype; ?></span>
                                </li>
                            </ul>
                    </div>

                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="portlet light">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-edit"></i>
                                <span class="caption-subject font-green-sharp bold uppercase">Assessment</span>
                                <span class="caption-helper"></span>
                            </div>

                        </div>
                        <div class="portlet-body">

                            <div class="row">
                                <div class="col-md-12">
                                    <ul class="list-group summary column no-border">
                                        <li class="list-group-item">
                                            <span class="label label-name col-md-7">Rate Class</span>
                                            <span class="label label-default col-md-5 number" id="">
                                                    <?php echo ($check_gdr) ? $check_gdr->rateclassname : 'N/A'; ?>
                                                </span>
                                        </li>
                                        <li class="list-group-item">
                                            <span class="label label-name col-md-7">Rate</span>
                                            <span class="label label-default col-md-5 text-danger number" id="">
                                                     <?php echo ($check_gdr) ? $check_gdr->rates : 'N/A'; ?>
                                                </span>
                                        </li>
                                        <li class="list-group-item">
                                            <span class="label label-name col-md-7">Demand</span>
                                            <span class="label label-default col-md-5 number" id="">
                                                    <?php echo ($check_gdr) ? $check_gdr->demand : 'N/A'; ?>
                                                </span>
                                        </li>
                                        <li class="list-group-item">
                                            <span class="label label-name col-md-7">Daily Operations(hours/day)</span>
                                            <span class="label label-default col-md-5 number" id="">
                                                    <?php echo ($check_gdr) ? $check_gdr->dailyop : 'N/A'; ?>
                                                </span>
                                        </li>
                                        <li class="list-group-item">
                                            <span class="label label-name col-md-7">Monthly Operations(days/month)</span>
                                            <span class="label label-default col-md-5 number" id="">
                                                    <?php echo ($check_gdr) ? $check_gdr->monthlyop : 'N/A'; ?>
                                                </span>
                                        </li>
                                        <li class="list-group-item">
                                            <span class="label label-name col-md-7">Total Load (Watts)</span>
                                            <span class="label label-default col-md-5 number" id="">
                                                    <?php echo ($check_gdr) ? number_format($check_gdr->totalwatt) : 'N/A'; ?>
                                                </span>
                                        </li>
                                        <li class="list-group-item" style="border-top: 1px #eee solid !important;">
                                            <span class="label label-name col-md-7">GDR Cost</span>
                                            <span class="label label-default col-md-5 number" style="color: red; font-size: 15px !important;" id="">
                                                    <?php echo ($check_gdr) ? number_format($check_gdr->totalcost, 2) : 'N/A'; ?>
                                                </span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="col-md-4">
            <div class="portlet light">
                Load Inspection data/history goes here.
            </div>
        </div>
    </div>
    <script src="<?php echo base_url() ?>assets/pages/jo/main.js"></script>

    <script>
        JO.init();
        JO.mistrn(<?php echo $trnid; ?>, <?php echo $dataid; ?>);
    </script>

    <?php
}else{
    page_data_notfound('Job Order not found!');
}
?>



