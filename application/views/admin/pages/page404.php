<link href="<?php echo base_url(); ?>assets/admin/pages/css/error.css" rel="stylesheet" type="text/css">
<div class="page-content-wrapper animated fadeIn fast">
    <div class="page-content">
				

        <?php $msg = (isset($msg)) ? $msg : 'not found!'; ?>
	  <h3 class="page-title">
				Error 404 <small> <code>The page is either not exist or you are not allowed to access.</code></small>
				</h3>
				<div class="page-bar">
					<?php echo create_breadcrumb($msg); ?>
				</div>
				<!-- END PAGE HEADER-->
				<div class="row">
				<div class="col-md-12 page-404">
					<div class="number">
						 404
					</div>
					<div class="details">
						<h3>Oops! You're lost.</h3>
						<p>
							 We can not find the page you're looking for.<br>
							<a href="<?php echo base_url(); ?>">
							Return home </a>
							or try the search bar below.
						</p>
						<form action="#">
							<div class="input-group input-medium">
								<input type="text" class="form-control" placeholder="keyword...">
								<span class="input-group-btn">
								<button type="submit" class="btn blue"><i class="fa fa-search"></i></button>
								</span>
							</div>
							<!-- /input-group -->
						</form>
					</div>
				</div>
			</div>
			</div>
            
            
            
</div>