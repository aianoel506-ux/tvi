<?php


class Model_messenger extends CI_Model
{
    function get_users_conversations() {
        $data = array();

        $msg = '';
        if(user_id() > 0) {
            $rec_userid = $this->input->post('userid');
            $frm_userid = user_id();


            $qry_converstaion_id = $this->db->query("
                SELECT cid FROM system_users_conversation_matrix
                WHERE userid = $rec_userid
            ")->row();

            if ($qry_converstaion_id) {

                $user_ids_arr = array();
                $qry_conversation_user_ids = $this->db->query("
                    SELECT userid FROM system_users_conversation_matrix
                    WHERE cid = {$qry_converstaion_id->cid}
                ");
                if($qry_conversation_user_ids->num_rows()>0) {
                    foreach($qry_conversation_user_ids->result() as $urow) {
                        $user_ids_arr[] = $urow->userid;
                    }
                    $data['userids'] = $user_ids_arr;
                    $data['cid'] = $qry_converstaion_id->cid;
                    if(in_array($frm_userid, $user_ids_arr)) {
                        $qry_converstion_user_matrix = $this->db->select(
                            '
                            ucms.sysid,
                            ucms.cid,
                            ucms.userid,
                            ucms.texts,
                            ucms.datecreated
                        '
                        )
                            ->from('system_users_conversation_messages AS ucms')
                            ->where(array('ucms.cid' => $qry_converstaion_id->cid))
                            ->where_in('ucms.userid', $user_ids_arr)
                            ->get();
                        if ($qry_converstion_user_matrix->num_rows() > 0) {
                            foreacH ($qry_converstion_user_matrix->result() as $row) {
                                $post_type = ($row->userid == $frm_userid) ? 'out' : 'in';


                                $user_info = get_users_info($row->userid);
                                $name = ($user_info) ? $user_info->lastname . ', ' . $user_info->firstname : $row->firstname;

                                $user_pic = get_users_pic_url($user_info->pid, true, true);

                                $msg .= '<li class="' . $post_type . ' post">';
                                $msg .= '<img class="avatar" alt="" src="' . $user_pic . '">';
                                $msg .= '<div class="message">';
                                $msg .= '<span class="arrow">';
                                $msg .= '</span>';
                                $msg .= '<a href="javascript:;" class="name">';
                                $msg .= $name;
                                $msg .= '</a>&nbsp;';
                                $msg .= '<br><span class="datetime small">';
                                $msg .= 'at ' . timeago($row->datecreated, sql_time()->DATETIME);
                                $msg .= '</span>';
                                $msg .= '<span class="body">';
                                $msg .= $row->texts;
                                $msg .= '</span>';
                                $msg .= '</div>';
                                $msg .= '</li>';

                                $data['list'] = $row;
                            }
                        }
                    }else{
                        $msg .= '<li class="post">';
                        $msg .= '<div class="message"><i class="fa fa-times text-danger"></i> No conversation yet!</div>';
                        $msg .= '</li>';
                    }
                }
            }else{
                $msg .= '<li class="post" id="empty_chat">';
                $msg .= '<div class="message"><i class="fa fa-question text-info"></i>Start your conversation!</span></div>';
                $msg .= '</li>';
            }
        }else{
            $msg .= '<li class="in post">';
            $msg .= '<div class="message">';
            $msg .= '<span class="arrow">';
            $msg .= '</span>';
            $msg .= '<a href="javascript:;" class="name">'.SYSTEM_NAME.'</a>';
            $msg .= '<span class="body">Session Timeout!</span>';
            $msg .= '</li>';
        }
        $data['msg'] = $msg;
        return json_encode($data);
    }

    function post_message() {
        $data = array();
        $message = $this->input->post('messages');
        $userid = $this->input->post('userid');
        $from = user_id();

        $echo = false;
        if($userid != 'undefined') {

        } else {
            $echo = true;
        }

        $user_info = get_users_info(false, true);
        $name = ($user_info) ? $user_info->firstname . ' ' . $user_info->lastname : 'Unknown';
        $personid = ($user_info && isset($user_info->pid)) ? $user_info->pid : false;
        if ($personid) {
            $user_pic = get_users_pic_url($personid, true, true);
        } else {
            $user_pic = base_url() . 'assets/global/img/admin_pic.png';
        }

        $data['echo'] = $echo;
        $data['name'] = $name;
        $data['text'] = $message;
        $data['avatar'] = $user_pic;
        $data['time'] = timeago(date('Y-m-d H:i:s'), sql_time()->DATETIME);
        return json_encode($data);
    }
}