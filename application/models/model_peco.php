<?php
/**
 * Created by PhpStorm.
 * User: ITD-SE
 * Date: 8/17/2018
 * Time: 11:34 AM
 */

class Model_peco extends CI_Model
{
    function compute_acct_kwh_average($acctid, $startmonth, $div = 12) {
        $data = array();
        $average = 0;
        $months = 0;
        $kwh = 0;
        $kwh_arr = array();
        $qry_billrep = $this->db->select('kwhuse, prsdte')
            ->from('billing_reports_main')
            ->where(array(
                'acctid' => $acctid,
                'month <= ' => $startmonth,
                'batch != ' => 'LATEBILL'
            ))
            ->limit(12)
            ->order_by('prsdte', 'desc')
            ->get();
        $num_rows = $qry_billrep->num_rows();

        if($num_rows) {
            $i = 0;
            foreach($qry_billrep->result() as $row) {
                if ( $i < $div ) {
                    $kwh += $row->kwhuse;
                    $months += 1;
                }
                $i++;
                $kwh_arr[] = array('kwh' => $row->kwhuse, 'prsdte' => $row->prsdte);
            }
            $average = $kwh / $months;
        }
        $data['average'] = round($average);
        $data['months'] = $months;
        $data['kwh'] = $kwh;
        $data['kwharr'] = $kwh_arr;
        return (object)$data;
    }

    function get_account_info() {
        $data = array();
        $sysid = $this->input->post('id');
        $info = get_active_account_info($sysid);


        $data['name'] = $info->name;
        $data['addr'] = $info->address;
        $data['servno'] = $info->servicenumber;
        $data['mtrno'] = $info->mtrno;
        $data['serial'] = $info->mtrserial;
        $data['infoarr'] = $info;
        return json_encode($data);
    }

    function get_customer_reading_data() {
        $data = array();
        $id = $this->input->post('id');
        $qry = $this->db->query("
            SELECT * FROM billing_reports_main
            WHERE acctid = $id
            ORDER BY byr DESC, byr DESC
        ");
        if($qry->num_rows()>0) {
            foreach($qry->result() as $row) {
                $ref = $row->batch;
                $button = '';
                $button .= '<a href="javascript:;" class="btn btn-default btn-sm inline"><i class="fa fa-print"></i></a>';
                $button .= '<a href="javascript:;" class="btn btn-info btn-sm inline"><i class="fa fa-search"></i></a>';
                $data['list'][] = array(
                    'expand' => btn_expand($row->sysid),
                    'year' => date_formating($row->byr, 'y', 'Y'),
                    'months' => date_formating($row->bmo, 'm', 'm-M'),
                    'kwh' => $row->kwhuse,
                    'reading' => round($row->prsrdg),
                    'amt' => number_format($row->current,2),
                    'duedate' => $row->duedate,
                    'ref' => $ref,
                    'payment' => '',
                    'control' => $button,
                );

            }
        }
        return json_encode($data);
    }

    function get_customer_reading_info() {
        $data = array();
        $id = $this->input->post('id');

        $get_billing_reports_main = $this->db->select('acctid, month, year, billno')
            ->from('billing_reports_main')
            ->where('sysid', $id)
            ->get()->row();
        if($get_billing_reports_main) {
            $acctid = $get_billing_reports_main->acctid;


            $qry = $this->db->select(
                '
                    billno, acctid, group, dist, lot, book, servno, mtr, mtrser, serial, name, addr, bmo, byr,
                    month, year, prvdte, prsdte, duedate, load, rate, prvrdg, prsrdg, multcd, kwhuse, genamt,
                    genamt1, trnamt, disamt, demamt, supamt, supper, mtramt, slamt, iccamt, iccsub, llramt,
                    llrsub, lldamt, misamt, envamt, framt, npcamt, iccsamt, papc, fitamt, genchg, genchg1, trnchg,
                    dischg, demchg, supchg, mtrchg, mtrper, slchg, mischg, envchg, npcchg, iccschg, fitchg, papcchg,
                    genvat, trnvat, disvat, slvat, othvat, appsur, surbal, current, overdue, totacc, totint, scdisc, dolpay
                '
                )
                ->from('billing_reports')
                ->where(array('acctid' => $acctid, 'billno' => $get_billing_reports_main->billno))
                ->get()->row();

            $html = '';

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


                $html .= '<div class="col-md-6 bill-data">';
                $html .= '<ul class="list-group summary column no-border">';
                $html .= '<li class="list-group-item"><span class="label text-danger bold col-md-6 text-bold text-align-left">PECO RELATED CHARGES</span>';
                $html .= '<span class="label text-danger bold col-md-3 text-align-right">PER KWH</span>';
                $html .= '<span class="label text-danger bold col-md-3 text-align-right">AMOUNT</span>';
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

                $html .= '<li class="list-group-item"><span class="label text-danger bold col-md-12  text-align-left text-bold">SUPPLIER RELATED CHARGES (PPC, PEDC)</span>';
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

                $html .= '<div class="col-md-6 bill-data">';
                $html .= '<ul class="list-group summary column no-border">';
                $html .= '<li class="list-group-item"><span class="label text-danger bold col-md-6 text-bold text-align-left">SUBSIDIES</span>';
                $html .= '<span class="label text-danger bold col-md-3 text-align-right">PER KWH</span>';
                $html .= '<span class="label text-danger bold col-md-3 text-align-right">AMOUNT</span>';
                $html .= '</li>';
                $html .= '<li class="list-group-item"><span class="label label-name col-md-6">Interclass Cross Subsidy</span>';
                $html .= '<span class="col-md-3 data text-align-right"></span>';
                $html .= '<span class="col-md-3 data text-align-right">'.number_format($iccsub, 2).'</span>';
                $html .= '</li>';
                $html .= '<li class="list-group-item"><span class="label label-name col-md-6">Lifeline Rate Subsidy</span>';
                $html .= '<span class="col-md-3 data text-align-right">'.$llrsub.'</span>';
                $html .= '<span class="col-md-3 data text-align-right">'.number_format($llramt, 2).'</span>';
                $html .= '</li>';

                $html .= '<li class="list-group-item"><span class="label text-danger bold col-md-12  text-align-left text-bold">TAXES AND UNIVERSAL CHARGES</span>';
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
            }else{
                $html = '<h4 class="text-warning"><i class="fa fa-warning"></i> No Record found!</h4>';
            }
        }
        $data['html'] = $html;
        return json_encode($data);
    }
}