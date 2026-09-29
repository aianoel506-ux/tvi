<?php
/**
 * Created by PhpStorm.
 * User: DUDEZKIE
 * Date: 6/24/2019
 * Time: 11:22 AM
 */

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
                            <span class="col-md-4 label-name">Source</span>
                            <span class="col-md-8 label-default number"><a href="javascript:;" class="label tooltips" data-placement="" title="" style="background: #FFF; color: #000" data-original-title=""><i class="fa "></i> <?php echo get_types_label_format($qry->repsource, false, false)  ?> </a></span>
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
            <div class="portlet light portlet-fit bordered table">
                <div class="portlet-title">
                    <div class="caption">
                        <i class=" icon-layers font-green"></i>
                        <span class="caption-subject font-green bold uppercase"></i> Reading History </span>
                        <div class="caption-desc font-grey-cascade"></div>
                    </div>
                    <div class="actions">
                        <div class="btn-group btn-group-devided">
                        </div>
                    </div>
                </div>
                <div class="portlet-body">
                    <div data-toggle="readingdata"></div>
                </div>
            </div>

        </div>
    </div>
    <script src="<?php echo base_url() ?>assets/pages/jo/main.js"></script>

    <script>
        JO.init();
        JO.mistrn(<?php echo $trnid; ?>, <?php echo $dataid; ?>);
        PECO.initCustomerReadingData(<?php echo $ownerid;?>);
    </script>

    <?php
}else{
    page_data_notfound('Job Order not found!');
}
?>



