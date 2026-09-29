<style>
    .dataTables_scrollBody{
        margin-right: -30px !important;
        width: 102% !important;
    }
</style>


<div class="row">

    <div class="col-md-12">
        <!-- BEGIN Portlet PORTLET-->
        <div class="portlet light">
            <div class="portlet-title">
                <form action="<?php echo base_url('mrd/addmainreadingfindings'); ?>" method="post" id="frm_add_reading_findings">
                    <div class="form-body">
                        <div class="form-group">
                            <div class="input-group" style="position: relative; padding-top: 25px;">
                                <input class="" type="hidden" value="0" id="submit_type_id" />

                                <span style="position: absolute; color: #0c83e7; font-size: 11px; top: 5px; left: 0px;">Findings Codes</span>
                                <span style="position: absolute; color: #0c83e7; font-size: 11px; top: 5px; left: 23.5%;">Findings Descriptions</span>
                                <span style="position: absolute; color: #0c83e7; font-size: 11px; top: 5px; left: 52%;">Department</span>
                                <span style="position: absolute; color: #0c83e7; font-size: 11px; top: 5px; left: 75.5%;">Recheck Print</span>
                                <input required style="width: 25%;" class="form-control" name="codes" placeholder="Findings Codes.."/>
                                <input required style="width: 30%;" class="form-control" name="descs" placeholder="Findings Descriptions.."/>
                                <input required style="width: 25%;" class="form-control" name="dept" placeholder="Findings Descriptions.." id="select2department"/>
                                <select style="width: 20%;" class="form-control" name="recheck" id="select2recheck">
                                    <option value="">Recheck Print..</option>
                                    <option value="1">True</option>
                                    <option value="0">False</option>
                                </select>
                                <span class="input-group-btn">
                                <button type="submit" class="btn blue"><i class="fa fa-save"></i> Save</button>
                            </span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="portlet-body">
                <div class="col-md-6 pull-left" style="margin-left: -15px;">
                    <button id="btn_refresh_list" class="btn btn-default pull-left">Refresh Table <i class="fa fa-refresh"></i></button>
                </div>
                <table class="table table-hover table-bordered table-striped table-condensed" id="tbl_findings_list">
                    <thead>
                    <th><i class="fa fa-reorder"></i></th>
                    <th>Codes</th>
                    <th>Descriptions</th>
                    <th>Department</th>
                    <th>Print Recheck</th>
                    <th>Status</th>
                    <th></th>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
        <!-- END Portlet PORTLET-->
    </div>

</div>



<script type="text/javascript" src="<?php echo base_url(); ?>assets/pages/mrd/mrd.js"></script>
<script type="text/javascript">
    MRD.findingsmain();
</script>