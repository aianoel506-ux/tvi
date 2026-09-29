<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
session_start(); // STARTING SESSION DATA
class Messenger extends CI_Controller
{
    private $user_login;
    public function __construct()
    {
        parent::__construct();
        $this->load->model('model_admin');
        $this->load->model('model_messenger');
        $this->user_login = $this->session->userdata('logged_in');
    }

    function echo_response()
    {
        $message = $this->input->post('messages');
        $data = array();
        if(user_id()>0) {
            $response_sessions = $this->session->userdata('user_chat_logs');
            if ($response_sessions['keywoard'] != '' && ($message == 'yes' || $message == 'YES')) {
                $data['response'] = 'What suppose to be my response to that statement? "<b>' . $response_sessions['keywoard'] . '</b>"';
                $chat_logs_arr = array(
                    'keywoard' => $response_sessions['keywoard'],
                    'response' => true,
                );
                $this->session->set_userdata('user_chat_logs', $chat_logs_arr);
            } else {
                $response_sessions = $this->session->userdata('user_chat_logs');
                if (isset($response_sessions['response']) && $response_sessions['response'] == true) {
                    $ins_chat = array(
                        'keywords' => $response_sessions['keywoard'],
                        'response' => $message
                    );
                    $this->db->insert('system_echo', $ins_chat);
                    $data['response'] = 'Thank you for your help!, I\'d love to use this answer next time!';
                    $this->session->unset_userdata('user_chat_logs');
                } else {
                    $query = $this->db->query("SELECT * FROM system_echo WHERE keywords LIKE '%" . $message . "%'")->row();
                    if ($query) {
                        if ($query->tags == "system") {
                            if ($message == "guess my name") {
                                $data['response'] = "Your name is : <strong class='text-danger'>" . $this->user_login['system_user_sessname'] . "</strong>";
                            } else {
                                $data['response'] = "Sorry, unable to comply";
                            }
                        } else if ($message == "log me out") {
                            $data['response'] = 'logout_user';
                        } else {
                            $data['response'] = $query->response;
                        }
                    } else {
                        $chat_logs_arr = array(
                            'keywoard' => $message
                        );
                        $this->session->set_userdata('user_chat_logs', $chat_logs_arr);
                        $data['response'] = "Sorry, my response is limited,  would you like to add an answer to that?";
                    }
                }
            }
        }else{
            $data['response'] = '<span class="text-danger">Session timeout!</span>';
        }
        echo json_encode($data);
    }

    function getechointro() {
        $data = array();
        $msg = '';
        $txt = '';
        $query = $this->db->query("SELECT * FROM system_echo WHERE keywords = 'intro'")->row();
        if($query) {
            $user_info = get_users_info(false, true);
            $user_name = ($user_info) ? '<b>'.$user_info->firstname.'</b>' : '<b>User</b>';
            $user_name_text = ($user_info) ? $user_info->firstname : 'User';
            $msg = 'Hi, ' .$user_name . ', ' . $query->response;
            $txt = 'Hi, ' .$user_name_text . ', ' . $query->response;
        }
        $data['msg'] = $msg;
        $data['txt'] = $txt;
        $data['online'] = SYSTEM_ONLINE;
        echo json_encode($data);
    }

    function getconversations() {
        echo $this->model_messenger->get_users_conversations();
    }
    function postmessage() {
        echo $this->model_messenger->post_message();
    }
}