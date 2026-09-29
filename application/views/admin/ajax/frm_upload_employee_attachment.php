<?php
$id = $this->input->post('ids');
?>
<input style="margin-top: 20px !important;" id="accomplishments" name="accomplishments" type="file" multiple>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/pages/hris/view.js"></script>
<script>

    $("#accomplishments", document).fileinput({
        'showUpload':true,
        'previewFileType':'any',
        'showPreview':true,
        'uploadUrl':PECO.base_url()+'hris/uploadaccomplishments',
        'uploadExtraData':function () {
            return {
                dataid: <?php echo $id;?>
            };
        },
        'dropZoneEnabled':true,
        'browseOnZoneClick':true
    }).on('filebatchuploadcomplete', function(event, data, previewId, index) {
        HRIS.attachementexplorer(<?php echo $id;?>);
    });
</script>
