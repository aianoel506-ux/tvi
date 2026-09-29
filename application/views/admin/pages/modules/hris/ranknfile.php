<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/jquery-tags-input/jquery.tagsinput.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-markdown/css/bootstrap-markdown.min.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/typeahead/typeahead.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/jquery-tags-input/jquery.tagsinput.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-markdown/css/bootstrap-markdown.min.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/typeahead/typeahead.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/datatables/extensions/Scroller/css/dataTables.scroller.min.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/datatables/extensions/ColReorder/css/dataTables.colReorder.min.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/datatables/extensions/TableTools/css/dataTables.tableTools.min.css"/>
<style>
    .form-md-line-input {
        position: relative !important;
    }
    .form-md-line-input .fileinput .input-group-addon{
        background: rgba(177,176,176,0.47) !important;
        z-index: 3000 !important;
    }
    .form-md-line-input .fileinput .input-group-addon .btn.red-intense {
        background: rgba(251,124,126,0.77) !important;
    }
    .form-md-line-input .select2-container{
        margin-bottom: 0px !important;
    }
    #processpayrollbtn{
        margin-top: 30px;
    }
    .listtabs{
        height: 87px !important;
    }
    .listtabs li{
        height: 50px !important;
    }
    .listtabs li a{
        height: 80px !important;
    }



</style>

<h4 class="pull-left">
    <i class="fa fa-edit"></i>
    <span class="caption-subject font-green-sharp bold uppercase">Payroll</span>
    <span class="caption-helper"><?php echo date('F d, Y'); ?></span>
</h4>
<div class="row"><div class="col-md-12">
        <form class="form-horizontal" id="frm_process_payroll" action="<?php echo base_url('payroll/emplist'); ?>" method="post">
            <?php pages_parent_navigation($navid, array('payclass' => 128));

            ?>
            <hr>

            <table class="table table-responsive table-hover table-striped table-condensed table-bordered tbl-sm" id="payroll_table" width="100%">
                <thead>
                <th></th>
                <th><i class="fa fa-reorder"></i></th>
                <th>Emp. Code</th>
                <th> Last Name</th>
                <th>First Name</th>
                <th>Middle Name</th>
                <th>Dept.</th>
                <th>Basic</th>
                <th>Earnings</th>
                <th>Loans</th>
                <th>Prem.</th>
                <th>TAX</th>
                <th>Deductions</th>
                <th>Net</th>
                <th>Status</th>
                <!-- <th><i class="fa fa-wrench"></i></th> -->
                </thead>
                <tbody>
                </tbody>
            </table>
            <div class="col-md-12">
                <hr>
            </div>
        </form>
    </div>
</div>
<div class="row">
    <div class="col-md-3">
        <ul class="list-group summary column no-border list-group-sm">
            <li class="list-group-item">
                <span class="label label-name col-md-7 col-xs-7 col-sm-7 col-lg-7">Total Loans</span>
                <span class="col-md-5 col-xs-5 col-sm-5 col-lg-5 label-default number" id="totalloanssum">00.00</span>
            </li>
            <li class="list-group-item">
                <span class="label label-name col-md-7 col-xs-7 col-sm-7 col-lg-7">Total Premiums</span>
                <span class="col-md-5 col-xs-5 col-sm-5 col-lg-5 label-default number" id="totalpremiumssum">00.00</span>
            </li>
        </ul>
    </div>
    <div class="col-md-3">
        <ul class="list-group summary column no-border list-group-sm">
            <li class="list-group-item">
                <span class="label label-name col-md-7 col-xs-7 col-sm-7 col-lg-7">Total TAX</span>
                <span class="col-md-5 col-xs-5 col-sm-5 col-lg-5 label-default number" id="totaltaxsum">00.00</span>
            </li>
            <li class="list-group-item">
                <span class="label label-name col-md-7 col-xs-7 col-sm-7 col-lg-7">Total Deductions</span>
                <span class="col-md-5 col-xs-5 col-sm-5 col-lg-5 label-default number" id="totaldeductionssum">00.00</span>
            </li>
        </ul>
    </div>
    <div class="col-md-3">

        <ul class="list-group summary column no-border list-group-sm">
            <li class="list-group-item">
                <span class="label label-name col-md-7 col-xs-7 col-sm-7 col-lg-7">Total Budget</span>
                <span class="col-md-5 col-xs-5 col-sm-5 col-lg-5 label-default number" id="totalbudget">00.00</span>
            </li>
            <li class="list-group-item">
                <span class="label label-name col-md-7 col-xs-7 col-sm-7 col-lg-7">Total Budget Balance</span>
                <span class="col-md-5 col-xs-5 col-sm-5 col-lg-5 label-default number" id="totalbudgetbalance">00.00</span>
            </li>
        </ul>
    </div>
    <div class="col-md-3">

        <ul class="list-group summary column no-border list-group-sm">
            <li class="list-group-item">
                <span class="label label-name col-md-7 col-xs-7 col-sm-7 col-lg-7">Total Earnings</span>
                <span class="col-md-5 col-xs-5 col-sm-5 col-lg-5 label-default number" id="totalearningssum">00.00</span>
            </li>
            <li class="list-group-item">
                <span class="label label-name col-md-7 col-xs-7 col-sm-7 col-lg-7">Total Net</span>
                <span class="col-md-5 col-xs-5 col-sm-5 col-lg-5 label-default number" id="totalnetsum">00.00</span>
            </li>
        </ul>
    </div>
</div>
<hr>
<hr>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/moment.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/jquery.mockjax.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-editable/bootstrap-editable/js/bootstrap-editable.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-editable/inputs-ext/address/address.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-editable/inputs-ext/wysihtml5/wysihtml5.js"></script>
<script src="<?php echo base_url(); ?>assets/pages/payroll/main.js"></script>
<script type="text/javascript">
    PAYROLL.init(128);
</script>
