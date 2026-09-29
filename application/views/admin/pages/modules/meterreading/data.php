<?php
	$firstname = $this->model_query->get_owner_info($dataid)->FIRSTNAME;
?>

<style>
	.asset-pic{
		display: inline-block;	
	}
	.asset-pic .main{
		width: 100%;	
	}
	.asset-pic .sub{
		width: 30%;	
		height: 90px;
	}
	.asset-pic .sub.more{
		border: 1px solid #ccc;
	}	
</style>

        <div class="tab-pane fade in <?php ($task_flow==false) ? 'active' : ''; ?>" id="data">
            <div class="row">
             <div class="col-md-8">
             <div class="row">
             	<div class="col-md-12">
                <div class="portlet light">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-edit"></i>
                            <span class="caption-subject font-green-sharp bold uppercase">Information</span>
                            <span class="caption-helper">General</span>
                        </div>
                        <div class="tools">
                            <a href="javascript:;" class="collapse" data-original-title="" title="">
                            </a>
                            <a href="#portlet-config" data-toggle="modal" class="config" data-original-title="" title="">
                            </a>
                            <a href="javascript:;" class="reload" data-original-title="" title="">
                            </a>
                            <a href="javascript:;" class="fullscreen" data-original-title="" title="">
                            </a>
                            <a href="javascript:;" class="remove" data-original-title="" title="">
                            </a>
                        </div>
                    </div>
                    <div class="portlet-body">
						  <!-- Customer Information -->
                   	<legend>Customer Information:</legend>
                                <h3>Name: <strong><?php echo $this->model_query->get_owner_info($dataid)->FIRSTNAME;?> <?php echo $this->model_query->get_owner_info($dataid)->MIDDLENAME;?> <?php echo $this->model_query->get_owner_info($dataid)->LASTNAME;?>
                                </strong>
                                </h3>
										   <h4>Address: <strong><?php
										  echo $this->model_query->get_owner_info($dataid)->STREET.', ';
										  echo $this->model_query->get_owner_info($dataid)->DIST.', ';
										  echo $this->model_query->get_owner_info($dataid)->CITY;
										  
										  ?></strong> </h4>
										  
										  <h4>Total Connected Load:</h4> <h1><strong>6800 watts </h1></strong> 
										  <h4>Connection Type: </h4> <strong><h1> Special</h1> </strong> 
										  
									
									 						
						                       
                         
                         <div class="margin-top-20"></div>
                            
                       			 </div>	
					
                </div>
            </div>
          </div>
          
          
			 
        </div>
		  
		  <div class="col-md-4">
          	<div class="portlet light">
           
					
                <div class="portlet-body">

		
                    <center><b>	<label>Assign Lot and Book: </label></b><br></center>
						  <div class="form-group form-md-line-input" style="margin-top: -20px">
							<div class="col-md-12">                    
						  Lot:<input id="mrd_lot" name="mrd_lot" type="text" class="form-control  input-sm " placeholder="">
                    </div>
                </div>
                <div class="form-group form-md-line-input" style="margin-top: -20px">
               		<div class="col-md-12">
                    	Book:<input id="mrd_book" name="mrd_book" type="text" class="form-control  input-sm " placeholder="">
                    </div>
                </div>
					  <div class="form-group form-md-line-input" style="margin-top: -20px">
               		<div class="col-md-12">
                    	Group:<input id="mrd_group" name="mrd_group" type="text" class="form-control  input-sm " placeholder="">
                    </div>
                </div>
                        
               
                <input class="form-control" name="moduleid" readonly type="hidden" value="<?php echo $this->model_admin->get_navigation_specific_details($this->uri->segment(2))->sysid;?>" />
                    
                </div>
                <div class="form-actions margin-top-20 clearfix">

                    <div class="row">
                        <div class="col-md-12 margin-top-10">
								<script>
function tempFunction() {
    alert("Not finished changes with the databases.. :'(");
}
</script>
			
                            <button type="submit" id="save" class="btn blue btn-block btn-lg red-stripe" onclick="tempFunction()"><i class="fa fa-save fa-fw"></i> Save</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 margin-top-10">
                            <button type="button" id="draft" class="btn green btn-block red-stripe"><i class="fa fa-edit fa-fw"></i> Draft</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 margin-top-10">
                            <button type="button" id="cancel" class="btn btn-default btn-block red-stripe"><i class="fa fa-times fa-fw"></i> Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
          </div>
		  
				<!-- END PAGE HEADER-->
				<!-- BEGIN PAGE CONTENT-->
				
				
                
        
        
        <!-- END PAGE CONTENT-->
    </div>
    
</div>
      
