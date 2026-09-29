

<?php
$select2_stocksql = json_decode($this->assets->select2_stocks());
?>
<style type="text/css">
    @media screen and (min-width: 900px) {
        .inventory-row {
            margin-top: 30px;
            margin-bottom: 30px;
        }
    }
</style>

<div class="row inventory-row">
    <div class="col-md-10 col-md-offset-1">
        <div class="portlet light bordered">
            <div class="portlet-title">
                <h2 class="caption bold "><i class="fa fa-search font-red-flamingo"></i> Entry</h2>
            </div>
            <div class="portlet-body">

                <form id="frm_scan_entry" method="post" action="<?php echo base_url('inventory/draftstockin') ;?>">
                    <div class="form-group">
                        <input class="form-control" name="stockid" id="select2stock" />
                    </div>

                    <div class="form-group" style="margin-top: 25px;">
                        <input class="form-control input-large" name="serials" id="search_text" placeholder="Search / Scan" />
                    </div>

                    <div class="form-group" style="margin-top: 25px; margin-bottom: 20px;">
                        <button type="reset" class="btn btn-danger btn-lg inline"><i class="fa fa-refresh"></i> Reset</button>
                        <button type="submit" class="btn btn-primary btn-lg pull-right"><i class="fa fa-save"></i> Save</button>
                    </div>


                </form>



            </div>
            <div class="portlet-footer margin-top-10">
                <div class="row margin-top-10">
                    <div class="col-md-12">
                        <table class="table table-hover table-striped" id="tbl_stocks_in_list">
                            <thead>
                            <th>#</th>
                            <th>Serial Number</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Control</th>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>





<h4 class="text-align-center font-white">
    PAE Inventory v2.0
</h4>


<script src="<?php echo base_url(); ?>assets/global/plugins/select2/select2.min.js" type="text/javascript"></script>


<script src="<?php echo base_url(); ?>assets/pages/inventory/main.js"></script>
<script type="text/javascript">
    INVENTORY.stockinentry();
</script>

