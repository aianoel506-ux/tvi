<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

Class Model_billing extends CI_Model {

    function get_smample_billing() {
        $this->datatables->select('p.sysid, addr.addrspec');
        $this->datatables->select("CONCAT(p.lastname,', ', p.firstname, ' ', p.middlename) as name", false);
        $this->datatables->add_column('mtr', '1');
        $this->datatables->add_column('servno', 'M000001');
        $this->datatables->add_column('due', date('Y-m-d'));
        $this->datatables->add_column('interest', '0.00');
        $this->datatables->add_column('prevamt', '0.00');
        $this->datatables->add_column('current', '0.00');
        $this->datatables->add_column('total', '0.00');
        $this->datatables->add_column('status', '<span class="label label-success">Printed</span>');
        $this->datatables->add_column('expand', '$1', 'btn_expand(sysid)');
        $this->datatables->add_column('control', '<button class="btn btn-info btn-xs"><i class="fa fa-search"></i></button><button class="btn btn-default btn-xs"><i class="fa fa-book"></i></button>');
        $this->datatables->from("person p");
        $this->datatables->join("person_address_matrix addr", 'addr.personid = p.sysid', 'left');
        $this->datatables->where("p.sysid >= ", 10);
        return $this->datatables->generate();
    }

    function get_select2_multcode() {
        $data = array();
        $qry_mult = $this->db->select()->from('billing_rates_main_multiplier')->get();
        if($qry_mult->num_rows() > 0) {
            foreach($qry_mult->result() as $row) {
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->codes . ' - ' .$row->rate
                );
            }
        }
        return json_encode($data);
    }

    function get_billing_details() {
        $data = array();
        $id = $this->input->post('id');
        $html = '';
        $qry = $this->db->select()->from('billing_reports')
            ->where(array('sysid' => $id))
            ->get()->row();
        if($qry) {

            $current = $qry->current;
            // ##############################################
            // PECO RELATED CHARGES
            // AMT
            $disamt = $qry->disamt;
            $demamt = $qry->demamt;
            $supamt = $qry->supamt;
            $supper = $qry->supper;
            $mtramt = $qry->mtramt;
            // CHARGES
            $dischg = $qry->dischg;
            $demchg = $qry->demchg;
            $supchg = $qry->supchg;
            $mtrchg = $qry->mtrchg;
            $mtrper = $qry->mtrper;
            $total_peco_charges = ($disamt + $demamt + $supamt + $supper + $mtramt + 5);
            $total_peco_charges_percent = ($total_peco_charges / $current ) * 100;
            // ##############################################
            // SUPPLIER RELATED CHARGES (PPC, DEPC)
            // AMT
            $genamt = $qry->genamt;
            $genamt1 = $qry->genamt1;
            $trnamt = $qry->trnamt;
            $slamt = $qry->slamt;
            $papc = $qry->papc;
            // CHARGES
            $genchg = $qry->genchg;
            $genchg1 = $qry->genchg1;
            $trnchg = $qry->trnchg;
            $slchg = $qry->slchg;
            $papcchg = $qry->papcchg;
            $total_supplier_charges = ($genamt + $genamt1 + $trnamt + $slamt + $papc);
            $total_supplier_charges_percent = ($total_supplier_charges / $current ) * 100;
            // ##############################################
            // SUBSIDIES
            // AMT
            $iccamt = $qry->iccamt;
            $iccsub = $qry->iccsub;
            $iccsamt = $qry->iccsamt;
            $llramt = $qry->llramt;
            $llrsub = $qry->llrsub;
            $lldamt = $qry->lldamt;
            // CHARGES
            $iccschg = $qry->iccschg;
            $total_subsidies_charges = ($iccamt + $llramt);
            $total_subsidies_charges_percent = ($total_subsidies_charges / $current ) * 100;
            // ##############################################
            // TAXES AND UNIVERSAL CHARGES
            // AMT
            $genvat = $qry->genvat;
            $trnvat = $qry->trnvat;
            $disvat = $qry->disvat;
            $slvat = $qry->slvat;
            $othvat = ($qry->othvat + $slvat + $disvat);
            $misamt = $qry->misamt;
            $envamt = $qry->envamt;
            $framt = $qry->framt;
            $npcamt = $qry->npcamt;
            $fitamt = $qry->fitamt;
            // CHARGES
            $mischg = $qry->mischg;
            $envchg = $qry->envchg;
            $npcchg = $qry->npcchg;
            $fitchg = $qry->fitchg;
            $total_tax_universal_charges = ($genvat + $trnvat + $othvat + $misamt + $envamt + $framt + $npcamt + $fitamt);
            $total_tax_universal_charges_percent = ($total_tax_universal_charges / $current ) * 100;
            // #######################################
            $appsur = $qry->appsur;
            $surbal = $qry->surbal;
            $overdue = $qry->overdue;
            $totacc = $qry->totacc;
            $totint = $qry->totint;
            $scdisc = $qry->scdisc;

            $billing_period = date_formating($qry->prvdte, 'Y-m-d', 'm/d/Y'). '-' .date_formating($qry->prsdte, 'Y-m-d', 'm/d/Y');


            $html .= '<div class="col-md-4">';
            $html .= '<ul class="list-group summary column no-border">';
            $html .= '<li class="list-group-item"><span class="label label-danger col-md-6 text-bold text-align-left">PECO RELATED CHARGES</span>';
            $html .= '<span class="label label-danger col-md-3 text-align-right">PER KWH</span>';
            $html .= '<span class="label label-danger col-md-3 text-align-right">AMOUNT</span>';
            $html .= '</li>';

            $html .= '<li class="list-group-item"><span class="label label-name col-md-6">Distribution Charge</span>';
            $html .= '<span class="col-md-3 data text-align-right">'.$dischg.'</span>';
            $html .= '<span class="col-md-3 data text-align-right">'.number_format($disamt, 2).'</span>';
            $html .= '</li>';
            $html .= '<li class="list-group-item"><span class="label label-name col-md-6">Demand Charge</span>';
            $html .= '<span class="col-md-3 data text-align-right">'.$demchg.'</span>';
            $html .= '<span class="col-md-3 data text-align-right">'.number_format($demamt, 2).'</span>';
            $html .= '</li>';
            $html .= '<li class="list-group-item"><span class="label label-name col-md-6">Supply Charge</span>';
            $html .= '<span class="col-md-3 data text-align-right">'.$supchg.'</span>';
            $html .= '<span class="col-md-3 data text-align-right">'.number_format($supamt, 2).'</span>';
            $html .= '</li>';
            $html .= '<li class="list-group-item"><span class="label label-name col-md-6">Metering Charge</span>';
            $html .= '<span class="col-md-3 data text-align-right">'.$mtrchg.'</span>';
            $html .= '<span class="col-md-3 data text-align-right">'.number_format($mtramt, 2).'</span>';
            $html .= '</li>';
            $html .= '<li class="list-group-item"><span class="label label-name col-md-6">Retail Custom Charge</span>';
            $html .= '<span class="col-md-3 data text-align-right"></span>';
            $html .= '<span class="col-md-3 data text-align-right">'.number_format(5, 2).'</span>';
            $html .= '</li>';

            $html .= '<li class="list-group-item"><span class="label label-danger col-md-12  text-align-left text-bold">SUPPLIER RELATED CHARGES (PPC, PEDC)</span>';
            $html .= '</li>';
            $html .= '<li class="list-group-item"><span class="label label-name col-md-6">Generation Charge</span>';
            $html .= '<span class="col-md-3 data text-align-right">'.$genchg.'</span>';
            $html .= '<span class="col-md-3 data text-align-right">'.number_format($genamt, 2).'</span>';
            $html .= '</li>';
            $html .= '<li class="list-group-item"><span class="label label-name col-md-6">Previous Months Adjustment  <br>on Generation Cost</span>';
            $html .= '<span class="col-md-3 data text-align-right"></span>';
            $html .= '<span class="col-md-3 data text-align-right"></span>';
            $html .= '</li>';
            $html .= '<li class="list-group-item"><span class="label label-name col-md-6">Previous Years\' Adjustment on <br>Power Cost (ERC Case No. 2001-333) </span>';
            $html .= '<span class="col-md-3 data text-align-right">'.$papcchg.'</span>';
            $html .= '<span class="col-md-3 data text-align-right">'.number_format($papc, 2).'</span>';
            $html .= '</li>';
            $html .= '<li class="list-group-item"><span class="label label-name col-md-6">Transmission Charge</span>';
            $html .= '<span class="col-md-3 data text-align-right">'.$trnchg.'</span>';
            $html .= '<span class="col-md-3 data text-align-right">'.number_format($trnamt, 2).'</span>';
            $html .= '</li>';
            $html .= '<li class="list-group-item"><span class="label label-name col-md-6">System Loss Charge</span>';
            $html .= '<span class="col-md-3 data text-align-right">'.$slchg.'</span>';
            $html .= '<span class="col-md-3 data text-align-right">'.number_format($slamt, 2).'</span>';
            $html .= '</li>';

            $html .= '</ul>';
            $html .= '</div>';

            $html .= '<div class="col-md-4">';
            $html .= '<ul class="list-group summary column no-border">';
            $html .= '<li class="list-group-item"><span class="label label-danger col-md-6 text-bold text-align-left">SUBSIDIES</span>';
            $html .= '<span class="label label-danger col-md-3 text-align-right">PER KWH</span>';
            $html .= '<span class="label label-danger col-md-3 text-align-right">AMOUNT</span>';
            $html .= '</li>';
            $html .= '<li class="list-group-item"><span class="label label-name col-md-6">Interclass Cross Subsidy</span>';
            $html .= '<span class="col-md-3 data text-align-right"></span>';
            $html .= '<span class="col-md-3 data text-align-right">'.number_format($iccsub, 2).'</span>';
            $html .= '</li>';
            $html .= '<li class="list-group-item"><span class="label label-name col-md-6">Lifeline Rate Subsidy</span>';
            $html .= '<span class="col-md-3 data text-align-right">'.$llrsub.'</span>';
            $html .= '<span class="col-md-3 data text-align-right">'.number_format($llramt, 2).'</span>';
            $html .= '</li>';

            $html .= '<li class="list-group-item"><span class="label label-danger col-md-12  text-align-left text-bold">TAXES AND UNIVERSAL CHARGES</span>';
            $html .= '</li>';
            $html .= '<li class="list-group-item"><span class="label label-name col-md-6">VAT on Generation</span>';
            $html .= '<span class="col-md-3 data text-align-right"></span>';
            $html .= '<span class="col-md-3 data text-align-right">'.number_format($genvat, 2).'</span>';
            $html .= '</li>';
            $html .= '<li class="list-group-item"><span class="label label-name col-md-6">VAT on Transmission</span>';
            $html .= '<span class="col-md-3 data text-align-right"></span>';
            $html .= '<span class="col-md-3 data text-align-right">'.number_format($trnvat, 2).'</span>';
            $html .= '</li>';
            $html .= '<li class="list-group-item"><span class="label label-name col-md-6">VAT on Other Charges</span>';
            $html .= '<span class="col-md-3 data text-align-right"></span>';
            $html .= '<span class="col-md-3 data text-align-right">'.number_format($othvat, 2).'</span>';
            $html .= '</li>';
            $html .= '<li class="list-group-item"><span class="label label-name col-md-6">Franchise Tax</span>';
            $html .= '<span class="col-md-3 data text-align-right"></span>';
            $html .= '<span class="col-md-3 data text-align-right">'.number_format($framt, 2).'</span>';
            $html .= '</li>';
            $html .= '<li class="list-group-item"><span class="label label-name col-md-6">Environmental</span>';
            $html .= '<span class="col-md-3 data text-align-right">'.$envchg.'</span>';
            $html .= '<span class="col-md-3 data text-align-right">'.number_format($envamt, 2).'</span>';
            $html .= '</li>';
            $html .= '<li class="list-group-item"><span class="label label-name col-md-6">NPC Stranded Contract Cost</span>';
            $html .= '<span class="col-md-3 data text-align-right">'.$npcamt.'</span>';
            $html .= '<span class="col-md-3 data text-align-right">'.number_format($npcchg, 2).'</span>';
            $html .= '</li>';
            $html .= '<li class="list-group-item"><span class="label label-name col-md-6">ICCS Adjustment</span>';
            $html .= '<span class="col-md-3 data text-align-right">'.$iccschg.'</span>';
            $html .= '<span class="col-md-3 data text-align-right">'.number_format($iccsamt, 2).'</span>';
            $html .= '</li>';
            $html .= '<li class="list-group-item"><span class="label label-name col-md-6">FIT Allowance</span>';
            $html .= '<span class="col-md-3 data text-align-right">'.$fitchg.'</span>';
            $html .= '<span class="col-md-3 data text-align-right">'.number_format($fitamt, 2).'</span>';
            $html .= '</li>';



            $html .= '</ul>';
            $html .= '</div>';

            $html .= '<div class="col-md-4">';
            $html .= '<h4>Reading History</h4>';
            $html .= '</div>';
        }else{
            $html = '<h4 class="text-warning"><i class="fa fa-warning"></i> No Record found!</h4>';
        }
        $data['html'] = $html;
        return json_encode($data);
        exit();


        $acctid = $this->input->post('acctid');
        $readingid = $this->input->post('readingid');
        $compute = compute_billing($readingid, $acctid);

        if($compute) {
            $qry = true;
            // CREATE HTML DISPLAY
            $col_num = count($compute->data);
            $col_width = (100 / $col_num);
            $total_charges = 0;
            $html .= '<div class="accordion" id="accordion_' . $readingid . '"><div class="" style="padding: 2px 15px; position: relative; border-bottom: 1px solid rgba(0,0,0, 0.05); margin-bottom: 5px;">'
                . '<a id="btn_show_charges" class="accordion-toggle" data-toggle="collapse" data-parent="#accordion_' . $readingid . '" href="#sales_details_' . $readingid . '"><i class="fa fa-search fa-fw"></i> Show Charges</a>'
                . '<span class="pull-right print-details hidden"><a id="btn_print_charges" class=""><i class="fa fa-print"></i> Print Charges</a></span>'
                . '</div>';
            $html .= '<div id="sales_details_' . $readingid . '" class="sales-details panel-collapse collapse" style="padding: 5px 15px; position: relative; border-bottom: 1px solid rgba(0,0,0, 0.05); margin-bottom: 20px;">';
            foreach ($compute->data as $row) {
                $html .= '<div class="sales-details-list" style="vertical-align: top; min-height: 100px; display: inline-block; width: ' . $col_width . '% !important; padding: 10px 10px">';
                $html .= '<b class="text-color-blue">' . $row['groupname'] . '</b>';
                $group_id = $row['groupid'];
                ${'total_charges_' . $group_id} = 0;
                if (isset($row['lists']) && count($row['lists']) > 0) {
                    $html .= '<ul class="list-group ">';
                    foreach ($row['lists'] as $lrow) {
                        $rate = number_format($lrow['rate'], 4);
                        if ($lrow['subs'] == false) {
                            $ratename = $lrow['ratename'];
                        } else {
                            $ratename = '<a class="rate-sub" href="javascript:;">' . $lrow['ratename'] . '</a>';
                        }
                        $total_charges += $lrow['amt'];
                        ${'total_charges_' . $group_id} += round($lrow['amt'], 2);
                        $html .= '<li class="list-group-item" style="border-bottom: 1px dotted #ccc !important; margin-top: 10px; margin-bottom: 5px;"><div class="row" style=""><span class="col-md-7">' . $ratename . ':</span><span class="col-md-2 data">' . $rate . '</span> <span class="col-md-3 data pull-right text-align-right">' . number_format($lrow['amt'], 2) . '</span></span>';
                        if ($lrow['subs'] == true) {
                            $html .= '<ul class="list-group sub hidden" style="list-style-type: circle !important;">';
                            foreach ($lrow['bradedown'] as $slrow) {
                                $html .= '<li class="list-group-item" style="list-style: circle inside !important; font-size: 11px !important;"> <span class="col-md-7" style="padding-left: 12px !important;"><span style="padding-left: 15px; display: inline-block; ">' . $slrow['ratename'] . ':</span></span><span class="col-md-2 data text-align-right" >' . number_format($slrow['rate'], 4) . '</span> <span class="col-md-3 data pull-right text-align-right">' . number_format($slrow['amt'], 2) . '</span></li>';
                            }
                            $html .= '</ul>';
                        }
                        $html .= '</li>';
                    }
                    $html .= '<li class="list-group-item" style="border-bottom: 1px dotted #ccc !important; margin-top: 10px; margin-bottom: 5px;"><div class="row" style=""><span class="col-md-7"><b>Total :</b></span><span class="col-md-5 data pull-right text-align-right">' . number_format(${'total_charges_' . $group_id}, 2) . '</span></span></li>';
                    $html .= '</ul>';
                }
                $html .= '</div>';
            }
            $html .= '</div></div>';


            $data['prevtotal'] = number_format($compute->previous, 2);
            $data['prevbill'] = number_format($compute->previous, 2);
            $data['prevvat'] = number_format($compute->prevvat, 2);

            $data['total'] = number_format($compute->total, 2);
            $data['totalcharges'] = number_format($compute->totalcharges, 2);
            $data['curamt'] = number_format($compute->totalcharges, 2);
            $data['curvat'] = number_format($compute->totalvat, 2);
            $data['rate'] = $compute->ratecode;
            $data['genamt'] = number_format($compute->genamt, 2);
            $data['kwh'] = number_format($compute->kwh);
            $data['billcount'] = $compute->billcnt;

            $data['dateprint'] = date('Y-m-d');
            $data['datedelevered'] = date('Y-m-d');
            $data['datebilled'] = date('Y-m-d');
        } else {
            $qry = false;
            $name = '';
        }

    }

    function close_billing_ofthemonth() {
        $data = array();
        $msg = SYSTEM_MSG_DEFAULT;
        $func = 'warning';

        $month = $this->input->post('month');
        $year = $this->input->post('year');

        if($year && $month) {
            $where_arr = array('months' => $month, 'years' => $year, 'status' => 1);
            $qry_cnt_schedule = $this->db->select('COUNT(sysid) AS cnt')->from('reading_schedule_main')
                ->where($where_arr)
                ->get()->row();
            if ($qry_cnt_schedule && $qry_cnt_schedule->cnt > 0) {
                $this->db->where($where_arr);
                $upd = $this->db->update('reading_schedule_main', array('status' => 2, 'updatedby' => user_id()));
                if($upd) {
                    $msg = 'Success: Schedule(s) has been closed, ' . $qry_cnt_schedule->cnt;
                    $func = 'success';
                }else{
                    $msg = 'Error SQL: ' . $this->db->_error_message();
                }
            } else {
                $msg = 'No schedule found!';
                $func = 'info';
            }
        }else{
            if($month=='' && $year == '') {
                $msg = 'Please provide billing year and month on the filter!';
            }else {
                if ($month) {
                    $msg = 'Please provide billing year on the filter!';
                } else {
                    $msg = 'Please provide billing month on the filter!';
                }
            }
        }

        $data['msg'] = $msg;
        $data['func'] = $func;
        return json_encode($data);
    }

    function get_billing_trn() {
        $data = array();
        $gdlbid = $this->input->post('gdlbid');
        $year = $this->input->post('year');
        $month = $this->input->post('month');

        $qry_billing_trn = $this->db->query("
                SELECT
                rm.sysid, 
                rm.acctid, 
                rm.billno, 
                rm.servno, 
                rm.name,
                rm.addr,
                rm.mtr, 
                rm.duedate, 
                rm.overdue, 
                rm.current, 
                rm.totacc, 
                rm.totint
                FROM billing_reports AS rm
                INNER JOIN customer_accounts_main AS m ON m.sysid = rm.acctid
                WHERE rm.`year` = $year AND rm.`month` = $month AND m.gdlb = $gdlbid
            ");
        $num_rows = $qry_billing_trn->num_rows();
        if ($num_rows > 0) {
            $i = 1;
            foreach ($qry_billing_trn->result() as $row) {
                $data['data'][] = array(
                    'expand' => '<i data-toggle="collapse" data-target="#expand_' . $row->acctid . '" data-id="' . $row->sysid . '" id="btn-expand" class="fa fa-plus-square-o"></i>',
                    'seq' => $i++,
                    'servno' => $row->servno,
                    'billno' => str_pad($row->billno, 8, '0', STR_PAD_LEFT),
                    'mtr' => $row->mtr,
                    'name' => $row->name,
                    'addrspec' => $row->addr,
                    'due' => $row->duedate,
                    'prevamt' => number_format($row->overdue, 2),
                    'surcharge' => number_format($row->totint, 2),
                    'current' => number_format($row->current, 2),
                    'total' => number_format($row->totacc, 2),
                    'stat' => '<span class="label label-success">Printed</span>',
                    'ebill' => '<i class="fa fa-check text-success"></i>',
                    'control' => '
                                <button class="btn btn-info btn-xs"><i class="fa fa-search"></i></button>
                                <button class="btn btn-default btn-xs"><i class="fa fa-print"></i></button>
                            ',
                );
            }
        }
        return json_encode($data);
    }




    function send_rates_to_audit() {
        $data = array();
        $qry = false;
        $month = $this->input->post('month');
        $year = $this->input->post('year');
        $rates_group_id = 0;


        $group_where_arr = array(
            'years' => $year,
            'months' => $month,
            'status > ' => 0
        );

        $get_group_main = $this->db->select('sysid')
            ->from('trn_billing_rates_requests_group')
            ->where($group_where_arr)
            ->get()->row();


        $this->db->where($group_where_arr);
        $this->db->update('trn_billing_rates_requests_group', array('status' => 0));

        if($get_group_main) {
            $this->db->where(array('groupid' => $get_group_main->sysid, 'status' => 1));
            $this->db->update('trn_billing_rates_requests_items', array('status' => 0));
        }


        $group_ins_arr = array(
            'years' => $year,
            'months' => $month,
            'createdby' => user_id(),
            'updatedby' => user_id(),
        );
        $this->db->insert('trn_billing_rates_requests_group', $group_ins_arr);
        $data['inserr'] = $this->db->_error_message();
        $rates_group_id = $this->db->insert_id();


        if ($rates_group_id > 0) {

            $qry_rates_items = $this->db->select('sysid')
                ->from('trn_billing_rates')
                ->where(array('status' => 1, 'year' => $year, 'month' => $month))
                ->get();

            if ($qry_rates_items->num_rows() > 0) {

                $data['err'] = 'rates found!';

                $this->db->where(array('groupid' => $rates_group_id, 'status' => 1));
                $this->db->update('trn_billing_rates_requests_items', array('status' => 0));

                foreach ($qry_rates_items->result() as $row) {
                    $ins_item_arr = array(
                        'ratesid' => $row->sysid,
                        'groupid' => $rates_group_id,
                        'createdby' => user_id(),
                        'updatedby' => user_id(),
                    );
                    $this->db->insert('trn_billing_rates_requests_items', $ins_item_arr);
                    $data['erritems'][] = $this->db->_error_message();
                }

                $trans = create_transaction_trails('RATES', 'Rate Approval', 75, $rates_group_id);
                if ($trans) {
                    $qry = true;
                }
            } else {
                $data['err'] = 'Cant find the billing rates!';
            }
        }

        $data['input'] = $this->input->post();
        $data['groupid'] = $rates_group_id;
        $data['qry'] = $qry;
        return json_encode($data);
    }


    function billing_rates_approval() {
        $data  = array();
        $post_year = $this->input->post('filteryear');
        $post_month = $this->input->post('filtermonth');
        $qry = false;
        $msg = '';
        $func = 'error';

        $this->db->trans_begin();

        $status = false;


        $check_stats = $this->db->select('sysid, status')
            ->from('trn_billing_rates_requests_group')
            ->where(array('months' => $post_month, 'years' => $post_year, 'status > ' => 0))
            ->get()->row();

        if($check_stats) {
            if($check_stats->status == 300) {
                $upd_arr = array(
                    'status' => 364,
                    'updatedby' => user_id(),
                );
                $this->db->where('sysid', $check_stats->sysid);
                $this->db->update('trn_billing_rates_requests_group', $upd_arr);
            }

            if($check_stats->status == 364) {
                $upd_arr = array(
                    'status' => 301,
                    'updatedby' => user_id(),
                );
                $this->db->where('sysid', $check_stats->sysid);
                $this->db->update('trn_billing_rates_requests_group', $upd_arr);

                $qry_items = $this->db->select('ratesid')
                    ->from('trn_billing_rates_requests_items')
                    ->where(array('status' => 1))
                    ->get();
                if($qry_items->num_rows()>0) {
                    foreach($qry_items->result() as $irow) {
                        $this->db->where(array('status' => 1, 'sysid' => $irow->ratesid));
                        $this->db->update('trn_billing_rates', array('status' => 2));
                    }
                }

            }
            if($this->db->trans_status() == true) {
                $qry = true;
                $this->db->trans_commit();
            }else{
                $this->db->trans_rollback();
            }
            $status = $check_stats->status;
        }

        $data['func'] = $func;
        $data['msg'] = $msg;
        $data['qry'] = $qry;
        $data['stat'] = $status;

        return json_encode($data);
    }


    function get_billing_rate_list() {
        $post_year = $this->input->post('year');
        $post_month = $this->input->post('month');
        $post_stats = $this->input->post('stats');

        $year = ($post_year) ? $post_year : date('Y');
        $month = ($post_month) ? $post_month : date('m');
        //echo '<pre>';
        $data = array();
        $colnum = 0;
        $column = array();
        $columnf = array();
        $qry = false;
        $static_arr = array();
        // GET BILLING REQUESTS
        $check_stats = $this->db->select('sysid, status')
            ->from('trn_billing_rates_requests_group')
            ->where(array('months' => $post_month, 'years' => $post_year, 'status > ' => 0))
            ->get()->row();


        // GET COLUMN FROM RATE CLASS TABLE
        $qry_rate_class = $this->db->select()->from('prime_system_rate_class_main')->get();
        $colnum = $qry_rate_class->num_rows();
        $locked_num = 0;
        if ($colnum > 0) {
            $qry = true;
            foreach ($qry_rate_class->result() as $row) {
                $column[] = array('th' => $row->classifications, 'input' => $row->names);
            }
            $qry_rates_list = $this->db->select('rm.sysid, rm.`names`, tbr.brateid, tbr.units AS UNITID')
                ->from('trn_billing_rates AS tbr')
                ->join('billing_rates_main AS rm', 'rm.sysid = tbr.brateid')
                ->join('prime_types_parameter AS tp', 'tp.sysid = tbr.units')
                ->where(array('tbr.year' => $year, 'tbr.month' => $month))
                ->group_by('rm.sysid, rm.`names`, , tbr.units')
                ->get();

            $check_stats = $this->db->select('sysid, status')
                ->from('trn_billing_rates_requests_group')
                ->where(array('months' => $post_month, 'years' => $post_year, 'status > ' => 0))
                ->get()->row();

            $data_dynamic_col = array();
            if ($qry_rates_list->num_rows() > 0) {
                $total_rates = $qry_rates_list->num_rows();
                foreach ($qry_rates_list->result() as $mrow) {
                    $next_col = 2;
                    $dynamic_col = array();
                    $dynamic_col_dt = array();
                    $total_class = 0;

                    $control_arr = array();
                    foreach ($qry_rate_class->result() as $rrow) {
                        // GET RATE FROM TBL RATE TRN
                        $qry_bill_rate_trn = $this->db->select()->from('trn_billing_rates')->where(array('classid' => $rrow->sysid, 'brateid' => $mrow->brateid, 'units' => $mrow->UNITID, 'year' => $year, 'month' => $month, 'status !=' => 0))->get()->row();
                        $bill_rate = ($qry_bill_rate_trn) ? $qry_bill_rate_trn->rates : 0;
                        if ($qry_bill_rate_trn) {
                            if ($qry_bill_rate_trn->status == 2) {
                                $locked_num += 1;
                                $dynamic_col[] = $bill_rate;
                                $btn_icon = 'fa-lock';
                                $btn_color = 'btn-danger';
                                $btn_info_popovers = 'popovers';
                            } else {

                                if($check_stats && $check_stats->status == 364) {
                                    $btn_color = 'btn-success';
                                    $btn_icon = 'fa-check';
                                }else{
                                    $btn_color = 'btn-default';
                                    $btn_icon = 'fa-unlock';
                                }

                                $dynamic_col[] = '<div class="input-icon left"><i class="fa fa-pencil"></i><input class="form-control input-xs inline" style="width: 100%" value="' . $bill_rate . '" /></div>';
                                $btn_info_popovers = 'popovers';
                            }
                            $btn_info_popcontent = '';
                            $btn_info_popcontent .= '<div id=\'details\' style=\'width: 250px; display: inline-block; margin: 0px 0px !important; padding: 0px 0px !important;\'><ul class=\'list-group summary column no-border list-group-sm\'>';
                            $btn_info_popcontent .= '<li class=\'list-group-item\'><span class=\'label-name\'>Added By: </span><span class=\'label-default pull-right\'>' . get_user_person($qry_bill_rate_trn->createdby)->firstname . '</span></li>';
                            $btn_info_popcontent .= '<li class=\'list-group-item\'><span class=\'label-name\'>Added: </span><span class=\'label-default pull-right\'>' . $qry_bill_rate_trn->datecreated . '</span></li>';
                            $btn_info_popcontent .= '<li class=\'list-group-item\'><span class=\'label-name\'>Locked On: </span><span class=\'label-default pull-right\'>' . $qry_bill_rate_trn->datecreated . '</span></li>';
                            $btn_info_popcontent .= '</ul></div>';
                        } else {
                            $dynamic_col[] = 0;
                            $btn_color = 'btn-danger';
                            $btn_icon = 'fa-unlock';
                            $btn_info_popovers = '';
                            $btn_info_popcontent = '';
                        }
                        if(isset($post_stats) && $post_stats == true) {

                            $button = '<button type="button" class="btn btn-xs ' . $btn_color . '"><i class="fa ' . $btn_icon . ' fa-fw"></i></button>';

                            $control_arr[] = array('<a type="button" class="btn btn-xs btn-info ' . $btn_info_popovers . '" data-trigger="hover" data-container="body" data-placement="left" data-html="true" data-content="' . $btn_info_popcontent . '" data-original-title="Details"><i class="fa fa-search"></i></a>'
                                . $button);
                        }else{
                            $control_arr[] = array('<a type="button" class="btn btn-xs btn-info ' . $btn_info_popovers . '" data-trigger="hover" data-container="body" data-placement="left" data-html="true" data-content="' . $btn_info_popcontent . '" data-original-title="Details"><i class="fa fa-search"></i></a>');
                        }
                    }

                    $static_arr = array($mrow->brateid, limit_text_tooltip($mrow->names, 30, 'Descriptions'), get_types_label_format($mrow->UNITID, false, false, false, false, false, true)->text);
                    $control = array('<button type="button" class="btn btn-xs btn-default"><i class="fa ' . $btn_icon . '"></i></button>');
                    $data['data'][] = array_merge($static_arr, $dynamic_col, $control_arr);
                }
            }
        }
        // #########################################################
        // GET RATE SELECT OPTION ##################################
        $qry_rate_main_list = $this->db->select()->from('billing_rates_main')->get();
        $rateselect = array();
        if ($qry_rate_main_list->num_rows() > 0) {
            foreach ($qry_rate_main_list->result() as $row) {
                $rateselect[] = array('id' => $row->sysid, 'text' => $row->names);
            }
        }
        // #########################################################
        // GET RATE UNIT OPTION ####################################
        $qry_rate_main_unit_list = $this->db->select()->from('prime_types_parameter')->where('codes', 'UNITS')->get();
        $rateunitlist = array();
        if ($qry_rate_main_unit_list->num_rows() > 0) {
            foreach ($qry_rate_main_unit_list->result() as $row) {
                $rateunitlist[] = array('id' => $row->sysid, 'text' => $row->names);
            }
        }
        $data['lockrate'] = $locked_num;
        $data['rateselect'] = $rateselect;
        $data['rateunitlist'] = $rateunitlist;
        $data['input'] = $this->input->post();
        $data['qry'] = $qry;
        $data['column'] = $column;
        $data['colnum'] = $colnum;
        $data['ratereqstat'] = ($check_stats) ? $check_stats->status : false;

        return json_encode($data);
    }

    function add_billing_rates() {
        $userid = user_session()->system_user_sessid;
        $qry_rate_class = $this->db->select()->from('prime_system_rate_class_main')->get();
        $colnum = $qry_rate_class->num_rows();
        $this->db->trans_begin();
        if ($colnum > 0) {
            $qry = true;
            $inputs = array();
            $ins_cnt = 0;
            foreach ($qry_rate_class->result() as $row) {
                $rates = $this->input->post($row->names);
                $brateid = $this->input->post('brateid');
                $rateunit = $this->input->post('rateunit');
                if ($rates && $brateid && $rateunit) {

                    $upd_where = array(
                        'brateid' => $brateid,
                        'units' => $rateunit,
                        'classid' => $row->sysid,
                        'month' => $this->input->post('filtermonth'),
                        'year' => $this->input->post('filteryear'),
                        'status' => 1
                    );
                    $this->db->where($upd_where);
                    $this->db->update('trn_billing_rates', array('status' => 0));

                    $ins_arr = array(
                        'brateid' => $brateid,
                        'units' => $rateunit,
                        'classid' => $row->sysid,
                        'rates' => $rates,
                        'month' => $this->input->post('filtermonth'),
                        'year' => $this->input->post('filteryear'),
                        'createdby' => $userid
                    );
                    $this->db->insert('trn_billing_rates', $ins_arr);
                    $ins_cnt += 1;
                }
            }
        }
        if ($this->db->trans_status() === FALSE || $ins_cnt < $colnum) {
            $msg = 'Incomplete query!';
            $qry = false;
            $this->db->trans_rollback();
        } else {
            $msg = 'Query success!';
            $qry = true;
            $this->db->trans_commit();
        }
        $data['qry'] = $qry;
        $data['msg'] = $msg;
        $data['input'] = $this->input->post();
        $data['column'] = $colnum;
        return json_encode($data);
    }

    function get_ar_tbl_new($servno = false, $mtr = false, $searchtype = false) {
        $data = array();
        $qry = false;
        $msg = '';
        $servno_input = $this->input->post('servno');
        $mtr_input = $this->input->post('mtr');

        $servno = ($servno) ? $servno : $servno_input;
        $mtr = ($mtr) ? $mtr : $mtr_input;
        if($servno && $mtr) {


            $qry_ar = $this->db->select(
                '
                ac.servicenumber AS SERVNO,
                ac.mtr AS MTR,
                ac.sysid AS ACCTID, 
                ac.mtrno AS MTRNO,
                ac.status AS STATUS,
                ac.ownerid AS OWNID,
                ac.types AS OWNERTYPE,
                addr.addrspecific AS ADDRSTR,
                rcs.codes AS RATECODE,
                rcs.descs AS RATEDESC,
                mm.codes AS MULT
                '
            )
                ->select("CONCAT(gdlb.g, '-', ads.codes, '-', gdlb.l, '-', gdlb.b) AS GDLB", false)
                ->from('customer_accounts_main AS ac')
                ->join('customer_accounts_address AS addr', 'addr.acctid = ac.sysid AND addr.status = 1', 'left')
                ->join('gdlb_main AS gdlb', 'gdlb.sysid = ac.gdlb', 'left')
                ->join('address_districts AS ads', 'gdlb.d = ads.sysid', 'left')
                ->join('rate_class_specification AS rcs', 'rcs.sysid = ac.rateclassid', 'left')
                ->join('billing_rates_main_multiplier AS mm', 'mm.sysid = ac.multid', 'left')
                ->where(array('ac.servicenumber' => $servno, 'ac.mtr' => $mtr))
                ->group_by('ac.sysid, ac.mtr, ac.servicenumber, ac.ownerid, ac.types, addr.addrspecific ')
                //->group_by('ac.sysid')
                ->get()->row();


            if ($qry_ar) {
                $acctid = $qry_ar->ACCTID;
                $qry_billing = $this->db->select()->from('billing_reports')
                    ->where(array('acctid' => $acctid, 'kwhuse > ' => 0, 'mtr' => 1, 'duedate != ' => ''))
                    ->order_by('year', 'desc')
                    ->order_by('month', 'desc')
                    ->limit(12)->get();
                if($qry_billing->num_rows()>0) {
                    foreach($qry_billing->result() as $row) {
                        $data['billing'][] = $row;
                        $billid = $row->sysid;
                        $vatamt = ($row->genvat + $row->trnvat + $row->disvat + $row->slvat + $row->othvat);
                        $year = $row->year;
                        $m = $row->month;
                        $schedid = $row->schedid;
                        $billno = $row->billno;
                        $kwhuse = $row->kwhuse;
                        $duedate = $row->duedate;
                        $amt_current = $row->current;
                        $dt = DateTime::createFromFormat('m', $m);
                        $monthname = $dt->format('F');
                        $monthcode = strtoupper($dt->format('M'));
                        $yr = convert_year('Y', 'y', $year);


                        $int_amt = 0;

                        $num_dues = 0;

                        $todate = date("Y-m-d");

                        $int_per = 0.0224;

                        // GET HOW MANYDUES
                        $qry_dues_bill = $this->db->select('duedate')
                            ->from('billing_reports')
                            ->where(array('duedate > ' => $duedate, 'acctid' => $acctid, 'mtr' => 1))
                            ->get();
                        $num_rows_d = $qry_dues_bill->num_rows();
                        $duedate_dte = new DateTime($duedate);
                        $today = new DateTime();
                        if( $today > $duedate_dte ) {
                            $num_rows_d = $num_rows_d + 1;
                        }
                        $num_dues = $num_rows_d;
                        $int_total_per = $int_per * $num_dues;
                        $int_amt = $amt_current * $int_total_per;

                        if($searchtype == 1) {
                            $net_amt = round($amt_current, 2);
                            $amt_pd = '<input style="width: 100%" value="' . $net_amt . '" placeholder="0.00" class="form-control inline input-xs number" name="netpay[' . $m . ']" />';
                            $data['tellering'][] = array(
                                'month' => '<input type="hidden" name="billid[' . $m . ']" value="'.$billid.'"/><input type="hidden" name="moyr[' . $m . ']" value="' . $yr . '" /><span id="month" data-schedid="' . $schedid . '" data-month="' . $m . '" data-year="' . $yr . '" data-id="' . $acctid . '">' . $monthcode . '</span>',
                                'year' => str_pad($yr, 2, '0', STR_PAD_LEFT),
                                'bill' => str_pad($billno, 9, "0", STR_PAD_LEFT),
                                'kwh' => number_format($kwhuse),
                                'current' => number_format($amt_current, 2),
                                'interest' => number_format($int_amt, 2),
                                'vat' => $vatamt,
                                'duedate' => $duedate,
                                'amtpd' => $amt_pd,
                                'frtx' => '',
                                'chk' => '',
                                'inf' => '',
                                'rem' => '',
                                'ref' => '',
                                'select' => '',
                                'control' => '',
                                'curmo' => '',
                                'rowbg' => ''
                            );
                        }
                    }
                }
            }

            $data['res'] = $qry_ar;
            return json_encode($data);
        }
    }
    function get_ar_tbl($servno = false, $mtr = false, $searchtype = false) {
        $data = array();
        $qry = false;
        $msg = '';
        $servno_input = $this->input->post('servno');
        $mtr_input = $this->input->post('mtr');

        $servno = ($servno) ? $servno : $servno_input;
        $mtr = ($mtr) ? $mtr : $mtr_input;



    }

    function get_ar_tbl_bak($servno = false, $mtr = false, $searchtype = false) {
        $data = array();
        $qry = false;
        $msg = '';
        $servno_input = $this->input->post('servno');
        $mtr_input = $this->input->post('mtr');

        $servno = ($servno) ? $servno : $servno_input;
        $mtr = ($mtr) ? $mtr : $mtr_input;



        $ar_month = 7;
        $ar_year = date('Y');
        $owner_name = '';
        $acct_addr = '';
        $acct_stat = '';

        // AR INFO
        $ar_gdlb = '';
        $ar_rate = '';
        $ar_mult = '';
        $mtr_no = '';

        // AMOUNTS
        $amt_balance = 0;
        $kwh_average = 0;

        $amt_due = array();
        $amt_num = 0;
        $amt_due_total = 0;
        $amt_cur_total = 0;
        $amt_paid_total = 0;
        $amt_int_total = 0;


        $sp_payment_mode = false;
        $min_amt = 0;
        // CHECK IF ACCOUNT IS ACTIVE FIRST

        if ($servno && $mtr) {

            $qry_ar = $this->db->select(
                '
                ac.servicenumber AS SERVNO,
                ac.mtr AS MTR,
                ac.sysid AS ACCTID, 
                ac.mtrno AS MTRNO,
                ac.status AS STATUS,
                ac.ownerid AS OWNID,
                ac.types AS OWNERTYPE,
                addr.addrspecific AS ADDRSTR,
                rcs.codes AS RATECODE,
                rcs.descs AS RATEDESC,
                mm.codes AS MULT
                '
            )
                ->select("GROUP_CONCAT(gdlb.g, '-', ads.codes, '-', gdlb.l, '-', gdlb.b) AS GDLB", false)
                ->select("GROUP_CONCAT(ar.amt_01, ',', ar.amt_02, ',', ar.amt_03, ',', ar.amt_04, ',', ar.amt_05, ',', ar.amt_06, ',', ar.amt_07, ',', ar.amt_08, ',', ar.amt_09, ',', ar.amt_10, ',', ar.amt_11, ',', ar.amt_12, ',', ar.amt_13) AS AMTARR", false)
                ->select("GROUP_CONCAT(ar.kwh_01, ',', ar.kwh_02, ',', ar.kwh_03, ',', ar.kwh_04, ',', ar.kwh_05, ',', ar.kwh_06, ',', ar.kwh_07, ',', ar.kwh_08, ',', ar.kwh_09, ',', ar.kwh_10, ',', ar.kwh_11, ',', ar.kwh_12) AS KWHARR", false)
                ->select("GROUP_CONCAT(ar.billno_01, ',', ar.billno_02, ',', ar.billno_03, ',', ar.billno_04, ',', ar.billno_05, ',', ar.billno_06, ',', ar.billno_07, ',', ar.billno_08, ',', ar.billno_09, ',', ar.billno_10, ',', ar.billno_11, ',', ar.billno_12) AS BILLARR", false)
                ->from('customer_accounts_main AS ac')
                ->join('customer_accounts_ar AS ar',  'ac.sysid = ar.acctid', 'left')
                ->join('customer_accounts_address AS addr', 'addr.acctid = ac.sysid AND addr.status = 1', 'left')
                ->join('gdlb_main AS gdlb', 'gdlb.sysid = ac.gdlb', 'left')
                ->join('address_districts AS ads', 'gdlb.d = ads.sysid', 'left')
                ->join('rate_class_specification AS rcs', 'rcs.sysid = ac.rateclassid', 'left')
                ->join('billing_rates_main_multiplier AS mm', 'mm.sysid = ac.multid', 'left')
                ->where(array('ac.servicenumber' => $servno, 'ac.mtr' => $mtr))
                ->group_by('
                    ac.sysid,
                    ac.servicenumber,
                    ac.mtr,
                    ac.mtrno,
                    ac.status,
                    ac.ownerid,
                    ac.types,
                    addr.addrspecific,
                    rcs.codes,
                    rcs.descs,
                    mm.codes
                ')
                ->get()->row();
            /*
            $qry_ar = $this->db->query("
                SELECT 
                    ac.servicenumber AS SERVNO,
                    ac.mtr AS MTR,
                    ac.sysid AS ACCTID,
                    ac.mtrno AS MTRNO,
                    ac.status AS STATUS,
                    ac.ownerid AS OWNID,
                    ac.types AS OWNERTYPE,
                    addr.addrspecific AS ADDRSTR,
                    rcs.codes AS RATECODE,
                    rcs.descs AS RATEDESC,
                    mm.codes AS MULT,
                    GROUP_CONCAT(gdlb.g, '-', ads.codes, '-', gdlb.l, '-', gdlb.b) AS GDLB,
                    GROUP_CONCAT(ar.amt_01, ',', ar.amt_02, ',', ar.amt_03, ',', ar.amt_04, ',', ar.amt_05, ',', ar.amt_06, ',', ar.amt_07, ',', ar.amt_08, ',', ar.amt_09, ',', ar.amt_10, ',', ar.amt_11, ',', ar.amt_12, ',', ar.amt_13) AS AMTARR,
                    GROUP_CONCAT(ar.kwh_01, ',', ar.kwh_02, ',', ar.kwh_03, ',', ar.kwh_04, ',', ar.kwh_05, ',', ar.kwh_06, ',', ar.kwh_07, ',', ar.kwh_08, ',', ar.kwh_09, ',', ar.kwh_10, ',', ar.kwh_11, ',', ar.kwh_12) AS KWHARR,
                    GROUP_CONCAT(ar.billno_01, ',', ar.billno_02, ',', ar.billno_03, ',', ar.billno_04, ',', ar.billno_05, ',', ar.billno_06, ',', ar.billno_07, ',', ar.billno_08, ',', ar.billno_09, ',', ar.billno_10, ',', ar.billno_11, ',', ar.billno_12) AS BILLARR
                FROM customer_accounts_main AS ac
                LEFT JOIN customer_accounts_ar AS ar ON ac.sysid = ar.acctid
                LEFT JOIN customer_accounts_address AS addr ON addr.acctid = ac.sysid AND addr.status = 1
                LEFT JOIN gdlb_main AS gdlb ON gdlb.sysid = ac.gdlb
                LEFT JOIN address_districts AS ads ON gdlb.d = ads.sysid
                LEFT JOIN rate_class_specification AS rcs ON rcs.sysid = ac.rateclassid
                LEFT JOIN billing_rates_main_multiplier AS mm ON mm.sysid = ac.multid
                WHERE ac.servicenumber = 'M13049' AND ac.mtr = 1
                GROUP BY
                    ac.sysid,
                    ac.servicenumber,
                    ac.mtr,
                    ac.mtrno,
                    ac.status,
                    ac.ownerid,
                    ac.types,
                    addr.addrspecific,
                    rcs.codes,
                    rcs.descs,
                    mm.codes
            ")->row();
            */

            $data['error'][] = $this->db->_error_message();

            $amt_arr = ($qry_ar) ? explode(',', $qry_ar->AMTARR) : false;
            $kwh_arr = ($qry_ar) ? explode(',', $qry_ar->KWHARR) : false;
            $bill_arr = ($qry_ar) ? explode(',', $qry_ar->BILLARR) : false;

            if ($qry_ar) {

                $acctid = $qry_ar->ACCTID;
                $status = $qry_ar->STATUS;
                // SUM BILLING CURRENT

                // GET INT ARRAY
                /*
                $int_amt_arr = array();
                $int_dte_arr = array();
                $qry_int = $this->db->select("
                    CONCAT(
                        int_01, ',',
                        int_02, ',',
                        int_03, ',',
                        int_04, ',',
                        int_05, ',',
                        int_06, ',',
                        int_07, ',',
                        int_08, ',',
                        int_09, ',',
                        int_10, ',',
                        int_11, ',',
                        int_12, ',',
                        int_13
                    ) AS INTAMTS
                    ", false)
                    ->select("
                    CONCAT(
                        dte_01, ',',
                        dte_02, ',',
                        dte_03, ',',
                        dte_04, ',',
                        dte_05, ',',
                        dte_06, ',',
                        dte_07, ',',
                        dte_08, ',',
                        dte_09, ',',
                        dte_10, ',',
                        dte_11, ',',
                        dte_12
                    ) AS INTDTE
                    ", false)
                    ->from('customer_accounts_ar_interest')
                    ->where(array('acctid' => $acctid))->get()->row();
                if ($qry_int) {
                    $int_amt_arr = explode(',', $qry_int->INTAMTS);
                    $int_dte_arr = explode(',', $qry_int->INTDTE);
                }
                */


                $kwh_total = 0;
                $due_arr = array();
                $vat_arr = array();
                $year_arr = array();
                $schedid_arr = array();
                /*
                $qry_billtrn = $this->db->select('kwhuse, duedate, schedid, byr, genvat, trnvat, disvat, slvat, othvat')
                    ->from('billing_reports')
                    ->where(array('acctid' => $acctid, 'year' => $ar_year))
                    //->group_by('kwhuse, duedate, schedid')
                    ->get();
                */
                $bnum_rows = 0;
                $qry_billtrn = get_ar_billing($acctid, $ar_year);
                if($qry_billtrn) {
                    $bnum_rows = $qry_billtrn->num_rows();
                }


                // SPACIAL TELLERING MODULE
                // #################################
                $qry_pay_approval = $this->db->select()->from('trn_customer_payments_approval')
                    ->where(array('acctid' => $acctid, 'status' => 1))->get()->row();
                if ($qry_pay_approval) {
                    $sp_payment_mode = true;
                    $sp_payment_min = $qry_pay_approval->amount;
                }


                if ($bnum_rows > 0) {
                    $i = 0;
                    foreach ($qry_billtrn->result() as $brow) {
                        $schedid_arr[] = $brow->schedid;
                        $due_arr[] = $brow->duedate;
                        $vat_arr[] = $brow->totalvat;
                        $year_arr[] += $brow->byr;
                        $kwh_total += $brow->kwhuse;
                    }
                }
                asort($due_arr);
                $kwh_average = ($kwh_total > 0) ? $kwh_total / $bnum_rows : 0;


                // $data['ownerid'] = $qry_ar->OWNID;
                // $data['acctid'] = $qry_ar->ACCTID;

                // GET OWNER INFO
                if ($qry_ar->OWNERTYPE == 1) {
                    $person = get_person_info($qry_ar->OWNID)->info;
                    $owner_name = $person->lastname . ', ' . $person->firstname . ' ' . $person->middlename;
                    $dir_pic = 'person';
                }
                if ($qry_ar->OWNERTYPE == 2) {
                    $corp = get_corporation_info($qry_ar->OWNID);
                    $owner_name = $corp->codes . ' - ' . $corp->descs;
                    $dir_pic = 'corporation';
                }
                if ($qry_ar->OWNERTYPE == 5) {
                    $qry_person = $this->db->select()
                        ->from('customer_accounts_name_legacy')
                        ->where(array('sysid' => $qry_ar->OWNID))
                        ->get()->row();
                    $owner_name = $qry_person->name;
                    $dir_pic = 'legacy';
                }
                $acct_addr = $qry_ar->ADDRSTR;
                $ar_gdlb = $qry_ar->GDLB;
                $ar_rate = '<span class="text-danger">' . $qry_ar->RATECODE . '</span>' . ' / ' . $qry_ar->RATEDESC;
                $ar_mult = $qry_ar->MULT;
                $mtr_no = $qry_ar->MTRNO;


                $acct_stat = ($qry_ar->STATUS == 1) ? '<span class="text-success">Active</span>' : '<span class="text-danger">FDO</span>';

                $bills_amt_arr = array();
                foreach ($amt_arr as $keys => $amt_num_row) {
                    $bills_amt_arr[] = array('amt' => $amt_num_row, 'month' => $keys + 1);
                }

                foreach ($amt_arr as $keys => $amt_num_row) {
                    if ($amt_num_row > 0) {
                        $amt_num += 1;
                        $amt_due[] = array('amt' => $amt_num_row, 'month' => $keys + 1);
                    }
                }
                $current_month = false;
                $ii = 0;
                foreach ($amt_due as $amt_due_row) {
                    if ($ii == 0) {
                        $current_month = $amt_due_row['month'];
                    } else if ($ii == $amt_num - 1) {
                        $current_month = $amt_due_row['month'];
                    }
                    $ii++;
                }

                $data['currentmonth'] = $current_month;

                for ($m = 1; $m <= 12; $m++) {


                    $row_bg = '';
                    $btn_control = '';

                    $index = $m - 1;

                    if($status == 1) {
                        $dt = DateTime::createFromFormat('m', $m);
                        $monthname = $dt->format('F');
                        $monthcode = $dt->format('M');
                        $amt_current = (isset($amt_arr[$index]) && $amt_arr[$index] > 0) ? $amt_arr[$index] : 0;


                        $is_current_month = false;
                        if ($current_month == $m) {
                            // @TODO ADD CONDITION IF THE CURRENT STATED IS NOT OVER DUE
                            $is_current_month = true;
                            $amt_cur_total += $amt_current;
                        } else {
                            // @TODO ADD INTEREST HERE FOR THE DUE
                            $amt_due_total += $amt_current;
                        }

                        $duedate = (isset($due_arr[$index])) ? $due_arr[$index] : '0000-00-00';
                        $yr = (isset($year_arr[$index])) ? $year_arr[$index] : '00';
                        $vatamt = (isset($vat_arr[$index]) && $amt_current > 0) ? $vat_arr[$index] : '0.00';
                        //$vatamt     =   0;
                        $billno = (isset($bill_arr[$index])) ? trim($bill_arr[$index]) : 0;
                        $schedid = (isset($schedid_arr[$index])) ? trim($schedid_arr[$index]) : 0;
                        $intamt = 0;
                        //$intamt     =   '0.00';
                        //$intamt = (isset($int_amt_arr[$index])) ? $int_amt_arr[$index] : 0;
                        //$intdte = (isset($int_dte_arr[$index])) ? $int_dte_arr[$index] : '1900-01-01';

                        $due_month_pass = count_months($duedate, date('Y-m-d'));
                        $due_month_pass_num = ($due_month_pass) ? $due_month_pass : 0;
                        $due = false;
                        if ($is_current_month) {
                            if (validateDate($duedate)) {
                                $date = new DateTime($duedate);
                                $now = new DateTime();
                                if ($date < $now) {
                                    $due = true;
                                }
                            }
                        }


                        $bill_exist = false;
                        if ($yr > 0) {
                            $bill_exist = true;
                        }

                        // @TODO CREATE MODULE FOR PAYMENTS
                        // PAY APPLIED CHECKING
                        $paid = false;
                        $amtpd = 0;
                        $dtepd = '0000-00-00';
                        if (isset($year_arr[$index]) && $year_arr[$index] > 0) {
                            $year = convert_year('y', 'Y', $year_arr[$index]);
                            $qry_payapplied = $this->db->select('amtpd, interest, frtax, CAST(datecreated AS DATE) AS datepd')
                                ->from('billing_payapplied')
                                ->where(array('acctid' => $acctid, 'billmo' => $m, 'billyr' => $year, 'status' => 1))
                                ->get()->row();
                            if ($qry_payapplied) {
                                $paid = true;
                                $amtpd = ($qry_payapplied->amtpd + $qry_payapplied->interest + $qry_payapplied->frtax);
                                $amt_paid_total += $amtpd;
                                $dtepd = $qry_payapplied->datepd;
                            }
                        }
                        $netamt = 0;

                        $check_box_check = '';
                        $check_box_select = '';
                        $check_box = '';
                        $rem = '';
                        $ref = '';
                        $input_frtx = '';
                        if ($bill_exist == false) {
                            $rem = '<span class="text-danger" data-toggle="tooltips" data-container="body" data-placement="left" title="Error Bill: Cannot find the billing info!">EB</span>';
                        } else {
                            // @TODO CREATE REFERALS QUERY
                        }

                        if (
                            ($is_current_month == false
                                && $amt_current > 0
                                && $bill_exist == true
                            )
                            || $intamt > 0
                        ) {
                            $check_box = 'checked';
                            $min_amt += $amt_current;
                        }


                        $todate = date("Y-m-d");
                        $int_per = 0.0224;
                        $data['duearr'][] = $due_arr;
                        if (validateDate($duedate, 'Y-m-d')) {
                            // GET HOW MANYDUES
                            $qry_dues_bill = $this->db->select('duedate')
                                ->from('billing_reports_main')
                                ->where(array('duedate > ' => $duedate, 'acctid' => $acctid, 'mtr' => 1))
                                ->get();
                            $num_rows_d = $qry_dues_bill->num_rows();
                            $duedate_dte = new DateTime($duedate);
                            $today = new DateTime($todate);
                            if ($today > $duedate_dte) {
                                $num_rows_d = $num_rows_d + 1;
                            }
                            $num_dues = $num_rows_d;
                            $int_total_per = $int_per * $num_dues;
                            $intamt = $num_rows_d;
                            $intamt = round(($amt_current * $int_total_per), 2);
                        } else {
                            $bill_exist = false;
                        }


                        if ($searchtype == 1) {


                            $year = convert_year('y', 'Y', $yr);
                            $acct_billing_ar = check_ar_billing($acctid, $mtr, $year, $m);
                            if($acct_billing_ar==false) {
                                $bill_exist = false;
                            }



                            $due_tag = '';
                            if ($due == true) {
                                $due_tag = '<span class="text-danger">due</span>';
                            }


                            if ($paid == true) {
                                $amt_pd = $amtpd; // @TODO change to amt paid from pay applied
                                $amt_int = '0.00';
                                $amt_vat = '0.00';
                            } else {
                                $netamt = $amt_current + $intamt;
                                // IF PAYMENT MODE IS SPECIAL PAYMENT IS APPROVED
                                if ($sp_payment_mode == true && $bill_exist == true) {
                                    $amt_pd = '<input style="width: 100%" value="' . $netamt . '" placeholder="0.00" class="form-control inline input-xs number" name="netpay[' . $m . ']" />';
                                    $amt_int = '<input style="width: 100%" value="' . $intamt . '" placeholder="0.00" class="form-control inline input-xs number" name="intamt[' . $m . ']" />';
                                    $amt_vat = '<span class="txt">' . number_format($vatamt, 2) . '</span><input type="hidden" readonly style="width: 100%" value="' . $vatamt . '" placeholder="0.00" class="form-control inline input-xs number" name="vatamt[' . $m . ']" />';
                                    $btn_control = '<a class="btn_new"><i class="fa fa-file-o"></i></a>';
                                    $check_box_select = '<input ' . $check_box . ' type="checkbox" class="icheck select" style="margin: 0px 0px !important; padding: 0px 0px !important; height: 20px !important;" value="' . $yr . '" name="select[' . $m . ']" />';
                                    $input_frtx = '<input style="width: 100%" name="frtx[' . $m . ']" class="form-control inline input-xs number" placeholder="0.00" />';
                                } else {
                                    $amt_vat = '<span class="txt">' . number_format($vatamt, 2) . '</span><input type="hidden" readonly style="width: 100%" value="' . $vatamt . '" placeholder="0.00" class="form-control inline input-xs number" name="vatamt[' . $m . ']" />';
                                    if ($is_current_month == true && $bill_exist == true) {
                                        $check_box_select = '<input ' . $check_box . ' type="checkbox" class="icheck select" style="margin: 0px 0px !important; padding: 0px 0px !important; height: 20px !important;" value="' . $yr . '" name="select[' . $m . ']" />';
                                        $amt_pd = '<div class="input-icon left">' .
                                            '<i class="fa fa-pencil tooltips" data-original-title=""></i>' .
                                            '<input type="" style="width: 100%" value="' . $netamt . '" placeholder="0.00" class="form-control inline input-xs number" name="netpay[' . $m . ']" />' .
                                            '</div>';
                                        $amt_int = '<div class="input-icon left">' .
                                            '<i class="fa fa-pencil tooltips" data-original-title=""></i>' .
                                            '<input type="" style="width: 100%" value="' . $intamt . '" placeholder="0.00" class="form-control inline input-xs number" name="intamt[' . $m . ']" />' .
                                            '</div>';
                                        $btn_control = '<a class="btn_new"><i class="fa fa-file-o"></i></a>';
                                        $input_frtx = '<input style="width: 100%" name="frtx[' . $m . ']" class="form-control inline input-xs number" placeholder="0.00" />';
                                    } else {
                                        if ($bill_exist == true) {
                                            $check_box_select = $due_tag . '<input ' . $check_box . ' type="checkbox" class="hidden select" style="margin: 0px 0px !important; padding: 0px 0px !important; height: 20px !important;" value="' . $yr . '" name="select[' . $m . ']" />';
                                            $amt_pd = '<input type="hidden" style="width: 100%" value="' . $netamt . '" placeholder="0.00" class="form-control inline input-xs number" name="netpay[' . $m . ']" />' . number_format($netamt, 2);
                                            $amt_int = '<input type="hidden" style="width: 100%" value="' . $intamt . '" placeholder="0.00" class="form-control inline input-xs number" name="intamt[' . $m . ']" />' . number_format($intamt, 2);
                                            $btn_control = '<a class=""><i class="fa fa-lock text-warning"></i></a>';
                                            $input_frtx = '<input style="width: 100%" name="frtx[' . $m . ']" class="form-control inline input-xs number" placeholder="0.00" />';
                                        } else {
                                            $row_bg = 'danger';
                                            $check_box_select = '<input type="checkbox" class="hidden select" style="margin: 0px 0px !important; padding: 0px 0px !important; height: 20px !important;" value="' . $yr . '" name="select[' . $m . ']" />';
                                            $amt_pd = '<input type="hidden" style="width: 100%" value="0" placeholder="0.00" class="form-control inline input-xs number" name="netpay[' . $m . ']" />' . number_format($netamt, 2);
                                            $amt_int = '<input type="hidden" style="width: 100%" value="0" placeholder="0.00" class="form-control inline input-xs number" name="intamt[' . $m . ']" />' . $intamt;
                                            $btn_control = '<a class="btn_del"><i class="fa fa-times text-danger"></i></a>';
                                        }
                                    }
                                }
                            }
                        } else {
                            if ($paid == true) {
                                $amt_pd = $amt_current; // @TODO change to amt paid from pay applied
                                $amt_int = '0.00';
                                $amt_vat = '0.00';
                            } else {
                                $amt_pd = '0.00';
                                $amt_int = '0.00';
                                $amt_vat = '0.00';
                            }
                        }

                        $info_popover = '<a class="" href="javascript:;" title="" data-toggle="popover" 
                                        data-content="
                                            <ul class=\'list-group summary column no-border\'>
                                            <li class=\'list-group-item\'>PN:  <span class=\'label-default pull-right\'>None</span></li> 
                                            <li class=\'list-group-item\'>R.A.: <span class=\'label-default pull-right\'>None</span></li> 
                                            <li class=\'list-group-item\'>BP22: <span class=\'label-default pull-right\'>None</span></li>
                                            <li class=\'list-group-item\'>REF:  <span class=\'label-default pull-right\'>None</span></li> 
                                            <li class=\'list-group-item\'>Duedate: <span class=\'label-default pull-right\'>' . $duedate . '</span></li>
                                            </ul>
                                        " data-placement="left" data-trigger="hover" data-original-title="Billing Info"><i class="fa fa-search fa-fw"></i></a>';

                        if ($searchtype == 1) {
                            $check_box_check = '<input type="checkbox" class="icheck check" style="margin: 0px 0px !important; padding: 0px 0px !important; height: 20px !important;" value="' . $m . '" name="check[' . $m . ']" />';

                            if ($amt_current > 0 && $paid == false) {
                                $kwh = (isset($kwh_arr[$m])) ? $kwh_arr[$m] : 0;
                                $data['tellering'][] = array(
                                    'month' => '<input type="hidden" name="moyr[' . $m . ']" value="' . $yr . '" /><span id="month" data-schedid="' . $schedid . '" data-month="' . $m . '" data-year="' . $ar_year . '" data-id="' . $acctid . '">' . $monthcode . '</span>',
                                    //'month' => $yr,
                                    'year' => str_pad($yr, 2, '0', STR_PAD_LEFT),
                                    'bill' => str_pad($billno, 9, "0", STR_PAD_LEFT),
                                    'kwh' => number_format($kwh),
                                    'current' => number_format($amt_current, 2),
                                    'interest' => $amt_int,
                                    'vat' => $amt_vat,
                                    'duedate' => $duedate,
                                    'amtpd' => $amt_pd,
                                    'frtx' => $input_frtx,
                                    'chk' => $check_box_check,
                                    'inf' => $info_popover,
                                    'rem' => $rem,
                                    'ref' => $ref,
                                    'select' => $check_box_select,
                                    'control' => $btn_control,
                                    'curmo' => $is_current_month,
                                    'datepaid' => '0000-00-00',
                                    'datepaidsur' => '0000-00-00',
                                    'rowbg' => $row_bg
                                );
                            }

                        } else {
                            $kwh = (isset($kwh_arr[$index])) ? (int)$kwh_arr[$index] : 0;
                            $data['inquiry'][] = array(
                                'month' => '<span id="month" data-schedid="' . $schedid . '" data-month="' . $m . '" data-year="' . $ar_year . '" data-id="' . $acctid . '">' . $monthname . '</span>',
                                //'month' => $monthname,
                                'bill' => str_pad($billno, 9, "0", STR_PAD_LEFT),
                                'kwh' => number_format($kwh),
                                'current' => number_format($amt_current, 2),
                                'interest' => $intamt,
                                'duedate' => $duedate,
                                'amtpd' => number_format($amtpd, 2),
                                'datepaid' => $dtepd,
                                'datepaidsur' => $dtepd,
                                'rem' => '',
                                'curmo' => $is_current_month,
                                'paid' => $paid,
                            );
                        }

                        $amt_balance += ($amt_current + $intamt);
                        $amt_int_total += $intamt;

                        $monthshort = $dt->format('M');
                        $kwh = (isset($kwh_arr[$index]) && $kwh_arr[$index] > 0) ? $kwh_arr[$index] : 0;
                        $data['kwharr'][] = array('month' => $monthshort, 'value' => $kwh);
                        $data['yeararr'][] = $year_arr;
                    }else{

                    }
                }

                if($status == 0) {
                    if ($searchtype == 1) {
                        $amt_current = array_sum($amt_arr);
                        $monthcode = '<span class="text-danger">BAL</span>';
                        $kwh = 0;
                        $billno = 0;
                        $schedid = 0;
                        $netamt = $amt_current;
                        $intamt = 0;
                        $vatamt = 0;
                        $duedate = 0;
                        $m = 13;
                        $yr = 0;
                        $check_box = 'checked';
                        $info_popover = '';
                        $rem = '';
                        $ref = '';
                        $amt_pd = '<input style="width: 100%" value="' . $netamt . '" placeholder="0.00" class="form-control inline input-xs number" name="netpay[' . $m . ']" />';
                        $amt_int = '<input style="width: 100%" value="' . $intamt . '" placeholder="0.00" class="form-control inline input-xs number" name="intamt[' . $m . ']" />';
                        $amt_vat = '<span class="txt">' . number_format($vatamt, 2) . '</span><input type="hidden" readonly style="width: 100%" value="' . $vatamt . '" placeholder="0.00" class="form-control inline input-xs number" name="vatamt[' . $m . ']" />';
                        $btn_control = '<a class="btn_new"><i class="fa fa-file-o"></i></a>';
                        $check_box_select = '<input ' . $check_box . ' type="checkbox" class="hidden select" style="margin: 0px 0px !important; padding: 0px 0px !important; height: 20px !important;" value="' . $yr . '" name="select[' . $m . ']" />';
                        $input_frtx = '<input style="width: 100%" name="frtx[' . $m . ']" class="form-control inline input-xs number" placeholder="0.00" />';
                        $check_box_check = '<input type="checkbox" class="icheck check" style="margin: 0px 0px !important; padding: 0px 0px !important; height: 20px !important;" value="' . $m . '" name="check[' . $m . ']" />';

                        $data['tellering'][] = array(
                            'month' => '<input type="hidden" name="moyr[' . $m . ']" value="' . $yr . '" /><span id="month" data-schedid="' . $schedid . '" data-month="' . $m . '" data-year="' . $ar_year . '" data-id="' . $acctid . '">' . $monthcode . '</span>',
                            //'month' => $yr,
                            'year' => str_pad($yr, 2, '0', STR_PAD_LEFT),
                            'bill' => str_pad($billno, 9, "0", STR_PAD_LEFT),
                            'kwh' => number_format($kwh),
                            'current' => number_format($amt_current, 2),
                            'interest' => $amt_int,
                            'vat' => $amt_vat,
                            'duedate' => $duedate,
                            'amtpd' => $amt_pd,
                            'frtx' => $input_frtx,
                            'chk' => $check_box_check,
                            'inf' => $info_popover,
                            'rem' => $rem,
                            'ref' => $ref,
                            'select' => $check_box_select,
                            'control' => '<span class="label label-danger"><i class="fa fa-check"></i></span>',
                            'curmo' => '',
                            'datepaid' => '0000-00-00',
                            'datepaidsur' => '0000-00-00',
                            'rowbg' => ''
                        );

                        $data['tellering'][] = array(
                            'month' => '<input type="hidden" name="moyr[' . $m . ']" value="' . $yr . '" /><span id="month" data-schedid="' . $schedid . '" data-month="' . $m . '" data-year="' . $ar_year . '" data-id="' . $acctid . '">RECON</span>',
                            //'month' => $yr,
                            'year' => '00',
                            'bill' => str_pad($billno, 9, "0", STR_PAD_LEFT),
                            'kwh' => number_format(0),
                            'current' => number_format(20, 2),
                            'interest' => '<input style="width: 100%" value="" placeholder="0.00" class="form-control inline input-xs number" name="intamt[0]" />',
                            'vat' => '<span class="txt">' . number_format(0, 2) . '</span><input type="hidden" readonly style="width: 100%" value="" placeholder="0.00" class="form-control inline input-xs number" name="vatamt[0]" />',
                            'amtpd' => '<input style="width: 100%" value="20.00" placeholder="0.00" class="form-control inline input-xs number" name="netpay[0]" />',
                            'frtx' => $input_frtx,
                            'chk' => $check_box_check,
                            'inf' => $info_popover,
                            'rem' => $rem,
                            'ref' => $ref,
                            'select' => $check_box_select,
                            'control' => '<span class="label label-danger"><i class="fa fa-check"></i></span>',
                            'curmo' => true,
                            'datepaid' => '0000-00-00',
                            'datepaidsur' => '0000-00-00',
                            'rowbg' => ''
                        );
                    }
                }

                // OVER 12
                if ($searchtype != 1) {
                    $intamt = 0;
                    $amt12 = (isset($amt_arr[12]) && $amt_arr[12] > 0) ? $amt_arr[12] : 0;
                    $data['inquiry'][] = array(
                        'month' => 'Prev. Yr.',
                        'bill' => '',
                        'kwh' => '',
                        'current' => number_format($amt12, 2),
                        'interest' => $intamt,
                        'duedate' => '',
                        'amtpd' => '',
                        'datepaid' => '',
                        'datepaidsur' => '',
                        'rem' => '',
                        'control' => '
                                <div class="btn btn-group" style="margin: 0px 0px; padding: 0px 0px;">
                                    <a href="#manage" id="btn-view" data-id="' . $servno . '" data-month="13" data-year="' . $ar_year . '" class="btn btn-info btn-xs"><i class="fa fa-arrow-right"></i></a> 
                                </div>
                            ',
                        'curmo' => FALSE
                    );
                }

                $qry = true;
            } else {
                $msg = 'Search: not found!';
            }
        } else {
            $qry = false;
            $msg = 'Complete search string!';
        }

        $footnote = '';

        // DATA DUMP

        $data['amtdue'] = $amt_due;
        // CHANGE MIN AMOUNT IF THE PAYMENT MODE IS SPECIAL APPROVAL
        if($sp_payment_mode==true) {
            $min_amt = $sp_payment_min;
            $footnote .= '<span class="label label-danger">Special Payment</span>';
        }else {
            $footnote .= '<span class="label label-info">Regular Payment</span>';
            $min_amt = $min_amt;
        }

        $data['footnote'] = $footnote;
        // AR MONITORING
        $data['nobill'] = number_format($amt_num);
        $data['amtdue'] = number_format($amt_due_total, 2);
        $data['amtcur'] = number_format($amt_cur_total,2);
        $data['minamt'] = number_format($min_amt, 2);
        $armon = '';

        // @TODO GET AR MON FOR EACH CLASS
        if($amt_num>=2 && $amt_due_total>=3000) {
            $armon = '<span class="label label-warning"><i class="fa fa-search"></i> W/ AR Mon</span>';
        }
        $data['armon'] = $armon;

        // AR DATA
        $data['paymode'] = $sp_payment_mode;
        $data['gdlb'] = $ar_gdlb;
        $data['rate'] = $ar_rate;
        $data['mult'] = $ar_mult;
        $data['mtrno'] = $mtr_no;

        $new_amt_balance = $amt_balance - $amt_paid_total;

        // AMOUNT
        $data['amtbal'] = number_format($new_amt_balance,2);
        $data['amtint'] = number_format($amt_int_total,2);
        $data['amtpd'] = number_format($amt_paid_total,2);
        $data['avkwh'] = number_format($kwh_average);

        $data['status'] = $acct_stat;
        $data['arname'] = $owner_name;
        $data['araddr'] = $acct_addr;
        $data['servno'] = $servno;
        $data['qry'] = $qry;
        $data['msg'] = $msg;

        return json_encode($data);
    }

    function get_billing_hist() {
        $data = array();

        $acctid = $this->input->post('acctid');
        $servno = $this->input->post('servno');
        $mtr = $this->input->post('mtr');
        if($servno && $mtr) {
            $qry_acct = $this->db->select('sysid')->from('customer_accounts_main')
                ->where(array('servicenumber' => $servno, 'mtr' => $mtr))
                ->get()->row();
        }else{
            if($acctid) {
                $qry_acct = true;
            }
        }

        if($qry_acct) {
            if($acctid == false) {
                $acctid = $qry_acct->sysid;
            }
            $qry_bill = $this->db->select()->from('billing_reports_main')
                ->where(array('acctid' => $acctid))
                ->order_by('year', 'desc')
                ->order_by('month', 'desc')
                ->get();
            if ($qry_bill->num_rows() > 0) {
                foreach ($qry_bill->result() as $row) {

                    $data['list'][] = array(
                        'month' => strtoupper(convert_date_number('m', 'M', $row->month)),
                        'year' => $row->year,
                        'kwhuse' => round($row->kwhuse, 0),
                        'current' => number_format($row->current, 2),
                        'prvrdg' => round($row->prvrdg, 0),
                        'prsrdg' => round($row->prsrdg, 0),
                        'prvdte' => $row->prvdte,
                        'prsdte' => $row->prsdte,
                        'nodays' => 0,
                        'mtrser' => $row->mtrser,
                        'serial' => $row->serial,
                        'moyr' => str_pad($row->bmo, 2, '0', STR_PAD_LEFT) . '-' . $row->byr,
                        'batch' => $row->batch,
                        'code' => get_billing_refcode($row->sysid),
                        'select' => '<input class="icheck" type="checkbox" name="bill[' . $row->sysid . ']" value="' . $row->sysid . '"/>'
                    );
                }
            }
        }
        return json_encode($data);
    }

    function get_payment_applied() {
        $data = array();
        $servno = $this->input->post('servno');
        $mtr = $this->input->post('mtr');
        $qry_acct = $this->db->select('sysid')->from('customer_accounts_main')
            ->where(array('servicenumber'=>$servno,'mtr'=>$mtr))
            ->get()->row();

        if($qry_acct) {
            $qry_payapplied = $this->db->select()->from('billing_payapplied')
                ->where(array('acctid' => $qry_acct->sysid, 'status' => 1))
                ->order_by("billyr, billmo", 'desc')
                ->get();
            if($qry_payapplied->num_rows()>0) {
                $i = 1;
                foreach($qry_payapplied->result() as $row) {
                    $data['list'][] = array(
                        'num' => $i++,
                        'orno' => $row->orno,
                        'year' => $row->billyr,
                        'month' => convert_date_number('m', 'M', $row->billmo),
                        'amtpd' => number_format($row->amtpd, 2),
                        'interest' => number_format($row->interest, 2),
                        'datecreated' => $row->datecreated,
                        'createdby' => $row->createdby,
                    );
                }
            }

        }
        return json_encode($data);
    }

    function get_ar_other_info() {
        $data = array();
        $qry = false;
        $msg = '';
        $servno = $this->input->post('servno');
        $mtr = $this->input->post('mtr');
        $year =$this->input->post('year');
        $month =$this->input->post('month');

        $ar_month = ($month) ? $month : date('m');
        $ar_year = ($year) ? $year : date('Y');
        $ar_year_prev = $ar_year - 1;
        $cur_arr = array();

        if($servno && $mtr) {

            $qry_ar = $this->db->select(
                '
                ac.servicenumber AS SERVNO,
                ac.mtr AS MTR,
                ac.sysid AS ACCTID, 
                ac.mtrno AS MTRNO,
                ac.status AS STATUS,
                ac.ownerid AS OWNID,
                ac.types AS OWNERTYPE,
                addr.addrspecific AS ADDRSTR,
                rcs.codes AS RATECODE,
                rcs.descs AS RATEDESC,
                mm.codes AS MULT
                '
            )
                ->select("GROUP_CONCAT(gdlb.g, '-', ads.codes, '-', gdlb.l, '-', gdlb.b) AS GDLB", false)
                ->select("GROUP_CONCAT(ar.amt_01, ',', ar.amt_02, ',', ar.amt_03, ',', ar.amt_04, ',', ar.amt_05, ',', ar.amt_06, ',', ar.amt_07, ',', ar.amt_08, ',', ar.amt_09, ',', ar.amt_10, ',', ar.amt_11, ',', ar.amt_12, ',', ar.amt_13) AS AMTARR", false)
                ->select("GROUP_CONCAT(ar.kwh_01, ',', ar.kwh_02, ',', ar.kwh_03, ',', ar.kwh_04, ',', ar.kwh_05, ',', ar.kwh_06, ',', ar.kwh_07, ',', ar.kwh_08, ',', ar.kwh_09, ',', ar.kwh_10, ',', ar.kwh_11, ',', ar.kwh_12) AS KWHARR", false)
                ->select("GROUP_CONCAT(ar.billno_01, ',', ar.billno_02, ',', ar.billno_03, ',', ar.billno_04, ',', ar.billno_05, ',', ar.billno_06, ',', ar.billno_07, ',', ar.billno_08, ',', ar.billno_09, ',', ar.billno_10, ',', ar.billno_11, ',', ar.billno_12) AS BILLARR", false)
                ->from('customer_accounts_main AS ac')
                ->join('customer_accounts_ar AS ar',  'ac.sysid = ar.acctid', 'left')
                ->join('customer_accounts_address AS addr', 'addr.acctid = ac.sysid AND addr.status = 1', 'left')
                ->join('gdlb_main AS gdlb', 'gdlb.sysid = ac.gdlb', 'left')
                ->join('address_districts AS ads', 'gdlb.d = ads.sysid', 'left')
                ->join('rate_class_specification AS rcs', 'rcs.sysid = ac.rateclassid', 'left')
                ->join('billing_rates_main_multiplier AS mm', 'mm.sysid = ac.multid', 'left')
                ->where(array('ac.servicenumber' => $servno, 'ac.mtr' => $mtr))
                ->group_by('
                    ac.sysid,
                    ac.servicenumber,
                    ac.mtr,
                    ac.mtrno,
                    ac.status,
                    ac.ownerid,
                    ac.types,
                    addr.addrspecific,
                    rcs.codes,
                    rcs.descs,
                    mm.codes
                ')->get()->row();

            $data['err'] = $this->db->_error_message();

            $kwh_arr = ($qry_ar) ? explode(',', $qry_ar->KWHARR) : false;
            $amt_arr = ($qry_ar) ? explode(',', $qry_ar->AMTARR) : false;
            $bill_arr = ($qry_ar) ? explode(',', $qry_ar->BILLARR) : false;

            if ($qry_ar) {
                $acctid = $qry_ar->ACCTID;
                $servno = $qry_ar->SERVNO;

                // PREVIOUS BILL TRN
                $qry_billtrn_prev = $this->db->select('kwhuse, current')
                    ->from('billing_reports_main')
                    ->where(array('acctid' => $acctid, 'year' => $ar_year_prev))->get();
                $bnum_rows = $qry_billtrn_prev->num_rows();

                // CURRENT BILLTRN
                $qry_billtrn_current = $this->db->select('kwhuse, current, month')
                    ->from('billing_reports_main')
                    ->where(array('acctid' => $acctid, 'year' => $ar_year))->get();
                $cnum_rows = $qry_billtrn_current->num_rows();

                $prev_arr = array();
                $curr_arr = array();

                if($bnum_rows>0) {
                    foreach($qry_billtrn_prev->result() as $brow) {
                        $prev_arr[] = $brow->current;
                    }
                }

                if($cnum_rows>0) {
                    $i = 0;
                    foreach($qry_billtrn_current->result() as $crow) {
                        $lastmonth = false;
                        if ($i == 0) {
                            // first
                        } else if ($i == $cnum_rows - 1) {
                            $lastmonth = true;
                        }
                        $i++;
                        $curr_arr[] = array('current' => $crow->current, 'month' => $crow->month, 'lastmonth' => $lastmonth);
                    }
                }

                $otheramt = array();
                for ($m = 1; $m <= 12; $m++) {

                    $index = $m - 1;
                    $dt = DateTime::createFromFormat('m', $m);
                    $monthname = $dt->format('F');
                    $bulletClass = (isset($curr_arr[$index]['lastmonth']) && $curr_arr[$index]['lastmonth'] == true) ? 'lastBullet' : '';
                    $otheramt[] = array(
                        'month' => $monthname,
                        'curr' => (isset($curr_arr[$index]['current'])) ? $curr_arr[$index]['current'] : '0.00',
                        'prev' => (isset($prev_arr[$index])) ? $prev_arr[$index] : '0.00',
                        'bulletClass' => $bulletClass
                    );

                }
            }
        }

        $data['otheramt'] = $otheramt;
        $data['input'] = $this->input->post();
        return json_encode($data);
    }


    function print_billing() {
        $data = array();
        $qry = false;
        $exec = true;
        $msg = '';
        $html = '';
        $schedid = $this->input->post('schedid');
        $top = $this->input->post('top');

        if($schedid=='') {
            $exec = false;
            $msg = 'Select GDLB!';
        }

        if($top=='') {
            $exec = false;
            $msg = 'Get GDLB First';
        }


        $get_sched_details = $this->db->select('sysid, gdlbid, months, years, status')
            ->from('reading_schedule_main')
            ->where('sysid', $schedid)
            ->get()->row();
        if($get_sched_details) {
            $sched_year = $get_sched_details->years;
            $sched_month = $get_sched_details->months;
            $qry_rates = $this->db->select('count(sysid) AS cnt')
                ->from('trn_billing_rates')
                ->where(array('year' => $sched_year, 'month' => $sched_month))
                ->get()->row();

            if ($qry_rates && $qry_rates->cnt == 0) {
                $exec = false;
                $msg = 'Please encode billig rate for the year ' . $sched_year . ' and month of ' . date_formating($sched_month, '!m', 'M');
                $func = 'info';

            } else {
                // CHECK SCHED IN BILLING REFOPT FIRST
                $qry_billed_sched = $this->db->select('schedid')
                    ->from('billing_reports')
                    ->where(array('schedid' => $schedid))
                    ->get()->row();
                if ($qry_billed_sched == false) {
                    $exec = false;
                    $msg = 'Generate Billing first!';
                }
            }
        }else{
            $exec = false;
            $msg = 'Schedule not found!';
        }

        if($exec == true) {
            $qry_bill = $this->db->select(
                '
            billno, acctid,
            group, dist,
            lot, book,
            servno, mtr,
            mtrser, serial,
            name, addr,
            bmo, byr,
            month, year,
            prvdte, prsdte,
            duedate, load,
            rate, prvrdg,
            prsrdg, multcd,
            kwhuse, genamt,
            genamt1, trnamt,
            disamt, demamt,
            supamt, supper,
            mtramt, slamt,
            iccamt, iccsub,
            llramt, llrsub,
            lldamt, misamt,
            envamt, framt,
            npcamt, iccsamt,
            papc, fitamt,
            genchg, genchg1,
            trnchg, dischg,
            demchg, supchg,
            mtrchg, mtrper,
            slchg, mischg,
            envchg, npcchg,
            iccschg, fitchg,
            papcchg, genvat,
            trnvat, disvat,
            slvat, othvat,
            appsur, surbal,
            current, overdue,
            totacc, totint,
            scdisc, dolpay
            '
            )
                ->from('billing_reports')
                ->where(array('schedid' => $schedid))
                ->limit($top)
                ->get();
            if ($qry_bill->num_rows() > 0) {
                $i = 1;
                foreach ($qry_bill->result() as $row) {
                    $acctid = $row->acctid;
                    $qry_hist = $this->db->select('bmo, byr, prvdte, prsdte, prvrdg, prsrdg, kwhuse, current')
                        ->from('billing_reports')
                        ->where(array('acctid' => $acctid, 'kwhuse > ' => 0, 'bmo != ' => $row->bmo, 'byr != ' => $row->byr))
                        ->order_by('year', 'desc')
                        ->order_by('month', 'desc')
                        ->limit(6)
                        ->get();
                    $hist_data = ($qry_hist->num_rows() > 0) ? $qry_hist->result() : false;
                    $html .= $this->billing_form_kyocera($row, $hist_data, $i++);
                }
                $qry = true;
            }
        }
        $data['qry'] = $qry;
        $data['msg'] = $msg;
        $data['html'] = $html;
        $data['input'] = $this->input->post();
        return json_encode($data);
    }


    function single_print_bill($billid = false) {
        $data = array();
        $input_billid = $this->input->post('id');
        $id = ($input_billid) ? $input_billid : $billid;

        $html = '';
        $pae_logo = FCPATH . 'assets/global/img/logo/peco-logo-login.png';

        $html .= '<html>';
        $html .= '<head><title>Proposal</title></head>';
        $html .= '<body>';
        $html .= '<div style="display: inline-block; width: 40%;">';
        $html .= '<img src="'.$pae_logo.'" width="200px"/>';
        $html .= '</div>';
        $html .= '<div style="display: inline-block; width: 60%; text-align: right;">';
        $html .= '<h2 style="color: #ff7200;">MONTHLY BILLING STATEMENT</h2>';
        $html .= '</div>';
        $html .= '<hr>';
        $html .= '<div style="display: block;">';

        $html .= '</div>';
        $html .= '</body>';
        $html .= '</html>';

        $data['html'] = $html;
        return (object)$data;
        exit();

        $data = array();
        $qry = false;
        $html = '';
        $get_billing_reports_main = $this->db->select('acctid, month, year, billno')
            ->from('billing_reports_main')
            ->where('sysid', $id)
            ->get()->row();
        if($get_billing_reports_main) {
            $acctid = $get_billing_reports_main->acctid;


            $qry_bill = $this->db->select(
                '
            billno,
            acctid,
            group,
            dist,
            lot,
            book,
            servno,
            mtr,
            mtrser,
            serial,
            name,
            addr,
            bmo,
            byr,
            month,
            year,
            prvdte,
            prsdte,
            duedate,
            load,
            rate,
            prvrdg,
            prsrdg,
            multcd,
            kwhuse,
            genamt,
            genamt1,
            trnamt,
            disamt,
            demamt,
            supamt,
            supper,
            mtramt,
            slamt,
            iccamt,
            iccsub,
            llramt,
            llrsub,
            lldamt,
            misamt,
            envamt,
            framt,
            npcamt,
            iccsamt,
            papc,
            fitamt,
            genchg,
            genchg1,
            trnchg,
            dischg,
            demchg,
            supchg,
            mtrchg,
            mtrper,
            slchg,
            mischg,
            envchg,
            npcchg,
            iccschg,
            fitchg,
            papcchg,
            genvat,
            trnvat,
            disvat,
            slvat,
            othvat,
            appsur,
            surbal,
            current,
            overdue,
            totacc,
            totint,
            scdisc,
            dolpay
            '
            )
                ->from('billing_reports')
                ->where(array('acctid' => $acctid, 'billno' => $get_billing_reports_main->billno))
                ->get()->row();
            $html = '';
            if ($qry_bill) {
                $qry_hist = $this->db->select('bmo, byr, prvdte, prsdte, prvrdg, prsrdg, kwhuse, current')
                    ->from('billing_reports_main')
                    ->where(array('acctid' => $acctid, 'kwhuse > ' => 0))
                    ->order_by('byr', 'desc')
                    ->order_by('bmo', 'desc')
                    ->limit(6)
                    ->get();
                $bill_data = (object)$qry_bill;
                $hist_data = ($qry_hist->num_rows() > 0) ? $qry_hist->result() : false;
                //for($i=1; $i<=3; $i++) {
                $html .= $this->bill_form($bill_data, $hist_data, 1);
                // }
                $qry = true;
            }
        }

        $data['qry'] = $qry;
        $data['html'] = $html;
        $data['input'] = $this->input->post();
        return json_encode($data);

    }


    function single_preview_bill() {
        $data = array();
        $qry = false;
        $id = $this->input->post('id');
        $html = '';
        $get_billing_reports_main = $this->db->select('acctid, month, year, billno')
            ->from('billing_reports_main')
            ->where('sysid', $id)
            ->get()->row();
        if($get_billing_reports_main) {
            $acctid = $get_billing_reports_main->acctid;
            $year = $get_billing_reports_main->year;
            $month = $get_billing_reports_main->month;
            $billno = $get_billing_reports_main->billno;

            $data['billno'] = $billno;

            if($billno != '' && $billno > 0) {
                $data['err'] = '1';
                $qry_bill = $this->db->select(
                    '
                    billno,
                    acctid,
                    group,
                    dist,
                    lot,
                    book,
                    servno,
                    mtr,
                    mtrser,
                    serial,
                    name,
                    addr,
                    bmo,
                    byr,
                    month,
                    year,
                    prvdte,
                    prsdte,
                    duedate,
                    load,
                    rate,
                    prvrdg,
                    prsrdg,
                    multcd,
                    kwhuse,
                    genamt,
                    genamt1,
                    trnamt,
                    disamt,
                    demamt,
                    supamt,
                    supper,
                    mtramt,
                    slamt,
                    iccamt,
                    iccsub,
                    llramt,
                    llrsub,
                    lldamt,
                    misamt,
                    envamt,
                    framt,
                    npcamt,
                    iccsamt,
                    papc,
                    fitamt,
                    genchg,
                    genchg1,
                    trnchg,
                    dischg,
                    demchg,
                    supchg,
                    mtrchg,
                    mtrper,
                    slchg,
                    mischg,
                    envchg,
                    npcchg,
                    iccschg,
                    fitchg,
                    papcchg,
                    genvat,
                    trnvat,
                    disvat,
                    slvat,
                    othvat,
                    appsur,
                    surbal,
                    current,
                    overdue,
                    totacc,
                    totint,
                    scdisc,
                    dolpay
                    '
                )
                    ->from('billing_reports')
                    ->where(array('acctid' => $acctid, 'billno' => $billno))
                    ->get()->row();

            }else{


                $data['err'] = '2';

                $qry_bill = $this->db->select(
                    '
                    billno,
                    acctid,
                    group,
                    dist,
                    lot,
                    book,
                    servno,
                    mtr,
                    mtrser,
                    serial,
                    name,
                    addr,
                    bmo,
                    byr,
                    month,
                    year,
                    prvdte,
                    prsdte,
                    duedate,
                    load,
                    rate,
                    prvrdg,
                    prsrdg,
                    multcd,
                    kwhuse,
                    genamt,
                    genamt1,
                    trnamt,
                    disamt,
                    demamt,
                    supamt,
                    supper,
                    mtramt,
                    slamt,
                    iccamt,
                    iccsub,
                    llramt,
                    llrsub,
                    lldamt,
                    misamt,
                    envamt,
                    framt,
                    npcamt,
                    iccsamt,
                    papc,
                    fitamt,
                    genchg,
                    genchg1,
                    trnchg,
                    dischg,
                    demchg,
                    supchg,
                    mtrchg,
                    mtrper,
                    slchg,
                    mischg,
                    envchg,
                    npcchg,
                    iccschg,
                    fitchg,
                    papcchg,
                    genvat,
                    trnvat,
                    disvat,
                    slvat,
                    othvat,
                    appsur,
                    surbal,
                    current,
                    overdue,
                    totacc,
                    totint,
                    scdisc,
                    dolpay
                    '
                )
                    ->from('billing_reports')
                    ->where(array('acctid' => $acctid, 'billno' => $billno, 'year' => $year, 'month' => $month))
                    ->get()->row();
            }

            $html = '';
            if ($qry_bill) {
                $qry_hist = $this->db->select('bmo, byr, prvdte, prsdte, prvrdg, prsrdg, kwhuse, current')
                    ->from('billing_reports_main')
                    ->where(array('acctid' => $acctid, 'kwhuse > ' => 0))
                    ->order_by('byr', 'desc')
                    ->order_by('bmo', 'desc')
                    ->limit(6)
                    ->get();
                $bill_data = (object)$qry_bill;
                $hist_data = ($qry_hist->num_rows() > 0) ? $qry_hist->result() : false;
                //for($i=1; $i<=3; $i++) {
                $html .= $this->bill_form_pdf($bill_data, $hist_data, 1);
                // }
                $qry = true;
            }
        }

        $data['qry'] = $qry;
        $data['html'] = $html;
        $data['input'] = $this->input->post();
        return json_encode($data);
    }


    function bill_form_pdf($bill, $readhist = false) {
        // GOOGLE CHROME MINIMUM MARGIN
        $history_html = '';
        $history_html .= '<p style="font-weight: bold; font-size: 10px; margin: 0px 0px padding: 0px 0px; line-height: 10px; height: 12px; margin: 0px 0px; padding: 0px 0px; margin-top: 5px;" class="charges-list-item">Reading History</p>';
        if($readhist) {

            foreach($readhist as $hrow) {
                $prsdte = date_formating($hrow->prsdte, 'Y-m-d', 'm/d/Y');
                $prvdte = date_formating($hrow->prvdte, 'Y-m-d', 'm/d/Y');
                $blmonthname = date_formating($hrow->bmo, 'm', 'M');
                $blyearname = date_formating($hrow->byr, 'y', 'Y');


                $history_html .= '
                    <p style="font-weight: bold; font-size: 7px; margin: 0px 0px padding: 0px 0px; line-height: 6px; height: 6px; margin: 0px 0px; padding: 0px 0px; margin-top: 5px;" class="charges-list-item">
                    <span class="" style="position: absolute; left: 0px; width: 100px; text-align: left; padding: 0px 0px; margin: 0px 0px;">'.$prvdte.'-'.$prsdte.'</span> 
                    <span class="" style="position: absolute; left: 100px; width: 80px; text-align: left; padding: 0px 0px; margin: 0px 0px;">'.$blmonthname.' - '.$blyearname.'</span> 
                    <span class="" style="position: absolute; left: 130px; width: 55px; text-align: right; padding: 0px 0px; margin: 0px 0px;">'.$hrow->prvrdg.'</span> 
                    <span class="" style="position: absolute; left: 200px; width: 55px; text-align: right; padding: 0px 0px; margin: 0px 0px;">'.$hrow->prsrdg.'</span>0 
                    <span class="" style="position: absolute; left: 260px; width: 55px; text-align: right; padding: 0px 0px; margin: 0px 0px;">'.number_format($hrow->kwhuse). '</span>
                    <span class="" style="position: absolute; left: 320px; width: 60px; text-align: right; padding: 0px 0px; margin: 0px 0px;">'.number_format($hrow->current,2). '</span>
                    </p>
                ';
            }
        }

        $gdlb = $bill->group.$bill->dist.' '.str_pad($bill->lot,2,"0",STR_PAD_LEFT).'-'.$bill->book;
        $moyr = $bill->year . '-' . $bill->month;


        $current = $bill->current;
        $gdlb = $bill->group.'-'.$bill->dist.'-'.$bill->lot.'-'.$bill->book;
        // ##############################################
        // PECO RELATED CHARGES
        // AMT
        $disamt = round($bill->disamt, 2);
        $demamt = round($bill->demamt, 2);
        $supamt = round($bill->supamt, 2);
        $supper = round($bill->supper, 2);
        $mtramt = round($bill->mtramt, 2);
        // CHARGES
        $dischg = $bill->dischg;
        $demchg = 0;
        $supchg = $bill->supchg;
        $mtrchg = $bill->mtrchg;
        $mtrper = $bill->mtrper;
        // @TODO SOLVE 5 PESOS
        if($mtrper>0) {
            $mtrcharge = $mtrper;
        }else {
            $mtrcharge = 5;
        }

        $total_peco_charges = round(($disamt + $demamt + $supamt + $supper + $mtramt + $mtrcharge), 2);
        if($total_peco_charges != 0) {
            if($current > 0) {
                if($total_peco_charges > $current) {
                    $total_peco_charges_percent = ($total_peco_charges / $current) * 100;
                }else {
                    $total_peco_charges_percent = ($total_peco_charges / $current) * 100;
                }
            }else{
                $total_peco_charges_percent = 0;
            }
        }else{
            $total_peco_charges_percent = 0;
        }
        // ##############################################
        // SUPPLIER RELATED CHARGES (PPC, DEPC)
        // AMT
        $genamt = round($bill->genamt, 2);
        $genamt1 = round($bill->genamt1, 2);
        $trnamt = round($bill->trnamt, 2);
        $slamt = round($bill->slamt, 2);
        $papc = round($bill->papc, 2);
        // CHARGES
        $genchg = $bill->genchg;
        $genchg1 = $bill->genchg1;
        $trnchg = $bill->trnchg;
        $slchg = $bill->slchg;
        $papcchg = $bill->papcchg;
        $total_supplier_charges = round(($genamt + $genamt1 + $trnamt + $slamt + $papc), 2);
        if($total_supplier_charges != 0) {
            if($current > 0) {
                if($total_supplier_charges > $current){
                    $total_supplier_charges_percent = ($total_supplier_charges / $current) * 100;
                }else {
                    $total_supplier_charges_percent = ($total_supplier_charges / $current) * 100;
                }
            }else{
                $total_supplier_charges_percent = 0;
            }
        }else{
            $total_supplier_charges_percent = 0;
        }
        // ##############################################
        // SUBSIDIES
        // AMT
        $iccamt = round($bill->iccamt, 2);
        $iccsub = round($bill->iccsub, 2);
        $iccsamt = round($bill->iccsamt, 2);
        $llramt = round($bill->llramt, 2);
        $llrsub = round($bill->llrsub, 2);
        $lldamt = round($bill->lldamt, 2);
        // CHARGES
        $iccschg = $bill->iccschg;
        $total_subsidies_charges = round(($iccamt + $llramt), 2);
        if($total_subsidies_charges != 0) {
            if( $current > 0) {
                if($total_subsidies_charges>$current) {
                    $total_subsidies_charges_percent = ($total_subsidies_charges / $current) * 100;
                }else {
                    $total_subsidies_charges_percent = ($total_subsidies_charges / $current) * 100;
                }
            }else{
                $total_subsidies_charges_percent = 0;
            }
        }else{
            $total_subsidies_charges_percent = 0;
        }
        // ##############################################
        // TAXES AND UNIVERSAL CHARGES
        // AMT
        $genvat = round($bill->genvat, 2);
        $trnvat = round($bill->trnvat, 2);
        $disvat = round($bill->disvat, 2);
        $slvat = round($bill->slvat, 2);
        $othvat = round(($bill->othvat + $slvat + $disvat), 2);
        $misamt = round($bill->misamt, 2);
        $envamt = round($bill->envamt, 2);
        $framt = round($bill->framt, 2);
        $npcamt = round($bill->npcamt, 2);
        $iccsamt = round($bill->iccsamt, 2);
        $fitamt = round(($bill->fitchg * $bill->kwhuse), 2);

        // CHARGES
        $mischg = $bill->mischg;
        $envchg = $bill->envchg;
        $npcchg = $bill->npcchg;
        $fitchg = $bill->fitchg;
        $total_tax_universal_charges = round(($genvat + $trnvat + $othvat + $misamt + $envamt + $framt + $npcamt + $iccsamt + $fitamt), 2);
        if($total_tax_universal_charges != 0) {
            if($current > 0) {
                if($total_tax_universal_charges > $current) {
                    $total_tax_universal_charges_percent = ($total_tax_universal_charges / $current) * 100;
                }else {
                    $total_tax_universal_charges_percent = ($total_tax_universal_charges / $current) * 100;
                }
            }else{
                $total_tax_universal_charges_percent = 0;
            }
        }else{
            $total_tax_universal_charges_percent = 0;
        }
        // #######################################
        $appsur = $bill->appsur;
        $surbal = $bill->surbal;
        $overdue = $bill->overdue;
        $totacc = $bill->totacc;
        $totint = $bill->totint;
        $scdisc = $bill->scdisc;

        $dt1 = DateTime::createFromFormat('Y-m-d', $bill->prvdte);
        $newdte_prvdte = $dt1->format('m/d/Y');

        $dt2 = DateTime::createFromFormat('Y-m-d', $bill->prsdte);
        $newdte_prsdte = $dt2->format('m/d/Y');

        $billing_period = $newdte_prvdte.'-'.$newdte_prsdte;
        // $billing_period = date_formating($hrow->prvdte, 'Y-m-d', 'm/d/Y'). '-' .date_formating($hrow->prsdte, 'Y-m-d', 'm/d/Y');
        // BILLING FORM
        // $bgimg = base_url('assets/global/img/bill_form_bg.jpg');


        // CURRENT
        $total_charges = round(($total_peco_charges + $total_supplier_charges + $total_subsidies_charges + $total_tax_universal_charges), 2);

        $bgimg = base_url() . 'assets/global/img/bill_form_bg.jpg';
        $html = '';
        $html .= '<html>';
        $html .= '<head>';
        $html .= '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" >';
        $html .= '<title>TVI | Tiedra Ventures, Inc.</title>';
        $html .= '<style> body, html, *, p, span, h1, h2, h3, h4, h5 { font-family: Courier !important; }</style>';
        $html .= '</head>';
        $html .= '<body>';
        $html .= '<img style="z-index: 0; position: absolute; top: 0px; left: 0px; width: 730px;" src="'.$bgimg.'" />';
        $html .= '<div class="bill-form" style="z-index: 1; display: inline-block; min-height: 800px;">';
        $html .= '<div class="rep-content" style="width: 650px; display: inline-block; top: 0px; left: 0px; ">';
        $html .= '<p style="font-weight: normal; font-size: 9px; margin: 0px 0px; padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px; margin-top: 55px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 70px;">'.$bill->name.'</span>';
        $html .= '<span style="position: absolute; left: 620px; width: 100px; display: inline-block;">'.$bill->servno.'</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; margin: 0px 0px; padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px; margin-top: 5px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 70px;">'.$bill->addr.'</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px; margin-top: 45px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 5px;">'.$gdlb.'</span>';
        $html .= '<span style="position: absolute; left: 65px;">'.$bill->servno.'</span>';
        $html .= '<span style="position: absolute; left: 160px;">'.$bill->mtr.'</span>';
        $html .= '<span style="position: absolute; left: 190px;">'.$moyr.'</span>';
        $html .= '<span style="position: absolute; left: 260px;">'.$billing_period.'</span>';
        $html .= '<span style="position: absolute; left: 410px;">'.$bill->duedate.'</span>';
        $html .= '<span style="position: absolute; left: 540px;">'.number_format($current,2).'</span>';
        $html .= '</p>';


        $html .= '<p style="font-weight: normal; font-size: 9px; padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px; margin-top: 50px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 5px;">'.$bill->mtrser.'</span>';
        $html .= '<span style="position: absolute; left: 70px;">'.$bill->serial.'</span>';
        $html .= '<span style="position: absolute; left: 150px;">'.number_format($bill->load).'</span>';
        $html .= '<span style="position: absolute; left: 245px;">'.$bill->rate.'</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px; margin-top: 25px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 5px;">'.round($bill->prsrdg).'</span>';
        $html .= '<span style="position: absolute; left: 70px;">'.round($bill->prvrdg).'</span>';
        $html .= '<span style="position: absolute; left: 125px;">'.$bill->multcd.'</span>';
        $html .= '<span style="position: absolute; left: 150px;">'.number_format($bill->kwhuse).'</span>';
        $html .= '</p>';


        // ##### UNPAID BILLS #####################
        $html .= '<p style="font-weight: normal; font-size: 9px; padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px; margin-top: 35px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 170px; text-align: right; display: inline-block; width: 70px;">'.number_format($current, 2).'</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px; margin-top: 15px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 170px; text-align: right; display: inline-block; width: 70px;">'.number_format($bill->overdue, 2).'</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px; margin-top: 15px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 170px; text-align: right; display: inline-block; width: 70px;">'.number_format($bill->totint, 2).'</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px; margin-top: 30px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 170px; text-align: right; display: inline-block; width: 70px;">'.number_format($bill->totacc, 2).'</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px; margin-top: 20px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 100px; text-align: right; display: inline-block; width: 70px;">'.$bill->dolpay.'</span>';
        $html .= '</p>';

        // #### CUSTOM MESSAGE
        $html .= '<p style="font-weight: normal; font-size: 9px; padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px; margin-top: 95px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 10px; text-align: left; display: inline-block; width: 200px;">';
        $html .= 'Hello valued customer! <br>Custom message, custom message, custom message, custom message, custom message, custom message, custom message.';
        $html .= '</span>';
        $html .= '</p>';

        // #### BILLING NUMBER -->
        $html .= '<p style="font-weight: normal; font-size: 8px; padding: 0px 0px; line-height: 12px; height: 10px;margin: 0px 0px; padding: 0px 0px; margin-top: 10px;" class="charges-list-item">';
        $html .= '<span style="font-family: courier, monospace; position: absolute; top: 700px; left: 5px; text-align: left; display: inline-block; width: 600px;">';
        $html .= 'BIR PERMIT NO.: 04-2015-123-0011-000. DATE OF ISSUE: MARCH 25, 2015. <br>';
        $html .= 'SOA SERIES: 00000001-99999999';
        $html .= '</span>';
        $html .= '<span style="font-family: courier, monospace; font-size: 15px; font-weight: bold; position: absolute; top: 710px; left: 550px; text-align: left; display: inline-block; width: 600px;">'.$bill->billno.'</span>';
        $html .= '</p>';

        // #### FIRST STUB #####################
        $html .= '<div style="position: absolute; top: 785px; left: 0px;">';
        $html .= '<p style="font-weight: normal; font-size: 9px; padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px; margin-top: 5px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 8px; text-align: left; display: inline-block; width: 100px;">';
        $html .= $bill->servno;
        $html .= '</span>';
        $html .= '<span style="position: absolute; left: 88px; text-align: left; display: inline-block; width: 70px;">';
        $html .= $bill->mtr;
        $html .= '</span>';
        $html .= '<span style="position: absolute; left: 115px; text-align: left; display: inline-block; width: 200px;">';
        $html .= $bill->name;
        $html .= '</span>';
        $html .= '<span style="position: absolute; left: 115px; margin-top: 12px; text-align: left; display: inline-block;width: 200px;">';
        $html .= $bill->addr;
        $html .= '</span>';
        $html .= '<span style="position: absolute; left: 380px; text-align: left; display: inline-block; width: 70px;">';
        $html .= $moyr;
        $html .= '</span>';
        $html .= '<span style="position: absolute; left: 490px; text-align: right; margin-top: 10px; display: inline-block; width: 70px;">';
        $html .= number_format($bill->totint, 2);
        $html .= '</span>';
        $html .= '<span style="position: absolute; left: 620px; text-align: right; margin-top: 5px; display: inline-block; width: 70px;">';
        $html .= number_format($current, 2);
        $html .= '</span>';
        $html .= '<span style="position: absolute; left: 620px; text-align: right; margin-top: 17px; display: inline-block; width: 70px;">';
        $html .= number_format($bill->overdue, 2);
        $html .= '</span>';
        $html .= '<span style="position: absolute; left: 490px; margin-top: 40px; text-align: right; display: inline-block; width: 70px;">';
        $html .= number_format($bill->totint, 2);
        $html .= '</span>';
        $html .= '<span style="position: absolute; left: 620px; margin-top: 40px; text-align: right; display: inline-block; width: 70px;">';
        $html .= number_format(($bill->overdue + $current), 2);
        $html .= '</span>';

        // ### GRAND TOTAL ############
        $html .= '<span style="position: absolute; left: 620px; margin-top: 65px; text-align: right; display: inline-block; width: 70px;">';
        $html .= number_format(($bill->totacc+$bill->totint), 2);
        $html .= '</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px; margin-top: 30px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 130px; text-align: left; margin-top: 0px; display: inline-block; width: 70px;">';
        $html .= $bill->dolpay;
        $html .= '</span>';
        $html .= '<span style="position: absolute; left: 335px; text-align: right; text-align: left; display: inline-block; width: 70px;">';
        $html .= $bill->duedate;
        $html .= '</span>';
        $html .= '</p>';
        $html .= '</div>';

        // #### BILLING NUMBER #############
        $html .= '<p style="font-weight: normal; font-size: 8px; padding: 0px 0px; line-height: 12px; height: 10px;margin: 0px 0px; padding: 0px 0px; margin-top: 10px;" class="charges-list-item">';
        $html .= '<span style="font-family: courier, monospace; position: absolute; top: 892px; left: 5px; text-align: left; display: inline-block; width: 600px;">';
        $html .= 'BIR PERMIT NO.: 04-2015-123-0011-000. DATE OF ISSUE: MARCH 25, 2015. <br>';
        $html .= 'SOA SERIES: 00000001-99999999';
        $html .= '</span>';
        $html .= '<span style="font-family: courier, monospace; font-size: 15px; font-weight: bold; position: absolute; top: 895px; left: 552px; text-align: left; display: inline-block; width: 600px;">'.$bill->billno.'</span>';
        $html .= '</p>';

        // #### SECOND STUB ##############
        $html .= '<div style="position: absolute; top: 975px; left: 5px;">';
        $html .= '<p style="font-weight: normal; font-size: 9px; padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px; margin-top: 2px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 630px; text-align: right; display: inline-block; width: 70px;">';
        $html .= number_format($bill->current, 2);
        $html .= '</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px; margin-top: 2px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 630px; text-align: right; display: inline-block; width: 70px;">';
        $html .= number_format($bill->overdue, 2);
        $html .= '</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px; margin-top: 2px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 630px; text-align: right; display: inline-block; width: 70px;">';
        $html .= number_format(($bill->totint), 2);
        $html .= '</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px; margin-top: 25px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 630px; text-align: right; display: inline-block; width: 70px;">';
        $html .= number_format(($bill->totacc+$bill->totint), 2);
        $html .= '</span>';
        $html .= '</p>';
        $html .= '</div>';
        $html .= '<div style="position: absolute; top: 965px; left: 5px;">';
        $html .= '<p style="font-weight: normal; font-size: 9px; padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px; margin-top: 10px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 130px; text-align: left; margin-top: 60px; display: inline-block; width: 70px;">';
        $html .= $bill->dolpay;
        $html .= '</span>';
        $html .= '<span style="position: absolute; left: 310px; text-align: right; margin-top: 60px; display: inline-block; width: 70px;">';
        $html .= $bill->duedate;
        $html .= '</span>';
        $html .= '</p>';
        $html .= '</div>';

        $html .= '<div style="position: absolute; top: 970px; left: 5px;">';
        $html .= '<p style="font-weight: normal; font-size: 9px; padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px; margin-top: 5px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 0px; text-align: left; display: inline-block; width: 70px;">';
        $html .= trim($gdlb);
        $html .= '</span>';
        $html .= '<span style="position: absolute; left: 58px; text-align: left; display: inline-block; width: 70px;">';
        $html .= $bill->servno;
        $html .= '</span>';
        $html .= '<span style="position: absolute; left: 60px; text-align: right; display: inline-block; width: 70px;">';
        $html .= $bill->mtr;
        $html .= '</span>';
        $html .= '<span style="position: absolute; left: 150px; text-align: left; display: inline-block; width: 200px;">';
        $html .= $bill->name;
        $html .= '</span>';
        $html .= '<span style="position: absolute; left: 305px; text-align: center; display: inline-block; width: 60px;">';
        $html .= $bill->mtrser;
        $html .= '</span>';
        $html .= '<span style="position: absolute; left: 368px; text-align: center; display: inline-block; width: 70px;">';
        $html .= $bill->serial;
        $html .= '</span>';
        $html .= '<span style="position: absolute; left: 440px; text-align: center; display: inline-block; width: 55px;">';
        $html .= $moyr;
        $html .= '</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px; margin-top: 5px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 150px; text-align: left; display: inline-block; width: 200px;">';
        $html .= $bill->addr;
        $html .= '</span>';
        $html .= '</p>';
        $html .= '</div>';


        // #### BILLING NUMBER ##############
        $html .= '<p style="font-weight: normal; font-size: 9px; padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px; margin-top: 10px;" class="charges-list-item">';
        $html .= '<span style="font-family: courier, monospace; font-size: 15px; font-weight: bold; position: absolute; top: 1100px; left: 550px; text-align: left; display: inline-block; width: 600px;">';
        $html .= $bill->billno;
        $html .= '</span>';
        $html .= '</p>';

        // #### PAGE NUMBER ###############
        $html .= '<div style="position: absolute; top: 1130px; left: 5px;">';
        $html .= '<p style="font-weight: normal; font-size: 9px; padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px; margin-top: 5px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 600px; text-align: right; display: inline-block; width: 100px;">';
        $html .= 'Page: '.str_pad(1,9,"0",STR_PAD_LEFT);
        $html .= '</span>';
        $html .= '</p>';
        $html .= '</div>';

        // ############ START CHARGES TABLE ####################################
        $html .= '<div style="position: absolute; left: 280px; width: 440px; top: 170px; height: 540px;">';
        // ########## PECO RELATED CHARGES ###############
        $html .= '<p style="font-weight: bold; font-size: 10px; line-height: 10px; padding: 0px 0px; margin: 0px 0px; margin-top: 10px;">PECO RELEATED CHARGES';
        $html .= '<span class="charges-header-rate" style="position: absolute; left: 220px; width: 80px; text-align: right">PER KWH</span>';
        $html .= '<span class="charges-header-amt" style="position: absolute; left: 290px; width: 100px; text-align: right">AMOUNT</span> </p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; margin: 0px 0px padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
        $html .= 'Distribution Charge';
        $html .= '<span style="position: absolute; left: 222px; width: 70px; display: inline-block; text-align: right;">'.$dischg.'</span>';
        $html .= '<span style="position: absolute; left: 315px; width: 70px; display: inline-block; text-align: right;">'.number_format($disamt,2).'</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; margin: 0px 0px padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
        $html .= 'Demand Charge ';
        $html .= '<span style="position: absolute; left: 222px; width: 70px; display: inline-block; text-align: right;"></span>';
        $html .= '<span style="position: absolute; left: 315px; width: 70px; display: inline-block; text-align: right;">'.number_format($demamt,2).'</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; margin: 0px 0px padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
        $html .= 'Supply Charge';
        $html .= '<span style="position: absolute; left: 222px; width: 70px; display: inline-block; text-align: right;">'.$supchg.'</span>';
        $html .= '<span style="position: absolute; left: 315px; width: 70px; display: inline-block; text-align: right;">'.number_format($supamt,2).'</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; margin: 0px 0px padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
        $html .= 'Metering Charge';
        $html .= '<span style="position: absolute; left: 222px; width: 70px; display: inline-block; text-align: right;">'.$mtrchg.'</span>';
        $html .= '<span style="position: absolute; left: 315px; width: 70px; display: inline-block; text-align: right;">'.number_format($mtramt,2).'</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; margin: 0px 0px padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
        $html .= 'Retail Custom Charge';
        $html .= '<span style="position: absolute; left: 222px; width: 70px; display: inline-block; text-align: right;"></span>';
        $html .= '<span style="position: absolute; left: 315px; width: 70px; display: inline-block; text-align: right;">'.number_format($mtrcharge, 2).'</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; margin: 0px 0px padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 222px; width: 70px; display: inline-block; text-align: right; font-weight: bold;">Sub Total</span>';
        $html .= '<span style="position: absolute; left: 315px; width: 70px; display: inline-block; text-align: right; font-weight: bold;">'.number_format($total_peco_charges, 2).'</span>';
        $html .= '<span style="position: absolute; left: 354px; width: 70px; display: inline-block; text-align: right;">'.number_format($total_peco_charges_percent, 2).'%</span>';
        $html .= '</p>';

        // ########## SUPLIER RELATED CHARGES ###############
        $html .= '<p style="font-weight: bold; font-size: 10px; line-height: 10px; padding: 0px 0px; margin: 0px 0px; margin-top: 10px;">SUPLIER RELATED CHARGES (PPC, PEDC)</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; margin: 0px 0px padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
        $html .= 'Generation Charge ';
        $html .= '<span style="position: absolute; left: 222px; width: 70px; display: inline-block; text-align: right;">'.$genchg.'</span>';
        $html .= '<span style="position: absolute; left: 315px; width: 70px; display: inline-block; text-align: right;">'.number_format($genamt, 2).'</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; margin: 0px 0px padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
        $html .= 'Previous Months Adjustment on Generation Cost ';
        $html .= '<span style="position: absolute; left: 222px; width: 70px; display: inline-block; text-align: right;"></span>';
        $html .= '<span style="position: absolute; left: 315px; width: 70px; display: inline-block; text-align: right;">0.00</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; margin: 0px 0px padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
        $html .= 'Previous Years\' Adjustment on Power Cost';
        $html .= '<span style="position: absolute; left: 222px; width: 70px; display: inline-block; text-align: right;">'.$papcchg.'</span>';
        $html .= '<span style="position: absolute; left: 315px; width: 70px; display: inline-block; text-align: right;">'.number_format($papc, 2).'</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; margin: 0px 0px padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
        $html .= '(ERC Case No. 2001-333) ';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; margin: 0px 0px padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
        $html .= 'Transmission Charge';
        $html .= '<span style="position: absolute; left: 222px; width: 70px; display: inline-block; text-align: right;">'.$trnchg.'</span>';
        $html .= '<span style="position: absolute; left: 315px; width: 70px; display: inline-block; text-align: right;">'.number_format($trnamt, 2).'</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; margin: 0px 0px padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
        $html .= 'System Loss Charge';
        $html .= '<span style="position: absolute; left: 222px; width: 70px; display: inline-block; text-align: right;">'.$slchg.'</span>';
        $html .= '<span style="position: absolute; left: 315px; width: 70px; display: inline-block; text-align: right;">'.number_format($slamt, 2).'</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; margin: 0px 0px padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 222px; width: 70px; display: inline-block; text-align: right; font-weight: bold;">Sub Total</span>';
        $html .= '<span style="position: absolute; left: 315px; width: 70px; display: inline-block; text-align: right; font-weight: bold;">'.number_format($total_supplier_charges,2).'</span>';
        $html .= '<span style="position: absolute; left: 354px; width: 70px; display: inline-block; text-align: right;">'.number_format($total_supplier_charges_percent,2).'%</span>';
        $html .= '</p>';


        $html .= '<p style="font-weight: bold; font-size: 10px; line-height: 10px; padding: 0px 0px; margin: 0px 0px; margin-top: 10px;">SUBSIDIES</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; margin: 0px 0px padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
        $html .= 'Inter-class scross-subsidy ';
        $html .= '<span style="position: absolute; left: 222px; width: 70px; display: inline-block; text-align: right;"></span>';
        $html .= '<span style="position: absolute; left: 315px; width: 70px; display: inline-block; text-align: right;">'.number_format($iccamt, 2).'</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; margin: 0px 0px padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
        $html .= 'Lifeline rate subsidy';
        $html .= '<span style="position: absolute; left: 222px; width: 70px; display: inline-block; text-align: right;">'.number_format($llrsub, 4).'</span>';
        $html .= '<span style="position: absolute; left: 315px; width: 70px; display: inline-block; text-align: right;">'.number_format($llramt, 2).'</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; margin: 0px 0px padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 222px; width: 70px; display: inline-block; text-align: right; font-weight: bold;">Sub Total</span>';
        $html .= '<span style="position: absolute; left: 315px; width: 70px; display: inline-block; text-align: right; font-weight: bold;">'.number_format($total_subsidies_charges,2).'</span>';
        $html .= '<span style="position: absolute; left: 354px; width: 70px; display: inline-block; text-align: right;">'.number_format($total_subsidies_charges_percent,2).'%</span>';
        $html .= '</p>';

        // ########## TAX  ##############################
        $html .= '<p style="font-weight: bold; font-size: 10px; line-height: 10px; padding: 0px 0px; margin: 0px 0px; margin-top: 10px;">TAX AND UNIVERSAL CHARGES</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; margin: 0px 0px padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
        $html .= 'VAT on Generation';
        $html .= '<span style="position: absolute; left: 222px; width: 70px; display: inline-block; text-align: right;"></span>';
        $html .= '<span style="position: absolute; left: 315px; width: 70px; display: inline-block; text-align: right;">'.number_format($genvat, 2).'</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; margin: 0px 0px padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
        $html .= 'VAT on Transmission';
        $html .= '<span style="position: absolute; left: 222px; width: 70px; display: inline-block; text-align: right;"></span>';
        $html .= '<span style="position: absolute; left: 315px; width: 70px; display: inline-block; text-align: right;">'.number_format($trnvat, 2).'</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; margin: 0px 0px padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
        $html .= 'VAT on Other Charges';
        $html .= '<span style="position: absolute; left: 222px; width: 70px; display: inline-block; text-align: right;"></span>';
        $html .= '<span style="position: absolute; left: 315px; width: 70px; display: inline-block; text-align: right;">'.number_format($othvat, 2).'</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; margin: 0px 0px padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
        $html .= 'Franchise Tax';
        $html .= '<span style="position: absolute; left: 222px; width: 70px; display: inline-block; text-align: right;"></span>';
        $html .= '<span style="position: absolute; left: 315px; width: 70px; display: inline-block; text-align: right;">'.number_format($framt, 2).'</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; margin: 0px 0px padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
        $html .= 'Missionary';
        $html .= '<span style="position: absolute; left: 222px; width: 70px; display: inline-block; text-align: right;">'.$mischg.'</span>';
        $html .= '<span style="position: absolute; left: 315px; width: 70px; display: inline-block; text-align: right;">'.number_format($misamt, 2).'</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; margin: 0px 0px padding: 0px 0px; line-height: 14px; height: 12px ;margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
        $html .= 'Environmental';
        $html .= '<span style="position: absolute; left: 222px; width: 70px; display: inline-block; text-align: right;">'.$envchg.'</span>';
        $html .= '<span style="position: absolute; left: 315px; width: 70px; display: inline-block; text-align: right;">'.number_format($envamt, 2).'</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; margin: 0px 0px padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
        $html .= 'NPC Stranded Cntract Cost';
        $html .= '<span style="position: absolute; left: 222px; width: 70px; display: inline-block; text-align: right;">'.$npcchg.'</span>';
        $html .= '<span style="position: absolute; left: 315px; width: 70px; display: inline-block; text-align: right;">'.number_format($npcamt, 2).'</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; margin: 0px 0px padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
        $html .= 'ICCS Adjustment';
        $html .= '<span style="position: absolute; left: 222px; width: 70px; display: inline-block; text-align: right;">'.$iccschg.'</span>';
        $html .= '<span style="position: absolute; left: 315px; width: 70px; display: inline-block; text-align: right;">'.number_format($iccsamt, 2).'</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; margin: 0px 0px padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
        $html .= 'FIT - Allowance';
        $html .= '<span style="position: absolute; left: 222px; width: 70px; display: inline-block; text-align: right;">'.$fitchg.'</span>';
        $html .= '<span style="position: absolute; left: 315px; width: 70px; display: inline-block; text-align: right;">'.number_format($fitamt, 2).'</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; margin: 0px 0px padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 222px; width: 70px; display: inline-block; text-align: right; font-weight: bold;">Sub Total</span>';
        $html .= '<span style="position: absolute; left: 315px; width: 70px; display: inline-block; text-align: right; font-weight: bold;">'.number_format($total_tax_universal_charges,2).'</span>';
        $html .= '<span style="position: absolute; left: 354px; width: 70px; display: inline-block; text-align: right;">'.number_format($total_tax_universal_charges_percent,2).'%</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: bold; font-size: 10px; margin: 0px 0px padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px; margin-top: 5px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 315px; width: 70px; display: inline-block; text-align: right;">'.number_format($current,2).'</span>';
        $html .= '</p>';

        $html .= $history_html;

        $html .= '<h3 style="position: absolute; bottom: 10px; left: 10px; right: 10px; font-size: 9px; font-weight: bold; margin-top: 20px; padding: 0px 0px;">THIS IS A SYSTEM GENERATED STATEMENT OF ACCOUNT. NO SIGNATURE IS REQUIRED.</H3>';
        $html .= '</div>';

        // ##### END OF CHARGES TABLE ################################
        $html .= '<footer></footer>';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '</body>';
        $html .= '</html>';
        return $html;
    }


    function billing_form($bill, $history, $page) {
        $gdlb = $bill->group.$bill->dist.' '.str_pad($bill->lot,2,"0",STR_PAD_LEFT).'-'.$bill->book;
        $moyr = str_pad($bill->bmo,2,"0",STR_PAD_LEFT).'-'.str_pad($bill->byr,2,"0",STR_PAD_LEFT);
        $history_html = '';
        $next_billing = '';
        if($history) {
            $i = 0;
            $len = count($history);
            foreach($history as $hrow) {
                $prsdte = ($hrow->prsdte!='') ? date_formating($hrow->prsdte, 'Y-m-d', 'm/d/Y') : '';
                $prvdte = ($hrow->prvdte!='') ? date_formating($hrow->prvdte, 'Y-m-d', 'm/d/Y') : '';
                $blmonthname = ($hrow->bmo!='') ? date_formating($hrow->bmo, 'm', 'M') : '';
                $blyearname = ($hrow->byr!='') ? date_formating($hrow->byr, 'y', 'Y') : '';


                $history_html .= '
                    <p style="font-weight: bold; font-size: 7px; line-height: 10px; height: 10px; padding: 0px 0px; margin: 0px 0px; position: relative">
                        <span class="" style="position: absolute; left: 0px; width: 80px; text-align: left; padding: 0px 0px; margin: 0px 0px;">'.$prvdte.'-'.$prsdte.'</span> 
                        <span class="" style="position: absolute; left: 80px; width: 80px; text-align: left; padding: 0px 0px; margin: 0px 0px;">'.$blmonthname.' - '.$blyearname.'</span> 
                        <span class="" style="position: absolute; left: 130px; width: 55px; text-align: right; padding: 0px 0px; margin: 0px 0px;">'.$hrow->prvrdg.'</span> 
                        <span class="" style="position: absolute; left: 200px; width: 55px; text-align: right; padding: 0px 0px; margin: 0px 0px;">'.$hrow->prsrdg.'</span>0 
                        <span class="" style="position: absolute; left: 260px; width: 55px; text-align: right; padding: 0px 0px; margin: 0px 0px;">'.number_format($hrow->kwhuse). '</span>
                        <span class="" style="position: absolute; left: 320px; width: 60px; text-align: right; padding: 0px 0px; margin: 0px 0px;">'.number_format($hrow->current,2). '</span>
                     </p>
                ';

                /*
                if ($i == 0) {
                    // first
                    $lastprsrdg = $hrow->prsdte;
                    $next_billing = date('Y-m-d', strtotime("+30 days", strtotime($lastprsrdg)));
                } else if ($i == $len - 1) {
                    // LAST
                    $lastprsrdg = $hrow->prsdte;
                    $next_billing = date('Y-m-d', strtotime("+30 days", strtotime($lastprsrdg)));
                }
                $i++;
                */
            }
        }

        $next_billing = date('Y-m-d', strtotime("+30 days", strtotime($bill->prsdte)));




        $current = $bill->current;
        // ##############################################
        // PECO RELATED CHARGES
        // AMT
        $disamt = $bill->disamt;
        $demamt = $bill->demamt;
        $supamt = $bill->supamt;
        $supper = $bill->supper;
        $mtramt = $bill->mtramt;
        // CHARGES
        $dischg = $bill->dischg;
        $demchg = $bill->demchg;
        $supchg = $bill->supchg;
        $mtrchg = $bill->mtrchg;
        $mtrper = $bill->mtrper;
        $total_peco_charges = ($disamt + $demamt + $supamt + $supper + $mtramt + 5);
        $total_peco_charges_percent = ($total_peco_charges / $current ) * 100;
        // ##############################################
        // SUPPLIER RELATED CHARGES (PPC, DEPC)
        // AMT
        $genamt = $bill->genamt;
        $genamt1 = $bill->genamt1;
        $trnamt = $bill->trnamt;
        $slamt = $bill->slamt;
        $papc = $bill->papc;
        // CHARGES
        $genchg = $bill->genchg;
        $genchg1 = $bill->genchg1;
        $trnchg = $bill->trnchg;
        $slchg = $bill->slchg;
        $papcchg = $bill->papcchg;
        $total_supplier_charges = ($genamt + $genamt1 + $trnamt + $slamt + $papc);
        $total_supplier_charges_percent = ($total_supplier_charges / $current ) * 100;
        // ##############################################
        // SUBSIDIES
        // AMT
        $iccamt = $bill->iccamt;
        $iccsub = $bill->iccsub;
        $iccsamt = $bill->iccsamt;
        $llramt = $bill->llramt;
        $llrsub = $bill->llrsub;
        $lldamt = $bill->lldamt;
        // CHARGES
        $iccschg = $bill->iccschg;
        $total_subsidies_charges = ($iccamt + $llramt);
        $total_subsidies_charges_percent = ($total_subsidies_charges / $current ) * 100;
        // ##############################################
        // TAXES AND UNIVERSAL CHARGES
        // AMT
        $genvat = $bill->genvat;
        $trnvat = $bill->trnvat;
        $disvat = $bill->disvat;
        $slvat = $bill->slvat;
        $othvat = ($bill->othvat + $slvat + $disvat);
        $misamt = $bill->misamt;
        $envamt = $bill->envamt;
        $framt = $bill->framt;
        $npcamt = $bill->npcamt;
        $fitamt = $bill->fitamt;
        // CHARGES
        $mischg = $bill->mischg;
        $envchg = $bill->envchg;
        $npcchg = $bill->npcchg;
        $fitchg = $bill->fitchg;
        $total_tax_universal_charges = ($genvat + $trnvat + $othvat + $misamt + $envamt + $framt + $npcamt + $fitamt);
        $total_tax_universal_charges_percent = ($total_tax_universal_charges / $current ) * 100;
        // #######################################
        $appsur = $bill->appsur;
        $surbal = $bill->surbal;
        $overdue = $bill->overdue;
        $totacc = $bill->totacc;
        $totint = $bill->totint;
        $scdisc = $bill->scdisc;

        $billing_period = date_formating($hrow->prvdte, 'Y-m-d', 'm/d/Y'). '-' .date_formating($hrow->prsdte, 'Y-m-d', 'm/d/Y');

        $overdue_amt = 0;
        $grand_total = $current + $overdue_amt;


        // BILLING FORM
        $bgimg = '';
        $kyocera = 'top: 50px; left: 10px;';
        //$bgimg = base_url().'assets/global/img/bill_form_bg.jpg';
        $html = '
        <div  class="bill-form" style="background: url('.$bgimg.'); background-size: 700px 100%">
        <div class="rep-content" style="width: 650px; display: inline-block; '.$kyocera.' ">
                <div style="width: 650px; display: inline-block; position: relative; left: 10px;; top: 5px;">
                    <p style="display: block; position: relative; font-weight: normal; width: 100%; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px;">
                        <span style="position: absolute; left: 80px; width: 200px; padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.$bill->name.'</span>
                        <span style="position: absolute; right: 0px; width: 90px; padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px; text-align: right">'.$bill->servno.'</span>
                    </p>
                    
                    <p style="display: block; position: relative; font-weight: normal; width: 100%; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px;">
                        <span style="position: absolute; left: 80px; width: 200px; padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.$bill->addr.'</span>
                    </p>
                </div>
                
                <div style="width: 650px; display: inline-block; position: relative; left: 10px;; top: 45px; ">
                    <p style="display: block; position: relative; font-weight: normal; width: 100%; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px;">
                        <span style="position: absolute; left: 05px; width: 80px; padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.$gdlb.'</span>
                        <span style="position: absolute; left: 75px; width: 50px; padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.$bill->servno.'</span>
                        <span style="position: absolute; left: 140px; width: 30px; padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.$bill->mtr.'</span>
                        <span style="position: absolute; left: 175px; width: 80px; padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.$moyr.'</span>
                        <span style="position: absolute; left: 230px; width: 80px; padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.$billing_period.'</span>
                        <span style="position: absolute; left: 385px; width: 80px; padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.$bill->duedate.'</span>
                        <span style="position: absolute; left: 510px; width: 80px; padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.number_format($bill->current,2).'</span>
                    </p>
                </div>
                
                <div style="width: 650px; display: inline-block; position: relative; left: 10px;; top: 85px; ">
                    <p style="display: block; position: relative; font-weight: normal; width: 100%; font-size: 9px; margin: 0px 0px padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px;">
                        <span style="position: absolute; left: 5px; width: 80px; padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.$bill->mtrser.'</span>
                        <span style="position: absolute; left: 70px; width: 50px; padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.$bill->serial.'</span>
                        <span style="position: absolute; left: 150px; width: 30px; padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.number_format($bill->load).'</span>
                        <span style="position: absolute; left: 215px; width: 30px; padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.$bill->rate.'</span>
                   </p>
                </div>
                
                <div style="width: 650px; display: inline-block; position: relative; left: 10px;; top: 105px; ">
                    <p style="display: block; position: relative; font-weight: normal; width: 100%; font-size: 9px; margin: 0px 0px padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px;">
                        <span style="position: absolute; left: 5px; width: 400px; padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.$bill->prsrdg.'</span>
                        <span style="position: absolute; left: 70px; width: 40px; padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.$bill->prvrdg.'</span>
                        <span style="position: absolute; left: 103px; width: 30px; padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.$bill->multcd.'</span>
                        <span style="position: absolute; left: 150px; width: 30px; padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.number_format($bill->kwhuse).'</span>
                   </p>
                </div>
                
                <div style="width: 650px; display: inline-block; position: relative; left: 10px; top: 140px; ">
                    <p style="display: block; position: relative; font-weight: normal; width: 100%; font-size: 9px; margin: 0px 0px padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px;">
                        <span style="position: absolute; left: 110px; width: 100px; padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px; text-align: right;">'
            .number_format($current, 2).
            '</span>
                   </p>
                </div>
                <div style="width: 650px; display: inline-block; position: relative; left: 10px;; top: 150px; "> <!-- TOTAL OVERDUE --->
                    <p style="display: block; position: relative; font-weight: normal; width: 100%; font-size: 9px; margin: 0px 0px padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px;">
                        <span style="position: absolute; left: 110px; width: 100px; padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px; text-align: right;">'
            .number_format($bill->overdue, 2).
            '</span>
                   </p>
                </div>
                <div style="width: 650px; display: inline-block; position: relative; left: 10px;; top: 160px; "> <!-- TOTAL INTEREST --->
                    <p style="display: block; position: relative; font-weight: normal; width: 100%; font-size: 9px; margin: 0px 0px padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px;">
                        <span style="position: absolute; left: 110px; width: 100px; padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px; text-align: right;">'
            .number_format($bill->totint, 2).
            '</span>
                   </p>
                </div>
                <div style="width: 650px; display: inline-block; position: relative; left: 10px;; top: 180px; "> <!-- TOTAL ACCOUNT --->
                    <p style="display: block; position: relative; font-weight: normal; width: 100%; font-size: 9px; margin: 0px 0px padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px;">
                        <span style="font-weight: bold; position: absolute; left: 110px; width: 100px; padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px; text-align: right;">'
            .number_format($bill->totacc, 2).
            '</span>
                   </p>
                </div>
                         
                <div style="width: 650px; display: inline-block; position: relative; left: 10px;; top: 185px; "> <!-- DATE OF LAST PAYMENT --->
                    <p style="display: block; position: relative; font-weight: normal; width: 100%; font-size: 9px; margin: 0px 0px padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px;">
                        <span style="font-weight: bold; position: absolute; left: 95px; width: 100px; padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px; text-align: left;">'
            .$bill->dolpay.
            '</span>
                   </p>
                </div>   
                     
                <div style="width: 650px; display: inline-block; position: relative; left: 10px;; top: 440px; "> <!-- BIR PERMIN NUMBER --->
                    <p style="display: block; position: relative; font-weight: normal; width: 100%; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 10px; height: 10px; margin: 0px 0px; padding: 0px 0px;">
                        <span style="font-weight: normal; font-family: courier, monospace; position: absolute; left: 5px; width: 350px; padding: 0px 0px; line-height: 10px; height: 10px; margin: 0px 0px; padding: 0px 0px; text-align: left;">
                            BIR PERMIT NO.: 04-2015-123-0011-000. DATE OF ISSUE: MARCH 25, 2015. <br>
                            SOA SERIES: 00000001-99999999
                        </span>
                   </p>
                </div>
                <!-- BILLING NUMBER -->
                <div style="width: auto; display: inline-block; position: relative; left: 510px;; top: 440px; "> <!-- BIR PERMIN NUMBER --->
                    <p style="display: block; position: relative; font-weight: bold; width: 100%; font-size: 12px; margin: 0px 0px padding: 0px 0px; line-height: 10px; height: 10px; margin: 0px 0px; padding: 0px 0px;">
                        <span style="font-weight: bold; font-family: courier, monospace; position: absolute; left: 5px; padding: 0px 0px; line-height: 10px; height: 10px; margin: 0px 0px; padding: 0px 0px; text-align: left;">
                            '.str_pad($bill->billno,8,"0", STR_PAD_LEFT).'
                        </span>
                   </p>
                </div>
                
                
                <div style="width: 650px; display: inline-block; position: relative; left: 5px;; top: 505px; "> <!-- NAME --->
                    <p style="font-size: 9px;">
                        <span style="font-weight: normal; position: absolute; left: 5px; width: 30px; padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px; text-align: left;">'
            .$bill->servno.
            '</span>
                        <span style="font-weight: normal; position: absolute; left: 80px; width: 20px; padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px; text-align: left;">'
            .$bill->mtr.
            '</span>
                        <span style="font-weight: normal; position: absolute; left: 110px; width: 220px; padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px; text-align: left;">'
            .$bill->name.
            '</span>
                        <span style="font-weight: normal; position: absolute; left: 355px; top: 10px; width: 50px; padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px; text-align: left;">'
            .$moyr.
            '</span>
                        <span style="font-weight: normal; position: absolute; left: 400px; top: 10px; width: 50px; padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px; text-align: left;">'
            .number_format($bill->overdue, 2).
            '</span>
                        <span style="font-weight: normal; position: absolute; left: 450px; top: 10px; width: 50px; padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px; text-align: left;">'
            .number_format($bill->totint, 2).
            '</span>
                        <span style="font-weight: normal; position: absolute; left: 530px; top: 0px; width: 100px; padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px; text-align: right;">'
            .number_format($current, 2).
            '</span>
                        <span style="font-weight: normal; position: absolute; left: 530px; top: 10px; width: 100px; padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px; text-align: right;">'
            .number_format($bill->overdue, 2).
            '</span>
                    </p>
                </div> 
                <div style="width: 650px; display: inline-block; position: relative; left: 10px;; top: 535px; "> <!-- NAME --->
                    <p style="font-size: 9px;">
                        <span style="font-weight: normal; position: absolute; left: 105px; top: 3px; width: 50px; padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px; text-align: left;">'
            .$bill->dolpay.
            '</span>
                        <span style="font-weight: normal; position: absolute; left: 310px; top: 3px; width: 50px; padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px; text-align: left;">'
            .$bill->duedate.
            '</span>
                    
                        <span style="font-weight: normal; position: absolute; left: 450px; top: 0px; width: 50px; padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px; text-align: left;">'
            .number_format($bill->totint, 2).
            '</span>
                        <span style="font-weight: normal; position: absolute; left: 530px; top: 5px; width: 100px; padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px; text-align: right;">'
            .number_format($bill->totacc, 2).
            '</span>
                        <span style="font-weight: normal; position: absolute; left: 530px; top: 25px; width: 100px; padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px; text-align: right;">'
            .number_format(($bill->totacc+$bill->totint), 2).
            '</span>
                    </p>
                </div>
                <div style="width: 650px; display: inline-block; position: relative; left: 5px;; top: 500px; "> <!-- NAME --->
                    <p style="display: block; position: relative; font-weight: normal; width: 100%; font-size: 9px; margin: 0px 0px padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px;">
                        <span style="font-weight: normal; position: absolute; left: 110px; width: 220px; padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px; text-align: left;">'
            .$bill->addr.
            '</span>
                    </p>
                </div> 
                
                <div style="width: 650px; display: inline-block; position: relative; left: 5px;; top: 565px; "> <!-- BIR PERMIN NUMBER --->
                    <p style="display: block; position: relative; font-weight: normal; width: 100%; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 10px; height: 10px; margin: 0px 0px; padding: 0px 0px;">
                        <span style="font-weight: normal; font-family: courier, monospace; position: absolute; left: 5px; width: 350px; padding: 0px 0px; line-height: 10px; height: 10px; margin: 0px 0px; padding: 0px 0px; text-align: left;">
                            BIR PERMIT NO.: 04-2015-123-0011-000. DATE OF ISSUE: MARCH 25, 2015. <br>
                            SOA SERIES: 00000001-99999999
                        </span>
                   </p>
                </div>
                <!-- BILLING NUMBER -->
                <div style="width: auto; display: inline-block; position: relative; left: 510px;; top: 565px; "> <!-- BIR PERMIN NUMBER --->
                    <p style="display: block; position: relative; font-weight: bold; width: 100%; font-size: 12px; margin: 0px 0px padding: 0px 0px; line-height: 10px; height: 10px; margin: 0px 0px; padding: 0px 0px;">
                        <span style="font-weight: bold; font-family: courier, monospace; position: absolute; left: 5px; padding: 0px 0px; line-height: 10px; height: 10px; margin: 0px 0px; padding: 0px 0px; text-align: left;">
                            '.str_pad($bill->billno,8,"0", STR_PAD_LEFT).'
                        </span>
                   </p>
                </div>
                
                <div style="width: 650px; display: inline-block; position: relative; left: 0px;; top: 635px; "> <!-- NAME --->
                    <p style="font-size: 9px;">
                        <span style="font-weight: normal; position: absolute; left: 0px; top: 10px; width: 50px; padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px; text-align: left;">'
            .$gdlb.
            '</span>
                        <span style="font-weight: normal; position: absolute; left: 55px; top: 10px; width: 50px; padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px; text-align: left;">'
            .$bill->servno.
            '</span>
                         <span style="font-weight: normal; position: absolute; left: 115px; top: 10px; width: 20px; padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px; text-align: left;">'
            .$bill->mtr.
            '</span>
                        <span style="font-weight: normal; position: absolute; left: 135px; top: 10px; width: 220px; padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px; text-align: left;">'
            .$bill->name.
            '</span>
                        <span style="font-weight: normal; position: absolute; left: 135px; top: 25px; width: 220px; padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px; text-align: left;">'
            .$bill->addr.
            '</span>
                        <span style="font-weight: normal; position: absolute; left: 300px; top: 10px; width: 70px; padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px; text-align: left;">'
            .$bill->mtrser.
            '</span>
                        <span style="font-weight: normal; position: absolute; left: 340px; top: 10px; width: 70px; padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px; text-align: left;">'
            .$bill->serial.
            '</span>
                        <span style="font-weight: normal; position: absolute; left: 400px; top: 10px; width: 50px; padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px; text-align: left;">'
            .$moyr.
            '</span>
                        
                        
                        <span style="font-weight: normal; position: absolute; left: 540px; top: 0px; width: 100px; padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px; text-align: right;">'
            .number_format($current, 2).
            '</span>
                        <span style="font-weight: normal; position: absolute; left: 540px; top: 10px; width: 100px; padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px; text-align: right;">'
            .number_format($bill->overdue, 2).
            '</span>
                        <span style="font-weight: normal; position: absolute; left: 540px; top: 20px; width: 100px; padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px; text-align: right;">'
            .number_format($bill->totint, 2).
            '</span>
                        <span style="font-weight: bold; position: absolute; left: 540px; top: 55px; width: 100px; padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px; text-align: right;">'
            .number_format(($bill->totacc + $bill->totint) , 2).
            '</span>
                        
                        
                        <span style="font-weight: normal; position: absolute; left: 105px; top: 50px; width: 50px; padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px; text-align: left;">'
            .$bill->dolpay.
            '</span>
                         <span style="font-weight: normal; position: absolute; left: 310px; top: 50px; width: 50px; padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px; text-align: left;">'
            .$bill->duedate.
            '</span>
                    </p>
                </div> 
                
                <!-- BILLING NUMBER -->
                <div style="width: auto; display: inline-block; position: relative; left: 510px;; top: 720px; ">
                    <p style="display: block; position: relative; font-weight: bold; width: 100%; font-size: 12px; margin: 0px 0px padding: 0px 0px; line-height: 10px; height: 10px; margin: 0px 0px; padding: 0px 0px;">
                        <span style="font-weight: bold; font-family: courier, monospace; position: absolute; left: 5px; padding: 0px 0px; line-height: 10px; height: 10px; margin: 0px 0px; padding: 0px 0px; text-align: left;">
                            '.str_pad($bill->billno,8,"0", STR_PAD_LEFT).'
                        </span>
                   </p>
                </div>
                
                
                
                
                <div style="position: absolute; left: 270px; width: 400px; top: 130px;">
                    <!-- #################################################################### -->
                    <!-- PECO RELATED CHARGES --->
                    
                    <p style="font-weight: bold; font-size: 10px; line-height: 10px; padding: 0px 0px; margin: 0px 0px;">PECO RELEATED CHARGES 
                    <span class="charges-header-rate" style="position: absolute; left: 190px; width: 80px; text-align: right">PER KWH</span> 
                    <span class="charges-header-amt" style="position: absolute; left: 260px; width: 100px; text-align: right">AMOUNT</span> </p>
                    <p style="font-weight: normal; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">
                        Distribution Charge 
                        <span class="charges-header-rate" style="position: absolute; left: 190px; width: 80px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.$dischg.'</span>
                        <span class="charges-header-amt" style="position: absolute; left: 260px; width: 100px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.number_format($disamt,2).'</span>
                     </p>
                     
                     <p style="font-weight: normal; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">
                        Demand Charge 
                        <span class="charges-header-rate" style="position: absolute; left: 190px; width: 80px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;"></span>
                        <span class="charges-header-amt" style="position: absolute; left: 260px; width: 100px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.number_format($demamt,2).'</span>
                     </p>
                     <p style="font-weight: normal; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">
                        Supply Charge 
                        <span class="charges-header-rate" style="position: absolute; left: 190px; width: 80px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.$supchg.'</span>
                        <span class="charges-header-amt" style="position: absolute; left: 260px; width: 100px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.number_format($supamt,2).'</span>
                     </p>
                     <p style="font-weight: normal; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">
                        Metering Charge 
                        <span class="charges-header-rate" style="position: absolute; left: 190px; width: 80px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.$mtrchg.'</span>
                        <span class="charges-header-amt" style="position: absolute; left: 260px; width: 100px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.number_format($mtramt,2).'</span>
                     </p>
                     <p style="font-weight: normal; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">
                        Retail Custom Charge 
                        <span class="charges-header-rate" style="position: absolute; left: 190px; width: 80px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;"></span>
                        <span class="charges-header-amt" style="position: absolute; left: 260px; width: 100px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">5.00</span>
                     </p>
                     
                     <p style="font-weight: normal; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">
                        <span class="charges-header-rate" style="position: absolute; left: 190px; width: 80px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">Sub Total</span>
                        <span class="charges-header-amt" style="position: absolute; left: 260px; width: 100px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.number_format($total_peco_charges, 2).'</span>
                        <span class="charges-header-amt" style="position: absolute; left: 295px;; width: 100px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.number_format($total_peco_charges_percent, 2).'%</span>
                    </p>
                     
                     
                     <!-- #################################################################### -->
                     <!-- SUPLIER RELATED CHARGES --->
                    <p style="font-weight: bold; font-size: 10px; line-height: 10px; padding: 0px 0px; margin: 0px 0px;">SUPLIER RELATED CHARGES (PPC, PEDC)</p>
                    <p style="font-weight: normal; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">
                        Generation Charge 
                        <span class="charges-header-rate" style="position: absolute; left: 190px; width: 80px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.$genchg.'</span>
                        <span class="charges-header-amt" style="position: absolute; left: 260px; width: 100px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.number_format($genamt, 2).'</span>
                     </p>
                     <p style="font-weight: normal; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">
                        Previous Months Adjustment on Generation Cost 
                        <span class="charges-header-rate" style="position: absolute; left: 190px; width: 80px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;"></span>
                        <span class="charges-header-amt" style="position: absolute; left: 260px; width: 100px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">0.00</span>
                     </p>
                     <p style="font-weight: normal; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">
                        Previous Years\' Adjustment on Power Cost (ERC Case No. 2001-333) 
                        <span class="charges-header-rate" style="position: absolute; left: 190px; width: 80px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.$papcchg.'</span>
                        <span class="charges-header-amt" style="position: absolute; left: 260px; width: 100px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.number_format($papc, 2).'</span>
                     </p>
                     <p style="font-weight: normal; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">
                        Transmission Charge 
                        <span class="charges-header-rate" style="position: absolute; left: 190px; width: 80px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.$trnchg.'</span>
                        <span class="charges-header-amt" style="position: absolute; left: 260px; width: 100px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.number_format($trnamt, 2).'</span>
                     </p>
                     <p style="font-weight: normal; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">
                        System Loss Charge 
                        <span class="charges-header-rate" style="position: absolute; left: 190px; width: 80px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.$slchg.'</span>
                        <span class="charges-header-amt" style="position: absolute; left: 260px; width: 100px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.number_format($slamt, 2).'</span>
                     </p>
                     
                     <p style="font-weight: normal; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">
                        <span class="charges-header-rate" style="position: absolute; left: 190px; width: 80px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">Sub Total</span>
                        <span class="charges-header-amt" style="position: absolute; left: 260px; width: 100px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.number_format($total_supplier_charges,2).'</span>
                        <span class="charges-header-amt" style="position: absolute; left: 295px;; width: 100px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.number_format($total_supplier_charges_percent,2).'%</span>
                     </p>
                    
                    <!-- #################################################################### -->
                    <!-- SUBSIDIES --->
                    <p style="font-weight: bold; font-size: 10px; line-height: 10px; padding: 0px 0px; margin: 0px 0px;">SUBSIDIES</p>
                    <p style="font-weight: normal; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">
                        Inter-class scross-subsidy 
                        <span class="charges-header-rate" style="position: absolute; left: 190px; width: 80px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;"></span>
                        <span class="charges-header-amt" style="position: absolute; left: 260px; width: 100px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.number_format($iccamt, 2).'</span>
                     </p>
                     <p style="font-weight: normal; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">
                        Lifeline rate subsidy
                        <span class="charges-header-rate" style="position: absolute; left: 190px; width: 80px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;"></span>
                        <span class="charges-header-amt" style="position: absolute; left: 260px; width: 100px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.number_format($llramt, 2).'</span>
                     </p>
                                          
                     <p style="font-weight: normal; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">
                        <span class="charges-header-rate" style="position: absolute; left: 190px; width: 80px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">Sub Total</span>
                        <span class="charges-header-amt" style="position: absolute; left: 260px; width: 100px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.number_format($total_subsidies_charges,2).'</span>
                        <span class="charges-header-amt" style="position: absolute; left: 295px;; width: 100px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.number_format($total_subsidies_charges_percent,2).'%</span>
                    </p>
                     
                     <!-- #################################################################### -->
                    <!-- TAX  --->
                    <p style="font-weight: bold; font-size: 10px; line-height: 10px; padding: 0px 0px; margin: 0px 0px;">TAX AND UNIVERSAL CHARGES</p>
                    <p style="font-weight: normal; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">
                        VAT on Generation
                        <span class="charges-header-rate" style="position: absolute; left: 190px; width: 80px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;"></span>
                        <span class="charges-header-amt" style="position: absolute; left: 260px; width: 100px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.number_format($genvat, 2).'</span>
                     </p>
                     <p style="font-weight: normal; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">
                        VAT on Transmission
                        <span class="charges-header-rate" style="position: absolute; left: 190px; width: 80px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;"></span>
                        <span class="charges-header-amt" style="position: absolute; left: 260px; width: 100px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.number_format($trnvat, 2).'</span>
                     </p>
                     <p style="font-weight: normal; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">
                        VAT on Other Charges
                        <span class="charges-header-rate" style="position: absolute; left: 190px; width: 80px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;"></span>
                        <span class="charges-header-amt" style="position: absolute; left: 260px; width: 100px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.number_format($othvat, 2).'</span>
                     </p>
                     <p style="font-weight: normal; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">
                        Franchise Tax
                        <span class="charges-header-rate" style="position: absolute; left: 190px; width: 80px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;"></span>
                        <span class="charges-header-amt" style="position: absolute; left: 260px; width: 100px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.number_format($framt, 2).'</span>
                     </p>
                     <p style="font-weight: normal; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">
                        Missionary
                        <span class="charges-header-rate" style="position: absolute; left: 190px; width: 80px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.$mischg.'</span>
                        <span class="charges-header-amt" style="position: absolute; left: 260px; width: 100px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.number_format($misamt, 2).'</span>
                     </p>
                     <p style="font-weight: normal; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">
                        Environmental
                        <span class="charges-header-rate" style="position: absolute; left: 190px; width: 80px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.$envchg.'</span>
                        <span class="charges-header-amt" style="position: absolute; left: 260px; width: 100px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.number_format($envamt, 2).'</span>
                     </p>
                     <p style="font-weight: normal; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">
                        NPC Stranded Cntract Cost
                        <span class="charges-header-rate" style="position: absolute; left: 190px; width: 80px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.$npcchg.'</span>
                        <span class="charges-header-amt" style="position: absolute; left: 260px; width: 100px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.number_format($npcamt, 2).'</span>
                     </p>
                     <p style="font-weight: normal; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">
                        ICCS Adjustment
                        <span class="charges-header-rate" style="position: absolute; left: 190px; width: 80px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.$iccschg.'</span>
                        <span class="charges-header-amt" style="position: absolute; left: 260px; width: 100px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.number_format($iccsamt, 2).'</span>
                     </p>
                     <p style="font-weight: normal; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">
                        FIT - Allowance
                        <span class="charges-header-rate" style="position: absolute; left: 190px; width: 80px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.$fitchg.'</span>
                        <span class="charges-header-amt" style="position: absolute; left: 260px; width: 100px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.number_format($fitamt, 2).'</span>
                     </p>
                                          
                     <p style="font-weight: normal; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">
                        <span class="charges-header-rate" style="position: absolute; left: 190px; width: 80px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">Sub Total</span>
                        <span class="charges-header-amt" style="position: absolute; left: 260px; width: 100px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.number_format($total_tax_universal_charges,2).'</span>
                        <span class="charges-header-amt" style="position: absolute; left: 295px;; width: 100px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.number_format($total_tax_universal_charges_percent,2).'%</span>
                     </p>
                     
                     <p style="font-weight: bold; font-size: 10px; margin: 0px 0px padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">
                        <span class="charges-header-amt" style="position: absolute; left: 260px; width: 100px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.number_format($current,2).'</span>
                     </p>
                     
                     <p style="font-weight: normal; font-size: 8px; line-height: 10px; height: 10px; padding: 0px 0px; margin: 0px 0px;">Historical Data</p>
                     <p style="font-weight: bold; font-size: 8px; line-height: 10px; height: 10px; padding: 0px 0px; margin: 0px 0px;">
                        <span class="" style="position: absolute; left: 0px; width: 70px; text-align: left">BILL PERIOD</span> 
                        <span class="" style="position: absolute; left: 80px; width: 80px; text-align: left">BILL MONTH</span> 
                        <span class="" style="position: absolute; left: 130px; width: 65px; text-align: center">PREV. READING</span> 
                        <span class="" style="position: absolute; left: 200px; width: 65px; text-align: center">PRES. READING</span> 
                        <span class="" style="position: absolute; left: 270px; width: 50px; text-align: right">KWH USE</span>
                        <span class="" style="position: absolute; left: 320px; width: 60px; text-align: right">AMOUNT</span>
                     </p>
                     
                     <!-- HISTORICAL DATA HERE ---->
                     '.$history_html.'

                     <p style="font-weight: bold; font-size: 9px; margin-top: 5px; line-height: 10px; height: 10px; padding: 0px 0px; margin: 0px 0px;">
                        <span class="" style="position: absolute; left: 150px; width: 150px; text-align: right; padding: 0px 0px; margin: 0px 0px;">NEXT METER REAING DATE: </span> 
                        <span class="" style="position: absolute; left: 300px; width: 80px; text-align: right; padding: 0px 0px; margin: 0px 0px;"> '.$next_billing.'</span>
                     </p>
                     
                    
                    <h3 style="font-size: 9px; font-weight: bold; ">THIS IS A SYSTEM GENERATED STATEMENT OF ACCOUNT. NO SIGNATURE IS REQUIRED.</H3>
                </div>
                <footer></footer>
            </div>
            </div>
        ';
        return $html;
    }





    function bill_form($bill, $readhist = false, $page = 1) {
        // GOOGLE CHROME MINIMUM MARGIN
        $history_html = '';
        $history_html .= '<p style="font-weight: bold; font-size: 10px; margin: 0px 0px padding: 0px 0px; line-height: 10px; height: 12px; margin: 0px 0px; padding: 0px 0px; margin-top: 5px;" class="charges-list-item">Reading History</p>';
        if($readhist) {

            foreach($readhist as $hrow) {
                $prsdte = date_formating($hrow->prsdte, 'Y-m-d', 'm/d/Y');
                $prvdte = date_formating($hrow->prvdte, 'Y-m-d', 'm/d/Y');
                $blmonthname = date_formating($hrow->bmo, 'm', 'M');
                $blyearname = date_formating($hrow->byr, 'y', 'Y');


                $history_html .= '
                    <p style="font-weight: bold; font-size: 7px; margin: 0px 0px padding: 0px 0px; line-height: 6px; height: 6px; margin: 0px 0px; padding: 0px 0px; margin-top: 3px;" class="charges-list-item">
                    <span class="" style="position: absolute; left: 0px; width: 100px; text-align: left; padding: 0px 0px; margin: 0px 0px;">'.$prvdte.'-'.$prsdte.'</span> 
                    <span class="" style="position: absolute; left: 100px; width: 80px; text-align: left; padding: 0px 0px; margin: 0px 0px;">'.$blmonthname.' - '.$blyearname.'</span> 
                    <span class="" style="position: absolute; left: 130px; width: 55px; text-align: right; padding: 0px 0px; margin: 0px 0px;">'.$hrow->prvrdg.'</span> 
                    <span class="" style="position: absolute; left: 200px; width: 55px; text-align: right; padding: 0px 0px; margin: 0px 0px;">'.$hrow->prsrdg.'</span>0 
                    <span class="" style="position: absolute; left: 260px; width: 55px; text-align: right; padding: 0px 0px; margin: 0px 0px;">'.number_format($hrow->kwhuse). '</span>
                    <span class="" style="position: absolute; left: 320px; width: 60px; text-align: right; padding: 0px 0px; margin: 0px 0px;">'.number_format($hrow->current,2). '</span>
                    </p>
                ';
            }
        }

        $gdlb = $bill->group.$bill->dist.' '.str_pad($bill->lot,2,"0",STR_PAD_LEFT).'-'.$bill->book;
        $moyr = $bill->year . '-' . $bill->month;


        $current = $bill->current;
        $gdlb = $bill->group.'-'.$bill->dist.'-'.$bill->lot.'-'.$bill->book;
        // ##############################################
        // PECO RELATED CHARGES
        // AMT
        $disamt = round($bill->disamt, 2);
        $demamt = round($bill->demamt, 2);
        $supamt = round($bill->supamt, 2);
        $supper = round($bill->supper, 2);
        $mtramt = round($bill->mtramt, 2);
        // CHARGES
        $dischg = $bill->dischg;
        $demchg = 0;
        $supchg = $bill->supchg;
        $mtrchg = $bill->mtrchg;
        $mtrper = $bill->mtrper;
        // @TODO SOLVE 5 PESOS
        if($mtrper>0) {
            $mtrcharge = $mtrper;
        }else {
            $mtrcharge = 5;
        }

        $total_peco_charges = round(($disamt + $demamt + $supamt + $supper + $mtramt + $mtrcharge), 2);
        if($total_peco_charges != 0) {
            if($current > 0) {
                if($total_peco_charges > $current) {
                    $total_peco_charges_percent = ($total_peco_charges / $current) * 100;
                }else {
                    $total_peco_charges_percent = ($total_peco_charges / $current) * 100;
                }
            }else{
                $total_peco_charges_percent = 0;
            }
        }else{
            $total_peco_charges_percent = 0;
        }
        // ##############################################
        // SUPPLIER RELATED CHARGES (PPC, DEPC)
        // AMT
        $genamt = round($bill->genamt, 2);
        $genamt1 = round($bill->genamt1, 2);
        $trnamt = round($bill->trnamt, 2);
        $slamt = round($bill->slamt, 2);
        $papc = round($bill->papc, 2);
        // CHARGES
        $genchg = $bill->genchg;
        $genchg1 = $bill->genchg1;
        $trnchg = $bill->trnchg;
        $slchg = $bill->slchg;
        $papcchg = $bill->papcchg;
        $total_supplier_charges = round(($genamt + $genamt1 + $trnamt + $slamt + $papc), 2);
        if($total_supplier_charges != 0) {
            if($current > 0) {
                if($total_supplier_charges > $current){
                    $total_supplier_charges_percent = ($total_supplier_charges / $current) * 100;
                }else {
                    $total_supplier_charges_percent = ($total_supplier_charges / $current) * 100;
                }
            }else{
                $total_supplier_charges_percent = 0;
            }
        }else{
            $total_supplier_charges_percent = 0;
        }
        // ##############################################
        // SUBSIDIES
        // AMT
        $iccamt = round($bill->iccamt, 2);
        $iccsub = round($bill->iccsub, 2);
        $iccsamt = round($bill->iccsamt, 2);
        $llramt = round($bill->llramt, 2);
        $llrsub = round($bill->llrsub, 2);
        $lldamt = round($bill->lldamt, 2);
        // CHARGES
        $iccschg = $bill->iccschg;
        $total_subsidies_charges = round(($iccamt + $llramt), 2);
        if($total_subsidies_charges != 0) {
            if( $current > 0) {
                if($total_subsidies_charges>$current) {
                    $total_subsidies_charges_percent = ($total_subsidies_charges / $current) * 100;
                }else {
                    $total_subsidies_charges_percent = ($total_subsidies_charges / $current) * 100;
                }
            }else{
                $total_subsidies_charges_percent = 0;
            }
        }else{
            $total_subsidies_charges_percent = 0;
        }
        // ##############################################
        // TAXES AND UNIVERSAL CHARGES
        // AMT
        $genvat = round($bill->genvat, 2);
        $trnvat = round($bill->trnvat, 2);
        $disvat = round($bill->disvat, 2);
        $slvat = round($bill->slvat, 2);
        $othvat = round(($bill->othvat + $slvat + $disvat), 2);
        $misamt = round($bill->misamt, 2);
        $envamt = round($bill->envamt, 2);
        $framt = round($bill->framt, 2);
        $npcamt = round($bill->npcamt, 2);
        $iccsamt = round($bill->iccsamt, 2);
        $fitamt = round(($bill->fitchg * $bill->kwhuse), 2);

        // CHARGES
        $mischg = $bill->mischg;
        $envchg = $bill->envchg;
        $npcchg = $bill->npcchg;
        $fitchg = $bill->fitchg;
        $total_tax_universal_charges = round(($genvat + $trnvat + $othvat + $misamt + $envamt + $framt + $npcamt + $iccsamt + $fitamt), 2);
        if($total_tax_universal_charges != 0) {
            if($current > 0) {
                if($total_tax_universal_charges > $current) {
                    $total_tax_universal_charges_percent = ($total_tax_universal_charges / $current) * 100;
                }else {
                    $total_tax_universal_charges_percent = ($total_tax_universal_charges / $current) * 100;
                }
            }else{
                $total_tax_universal_charges_percent = 0;
            }
        }else{
            $total_tax_universal_charges_percent = 0;
        }
        // #######################################
        $appsur = $bill->appsur;
        $surbal = $bill->surbal;
        $overdue = $bill->overdue;
        $totacc = $bill->totacc;
        $totint = $bill->totint;
        $scdisc = $bill->scdisc;

        $dt1 = DateTime::createFromFormat('Y-m-d', $bill->prvdte);
        $newdte_prvdte = $dt1->format('m/d/Y');

        $dt2 = DateTime::createFromFormat('Y-m-d', $bill->prsdte);
        $newdte_prsdte = $dt2->format('m/d/Y');

        $billing_period = $newdte_prvdte.'-'.$newdte_prsdte;
        // $billing_period = date_formating($hrow->prvdte, 'Y-m-d', 'm/d/Y'). '-' .date_formating($hrow->prsdte, 'Y-m-d', 'm/d/Y');
        // BILLING FORM
        // $bgimg = base_url('assets/global/img/bill_form_bg.jpg');


        // CURRENT
        $total_charges = round(($total_peco_charges + $total_supplier_charges + $total_subsidies_charges + $total_tax_universal_charges), 2);

        //$bgimg = FCPATH . 'assets/global/img/bill_form_bg.jpg';
        $bgimg = '';
        $html = '';
        $html .= '<html>';
        $html .= '<head>';
        $html .= '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" >';
        $html .= '<title>TVI | Tiedra Ventures, Inc.</title>';
        $html .= '<style> body, html, *, p, span, h1, h2, h3, h4, h5 { font-family: Courier !important; }</style>';
        $html .= '</head>';
        $html .= '<body>';
        if($bgimg!='') {
            $html .= '<img style="z-index: 0;" src="' . $bgimg . '" width="100%" />';
        }
        $html .= '<div class="bill-form" style="z-index: 1; display: inline-block; min-height: 800px;">';
        $html .= '<div class="rep-content" style="width: 650px; display: inline-block; top: 0px; left: 30px; ">';
        $html .= '<p style="font-weight: normal; font-size: 9px; margin: 0px 0px; padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px; margin-top: 20px; margin-left: 40px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 70px;">'.$bill->name.'</span>';
        $html .= '<span style="position: absolute; left: 620px; width: 100px; display: inline-block;">'.$bill->servno.'</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; margin: 0px 0px; padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px; margin-top: 5px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 70px;">'.$bill->addr.'</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px; margin-top: 45px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 10px;">'.$gdlb.'</span>';
        $html .= '<span style="position: absolute; left: 70px;">'.$bill->servno.'</span>';
        $html .= '<span style="position: absolute; left: 160px;">'.$bill->mtr.'</span>';
        $html .= '<span style="position: absolute; left: 190px;">'.$moyr.'</span>';
        $html .= '<span style="position: absolute; left: 260px;">'.$billing_period.'</span>';
        $html .= '<span style="position: absolute; left: 410px;">'.$bill->duedate.'</span>';
        $html .= '<span style="position: absolute; left: 540px;">'.number_format($current,2).'</span>';
        $html .= '</p>';


        $html .= '<p style="font-weight: normal; font-size: 9px; padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px; margin-top: 45px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 10px;">'.$bill->mtrser.'</span>';
        $html .= '<span style="position: absolute; left: 75px;">'.$bill->serial.'</span>';
        $html .= '<span style="position: absolute; left: 150px;">'.number_format($bill->load).'</span>';
        $html .= '<span style="position: absolute; left: 235px;">'.$bill->rate.'</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px; margin-top: 25px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 10px;">'.round($bill->prsrdg).'</span>';
        $html .= '<span style="position: absolute; left: 75px;">'.round($bill->prvrdg).'</span>';
        $html .= '<span style="position: absolute; left: 125px;">'.$bill->multcd.'</span>';
        $html .= '<span style="position: absolute; left: 150px;">'.number_format($bill->kwhuse).'</span>';
        $html .= '</p>';


        // ##### UNPAID BILLS #####################
        $html .= '<p style="font-weight: normal; font-size: 9px; padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px; margin-top: 35px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 170px; text-align: right; display: inline-block; width: 70px;">'.number_format($current, 2).'</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px; margin-top: 15px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 170px; text-align: right; display: inline-block; width: 70px;">'.number_format($bill->overdue, 2).'</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px; margin-top: 15px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 170px; text-align: right; display: inline-block; width: 70px;">'.number_format($bill->totint, 2).'</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px; margin-top: 20px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 170px; text-align: right; display: inline-block; width: 70px;">'.number_format($bill->totacc, 2).'</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px; margin-top: 5px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 100px; text-align: right; display: inline-block; width: 70px;">'.$bill->dolpay.'</span>';
        $html .= '</p>';

        // #### CUSTOM MESSAGE
        $html .= '<p style="font-weight: normal; font-size: 9px; padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px; margin-top: 60px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 30px; text-align: left; display: inline-block; width: 200px;">';
        $html .= 'Hello valued customer! <br>Custom message, custom message, custom message, custom message, custom message, custom message, custom message.';
        $html .= '</span>';
        $html .= '</p>';

        // #### BILLING NUMBER -->
        $html .= '<p style="font-weight: normal; font-size: 8px; padding: 0px 0px; line-height: 12px; height: 10px;margin: 0px 0px; padding: 0px 0px; margin-top: 10px;" class="charges-list-item">';
        $html .= '<span style="font-family: courier, monospace; position: absolute; top: 620px; left: 5px; text-align: left; display: inline-block; width: 600px;">';
        $html .= 'BIR PERMIT NO.: 04-2015-123-0011-000. DATE OF ISSUE: MARCH 25, 2015. <br>';
        $html .= 'SOA SERIES: 00000001-99999999';
        $html .= '</span>';
        $html .= '<span style="font-family: courier, monospace; font-size: 15px; font-weight: bold; position: absolute; top: 630px; left: 550px; text-align: left; display: inline-block; width: 600px;">'.$bill->billno.'</span>';
        $html .= '</p>';

        // #### FIRST STUB #####################
        $html .= '<div style="position: absolute; top: 700px; left: 0px;">';
        $html .= '<p style="font-weight: normal; font-size: 9px; padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px; margin-top: 5px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 20px; text-align: left; display: inline-block; width: 100px;">';
        $html .= $bill->servno;
        $html .= '</span>';
        $html .= '<span style="position: absolute; left: 95px; text-align: left; display: inline-block; width: 70px;">';
        $html .= $bill->mtr;
        $html .= '</span>';
        $html .= '<span style="position: absolute; left: 120px; text-align: left; display: inline-block; width: 200px;">';
        $html .= $bill->name;
        $html .= '</span>';
        $html .= '<span style="position: absolute; left: 120px; margin-top: 12px; text-align: left; display: inline-block;width: 200px;">';
        $html .= $bill->addr;
        $html .= '</span>';
        $html .= '<span style="position: absolute; left: 365px; text-align: left; display: inline-block; width: 70px;">';
        $html .= $moyr;
        $html .= '</span>';
        $html .= '<span style="position: absolute; left: 460px; text-align: right; margin-top: 5px; display: inline-block; width: 70px;">';
        $html .= number_format($bill->totint, 2);
        $html .= '</span>';
        $html .= '<span style="position: absolute; left:580px; text-align: right; margin-top: 5px; display: inline-block; width: 70px;">';
        $html .= number_format($current, 2);
        $html .= '</span>';
        $html .= '<span style="position: absolute; left: 580px; text-align: right; margin-top: 17px; display: inline-block; width: 70px;">';
        $html .= number_format($bill->overdue, 2);
        $html .= '</span>';
        $html .= '<span style="position: absolute; left: 460px; margin-top: 35px; text-align: right; display: inline-block; width: 70px;">';
        $html .= number_format($bill->totint, 2);
        $html .= '</span>';
        $html .= '<span style="position: absolute; left: 580px; margin-top: 40px; text-align: right; display: inline-block; width: 70px;">';
        $html .= number_format(($bill->overdue + $current), 2);
        $html .= '</span>';

        // ### GRAND TOTAL ############
        $html .= '<span style="position: absolute; left: 580px; margin-top: 60px; text-align: right; display: inline-block; width: 70px;">';
        $html .= number_format(($bill->totacc+$bill->totint), 2);
        $html .= '</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px; margin-top: 20px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 130px; text-align: left; margin-top: 0px; display: inline-block; width: 70px;">';
        $html .= $bill->dolpay;
        $html .= '</span>';
        $html .= '<span style="position: absolute; left: 330px; text-align: right; text-align: left; display: inline-block; width: 70px;">';
        $html .= $bill->duedate;
        $html .= '</span>';
        $html .= '</p>';
        $html .= '</div>';

        // #### BILLING NUMBER #############
        $html .= '<p style="font-weight: normal; font-size: 8px; padding: 0px 0px; line-height: 10px; height: 10px;margin: 0px 0px; padding: 0px 0px; margin-top: 10px;" class="charges-list-item">';
        $html .= '<span style="font-family: courier, monospace; position: absolute; top: 790px; left: 5px; text-align: left; display: inline-block; width: 600px;">';
        $html .= 'BIR PERMIT NO.: 04-2015-123-0011-000. DATE OF ISSUE: MARCH 25, 2015. <br>';
        $html .= 'SOA SERIES: 00000001-99999999';
        $html .= '</span>';
        $html .= '<span style="font-family: courier, monospace; font-size: 15px; font-weight: bold; position: absolute; top: 805px; left: 552px; text-align: left; display: inline-block; width: 600px;">'.$bill->billno.'</span>';
        $html .= '</p>';

        // #### SECOND STUB ##############
        $html .= '<div style="position: absolute; top: 870px; left: 5px;">';
        $html .= '<p style="font-weight: normal; font-size: 9px; padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px; margin-top: 2px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 580px; text-align: right; display: inline-block; width: 70px;">';
        $html .= number_format($bill->current, 2);
        $html .= '</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px; margin-top: 2px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 580px; text-align: right; display: inline-block; width: 70px;">';
        $html .= number_format($bill->overdue, 2);
        $html .= '</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px; margin-top: 2px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 580px; text-align: right; display: inline-block; width: 70px;">';
        $html .= number_format(($bill->totint), 2);
        $html .= '</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px; margin-top: 25px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 580px; text-align: right; display: inline-block; width: 70px;">';
        $html .= number_format(($bill->totacc+$bill->totint), 2);
        $html .= '</span>';
        $html .= '</p>';
        $html .= '</div>';
        $html .= '<div style="position: absolute; top: 900px; left: 5px;">';
        $html .= '<p style="font-weight: normal; font-size: 9px; padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px; margin-top: 10px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 130px; text-align: left; margin-top: 10px; display: inline-block; width: 70px;">';
        $html .= $bill->dolpay;
        $html .= '</span>';
        $html .= '<span style="position: absolute; left: 310px; text-align: right; margin-top: 10px; display: inline-block; width: 70px;">';
        $html .= $bill->duedate;
        $html .= '</span>';
        $html .= '</p>';
        $html .= '</div>';

        $html .= '<div style="position: absolute; top: 870px; left: 5px;">';
        $html .= '<p style="font-weight: normal; font-size: 9px; padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px; margin-top: 5px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 5px; text-align: left; display: inline-block; width: 70px;">';
        $html .= trim($gdlb);
        $html .= '</span>';
        $html .= '<span style="position: absolute; left: 62px; text-align: left; display: inline-block; width: 70px;">';
        $html .= $bill->servno;
        $html .= '</span>';
        $html .= '<span style="position: absolute; left: 60px; text-align: right; display: inline-block; width: 70px;">';
        $html .= $bill->mtr;
        $html .= '</span>';
        $html .= '<span style="position: absolute; left: 150px; text-align: left; display: inline-block; width: 200px;">';
        $html .= $bill->name;
        $html .= '</span>';
        $html .= '<span style="position: absolute; left: 267px; text-align: right; display: inline-block; width: 70px;">';
        $html .= $bill->mtrser;
        $html .= '</span>';
        $html .= '<span style="position: absolute; left: 335px; text-align: right; display: inline-block; width: 70px;">';
        $html .= $bill->serial;
        $html .= '</span>';
        $html .= '<span style="position: absolute; left: 390px; text-align: right; display: inline-block; width: 70px;">';
        $html .= $moyr;
        $html .= '</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 9px; padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px; margin-top: 5px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 150px; text-align: left; display: inline-block; width: 200px;">';
        $html .= $bill->addr;
        $html .= '</span>';
        $html .= '</p>';
        $html .= '</div>';


        // #### BILLING NUMBER ##############
        $html .= '<p style="font-weight: normal; font-size: 9px; padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px; margin-top: 10px;" class="charges-list-item">';
        $html .= '<span style="font-family: courier, monospace; font-size: 15px; font-weight: bold; position: absolute; top: 970px; left: 550px; text-align: left; display: inline-block; width: 600px;">';
        $html .= $bill->billno;
        $html .= '</span>';
        $html .= '</p>';

        // #### PAGE NUMBER ###############
        $html .= '<div style="position: absolute; top: 980px; left: 5px;">';
        $html .= '<p style="font-weight: normal; font-size: 9px; padding: 0px 0px; line-height: 14px; height: 12px; margin: 0px 0px; padding: 0px 0px; margin-top: 5px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 550px; text-align: right; display: inline-block; width: 100px;">';
        $html .= 'Page: '.str_pad(1,9,"0",STR_PAD_LEFT);
        $html .= '</span>';
        $html .= '</p>';
        $html .= '</div>';

        // #####################################################################
        // ############ START CHARGES TABLE ####################################
        $html .= '<div style="position: absolute; left: 265px; width: 430px; top: 130px;">';
        // ############ PECO RELATED CHARGES ###############
        $html .= '<p style="font-weight: bold; font-size: 10px; line-height: 10px; padding: 0px 0px; margin: 0px 0px; margin-top: 10px;">PECO RELEATED CHARGES';
        $html .= '<span class="charges-header-rate" style="position: absolute; left: 190px; width: 80px; text-align: right">PER KWH</span>';
        $html .= '<span class="charges-header-amt" style="position: absolute; left: 260px; width: 100px; text-align: right">AMOUNT</span> </p>';
        $html .= '<p style="font-weight: normal; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 10px; height: 10px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
        $html .= 'Distribution Charge';
        $html .= '<span style="position: absolute; left: 200px; width: 70px; display: inline-block; text-align: right;">'.$dischg.'</span>';
        $html .= '<span style="position: absolute; left: 290px; width: 70px; display: inline-block; text-align: right;">'.number_format($disamt,2).'</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 10px; height: 10px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
        $html .= 'Demand Charge ';
        $html .= '<span style="position: absolute; left: 200px; width: 70px; display: inline-block; text-align: right;"></span>';
        $html .= '<span style="position: absolute; left: 290px; width: 70px; display: inline-block; text-align: right;">'.number_format($demamt,2).'</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 10px; height: 10px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
        $html .= 'Supply Charge';
        $html .= '<span style="position: absolute; left: 200px; width: 70px; display: inline-block; text-align: right;">'.$supchg.'</span>';
        $html .= '<span style="position: absolute; left: 290px; width: 70px; display: inline-block; text-align: right;">'.number_format($supamt,2).'</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 10px; height: 10px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
        $html .= 'Metering Charge';
        $html .= '<span style="position: absolute; left: 200px; width: 70px; display: inline-block; text-align: right;">'.$mtrchg.'</span>';
        $html .= '<span style="position: absolute; left: 290px; width: 70px; display: inline-block; text-align: right;">'.number_format($mtramt,2).'</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 10px; height: 10px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
        $html .= 'Retail Custom Charge';
        $html .= '<span style="position: absolute; left: 200px; width: 70px; display: inline-block; text-align: right;"></span>';
        $html .= '<span style="position: absolute; left: 290px; width: 70px; display: inline-block; text-align: right;">'.number_format($mtrcharge, 2).'</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 10px; height: 10px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 200px; width: 70px; display: inline-block; text-align: right; font-weight: bold;">Sub Total</span>';
        $html .= '<span style="position: absolute; left: 290px; width: 70px; display: inline-block; text-align: right; font-weight: bold;">'.number_format($total_peco_charges, 2).'</span>';
        $html .= '<span style="position: absolute; left: 330px; width: 70px; display: inline-block; text-align: right;">'.number_format($total_peco_charges_percent, 2).'%</span>';
        $html .= '</p>';

        // ########## SUPLIER RELATED CHARGES ###############
        $html .= '<p style="font-weight: bold; font-size: 10px; line-height: 10px; padding: 0px 0px; margin: 0px 0px; margin-top: 10px;">SUPLIER RELATED CHARGES (PPC, PEDC)</p>';
        $html .= '<p style="font-weight: normal; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 10px; height: 10px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
        $html .= 'Generation Charge ';
        $html .= '<span style="position: absolute; left: 200px; width: 70px; display: inline-block; text-align: right;">'.$genchg.'</span>';
        $html .= '<span style="position: absolute; left: 290px; width: 70px; display: inline-block; text-align: right;">'.number_format($genamt, 2).'</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 10px; height: 10px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
        $html .= 'Previous Months Adjustment on Generation Cost ';
        $html .= '<span style="position: absolute; left: 200px; width: 70px; display: inline-block; text-align: right;"></span>';
        $html .= '<span style="position: absolute; left: 290px; width: 70px; display: inline-block; text-align: right;">0.00</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 10px; height: 10px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
        $html .= 'Previous Years\' Adjustment on Power Cost';
        $html .= '<span style="position: absolute; left: 200px; width: 70px; display: inline-block; text-align: right;">'.$papcchg.'</span>';
        $html .= '<span style="position: absolute; left: 290px; width: 70px; display: inline-block; text-align: right;">'.number_format($papc, 2).'</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 10px; height: 10px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
        $html .= '(ERC Case No. 2001-333) ';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 10px; height: 10px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
        $html .= 'Transmission Charge';
        $html .= '<span style="position: absolute; left: 200px; width: 70px; display: inline-block; text-align: right;">'.$trnchg.'</span>';
        $html .= '<span style="position: absolute; left: 290px; width: 70px; display: inline-block; text-align: right;">'.number_format($trnamt, 2).'</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 10px; height: 10px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
        $html .= 'System Loss Charge';
        $html .= '<span style="position: absolute; left: 200px; width: 70px; display: inline-block; text-align: right;">'.$slchg.'</span>';
        $html .= '<span style="position: absolute; left: 290px; width: 70px; display: inline-block; text-align: right;">'.number_format($slamt, 2).'</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 10px; height: 10px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 200px; width: 70px; display: inline-block; text-align: right; font-weight: bold;">Sub Total</span>';
        $html .= '<span style="position: absolute; left: 290px; width: 70px; display: inline-block; text-align: right; font-weight: bold;">'.number_format($total_supplier_charges,2).'</span>';
        $html .= '<span style="position: absolute; left: 330px; width: 70px; display: inline-block; text-align: right;">'.number_format($total_supplier_charges_percent,2).'%</span>';
        $html .= '</p>';

        $html .= '<p style="font-weight: bold; font-size: 10px; line-height: 10px; padding: 0px 0px; margin: 0px 0px; margin-top: 10px;">SUBSIDIES</p>';
        $html .= '<p style="font-weight: normal; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 10px; height: 10px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
        $html .= 'Inter-class scross-subsidy ';
        $html .= '<span style="position: absolute; left: 200px; width: 70px; display: inline-block; text-align: right;"></span>';
        $html .= '<span style="position: absolute; left: 290px; width: 70px; display: inline-block; text-align: right;">'.number_format($iccamt, 2).'</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 10px; height: 10px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
        $html .= 'Lifeline rate subsidy';
        $html .= '<span style="position: absolute; left: 200px; width: 70px; display: inline-block; text-align: right;">'.number_format($llrsub, 4).'</span>';
        $html .= '<span style="position: absolute; left: 290px; width: 70px; display: inline-block; text-align: right;">'.number_format($llramt, 2).'</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 10px; height: 10px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 200px; width: 70px; display: inline-block; text-align: right; font-weight: bold;">Sub Total</span>';
        $html .= '<span style="position: absolute; left: 290px; width: 70px; display: inline-block; text-align: right; font-weight: bold;">'.number_format($total_subsidies_charges,2).'</span>';
        $html .= '<span style="position: absolute; left: 330px; width: 70px; display: inline-block; text-align: right;">'.number_format($total_subsidies_charges_percent,2).'%</span>';
        $html .= '</p>';

        // ########## TAX  ##############################
        $html .= '<p style="font-weight: bold; font-size: 10px; line-height: 10px; padding: 0px 0px; margin: 0px 0px; margin-top: 10px;">TAX AND UNIVERSAL CHARGES</p>';
        $html .= '<p style="font-weight: normal; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 10px; height: 10px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
        $html .= 'VAT on Generation';
        $html .= '<span style="position: absolute; left: 200px; width: 70px; display: inline-block; text-align: right;"></span>';
        $html .= '<span style="position: absolute; left: 290px; width: 70px; display: inline-block; text-align: right;">'.number_format($genvat, 2).'</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 10px; height: 10px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
        $html .= 'VAT on Transmission';
        $html .= '<span style="position: absolute; left: 200px; width: 70px; display: inline-block; text-align: right;"></span>';
        $html .= '<span style="position: absolute; left: 290px; width: 70px; display: inline-block; text-align: right;">'.number_format($trnvat, 2).'</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 10px; height: 10px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
        $html .= 'VAT on Other Charges';
        $html .= '<span style="position: absolute; left: 200px; width: 70px; display: inline-block; text-align: right;"></span>';
        $html .= '<span style="position: absolute; left: 290px; width: 70px; display: inline-block; text-align: right;">'.number_format($othvat, 2).'</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 10px; height: 10px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
        $html .= 'Franchise Tax';
        $html .= '<span style="position: absolute; left: 200px; width: 70px; display: inline-block; text-align: right;"></span>';
        $html .= '<span style="position: absolute; left: 290px; width: 70px; display: inline-block; text-align: right;">'.number_format($framt, 2).'</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 10px; height: 10px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
        $html .= 'Missionary';
        $html .= '<span style="position: absolute; left: 200px; width: 70px; display: inline-block; text-align: right;">'.$mischg.'</span>';
        $html .= '<span style="position: absolute; left: 290px; width: 70px; display: inline-block; text-align: right;">'.number_format($misamt, 2).'</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 10px; height: 10px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
        $html .= 'Environmental';
        $html .= '<span style="position: absolute; left: 200px; width: 70px; display: inline-block; text-align: right;">'.$envchg.'</span>';
        $html .= '<span style="position: absolute; left: 290px; width: 70px; display: inline-block; text-align: right;">'.number_format($envamt, 2).'</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 10px; height: 10px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
        $html .= 'NPC Stranded Cntract Cost';
        $html .= '<span style="position: absolute; left: 200px; width: 70px; display: inline-block; text-align: right;">'.$npcchg.'</span>';
        $html .= '<span style="position: absolute; left: 290px; width: 70px; display: inline-block; text-align: right;">'.number_format($npcamt, 2).'</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 10px; height: 10px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
        $html .= 'ICCS Adjustment';
        $html .= '<span style="position: absolute; left: 200px; width: 70px; display: inline-block; text-align: right;">'.$iccschg.'</span>';
        $html .= '<span style="position: absolute; left: 290px; width: 70px; display: inline-block; text-align: right;">'.number_format($iccsamt, 2).'</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 10px; height: 10px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
        $html .= 'FIT - Allowance';
        $html .= '<span style="position: absolute; left: 200px; width: 70px; display: inline-block; text-align: right;">'.$fitchg.'</span>';
        $html .= '<span style="position: absolute; left: 290px; width: 70px; display: inline-block; text-align: right;">'.number_format($fitamt, 2).'</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: normal; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 10px; height: 10px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 200px; width: 70px; display: inline-block; text-align: right; font-weight: bold;">Sub Total</span>';
        $html .= '<span style="position: absolute; left: 290px; width: 70px; display: inline-block; text-align: right; font-weight: bold;">'.number_format($total_tax_universal_charges,2).'</span>';
        $html .= '<span style="position: absolute; left: 330px; width: 70px; display: inline-block; text-align: right;">'.number_format($total_tax_universal_charges_percent,2).'%</span>';
        $html .= '</p>';
        $html .= '<p style="font-weight: bold; font-size: 10px; margin: 0px 0px padding: 0px 0px; line-height: 10px; height: 10px; margin: 0px 0px; padding: 0px 0px; margin-top: 5px;" class="charges-list-item">';
        $html .= '<span style="position: absolute; left: 290px; width: 70px; display: inline-block; text-align: right;">'.number_format($current,2).'</span>';
        $html .= '</p>';

        $html .= $history_html;

        $html .= '<h3 style="font-size: 9px; font-weight: bold; margin-top: 10px;">THIS IS A SYSTEM GENERATED STATEMENT OF ACCOUNT. NO SIGNATURE IS REQUIRED.</H3>';
        $html .= '</div>';
        // ##### END OF CHARGES TABLE ################################
        $html .= '<footer></footer>';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '</body>';
        $html .= '</html>';
        return $html;
    }



    function billing_form_kyocera($bill, $history, $page) {
        // ######################################################################
        // ################## GOOGLE CHROME MINIMUM MARGIN ######################
        $gdlb = $bill->group.$bill->dist.' '.str_pad($bill->lot,2,"0",STR_PAD_LEFT).'-'.$bill->book;
        $moyr = str_pad($bill->bmo,2,"0",STR_PAD_LEFT).'-'.str_pad($bill->byr,2,"0",STR_PAD_LEFT);
        $history_html = '';
        $next_billing = '';
        if($history) {
            $i = 0;
            $len = count($history);
            foreach($history as $hrow) {
                $prsdte = date_formating($hrow->prsdte, 'Y-m-d', 'm/d/Y');
                $prvdte = date_formating($hrow->prvdte, 'Y-m-d', 'm/d/Y');
                $blmonthname = date_formating($hrow->bmo, 'm', 'M');
                $blyearname = date_formating($hrow->byr, 'y', 'Y');

                $history_html .= '
                    <p style="font-weight: normal; font-size: 7px; line-height: 10px; height: 10px; padding: 0px 0px; margin: 0px 0px; position: relative">
                        <span class="" style="position: absolute; left: 0px; width: 80px; text-align: left; padding: 0px 0px; margin: 0px 0px;">'.$prvdte.'-'.$prsdte.'</span> 
                        <span class="" style="position: absolute; left: 80px; width: 80px; text-align: left; padding: 0px 0px; margin: 0px 0px;">'.$blmonthname.' - '.$blyearname.'</span> 
                        <span class="" style="position: absolute; left: 130px; width: 55px; text-align: right; padding: 0px 0px; margin: 0px 0px;">'.$hrow->prvrdg.'</span> 
                        <span class="" style="position: absolute; left: 200px; width: 55px; text-align: right; padding: 0px 0px; margin: 0px 0px;">'.$hrow->prsrdg.'</span>0 
                        <span class="" style="position: absolute; left: 260px; width: 55px; text-align: right; padding: 0px 0px; margin: 0px 0px;">'.number_format($hrow->kwhuse). '</span>
                        <span class="" style="position: absolute; left: 320px; width: 60px; text-align: right; padding: 0px 0px; margin: 0px 0px;">'.number_format($hrow->current,2). '</span>
                     </p>
                ';

                /*
                if ($i == 0) {
                    // first
                    $lastprsrdg = $hrow->prsdte;
                    $next_billing = date('Y-m-d', strtotime("+30 days", strtotime($lastprsrdg)));
                } else if ($i == $len - 1) {
                    // LAST
                    $lastprsrdg = $hrow->prsdte;
                    $next_billing = date('Y-m-d', strtotime("+30 days", strtotime($lastprsrdg)));
                }
                $i++;
                */
            }
        }

        $next_billing = date('Y-m-d', strtotime("+30 days", strtotime($bill->prsdte)));

        $current = $bill->current;
        // ##############################################
        // PECO RELATED CHARGES
        // AMT
        $disamt = round($bill->disamt, 2);
        $demamt = round($bill->demamt, 2);
        $supamt = round($bill->supamt, 2);
        $supper = round($bill->supper, 2);
        $mtramt = round($bill->mtramt, 2);
        // CHARGES
        $dischg = $bill->dischg;
        $demchg = $bill->demchg;
        $supchg = $bill->supchg;
        $mtrchg = $bill->mtrchg;
        $mtrper = $bill->mtrper;
        // @TODO SOLVE 5 PESOS
        $total_peco_charges = round(($disamt + $demamt + $supamt + $supper + $mtramt + 5), 2);
        if($total_peco_charges > 0) {
            if($current > 0) {
                if($total_peco_charges > $current) {
                    $total_peco_charges_percent = 0;
                }else {
                    $total_peco_charges_percent = ($total_peco_charges / $current) * 100;
                }
            }else{
                $total_peco_charges_percent = 0;
            }
        }else{
            $total_peco_charges_percent = 0;
        }
        // ##############################################
        // SUPPLIER RELATED CHARGES (PPC, DEPC)
        // AMT
        $genamt = round($bill->genamt, 2);
        $genamt1 = round($bill->genamt1, 2);
        $trnamt = round($bill->trnamt, 2);
        $slamt = round($bill->slamt, 2);
        $papc = round($bill->papc, 2);
        // CHARGES
        $genchg = $bill->genchg;
        $genchg1 = $bill->genchg1;
        $trnchg = $bill->trnchg;
        $slchg = $bill->slchg;
        $papcchg = $bill->papcchg;
        $total_supplier_charges = round(($genamt + $genamt1 + $trnamt + $slamt + $papc), 2);
        if($total_supplier_charges > 0) {
            if($current > 0) {
                if($total_supplier_charges > $current){
                    $total_supplier_charges_percent = 0;
                }else {
                    $total_supplier_charges_percent = ($total_supplier_charges / $current) * 100;
                }
            }else{
                $total_supplier_charges_percent = 0;
            }
        }else{
            $total_supplier_charges_percent = 0;
        }
        // ##############################################
        // SUBSIDIES
        // AMT
        $iccamt = round($bill->iccamt, 2);
        $iccsub = round($bill->iccsub, 2);
        $iccsamt = round($bill->iccsamt, 2);
        $llramt = round($bill->llramt, 2);
        $llrsub = round($bill->llrsub, 2);
        $lldamt = round($bill->lldamt, 2);
        // CHARGES
        $iccschg = $bill->iccschg;
        $total_subsidies_charges = round(($iccamt + $llramt), 2);
        if($total_subsidies_charges > 0) {
            if( $current > 0) {
                if($total_subsidies_charges>$current) {
                    $total_subsidies_charges_percent = 0;
                }else {
                    $total_subsidies_charges_percent = ($total_subsidies_charges / $current) * 100;
                }
            }else{
                $total_subsidies_charges_percent = 0;
            }
        }else{
            $total_subsidies_charges_percent = 0;
        }
        // ##############################################
        // TAXES AND UNIVERSAL CHARGES
        // AMT
        $genvat = round($bill->genvat, 2);
        $trnvat = round($bill->trnvat, 2);
        $disvat = round($bill->disvat, 2);
        $slvat = round($bill->slvat, 2);
        $othvat = round(($bill->othvat + $slvat + $disvat), 2);
        $misamt = round($bill->misamt, 2);
        $envamt = round($bill->envamt, 2);
        $framt = round($bill->framt, 2);
        $npcamt = round($bill->npcamt, 2);
        $iccsamt = round($bill->iccsamt, 2);
        $fitamt = round($bill->fitamt, 2);
        // CHARGES
        $mischg = $bill->mischg;
        $envchg = $bill->envchg;
        $npcchg = $bill->npcchg;
        $fitchg = $bill->fitchg;
        $total_tax_universal_charges = round(($genvat + $trnvat + $othvat + $misamt + $envamt + $framt + $npcamt + $iccsamt + $fitamt), 2);
        if($total_tax_universal_charges > 0) {
            if($current > 0) {
                if($total_tax_universal_charges > $current) {
                    $total_tax_universal_charges_percent = 0;
                }else {
                    $total_tax_universal_charges_percent = ($total_tax_universal_charges / $current) * 100;
                }
            }else{
                $total_tax_universal_charges_percent = 0;
            }
        }else{
            $total_tax_universal_charges_percent = 0;
        }
        // #######################################
        $appsur = $bill->appsur;
        $surbal = $bill->surbal;
        $overdue = $bill->overdue;
        $totacc = $bill->totacc;
        $totint = $bill->totint;
        $scdisc = $bill->scdisc;
        $billing_period = '';
        // $billing_period = date_formating($hrow->prvdte, 'Y-m-d', 'm/d/Y'). '-' .date_formating($hrow->prsdte, 'Y-m-d', 'm/d/Y');
        // BILLING FORM
        $bgimg = '';


        // CURRENT
        $total_charges = round(($total_peco_charges + $total_supplier_charges + $total_subsidies_charges + $total_tax_universal_charges), 2);


        //$bgimg = base_url().'assets/global/img/bill_form_bg.jpg';
        $html = '
        <div  class="bill-form" style="display: inline-block; min-height: 800px;">
        <div class="rep-content" style="width: 650px; display: inline-block; top: 50px; left: 30px; ">
                <div style="width: 650px; display: inline-block; position: relative; left: 10px;; top: 30px;">
                    <p style="display: block; position: relative; font-weight: normal; width: 100%; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px;">
                        <span style="position: absolute; left: 80px; width: 200px; padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.$bill->name.'</span>
                        <span style="position: absolute; right: 0px; width: 90px; padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px; text-align: right">'.$bill->servno.'</span>
                    </p>
                    
                    <p style="display: block; position: relative; font-weight: normal; width: 100%; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px;">
                        <span style="position: absolute; left: 80px; width: 200px; padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.$bill->addr.'</span>
                    </p>
                </div>
                
                <div style="width: 650px; display: inline-block; position: relative; left: 10px;; top: 70px; ">
                    <p style="display: block; position: relative; font-weight: normal; width: 100%; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px;">
                        <span style="position: absolute; left: 05px; width: 80px; padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.$gdlb.'</span>
                        <span style="position: absolute; left: 75px; width: 50px; padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.$bill->servno.'</span>
                        <span style="position: absolute; left: 140px; width: 30px; padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.$bill->mtr.'</span>
                        <span style="position: absolute; left: 175px; width: 80px; padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.$moyr.'</span>
                        <span style="position: absolute; left: 235px; width: 100px; padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.$billing_period.'</span>
                        <span style="position: absolute; left: 385px; width: 80px; padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.$bill->duedate.'</span>
                        <span style="position: absolute; left: 510px; width: 80px; padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.number_format($current,2).'</span>
                    </p>
                </div>
                
                <div style="width: 650px; display: inline-block; position: relative; left: 10px;; top: 110px; ">
                    <p style="display: block; position: relative; font-weight: normal; width: 100%; font-size: 9px; margin: 0px 0px padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px;">
                        <span style="position: absolute; left: 5px; width: 80px; padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.$bill->mtrser.'</span>
                        <span style="position: absolute; left: 70px; width: 50px; padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.$bill->serial.'</span>
                        <span style="position: absolute; left: 150px; width: 30px; padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.number_format($bill->load).'</span>
                        <span style="position: absolute; left: 215px; width: 30px; padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.$bill->rate.'</span>
                   </p>
                </div>
                
                <div style="width: 650px; display: inline-block; position: relative; left: 10px;; top: 125px; ">
                    <p style="display: block; position: relative; font-weight: normal; width: 100%; font-size: 9px; margin: 0px 0px padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px;">
                        <span style="position: absolute; left: 5px; width: 400px; padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.$bill->prsrdg.'</span>
                        <span style="position: absolute; left: 70px; width: 40px; padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.$bill->prvrdg.'</span>
                        <span style="position: absolute; left: 105px; width: 30px; padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.$bill->multcd.'</span>
                        <span style="position: absolute; left: 150px; width: 30px; padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.number_format($bill->kwhuse).'</span>
                   </p>
                </div>
                
                <div style="width: 650px; display: inline-block; position: relative; left: 10px; top: 155px; ">
                    <p style="display: block; position: relative; font-weight: normal; width: 100%; font-size: 9px; margin: 0px 0px padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px;">
                        <span style="position: absolute; left: 110px; width: 100px; padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px; text-align: right;">'
            .number_format($current, 2).
            '</span>
                   </p>
                </div>
                <div style="width: 650px; display: inline-block; position: relative; left: 10px;; top: 165px; "> <!-- TOTAL OVERDUE --->
                    <p style="display: block; position: relative; font-weight: normal; width: 100%; font-size: 9px; margin: 0px 0px padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px;">
                        <span style="position: absolute; left: 110px; width: 100px; padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px; text-align: right;">'
            .number_format($bill->overdue, 2).
            '</span>
                   </p>
                </div>
                <div style="width: 650px; display: inline-block; position: relative; left: 10px;; top: 175px; "> <!-- TOTAL INTEREST --->
                    <p style="display: block; position: relative; font-weight: normal; width: 100%; font-size: 9px; margin: 0px 0px padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px;">
                        <span style="position: absolute; left: 110px; width: 100px; padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px; text-align: right;">'
            .number_format($bill->totint, 2).
            '</span>
                   </p>
                </div>
                <div style="width: 650px; display: inline-block; position: relative; left: 10px;; top: 195px; "> <!-- TOTAL ACCOUNT --->
                    <p style="display: block; position: relative; font-weight: normal; width: 100%; font-size: 9px; margin: 0px 0px padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px;">
                        <span style="font-weight: bold; position: absolute; left: 110px; width: 100px; padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px; text-align: right;">'
            .number_format($bill->totacc, 2).
            '</span>
                   </p>
                </div>
                         
                <div style="width: 650px; display: inline-block; position: relative; left: 10px;; top: 210px; "> <!-- DATE OF LAST PAYMENT --->
                    <p style="display: block; position: relative; font-weight: normal; width: 100%; font-size: 9px; margin: 0px 0px padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px;">
                        <span style="font-weight: bold; position: absolute; left: 95px; width: 100px; padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px; text-align: left;">'
            .$bill->dolpay.
            '</span>
                   </p>
                </div>   
                     
                <div style="width: 650px; display: inline-block; position: relative; left: 10px;; top: 460px; "> <!-- BIR PERMIN NUMBER --->
                    <p style="display: block; position: relative; font-weight: normal; width: 100%; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 10px; height: 10px; margin: 0px 0px; padding: 0px 0px;">
                        <span style="font-weight: normal; font-family: courier, monospace; position: absolute; left: 5px; width: 350px; padding: 0px 0px; line-height: 10px; height: 10px; margin: 0px 0px; padding: 0px 0px; text-align: left;">
                            BIR PERMIT NO.: 04-2015-123-0011-000. DATE OF ISSUE: MARCH 25, 2015. <br>
                            SOA SERIES: 00000001-99999999
                        </span>
                   </p>
                </div>
                <!-- BILLING NUMBER -->
                <div style="width: auto; display: inline-block; position: relative; left: 510px;; top: 460px; "> <!-- BILL NUMBER --->
                    <p style="display: block; position: relative; font-weight: bold; width: 100%; font-size: 12px; margin: 0px 0px padding: 0px 0px; line-height: 10px; height: 10px; margin: 0px 0px; padding: 0px 0px;">
                        <span style="font-weight: bold; font-family: courier, monospace; position: absolute; left: 5px; padding: 0px 0px; line-height: 10px; height: 10px; margin: 0px 0px; padding: 0px 0px; text-align: left;">
                            '.str_pad($bill->billno,8,"0", STR_PAD_LEFT).'
                        </span>
                   </p>
                </div>
                
                
                <div style="width: 650px; display: inline-block; position: relative; left: 5px;; top: 520px; "> <!-- NAME --->
                    <p style="font-size: 9px;">
                        <span style="font-weight: normal; position: absolute; left: 5px; width: 30px; padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px; text-align: left;">'
            .$bill->servno.
            '</span>
                        <span style="font-weight: normal; position: absolute; left: 80px; width: 20px; padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px; text-align: left;">'
            .$bill->mtr.
            '</span>
                        <span style="font-weight: normal; position: absolute; left: 110px; width: 220px; padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px; text-align: left;">'
            .$bill->name.
            '</span>
                        <span style="font-weight: normal; position: absolute; left: 355px; top: 10px; width: 50px; padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px; text-align: left;">'
            .$moyr.
            '</span>
                        <span style="font-weight: normal; position: absolute; left: 410px; top: 10px; width: 50px; padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px; text-align: left;">'
            .number_format($bill->overdue, 2).
            '</span>
                        <span style="font-weight: normal; position: absolute; left: 460px; top: 10px; width: 50px; padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px; text-align: left;">'
            .number_format($bill->totint, 2).
            '</span>
                        <span style="font-weight: normal; position: absolute; left: 530px; top: 0px; width: 100px; padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px; text-align: right;">'
            .number_format($current, 2).
            '</span>
                        <span style="font-weight: normal; position: absolute; left: 530px; top: 10px; width: 100px; padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px; text-align: right;">'
            .number_format($bill->overdue, 2).
            '</span>
                    </p>
                </div> 
                <div style="width: 650px; display: inline-block; position: relative; left: 10px;; top: 550px; "> <!-- NAME --->
                    <p style="font-size: 9px;">
                        <span style="font-weight: normal; position: absolute; left: 105px; top: 3px; width: 50px; padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px; text-align: left;">'
            .$bill->dolpay.
            '</span>
                        <span style="font-weight: normal; position: absolute; left: 310px; top: 3px; width: 50px; padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px; text-align: left;">'
            .$bill->duedate.
            '</span>
                    
                        <span style="font-weight: normal; position: absolute; left: 450px; top: 0px; width: 50px; padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px; text-align: left;">'
            .number_format($bill->totint, 2).
            '</span>
                        <span style="font-weight: normal; position: absolute; left: 530px; top: 5px; width: 100px; padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px; text-align: right;">'
            .number_format($bill->totacc, 2).
            '</span>
                        <span style="font-weight: normal; position: absolute; left: 530px; top: 25px; width: 100px; padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px; text-align: right;">'
            .number_format(($bill->totacc+$bill->totint), 2).
            '</span>
                    </p>
                </div>
                <div style="width: 650px; display: inline-block; position: relative; left: 5px;; top: 510px; "> <!-- NAME --->
                    <p style="display: block; position: relative; font-weight: normal; width: 100%; font-size: 9px; margin: 0px 0px padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px;">
                        <span style="font-weight: normal; position: absolute; left: 110px; width: 220px; padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px; text-align: left;">'
            .$bill->addr.
            '</span>
                    </p>
                </div> 
                
                <div style="width: 650px; display: inline-block; position: relative; left: 5px;; top: 575px; "> <!-- BIR PERMIN NUMBER --->
                    <p style="display: block; position: relative; font-weight: normal; width: 100%; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 10px; height: 10px; margin: 0px 0px; padding: 0px 0px;">
                        <span style="font-weight: normal; font-family: courier, monospace; position: absolute; left: 5px; width: 350px; padding: 0px 0px; line-height: 10px; height: 10px; margin: 0px 0px; padding: 0px 0px; text-align: left;">
                            BIR PERMIT NO.: 04-2015-123-0011-000. DATE OF ISSUE: MARCH 25, 2015. <br>
                            SOA SERIES: 00000001-99999999
                        </span>
                   </p>
                </div>
                <!-- BILLING NUMBER -->
                <div style="width: auto; display: inline-block; position: relative; left: 510px;; top: 570px; "> <!-- BIR PERMIN NUMBER --->
                    <p style="display: block; position: relative; font-weight: bold; width: 100%; font-size: 12px; margin: 0px 0px padding: 0px 0px; line-height: 10px; height: 10px; margin: 0px 0px; padding: 0px 0px;">
                        <span style="font-weight: bold; font-family: courier, monospace; position: absolute; left: 5px; padding: 0px 0px; line-height: 10px; height: 10px; margin: 0px 0px; padding: 0px 0px; text-align: left;">
                            '.str_pad($bill->billno,8,"0", STR_PAD_LEFT).'
                        </span>
                   </p>
                </div>
                
                <div style="width: 650px; display: inline-block; position: relative; left: 5px;; top: 635px; "> <!-- NAME --->
                    <p style="font-size: 9px;">
                        <span style="font-weight: normal; position: absolute; left: 0px; top: 10px; width: 50px; padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px; text-align: left;">'
            .$gdlb.
            '</span>
                        <span style="font-weight: normal; position: absolute; left: 55px; top: 10px; width: 50px; padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px; text-align: left;">'
            .$bill->servno.
            '</span>
                         <span style="font-weight: normal; position: absolute; left: 115px; top: 10px; width: 20px; padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px; text-align: left;">'
            .$bill->mtr.
            '</span>
                        <span style="font-weight: normal; position: absolute; left: 135px; top: 10px; width: 220px; padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px; text-align: left;">'
            .$bill->name.
            '</span>
                        <span style="font-weight: normal; position: absolute; left: 135px; top: 25px; width: 220px; padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px; text-align: left;">'
            .$bill->addr.
            '</span>
                        <span style="font-weight: normal; position: absolute; left: 300px; top: 10px; width: 70px; padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px; text-align: left;">'
            .$bill->mtrser.
            '</span>
                        <span style="font-weight: normal; position: absolute; left: 340px; top: 10px; width: 70px; padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px; text-align: left;">'
            .$bill->serial.
            '</span>
                        <span style="font-weight: normal; position: absolute; left: 400px; top: 10px; width: 50px; padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px; text-align: left;">'
            .$moyr.
            '</span>
                        
                        
                        <span style="font-weight: normal; position: absolute; left: 540px; top: 0px; width: 100px; padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px; text-align: right;">'
            .number_format($current, 2).
            '</span>
                        <span style="font-weight: normal; position: absolute; left: 540px; top: 10px; width: 100px; padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px; text-align: right;">'
            .number_format($bill->overdue, 2).
            '</span>
                        <span style="font-weight: normal; position: absolute; left: 540px; top: 20px; width: 100px; padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px; text-align: right;">'
            .number_format($bill->totint, 2).
            '</span>
                        <span style="font-weight: bold; position: absolute; left: 540px; top: 50px; width: 100px; padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px; text-align: right;">'
            .number_format(($bill->totacc + $bill->totint) , 2).
            '</span>
                        
                        
                        <span style="font-weight: normal; position: absolute; left: 105px; top: 45px; width: 50px; padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px; text-align: left;">'
            .$bill->dolpay.
            '</span>
                         <span style="font-weight: normal; position: absolute; left: 310px; top: 45px; width: 50px; padding: 0px 0px; line-height: 12px; height: 15px; margin: 0px 0px; padding: 0px 0px; text-align: left;">'
            .$bill->duedate.
            '</span>
                    </p>
                </div> 
                
                <!-- BILLING NUMBER -->
                <div style="width: auto; display: inline-block; position: relative; left: 510px;; top: 710px; ">
                    <p style="display: block; position: relative; font-weight: bold; width: 100%; font-size: 12px; margin: 0px 0px padding: 0px 0px; line-height: 10px; height: 10px; margin: 0px 0px; padding: 0px 0px;">
                        <span style="font-weight: bold; font-family: courier, monospace; position: absolute; left: 5px; padding: 0px 0px; line-height: 10px; height: 10px; margin: 0px 0px; padding: 0px 0px; text-align: left;">
                            '.str_pad($bill->billno,8,"0", STR_PAD_LEFT).'
                        </span>
                   </p>
                </div>
                <!-- PAGE NUMBER -->
                <div style="width: auto; display: inline-block; position: relative; left: 580px;; top: 710px; ">
                    <p style="display: block; position: relative; font-weight: bold; width: 100%; font-size: 10px; margin: 0px 0px padding: 0px 0px; line-height: 10px; height: 10px; margin: 0px 0px; padding: 0px 0px;">
                        <span style="width: 50px; font-weight: bold; font-family: courier, monospace; position: absolute; left: 5px; padding: 0px 0px; line-height: 10px; height: 10px; margin: 0px 0px; padding: 0px 0px; text-align: right;">
                            Page: '.str_pad($page,9,"0",STR_PAD_LEFT).'
                        </span>
                   </p>
                </div>
                
                
                
                
                <div style="position: absolute; left: 280px; width: 400px; top: 140px;">
                    <!-- #################################################################### -->
                    <!-- PECO RELATED CHARGES --->
                    
                    <p style="font-weight: bold; font-size: 10px; line-height: 10px; padding: 0px 0px; margin: 0px 0px;">PECO RELEATED CHARGES 
                    <span class="charges-header-rate" style="position: absolute; left: 190px; width: 80px; text-align: right">PER KWH</span> 
                    <span class="charges-header-amt" style="position: absolute; left: 260px; width: 100px; text-align: right">AMOUNT</span> </p>
                    <p style="font-weight: normal; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">
                        Distribution Charge 
                        <span class="charges-header-rate" style="position: absolute; left: 190px; width: 80px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.$dischg.'</span>
                        <span class="charges-header-amt" style="position: absolute; left: 260px; width: 100px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.number_format($disamt,2).'</span>
                     </p>
                     
                     <p style="font-weight: normal; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">
                        Demand Charge 
                        <span class="charges-header-rate" style="position: absolute; left: 190px; width: 80px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;"></span>
                        <span class="charges-header-amt" style="position: absolute; left: 260px; width: 100px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.number_format($demamt,2).'</span>
                     </p>
                     <p style="font-weight: normal; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">
                        Supply Charge 
                        <span class="charges-header-rate" style="position: absolute; left: 190px; width: 80px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.$supchg.'</span>
                        <span class="charges-header-amt" style="position: absolute; left: 260px; width: 100px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.number_format($supamt,2).'</span>
                     </p>
                     <p style="font-weight: normal; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">
                        Metering Charge 
                        <span class="charges-header-rate" style="position: absolute; left: 190px; width: 80px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.$mtrchg.'</span>
                        <span class="charges-header-amt" style="position: absolute; left: 260px; width: 100px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.number_format($mtramt,2).'</span>
                     </p>
                     <p style="font-weight: normal; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">
                        Retail Custom Charge 
                        <span class="charges-header-rate" style="position: absolute; left: 190px; width: 80px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;"></span>
                        <span class="charges-header-amt" style="position: absolute; left: 260px; width: 100px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">5.00</span>
                     </p>
                     
                     <p style="font-weight: normal; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">
                        <span class="charges-header-rate" style="position: absolute; left: 190px; width: 80px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">Sub Total</span>
                        <span class="charges-header-amt" style="position: absolute; left: 260px; width: 100px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.number_format($total_peco_charges, 2).'</span>
                        <span class="charges-header-amt" style="position: absolute; left: 295px;; width: 100px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.number_format($total_peco_charges_percent, 2).'%</span>
                    </p>
                     
                     
                     <!-- #################################################################### -->
                     <!-- SUPLIER RELATED CHARGES --->
                    <p style="font-weight: bold; font-size: 10px; line-height: 10px; padding: 0px 0px; margin: 0px 0px;">SUPLIER RELATED CHARGES (PPC, PEDC)</p>
                    <p style="font-weight: normal; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">
                        Generation Charge 
                        <span class="charges-header-rate" style="position: absolute; left: 190px; width: 80px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.$genchg.'</span>
                        <span class="charges-header-amt" style="position: absolute; left: 260px; width: 100px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.number_format($genamt, 2).'</span>
                     </p>
                     <p style="font-weight: normal; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">
                        Previous Months Adjustment on Generation Cost 
                        <span class="charges-header-rate" style="position: absolute; left: 190px; width: 80px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;"></span>
                        <span class="charges-header-amt" style="position: absolute; left: 260px; width: 100px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">0.00</span>
                     </p>
                     <p style="font-weight: normal; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">
                        Previous Years\' Adjustment on Power Cost
                        <span class="charges-header-rate" style="position: absolute; left: 190px; width: 80px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.$papcchg.'</span>
                        <span class="charges-header-amt" style="position: absolute; left: 260px; width: 100px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.number_format($papc, 2).'</span>
                     </p>
                     <p style="font-weight: normal; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">
                        (ERC Case No. 2001-333) 
                     </p>
                     <p style="font-weight: normal; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">
                        Transmission Charge 
                        <span class="charges-header-rate" style="position: absolute; left: 190px; width: 80px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.$trnchg.'</span>
                        <span class="charges-header-amt" style="position: absolute; left: 260px; width: 100px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.number_format($trnamt, 2).'</span>
                     </p>
                     <p style="font-weight: normal; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">
                        System Loss Charge 
                        <span class="charges-header-rate" style="position: absolute; left: 190px; width: 80px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.$slchg.'</span>
                        <span class="charges-header-amt" style="position: absolute; left: 260px; width: 100px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.number_format($slamt, 2).'</span>
                     </p>
                     
                     <p style="font-weight: normal; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">
                        <span class="charges-header-rate" style="position: absolute; left: 190px; width: 80px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">Sub Total</span>
                        <span class="charges-header-amt" style="position: absolute; left: 260px; width: 100px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.number_format($total_supplier_charges,2).'</span>
                        <span class="charges-header-amt" style="position: absolute; left: 295px;; width: 100px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.number_format($total_supplier_charges_percent,2).'%</span>
                     </p>
                    
                    <!-- #################################################################### -->
                    <!-- SUBSIDIES --->
                    <p style="font-weight: bold; font-size: 10px; line-height: 10px; padding: 0px 0px; margin: 0px 0px;">SUBSIDIES</p>
                    <p style="font-weight: normal; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">
                        Inter-class scross-subsidy 
                        <span class="charges-header-rate" style="position: absolute; left: 190px; width: 80px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;"></span>
                        <span class="charges-header-amt" style="position: absolute; left: 260px; width: 100px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.number_format($iccamt, 2).'</span>
                     </p>
                     <p style="font-weight: normal; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">
                        Lifeline rate subsidy
                        <span class="charges-header-rate" style="position: absolute; left: 190px; width: 80px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;"></span>
                        <span class="charges-header-amt" style="position: absolute; left: 260px; width: 100px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.number_format($llramt, 2).'</span>
                     </p>
                                          
                     <p style="font-weight: normal; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">
                        <span class="charges-header-rate" style="position: absolute; left: 190px; width: 80px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">Sub Total</span>
                        <span class="charges-header-amt" style="position: absolute; left: 260px; width: 100px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.number_format($total_subsidies_charges,2).'</span>
                        <span class="charges-header-amt" style="position: absolute; left: 295px;; width: 100px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.number_format($total_subsidies_charges_percent,2).'%</span>
                    </p>
                     
                     <!-- #################################################################### -->
                    <!-- TAX  --->
                    <p style="font-weight: bold; font-size: 10px; line-height: 10px; padding: 0px 0px; margin: 0px 0px;">TAX AND UNIVERSAL CHARGES</p>
                    <p style="font-weight: normal; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">
                        VAT on Generation
                        <span class="charges-header-rate" style="position: absolute; left: 190px; width: 80px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;"></span>
                        <span class="charges-header-amt" style="position: absolute; left: 260px; width: 100px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.number_format($genvat, 2).'</span>
                     </p>
                     <p style="font-weight: normal; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">
                        VAT on Transmission
                        <span class="charges-header-rate" style="position: absolute; left: 190px; width: 80px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;"></span>
                        <span class="charges-header-amt" style="position: absolute; left: 260px; width: 100px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.number_format($trnvat, 2).'</span>
                     </p>
                     <p style="font-weight: normal; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">
                        VAT on Other Charges
                        <span class="charges-header-rate" style="position: absolute; left: 190px; width: 80px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;"></span>
                        <span class="charges-header-amt" style="position: absolute; left: 260px; width: 100px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.number_format($othvat, 2).'</span>
                     </p>
                     <p style="font-weight: normal; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">
                        Franchise Tax
                        <span class="charges-header-rate" style="position: absolute; left: 190px; width: 80px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;"></span>
                        <span class="charges-header-amt" style="position: absolute; left: 260px; width: 100px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.number_format($framt, 2).'</span>
                     </p>
                     <p style="font-weight: normal; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">
                        Missionary
                        <span class="charges-header-rate" style="position: absolute; left: 190px; width: 80px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.$mischg.'</span>
                        <span class="charges-header-amt" style="position: absolute; left: 260px; width: 100px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.number_format($misamt, 2).'</span>
                     </p>
                     <p style="font-weight: normal; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">
                        Environmental
                        <span class="charges-header-rate" style="position: absolute; left: 190px; width: 80px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.$envchg.'</span>
                        <span class="charges-header-amt" style="position: absolute; left: 260px; width: 100px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.number_format($envamt, 2).'</span>
                     </p>
                     <p style="font-weight: normal; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">
                        NPC Stranded Cntract Cost
                        <span class="charges-header-rate" style="position: absolute; left: 190px; width: 80px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.$npcchg.'</span>
                        <span class="charges-header-amt" style="position: absolute; left: 260px; width: 100px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.number_format($npcamt, 2).'</span>
                     </p>
                     <p style="font-weight: normal; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">
                        ICCS Adjustment
                        <span class="charges-header-rate" style="position: absolute; left: 190px; width: 80px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.$iccschg.'</span>
                        <span class="charges-header-amt" style="position: absolute; left: 260px; width: 100px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.number_format($iccsamt, 2).'</span>
                     </p>
                     <p style="font-weight: normal; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">
                        FIT - Allowance
                        <span class="charges-header-rate" style="position: absolute; left: 190px; width: 80px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.$fitchg.'</span>
                        <span class="charges-header-amt" style="position: absolute; left: 260px; width: 100px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.number_format($fitamt, 2).'</span>
                     </p>
                                          
                     <p style="font-weight: normal; font-size: 8px; margin: 0px 0px padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">
                        <span class="charges-header-rate" style="position: absolute; left: 190px; width: 80px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">Sub Total</span>
                        <span class="charges-header-amt" style="position: absolute; left: 260px; width: 100px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.number_format($total_tax_universal_charges,2).'</span>
                        <span class="charges-header-amt" style="position: absolute; left: 295px;; width: 100px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.number_format($total_tax_universal_charges_percent,2).'%</span>
                     </p>
                     
                     <p style="font-weight: bold; font-size: 10px; margin: 0px 0px padding: 0px 0px; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;" class="charges-list-item">
                        <span class="charges-header-amt" style="position: absolute; left: 260px; width: 100px; text-align: right; line-height: 12px; height: 10px; margin: 0px 0px; padding: 0px 0px;">'.number_format($current,2).'</span>
                     </p>
                     
                     <p style="font-weight: normal; font-size: 8px; line-height: 10px; height: 10px; padding: 0px 0px; margin: 0px 0px;">Historical Data</p>
                     <p style="font-weight: bold; font-size: 8px; line-height: 10px; height: 10px; padding: 0px 0px; margin: 0px 0px;">
                        <span class="" style="position: absolute; left: 0px; width: 70px; text-align: left">BILL PERIOD</span> 
                        <span class="" style="position: absolute; left: 80px; width: 80px; text-align: left">BILL MONTH</span> 
                        <span class="" style="position: absolute; left: 130px; width: 65px; text-align: center">PREV. READING</span> 
                        <span class="" style="position: absolute; left: 200px; width: 65px; text-align: center">PRES. READING</span> 
                        <span class="" style="position: absolute; left: 270px; width: 50px; text-align: right">KWH USE</span>
                        <span class="" style="position: absolute; left: 320px; width: 60px; text-align: right">AMOUNT</span>
                     </p>
                     
                     <!-- HISTORICAL DATA HERE ---->
                     '.$history_html.'

                     <p style="font-weight: bold; font-size: 9px; margin-top: 5px; line-height: 10px; height: 10px; padding: 0px 0px; margin: 0px 0px;">
                        <span class="" style="position: absolute; left: 150px; width: 150px; text-align: right; padding: 0px 0px; margin: 0px 0px;">NEXT METER REAING DATE: </span> 
                        <span class="" style="position: absolute; left: 300px; width: 80px; text-align: right; padding: 0px 0px; margin: 0px 0px;"> '.$next_billing.'</span>
                     </p>
                     
                    
                    <h3 style="font-size: 9px; font-weight: bold; ">THIS IS A SYSTEM GENERATED STATEMENT OF ACCOUNT. NO SIGNATURE IS REQUIRED.</H3>
                </div>
                <footer></footer>
            </div>
            </div>
        ';
        return $html;
    }



    function father_query() {
        ini_set('MAX_EXECUTION_TIME', -1);
        $data = array();
        $msg = '';
        $num_exist = 0;
        $num_insert = 0;
        $d = $this->input->post('d');
        $l = $this->input->post('l');
        $b = $this->input->post('b');
        if (pecoapps_conn()) {
            $conn = $this->load->database('pecoapps', TRUE);
            $conn->initialize();
            if(($d && $d != '') && ($l && $l != '') && ($b && $b != '')) {
                $conn->where(array(
                    'f.dist______' => $d,
                    'f.lot_______' => $l,
                    'f.book______' => $b,
                ));
            }
            $qry = $conn->select('
                LTRIM(RTRIM(f.servno____)) AS servno,
                LTRIM(RTRIM(f.group_____)) AS g,
                LTRIM(RTRIM(f.dist______)) AS d,
                LTRIM(RTRIM(f.lot_______)) AS l,
                LTRIM(RTRIM(f.book______)) AS b,
                f.mtr_______ AS mtr,
                LTRIM(RTRIM(f.class_____)) AS class,
                LTRIM(RTRIM(f.multcd____)) AS multcd,
                LTRIM(RTRIM(f.name______)) AS name,
                LTRIM(RTRIM(f.addr______)) AS addr,
                LTRIM(RTRIM(f.condte____)) AS contractdate,
                LTRIM(RTRIM(f.status____)) AS status,
                LTRIM(RTRIM(f.stadte____)) AS conndate,
                LTRIM(RTRIM(f.mtrser____)) AS mtrno,
                LTRIM(RTRIM(f.serial____)) AS mtrserial,
                f.load______ AS load,
                f.kwh1______ AS kwh_01,
                f.kwh2______ AS kwh_02,
                f.kwh3______ AS kwh_03,
                f.kwh4______ AS kwh_04,
                f.kwh5______ AS kwh_05,
                f.kwh6______ AS kwh_06,
                f.kwh7______ AS kwh_07,
                f.kwh8______ AS kwh_08,
                f.kwh9______ AS kwh_09,
                f.kwh10_____ AS kwh_10,
                f.kwh11_____ AS kwh_11,
                f.kwh12_____ AS kwh_12,
                f.amt1______ AS amt_01,
                f.amt2______ AS amt_02,
                f.amt3______ AS amt_03,
                f.amt4______ AS amt_04,
                f.amt5______ AS amt_05,
                f.amt6______ AS amt_06,
                f.amt7______ AS amt_07,
                f.amt8______ AS amt_08,
                f.amt9______ AS amt_09,
                f.amt10_____ AS amt_10,
                f.amt11_____ AS amt_11,
                f.amt12_____ AS amt_12,
                f.amt13_____ AS amt_13,
                f.bill1_____ AS bill_01,
                f.bill2_____ AS bill_02,
                f.bill3_____ AS bill_03,
                f.bill4_____ AS bill_04,
                f.bill5_____ AS bill_05,
                f.bill6_____ AS bill_06,
                f.bill7_____ AS bill_07,
                f.bill8_____ AS bill_08,
                f.bill9_____ AS bill_09,
                f.bill10____ AS bill_10,
                f.bill11____ AS bill_11,
                f.bill12____ AS bill_12
            ')
                ->from('father AS f')
                ->order_by('f.servno____')
                ->get();

            $num_rows = $qry->num_rows();

            if($num_rows>0) {
                // CLEAR EXISTING DATA FIRST
                //$this->db->query("TRUNCATE TABLE customer_accounts_main;");
                //$this->db->query("TRUNCATE TABLE customer_accounts_ar;");
                //$this->db->query("TRUNCATE TABLE customer_accounts_name_legacy;");
                //$this->db->query("TRUNCATE TABLE customer_accounts_address;");
                // #########################

                foreach($qry->result() as $row) {
                    $servno = $row->servno;
                    $mtr = $row->mtr;

                    if(trim($servno) != '') {
                        // CHECK AND UPDATE FIRST
                        $qry_main_check = $this->db->select('sysid, ownerid')
                            ->from('customer_accounts_main')
                            ->where(array('servicenumber' => $servno, 'mtr' => $mtr))
                            ->get()->row();

                        if ($qry_main_check) {
                            // ##############################################################
                            // UPDATE EXISTING ##############################################

                            $acctid = $qry_main_check->sysid;

                            // GDLBID
                            $get_gdlb = $this->db->select('gdlb.sysid')
                                ->from('gdlb_main AS gdlb')
                                ->join('address_districts AS ads', 'gdlb.d = ads.sysid', 'left')
                                ->where(array('g' => $row->g, 'ads.codes' => $row->d, 'l' => $row->l, 'b' => $row->b, 'g' => 2))
                                ->get()->row();
                            // RATE CLASS ID
                            $get_rateclassid = $this->db->select('sysid')->from('rate_class_specification')
                                ->where(array('codes' => $row->class))
                                ->get()->row();
                            // MULTCODE ID
                            $get_multcodeid = $this->db->select('sysid')->from('billing_rates_main_multiplier')
                                ->where(array('codes' => $row->multcd))
                                ->get()->row();
                            if ($get_gdlb && $get_rateclassid && $get_multcodeid) {
                                // UPDATE
                                $name = ucfirst(utf8_decode($row->name));
                                $upd_own_arr = array('name' => $name);
                                $this->db->where('sysid', $qry_main_check->ownerid);
                                $upd_owner = $this->db->update('customer_accounts_name_legacy', $upd_own_arr);

                                if ($upd_owner) {
                                    // INSERT MIGRATION HERE
                                    $gdlbid = $get_gdlb->sysid;
                                    $rateclassid = $get_rateclassid->sysid;
                                    $multcodeid = $get_multcodeid->sysid;

                                    $contractdate = date('Y-m-d', strtotime($row->contractdate));
                                    $connectdate = date('Y-m-d', strtotime($row->conndate));
                                    $ownerid = $qry_main_check->ownerid;
                                    $status = ($row->status == 1) ? 1 : 0;

                                    $update_acct_main = array(
                                        'datecontract' => $contractdate,
                                        'dateconnected' => $connectdate,
                                        'ownerid' => $ownerid,
                                        'types' => 5,
                                        'gdlb' => $gdlbid,
                                        'mtrno' => $row->mtrno,
                                        'mtrserial' => $row->mtrserial,
                                        'mtr' => $row->mtr,
                                        'rateclassid' => $rateclassid,
                                        'multid' => $multcodeid,
                                        'status' => $status
                                    );
                                    $this->db->where('sysid', $acctid);
                                    $this->db->update('customer_accounts_main', $update_acct_main);


                                    // #############################################################
                                    // UPDATE AR
                                    $upd_ar_arr = array(
                                        'amt_01' => $row->amt_01,
                                        'amt_02' => $row->amt_02,
                                        'amt_03' => $row->amt_03,
                                        'amt_04' => $row->amt_04,
                                        'amt_05' => $row->amt_05,
                                        'amt_06' => $row->amt_06,
                                        'amt_07' => $row->amt_07,
                                        'amt_08' => $row->amt_08,
                                        'amt_09' => $row->amt_09,
                                        'amt_10' => $row->amt_10,
                                        'amt_11' => $row->amt_11,
                                        'amt_12' => $row->amt_12,
                                        'amt_13' => $row->amt_13,
                                        'kwh_01' => $row->kwh_01,
                                        'kwh_02' => $row->kwh_02,
                                        'kwh_03' => $row->kwh_03,
                                        'kwh_04' => $row->kwh_04,
                                        'kwh_05' => $row->kwh_05,
                                        'kwh_06' => $row->kwh_06,
                                        'kwh_07' => $row->kwh_07,
                                        'kwh_08' => $row->kwh_08,
                                        'kwh_09' => $row->kwh_09,
                                        'kwh_10' => $row->kwh_10,
                                        'kwh_11' => $row->kwh_11,
                                        'kwh_12' => $row->kwh_12,
                                        'billno_01' => $row->bill_01,
                                        'billno_02' => $row->bill_02,
                                        'billno_03' => $row->bill_03,
                                        'billno_04' => $row->bill_04,
                                        'billno_05' => $row->bill_05,
                                        'billno_06' => $row->bill_06,
                                        'billno_07' => $row->bill_07,
                                        'billno_08' => $row->bill_08,
                                        'billno_09' => $row->bill_09,
                                        'billno_10' => $row->bill_10,
                                        'billno_11' => $row->bill_11,
                                        'billno_12' => $row->bill_12,
                                    );
                                    $this->db->where(array(
                                        'acctid' => $acctid,
                                        'mtr' => $row->mtr
                                    ));
                                    $this->db->update('customer_accounts_ar', $upd_ar_arr);
                                    $data['error']['ar'][] = array('servno' => $servno, 'msg' => $this->db->_error_message());
                                }
                            }
                        } else {
                            // ##############################################################
                            // INSERT NEW ###################################################
                            // GDLBID
                            $get_gdlb = $this->db->select('gdlb.sysid')
                                ->from('gdlb_main AS gdlb')
                                ->join('address_districts AS ads', 'gdlb.d = ads.sysid', 'left')
                                ->where(array('g' => $row->g, 'ads.codes' => $row->d, 'l' => $row->l, 'b' => $row->b))
                                ->get()->row();
                            // RATE CLASS ID
                            $get_rateclassid = $this->db->select('sysid')->from('rate_class_specification')
                                ->where(array('codes' => $row->class))
                                ->get()->row();
                            // MULTCODE ID
                            $get_multcodeid = $this->db->select('sysid')->from('billing_rates_main_multiplier')
                                ->where(array('codes' => $row->multcd))
                                ->get()->row();
                            if ($get_gdlb && $get_rateclassid && $get_multcodeid) {
                                // INSERT OWNER INFO LEGACY
                                $name = ucfirst(utf8_decode($row->name));
                                $ins_own_arr = array('name' => $name);
                                $ins_owner = $this->db->insert('customer_accounts_name_legacy', $ins_own_arr);
                                $ownerid = $this->db->insert_id();
                                if ($ins_owner) {
                                    // INSERT MIGRATION HERE
                                    $gdlbid = $get_gdlb->sysid;
                                    $rateclassid = $get_rateclassid->sysid;
                                    $multcodeid = $get_multcodeid->sysid;

                                    $contractdate = date('Y-m-d', strtotime($row->contractdate));
                                    $connectdate = date('Y-m-d', strtotime($row->conndate));

                                    $status = ($row->status == 1) ? 1 : 0;
                                    $ins_acct_arr = array(
                                        'servicenumber' => $servno,
                                        'createdby' => 1,
                                        'datecontract' => $contractdate,
                                        'dateconnected' => $connectdate,
                                        'ownerid' => $ownerid,
                                        'types' => 5,
                                        'gdlb' => $gdlbid,
                                        'mtrno' => $row->mtrno,
                                        'mtrserial' => $row->mtrserial,
                                        'mtr' => $row->mtr,
                                        'rateclassid' => $rateclassid,
                                        'multid' => $multcodeid,
                                        'status' => $status
                                    );

                                    //$data['acctinfo'][] = $ins_acct_arr;

                                    // INSERT ACCOUNT
                                    $ins_acct = $this->db->insert('customer_accounts_main', $ins_acct_arr);
                                    $acctid = $this->db->insert_id();


                                    if ($ins_acct) {

                                        // GET MTRSEQ
                                        $qry_mrseq = $conn->select('mrseq')
                                            ->from('seqtab')
                                            ->where(array('servno' => $servno, 'mtr' => $row->mtr, 'mrseq > ' => 0))
                                            ->get()->row();
                                        $mrseq = ($qry_mrseq) ? $qry_mrseq->mrseq : 0;

                                        // INSERT MRSEQ
                                        $this->db->insert('customer_accounts_mtrseq', array('mrseq' => $mrseq, 'acctid' => $acctid));

                                        // INSERT ADDR INFO IF ACCOUNT CREATED
                                        $ins_addr_arr = array(
                                            'acctid' => $acctid,
                                            'country' => 175,
                                            'addrspecific' => ucfirst(utf8_decode($row->addr))
                                        );
                                        $this->db->insert('customer_accounts_address', $ins_addr_arr);

                                        // INSERT LOAD
                                        $ins_load_arr = array(
                                            'acctid' => $acctid,
                                            'load' => $row->load,
                                            'createdby' => 1
                                        );

                                        $this->db->insert('customer_accounts_load_logs', $ins_load_arr);

                                        $ins_arr = array(
                                            'acctid' => $acctid,
                                            'mtr' => $row->mtr,
                                            'amt_01' => $row->amt_01,
                                            'amt_02' => $row->amt_02,
                                            'amt_03' => $row->amt_03,
                                            'amt_04' => $row->amt_04,
                                            'amt_05' => $row->amt_05,
                                            'amt_06' => $row->amt_06,
                                            'amt_07' => $row->amt_07,
                                            'amt_08' => $row->amt_08,
                                            'amt_09' => $row->amt_09,
                                            'amt_10' => $row->amt_10,
                                            'amt_11' => $row->amt_11,
                                            'amt_12' => $row->amt_12,
                                            'amt_13' => $row->amt_13,
                                            'kwh_01' => $row->kwh_01,
                                            'kwh_02' => $row->kwh_02,
                                            'kwh_03' => $row->kwh_03,
                                            'kwh_04' => $row->kwh_04,
                                            'kwh_05' => $row->kwh_05,
                                            'kwh_06' => $row->kwh_06,
                                            'kwh_07' => $row->kwh_07,
                                            'kwh_08' => $row->kwh_08,
                                            'kwh_09' => $row->kwh_09,
                                            'kwh_10' => $row->kwh_10,
                                            'kwh_11' => $row->kwh_11,
                                            'kwh_12' => $row->kwh_12,
                                            'billno_01' => $row->bill_01,
                                            'billno_02' => $row->bill_02,
                                            'billno_03' => $row->bill_03,
                                            'billno_04' => $row->bill_04,
                                            'billno_05' => $row->bill_05,
                                            'billno_06' => $row->bill_06,
                                            'billno_07' => $row->bill_07,
                                            'billno_08' => $row->bill_08,
                                            'billno_09' => $row->bill_09,
                                            'billno_10' => $row->bill_10,
                                            'billno_11' => $row->bill_11,
                                            'billno_12' => $row->bill_12,
                                        );
                                        $this->db->insert('customer_accounts_ar', $ins_arr);
                                        $data['error'][] = $this->db->_error_message();
                                        $num_exist += 1;
                                    } else {
                                        $this->db->insert('customer_migrate_xexist', array('servno' => $servno, 'rem' => 'ACCT'));
                                    }
                                }
                            } else {
                                // @TODO insert error message here
                                $this->db->insert('customer_migrate_xexist', array('servno' => $servno, 'rem' => 'EXT'));
                            }
                        }
                    }

                }
            }
        }else{
            $msg = 'connection failed!';
        }
        $data['msg'] = $msg;
        echo json_encode($data);
    }

    function interest_query() {
        ini_set('MAX_EXECUTION_TIME', -1);
        $data = array();
        $msg = '';
        $num_rows = 0;
        $num_ins = 0;
        if (pecoapps_conn()) {
            $conn = $this->load->database('pecoappsdev', TRUE);
            $conn->initialize();
            $qry = $conn->select('
                    LTRIM(RTRIM(iserv_____)) AS servno,
                    imtr______ AS mtr,
                    int1______ AS int_01,
                    int2______ AS int_02,
                    int3______ AS int_03,
                    int4______ AS int_04,
                    int5______ AS int_05,
                    int6______ AS int_06,
                    int7______ AS int_07,
                    int8______ AS int_08,
                    int9______ AS int_09,
                    int10_____ AS int_10,
                    int11_____ AS int_11,
                    int12_____ AS int_12,
                    int13_____ AS int_13,
                    idte1_____ AS dte_01,
                    idte2_____ AS dte_02,
                    idte3_____ AS dte_03,
                    idte4_____ AS dte_04,
                    idte5_____ AS dte_05,
                    idte6_____ AS dte_06,
                    idte7_____ AS dte_07,
                    idte8_____ AS dte_08,
                    idte9_____ AS dte_09,
                    idte10____ AS dte_10,
                    idte11____ AS dte_11,
                    idte12____ AS dte_12
                ')
                ->from('interest1')->get();
            $num_rows = $qry->num_rows();
            if($num_rows > 0) {
                $this->db->query("TRUNCATE TABLE customer_accounts_ar_interest;");

                foreach($qry->result() as $row) {
                    $qry_acctid = $this->db->select('sysid')->from('customer_accounts_main')
                        ->where(array('servicenumber' => $row->servno, 'mtr' => $row->mtr))
                        ->get()->row();
                    $acctid = ($qry_acctid) ? $qry_acctid->sysid : 0;
                    $ins_int_arr = array(
                        'acctid' => $acctid,
                        'mtr' => $row->mtr,
                        'int_01' => $row->int_01,
                        'int_02' => $row->int_02,
                        'int_03' => $row->int_03,
                        'int_04' => $row->int_04,
                        'int_05' => $row->int_05,
                        'int_06' => $row->int_06,
                        'int_07' => $row->int_07,
                        'int_08' => $row->int_08,
                        'int_09' => $row->int_09,
                        'int_10' => $row->int_10,
                        'int_11' => $row->int_11,
                        'int_12' => $row->int_12,
                        'int_13' => $row->int_13,
                        'dte_01' => date('Y-m-d', strtotime( $row->dte_01 )),
                        'dte_02' => date('Y-m-d', strtotime( $row->dte_02 )),
                        'dte_03' => date('Y-m-d', strtotime( $row->dte_03 )),
                        'dte_04' => date('Y-m-d', strtotime( $row->dte_04 )),
                        'dte_05' => date('Y-m-d', strtotime( $row->dte_05 )),
                        'dte_06' => date('Y-m-d', strtotime( $row->dte_06 )),
                        'dte_07' => date('Y-m-d', strtotime( $row->dte_07 )),
                        'dte_08' => date('Y-m-d', strtotime( $row->dte_08 )),
                        'dte_09' => date('Y-m-d', strtotime( $row->dte_09 )),
                        'dte_10' => date('Y-m-d', strtotime( $row->dte_10 )),
                        'dte_11' => date('Y-m-d', strtotime( $row->dte_11 )),
                        'dte_12' => date('Y-m-d', strtotime( $row->dte_12 ))
                    );
                    $ins = $this->db->insert('customer_accounts_ar_interest', $ins_int_arr);
                    if($ins) {
                        $num_ins += 1;
                    }
                }
            }
            $msg = 'Row Count: '. $num_rows . ' | Ins. Count: '.$num_ins;
        }
        $data['msg'] = $msg;
        $data['num'] = $num_rows;
        return json_encode($data);
    }

    function get_charges_maintenance() {
        $data = array();

        $qry_chrg_main = $this->db->select()->from('trn_billing_rates_group_charges')->get();
        if($qry_chrg_main->num_rows()>0) {
            foreach($qry_chrg_main->result() as $row) {
                $data['list'][] = array(
                    'num' => $row->sorting . '<input id="ch_id" value="'.$row->sysid.'" type="hidden" />',
                    'name' => $row->codes,
                    'descs' => $row->descs,
                    'year' => '<code>N/A</code>',
                    'month' => '<code>N/A</code>',
                );
            }
        }

        return json_encode($data);
    }

    function get_charges_comp() {
        $data = array();
        $id = $this->input->post('id');
        $qry_chrg_main = $this->db->query("
            SELECT
            brm.`names`
            FROM trn_billing_rates_group_list brgl
            LEFT JOIN billing_rates_main brm ON brm.sysid = brgl.rateid
            WHERE brgl.groupid = $id AND brgl.parentid = ''
        ");
        if($qry_chrg_main->num_rows()>0) {
            foreach($qry_chrg_main->result() as $row) {
                $data['list'][] = array(
                    'ratename' => $row->names,
                    'rate' => 0.0,
                );
            }
        }
        return json_encode($data);
    }

    function get_billing_compute() {
        $data = array();
        $html = '';
        $msg = '';

        $sysid = $this->input->post('acctid');
        $schedid = $this->input->post('schedid');
        $ctt = $this->input->post('ctt');

        if( $ctt == 1 ) {
            $get_sched_details = $this->db->select('sysid, gdlbid, months, years, status')
                ->from('reading_schedule_main')
                ->where(array('gdlbid' => 33, 'status' => 1))
                ->order_by('datecreated', 'desc')
                ->get()->row();
            $data['SCHEDID'] = 'CTT: ' . $schedid;
        }else{
            $get_sched_details = $this->db->select('sysid, gdlbid, months, years, status')
                ->from('reading_schedule_main')
                ->where(array('sysid' => $schedid))
                ->get()->row();

            $data['SCHEDID'] = $schedid;
        }

        $data['cct'] = $ctt;

        if ($get_sched_details) {

            $schedid = $get_sched_details->sysid;
            $sched_month = $get_sched_details->months;
            $sched_year = $get_sched_details->years;
            $sched_gdlb = $get_sched_details->gdlbid;

            $data['gdlbid'] = $sched_gdlb;
            $data['year'] = $sched_year;
            $data['month'] = $sched_month;

            /*
            $get_readers_details = $this->db->select()
                ->from('reading_schedule_reader')
                ->where('schedid', $get_sched_details->sysid)
                ->get()->row();
            */

            $prev_month = ($sched_month == 1) ? 12 : $sched_month - 1;
            $prev_year = ($sched_month == 1) ? $sched_year - 1 : $sched_year;

            /*
            $row = $this->db->query("
                        SELECT
                            acct.sysid AS SYSID,
                            acct.servicenumber AS SERVNO,
                            acct.types AS OWNERTYPE,
                            acct.ownerid AS OWNERID,
                            acct.mtr AS MTR,
                            acct.mtrno AS MTRNO,
                            acct.mtrserial AS MTRSER,
                            acct.rateclassid AS RATEID,
                            bm.codes AS MULTCODE,
                            br.prsrdg AS PREVRDG,
                            br.kwhuse AS PREVKWH
                        FROM customer_accounts_main AS acct
                        LEFT JOIN billing_reports_main AS br ON br.acctid = acct.sysid 
                        LEFT JOIN billing_rates_main_multiplier AS bm ON bm.sysid = acct.multid
                        WHERE acct.sysid = $sysid AND br.year = $prev_year AND br.month = $prev_month
                        GROUP BY 
                            acct.sysid,
                            acct.servicenumber,
                            acct.types,
                            acct.ownerid,
                            acct.mtr,
                            acct.mtrno,
                            acct.mtrserial,
                            acct.rateclassid,
                            bm.codes,
                            br.prsrdg,
                            br.kwhuse
                     ")->row();
            */

            // CHECK SPECIFIC
            $row = $this->db->query("
                                SELECT
                                    am.sysid AS SYSID,
                                    am.servicenumber AS SERVNO,
                                    am.types AS OWNERTYPE,
                                    am.ownerid AS OWNERID,
                                    am.mtr AS MTR,
                                    am.mtrno AS MTRNO,
                                    am.mtrserial AS MTRSER,
                                    am.rateclassid AS RATEID,
                                    am.netmtr AS NETMTR,
                                    bm.codes AS MULTCODE,
                                    bm.rate AS MULTIPLIER,
                                    ml.prsrdg AS PRESRDG,
                                    ml.prvrdg AS PREVRDG,
                                    ml.prsdte AS PRESDTE,
                                    ml.prvdte AS PREVDTE,
                                    (ml.prsrdg - ml.prvrdg) AS KWHUSE,
                                    class.codes AS CLASSCODE,
                                    ml.seq AS MRSEQ,
                                    ml.`status` AS MLSTATUS
                                FROM
                                    customer_accounts_main AS am
                                    JOIN reading_schedule_meters_logs AS ml ON ml.acctid = am.sysid 
                                    LEFT JOIN billing_rates_main_multiplier AS bm ON bm.sysid = am.multid
                                    LEFT JOIN rate_class_specification AS class ON class.sysid = am.rateclassid AND class.`status` = 1
                                WHERE
                                    am.`status` = 1 AND ml.`status` = 1 AND ml.schedid = $schedid AND am.sysid = $sysid
                                GROUP BY
                                    am.sysid,
                                    am.servicenumber,
                                    am.types,
                                    am.ownerid,
                                    am.mtr,
                                    am.mtrno,
                                    am.mtrserial,
                                    am.rateclassid,
                                    am.netmtr,
                                    bm.codes,
                                    bm.rate,
                                    class.codes,
                                    ml.seq,
                                    ml.`status`,
                                    ml.prsrdg,
                                    ml.prvrdg,
                                    ml.prsdte,
                                    ml.prvdte
                                ORDER BY
                                    am.servicenumber
                             ")->row();


            $prev_read = $row->PRESRDG;
            $prev_cons = $row->KWHUSE;

            $qry_reading = $this->db->query("
                                        SELECT mrl.sysid, mrl.reading, mrl.demand, mrl.netmtr
                                        FROM customer_accounts_subscription_meter_reading_logs AS mrl
                                        WHERE mrl.acctid = $sysid AND mrl.schedid = $schedid AND mrl.status = 1
                                        ORDER BY mrl.sysid DESC
                            ")->row();

            $pres_read = 0;
            $pres_cons = 0;
            $pres_dems = 0;
            $netmtr = 0;
            if ($qry_reading) {

                $data['readingstat'] = 'Regular Reading Entry';
                $pres_read = $qry_reading->reading;
                $readid = $qry_reading->sysid;
                $pres_cons = mtr_wrap_kwh($pres_read, $prev_read, $readid);
                $pres_dems = $qry_reading->demand;
                $netmtr = $qry_reading->netmtr;
                $readid = $qry_reading->sysid;


                // GET CLASS RATE GROUP
                $qry_rateclass_group = $this->db->select('rs.descs')
                    ->from('rate_class_specification AS rs')
                    ->join('rate_class_group AS rg', 'rg.classid = rs.sysid', 'left')
                    ->where(array('rs.sysid' => $row->RATEID, 'rg.rateid' => 3))
                    ->get()->row();

                if ($qry_rateclass_group) {
                    $demand = '<div class="input-icon left">' .
                        '<i class="fa fa-pencil tooltips" data-original-title="Enter Reading Amount"></i>' .
                        '<input name="demand[' . $row->SYSID . ']" placeholder="0" class="form-control input-xs inline" style="width: 100%;" id="demand" value="' . $pres_dems . '"/>' .
                        '</div>';
                } else {
                    $demand = '<span class="label label-danger">N/A</span>';
                }
            } else {
                $msg = 'No reading data!';
            }


            $row_color = '';
            $row_rem = '';
            // ################################################################
            // CHECK FOR RECHECK

            $findings = '';
            $readrecheck = false;
            $readid = 0;
            if ($pres_cons <= 0) {
                $data['readingstat'] = 'Analysis Reading Entry';
                $qry_reading_recheck = $this->db->query("
                                        SELECT mrl.sysid, mrl.reading, mrl.demand, mrl.netmtr
                                        FROM customer_accounts_subscription_meter_reading_logs AS mrl
                                        WHERE mrl.acctid = $sysid AND mrl.schedid = $schedid AND mrl.status = 1 AND mrl.types = 2
                                        ORDER BY mrl.sysid DESC
                            ")->row();
                if ($qry_reading_recheck) {
                    $pres_read = $qry_reading_recheck->reading;
                    $readid = $qry_reading_recheck->sysid;
                    $netmtr = $qry_reading_recheck->netmtr;
                    $pres_cons = mtr_wrap_kwh($pres_read, $prev_read, $readid);
                    $readrecheck = true;
                    $row_color = 'warning';


                    // GET CLASS RATE GROUP
                    $qry_rateclass_group = $this->db->select('rs.descs')
                        ->from('rate_class_specification AS rs')
                        ->join('rate_class_group AS rg', 'rg.classid = rs.sysid', 'left')
                        ->where(array('rs.sysid' => $row->RATEID, 'rg.rateid' => 3))
                        ->get()->row();

                    if ($qry_rateclass_group) {
                        $demand = '<div class="input-icon left">' .
                            '<i class="fa fa-pencil tooltips" data-original-title="Enter Reading Amount"></i>' .
                            '<input name="demand[' . $row->SYSID . ']" placeholder="0" class="form-control input-xs inline" style="width: 100%;" id="demand" value="' . $pres_dems . '"/>' .
                            '</div>';
                    } else {
                        $demand = '<span class="label label-danger">N/A</span>';
                    }
                }
            }
            // ################################################################
            // CHECK FOR ADD BILL ENTRY
            $addbill = false;
            if ($pres_cons <= 0) {
                $qry_addbill = $this->db->select('kwhuse')->from('trn_reading_analysis_addbills')
                    ->where(array('acctid' => $sysid, 'schedid' => $schedid, 'status > ' => 0))
                    ->get()->row();
                if ($qry_addbill) {
                    $addbill = true;
                    $row_color = 'warning';
                    $pres_cons = $qry_addbill->kwhuse;
                    $row_rem = '<span class="label label-warning" style="width: 100%; display: inline-block">A/L</span>';
                } else {
                    $qry_addbill_2 = $this->db->select('kwhuse')->from('trn_reading_analysis_addbills')
                        ->where(array('acctid' => $sysid, 'schedid' => $schedid, 'status > ' => 0, 'types' => 2))
                        ->get()->row();
                    if ($qry_addbill_2) {
                        $addbill = true;
                        $pres_cons = $qry_addbill_2->kwhuse;
                        $row_color = 'warning';
                        $row_rem = '<span class="label label-danger">RV CHECK</span>';
                    }
                }
            }
            // ################################################################

            $data = array();

            $compute = (object)compute_billing($sysid, $sched_year, $sched_month, $pres_cons, $pres_dems, $netmtr);


            $data['addbill'] = $addbill;
            $data['acctid'] = $sysid;
            $data['schedid'] = $schedid;
            $data['year'] = $sched_year;
            $data['month'] = $sched_month;
            $data['kwh'] = $pres_cons;

            //$data['compute'] = $compute;

            if ($compute) {
                $qry = true;
                // #####################################################################
                // CREATE HTML DISPLAY #################################################
                // $html .= '<div class="row">';

                $html .= '<div class="col-md-3">';
                $html .= '<ul class="list-group list-group-sm summary column no-border">';

                $html .= '<li class="list-group-item" style="border-bottom: 1px dotted #ccc !important; margin-top: 10px; margin-bottom: 5px;">';
                $html .= '<span class="col-md-3 label-name">KWH</span>';
                //$compute->kwh
                $html .= '<span class="col-md-9 label-default number">' . number_format($pres_cons) . '</span>';
                $html .= '</li>';

                $html .= '<li class="list-group-item" style="border-bottom: 1px dotted #ccc !important; margin-top: 10px; margin-bottom: 5px;">';
                $html .= '<span class="col-md-3 label-name">Current</span>';
                $html .= '<span class="col-md-9 label-default number">' . number_format($compute->curr, 2) . '</span>';
                $html .= '</li>';

                if ($compute->netmtr == 1) {
                    $html .= '<li class="list-group-item" style="border-bottom: 1px dotted #ccc !important; margin-top: 10px; margin-bottom: 5px;">';
                    $html .= '<span class="col-md-5 label-name">Net Metring Amt.</span>';
                    $html .= '<span class="col-md-7 label-default number">' . number_format($compute->netmtramt, 2) . '</span>';
                    $html .= '</li>';
                }

                $html .= '</ul>';
                $html .= '</div>';

                $html .= '<div class="col-md-3">';
                $html .= '<ul class="list-group list-group-sm summary column no-border">';

                $html .= '<li class="list-group-item" style="border-bottom: 1px dotted #ccc !important; margin-top: 10px; margin-bottom: 5px;">';
                $html .= '<span class="col-md-4 label-name">Overdue</span>';
                $html .= '<span class="col-md-8 label-default number">' . number_format($compute->previous, 2) . '</span>';
                $html .= '</li>';

                $html .= '<li class="list-group-item" style="border-bottom: 1px dotted #ccc !important; margin-top: 10px; margin-bottom: 5px;">';
                $html .= '<span class="col-md-4 label-name">Interest</span>';
                $html .= '<span class="col-md-8 label-default number">' . number_format($compute->interest, 2) . '</span>';
                $html .= '</li>';

                if ($compute->scdisc < 0) {
                    $html .= '<li class="list-group-item" style="border-bottom: 1px dotted #ccc !important; margin-top: 10px; margin-bottom: 5px;">';
                    $html .= '<span class="col-md-4 label-name">SC Discount</span>';
                    $html .= '<span class="col-md-8 label-default number">' . number_format($compute->scdisc, 2) . '</span>';
                    $html .= '</li>';
                }

                $html .= '</ul>';
                $html .= '</div>';

                $html .= '<div class="col-md-3">';
                $html .= '<ul class="list-group list-group-sm summary column no-border">';

                $html .= '<li class="list-group-item" style="border-bottom: 1px dotted #ccc !important; margin-top: 10px; margin-bottom: 5px;">';
                $html .= '<span class="col-md-4 label-name">Vat Amt.</span>';
                $html .= '<span class="col-md-8 label-default number">' . number_format($compute->totalvat, 2) . '</span>';
                $html .= '</li>';

                $html .= '<li class="list-group-item" style="border-bottom: 1px dotted #ccc !important; margin-top: 10px; margin-bottom: 5px;">';
                $html .= '<span class="col-md-4 label-name">Charges Amt.</span>';
                $html .= '<span class="col-md-8 label-default number">' . number_format($compute->totalcharges, 2) . '</span>';
                $html .= '</li>';

                $html .= '</ul>';
                $html .= '</div>';

                $html .= '<div class="col-md-3" style="padding-right: 30px;">';
                $html .= '<ul class="list-group list-group-sm summary column no-border">';

                $html .= '<li class="list-group-item" style="border-bottom: 1px dotted #ccc !important; margin-top: 10px; margin-bottom: 5px;">';
                $html .= '<span class="col-md-6 label-name">Date of Last Payment</span>';
                $html .= '<span class="col-md-6 label-default number"></span>';
                $html .= '</li>';

                $html .= '<li class="list-group-item" style="border-bottom: 1px dotted #ccc !important; margin-top: 10px; margin-bottom: 5px;">';
                $html .= '<span class="col-md-6 label-name">Due Date</span>';
                $html .= '<span class="col-md-6 label-default number">' . $compute->duedate . '</span>';
                $html .= '</li>';
                $html .= '</ul>';
                $html .= '</div>';

                $html .= '</div>';
                $html .= '<div class="row margin-top-15 footer">';
                $html .= '<div class="col-md-12" style="padding-top: 10px;">';

                if($addbill == true) {
                    $html .= '<span class="label label-danger">Add Bill</span>';
                }

                $html .= $compute->footnote;
                $html .= $compute->discounts;

                $html .= '<div class="row">';
                $html .= '<div class="col-md-12">';
                $html .= '<div class="btn-group" style="float: right; display: inline-block;">';
                $html .= '<button id="btn_show_charges" class="btn btn-default btn-xs accordion-toggle" data-toggle="collapse" data-parent="#accordion_' . $readid . '" href="#sales_details_' . $readid . '"><i class="fa fa-search fa-fw"></i> Show Charges</a>';
                $html .= '</div>';
                $html .= '</div>';
                $html .= '</div>';
                $html .= '</div>';
                // $html .= '</div>';
                $html .= '<div class="row margin-top-20">';

                // #####################################################################
                $col_num = count($compute->data);
                $col_width = (100 / $col_num);
                $total_charges = 0;
                $html .= '<div id="sales_details_' . $readid . '" class="sales-details panel-collapse collapse" style="padding: 5px 15px; position: relative; border-bottom: 1px solid rgba(0,0,0, 0.05); margin-bottom: 20px;">';
                foreach ($compute->data as $row) {
                    $html .= '<div class="sales-details-list" style="vertical-align: top; min-height: 100px; display: inline-block; width: ' . $col_width . '% !important; padding: 10px 10px">';
                    $html .= '<b class="text-color-blue">' . $row['groupname'] . '</b>';
                    $group_id = $row['groupid'];
                    ${'total_charges_' . $group_id} = 0;
                    if (isset($row['lists']) && count($row['lists']) > 0) {
                        $html .= '<ul class="list-group ">';
                        foreach ($row['lists'] as $lrow) {
                            $rate = ($lrow['rate'] > 0) ? number_format($lrow['rate'], 4) : '';
                            if ($lrow['subs'] == false) {
                                $ratename = $lrow['ratename'];
                            } else {
                                $ratename = '<a style="" class="rate-sub" href="javascript:;">' . $lrow['ratename'] . '</a>';
                            }
                            $total_charges += $lrow['amt'];
                            ${'total_charges_' . $group_id} += round($lrow['amt'], 2);
                            $html .= '<li class="list-group-item" style="border-bottom: 1px dotted #ccc !important; margin-top: 10px; margin-bottom: 5px;"><div class="row" style=""><span class="col-md-7">' . $ratename . ':</span><span class="col-md-2 data">' . $rate . '</span> <span class="col-md-3 data pull-right text-align-right">' . number_format($lrow['amt'], 2) . '</span></span>';
                            if ($lrow['subs'] == true) {
                                $html .= '<ul class="list-group sub hidden animated fadeInDown fast" style="list-style-type: circle !important; margin-top: 20px;">';
                                foreach ($lrow['bradedown'] as $slrow) {
                                    $rate_amt = ($slrow['rate'] > 0) ? number_format($slrow['rate'], 4) : '';
                                    $html .= '<li class="list-group-item" style="list-style: circle inside !important; font-size: 11px !important;"> <span class="col-md-7" style="padding-left: 12px !important;"><span style="padding-left: 15px; display: inline-block; ">' . $slrow['ratename'] . ':</span></span><span class="col-md-2 data text-align-right" >' . $rate_amt . '</span> <span class="col-md-3 data pull-right text-align-right">' . number_format($slrow['amt'], 2) . '</span></li>';
                                }
                                $html .= '</ul>';
                            }
                            $html .= '</li>';
                        }
                        $html .= '<li class="list-group-item" style="border-bottom: 1px dotted #ccc !important; margin-top: 10px; margin-bottom: 5px;"><div class="row" style=""><span class="col-md-7"><b>Total :</b></span><span class="col-md-5 data pull-right text-align-right">' . number_format(${'total_charges_' . $group_id}, 2) . '</span></span></li>';
                        $html .= '</ul>';
                    }
                    $html .= '</div>';
                }
                $html .= '</div></div></div>';

                //$data['discarr'] = $compute->discarr;
                $data['lldamt'] = number_format($compute->lldamt, 2);
                $data['scdisc'] = number_format($compute->scdisc, 2);
                $data['prevtotal'] = number_format($compute->previous, 2);
                $data['prevbill'] = number_format($compute->previous, 2);
                $data['prevvat'] = number_format($compute->prevvat, 2);

                $data['total'] = number_format($compute->total, 2);
                $data['totalcharges'] = number_format($compute->totalcharges, 2);
                $data['curamt'] = number_format($compute->totalcharges, 2);
                $data['curvat'] = number_format($compute->totalvat, 2);
                $data['rate'] = $compute->ratecode;
                $data['billrate'] = $compute->billratecode;
                $data['gdlbid'] = $compute->gdlbid;
                $data['genamt'] = number_format($compute->genamt, 2);
                $data['kwh'] = number_format($compute->kwh);
                $data['billcount'] = $compute->billcnt;
                $data['html'] = $html;

                $data['dateprint'] = date('Y-m-d');
                $data['datedelevered'] = date('Y-m-d');
                $data['datebilled'] = date('Y-m-d');
            }


        }else{
            $msg = 'Unable to get schedule details!';
        }
        $data['msg'] = $msg;
        $data['html'] = $html;

        return json_encode($data);
    }


    function get_billing_compute_ct() {
        $data = array();

        $ctt = $this->input->post('ctt');

        $get_sched_details = $this->db->select('sysid, gdlbid, months, years, status')
            ->from('reading_schedule_main')
            ->where(array('gdlbid' => 33, 'status' => 1))
            ->order_by('datecreated', 'desc')
            ->get()->row();

        if ($get_sched_details) {
            $schedid = $get_sched_details->sysid;
            $sched_month = $get_sched_details->months;
            $sched_year = $get_sched_details->years;
            $sched_gdlb = $get_sched_details->gdlbid;

            $data['gdlbid'] = $sched_gdlb;
            $data['year'] = $sched_year;
            $data['month'] = $sched_month;

            $prev_month = ($sched_month == 1) ? 12 : $sched_month - 1;
            $prev_year = ($sched_month == 1) ? $sched_year - 1 : $sched_year;

            $row = $this->db->query("
                        SELECT
                            acct.sysid AS SYSID,
                            acct.servicenumber AS SERVNO,
                            acct.types AS OWNERTYPE,
                            acct.ownerid AS OWNERID,
                            acct.mtr AS MTR,
                            acct.mtrno AS MTRNO,
                            acct.mtrserial AS MTRSER,
                            acct.rateclassid AS RATEID,
                            bm.codes AS MULTCODE,
                            br.prsrdg AS PREVRDG,
                            br.kwhuse AS PREVKWH
                        FROM customer_accounts_main AS acct
                        LEFT JOIN billing_reports_main AS br ON br.acctid = acct.sysid 
                        LEFT JOIN billing_rates_main_multiplier AS bm ON bm.sysid = acct.multid
                        WHERE acct.sysid = $sysid AND br.year = $prev_year AND br.month = $prev_month
                        GROUP BY 
                            acct.sysid,
                            acct.servicenumber,
                            acct.types,
                            acct.ownerid,
                            acct.mtr,
                            acct.mtrno,
                            acct.mtrserial,
                            acct.rateclassid,
                            bm.codes,
                            br.prsrdg,
                            br.kwhuse
                     ")->row();

            $prev_read = $row->PREVRDG;
            $prev_cons = $row->PREVKWH;
            $qry_reading = $this->db->query("
                                        SELECT mrl.sysid, mrl.reading, mrl.demand, mrl.netmtr
                                        FROM customer_accounts_subscription_meter_reading_logs AS mrl
                                        WHERE mrl.acctid = $sysid AND mrl.schedid = $schedid AND mrl.status = 1 AND mrl.types < 2
                                        ORDER BY mrl.sysid DESC
                            ")->row();

            $pres_read = 0;
            $pres_cons = 0;
            $pres_dems = 0;
            $netmtr = 0;
            if ($qry_reading) {
                $pres_read = $qry_reading->reading;
                $readid = $qry_reading->sysid;
                $pres_cons = mtr_wrap_kwh($pres_read, $prev_read, $readid);
                $pres_dems = $qry_reading->demand;
                $netmtr = $qry_reading->netmtr;
                $readid = $qry_reading->sysid;


                // GET CLASS RATE GROUP
                $qry_rateclass_group = $this->db->select('rs.descs')
                    ->from('rate_class_specification AS rs')
                    ->join('rate_class_group AS rg', 'rg.classid = rs.sysid', 'left')
                    ->where(array('rs.sysid' => $row->RATEID, 'rg.rateid' => 3))
                    ->get()->row();

                if ($qry_rateclass_group) {
                    $demand = '<div class="input-icon left">' .
                        '<i class="fa fa-pencil tooltips" data-original-title="Enter Reading Amount"></i>' .
                        '<input name="demand[' . $row->SYSID . ']" placeholder="0" class="form-control input-xs inline" style="width: 100%;" id="demand" value="' . $pres_dems . '"/>' .
                        '</div>';
                } else {
                    $demand = '<span class="label label-danger">N/A</span>';
                }
            }


            $row_color = '';
            $row_rem = '';
            // ################################################################
            // CHECK FOR RECHECK

            $findings = '';
            $readrecheck = false;
            $readid = 0;
            if ($pres_cons <= 0) {
                $qry_reading_recheck = $this->db->query("
                                        SELECT mrl.sysid, mrl.reading, mrl.demand, mrl.netmtr
                                        FROM customer_accounts_subscription_meter_reading_logs AS mrl
                                        WHERE mrl.acctid = $sysid AND mrl.schedid = $schedid AND mrl.status = 1 AND mrl.types = 2
                                        ORDER BY mrl.sysid DESC
                            ")->row();
                if ($qry_reading_recheck) {
                    $pres_read = $qry_reading_recheck->reading;
                    $readid = $qry_reading_recheck->sysid;
                    $netmtr = $qry_reading_recheck->netmtr;
                    $pres_cons = mtr_wrap_kwh($pres_read, $prev_read, $readid);
                    $readrecheck = true;
                    $row_color = 'warning';


                    // GET CLASS RATE GROUP
                    $qry_rateclass_group = $this->db->select('rs.descs')
                        ->from('rate_class_specification AS rs')
                        ->join('rate_class_group AS rg', 'rg.classid = rs.sysid', 'left')
                        ->where(array('rs.sysid' => $row->RATEID, 'rg.rateid' => 3))
                        ->get()->row();

                    if ($qry_rateclass_group) {
                        $demand = '<div class="input-icon left">' .
                            '<i class="fa fa-pencil tooltips" data-original-title="Enter Reading Amount"></i>' .
                            '<input name="demand[' . $row->SYSID . ']" placeholder="0" class="form-control input-xs inline" style="width: 100%;" id="demand" value="' . $pres_dems . '"/>' .
                            '</div>';
                    } else {
                        $demand = '<span class="label label-danger">N/A</span>';
                    }
                }
            }
            // ################################################################
            // CHECK FOR ADD BILL ENTRY
            $addbill = false;
            if ($pres_cons <= 0) {
                $qry_addbill = $this->db->select('kwhuse')->from('trn_reading_analysis_addbills')
                    ->where(array('acctid' => $sysid, 'schedid' => $schedid, 'status > ' => 0, 'types' => 1))
                    ->get()->row();
                if ($qry_addbill) {
                    $addbill = true;
                    $row_color = 'warning';
                    $pres_cons = $qry_addbill->kwhuse;
                    $row_rem = '<span class="label label-warning" style="width: 100%; display: inline-block">A/L</span>';
                } else {
                    $qry_addbill_2 = $this->db->select('kwhuse')->from('trn_reading_analysis_addbills')
                        ->where(array('acctid' => $sysid, 'schedid' => $schedid, 'status > ' => 0, 'types' => 2))
                        ->get()->row();
                    if ($qry_addbill_2) {
                        $addbill = true;
                        $pres_cons = $qry_addbill_2->kwhuse;
                        $row_color = 'warning';
                        $row_rem = '<span class="label label-danger">RV CHECK</span>';
                    }
                }
            }
            // ################################################################

            $data = array();

            $compute = (object)compute_billing($sysid, $sched_year, $sched_month, $pres_cons, $pres_dems, $netmtr);

            $html = '';

            if ($compute) {
                $qry = true;
                // #####################################################################
                // CREATE HTML DISPLAY #################################################
                // $html .= '<div class="row">';

                $html .= '<div class="col-md-3">';
                $html .= '<ul class="list-group list-group-sm summary column no-border">';

                $html .= '<li class="list-group-item" style="border-bottom: 1px dotted #ccc !important; margin-top: 10px; margin-bottom: 5px;">';
                $html .= '<span class="col-md-3 label-name">KWH</span>';
                //$compute->kwh
                $html .= '<span class="col-md-9 label-default number">'.number_format($pres_cons).'</span>';
                $html .= '</li>';

                $html .= '<li class="list-group-item" style="border-bottom: 1px dotted #ccc !important; margin-top: 10px; margin-bottom: 5px;">';
                $html .= '<span class="col-md-3 label-name">Current</span>';
                $html .= '<span class="col-md-9 label-default number">'.number_format($compute->curr, 2).'</span>';
                $html .= '</li>';

                if( $compute->netmtr == 1) {
                    $html .= '<li class="list-group-item" style="border-bottom: 1px dotted #ccc !important; margin-top: 10px; margin-bottom: 5px;">';
                    $html .= '<span class="col-md-5 label-name">Net Metring Amt.</span>';
                    $html .= '<span class="col-md-7 label-default number">' . number_format($compute->netmtramt, 2) . '</span>';
                    $html .= '</li>';
                }



                $html .= '</ul>';
                $html .= '</div>';

                $html .= '<div class="col-md-3">';
                $html .= '<ul class="list-group list-group-sm summary column no-border">';

                $html .= '<li class="list-group-item" style="border-bottom: 1px dotted #ccc !important; margin-top: 10px; margin-bottom: 5px;">';
                $html .= '<span class="col-md-4 label-name">Overdue</span>';
                $html .= '<span class="col-md-8 label-default number">'.number_format($compute->previous, 2).'</span>';
                $html .= '</li>';

                $html .= '<li class="list-group-item" style="border-bottom: 1px dotted #ccc !important; margin-top: 10px; margin-bottom: 5px;">';
                $html .= '<span class="col-md-4 label-name">Interest</span>';
                $html .= '<span class="col-md-8 label-default number">'.number_format($compute->previous, 2).'</span>';
                $html .= '</li>';

                if( $compute->scdisc < 0) {
                    $html .= '<li class="list-group-item" style="border-bottom: 1px dotted #ccc !important; margin-top: 10px; margin-bottom: 5px;">';
                    $html .= '<span class="col-md-4 label-name">SC Discount</span>';
                    $html .= '<span class="col-md-8 label-default number">' . number_format($compute->scdisc, 2) . '</span>';
                    $html .= '</li>';
                }

                $html .= '</ul>';
                $html .= '</div>';

                $html .= '<div class="col-md-3">';
                $html .= '<ul class="list-group list-group-sm summary column no-border">';

                $html .= '<li class="list-group-item" style="border-bottom: 1px dotted #ccc !important; margin-top: 10px; margin-bottom: 5px;">';
                $html .= '<span class="col-md-4 label-name">Vat Amt.</span>';
                $html .= '<span class="col-md-8 label-default number">'.number_format($compute->totalvat, 2).'</span>';
                $html .= '</li>';

                $html .= '<li class="list-group-item" style="border-bottom: 1px dotted #ccc !important; margin-top: 10px; margin-bottom: 5px;">';
                $html .= '<span class="col-md-4 label-name">Charges Amt.</span>';
                $html .= '<span class="col-md-8 label-default number">'.number_format($compute->totalcharges, 2).'</span>';
                $html .= '</li>';

                $html .= '</ul>';
                $html .= '</div>';

                $html .= '<div class="col-md-3" style="padding-right: 30px;">';
                $html .= '<ul class="list-group list-group-sm summary column no-border">';

                $html .= '<li class="list-group-item" style="border-bottom: 1px dotted #ccc !important; margin-top: 10px; margin-bottom: 5px;">';
                $html .= '<span class="col-md-6 label-name">Date of Last Payment</span>';
                $html .= '<span class="col-md-6 label-default number"></span>';
                $html .= '</li>';

                $html .= '<li class="list-group-item" style="border-bottom: 1px dotted #ccc !important; margin-top: 10px; margin-bottom: 5px;">';
                $html .= '<span class="col-md-6 label-name">Due Date</span>';
                $html .= '<span class="col-md-6 label-default number">'.$compute->duedate.'</span>';
                $html .= '</li>';
                $html .= '</ul>';
                $html .= '</div>';

                $html .= '</div>';
                $html .= '<div class="row margin-top-15 footer">';
                $html .= '<div class="col-md-12" style="padding-top: 10px;">';
                if($addbill == true) {
                    $html .= '<span class="label label-danger">Add Bill</span>';
                }
                $html .= $compute->footnote;
                $html .= $compute->discounts;

                $html .= '<div class="row">';
                $html .= '<div class="col-md-12">';
                $html .= '<div class="btn-group" style="float: right; display: inline-block;">';
                $html .= '<button id="btn_show_charges" class="btn btn-default btn-xs accordion-toggle" data-toggle="collapse" data-parent="#accordion_' . $readid . '" href="#sales_details_' . $readid . '"><i class="fa fa-search fa-fw"></i> Show Charges</a>';
                $html .= '</div>';
                $html .= '</div>';
                $html .= '</div>';
                $html .= '</div>';
                // $html .= '</div>';
                $html .= '<div class="row margin-top-20">';

                // #####################################################################
                $col_num = count($compute->data);
                $col_width = (100 / $col_num);
                $total_charges = 0;
                $html .= '<div id="sales_details_' . $readid . '" class="sales-details panel-collapse collapse" style="padding: 5px 15px; position: relative; border-bottom: 1px solid rgba(0,0,0, 0.05); margin-bottom: 20px;">';
                foreach ($compute->data as $row) {
                    $html .= '<div class="sales-details-list" style="vertical-align: top; min-height: 100px; display: inline-block; width: ' . $col_width . '% !important; padding: 10px 10px">';
                    $html .= '<b class="text-color-blue">' . $row['groupname'] . '</b>';
                    $group_id = $row['groupid'];
                    ${'total_charges_' . $group_id} = 0;
                    if (isset($row['lists']) && count($row['lists']) > 0) {
                        $html .= '<ul class="list-group ">';
                        foreach ($row['lists'] as $lrow) {
                            $rate = ($lrow['rate']>0) ? number_format($lrow['rate'], 4) : '';
                            if ($lrow['subs'] == false) {
                                $ratename = $lrow['ratename'];
                            } else {
                                $ratename = '<a style="" class="rate-sub" href="javascript:;">' . $lrow['ratename'] . '</a>';
                            }
                            $total_charges += $lrow['amt'];
                            ${'total_charges_' . $group_id} += round($lrow['amt'], 2);
                            $html .= '<li class="list-group-item" style="border-bottom: 1px dotted #ccc !important; margin-top: 10px; margin-bottom: 5px;"><div class="row" style=""><span class="col-md-7">' . $ratename . ':</span><span class="col-md-2 data">' . $rate . '</span> <span class="col-md-3 data pull-right text-align-right">' . number_format($lrow['amt'], 2) . '</span></span>';
                            if ($lrow['subs'] == true) {
                                $html .= '<ul class="list-group sub hidden animated fadeInDown fast" style="list-style-type: circle !important; margin-top: 20px;">';
                                foreach ($lrow['bradedown'] as $slrow) {
                                    $rate_amt = ($slrow['rate']>0) ? number_format($slrow['rate'], 4) : '';
                                    $html .= '<li class="list-group-item" style="list-style: circle inside !important; font-size: 11px !important;"> <span class="col-md-7" style="padding-left: 12px !important;"><span style="padding-left: 15px; display: inline-block; ">' . $slrow['ratename'] . ':</span></span><span class="col-md-2 data text-align-right" >' . $rate_amt . '</span> <span class="col-md-3 data pull-right text-align-right">' . number_format($slrow['amt'], 2) . '</span></li>';
                                }
                                $html .= '</ul>';
                            }
                            $html .= '</li>';
                        }
                        $html .= '<li class="list-group-item" style="border-bottom: 1px dotted #ccc !important; margin-top: 10px; margin-bottom: 5px;"><div class="row" style=""><span class="col-md-7"><b>Total :</b></span><span class="col-md-5 data pull-right text-align-right">' . number_format(${'total_charges_' . $group_id}, 2) . '</span></span></li>';
                        $html .= '</ul>';
                    }
                    $html .= '</div>';
                }
                $html .= '</div></div></div>';

                $data['lldamt'] = number_format($compute->lldamt, 2);
                $data['scdisc'] = number_format($compute->scdisc, 2);
                $data['prevtotal'] = number_format($compute->previous, 2);
                $data['prevbill'] = number_format($compute->previous, 2);
                $data['prevvat'] = number_format($compute->prevvat, 2);

                $data['total'] = number_format($compute->total, 2);
                $data['totalcharges'] = number_format($compute->totalcharges, 2);
                $data['curamt'] = number_format($compute->totalcharges, 2);
                $data['curvat'] = number_format($compute->totalvat, 2);
                $data['rate'] = $compute->ratecode;
                $data['billrate'] = $compute->billratecode;
                $data['gdlbid'] = $compute->gdlbid;
                $data['genamt'] = number_format($compute->genamt, 2);
                $data['kwh'] = number_format($compute->kwh);
                $data['billcount'] = $compute->billcnt;
                $data['html'] = $html;

                $data['dateprint'] = date('Y-m-d');
                $data['datedelevered'] = date('Y-m-d');
                $data['datebilled'] = date('Y-m-d');
            }
        }

        return json_encode($data);
    }

    function pae_billing() {
        $data = array();
        $years = $this->input->post('year');
        $months = $this->input->post('month');

        $sql = $this->db->query("SELECT
                                      CONCAT(
                                        p.firstname, ' ',
                                        p.lastname, ' ', 
                                        p.middlename) AS `name`,
                                        a.essrno,
                                        b.duedate,
                                        b.amount 
                                    FROM
                                        billing_pae_trn AS b
                                        INNER JOIN application_customers_details AS a ON b.appid = a.sysid
                                        INNER JOIN person AS p ON a.personid = p.sysid
                                        WHERE b.years = 2021 AND b.months = 11 AND b.`status` > 0
                                        ");
        if($sql->num_rows()>0) {
            foreach($sql->result() as $row) {
                $data['list'][] = array(

                );
            }
        }
        return json_encode($data);
    }
}

?>