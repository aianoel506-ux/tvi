<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->


<form role="form" class="form-horizontal" id="frm_filter_customers" action="#" method="post">	

    <div class="col-md-9">
        <div class="portlet light box tabbed table">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-search"></i>
                    <span class="caption-subject font-green-sharp bold uppercase" id="view_title"></span>
                    <span class="caption-helper" id="view_desc"></span>
                    <!-- <input class="form-control" id="pac-input" name="mapsearch" /> -->
                </div>
                <div class="tabbable-line pull-right" style="margin-right: -18px;">
                    <ul class="nav nav-tabs ">
                        <li class="active">
                            <a href="#tab_map" data-toggle="tab">
                                <i class="fa fa-map-o"></i> Map </a>
                        </li>
                        <li>
                            <a href="#tab_list" data-toggle="tab">
                                <i class="fa fa-navicon"></i> List </a>
                        </li>
                        <li>
                            <a href="#tab_chart" data-toggle="tab">
                                <i class="fa fa-line-chart"></i> Chart </a>
                        </li>
                        <li>
                            <a href="#tab_geoup" data-toggle="tab">
                                <i class="fa fa-gear"></i> Upload Geo List </a>
                        </li>

                    </ul>
                </div>
            </div>
            <div class="portlet-body" style="margin-top: -5px !important;">
                <div class="tab-content"  style="border-top: 1px solid rgba(0,0,0,0.05) !important">
                    <div class="tab-pane active" id="tab_map">
                        <div id="map" style="width: 100%; height: 500px;">Loading markers....</div>
                    </div>

                    <div class="tab-pane margin-top-20" id="tab_list">
                        <table id="tbl_cust_list" class="table table-hover table-bordered table-condensed table-striped margin-top-10 tbl-xs">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Servno</th>
                                    <th>MTR No.</th>
                                    <th>Name</th>
                                    <th>Address</th>
                                    <th>GDLB</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>

                    <div class="tab-pane" id="tab_chart">
                        <div id="chart1" style="height: 500px;"></div>
                    </div>

                    <div class="tab-pane" id="tab_geoup">
                        <div class="form-group" style="padding: 5px 30px;">
                            <label>Display Type: </label>
                            <select class="form-control" name="display" id="disp_type_input">
                                <option></option>
                                <option value="1">Houses</option>
                                <option value="2">Meters</option>
                            </select>
                        </div>
                        <div class="form-group" style="padding: 30px 30px;">
                            <input id="reqfiledrop" name="reqfiledrop" data-upload-url="<?php echo base_url('mrd/uploadexcelgeodata'); ?>" multiple class="file" type="file" data-preview-file-type="any"  />
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-wrench"></i>
                    <span class="caption-subject font-green-sharp bold uppercase">Filter</span>
                    <br>
                    <span class="caption-helper">Filter map view</span>
                </div>
            </div>
            <div class="portlet-body form">
                <div class="form-body">
                <div class="form-group form-md-line-input">
                    <div class="col-md-12">
                        <label>Display Type: </label>
                        <select class="form-control" name="display" id="disp_type">
                            <option></option>
                            <option value="1">Houses</option>
                            <option value="2">Meters</option>
                        </select>

                    </div>
                </div>

                <div class="form-group form-md-line-input">
                    <div class="col-md-12">
                        <label>Location: </label>
                        <input class="form-control" name="dist" id="dist_list" />

                    </div>
                </div>

                <div class="form-group form-md-line-input" style="margin-top: -20px">
                    <div class="col-md-12">
                        <label>Rate Class: </label>
                        <input class="form-control" name="class" id="rate_class" />
                    </div>
                </div>

                <div class="form-group form-md-line-input has-success margin-bottom-20">
                    <div class="col-md-12" style="display: inline-block;">
                        <div class="input-icon">
                            <input type="text" class="form-control" placeholder="Search..." name="specs">
                            <span class="help-block">Search Service No. / Name.. </span>
                            <i class="fa fa-search"></i>
                        </div>
                    </div>
                </div>

                </div>
                <div class="form-actions fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <button type="submit" id="search_btn" class="btn green">Search</button>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>
</form>