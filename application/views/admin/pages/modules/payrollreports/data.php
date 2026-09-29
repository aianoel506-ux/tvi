<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/jquery-tags-input/jquery.tagsinput.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-markdown/css/bootstrap-markdown.min.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/typeahead/typeahead.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/jquery-tags-input/jquery.tagsinput.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-markdown/css/bootstrap-markdown.min.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/typeahead/typeahead.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/datatables/extensions/Scroller/css/dataTables.scroller.min.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/datatables/extensions/ColReorder/css/dataTables.colReorder.min.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/datatables/extensions/TableTools/css/dataTables.tableTools.min.css"/>


<div class="tabbable-line">
    <ul class="nav nav-tabs ">
        <li class="active">
            <a href="#payrollregister" data-toggle="tab" aria-expanded="false"> Payroll Register </a>
        </li>
        <li class="">
            <a href="#earningregister" data-toggle="tab" aria-expanded="true"> Earning Register</a>
        </li>
        <li class="">
            <a href="#deductionsregister" data-toggle="tab" aria-expanded="false">Deductions Register</a>
        </li>
        <li class="">
            <a href="#overtimeregister" data-toggle="tab" aria-expanded="false">Overtime Register</a>
        </li>
    </ul>
    <div class="tab-content">

        <div class="tab-pane active" id="payrollregister">
            <!--  <button id="printdepttotalpayreg">PRINT DEPARTMENT TOTALS</button> -->
            <button id="printpayreg" class="btn btn-primary btn-xs">PRINT PAYROLL REGISTER</button>
            <button id="printpayregbyemp" class="btn btn-primary btn-xs">PRINT PAYROLL REGISTER BY EMPLOYEE</button>

            <table class="table table-bordered table-condensed table-hover tbl-xs" id="payrollregistertable">
                <thead>
                <tr>
                    <th></th>
                    <th>DEPT CODE</th>
                    <th>GROSS EARNINGS</th>
                    <th>TOTAL DEDN</th>
                    <th>TOTAL NET</th>
                    <th>SSS CONT</th>
                    <th>SSS LOAN</th>
                    <th>HDMF CONT</th>
                    <th>HDMF LOAN</th>
                    <th>PECEWA LOAN</th>
                    <th>COOP LOAN</th>
                    <th>PAGIBIG AD</th>
                    <th>OTHER DEDN</th>
                    <th>HMO DEDN</th>
                    <th>DED A</th>
                    <th>ELECTRIC BILL</th>
                    <th>MEM INS</th>
                    <th>LWOP</th>
                    <th>BASE TAX</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
                <tfoot>
                <tr>
                    <td></td>
                    <td>Total Result</td>
                    <td class="number bold text-info" id="resultgrossearnings"></td>
                    <td class="number bold text-info" id="resulttotaldedn"></td>
                    <td class="number bold text-info" id="resulttotalnet"></td>
                    <td class="number bold text-info" id="resultssscont"></td>
                    <td class="number bold text-info" id="resultsssloan"></td>
                    <td class="number bold text-info" id="resulthdmfcont"></td>
                    <td class="number bold text-info" id="resulthdmfloan"></td>
                    <td class="number bold text-info" id="resultpecewaloan"></td>
                    <td class="number bold text-info" id="resultcooploan"></td>
                    <td class="number bold text-info" id="resultpagibigadd"></td>
                    <td class="number bold text-info" id="resultotherdeduction"></td>
                    <td class="number bold text-info" id="resulthmodedn"></td>
                    <td class="number bold text-info" id="resultdeda"></td>
                    <td class="number bold text-info" id="resultelectricbill"></td>
                    <td class="number bold text-info" id="resultmemins"></td>
                    <td class="number bold text-info" id="resultlwop"></td>
                    <td class="number bold text-info" id="resultbasetax"></td>
                </tr>
                </tfoot>
            </table>
        </div>
        <div class="tab-pane" id="earningregister">
            <button id="printearregdepttotals" class="btn btn-primary btn-xs">PRINT EARNINGS REGISTER</button>
            <button id="printearregbyemp" class="btn btn-primary btn-xs">PRINT EARNINGS REGISTER BY EMPLOYEE</button>
            <table class="table table-bordered table-condensed table-hover tbl-xs" id="earningregistertable">
                <thead>
                <tr>
                    <th></th>
                    <th></th>
                    <th>401</th>
                    <th>410</th>
                    <th>401</th>
                    <th>410</th>
                    <th>401</th>
                    <th>401</th>
                    <th>401</th>
                    <th>401</th>
                    <th>401</th>
                </tr>
                <tr>
                    <th></th>
                    <th>DEPT CODE</th>
                    <th>BASIC RATE</th>
                    <th>COLA</th>
                    <th>TRANS ALLW</th>
                    <th>RICE SUBSI</th>
                    <th>HOLIDAY PAY</th>
                    <th>NITE DIFF</th>
                    <th>OT PAY</th>
                    <th>ACTING ALLW</th>
                    <th>OTHERADD</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
                <tfoot>
                <tr>
                    <td></td>
                    <td>Total Result</td>
                    <td class="number bold text-info" id="totaleaningbasicrate"></td>
                    <td class="number bold text-info" id="totalearningcola"></td>
                    <td class="number bold text-info" id="totalearningtransallw"></td>
                    <td class="number bold text-info" id="totalearningricesubsi"></td>
                    <td class="number bold text-info" id="totalearningholidaypay"></td>
                    <td class="number bold text-info" id="totalearningnitediff"></td>
                    <td class="number bold text-info" id="totalearningotpay"></td>
                    <td class="number bold text-info" id="totalearningactingallw"></td>
                    <td class="number bold text-info" id="totalearningotheradd"></td>
                </tr>
                </tfoot>
            </table>
        </div>
        <div class="tab-pane" id="deductionsregister">
            <button id="printdednregdepttotals" class="btn btn-primary btn-xs">PRINT DEDUCTIONS REGISTER</button>
            <button id="printdednregbyemp" class="btn btn-primary btn-xs">PRINT DEDUCTIONS REGISTER BY EMPLOYEE</button>
            <table class="table borderless table-condensed table-hover tbl-xs" id="deductionsregistertable">
                <thead>
                <tr>
                    <th></th>
                    <th></th>
                    <th>256</th>
                    <th>262</th>
                    <th>260/408/261</th>
                    <th>274</th>
                    <th>264</th>
                    <th>265</th>
                    <th>261</th>
                    <th></th>
                    <th>405</th>
                    <th>178</th>
                    <th>175</th>
                    <th>MEM INS</th>
                    <th>401</th>
                    <th>245</th>
                </tr>
                <tr>
                    <th></th>
                    <th>DEPT CODE</th>
                    <th>SSS CONT</th>
                    <th>SSS LOAN</th>
                    <th>HDMF CONT</th>
                    <th>HDMF LOAN</th>
                    <th>PECEWA LOAN</th>
                    <th>COOP LOAN</th>
                    <th>PAGIBIG AD</th>
                    <th>OTHER DEDN</th>
                    <th>HMO DEDN</th>
                    <th>DED A</th>
                    <th>ELECT BILL</th>
                    <th>MEM INS</th>
                    <th>LWOP</th>
                    <th>BASE TAX</th>
                </tr>
                </thead>
                <tbody>

                </tbody>
                <tfoot>
                <tr>
                    <td ></td>
                    <td >TOTAL RESULT</td>
                    <td class="text-danger number" id="totaldeductionssscont"></td>
                    <td class="text-danger" id="totaldeductionsssloan"></td>
                    <td class="text-danger" id="totaldeductionhdmfcont"></td>
                    <td class="text-danger" id="totaldeductionhdmfloan"></td>
                    <td class="text-danger" id="totaldeductionpecewaloan"></td>
                    <td class="text-danger" id="totaldeductioncooploan"></td>
                    <td class="text-danger" id="totaldeductionpagibigad"></td>
                    <td class="text-danger" id="totaldeductionotherdedn"></td>
                    <td class="text-danger" id="totaldeductionhmodedn"></td>
                    <td class="text-danger" id="totaldeductiondeda"></td>
                    <td class="text-danger" id="totaldeductionelectbill"></td>
                    <td class="text-danger" id="totaldeductionmemins"></td>
                    <td class="text-danger" id="totaldeductionlwop"></td>
                    <td class="text-danger" id="totaldeductionbasetax"></td>
                </tr>
                </tfoot>
            </table>
        </div>
        <div class="tab-pane" id="overtimeregister">
            <button id="printotregdepttotals" class="btn btn-primary btn-xs">PRINT OVERTIME REGISTER</button>
            <button id="printotregbyemp" class="btn btn-primary btn-xs">PRINT OVERTIME REGISTER BY EMPLOYEE</button>
            <table class="table table-bordered table-condensed table-hover tbl-xs" id="overtimeregistertable">
                <thead>
                <tr>
                    <th></th>
                    <th>DEPT CODE</th>
                    <th>NDOT 8 Hrs</th>
                    <th>NDOT 8 Pay</th>
                    <th>OT Hrs</th>
                    <th>125%</th>
                    <th>130%</th>
                    <th>150%</th>
                    <th>160%</th>
                    <th>180%</th>
                    <th>210%</th>
                    <th>230%</th>
                    <th>260%</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
                <tfoot>
                <tr>
                    <th></th>
                    <th>Total Result</th>
                    <th id="totalndothrs">0.00</th>
                    <th id="totalndotpay">0.00</th>
                    <th id="totalothrs">0.00</th>
                    <th id="totalone25">0.00</th>
                    <th id="totalone30">0.00</th>
                    <th id="totalone50">0.00</th>
                    <th id="totalone60">0.00</th>
                    <th id="totalone80">0.00</th>
                    <th id="totaltwo10">0.00</th>
                    <th id="totaltwo30">0.00</th>
                    <th id="totaltwo60">0.00</th>
                </tr>
                </tfoot>

            </table>
        </div>
    </div>
</div>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/moment.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/jquery.mockjax.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-editable/bootstrap-editable/js/bootstrap-editable.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-editable/inputs-ext/address/address.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-editable/inputs-ext/wysihtml5/wysihtml5.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/pages/payroll/report.js"></script>
<script>
    PAYROLLREPORTS.init(<?php echo $dataid; ?>);
</script>