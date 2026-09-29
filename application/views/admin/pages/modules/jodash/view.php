<?php
/**
 * Created by PhpStorm.
 * User: DUDEZKIE
 * Date: 6/21/2019
 * Time: 11:23 AM
 */

if($dataid > 0) {

    $acct_info = get_active_account_info($dataid);
    $year = '';
    $month = '';



    $qry_av = $this->db->query("
                SELECT month, year, prsrdg, prvrdg, prsdte, prvdte, kwhuse, batch 
                FROM billing_reports_main 
                WHERE acctid = $dataid AND batch != 'LATEBILL'
                ORDER BY prsdte DESC
                LIMIT 12
            ");

    $mrd_lastfindings = mrd_get_last_findings($dataid, $acct_info->mtrno);
    $last_findings = '';
    $last_findings_date = '';
    if($mrd_lastfindings->qry) {
        $last_findings = $mrd_lastfindings->code;
        $last_findings_date = $mrd_lastfindings->date;
    }



    // READING HISTORY TABLE
    $reading_hist_row = '';
    $kwh_av = 0;
    $kwh_total = 0;
    $kwh_cnt = 0;
    $kwh_num = $qry_av->num_rows();
    if ($kwh_num > 0) {
        foreach ($qry_av->result() as $row) {
            $prsdte = new DateTime($row->prsdte);
            $prvdte = new DateTime($row->prvdte);
            $prsdte_format = $prsdte->format('Y/m/d');
            $prvdte_format = $prvdte->format('Y/m/d');
            $period = $prvdte_format . '-' . $prsdte_format;

            $reading_hist_row .= '<tr>';
            $reading_hist_row .= '<td>' . $period . '</td>';
            $reading_hist_row .= '<td>' . $row->prvrdg . '</td>';
            $reading_hist_row .= '<td>' . $row->prsrdg . '</td>';
            $reading_hist_row .= '<td>' . number_format($row->kwhuse) . '</td>';
            $reading_hist_row .= '<td>' . $row->batch . '</td>';
            $reading_hist_row .= '</tr>';
            if($row->kwhuse > 0) {
                $kwh_cnt += 1;
                $kwh_total += $row->kwhuse;
            }
        }
        if($kwh_cnt) {
            $kwh_av = $kwh_total / $kwh_cnt;
        } else {
            $kwh_av = $kwh_total;
        }
    }

    echo '<h3 class="pull-right font-red-flamingo bold">'.str_pad($dataid, 6, '0', STR_PAD_LEFT).'</h3>';
    echo btn_back_to_list('table', 'btn btn-default', 'Job Order List', 'fa fa-arrow-left');



    ?>
    <hr>


    <form action="<?php echo base_url(); ?>" method="post">
        <div class="row">
            <div class="col-md-4">
                <div class="portlet light">
                    <div class="portlet-title">
                        <div class="caption">
                            <span class="font-red-haze"><i class="fa fa-info"></i> Job Order Details</span>
                        </div>
                    </div>
                    <div class="portlet-body">

                        <?php

                        // INFORMATION
                        $gdlb = get_acct_gdlb($dataid);
                        echo '<h5 class="text-info"><i class="fa fa-map-marker fa-fw"></i> <b>Location Details</b></h5>';
                        echo '<ul class="list-group summary column border-top list-group-xs">';
                        echo '<li class="list-group-item"><span class="col-md-5 label-name">Brgy / Streen Name</span><span class="col-md-7 label-default data">' . $gdlb->ADDR . '</span></li>';
                        echo '<li class="list-group-item"><span class="col-md-5 label-name">District</span><span class="col-md-7 label-default data">' . $gdlb->DISTNAME . '</span></li>';
                        echo '<li class="list-group-item"><span class="col-md-5 label-name">GDLB</span><span class="col-md-7 label-default data">' . $gdlb->GDLB . '</span></li>';
                        echo '<li class="list-group-item"><span class="col-md-5 label-name">Map Updated: </span><span class="col-md-7 label-default data">2016-01-01</span></li>';
                        echo '<li class="list-group-item"><span class="col-md-5 label-name">Ready By</span><span class="col-md-7 label-default data"></span></li>';
                        echo '<li class="list-group-item"><span class="col-md-5 label-name">Year</span><span class="col-md-7 label-default data">'.$year.'</span></li>';
                        echo '<li class="list-group-item"><span class="col-md-5 label-name">Month</span><span class="col-md-7 label-default data">'.date_formating($month, '!m', 'M').'</span></li>';
                        echo '<li class="list-group-item"><span class="col-md-5 label-name">Last Findings</span><span class="col-md-7 label-default data">'.$last_findings.'</span></li>';
                        echo '<li class="list-group-item"><span class="col-md-5 label-name">Finding\'s Date</span><span class="col-md-7 label-default data">'.$last_findings_date.'</span></li>';
                        echo '<li class="list-group-item"><span class="col-md-5 label-name">Ave. Kwh: </span><span class="col-md-7 label-default data">' . number_format($kwh_av) . '</span></li>';
                        echo '</ul>';
                        ?>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="portlet light">
                    <div class="portlet-title">
                        <div class="caption">
                            <span class="font-red-haze"><i class="fa fa-edit"></i> Transaction</span>
                        </div>
                    </div>
                    <div class="portlet-body form">

                        <div class="form-body">
                            <div class="form-group">
                                <label>Status</label>
                                <input class="form-control" placeholder="Select status.." id="select2status" />
                            </div>
                            <div class="form-group">
                                <label>Asset Issuance</label>
                                <input class="form-control" placeholder="Search Asset Number.." id="select2asset" />
                            </div>
                            <div class="form-group">
                                <label>Accomplished By</label>
                                <input class="form-control" placeholder="Select Employee" id="accomplylastname" />
                            </div>
                            <div class="form-group">
                                <label>Accomplished Date</label>
                                <input class="form-control" placeholder="Select Employee" id="accomplydate" />
                            </div>
                            <div class="form-group">
                                <label>Remarks</label>
                                <input class="form-control" placeholder="Remarks.. " id="" />
                            </div>
                        </div>

                        <div class="form-actions">
                            <div class="btn-group pull-right">
                                <button type="submit" class="btn green">Save</button>
                                <button type="button" class="btn default">Cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="portlet light">
                    <div class="portlet-title">
                        <div class="caption">
                            <span class="font-red-haze"><i class="fa fa-history"></i> History</span>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <table class="table table-hover table-condensed table-striped" id="tbl_jo_trail">
                            <thead>
                            <th>Date Created</th>
                            <th>Transactions</th>
                            </thead>
                            <tbody>


                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </form>

<?php }else{
    page_data_notfound();
}
?>