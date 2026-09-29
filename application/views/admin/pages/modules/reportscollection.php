<?php
/**
 * Created by PhpStorm.
 * User: SE
 * Date: 5/16/2018
 * Time: 10:08 AM
 */

?>
<h3><i class="fa fa-bar-chart-o"></i> CNC Statistics <span class="text-info text-bold pull-right"><?php echo strtoupper(date('Y-M'));?></span></h3>
<div class="row">
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 green" href="#">
            <div class="visual">
                <i class="fa fa-check"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span id="servcnt" data-counter="counterup" data-value="1349">320</span>
                </div>
                <div class="desc"> Queue Count</div>
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
                    <span data-counter="counterup" data-value="45,6.14">45,6.14</span>M</div>
                <div class="desc"> Collection </div>
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
                    <span data-counter="counterup" data-value="730,7">730,7K</span>
                </div>
                <div class="desc"> Cash</div>
            </div>
        </a>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 purple" href="#">
            <div class="visual">
                <i class="fa fa-calendar"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span data-counter="counterup" data-value="44,8">44,8</span>K</div>
                <div class="desc"> Check </div>
            </div>
        </a>
    </div>
</div>

<div class="row">
    <?php
    $cwd_user_arr = array(
        array('user' => 'Soliva', 'serving' =>'S30591', 'status' => 'Billing'),
        array('user' => 'Zata', 'serving' =>'M13033', 'status' => 'Billing'),
        array('user' => 'Camacho', 'serving' =>'J32563', 'status' => 'Billing'),
    );

    $cwd_summart = array(
        array('types' => 'CAD', 'check' => '30252651.32', 'cash' => '125685.22'),
        array('types' => 'LEGAL', 'check' => '10232621', 'cash' => '236858.12'),
        array('types' => 'BILLING', 'check' => '3152655.32', 'cash' => '225658.2'),
        array('types' => 'SERVICES', 'check' => '1252651.85', 'cash' => '142535.11'),
    );
    ?>

    <div class="col-md-4">
        <h3>Summary <span class="btn-group pull-right"><button class="btn btn-default btn-round btn-sm"><i class="fa fa-refresh"></i> Refresh</button></span></h3>
        <table class="table table-hover table-striped" id="tbl_cwd_service">
            <thead>
            <th>Collection</th>
            <th>Check</th>
            <th>Cash</th>
            <th>Total</th>
            </thead>
            <tbody>
            <?php
            $sum_total = 0;
            $sum_total_accomp = 0;
            $sum_total_unaccomp = 0;
            foreach($cwd_summart as $row) {
                $total = $row['check'] + $row['cash'];
                $sum_total += $total;
                $sum_total_accomp += $row['check'];
                $sum_total_unaccomp +=  $row['cash'];

                ?>
                <tr>
                    <td><?php echo $row['types']; ?></td>
                    <td class="number"><?php echo number_format($row['check'], 2); ?></td>
                    <td class="number"><?php echo number_format($row['cash'], 2); ?></td>
                    <td class="number"><span class="text-danger text-bold"><?php echo number_format($total, 2); ?></span></td>
                </tr>
            <?php } ?>
            <tr>
                <th>Total</th>
                <th><?php echo number_format($sum_total_accomp, 2); ?></th>
                <th><?php echo number_format($sum_total_unaccomp, 2); ?></code></th>
                <th><span class="text-danger text-bold"><?php echo number_format($sum_total, 2); ?></span></th>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="col-md-8">
        <h3>CNC Staff Stats <span class="btn-group pull-right"><button class="btn btn-default btn-round btn-sm"><i class="fa fa-refresh"></i> Refresh</button></span></h3>
        <table class="table table-hover table-striped" id="tbl_cwd_service">
            <thead>
            <th><i class="fa fa-reorder"></i></th>
            <th>User</th>
            <th>Now Serving</th>
            <th>Served</th>
            <th>Collection</th>
            </thead>
            <tbody>
            <?php
            $i = 1;
            foreach($cwd_user_arr as $row) {
                $served_rand = rand(100, 420);

                ?>


                <tr>
                    <td><?php echo $i; ?></td>
                    <td><?php echo $row['user']; ?></td>
                    <td><code><?php echo $row['serving']; ?></code></td>
                    <td id="served"><?php echo $served_rand; ?></td>
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

<script>
    var total_served = 0;
    $('#tbl_cwd_service tr td#served').each(function(){
        var serv_cnt = $(this).text();
        total_served += Number(serv_cnt);
    });
    $('#servcnt').text(total_served);
</script>


