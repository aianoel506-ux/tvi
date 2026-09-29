<?php
$appinfo = application_info($appid);

//$nf = new NumberFormatter('en_US', NumberFormatter::ORDINAL);

$day_ord = ordinal(date('d'));
$cont_month = date('F');
$cont_year = date('Y');
?>
<style>
    *, html {
        font-size: 12px !important;
    }
    table.details th, table.details td {
        border: 1px solid #000;
        height: 20px;
    }
    li {
        text-align: justify;
        text-justify: inter-word;
    }

    ol.list li{
        margin-bottom: 15px;
    }

    ol.sublist li{
        margin-bottom: 0px;
    }
</style>


    <div style="width: 100%; display: inline-block; position: absolute;">
        <div class="header">
            <?php echo system_print_header('','CONTRACT FOR ELECTRIC CURRENT SERVICE','',true, false);?>
        </div>

        <br>
        <br>
        <div style="width: 50%; display: inline-block; vertical-align: top;">
            <div style="position: relative; width: 100%; display: block;">
                <p style="position: absolute; left: 0px; top: 3px;">Consumer: </p>
                <span style="position: absolute; font-weight: bold; left: 70px; top: -22px; padding: 5px 5px; right: 0px; display: block; border: none; border-bottom: 1px solid #000; margin-top: 20px; height: 10px;"><?php echo strtoupper($appinfo->appname); ?></span>
            </div>
            <div style="position: relative; width: 100%; display: block;">
                <p style="display: inline-block; margin-top: 25px !important;">Connection/Billing Address: </p>
                <span style="width: 100%; font-weight: bold; display: block; top: -22px; border: none; border-bottom: 1px solid #000; margin-top: 0px; height: 15px;"><?php echo strtoupper($appinfo->address); ?></span>
            </div>
            <div style="position: relative; width: 100%; display: block; margin-bottom: 30px;">
                <p style="display: inline-block; margin-top: 0px !important;">Rate Classification/Schedule: </p>
                <span style="position: absolute; font-weight: bold; left: 140px; top: -20px; padding: 5px 5px; right: 0px; display: block; border: none; border-bottom: 1px solid #000; margin-top: 20px; height: 10px;"><?php echo strtoupper($appinfo->rateclass); ?></span>
            </div>
        </div>

        <div style="width: 40%; font-size: 12px !important; display: inline-block; vertical-align: top; float: right">
            <div style="position: relative; width: 100%; display: block;">
                <p style="position: absolute; left: 10px; top: 3px;">Service No: </p>
                <span style="position: absolute; font-weight: bold; left: 70px; top: -22px; padding: 5px 5px; right: 0px; display: block; border: none; border-bottom: 1px solid #000; margin-top: 20px; height: 10px;"><?php echo strtoupper($appinfo->servno); ?></span>
            </div>
            <div style="position: relative; width: 100%; display: block; top: 30px;">
                <p style="display: inline-block; margin-left: 10px !important;">Lot No.:</p>
                <span style="position: absolute; font-weight: bold; left: 60px; width: 70px; top: -20px; padding: 5px 5px; right: 0px; display: block; border: none; border-bottom: 1px solid #000; margin-top: 20px; height: 10px;">000</span>
                <span style="position: absolute; font-weight: bold; left: 140px; width: 80px; top: -20px; padding: 5px 5px; right: 0px; display: block; border: none; margin-top: 20px; height: 10px;">Book No.: </span>
                <span style="position: absolute; font-weight: bold; left: 200px; width: 80px; top: -20px; padding: 5px 5px; right: 0px; display: block; border: none; border-bottom: 1px solid #000; margin-top: 20px; height: 10px;">000</span>
            </div>
            <div style="position: relative; width: 100%; display: block; top: 20px;">
                <p style="position: absolute; left: 10px; top: 3px;">Deposit Receipt No.:</p>
                <span style="position: absolute; font-weight: bold; left: 110px; top: -22px; padding: 5px 5px; right: 0px; display: block; border: none; border-bottom: 1px solid #000; margin-top: 20px; height: 10px;"><?php echo strtoupper($appinfo->servno); ?></span>
            </div>
        </div>

    <div>

        <table class="details" width="100%" cellpadding="5" cellspacing="0">
            <thead>
            <tr>

                <th >No. of Units</th>
                <th >Capacity of Lights or other Apparatus</th>
                <th >Total Watts</th>
                <th >Amount per Month</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td></td>
                <td align="center">ESSR#: <?php echo strtoupper($appinfo->essrno); ?></td>
                <td align="center"></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td align="center"><?php echo strtoupper($appinfo->rateclass); ?></td>
                <td align="center"><?php echo number_format($appinfo->totalload); ?></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td align="center"></td>
                <td align="center"></td>
                <td></td>
            </tr>
            </tbody>
        </table>


        <p style="text-wrap: normal; display: inline-block; width: 100%;">
            AGREEMENT entered into this <span style="display: inline-block; width: 40px; text-align:center; border-bottom: 1px solid #000; font-weight: bold;"><?php echo $day_ord; ?></span> day of  <span style="display: inline-block; width: 60px; text-align:center; border-bottom: 1px solid #000; font-weight: bold;"><?php echo $cont_month; ?></span>, <span style="display: inline-block; width: 40px; text-align:center; border-bottom: 1px solid #000; font-weight: bold;"><?php echo $cont_year;?></span> between the PANAY ELECTRIC COMPANY, INC., hereinafter referred to as the COMPANY and the consumer named above hereinafter referred to as the CONSUMER.
        </p>
        <ol class="list" style="margin: 0px 0px;  padding: 0px 12px; text-wrap: normal; display: block; width: 100%;">
            <p style="margin-left: -12px !important;">THE COMPANY AGREES:</p>
            <li style=" ">
                To provide electric current service to the Consumer's installation located at the connection/billing address above during the period of this Contract, under the conditions stated in its Electric Service Rate Schedule <span style="display: inline; width: 100px; text-align: center; border-bottom: 1px solid #000; font-weight: bold;"><?php echo strtoupper($appinfo->rateclass); ?></span> the total wattage of which shall not exceed  <span style="display: inline; width: 40px; text-align:center; border-bottom: 1px solid #000; font-weight: bold;"><?php echo number_format($appinfo->totalload); ?></span> watts, provided that the wiring installation and electrical equipment are in satisfactory condition to the Company and complies with the standards set by the Building Code as approved by the City Building Official.
            </li>
            <p style="margin-left: -12px !important;">THE CONSUMER AGREES:</p>
           <li style="">
                To pay the corresponding charges at the current rate for the following services:
                <ol class="sublist" type="a">
                   <li style="">New Connections</li>
                   <li style="">Reconnections</li>
                   <li style="">Change of meter's location on Consumer's request</li>
                   <li style="">Transfer of service and meter on Consumer's request</li>
                   <li style="">Testing and/or replacement of meter on Consumer's request</li>
                   <li style="">Change of type of service</li>
                   <li style="">Engineering works and services on Consumer's request</li>
                </ol>
            </li>
           <li style="">
                To pay monthly to the Company, within nine (9) days after presentation of bill, unless otherwise provided for by law, for electric service furnished by the Company at the rates stipulated and under the conditions stated in its Electric Service Rate Schedule <span style="display: inline; width: 100px; text-align:center; border-bottom: 1px solid #000; font-weight: bold;"><?php echo strtoupper($appinfo->rateclass); ?></span> a copy of which has been furnished the Consumer. For failure of the Consumer to pay the bill within the period herein fixed, a surcharge shall be collected equal to 2% (exclusive of VAT) of the unpaid amount of the bill for every month or fraction thereof that the bill remains unpaid. The word ”month” as used herein is  hereby defined to be elapsed time between two (2) succeeding meter readings, at least twenty-eight (28) to thirty-one (31) days apart. In the event of stoppage, or the failure of any meter to register the full amount of energy consumed, the Consumer shall be billed for such period on an estimated consumption based upon his use of energy in a similar period of like use based on applicable rules set by the ERC as amended from time to time.
                <br>
                <br>
                In case of payment in checks that bounced, the Consumer shall pay 2% surcharge for every month or a fraction thereof and 5% attorney's fees exclusive of VAT.  The redemption of checks shall be in cash and no partial redemption shall be allowed.
            </li>
           <li style="">
                To deposit with the Company an amount equivalent to the value of the estimated monthly billing at the current applicable rate class as specified above provided that when the subsequent monthly bills are more than the estimated amount such deposit shall be correspondingly increased to the approximate of the Consumer's monthly billing. Such deposit shall guarantee the prompt payment of bills of the Consumer or of his lessees or occupants. In case of delay or failure of payment, the Company reserves the right to apply the said deposit or so much thereof to the balance of the account without prejudice to other legal remedies which the Company may have against the Consumer for the collection of the delinquency.  In the event of damage or loss of kWh meter through misuse or negligence on the part of the Consumer or his employees or occupants or household members, the cost of the necessary repairs or replacement of the meter or other equipment shall be paid to the Company by the Consumer.
            </li>
           <li style="">
                That the meters, wires, materials and appliances installed at the Company's expense at or in the Consumer's premises belong to and remain the properties of the Company and may be removed, replaced, and or their installation moved by the Company at any time without notice.
            </li>
           <li style="">
                To maintain his installation in proper condition during the period of its connection with the lines of the Company. To make no additions or changes in his installation affecting his total wattage contracted for without the knowledge and consent of the Company that may cause the Company's meter to be overloaded or otherwise damaged. In case of such unauthorized addition, change, overloading, grounding negligence and other causes within the control of the Consumer, the latter shall entitle the Company to confiscate the bulbs, wires and other materials used in the violation. The Company shall be entitled to collect from the Consumer unbilled income which otherwise would have been earned and collected if not for said unauthorized acts.
            </li>
           <li style="page-break-before: always;">
                The employees and/or representatives of the Company are hereby given permission by the Consumer to enter his premises without being liable for trespass to dwelling for the purpose of inspecting, installing reading, removing, testing, replacing, or otherwise disposing of its apparatus and property, and/or removing the Company's entire property in the event of the termination of the Contract for any cause.
            </li>
           <li style="">
                That the Company reserves the right to disconnect its service for any of the following causes: (a) for repairs; (b) for want of supply; (c) for cancellation of rights of way of Company's lines serving the Consumer; (d) for non-payment of bills when due and proper warning had been given; (e) for non-payment of damages to Company's properties for which Consumer is liable; (f) for fraudulent use of current; (g) for expired promissory notes; (h) for non-payment of re-imposed or adjusted bill deposit including failure to pay the adjusted bills in those cases where the meter stopped or failed to register the amount of energy consumed; (i) for refusal without any justifiable reason, to allow the Company's representatives entry into the premises to effect the relocation; (j) for failure to pay any obligation stipulated in an undertaking or promissory note; and (k) for violation of any condition of this contract or of any of the terms and conditions of the standard rules and regulations of the Energy Regulatory Commission by the Consumer. Such disconnection, however, shall be without prejudice to other legal remedies which the Company may have against the Consumer, and no delay by the Company in enforcing any of its rights shall be deemed a waiver of such rights, nor shall a waiver by the Company of one Consumer's default be deemed a waiver of any other or subsequent defaults. In the case of arrears in the payment of bills or non-payment of the adjusted bills, the Company may discontinue the service notwithstanding the existence of the Consumer's deposit with the Company which will serve as guarantee for the payment of future bills after service is reconnected upon payment by the Consumer of his obligation with the Company.
            </li>
           <li style="">
                The Company shall use reasonable diligence in furnishing a regular and uninterrupted supply of energy but in case such supply should be interrupted or fail by reason of an act of God, the public enemy, accidents, strikes, riots, legal process, Provincial or Municipal interferences, breakdown or damage to the distribution lines of the Company, or extra-ordinary repairs, the Company shall not be liable for damages. The Company shall not be liable to the Consumer for any loss, injury or damage resulting from the Consumer's use of his equipment or from the connections of the Company's wires with the Consumer's wires and appliances.
            </li>
           <li style="">
                The Consumer agrees not to engage in any form of illegal use of electricity such as but not limited to bypassing of meter, illegal connection and tampering of meter and seals and shall be held liable for the same in accordance with law. All implements used in the illegal use of electricity shall be confiscated. The Consumer agrees that no one except the employees of the Company showing their proper identification card shall be allowed to make any external adjustments of any meter or any other piece of apparatus owned by and belonging to the Company.
            </li>
           <li style="">
                That in case of commercial building and or residential buildings leased and or occupied by other persons, both the owner of the building and the occupant thereof shall be signatories to this contract and shall be jointly and severally liable for the bills and for damages in case of breach hereof.
            </li>
           <li style="">
                To pay the Company in case of breach of this contract or government rules, aside from the principal and surcharges, an amount equivalent to twenty-five percent (25%) of the total amount due as attorney's fee, aside from cost, whenever the account is handed to an attorney for collection or enforcement.
            </li>
           <li style="">
                That finally both the Company and the Consumer agree to abide by all the terms and conditions specified in this Contract and any Contract or Agreement made before this date by any employee or agent of the Company shall not be binding on both parties except those that concur with the terms of this Contract.
            </li>
        </ol>

    </div>


        <br>
        <p>
            I hereby request and consent to have my watthour meter mounted on the nearest pole and I am willing to assume responsibility for the integrity of its connection and well-being of the instrument and its seals.
        </p>
        <br>
        <p>
            In case of dispute, the venue of suit shall be the courts of Iloilo City only to the exclusion of other courts.
        </p>
        <br>
        <p>
            Done at Iloilo City, Philippines on the date herein above-written.
        </p>
        <br>
        <p>
            PANAY ELECTRIC COMPANY, INC.
        </p>
        <br>
        <br>


        <div style="width: 40%; display: inline-block; vertical-align: top;">
            <div style="position: relative; width: 100%; display: block;">
                <p style="position: absolute; left: 0px; top: 3px;">By: </p>
                <span style="position: absolute; font-weight: bold; left: 20px; top: -22px; padding: 5px 5px; right: 0px; display: block; border: none; border-bottom: 1px solid #000; margin-top: 20px; height: 10px;"></span>
                <span style="position: absolute; text-align: center; font-weight: bold; left: 10px; top: -5px; padding: 5px 5px; right: 0px; display: block; border: none; margin-top: 20px; height: 10px;">I AGREE TO BE SURETY OF THE CONSUMER:</span>
            </div>

            <br>
            <br>
            <div style="position: relative; width: 100%; display: block;">
                <span style="position: absolute; text-align: center; font-weight: bold; left: 10px; top: 10px; padding: 5px 5px; right: 0px; display: block; border: none; border-bottom: 1px solid #000; margin-top: 20px; height: 10px;"></span>
            </div>

            <div style="position: relative; width: 100%; display: block;">
                <span style="position: absolute; text-align: center; font-weight: bold; left: 10px; top: 30px; padding: 5px 5px; right: 0px; display: block; border: none; margin-top: 20px; height: 10px;">Consumer’s Signature</span>
            </div>

            <br>
            <br>
            <br>
            <br>
            <div style="position: relative; width: 100%; display: block; margin-top: 20px;">
                <p style="position: absolute; left: 0px; top: 3px;">Res. Cert No. A- </p>
                <span style="position: absolute; font-weight: bold; left: 80px; top: -22px; padding: 5px 5px; right: 0px; display: block; border: none; border-bottom: 1px solid #000; margin-top: 20px; height: 10px;"></span>
            </div>
            <br>
            <div style="position: relative; width: 100%; display: block; margin-top: 20px;">
                <p style="position: absolute; left: 0px; top: 5px;">Issued at </p>
                <p style="position: absolute; left: 0px; top: 5px;">Issued on</p>
                <span style="position: absolute; font-weight: bold; left: 48px; top: -22px; padding: 5px 5px; right: 0px; display: block; border: none; border-bottom: 1px solid #000; margin-top: 20px; height: 10px;"></span>
                <span style="position: absolute; font-weight: bold; left: 48px; top: -8px; padding: 5px 5px; right: 0px; display: block; border: none; border-bottom: 1px solid #000; margin-top: 20px; height: 10px;"></span>
            </div>
            <br>
            <br>
            <br>
            <div style="position: relative; width: 100%; display: block; margin-top: 20px;">
                <p style="position: absolute; left: 0px; top: 5px;">Govt. Issued ID: </p>
                <p style="position: absolute; left: 0px; top: 5px;">Issued On: </p>
                <p style="position: absolute; left: 0px; top: 18px;">Valid Until: </p>
                <span style="position: absolute; font-weight: bold; left: 80px; top: -20px; padding: 5px 5px; right: 0px; display: block; border: none; border-bottom: 1px solid #000; margin-top: 20px; height: 10px;"></span>
                <span style="position: absolute; font-weight: bold; left: 48px; top: -10px; padding: 5px 5px; right: 0px; display: block; border: none; border-bottom: 1px solid #000; margin-top: 20px; height: 10px;"></span>
                <span style="position: absolute; font-weight: bold; left: 54px; top: 2px; padding: 5px 5px; right: 0px; display: block; border: none; border-bottom: 1px solid #000; margin-top: 20px; height: 10px;"></span>
            </div>
        </div>

        <div style="width: 40%; font-size: 12px !important; display: inline-block; vertical-align: top; float: right">
            <div style="position: relative; width: 100%; display: block; top: 5px">
                <span style="position: absolute; text-align: center; font-weight: bold; left: 10px; top: -22px; padding: 5px 5px; right: 0px; display: block; border: none; border-bottom: 1px solid #000; margin-top: 20px; height: 10px;"><?php echo strtoupper($appinfo->appname); ?></span>
            </div>
            <div style="position: relative; width: 100%; display: block;">
                <span style="position: absolute; text-align: center; font-weight: bold; left: 10px; top: -5px; padding: 5px 5px; right: 0px; display: block; border: none; margin-top: 20px; height: 10px;">(Signature over Printed Name)</span>
            </div>
            <br>
            <br>
            <div style="position: relative; width: 100%; display: block;">
                <span style="position: absolute; text-align: center; font-weight: bold; left: 10px; top: 10px; padding: 5px 5px; right: 0px; display: block; border: none; border-bottom: 1px solid #000; margin-top: 20px; height: 10px;"></span>
            </div>
            <div style="position: relative; width: 100%; display: block;">
                <span style="position: absolute; text-align: center; font-weight: bold; left: 10px; top: 30px; padding: 5px 5px; right: 0px; display: block; border: none; margin-top: 20px; height: 10px;">Consumer’s Signature</span>
            </div>

            <br>
            <br>
            <br>
            <br>
            <div style="position: relative; width: 100%; display: block; margin-top: 20px;">
                <p style="position: absolute; left: 0px; top: 3px;">Res. Cert No. A- </p>
                <span style="position: absolute; font-weight: bold; left: 80px; top: -22px; padding: 5px 5px; right: 0px; display: block; border: none; border-bottom: 1px solid #000; margin-top: 20px; height: 10px;"></span>
            </div>
            <br>
            <div style="position: relative; width: 100%; display: block; margin-top: 20px;">
                <p style="position: absolute; left: 0px; top: 5px;">Issued at </p>
                <p style="position: absolute; left: 0px; top: 5px;">Issued on</p>
                <span style="position: absolute; font-weight: bold; left: 48px; top: -22px; padding: 5px 5px; right: 0px; display: block; border: none; border-bottom: 1px solid #000; margin-top: 20px; height: 10px;"></span>
                <span style="position: absolute; font-weight: bold; left: 48px; top: -8px; padding: 5px 5px; right: 0px; display: block; border: none; border-bottom: 1px solid #000; margin-top: 20px; height: 10px;"></span>
            </div>
            <br>
            <br>
            <br>
            <div style="position: relative; width: 100%; display: block; margin-top: 20px;">
                <p style="position: absolute; left: 0px; top: 5px;">Govt. Issued ID: </p>
                <p style="position: absolute; left: 0px; top: 5px;">Issued On: </p>
                <p style="position: absolute; left: 0px; top: 18px;">Valid Until: </p>
                <span style="position: absolute; font-weight: bold; left: 80px; top: -20px; padding: 5px 5px; right: 0px; display: block; border: none; border-bottom: 1px solid #000; margin-top: 20px; height: 10px;"></span>
                <span style="position: absolute; font-weight: bold; left: 48px; top: -10px; padding: 5px 5px; right: 0px; display: block; border: none; border-bottom: 1px solid #000; margin-top: 20px; height: 10px;"></span>
                <span style="position: absolute; font-weight: bold; left: 54px; top: 2px; padding: 5px 5px; right: 0px; display: block; border: none; border-bottom: 1px solid #000; margin-top: 20px; height: 10px;"></span>
            </div>
        </div>

        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
    <div style="width: 100%; display: block; vertical-align: top; margin-top: 30px;">

    <table width="100%" cellspacing="5" cellpadding="5">
            <thead>
            <th style="border-bottom: 1px solid #000; width: 30%;"></th>
            <th style="text-align: center;">SIGNED IN THE PRESENCE OF:</th>
            <th style="border-bottom: 1px solid #000; width: 30%;"></th>
            </thead>
        </table>
    </div>
    </div>
<?php
// echo '<pre>';
// print_r($appinfo);