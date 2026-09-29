<?php

$info = application_info($id);

$appname = ($info) ? $info->appname : 'N/A';
$address = ($info) ? $info->address : 'N/A';
$conntype = ($info) ? $info->conntype : 'N/A';
$totalload = ($info) ? $info->totalload : 'N/A';
$rateclass = ($info) ? $info->rateclass : 'N/A';
$gdlb = ($info) ? $info->gdlb : 'N/A';
$servno = ($info) ? $info->servno : 'N/A';
$origin = ($info) ? $info->origin : 0;
$mats_amt = 0;
$labor_amt = 0;
$linext_amt = 0;
$prop_sharing = 0;
$gdeposit_amt = 0;
$servfee_amt = 0;
$servfeevat_amt = 0;
$laborvat_amt = 0;
$oth_amt = 0;
$old_gd_amt = 0;
$initdeposit_amt = 0;

if (in_array($origin,array(35,188,189))) {
    $old_gd_check = '&#9744';
} else {
    $old_gd_check = '&#128505';
}


$charges_qry = $this->db->select('c.sysid, c.chargeid, a.codes, a.descs, c.amt, c.vatamt, c.vattype, a.groups')
    ->from('application_customers_charges AS c')
    ->join('prime_chart_of_accounts AS a', 'a.sysid = c.chargeid')
    ->where(array('c.appid' => $id, 'c.status' => 1))
    ->group_by('c.sysid, c.chargeid, a.codes, a.descs, c.amt, c.vatamt, c.vattype, a.groups')
    ->order_by('CAST(c.datecreated AS DATE)', 'asc')
    ->order_by('a.groups')
    ->order_by('a.codes')
    ->get();

if ($charges_qry->num_rows() > 0) {
    foreach ($charges_qry->result() as $row) {
        if ($row->chargeid == 162) {
            $gdeposit_amt = $row->amt;
        }
        if ($row->chargeid == 163) {
            $payment_qry = $this->db->select('sysid')
                ->from('transaction_payments_logs')
                ->where(array('dataid' => $id, 'payforacctno' => 163, 'status' => 1))
                ->get()->row();

            if ($payment_qry) {
                $initdeposit_check = '&#128505';
                $initdeposit_amt = $row->amt;
            } else {
                $initdeposit_check = '&#9744';
            }
        }
    }
}

$net_amt = array_sum(array($mats_amt,$labor_amt,$linext_amt,$prop_sharing,$gdeposit_amt,$servfee_amt,$servfeevat_amt,$laborvat_amt,$oth_amt,$old_gd_amt,-$initdeposit_amt));
?>

<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Summary of Cost</title>
    <style>
        td {
            padding-left: 5px;
            padding-right: 5px;
        }
        p {
            padding: 5px;
            margin: 0;
        }
        table {
            border-collapse: collapse;
			border-color: #F0572D;
        }
    </style>
</head>

<body style="font-family: Arial; color: #1B607F; font-weight: bold; font-size: 10pt;">
<?php
/*echo '<pre>';
print_r($info);
echo '</pre>';*/
?>
<p align="center" style="text-align: center; color: #1B607F">
<h2 style="text-align: center; margin-bottom: 0; padding-bottom: 0">Customer  Applications Department</h2>
<span style="text-align: center; position: absolute; width: 100%;">
		No. 12 General Luna  St., Iloilo City
</span>
</p>
<p></p>
<p></p>
<p align="center">
    <span style="left: 43%; position: absolute; font-size: 12pt">SUMMARY OF COST</span>
    <span style="float: right; color: #F0572D;">Date: <span style="width: 40px; border-bottom: 1px solid #F0572D; color: #1B607F"><?php echo date("F j, Y");?></span></span>
</p>
<br>
<table width="100%" border="1" cellspacing="0" cellpadding="0" bordercolor="#F0572D">
    <tr>
        <td width="100%" colspan="3" valign="top" style="border-bottom: none; color: #F0572D;">NAME:</td>
    </tr>
    <tr>
        <td colspan="3" align="center" valign="top" style="border-top: none"><?php echo strtoupper($appname);?></td>
    </tr>
    <tr>
        <td width="100%" colspan="3" valign="top" style="border-bottom: none; color: #F0572D;">ADDRESS:</td>
    </tr>
    <tr>
        <td colspan="3" align="center" valign="top" style="border-top: none;"><?php echo strtoupper($address);?></td>
    </tr>
    <tr>
        <td width="50%" style="border-bottom: none; color: #F0572D;">Requested Service:</td>
        <td rowspan="2" width="25%" align="center" valign="middle" style="border-bottom: none; border-right: none; color: #F0572D"></td>
        <td rowspan="2" width="25%" valign="middle" style="border-bottom: none; border-left: none; color: #F0572D">SUMMARY OF COST</td>
    </tr>
    <tr>
        <td align="center" valign="top" style="border-bottom: none; border-top: none"><?php echo $conntype;?></td>
    </tr>
</table>
<table width="100%" border="1" cellspacing="0" cellpadding="0" bordercolor="#F0572D">
    <tr>
        <td width="70%" colspan="2"><p>1.) COST OF MATERIALS CHARGEABLE TO CUSTOMER (VAT INCLUSIVE)</p></td>
        <td><p align="right"><?php echo number_format($mats_amt,2);?></p></td>
    </tr>
    <tr>
        <td width="70%" colspan="2"><p>2.) COST OF LABOR CHARGABLE TO THE CUSTOMER</p></td>
        <td><p align="right"><?php echo  number_format($labor_amt,2);?></p></td>
    </tr>
    <tr>
        <td width="70%" colspan="2"><p>3.) PROPORTIONATE SHARING OF LINE EXTENSIONS COST</p></td>
        <td><p align="right"><?php echo  number_format($prop_sharing,2);?></p></td>
    </tr>
    <tr>
        <td width="70%" colspan="2"><p>4.) GUARANTY DEPOSIT <span style="color: #F0572D; font-size: 5pt; vertical-align: middle">(Estimated on month bill based    on the contracted load. Refundable when contract ceases.)</span></p></td>
        <td><p align="right"><?php echo  number_format($gdeposit_amt,2);?></p></td>
    </tr>
    <tr>
        <td width="70%" colspan="2"><p>5.) SERVICE FEE</p></td>
        <td><p align="right"><?php echo  number_format($servfee_amt,2);?></p></td>
    </tr>
    <tr>
        <td width="70%" colspan="2"><p>6.) VAT ON SERVICE FEE</p></td>
        <td><p align="right"><?php echo  number_format($servfeevat_amt,2);?></p></td>
    </tr>
    <tr>
        <td width="70%" colspan="2"><p>7.) VAT ON LABOR</p></td>
        <td><p align="right"><?php echo  number_format($laborvat_amt,2);?></p></td>
    </tr>
    <tr>
        <td width="541" colspan="2"><p>8.) OTHERS</p></td>
        <td width="178"><p align="right"><?php echo  number_format($oth_amt,2);?></p></td>
    </tr>
    <tr>
        <td width="54" rowspan="2"><p>LESS:</p></td>
        <td width="487"><p>(<?php echo $old_gd_check; ?> FOR OLD CUSTOMERS ONLY) – OLD GUARANTY DEPOSIT</p></td>
        <td width="178"><p align="right"><?php echo  number_format($old_gd_amt,2);?></p></td>
    </tr>
    <tr>
        <td width="487"><p>(<?php echo $initdeposit_check; ?> INITIAL DEPOSIT)</p></td>
        <td width="178"><p align="right"><?php echo  number_format($initdeposit_amt,2);?></p></td>
    </tr>
    <tr>
        <td width="541" colspan="2" rowspan="2" style=""></td>
        <td width="178" valign="top" style="border-bottom: none;">Net. Amount</td>
    </tr>
    <tr>
        <td width="178" align="right" valign="top" style="border-top: none;"><p><?php echo  number_format($net_amt,2);?></p></td>
    </tr>
</table>
<h3 style="text-align: center; margin-bottom: 0; padding-bottom: 0">COMPUTATION OF THE GUARANTY DEPOSIT</h3>
<p><span style="color: #F0572D">FORMULA:</span><br>
    Guaranty Deposit = Total Load X Daily Operation X Current Rate X  Demand Factor / 1000</p>
<p style="color: #F0572D">Where:</p>
<table width="80%" border="1" cellspacing="0" cellpadding="0" bordercolor="#F0572D">
    <tr>
        <td width="40%" rowspan="2">Total Load </td>
        <td width="20%" colspan="2" valign="top" style="color: #F0572D; font-size: 7pt; border-bottom: none;">watts</td>
        <td width="20%" valign="top" style="color: #F0572D; font-size: 7pt; border-bottom: none;">Date Paid</td>
        <td width="20%" rowspan="2" align="center" valign="middle">Amount Paid</td>
    </tr>
    <tr>
        <td width="20%" colspan="2" align="center" valign="top" style="border-top: none;"><?php echo $totalload;?></td>
        <td width="20%" align="center" valign="top" style="border-top: none;">12/24/2020</td>
    </tr>
    <tr>
        <td width="40%" rowspan="2">Estimated Daily Operation</td>
        <td width="20%" colspan="2" valign="top" style="color: #F0572D; font-size: 7pt; border-bottom: none;">hours</td>
        <td width="20%" valign="top" style="color: #F0572D; font-size: 7pt; border-bottom: none;">OR No.</td>
        <td width="20%" valign="top" style="color: #F0572D; font-size: 7pt; border-bottom: none;">Other Charges</td>
    </tr>
    <tr>
        <td width="20%" colspan="2" align="center" valign="top" style="border-top: none;">10</td>
        <td width="20%" align="center" valign="top" style="border-top: none;">999999</td>
        <td width="20%" align="center" valign="top" style="border-top: none;">12,345.67</td>
    </tr>
    <tr>
        <td width="40%" rowspan="2">Estimated Monthly Operation</td>
        <td width="20%" colspan="2" valign="top" style="color: #F0572D; font-size: 7pt; border-bottom: none;">days</td>
        <td width="20%" valign="top" style="color: #F0572D; font-size: 7pt; border-bottom: none;">OR No.</td>
        <td width="20%" valign="top" style="color: #F0572D; font-size: 7pt; border-bottom: none;">Guaranty Deposit</td>
    </tr>
    <tr>
        <td width="20%" colspan="2" align="center" valign="top" style="border-top: none;">30</td>
        <td width="20%" align="center" valign="top" style="border-top: none;">999999</td>
        <td width="20%" align="center" valign="top" style="border-top: none;">12,345.67</td>
    </tr>
    <tr>
        <td width="40%" rowspan="2">Current Average Rate</td>
        <td width="20%" colspan="2" valign="top" style="color: #F0572D; font-size: 7pt; border-bottom: none;">Pesos</td>
        <td width="20%" valign="top" style="color: #F0572D; font-size: 7pt; border-bottom: none;">Lot / Book</td>
        <td width="20%" valign="top" style="color: #F0572D; font-size: 7pt; border-bottom: none;">Service No.</td>
    </tr>
    <tr>
        <td width="20%" colspan="2" align="center" valign="top" style="border-top: none;">8,125.9</td>
        <td width="20%" align="center" valign="top" style="border-top: none;"><?php echo $gdlb;?></td>
        <td width="20%" align="center" valign="top" style="border-top: none;"><?php echo $servno;?></td>
    </tr>
</table>
<table width="64%" border="1" cellspacing="0" cellpadding="0" bordercolor="#F0572D">
    <tr>
        <td width="50%" rowspan="2" style="border-top: 0px">Demand Factor</td>
        <td width="15%" rowspan="2" align="center" valign="middle" style="border-top: none; color: #F0572D">30</td>
        <td width="35%" colspan="2" valign="top" style="color: #F0572D; font-size: 7pt; border-top: none; border-bottom: none;">Rate Classification:</td>
    </tr>
    <tr>
        <td width="35%" colspan="2" align="center" valign="top" style="border-top: none;"><?php echo $rateclass;?></td>
    </tr>
</table>
<br>
<br>
<br>
<br>
<br>
	<table width="100%" cellpadding="0" cellspacing="0" style="border: none" align="center">
  <tbody>
    <tr>
      <td width="15%">Prepared by:</td>
      <td width="30%">&nbsp;</td>
      <td width="5%">&nbsp;</td>
      <td width="15%">Checked by:</td>
      <td width="30%">&nbsp;</td>
    </tr>
    <tr>
      <td align="center">&nbsp;</td>
      <td align="center">ANNIE A. ABELLO</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td align="center" valign="middle">MARCELO U. CACHO</td>
    </tr>
    <tr>
      <td align="center" valign="middle">&nbsp;</td>
      <td align="center" valign="middle" style="border-top: 1px solid #1B607F">CUSTOMER APPLICATIONS</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td align="center" valign="middle" style="border-top: 1px solid #1B607F">ADMINISTRATIVE MANAGER</td>
    </tr>
    <tr>
      <td align="center" valign="middle">&nbsp;</td>
      <td align="center" valign="middle">DEPARTMENT HEAD</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td align="center" valign="middle">Tel No. 500-4290</td>
    </tr>
    <tr>
      <td align="center" valign="middle">&nbsp;</td>
      <td align="center" valign="middle">Tel. No. 500-4290 Local 120</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td align="center" valign="middle">&nbsp;</td>
    </tr>
  </tbody>
</table>
</body>
</html>

