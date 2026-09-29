<form enctype="multipart/form-data" id="submitmeterissuance" action="<?php echo base_url() ?>assets/submitmeterissuance" method="post" >
    <div class="row">
        <div class="col-md-12">
            <div class="portlet light">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-pencil font-green-haze"></i> New Asset
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="row">
                       <div class="col-md-6">
                           <div class="form-group">
                               <label>Asset Number</label>
                               <input required type="text" name="assetno" class="form-control" />
                           </div>
                       </div>
                       <div class="col-md-6">
                           <div class="form-group">
                               <label>Serial Number</label>
                               <input required type="text" name="serialno" class="form-control" />
                           </div>
                       </div>

                    </div>
                    <div class="input-group">
                        <div class="form-group">
                            <label>Brand</label>
                            <input required type="text" id="assetbrand" name="assetbrand" class="form-control" />
                        </div>
                        <span class="input-group-btn">
                            <button style="margin-top: 24px;" class="btn btn-default" href="#brandmodal" title="Add New Brankd" data-toggle="ajax-modal"><i class="fa fa-plus"></i></button>
                        </span>
                    </div>
                    <div class="row">
                        <?php
                        $getassetspec = $this->db->select("sysid,names")
                            ->from("prime_types_parameter")
                            ->where(array("codes" => 'MISSPEC' , "status" => 1))
                            ->get();
                        if($getassetspec->num_rows() > 0){
                            foreach ($getassetspec->result() as $row){
                                echo '
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>'.$row->names.'</label>
                                        <input type="text"  name="'.$row->sysid.'" class="form-control" />
                                    </div>
                                </div>
                                    ';
                            }
                        }
                        ?>
                        <div class="form-group">
                            <button style="margin-right: 13px;" type="reset" class="btn btn-md btn-default pull-right"><i class="fa fa-refresh"></i> Reset</button>
                            <button style="margin-right: 13px;" type="submit" class="btn btn-md btn-primary pull-right"><i class="fa fa-save"></i> Save</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</form>

<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/js/fileinput.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/js/locales/fr.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/js/locales/es.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/themes/explorer/theme.js" type="text/javascript"></script>
<script src="<?php echo base_url() ?>assets/pages/assets/mtr.js"></script>

<script>
    MTR.newmi();
    PECO.select2Basic($('#assetbrand',document) , 'assets/getbrands' , 'Select Brand',false,false,false);

   $('#reqfiledrop2' , document).fileinput({
        maxFileSize: 10000,
        showUpload: false
    });
</script>