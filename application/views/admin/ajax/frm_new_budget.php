<?php
        $ids = $this->input->post('ids');
        $view = $this->input->post('view');

        $data_arr = explode('-', $ids);
        $year = $data_arr[0];
        $ccid = $data_arr[1];
?>
<form id="submittransaction" action="<?php echo base_url() ?>bos/submittransaction" method="post">
    <div class="container">
        <div class="row">
            <div class="col-md-2">
                <input type="hidden" name="ccid" value="<?php echo $ccid; ?>" />
                <div class="form-group">
                    <label>Select Group</label>
                    <input type="text" name="selectgroup" id="selectgroup" class="form-control" />
                </div>
                <div class="form-group">
                    <label>Year</label>
                    <input type="text" name="selectyear" id="selectyear" class="form-control" />
                </div>
                <div class="form-group">
                    <label>Remarks</label>
                    <textarea wrap="physical" name="remarks" class="form-control" rows="4" cols="50"></textarea>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <input type="text" placeholder="Count" id="selectquatercount" class="form-control" />
                        <span class="input-group-btn">
                            <button type="button" id="gobtn" class="btn btn-default" ><i class="fa fa-arrow-right"></i></button>
                            </span>
                    </div><!-- /input-group -->
                </div>
            </div>
            <div class="col-md-7">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="portlet light portlet-fit">
                            <div class="portlet-body">
                                <div id="transactionlist"></div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
</form>



<script>
    PECO.select2Basic($('#selectgroup',document),'bos/getbudgettypesgroup','Select Group',false,false,false,false,false,<?php echo $view; ?>);
    PECO.select2Basic($('#selectyear',document),'systems/select2year','Select Year');

</script>