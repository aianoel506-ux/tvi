<div class="row">
    <div class="col-md-12">
        <div class="col-md-12">
            <div class="portlet light table">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-edit"></i>
                        <span class="caption-subject font-green-sharp bold uppercase">Reading Entry</span>
                        <span class="caption-helper"><?php echo date('F d, Y'); ?></span>
                    </div>
                    <div class="tools">
                        <a href="javascript:;" class="collapse" data-original-title="" title="">
                        </a>
                        <a href="#portlet-config" data-toggle="modal" class="config" data-original-title="" title="">
                        </a>
                        <a href="javascript:;" class="reload" data-original-title="" title="">
                        </a>
                        <a href="javascript:;" class="fullscreen" data-original-title="" title="">
                        </a>
                        <a href="javascript:;" class="remove" data-original-title="" title="">
                        </a>
                    </div>
                </div>
                <div class="table-toolbar">
                    <div class="col-md-12 well"  style="margin-bottom: 0px !important;">

                        <div class="col-md-5">
                            <div class="input-group">
                                    <span class="input-group-addon">
                                        GDLB
                                    </span>
                                <input style="width: 60%; display: inline-block;" id="schedid" name="schedid" type="text" class="form-control input-sm" placeholder="No schedule yet." />
                                <select style="width: 40%; display: inline-block;" id="showall" name="showall" type="text" class="form-control input-sm" >
                                    <option value="0">Select..</option>
                                    <option value="3">All</option>
                                    <option value="1">Re-Check</option>
                                    <option value="2">For Billing</option>
                                </select>

                                <span class="input-group-btn">
                                            <button id="get_mrd_list" class="btn btn-info btn-sm "><i class="fa fa-search"></i> Get</button>
                                        </span>
                            </div>
                        </div>

                        <div class="col-md-7">
                            <div class="btn-group pull-right">
                                <button id="btn_generate_regbill" type="button" class="btn btn-sm btn-success"><i class="fa fa-tag"></i> Send For Billing <span class="badge badge-danger">B</span></button>
                                <button id="btn_generate_addbill" type="button" class="btn btn-sm btn-warning"><i class="fa fa-calculator"></i> Compute <span class="badge badge-danger">A</span></button>
                                <button id="btn_print_report" type="button" class="btn btn-sm btn-default"><i class="fa fa-print"></i> Print Analysis Report</button>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="portlet-body ">
                </div>
            </div>
        </div>
    </div>
</div>