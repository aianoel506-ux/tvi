<div class="modal-body">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="portlet light portlet-fit bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <i class=" icon-layers font-green"></i>
                        <span class="caption-subject font-green bold uppercase">Assignment</span>
                        <div class="caption-desc font-grey-cascade"></div>
                    </div>
                </div>
                <div class="portlet-body">
                    <form id="frm_crm_assignment" action="<?php echo base_url();?>crm/assignment" method="post">
                        <div class="input-group">
                            <div class="input-group-addon">
                                Select
                            </div>
                            <input name="assignee" class="form-control" id="input_select2_assignee">
                            <div class="input-group-btn">
                                <button type="submit" class="btn btn-primary" id="btn_assign">Assign</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    var select_elem = $('#input_select2_assignee',document);
    console.log(select_elem);
    PECO.select2Basic(select_elem,'crm/selectassigne','Select Employee',false,false,false);
</script>