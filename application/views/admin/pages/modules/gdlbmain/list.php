
<style>
.form-md-line-input {
	posiotn: relative !important;	
}
.form-md-line-input .fileinput .input-group-addon{
	background: rgba(177,176,176,0.47) !important;
	z-index: 3000 !important;	
}
.form-md-line-input .fileinput .input-group-addon .btn.red-intense {
	background: rgba(251,124,126,0.77) !important;
}
.form-md-line-input .select2-container{
	margin-bottom: 0px !important;
}
.select2-drop{
	margin-top: -15px !important;
}
.portlet.table {
	padding: 0px 0px !important;	
}

</style>

				
    <h3 class="page-title">
    <?php echo $pagename->pname; ?> <small><?php echo $pagename->desc; ?></small>
    </h3>
    <div class="row">
		<form role="form" class="form-horizontal asset-entry-form" id="entry-form-ajaxify">	
       
        	<div class="col-md-12">
            <div class="portlet light table">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-edit"></i>
                        <span class="caption-subject font-green-sharp bold uppercase">GDLB</span>
                        <span class="caption-helper">Maintenance</span>
                        
                        <div class="btn-group" style="margin-left: 50px;">
                            <button type="button" class="btn btn-success btn-xs">Active</button>
                            <button type="button" class="btn btn-danger btn-xs">In-Active</button>
                        </div>
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
                <div class="portlet-body ">
				
                  <table class="table table-responsive table-hover table-striped table-condensed table-bordered tbl-sm" id="gdlb_tbl">
                    <thead>
                        <th></th>
                        <th>Group</th>
                        <th>District</th>
                        <th>Lot</th>
                        <th>Book</th>
                        <th>No. of Cust.</th>
                        <th>Limit</th>
                        <th>CAD Load Assessment</th>
                        <th>Monthly Load Stats.</th>
                        <th>Diff</th>
                        <th>Load Increase</th>
                        <th width="60px">Control</th>
                    </thead>
                    <tbody>
                    	<?php
							$qry_gdlb = $this->db->select()->from('gdlb_main')->get();
							if($qry_gdlb->num_rows()>0) {
								$num = 1;
								foreach($qry_gdlb->result() as $row) {
									$count_cust = $this->db->select('count(accountid) AS CNT')
										->from('customer_accounts_glb')
										->where('gdlbid', $row->sysid)->group_by('gdlbid')
										->get()->row();
									$count_cust = ($count_cust) ? $count_cust->CNT : 0;
									$count_pers = ($count_cust/$row->limit) * 100;
									if($count_pers>=50) {
										$count_colr = 'rgba(244, 150, 56, 0.30)';
									}else{
										if($count_pers>=80) {
											$count_colr = 'rgba(191, 89, 63, 0.30)';
										}else{ 
											$count_colr = 'rgba(65,177,38,0.30)';
										}
									}

									$cad_load = rand(20000, 999999);
									$bill_load = rand(60000, 999999);
									if($cad_load > $bill_load) {
									    $adds_per = ($cad_load * 0.10);
                                        $bill_load = ($cad_load + $adds_per);
                                    }
									$load_inc = ((($bill_load - $cad_load) / $cad_load) * 100);

                                    $row_class = '';

									if($load_inc > 100) {
									    $row_class = 'row-danger';
                                    }

									if($load_inc > 85) {
									    $row_class = 'row-warning';
                                    }

									$diff_load = ($bill_load - $cad_load);
									echo '<tr class="'.$row_class.'">';
									echo '<td>'.$num++.'</td>';
									echo '<td>'.$row->g.'</td>';
									echo '<td>'.get_address_name($row->d, 'district')->addrname.'</td>';
									echo '<td>'.str_pad($row->l, 2, '0', STR_PAD_LEFT).'</td>';
									echo '<td>'.str_pad($row->b, 2, '0', STR_PAD_LEFT).'</td>';
									echo '<td style="position: relative">';
									echo '<span style="z-index: 3"><b>'.number_format($count_cust).'</b> / '.number_format($count_pers, 2).'%</span>';
									echo '<div style="position: absolute; top: -1px; left: -1px; background-color: '.$count_colr.'; width: '.$count_pers.'%; height: 100%;"></div>';
									echo '</td>';
									echo '<td>'.$row->limit.'</td>';
									echo '<td class="number">'.number_format($cad_load).'</td>';
									echo '<td class="number">'.number_format($bill_load).'</td>';
									echo '<td class="number">'.number_format($diff_load).'</td>';
									echo '<td class="number">'.number_format($load_inc, 2).'%</td>';
									echo '<td><button class="btn btn-warning btn-xs"><i class="fa fa-edit"></i></button></td>';
									echo '</tr>';
								}
							}
						?>
                    </tbody>
                  </table>

                </div>		

            </div>
          </div>
          
          </form>

        </div>
				<!-- END PAGE HEADER-->
				<!-- BEGIN PAGE CONTENT-->
				
	
<script src="<?php echo base_url(); ?>assets/global/plugins/select2/select2.min.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/datatables/jquery.dataTables.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/datatables/dataTables.bootstrap.js"></script>

<script type="text/javascript">
	var tbl = $('#gdlb_tbl');
	tbl.dataTable({
        "language": {
            "aria": {
                "sortAscending": ": activate to sort column ascending",
                "sortDescending": ": activate to sort column descending"
            },
            "emptyTable": "No data available in table",
            "info": "Showing _START_ to _END_ of _TOTAL_ entries",
            "infoEmpty": "No entries found",
            "infoFiltered": "(filtered1 from _MAX_ total entries)",
            "lengthMenu": "Show _MENU_ entries",
            "search": "Search:",
            "zeroRecords": "No matching records found"
        },
        "bStateSave": true,
        "lengthMenu": [
            [5, 15, 20, 30, 50, 100, -1],
            [5, 15, 20, 30, 50, 100, "All"]
        ],
        "pageLength": 50,
        "language": {
            "lengthMenu": " _MENU_ records",
            "paging": {
                "previous": "Prev",
                "next": "Next"
            }
        },
        "columnDefs": [{
            'orderable': true,
            'targets': [0]
        }, {
            "searchable": false,
            "targets": [0]
        }],
        "order": [
            [1, "asc"]
        ],
    });
    var tableWrapper = $('#gdlb_tbl_wrapper');
    tableWrapper.find('.dataTables_length select').addClass("form-control").select2(); // modify table per page dropdown
</script>