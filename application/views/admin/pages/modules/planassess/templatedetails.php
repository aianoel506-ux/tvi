<?php
$id = $this->input->post('id');
?>

<style type="text/css">
    .table tr#details td {
        background: transparent !important;
    }
</style>

<div class="row">
    <div class="col-md-12">
        <h3 class="text-center bold" id="name_template"></h3>
    </div>
    <div class="col-md-12">
        <div class="list-group summary column">
            <div class="row" style="border: transparent !important; border-bottom: 1px solid #ddd !important;">
                <div class="list-group-item col-md-4">
                    <span class="col-md-6 label-name text-primary bold" >Panel Type </span>
                    <span class="col-md-6 font-red-flamingo bold number" id="paneltypes_template"></span>
                </div>
                <div class="list-group-item col-md-4" >
                    <span class="col-md-6 label-name text-primary bold" >Number of Panels </span>
                    <span class="col-md-6 font-red-flamingo bold number" id="numberofpanels_template"></span>
                </div>
                <div class="list-group-item col-md-4" >
                    <span class="col-md-6 label-name text-primary bold" >Number of Strings </span>
                    <span class="col-md-6 font-red-flamingo bold number" id="numberofstrings_template"></span>
                </div>
            </div>
            <div class="row" style="border: transparent !important; border-bottom: 1px solid #ddd !important;">
                <div class="list-group-item  col-md-6">
                    <span class="col-md-6 label-name text-primary bold" >Panels per String </span>
                    <span class="col-md-6 font-red-flamingo bold number" id="panelsperstring_template"></span>
                </div>
                <div class="list-group-item  col-md-6">
                    <span class="col-md-6 label-name text-primary bold" >Inverter Size(s) </span>
                    <span class="col-md-6 font-red-flamingo bold number" id="invertersize_template"></span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="tabbable-line">
    <ul class="nav nav-tabs ">
        <li class="active">
            <a href="#sps_components" data-toggle="tab" aria-expanded="true" data-id="1"> Components </a>
        </li>
        <li class="">
            <a href="#sps_accessories" data-toggle="tab" aria-expanded="true" data-id="2"> Accessories </a>
        </li>
        <li class="">
            <a href="#sps_consumables" data-toggle="tab" aria-expanded="true" data-id="3"> Consumables </a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade in active" id="sps_items">
            <table class="table table-striped table-hover types table-condensed table-sm" id="tbl_template_components">
                <thead>
                <th>#</th>
                <th>Item</th>
                <th>Qty</th>
                <th>Unit</th>
                <th>Price</th>
                <th>Total</th>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="list-group summary column list-group-lg">
<div class="row" style="border: transparent !important;">
    <div class="list-group-item col-md-6" style="border: transparent !important;">
        <span class="col-md-6 label-name" >Total 10-Year Plan </span>
        <span class="col-md-6 font-red-flamingo bold text-right h4" id="total_10years_template" style="padding: 0px 20px !important;"></span>
    </div>
    <div class="list-group-item col-md-6" style="border: transparent !important;">
        <span class="col-md-6 label-name" >Total 5-Year Plan </span>
        <span class="col-md-6 font-red-flamingo bold  text-right h4" id="total_5years_template" style="padding: 0px 20px !important;"></span>
    </div>
</div>
</div>

<script type="text/javascript">
    INSPECTION.templatedetails(<?php echo $id;?>);
</script>