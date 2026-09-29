<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Test extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
    }
    function index (){

        $data = array();
        $data['pagetitle'] = 'PECO - Access Test';
        init_frontend_header($data);

        echo '<div style="width: 50%; margin: auto 25%; padding: 30px 30px; display: inline-block; margin-top: 10%; text-align: center; background: #f1f1e3; border: #ccc;">';
        if(check_whitelisting() && check_whitelisting()->allowed == true) {
            echo '<h3 style="color: limegreen"><i class="fa fa-check"></i> Access Allowed!</h3>';
        }else{
            echo '<h3 style="color: red"><i class="fa fa-warning"></i> Access Forbidden!</h3>';
            echo '<hr>';
            echo '<pre>';
            print_r(check_whitelisting());
            echo '</pre>';
        }
        echo '</div>';

        init_frontend_footer($data);
    }
    function computeemployeenetpay(){
        $data = array();
        $data['datas'] =  compute_employee_netpay(656,5, 2019, 1, 0, 3078, 1);
       // $data['datas'] =  compute_employee_netpay(551,6, 2019, 2, 0, 128 , 1);
        echo '<pre>';
       //$data['data2'] = get_employee_transactions(159, 9, 2018, 1, 0, 129) ;
        print_r($data);
    }
    function computeemployeenetpaytemp(){
        $data = array();
        $data['datas'] =  compute_employee_netpay_temp(551, 6, 2019,1, 0, 128 , 1);
       // $data['datas'] =  compute_employee_netpay(551,6, 2019, 2, 0, 128 , 1);
        echo '<pre>';
       //$data['data2'] = get_employee_transactions(159, 9, 2018, 1, 0, 129) ;
        print_r($data);
    }
    function getemployeettransactions(){
        $data = array();
        $data['datas'] =  get_employee_transactions(150, 1, 2019, 1, 1, 129);
        //  $data['data2'] = get_employee_transactions(159, 9, 2018, 1, 0, 129) ;
        echo json_encode($data);
    }
    function displaypayslip(){
        $data = array();
        $data['payslip'] = get_payslip_trn(551 , 1 , 2019  ,1 , 128);
        echo json_encode($data);
    }
    function getwan() {
        echo $_SERVER['HTTP_CLIENT_IP'];
    }
    function convert(){
        $str = "03:23:00 AM";
        $time_in_24_hour_format  = date("H:i A", strtotime($str));
        echo $time_in_24_hour_format;
    }
    function gettardtest(){
        $data = array();

        $data['datatest'] = checkempsched(142, 28, 8 , 2018 , 301);
        echo json_encode($data);
    }
    function testresult(){
        $data = array();

        $amoutspecifiedtime1 = '12:00:00';
        $pmoutspecifiedtime2 = '5:00:00';

        $amout = '11:00:00';
        $pmout = '4:59:59';

        $amundertimeresult = 0;
        $pmundertimeresult = 0;


        if($amout >= $amoutspecifiedtime1){
            $amundertimeresult = '00:00:00';
            $data['amundertime'] = $amundertimeresult;
        }else{
            $amundertime  = ((strtotime($amoutspecifiedtime1) - strtotime($amout)+ 86400) % 86400) / 60;
            $amundertime = $this->convertminutestotimeformat($amundertime);
            $data['amundertime'] = $amundertime;
        }

        if($pmout >= $pmoutspecifiedtime2){
            $pmundertimeresult= '00:00:00';
            $data['pmundertime'] = $pmundertimeresult;
        }else{
            $pmundertime  = ((strtotime($pmoutspecifiedtime2) - strtotime($pmout)+ 86400) % 86400) / 60;
            $pmundertime = $this->convertminutestotimeformat($pmundertime);
            $data['pmundertime'] = $pmundertime;
        }

        echo json_encode($data);
    }

    function convertminutestotimeformat($minutesvalue){
        $hours = floor($minutesvalue / 60);
        $minutes = ($minutesvalue % 60);
        $seconds = floor(($minutesvalue * 60) % 60) ;
        return sprintf('%02d:%02d:%02d', $hours, $minutes,$seconds);
    }
    function getsalary(){
        echo get_emp_basic_salary(159);
    }
    function drawcalendar(){
        echo draw_employee_calendar(9, 2018, 551);
    }
    function subtracttime (){
        $time1 = '09:00:00';
        $time2 = '09:06:00';
        $timeDifference = (strtotime($time2) - strtotime($time1) + 86400) % 86400;
       echo $timeDifference;


    }
    function loopdate(){
        // Set timezone
        date_default_timezone_set('UTC');

        // Start date
        $date = '2018-09-01';
        // End date
        $end_date = '2018-09-15';

        while (strtotime($date) <= strtotime($end_date)) {
            $date = date ("Y-m-d", strtotime("+1 day", strtotime($date)));
        }
    }

    function updateempid(){

        $sql = $this->db->select("sysid , datestart , YEAR(datestart) as YEAR , MONTH(datestart) as MONTH")->from("prime_employee_main")
            ->where(array('status' => 1 , "type" => 1))->get();
        foreach ($sql->result() as $row){
            $month = str_pad($row->MONTH, 2, '0', STR_PAD_LEFT );
            $inc = str_pad($row->sysid, 4, '0', STR_PAD_LEFT );
            $newid = $row->YEAR.$month.$inc;
            echo $row->sysid.' - '.$row->datestart.' - '.$newid.'<br>';



        }

    }

    function writetxtfile(){
      /* $filepath = "D:createdfile.txt";
        $handle = fopen($filepath, "w");

                //write to file

                // Open the file to get existing content
                $current = file_get_contents($filepath);
                // Append a new person to the file
                    $current .= "John Smith\t\t"."1000\n";
                    $current .= "John Smith\t\t"."1000\n";
                    $current .= "John Smith\t\t"."1000\n";
                // Write the contents back to the file
                file_put_contents($filepath, $current); */
        $file = 'C:\xampp\htdocs\erp\uploads\payroll\2018-10.txt';
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename='.basename($file));
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        ob_clean();
        flush();
        readfile($file);
        exit;

    }

    function getpayrolldata(){

        $montharr = array(
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December',
        );
        $getpayrolldatalist =  $this->db->select("sysid,months,years,paytype")
            ->from("payroll_reports_group")
            ->where(array("status" => 301 , "payclass" => 128 , "sysid" => 76))
            ->get();
        if($getpayrolldatalist->num_rows() > 0){
            foreach ($getpayrolldatalist->result() as $prgrow){
                $html = '';
                $html .= '<h3>'.$prgrow->years.' '.$montharr[$prgrow->months].' '.$prgrow->paytype.'</h3>';
                $html .= '<table class="table table-bordered table-condensed">';
                $html .= '<thead>';
                // $html .= '<th>Year</th>';
                //  $html .= '<th>Months</th>';
                $html .= '<th>Lastname</th>';
                $html .= '<th>Firstname</th>';
                $html .= '<th>CCID</th>';
                $html .= '<th>Basic</th>';

                $getcontributions = $this->db->query("SELECT
                            payroll_matrix.`names`
                            FROM
                            payroll_matrix
                            WHERE
                            payroll_matrix.functions = 1 AND
                            payroll_matrix.effects = 0
                            AND typesid NOT IN (73 ,1010 )
                            ");
                if($getcontributions->num_rows() > 0){
                    foreach ($getcontributions->result() as $row){
                        $html .= '<th>'.$row->names.'</th>';
                    }
                }


                $getearnings = $this->db->query("SELECT
                            payroll_matrix.`names`
                            FROM
                            payroll_matrix
                            WHERE
                            payroll_matrix.functions = 1 AND
                            payroll_matrix.effects = 1
                            AND typesid NOT IN (265 )
                            ");
                if($getearnings->num_rows() > 0){
                    foreach ($getearnings->result() as $row){
                        $html .= '<th>'.$row->names.'</th>';
                    }
                }

                $getloans = $this->db->query("SELECT
                            payroll_matrix.`names`
                            FROM
                            payroll_matrix
                            WHERE
                            payroll_matrix.functions = 0 AND
                            payroll_matrix.effects = 1
                  
                            ");
                if($getloans->num_rows() > 0){
                    foreach ($getloans->result() as $row){
                        $html .= '<th>'.$row->names.'</th>';
                    }
                }


                $getdeductions = $this->db->query("SELECT
                            payroll_matrix.`names`, payroll_matrix.`typesid`
                            FROM
                            payroll_matrix
                            WHERE
                            payroll_matrix.functions = 0 AND
                            payroll_matrix.effects = 0
                  
                            ");
                if($getdeductions->num_rows() > 0){
                    foreach ($getdeductions->result() as $deductionrow){
                        $html .= '<th>'.$deductionrow->names.'</th>';
                    }
                }


                $html .= '</thead>';
                $html .= '<tbody>';

                $sql = $this->db->query("SELECT prm.sysid, prg.years , prg.months , prg.paytype , prm.empid , p.lastname , p.firstname ,  prm.ccid , prm.basic  FROM payroll_reports_group as prg 
JOIN payroll_reports_main as prm ON prm.groupid = prg.sysid
JOIN prime_employee_main as pem ON pem.sysid = prm.empid
JOIN person as p ON p.sysid  = pem.personid
WHERE prg.years = $prgrow->years  AND prg.`status` = 301 AND prg.sysid = $prgrow->sysid AND prg.months = $prgrow->months
ORDER BY p.lastname");
                if($sql->num_rows() > 0){
                    foreach ($sql->result() as $row){
                        $payrollid = $row->sysid;

                        $html .= '<tr>';
                        // $html .= '<td>'.$row->years.'</td>';
                        //  $html .= '<td>'.$row->months.'</td>';
                        $html .= '<td>'.$row->lastname.'</td>';
                        $html .= '<td>'.$row->firstname.'</td>';
                        $html .= '<td>'.$row->ccid.'</td>';
                        $html .= '<td>'.number_format($row->basic , 2).'</td>';

                        //FOR CONTRIBUTION
                        $getcontributions = $this->db->query("SELECT
                            payroll_matrix.`names` , payroll_matrix.`typesid`
                            FROM
                            payroll_matrix
                            WHERE
                            payroll_matrix.functions = 1 AND
                            payroll_matrix.effects = 0
                            AND typesid NOT IN (73 ,1010 )
                            ");
                        if($getcontributions->num_rows() > 0){
                            foreach ($getcontributions->result() as $controw){
                                $html .= '<td>'.number_format( getpayrolltrn($payrollid , $controw->typesid) , 2).'</td>';

                            }
                        }
                        // FOR EARNINGS


                        $getearnings = $this->db->query("SELECT
                            payroll_matrix.`names` , payroll_matrix.`typesid`
                            FROM
                            payroll_matrix
                            WHERE
                            payroll_matrix.functions = 1 AND
                            payroll_matrix.effects = 1
                            AND typesid NOT IN (265 )
                            ");
                        if($getearnings->num_rows() > 0){
                            foreach ($getearnings->result() as $earningrow){
                                $html .= '<td>'.number_format( getpayrolltrn($payrollid , $earningrow->typesid) , 2).'</td>';
                            }
                        }

                        //LOANS

                        $getloans = $this->db->query("SELECT
                            payroll_matrix.`names`, payroll_matrix.`typesid`
                            FROM
                            payroll_matrix
                            WHERE
                            payroll_matrix.functions = 0 AND
                            payroll_matrix.effects = 1
                  
                            ");
                        if($getloans->num_rows() > 0){
                            foreach ($getloans->result() as $loansrow){
                                $html .= '<td>'.number_format( getpayrolltrn($payrollid , $loansrow->typesid) , 2).'</td>';
                            }
                        }


                        //DEDUCTIONS

                        $getdeductions = $this->db->query("SELECT
                            payroll_matrix.`names`, payroll_matrix.`typesid`
                            FROM
                            payroll_matrix
                            WHERE
                            payroll_matrix.functions = 0 AND
                            payroll_matrix.effects = 0
                  
                            ");
                        if($getdeductions->num_rows() > 0){
                            foreach ($getdeductions->result() as $deductionrow){
                                $html .= '<td>'.number_format( getpayrolltrn($payrollid , $deductionrow->typesid) , 2).'</td>';
                            }
                        }



                        $html .= '</tr>';
                    }
                }
                $html .= '</tbody>';
                $html .= '</table>';
                $html .= '<br>';
                $html .= '<br>';
                echo  $html;
            }
        }




    }
    function testconfipayroll(){
        $html = '';
        $html .= '<table class="table table-bordered">';
        $html .= '<thead>';
        $html .= '<th>1 month</th>';
        $html .= '<th>1st Half</th>';
        $html .= '<th>2nd Half</th>';
        $html .= '</thead>';
        $html .= '<tbody>';
        $sql = $this->db->select("net")->from("payroll_reports_main")
            ->where(array("groupid" => 41))->get();
        if($sql->num_rows() > 0){
            foreach ($sql->result() as $row){

                $html .= '<tr>';
                $html .= '<td>'.$row->net.'</td>';
                $html .= '<td>'.($row->net / 2).'</td>';
                $html .= '<td>'.($row->net / 2).'</td>';
                $html .= '</tr>';

            }
        }
        $html .= '</tbody>';
        $html .= '</table>';

        echo $html;
    }
    function testrun(){
        $echo = '';
        $echo .= '<h1>Test Run';
        $echo .= '<br>';
        $echo .= 'Hey, Hi, Hello.</h1>';

        echo $echo;
    }
    function getpersonsdata(){
        $data = array();

        $sql = $this->db->select("firstname, lastname")->from("person")->get();
        if($sql->num_rows() > 0){
            foreach ($sql->result() as $row){
                $data['persondata'][] = array(
                    'firstname23' => $row->firstname,
                    'lastname23' => $row->lastname
                );
            }
        }

        echo json_encode($data);
    }

    function testdata(){
        $data = array();
        $sql = $this->db->select("bioid, logtime, logdate, workcode, dateupdated, datecreated")->from("test")->get();
        if($sql->num_rows() > 0){
            foreach ($sql->result() as $row){
                $data['test'][] = array(
                    'bioid' => $row->bioid,
                    'logtime' => $row->logtime,
                    'logdate' => $row->logdate,
                    'workcode' => $row->workcode,
                    'dateupdated' => $row->dateupdated,
                    'datecreated' => $row->datecreated
                );
            }
        }
        echo json_encode($data);
    }
    function getbarangays(){
        $data = array();

        $sql = $this->db->select("sysid , texts")->from("address_barangay")->get();
        if($sql->num_rows() > 0){
            foreach ($sql->result() as $row){
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->texts
                );
            }
        }

        echo json_encode($data);
    }

    function testbioid(){
        $data = array();

        $sql = $this->db->select("sysid , bioid")->from("test")->get();
        if($sql->num_rows > 0){
            foreach ($sql->result() as $row){
                $data['list'][] = array(
                  'id' => $row->sysid,
                  'text'  => $row->bioid
                );
            }
        }

        echo  json_encode($data);
    }

    function register(){
        $data = array();

        $firstname = $this->input->post('fname');
        $middlename = $this->input->post('mname');
        $lastname = $this->input->post('lname');
       
        $insarr = array(
            'lastname' => $lastname,
            'firstname' => $firstname,
            'middlename' => $middlename
        );

        $sql = $this->db->insert("test2" , $insarr);
        $data['errormessage'] = $this->db->_error_message();
        if($sql){
            $msg = 'OK';
        }else{
            $msg = 'NOT OK';
        }
        $data['msg'] = $msg;
        echo json_encode($data);
    }

    function personentry(){
        $data = array();

        $lname = $this->input->post('lname');
        $fname = $this->input->post('fname');
        $mname = $this->input->post('mname');
        $address = $this->input->post('address');
        $bdate = $this->input->post('bdate');

        $insertarr = array(
            'lastname' => $lname,
            'firstname' => $fname,
            'middlename' => $mname,
            'address' => $address,
            'birthdate' => $bdate,
        );

        $sql = $this->db->insert("test2", $insertarr);
        $data['error'] = $this->db->_error_message();
        if($sql){
            $msg = 'OK';
        }else{
            $msg = 'NOT OK';
        }
        $data['msg'] = $msg;

        echo json_encode($data);
    }

    function getpersondata(){
        $data = array();

        $sql = $this->db->select("sysid,lastname,firstname,middlename,address,birthdate")->from("test2")
            ->where(array('status' => 1))
            ->get();
        $data['error'] = $this->db->_error_message();
        if ($sql->num_rows > 0){
            $num = 1;
            foreach ($sql->result() as $row){
                $data['persondata2'][] = array(
                    'num' => $num++,
                    'lastname' => $row->lastname,
                    'firstname' => $row->firstname,
                    'middlename' => $row->middlename,
                    'address' => $row->address,
                    'birthdate' => date("M. d, Y", strtotime($row->birthdate)),
                    'control' => '<button type="button" class="btn btn-primary btn-xs"><i class="fa fa-edit"></i></button><button type="button" id="deleteinfo" data-id="'.$row->sysid.'" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i></button>'
                );
            }
        }

        echo json_encode($data);
    }

    function removeinfo(){
        $data = array();

        $sysid = $this->input->post('dataid');

        $updatearr = array(
            'status' => 0
        );

        $this->db->where(array('sysid'=>$sysid));
        $sql = $this->db->update('test2',$updatearr);

        if ($sql){
            $msg = 'Data removed.';
        }else{
            $msg = "Something's Wrong.";
        }
        $data['msg'] = $msg;

        echo json_encode($data);
    }

    function readmeter(){
        // Create a stream
        $options = array(
            'http'=>array(
                'method'=>"GET",
                'header'=>"Accept-language: en\r\n" .
                    "Cookie: foo=bar\r\n" .  // check function.stream-context-create on php.net
                    "User-Agent: Mozilla/5.0 (iPad; U; CPU OS 3_2 like Mac OS X; en-us) AppleWebKit/531.21.10 (KHTML, like Gecko) Version/4.0.4 Mobile/7B334b Safari/531.21.102011-10-16 20:23:10\r\n" // i.e. An iPad
            )
        );
        $context = stream_context_create($options);
        $param = 2; // 2 - READ, 0 - DISCONNECT, 1 - CONNECT
        $serial = '0219004304';
        $url = "http://172.20.224.143/amr_gateway.php?serial_no=$serial&config_file=nc30_lora.cfg&param=$param";
        $theHtmlToParse = file_get_contents($url, false, $context);

        $explode_params = explode('&', $theHtmlToParse);

        echo '<pre>';
        print_r($explode_params);
    }

    function billingrate() {
        $rate_s_amt = get_spec_rates(2019, 10, 1, 21, 100);
        print_r($rate_s_amt);
    }

    function mailtest() {
        mailer('lfaderon@gmail.com', 'This is just a test.', 'PECO Mailer');
    }

    function loopfiles() {
        $this->load->helper('directory', false);

        $file_directory = FCPATH . "uploads/attachments/cad/applications/" . str_pad(2593, 6, "0", STR_PAD_LEFT) . "/";

        $old_files = directory_map($file_directory);
        // LOOP CONTRACT ONLY
        if($old_files && is_array($old_files)) {
            foreach ($old_files as $ofile_row) {
                if(!is_array($ofile_row)) {
                    if (strpos($ofile_row, 'CONTRACT') !== false) {
                        $fileName_string = explode('.', $ofile_row);
                        $filename2 = $file_directory . 'old/' . $fileName_string[0] . '-' . time() . '.' . $fileName_string[1];
                        rename($file_directory.$ofile_row, $filename2);
                    }
                }
            }
        }
    }

    function getlastfile() {
        $file_directory = FCPATH . "uploads/attachments/cad/applications/" . str_pad(2967, 6, "0", STR_PAD_LEFT);
        $files = glob($file_directory.'/*.{gif,jpg,png,pdf,doc,docx}', GLOB_BRACE);
        echo '<pre>';
        print_r($files);
    }
}
