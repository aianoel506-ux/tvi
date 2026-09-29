<?php
/**
 * Created by PhpStorm.
 * User: SE
 * Date: 2/13/2018
 * Time: 4:52 PM
 */



$servno = $this->input->post('servno');
$mtr = $this->input->post('mtr');
$moduleid = $this->input->post('moduleid');


$appid = str_pad($servno, 6, '0', STR_PAD_LEFT);
$sq = false;

$info = get_joborder_info($appid);
if ($info) {
    $accinfo = get_active_account_info($info->acctid);
    $sq = true;
}

$appname = $accinfo->name;
$appaddr = $accinfo->address;

if($sq) {
    ?>
    <div class="row">
        <form class="form-horizontal" method="post" id="frm_cad_pay" action="<?php echo base_url('tellering/paycad'); ?>">
            <div class="col-md-12">

                <input value="<?php echo $servno; ?>" type="hidden" id="appid" name="appid" placeholder="" class="form-control" />
                <input value="<?php echo $mtr; ?>" type="hidden" id="mtr" name="mtr" placeholder="" class="form-control" />
                <input value="<?php echo $moduleid; ?>" type="hidden" id="moduleid" name="moduleid" placeholder="" class="form-control" />

                <div class="portlet-title">
                    <div class="caption" style="font-size:18px;">
                        <i class="fa fa-user"></i>
                        <span class="caption-subject font-blue-sharp bold uppercase" id="appid"><?php echo $appid; ?></span>
                        <span class="caption-subject font-green-sharp bold uppercase" id="name"><?php echo $appname; ?></span>
                        <span class="caption-helper pull-right" id="addr"><?php echo $appaddr; ?></span>
                    </div>
                </div>

                <div class="portlet-body table">
                    <div class="row">
                        <div class="col-md-4">
                            <ul class="list-group column summary no-border">
                                <li class="list-group-item">
                                    <span class="col-md-4 label-name">Total Item(s)</span><span class="label-default col-md-8 number" id="totalitem">0</span>
                                    <span class="col-md-4 label-name">Total FR Tax</span><span class="label-default col-md-8 number" id="totalfrtx">0.00</span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-4"></div>
                        <div class="col-md-4">
                            <ul class="list-group column summary no-border">
                                <li class="list-group-item">
                                    <span class="col-md-4 label-name">Total Cash</span><span class="label-default col-md-8 number" id="totalcash">0.00</span>
                                    <span class="col-md-4 label-name">Total Check</span><span class="label-default col-md-8 number" id="totalcheck">0.00</span>
                                </li>
                            </ul>
                        </div>
                    </ul>
                    </div>
                    <div class="row">
                        <div class="col-md-12" style="min-height: 30vh;">
                            <table width="100%" class="table table-hover table-bordered table-striped  tbl-sm tbl-zoom" id="tbl_assesstments">
                                <thead>
                                <th><i class="fa fa-reorder"></i></th>
                                <th>Account Code</th>
                                <th>No-VAT Amt</th>
                                <th>VAT amount</th>
                                <th>CWT</th>
                                <th>Total</th>
                                <th>CHK</th>
                                <th></th>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>

                    <div class="row" style="margin: 0px 0px;">
                        <div class="col-md-3">
                            <div class="form-group form-md-line-input pay-input">
                                <input class="form-control input-lg" value="" placeholder="Cash Amt." id="amtcash" name="amtcash">
                                <label class="" for="amtcash">Amt Cash: <span class="label label-shortcut pull-right">[F2]</span></label>
                            </div>
                            <div class="form-group form-md-line-input pay-input">
                                <input class="form-control input-lg" value="" placeholder="Chk Amt." id="amtchk" name="amtchk">
                                <label class="" for="amtchk">Amt Chk: <span class="label label-shortcut pull-right">[F3]</span></label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group form-md-line-input pay-input">
                                <input class="form-control input-lg" value="" placeholder="Cash Bal." id="amtcashb" name="amtcashb" readonly style="background: transparent !important">
                                <label class="">Cash Balance:</label>
                            </div>
                            <div class="form-group form-md-line-input pay-input">
                                <input class="form-control input-lg" value="" placeholder="Chk Bal." id="amtchkb" name="amtchkb" readonly style="background: transparent !important">
                                <label class="">Check Balance:</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group form-md-line-input pay-input">
                                <input class="form-control text-danger input-lg" value="" required="" placeholder="Total: " id="sptotalamt" name="amttotal" readonly style="background: transparent !important">
                                <label class="">Total Amount:</label>
                            </div>
                            <div class="form-group form-md-line-input pay-input">
                                <input class="form-control input-lg" value="" placeholder="Amt Rec: " id="spamtrec" name="amtrec" readonly style="background: transparent !important">
                                <label class="">Amt Rec.</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group form-md-line-input pay-input">
                                <input class="form-control input-lg" value="" placeholder="Change: " id="spamtchange" name="amtchange" readonly style="background: transparent !important">
                                <label class="">Change.</label>
                            </div>
                            <div class="btn-group pull-right margin-top-20">
                                <button type="submit" class="btn btn-primary">PAY</button>
                                <button type="reset" class="btn btn-default">Reset</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <ul class="list-group summary column no-border">
                            <li class="list-group-item">
                                <label class="col-md-5 label-name">Amount (no-vat)</label>
                                <span class="col-md-7 label-default number" id="assesstmentnovat">0.00</span>
                            </li>
                            <li class="list-group-item">
                                <label class="col-md-5 label-name">Total Vat</label>
                                <span class="col-md-7 label-default number" id="assesstmentvat">0.00</span>
                            </li>
                            <li class="list-group-item">
                                <label class="col-md-5 label-name">Total Amount</label>
                                <span class="col-md-7 label-default number" id="assessmenttotalamt">0.00</span>
                            </li>
                            <li class="list-group-item " >
                                <label class="col-md-5 label-name">Total Amount (Paid)</label>
                                <span class="col-md-7 label-default number" id="assessmenttotalamtpaid" style="color: red !important;">0.00</span>
                            </li>
                            <li class="list-group-item">
                                <label class="col-md-5 label-name">Total Balance</label>
                                <span class="col-md-7 label-default number" id="assessmenttotalamtbal">0.00</span>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-6 ">
                        <ul class="list-group summary column">
                            <li class="list-group-item">
                                <label class="col-md-5 label-name">Initial Deposit</label>
                                <span class="col-md-7 label-default number" id="initdepamt">0.00</span>
                            </li>
                            <li class="list-group-item">
                                <label class="col-md-5 label-name">Guaranty Deposit</label>
                                <span class="col-md-7 label-default number" id="gdrdepamt">0.00</span>
                            </li>
                            <li class="list-group-item">
                                <label class="col-md-5 label-name">Labor & Services</label>
                                <span class="col-md-7 label-default number" id="laborservamt">0.00</span>
                            </li>
                            <li class="list-group-item">
                                <label class="col-md-5 label-name">Others</label>
                                <span class="col-md-7 label-default number" id="otheramt">0.00</span>
                            </li>
                        </ul>
                    </div>
                </div>

            </div>
            <div class="form-actions hidden">
                <div class="btn-group ">
                    <button class="btn btn-primary pull-right" type="submit">Pay</button>
                </div>
            </div>


        </form>



    </div>
<?php } else {
    page_file_notfound('Application', 'System cannot find a record(s) from Application No. '.$appid);
} ?>

<script src="<?php echo base_url(); ?>assets/pages/tellering/payments.js"></script>
<script src="<?php echo base_url(); ?>assets/pages/tellering/assessments.js" type="text/javascript"></script>

<script type="text/javascript">
    PAYMENTS.cad(<?php echo $servno; ?>, <?php echo $moduleid; ?>);
</script>