<style>
    body{
        background-color: white;
    }
    .modal{
        z-index: 4 !important;
    }

</style>




        <div class="col-md-12" style="padding:80px 40px 40px 40px;">

            <span class="btn-group" id="btn-filters">
                <button data-id="0" class="btn btn-default"><i class="fa fa-tag fa-fw"></i> All</button>
                <button data-id="305" class="btn btn-success"><i class="fa fa-check fa-fw"></i> Done</button>
                <button data-id="303" class="btn btn-danger"><i class="fa fa-times fa-fw"></i> Canceled</button>
                <button data-id="300" class="btn btn-warning"><i class="fa fa-refresh fa-fw"></i> Pending</button>
            </span>
            <a class="pull-right btn btn-primary" data-toggle="modal" href="#modal_ticketentry" id="addticketbtn">
                <i class="fa fa-plus"></i> Add Ticket </a>

            <h3 style="color:deepskyblue;">Customer's List</h3>

            <div class="portlet-body ">

                <table class="table table-responsive table-hover table-striped table-condensed table-bordered" id="tbl_tickets">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Complain</th>
                            <th>Particular</th>
                            <th>Service No.</th>
                            <th>Name</th>
                            <th>Address</th>
                            <th>District</th>
                            <th>Remarks</th>
                            <th>Date Created</th>
                            <th>Created By</th>
                            <th>Department</th>
                            <th>Findings</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th><i class="fa fa-wrench" aria-hidden="true"></i></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>

                </table>

            </div>
        </div>


<div class="modal fade bs-modal-lg"  id="modal_ticketentry" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Ticket Entry</h4>
            </div>

            <form id="frm_ticket_entry" method="post" action="<?php echo base_url('user/submitticket');?>">
            <div class="modal-body">

                <div class="row">
                    <div class="col-md-4">
                        <label>Complain<span class="required">
													 </span>
                        </label>
                        <input class="table-group-action-input form-control input-medium" name="tickettype" id="select_ticket"/>

                        <label >Particular <span class="required">
													 </span>
                        </label>
                        <input placeholder="Particular select.." class="table-group-action-input form-control input-medium" name="ticketpart" id="select_ticketpart" readonly/>

                        <label>District<span class="required">
													 </span>
                        </label>
                        <input class="table-group-action-input form-control input-medium" name="district" id="select_district"/>

                        <label>Priority<span class="required">
													 </span>
                        </label>
                        <input class="table-group-action-input form-control input-medium" name="priority" id="select_priority"/>

                    </div>
                    <div class="col-md-4">
                        <label>Name: <span class="required">
													</span>
                        </label>
                        <input type="text"  class="form-control" name="custname" id="custname" placeholder="Enter your name here...">

                        <label>Address: <span class="required">
													 </span>
                        </label>
                        <input type="text" class="form-control" name="custaddr" placeholder="Enter your address here...">

                        <label>Service No: <span class="required">
													 </span>
                        </label>
                        <input type="text" class="form-control" name="custservno" placeholder="Enter your service no. here...">
                    </div>
                    <div class="col-md-4">
                        <label>Remarks: <span class="required">
													 </span>
                        </label>
                        <textarea rows="7" cols="50" class="form-control" name="remarks" placeholder="Enter remarks here..."></textarea>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <div class="col-md-12">
                    <span class=" text-success" style="font-size: 15px;" id="qry_stat"></span>
                    <button id="submit_btn" type="submit" class="btn btn-primary">Save Ticket</button>

                    <button type="button" class="btn default" data-dismiss="modal">Close</button>
                </div>
            </div>
            </form>
        </div>

    </div>

</div>



<div class="modal fade bs-modal-lg"  id="view_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Ticket Info</h4>
            </div>


            <form id="frm_ticket_update" method="post" action="<?php echo base_url('user/updateticket');?>">
                <div class="modal-body">

                    <div class="row">
                        <div class="col-md-6">
                            <h3>Details</h3>
                            <ul class="list-group summary column no-border">
                                <li class="list-group-item">
                                    <span class="label-name col-md-4">Name</span>
                                    <span class="label-default col-md-8" name="ticket_name" id="ticket_name">N/A</span>
                                </li>
                                <li class="list-group-item">
                                    <span class="label-name col-md-4">Address</span>
                                    <span class="label-default col-md-8" name="ticket_address" id="ticket_address">N/A</span>
                                </li>
                                <li class="list-group-item">
                                    <span class="label-name col-md-4">Complain</span>
                                    <span class="label-default col-md-8" name="ticket_complain" id="ticket_complain">N/A</span>
                                </li>
                                <li class="list-group-item">
                                    <span class="label-name col-md-4">Particular</span>
                                    <span class="label-default col-md-8" name="ticket_particular" id="ticket_particular">N/A</span>
                                </li>
                                <li class="list-group-item">
                                    <span class="label-name col-md-4">Service No</span>
                                    <span class="label-default col-md-8" name="ticket_servno" id="ticket_servno">N/A</span>
                                </li>
                                <li class="list-group-item">
                                    <span class="label-name col-md-4">District</span>
                                    <span class="label-default col-md-8" name="ticket_district" id="ticket_district">N/A</span>
                                </li>
                                <li class="list-group-item">
                                    <span class="label-name col-md-4">Date Created</span>
                                    <span class="label-default col-md-8" name="ticket_datecreated" id="ticket_datecreated">N/A</span>
                                </li>
                                <li class="list-group-item">
                                    <span class="label-name col-md-4">Created By</span>
                                    <span class="label-default col-md-8" name="ticket_createdby" id="ticket_createdby">N/A</span>
                                </li>
                                <li class="list-group-item">
                                    <span class="label-name col-md-4">Remarks</span><br><br>
                                    <span class="label-default col-md-8" name="ticket_remarks" id="ticket_remarks">N/A</span>
                                </li>
                            </ul>
                        </div>

                        <div class="col-md-6">
                            <h3>Update</h3>

                                    <input type="hidden" name="ticket_id" id="ticket_id" value=""/>


                                        <div class="row">
                                            <div class="col-md-3">
                                                <label>Status<span class="required"></span>
                                                </label>
                                            </div>
                                            <div class="col-md-5">
                                                <input class="table-group-action-input form-control input-medium" name="ticketstatus" required id="select_status"/>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label>Priority<span class="required"></span>
                                                </label>
                                            </div>
                                            <div class="col-md-5">
                                                <input class="table-group-action-input form-control input-medium" name="ticketpriority" required id="select_selected_priority_ticket"/>
                                            </div>
                                        </div>

                                        <br>

                                        <div class="row">
                                            <div class="col-md-3">
                                                <label>Department <span class="required"></span>
                                                </label>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="text"  class="form-control" name="ticketdepart" id="ticketdepart" placeholder="Enter department here..." required>
                                            </div>
                                        </div>

                                        <br>

                                        <div class="row">
                                            <div class="col-md-3">
                                                <label>Address <span class="required"></span>
                                                </label>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="text"  class="form-control" name="ticketaddr" id="ticketaddr" placeholder="Enter your address here..."  required>
                                            </div>
                                        </div>

                                        <br>

                                        <div class="row">
                                            <div class="col-md-3">
                                                <label>Findings <span class="required"></span>
                                                </label>
                                            </div>
                                            <div class="col-md-8">
                                                <textarea rows="7" cols="50" class="form-control" name="ticketfindings" id="ticketfindings" placeholder="Enter findings here..."  required></textarea>
                                            </div>
                                        </div>

                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <div class="col-md-12">
                        <span class="pull-left text-success" style="font-size: 15px;" id="qry_update_stat"></span>
                        <button id="save_btn" type="submit" class="btn btn-primary">Save</button>

                        <button type="button" class="btn default" data-dismiss="modal">Close</button>
                    </div>
                </div>
                </form>

        </div>

    </div>

</div>
