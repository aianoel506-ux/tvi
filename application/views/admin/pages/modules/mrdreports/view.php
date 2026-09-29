<?php

?>

<div class="row">
    <div class="col-md-12">
        <div class="tabbable-line">
            <ul class="nav nav-tabs ">
                <li class="active">
                    <a href="#summary" data-toggle="tab"> Summary </a>
                </li>
                <li>
                    <a href="#accomplishments" data-toggle="tab"> Reader Accomplishments </a>
                </li>
                <li>
                    <a href="#analysisreport" data-toggle="tab"> Analysis Report</a>
                </li>
                <li>
                    <a href="#analysisreport" data-toggle="tab"> Re-Analysis</a>
                </li>
                <li>
                    <a href="#msuhno" data-toggle="tab"> MSU / HNO Reports</a>
                </li>
                <li>
                    <a href="#zeroreadingaging" data-toggle="tab"> Zero Reading Aging</a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active fade in" id="summary">
                    <h4>Weekly Summary</h4>
                    <table class="table table-bordered table-hover table-condensed tbl-sm" id="summary">
                        <thead>
                        <th>Reading Findings</th>
                        <th>Meter</th>
                        <th>Reader 1</th>
                        <th>Reader 2</th>
                        <th>Reader 3</th>
                        <th>Reader 4</th>
                        <th>Total</th>
                        </thead>
                        <tbody>

                        <?php
                        $total_grand = 0;
                        $total_meter_grand = 0;
                        $rand_1_total = 0;
                        $rand_2_total = 0;
                        $rand_3_total = 0;
                        $rand_4_total = 0;
                        $rand_5_total = 0;
                        $rand_6_total = 0;
                        $qry_findings = $this->db->select()->from('meter_reading_findings')->get();
                        if($qry_findings->num_rows()>0) {
                            foreach($qry_findings->result() as $row) {
                                $rand_1 = rand(0, 30);
                                $rand_2 = rand(0, 30);
                                $rand_3 = rand(0, 30);
                                $rand_4 = rand(0, 30);
                                $rand_5 = rand(0, 30);
                                $total = ($rand_1 + $rand_2 + $rand_3 + $rand_3 + $rand_4 + $rand_5);
                                $total_grand += $total;

                                $meter = $total + rand(30, 40);
                                $total_meter_grand += $meter;



                                $rand_1_total += $rand_1;
                                $rand_2_total += $rand_2;
                                $rand_3_total += $rand_3;
                                $rand_4_total += $rand_4;
                                $rand_5_total += $rand_5;

                                echo '<tr>';
                                echo '<td>';
                                echo '<div class="row">';
                                echo '<span class="col-md-2 text-bold">'.$row->codes.'</span>';
                                echo '<span class="col-md-10">'.$row->descriptions.'</span>';
                                echo '</div>';
                                echo '</td>';
                                echo '<td>'.$meter.'</td>';
                                echo '<td>'.$rand_1.'</td>';
                                echo '<td>'.$rand_2.'</td>';
                                echo '<td>'.$rand_4.'</td>';
                                echo '<td>'.$rand_5.'</td>';
                                echo '<td>'.$total.'</td>';
                                echo '</tr>';
                            }
                        }
                        ?>

                        </tbody>
                        <tr>
                        <th>Total</th>
                        <th><?php echo $rand_1_total; ?></th>
                        <th><?php echo $rand_2_total; ?></th>
                        <th><?php echo $rand_3_total; ?></th>
                        <th><?php echo $rand_4_total; ?></th>
                        <th><?php echo $rand_5_total; ?></th>
                        <th><?php echo number_format($total_grand); ?></th>
                        </tr>
                    </table>
                    <hr>
                    <div class="row">

                        <div  class="pull-right col-md-4">
                            <ul class="list-group summary column">
                                <li class="list-group-item">
                                    <span class="col-md-5 label-name">Previous week Total</span>
                                    <span class="col-md-7 label-default number"><?php echo number_format($total_grand - rand(20, 30)); ?></span>
                                </li>
                                <li class="list-group-item">
                                    <span class="col-md-5 label-name">This week total</span>
                                    <span class="col-md-7 label-default number"><?php echo number_format($total_grand); ?></span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade in" id="accomplishments">
                    <table class="table table-bordered table-hover" id="tbl_reader_accomplishments">
                        <thead>
                        <th>Meter Reader</th>
                        <th>GDLB Read</th>
                        <th>Total Cust.</th>
                        <th>Read</th>
                        <th>Recheck</th>
                        <th>Billed</th>
                        <th>Unbilled</th>
                        <th>Errors</th>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Abella</td>
                                <td>2-M-3-02</td>
                                <td>322</td>
                                <td>314</td>
                                <td>15</td>
                                <td>310</td>
                                <td>12</td>
                                <td>4</td>
                            </tr>
                        </tbody>
                        <tr>

                        <th>Total</th>
                        <th></th>
                        <td>322</td>
                        <td>314</td>
                        <td>15</td>
                        <td>310</td>
                        <td>12</td>
                        <td>4</td>
                        </tr>
                    </table>
                </div>
                <div class="tab-pane fade in" id="analysisreport">
                    <table class="table table-bordered table-hover" id="tbl_analysis_reports">
                        <thead>
                        <th>Meter Reader</th>
                        <th>GDLB Read</th>
                        <th>Total Cust.</th>
                        <th>Read</th>
                        <th>Recheck</th>
                        <th>Errors</th>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <div class="tab-pane fade in" id="msuhno">
                    <table class="table table-bordered table-hover tbl-sm" id="tbl_analysis_reports">
                        <thead>
                        <th><i class="fa fa-reorder"></i></th>
                        <th>Servno</th>
                        <th>MTR No.</th>
                        <th>Serial No.</th>
                        <th>Findings</th>
                        <th>Count</th>
                        </thead>
                        <tbody>
                        <?php
                        $qry_cust = $this->db->select()->from('customer_accounts_main')
                            ->limit(50)
                            ->where('status', 1)
                            ->get();
                        if($qry_cust->num_rows()>0) {
                            $num = 1;
                            foreach ($qry_cust->result() as $row) {
                                $rand = rand(0, 3);
                                $class = ($rand==3) ? 'danger' : '';
                                echo '<tr class="'.$class.'">';
                                echo '<td>'.$num++.'</td>';
                                echo '<td>'.$row->servicenumber.'</td>';
                                echo '<td>'.$row->mtrno.'</td>';
                                echo '<td>'.$row->mtrserial.'</td>';
                                echo '<td>HNO</td>';
                                echo '<td>'.$rand.'</td>';
                                echo '</tr>';
                            }
                        }
                        ?>
                        </tbody>
                    </table>
                </div>


                <div class="tab-pane fade in" id="zeroreadingaging">
                    <table class="table table-bordered table-hover tbl-sm" id="tbl_zeroreading_aging">
                        <thead>
                        <th><i class="fa fa-reorder"></i></th>
                        <th>Reader</th>
                        <th>60 Days</th>
                        <th>90 Days</th>
                        <th>120 Days</th>
                        <th>Current</th>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">

        <hr>
    </div>
</div>

<script>
    $('.table').each(function(){
        PECO.DTDefault($(this), 'No data yet');
    });
</script>