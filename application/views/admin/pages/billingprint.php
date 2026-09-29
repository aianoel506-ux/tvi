<style>
    body{
        margin: 0px 0px;
        padding: 0px 0px;
        background: #fff !important;
        min-height:  500px;

    }
    .rep-content {
        padding: 10px 10px;
        display: inline-block;
        min-height: 500px;
    }
    .accountinfo{
        width: 100%;
        height: 50px;
        display: inline-block;
        margin-top: 100px;
    }
    .accountdetails{
        width: 100%;
        height: 50px;
        display: inline-block;
        margin-top: 30px;
    }
    .accountinfo .column-1, .accountinfo .column-2 {
        width: 45%;
        padding-left: 100px;
        display: inline-block;
        vertical-align: top;
    }
    .info-list{
        display: inline-block;
        width: 100%;
    }
    .gdlb {
        width: 10%;
        display: inline-block;
    }
    .servno {
        width: 15%;
        display: inline-block;
    }
    .mn {
        width: 10%;
        display: inline-block;
    }
    .moyr {
        width: 15%;
        display: inline-block;
    }
    .due {
        width: 15%;
        display: inline-block;
    }
    .metering{
        float: right;
        width: 300px;
        display: inline-block;
        margin-right: 30px;
    }
    .charges{
        float: right;
        width: 420px;
        display: inline-block;
        margin-right: 30px;
    }


    .print-btn {
        display: inline-block;
        text-decoration: none;
        float: left;
        position: fixed;
        top: 2px;
        right: 2px;
        z-index: 1000;
    }
    .billno {positiont: absolute; bottom: 0px; right: 0px; float: right;}
    @media print {
        footer {page-break-after: always;}
        .print-hidden {display: none;}
        .print-btn {display: none;}
        .printing {display: inline !important; position: relative; float: left;}
        .billno {positiont: absolute; bottom: 0px; right: 0px; float: right;}
    }
</style>
<a class="btn btn-info btn-xs print-btn" href="javascript:window.print();"><i class="fa fa-print"></i> Print</a>

<?php
if ($readinghist->num_rows() > 0) {
    foreach ($readinghist->result() as $row) {
        $acctid = get_acctinfo_mtr($row->mtrid)->ownerid;
        if ($acctid) {
            $compute = compute_billing($row->sysid, $acctid);
            ?>
            <div class="rep-content">
                <div class="accountinfo">
                    <div class="column-1">
                        <span class="info-list"><?php echo $compute->name; ?></span>
                        <span class="info-list"><?php echo $compute->addr; ?></span>
                    </div>
                    <div class="column-2">
                        <span class="info-list"><?php echo $compute->servno; ?></span>
                    </div>
                </div>
                <div class="accountdetails">
                    <span class="gdlb"><?php echo $compute->gdlb; ?></span>
                    <span class="servno"><?php echo $compute->servno; ?></span>
                    <span class="mn">1</span>
                    <span class="moyr"><?php echo $compute->moyr; ?></span>
                    <span class="due"><?php echo $compute->duedate; ?></span>
                </div>
                <div class="metering"></div>
                <div class="charges"><?php echo $compute->rep; ?>
                    <br>
                    <h3 style="font-size: 9px; font-weight: bold">THIS IS A SYSTEM GENERATED STATEMENT OF ACCOUNT. NO SIGNATURE IS REQUIRED.</H3>
                </div>
                <footer></footer>
            </div>
            <?php
        }
    }
}
?>


