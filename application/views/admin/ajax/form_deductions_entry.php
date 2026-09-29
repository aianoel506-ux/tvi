<?php
$dataid = $this->input->post('ids');
$premiumid = $this->input->post('view');


?>
<div class="row">

    <div class="col-md-12" style="padding: 30px 30px;">

        <form action="<?php echo base_url() ?>payroll/addaddictionalded" method="post" id="submitdeductions">

            <!-- Modal content-->
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Type of Deductions</label>
                        <input type="text" name="deducttype" id="deducttype" placeholder="Select deduction type." class="form-control" />
                    </div>
                </div>
                <div class="col-md-4"></div>
                <div class="col-md-4"></div>
            </div>
            <div class="row">

                <div class="col-md-4">
                    <input type="hidden" name="hiddenempidded" id="hiddenempidded" value="<?php echo $dataid; ?>" />
                    <div class="form-group">
                        <label>Amount</label>
                        <input type="text" name="amountded" id="amountded" placeholder="Enter amount here" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label>Month</label>
                        <input type="text" id="monthded" name="monthded" class="form-control" />
                    </div>

                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label>For</label>
                        <input type="text" name="monthdevide" id="monthdevide" placeholder="For how many months" value="1" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label>Year</label>
                        <select name="yearded" id="yearded" class="form-control">
                            <?php
                            $startyear = 2018;
                            for($startyear=2018;$startyear<=4000;$startyear++){
                                echo '<option value="'.$startyear.'">'.$startyear.'</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Amount Per Month</label>
                        <input type="text" name="amountpermonth" id="amountpermonth" class="form-control" readonly/>
                    </div>

                    <?php



                    ?>

                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Add</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </form>
    </div>
</div>


<script>
    var d = new Date();
    var month = d.getMonth()+1;
    PECO.select2Basic($('#deducttype' , document) , 'hris/getdeductionstype', 'Select deduction type',false,false,false);
    PECO.select2Basic($('#monthded',document),'hris/getpremiummonth','Select month',false,false,month);
    $('#dedpaytype').select2({
        placeholder: 'Select paytype',
        allowClear: true
    });

    $('#yearded').select2({
        placeholder: 'Select year',
        allowClear: true
    });

    $(document).on('keyup','#amountded',function (e) {
        e.preventDefault();
        var this_ = $(this);
        var monthdevide = $(document).find('#monthdevide').val();
        var totalpermonth = this_.val() / monthdevide;

        if(this_.val() == '' || monthdevide == ''){
            $(document).find('#amountpermonth').val('');
        }else{
            $(document).find('#amountpermonth').val(totalpermonth);
        }
    });

    $(document).on('keyup','#monthdevide',function (e) {
        e.preventDefault();
        var this_ = $(this);
        var amountprem = $(document).find('#amountded').val();
        var totalpermonth =amountprem /  this_.val();
        if(this_.val() == '' || amountprem == ''){
            $(document).find('#amountpermonth').val('');
        }else{
            $(document).find('#amountpermonth').val(totalpermonth);
        }

    });
</script>
