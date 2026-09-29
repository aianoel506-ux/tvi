<?php
$ids = $this->input->post('ids');

$info_arr = get_application_details($ids);

if($info_arr->info) {
    $info = $info_arr->info;
    $person = get_person_info($info->personid);
    if ($person->qry == true) {
        $marital = select_marital($person->info->marital);
        $marital_text = '<span class="label " style="background: '.$marital->color.' !important; font-size: 14px !important; padding: 2px 2px !important;">' . $marital->text . '</span>';
        $appname = $info->lastname . ', ' . $info->firstname;
        $status = ($person->qry) ? gender($person->info->genderid).', '.$person->info->birthdate.' '. $marital_text : '';
    }

    $address = get_district_name($info->distid) . ', ' . $info->addrspec;
    $contact = ($info->contactmobile != 'None') ? $info->contactmobile : (($info->contactphone != 'None') ? $info->contactphone : 'None');
    ?>
    <form action="<?php echo base_url();?>cad/editowner" method="post" id="frm_ownership_edit">
        <input type="hidden" name="requestid" value="<?php echo $ids;?>">
        <div class="modal-body">
            <div class="row">
                <div class="col-md-5">
                    <h3>Current</h3>
                    <div class="well" style="padding: 5px 5px">
                        <ul class="list-group summary column no-border">
                            <?php if (isset($appname)) {?>
                                <li class="list-group-item">
                                    <span class="label-name col-md-3">Name</span>
                                    <span class="label-default col-md-9" id="curr_appname"><?php echo $appname; ?></span>
                                </li>
                                <li class="list-group-item">
                                    <span class="label-name col-md-3">Status</span>
                                    <span class="label-default col-md-9" id="curr_status"><?php echo $status; ?></span>
                                </li>
                                <li class="list-group-item">
                                    <span class="label-name col-md-3">Address</span>
                                    <span class="label-default col-md-9" id="curr_address"><?php echo $address; ?></span>
                                </li>
                                <li class="list-group-item">
                                    <span class="label-name col-md-3">Contact</span>
                                    <span class="label-default col-md-9" id="curr_contact"><?php echo $contact; ?></span>
                                </li>
                            <?php } else {?>
                                <li class="list-group-item">
                                    <span class="label-default">No authorized representative defined.</span>
                                </li>
                            <?php }?>
                        </ul>
                    </div>
                    <div id="sub_owners">
                        <h4 class="text-primary">Sub-Owners</h4>
                        <table id="sub_owners_list" class="table table-condensed table-hover tbl-xs">
                            <thead>
                                <th>#</th>
                                <th>Name</th>
                                <th>Address</th>
                                <th>Contact</th>
                                <th><i class="fa fa-minus-circle"></i> </th>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-7">
                    <h3>Add Authorized Representative / Sub-Owner</h3>
                    <div class="row">
                        <div class="col-md-5">
                            <h4 class="text-primary text-bold">Name</h4>
                            <div class="form-group">
                                <label>Last Name</label>
                                <input class="form-control" placeholder="Lastname" name="lastname" id="lastname" autocomplete="off" required/>
                            </div>

                            <div class="form-group">
                                <label>Firstname Name</label>
                                <input class="form-control" placeholder="Firstname" name="firstname" id="firstname" required/>
                            </div>

                            <div class="form-group">
                                <label>Middle Name</label>
                                <input class="form-control" placeholder="Middlename" name="middlename" id="middlename" required/>
                            </div>

                            <div class="form-group">
                                <label>Birth Date<span class="required"></span></label>
                                <input class="form-control" placeholder="Birthday" name="birthday" id="birthday" required/>

                                <label><input name="gender" value="1" type="radio" class="icheck" required /> Male</label>
                                <label><input name="gender" value="2" type="radio" class="icheck" required /> Female</label>

                                <label>Marital Status<span class="required"></span></label>
                                <input type="text" class="form-control" name="marital" id="select_marital" placeholder="Marital Status..." required>

                                <label>Contact No.<span class="required"></span></label>
                                <input type="text" class="form-control" name="contactno" id="contactno" placeholder="09xxxxxx .. "  style="text-transform: uppercase" required>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <h4 class="text-primary text-bold">Address</h4>

                            <label>Country<span class="required"></span></label>
                            <input class="table-group-action-input form-control input-medium" name="country" id="select2_country" required/>

                            <label>Region<span class="required"></span></label>
                            <input placeholder="Select region.. " class="table-group-action-input form-control input-medium" name="region" id="select2_region" required/>

                            <label>Province</label>
                            <input placeholder="Select province.." class="table-group-action-input form-control input-medium" name="province" id="select2_province"/>

                            <label>Municipal / City<span class="required"></span></label>
                            <input placeholder="Select Municipal / City.." class="table-group-action-input form-control input-medium" name="city" id="select2_citymun" required/>

                            <label>Specific Address<span class="required"></span></label>
                            <textarea class="table-group-action-input form-control input-medium" rows="2" id="addrspecific" name="addrspecific" placeholder="Ex: Blk9 Lot20, DECA Homes Subd., Red Gate, Near Security Guard Outpost"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <div class="col-md-8">
                    <div class="form-group">
                        <label class="form-label col-md-5"><i class="fa fa-asterisk text-danger"></i> Ownership</label>
                        <div class="col-md-7">
                            <div class="icheck-inline">
                                <label><input name="type" value="1" type="radio" class="icheck" required/> Owner</label>
                                <label><input name="type" value="2" type="radio" class="icheck" required/> Representative</label>
                            </div>
                        </div>
                    </div>
                </div>
            <button type="submit" class="btn btn-primary"> <i class="fa fa-save"></i> Save</button>
        </div>
    </form>
    <?php
}else{
    page_data_notfound_modal('Application info not found!');
}
?>
<script src="<?php echo base_url(); ?>assets/pages/cad/newaccount.js"></script>
<script src="<?php echo base_url(); ?>assets/global/scripts/address.js"></script>

<script type="text/javascript">
    ADDRESS.init();
    PECO.select2Basic($('#select_city',document),'query/select2city','City...',true,false,1);
    PECO.select2Basic($('#select_marital',document),'hris/select2marital','Marital Status...',true,false,true);
    var frm_ownership_edit = $('#frm_ownership_edit',document);
    PECO.handlerComplaintsInputBasic();
    PECO.handleriCheckForm(frm_ownership_edit);
    $('#birthday',document).datepicker({
        format: 'yyyy-mm-dd'
    });

    CAD.editOwner(<?php echo $ids;?>);
</script>
