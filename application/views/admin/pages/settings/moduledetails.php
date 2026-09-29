<?php
/*
?>

<h1>Hello, World!!!</h1>
<?php
echo 'sysid: '.$sysid.'<br>';
echo 'codes: '.$codes.'<br>';
echo 'names: '.$names.'<br>';
echo 'titles: '.$titles.'<br>';
echo 'descs: '.$descs.'<br>';
echo 'types: '.$types.'<br>';
echo 'icons: '.$icons.'<br>';
echo 'pagename: '.$pagename.'<br>';
echo 'pagefile: '.$pagefile.'<br>';
echo 'colorclass: '.$colorclass.'<br>';
echo 'status: '.$status.'<br>';
*/

/*$icon_qry = $this->db->select('icon')
    ->from('sys_icons')
    ->where('sysid',$icons)
    ->get()->row();

$icon = ($icon_qry) ? $icon_qry->icon : 'N/A';*/
?>


<div class="row">
    <div class="col-md-6" id="module_details">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Code</label>
                    <div class="input-icon">
                        <i class="fa fa-tag"></i>
                        <input value="<?php echo $code;?>" type="text" class="form-control" placeholder="Code">
                    </div>
                </div>
                <div class="form-group">
                    <label>Name</label>
                    <div class="input-icon">
                        <i class="fa fa-tag"></i>
                        <input value="<?php echo $name;?>" type="text" class="form-control" placeholder="Name">
                    </div>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <div class="input-icon">
                        <i class="fa fa-tag"></i>
                        <input value="<?php echo $desc;?>" type="text" class="form-control" placeholder="Description">
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Icon</label>
                    <div class="input-icon">
                        <i class="fa fa-tag"></i>
                        <input id="module_icon" value="<?php echo $icon;?>" type="text" class="form-control" placeholder="Icon">
                    </div>
                </div>
                <div class="form-group">
                    <label>Page Name</label>
                    <div class="input-icon">
                        <i class="fa fa-tag"></i>
                        <input value="<?php echo $pagefile;?>" type="text" class="form-control" placeholder="Page File">
                    </div>
                </div>
                <div class="form-group">
                    <label>Class</label>
                    <div class="input-icon">
                        <i class="fa fa-tag"></i>
                        <input value="<?php echo $htmlclass;?>" type="text" class="form-control" placeholder="Class">
                    </div>
                </div>
            </div>
            <div class="col-md-12 margin-top-10">
                <button class="btn btn-default btn-sm update" id="btn_update_module"><i class="fa fa-edit"></i> Update</button>
                <button class="btn btn-danger btn-sm hidden" id="btn_update_module_cancel"><i class="fa fa-times-circle-o"></i> Cancel</button>
                <button class="btn btn-danger btn-sm pull-right"><i class="fa fa-times-circle-o"></i> Deactivate</button>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-sm-6 col-xs-6 tabbable-line">
        <ul class="nav nav-tabs" style="">
            <li class="active">
                <a href="#subs" data-toggle="tab" aria-expanded="false">
                    <i class="fa fa-bars"></i> </a>
            </li>
            <li class="">
                <a href="#control" data-toggle="tab" aria-expanded="true">
                    <i class="fa fa-plus"></i> </a>
            </li>
            <li class="">
                <a href="#info" data-toggle="tab" aria-expanded="false">
                    <i class="fa fa-question"></i> </a>
            </li>
        </ul>
        <div class="tab-content" style="">
            <div class="tab-pane fade in " id="control">
                <h3 style="margin-top: 0px !important;">Add New Sub Menu</h3>
                <form class="frm_add_nav" id="frm_add_nav" role="form" action="<?php echo base_url();?>settings/addmodulenav" style="margin: 10px 10px;" method="post">
                    <input name="types" type="hidden" value="2">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label class="form-label-stripped">Code</label>
                                <input class="form-control input-sm" type="text" onblur="$(this).val($(this).val().toUpperCase())" placeholder="Navigation Code" name="codes">
                            </div>
                            <div class="form-group">
                                <label class="form-label-stripped">Name</label>
                                <input class="form-control input-sm" type="text" value="" placeholder="Navigation Name" name="names">
                            </div>
                            <div class="form-group">
                                <label class="form-label-stripped">Description</label>
                                <textarea class="form-control input-sm" placeholder="Descriptions" name="descs"></textarea>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label-stripped">Parent</label>
                                <input disabled class="form-control input-sm" type="text" name="parent_name" value="<?php echo $name;?>" />
                                <input class="form-control input-sm" type="hidden" name="parent" value="<?php echo $sysid;?>" />
                            </div>
                            <div class="form-group">
                                <label class="form-label-stripped">Page File</label>
                                <input class="form-control input-sm" type="text" value="" placeholder="Page Name" name="file">
                            </div>

                            <div class="form-group">
                                <label class="form-label-stripped">URL</label>
                                <input class="form-control input-sm" type="text" value="" placeholder="Page File" name="url">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label-stripped">Class</label>
                                <select class="form-control input-sm" id="select_module_class_<?php echo $sysid;?>" placeholder="Class" name="class">
                                    <option value="info">info - Info</option>
                                    <option value="success">success - Success</option>
                                    <option value="warning">warning - Warning</option>
                                    <option value="danger">danger - Danger</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label-stripped">Icon</label>
                                <!--<a class="btn purple btn-outline sbold" data-toggle="modal" href="#large"> View Demo </a>-->
                                <!--<a href="#dt_select_icon" title="Select Icon" data-target="ajax_modal" data-toggle="modal" class="btn btn-default"><i class="fas fa-info-circle"></i> Icon</a>-->
                                <input class="form-control input-sm" id="select_module_icon_<?php echo $sysid;?>" type="text" placeholder="Icon" name="icon" />
                            </div>
                        </div>
                        <div class="col-md-12 margin-top-10">
                            <button type="reset" class="btn btn-default btn-sm">Reset</button>
                            <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-plus fa-fw"></i> Save</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="tab-pane fade in active" id="subs">
                <h3 style="margin-top: 0px !important;">Sub Menus</h3>
                <table width="100%" class="table table-striped table-hover table-condensed tbl-sm" id="tbl_sub_modules">
                    <thead>
                        <th><i class="fa fa-bars"></i></th>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Icon</th>
                        <th>Status</th>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="<?php echo base_url(); ?>assets/pages/modules.js" ></script>

<script type="text/javascript">
    MODULES.maintenance(<?php echo $sysid;?>);
</script>