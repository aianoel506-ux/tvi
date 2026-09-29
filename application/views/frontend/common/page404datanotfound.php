<link href="<?php echo base_url(); ?>assets/admin/pages/css/error.css" rel="stylesheet" type="text/css">
<div class="page-content animated fadeInUp fast">

    <div class="col-md-12 page-404"  style="min-height: 600px; margin-top: 30px;">
        <div class="number">
            404
        </div>
        <div class="details margin-bottom-20">
            <h3><i class="fa fa-warning fa-fw text-warning"></i> Data Not Found!</h3>
            <p>
                <?php if($message != false) {
                    echo $message;
                }else{

                    ?>
                Opps.. data is lost, or cannot be found!<br>
                Sorry for the inconvenient.<br>
                <?php } ?>
                <br>
                <a href="<?php echo base_url(); ?>">
                    Return home </a>

            </p>

        </div>

    </div>
</div>
