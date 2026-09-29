<div class="portlet light table">

    <div class="portlet-title ">
        <div class="caption">
            <div class="input-group">
                <input class="form-control" id="select2gdlb" placeholder="Select GDLB..." />
                <span class="input-group-btn">
                    <button class="btn btn-default" style="margin-left: 5px;">Get Data</button>
                    <button class="btn btn-default">Import To Reading</button>
                </span>
            </div>
        </div>
        <div class="tools">
            <a href="javascript:;" class="collapse" data-original-title="" title="">
            </a>
            <a href="#portlet-config" data-toggle="modal" class="config" data-original-title="" title="">
            </a>
            <a href="javascript:;" class="reload" data-original-title="" title="">
            </a>
        </div>
    </div>

    <div class="portlet-body">
        <table class="table table-hover table-striped tbl-sm" id="tbl_smart_meter_reading">
            <thead>
            <th>#</th>
            <th>Servno</th>
            <th>Meter#</th>
            <th>Serial</th>
            <th>Status</th>
            <th>Average KWH/m</th>
            <th>Average Load/m</th>
            <th>Last Reading</th>
            <th>Last Update</th>
            <th>Control</th>
            </thead>
            <tbody>
            <?php


            $ii = 0;
            for($i = 0; $i<=30; $i++) {
                $ii++;
                $rand_mtrno = rand(300, 53232);
                $rand_serial = rand(66855, 5323232);
                $rand_kwh = (rand(10, 30)/100);
                $rand_load = (rand(5.33, 30.99)/100);
                $rand_reading = rand(6000, 889588);
                $num_ = str_pad($i, 3, '0', STR_PAD_LEFT);
                echo '<tr>';
                echo '<td>'.$ii.'</td>';
                echo '<td>L3202'.$num_.'</td>';
                echo '<td class="number">'.$rand_mtrno.'</td>';
                echo '<td class="number">'.$rand_serial.'</td>';
                echo '<td><span class="label label-success"><i class="fa fa-check"></i> Active</span></td>';
                echo '<td class="number">'.$rand_kwh.'</td>';
                echo '<td class="number">'.$rand_load.'</td>';
                echo '<td class="number">'.$rand_reading.'</td>';
                echo '<td class="text-center">'.date('Y-m-d').'</td>';
                echo '<td>';
                echo '<button class="btn btn-default inline btn-xs" type="button"><i class="fa fa-refresh"></i></button>';
                echo '<button class="btn btn-warning inline btn-xs" type="button"><i class="fa fa-sign-out"></i></button>';
                echo '</td>';
                echo '</tr>';
            }
            ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    PECO.select2Basic($('#select2gdlb', document), 'query/select2gdlb', 'Select GDLB...');
    $('#tbl_smart_meter_reading', document).DataTable();
</script>

