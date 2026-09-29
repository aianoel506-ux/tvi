<style>
    body{
        margin: 0px 0px;
        padding: 0px 0px;
        background: #fff !important;
        min-height:  500px;
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
<hr class="print-hidden margin-bttom-20">
<?php 
$qry = $this->db->select()->from('person')->where('sysid >= 20')->get();
$i = 0;
foreach($qry->result() as $row) {
    $i += 1;
    $rand_servno = rand(1, 300);
    $a = array("L","M","A","D","J");
    $ar_num = array_rand($a);
    $servno = $a[$ar_num].str_pad($rand_servno, 5, '0', STR_PAD_LEFT);
    $billno = str_pad($i, 8, '0', STR_PAD_LEFT);
    echo '<div class="print-body" style="position: relative;">';
    echo '<span style="position: absolute; float:left; margin-left: 120px !important; margin-top: 105px !important; z-index: 2000;" >'.$servno.'</span>';
    echo '<img style="width: 100%; float: left; position: relative; top: 0px left: 0px;" class="printing" src="'.base_url('assets/global/img/billing_header.png').'" />';
    echo '<div class="container">';
    echo '<div class="col-md-4 pull-right">'.$servno.'</div>';
    echo '<div class="row"><div class="col-md-4">'.$row->lastname.', '.$row->firstname.'</div>';
    echo '</div>';
    echo '<img style="width: 100%; float: left; position: relative; top: 0px left: 0px;" class="printing" src="'.base_url('assets/global/img/sample_billing.png').'" />';
    echo '<h4 class="billno" >Bill No.: '.$billno.'</h4>';
    echo '<img style="width: 100%;: left; position: relative; top: 0px left: 0px; z-index: -1;" class="printing" src="'.base_url('assets/global/img/billing_up_footer.png').'" />';
    echo '<span style="float:left; margin-left: 40px !important; margin-top: -90px !important; z-index: 2000;" >'.$servno.'</span>';
    echo '<h4 class="billno" style="margin-right: 100px !important; margin-top: -20px !important; z-index: 2000;" >Bill No.: '.$billno.'</h4>';
    echo '<img style="width: 100%; position: relative; top: 0px left: 0px; z-index: -1;" class="printing" src="'.base_url('assets/global/img/billing_footer.png').'" />';
    echo '<h4 class="billno" style="margin-right: 100px !important; margin-top: -30px !important; z-index: 2000;" >Bill No.: '.$billno.'</h4>';
    echo '</div>';
    echo '</div><footer></footer>';
    echo '<hr class="print-hidden">';
}
?>