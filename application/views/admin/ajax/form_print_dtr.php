<hr>
<div class="container">
    <div class="row">
        <form id="submitdtr" action="<?php echo base_url() ?>hris/submitdtr" method="post">
            <div class="col-md-2">
                <div class="form-group">
                    <label>Employees</label>
                    <input required type="text" name="employees[]" multiple="multiple" id="employeesdtr" class="form-control" />
                </div>
            </div>
            <div class="col-md-2">
                <label>Month</label>
                <input required type="text" value="<?php echo date('m'); ?>" class="form-control" name="month" id="monthc" />
                <label>Year</label>
                <input required type="text" value="<?php echo date('Y'); ?>" class="form-control" name="year" id="yearc" />
                <br>
                <br>
                <div class="form-check">
                    <input type="checkbox" name="spacing" class="form-check-input" id="spacing">
                    <label class="form-check-label" for="spacing">Spacing</label>
                </div>
            </div>
            <div class="col-md-2">
                <label>From</label>
                <input required type="text" class="form-control" name="from" id="from" />
                <label>To</label>
                <input required type="text" class="form-control" name="to" id="to" />
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary pull-right" style="margin-top: 50px !important;">Generate</button>
            </div>
        </form>
    </div>
</div>
<hr>
<script>
    PECO.select2BasicMult($('#employeesdtr',document) , 'hris/getallemployeesfordtr' ,false);
    PECO.select2Basic($('#monthc',document),'systems/select2month' , 'Select Month',false,false,false);
    PECO.select2Basic($('#yearc',document),'systems/select2year' , 'Select year',false,false,false);
</script>