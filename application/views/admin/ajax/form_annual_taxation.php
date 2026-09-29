<?php
/**
 * Created by PhpStorm.
 * User: IT
 * Date: 11/15/2018
 * Time: 11:33 AM
 */
?>
<div class="row" style="margin-left: 15px; margin-right: 15px;">
    <form id="submitannualtax" action="<?php echo base_url() ?>payroll/submitannualtax" method="post">
        <div class="col-md-12">
            <div class="form-group">
                <label>Employee</label>
                <input required type="text" name="employees[]" multiple="multiple" id="employees" class="form-control" />
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label>Amount</label>
                <input required type="text" name="amount" id="amount" class="form-control" />
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label>Type</label>
                <input required type="text" name="typehalf" id="typehalfpay" class="form-control" />
            </div>
        </div>

        <div class="col-md-2">
            <div class="form-group">
                <label>Month</label>
                <input type="text" name="month" id="month" class="form-control" />
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label>Year</label>
                <input type="text" name="year" id="year" class="form-control" />
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <button type="submit" style="margin-top: 23px;" class="btn btn-primary pull-right">Save</button>
            </div>
        </div>
    </form>
</div>

<script>
    var d = new Date();
    var month = d.getMonth()+1;

    PECO.select2Basic($('#month',document),'systems/select2month','Select month',false,false,month);
    PECO.select2Basic($('#year',document),'systems/select2year','Select year',false,false,false);
    PECO.select2Basic($('#typehalfpay',document),'ts/gettypesched','Select Type',false,false);


    $(document).on('keyup','#amount',function (e) {
        e.preventDefault();
        var this_ = $(this);
        var monthdevide = $(document).find('#for').val();
        var totalpermonth = this_.val() / monthdevide;

        if(this_.val() == '' || monthdevide == ''){
            $(document).find('#amountpermonth').val('');
        }else{
            $(document).find('#amountpermonth').val(totalpermonth);
        }
    });

    $(document).on('keyup','#for',function (e) {
        e.preventDefault();
        var this_ = $(this);
        var amountloans = $(document).find('#amount').val();
        var totalpermonth =amountloans /  this_.val();
        if(this_.val() == '' || amountloans == ''){
            $(document).find('#amountpermonth').val('');
        }else{
            $(document).find('#amountpermonth').val(totalpermonth);
        }
    });
    PECO.select2BasicMult($('#employees',document) , 'payroll/getpayrollemployees' ,false);
</script>