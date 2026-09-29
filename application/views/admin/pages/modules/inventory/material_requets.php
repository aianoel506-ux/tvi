<?php
?>
<div class="row">

    <div class="col-md-10 col-md-offset-1">
        <div class="portlet light bordered">
            <div class="portlet-header">
                <h1>Request Materials</h1>
            </div>
            <div class="portlet-body">
                <div class="row">
                    <div class="col-md-3"><h3>Request Form</h3>
                        <div class="form-group">
                            <label class="control-label">Item Name / Description</label>
                            <input class="form-control" name="itemname" placeholder="Item name / description" />
                        </div>
                        <div class="form-group">
                            <label class="control-label">Qty</label>
                            <input class="form-control" name="itemqty" placeholder="Qty / Length" />
                        </div>
                        <div class="form-group">
                            <label class="control-label">Remarks</label>
                            <input class="form-control" name="itemrem" placeholder="Remarks" />
                        </div>
                        <div class="form-group">
                            <hr>
                            <button class="btn btn-default">Add</button>
                        </div>
                    </div>
                    <div class="col-md-7"><h3>Request Item(s)</h3>
                        <table class="table table-hover table-striped">
                            <thead>
                            <th>#</th>
                            <th>Item Code</th>
                            <th>Item Descriptions</th>
                            <th>Item Remarks</th>
                            <th>Control</th>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <div class="col-md-2"><h3>Submit</h3>
                        <div class="form-group">
                            <label class="control-label">Client Name</label>
                            <input class="form-control" name="requestclient" placeholder="Name / Company Name" />
                        </div>
                        <div class="form-group">
                            <label class="control-label">Project Name</label>
                            <input class="form-control" name="requestprojectname" placeholder="Project Name" />
                        </div>
                        <div class="form-group">
                            <button class="btn btn-default">Save Draft</button>
                            <button class="btn btn-primary">Submit Request</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

