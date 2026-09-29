
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

        $count = 0;
        ?>

        <form id="submitcustomeratt" action="<?php echo base_url() ?>cad/submitcustomeratt" method="post">
            <div id="frame">
                <input type="hidden" value="<?php echo $ids; ?>" name="ids"/>
                <?php


                //extract only the name of the file without the extension and save in an array named $find
                foreach ($images as $image):
                    $location = base_url() . $image;
                    $filename =  basename($location);

                    $checkifimgexist = $this->db->select("fileurl")->from("application_customers_attachments")
                        ->where(array("status" => 1, "fileurl" => $image))->get()->row();
                    if($checkifimgexist == false) {
                        $count++;
                        ?>
                        <div class="cbp-item web-design graphic"
                             style="width: 200px; padding: 10px 10px !important;background-color: #ffffff">
                            <div class="cbp-item-wrapper tooltips" title="<?php echo $filename; ?>">


                                        <?php
                                            if(@is_array(getimagesize($location))){

                                              ?>
                                            <a href="<?php echo $location; ?>" class="cbp-caption cbp-lightbox"
                                               data-title="CAD">
                                                <div class="cbp-caption-defaultWrap">
                                                <div id="file" class="inline "
                                                     style="background-size: 100% !important;padding: 5px auto;width: 100% !important; height: 200px;background-image: url(<?php echo $location; ?>);background-repeat: no-repeat;background-size: 100% auto;background-position: center top;
                                                             background-attachment: fixed;">
                                                </div>

                                                </div>
                                            </a>
                                        <?php
                                            } else {
                                                $pdfimage = base_url().'assets/global/img/pdfdoc.png';

                                            ?>
                                                <a href="<?php echo $location; ?>" class="cbp-caption cbp-lightbox iframe text-center"
                                                   data-title="CAD">
                                                    <div class="cbp-caption-defaultWrap">
                                                        <div id="file" class="inline "
                                                             style="background-size: 100% !important;padding: 5px auto;width: 100% !important; height: 200px;background-image: url(<?php echo $pdfimage; ?>);background-repeat: no-repeat;background-size: 100% auto;background-position: center top;
                                                                     background-attachment: fixed;">

                                                        </div>
                                                    </div>
                                                </a>
                                               <!-- <p><a class="iframe" href="<?php echo $location; ?>">Zero</a></p> -->
                                        <?php
                                            }
                                        ?>

                                <div class="cbp-caption-activeWrap">
                                    <div class="cbp-l-caption-alignCenter">
                                        <div class="cbp-l-caption-body">
                                            <div class="form-group" style="width: 180px !important;">
                                                <span style="word-wrap: break-word !important;"><?php echo $filename; ?></span>
                                                <input type="checkbox" value="<?php echo $image; ?>"
                                                       class="form-control" name="checkimg[]" />
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php
                    }
                endforeach;
                if($count == 0){
                    echo '<h3 class="text-center">This folder is empty.</h3>';
                }
                ?>
            </div>

            <button id="assignattachmentbtn" type="submit" data-id=""
                    class="btn btn-primary btn-md">Assign
            </button>
        </form>

    </div>
</div>


<!-- <script src="<?php echo base_url() ?>assets/pages/scripts/portfolio-2.min.js" type="text/javascript"></script> -->

<script type="text/javascript" src="<?php echo base_url(); ?>assets/pages/cad/form-editable.js"></script>

<script>
    $(function() {
        $( "#submitcustomeratt").unbind( "submit" );
        $("a.iframe").fancybox({
            'width': 640, // or whatever you want
            'height': 480, // or whatever you want
            'type': 'iframe'
        });
        $('div.cbp-item.tooltips').each(function () {
                var this_ = $(this);
                this_.tooltip();
        });
    });


    $(document).on('submit' , '#submitcustomeratt' , function (e) {
        e.preventDefault();
        var this_ = $(this);
        $.ajax({
            url:this_.attr("action"),
            type:this_.attr("method"),
            data:this_.serialize(),
            dataType:'json'
        }).done(function (data) {
            PECO.initAlerts(data.msg , "PECO" , data.func);
            if(data.qry == true){
                FormEditable.init();
            }

            $('input[type=checkbox]').each(function () {
                $('input[name="checkimg[]"]:checked').closest('div.cbp-item').fadeOut(300, function(){ $(this).remove();});
            });

        }).fail(function () {
            PECO.phpError();
        });
        e.stopImmediatePropagation();
    });

</script>

