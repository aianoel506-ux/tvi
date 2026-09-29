<?php
$gallery_name = str_pad($id, 8, 0, STR_PAD_LEFT);

$location 	= './uploads/attachments/crm';
$files 		= glob($location . '/' . $gallery_name . '/*.{jpg,gif,png}', GLOB_BRACE);


?>

<div class="portlet light portlet-fit bordered">
    <div class="portlet-title">
        <div class="caption">
            <i class=" icon-layers font-green"></i>
            <span class="caption-subject font-green bold uppercase">Gallery</span>
            <div class="caption-desc font-grey-cascade">File attachment submitted.</div>
        </div>
    </div>
    <div class="portlet-body">
        <div class="row">

        <?php
        if($files && is_array($files) && count($files)>0) {
            foreach($files as $frow) {
                $file = explode('./', $frow);
                $file_name = explode('/', $file[1]);
                ?>

                <div class="col-md-4">
                    <div class="mt-widget-4">
                        <div class="mt-img-container">
                            <img src="<?php echo base_url($file[1]);?>"> </div>
                        <div class="mt-container bg-purple-opacity">
                            <div class="mt-head-title"><?php echo $file_name[4];?></div>
                            <div class="mt-body-icons">
                                <a href="#">
                                    <i class=" icon-pencil"></i>
                                </a>
                                <a href="#">
                                    <i class=" icon-trash"></i>
                                </a>
                            </div>
                            <div class="mt-footer-button">
                                <a href="<?php echo base_url($file[1]);?>" class="btn btn-circle btn-danger btn-sm gallery-btn"">View</a>
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
