<!-- DATA TABLE CSS START!-->
<link href="<?php echo base_url(); ?>assets/global/plugins/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
<!-- DATA TABLE CSS END!-->

<style>
	.form-group.form-md-line-input{
		margin-top: 0px !important;	
		padding-top: 0px !important;	
	}

</style>

				
    <h3 class="page-title">
    <?php echo $pagename->pname; ?> <small><?php echo $pagename->desc; ?></small>
    </h3>
    <div class="row">
   		<div class="col-md-12">
            <div class="portlet light">
                <div class="portlet-body form">
                    <form class="">
                        <div class="form-body">
                            <div class="form-group form-md-line-input has-info">
                                <div class="input-icon">
                                    <input type="text" class="form-control" placeholder="Search: Service Number / Name / Address">
                                    <span class="help-block">for quick search, use transaction number to search specific applications.</span>
                                    <i class="fa fa-search"></i>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
          </div>
    </div>
    
    <div class="row">
   		<div class="col-md-12">
        	<div class="portlet light">
            <div class="portlet-title">
                    <div class="caption font-red-sunglo">
                        <i class="icon-settings font-red-sunglo"></i>
                        <span class="caption-subject bold uppercase"> Recent Applications</span>
                    </div>
                    <div class="actions">
                        <div class="btn-group">
                            <a class="btn btn-sm green dropdown-toggle" href="javascript:;" data-toggle="dropdown">
                            Actions <i class="fa fa-angle-down"></i>
                            </a>
                            <ul class="dropdown-menu pull-right">
                                <li>
                                    <a href="<?php echo base_url('module/'.$this->uri->segment(2).'/new'); ?>">
                                    <i class="fa fa-file"></i> New </a>
                                </li>
                                <li>
                                    <a href="<?php echo base_url('module/'.$this->uri->segment(2).'/pending'); ?>">
                                    <i class="fa fa-trash-o"></i> Pending </a>
                                </li>
                                <li>
                                    <a href="<?php echo base_url('module/'.$this->uri->segment(2).'/submited'); ?>">
                                    <i class="fa fa-ban"></i> Submited </a>
                                </li>
                                <li class="divider">
                                </li>
                                <li>
                                    <a href="javascript:;">
                                    <i class="i"></i> Make admin </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="portlet-body form">
                	<table class="table table-bordered table-condensed table-hover" id="app-list">
                    	<thead>
                        	<th>#</th>
                        	<th>Name</th>
                        	<th>Address</th>
									<th>Date Created </th>
                        	<th width="100">Status</th>
                        	<th width="50"></th>
                        </thead>
                        <tbody>
									
								<?php
								/* SAVE FOR MARLON REMEMBRANCE *
								$query =$this->db->query('SELECT pcao.sysid, concat(pcao.firstname," ", pcao.lastname) as Name,
																	concat(pcaoa.addrspecific,", " ,pd.names,", ", pc.names) as Address, pcao.datecreated
																	FROM prime_customer_accounts_owners as pcao
																	FROM prime_customer_accounts_main
																	INNER JOIN transaction_request_main AS TRN ON TRN.dataid = 
																	left join prime_customer_accounts_owners_address as pcaoa on pcaoa.ownerid = pcao.sysid
																	left join prime_districts as pd on pd.sysid = pcaoa.district
																	left join prime_city as pc on pc.sysid = pcaoa.city
														 ');
							    */
								
								$query =$this->db->query("
															SELECT TRN.sysid AS TRNID, MAIN.sysid AS ACCTID, CONCAT(OWNER.lastname, ', ', OWNER.firstname) AS NAMES, CONCAT(ADDR.addrspecific, ', ', DISTRICT.names, ', ', CITY.names) AS ADDRESS, TRN.datecreated AS CREATION, TRN.status AS STAT FROM prime_customer_accounts_main AS MAIN
															INNER JOIN transaction_request_main AS TRN ON TRN.dataid = MAIN.sysid
															LEFT JOIN prime_customer_accounts_owners AS OWNER ON OWNER.accountid = MAIN.sysid
															LEFT JOIN prime_customer_accounts_owners_address AS ADDR ON ADDR.ownerid = OWNER.sysid
															LEFT JOIN address_districts AS DISTRICT ON DISTRICT.sysid = ADDR.district
															LEFT JOIN address_city AS CITY ON CITY.sysid = ADDR.city
															WHERE TRN.stagesid = 3 AND TRN.moduleid = ".$this->model_admin->get_navigation_specific_details($this->uri->segment(2))->sysid."
														 ");

								?>
                                
								<?php 
								if($query->num_rows()>0){
									foreach ($query->result() as $row) { ?>
										<tr>
										<td><?php echo $row->ACCTID;?></td>
										<td><?php echo $row->NAMES;?></td>
										<td><?php echo $row->ADDRESS;?></td>
										<td><?php echo $row->CREATION;?></td>
										<td><span class="label label-success">New</span></td>
										<td>
                                        
                                        <?php 
										if ( $row->STAT == 0 ) { ?>
											<a href="<?php echo base_url('module/'.$this->uri->segment(2).'/edit/'.$row->ACCTID); ?>" class="btn btn-warning btn-xs"><i class="fa fa-pencil"></i></a>
                                            <?php } else { echo ''; } ?>
											<a href="<?php echo base_url('module/'.$this->uri->segment(2).'/view/'.$row->ACCTID); ?>" class="btn btn-info btn-xs"><i class="fa fa-search"></i></a>
										</td>
										</tr>
								<?php 
                                        }
                                    }
                                 ?> 

							
                        </tbody>
                    </table>
                	
                </div>
            </div>
        </div> 	
    </div>
    

<script src="<?php echo base_url(); ?>assets/global/plugins/datatables/jquery.dataTables.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/datatables/dataTables.bootstrap.min.js" type="text/javascript"></script>

<script>
	$('#app-list').dataTable({
		"order": [[ 3, "desc" ]]
	});
</script>