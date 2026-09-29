<?php
/**
 * Created by PhpStorm.
 * User: ITD-SE
 * Date: 7/3/2018
 * Time: 4:00 PM
 */

?>
<div class="page-content-wrapper animated fadeIn fast">
    <div class="page-content">


        <h3 class="page-title">
            Icons <small>system icons</small>
        </h3>
        <div class="page-bar">
            <?php echo create_breadcrumb(); ?>
        </div>

        <table class="table table-hover table-striped table-bordered table-condensed" id="tbl_icons">
            <thead>
            <th>ID</th>
            <th>Icon Class</th>
            <th>Preview</th>
            </thead>
            <tbody>
            <?php
            $qry_icons = $this->db->select()->from('system_icons')->get();
            if($qry_icons->num_rows()>0) {
                foreach($qry_icons->result() as $irow) {
                    echo '<tr>';
                    echo '<td>'.$irow->sysid.'</td>';
                    echo '<td class="icon">'.$irow->icon.'</td>';
                    echo '<td><i class="fa '.$irow->icon.'"></i></td>';
                    echo '</tr>';
                }
            }
            ?>
            </tbody>
        </table>

    </div>
</div>



<script>
    $('#tbl_icons').dataTable({
        bDestroy: true,
        bPaginate: true,
        bFilter: true,
        bInfo: true,
        scrollY: '300px',
        searchHighlight: true,
    });
    PECO.initDTNicescroller();
</script>

