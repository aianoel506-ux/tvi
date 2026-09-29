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

$info = $this->db->select('sysid')->from('customer_accounts_main')->where(array('servicenumber' => $servno))->get()->row();
$appid = ($info) ? $info->sysid : 0;
?>
<form class="" action="<?php echo base_url('tellering/submitpay'); ?>" method="post" id="frm_submit_pay">
    <input class="" type="hidden" name="acctid" value="<?php echo $appid; ?>" />
    <input class="" type="hidden" name="mtr" value="<?php echo $mtr; ?>" />
    <input class="" type="hidden" name="moduleid" value="<?php echo $moduleid; ?>" />
    <div class="row">
        <div class="col-md-12">
        <span class="text-primary" id="cust-servno"><?php echo $servno; ?></span>
        <span style="font-size: 20px;" class="caption-subject font-green-sharp bold uppercase" id="cust-name">Name</span>
        <span style="font-size: 20px;" class="caption-helper pull-right"  id="cust-addr" >Address</span>

        <div class="row">
            <div class="col-md-12" >
                <hr style="margin: 2px 0px;">
                <div class="row">
                    <div class="col-md-4">
                        <ul class="list-group column summary no-border list-group-sm">
                            <li class="list-group-item">
                                <span class="label-name col-md-4">Total Bill</span> <span class="col-md-8 label-default number" id="num_ar_bill">0</span>
                            </li>
                            <li class="list-group-item">
                                <span class="label-name col-md-4">Total Interest</span> <span class="col-md-8 label-default number" id="totalint">0</span>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <ul class="list-group column summary no-border list-group-sm">
                            <li class="list-group-item">
                                <span class="label-name col-md-4">Total Vat</span> <span class="col-md-8 label-default number" id="totalvat">0</span>
                            </li>
                            <li class="list-group-item">
                                <span class="label-name col-md-4">Total Cash</span> <span class="col-md-8 label-default number" id="totalcash">0</span>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <ul class="list-group column summary no-border list-group-sm">
                            <li class="list-group-item">
                                <span class="label-name col-md-4">Total Check</span> <span class="col-md-8 label-default number" id="totalcheck">0</span>
                            </li>
                            <li class="list-group-item">
                                <span class="label-name col-md-4">Total FR Tax</span> <span class="col-md-8 label-default number" id="totalfrtx">0</span>
                            </li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>
        <hr style="margin: 4px 0px;">
        <div class="row">
            <div class="col-md-12" style="min-height: 30vh;">
                <table width="100%" id="tbl_billing" class="table table-hover table-bordered table-striped tbl-sm tbl-zoom"  style="">
                    <thead>
                    <th>MO</th>
                    <th>YR</th>
                    <th>Bill</th>
                    <th>Interest</th>
                    <th>VAT</th>
                    <th>NET Amt.</th>
                    <th>FR. TX.</th>
                    <th>CHK</th>
                    <th>R</th>
                    <th><i class="fa fa-search"></i></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    </thead>
                    <tbody>

                    </tbody>
                </table>

            </div>
            <div class="row" style="margin: 0px 0px; margin-bottom: 20px;">
                <div class="col-md-10">
                    <ul class="list-group summary column table">
                        <li class="list-group-item"> <i class="fa fa-warning text-warning"></i> Min. Amount: <span class="label label-default pull-right" id="min_amt">0.00</span></li>
                        <li class="list-group-item"> <i class="fa fa-tag text-info"></i> Total Interest: <span class="label label-default pull-right" id="int_amt">0.00</span></li>
                        <li class="list-group-item"> <i class="fa fa-tag text-info"></i> Current Amount: <span class="label label-default pull-right" id="curr_amt">0.00</span></li>
                    </ul>
                </div>
                <div class="col-md-2">
                    <span class="btn-group pull-right" style="margin-top: 5px;">
                        <button class="btn btn-default btn-xs" id="btn_refresh_tbl"><i class="fa fa-refresh"></i> Refresh</button>
                        <button class="btn btn-primary btn-xs" id="btn_add_row"><i class="fa fa-plus"></i> Add</button>
                    </span>
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
                        <input class="" value="" type="hidden" id="spamtrec" name="amtrec" readonly style="background: transparent !important">
                        <input class="form-control input-lg" value="" placeholder="Amt Rec: " id="spamtrectxt" name="" readonly style="background: transparent !important">
                        <label class="">Amt Rec.</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group form-md-line-input pay-input">
                        <input class="form-control input-lg" value="" placeholder="Change: " id="spamtchange" name="amtchange" readonly style="background: transparent !important">
                        <label class="">Change.</label>
                    </div>
                    <div class="form-group form-md-line-input pull-left">
                        <label for="bulplaycheckbox">Bulk Pay <span class="label label-shortcut pull-right">[F4]</span></label>
                        <input class="icheck " type="checkbox" id="bulplaycheckbox" name="bulkpay" />
                    </div>
                    <div class="btn-group pull-right margin-top-20">
                        <button type="submit" class="btn btn-primary">PAY</button>
                        <button type="reset" class="btn btn-default">Reset</button>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12" style="padding: 10px 30px;" id="footnote"></div>
            </div>


        </div>

        </div>
    </div>
</form>




<script src="<?php echo base_url(); ?>assets/pages/tellering/payments.js"></script>
<script type="text/javascript">
    PAYMENTS.bill('<?php echo $servno; ?>', <?php echo $mtr; ?>, <?php echo $moduleid; ?>);
</script>