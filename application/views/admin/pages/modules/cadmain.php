
<style>
    .tile .fa {
        font-size: 3em;
        position: absolute;
        top: 5px;
        right: 5px;
        z-index: 1;
        opacity: 0.5;
    }
</style>

        <h3 class="page-title">
            <i class="fa <?php echo $pageicon; ?> fa-fw text-<?php echo $pageclass; ?>"></i><span class="text-<?php echo $pageclass; ?>"><?php echo $pagetitle; ?></span><small> view</small>
        </h3>
        <div class="portlet box blue">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-wrench"></i>Setup
                </div>
            </div>
            <div class="portlet-body" style="min-height: 400px;">
                <div class="tabbable tabbable-tabdrop">
                    <ul class="nav nav-tabs">
                        <li class="dropdown pull-right tabdrop">
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#" aria-expanded="false"><i class="fa fa-ellipsis-v"></i>&nbsp;<i class="fa fa-angle-down"></i> <b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="#tab6" data-toggle="tab">Section 6</a>
                                </li>
                                <li>
                                    <a href="#tab7" data-toggle="tab">Section 7</a>
                                </li>
                                <li>
                                    <a href="#tab8" data-toggle="tab">Section 8</a>
                                </li>
                            </ul>
                        </li>

                        <li class="active">
                            <a href="#requirements" data-toggle="tab" aria-expanded="true">Requirements</a>
                        </li>
                        <li class="">
                            <a href="#tab2" data-toggle="tab" aria-expanded="false">Services</a>
                        </li>
                        <li class="">
                            <a href="#tab3" data-toggle="tab" aria-expanded="false">Discounts</a>
                        </li>



                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="requirements">
                            <div class="col-md-4">
                                <form id="frm_get_requirements" class="form-horizontal" action="<?php echo base_url('admin/getcadrequirements'); ?>" method="post">
                                    <div class="well">
                                    <div class="form-group  form-md-line-input" >
                                        <label class="col-md-3 control-label" for="acct_rate">Add Requirements</label>
                                        <div class="col-md-9">
                                            <input id="acct_req" name="reqid" type="text" class="form-control input-sm data-entry" placeholder="Additional Requirements">
                                        </div>
                                    </div>
                                    </div>
                                        <div class="form-group margin-top-20 form-md-line-input">
                                            <label class="col-md-3 control-label" for="stat_conn">Status of connection</label>
                                            <div class="col-md-9">
                                                <!-- accttype -->
                                                <input required id="stat_conn" name="statconn" type="text" class="form-control input-sm data-entry" placeholder="Click to add Customer's Status Connection">
                                            </div>
                                        </div>
                                        <!-- Customer Requirements Listing -->
                                        <div class="form-group margin-top-20 form-md-line-input">
                                            <label class="col-md-3 control-label" for="owner_type">Type of Owner</label>
                                            <div class="col-md-9">
                                                <!-- conntype -->
                                                <input required id="owner_type" name="ownertype" type="text" class="form-control input-sm " placeholder="Click to add Customer's Owner Type">
                                            </div>
                                        </div>
                                        <div class="form-group margin-top-20 form-md-line-input">
                                            <label class="col-md-3 control-label" for="loc_type">Type of Location</label>
                                            <div class="col-md-9">
                                                <input required id="loc_type" name="loctype" type="text" class="form-control input-sm " placeholder="Click to add Customer's Location Type">
                                            </div>
                                        </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="btn-group pull-right">
                                                <button class="btn btn-default" type="button" id="btn_reset"><i class="fa fa-refresh fa-fw"></i> Reset</button>
                                                <button class="btn btn-default" type="button" id="btn_add"><i class="fa fa-plus fa-fw"></i> Add</button>
                                                <button class="btn btn-primary" type="submit" id="btn_get"><i class="fa fa-search fa-fw"></i> Get</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <div class="col-md-8">
                                <h3>Requirements List</h3>
                                <div class="tiles">

                                    <div class="tile bg-green-meadow connection" style="height: 75px; width: 31% !important;">
                                        <i class="fa fa-link"></i>
                                        <div class="tile-object">
                                            <div class="name">

                                            </div>
                                            <div class="number">

                                            </div>
                                        </div>
                                    </div>
                                    <div class="tile bg-green-meadow ownership" style="height: 75px; width: 31% !important;">
                                        <i class="fa fa-user"></i>
                                        <div class="tile-object">
                                            <div class="name">

                                            </div>
                                            <div class="number">

                                            </div>
                                        </div>
                                    </div>
                                    <div class="tile bg-green-meadow location" style="height: 75px; width: 31% !important;">
                                        <i class="fa fa-map-marker"></i>
                                        <div class="tile-object">
                                            <div class="name">

                                            </div>
                                            <div class="number">

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <table class="table table-hover table-condensed table-striped" id="tbl_req_list">
                                    <thead>
                                    <th><i class="fa fa-navicon"></i> </th>
                                    <th>Code</th>
                                    <th>Descriptions</th>
                                    <th width="50px">Control</th>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane" id="tab2">
                            <p>
                                Howdy, I'm in Section 2.
                            </p>
                        </div>
                        <div class="tab-pane" id="tab3">
                            <p>
                                Howdy, I'm in Section 3.
                            </p>
                        </div>
                        <div class="tab-pane" id="tab4">
                            <p>
                                Howdy, I'm in Section 4.
                            </p>
                        </div>
                        <div class="tab-pane" id="tab5">
                            <p>
                                Howdy, I'm in Section 5.
                            </p>
                        </div>
                        <div class="tab-pane" id="tab6">
                            <p>
                                Howdy, I'm in Section 6.
                            </p>
                        </div>
                        <div class="tab-pane" id="tab7">
                            <p>
                                Howdy, I'm in Section 7.
                            </p>
                        </div>
                        <div class="tab-pane" id="tab8">
                            <p>
                                Howdy, I'm in Section 8.
                            </p>
                        </div>
                        <div class="tab-pane" id="tab9">
                            <p>
                                Howdy, I'm in Section 9.
                            </p>
                        </div>
                    </div>
                </div>
                <p>
                    &nbsp;
                </p>
                <p>
                    &nbsp;
                </p>
            </div>
        </div>


<script type="text/javascript" src="<?php echo base_url(); ?>assets/pages/cad/main.js" ></script>

<script>
    CADMAIN.requirements();
</script>