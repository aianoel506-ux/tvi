<?php
$dataid = $this->input->post('ids');
$loansid = $this->input->post('view');


?>
<div class="row">

    <div class="col-md-12" style="padding: 30px 30px;">

        <form action="<?php echo base_url() ?>payroll/addadditionalloans" method="post" id="submitadditionalloans">

            <!-- Modal content-->
            <div class="row">
                <div class="col-md-4">
                    <input type="hidden" name="hiddenloansval" id="hiddenloansval" value="<?php echo $loansid; ?>"/>
                    <input type="hidden" name="hiddenempidloans" id="hiddenempidloans" value="<?php echo $dataid; ?>" />
                    <div class="form-group">
                        <label>Amount</label>
                        <input type="text" name="amountloans" id="amountloans" placeholder="Enter amount here" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label>Month</label>
                        <input type="text" id="monthloans" name="monthloans" class="form-control" />
                    </div>

                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label>For</label>
                        <input type="text" name="monthdevide" id="monthdevide" placeholder="For how many months" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label>Year</label>
                        <select name="yearloans" id="yearloans" class="form-control">
                            <?php
                            $startyear = date('Y');
                            for($startyear=$startyear;$startyear<=4000;$startyear++){
                                echo '<option value="'.$startyear.'">'.$startyear.'</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Amount</label>
                        <input type="text" name="amountpermonth" id="amountpermonth" class="form-control" readonly/>
                    </div>
                    <div class="form-group">
                        <label>Type</label>
                        <select name="typeofloans" id="typeofloans" class="form-control">
                            <?php
                                $gettypeofloans = $this->db->select("sysid,subtype")
                                    ->from("payroll_manual_transactions_subtypes")
                                    ->where(array("status" => 1 , "typesid" => $loansid))
                                    ->get();
                                if($gettypeofloans){
                                    foreach ($gettypeofloans->result() as $row){
                                        echo '<option value="'.$row->sysid.'">'.$row->subtype.'</option>';
                                    }
                                }


                            ?>
                        </select>
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

    PECO.select2Basic($('#monthloans',document),'hris/getpremiummonth','Select month',false,false,month);
    $('#loanspaytype').select2({
        placeholder: 'Select paytype',
        allowClear: true
    });

    $('#yearloans').select2({
        placeholder: 'Select year',
        allowClear: true
    });

    $('#typeofloans').select2({
        placeholder: 'Select Type',
        allowClear: true
    });

    $(document).on('keyup','#amountloans',function (e) {
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
        var amountloans = $(document).find('#amountloans').val();
        var totalpermonth =amountloans /  this_.val();
        if(this_.val() == '' || amountloans == ''){
            $(document).find('#amountpermonth').val('');
        }else{
            $(document).find('#amountpermonth').val(totalpermonth);
        }

    });

</script>
