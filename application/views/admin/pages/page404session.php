
<link href="<?php echo base_url(); ?>assets/pages/error.min.css" rel="stylesheet" type="text/css" />
<style>
    .page-500-full-page {
        height: 100%;
        width: 100%;
        display: inline-block;
    }
</style>
<div class=" page-500-full-page">
    <div class="row">
        <div class="col-md-12 page-500">
            <div class=" number font-red"> 599 </div>
            <div class=" details">
                <h3><i class="fa fa-warning text-warning"></i> Session Timeout</h3>
                <p><?php echo $message; ?>
                    <br/> </p>
                <p><a href="<?php echo base_url(); ?>" class="btn red btn-outline"> Return home </a><br> </p>
            </div>
        </div>
    </div>
</div>

<script>
    setTimeout(function(){
        window.location.href='<?php echo base_url(); ?>'
    },3000);
</script>