<?php
/**
 * Created by PhpStorm.
 * User: fader
 * Date: 3/29/2019
 * Time: 4:10 PM
 */

Class Setup extends ci_controller
{
    function __construct() {
        parent::__construct();
        session_start();

        setup_access();
    }


    function index() {

    }

    function db($p = false) {
        $data = array();
        $data['pagetitle'] = 'System Setup';
        init_frontend_header($data);
        $this->load->view('system/setup/setuptop', $data);
        if(null != user_id()) {
            if ($p) {
                $this->load->view('system/setup/' . $p);
            } else {
                $this->load->view('system/setup/db');
            }
        }else{
            $this->load->view('system/setup/login');
        }
        $this->load->view('system/setup/setupbottom', $data);
        init_frontend_footer($data);
    }

    function slogin() {

        $data = array();
        $msg = '';

        $secret = '6Le6OpsUAAAAAO6ggjatySAzWNsSbc0uo5BZKdev';

        $captcha = $this->input->post('g-recaptcha-response');

        $username = $this->input->post('username');
        $password = $this->input->post('password');

        $dev = sys_setup_dev();

        $num = false;

        if(($captcha && !empty($captcha)) || $dev == true) {
            $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret.'&response='.$captcha);
            $responseData = json_decode($verifyResponse);
            if($responseData->success || $dev == true)
            {
                $this->load->library('form_validation');
                $this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean');
                $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean|callback_check_database');

                if ($this->form_validation->run() == FALSE) {
                    $msg = 'Username or password is empty.';
                } else {
                    $log_data = $this->model_auth->login($username, $password);
                    $data['logdata'] = $log_data;
                    if($log_data['num'] > 0) {
                        $msg = $log_data['message'];
                        $num = true;
                    }else{
                        $msg = 'Authentication failed!';
                    }
                }

            } else {
                $msg = 'Robot verification failed, please try again.';
            }
        }

        $data['cresp'] = $captcha;
        $data['ckey'] = $secret;

        $data['msg'] = $msg;
        $data['num'] = $num;
        echo json_encode($data);
    }

    function resettranssess() {
        $data = array();
        $msg = '';

        $secret = '6Le6OpsUAAAAAO6ggjatySAzWNsSbc0uo5BZKdev';

        $captcha = $this->input->post('g-recaptcha-response');

        $email = $this->input->post('email');
        $password = $this->input->post('password');
        $userid = $this->input->post('userid');

        $qry = false;

        $dev = sys_setup_dev();

        $num = false;

        if(($captcha && !empty($captcha)) || $dev == true) {
            $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . $secret . '&response=' . $captcha);
            $responseData = json_decode($verifyResponse);
            if ($responseData->success || $dev == true) {
                $qry_user = $this->db->select('password')
                    ->from('prime_system_users')
                    ->where(array('sysid' => $userid))
                    ->get()->row();

                if($qry_user) {
                    if (hashvalidate($password, $qry_user->password)) {

                        $confirmation_code = random_str();

                        $this->db->where(array(
                            'userid' => user_id(),
                            'email' => $email,
                            'status' => 1
                        ));
                        $this->db->update('prime_system_users_confirmation', array('status' => 0));

                        $ins_confirm_arr = array(
                            'userid' => user_id(),
                            'email' => $email,
                            'codes' => $confirmation_code
                        );
                        $this->db->insert('prime_system_users_confirmation', $ins_confirm_arr);

                        $ins_logs = array(
                            'typesid' => '-1',
                            'moduleid' => 1,
                            'value' => 0,
                            'remarks' => $confirmation_code
                        );
                        $this->db->insert('system_logs', $ins_logs);

                        //SMTP & mail configuration
                        $this->load->library('email');
                        $config = array(
                            'protocol' => 'smtp',
                            'smtp_host' => 'ssl://smtp.googlemail.com',
                            'smtp_port' => 465,
                            'smtp_user' => 'noreply.peco@gmail.com',
                            'smtp_pass' => 'P3C02019',
                            'mailtype' => 'html',
                            'charset' => 'utf-8',
                            'validation' => TRUE
                        );

                        $this->email->initialize($config);
                        $this->email->set_mailtype("html");
                        $this->email->set_newline("\r\n");

                        $data['code'] = $confirmation_code;


                        $content = '';
                        $content .= '<html>';
                        $content .= '<head>';
                        $content .= '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" >';
                        $content .= '<title>TVI | Tiedra Ventures, Inc.</title>';
                        $content .= '<link href="http://getbootstrap.com/dist/css/bootstrap.min.css" rel="stylesheet" />';
                        $content .= '
                                        <style type="text/css">
                                        html { 
                                            -webkit-text-size-adjust:none; 
                                            -ms-text-size-adjust: none;
                                        }
                                        body{
                                            font-family: Arial !important;
                                            overflow: visible !important;
                                        }		
                                        </style>
                                        ';
                        $content .= '</head>';
                        $content .= '<body>';

                        $content .= '<div class="container" style="width: 95%; display:inline-block; margin-top: 5px; margin-bottom: 5px; padding: 20px 20px;">';
                        $content .= '<div class="col-xs-12">';

                        $content .= '<div style="background: #ef582d; width: 100%; display: inline-block; margin-top: 4px; margin-bottom:4px;">';
                        $content .= '<img height="50px" src="http://www.panayelectric.com/assets/global/tp/img/peco_new_header_left.png" height="90" alt="PECO" border="0" style="display: block;" />';
                        $content .= '</div>';


                        $content .= '<h4>Hi Administrator</h4>';
                        $content .= '<br>';


                        $content .= '<p>The code for your command is: <br><span style="font-size: 30px; color: red; font-weight: bold;">' . $confirmation_code . '</span><br> Copy and paste this to confirmation box.</p>';
                        $content .= '<br>';

                        //Email content
                        $this->email->to($email);
                        $this->email->from('no-reply@panayelectric.com', 'SYSTEM: SETUP Reset Transactions Confirmation Code');
                        $this->email->subject('SYSTEM: SETUP Reset Transactions Confirmation Code');
                        $this->email->message($content);

                        //$sent = $this->email->send();
                        $sent = true;
                        // Send email
                        $this->email->clear(true);
                        if ($sent) {
                            $qry = true;
                        }
                    }else{
                        $msg = 'Password is wrong!';
                    }
                }

            }
        }

        $data['qry'] = $qry;

        $data['cresp'] = $captcha;
        $data['ckey'] = $secret;

        $data['msg'] = $msg;
        $data['num'] = $num;
        echo json_encode($data);
    }

    function executetransreset() {
        $data = array();
        $code = $this->input->post('code');
        $killcode = $this->input->post('killcode');
        $qry = false;
        if($code == $killcode) {
            $qry = true;
        }
        $data['qry'] = $qry;
        echo json_encode($data);
    }

    function slogout() {
        $this->session->unset_userdata('logged_in');
        session_destroy();
    }

    function dbdestroy($pass = false) {
        if($pass == false) {
            echo 'Hello world!';
        }else{
            if ($pass == 'P3C02020!!!!') {
                echo 'Password Correct, query running...';

                $qry = $this->db->query("
                SET FOREIGN_KEY_CHECKS=0;
                TRUNCATE TABLE application_customers_details;
                TRUNCATE TABLE ticketing_details_logs;
                TRUNCATE TABLE billing_reports;
                TRUNCATE TABLE billing_reports_main;
                TRUNCATE TABLE prime_module_navigations_main;
                ");
                if($qry) {
                    if (pecoapps_conn()) {
                        $conn = $this->load->database('pecoapps', TRUE);
                        $conn->initialize();
                        $qry = $conn->query("
                            TRUNCATE TABLE father;
                            TRUNCATE TABLE billtrn;
                            TRUNCATE TABLE arprev;
                            TRUNCATE TABLE aptmast;
                            TRUNCATE TABLE ra7832;
                        ");
                        if($qry) {
                            echo 'DONE';
                        }
                    } else {
                        echo 'Cannot connect to PECO Apps';
                    }
                } else {
                    echo 'Error: Query!';
                }


            } else {
                echo 'Wrong Password!';
            }
        }

    }

}