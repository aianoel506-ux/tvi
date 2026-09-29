<?php
$user_nav_ids = array();
$get_user_nav_arr = get_users_info_navigation_matrix();

if($get_user_nav_arr) {
    foreach($get_user_nav_arr as $aarow) {
        $user_nav_ids[] = $aarow->navid;
    }
}

if(count($user_nav_ids) > 0) {
    $sql_subpages = $this->db->select()
        ->from('prime_module_navigations_main')
        ->where(array('parent' => $navid, 'status' => 1))
        ->where_in('sysid', $user_nav_ids)
        ->get();
    if ($sql_subpages->num_rows() > 0) {
        ?>


        <h3 class=" font-red-flamingo bold "><i class="fa <?php echo $pageicon; ?>"></i> <?php echo $pagetitle; ?>
            <span class="pull-right small"><?php echo $pagedesc; ?></span>
        </h3>
        <hr>
        <div class="row">
            <div class="col-md-12">
                <div class="row" id="">
                    <?php
                    foreach ($sql_subpages->result() as $row) {
                        $link_action = ($row->type > 2) ? 'target="_blank "' : '';
                        $qry_session_module = $this->db->select()->from('prime_module_users_logs')
                            ->where(array('moduleid' => $row->sysid, 'userid' => user_id()))
                            ->get()->row();

                        if ($row->type == 3) {
                            $link = base_url($row->url);
                        } else {
                            $link = base_url('module/' . $row->hashcode . '/' . $row->url);
                        }
                        echo '<div class="col-lg-3 col-md-4 col-xs-12" id="">';
                        echo '<div class="mt-element-ribbon bg-grey-steel">';
                        echo '<div class="ribbon ribbon-shadow ribbon-color-success uppercase">';
                        echo '<a ' . $link_action . ' href="' . $link . '" class="btn btn-default btn-xs inline pull-right "><i class="fa ' . $row->icon . ' text-' . $row->htmlclass . ' fa-fw"></i> ' . $row->name . '</a>';
                        echo '</div>';
                        echo '<p class="ribbon-content small font-blue" style="min-height: 50px; padding-top: 8px !important; padding-bottom: 5px !important;">';

                        if ($qry_session_module) {
                            echo 'Last Visit: <br>' . $qry_session_module->datecreated;
                        }

                        echo '</p>';
                        echo '</div>';
                        echo '</div>';

                    }
                    ?>
                </div>
            </div>
        </div>

        <?php
    } else {
        page_data_notfound('No sub pages found!');
    }
}else{
    page_file_notfound('No Access Module Assigned', 'You have no access modules in this part!');
}
?>