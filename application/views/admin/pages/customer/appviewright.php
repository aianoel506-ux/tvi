<?php


    $qry_customer_system_size = $this->db->select()
    ->from('application_customers_system_size')
    ->where(array('status' => 305, 'appid' => $dataid))
    ->order_by('sysid', 'desc')
    ->get()->row();

    $power = ($qry_customer_system_size) ? $qry_customer_system_size->power : 0;
    $nop = ($qry_customer_system_size) ? $qry_customer_system_size->nop : 0;
    $total_amt = 0;

    $referrals_name = 'Lucky John Faderon';
    $referrals_email = 'lfaderon@gmail.com';
    $referrals_contact = '09491234567';
?>

<div class="row">
    <div class="col-md-12">
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-edit"></i>
                    <span class="caption-subject font-green-sharp bold uppercase">Referrals</span>
                    <span class="caption-helper"></span>
                </div>
                <div class="tools">

                    <a href="#frm_update_application_referrals" data-toggle="ajax-modal" title="Update Referrals">
                        <i class="fa fa-edit"></i> Edit
                    </a>

                </div>
            </div>
            <div class="portlet-body">
                <div class="list-group summary column no-border" style="margin: 0px 0px;">
                    <li class="list-group-item">
                        <span class="col-md-4 label-name">Name</span>
                        <span class="col-md-8 label-default"><?php echo $referrals_name;?></span>
                    </li>
                    <li class="list-group-item">
                        <span class="col-md-4 label-name">Email</span>
                        <span class="col-md-8 label-default"><?php echo $referrals_email;?></span>
                    </li>
                    <li class="list-group-item">
                        <span class="col-md-4 label-name">Contact</span>
                        <span class="col-md-8 label-default"><?php echo $referrals_contact;?></span>
                    </li>
                </div>
            </div>
        </div>
    </div>

</div>

<div class="row">
    <div class="col-md-12">
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-edit"></i>
                    <span class="caption-subject font-green-sharp bold uppercase">System Information</span>
                    <span class="caption-helper"></span>
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
                <div class="list-group summary column no-border" style="margin: 0px 0px;">
                    <li class="list-group-item">
                        <span class="col-md-4 label-name">Power</span>
                        <span class="col-md-8 label-default"><?php echo number_format($power);?></span>
                    </li>
                    <li class="list-group-item">
                        <span class="col-md-4 label-name">NOP</span>
                        <span class="col-md-8 label-default"><?php echo number_format($nop);?></span>
                    </li>
                    <li class="list-group-item">
                        <span class="col-md-4 label-name">Total Amount</span>
                        <span class="col-md-8 label-default"><?php echo number_format($total_amt, 2);?></span>
                    </li>
                </div>
            </div>
        </div>
    </div>

</div>

<div class="row margin-top-10">

    <div class="col-md-12">
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-edit"></i>
                    <span class="caption-subject font-green-sharp bold uppercase">Billing History</span>
                    <span class="caption-helper"></span>
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
                <div class="list-group summary column no-border">
                    <li class="list-group-item">
                        <span class="col-md-4 label-name">Total Due</span>
                        <span class="col-md-8 label-default">N/A</span>
                    </li>
                    <li class="list-group-item">
                        <span class="col-md-4 label-name">Total Interest</span>
                        <span class="col-md-8 label-default">N/A</span>
                    </li>
                    <li class="list-group-item">
                        <span class="col-md-4 label-name">Total Paid</span>
                        <span class="col-md-8 label-default">N/A</span>
                    </li>
                </div>
            </div>
            <hr style="margin: 0px 0px;">
            <table class="table table-hover table-striped" id="tbl_transaction_history">
                <thead>
                <th>#</th>
                <th>Amount</th>
                <th>Date</th>
                <th>Due</th>
                </thead>
                <tbody></tbody>
            </table>

        </div>
    </div>
</div>

<script>

    PECO.DTDefault($('#tbl_transaction_history', document), 'Data history not found!');
</script>