<?php

    $bosid =  $this->uri->segment(5);

    $sql = $this->db->select("tbg.year ,tbg.ccid , tbg.types, pcm.desc")->from("trn_bos_group as tbg")
        ->join("prime_costcenter_main as pcm" , "pcm.sysid = tbg.ccid" , "left")
        ->where(array("tbg.sysid" => $bosid))
        ->get()->row();

?>

<div class="invoice-content-2 bordered">
    <div class="row invoice-head">
        <div class="col-md-7 col-xs-6">
            <div class="invoice-logo">
                <img src="../assets/pages/img/logos/logo5.jpg" class="img-responsive" alt="">
                <h1 class="uppercase">Budget List</h1>
            </div>
        </div>
    </div>
    <div class="row invoice-cust-add">
        <div class="col-xs-3">
            <h2 class="invoice-title uppercase">Cost Center</h2>
            <p class="invoice-desc"><?php echo  ($sql) ? $sql->desc : ''; ?></p>
        </div>
        <div class="col-xs-3">
            <h2 class="invoice-title uppercase">Year</h2>
            <p class="invoice-desc"><?php echo  ($sql) ? $sql->year : ''; ?></p>
        </div>
        <div class="col-xs-6">
            <h2 class="invoice-title uppercase">Date Requested</h2>
            <p class="invoice-desc inv-address">Dec. 22, 2018</p>
        </div>
    </div>
    <div class="row invoice-body">
        <div class="col-xs-12 table-responsive">
            <table class="table table-hover" id="budgetapprovaltbl">
                <thead>
                <tr>
                    <th class="invoice-title uppercase"></th>
                    <th class="invoice-title uppercase">#</th>
                    <th class="invoice-title uppercase">Codes</th>
                    <th class="invoice-title uppercase">Description</th>
                    <th class="invoice-title uppercase text-center">Amount</th>
                    <th class="invoice-title uppercase text-center">Items</th>
                    <th class="invoice-title uppercase text-center">Adj. (+)</th>
                    <th class="invoice-title uppercase text-center">Adj. (-)</th>
                    <th class="invoice-title uppercase text-center">Expenses</th>
                    <th class="invoice-title uppercase text-center">Balance</th>
                    <th class="invoice-title uppercase text-center"></th>
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
    <div class="row invoice-subtotal">
        <div class="col-xs-3">
            <h2 class="invoice-title uppercase">Items</h2>
            <p class="invoice-desc" id="totalitems">0.00</p>
        </div>
        <div class="col-xs-3">
            <h2 class="invoice-title uppercase">Total Amt.</h2>
            <p class="invoice-desc" id="totalamount">0.00</p>
        </div>
        <div class="col-xs-3">
            <h2 class="invoice-title uppercase">Total Exp.</h2>
            <p class="invoice-desc grand-total" id="totalexp">0.00</p>
        </div>
        <div class="col-xs-3">
            <h2 class="invoice-title uppercase"> Total Bal.</h2>
            <p class="invoice-desc grand-total" id="totalbal">0.00</p>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <a class="btn btn-lg green-haze hidden-print uppercase print-btn" onclick="javascript:window.print();">Print</a>
        </div>
    </div>
</div>


<script src="<?php echo base_url(); ?>assets/pages/bos/bos.js"></script>
<script>
    BOS.budgetapproval(<?php echo $bosid; ?> ,<?php echo  ($sql) ? $sql->ccid : ''; ?> , <?php echo  ($sql) ? $sql->year : ''; ?> , <?php echo  ($sql) ? $sql->types : ''; ?>);
</script>
