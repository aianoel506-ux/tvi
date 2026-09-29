<div class="row">
    <div class="col-md-12">
            <div class="col-md-6 pull-left" style="margin-left: -15px;">
                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group">
                            <input class="form-control" id="select_district" name="select_district" placeholder="Inspection District...">
                            <div class="input-group-btn">
                                <button class="btn btn-primary" id="print_inspection_list"><i class="fa fa-print"></i> Print</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <table style="width: 100%;" id="cad_trn_list" class="table table-hover table-striped table-condensed table-bordered no-footer tbl-sm" >
            <thead>
                <th></th>
                <th>ESSR</th>
                <th>Date Encoded</th>
                <th>Date Updated</th>
                <th>Grid</th>
                <th>Data</th>
                <th>From</th>
                <th>Transaction</th>
                <th>Remarks</th>
                <th>Status</th>
                <th>View</th>
            </thead>

            <tbody></tbody>
        </table>

        <hr>
        <button class="btn btn-danger" id="btn_import_applications_legacy"><i class="fa fa-download"></i> Import Legacy</button>
    </div>
</div>

<script type="text/javascript" src="<?php echo base_url(); ?>assets/pages/cad/dashboard.js"></script>
<script type="text/javascript">
    CAD.inspection();
</script>