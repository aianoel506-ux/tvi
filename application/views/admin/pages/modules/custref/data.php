<?php
$referrals_name = 'John B. Tadifa';
$referrals_email = 'john.tadifa@panayelectric.com';
$referrals_contact = '0919122346';
?>
<div>
    <div class="row">
        <div class="col-md-5">
            <?php customer_application_basicinfo($dataid, true);  ?>
        </div>
        <div class="col-md-7">

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
                            <hr>
                            <form id="frm_save_referrals" method="post" action="<?php echo base_url('cad/savereferrals');?>">
                                <input name="dataid" type="hidden" value="<?php echo $dataid; ?>" />
                                <h4 class="font-red-flamingo bold">
                                    <div class="btn-group pull-right">
                                        <button class="btn btn-primary inline btn-xs"><i class="fa fa-save"></i> Save</button>
                                        <button class="btn btn-default inline btn-xs"><i class="fa fa-refresh"></i> Reset</button>
                                    </div>
                                    Payment Computation
                                </h4>

                                <div class="row">
                                    <div class="col-md-12">
                                        <ul class="list-group summary column table" id="refinput">
                                            <li class="list-group-item">
                                                <span class="col-md-12">Project Amount</span>
                                                <span class="col-md-12">
                                                    <div type="text" class="form-control bold text-primary">
                                                        210,000.00
                                                    </div>
                                                </span>
                                            </li>
                                            <li class="list-group-item" style="width: 170px;">
                                                <span class="col-md-12">Ref Type</span>
                                                <span class="col-md-12">
                                                    <select class="form-control" id="select2reftype" name="reftype">
                                                        <option value="">Select...</option>
                                                        <option value="1">Fix Amount</option>
                                                        <option value="2">Compute (6%)</option>
                                                    </select>
                                                </span>
                                            </li>
                                            <li class="list-group-item" style="width: 150px;">
                                                <span class="col-md-12">Ref Amount</span>
                                                <span class="col-md-12">
                                                    <input type="text" name="refamt" class="form-control" disabled placeholder="0.00" id="input_ref_amount" />
                                                </span>
                                            </li>
                                            <li class="list-group-item" style="width: 160px;">
                                                <span class="col-md-12">Start Pay Month</span>
                                                <span class="col-md-12">
                                                    <input type="text" class="form-control" name="startmonth" placeholder="Month.." id="input_ref_months" />
                                                </span>
                                            </li>
                                            <li class="list-group-item" style="width: 160px;">
                                                <span class="col-md-12">Start Pay Year</span>
                                                <span class="col-md-12">
                                                    <input type="text" class="form-control" value="<?php echo date('Y');?>" name="startyear" placeholder="Year.." id="input_ref_year" />
                                                </span>
                                            </li>
                                            <li class="list-group-item">
                                                <span class="col-md-12">Payable for</span>
                                                <span class="col-md-12">
                                                    <input type="text" name="refcnt" class="form-control" placeholder="1" id="input_ref_payable_cnt" />
                                                </span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </form>
                            <hr>
                            <h4 class="font-red-flamingo bold">Payment Arrangement</h4>
                            <table class="table table-bordered table-hover table-striped" id="tbl_referrals_ar">
                                <thead>
                                <th>#</th>
                                <th>Amount</th>
                                <th>Year</th>
                                <th>Month</th>
                                <th>Status</th>
                                <th>File</th>
                                <th></th>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>



<script type="text/javascript" src="<?php echo base_url();?>assets/pages/referrals/main.js"></script>
<script type="text/javascript">
    REFERRALS.init(<?php echo $dataid;?>);
</script>
