<?php
$dataid = $this->input->post('ids');
$premiumid = $this->input->post('view');


?>
<div class="row">

    <div class="col-md-12" style="padding: 30px 30px;">

        <form action="<?php echo base_url() ?>payroll/addaddictionalprem" method="post" id="submitpremiums">

            <!-- Modal content-->
            <div class="row">
                <div class="col-md-4">
                        <input type="hidden" name="hiddenpremval" id="hiddenpremval" value="<?php echo $premiumid; ?>"/>
                        <input type="hidden" name="hiddenempidprem" id="hiddenempidprem" value="<?php echo $dataid; ?>" />
                    <div class="form-group">
                        <label>Amount</label>
                        <input type="text" name="amountprem" id="amountprem" placeholder="Enter amount here" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label>Month</label>
                        <input type="text" id="monthprem" name="monthprem" class="form-control" />
                    </div>

                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label>For</label>
                        <input type="text" name="monthdevide" id="monthdevide" placeholder="For how many months" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label>Year</label>
                        <select name="yearprem" id="yearprem" class="form-control">
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
    PECO.select2Basic($('#monthprem',document),'hris/getpremiummonth','Select month',false,false,month);
    $('#prempaytype').select2({
       placeholder: 'Select paytype',
       allowClear: true
   });

   $('#yearprem').select2({
       placeholder: 'Select year',
       allowClear: true
   });

    $(document).on('keyup','#amountprem',function (e) {
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
        var amountprem = $(document).find('#amountprem').val();
        var totalpermonth =amountprem /  this_.val();
        if(this_.val() == '' || amountprem == ''){
            $(document).find('#amountpermonth').val('');
        }else{
            $(document).find('#amountpermonth').val(totalpermonth);
        }

    });
</script>
