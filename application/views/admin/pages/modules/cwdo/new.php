
<link href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/css/fileinput.css" media="all" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/themes/explorer/theme.css" media="all" rel="stylesheet" type="text/css"/>


<div class="row">
    <form class="" action="<?php echo base_url('cwdo/newticket'); ?>" method="post" id="frm_new_ticket" multiple="">

        <div class="col-md-12">
            <div class="well" style="display: inline-block; width: 100%; padding: 5px 5px;">
                <a href="<?php echo base_url('module/524e05dc77239f3a15dab766aaa59a9e432efde7/list'); ?>" class="btn btn-default"><i class="fa fa-angle-double-left fa-fw"></i> To List</a>
                <div class="btn-broup pull-right margin-top-10">
                    <button type="reset" class="btn btn-default" id="reset">Reset <i class="fa fa-refresh"></i></button>
                    <button type="submit" class="btn btn-primary">Create Ticket <i class="fa fa-angle-double-right fa-fw"></i></button>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <h4>Complainants Name</h4>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-4">
                        <label>Last Name</label>
                        <input class="form-control" placeholder="Lastname" name="lastname" />
                    </div>
                    <div class="col-md-5">
                        <label>Firstname Name</label>
                        <input class="form-control" placeholder="Firstname" name="firstname" />
                    </div>
                    <div class="col-md-3">
                        <label>Middle Name</label>
                        <input class="form-control" placeholder="Middlename" name="middlename" />
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-md-6">
                    <label class="">Address</label>
                    <input name="address" class="form-control" placeholder="Address..."/>
                </div>

                <div class="col-md-6">
                    <label class="">Contact</label>
                    <input name="contact" class="form-control" placeholder="Contact..."/>
                </div>
            </div>
            <div class="form-group">
                <label class="">Remarks</label>
                <textarea name="remarks" class="form-control" placeholder="Remarks..."></textarea>
            </div>

            <h4>Under the account of</h4>

            <div class="form-group">
                <label class="">Search Service/Name</label>
                <div class="input-group">
                    <input name="acctsearch" id="search_acct" class="form-control" placeholder="Search..."/>
                    <div class="input-group-btn">
                        <button type="button" id="btn_search_acct" class="btn btn-default"><i class="fa fa-search"></i></button>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <ul class="list-group summary column">
                    <li class="list-group-item">
                        <span class="col-md-4 label-name">Servno</span>
                        <span class="col-md-8 label-default" id="res_servno">N/A</span>
                    </li>
                    <li class="list-group-item">
                        <span class="col-md-4 label-name">Name</span>
                        <span class="col-md-8 label-default" id="res_name">N/A</span>
                    </li>
                    <li class="list-group-item">
                        <span class="col-md-4 label-name">Address</span>
                        <span class="col-md-8 label-default" id="res_address">N/A</span>
                    </li>
                </ul>
            </div>

        </div>
        <div class="col-md-7">

            <h4>Specification</h4>
            <div class="form-group row">
                <div class="col-md-6">

                    <label>Complain <span class="required"></span></label>
                    <input class="form-control" name="tickettype" id="select_ticket"/>
                </div>

                <div class="col-md-6">
                    <label>Particular <span class="required"></span></label>
                    <input placeholder="Particular select.." class="form-control" name="ticketpart" id="select_ticketpart" readonly/>
                </div>
            </div>
            <div class="form-group ">
                <label>District <span class="required"></span></label>
                <input class="form-control" name="district" id="select_district"/>
            </div>
            <div class="form-group ">
                <label>Priority <span class="required"></span></label>
                <input class="form-control" name="priority" id="select_priority"/>

            </div>

            <div class="form-group billing hidden">
                <div class="input-group">
                    <div class="icheck-inline">
                        <label><input type="checkbox" class="icheck"> Request for Verification</label>
                    </div>
                </div>
                <table class="table table-hover table-condensed table-bordered table-striped" id="tbl_billhist">
                    <thead>
                    <th>Year</th>
                    <th>Month</th>
                    <th>Billno</th>
                    <th>Prev RDG</th>
                    <th>Pres RDG</th>
                    <th>KWH</th>
                    <th>Amt.</th>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="form-group payments  hidden">
                <label>OR No.</label>
                <input class="form-control" id="orno" placeholder="Employee.." name="orno" />
            </div>
            <div class="form-group services  hidden">
                <label>Tag Personel</label>
                <input class="form-control" id="empid" placeholder="Employee.." name="empid" />
                <label>Comment</label>
                <textarea class="form-control" id="empcomp" placeholder="Comment.." name="empcomp"></textarea>
            </div>
        </div>
    </form>
</div>

<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/js/fileinput.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/js/locales/fr.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/js/locales/es.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/themes/explorer/theme.js" type="text/javascript"></script>

<script src="<?php echo base_url(); ?>assets/pages/cwdo/main.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/pages/cwdo/new.js" type="text/javascript"></script>

<script>
    CWDO.init();
    TICKETING.init();
</script>
