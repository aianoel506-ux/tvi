<?php
/**
 * Created by PhpStorm.
 * User: ITDSE
 * Date: 7/10/2019
 * Time: 9:43 AM
 */

?>

<form enctype="multipart/form-data" id="submitmeterissuance" action="<?php echo base_url() ?>assets/submitmeterissuance" method="post" >
    <div class="modal-body">
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>Asset Number</label>
                    <input required type="text" name="assetno" class="form-control" />
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Serial Number</label>
                    <input required type="text" name="serialno" class="form-control" />
                </div>
            </div>
            <div class="col-md-4">
                <div class="input-group">
                    <div class="form-group">
                        <label>Brand</label>
                        <input required type="text" id="assetbrand" name="assetbrand" class="form-control" />
                    </div>
                    <span class="input-group-btn">
                        <!-- <a style="margin-top: 24px;" class="btn btn-default" href="#brandmodal" title="Add New Brankd" data-toggle="ajax-modal"><i class="fa fa-plus"></i></a> -->
                        <a style="margin-top: 24px;" class="btn btn-default" href="#modal_add_brand" title="Add New Brankd" data-toggle="modal"><i class="fa fa-plus"></i></a>
                    </span>
                </div>
            </div>

        </div>
        <div class="row">
            <?php
            $getassetspec = $this->db->select("sysid,names")
                ->from("prime_types_parameter")
                ->where(array("codes" => 'MISSPEC' , "status" => 1))
                ->get();
            if($getassetspec->num_rows() > 0){
                foreach ($getassetspec->result() as $row){
                    if($row->names == 'Date Issuance') {
                        $types = 'type="date"';
                    }else{
                        $types = 'type="text"';
                    }
                    echo '
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>'.$row->names.'</label>
                                <input '.$types.'  name="'.$row->sysid.'" class="form-control" />
                            </div>
                        </div>
                     ';
                }
            }
            ?>
        </div>
    </div>

    <div class="modal-footer">
        <button style="margin-right: 13px;" type="reset" class="btn btn-md btn-default"><i class="fa fa-refresh"></i> Reset</button>
        <button style="margin-right: 13px;" type="submit" class="btn btn-md btn-primary"><i class="fa fa-save"></i> Save</button>
        <button id="modal_close" style="margin-right: 13px;" type="button" class="btn btn-md btn-danger"><i class="fa fa-times"></i> Close</button>
    </div>
</form>


<form id="submitbrand" action="<?php echo base_url() ?>assets/addnewbrands" method="post">
    <div id="modal_add_brand" class="modal animated fadeInDown fast" tabindex="-1" data-width="400" style="display: none; padding-right: 19px;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" id="btn_close_brand"></button>
                    <h4 class="modal-title">Add Brand</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label>Codes</label>
                            <input placeholder="Enter codes here" required type="text" name="brandcodes" class="form-control" />
                        </div>
                        <div class="form-group  col-md-6">
                            <label>Brand Name</label>
                            <input placeholder="Enter brand name here" required type="text" name="brandname" class="form-control" />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button style="margin-right: 17px;"  class="btn btn-primary pull-right" type="submit"><i class="fa fa-save"></i> Add</button>
                </div>
            </div>
        </div>
    </div>

</form>

<script>
    PECO.select2Basic($('#assetbrand',document) , 'assets/getbrands' , 'Select Brand',false,false,false);
    PECO.popOverRow($('#btn_add_new_brand', document), true, true, 'popover-info');

    $('#btn_close_brand', document).click(function() {
        $('#modal_add_brand', document).modal('toggle');
    });

    $("#modal_add_brand", document).draggable({
        handle: ".modal-header"
    });
</script>
