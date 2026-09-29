<?php
/**
 * Created by PhpStorm.
 * User: ITD-SE
 * Date: 8/30/2018
 * Time: 4:27 PM
 */

?>

<form id="frm_search_name" class="" action="<?php echo base_url('ar/searchaccount'); ?>" method="post">
    <div class="form-group" style="margin-bottom: 0px !important">
        <div class="input-group">
            <input class="form-control input-lg" name="searchtxt" id="searchtxt" placeholder="Search..." />
            <div class="input-group-btn">
                <button type="submit" class="btn btn-default btn-lg">Search</button>
            </div>
        </div>
    </div>
</form>
<table class="table table-hover table-bordered table-striped" id="tbl_acct_search">
    <thead>
    <th>#</th>
    <th>Service No.</th>
    <th>Name</th>
    <th>Address</th>
    <th></th>
    </thead>
    <tbody>

    </tbody>
</table>

<script src="<?php echo base_url();?>assets/pages/billing/ar.js" type="text/javascript"></script>
<script>
    AR.search();
</script>
