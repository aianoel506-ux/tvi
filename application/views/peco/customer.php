<?php
/**
 * Created by PhpStorm.
 * User: DUDEZKIE
 * Date: 6/26/2019
 * Time: 1:45 PM
 */

$qry = $acct_arr;

$acct_name = '';

$ownerid = $qry->ownerid;

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

?>

<div class="page-content-wrapper">
    <div class="page-bar">
        <ul style="width: 98% !important;" class="page-breadcrumb">
            <li><a href="/"><i class="fa fa-home"></i> Home</a></li>
            <li><i class="fa fa-angle-right"></i> <a href="#">PECO</a></li>
            <li><i class="fa fa-angle-right"></i> <a href="<?php echo base_url('peco/customer/'.$acctid); ?>"><b><?php echo $qry->servicenumber ?></b> - <?php echo $ownername; ?></a></li>
        </ul>
    </div>
    <!-- BEGIN HEADER INNER -->
    <div class="page-content  animated fadeInUp fast">

        <div class="col-md-2">
            <div align="center" class="asset-pic">
                <img style="width: 90%;" src="http://localhost/erp/assets/global/img/not-available.png">

                <div class="btn btn-group">

                    <a class="btn btn-default"><i class="fa fa-tags"></i></a>
                    <a class="btn btn-default"><i class="fa fa-star"></i></a>
                    <a class="btn btn-default"><i class="fa fa-edit"></i></a>
                    <a class="btn btn-default"><i class="fa fa-envelope"></i></a>
                </div>

            </div>
        </div>
        <div class="col-md-4">


            <div class="portlet light portlet-fit bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <i class=" icon-layers font-green"></i>
                        <span class="caption-subject font-green bold uppercase">
                            <?php echo $ownername; ?>
                        </span>
                        <div class="caption-desc font-grey-cascade"></div>
                    </div>
                    <div class="actions">
                        <div class="btn-group btn-group-devided">
                        </div>
                    </div>
                </div>
                <div class="portlet-body legend-info">
                    <legend>
                        Service No.
                        <strong class="pull-right"><?php echo $qry->servicenumber ?></strong>
                    </legend>

                    <legend>
                        Meter No.
                        <strong class="pull-right"><?php echo $qry->mtrno ?> </strong>
                    </legend>

                    <legend>
                        Serial No.
                        <strong class="pull-right"><?php echo $qry->mtrserial ?> </strong>
                    </legend>

                    <legend>
                        G-D-L-B
                        <strong class="pull-right"><?php echo get_gdlb_name($qry->gdlb) ?> </strong>
                    </legend>

                    <legend>
                        Date Contract
                        <strong class="pull-right"><?php echo $qry->datecontract ?></strong>
                    </legend>

                    <legend>
                        Date Connected
                        <strong class="pull-right"><?php echo $qry->dateconnected ?></strong>
                    </legend>

                    <legend>
                        Specific Address
                        <strong class="pull-right"><?php echo $qry->address ?></strong>
                    </legend>
                </div>
            </div>

            <div class="portlet light portlet-fit bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <i class=" icon-layers font-green"></i>
                        <span class="caption-subject font-green bold uppercase">
                            Transaction History
                        </span>
                        <div class="caption-desc font-grey-cascade"></div>
                    </div>
                    <div class="actions">
                        <div class="btn-group btn-group-devided">
                        </div>
                    </div>
                </div>
                <div class="portlet-body ">
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="portlet light portlet-fit bordered table">
                <div class="portlet-title">
                    <div class="caption">
                        <i class=" icon-layers font-green"></i>
                        <span class="caption-subject font-green bold uppercase">
                            Billing
                        </span>
                        <div class="caption-desc font-grey-cascade"></div>
                    </div>
                    <div class="actions bold font-red-haze" style="font-size: 22px;">
                        3,023.22
                    </div>
                </div>
                <div class="portlet-body ">
                    <table id="acct_billing" class="table table-hover table-stripped table-bordered">
                        <thead>
                        <th>Year</th>
                        <th>Month</th>
                        <th>Kwh</th>
                        <th>Amount</th>
                        <th>Reading</th>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
