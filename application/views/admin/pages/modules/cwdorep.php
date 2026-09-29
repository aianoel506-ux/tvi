<?php
/**
 * Created by PhpStorm.
 * User: SE
 * Date: 5/16/2018
 * Time: 10:08 AM
 */

?>
<h3><i class="fa fa-bar-chart-o"></i> CWDO Statistics <span class="text-info text-bold pull-right"><?php echo strtoupper(date('Y-M'));?></span></h3>
<div class="row">
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 green" href="#">
            <div class="visual">
                <i class="fa fa-check"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span data-counter="counterup" data-value="1349">1349</span>
                </div>
                <div class="desc"> Resolved</div>
            </div>
        </a>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 red" href="#">
            <div class="visual">
                <i class="fa fa-bar-chart-o"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span data-counter="counterup" data-value="132">132</span> Mix</div>
                <div class="desc"> Unresolved </div>
            </div>
        </a>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 blue" href="#">
            <div class="visual">
                <i class="fa fa-shopping-cart"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span data-counter="counterup" data-value="22">22</span>
                </div>
                <div class="desc"> Walk-in </div>
            </div>
        </a>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 purple" href="#">
            <div class="visual">
                <i class="fa fa-calendar"></i>
            </div>
            <div class="details">
                <div class="number"> +
                    <span data-counter="counterup" data-value="89">89</span> Mix</div>
                <div class="desc"> Scheduled </div>
            </div>
        </a>
    </div>
</div>

<div class="row">
    <?php
        $cwd_user_arr = array(
            array('user' => 'Llanora', 'serving' =>'S30591', 'status' => 'Pending'),
            array('user' => 'Margasino', 'serving' =>'M13033', 'status' => 'Pending'),
            array('user' => 'Tuble', 'serving' =>'J32563', 'status' => 'Resolved'),
        );

        $cwd_summart = array(
            array('types' => 'Over Read', 'accomp' => '20', 'unaccomp' => '5'),
            array('types' => 'Under Read', 'accomp' => '22', 'unaccomp' => '2'),
            array('types' => 'Double Payment', 'accomp' => '3', 'unaccomp' => '0'),
            array('types' => 'Applied Payment', 'accomp' => '1', 'unaccomp' => '0'),
            array('types' => 'Personel', 'accomp' => '21', 'unaccomp' => '14'),
            array('types' => 'Facilities', 'accomp' => '0', 'unaccomp' => '0'),
        );
    ?>

    <div class="col-md-4">
        <h3>Summary <span class="btn-group pull-right"><button class="btn btn-default btn-round btn-sm"><i class="fa fa-refresh"></i> Refresh</button></span></h3>
        <table class="table table-hover table-striped" id="tbl_cwd_service">
            <thead>
            <th>Types</th>
            <th>Accomplished</th>
            <th>Unaccomplished</th>
            <th>Total</th>
            </thead>
            <tbody>
            <?php
            $sum_total = 0;
            $sum_total_accomp = 0;
            $sum_total_unaccomp = 0;
            foreach($cwd_summart as $row) {
                $total = $row['accomp'] + $row['unaccomp'];
                $sum_total += $total;
                $sum_total_accomp += $row['accomp'];
                $sum_total_unaccomp +=  $row['unaccomp'];

            ?>
                <tr>
                    <td><?php echo $row['types']; ?></td>
                    <td><code><?php echo $row['accomp']; ?></code></td>
                    <td><?php echo $row['unaccomp']; ?></code></td>
                    <td><span class="text-danger text-bold"><?php echo $total; ?></span></td>
                </tr>
            <?php } ?>
            <tr>
                <th>Total</th>
                <th><code><?php echo $sum_total_accomp; ?></code></th>
                <th><?php echo $sum_total_unaccomp; ?></code></th>
                <th><span class="text-danger text-bold"><?php echo $sum_total; ?></span></th>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="col-md-8">
        <h3>CWD Staff Stats <span class="btn-group pull-right"><button class="btn btn-default btn-round btn-sm"><i class="fa fa-refresh"></i> Refresh</button></span></h3>
        <table class="table table-hover table-striped" id="tbl_cwd_service">
            <thead>
                <th><i class="fa fa-reorder"></i></th>
                <th>User</th>
                <th>Now Serving</th>
                <th>Resolved Immediately</th>
                <th>Served</th>
                <th>Status</th>
            </thead>
            <tbody>
            <?php
            $i = 1;
                foreach($cwd_user_arr as $row) {
                    $served_rand = rand(5, 22);

             ?>
                    <tr>
                        <td><?php echo $i; ?></td>
                        <td><?php echo $row['user']; ?></td>
                        <td><code><?php echo $row['serving']; ?></code></td>
                        <td><code><?php echo $served_rand;?></code></td>
                        <td><?php echo $served_rand; ?></td>
                        <td><span class="label label-danger"><?php echo $row['status']; ?></span></td>
                    </tr>

            <?php
                    $i++;
                }
            ?>
            </tbody>
        </table>
    </div>
</div>


