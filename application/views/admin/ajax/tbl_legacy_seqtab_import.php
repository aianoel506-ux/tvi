<?php
/**
 * Created by PhpStorm.
 * User: fader
 * Date: 2/13/2019
 * Time: 1:58 PM
 */

?>

<div class="row">
    <div class="col-md-12">
        <div class="well" style="margin: 0px 0px;">

            <div class="input-group">
    <span class="input-group-addon">
        <i class="fa fa-search"></i>
    </span>
                <input class="form-control " id="legacy_gdlbid" placeholder="Select GDLB..." />
                <span class="input-group-btn">
        <button class="btn btn-default" id="btn_get_legacy_gdlb">Get</button>
        <button class="btn btn-danger" id="btn_update_legacy_gdlb">Update</button>
    </span>
            </div>

        </div>
    </div>
</div>

<table class="table table-condensed table-hover table-bordered" id="tbl_seq_tab_list">
    <thead>
    <th>Servno</th>
    <th>MTR</th>
    <th>Mtr No.</th>
    <th>Ref</th>
    <th>Status</th>
    </thead>
    <tbody>

    </tbody>
</table>
<script>

    PECO.select2Basic($('#legacy_gdlbid', document), 'query/select2gdlb', 'Select GDLB..');
</script>

