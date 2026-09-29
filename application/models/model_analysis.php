<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Model_analysis extends CI_Model
{
    function save_ecales_log() {
        $data = array();
        $id = $this->input->post('id');
        $q = false;
        $this->db->trans_begin();
        // UPDATE EXISTING FIRST
        $this->db->where('dataid', $id);
        $this->db->update('customer_ecales_logs', array('status' => 0, 'updatedby' => user_id()));

        // INSERT NEW
        $ins_arr = array(
            'dataid' => $id,
            'flowid' => 2,
            'createdby' => user_id(),
        );
        $this->db->insert('customer_ecales_logs', $ins_arr);

        if ($this->db->trans_status() == true) {
            $this->db->trans_commit();
            $q = true;
        } else {
            $this->db->trans_rollback();
        }

        $data['input'] = $this->input->post();
        $data['qry'] = $q;
        return json_encode($data);
    }

    function add_ecales_item() {
        $ecalesid = $this->input->post('ecalesid');
        $itemid = $this->input->post('itemid');
        $qty = $this->input->post('qty');
        $quoteid = $this->input->post('quoteid');

        $data = array();
        $this->db->trans_begin();

        $ins_arr = array(
            'ecalesid' => $ecalesid,
            'itemid' => $itemid,
            'qty' => $qty,
            'createdby' => user_id(),
        );
        $this->db->insert('customer_ecales_item_trn', $ins_arr);

        if ($this->db->trans_status() == true) {
            $this->db->trans_commit();
            $qry = true;
            $msg = $qty . ' item(s) added!';
        } else {
            $this->db->trans_rollback();
            $qry = false;
            $msg = '0 item(s) added!';
        }
        $data['msg'] = $msg;
        $data['qry'] = $qry;
        $data['inp'] = $this->input->post();
        return json_encode($data);
    }

    function add_ecales_service() {
        $ecalesid = $this->input->post('ecalesid');
        $serviceid = $this->input->post('serviceid');
        $days = $this->input->post('days');

        $data = array();
        $this->db->trans_begin();

        $ins_arr = array(
            'ecalesid' => $ecalesid,
            'serviceid' => $serviceid,
            'days' => $days,
            'createdby' => user_id(),
        );
        $this->db->insert('customer_ecales_service_trn', $ins_arr);

        if ($this->db->trans_status() == true) {
            $this->db->trans_commit();
            $qry = true;
            $msg = 'Service/Equipment added!';
        } else {
            $this->db->trans_rollback();
            $qry = false;
            $msg = 'No service/equipment added!';
        }
        $data['msg'] = $msg;
        $data['qry'] = $qry;
        $data['inp'] = $this->input->post();
        return json_encode($data);
    }

    function get_customer_ecales_table($ecalesid = false, $status = false) {
        $data = array();
        if($ecalesid == false) {
            $ecalesid = $this->input->post('ecalesid');
        }

        if($status == true) {
            $this->db->where('it.status', $status);
        } else {
            $this->db->where('it.status != ', 0);
        }
        $qry = $this->db->select('it.sysid, ms.descs, it.itemid, it.qty, it.status, it.customerprovided as custprov')
            ->from('customer_ecales_item_trn AS it')
            ->join('items_main_spec AS ms', 'it.itemid = ms.sysid', 'left')
            ->where(array('it.ecalesid' => $ecalesid, 'it.status != ' => 0))
            ->order_by('ms.descs')
            ->get();

        $data['item_qry'] = $this->db->last_query();

        $total_amt = 0;
        $total_qty = 0;
        $cust_qty = 0;
        $peco_qty = 0;
        $cust_amt = 0;
        $peco_amt = 0;
        if ($qry->num_rows() > 0) {
            $i = 1;
            foreach ($qry->result() as $row) {
                $item_info = get_item_info($row->itemid);
                //$data['iteminfo'][] = $this->db->last_query();
                $row_amt = ($item_info) ? $item_info->amt : 0;
                $row_total_amt = bcmul($row_amt, $row->qty, 2);
                $supp_id = ($item_info) ? $item_info->suppid : 0;

                $total_amt += $row_total_amt;
                $total_qty += $row->qty;

                if ($row->custprov != 1) {
                    $cust_amt += $row_total_amt;
                    $cust_qty += $row->qty;
                } else {
                    $peco_amt += $row_total_amt;
                    $peco_qty += $row->qty;
                }

                $control = '';
                $checked = ($row->custprov == 1) ? 'checked' : '';

                if($row->status == 1) {
                    $control .= '<a data-id="' . $row->sysid . '" title="Remove Item" id="del_btn" href="' . base_url('analysis/delecalesitem') . '" class="btn btn-danger btn-xs inline"><i class="fa fa-times"></i></a>';
                    $amt = '<input name="item_price" id="item_price" class="form-control inline text-danger" data-id="'.$row->itemid.'" data-supplier="'.$supp_id.'" value="'.$row_amt.'">';
                    $qty = '<input name="item_qty" id="item_qty" class="form-control inline item_qty" data-id="'.$row->sysid.'" value="'.$row->qty.'">';
                    $person = '<input type="checkbox" class="icheck" data-checkbox="icheckbox_square-grey" id="customer_provided" data-ecales="'.$ecalesid.'" data-id="' . $row->sysid . '" name="customer_provided" ' . $checked . ' >';
                }else{
                    $control .= '<i class="fa fa-check text-success"></i>';
                    $amt = number_format($row_amt,2);
                    $qty = number_format($row->qty);
                    $person = ($row->custprov == 1) ? '<i class="fa fa-check text-success"></i>' : '';
                }

                $stock_total = 0;

                $stock_label = '';

                $stock_label .= '<a href="#frm_request_item" class="btn btn-default btn-xs inline pull-right" data-toggle="ajax-modal" title="ePRS Request Item(s)"><i class="fa fa-download"></i></a>';
                $stock_label .= '<code>0</code>';

                if($stock_total>0) {
                    $stock_label = 1;
                }



                $data['list'][] = array(
                    'sysid' => $row->sysid,
                    'num' => $i++,
                    'item' => $row->descs,
                    'amt' => $amt,
                    'amt1' => $row_amt,
                    'qty' =>$qty,
                    'stock' => $stock_label,
                    'qty1' =>$row->qty,
                    'total' => number_format($row_total_amt, 2),
                    'person' => $person,
                    'control' => $control,
                    'customerprov' => $row->custprov,
                );
            }
        }

        $data['ecalesnum'] = str_pad($ecalesid, 8, '0', STR_PAD_LEFT);
        $data['totalamt'] = number_format($total_amt, 2);
        $data['totalqty'] = number_format($total_qty, 0);
        $data['custqty'] = number_format($cust_qty,0);
        //$data['pecoqty'] = number_format($peco_qty,0);
        $data['custamt'] = number_format($cust_amt,2);
        //$data['pecoamt'] = number_format($peco_amt,2);
        return json_encode($data);
    }

    function del_ecales_item() {
        $data = array();
        $this->db->trans_begin();
        $id = $this->input->post('id');
        $qry = false;
        $upd_arr = array(
            'status' => 0, 'updatedby' => user_id()
        );
        $this->db->where('sysid', $id);
        $this->db->update('customer_ecales_item_trn', $upd_arr);

        if($this->db->trans_status()===true) {
            $this->db->trans_commit();
            $qry = true;
            $msg = 'Item has been deleted!';
        }else{
            $this->db->trans_rollback();
            $msg = 'Item has not deleted!';
        }
        $data['inp'] = $this->input->post();
        $data['msg'] = $msg;
        $data['qry'] = $qry;
        return json_encode($data);
    }

    function change_ecales_payable() {
        $data = array();
        $trnid = $this->input->post('trnid');
        $custprovided = $this->input->post('checked');
        $ecalesid = $this->input->post('ecalesid');
        $audit_ins = '';
        $cust_amt = 0;
        $peco_amt = 0;
        $cust_qty = 0;
        $peco_qty = 0;
        $total_amt = 0;

        $this->db->trans_begin();
        $this->db->update(
            'customer_ecales_item_trn',
            array('customerprovided' => $custprovided , 'updatedby' => user_id()),
            array('sysid' => $trnid)
        );
        $data = db_trans($this->db);
        $changed = ($custprovided == 1) ? 'Customer Provided' : 'PECO Provided';

        if ($data['qry'] == true) {
            $msg = 'Successfully changed status to ' . $changed . '.';
            $func = 'success';
            $audit_ins_arr = array(
                'dataid' => $trnid,
                'moduleid' => 13,
                'valueold' => ($custprovided == 1) ? 0 : 1,
                'valuenew' => $custprovided,
                'createdby' => user_id(),
                'remarks' => 'CAD - ECALES : Change customer provided status'
            );
            $audit_ins = audit_insert($audit_ins_arr);

            $amounts = $this->db->select('itemid , qty , customerprovided as custprov')
                ->from('customer_ecales_item_trn')
                ->where(array('ecalesid' => $ecalesid, 'status' => 1))
                ->get();

            if ($amounts->num_rows() > 0) {
                foreach ($amounts->result() as $row) {
                    $item = get_item_info($row->itemid);
                    $item_amt = ($item) ? $item->amt : 0;
                    $item_qty = ($item) ? $row->qty : 0;
                    $amt_total = $item_qty * $item_amt;
                    if ($row->custprov != 1) {
                        $cust_amt += $amt_total;
                        $cust_qty += $item_qty;
                    } else {
                        $peco_amt += $amt_total;
                        $peco_qty += $item_qty;
                    }
                }
            }
            $total_amt = $cust_amt + $peco_amt;
        } else {
            $msg = 'Failed to change status to ' . $changed . '.';
            $func = 'error';
        }

        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['audit'] = $audit_ins;
        $data['custqty'] = $cust_qty;
        //$data['pecoqty'] = $peco_qty;
        $data['custamt'] = number_format($cust_amt,2);
        //$data['pecoamt'] = number_format($peco_amt,2);
        $data['totalamt'] = number_format($total_amt,2);
        return json_encode($data);
    }

    function process_ecales() {
        $totalload = $this->input->post('totalload');
        $ecalesid = $this->input->post('ecalesid');
        $remarks = $this->input->post('remarks');
        $origin = $this->input->post('origin');
        $totalcost = 0;
        $totalqty = 0;
        $total_item_cost = 0;
        $total_service_cost = 0;
        $service_cost = 0;
        $response = array();

        $appid = $this->input->post('appid');

        $qry_items = json_decode($this->get_customer_ecales_table($ecalesid));
        //print_r($qry_items);
        if(isset($qry_items->list) && count($qry_items->list) > 0) {
            foreach($qry_items->list as $row) {
                $sysid = $row->sysid;
                $this->db->update('customer_ecales_item_trn',
                    array('status' => 2, 'updatedby' => user_id()),
                    array(
                        'status' => 1,
                        'sysid' => $sysid
                    )
                );
                //print_r('items_update'.$this->db->last_query());

                $amt = remove_number_format($row->amt1);
                $qty = remove_number_format($row->qty1);
                if ($row->customerprov != 1) {
                    $total_item_cost += bcmul($amt, $qty, 2);
                }
                $totalqty += $row->qty1;
                $totalcost += bcmul($amt, $qty, 2);
            }
            if ($total_item_cost > 0) {
                $ins_item_charges = insert_application_charges(263, $total_item_cost, $appid, $origin, 2);
                if($ins_item_charges->qry) {
                    $qry    = true;
                    $func   = 'success';
                    $msg    = 'Materials Added!';
                } else {
                    $msg    = $ins_item_charges->errmsg;
                }
                $response[] = array(
                    'qry' => $qry,
                    'func' => $func,
                    'msg' => $msg,
                );
            }
        }
        $svcs = array();
        $svcs_update = array();
        $qry_services = json_decode($this->get_customer_ecales_services($ecalesid));
        //print_r($qry_services);
        if (isset($qry_services->list) && count($qry_services->list) > 0) {
            foreach ($qry_services->list as $row) {
                $this->db->update('customer_ecales_service_trn',
                    array('status' => 2, 'updatedby' => user_id()),
                    array('status' => 1, 'sysid' => $row->sysid)
                );

                $svcs_update[] = $this->db->last_query();
                $amt = remove_number_format($row->rate1);
                $qty = remove_number_format($row->days1);
                $svcs[] = array(
                    'sysid' => $row->sysid,
                    'amt' => $amt,
                    'qty' => $qty
                );
                $service_cost += bcmul($amt, $qty, 2);
                $svcs_cost[] = $service_cost;
            }
            $total_service_cost = bcmul($service_cost, 1.12, 2);
            $totalcost = $total_service_cost;
            if ($total_service_cost > 0) {
                $ins_service_charges = insert_application_charges(266, $service_cost, $appid, $origin, 2);
                if($ins_service_charges->qry) {
                    $qry    = true;
                    $func   = 'success';
                    $msg    = 'Services Added!';
                } else {
                    $msg    = $ins_service_charges->errmsg;
                }
                $response[] = array(
                    'qry' => $qry,
                    'func' => $func,
                    'msg' => $msg,
                );
            }
        }

        $this->db->trans_begin();
        $this->db->update('customer_ecales_logs',
            array('status' => 314, 'totalload' => $totalload, 'totalcost' => $totalcost, 'totalqty' => $totalqty , 'remarks' => $remarks, 'updatedby' => user_id()),
            array('status' => 1, 'sysid' => $ecalesid)
        );


        $data = db_trans($this->db);
        $data['charges'] = $response;
        $data['svcs'] = $svcs;
        $data['svcs_update'] = $svcs_update;
        $data['svcs_cost'] = $svcs_cost;
        return json_encode($data);
    }

    function update_ecales_items() {
        $data = array();
        $input_arr = $this->input->post();
        $data['type'] = $input_arr['type'];

        if ($input_arr['type'] == 'price') {
            $exist = false;
            $msg = 'Item price not changed.';
            $func = 'info';
            $price = $input_arr['amt'];

            unset($input_arr['type']);
            $find = $this->db->select('itemspecid,suppid,amt')
                ->from('trn_prs_quotations')
                ->where('itemspecid', $input_arr['itemspecid'])
                ->order_by('datecreated', 'desc')->get()->row();

            if ($find) {
                $find_values = (array)$find;
                if ($find_values == $input_arr) {
                    $exist = true;
                }
            }

            if (!$exist) {
                $input_arr['createdby'] = user_id();
                $this->db->insert('trn_prs_quotations', $input_arr);
                $error = $this->db->_error_message();

                if (!$error || $error == '') {
                    $msg = 'Item price updated.';
                    $func = 'success';
                }
            }
            $data['price'] = number_format($price,2);
            $data['exist'] = $exist;
        } else {
            $msg = 'Item quantity not changed!';
            $func = 'info';
            unset($input_arr['type']);
            $trn_qry = $this->db->select()
                ->from('customer_ecales_item_trn')
                ->where($input_arr)->get()->row();

            if (!$trn_qry) {
                $this->db->update('customer_ecales_item_trn',array('qty' => $input_arr['qty'], 'updatedby' => user_id()),array('sysid' => $input_arr['sysid']));
                $error = $this->db->_error_message();

                if (!$error || $error == '') {
                    $msg = 'Item quantity successfully changed!';
                    $func = 'info';
                    $data['qty'] = $input_arr['qty'];
                }
            }
        }

        //$data['inputs'] = $input_arr;
        //$data['find'] = $find_values;
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['inputs'] = $input_arr;

        return json_encode($data);
    }

    function get_customer_ecales_services($ecalesid = false) {
        $data = array();
        if ($ecalesid == false) {
            $ecalesid = $this->input->post('ecalesid');
        }
        $days = 0;

        $services_qry = $this->db->select('cest.sysid as trnid, cest.status as trnstat, cest.days, psm.*')
            ->from('customer_ecales_service_trn as cest')
            ->join('prime_services_main as psm','cest.serviceid = psm.sysid','left')
            ->where(array('cest.ecalesid' => $ecalesid, 'cest.status != ' => 0))
            ->get();

        $data['svcs_qry'] = $this->db->last_query();

        $total_cost = 0;
        $total_days = 0;
        if ($services_qry->num_rows() > 0) {
            $i = 1;
            foreach ($services_qry->result() as $row) {
                $rate = $this->db->select('servicerate')
                    ->from('prime_service_rate_history')
                    ->where(array('serviceid' => $row->sysid , 'status' => 1))
                    ->order_by('sysid','DESC')->get()->row();

                $amt_total = $rate->servicerate * $row->days;
                $total_cost += $amt_total;
                $total_days += $row->days;
                $days = ($row->days) ? $row->days : 0;

                $control = '';
                if($row->trnstat == 1) {
                    $control .= '<a data-id="' . $row->trnid . '" title="Remove Item" id="del_btn" href="' . base_url('analysis/delecalesservice') . '" class="btn btn-danger btn-xs inline"><i class="fa fa-times"></i></a>';
                    $amt = '<input name="service_rate" id="service_rate" class="form-control inline text-danger" data-id="'.$row->sysid.'" value="'.number_format($rate->servicerate,2).'">';
                    $day = '<input name="no_days" id="no_days" class="form-control inline text-info" data-serv="'.$row->trnid.'" data-id="'.$ecalesid.'" value="'.$days.'">';
                }else{
                    $control .= '<i class="fa fa-check text-success"></i>';
                    $amt = number_format($rate->servicerate,2);
                    $day = number_format($days,1);
                }

                $data['list'][] = array(
                    'sysid' => $row->sysid,
                    'num' => $i++,
                    'service' => $row->names,
                    'rate' => $amt,
                    'rate1' => $rate->servicerate,
                    'days' => $day,
                    'days1' => $days,
                    'total' => number_format($amt_total, 2),
                    'control' => $control,
                    'status' => $row->trnstat,
                );
            }
        }
        $data['days'] = '<input name="no_days" id="no_days" class="form-control inline text-info" data-id="'.$ecalesid.'" value="'.$days.'">';
        $data['totalcost'] = number_format($total_cost,2);
        return json_encode($data);
    }

    function update_ecales_service() {
        $data = array();
        $input_arr = $this->input->post();
        $data['type'] = $input_arr['type'];

        if ($input_arr['type'] == 'rate') {
            $exist = false;
            $msg = 'Service rate not changed.';
            $func = 'info';
            $rate = $input_arr['servicerate'];

            unset($input_arr['type']);
            $find = $this->db->select('serviceid,servicerate')
                ->from('prime_service_rate_history')
                ->where('serviceid', $input_arr['serviceid'])
                ->order_by('datecreated', 'desc')->get()->row();

            if ($find) {
                $find_values = (array)$find;
                if ($find_values == $input_arr) {
                    $exist = true;
                }
            }

            if (!$exist) {
                $input_arr['createdby'] = user_id();
                $this->db->insert('prime_service_rate_history', $input_arr);
                $error = $this->db->_error_message();

                if (!$error || $error == '') {
                    $msg = 'Service rate updated.';
                    $func = 'success';
                }
            }
            $data['rate'] = number_format($rate,2);
            $data['exist'] = $exist;
        } else {
            $msg = 'No of days not changed!';
            $func = 'info';
            unset($input_arr['type']);
            $trn_qry = $this->db->select('serviceid,days')
                ->from('customer_ecales_service_trn')
                ->where($input_arr)->get()->row();

            if (!$trn_qry) {
                $this->db->update('customer_ecales_service_trn',array('days' => $input_arr['days']),array('sysid' => $input_arr['sysid'],'ecalesid' => $input_arr['ecalesid'], 'updatedby' => user_id()));
                $error = $this->db->_error_message();

                if (!$error || $error == '') {
                    $msg = 'Number of days successfully changed!';
                    $func = 'success';
                }
            }
            $data['days'] = number_format($input_arr['days'],1);
        }

        //$data['inputs'] = $input_arr;
        //$data['find'] = $find_values;
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['inputs'] = $input_arr;

        return json_encode($data);
    }

    function del_ecales_service() {
        $data = array();
        $this->db->trans_begin();
        $id = $this->input->post('id');
        $qry = false;
        $upd_arr = array(
            'status' => 0, 'updatedby' => user_id()
        );
        $this->db->where('sysid', $id);
        $this->db->update('customer_ecales_service_trn', $upd_arr);

        if($this->db->trans_status()===true) {
            $this->db->trans_commit();
            $qry = true;
            $msg = 'Service/Equipment has been deleted!';
        }else{
            $this->db->trans_rollback();
            $msg = 'Service/Equipment was not deleted!';
        }
        $data['inp'] = $this->input->post();
        $data['msg'] = $msg;
        $data['qry'] = $qry;
        return json_encode($data);
    }

    function get_ecales_summary($ecalesid = false) {
        if ($ecalesid == false) {
            $ecalesid = $this->input->post('ecalesid');
        }
        $data = array();
        $total_amt = 0;
        $total_qty = 0;
        $cust_qty = 0;
        $peco_qty = 0;
        $cust_amt = 0;
        $peco_amt = 0;
        $i = 1;
        $total_vat = 0;
        $total_vat_customer = 0;
        $total_vat_peco = 0;
        $customer_items_total = 0;
        $customer_snu_vat = 0;
        $customer_items_amt = 0;
        $customer_items_vat = 0;
        $customer_snu_amt= 0;

        $items_qry = $this->db->select('
            it.sysid, 
            ms.descs, 
            it.itemid, 
            it.qty, 
            u.unit_name as unit, 
            it.`status`, 
            it.customerprovided AS custprov
            ')
            ->from('customer_ecales_item_trn AS it')
            ->join('items_main_spec AS ms','it.itemid = ms.sysid','left')
            ->join('prime_unit AS u','ms.unitid = u.sysid','left')
            ->where(array('it.ecalesid' => $ecalesid, 'it.status !=' => 0))
            ->order_by('ms.descs','ASC')->get();

        if ($items_qry->num_rows() > 0) {
            foreach ($items_qry->result() AS $row) {
                $item_info = get_item_info($row->itemid);
                $row_amt = ($item_info) ? $item_info->amt : 0;
                $row_total_amt = bcmul($row_amt, $row->qty, 2);
                $total_amt += $row_total_amt;
                $total_qty += $row->qty;

                if ($row->custprov == 1) {
                    $cust_amt += $row_total_amt;
                    $cust_qty += $row->qty;
                    $paidby = 'Customer';
                } else {
                    $peco_amt += $row_total_amt;
                    $peco_qty += $row->qty;
                    $paidby = 'PECO';
                }

                $amt = number_format($row_amt,2);
                $qty = number_format($row->qty);

                $data['list'][] = array(
                    'sysid' => $row->sysid,
                    'num' => $i++,
                    'type' => 'Items',
                    'item' => $row->descs,
                    'amt' => $amt,
                    'qty' =>$qty,
                    'unit' =>($row->unit) ? $row->unit : 'Unit(s)',
                    'total' => number_format($row_total_amt, 2),
                    'paidby' => $paidby,
                    'acctid' => 266
                );

            }
            $customer_items_vat = bcmul($peco_amt,0.12,2);
            $customer_items_amt = bcsub($peco_amt,$customer_items_vat,2);
            $customer_items_total = $peco_amt;
        }

        $services_qry = $this->db->select('cest.sysid as trnid, cest.status as trnstat, cest.days, psm.*')
            ->from('customer_ecales_service_trn as cest')
            ->join('prime_services_main as psm','cest.serviceid = psm.sysid','left')
            ->where(array('cest.ecalesid' => $ecalesid, 'cest.status != ' => 0))
            ->get();

        $total_cost = 0;
        $total_days = 0;
        $snu_total = 0;
        if ($services_qry->num_rows() > 0) {
            foreach ($services_qry->result() as $row) {
                $rate = $this->db->select('servicerate')
                    ->from('prime_service_rate_history')
                    ->where(array('serviceid' => $row->sysid , 'status' => 1))
                    ->order_by('sysid','DESC')->get()->row();

                $amt_total = $rate->servicerate * $row->days;
                $total_amt += $amt_total;
                $days = ($row->days) ? $row->days : 0;

                $cust_amt += $amt_total;

                $snu_total += $amt_total;

                $amt = number_format($rate->servicerate,2);
                $day = number_format($days,1);

                $nonvat_amt = bcdiv($amt_total, 1.12, 2);
                $vat_amt = bcsub($amt_total, $nonvat_amt, 2);
                //$amt = bcadd($nonvat_amt, $vat_amt, 2);

                $total_vat += $vat_amt;

                $data['list'][] = array(
                    'sysid' => $row->sysid,
                    'num' => $i++,
                    'type' => 'Utility & Services',
                    'item' => $row->names,
                    'amt' => $amt,
                    'qty' => $day,
                    'unit' => 'Day(s)',
                    'total' => number_format($amt_total, 2),
                    'paidby' => 'PECO',
                    'acctid' => 263
                );
            }
            $customer_snu_vat = bcmul($snu_total, 0.12, 2);
            $customer_snu_amt = bcadd($snu_total, $customer_snu_vat, 2);
        }

        $cust_total_amt = bcadd($customer_items_total,$snu_total,2);
        $cust_gt = bcadd($cust_total_amt,$customer_snu_vat,2);

        $data['summary_items_amt'] = number_format($customer_items_amt,2);
        $data['summary_items_vat'] = number_format($customer_items_vat,2);
        $data['summary_items_total'] = number_format($customer_items_total,2);
        $data['summary_util_amt'] = number_format($snu_total,2);
        $data['summary_util_vat'] = number_format($customer_snu_vat,2);
        $data['summary_util_total'] = number_format($customer_snu_amt,2);
        $data['summary_total_amt'] = number_format($cust_total_amt,2);
        $data['summary_total_vat'] = number_format($customer_snu_vat,2);
        $data['summary_grand_total'] = number_format($cust_gt,2);
        return json_encode($data);
    }

    function get_ecales_info() {
        $data = array();
        $ecales_id = $this->input->post('id');

        $qry = $this->db->select()
            ->from('customer_ecales_logs')
            ->where(array('sysid' => $ecales_id, 'status' => 314))->get()->row();

        if ($qry) {
            $data = $qry;
        }

        return json_encode($data);
    }

    function revoke_ecales() {
        $data = array();
        $ecalesid = $this->input->post('ecalesid');
        $origin = $this->input->post('origin');
        $iad_action = $this->input->post('indaction');
        $reason = $this->input->post('reason');

        $msg = 'New ECALES has been created!';
        $func = 'success';
        $error = array();
        $ecales_log_revoked = false;
        $ecales_items_revoked = false;
        $ecales_svcs_revoked = false;
        $ecales_charges_revoked = false;

        $ecales_info = $this->db->select()
            ->from('customer_ecales_logs')
            ->where(array('sysid' => $ecalesid, 'status' => 314))
            ->get()->row();

        if ($ecales_info) {
            $appid = $ecales_info->dataid;
            $flowid = $ecales_info->flowid;
            $totalload = $ecales_info->totalload;
            $totalcost = $ecales_info->totalcost;
            /*
             * 1. UPDATE ALL ITEMS, SERVICES AND EQUIPMENT TO STATUS 303
             * 2. IF IND_ACTION = 1 CREATE CHARGE WITH SAME AMOUNT AS LABOR ONLY
             * 3. IF IND_ACTION = 2 COPY I&D DETAILS AND INSERT WITH SERVICEID 4
             */

            //UPDATE ECALES_LOG
            $this->db->update('customer_ecales_logs',array('status' => 303, 'updatedby' => user_id()),array('sysid' => $ecalesid));
            if($this->db->affected_rows() > 0) {
                $ecales_log_revoked = true;
            } else {
                $error[] = 'ECALES not revoked.';
            }

            $status = array('status' => 303, 'updatedby' => user_id());
            $where = array('ecalesid' => $ecalesid, 'status' => 2);

            //UPDATE SERVICES
            $this->db->update('customer_ecales_service_trn',$status,$where);
            if($this->db->affected_rows() > 0) {
                $ecales_svcs_revoked = true;
            } else {
                $error[] = 'Services not revoked.';
            }

            //UPDATE ITEMS
            $this->db->update('customer_ecales_item_trn',$status,$where);
            if($this->db->affected_rows() > 0) {
                $ecales_items_revoked = true;
            } else {
                $error[] = 'Items not revoked.';
            }

            //UPDATE CHARGES
            $this->db->where_in('chargeid',array(263,266));
            $this->db->update('application_customers_charges', array('status' => 0, 'updatedby' => user_id()));
            if($this->db->affected_rows() > 0) {
                $ecales_charges_revoked = true;
            } else {
                $error[] = 'Charges not cancelled.';
            }

            if ($ecales_log_revoked && $ecales_svcs_revoked && $ecales_items_revoked && $ecales_charges_revoked) {
                $iad_trans = false;
                //LOG REVOKE
                $revoke_log_arr = array(
                    'ecalesid' => $ecalesid,
                    'reason' => $reason,
                    'indcharge' => $iad_action,
                    'createdby' => user_id()
                );
                $this->db->insert('ecales_revokation_logs',$revoke_log_arr);
                if($this->db->affected_rows() > 0) {
                    $revokeid = $this->db->insert_id();
                    $audit_ins_arr = array(
                        'dataid' => $appid,
                        'moduleid' => 0,
                        'valueold' => '314 - Accomplished',
                        'valuenew' => '303 - Canceled',
                        'createdby' => user_id(),
                        'remarks' => 'ECALES Revoked LogID: ' .$revokeid.'. ECALES ID: ' . $ecalesid
                    );
                    audit_insert($audit_ins_arr);
                }
                $iad_qry = $this->db->select('cest.serviceid,cest.days,psrh.servicerate')
                    ->from('customer_ecales_service_trn as cest')
                    ->join('prime_service_rate_history as psrh','psrh.serviceid = cest.serviceid','left')
                    ->where(array('cest.ecalesid' => $ecalesid, 'cest.serviceid' => 1, 'cest.status = ' => 303))
                    ->order_by('psrh.servicerate','desc')
                    ->get()->row();

                $new_ecales_arr = array(
                    'dataid' => $appid,
                    'flowid' => $flowid,
                    'createdby' => user_id(),
                );

                if ($iad_qry) {
                    //CREATE CHARGE EQUIVALENT TO LABOR ONLY
                    /*$data['iad'] = $iad_qry;
                    echo '<pre>';
                    print_r($iad_qry);
                    print_r($this->db->last_query());
                    exit();*/
                    if ($iad_action == 1) {
                        $days = $iad_qry->days;
                        $rate = $iad_qry->servicerate;

                        $iad_amt = bcmul($rate, $days, 2);
                        $create_charge = insert_application_charges(266, $iad_amt, $appid, $origin, 2);

                        if ($create_charge && $create_charge->qry == true) {
                            $this->db->insert('customer_ecales_logs',$new_ecales_arr);
                            if ($this->db->affected_rows() > 0) {
                                $msg = 'New ECALES has been created!';
                                $func = 'success';
                            } else {
                                $msg = 'Failed to create new ECALES!';
                                $func = 'error';
                            }
                        }
                    } else {
                        //CREATE SERVICE AND INSERT WITH SERVICEID 4
                        if ($iad_action == 2) {
                            $this->db->insert('customer_ecales_logs',$new_ecales_arr);
                            $new_ecales = $this->db->insert_id();

                            $create_service_arr = array(
                                'ecalesid' => $new_ecales,
                                'serviceid' => 4,
                                'days' => $iad_qry->days,
                                'createdby' => user_id(),
                                'status' => 2
                            );
                            $this->db->insert('customer_ecales_service_trn',$create_service_arr);
                            if ($this->db->affected_rows() > 0) {
                                $msg = 'New ECALES has been created!';
                                $func = 'success';
                            } else {
                                $msg = 'Failed to create new ECALES!';
                                $func = 'error';
                            }
                        }
                    }
                }
            } else {
                $msg = join(" | ",$error);
                $func = 'error';
            }
        }

        $data['msg'] = $msg;
        $data['func'] = $func;
        return json_encode($data);
    }

    function ecales_revoked_logs() {
        $data = array();
        $appid = $this->input->post('appid');

        $logs_qry = $this->db->select('cel.sysid, cel.totalload, cel.totalcost, cel.totalqty, cel.remarks, erl.indcharge, erl.reason, erl.attachment')
            ->from('ecales_revokation_logs AS erl')
            ->join('customer_ecales_logs AS cel','erl.ecalesid = cel.sysid AND erl.`status` = 1','left')
            ->where(array('cel.dataid' => $appid, 'cel.status' => 303))
            ->get();

        if ($logs_qry->num_rows() > 0) {
            foreach ($logs_qry->result() as $log) {
                $iadcharge = ($log->indcharge == 1) ? 'Pay Insp. & Design first.' : 'Add to NEW computation';
                $attachment = ($log->attachment) ? '<a href="'.base_url().$log->attachment.'" class="cbp-caption cbp-lightbox iframe text-center"><i class="fa fa-search text-info"></i></a>' : '<a class="btn btn-xs btn-danger"><i class="fa fa-times"></i></a>';
                $data['list'][] = array(
                    'expand' => btn_expand($log->sysid),
                    'totalload' => number_format($log->totalload,0).' Watts',
                    'totalcost' => number_format($log->totalcost,2),
                    'totalqty' => number_format($log->totalqty,0),
                    'remarks' => $log->remarks,
                    'indcharge' => $iadcharge,
                    'reason' => $log->reason,
                    'attachment' => $attachment,
                );
            }
        }

        return json_encode($data);
    }

    function get_ecales_subdetails() {
        $data = array();
        $id = $this->input->post('id');
        $tbl_details = '';
        $html = '';

        $ecales_data = $this->get_ecales_summary($id);

        $ecales_data = json_decode($ecales_data);

        if (count($ecales_data->list) > 0) {
            foreach ($ecales_data->list as $row) {
                $tbl_details .= '<tr>';
                $tbl_details .= '<td class="text-align-center">'.$row->num.'</td>';
                $tbl_details .= '<td>'.$row->type.'</td>';
                $tbl_details .= '<td>'.$row->item.'</td>';
                $tbl_details .= '<td class="number">'.$row->amt.'</td>';
                $tbl_details .= '<td class="number">'.$row->qty.'</td>';
                $tbl_details .= '<td>'.$row->unit.'</td>';
                $tbl_details .= '<td class="number">'.$row->total.'</td>';
                $tbl_details .= '<td class="text-align-center">'.$row->paidby.'</td>';
                $tbl_details .= '</tr>';
            }

            $html .= '<div class="portlet light table bordered">';
            $html .= '<div class="portlet-title">';
            $html .= '<div class="caption col-md-6">';
            $html .= '<i class="fa fa-edit"></i>';
            $html .= '<span class="caption-subject font-green-sharp bold uppercase">ECALES ITEMS AND SERVICES SUMMARY</span>';
            $html .= '<span class="caption-helper"></span>';
            $html .= '</div>';
            $html .= '<div class="portlet-body">';
            $html .= '<table width="100%" class="table table-bordered table-hover table-striped table-condensed table-striped">';
            $html .= '<thead>';
            $html .= '<th><i class="fa fa-bars"></i></th>';
            $html .= '<th>Type</th>';
            $html .= '<th>Item</th>';
            $html .= '<th>Amount</th>';
            $html .= '<th>Qty</th>';
            $html .= '<th>Unit</th>';
            $html .= '<th>Total</th>';
            $html .= '<th>Provided by</th>';
            $html .= '</thead>';
            $html .= '<tbody>';
            $html .= $tbl_details;
            $html .= '</tbody>';
            $html .= '</table>';
            $html .= '</div>';
            $html .= '<div class="portlet-footer">';
            $html .= '<h4 class="bold text-danger">Amounts Payable by Customer</h4>';
            $html .= '<div class="row">';
            $html .= '<div class="col-md-4">';
            $html .= '<h4 class="bold">Utilities & Services</h4>';
            $html .= '<ul class="list-group summary column no-border">';
            $html .= '<li class="list-group-item">';
            $html .= '<span class="label label-name col-md-6 bold">Amount</span>';
            $html .= '<span class="col-md-6 text-primary text-bold number">'.$ecales_data->summary_util_amt.'</span>';
            $html .= '</li>';
            $html .= '<li class="list-group-item">';
            $html .= '<span class="label label-name col-md-6 bold">VAT(+12%)</span>';
            $html .= '<span class="col-md-6 text-primary text-bold number">'.$ecales_data->summary_util_vat.'</span>';
            $html .= '</li>';
            $html .= '<li class="list-group-item">';
            $html .= '<span class="label label-name col-md-6 bold">Total</span>';
            $html .= '<span class="col-md-6 text-primary text-bold number">'.$ecales_data->summary_util_total.'</span>';
            $html .= '</li>';
            $html .= '</ul>';
            $html .= '</div>';
            $html .= '<div class="col-md-4">';
            $html .= '<h4 class="bold">Items</h4>';
            $html .= '<ul class="list-group summary column no-border list-group-sm">';
            $html .= '<li class="list-group-item">';
            $html .= '<span class="label label-name col-md-6 bold"> Amount(Ex-VAT)</span>';
            $html .= '<span class="col-md-6 text-primary text-bold number">'.$ecales_data->summary_items_amt.'</span>';
            $html .= '</li>';
            $html .= '<li class="list-group-item">';
            $html .= '<span class="label label-name col-md-6 bold">VAT(Inc.)</span>';
            $html .= '<span class="col-md-6 text-primary text-bold number">'.$ecales_data->summary_items_vat.'</span>';
            $html .= '</li>';
            $html .= '<li class="list-group-item">';
            $html .= '<span class="label label-name col-md-6 bold">Total</span>';
            $html .= '<span class="col-md-6 text-primary text-bold number">'.$ecales_data->summary_items_total.'</span>';
            $html .= '</li>';
            $html .= '</ul>';
            $html .= '</div>';
            $html .= '<div class="col-md-4">';
            $html .= '<h4 class="bold">Total</h4>';
            $html .= '<ul class="list-group summary column no-border list-group-sm">';
            $html .= '<li class="list-group-item">';
            $html .= '<span class="label label-name col-md-6 bold">Total Amount</span>';
            $html .= '<span class="col-md-6 text-primary text-bold number">'.$ecales_data->summary_total_amt.'</span>';
            $html .= '</li>';
            $html .= '<li class="list-group-item">';
            $html .= '<span class="label label-name col-md-6 bold">Total VAT</span>';
            $html .= '<span class="col-md-6 text-primary text-bold number">'.$ecales_data->summary_total_vat.'</span>';
            $html .= '</li>';
            $html .= '<li class="list-group-item">';
            $html .= '<span class="label label-name col-md-6 bold">Grand Total</span>';
            $html .= '<span class="col-md-6 text-primary text-bold number">'.$ecales_data->summary_grand_total.'</span>';
            $html .= '</li>';
            $html .= '</ul>';
            $html .= '</div>';
            $html .= '</div>';
            $html .= '</div>';
            $html .= '</div>';
        }

        $data['html'] = $html;
        return json_encode($data);
    }

    function save_ecales_template() {
        $data = array();
        $name = $this->input->post('name');
        $ecalesid = $this->input->post('ecalesid');
        $qry = false;
        $msg = '';
        $title = 'Failed!';
        $func = 'error';

        $items_qry = $this->db->select('itemid,customerprovided,qty')
            ->from('customer_ecales_item_trn')
            ->where(array('ecalesid' => $ecalesid, 'status' => 1))
            ->get();

        if ($items_qry->num_rows() > 0) {
            foreach ($items_qry->result() as $row) {
                $templateitems[] = array(
                    'typeid' => 263,
                    'itemid' => $row->itemid,
                    'qty' => $row->qty,
                    'custprovided' => $row->customerprovided,
                );
            }
        }

        $svcs_qry = $this->db->select('serviceid,days')
            ->from('customer_ecales_service_trn')
            ->where(array('ecalesid' => $ecalesid, 'status' => 1, 'serviceid != ' => 4))
            ->get();

        if ($svcs_qry->num_rows() > 0) {
            foreach ($svcs_qry->result() as $row) {
                $templateitems[] = array(
                    'typeid' => 266,
                    'itemid' => $row->serviceid,
                    'qty' => $row->days,
                    'custprovided' => 0,
                );
            }
        }

        $insert_arr = array(
            'name' => $name,
            'createdby' => user_id()
        );

        $this->db->insert('ecales_templates_main',$insert_arr);
        $templateid = $this->db->insert_id();

        if ($templateid) {
            $num = 0;
            foreach ($templateitems as $ins_arr) {
                $ins_arr['templateid'] = $templateid;
                $ins_arr['createdby'] = user_id();
                $this->db->insert('ecales_template_items',$ins_arr);
                //$data['insert_queries'][] = $this->db->last_query();
                if ($this->db->insert_id()) {
                    $num++;
                }
            }

            if ($num > 0) {
                $qry = true;
                $title = 'Success!';
                $msg = $num.' item(s) added to template '.$name.'.';
                $func = 'success';
            }
        }

        $data['qry'] = $qry;
        $data['title'] = $title;
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['num'] = $num;
        return json_encode($data);
    }

    function dt_ecales_templates() {
        $data = array();
        $ecalesid = $this->input->post('ecalesid');

        $templates_qry = $this->db->select()
            ->from('ecales_templates_main')
            ->where('status',1)->get();

        if ($templates_qry->num_rows() > 0) {
            foreach ($templates_qry->result() as $row) {
                $controls = '<button type="button" id="btn_delete_ecales_template" class="btn btn-danger inline" data-id="'.$row->sysid.'"><i class="fa fa-trash"></i> </button>';
                $controls .= '<button type="button" id="btn_apply_ecales_template" class="btn btn-info inline" data-id="'.$row->sysid.'"><i class="fa fa-sign-out"></i> </button>';
                $data['list'][] = array(
                    'expand' => btn_expand($row->sysid),
                    'name' => $row->name,
                    'desc' => '<input name="templatedesc" id="input_ecalest_template_description" class="form-control inline" style="width: 100% !important;">'.$row->desc.'</input>',
                    'control' => $controls
                );
            }
        }

        return json_encode($data);
    }


    function get_ecales_template_details() {
        $data = array();
        $templateid = $this->input->post('id');
        $itemlist = '';
        $list_items = $this->db->select('typeid,itemid,qty,custprovided')
            ->from('ecales_template_items')
            ->where(array('templateid' => $templateid, 'status' => 1))
            ->group_by('typeid,itemid')
            ->get();

        if ($list_items->num_rows() > 0) {
            $num = 1;
            foreach ($list_items->result() as $row) {
                $custprov = ($row->custprovided == 1) ? 'Customer' : 'PECO';
                if ($row->typeid == 263) {
                    $type = 'Items/Materials';
                    $iteminfo = get_item_info($row->itemid);
                    $itemname = ($iteminfo) ? $iteminfo->descs : 'N/A';
                    $itemamt = ($iteminfo) ? $iteminfo->amt : 0.00;

                    $unit_qry = $this->db->select('pu.unit_code')
                        ->from('items_main_spec as ims')
                        ->join('prime_unit as pu','ims.unitid = pu.sysid','left')
                        ->where('ims.sysid',$row->itemid)->get()->row();

                    $unit = ($unit_qry) ? $unit_qry->unit_code : 'Unit(s)';
                } else {
                    /*
                    $type = 'Service/Equipments';
                    $svcs_name = $this->db->select('names')
                        ->from('prime_services_main')
                        ->where('sysid',$row->itemid)->get()->row();

                    $svcs_amt = $this->db->select('servicerate')
                        ->from('prime_service_rate_history')
                        ->where(array('serviceid' => $row->itemid , 'status' => 1))
                        ->order_by('sysid','DESC')->get()->row();

                    $itemname = ($svcs_name) ? $svcs_name->names : 'N/A';
                    $itemamt = ($svcs_amt) ? $svcs_amt->servicerate : 0.00;
                    $unit = 'Day(s)';
                    */
                }

                $qty = ($row->qty != '') ? $row->qty : 0;

                $total = bcmul($qty,$itemamt);

                $itemlist .= '<tr>';
                $itemlist .= '<td>'.$num++.'</td>';
                $itemlist .= '<td>'.$type.'</td>';
                $itemlist .= '<td>'.$itemname.'</td>';
                $itemlist .= '<td class="number">'.number_format($itemamt,2).'</td>';
                $itemlist .= '<td class="number">'.$qty.'</td>';
                $itemlist .= '<td>'.$unit.'</td>';
                $itemlist .= '<td class="number">'.number_format($total,2).'</td>';
                $itemlist .= '<td class="text-align-center">'.$custprov.'</td>';
                $itemlist .= '</tr>';
            }
        }

        $html = '';
        $html .= '<table width="100%" class="table table-bordered table-hover table-striped table-condensed table-striped" id="tbl_ecales_summary">';
        $html .= '<thead>';
        $html .= '<th><i class="fa fa-bars"></i></th>';
        $html .= '<th>Type</th>';
        $html .= '<th>Item</th>';
        $html .= '<th>Amount</th>';
        $html .= '<th>Qty</th>';
        $html .= '<th>Unit</th>';
        $html .= '<th>Total</th>';
        $html .= '<th>Provided by</th>';
        $html .= '</thead>';
        $html .= '<tbody>';
        $html .= $itemlist;
        $html .= '</tbody>';
        $html .= '</table>';

        $data['html'] = $html;
        return json_encode($data);
    }

    function apply_template() {
        $data = array();
        $templateid = $this->input->post('templateid');
        $ecalesid = $this->input->post('ecalesid');
        $msg = '';
        $func = 'error';
        $qry = false;

        $itemcnt = 0;
        $svcscnt = 0;

        $templateitems_qry = $this->db->select()
            ->from('ecales_template_items')
            ->where(array('templateid' => $templateid, 'status' => 1))
            ->get();

        if ($templateitems_qry->num_rows() > 0) {
            //REMOVE ALL MATERIALS UNTER ITEMS
            $this->db->update(
                'customer_ecales_item_trn',
                array('status' => 0, 'updatedby' => user_id()),
                array('ecalesid' => $ecalesid)
            );

            //REMOVE SERVICES EXCEPT RE-ECALES CHARGE
            $this->db->update(
                'customer_ecales_service_trn',
                array('status' => 0, 'updatedby' => user_id()),
                array('ecalesid' => $ecalesid,'serviceid != ' => 4)
            );

            foreach ($templateitems_qry->result() as $row) {
                //Add Template Items/Materials to ECALES
                if ($row->typeid == 263) {
                    $items_arr = array(
                        'ecalesid' => $ecalesid,
                        'itemid' => $row->itemid,
                        'qty' => $row->qty,
                        'customerprovided' => $row->custprovided,
                        'createdby' => user_id()
                    );
                    $this->db->insert('customer_ecales_item_trn',$items_arr);
                    if ($this->db->insert_id() > 0) {
                        $itemcnt++;
                    }
                }

                //Add Template Services to ECALES
                if ($row->typeid == 266) {
                    $svcs_arr = array(
                        'ecalesid' => $ecalesid,
                        'serviceid' => $row->itemid,
                        'days' => $row->qty,
                        'createdby' => user_id()
                    );
                    $this->db->insert('customer_ecales_service_trn',$svcs_arr);
                    if ($this->db->insert_id() > 0) {
                        $svcscnt++;
                    }
                }
            }

            $msg = $itemcnt.' item(s) and '.$svcscnt.' service(s) added to ECALES.';
            $func = 'success';
        }

        $data['msg'] = $msg;
        $data['func'] = $func;

        return json_encode($data);
    }

}