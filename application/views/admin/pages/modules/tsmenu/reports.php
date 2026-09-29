<div class="row">
    <div class="col-md-12">
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    <form id="frm_ts_search" method="post" action="<?php echo base_url('ts/search'); ?>">
                        <div class="input-group input-group-lg">
                            <div class="input-group-btn">
                                <button type="button" class="btn green dropdown-toggle btn-lg" data-toggle="dropdown">
                                    <span id="search_label">TC No.</span>
                                    <i class="fa fa-angle-down"></i>
                                </button>
                                <ul class="dropdown-menu" id="opt_search_action">
                                    <li data-id="1">
                                        <a href="javascript:;"> <i class="fa fa-tag"></i> TC No. </a>
                                    </li>
                                    <li data-id="2">
                                        <a href="javascript:;"> <i class="fa fa-users"></i> Team No. </a>
                                    </li>
                                </ul>
                                <input type="hidden" name="searchtype" id="input_search_type" value="1" />
                                <input type="hidden" name="searchtext" id="input_search_text" value="TC No." />
                            </div>

                            <div class="input-icon right">
                                <input required class="form-control input-lg" type="text" placeholder="Search.." name="searchkey" />
                                <i class="fa fa-search search-stat  text-info "></i>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="actions">
                    <h3 class=""></h3>
                </div>
            </div>
            <div class="portlet-body">

                <form class="" id="ts_frm_accomplishment" action="<?php echo base_url('ts/accomplish'); ?>" method="post">
                    <input name="tcno" id="accomp_tcno" type="hidden" required/>
                    <div class="row">
                        <div class="col-md-4">

                            <h4><i class="fa fa-search text-success"></i> Trouble Call Details</h4>
                            <hr>
                            <ul class="list-group summary column">
                                <li class="list-group-item">
                                    <span class="col-md-4 label-name">Complainants</span>
                                    <span class="col-md-8 label-default number" id="comp_name"></span>
                                </li>
                                <li class="list-group-item">
                                    <span class="col-md-4 label-name">District</span>
                                    <span class="col-md-8 label-default number" id="comp_dist"></span>
                                </li>
                                <li class="list-group-item">
                                    <span class="col-md-4 label-name">Landmarks</span>
                                    <span class="col-md-8 label-default number" id="comp_landmarks"></span>
                                </li>
                                <li class="list-group-item">
                                    <span class="col-md-4 label-name">Report Stated</span>
                                    <span class="col-md-8 label-default number" id="comp_compstated"></span>
                                </li>
                                <li class="list-group-item">
                                    <span class="col-md-4 label-name">Date Created</span>
                                    <span class="col-md-8 label-default number" id="comp_datecreated"></span>
                                </li>
                                <li class="list-group-item">
                                    <span class="col-md-4 label-name">Created By</span>
                                    <span class="col-md-8 label-default number" id="comp_createdby"></span>
                                </li>
                                <li class="list-group-item">
                                    <span class="col-md-4 label-name">Status</span>
                                    <span class="col-md-8 label-default number"  id="comp_status"></span>
                                </li>
                            </ul>
                        </div>

                        <div class="col-md-8">
                            <h4><i class="fa fa-check text-success"></i> Accomplishments Entry</h4>


                            <div class="form-group" >
                                <div class="pull-right" style="position:absolute; top: -70px; right: 15px;">
                                    <a class="btn btn-default" href="<?php echo base_url('module/eb4ac3033e8ab3591e0fcefa8c26ce3fd36d5a0f/list'); ?>">Back To List</a>

                                    <button type="reset" class="btn btn-default"><i class="fa fa-refresh"></i> Reset</button>
                                    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Accomplish</button>
                                </div>
                            </div>
                            <hr>

                            <div class="form-group row" >
                                <div class="col-md-12">

                                    <label class="col-md-2 control-label" for="form_control_1">Team Assigned</label>
                                    <div class="col-md-10" id="comp_teams">
                                        None
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="form-group form-md-line-input">
                                <label class="col-md-2 control-label" for="form_control_1">Remarks</label>
                                <div class="col-md-10 ">
                                    <textarea class="form-control" name="tsremarks" rows="3" placeholder="Enter some text..."></textarea>
                                    <div class="form-control-focus"> </div>
                                </div>
                            </div>

                            <div class="form-group" style="margin-top: 20px; display: inline-block; width: 100%;">
                                <label class="col-md-2 control-label" for="form_control_1">Findings</label>
                                <div class="col-md-5 ">
                                    <input class="form-control" name="tsfindings" id="select2tsfindings" placeholder="Select findings.." />
                                    <div class="form-control-focus"> </div>
                                </div>
                                <div class="col-md-5 ">
                                    <select required class="form-control" id="select_accomp_type" name="accomptype">
                                        <option></option>
                                        <option value="305">Accomplished</option>
                                        <option value="304">On-Hold</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group disabled" style="margin-top: 20px; display: inline-block; width: 100%;">
                                <label class="col-md-2 control-label" for="form_control_2"></label>
                                <div class="col-md-5 ">
                                    Attachedments
                                    <input class="form-control " disabled name="attachements" placeholder="..." />
                                    <div class="form-control-focus"> </div>
                                </div>
                                <div class="col-md-5 ">
                                    Date Accomplishment
                                    <input class="form-control " type="date" name="dateaccomp" placeholder="Date Accomplishments" />
                                    <div class="form-control-focus"> </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url(); ?>assets/pages/tsmenu/main.js"></script>

<script>
    TS.accomplishment();
</script>
