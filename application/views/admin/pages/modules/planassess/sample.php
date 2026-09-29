<div class="container-fluid" >
    <div class="row-fluid">
        <div class="col-md-12">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <b>Purchase Requisition Form List:</b><span style="margin:0px 5px;">All</span>
                    <span class="supp_del"><?php echo $this->session->flashdata("delete_supp"); ?></span>
                    <span class="err_msg"><?php echo $this->session->flashdata("select_supp_exist"); ?></span>
                </div>
                <div class="col-md-12 panel-footer">
                    <div class="top_btn">
                        <a href="<?php echo $base_url ?>prs" class="btn btn-warning btn-xs">Back</a>
                    </div>
                    <div class="top_btn">
                        <a href="<?php echo $base_url ?>prf/prf_details/<?php echo $prf_no ?>" target="_blank" class="btn btn-info btn-xs">View PRF</a>
                    </div>
                    <div class="top_btn">
                        <a href="<?php echo $base_url ?>prs/viewprs/<?php echo $query_type->row()->type ?>/<?php echo $budget_code ?>/<?php echo $cc_id ?>" target="_blank" class="btn btn-success btn-xs">View Summary</a>
                    </div>
                    <div class="top_btn">
                        <a href="<?php echo $base_url ?>prs/priceHistory/<?php echo $query_type->row()->cc_id ?>/<?php echo $budget_code ?>" target="_blank" class="btn btn-success btn-xs">View Price History</a>
                    </div>
                    <div class="top_btn">
                        <a href="<?php echo $base_url ?>prs/pdf/<?php echo $prs_no ?>" target="_blank" class="btn btn-success btn-xs">Print</a>
                    </div>
                    <?php
                        if($supplier_check):
                    ?>
                    <?php endif ?>
                    <?php if(!$disapproved) : ?>
                    <?php if($user_send_approval && !$supplier_check) : ?>
                    <div class="top_btn">
                        <a href="#" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#addSupplier_only">Add Supplier</a>
                    </div>
                    <div class="top_btn">
                        <a href="#" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#supplierApproval">Send For Approval</a>
                    </div>
                    <div class="top_btn">
                        <form action="<?php echo base_url() ?>prs/prs_disapproved" method="post" >
                            <input type="hidden" value="<?php echo $prs_no ?>" name="prs_no" />
                            <input type="submit" value="Disapproved" name="disapproved"class="btn btn-xs btn-danger" />
                        </form>
                    </div>
                    <div class="top_btn">
                        <span class="err_msg"><?php echo $this->session->flashdata("select_supp"); ?>
                        </span>
                    </div>
                    <?php endif; ?>
                    <?php else: ?>
                    <div class="top_btn">
                        <form action="<?php echo base_url() ?>prs/prs_cancel_disapproved" method="POST">
                            <input type="hidden" value="<?php echo $prs_no ?>" name="prs_no" />
                            <input type="submit" value="cancel disapproved" name="cancel_disapproved"class="btn btn-xs btn-info" />
                        </form>
                    </div>

                    <?php endif; ?>
                </div>
                <div class="panel-body">
                    <div class="col-md-5">
                        <div class="row">
                            <label><strong>PRS Number:</strong></label>
                            <span><?php echo $prs_no ?></span>
                        </div>
                        <div class="row">
                            <label><strong>Requesting Department:</strong></label>
                            <span><?php echo $department ?></span>
                        </div>
                        <div class="row">
                            <label><strong>Type:</strong></label>
                            <span><?php echo $type ?></span>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="row">
                            <label><strong>Request Title:</strong></label>
                            <span><?php echo $title ?></span>
                        </div>
                        <div class="row">
                            <label><strong>Quarter:</strong></label>
                            <span><?php echo $quarter ?></span>
                        </div>
                        <div class="row">
                            <label><strong>Status:</strong></label>
                            <span><?php echo $status ?></span>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="row">
                            <label><strong>Pending:</strong></label>
                            <span><?php echo $pending; ?></span>
                        </div>
                        <div class="row">
                            <label><strong>Last approved by:</strong></label>
                            <span><?php echo $last_user_approval; ?></span>
                        </div>
                    </div>
                </div>

                <div class="panel-body">
                    <div id="slider_wrapper" >
                        <a href="javascript:void(0)" class="prev_arrow" style="  position: fixed;left: 0px; z-index: 999999;margin-top:70px;"><img src="../../images/left-arrow.png"></a>
                        <a href="javascript:void(0)" class="next_arrow" style="  position: fixed;right: 0px; z-index: 999999;margin-top:70px;"><img src="../../images/right-arrow.png"></a>
                        <div class="slider">
                            <!-----Slider title------------->
                            <div class="slider_title">
                            <div class="slider-row">
                                <span class="static_anim">
                                    <div class="sl-col sl-title">Item Desc</div>
                                    <div class="sl-col sl-title">QTY</div>
                                    <div class="sl-col sl-title">Unit</div>
                                    <div class="sl-col sl-title">Last Price</div>
                                    <div class="sl-col sl-title">Last Qty</div>
                                    <div class="sl-col sl-title last_supplier">Last Supplier</div>
                                    <div class="sl-col sl-title">Acct No.</div>
                                    <div class="sl-col sl-title">Job Order #</div>
                                </span>
                                <span class="dym_anim">
                                    <?php if(!empty($supplier_name)) : ?>
                                    <?php foreach($supplier_name as $supplier){
                                        echo "<div class=\"sl-col sl-title\">";
                                        if(!$supplier_check) {
                                            echo "<a href=\"#\" data-attr-id=\"{$supplier->prs_no}\" dat-attr-supp=\"{$supplier->supplier_code}\"  class=\"btn btn-danger btn-xs btn-rmv-sup\" title=\"Remove supplier\"><span class=\"glyphicon glyphicon-remove\"></span></a>";
                                            echo "<a href=\"#\" class=\"btn btn-primary btn-xs btn-change-prc\" data-toggle=\"modal\" data-supplier_prs=\"{$supplier->prs_no}\" data-supplier_code=\"{$supplier->supplier_code}\" data-supplier_name=\"{$supplier->comp_name}\" data-target=\"#edit_SupplierPrice\">";
                                            echo "<span class=\"glyphicon glyphicon-edit\"></span></a>";
                                        }
                                        echo "<br />";
                                        echo "{$supplier->comp_name}";
                                        echo "</div>";
                                    } ?>
                                    <?php endif; ?>
                                    <div class="sl-col sl-title">Total Cost</div>
                                    <div class="sl-col sl-title" style="width:190px !important;"  >Action</div>
                                </span>
                                </div>
                            </div>
                            <!-----Slider title------------->
                            <div class="slider-content">
                                <div class="slider-row">
                                <span class="static_anim">
                                <div class="sl-col sl-title">Item Desc</div>
                                <div class="sl-col sl-title">QTY</div>
                                <div class="sl-col sl-title">Unit</div>
                                <div class="sl-col sl-title">Last Price</div>
                                <div class="sl-col sl-title">Last Qty</div>
                                <div class="sl-col sl-title last_supplier">Last Supplier</div>
                                <div class="sl-col sl-title">Acct No.</div>
                                <div class="sl-col sl-title">Job Order #</div>
                                </span>
                                <span class="dym_anim">
                                <?php if(!empty($supplier_name)) : ?>
                                <?php foreach($supplier_name as $supplier){
                                echo "<div class=\"sl-col sl-title\">";
                                if(!$supplier_check) {
                                echo "<a href=\"#\" data-attr-id=\"{$supplier->prs_no}\" dat-attr-supp=\"{$supplier->supplier_code}\"  class=\"btn btn-danger btn-xs btn-rmv-sup\" title=\"Remove supplier\"><span class=\"glyphicon glyphicon-remove\"></span></a>";
                                echo "<a href=\"#\" class=\"btn btn-primary btn-xs btn-change-prc\" data-toggle=\"modal\" data-supplier_prs=\"{$supplier->prs_no}\" data-supplier_code=\"{$supplier->supplier_code}\" data-supplier_name=\"{$supplier->comp_name}\" data-target=\"#edit_SupplierPrice\">";
                                echo "<span class=\"glyphicon glyphicon-edit\"></span></a>";
                                }
                                echo "<br />";
                                echo "{$supplier->comp_name}";
                                echo "</div>";
                                } ?>
                                <?php endif; ?>
                                <div class="sl-col sl-title" >Total Cost</div>
                                <div class="sl-col sl-title"  style="width:190px !important;" >Action</div>
                                </span>
                                </div>
                                <!-------------------------------------------------------------------------------------------->

                                <!-------------------------------------------------------------------------------------------->
                                <div class="clr"></div>
                                <?php
                                $count = 1;
                                $total_price = 0.00;
                                foreach($query as $row){	
                                $min_price = array();
                                $mini_price = "";
                                echo "<div class='slider-row prs_items' id='item_code-{$row->item_code}' data-prs_no='{$row->prs_no}' data-item_code='{$row->item_code}' data-trn_id='{$row->id}'>";
                                echo "<span class=\"static_anim\">";
                                echo "<div class=\"sl-col static-box\">({$count})<span>{$row->item_desc}</div>";
                                $qty = explode(".", $row->qty);
                                $qty_o = $qty[0];
                                if($qty[1] > 0){
                                $qty_o = $row->qty;	
                                }
                                if(!$supplier_check){
                                echo "<div class=\"sl-col static-box\"><input type=\"number\" value=\"{$qty_o}\" style=\"width: 40px;\" class=\"prs_qty\"/></div>";
                                }else{
                                echo "<div class=\"sl-col static-box\">{$qty_o}</div>";	
                                }
                                echo "<div class=\"sl-col static-box\">{$row->unit_desc}</div>";

                                /**************************Item Last Price*******************************/
                                if(!empty($item_lastprice[$row->id])){
                                echo "<div class='sl-col static-box'>{$item_lastprice[$row->id]["last_price"]}</div>";
                                echo "<div class='sl-col static-box'>{$item_lastprice[$row->id]["qty"]}</div>";
                                echo "<div class=\"sl-col static-box last_supplier\">{$item_lastprice[$row->id]["comp_name"]}</div>";		
                                }else{
                                echo "<div class='sl-col static-box'>0.00</div>";
                                echo "<div class='sl-col static-box'>0</div>";
                                echo "<div class=\"sl-col static-box last_supplier\">none</div>";		
                                }
                                /***********************************************/

                                echo "<div class=\"sl-col static-box\">{$row->acct_code}</div>";
                                echo "<div class=\"sl-col static-box\">{$row->joborderno}</div>";
                                echo "</span>";
                                echo "<span class=\"dym_anim item_anim\">";
                                $total_cost = 0.00;
                                if(!empty($supplier_item)) {
                                foreach($supplier_item as $supplier){
                                        if($supplier->item_code == $row->item_code && $supplier->prs_id == $row->id){
                                                if($supplier->price != 0.00){
                                                        $min_price[] = $supplier->price;
                                                }
                                        }
                                }
                                $mini_price = 0.00;
                                if(count($min_price)){
                                        $mini_price = min($min_price);	
                                        if($row->prs_status == 1 || $row->prs_status == 0){

                                                $total_price += $mini_price * $row->qty;

                                                $total_cost = number_format($mini_price * $row->qty, 2, ".", ",");
                                        }
                                }
                                foreach($supplier_item as $supplier){
                                if($supplier->item_code == $row->item_code && $supplier->prs_id == $row->id){	
                                $style = "";
                                $supplier_item_class = "";
                                $mini_price_class = "";
                                if($supplier->price == $mini_price){
                                if($row->prs_status != -1){
                                $style = "style='background-color: #FFF2BC;'";
                                $mini_price_class = "mini_price";
                                }
                                }
                                $disabled_class = "";
                                $dis_appr_style = "";
                                $appr_check_class = "";
                                if($row->prs_status == -1){
                                $disabled_class = 'item_disabled';	
                                $dis_appr_style = "dis_appr";
                                }

                                if(!$supplier_check){
                                        $supplier_item_class = "supplier_item";
                                }

                                if($supplier_check){
                                        if($supplier->status == 1){
                                                $appr_check_class = "appr_check";

                                                $total_price += $supplier->price * $row->qty;

                                                $total_cost = number_format($supplier->price * $row->qty, 2, ".", ",");
                                        }
                                }

                                echo "<div title=\"Award Supplier\" id=\"quote_id-{$supplier->id}\" {$style} class=\"{$dis_appr_style} {$supplier_item_class} {$appr_check_class} {$mini_price_class} sl-col text-center td-award highlight-even {$disabled_class}\" data-quote_id=\"{$supplier->id}\">";
                                $checked = "";
                                $disabled = "";
                                if($supplier_check){
                                        if($supplier->status == 1){
                                                $checked = 'checked';	
                                        }
                                $disabled = 'disabled';	
                                }
                                if($row->prs_status == 2){
                                echo "<input type=\"checkbox\" name=\"supplier_check[{$count}]\" class=\"supplier_check\" value=\"" . base64_encode($supplier->supplier_code ."-" . $supplier->item_code) . "\" {$disabled} {$checked}>";
                                }
                                echo "<span>{$supplier->price}</span>";
                                echo "</div>";
                                }
                                }
                                }
                                echo "<div class=\"sl-col text-center\"><strong>{$total_cost}</strong></div>";

                                if($user_send_approval && !$supplier_check){
                                if($row->prs_status != -1){
                                echo "<div class=\"sl-col\"  style=\"width:190px !important;\" >";
                                echo "<table>";
                                echo "<tr>";
                                echo "<td>";
                                echo "<form action=\"{$base_url}prs/disapproved_item\" method=\"post\" style=\"margin-right:5px;\">";
                                echo "<input type=\"hidden\" value=\"{$row->id}\" name=\"item_code\"/>";
                                echo "<input type=\"hidden\" value=\"{$row->prs_no}\" name=\"prs_no\" />";
                                echo "<input type=\"submit\" value=\"disapproved\" name=\"submit\" class=\"btn btn-primary btn-xs\"/>";
                                echo "</form>";
                                echo "&nbsp;</td>";
                                echo "<td valign=\"top\">";
                                echo "<span class=\"remarks btn btn-xs btn-success\" style=\"margin-right:5px;\"><a href=\"javascript:void(0)\" class=\"show_remarks\">Show Remarks</a></span>";
                                echo "</td>";
                                echo "</tr>";
                                echo "</table>";
                                echo "</div>";
                                }else{
                                echo "<div class=\"sl-col\" style=\"width:190px !important;\"><form action=\"{$base_url}prs/cancelled_disapproved\" method=\"post\">";
                                echo "<input type=\"hidden\" value=\"{$row->id}\" name=\"item_code\"/>";
                                echo "<input type=\"hidden\" value=\"{$row->prs_no}\" name=\"prs_no\" />";
                                echo "<input type=\"submit\" value=\"cancel dis..\" name=\"submit\" class=\"btn-danger btn-xs cancel_dis\"/>";
                                echo "<span class=\"remarks btn btn-xs btn-success\" style=\"margin-right:5px;\"><a href=\"javascript:void(0)\" class=\"show_remarks\">Show Remarks</a></span>";
                                echo "</form></div>";
                                if(!$disapproved){

                                }
                                }
                                }
                                else{
                                if($row->prs_status != -1){
                                echo "<div class=\"sl-col\" ><a href=\"javascript:void(0)\" class=\"btn-info btn-xs\">Approved</a></div>";
                                }else{
                                echo "<div class=\"sl-col\" ><a href=\"javascript:void(0)\" class=\"btn-danger btn-xs\">Disapproved</a></div>";		
                                }
                                }
                                echo "</span>";
                                echo "</div>";
                                echo "<div class=\"clr\"></div>";
                                /***************************************Remarks**********************************************************/
                                if($user_send_approval && !$supplier_check){

                                /***************************************Remark**************************************************/
                                $display_detail = "";
                                $rem = "<td class=\"remarks_row\">";
                                $rem .= "<div id=\"remarks_wrapper\">";
                                $rem .= "<div class=\"refresh_rem_{$row->id}\">";
                                foreach($query_remarks->result() as $remark){	
                                        if($remark->itemid == $row->id){
                                                $display_detail = "style='display:block;'";
                                                if($user_id == $remark->user_id){
                                                        $rem .= "<div class=\"row\" data_group=\"{$remark->group_id}\"><div class=\"col-md-3 user_image_left\"><div class=\"image \"><img src=\"{$base_url}files/profile_pics/{$profile_pic}\" width=\"30\" align=\"left\"/></div>";
                                                        $rem .= "<div class=\"user\">{$remark->firstname}  {$remark->lastname}</div><div class=\"c_date\">{$remark->creationDate}</div>
                                                        <a href=\"javascript:void(0)\" 
                                                        data_item_id = \"{$row->id}\" data_groupid=\"{$remark->group_id}\" data-toggle=\"modal\" data-target=\"#edit_remarks_modal\"class=\"btn btn-success btn-xs edit_rem\">edit</a></div>";
                                                        $rem .= "<div class=\"col-md-6 user_comment_left\"><span class=\"comment_text\">{$remark->comment}</span></div>";
                                                        $rem .= "<div class=\"clearfix user_notif\">";
                                                        $rem .= "<strong>Notified Users</strong>";
                                                        $rem .= "<div class=\"clearfix user_notif_con\">";
                                                        foreach($query_notif->result() as $user_notif){
                                                                if($user_notif->itemid == $remark->itemid && $user_notif->forUserId != "" && $user_notif->group_id == $remark->group_id){
                                                                        $rem .= "<span class=\"btn btn-xs btn-info\">{$user_notif->firstname}</span>&nbsp;";
                                                                }
                                                        }
                                                        $rem .= "</div>";
                                                        $rem .= "</div>";
                                                        $rem .= "</div>";	
                                                }else{
                                                        $rem .= "<div class=\"row\"><div class=\"col-md-3 user_image_left\"><div class=\"image \"><img src=\"{$base_url}images/noPicture.png\" width=\"30\" align=\"left\"/></div>";
                                                        $rem .= "<div class=\"user\">{$remark->firstname}  {$remark->lastname}</div><div class=\"c_date\">{$remark->creationDate}</div></div>";
                                                        $rem .= "<div class=\"col-md-6 user_comment_left\"><span class=\"comment_text\">{$remark->comment}</span></div></div>";	
                                                }	
                                        }
                                }
                                $rem .= "</div>";
                                $rem .= "<div class=\"btn-remark\">";
                                $rem .= "<a href=\"javascript:void(0)\" style=\"clear:both;\" class=\"show_remark_modal btn btn-xs btn-info\" data-toggle=\"modal\" data_item_id = \"{$row->id}\" data-target=\"#remarks_modal\">add remark</a></div>";
                                $rem .= "</td>";

                                /************************Details*************************/

                                $spec_o = json_decode($row->specifications);

                                $rem .= "<td colspan=\"3\" class=\"detail_row\">";
                                $rem .= "<table><tr><td valign=\"top\">";
                                $rem .= "<div class=\"details_info\"><h4>Details: </h4>";
                                $rem .= "<div class=\"load_details-{$row->id}\" >{$row->description}</div>";
                                $rem .= "<div><a href=\"#\" data-toggle=\"modal\" data-target=\"#addDetail_modal\" data_item_id = \"{$row->id}\" class=\"btn btn-xs btn-success add_item_detail\">Add Details</a></div>";
                                $rem .= "</div>";
                                $rem .= "</td>";
                                $rem .= "<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>";
                                $rem .= "<td valign=\"top\">";

                                $rem .= "</td>";
                                $rem .= "</tr>";
                                $rem .= "</table>";
                                /********************************************/
                                if(count($spec_o) || $row->description != ""){
                                $display_detail = "style='display:block'";	
                                }

                                $rem .= "</td>";
                                echo "<div class=\"slider-row remark_com\" {$display_detail}>";
                                echo "<table class=\"table\" style=\"width:1000px;\">";
                                echo "<tr data_attr=\"{$row->item_code}\" prs_no=\"{$prs_no}\" >" . $rem . "</tr>";
                                echo "</table>";
                                echo "</div>";

                                /***********************************************************************************************/

                                }else{

                                $display_detail = "";
                                $rem = "<td class=\"remarks_row\">";
                                $rem .= "<div id=\"remarks_wrapper\">";
                                $rem .= "<div class=\"refresh_rem_{$row->id}\">";
                                foreach($query_remarks->result() as $remark){
                                        if($remark->itemid == $row->id){
                                                $display_detail = "style='display:block;'";
                                                if($user_id == $remark->user_id){
                                                        $rem .= "<div class=\"row\" data_group=\"{$remark->group_id}\"><div class=\"col-md-3 user_image_left\"><div class=\"image \"><img src=\"{$base_url}files/profile_pics/{$profile_pic}\" width=\"30\" align=\"left\"/></div>";
                                                        $rem .= "<div class=\"user\">{$remark->firstname}  {$remark->lastname}</div><div class=\"c_date\">{$remark->creationDate}</div><a href=\"javascript:void(0)\" data-toggle=\"modal\" 
                                                        data-target=\"#edit_remarks_modal\" data_item_id = \"{$row->id}\" data_groupid=\"{$remark->group_id}\" class=\"btn btn-success btn-xs  edit_rem\">edit</a></div>";
                                                        $rem .= "<div class=\"col-md-6 user_comment_left\"><span class=\"comment_text\">{$remark->comment}</span></div>";
                                                        $rem .= "<div class=\"clearfix user_notif\">";
                                                        $rem .= "<strong>Notified Users</strong>";
                                                        $rem .= "<div class=\"clearfix user_notif_con\">";
                                                        foreach($query_notif->result() as $user_notif){
                                                                if($user_notif->itemid == $remark->itemid && $user_notif->forUserId != "" && $user_notif->group_id == $remark->group_id){
                                                                        $rem .= "<span class=\"btn btn-xs btn-info\">{$user_notif->firstname}</span>&nbsp;";
                                                                }
                                                        }
                                                        $rem .= "</div>";
                                                        $rem .= "</div>";
                                                        $rem .= "</div>";	
                                                }else{
                                                        $rem .= "<div class=\"row\"><div class=\"col-md-3 user_image_left\"><div class=\"image \"><img src=\"{$base_url}images/noPicture.png\" width=\"30\" align=\"left\"/></div>";
                                                        $rem .= "<div class=\"user\">{$remark->firstname}  {$remark->lastname}</div><div class=\"c_date\">{$remark->creationDate}</div></div>";
                                                        $rem .= "<div class=\"col-md-6 user_comment_left\"><span class=\"comment_text\">{$remark->comment}</span></div>";
                                                        $rem .= "<div class=\"clearfix user_notif\">";
                                                        $rem .= "<strong>Notified Users</strong>";
                                                        $rem .= "<div class=\"clearfix user_notif_con\">";
                                                        foreach($query_notif->result() as $user_notif){
                                                                if($user_notif->itemid == $remark->itemid && $user_notif->forUserId != "" && $user_notif->group_id == $remark->group_id){
                                                                        $rem .= "<span class=\"btn btn-xs btn-info\">{$user_notif->firstname}</span>&nbsp;";
                                                                }
                                                        }
                                                        $rem .= "</div>";
                                                        $rem .= "</div>";
                                                        $rem .= "</div>";	
                                                }	
                                        }
                                }
                                $rem .= "</div>";
                                $rem .= "<div class=\"btn-remark\">";
                                $rem .= "<a href=\"javascript:void(0)\" style=\"clear:both;\" class=\"show_remark_modal btn btn-xs btn-info\" data-toggle=\"modal\" data_item_id = \"{$row->id}\" data-target=\"#remarks_modal\">add remark</a></div>";
                                $rem .= "</td>";

                                /************************Details*************************/
                                $rem .= "<td colspan=\"3\" class=\"detail_row\"><div class=\"details_info\"><h4>Details: </h4>";
                                $rem .= "<div class=\"load_details-{$row->item_code}\" >{$row->description}</div>";
                                $rem .= "</div>";
                                $rem .= "";
                                /********************************************/

                                if(count($spec_o) || $row->description != ""){
                                $display_detail = "style='display:block'";	
                                }

                                $rem .= "</div>";
                                $rem .= "</td>";
                                echo "<div class=\"slider-row remark_com\" {$display_detail}>";
                                echo "<table class=\"table\" style=\"width:1000px;\">";
                                echo "<tr data_attr=\"{$row->item_code}\" prs_no=\"{$prs_no}\" >" . $rem . "</tr>";
                                echo "</table>";
                                echo "</div>";
                                echo "<div class=\"clr\"></div>";
                                }
                                $count++;
                                }
                                ?>
                            </div>
                        </div>
                        <table class="table" id="summary-table">
                        <thead>
                        <tr>
                        <th>Acct Code</th>
                        <th>Job Order</th>
                        <th class="text-center">Cost Center</th>
                        <th>Amount</th>
                        <th class="text-center">Approved Budget</th>
                        <th class="text-center">Actual Exp. To Date</th>
                        <th class="text-center">Budget<br> Balance</th>
                        </tr>		
                        </thead>
                        <tbody>
                        <?php
                        foreach($quarter_arr["approved_amount"] as $key => $value){
                                echo "<tr>";
                                echo "<td>";
                                echo $quarter_arr["acct_code"][$key];
                                echo "</td>";
                                echo "<td>";
                                echo $quarter_arr["joborderno"][$key];
                                echo "</td>";
                                echo "<td>";
                                echo $quarter_arr["cost_center"];
                                echo "</td>";
                                echo "<td>";
                                echo "<span class=\"total_price\">".number_format($total_price, 2, ".", ",")."</span>";
                                echo "</td>";
                                echo "<td>";
                                echo (is_null($quarter_arr["approved_amount"][$key])) ? "0.00" : number_format($quarter_arr["approved_amount"][$key], 2, ".", ",");	
                                echo "</td>";
                                echo "<td>";
                                echo (is_null($quarter_arr["credit_amount"][$key])) ? "0.00" : number_format(($quarter_arr["credit_amount"][$key] + $quarter_arr["total_exp"]->row()->total_exp), 2, ".", ",");
                                echo "</td>";
                                echo "<td>";
                                echo (is_null($quarter_arr["balance_amount"][$key])) ? "0.00" : number_format(($quarter_arr["approved_amount"][$key] - $quarter_arr["total_exp"]->row()->total_exp), 2, ".", ",");
                                echo "</td>";
                                echo "</tr>";
                        }
                        ?>							
                        </tbody>
                        </table>	
                        <table class="table">
                        <thead>
                        <tr><th>REQUISITIONED BY:</th><th>APPROVED BY:</th><th>ACTION APPROVED BY:</th></tr>
                        </thead>
                        <tbody>
                        <tr>
                        <td>
                        <?php if(isset($query_approval_date["date"][2])) { ?>
                        <?php echo $query_approval_date["name"][2] ?> / <?php echo date("M j, Y g:i:s", strtotime($query_approval_date["date"][2])) ?>
                        <?php }else{ ?>
                        <?php echo "None"; ?>
                        <?php } ?>
                        </td>
                        <td>
                        <?php if(isset($query_approval_date["name"][3])) { ?>
                        <?php echo $query_approval_date["name"][3] ?>  / <?php echo date("M j, Y g:i:s", strtotime($query_approval_date["date"][3])); ?>
                        <?php }else{ ?>
                        <?php echo "None"; ?>
                        <?php } ?>
                        </td>
                        <td>
                        <?php if(isset($query_approval_date["name"][4])) { ?>
                        <?php echo $query_approval_date["name"][4] ?> / <?php echo date("M j, Y g:i:s", strtotime($query_approval_date["date"][4])); ?>
                        <?php }else{ ?>
                        <?php echo "None"; ?>
                        <?php } ?>
                        </td></tr>
                        </tbody>
                        <tfoot><tr><td>CCO/CORPORATE COMMUNICATIONS OFFICER</td><td>SEC./DIV./DEPT./HEAD</td><td>PRESIDENT CEO</td></tr></tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
	var initmaxWidth = 0;
	var count_node = 0;
	var init_count_node = 0;
	$(".slider-content .static_anim").find(".sl-col").each(function(index_o, value_o){
		initmaxWidth += $(this).outerWidth();
	});
	
	$(".slider-content .dym_anim").find(".sl-col").each(function(index_o, value_o){
		initmaxWidth += $(this).outerWidth();		
	});
	
	$(".slider-content .dym_anim:eq(0)").find(".sl-col").each(function(index_o, value_o){
		count_node++;
		init_count_node++;
	});
	
	$("#slider_wrapper").find(".slider-content").css({"width" : initmaxWidth + "px"});
	
	var count_supp = $(".dym_anim").eq(0).find(".sl-col").length;
	var countwidth = 0;
	$(".next_arrow").click(function(){		
		if(count_node - 1){
			countwidth -= $(".dym_anim").eq(0).find(".sl-col").outerWidth();			
			$(".dym_anim").find(".sl-col").animate({"left" : countwidth}, "fast");	
			count_node--;
		}
	});
	
	$(".prev_arrow").click(function(){
		if(count_node < init_count_node){
			countwidth += $(".dym_anim").eq(0).find(".sl-col").outerWidth();			
			$(".dym_anim").find(".sl-col").animate({"left" : countwidth}, "fast");	
			count_node++;
		}
	});
	
	
var off_top = 0;
$(window).scroll(function(){
var mov_top = 0;
var pos_top = $(this).scrollTop();	
if( pos_top > (off_top + 400)){
	mov_top = pos_top - 370;
}	
$("#slider_wrapper .slider .slider_title .slider-row").css({"top" : mov_top + "px"});
});	

$(".prs_qty").change(function(){
	var box = $(this);
	var qty = $(this).val();
	var prs_item = $(this).closest(".prs_items");
	var prs_no = prs_item.attr("data-prs_no");
	var item_code = prs_item.attr("data-item_code");
	var item_id = prs_item.attr("data-trn_id");
	$.ajax({
		url : base_url + "prs/update_qty",
		type : "POST",
		data : {"qty" : qty, "prs_no" : prs_no, "item_code" : item_code, "item_id" : item_id},
		error: function(xhr, status, error) {
		alert("Status: " + status);
		alert("Error: " + error);
		alert("xhr: " + xhr.readyState);
		},
		success : function(data){
		
		}
	});
});
	
$(".show_remarks").click(function(){
	var index_rem = $(".show_remarks").index(this);
	if(!$(this).hasClass("active_remarks")){
		$(".remark_com").eq(index_rem).css({"display" : "block"});	
		$(this).addClass("active_remarks");
		$(this).text("Hide Remarks");
	}else{
		$(".remark_com").eq(index_rem).css({"display" : "none"});	
		$(this).text("Show Remarks");
		$(this).removeClass("active_remarks");
	}		
});

$(".add_item_detail").click(function(){
	var item_id = $(this).attr("data_item_id");
	$.ajax({
		url : base_url + "prs/load_details",
		type: "POST",
		data : { "prs_id" : item_id },
		error: function(xhr, status, error) {
		alert("Status: " + status);
		alert("Error: " + error);
		alert("xhr: " + xhr.readyState);
		},
		success : function(data){
			$("#addDetail_modal #load_ajax").html(data);
		}
	});
});

});
</script>


<?php
//$slider->view_script();
?>

<style>
body{
	padding-top:0px !important;
}
.navbar-fixed-top, .navbar-fixed-bottom{
	position:relative !important;
}

.last_supplier{
	width:100px !important;
}

.static-box span{
	
}
.static_anim .static-box{
	
}
.comment_text{
	font-size:10.5pt;
}
.item_anim .mini_price_sel{
background: #FFF2BC url(/image/blue_check.png) right no-repeat !important;
background-size:40px !important;
background-position:50px 40px !important;			
}

.item_anim .supplier_sel_item{
background: #D1E3F5 url(/image/blue_check.png) right no-repeat;
background-size:40px;
background-position:60px 50px;
}

.item_anim .appr_check{
background-image: url('/image/blue_check.png');	
background-size:30px;
background-position:3px 0px;
background-repeat:no-repeat;
}

.sl-title{
	font-family:"tahoma";
	font-weight: bold;
}

.item_anim .td-award  .supplier_check{
display:none;	
}

.item_anim .td-award span{
    font-size: 7.5pt;
    font-weight: bold;
    font-family: "tahoma" sans-serif;
}

.scroll_wrapper{
	
}
.slider-row .static_anim .sl-col{
	background:#FFF;
	border-left: solid 1px #000;
}

.slider-row:nth-child(1) .dym_anim .sl-col{
	background:#90C4F7;
}

.slider-row:nth-child(1) .static_anim .sl-col{
	background:#63A8EC;
}

.prs_details_table .item_disabled{
	background-color: #B96DC7;
}
.detail_row{

}
.item_anim .sl-col{
	border-left:solid 1px #000;
}
.top_btn{
	 float: left;
    margin-right: 10px;	
}
.edit_div{
		text-align:right;
}
.edit_done{
	text-align:right;
}

.remarks{
	float: left;
    margin-right: 14px;
}

.remarks_row{
width:500px;
}

.edit_rem{
position: absolute;

}

#comment{
	 width: 72.666667%;

}
.add_remark_o{
	text-align:center;
}
#remarks_wrapper{
	padding-left:30px;
	width:590px;
}
#remarks_wrapper .c_date{
	 margin-left: 10px;
    font-size: 7.5px;
    color: #9197a3;
    font-weight: bold;	
}
#remarks_wrapper .row{
	margin-bottom:10px;
}
#remarks_wrapper .user{
    margin-left: 10px;
    font-size: 7.5pt;
    color: #3b5998;
    font-weight: bold;
}
#remarks_wrapper .user_image_left{
	 margin-bottom: 10px;
}

#remarks_wrapper .user_comment_left{
	position: relative;
    left: -20px;
    background: #A2C5C8;
    padding: 7px 30px 7px 5px;	
	-webkit-border-radius: 7px;
	-moz-border-radius: 7px;
	border-radius: 7px;
	margin-bottom:20px;
}

.clearfix.user_notif strong, .clearfix.user_notif_con span{
	font-size:9pt;
}

.clearfix.user_notif {
    position: relative;
    top: -14px;
    margin-left: 23%;
}

.err_msg{
	color:red;
}

.clearfix {
    clear: both;
}

#slider_wrapper .slider{
	width:100%;	
	overflow-x: hidden;
}

#slider_wrapper .slider .slider-row{
	background: #F0F0F0;
}

.static_anim .sl-col{
	background: green;
	z-index: 100;
    position: relative;
}

.dym_anim .sl-col{
position: relative;
left: -1px;
z-index: 1;
background-color: #D1E3F5;
width: 100px !important;
}

#slider_wrapper .slider .slider-content{
	width:2000px;
}

#slider_wrapper .slider .slider_title{
	 width: 100%;
    position: relative;
}

#slider_wrapper .slider .slider_title .sl-col{
	float:left;
	padding:10px;
	width:75px;
	height:100px;	
}

#slider_wrapper .slider .slider_title .slider-row{
	width: 5340px;
    position: absolute;
    top: 3px;
    z-index: 999;
}

#slider_wrapper .slider .slider-content .sl-col{
	float:left;
	padding:10px;
	width:55px;
	height:36px;
}

#slider_wrapper .slider .slider_title .sl-title{
	width:55px;
}

#slider_wrapper .slider-row .slider_title .sl-col:nth-child(1){
	width:130px;
}

#slider_wrapper  .slider-row .static_anim .sl-col:nth-child(1){
	width: 130px;
}

#slider_wrapper .slider .slider-col-2{
	float:left;
}
.sl-col .btn-primary{
	font-size:7.5pt;
}
#slider_wrapper .slider .slider-col-2 .sl-col{
	float:left;
	padding:10px;
	background: blue;
	width:100px;
}
.clr{
	clear:both;
}

.dym_anim{
	position:relative;
}

.prs_items .static-box{

    font-size: 7.5pt;
    font-weight: bold;
}
.sl-title{
	font-size:7.5pt;
	height:65px !important;
}
.dis_appr{
	background:#FF8383 !important;
}
.supp_del{
	color:red;
}
.remark_com{
display:none;
}
.remarks a{
	font-size:7.5pt;
	color:white;
}
.details a{
	font-size:7.5pt;
	color:white;
}
.details_info, .specs_info{
	font-size: 10.5pt;
    line-height: 15px;
}
.details_info h4, .specs_info h4{
	font-size: 17px;
}
.cancel_dis{
	float:left;
	margin-right:5px;
}
</style>



