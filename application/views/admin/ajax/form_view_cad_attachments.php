
<style>
    img{
        width: 100% !important;
    }
    div.inline { float:left; }
    .clearBoth { clear:both; }
    #frame{
        height:320px !important;
        overflow-y: scroll;
    }
    div.desc {
        padding: 15px;
        text-align: center;
        border: 1px solid #ccc;

        display: block;
        width: 100%;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }
    div.panel:hover{
    //   border: 1px solid #777;
    }
    div.cbp-item > div > a > div > div{
        background-color: white !important;
        border: 1px solid whitesmoke;
    }
    div.cbp-item > div > div > div{
        background-color: white !important;
        border: 1px solid whitesmoke;
    }
    #assignattachmentbtn{
        margin: 10px 15px; !important;
    }
</style>
<div class="row">
    <div class="col-md-12">

        <?php
        $view = $this->input->post('view');
        $ids  = $this->input->post('ids');
        $dataid =  str_pad($view,6,"0",STR_PAD_LEFT);

        $dir = "uploads/attachments/cad/applications/".$dataid."/*.*";
        //get the list of all files with .jpg extension in the directory and safe it in an array named $images
        $images = glob( $dir );

        

        $getattachments = $this->db->select("sysid,attachmentid,fileurl")->from("application_customers_attachments")
        ->where(array("attachmentid" => $ids, "status" => 1))->get();

        ?>

            <div id="frame">
                <input type="hidden" value="<?php echo $ids; ?>" name="ids"/>
                <?php
                //extract only the name of the file without the extension and save in an array named $find
                if($getattachments->num_rows() > 0){
                    foreach ($getattachments->result() as $image){
                    $location = base_url().$image->fileurl;
                    $filename =  basename($location);

                        ?>
                        <div class="cbp-item web-design graphic"
                             style="width: 200px; padding: 10px 10px !important;background-color: #ffffff">
                            <div class="cbp-item-wrapper">

                                <?php
                                    if(@is_array(getimagesize($location))){
                                    ?>
                                        <a href="<?php echo $location; ?>" class="cbp-caption cbp-lightbox"
                                           data-title="Bolt UI<br>by Tiberiu Neamu">
                                            <div class="cbp-caption-defaultWrap">
                                                <div id="file" class="inline "
                                                     style="background-size: 100% !important;padding: 5px auto;width: 100% !important; height: 200px;background-image: url(<?php echo $location; ?>);background-repeat: no-repeat;background-size: 100% auto;background-position: center top;
                                                             background-attachment: fixed;">
                                                </div>
                                            </div>
                                        </a>
                                        <?php
                                    }else{
                                        $pdfimage = base_url().'assets/global/img/pdfdoc.png';
                                        ?>
                                        <a href="<?php echo $location; ?>" class="cbp-caption cbp-lightbox iframe text-center"
                                           data-title="Bolt UI<br>by Tiberiu Neamu">
                                            <div class="cbp-caption-defaultWrap">
                                                <div id="file" class="inline "
                                                     style="background-size: 100% !important;padding: 5px auto;width: 100% !important; height: 200px;background-image: url(<?php echo $pdfimage; ?>);background-repeat: no-repeat;background-size: 100% auto;background-position: center top;
                                                             background-attachment: fixed;">

                                                </div>
                                            </div>
                                        </a>
                                        <?php
                                    }
                                ?>

                                <div class="cbp-caption-activeWrap">
                                    <div class="cbp-l-caption-alignCenter">
                                        <div class="cbp-l-caption-body">
                                            <div class="form-group" style="width: 180px !important;">
                                                <span style="word-wrap: break-word !important;"><?php echo $filename; ?></span>
                                                <button id="deleteattachment" data-id="<?php echo $image->sysid; ?>" data-appid="<?php echo $image->attachmentid; ?>" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php

                    }
                }

                ?>
            </div>



    </div>
</div>


<!-- <script src="<?php echo base_url() ?>assets/pages/scripts/portfolio-2.min.js" type="text/javascript"></script> -->

<script type="text/javascript" src="<?php echo base_url(); ?>assets/pages/cad/form-editable.js"></script>

<script>
    $(document).ready(function () {
        $("a.iframe").fancybox({
            'width': 640, // or whatever you want
            'height': 480, // or whatever you want
            'type': 'iframe'
        });
    });
    $(document).on('click','#deleteattachment' , function () {
        var this_ = $(this);
        var dataid = this_.attr("data-id");
        var appid = this_.attr("data-appid");
        var target = $(this).attr("href");
        swal({
            title: "Are you sure?",
            text: "Delete attachment.",
            type: "error",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Yes, Remove!",
            closeOnConfirm: false,
            closeOnCancel: false,
            showLoaderOnConfirm: true
        }, function(isConfirm) {
            if (isConfirm) {
                $.ajax({
                    url:PECO.base_url()+'cad/deleteattachment',
                    type:'post',
                    data:{"dataid" : dataid , "appid" : appid},
                    dataType:'json'
                }).done(function (data) {
                    swal("PECO" , data.msg , data.func);
                    if(data.qry == true){
                        this_.closest('.cbp-item-wrapper').hide();
                        FormEditable.init();

                        if(data.filecount == 0){
                            $('#modal_ajax', document).modal('hide');
                        }
                    }
                }).fail(function () {
                    PECO.phpError();
                });
            }else{
                swal.close();
            }
        });


    });
</script>

