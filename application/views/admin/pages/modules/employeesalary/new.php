<ul class="nav nav-tabs">
    <li class="active">
        <a href="#salinc" data-toggle="tab" aria-expanded="false">Salary Increase</a>
    </li>
    <li class="">
        <a href="#salaries" data-toggle="tab" aria-expanded="false">Salaries</a>
    </li>

</ul>

<div class="tab-content">
    <div class="tab-pane fade in active" id="salinc">
        <div class="col-md-3">
            <div class="form-group">
                <label>Payclass</label>
                <input type="text" class="form-control" id="payclasstype" name="payclasstype" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="portlet">
                    <div class="portlet-title">
                        <div class="caption">

                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-bordered table-hover table-responsive table-condensed tbl-sm" id="salaryinctbl">
                                    <thead>
                                    <th></th>
                                    <th>Emp. Code</th>
                                    <th>Name</th>
                                    <th>Department</th>
                                    <th>Basic</th>
                                    <th>Amount</th>
                                    <th>Purpose</th>
                                    <th>Remarks</th>
                                    <th>Total</th>
                                    </thead>

                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <hr>
                        <button id="saveallsalaries" type="button" class="btn btn-primary">Save Salaries</button>
                        <hr>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="tab-pane fade in " id="salaries">
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption">

                </div>
            </div>
            <div class="portlet-body">
                <table>
                    <thead>

                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>


<script src="<?php echo base_url() ?>assets/pages/hris/salaryinc.js"></script>

<script>
    SALARYINC.init();
</script>