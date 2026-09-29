<?php
$ids = $this->input->post('ids');

$info_arr = get_application_details($ids);

if($info_arr->info) {
    $info = $info_arr->info;
    $apptype = $info->apptype;
    $profile = ($apptype > 1) ? 'Authorized Representative' : 'Owner';
    ?>
    <form action="<?php echo base_url();?>cad/updatereplaceowner" method="post" id="frm_ownership_edit">
        <input type="hidden" name="requestid" value="<?php echo $ids;?>">
        <input type="hidden" id="newowner" name="newowner" value="0">
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <h3 class="col-md-8"><?php echo $profile;?> Information</h3>
                    <div class="col-md-4">

                    </div>
                    <div class="row">
                        <div class="col-md-5">
                            <h4 class="text-primary text-bold">Name</h4>
                            <div class="form-group">
                                <label>Last Name</label>
                                <input class="form-control" placeholder="Lastname" name="lastname" id="lastname" value="" required/>
                            </div>

                            <div class="form-group">
                                <label>First Name</label>
                                <input class="form-control" placeholder="Firstname" name="firstname" id="firstname" value="" required/>
                            </div>
                            <div class="form-group">
                                <label>Middle Name</label>
                                <input class="form-control" placeholder="Middlename" name="middlename" id="middlename" value="" />
                            </div>
                            <div class="form-group">
                                <label>Birth Date<span class="required"></span></label>
                                <input class="form-control" placeholder="Birthday" name="birthday" id="birthday" value=""/>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3" style="padding-left: 0px !important;">Gender<span class="required"></label>
                                <div class="col-md-9">
                                    <div class="icheck-inline">
                                        <?php
                                        $gender = $this->db->select()->from('prime_gender')->where('status',1)->get();
                                        if ($gender->num_rows() > 0) {
                                            foreach ($gender->result() as $g) {
                                                //$checked = ($g->sysid == $person->info->genderid) ? 'checked = "checked"' : '';
                                                echo '<label><input name="gender" value="'.$g->sysid.'" type="radio" class="icheck" required/> '.$g->name.'</label>';
                                            }
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Marital Status<span class="required"></span></label>
                                <input type="text" class="form-control" name="marital" id="select_marital" placeholder="Marital Status..." value="" required>
                            </div>


                        </div>
                        <div class="col-md-7">
                            <h4 class="text-primary text-bold">Address and Contact Info</h4>

                            <div class="form-group">
                                <label>Contact No.<span class="required"></span></label>
                                <input type="text" class="form-control" name="contactno" id="contactno" placeholder="09xxxxxx .. "  style="text-transform: uppercase" value="" required>
                            </div>
                            <div class="form-group">
                            <label>Country<span class="required"></span></label>
                            <input class="form-control" name="country" id="select2_country"  value="" required/>
                            </div>
                            <div class="form-group">
                            <label>Region<span class="required"></span></label>
                            <input placeholder="Select region.. " class="form-control" name="region" id="select2_region" value="" required/>
                            </div>
                            <div class="form-group">
                            <label>Province</label>
                            <input placeholder="Select province.." class="form-control" name="province" id="select2_province" value="" required/>
                            </div>
                            <div class="form-group">
                            <label>Municipal / City<span class="required"></span></label>
                            <input placeholder="Select Municipal / City.." class="form-control" name="city" id="select2_citymun" value="" required/>
                            </div>
                            <div class="form-group">
                            <label>Specific Address<span class="required"></span></label>
                            <textarea class="form-control" rows="2" id="addrspecific" name="addrspec" placeholder="Ex: Blk9 Lot20, DECA Homes Subd., Red Gate, Near Security Guard Outpost"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="reset" class="btn btn-primary" id="btn_new_owner"><i class="fa fa-plus"></i> New</button>
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
    //PECO.select2Basic($('#select_city',document),'query/select2city','City...',true,false,1);
    PECO.select2Basic($('#select_marital',document),'hris/select2marital','Marital Status...',true,false,true);
    //PECO.handlerComplaintsInputBasic();
    $('#birthday',document).datepicker({
        format: 'yyyy-mm-dd'
    });

    CAD.updateOwner(<?php echo $ids;?>);
</script>
