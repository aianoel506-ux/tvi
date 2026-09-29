
<style>
    .media {
        position: relative;
    }
    .media:hover {
        background: rgba(0,92,255,0.05);
    }
    .media-body {
        padding: 5px 5px;
        position: relative;
    }
    .media .media-number {
        position: absolute;
        top: 0px;
        left: 0px;
        float: left;
        background: #00a5bb;
        color: #fff;
        display: inline-block;
        width: auto;
        padding: 2px 2px;
        font-size: 9px;
    }

    .media .media-body .btn-group {
        visibility: hidden;
        position: absolute;
        top: 5px;
        right: 5px;
    }

    .media:hover .btn-group {
        visibility: visible;
    }

</style>

        <h3 class="page-title">
            <i class="fa <?php echo $pageicon; ?> fa-fw text-<?php echo $pageclass; ?>"></i><span class="text-<?php echo $pageclass; ?>"><?php echo $pagetitle; ?></span><small> view</small>
        </h3>


        <!-- START PAGE CONTENT-->
        <div class="portlet light">
            <div class="portlet-title tabbable-line">
                <div class="caption">
                    <i class="fa fa-users"></i> Table
                </div>
                <div class="col-md-3 pull-right">
                    <form action="./view" method="post">
                        <div class="input-group input-icon">
                            <i class="fa fa-search"></i>
                            <input name="servno" class="form-control" placeholder="Jump to..">
                            <span class="input-group-btn">
                            <button type="submit" class="btn btn-default">Go</button>
                            <div class="btn-group">
                                    <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="true" aria-expanded="true">
                                    <span id="btn_view_val">View</span>  <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu" role="menu" id="view_val_group">
                                        <li class="view-val" data-val="50">
                                            <a href="javascript:;">Top 50</a>
                                        </li>
                                        <li class="view-val" data-val="100">
                                            <a href="javascript:;">Top 100</a>
                                        </li>
                                        <li class="view-val" data-val="200">
                                            <a href="javascript:;">Top 200</a>
                                        </li>
                                        <li class="view-val" data-val="500">
                                            <a href="javascript:;">Top 500</a>
                                        </li>
                                        <li class="view-val" data-val="1000">
                                            <a href="javascript:;">Top 1000</a>
                                        </li>
                                        <li class="divider">
                                        </li>
                                        <li>
                                            <a href="javascript:;">Clear Views</a>
                                        </li>
                                    </ul>
                                </div>
                        </span>

                        </div>
                    </form>
                </div>
            </div>

            <div class="portlet-body">
                <div class="list-container">
                    <h3 class="text-align-center"><i class="fa fa-navicon"></i> Customers List..</h3>
                </div>
                <hr>
                <button class="btn btn-default btn-block hidden" id="btn_more_data">View More</button>
            </div>

        </div>


<script src="<?php echo base_url(); ?>assets/pages/crm/lists.js" type="text/javascript"></script>
<script>
    CRMLIST.list();
</script>

