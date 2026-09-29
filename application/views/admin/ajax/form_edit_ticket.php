<?php
/**
 * Created by PhpStorm.
 * User: ITD-SE
 * Date: 7/20/2018
 * Time: 2:53 PM
 */

$ids = $this->input->post('ids');


$view = $this->input->post("view");

$qry = $this->db->select('
                            tdl.sysid, 
                            tdl.repsource, 
                            tdl.complainants,
                            tdl.tickettype,
                            tdl.compname,
                            tp.desc, 
                            tpr.descs AS particular, 
                            tdl.remarks, 
                            tdl.district, 
                            tdl.barangays, 
                            tdl.address, 
                            tdl.contact, 
                            tdl.landmarks, 
                            tdl.createdby, 
                            tdl.updatedby, 
                            tdl.datecreated, 
                            tdl.dateupdated, 
                            tdl.status,
                            tdl.reqverification,
                            p.firstname,
                            p.middlename,
                            p.lastname,
                            tdl.priority
                        ')
                    ->from('ticketing_details_logs AS tdl')
                    ->join('person AS p', 'p.sysid = tdl.complainants', 'left')
                    ->join('prime_types_parameter AS tp', 'tp.sysid = tdl.tickettype', 'left')
                    ->join('ticketing_particular AS tpr', 'tpr.sysid = tdl.ticketpart', 'left')
                    ->where(array('tdl.sysid' => $ids))
                    ->get()->row();
if($qry) {
?>
<form id="frm_ticket_entry" method="post" action="<?php echo base_url('ts/submitticketupdate');?>">
    <div class="modal-body">
        <?php
        if( $view>0 ) {
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

                                    $qry_ts_source = $this->db->query("SELECT * FROM prime_types_parameter WHERE codes = 'TSSOURCE' AND status = 1");
                                    if($qry_ts_source->num_rows()>0) {
                                        foreach($qry_ts_source->result() as $tsrow) {
                                            echo '<label><input ';

                                            if($tsrow->sysid == $qry->repsource) {
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
                    <input class="form-control" placeholder="Lastname" name="lastname" id="lastname" autocomplete="off" value="<?php echo $qry->lastname; ?>"/>
                </div>

                <div class="form-group">
                    <label>Firstname Name</label>
                    <input class="form-control" placeholder="Firstname" name="firstname" id="firstname" value="<?php echo $qry->firstname; ?>"/>
                </div>

                <div class="form-group">
                    <label>Middle Name</label>
                    <input class="form-control" placeholder="Middlename" name="middlename" id="middlename" value="<?php echo $qry->middlename; ?>"/>
                </div>

                <label>Contact No.<span class="required"></span></label>
                <input type="text" class="form-control" name="contactno" id="contactno" placeholder="0919xxxx .. "  style="text-transform: uppercase" value="<?php echo $qry->contact; ?>">
            </div>
            <div class="col-md-4">
                <label>Complaints<span class="required"></span></label>
                <input class="table-group-action-input form-control input-medium" name="outage" id="select_outage"/>

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
                <input type="text" class="form-control" name="custaddr" id="address" placeholder="Enter your physical address here..."  style="" value="<?php echo $qry->address; ?>">



                <label class="margin-top-10">Describe / Addional Information: <span class="required"></span></label>
                <textarea rows="5" cols="50" class="form-control" name="remarks" placeholder="Enter remarks here..."><?php echo $qry->remarks; ?></textarea>

                <label class="margin-top-10">Service No. / Name: <span class="required"></span></label>
                <input class="form-control" name="acctid" id="re_acctid" placeholder="Tag account.." />
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <div class="col-md-12">
            <span class="pull-left text-success" style="font-size: 15px;" id="qry_stat"></span>
            <button id="submit_btn" type="submit" class="btn btn-primary"><i class="fa fa-save fa-fw"></i> Update Ticket</button>

            <button type="reset" class="btn btn-default">Reset</button>
            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        </div>
    </div>
</form>
<?php } else {
    page_data_notfound();
}
?>



<script>
    PECO.tcinit(<?php echo $view;?>);
    $('#select_outage').val(<?php echo $qry->tickettype;?>).trigger('change.select2');
    $('#select_district').val(<?php echo $qry->district;?>).trigger('change.select2');
    $('#select_priority').val(<?php echo $qry->priority;?>).trigger('change.select2');
    // el, distid, brgyid, initdata, mode
    PECO.handlerBarangay($('#select_barangay', document), <?php echo $qry->district; ?>, <?php echo $qry->barangays; ?>, false, false);
    PECO.handlerLandmark($('#select_landmark', document), <?php echo $qry->district; ?>, <?php echo $qry->barangays; ?>);
</script>

