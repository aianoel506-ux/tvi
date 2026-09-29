<div class="portlet light box table">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-warning text-danger"></i>
            <span class="caption-subject font-green-sharp bold uppercase"> Billing Rates Maintenance</span>
            <span class="caption-helper"><?php echo date('F d, Y'); ?></span>
        </div>
    </div>
    <div class="portlet-body">
        <form class="" action="<?php echo base_url('billing/addbillingrates'); ?>" method="post" id="frm_add_rates">
            <div class="row">
                <div class="col-md-12 margin-bottom-20">
                    <div class="col-md-6">

                        <div class="row">

                            <div class="col-md-7">
                                <div class="form-group">
                                    <div class="input-gorup" >
                                        <input style="width: 30%; display: inline-block;" class="form-control" placeholder="Year" value="<?php echo date('Y'); ?>" name="filteryear" id="filteryear"/>
                                        <select style="width: 65%; display: inline-block; margin-top: -3px;" class="form-control input-sm" name="filtermonth" id="filtermonth">
                                            <option value=""></option>
                                            <?php
                                            for($i=1; $i<=12; $i++) {
                                                if($i==date('m')) {
                                                    $selected = 'selected';
                                                }else{
                                                    $selected = '';
                                                }
                                                echo '<option '.$selected.' value="'.$i.'">'.date_formating($i, 'm', 'F').'</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5">

                                <div class="input-group-btn">
                                    <button type="button" id="btn_rate_all" class="btn btn-default">All</button>
                                    <button type="button" id="btn_rate_search" class="btn btn-primary">Search</button>

                                    <?php if(user_id() == 1) {  ?>
                                        <button type="button" id="btn_import_rates" class="btn btn-danger btn-trn">Import from Legacy</button>
                                    <?php } ?>
                                    <button type="button" id="btn_send_btn" class="btn btn-default btn-trn">Send To Audit</button>
                                </div>

                            </div>



                        </div>
                    </div>
                    <table class="table table-bordered table-hover table-condensed tbl-xs" id="tbl_bill_rate">
                        <thead>
                        <th>#</th>
                        </thead>

                        <tfoot>
                        <tr>

                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </form>
    </div>
</div>


<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-select/bootstrap-select.min.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/select2/select2.min.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/datatables/jquery.dataTables.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/datatables/dataTables.bootstrap.min.js" type="text/javascript"></script>



<script src="<?php echo base_url(); ?>assets/pages/billing/main.js" type="text/javascript"></script>
<script>
    BILLING.ratemaintenance();
</script>