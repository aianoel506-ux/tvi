<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


Class Model_bos extends CI_Model {


    function select2_ccid() {
        $data = array();
        $query = $this->db->select('sysid, codes, desc')
            ->from('prime_costcenter_main')
            ->where(array('status' => 1))
            ->get();
        $num_rows = $query->num_rows();
        if($num_rows>0) {
            foreach($query->result() as $row) {
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' =>  $row->codes . ' - ' .$row->desc
                );
            }
        }
        return json_encode($data);
    }


    function select2_budget_types() {
        $data = array();
        $query = $this->db->select('sysid, names, desc')
            ->from('prime_types_parameter')
            ->where(array('status' => 1, 'codes' => 'BUDGET'))
            ->get();
        $num_rows = $query->num_rows();
        if($num_rows>0) {
            foreach($query->result() as $row) {
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' =>  $row->names . ' - ' .$row->desc
                );
            }
        }
        return json_encode($data);
    }

    function submit_budget() {
        $data = array();

        $selected = $this->input->post('selected');
        $remarks = $this->input->post('remarks');
        $ccid = $this->input->post('ccid');
        $types = $this->input->post('types');
        $year = $this->input->post('year');

        $this->db->trans_begin();
        if($selected && count($selected)>0) {
            // sysid, ccid, year, createdby, updatedby ....
            $bosgrouparr = array(
                'ccid' => $ccid,
                'year' => $year,
                'createdby' => user_id(),
                'updatedby' => user_id(),
                'types' => $types,
                'remarks' => $remarks
            );
           $create_bos_trn = $this->db->insert('trn_bos_group' , $bosgrouparr);

           $dataid = $this->db->insert_id();
           if($create_bos_trn) {
               foreach($selected as $row) {
                   $boslogsarr = array(
                       'groupid' => $dataid,
                       'bosid' => $row,
                       'createdby' => user_id(),
                       'updatedby' => user_id()
                   );
                   $this->db->insert('trn_bos_logs' , $boslogsarr);
               }
               create_transaction_trails('BOS-CREATION', $remarks, 54, $dataid);
           }
            if($this->db->trans_status() == true){
                $this->db->trans_commit();
                $func = 'success';
                $msg = 'Budget has been submitted!';
                $qry = true;
            }else{
                $this->db->trans_rollback();
                $func = 'error';
                $msg = 'Failed to send budget approval.';
                $qry = false;
            }

       }else{
           $this->db->trans_rollback();
           $func = 'info';
           $msg = 'Please select budget!';
           $qry  = false;
       }

        $data['msg'] = $msg;
        $data['qry'] = $qry;
        $data['func'] = $func;
        return json_encode($data);
    }

    function get_budget() {
        $data = array();
        $ccid = $this->input->post('ccid');
        $types = $this->input->post('types');
        $year = $this->input->post('year');
        $query = $this->db->select('btm.sysid, btg.typesid, btg.codes, btg.descs, btm.status')
            ->from('bos_transaction_main AS btm')
            ->join('bos_transaction_group AS btg', 'btm.groupid = btg.sysid')
            ->where(
                array(
                    'btm.ccid' => $ccid,
                    'btg.typesid' => $types,
                    'btm.years' => $year,
                    'btm.status !=' => 0
                )
            )
            ->get();
        $total_items = 0;
        $total_amt = 0;
        $total_bal = 0;
        $total_exp = 0;
        if($query->num_rows()>0) {
            $num = 1;
            foreach($query->result() as $row) {
                $control = '';
                if($row->status == 307) {
                    $control .= '<a href="javascript:;" data-id="'.$row->sysid.'" id="btn_delete_bos" class="btn btn-danger inline btn-xs"><i class="fa fa-times"></i></a>';
                    $control .= '<a href="'.base_url('module/5b384ce32d8cdef02bc3a139d4cac0a22bb029e8/view/'.$row->sysid).'" id="btn_view" class="btn btn-info inline btn-xs"><i class="fa fa-search"></i></a>';
                }
                $checkbox = '<input id="selected" name="selected['.$row->sysid.']" type="checkbox" value="'.$row->sysid.'" class="hidden" />';

                $amt_approved = $this->get_budget_approved_amount($row->sysid);
                $amt_expense = $this->get_budget_expense_amount($row->sysid);
                $amt_addition = $this->get_budget_additional_amount($row->sysid);
                $amt_deduction = $this->get_budget_deduction_amount($row->sysid);
                $amt_balance = (($amt_approved->amt + $amt_addition) - $amt_expense) - $amt_deduction;

                $total_items += 0;
                $total_amt += $amt_approved->amt;
                $total_bal += $amt_balance;
                $total_exp += $amt_expense;
                $data['list'][] = array(
                    'expand' => btn_expand($row->sysid),
                    'num' => $num++,
                    'codes' => $row->codes,
                    'desc' => $row->descs,
                    'items' => $amt_approved->cnt,
                    'acctcode' => 0,
                    'prevamt' => number_format(0, 2),
                    'adjp' => number_format($amt_addition, 2),
                    'adjm' => number_format($amt_deduction, 2),
                    'amt' => number_format($amt_approved->amt, 2),
                    'exp' => number_format($amt_expense, 2),
                    'bal' => number_format($amt_balance, 2),
                    'status' => get_types_label_format($row->status),
                    'control' => $control,
                    'checkbox' => $checkbox,
                    'approvalbtn' => '<a id="approvebudgetbtn" data-id="'.$row->sysid.'" class="btn btn-xs green-haze  uppercase " ><i class="fa fa-check"></i></a>
                    <a id=disapprovebudgetbtn data-id="'.$row->sysid.'" class="btn btn-xs  uppercase btn-danger" ><i class="fa fa-times"></i></a>'
                );
            }
        }
        $data['totalamt'] = number_format($total_amt, 2);
        $data['totalexp'] = number_format($total_exp, 2);
        $data['totalbal'] = number_format($total_bal, 2);
        $data['totalitem'] = number_format($total_items);

        $data['budgetlabel'] = get_costcenter_name($ccid, true) . ' - ' .get_types_label_format($types, false, false, false, false, false, true)->text;
        return json_encode($data);
    }

    function get_subdetails() {
        $data = array();
        $id = $this->input->post('id');


        $get_budget_creation_desc = $this->db->select('remarks')
            ->from('bos_transaction_trails')
            ->where(array('bosid' => $id, 'codes' => 'CREATE'))
            ->order_by('datecreated', 'desc')
            ->get()->row();
        $budget_details = ($get_budget_creation_desc) ? $get_budget_creation_desc->remarks : 'N/A';

        $html = '';
        $html .= '<h4 style="margin: 5px; 20px;">Details: <span class="pull-right font-green-haze">'.$budget_details.'</span></h4>';

       $sql = $this->db->select("sysid,quarterspec")->from("bos_transaction_item")
           ->where(array("bosmainid" => $id))->get();
       if($sql->num_rows() > 0){

           foreach ($sql->result() as $row){
               $total = 0;
               if($row->quarterspec == 1){
                   $desc = '1st Quarter';
               }else if($row->quarterspec == 2){
                   $desc = '2nd Quarter';
               }else if($row->quarterspec == 3){
                   $desc = '3rd Quarter';
               }else if($row->quarterspec == 4){
                   $desc = '4th Quarter';
               }else{
                   $desc = '';
               }
               $html .= '<table class="table table-bordered table-hover table-striped " id="subtable">';
               $html .= '<thead>';
               $html .= '<th width="200px"><h4>'.$desc.'</h4></th>';
               $html .= '<th>Account Code</th>';
               $html .= '<th colspan="2"></th>';
               $html .= '</thead>';
               $html .= '<tbody>';

                    $getamounts = $this->db->select("sysid,bosid, amt , refid")->from("bos_transaction_ledger")
                        ->where(array("bosid" => $row->sysid,"status" => 1))->get();
                    if($getamounts->num_rows() > 0){

                        foreach ($getamounts->result() as $row1){
                            $refid = $row1->refid;
                            $total += $row1->amt;
                            $html .= '<tr>';
                            $html .= '<td class="text-danger"><input type="hidden" class="bosid" value="'.$row1->sysid.'" />'.number_format($row1->amt , 2).'</td>';
                            $html .= '<td width="350px">';
                            $html .= '<input type="text" id="acctcode" data-id="'.$row1->sysid.'" value="'.$refid.'" name="acctcode" class="form-control inline" style="width: 100%" />';
                            $html .= '</td>';
                            $html .= '<td class="number">'.$this->itemcount($row1->sysid).' item(s)</td>';
                            $html .= '<td class="number"><a href="#tbl_bos_items" class="btn btn-xs btn-default" data-view="'.$desc.'-'.$id.'-'.$refid.'" data-arr="'.$row1->sysid.'" data-toggle="ajax-modal"><i class="fa fa-reorder"></i> View Item(s)</a></td>';
                            $html .= '</tr>';

                        }
                    }

               $html .= '</tbody>';
               $html .= '<tfoot>';
                   $html .= '<tr class="bg-info">';
                   $html .= '<td class="bold ">Total: '.number_format($total , 2).'</td>';
                   $html .= '<td class="bold "></td>';
                   $html .= '<td class="bold "></td>';
                   $html .= '<td class="bold "></td>';
                   $html .= '</tr>';

               $html .= '</tfoot>';
               $html .= '<table>';

           }
       }


        /*
         * @todo check if a budget has an items in db and create table
         * @todo check if a budget has a multiple quarter and create a table each quarter
         * @todo check if a budget has no multiple quarter or items, then create summary details of budget not table.
         */

        $data['html'] = $html;
        return json_encode($data);
    }

    function itemcount($bositemid){
        $sql = $this->db->select("COUNT(sysid) AS totalitemscount")->from("bos_transaction_details")
            ->where(array("bositemid" => $bositemid ,"status !=" => 0))->get()->row();
        return ($sql) ? $sql->totalitemscount : 0;
    }

    function get_budget_approved_amount($bosid) {
        $qry = $this->db->select('SUM(l.amt) AS amt, l.bosid')
            ->from('bos_transaction_ledger AS l')
            ->join('bos_transaction_item AS i', 'i.sysid = l.bosid')
            ->join('bos_transaction_main AS m', 'm.sysid = i.bosmainid')
            ->where(array('m.sysid' => $bosid, 'l.ledgertype' => 1, 'l.typesid' => 0))
            ->group_by('l.bosid')
            ->get();

        $amt_total = 0;
        $cnt_total = 0;
        if($qry->num_rows() > 0) {
            foreach($qry->result() as $row) {
                $amt_total += $row->amt;
                $cnt_total += 1;
            }
        }
        $data = array('amt' => $amt_total, 'cnt' => $cnt_total);
        return (object)$data;
    }

    function get_budget_expense_amount($bosid) {
        $qry = $this->db->select('SUM(amt) AS amt')
            ->from('bos_transaction_ledger')
            ->where(array('bosid' => $bosid, 'ledgertype' => 0, 'typesid' => 1083, 'refid > ' => 0, 'status' => 1))
            ->get();
        $amt_total = 0;
        if($qry->num_rows() > 0) {
            foreach($qry->result() as $row) {
                $amt_total += $row->amt;
            }
        }
        return $amt_total;
    }

    function get_budget_additional_amount($bosid) {
        $qry = $this->db->select('SUM(amt) AS amt')
            ->from('bos_transaction_ledger')
            ->where(array('bosid' => $bosid, 'ledgertype' => 1, 'typesid' => 1084, 'status' => 1))
            ->get();
        $amt_total = 0;
        if($qry->num_rows() > 0) {
            foreach($qry->result() as $row) {
                $amt_total += $row->amt;
            }
        }
        return $amt_total;
    }
    function get_budget_deduction_amount($bosid) {
        $qry = $this->db->select('SUM(amt) AS amt')
            ->from('bos_transaction_ledger')
            ->where(array('bosid' => $bosid, 'ledgertype' => 0, 'typesid' => 1084, 'status' => 1))
            ->get();
        $amt_total = 0;
        if($qry->num_rows() > 0) {
            foreach($qry->result() as $row) {
                $amt_total += $row->amt;
            }
        }
        return $amt_total;
    }
    function get_budget_types_group(){
        $data = array();
        $budgettypes = $this->input->post('data');

        $sql = $this->db->select("sysid,descs")->from("bos_transaction_group")
            ->where(array("typesid" => $budgettypes , "status" => 1))->get();
        if($sql->num_rows() > 0){
            foreach ($sql->result() as $row){
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->descs
                );
            }
        }

        return json_encode($data);
    }
    function submit_transaction(){
        $data = array();
        $quarter = $this->input->post('quarter');
        $itemcount = $this->input->post('itemcount');
        $amteach = $this->input->post('amteach');
        $total = $this->input->post('total');

        $remarks = $this->input->post('remarks');
        $selectgroup = $this->input->post('selectgroup');
        $selectyear = $this->input->post('selectyear');
        $ccid = $this->input->post('ccid');
        $year = $this->input->post('year');

        $this->db->trans_begin();

        //bos_transaction_main
        $bostransactionmain = array(
            'groupid' => $selectgroup,
            'ccid' => $ccid,
            'years' => $selectyear,
            'months' => 0,
            'createdby' => user_id(),
            'updatedby' => user_id()
        );
        $this->db->insert("bos_transaction_main" , $bostransactionmain);
        $mainlastid = $this->db->insert_id();
        $data['bos_transaction_main'] = $this->db->_error_message();

        foreach ($quarter  as $key => $quarterval){
            //bos_transaction_item
            $bos_transaction_item = array(
                'bosmainid' => $mainlastid,
                'quarterspec' => $quarterval,
                'createdby' => user_id(),
                'updatedby' => user_id()
            );
            $this->db->insert("bos_transaction_item" , $bos_transaction_item);
            $data['bos_transaction_item'] = $this->db->_error_message();
            $itemlastid = $this->db->insert_id();
            $loopcount = $itemcount[$key];
            for($i = 0; $i < $loopcount; $i++){
                //bos_transaction_ledger
                $bos_transaction_ledger = array(
                    'bosid' => $itemlastid,
                    'amt' => $amteach[$key],
                    'ledgertype' => 1,
                    'status' => 1,
                    'typesid' => 0,
                    'createdby' => user_id(),
                    'updatedby' => user_id()
                );
                $this->db->insert("bos_transaction_ledger" , $bos_transaction_ledger);
                $data['bos_transaction_ledger'] = $this->db->_error_message();
            }
        }

        if($this->db->trans_status() == true){
            $this->db->trans_commit();
            $msg = 'Budget has been added.';
            $func = 'success';
            $qry = true;
        }else{
            $this->db->trans_rollback();
            $msg = 'Failed to add budget.';
            $func = 'error';
            $qry = false;
        }
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;
        return json_encode($data);
    }
    function getacct_codelist(){
        $data= array();
        $sql = $this->db->select("sysid,codes,descs")->from("prime_chart_of_accounts")
            ->where(array('status' => 1, 'types' => 0))->get();
        if($sql->num_rows() > 0){
            foreach ($sql->result() as $row){
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->codes.'-'.$row->descs
                );
            }
        }
        return json_encode($data);
    }
    function update_ledger_refid(){
        $data = array();
        $bosid = $this->input->post('bosid');
        $refid = $this->input->post('refidval');
        if($refid == ''){
            $refid = null;
        }
        $this->db->trans_begin();

        $getrefidold = $this->db->select("refid")->from("bos_transaction_ledger")
            ->where(array("sysid" => $bosid))->get()->row();
        if($getrefidold) {
            $data['hasrefif'] = $getrefidold->refid;
            $updatearr = array(
                'refid' => $refid
            );
            $this->db->where(array("sysid" => $bosid));
            $this->db->update("bos_transaction_ledger", $updatearr);
            $data['errormessage'] = $this->db->_error_message();

            $insarray = array(
                'bosid' => $bosid,
                'codes' => 'UPDATE',
                'descs' => 'Update Account Code',
                'statusid' => 307,
                'remarks' => 'FROM Account Code ' . $getrefidold->refid . ' TO Account Code ' . $refid,
                'createdby' => user_id()
            );
            $this->db->insert("bos_transaction_trails", $insarray);
        }
        if($this->db->trans_status() == true){
            $this->db->trans_commit();
            $msg = 'Account code has been updated.';
            $func = 'success';
            $qry = true;
        }else{
            $this->db->trans_rollback();
            $msg = 'Failed to update account code.';
            $func = 'error';
            $qry = false;
        }
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;
        return json_encode($data);
    }
    function get_all_items(){
        $data = array();
        $sql = $this->db->select("sysid,codes,names")->from("items_main_category")
            ->where(array("status" => 1))->get();
        if($sql->num_rows() > 0){
            foreach ($sql->result() as $row){
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->codes.'-'.$row->names
                );
            }
        }
        return json_encode($data);
    }
    function submititemdetails(){
        $data = array();
        $bositemid = $this->input->post('bositemid');
        $items = $this->input->post('items');
        $quantity = $this->input->post('quantity');

        $this->db->trans_begin();
        $insarr = array(
            'bositemid' => $bositemid,
            'itemid' => $items,
            'qty' => $quantity,
            'status' => 307,
            'createdby' => user_id(),
            'updatedby' => user_id()
        );
        $this->db->insert("bos_transaction_details",$insarr);
        $lastid = $this->db->insert_id();
        $getitemdetails = $this->db->select("im.names")->from("bos_transaction_details as btd")
            ->join("items_main_category as im" , "im.sysid = btd.itemid" , "left")
            ->where(array("btd.sysid" => $lastid))->get()->row();
        $getitemcount = $this->db->select("COUNT(sysid) as totalcount")->from("bos_transaction_details as btd")
            ->where(array("bositemid" => $bositemid))->get()->row();

        if($this->db->trans_status() == true){
            $this->db->trans_commit();
            $msg = 'Item has been added.';
            $func = 'success';
            $qry = true;
        }else{
            $this->db->trans_rollback();
            $msg = 'Failed to add item.';
            $func = 'error';
            $qry = false;
        }
        $data['idcount'] = ($getitemcount) ? $getitemcount->totalcount : 0;
        $data['descs'] = ($getitemdetails) ? $getitemdetails->names : '';
        $data['quantity'] = $quantity;
        $data['bositemid'] = $bositemid;
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;
        return json_encode($data);
    }
    function getbositems(){
        $data = array();
        $bositemid = $this->input->post('bositemid');

        $sql = $this->db->select("btd.sysid , btd.qty , im.names")->from("bos_transaction_details as btd")
            ->join("items_main_category as im" , "im.sysid = btd.itemid","left")
            ->where(array("btd.bositemid" => $bositemid , "btd.status !=" => 0))
            ->get();
        $data['error'] = $this->db->_error_message();
        if($sql->num_rows() > 0){
            $num = 1;
            foreach ($sql->result() as $row){
                $data['bositemslist'][] = array(
                    "num" => $num++,
                    "item" => $row->names,
                    "qty" => $row->qty,
                    "control" => '<button type="button" data-id="'.$row->sysid.'" id="deleteitem" class="btn btn-xs btn-danger inline"> <i class="fa fa-times"></i></button>'
                );
            }
        }
        return json_encode($data);
    }
    function deleteitem(){
        $data = array();

        $dataid=  $this->input->post('dataid');
        $this->db->trans_begin();
        $updatearr = array(
            'status' => 0
        );
        $this->db->where(array("sysid"=> $dataid));
        $sql = $this->db->update("bos_transaction_details" , $updatearr);
        $data['error'] = $this->db->_error_message();
        if($this->db->trans_status() == true && $sql){
            $this->db->trans_commit();
            $msg = 'Item has been deleted.';
            $func = 'success';
            $qry = true;
        }else{
            $this->db->trans_rollback();
            $msg = 'Failed to delete item.';
            $func = 'error';
            $qry = false;
        }
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;
        return json_encode($data);
    }
    function removebudget(){
        $data = array();
        $dataid = $this->input->post('dataid');
        $this->db->trans_begin();
        $updatearr = array(
            'status' => 0
        );
        $this->db->where(array("sysid" => $dataid));
        $sql = $this->db->update("bos_transaction_main" , $updatearr);

        if($this->db->trans_status() == true && $sql){
            $this->db->trans_commit();
            $msg = 'Budget has been deleted.';
            $func = 'success';
            $qry = true;
        }else{
            $this->db->trans_rollback();
            $msg = 'Failed to delete budget.';
            $func = 'error';
            $qry = false;
        }
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;

        return json_encode($data);
    }
    function subdetailsapproval(){
        $data = array();
        $id = $this->input->post('id');


        $get_budget_creation_desc = $this->db->select('remarks')
            ->from('bos_transaction_trails')
            ->where(array('bosid' => $id, 'codes' => 'CREATE'))
            ->order_by('datecreated', 'desc')
            ->get()->row();
        $budget_details = ($get_budget_creation_desc) ? $get_budget_creation_desc->remarks : 'N/A';

        $html = '';
        $html .= '<h4 style="margin: 5px; 20px;">Details: <span class="pull-right font-green-haze">'.$budget_details.'</span></h4>';

        $sql = $this->db->select("sysid,quarterspec")->from("bos_transaction_item")
            ->where(array("bosmainid" => $id))->get();
        if($sql->num_rows() > 0){

            foreach ($sql->result() as $row){
                $total = 0;
                if($row->quarterspec == 1){
                    $desc = '1st Quarter';
                }else if($row->quarterspec == 2){
                    $desc = '2nd Quarter';
                }else if($row->quarterspec == 3){
                    $desc = '3rd Quarter';
                }else if($row->quarterspec == 4){
                    $desc = '4th Quarter';
                }else{
                    $desc = '';
                }
                $html .= '<table class="table table-bordered table-hover table-striped " id="subtable">';
                $html .= '<thead>';
                $html .= '<th width="200px"><h4>'.$desc.'</h4></th>';
                $html .= '<th>Account Code</th>';
                $html .= '<th colspan="2"></th>';
                $html .= '</thead>';
                $html .= '<tbody>';

                $getamounts = $this->db->select("btl.sysid,btl.bosid, btl.amt , btl.refid , pcoa.descs")->from("bos_transaction_ledger as btl")
                    ->join("prime_chart_of_accounts as pcoa" , "pcoa.sysid = btl.refid" , "left")
                    ->where(array("btl.bosid" => $row->sysid,"btl.status" => 1))->get();
                if($getamounts->num_rows() > 0){

                    foreach ($getamounts->result() as $row1){
                        $refid = $row1->refid;
                        $total += $row1->amt;
                        $html .= '<tr>';
                        $html .= '<td class="text-danger"><input type="hidden" class="bosid" value="'.$row1->sysid.'" />'.number_format($row1->amt , 2).'</td>';
                        $html .= '<td width="350px">';
                        $html .=  $row1->descs;
                        $html .= '</td>';
                        $html .= '<td class="number">'.$this->itemcount($row1->sysid).' item(s)</td>';
                        $html .= '<td class="number"><a href="#tbl_bos_items_view" class="btn btn-xs btn-default" data-view="'.$desc.'-'.$id.'-'.$refid.'" data-arr="'.$row1->sysid.'" data-toggle="ajax-modal"><i class="fa fa-reorder"></i> View Item(s)</a></td>';
                        $html .= '</tr>';

                    }
                }

                $html .= '</tbody>';
                $html .= '<tfoot>';
                $html .= '<tr class="bg-info">';
                $html .= '<td class="bold ">Total: '.number_format($total , 2).'</td>';
                $html .= '<td class="bold "></td>';
                $html .= '<td class="bold "></td>';
                $html .= '<td class="bold "></td>';
                $html .= '</tr>';

                $html .= '</tfoot>';
                $html .= '<table>';

            }
        }
        /*
         * @todo check if a budget has an items in db and create table
         * @todo check if a budget has a multiple quarter and create a table each quarter
         * @todo check if a budget has no multiple quarter or items, then create summary details of budget not table.
         */

        $data['html'] = $html;
        return json_encode($data);
    }
    function getapprovalitems(){
        $data = array();

        $bositemid = $this->input->post('bositemid');

        $sql = $this->db->select("btd.qty , btd.descs  , btd.datecreated, im.names")->from("bos_transaction_details as btd")
            ->join("items_main_category as im" , "im.sysid = btd.itemid" , "left")
            ->where(array("btd.bositemid" => $bositemid))
            ->get();
        if($sql->num_rows() > 0){
            $num = 1;
            foreach ($sql->result() as $row){
                $data['list'][] = array(
                    'num' => $num++,
                    'names' => $row->names,
                    'desc' => $row->descs,
                    'qty' => $row->qty,
                    'datecreated' => $row->datecreated
                );
            }
        }

        return json_encode($data);
    }
}
