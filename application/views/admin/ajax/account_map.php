<?php
/**
 * Created by PhpStorm.
 * User: ITD-SE
 * Date: 11/22/2018
 * Time: 1:44 PM
 */
    $id = $this->input->post('ids');
    $acct_info = get_active_account_info($id);
    if($acct_info) {
?>
        <style>
            .loading-box {
                padding: 10px 20px;
                position: absolute;
                top: 35%;
                height: 60px;
                left: 0px;
                right: 0px;
                z-index: 100;
                text-align:center;
                background: #fff;
                box-shadow: rgba(0,0,0,0.35) 0px 0px 50px;
            }
        </style>
        <div class="row">
            <div class="col-md-12 ">
                <div class="row">
                    <div class="col-md-3 tabbable-line">
                        <ul class="nav nav-tabs">
                            <li class="active">
                                <a href="#meter" data-toggle="tab" aria-expanded="true"><i class="fa fa-dashboard"></i> Meter</a>
                            </li>
                            <li class="">
                                <a href="#house" data-toggle="tab" aria-expanded="false"><i class="fa fa-home"></i> House</a>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-8">
                        <h5 class="pull-right">
                            <span style="width: 170px; display:inline-block;">Lat: <span id="lat" class="text-danger text-bold">0</span></span>
                            <span style="width: 170px; display:inline-block;">Long: <span id="lng" class="text-danger text-bold">0</span></span>
                        </h5>
                    </div>
                    <div class="col-md-1">
                        <button style="margin: 8px 5px;" id="print_map" class="btn btn-xs btn-outline red-flamingo pull-right"><i class="fa fa-print"></i> Print</button>
                    </div>
                </div>
            </div>
        </div>
        <div style="">
            <div id="loading_box" class="loading-box hidden"></div>
            <div id="map" style="height: 500px;"></div>
        </div>

        <script>
            PECO.handlerAccntMap(<?php echo $id; ?>, 2);
        </script>
<?php } ?>

