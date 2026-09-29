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

<form id="frm_apprehension_entry" method="post" action="<?php echo base_url('ts/submitticket');?>" enctype="multipart/form-data">
    <div class="modal-body">
        <input name="repsource" type="hidden" value="1087" />

        <div class="row">

            <div class="col-md-4 complainants">
                <h4 class="text-primary text-bold">Apprehended</h4>
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

                <h4 class="text-primary text-bold">Nature of Apprehension</h4>
                <label>Apprehension Type<span class="required"></span></label>
                <input class="table-group-action-input form-control input-medium" name="outage" id="select2apprehension"/>

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
                <h4 class="text-primary text-bold">More Details</h4>
                <label class="">Physical Address: <span class="required"></span></label>
                <input type="text" class="form-control" name="custaddr" id="address" placeholder="Enter your physical address here..."  style="">

                <label class="margin-top-10">Describe / Addional Information: <span class="required"></span></label>
                <textarea rows="1" cols="50" class="form-control" name="remarks" placeholder="Enter remarks here..."></textarea>
                <label class="margin-top-10">Map Location: <span class="required"></span></label>
                <textarea rows="1" cols="" class="form-control" name="mapurl" placeholder="Paste Link of Map Location of the trouble call"></textarea>

                <label class="margin-top-10">Service No. / Name: <span class="required"></span></label>
                <input class="form-control" name="acctid" id="re_acctid" placeholder="Tag account.." />
            </div>
        </div>

    </div>
    <div class="modal-footer">
        <div class="col-md-12">
            <span class="pull-left" id="">
                <div class="col-md-5">
                    <i class="fa fa-files-o pull-left"></i> Attachedments:
                </div>
                <div class="col-md-7">
                    <input class="" type="file" name="pics[]" multiple />
                </div>
            </span>
            <button id="submit_btn" type="submit" class="btn btn-primary"><i class="fa fa-save fa-fw"></i> Save Ticket</button>

            <button type="reset" class="btn btn-default">Reset</button>
            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        </div>
    </div>
</form>

<script>
    PECO.apprehension();
</script>