<?php
/**
 * Created by PhpStorm.
 * User: SE
 * Date: 2/20/2018
 * Time: 8:44 AM
 */
?>

<div class="portlet-title">
    <div class="caption">
        <i class="fa fa-edit"></i>
        <span class="caption-subject font-green-sharp bold uppercase">Transactions</span>
        <span class="caption-helper">list</span>

    </div><div class="tabbable-line pull-right" style="margin-bottom: -20px !important; margin-top: 7px;">
        <ul class="nav nav-tabs " id="trn_type_filter">
            <li class="active">
                <a href="#" data-type="0" data-toggle="tab"> All </a>
            </li>
            <li>
                <a href="#" data-type="1" data-toggle="tab"> Billing Payments </a>
            </li>
            <li>
                <a href="#" data-type="2" data-toggle="tab"> Cad Payments </a>
            </li>
            <li>
                <a href="#" data-type="3" data-toggle="tab"> Legal Payments </a>
            </li>
        </ul>
    </div>
</div>
<div class="portlet-body">
    <div class="row">
        <div class="col-md-12">
            <table class="table table-hover table-stripped table-bordered tbl-sm" id="tbl_trn_list">
                <thead>
                <th><i class="fa fa-reorder"></i></th>
                <th>Trn No.</th>
                <th>OR #</th>
                <th>Service No.</th>
                <th>CWT</th>
                <th>Amount Paid</th>
                <th>Type</th>
                <th>Payment</th>
                <th><i class="fa fa-search"></i></th>
                <th><i class="fa fa-check"></i></th>
                </thead>
                <tbody>

                </tbody>
            </table>

            <hr style="margin: 5px 0px;">

            <form id="frm_teller_validation" action="<?php echo base_url('tellering/savevalidation'); ?>" method="post">
                <input type="hidden" name="userid" value="<?php echo user_id(); ?>"/>
                <ul class="list-group summary column table no-border margin-top-10">
                    <li class="list-group-item" style="width: 25%">
                        <span class="col-md-5 label-name">Total Cash</span>
                        <span class="col-md-7 label-default number" id="">
                        <input class="form-control inline" placeholder="0.00" name="totalcash" id="totalcash" />
                    </span>
                    </li>
                    <li class="list-group-item" style="width: 25%">
                        <span class="col-md-5 label-name">Total Check</span>
                        <span class="col-md-7 label-default number" id="">
                        <input class="form-control inline" placeholder="0.00" name="totalcheck" id="totalcheck" />
                    </span>
                    </li>
                    <li class="list-group-item">
                        <span class="col-md-6 label-name">Total Amount</span>
                        <span class="col-md-6 label-default number" ><input id="total_amt_validate" class="form-control inline" value="" placeholder="0.00" disabled/></span>
                    </li>
                    <li class="list-group-item">
                        <div class="btn-group pull-right">
                            <button type="reset" class="btn btn-default"><i class="fa fa-times"></i></button>
                            <button type="submit" class="btn btn-default"><i class="fa fa-save"></i> Validate</button>
                        </div>
                    </li>
                    <span id="collection_details"></span>
                </ul>
            </form>

                <hr style="margin: 5px 0px;">

                <div class="btn-group margin-top-20">
                    <?php if(user_id()==1) { ?>
                        <button data-id="<?php echo user_id(); ?>" id="btn_clear_trans" type="button" class="btn red"><i class="fa fa-times fa-fw"></i>(admin) Clear Transaction</button>
                    <?php } ?>
                    <button type="button" class="btn btn-default" id="orvoidbtn"><i class="fa fa-edit"></i> OR Void</button>
                    <button type="button" class="btn btn-info" id="reqcashcount"><i class="fa fa-edit"></i> Cash Count</button>
                    <button type="button" class="btn btn-success" id="reqmanager"><i class="fa fa-flag-o"></i> Manager</button>
                </div>
        </div>
    </div>
</div>


<div class="modal fade bs-modal-lg draggable-modal in" id="void_window">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form class="" action="<?php echo base_url('tellering/submitorvoid'); ?>" method="post" id="submit_or_void">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title"><i class="fa fa-warning fa-fw text-warning"></i> Administrator Void</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="ors" name="ors" value="" />
                    <input type="hidden" id="dataid" name="dataid" value="" />
                    <div class="row">
                        <div class="col-md-6">
                            <div class="tabbable-line">
                                <h4 class="pull-left">Payment Information</h4>
                                <ul class="nav nav-tabs">
                                    <li class="active pull-right">
                                        <a href="#paysum" data-toggle="tab" aria-expanded="true">
                                            <i class="fa fa-file"></i> Details</a>
                                    </li>
                                    <!--  <li class=" pull-right">
                                          <a href="#paylist" data-toggle="tab" aria-expanded="true">
                                              <i class="fa fa-reorder"></i> Details</a>
                                      </li> -->
                                </ul>
                                <div class="tab-content"  style="min-height: 200px;">
                                    <div class="tab-pane fade in active" id="paysum">
                                        <ul class="list-group summary">
                                            <li class="list-group-item"> Amount Paid: <span class="label label-default pull-right" id="amtpd">0.00</span> </li>
                                            <li class="list-group-item"> Amount NonVat: <span class="label label-default pull-right" id="amtnovat">0.00</span> </li>
                                            <li class="list-group-item"> Amount Vat: <span class="label label-default pull-right" id="amtvat">0.00</span> </li>
                                            <li class="list-group-item"> Amount FR Tx: <span class="label label-default pull-right" id="amtfrtx">0.00</span> </li>
                                        </ul>
                                    </div>

                                    <div class="tab-pane fade in" id="paylist">
                                        <table class="table table-hover table-stripped table-condensed" id="paylisttbl">
                                            <thead>
                                            <th><i class="fa fa-reorder"></i></th>
                                            <th>Acct No.</th>
                                            <th>Pay For</th>
                                            <th>Amount</th>
                                            <th> </th>
                                            </thead>
                                            <tbody>

                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group form-md-radios">
                                <label><i class="fa fa-eye"></i> Select Void Type</label>
                                <div class="md-radio-inline">
                                    <?php

                                    $codes = 'REQTYPEOR';
                                    $sql = $this->db->select("sysid , names")
                                        ->from("prime_types_parameter")
                                        ->where(array("codes"=>$codes,  "status" => 1))
                                        ->get();
                                    $num_rows = $sql->num_rows();
                                    if($num_rows > 0){
                                        foreach ($sql->result() as $row){
                                            ?>
                                            <div class="md-radio">
                                                <input type="radio" id="<?php echo $row->sysid; ?>" name="voidtype" class="md-radiobtn" value="<?php echo $row->sysid; ?>" required>
                                                <label for="<?php echo $row->sysid; ?>">
                                                    <span class="inc"></span>
                                                    <span class="check"></span>
                                                    <span class="box"></span>
                                                    <?php echo $row-> names;?> </label>
                                            </div>
                                            <?php
                                        }
                                    }

                                    ?>



                                </div>
                            </div>
                            <hr>
                            <div class="form-group form-md-line-input">

                                <textarea class="form-control" name="reason" id="reason" placeholder="write you reason of transaction void here." required></textarea>
                                <label for="reason"><i class="fa fa-edit"></i> Reason</label>
                                <span class="help-block">what is the reason of revoking this transaction?</span>

                            </div>


                        </div>
                    </div>




                </div>
                <div class="modal-footer">
                    <button id="btn_submit_void" type="submit" class="btn blue"><i class="fa fa-save fa-fw"></i> Send for Approval</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
