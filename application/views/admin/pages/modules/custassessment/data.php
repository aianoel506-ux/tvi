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
                                <span class="caption-subject font-green-sharp bold uppercase">Proposal</span>
                                <span class="caption-helper"></span>
                            </div>
                            <div class="tools">
                                <button class="btn btn-warning inline"><i class="fa fa-pencil"></i> Edit</button>
                                <button class="btn btn-primary inline"><i class="fa fa-save"></i> Save</button>
                                <button class="btn btn-default inline"><i class="fa fa-refresh"></i> Reset</button>
                            </div>
                        </div>
                        <div class="portlet-body">
                            <form id="frm_save_assessment" method="post" action="<?php echo base_url('cad/saveassessment');?>">
                                <input id="app_id" name="dataid" value="<?php echo $dataid; ?>" type="hidden"/>

                                <h1 style="margin: 2px 0px;">Project Amount: <span class="font-red-flamingo bold pull-right"><span class="small">Php</span>210,000.00</span></h1>
                                <hr>
                                <h4 class="font-red-flamingo bold">
                                    <div class="btn-group pull-right">
                                    </div>
                                    Proposal
                                </h4>


                                <div class="row">
                                    <div class="col-md-12">
                                        <ul class="list-group summary column table" id="">
                                            <li class="list-group-item" style="width: 230px;">
                                                <span class="col-md-12">DU Name</span>
                                                <span class="col-md-12">
                                                    <input class="form-control"  style="display: block; width: 100%;" id="select2DUname" name="duname" />
                                                </span>
                                            </li>
                                            <li class="list-group-item" style="width: 150px;">
                                                <span class="col-md-12">DU Average Rate</span>
                                                <span class="col-md-12">
                                                    <input class="form-control"  style="display: block; width: 100%;" id="durate" name="durate" placeholder="0.00" />
                                                </span>
                                            </li>
                                            <li class="list-group-item" style="width: 230px;">
                                                <span class="col-md-12">
                                                    <div class="btn-group">
                                                        <a id="generate_proposal" href="javascript:;" type="button" class="btn btn-primary btn-lg inline"><i class="fa fa-save"></i> Generate</a>
                                                        <a id="download_proposal" href="<?php echo base_url('cad/getproposalpdf/1'); ?>"  target="_blank" id="download_proposal" type="button" class="btn btn-default btn-lg inline"><i class="fa fa-file-pdf-o"></i> Download</a>
                                                    </div>
                                                </span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <hr>

                                <h4 class="font-red-flamingo bold">
                                    <div class="btn-group pull-right">
                                    </div>
                                    Billing Computation
                                </h4>

                                <div class="row">
                                    <div class="col-md-12">
                                        <ul class="list-group summary column table" id="assessment_input">
                                            <li class="list-group-item" style="width: 180px;">
                                                <span class="col-md-12">Pay Type</span>
                                                <span class="col-md-12">
                                                    <input class="form-control"  style="display: block; width: 100%;" id="select2paytype" name="paytype" />
                                                </span>
                                            </li>

                                            <li class="list-group-item">
                                                <span class="col-md-12">Payable <span class="small">(Month)</span></span>
                                                <span class="col-md-12">
                                                    <input type="text" name="cntmonth" class="form-control" placeholder="1" id="input_payable_count" />
                                                </span>
                                            </li>
                                            <li class="list-group-item" style="width: 150px;">
                                                <span class="col-md-12">Amount <br><span class="small">(Per Month)</span></span>
                                                <span class="col-md-12">
                                                    <input type="text" name="amtpermonth" class="form-control" disabled placeholder="0.00" id="input_amt_permonth" />
                                                </span>
                                            </li>
                                            <li class="list-group-item" style="width: 160px;">
                                                <span class="col-md-12">Start Pay Month</span>
                                                <span class="col-md-12">
                                                    <input type="text" class="form-control" name="startmonth" placeholder="Month.." id="input_start_months" value="<?php echo date('m');?>" />
                                                </span>
                                            </li>
                                            <li class="list-group-item" style="width: 160px;">
                                                <span class="col-md-12">Start Pay Year</span>
                                                <span class="col-md-12">
                                                    <input type="text" class="form-control" value="<?php echo date('Y');?>" name="startyear" placeholder="Year.." id="input_ref_year" />
                                                </span>
                                            </li>
                                            <li class="list-group-item" style="width: 160px;">
                                                <span class="col-md-12">Interest Rate</span>
                                                <span class="col-md-12">
                                                    <input type="text" class="form-control" value="" name="interest" placeholder="Year.." id="input_interest_rate" />
                                                </span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </form>
                            <hr>
                            <h4 class="font-red-flamingo bold">
                                <span class="pull-right"><span class="small">Principal Amount:</span> <span id="text_principal_amt">0.00</span></span>
                                Billing Table</h4>
                            <table class="table table-bordered table-hover table-striped table-condensed" id="tbl_assessment_billing">
                                <thead>
                                <th>#</th>
                                <th>Year</th>
                                <th>Month</th>
                                <th>Duedate</th>
                                <th>Amount</th>
                                <th>Paid</th>
                                <th>Status</th>
                                <th>Emailed</th>
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



<script type="text/javascript" src="<?php echo base_url();?>assets/pages/assessment/main.js"></script>
<script type="text/javascript">
    ASSESSMENT.init(<?php echo $dataid;?>);
</script>
