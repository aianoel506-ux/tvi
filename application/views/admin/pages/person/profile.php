<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/admin/pages/css/profile.css">

<?php
/**
 * Created by PhpStorm.
 * User: ITD-SE
 * Date: 7/3/2018
 * Time: 4:50 PM
 */


$details = get_person_info($personid);


?>
<div class="page-content-wrapper animated fadeIn fast">
    <div class="page-content  margin-top-10">

        <div class="page-bar">
            <?php echo create_breadcrumb($fullname); ?>
            <div class="page-toolbar">
            </div>
        </div>

        <div class="profile-sidebar">

            <div class="portlet light profile-sidebar-portlet">
                <!-- SIDEBAR USERPIC -->
                <div class="profile-userpic">
                    <img src="<?php echo ($details->qry) ? $details->info->pics : ''; ?>" class="img-responsive" alt=""> </div>
                <!-- END SIDEBAR USERPIC -->
                <!-- SIDEBAR USER TITLE -->
                <div class="profile-usertitle">
                    <h4><?php echo $fullname; ?></h4>
                    <div><?php echo ($details->qry) ? $details->info->gender : ''; ?>, <?php echo ($details->qry) ? $details->info->birthdate : ''; ?></div>
                    <div class="profile-usertitle-job"><?php echo ($details->qry) ? $details->info->contact : ''; ?></div>
                </div>
                <!-- END SIDEBAR USER TITLE -->
                <!-- SIDEBAR BUTTONS -->
                <div class="profile-userbuttons">

                </div>
                <!-- END SIDEBAR BUTTONS -->
                <!-- SIDEBAR MENU -->
                <div class="profile-usermenu">
                    <ul class="nav">
                        <li class="active">
                            <a href="#timeline" data-toggle="tab"><i class="icon-home"></i> Timeline </a>
                        </li>
                        <li>
                            <a href="#overview" data-toggle="tab"><i class="icon-home"></i> Overview </a>
                        </li>
                    </ul>
                </div>
                <!-- END MENU -->
            </div>

            <div class="portlet light ">
                <!-- STAT -->
                <div class="row list-separated profile-stat">
                    <div class="col-md-4 col-sm-4 col-xs-6">
                        <div class="uppercase profile-stat-title"> 37 </div>
                        <div class="uppercase profile-stat-text"> Attributes </div>
                    </div>
                    <div class="col-md-4 col-sm-4 col-xs-6">
                        <div class="uppercase profile-stat-title"> 51 </div>
                        <div class="uppercase profile-stat-text"> Transactions </div>
                    </div>
                    <div class="col-md-4 col-sm-4 col-xs-6">
                        <div class="uppercase profile-stat-title"> 61 </div>
                        <div class="uppercase profile-stat-text"> Uploads </div>
                    </div>
                </div>
                <!-- END STAT -->
                <div>
                    <h4 class="profile-desc-title"><?php echo ($details->qry) ? $details->info->city : ''; ?>, <?php echo ($details->qry) ? $details->info->district : ''; ?></h4>
                    <span class="profile-desc-text"><?php echo ($details->qry) ? $details->info->addrspec : ''; ?></span>
                    <div class="margin-top-20 profile-desc-link">
                        <i class="fa fa-mobile"></i>
                        <a href="javascript::"><?php echo ($details->qry) ? $details->info->mobilephone : ''; ?></a>
                    </div>
                    <div class="margin-top-20 profile-desc-link">
                        <i class="fa fa-phone"></i>
                        <a href="javascript:;"><?php echo ($details->qry) ? $details->info->telephone : ''; ?></a>
                    </div>
                    <div class="margin-top-20 profile-desc-link">
                        <i class="fa fa-envelope"></i>
                        <a href="<?php echo ($details->qry) ? 'mailto:'.$details->info->personalemail : 'javascript:;'; ?>"><?php echo ($details->qry) ? $details->info->personalemail : ''; ?></a>
                    </div>
                </div>
            </div>


        </div>

        <div class="profile-content tab-content">

            <div class="tab-pane fade in active" id="timeline">
                <div class="portlet light portlet-fit ">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="icon-microphone font-green"></i>
                            <span class="caption-subject bold font-green uppercase">Timeline</span>
                            <span class="caption-helper">person timeline</span>
                        </div>
                        <div class="actions">
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="timeline">
                            <!-- TIMELINE ITEM -->
                            <div class="timeline-item">
                                <div class="timeline-badge">
                                    <img class="timeline-badge-userpic" src="<?php echo base_url(); ?>assets/global/img/admin_pic.png"> </div>
                                <div class="timeline-body">
                                    <div class="timeline-body-arrow"> </div>
                                    <div class="timeline-body-head">
                                        <div class="timeline-body-head-caption">
                                            <a href="javascript:;" class="timeline-body-title font-blue-madison">PECO.net</a>
                                            <span class="timeline-body-time font-grey-cascade">Replied at 17:45 PM</span>
                                        </div>
                                        <div class="timeline-body-head-actions">
                                            <div class="btn-group">
                                                <button class="btn btn-circle green btn-outline btn-sm dropdown-toggle" type="button" data-toggle="dropdown" data-hover="dropdown" data-close-others="true"> <i class="fa fa-sliders"></i>
                                                    <i class="fa fa-angle-down"></i>
                                                </button>
                                                <ul class="dropdown-menu pull-right" role="menu">
                                                    <li>
                                                        <a href="javascript:;">Action </a>
                                                    </li>
                                                    <li>
                                                        <a href="javascript:;">Another action </a>
                                                    </li>
                                                    <li>
                                                        <a href="javascript:;">Something else here </a>
                                                    </li>
                                                    <li class="divider"> </li>
                                                    <li>
                                                        <a href="javascript:;">Separated link </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="timeline-body-content">
                                        <span class="font-grey-cascade">Added access to the system as <span class="font-red">@administrator</span></span>
                                    </div>
                                </div>
                            </div>
                            <!-- END TIMELINE ITEM -->
                            <!-- TIMELINE ITEM WITH GOOGLE MAP -->
                            <div class="timeline-item">
                                <div class="timeline-badge">
                                    <img class="timeline-badge-userpic" src="<?php echo base_url(); ?>assets/global/img/admin_pic.png"> </div>
                                <div class="timeline-body">
                                    <div class="timeline-body-arrow"> </div>
                                    <div class="timeline-body-head">
                                        <div class="timeline-body-head-caption">
                                            <a href="javascript:;" class="timeline-body-title font-blue-madison">PECO.net</a>
                                            <span class="timeline-body-time font-grey-cascade">Added office location at 2:50 PM</span>
                                        </div>
                                        <div class="timeline-body-head-actions">
                                            <div class="btn-group">
                                                <button class="btn btn-circle red btn-sm dropdown-toggle" type="button" data-toggle="dropdown" data-hover="dropdown" data-close-others="true"> <i class="fa fa-sliders"></i>
                                                    <i class="fa fa-angle-down"></i>
                                                </button>
                                                <ul class="dropdown-menu pull-right" role="menu">
                                                    <li>
                                                        <a href="javascript:;">Action </a>
                                                    </li>
                                                    <li>
                                                        <a href="javascript:;">Another action </a>
                                                    </li>
                                                    <li>
                                                        <a href="javascript:;">Something else here </a>
                                                    </li>
                                                    <li class="divider"> </li>
                                                    <li>
                                                        <a href="javascript:;">Separated link </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="timeline-body-content">
                                        <div id="gmap_polygons" class="gmaps" style="position: relative; overflow: hidden;"><div style="height: 100%; width: 100%; position: absolute; top: 0px; left: 0px; background-color: rgb(229, 227, 223);"><div style="position: absolute; z-index: 0; left: 0px; top: 0px; height: 100%; width: 100%; padding: 0px; border-width: 0px; margin: 0px;" class="gm-style"><div style="position: absolute; z-index: 0; left: 0px; top: 0px; height: 100%; width: 100%; padding: 0px; border-width: 0px; margin: 0px; cursor: url(&quot;http://maps.gstatic.com/mapfiles/openhand_8_8.cur&quot;), default; touch-action: pan-x pan-y;" tabindex="0"><div style="z-index: 1; position: absolute; left: 50%; top: 50%; transform: translate(0px, 0px);"><div style="position: absolute; left: 0px; top: 0px; z-index: 100; width: 100%;"><div style="position: absolute; left: 0px; top: 0px; z-index: 0;"><div style="position: absolute; z-index: 1; transform: matrix(1, 0, 0, 1, -182, -96);"><div style="position: absolute; left: 0px; top: 0px; width: 256px; height: 256px;"><div style="width: 256px; height: 256px;"></div></div><div style="position: absolute; left: -256px; top: 0px; width: 256px; height: 256px;"><div style="width: 256px; height: 256px;"></div></div><div style="position: absolute; left: -256px; top: -256px; width: 256px; height: 256px;"><div style="width: 256px; height: 256px;"></div></div><div style="position: absolute; left: 0px; top: -256px; width: 256px; height: 256px;"><div style="width: 256px; height: 256px;"></div></div><div style="position: absolute; left: 256px; top: -256px; width: 256px; height: 256px;"><div style="width: 256px; height: 256px;"></div></div><div style="position: absolute; left: 256px; top: 0px; width: 256px; height: 256px;"><div style="width: 256px; height: 256px;"></div></div></div></div></div><div style="position: absolute; left: 0px; top: 0px; z-index: 101; width: 100%;"><div style="position: absolute; left: 0px; top: 0px; z-index: 30;"><div style="position: absolute; z-index: 1; transform: matrix(1, 0, 0, 1, -182, -96);"><div style="width: 256px; height: 256px; position: absolute; left: -256px; top: -256px;"></div><div style="width: 256px; height: 256px; position: absolute; left: 0px; top: -256px;"></div><div style="width: 256px; height: 256px; position: absolute; left: 256px; top: -256px;"></div><div style="width: 256px; height: 256px; position: absolute; left: 0px; top: 0px;"><canvas width="256" height="256" style="width: 256px; height: 256px; -moz-user-select: none; position: absolute; left: 0px; top: 0px;" draggable="false"></canvas></div><div style="width: 256px; height: 256px; position: absolute; left: -256px; top: 0px;"><canvas width="256" height="256" style="width: 256px; height: 256px; -moz-user-select: none; position: absolute; left: 0px; top: 0px;" draggable="false"></canvas></div><div style="width: 256px; height: 256px; position: absolute; left: 256px; top: 0px;"><canvas width="256" height="256" style="width: 256px; height: 256px; -moz-user-select: none; position: absolute; left: 0px; top: 0px;" draggable="false"></canvas></div></div></div></div><div style="position: absolute; left: 0px; top: 0px; z-index: 102; width: 100%;"></div><div style="position: absolute; left: 0px; top: 0px; z-index: 103; width: 100%;"></div><div style="position: absolute; left: 0px; top: 0px; z-index: 0;"><div style="position: absolute; z-index: 1; transform: matrix(1, 0, 0, 1, -182, -96);"><div style="position: absolute; left: 0px; top: 0px; width: 256px; height: 256px;"><img style="width: 256px; height: 256px; -moz-user-select: none; border: 0px none; padding: 0px; margin: 0px; max-width: none;" draggable="false" alt="" src="http://maps.google.com/maps/vt?pb=!1m5!1m4!1i15!2i9372!3i17488!4i256!2m3!1e2!6m1!3e5!2m3!1e0!2sm!3i427129752!3m14!2sen-US!3sUS!5e18!12m1!1e68!12m3!1e37!2m1!1ssmartmaps!12m4!1e26!2m2!1sstyles!2zcC5zOi02MHxwLmw6LTYw!4e0!23i1301875&amp;token=105324"></div><div style="position: absolute; left: -256px; top: 0px; width: 256px; height: 256px;"><img style="width: 256px; height: 256px; -moz-user-select: none; border: 0px none; padding: 0px; margin: 0px; max-width: none;" draggable="false" alt="" src="http://maps.google.com/maps/vt?pb=!1m5!1m4!1i15!2i9371!3i17488!4i256!2m3!1e2!6m1!3e5!2m3!1e0!2sm!3i427129752!3m14!2sen-US!3sUS!5e18!12m1!1e68!12m3!1e37!2m1!1ssmartmaps!12m4!1e26!2m2!1sstyles!2zcC5zOi02MHxwLmw6LTYw!4e0!23i1301875&amp;token=107805"></div><div style="position: absolute; left: -256px; top: -256px; width: 256px; height: 256px;"><img style="width: 256px; height: 256px; -moz-user-select: none; border: 0px none; padding: 0px; margin: 0px; max-width: none;" draggable="false" alt="" src="http://maps.google.com/maps/vt?pb=!1m5!1m4!1i15!2i9371!3i17487!4i256!2m3!1e2!6m1!3e5!2m3!1e0!2sm!3i427129704!3m14!2sen-US!3sUS!5e18!12m1!1e68!12m3!1e37!2m1!1ssmartmaps!12m4!1e26!2m2!1sstyles!2zcC5zOi02MHxwLmw6LTYw!4e0!23i1301875&amp;token=59420"></div><div style="position: absolute; left: 0px; top: -256px; width: 256px; height: 256px;"><img style="width: 256px; height: 256px; -moz-user-select: none; border: 0px none; padding: 0px; margin: 0px; max-width: none;" draggable="false" alt="" src="http://maps.google.com/maps/vt?pb=!1m5!1m4!1i15!2i9372!3i17487!4i256!2m3!1e2!6m1!3e5!2m3!1e0!2sm!3i427129704!3m14!2sen-US!3sUS!5e18!12m1!1e68!12m3!1e37!2m1!1ssmartmaps!12m4!1e26!2m2!1sstyles!2zcC5zOi02MHxwLmw6LTYw!4e0!23i1301875&amp;token=56939"></div><div style="position: absolute; left: 256px; top: -256px; width: 256px; height: 256px;"><img style="width: 256px; height: 256px; -moz-user-select: none; border: 0px none; padding: 0px; margin: 0px; max-width: none;" draggable="false" alt="" src="http://maps.google.com/maps/vt?pb=!1m5!1m4!1i15!2i9373!3i17487!4i256!2m3!1e2!6m1!3e5!2m3!1e0!2sm!3i427129704!3m14!2sen-US!3sUS!5e18!12m1!1e68!12m3!1e37!2m1!1ssmartmaps!12m4!1e26!2m2!1sstyles!2zcC5zOi02MHxwLmw6LTYw!4e0!23i1301875&amp;token=54458"></div><div style="position: absolute; left: 256px; top: 0px; width: 256px; height: 256px;"><img style="width: 256px; height: 256px; -moz-user-select: none; border: 0px none; padding: 0px; margin: 0px; max-width: none;" draggable="false" alt="" src="http://maps.google.com/maps/vt?pb=!1m5!1m4!1i15!2i9373!3i17488!4i256!2m3!1e2!6m1!3e5!2m3!1e0!2sm!3i427129704!3m14!2sen-US!3sUS!5e18!12m1!1e68!12m3!1e37!2m1!1ssmartmaps!12m4!1e26!2m2!1sstyles!2zcC5zOi02MHxwLmw6LTYw!4e0!23i1301875&amp;token=4525"></div></div></div></div><div style="z-index: 2; position: absolute; height: 100%; width: 100%; padding: 0px; border-width: 0px; margin: 0px; left: 0px; top: 0px; opacity: 0; transition-duration: 0.8s;" class="gm-style-pbc"><p class="gm-style-pbt">Use ctrl + scroll to zoom the map</p></div><div style="z-index: 3; position: absolute; height: 100%; width: 100%; padding: 0px; border-width: 0px; margin: 0px; left: 0px; top: 0px; touch-action: pan-x pan-y;"><div style="z-index: 4; position: absolute; left: 50%; top: 50%; transform: translate(0px, 0px);"><div style="position: absolute; left: 0px; top: 0px; z-index: 104; width: 100%;"></div><div style="position: absolute; left: 0px; top: 0px; z-index: 105; width: 100%;"></div><div style="position: absolute; left: 0px; top: 0px; z-index: 106; width: 100%;"></div><div style="position: absolute; left: 0px; top: 0px; z-index: 107; width: 100%;"><div style="z-index: -202; cursor: pointer; display: none; touch-action: none;"><div style="width: 30px; height: 27px; overflow: hidden; position: absolute;"><img style="position: absolute; left: 0px; top: 0px; -moz-user-select: none; border: 0px none; padding: 0px; margin: 0px; max-width: none; width: 90px; height: 27px;" alt="" src="http://maps.gstatic.com/mapfiles/undo_poly.png" draggable="false"></div></div></div></div></div></div><iframe style="z-index: -1; position: absolute; width: 100%; height: 100%; top: 0px; left: 0px; border: medium none;" src="about:blank" frameborder="0"></iframe><div style="margin-left: 5px; margin-right: 5px; z-index: 1000000; position: absolute; left: 0px; bottom: 0px;"><a style="position: static; overflow: visible; float: none; display: inline;" target="_blank" rel="noopener" href="https://maps.google.com/maps?ll=-12.043333,-77.028333&amp;z=15&amp;t=m&amp;hl=en-US&amp;gl=US&amp;mapclient=apiv3" title="Click to see this area on Google Maps"><div style="width: 66px; height: 26px; cursor: pointer;"><img style="position: absolute; left: 0px; top: 0px; width: 66px; height: 26px; -moz-user-select: none; border: 0px none; padding: 0px; margin: 0px;" alt="" src="http://maps.gstatic.com/mapfiles/api-3/images/google4.png" draggable="false"></div></a></div><div style="background-color: white; padding: 15px 21px; border: 1px solid rgb(171, 171, 171); font-family: Roboto, Arial, sans-serif; color: rgb(34, 34, 34); box-shadow: rgba(0, 0, 0, 0.2) 0px 4px 16px; z-index: 10000002; display: none; width: 256px; height: 148px; position: absolute; left: 42px; top: 60px;"><div style="padding: 0px 0px 10px; font-size: 16px;">Map Data</div><div style="font-size: 13px;">Map data ©2018 Google</div><div style="width: 13px; height: 13px; overflow: hidden; position: absolute; opacity: 0.7; right: 12px; top: 12px; z-index: 10000; cursor: pointer;"><img style="position: absolute; left: -2px; top: -336px; width: 59px; height: 492px; -moz-user-select: none; border: 0px none; padding: 0px; margin: 0px; max-width: none;" alt="" src="http://maps.gstatic.com/mapfiles/api-3/images/mapcnt6.png" draggable="false"></div></div><div class="gmnoprint" style="z-index: 1000001; position: absolute; right: 256px; bottom: 0px; width: 121px;"><div draggable="false" style="-moz-user-select: none; height: 14px; line-height: 14px;" class="gm-style-cc"><div style="opacity: 0.7; width: 100%; height: 100%; position: absolute;"><div style="width: 1px;"></div><div style="background-color: rgb(245, 245, 245); width: auto; height: 100%; margin-left: 1px;"></div></div><div style="position: relative; padding-right: 6px; padding-left: 6px; font-family: Roboto, Arial, sans-serif; font-size: 10px; color: rgb(68, 68, 68); white-space: nowrap; direction: ltr; text-align: right; vertical-align: middle; display: inline-block;"><a style="text-decoration: none; cursor: pointer; display: none;">Map Data</a><span>Map data ©2018 Google</span></div></div></div><div class="gmnoscreen" style="position: absolute; right: 0px; bottom: 0px;"><div style="font-family: Roboto, Arial, sans-serif; font-size: 11px; color: rgb(68, 68, 68); direction: ltr; text-align: right; background-color: rgb(245, 245, 245);">Map data ©2018 Google</div></div><div class="gmnoprint gm-style-cc" style="z-index: 1000001; -moz-user-select: none; height: 14px; line-height: 14px; position: absolute; right: 95px; bottom: 0px;" draggable="false"><div style="opacity: 0.7; width: 100%; height: 100%; position: absolute;"><div style="width: 1px;"></div><div style="background-color: rgb(245, 245, 245); width: auto; height: 100%; margin-left: 1px;"></div></div><div style="position: relative; padding-right: 6px; padding-left: 6px; font-family: Roboto, Arial, sans-serif; font-size: 10px; color: rgb(68, 68, 68); white-space: nowrap; direction: ltr; text-align: right; vertical-align: middle; display: inline-block;"><a style="text-decoration: none; cursor: pointer; color: rgb(68, 68, 68);" href="https://www.google.com/intl/en-US_US/help/terms_maps.html" target="_blank" rel="noopener">Terms of Use</a></div></div><button style="background: rgb(255, 255, 255) none repeat scroll 0% 0%; border: 0px none; margin: 10px 14px; padding: 0px; position: absolute; cursor: pointer; -moz-user-select: none; width: 25px; height: 25px; overflow: hidden; top: 0px; right: 0px;" draggable="false" title="Toggle fullscreen view" aria-label="Toggle fullscreen view" type="button"><img style="position: absolute; left: -52px; top: -86px; width: 164px; height: 175px; -moz-user-select: none; border: 0px none; padding: 0px; margin: 0px;" alt="" src="http://maps.gstatic.com/mapfiles/api-3/images/sv9.png" draggable="false" class="gm-fullscreen-control"></button><div draggable="false" style="-moz-user-select: none; height: 14px; line-height: 14px; position: absolute; right: 0px; bottom: 0px;" class="gm-style-cc"><div style="opacity: 0.7; width: 100%; height: 100%; position: absolute;"><div style="width: 1px;"></div><div style="background-color: rgb(245, 245, 245); width: auto; height: 100%; margin-left: 1px;"></div></div><div style="position: relative; padding-right: 6px; padding-left: 6px; font-family: Roboto, Arial, sans-serif; font-size: 10px; color: rgb(68, 68, 68); white-space: nowrap; direction: ltr; text-align: right; vertical-align: middle; display: inline-block;"><a target="_blank" rel="noopener" title="Report errors in the road map or imagery to Google" style="font-family: Roboto, Arial, sans-serif; font-size: 10px; color: rgb(68, 68, 68); text-decoration: none; position: relative;" href="https://www.google.com/maps/@-12.043333,-77.028333,15z/data=!10m1!1e1!12b1?source=apiv3&amp;rapsrc=apiv3">Report a map error</a></div></div><div class="gmnoprint gm-bundled-control" style="margin: 10px; -moz-user-select: none; position: absolute; left: 0px; top: 0px;" draggable="false" controlwidth="28" controlheight="55"><div class="gmnoprint" style="position: absolute; left: 0px; top: 0px;" controlwidth="28" controlheight="55"><div draggable="false" style="-moz-user-select: none; box-shadow: rgba(0, 0, 0, 0.3) 0px 1px 4px -1px; border-radius: 2px; cursor: pointer; background-color: rgb(255, 255, 255); width: 28px; height: 55px;"><button style="background: rgba(0, 0, 0, 0) none repeat scroll 0% 0%; display: block; border: 0px none; margin: 0px; padding: 0px; position: relative; cursor: pointer; -moz-user-select: none; width: 28px; height: 27px; top: 0px; left: 0px;" draggable="false" title="Zoom in" aria-label="Zoom in" type="button"><div style="overflow: hidden; position: absolute; width: 15px; height: 15px; left: 7px; top: 6px;"><img style="position: absolute; left: 0px; top: 0px; -moz-user-select: none; border: 0px none; padding: 0px; margin: 0px; max-width: none; width: 120px; height: 54px;" alt="" src="http://maps.gstatic.com/mapfiles/api-3/images/tmapctrl.png" draggable="false"></div></button><div style="position: relative; overflow: hidden; width: 67%; height: 1px; left: 16%; background-color: rgb(230, 230, 230); top: 0px;"></div><button style="background: rgba(0, 0, 0, 0) none repeat scroll 0% 0%; display: block; border: 0px none; margin: 0px; padding: 0px; position: relative; cursor: pointer; -moz-user-select: none; width: 28px; height: 27px; top: 0px; left: 0px;" draggable="false" title="Zoom out" aria-label="Zoom out" type="button"><div style="overflow: hidden; position: absolute; width: 15px; height: 15px; left: 7px; top: 6px;"><img style="position: absolute; left: 0px; top: -15px; -moz-user-select: none; border: 0px none; padding: 0px; margin: 0px; max-width: none; width: 120px; height: 54px;" alt="" src="http://maps.gstatic.com/mapfiles/api-3/images/tmapctrl.png" draggable="false"></div></button></div></div></div><div class="gmnoprint gm-bundled-control gm-bundled-control-on-bottom" style="margin: 10px; -moz-user-select: none; position: absolute; display: none; bottom: 14px; right: 0px;" draggable="false" controlwidth="0" controlheight="0"><div style="position: absolute; left: 0px; top: 0px;"></div><div class="gmnoprint" controlwidth="28" controlheight="0" style="display: none; position: absolute;"><div style="width: 28px; height: 28px; overflow: hidden; position: absolute; background-color: rgb(255, 255, 255); box-shadow: rgba(0, 0, 0, 0.3) 0px 1px 4px -1px; border-radius: 2px; cursor: pointer; display: none;" title="Rotate map 90 degrees"><img style="position: absolute; left: -141px; top: 6px; width: 170px; height: 54px; -moz-user-select: none; border: 0px none; padding: 0px; margin: 0px; max-width: none;" alt="" src="http://maps.gstatic.com/mapfiles/api-3/images/tmapctrl4.png" draggable="false"></div><div style="width: 28px; height: 28px; overflow: hidden; position: absolute; background-color: rgb(255, 255, 255); box-shadow: rgba(0, 0, 0, 0.3) 0px 1px 4px -1px; border-radius: 2px; top: 0px; cursor: pointer;" class="gm-tilt"><img style="position: absolute; left: -141px; top: -13px; width: 170px; height: 54px; -moz-user-select: none; border: 0px none; padding: 0px; margin: 0px; max-width: none;" alt="" src="http://maps.gstatic.com/mapfiles/api-3/images/tmapctrl4.png" draggable="false"></div></div></div><div style="position: absolute; -moz-user-select: none; height: 14px; line-height: 14px; right: 166px; bottom: 0px;" draggable="false" class="gm-style-cc"><div style="opacity: 0.7; width: 100%; height: 100%; position: absolute;"><div style="width: 1px;"></div><div style="background-color: rgb(245, 245, 245); width: auto; height: 100%; margin-left: 1px;"></div></div><div style="position: relative; padding-right: 6px; padding-left: 6px; font-family: Roboto, Arial, sans-serif; font-size: 10px; color: rgb(68, 68, 68); white-space: nowrap; direction: ltr; text-align: right; vertical-align: middle; display: inline-block;"><span>200 m&nbsp;</span><div style="position: relative; display: inline-block; height: 8px; bottom: -1px; width: 47px;"><div style="width: 100%; height: 4px; position: absolute; left: 0px; top: 0px;"></div><div style="width: 4px; height: 8px; left: 0px; top: 0px; background-color: rgb(255, 255, 255);"></div><div style="width: 4px; height: 8px; position: absolute; background-color: rgb(255, 255, 255); right: 0px; bottom: 0px;"></div><div style="position: absolute; background-color: rgb(102, 102, 102); height: 2px; left: 1px; bottom: 1px; right: 1px;"></div><div style="position: absolute; width: 2px; height: 6px; left: 1px; top: 1px; background-color: rgb(102, 102, 102);"></div><div style="width: 2px; height: 6px; position: absolute; background-color: rgb(102, 102, 102); bottom: 1px; right: 1px;"></div></div></div></div></div></div></div>
                                    </div>
                                </div>
                            </div>
                            <!-- END TIMELINE ITEM WITH GOOGLE MAP -->
                            <!-- TIMELINE ITEM -->
                            <div class="timeline-item">
                                <div class="timeline-badge">
                                    <div class="timeline-icon">
                                        <i class="icon-user-following font-green-haze"></i>
                                    </div>
                                </div>
                                <div class="timeline-body">
                                    <div class="timeline-body-arrow"> </div>
                                    <div class="timeline-body-head">
                                        <div class="timeline-body-head-caption">
                                            <span class="timeline-body-alerttitle font-red-intense">You have new follower</span>
                                            <span class="timeline-body-time font-grey-cascade">at 11:00 PM</span>
                                        </div>
                                        <div class="timeline-body-head-actions">
                                            <div class="btn-group">
                                                <button class="btn btn-circle green btn-outline

                                        btn-sm dropdown-toggle" type="button" data-toggle="dropdown" data-hover="dropdown" data-close-others="true"> Actions
                                                    <i class="fa fa-angle-down"></i>
                                                </button>
                                                <ul class="dropdown-menu pull-right" role="menu">
                                                    <li>
                                                        <a href="javascript:;">Action </a>
                                                    </li>
                                                    <li>
                                                        <a href="javascript:;">Another action </a>
                                                    </li>
                                                    <li>
                                                        <a href="javascript:;">Something else here </a>
                                                    </li>
                                                    <li class="divider"> </li>
                                                    <li>
                                                        <a href="javascript:;">Separated link </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="timeline-body-content">
                                                                    <span class="font-grey-cascade"> You have new follower
                                                                        <a href="javascript:;">Ivan Rakitic</a>
                                                                    </span>
                                    </div>
                                </div>
                            </div>
                            <!-- END TIMELINE ITEM -->
                            <!-- TIMELINE ITEM -->
                            <div class="timeline-item">
                                <div class="timeline-badge">
                                    <div class="timeline-icon">
                                        <i class="icon-docs font-red-intense"></i>
                                    </div>
                                </div>
                                <div class="timeline-body">
                                    <div class="timeline-body-arrow"> </div>
                                    <div class="timeline-body-head">
                                        <div class="timeline-body-head-caption">
                                            <span class="timeline-body-alerttitle font-green-haze">Server Report</span>
                                            <span class="timeline-body-time font-grey-cascade">Yesterday at 11:00 PM</span>
                                        </div>
                                        <div class="timeline-body-head-actions">
                                            <div class="btn-group dropup">
                                                <button class="btn btn-circle red btn-sm dropdown-toggle" type="button" data-toggle="dropdown" data-hover="dropdown" data-close-others="true"> Actions
                                                    <i class="fa fa-angle-down"></i>
                                                </button>
                                                <ul class="dropdown-menu pull-right" role="menu">
                                                    <li>
                                                        <a href="javascript:;">Action </a>
                                                    </li>
                                                    <li>
                                                        <a href="javascript:;">Another action </a>
                                                    </li>
                                                    <li>
                                                        <a href="javascript:;">Something else here </a>
                                                    </li>
                                                    <li class="divider"> </li>
                                                    <li>
                                                        <a href="javascript:;">Separated link </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="timeline-body-content">
                                                                    <span class="font-grey-cascade"> Lorem ipsum dolore sit amet
                                                                        <a href="javascript:;">Ispect</a>
                                                                    </span>
                                    </div>
                                </div>
                            </div>
                            <!-- END TIMELINE ITEM -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade in" id="overview">
                <div class="portlet light portlet-fit ">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="icon-microphone font-green"></i>
                            <span class="caption-subject bold font-green uppercase">Overview</span>
                            <span class="caption-helper"></span>
                        </div>
                        <div class="actions">
                        </div>
                    </div>
                    <div class="portlet-body">

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
