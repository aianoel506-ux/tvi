<?php
    $dataarr = $this->input->post('ids');
    $dataarray = explode("-" , $dataarr);
    $ccid = $this->input->post('view');
?>
<form id="submitcostheadexec" action="<?php echo base_url() ?>hris/submitcostheadexec" method="post">
    <div class="row">
        <div class="col-md-12">
            <h4 style="margin-top: 21px;margin-left: 10px;"><?php echo $dataarray[2]; ?></h4>
            <hr>
        </div>
        <div class="container">
            <input type="hidden" name="ccid" value="<?php echo $ccid; ?>">
            <div class="col-md-3">
                <div class="form-group">
                    <label>Head</label>
                    <input type="text" name="heademp" id="heademp" class="form-control" />
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Executive</label>
                    <input  type="text" name="execemp[]" multiple="multiple" id="execemp" class="form-control" />
                </div>
            </div>
            <div class="col-md-1">
                <button type="submit" style="margin-top: 26px;" class="btn btn-primary btn-sm"><i class="fa fa-save"></i></button>
            </div>
        </div>
    </div>
</form>
<script src="<?php echo base_url(); ?>assets/pages/hris/costmain.js"></script>
<script>
    COSTMAIN.init();
    PECO.select2Basic($('#heademp' , document), 'hris/getheads' ,false,false,false,<?php echo $dataarray[0]; ?> );
   // PECO.select2Basic($('#execemp' , document), 'hris/getexecutives' ,false,false,false,<?php echo $dataarray[1]; ?> );
    PECO.select2BasicMult($('#execemp',document) , 'hris/getallemployeesforexeceval' ,false);
</script>