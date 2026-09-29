

<!-- START PAGE CONTENT-->
<div class="row">
    <div class="col-md-4">
        <form id="frm_save_mts_reading" action="<?php echo base_url('assets/savemtsreading'); ?>" method="post">
            <div data-toggle="metersearchform"></div>
            <div class="form-group row">
                <label class="col-md-4">Date:</label>
                <div class="col-md-8">
                    <input class="form-control" id="input_date_ret" name="datereturned" placeholder="" type="date" />
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-4">Servno</label>
                <div class="col-md-6">
                    <input required class="form-control" placeholder="Account Search.. " name="acctsearch" id="acctsearch" autocomplete="off"/>
                    <input required type="hidden" name="acctid" id="acctid" autocomplete="off"/>
                </div>
                <div class="col-md-2">
                    <input class="form-control" value="1" name="mtr">
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-4">Old Reading</label>
                <div class="col-md-8">
                    <input class="form-control" name="reading" placeholder="Reading.." />
                </div>
            </div>

            <div class="form-group row">
                <label class="col-md-4">TFDO (check if true)</label>
                <div class="col-md-8">
                    <input type="checkbox" class="form-control" name="tfdo" value="1" />
                </div>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>

    <div class="col-md-8">
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    <h3>Reading List <span id="reading_date_text"></span></h3>
                </div>
            </div>
            <div class="portlet-body">
                <table id="tbl_mts_rdg" class="table table-bordered table-striped">
                    <thead>
                    <th>#</th>
                    <th>Meter #</th>
                    <th>Serial #</th>
                    <th>Servno</th>
                    <th>Owner</th>
                    <th>Reading</th>
                    <th>Encoded By</th>
                    <th>Date Encoded</th>
                    </thead>

                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>



<script type="text/javascript" src="<?php echo base_url(); ?>assets/pages/utility/mts.js"></script>
<script>
    MTS.init();
</script>

<!-- END PAGE CONTENT-->

