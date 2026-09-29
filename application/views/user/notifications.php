<link href="<?php echo base_url(); ?>assets/global/plugins/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />


<style>
.row-flow-prime a {
	text-decoration: none;
}
.row-flow-prime em {
	font-size: 10px;
	padding-bottom: 10px;
}
.panel-group {
	padding-bottom: 5px !important;
	margin-bottom: 5px !important;
}
.tiles .tile:last-child{
	width: auto !important;	
}

.tiles {
  display: flex !important;
  flex-wrap: wrap !important;
}

.tiles .tile {
  flex-grow: 1;
  min-width: 10%;
}

.tiles .tile {
	position: relative;	
}
.tiles .tile .fa-bg {
	font-size: 200px !important;
	position: absolute;
	bottom: -20px;
	color: #fff;	
	opacity: 0.2;
	-moz-opacity: 0.2;
	-webkit-opacity: 0.2;
	margin: none !important;
	height: 100%;
}
</style>
<div class="row margin-top-20">
  <div class="col-md-12">
  
  
  
  
    <div class="tiles">
  
  		<div class="tile double bg-grey-cascade">
        	<div class="corner"></div><div class="check"></div>
            <i class="fa fa-home fa-2x fa-bg"></i>
            <div class="tile-body">
            	<h2><i class="fa fa-money"></i> <span class="pull-right">233,300,230.33</span></h2>
                <br>
                <h2><i class="fa fa-user"></i> <span class="pull-right">1,200</span></h2>
                
            </div>
            <div class="tile-object">
                <div class="name">
                     Sales
                </div>
                <div class="number">
                     
                </div>
            </div>
        </div>
        
        
        
        
  
		<div class="tile bg-purple-studio">
        	<div class="corner"></div><div class="check"></div>
            <i class="fa fa-bell fa-2x fa-bg"></i>
            <div class="tile-body">
                <i class="fa fa-bell-o animated shake fast"></i>
            </div>
            <div class="tile-object">
                <div class="name">
                     Notifications
                </div>
                <div class="number">
                     6
                </div>
            </div>
        </div>
        
        
        
					<div class="tile double bg-red-sunglo">
                    <div class="corner"></div><div class="check"></div>
						<div class="tile-body">
							<i class="fa fa-calendar  animated zoomIn fast"></i>
						</div>
						<div class="tile-object">
							<div class="name">
								 Events
							</div>
							<div class="number">
								 12
							</div>
						</div>
					</div>
	  				<div class="tile double bg-green-turquoise">
					<div class="corner"></div><div class="check"></div>
						<div class="tile-body">
							<h4>marcelo.cacho@panayelectric.com</h4>
							<p>
								 Re: Design Proposal
							</p>
							<p>
								 Please do something about the reponsive display, viewing via ipad...
							</p>
						</div>
						<div class="tile-object">
							<div class="name">
								<i class="fa fa-envelope"></i>
							</div>
							<div class="number">
								 14
							</div>
						</div>
					</div>
					<div class="tile double bg-blue-madison">
                    <div class="corner"></div><div class="check"></div>
                    <i class="fa fa-warning fa-2x fa-bg"></i>
						<div class="tile-body">
							<img width="32" height="32" src="<?php echo base_url(); ?>uploads/images/users/lucky-john-faderon/lucky-default.png" alt="">
							<h4>Announcements</h4>
							<p>
								 New dashboard look, easy access to your stuff..
							</p>
					  </div>
						<div class="tile-object">
							<div class="name">
								 Lucky John Faderon
							</div>
							<div class="number">
								 <?php echo date('F d, Y'); ?>
							</div>
						</div>
	  </div>
	  <div class="tile bg-green-meadow">
      <div class="corner"></div><div class="check"></div>
      <i class="fa fa-comments fa-2x fa-bg"></i>
						<div class="tile-body">
                        <img width="32" height="32" src="<?php echo base_url(); ?>uploads/images/users/lucky-john-faderon/lucky-default.png" alt="">
							<h4>Lucky John Faderon</h4>
							<p>
								Diin na to ang files?
							</p>
							
		</div>
						<div class="tile-object">
							<div class="name">
								 Messages
							</div>
							<div class="number">
								 12
							</div>
						</div>
					</div>
                    
     <div class="tile bg-yellow-lemon">
         <div class="corner"></div><div class="check"></div>
                            <div class="tile-body">
                                <i class="fa fa-tasks"></i>
            </div>
                <div class="tile-object">
                    <div class="name">
                         Tasks
                    </div>
                    <div class="number">
                         3
                    </div>
            </div>
        </div>
        
        <div class="tile bg-green-meadow">
         <div class="corner"></div><div class="check"></div>
         <i class="fa fa-users fa-2x fa-bg"></i>
                            <div class="tile-body">
                                <i class="fa fa-users"></i>
            </div>
                <div class="tile-object">
                    <div class="name">
                         Online
                    </div>
                    <div class="number">
                         5
                    </div>
            </div>
        </div>
        
  </div>
  </div>
</div>
<script src="<?php echo base_url(); ?>assets/global/plugins/datatables/jquery.dataTables.min.js" type="text/javascript"></script> 
<script src="<?php echo base_url(); ?>assets/global/plugins/datatables/dataTables.bootstrap.min.js" type="text/javascript"></script> 



<script>



$('.tiles').on('click', '.tile', function(e){
	$(this).toggleClass('selected');
});

 $(".tile").draggable({
	helper: 'clone',
	opacity: 0.8,
	scroll: true,
	refreshPositions: true,
	scrollSensitivity: 100,
}).resizable();



document.addEventListener('DOMContentLoaded', function () {
  if (!Notification) {
	alert('Desktop notifications not available in your browser. Try Chromium.'); 
	return;
  }

  if (Notification.permission !== "granted")
	Notification.requestPermission();
});

function notifyMe(title, text, icon) {
  if (Notification.permission !== "granted")
	Notification.requestPermission();
  else {
	var notification = new Notification(title, {
	  icon: icon,
	  body: text,
	});

	notification.onclick = function () {
	  window.open(base_url);      
	};

  }
}

notifyMe('P4 - Notify', "You have 6 pending tasks, 3 unread messages", base_url+'assets/global/img/logo/peco-small-logo-compress.png');

</script> 
