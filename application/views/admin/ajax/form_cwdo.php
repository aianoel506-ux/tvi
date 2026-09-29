<style>
    .tt-input  {
        color: #000 !important;
    }
</style>
<?php
/**
 * Created by PhpStorm.
 * User: ITD-SE
 * Date: 6/22/2018
 * Time: 4:42 PM
 */


$view = $this->input->post("view");
?>

<form id="frm_ticket_entry" method="post" action="<?php echo base_url('ts/submitticket');?>">
    <div class="modal-body">
        <?php
        if($view>0) {
            echo '<input name="repsource" type="hidden" value="'.$view.'" />';
        }else{ ?>
        <div class="row">
            <div class="col-md-12">
                <div class="well">
                    <div class="form-group">
                        <label class="form-label col-md-3"><i class="fa fa-flag fa-fw"></i> Report Source</label>
                        <div class="col-md-9">

                            <div class="icheck-inline">
                                <?php

                                $qry_ts_source = $this->db->query("SELECT * FROM prime_types_parameter WHERE codes = 'CRMTYPE' AND status = 1");
                                if($qry_ts_source->num_rows()>0) {
                                    foreach($qry_ts_source->result() as $tsrow) {
                                        echo '<label><input ';
                                        if($tsrow->names == 'CWD') {
                                            echo 'checked="checked"';
                                        }
                                        echo ' name="repsource" value="'.$tsrow->sysid.'" type="radio" class="icheck" /> '.$tsrow->desc.'</label>';
                                    }
                                }

                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>

        <div class="row">

            <div class="col-md-4 complainants">

                <div class="form-group">
                    <label>Last Name</label>
                    <input class="form-control" placeholder="Lastname" name="lastname" id="lastname" autocomplete="off"/>
                </div>

                <div class="form-group">
                    <label>Firstname Name</label>
                    <input class="form-control" placeholder="Firstname" name="firstname" id="firstname"/>
                </div>

                <div class="form-group">
                    <label>Middle Name</label>
                    <input class="form-control" placeholder="Middlename" name="middlename" id="middlename"/>
                </div>

                <label>Contact No.<span class="required"></span></label>
                <input type="text" class="form-control" name="contactno" id="contactno" placeholder="0919xxxx .. "  style="text-transform: uppercase">
            </div>
            <div class="col-md-4">
                <label>Complaints<span class="required"></span></label>
                <input class="table-group-action-input form-control input-medium" name="outage" id="select_complaints"/>

                <label>District<span class="required"></span></label>
                <input class="table-group-action-input form-control input-medium" name="district" id="select_district"/>

                <label>Barangay<span class="required"></span></label>
                <input readonly placeholder="Barangay.. " class="table-group-action-input form-control input-medium" name="barangay" id="select_barangay"/>

                <label>Landmarks<span class="required"></span></label>
                <input readonly placeholder="Landmark.. " class="table-group-action-input form-control input-medium" name="landmark" id="select_landmark"/>

                <label>Priority<span class="required"></span></label>
                <input class="table-group-action-input form-control input-medium" name="priority" id="select_priority"/>

            </div>
            <div class="col-md-4">

                <label class="">Physical Address: <span class="required"></span></label>
                <input type="text" class="form-control" name="custaddr" id="address" placeholder="Enter your physical address here..."  style="">



                <label class="margin-top-10">Describe / Addional Information: <span class="required"></span></label>
                <textarea rows="5" cols="50" class="form-control" name="remarks" placeholder="Enter remarks here..."></textarea>

                <label class="margin-top-10">Service No. / Name: <span class="required"></span></label>
                <input class="form-control" name="acctid" id="re_acctid" placeholder="Tag account.." />
            </div>
        </div>

    </div>
    <div class="modal-footer">
        <div class="col-md-12">
            <span class="pull-left text-success" style="font-size: 15px;" id="qry_stat"></span>
            <button id="submit_btn" type="submit" class="btn btn-primary"><i class="fa fa-save fa-fw"></i> Save Ticket</button>

            <button type="reset" class="btn btn-default">Reset</button>
            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        </div>
    </div>
</form>

<script>
    PECO.handlerComplaintsEntry();
</script>