<div class="tab-pane fade in" id="requestgroupworkshift">
    <div class="row">
        <div class="col-md-12">
            <div class="portlet light">
                <div class="portlet-title">
                    <div class="caption">
                        Select Workshift
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Month</label>
                                <input type="text" name="monthdate" id="monthdate" class="form-control" />
                            </div>
                        </div>
                        <div class="col-md-2">

                            <div class="form-group">
                                <label for="typedate">Select list:</label>
                                <select class="form-control" id="typedate">
                                    <option value="1">1 - 15</option>
                                    <option value="2">16 - 30</option>
                                </select>
                            </div>

                        </div>
                    </div>
                    <hr>
                    <table class="table table-hover table-bordered table-responsive tbl-sm" id="groupworkshift">
                        <thead>
                        <th style="width: 10px !important;"></th>
                        <th style="width: 70px !important;">Name</th>
                        <th style="width: 100px !important;">Workshift</th>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                    <button class="btn btn-primary" type="button" id="workshiftapprovalbtn">Send for Approval</button>

                </div>
            </div>
        </div>
        <div class="col-md-6">

        </div>
    </div>

</div>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/pages/hris/hrmain.js"></script>


<script type="text/javascript">
    MAINTENACE.initworkshiftrequest();
</script>
