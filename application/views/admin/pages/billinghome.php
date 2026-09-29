<?php
/**
 * Created by PhpStorm.
 * User: ITD-SE
 * Date: 9/17/2018
 * Time: 2:07 PM
 */

?>
<div class="page-content-wrapper animated fadeIn fast">
    <div class="page-content">
        <div class="page-bar">
            <ul class="page-breadcrumb">
                <li>
                    <i class="fa fa-home"></i>
                    <a href="index.html">Home</a>
                    <i class="fa fa-angle-right"></i>
                </li>
                <li>
                    <a href="#">Admin</a>
                    <i class="fa fa-angle-right"></i>
                </li>
                <li>
                    <a href="#">Billing Home</a>
                </li>
            </ul>

        </div>
        <!-- END PAGE HEADER-->
        <!-- BEGIN PAGE CONTENT--><!-- END PAGE CONTENT-->

        <h3><i class="fa fa-file-text-o font-green-haze"></i> Billing A/R </h3>
        <div class="portlet light ">
            <div class="portlet-title">
                <div class="caption">
                    <h3 class="font-green-haze">Top 50</h3>
                </div>
            </div>
            <div class="portlet-body">
                <table class="table table-hover table-bordered table-condensed" id="tbl_top_50">
                    <thead>
                    <th>#</th>
                    <th>Servno</th>
                    <th>Mtr</th>
                    <th>Name</th>
                    <th>Address</th>
                    <th>Overdue</th>
                    <th>Current</th>
                    <th></th>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="form-actions">
                <button class="btn btn-default">Save</button>
            </div>
        </div>
    </div>
</div>
