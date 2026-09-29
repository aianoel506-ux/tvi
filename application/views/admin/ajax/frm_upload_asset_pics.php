<?php
/**
 * Created by PhpStorm.
 * User: fader
 * Date: 6/18/2019
 * Time: 1:34 PM
 */

?>
<form id="frm_upload_assets_img" action="<?php echo base_url('assets/uploadimgs'); ?>" method="post">
    <div class="row">
        <div class="col-md-12">
            <div class="col-md-4">
                <div align="center">

                    <h4>Brows File</h4>
                    <div class="fileinput fileinput-new" data-provides="fileinput">
                        <div class="fileinput-new thumbnail" style="width: 200px; height: 150px;">
                            <img src="http://www.placehold.it/200x150/EFEFEF/AAAAAA&amp;text=no+image" alt=""> </div>
                        <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 200px; max-height: 150px; line-height: 10px;"></div>
                        <div>
                                                                <span class="btn default btn-file">
                                                                    <span class="fileinput-new"> Select image </span>
                                                                    <span class="fileinput-exists"> Change </span>
                                                                    <input type="hidden" value="" name="..."><input type="file" name=""> </span>
                            <a href="javascript:;" class="btn red fileinput-exists" data-dismiss="fileinput"> Remove </a>
                        </div>
                    </div>
                    <div class="clearfix margin-top-10"></div>
                </div>
            </div>

            <div class="col-md-8">
                <h4>Image Details</h4>
                <div class="form-group">
                    <label class="input-label">Tag</label>
                    <input class="form-control" placeholder="Image tags.." name="assetimgtag" name="assetimgtag" />
                </div>
                <div class="form-group">
                    <label class="input-label">Descriptions</label>
                    <input class="form-control" placeholder="Image descriptions.." name="assetimgdescs" name="assetimgdescs" />
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button class="btn btn-default" type="reset">Clear</button>
        <button class="btn btn-primary" type="submit">Save / Upload</button>
    </div>

</form>


