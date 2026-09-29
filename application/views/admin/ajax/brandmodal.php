<?php
/**
 * Created by PhpStorm.
 * User: peco_
 * Date: 7/1/2019
 * Time: 1:24 PM
 */
?>

<div class="row">
    <div class="col-md-12">
        <form id="submitbrand" action="<?php echo base_url() ?>assets/addnewbrands" method="post">
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
        </form>
    </div>
</div>


<script src="<?php echo base_url() ?>assets/pages/assets/mtr.js"></script>

<script>
    MTR.newmi();
</script>