<div class="page-content-wrapper" >

    <div class="page-content  animated fadeInUp fast" >
        <?php
        /**
         * Created by PhpStorm.
         * User: DUDEZ
         * Date: 7/21/2018
         * Time: 10:33 AM
         */
        // CHECK IF PASSWORD HAS CHANGED
        $user_info = get_users_info(user_id());

        if($user_info) {
            $check_confirm = $this->db->select()->from('prime_system_users_confirmation')
                ->where(array('personid' => $user_info->pid, 'status' => 2))
                ->get()->row();
            if($check_confirm) {
                redirect(base_url('profile', 'refresh'));
            } else {
                echo '<div class="note note-info note-bordered"><h3>Welcome, '.$user_info->firstname.'!</h3></div>';
            }
        } else {
            page_data_notfound('Account Information is not found!');
        }
        ?>
    </div>
</div>
