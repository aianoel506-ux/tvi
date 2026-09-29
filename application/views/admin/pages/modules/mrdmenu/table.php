<!-- END PAGE LEVEL STYLES -->

<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/jquery-tags-input/jquery.tagsinput.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-markdown/css/bootstrap-markdown.min.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/typeahead/typeahead.css">



<style>
    .table thead {

        background: url(data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiA/Pgo8c3ZnIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgdmlld0JveD0iMCAwIDEgMSIgcHJlc2VydmVBc3BlY3RSYXRpbz0ibm9uZSI+CiAgPGxpbmVhckdyYWRpZW50IGlkPSJncmFkLXVjZ2ctZ2VuZXJhdGVkIiBncmFkaWVudFVuaXRzPSJ1c2VyU3BhY2VPblVzZSIgeDE9IjAlIiB5MT0iMCUiIHgyPSIwJSIgeTI9IjEwMCUiPgogICAgPHN0b3Agb2Zmc2V0PSIwJSIgc3RvcC1jb2xvcj0iI2ZmZmZmZiIgc3RvcC1vcGFjaXR5PSIxIi8+CiAgICA8c3RvcCBvZmZzZXQ9IjMwJSIgc3RvcC1jb2xvcj0iI2Y2ZjZmNiIgc3RvcC1vcGFjaXR5PSIxIi8+CiAgICA8c3RvcCBvZmZzZXQ9IjMwJSIgc3RvcC1jb2xvcj0iI2Y2ZjZmNiIgc3RvcC1vcGFjaXR5PSIxIi8+CiAgICA8c3RvcCBvZmZzZXQ9IjEwMCUiIHN0b3AtY29sb3I9IiNlNWU1ZTUiIHN0b3Atb3BhY2l0eT0iMSIvPgogIDwvbGluZWFyR3JhZGllbnQ+CiAgPHJlY3QgeD0iMCIgeT0iMCIgd2lkdGg9IjEiIGhlaWdodD0iMSIgZmlsbD0idXJsKCNncmFkLXVjZ2ctZ2VuZXJhdGVkKSIgLz4KPC9zdmc+) !important;
        background: -moz-linear-gradient(top,  #ffffff 0%, #f6f6f6 30%, #f6f6f6 30%, #e5e5e5 100%) !important;
        background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#ffffff), color-stop(30%,#f6f6f6), color-stop(30%,#f6f6f6), color-stop(100%,#e5e5e5)) !important;
        background: -webkit-linear-gradient(top,  #ffffff 0%,#f6f6f6 30%,#f6f6f6 30%,#e5e5e5 100%) !important;
        background: -o-linear-gradient(top,  #ffffff 0%,#f6f6f6 30%,#f6f6f6 30%,#e5e5e5 100%) !important;
        background: -ms-linear-gradient(top,  #ffffff 0%,#f6f6f6 30%,#f6f6f6 30%,#e5e5e5 100%) !important;
        background: linear-gradient(to bottom,  #ffffff 0%,#f6f6f6 30%,#f6f6f6 30%,#e5e5e5 100%) !important;
        filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#e5e5e5',GradientType=0 ) !important;

    }
    .table tr.odd td.zui-sticky-col
    {
        background: rgba(73,169,255,0.30) !important;
    }
    .table tr.even td.zui-sticky-col
    {
        background: rgba(73,169,255,0.15) !important;
    }
</style>



<div class="row">
    <div class="col-md-12 well"  style="margin-bottom: 0px !important;">

        <div class="col-md-4">
            <div class="input-group">
                <span class="input-group-addon">
                    GDLB
                </span>
                <input id="select2gdlb" name="select2gdlb" type="text" class="form-control" placeholder="No schedule yet." />
                <span class="input-group-btn">
                    <button id="get_gdlb_list" class="btn btn-info btn-sm "><i class="fa fa-search"></i> Get</button>
                </span>
            </div>
        </div>

        <div class="col-md-8">

            <div class="input-group pull-right">
                <a class="btn btn-default" href="#tbl_legacy_seqtab_import" data-toggle="ajax-modal"><i class="fa fa-download"></i> Import Seqtab</a>
                <a class="btn btn-primary" href="javascript:;" id="btn_update_sequences" ><i class="fa fa-refresh"></i> Update Sequence</a>
            </div>
        </div>
    </div>

    <div class="portlet light table">
        <div class="portlet-body" style="min-height: 400px;">


            <table class="zui-table table table-hover table-striped table-bordered tbl-sm" id="tbl_gdlb_tagging">
                <thead>
                <tr>
                    <th>SEQ</th>
                    <th>SERVNO</th>
                    <th>NAME</th>
                    <th>ADDRESS</th>
                    <th>MTR</th>
                    <th>SERIAL</th>
                    <th>MTRNO</th>
                    <th>R.CODE</th>
                    <th>READER</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>


        </div>
    </div>

    <hr>

</div>

<script type="text/javascript" src="<?php echo base_url(); ?>assets/pages/mrd/entry.js"></script>
<script type="text/javascript">
    MRD.metertagging();
</script>