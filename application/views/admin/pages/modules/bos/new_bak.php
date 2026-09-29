<!-- TESTING --->
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/jquery-tags-input/jquery.tagsinput.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-markdown/css/bootstrap-markdown.min.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/typeahead/typeahead.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/jquery-tags-input/jquery.tagsinput.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-markdown/css/bootstrap-markdown.min.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/typeahead/typeahead.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/datatables/dataTables.bootstrap.css">




        <div class="portlet box tabbed table">
            <div class="portlet-title">
                <div class="caption" style="color: #000">
                    <i class="fa fa-money fa-fw"></i> Creation - 
                    <span class="caption-helper">Budget</span>

                </div>
                <a id="btn_add_row" class="btn btn-primary btn-xs pull-right margin-top-10"><i class="fa fa-plus"></i> Add Row</a>
            </div>
            <div class="portlet-body" style="margin-top:-15px">
                <div class="row">
                    <div class="col-md-12" style="">
                        <table class="table table-bordered table-condensed" id="exampleTable" style="margin: 0px 0px !important;">
                            <thead> 
                                <tr>
                                    <th>Job-order Number</th>
                                    <th>Description</th>
                                    <th>Delivery Quarter</th>
                                    <th>Account Code</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>

                        <div style="display:none">    
                            <table id="detailsTable">
                                <thead> 
                                    <tr>
                                        <th>Item name</th>
                                        <th>Qty</th>
                                        <th>Unit</th>
                                        <th>Price</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>

                        <hr>
                        <div class="col-md-12" style="padding: 10px 10px;">
                            <button class="btn btn-primary pull-right"><i class="fa fa-save"></i> Save</button>
                            <h3>Summary</h3>
                        </div>

                    </div>
                </div>

            </div>
        </div>


        <!--
        <tr>
            <td>
                <select class="form-control inline" id="btype" style="width: 100%" >
                    <option value=""></option>
                    <option value="1">CAPEX - Capitalized Expenses</option>
                    <option value="2">OPEX - Operation Expenses</option>
                    <option value="2">SP - Special Projects</option>
                </select>
            </td>
            <td>
                <select class="form-control inline" id="ccid" style="width: 100%" >
                    <option value=""></option>
                    <option value="1">400 - IT Department</option>
                    <option value="2">300 - Accounting Department</option>
                </select>                                    
            </td>
            <td>400-2016-01</td>
            <td></td>
            <td></td>
            <td></td>
            <td>
                <a href="javascript:;" class="btn btn-xs btn-default" id="copythis"><i class="fa fa-copy"></i></a><a href="javascript:;" class="btn btn-xs btn-danger" id="delthis"><i class="fa fa-times"></i></a>
            </td>
        </tr>
        -->

<script src="<?php echo base_url(); ?>assets/global/plugins/fuelux/js/spinner.min.js"></script> 
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js"></script> 
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js"></script> 
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery.input-ip-address-control-1.0.min.js"></script> 
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-pwstrength/pwstrength-bootstrap.min.js" type="text/javascript"></script> 
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script> 
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery-tags-input/jquery.tagsinput.min.js" type="text/javascript"></script> 
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js" type="text/javascript"></script> 
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-touchspin/bootstrap.touchspin.js" type="text/javascript"></script> 
<script src="<?php echo base_url(); ?>assets/global/plugins/typeahead/handlebars.min.js" type="text/javascript"></script> 
<script src="<?php echo base_url(); ?>assets/global/plugins/typeahead/typeahead.bundle.min.js" type="text/javascript"></script> 
<script src="<?php echo base_url(); ?>assets/global/plugins/ckeditor/ckeditor.js"></script> 
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-select/bootstrap-select.min.js"></script> 
<script src="<?php echo base_url(); ?>assets/global/plugins/select2/select2.min.js"></script> 
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js"></script> 

<script src="<?php echo base_url(); ?>assets/global/plugins/datatables/jquery.dataTables.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/datatables/extensions/TableTools/js/dataTables.tableTools.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/datatables/extensions/ColReorder/js/dataTables.colReorder.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/datatables/extensions/Scroller/js/dataTables.scroller.min.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/datatables/dataTables.bootstrap.js"></script>
<script src="<?php echo base_url(); ?>assets/pages/bos/bos.js"></script>

<script src="http://172.20.224.25/tellering/assets/jquery-shortcutkeys/shortcutkey.js"></script>

<!--Additional Scripts for testing-->
<script src="<?php echo base_url(); ?>assets/global/plugins/datatables/jquery.jeditable.mini.js"></script><!--The script below is for editable dataTable plugin-->
<script src="http://cdn.datatables.net/keytable/2.1.2/js/dataTables.keyTable.min.js"></script><!--The script below is for keys dataTable plugin-->

<script type="text/javascript">
    function fnFormatDetails(table_id, html) {
        var sOut = "<table id=\"exampleTable_" + table_id + "\">";
        sOut += html;
        sOut += "</table>";
        return sOut;
    }
    //Sample Data for debugging Purposes
    //////////////////////////////////////////////////////////// EXTERNAL DATA - Array of Objects 
    var terranImage = "https://i.imgur.com/HhCfFSb.jpg";
    var jaedongImage = "https://i.imgur.com/s3OMQ09.png";
    var grubbyImage = "https://i.imgur.com/wnEiUxt.png";
    var stephanoImage = "https://i.imgur.com/vYJHVSQ.jpg";
    var scarlettImage = "https://i.imgur.com/zKamh3P.jpg";

    // DETAILS ROW A: Inner table
    var detailsRowAPlayer1 = {itemName: "KWH Meter", qty: "20", unit: "set", price: "100", amount: "2000"};
    var detailsRowAPlayer2 = {itemName: "Copper wire", qty: "100", unit: "meters", price: "300", amount: "3000"};
    var detailsRowAPlayer3 = {itemName: "Welding Rod", qty: "50", unit: "pc", price: "20", amount: "1000"};

    var detailsRowA = [detailsRowAPlayer1, detailsRowAPlayer2, detailsRowAPlayer3];

    // DETAILS ROW B 
    var detailsRowBPlayer1 = {itemName: "Iron man suit", qty: "1", unit: "set", price: "6999999", amount: "6999999"};

    var detailsRowB = [detailsRowBPlayer1];

    // DETAILS ROW C 
    var detailsRowCPlayer1 = {itemName: "PPAP", qty: "1", unit: "unit ", price: "2", amount: "2"};

    var detailsRowC = [detailsRowCPlayer1];

    var rowA = {joborderno: "200-2017-01", description: "For Maintenance of Substation 5", deliveryQuarter: "09/21/2017", details: detailsRowA, accountCode: "119"};
    var rowB = {joborderno: "200-2017-02", description: "For Maintenance of Substation 1", deliveryQuarter: "01/30/2017", details: detailsRowB, accountCode: "119"};
    var rowC = {joborderno: "200-2017-03", description: "For Maintenance of Substation 3", deliveryQuarter: "06/16/2017", details: detailsRowC, accountCode: "119"};

    var newRowData = [rowA, rowB, rowC];
    var newRowData2 = {"data":[rowA, rowB, rowC]}; //for editor
    ////////////////////////////////////////////////////////////
    //initialize globals
    var iTableCounter = 1;
    var oTable;
    var oInnerTable;
    var detailsTableHtml;
    var editor;//this is for jquery editor plugin
    //Run On HTML Build
    $(document).ready(function () {
        
        // you would probably be using templates here
        detailsTableHtml = $("#detailsTable").html();//this is the inner table

        //Insert a 'details' column to the table
        var nCloneTh = document.createElement('th');
        var nCloneTd = document.createElement('td');
        nCloneTd.innerHTML = '<img src="http://i.imgur.com/SD7Dz.png">';
        nCloneTd.className = "center";

        $('#exampleTable thead tr').each(function () {
            this.insertBefore(nCloneTh, this.childNodes[0]);
        });

        $('#exampleTable tbody tr').each(function () {
            this.insertBefore(nCloneTd.cloneNode(true), this.childNodes[0]);
        });


        //Initialse DataTables, with no sorting on the 'details' column
        var oTable = $('#exampleTable').dataTable({
            //This table is the mother table.
            "bJQueryUI": true,
            "aaData": newRowData,
            "bPaginate": false,
            "aoColumns": [
                {
                    "mDataProp": null,
                    "sClass": "control center",
                    "sDefaultContent": '<img src="http://i.imgur.com/SD7Dz.png">'
                },
                {"mDataProp": "joborderno"},
                {"mDataProp": "description"},
                {"mDataProp": "deliveryQuarter"},
                {"mDataProp": "accountCode"}
            ],
            "aoColumnDefs":[
                {
                    "sClass":"editMe",
                    
                    "aTargets": [1,2,3,4],
                }
            ],
            "oLanguage": {
                "sInfo": "_TOTAL_ entries"
            },
            "aaSorting": [[1, 'asc']]
        });
        //Make other table editable.
        $(oTable).find('td.editMe').editable(function(v,s){//make inner table editable.
            return(v);
        });
        /* Add event listener for opening and closing details
         * Note that the indicator for showing which row is open is not controlled by DataTables,
         * rather it is done here
         */
        $('#exampleTable tbody td img').live('click', function () {
            /*
            $(oTable).dataTable().fnAddData([{joborderno:"NULL", description: "NULL", deliveryQuarter:"NULL", accountCode: "NULL"}]);//add new row with null data
            $(oTable).find('td.editMe').editable(function(v,s){//make data editable on the mother table.
                //this function makes the mother table editable.
                console.log(v);
                return(v);
            });
            */
            //invoke the inner table when the plus image button is clicked.
            var nTr = $(this).parents('tr')[0];
            var nTds = this;
            
            if (oTable.fnIsOpen(nTr)) {
                /* This row is already open - close it */
                this.src = "http://i.imgur.com/SD7Dz.png";//This is for the minus image button.
                oTable.fnClose(nTr);
            }
            else {
                /* Open this row */
                var rowIndex = oTable.fnGetPosition($(nTds).closest('tr')[0]);//returns the index of the expanded row
                var detailsRowData = newRowData[rowIndex].details;//returns the array of data inside the details array and returns them as object.

                this.src = "http://i.imgur.com/d4ICC.png";
                oTable.fnOpen(nTr, fnFormatDetails(iTableCounter, detailsTableHtml), 'details');
                oInnerTable = $("#exampleTable_" + iTableCounter).dataTable({
                    //This table is the inner table
                    "bJQueryUI": true,
                    "bFilter": false,
                    "aaData": detailsRowData,
                    "bSort": false, // disables sorting
                    "aoColumns": [
                        {"mDataProp": "itemName"},
                        {"mDataProp": "qty"},
                        {"mDataProp": "unit"},
                        {"mDataProp": "price"},
                        {"mDataProp": "amount"}
                    ],
                    "bPaginate": false,
                    "oLanguage": {
                        "sInfo": "_TOTAL_ entries"
                    },
                    "aoColumnDefs":[
                        {
                            "sClass":"editMeInner",
                            "aTargets": [0,1,2,3,4],
                        }
                    ],
                    "fnRowCallback": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                        var imgTag = aData['pic'];
                        $('td:eq(0)', nRow).html(imgTag);
                        return nRow;
                    }
                });
                
                $(oInnerTable).dataTable().fnAddData([{itemName:"NULL", qty: "NULL", unit:"NULL", price: "NULL", amount:"NULL"}]);//add new row with null column.
                $(oInnerTable).find('td.editMeInner').editable(function(v,s){//make inner table editable.
                    return(v);
                });
                iTableCounter = iTableCounter + 1;
            }
        });


    });
</script>
<script src="<?php echo base_url(); ?>assets/pages/bos/new.js"></script>
