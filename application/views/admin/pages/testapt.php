<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/datatables/extensions/TableTools/js/dataTables.tableTools.min.js"></script>
<style>
    body, html, body * {
        font-family: Calibri;
    }
</style>

<?php
function validateDate($date)
{
    $d = DateTime::createFromFormat('Y-m-d h:i:s', $date);
    return $d && $d->format('Y-m-d h:i:s') === $date;
}

?>

<div class="page-content-wrapper">
    <div class="page-content  animated fadeInUp fast">
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light">
                    <div class="portlet-title">
                        <div class="caption"><?php echo $jobtype; ?></div>
                    </div>
                    <div class="portlet-body">
                        <table class="table table-condensed table-bordered tbl-xs" id="apt_data">
                            <thead>
                            <th>#</th>
                            <th>ESSR NO.</th>
                            <th>SERVNO</th>
                            <th>NAME</th>
                            <th>ADDRESS</th>
                            <th>JOB TYPE</th>
                            <th>Transactions</th>
                            <th></th>
                            <th></th>
                            </thead>
                            <tbody>
                            <?php

                            if($qry->num_rows()>0) {
                                $num = 1;


                                foreach($qry->result() as $row) {
                                    $trn_arr = explode('|', $row->TRN);
                                    $row_span = count($trn_arr);



                                    echo '<tr>';
                                    echo '<td>'.$num++.'</td>';
                                    echo '<td>'.$row->ESSR.'</td>';
                                    echo '<td>'.$row->SERVNO.'</td>';
                                    echo '<td>'.$row->NAME.'</td>';
                                    echo '<td>'.$row->ADDR.'</td>';
                                    echo '<td>'.$row->JOB.'</td>';
                                    echo '<td colspan="3">Transaction Details</td>';




                                    $time_av = '';
                                    $time_row_diff = '';
                                    $total = 0;
                                    krsort($trn_arr);
                                    $i = 0;

                                    foreach($trn_arr as $key => $trow) {
                                        $trow_arr = explode('/', $trow);
                                        $col_1 = (isset($trow_arr[0])) ? $trow_arr[0] : '';
                                        $col_2 = (isset($trow_arr[1])) ? $trow_arr[1] : '';

                                        $date_diff = '';
                                        $first_date = '';
                                        $last_date = '';

                                        if ($i == 0) {
                                            // first
                                            $first_date = $col_2;
                                        } else if ($i == $row_span - 1) {
                                            // last
                                            $last_date = $col_2;
                                        }


                                        $date_diff_r  = '';


                                        if($trow != '') {

                                            $next_val = '';
                                            $next = '';

                                            if(isset($trn_arr[$key-1])) {
                                                $next = explode('/', $trn_arr[$key-1]);
                                                if (count($next) > 1) {
                                                    if($col_1 != $next[0]) {
                                                        $col_2_next = (isset($next[1])) ? $next[1] : '';

                                                        $col_2_fixed = validateDate($col_2);
                                                        $col_2_next_fixed = validateDate($col_2_next);
                                                        $datetime1a = new DateTime($col_2);
                                                        $datetime1b = new DateTime($col_2_next);
                                                        $interval = $datetime1a->diff($datetime1b);
                                                        $date_spent_mt = $interval->format('%M');
                                                        $date_spent_d = $interval->format('%d');
                                                        $date_spent_h = $interval->format('%H');
                                                        $date_spent_mn = $interval->format('%I');
                                                        $date_spent_s = $interval->format('%S');
                                                        $date_diff_r = time_to_word($date_spent_mt, $date_spent_d, $date_spent_h, $date_spent_mn, $date_spent_s);
                                                    }else{
                                                        $col_2_next = '';
                                                    }
                                                } else {
                                                    $col_2_next = '';
                                                }
                                            }else{
                                                $col_2_next = '';
                                            }
                                            if($last_date) {
                                                $datetime1 = new DateTime($first_date);
                                                $datetime2 = new DateTime($last_date);
                                                $interval1 = $datetime1->diff($datetime2);
                                                $date_spent_mt = $interval1->format('%M');
                                                $date_spent_d = $interval1->format('%d');
                                                $date_spent_h = $interval1->format('%H');
                                                $date_spent_mn = $interval1->format('%I');
                                                $date_spent_s = $interval1->format('%S');
                                                $date_diff = time_to_word($date_spent_mt, $date_spent_d, $date_spent_h, $date_spent_mn, $date_spent_s);
                                            }

                                            echo '<tr>';
                                            echo '<td colspan="6"></td>';
                                            echo '<td width="">' . $col_1 . '</td>';
                                            echo '<td width="">' . $col_2 . '</td>';
                                            echo '<td width="300px;">' . $date_diff_r . '</td>';
                                            echo '</tr>';
                                            $i++;

                                        }



                                    }


                                    echo '<tr>';
                                    echo '<td colspan="7"></td>';
                                    echo '<td colspan="2"><b>Total: <span class="pull-right">'.$date_diff.'</span></b></td>';
                                    echo '</tr>';

                                    echo '<tr>';
                                    echo '<td colspan="8"></td>';
                                    echo '</tr>';
                                }

                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>

    $.extend(true, $.fn.DataTable.TableTools.classes, {
        "container": "btn-group tabletools-dropdown-on-portlet",
        "buttons": {
            "normal": "btn btn-sm default",
            "disabled": "btn btn-sm default disabled"
        },
        "collection": {
            "container": "DTTT_dropdown dropdown-menu tabletools-dropdown-menu"
        }
    });


    $('#apt_datas').dataTable({
        // Internationalisation. For more info refer to http://datatables.net/manual/i18n
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

        // Or you can use remote translation file
        //"language": {
        //   url: '//cdn.datatables.net/plug-ins/3cfcc339e89/i18n/Portuguese.json'
        //},

        "order": [
            [0, 'asc']
        ],

        "lengthMenu": [
            [5, 15, 20, -1],
            [5, 15, 20, "All"] // change per page values here
        ],
        // set the initial value
        "pageLength": 10,

        "dom": "<'row' <'col-md-12'T>><'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>", // horizobtal scrollable datatable

        // Uncomment below line("dom" parameter) to fix the dropdown overflow issue in the datatable cells. The default datatable layout
        // setup uses scrollable div(table-scrollable) with overflow:auto to enable vertical scroll(see: assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js).
        // So when dropdowns used the scrollable div should be removed.
        //"dom": "<'row' <'col-md-12'T>><'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r>t<'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>",

        "tableTools": {
            "sSwfPath": "<?php echo base_url(); ?>assets/global/plugins/datatables/extensions/TableTools/swf/copy_csv_xls_pdf.swf",
            "aButtons": [{
                "sExtends": "pdf",
                "sButtonText": "PDF"
            }, {
                "sExtends": "csv",
                "sButtonText": "CSV"
            }, {
                "sExtends": "xls",
                "sButtonText": "Excel"
            }, {
                "sExtends": "print",
                "sButtonText": "Print",
                "sInfo": 'Please press "CTR+P" to print or "ESC" to quit',
                "sMessage": "Generated by DataTables"
            }, {
                "sExtends": "copy",
                "sButtonText": "Copy"
            }]
        }
    });
</script>