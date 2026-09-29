<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Model_settings extends CI_Model
{

    public function get_list_modules()
    {
        $level = $this->input->post('level');
        $q = $this -> db->select('sysid, code, name, desc, icon, status, htmlclass')
            ->from('prime_module_navigations_main')
            ->where('levels', $level)
            ->get();
        return ($q->num_rows()>0) ? $q->result() : false;
    }

    function get_module_info() {
        $data = array();
        $id = $this->input->post('id');
        $params = array();
        $module = $this->db->select()
            ->from('prime_module_navigations_main')
            ->where('sysid',$id)->get()->row();

        if ($module) {
            $params = (array)$module;
        }
        $html = $this->load->view('admin/pages/settings/moduledetails',$params,true);

        $data['html'] = $html;
        echo json_encode($data);
    }

    function dt_modules_list() {
        $data = array();
        $moduleid = $this->input->post('moduleid');

        if ($moduleid) {
            $query = $this->db->select('m.*')
                ->from('prime_module_navigations_main AS m')
                ->where('m.parent',$moduleid)->get();

            if ($query->num_rows() > 0) {
                $num = 1;
                foreach ($query->result() as $row) {
                    $status = array('Inactive', 'Active');
                    $statclass = ($row->status) ? 'success' : 'danger';
                    $subcnt = count_menu_children($row->sysid);
                    $subs = ($subcnt > 0) ? $subcnt : 'N/A';
                    $data['list'][] = array(
                        'num' => $num++,
                        'codes' => $row->code,
                        'names' => $row->name,
                        'desc' => $row->desc,
                        'icon' => '<i class="fa '.$row->icon.' text-'.$row->htmlclass.'"></i>',
                        'status' => '<span class="label label-info">Sub: <span class="badge badge-danger">' . $subs . '</span></span><span class="pull-right"><span class="text-' . $statclass . '">' . $status[$row->status] . '</span></span>',
                    );
                }
            }
        } else {
            $query = $this->db->select()
                ->from('sys_menu')->get();

            if ($query->num_rows() > 0) {
                foreach ($query->result() as $row) {
                    $status = array('Inactive', 'Active');
                    $statclass = ($row->status) ? 'success' : 'danger';
                    $subcnt = count_menu_children($row->sysid);
                    $subs = ($subcnt > 0) ? $subcnt : 'N/A';
                    $data['list'][] = array(
                        'id' => btn_expand($row->sysid),
                        'codes' => $row->codes,
                        'names' => $row->names,
                        'desc' => $row->descs,
                        'icon' => sys_icon($row->icons),
                        'status' => '<span class="label label-info">Sub: <span class="badge badge-danger">' . $subs . '</span></span><span class="pull-right"><span class="text-' . $statclass . '">' . $status[$row->status] . '</span></span>',
                        'control' => 'control',
                    );
                }
            }
        }

        return json_encode($data);
    }


    function get_module_info_back() {
        $id = $this->input->post('id');
        $q = $this -> db->select()
            ->from('prime_module_navigations_main')
            ->where('sysid', $id)
            ->get()->row();


        $data_details = '<div class="row">';
        if($q) {

            $data_details .= '<div class="col-md-6">';
            $data_details .= '<div class="row">';
            $data_details .= '<div class="col-md-6">';
            $data_details .= '<div class="form-group" >
                          <div class="input-icon">
                               <label>Code</label>
                               <i class="fa fa-tag"></i>
							   <input value="'.$q->code.'" type="text" class="form-control" placeholder="Code">
                          </div>
                          </div>';
            $data_details .= '<div class="form-group" >
                          <div class="input-icon">
                               <label>Name</label>
                               <i class="fa fa-tag"></i>
							   <input value="'.$q->name.'" type="text" class="form-control" placeholder="Name">
                          </div>
                          </div>';
            $data_details .= '<div class="form-group" >
                          <div class="input-icon">
                               <label>Description</label>
                               <i class="fa fa-tag"></i>
							   <input value="'.$q->desc.'" type="text" class="form-control" placeholder="Description">
                          </div>
                          </div>';
            $data_details .= '</div>';
            $data_details .= '<div class="col-md-6">';
            $data_details .= '<div class="form-group" >
                          <div class="input-icon">
                               <label>Icon</label>
                               <i class="fa fa-tag"></i>
							   <input value="'.$q->icon.'" type="text" class="form-control" placeholder="Icon">
                          </div>
                          </div>';
            $data_details .= '<div class="form-group" >
                          <div class="input-icon">
                               <label>Page File</label>
                               <i class="fa fa-tag"></i>
							   <input value="'.$q->pagefile.'" type="text" class="form-control" placeholder="Page File">
                          </div>
                          </div>';
            $data_details .= '<div class="form-group" >
                          <div class="input-icon">
                               <label>Class</label>
                               <i class="fa fa-tag"></i>
							   <input value="'.$q->htmlclass.'" type="text" class="form-control" placeholder="Class">
                          </div>
                          </div>';

            $data_details .= '</div>';

            $data_details .= '<div class="col-md-12 margin-top-10">
                                <button class="btn btn-default btn-sm">Update</button> 
                                <button class="btn btn-danger btn-sm">Deactivate</button> 
                             </div>';
            $data_details .= '</div>';
            $data_details .= '</div>';
            $data_details .= '<div class="col-md-6 col-sm-6 col-xs-6 tabbable-line">
                            <ul class="nav nav-tabs" style="">
                                    <li class="active">
                                        <a href="#control" data-toggle="tab" aria-expanded="true">
                                        <i class="fa fa-plus"></i> </a>
                                    </li>
                                    <li class="">
                                        <a href="#subs" data-toggle="tab" aria-expanded="false">
                                        <i class="fa fa-navicon"></i> </a>
                                    </li>
                                    <li class="">
                                        <a href="#info" data-toggle="tab" aria-expanded="false">
                                        <i class="fa fa-question"></i> </a>
                                    </li>
                                </ul>
                            <div class="tab-content"  style="">
							    <div class="tab-pane fade in active" id="control" style="padding-bottom: 10px !important;">
                                    <form class="frm_add_nav" role="form" action="'.base_url('settings/addmodulenav').'" style="margin: 10px 10px;" method="post">
                                        <input name="types" type="hidden" value="2"/>
                                        <h3>Add New Sub Menu</h3>
                                        <div class="row">
                                            <div class="col-md-5">
                                                <div class="form-group">
                                                    <label class="form-label-stripped">Code</label>
                                                    <input class="form-control input-sm" type="text" value="" placeholder="Navigation Code" name="codes"/>
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label-stripped">Name</label>
                                                    <input class="form-control input-sm" type="text" value="" placeholder="Navigation Name" name="names" />
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label-stripped">Description</label>
                                                    <textarea class="form-control input-sm" placeholder="Descriptions" name="descs" ></textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="form-label-stripped">Parent</label>
                                                    <input class="form-control input-sm" type="text" name="parent" id="this_parent_'.$q->sysid.'" value="'.$q->sysid.'" />
                                                    
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label-stripped">Page File</label>
                                                    <input class="form-control input-sm" type="text" value="" placeholder="Page File" name="file" />
                                                </div>
                                                
                                                <div class="form-group">
                                                    <label class="form-label-stripped">URL</label>
                                                    <input class="form-control input-sm" type="text" value="" placeholder="URL" name="url" />
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="form-label-stripped">Class</label>
                                                    <select class="form-control input-sm" type="text" id="this_class_'.$q->sysid.'" placeholder="Class" name="class">
                                                        <option value="info">info - Info</option>
                                                        <option value="success">success - Success</option>
                                                        <option value="warning">warning - Warning</option>
                                                        <option value="danger">danger - Danger</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label-stripped">Icon</label>
                                                    <input class="form-control input-sm" id="this_icons_'.$q->sysid.'" type="text" value="" placeholder="Icon" name="icon" />
                                                </div>
                                            </div>
                                            <div class="col-md-12 margin-top-10">
                                                <button type="reset" class="btn btn-default btn-sm">Reset</button>
                                                <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-plus fa-fw"></i> Save</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="tab-pane fade in " id="subs">
							    Subs Subs Subs Subs Subs Subs Subs Subs Subs Subs Subs Subs Subs <br>
							    Subs Subs Subs Subs Subs Subs Subs Subs Subs Subs Subs Subs Subs <br>
							    Subs Subs Subs Subs Subs Subs Subs Subs Subs Subs Subs Subs Subs <br>
							    Subs Subs Subs Subs Subs Subs Subs Subs Subs Subs Subs Subs Subs <br>
							    Subs Subs Subs Subs Subs Subs Subs Subs Subs Subs Subs Subs Subs <br>
							    Subs Subs Subs Subs Subs Subs Subs Subs Subs Subs Subs Subs Subs <br>
							    Subs Subs Subs Subs Subs Subs Subs Subs Subs Subs Subs Subs Subs <br>
							    Subs Subs Subs Subs Subs Subs Subs Subs Subs Subs Subs Subs Subs <br>
							    Subs Subs Subs Subs Subs Subs Subs Subs Subs Subs Subs Subs Subs <br>
							    Subs Subs Subs Subs Subs Subs Subs Subs Subs Subs Subs Subs Subs <br>
							    Subs Subs Subs Subs Subs Subs Subs Subs Subs Subs Subs Subs Subs <br>
                                </div>	
							</div>
							</div>';
            $func = 'success';
        }else{
            $data_details .= '<div class="row"><div class="col-md-12">No data found!</div> </div>';
            $func = 'warning';
        }

        $data_details .= '</div>';


        $data['func'] = $func;
        $data['html'] = $data_details;
        return json_encode($data);
    }

    function add_module_nav() {
        $msg = '';

        $parent         = $this->input->post('parent');
        $navname        = $this->input->post('names');
        $navcodes       = $this->input->post('codes');
        $navdescs       = $this->input->post('descs');
        $navtype        = $this->input->post('types');
        $navclass       = $this->input->post('class');
        $navid          = $this->input->post('id');
        $navurl         = $this->input->post('url');
        $navicon        = $this->input->post('icon');
        $navfile        = $this->input->post('file');

        // GET HASH
        $qry_nav_last   = $this->db->select('sysid, levels')->from('prime_module_navigations_main')->order_by('sysid', 'desc')->get()->row();
        $navsysid_last  = ($qry_nav_last) ? $qry_nav_last->sysid : 1;
        $navhash        = sha1($navsysid_last);
        //$navlevel       = ($qry_nav_last) ? $qry_nav_last->levels : 1;

        $parent_lvl = $this->db->select('levels')
            ->from('prime_module_navigations_main')
            ->where('sysid',$parent)->get()->row();

        $navlevel = ($parent_lvl) ? $parent_lvl->levels + 1 : 1;

        // GET SORT
        $qry_sort_last = $this->db->select('sorting')->from('prime_module_navigations_main')->where('parent', $parent)->order_by('sorting', 'desc')->get()->row();
        $navsysid_last = ($qry_sort_last) ? $qry_sort_last->sorting : 1;
        $navsorts      = $navsysid_last + 1;

        $icon = $this->db->select('icon')
            ->from('system_icons')
            ->where('sysid',$navicon)
            ->get()->row();

        if ($icon) {
            $navicon = $icon->icon;
        }


        // INSERT TO NAVIGATION
        $this->db->trans_begin();

        $ins_trans = array(
            'code' => $navcodes,
            'name' => $navname,
            'desc' => $navdescs,
            'parent' => $parent,
            'levels' => $navlevel,
            'type' => $navtype,
            'sorting' => $navsorts,
            'htmlclass' => $navclass,
            'htmlid' => $navid,
            'url' => $navurl,
            'icon' => $navicon,
            'hashcode' => $navhash,
            'pagefile' => $navfile,
        );
        $this->db->insert('prime_module_navigations_main', $ins_trans);

        if($this->db->trans_status()===true) {
            $this->db->trans_commit();
            $qry = true;

            $pagefilename = $navfile;
            $moduledir = FCPATH . 'application/views/admin/pages/modules/';
            $filename = $moduledir . $pagefilename;
            $phpfile = $moduledir . $pagefilename . '.php';
            $phpsubfile = $moduledir . $pagefilename . '/' . $navurl . '.php';

            // GET FILE TEMPLATE
            $file_contents = file_get_contents($moduledir = FCPATH . 'application/views/includes/default_navfile.php');

            // MAKE FOLDER
            if (!file_exists($filename)) {
                mkdir($filename, 0777, true);
            }

            if (file_exists($phpfile)) {
                unlink($phpfile);
                $fh = fopen($phpfile, 'w');
                fwrite($fh, $file_contents . "\n");
                //$fh = fopen($phpfile, 'a');
                //fwrite($fh, $file_contents."\n");

                $msg_file = 'File exists and replaced!';
                $func = 'warning';
            } else {
                $fh = fopen($phpfile, 'w');
                fwrite($fh, $file_contents . "\n");
                $msg_file = 'New File created!';
                $func = 'success';
            }

            fclose($fh);

            // MAKDE SUB FILE INSIDER FOLDER
            if($navfile) {
                if (file_exists($phpsubfile)) {
                    unlink($phpsubfile);
                    $fh = fopen($phpsubfile, 'w');
                    fwrite($fh, $file_contents . "\n");
                } else {
                    $fh = fopen($phpsubfile, 'w');
                    fwrite($fh, $file_contents . "\n");
                }
            }

            fclose($fh);

            $msg = 'Navigation Created / ' . $msg_file;
        }else{
            $this->db->trans_rollback();
            $qry = false;
            $func = 'error';
        }
        $data['input'] = $this->input->post();
        $data['func'] = $func;
        $data['qry'] = $qry;
        $data['msg'] = $msg;
        return json_encode($data);
    }

    public function get_list_access_roles()
    {
        $q = $this -> db->select('sysid, code, descriptions, color')
            ->from('prime_system_users_roles_main')
            ->get();
        return ($q->num_rows()>0) ? $q->result() : false;
    }

    function profanity_filter($phrase) {
        $q	= $this->db->select("keywords, replaces")->get('tbl_profanity_main');
        if($q->num_rows()>0){
            foreach($q->result() as $r) { $sr[] = $r->keywords; $rp[] = $r->replaces; }
            if (is_array($phrase))
            {
                foreach($subject as &$oneSubject)
                    $oneSubject = str_replace_deep($search, $replace, $oneSubject);
                unset($oneSubject);
                return $subject;
            } else {
                return str_replace($sr, $rp, $phrase);
            }
        }else{
            return $phrase;
        }
    }

    function emoticonize($phrase) {
        $q	= $this->db->select("keywords, replaces")->get('sys_emoticons_main');
        if($q->num_rows()>0){
            foreach($q->result() as $r) { $sr[] = $r->keywords; $rp[] = '<img width="32" height="32" src="'.base_url().'assets/global/img/emoticons/'.$r->replaces.'.png" />'; }
            if (is_array($phrase))
            {
                foreach($subject as &$oneSubject)
                    $oneSubject = str_replace_deep($search, $replace, $oneSubject);
                unset($oneSubject);
                return $subject;
            } else {
                return str_replace($sr, $rp, $phrase);
            }
        }else{
            return $phrase;
        }
    }

    function add_user() {
        $data = array();
        $submit = true;
        $qry = false;
        $msg = "";
        $firstname = $this->input->post('firstname');
        $lastname = $this->input->post('lastname');
        $middlename = $this->input->post('middlename');
        $username = $this->input->post('username');
        $password = $this->input->post('password');
        $roles = explode(',', $this->input->post('selectroles'));
        if (empty($firstname)) {
            $submit = false;
            $msg = "First name is empty";
        }
        if (empty($lastname)) {
            $submit = false;
            $msg = "Last name is empty";
        }
        if (empty($username)) {
            $submit = false;
            $msg = "Username is empty";
        }
        if (empty($password)) {
            $submit = false;
            $msg = "Password is empty";
        }
        if (empty($roles)) {
            $submit = false;
            $msg = "Roles is empty";
        }
        if ($this->input->post('rpassword') == $password) {

            if ($submit == true) {
                $this->db->trans_begin();

                $qry_person = $this->db->select('sysid, lastname, firstname, middlename')
                    ->from('person')
                    ->where(array('lastname' => $lastname, 'firstname' => $firstname, 'middlename' => $middlename))
                    ->get()->row();
                if($qry_person) {
                    $personid = $qry_person->sysid;
                }else{
                    $person_ins = array(
                       // 'username' => $username,
                        'firstname' => $firstname,
                        'lastname' => $lastname
                    );
                    $this->db->insert('person', $person_ins);
                    $data['error_persons'] = $this->db->_error_message();
                    $personid = $this->db->insert_id();
                }

                $ins = array(
                    'username' => $username,
                    'password' => encrypt_pass($password),
                    'personid' => $personid,
                    'status' => 1
                );
                $this->db->insert('prime_system_users', $ins);
                $data['error_system_users'] = $this->db->_error_message();
                $user_id = $this->db->insert_id();


                foreach($roles as $row){
                    $role_in = array('userid' => $user_id, 'roleid' => $row);
                    $this->db->insert('prime_system_users_roles_matrix', $role_in);
                    $data['error_system_roles'] = $this->db->_error_message();
                }

                if ($this->db->trans_status()) {
                    $qry = true;
                    $msg = 'User has been added';
                    $this->db->trans_commit();
                } else {
                    $qry = false;
                    $msg = 'Error Adding User';
                    $this->db->trans_rollback();
                }
            }
        } else {
            $msg = 'Password did not match';
            $qry = false;
        }
        $data['msg'] = $msg;
        $data['qry'] = $qry;
        return json_encode($data);
    }

    function get_users_lists() {

        $data = array();
        $draw           = $this->input->post('draw');
        $start          = $this->input->post("start");
        $length         = $this->input->post("length");
        $search         = $this->input->post("search");
        $order          = $this->input->post("order");
        $stat           = $this->input->post("stat");
        $roleid         = $this->input->post('roleid');
        if($stat<2) {
            if ($stat) {
                $this->db->where('psu.status', 1);
            } else {
                $this->db->where('psu.status', 0);
            }
        }
        $qry = $this->db->select('
            psu.sysid,psu.username,
            psu.firstname,
            psu.lastname,
            psu.status,
            psu.datecreated,
            psu.dateupdated,
            MAX(psul.sessiondatetime) as latestactive,
            psulc.telcode,
            psu.personid,
            p.lastname AS plname,
            p.firstname AS pfname
        ')
            ->from('prime_system_users as psu')
            ->join('prime_system_users_logs as psul' , 'psul.userid = psu.sysid' , 'left')
            ->join('prime_system_users_legacy_code as psulc' , 'psulc.userid = psu.sysid && psulc.status = 1' , 'left')
            ->join('person AS p', 'p.sysid = psu.personid', 'left')
            ->where('psu.sysid != ', 1)
            ->group_by("
                psu.sysid,
                psulc.telcode,
                psu.personid,
                psu.firstname,
                psu.lastname,
                psu.datecreated,
                psu.dateupdated,
                p.lastname,
                p.firstname
            ")
            ->get();

        $num_rows = $qry->num_rows();
        if($num_rows>0) {
            foreach($qry->result() as $row) {
                $name = $row->firstname . ' ' . $row->lastname;
                if($row->personid != '') {
                    $name = $row->pfname . ' ' . $row->plname;
                }

                $fist_log = $this->db->select('sessiondatetime')
                    ->from('prime_system_users_logs')
                    ->where(array('userid' => $row->sysid))
                    ->order_by('sessiondatetime')
                    ->get()->row();
                $first_log_date = ($fist_log) ? $fist_log->sessiondatetime : 'N/A';

                $data['list'][]  = array(
                    'sysid' => $row->sysid,
                    'username' => $row->username,
                    'legacyuname' =>'<input data-id="'.$row->sysid.'" type="text" id="leguname" class="form-control inline" />',
                    'firstname' => $name,
                    'status' => row_status($row->status),
                    'roles' => get_users_info_roles_control($row->sysid , $roleid),
                    'activity' => $row->latestactive,
                    'created' => $first_log_date,
                    'updated' => $row->dateupdated,
                    'control' => get_users_list_control($row->sysid),
                    'telcode' => $row->telcode
                );
            }
        }

        $data['draw'] = $draw;
        $data['recordsTotal'] = $num_rows;
        $data['recordsFiltered'] = $num_rows;
        return json_encode($data);
    }

    function get_billtrn_update_loop() {
        $data = array();
        $cnt = $this->input->post('cnt');
        $num = $this->input->post('num');
        $year = $this->input->post('year');
        $month = $this->input->post('month');
        if($year && $month) {

            if (pecoapps_conn()) {
                $conn = $this->load->database('pecoapps', TRUE);
                $conn->initialize();
                $row = $conn->select("
                          ctrlinc
                          ,group_____ AS 'group'
                          ,dist______ AS dist
                          ,lot_______ AS lot
                          ,book______ AS book
                          ,servno____ AS servno
                          ,name______ AS name
                          ,addr______ AS addr
                          ,mtr_______ AS mtr
                          ,m_________ AS month
                          ,yr________ AS year
                          ,prvdte____ AS prvdte
                          ,prsdte____ AS prsdte
                          ,duedate___ AS duedate
                          ,load______ AS load
                          ,rate______ AS rate
                          ,prvrdg____ AS prvrdg
                          ,prsrdg____ AS prsrdg
                          ,multcd____ AS multcd
                          ,kwhuse____ AS kwhuse
                          ,genamt____ AS genamt
                          ,genchg____ AS genchg
                          ,trnamt____ AS trnamt
                          ,trnchg____ AS trnchg
                          ,disamt____ AS disamt
                          ,dischg____ AS dischg
                          ,demamt____ AS demamt
                          ,supamt____ AS supamt
                          ,supchg____ AS supchg
                          ,supper____ AS supper
                          ,mtramt____ AS mtramt
                          ,mtrchg____ AS mtrchg
                          ,mtrper____ AS mtrper
                          ,slamt_____ AS slamt
                          ,slchg_____ AS slchg
                          ,iccamt____ AS iccamt
                          ,iccsub____ AS iccsub
                          ,llramt____ AS llramt
                          ,llrsub____ AS llrsub
                          ,lldamt____ AS lldamt
                          ,misamt____ AS misamt
                          ,mischg____ AS mischg
                          ,envamt____ AS envamt
                          ,envchg____ AS envchg
                          ,framt_____ AS framt
                          ,genvat____ AS genvat
                          ,trnvat____ AS trnvat
                          ,disvat____ AS disvat
                          ,slvat_____ AS slvat
                          ,othvat____ AS othvat
                          ,appsur____ AS appsur
                          ,surbal____ AS surbal
                          ,current___ AS 'current'
                          ,overdue___ AS overdue
                          ,totacc____ AS totacc
                          ,totint____ AS totint
                          ,dolpay____ AS dolpay
                          ,cntapp____ AS cntapp
                          ,billno
                          ,moyr
                          ,batch
                          ,dteprt
                          ,lttype
                          ,ltsta
                          ,lttodt
                          ,ltfrdt
                          ,ltmo
                          ,ltyear
                          ,ctrlinc
                          ,intdte
                          ,genamt1___ AS genamt1
                          ,genchg1___ AS genchg1
                          ,papc
                          ,papcchg
                          ,mtrser
                          ,serial
                          ,scdisc
                          ,npcchg____ AS npcchg
                          ,npcamt____ AS npcamt
                          ,iccschg
                          ,iccsamt
                          ,fitchg
                          ,fitamt
                        ")
                    ->from('billtrn')
                    ->where(
                        array(
                            'yr________' => $year,
                            'm_________' => $month,
                            'group_____ > ' => 0,
                            'lot_______ > ' => 0,
                            'ctrlinc > ' => $num
                        )
                    )
                    ->order_by('ctrlinc')
                    ->get()->row();
                if ($row) {
                    $last_inc = $row->ctrlinc;

                    $percent_ind = $num / $cnt;
                    if ($percent_ind < 1) {
                        $num = $num + 1;
                        $per = ($percent_ind * 100);

                    } else {
                        $end = true;
                        $per = 100;
                    }
                }
            }
        }

        $data['per'] = $per;
        $data['num'] = $last_inc;
        return json_encode($data);
    }


    function get_father_update_loop() {
        $data = array();
        $num = $this->input->post('num');
        $servno = $this->input->post('servno');
        $total_cnt = $this->input->post('cnt');
        $end = false;
        $custname = '';
        $servno_next = '';
        $mtr_next = '';

        if (pecoapps_conn()) {

            $conn = $this->load->database('pecoapps', TRUE);
            $conn->initialize();

            if($servno != '') {
                $qry_fahter_next = $conn->select('LTRIM(RTRIM(f.servno____)) AS servno, f.mtr_______ AS mtr')
                    ->from('father AS f')
                    ->where('f.servno____ > ', $servno)
                    ->order_by('f.servno____')
                    ->get()->row();
            }else{
                $qry_fahter_next = $conn->select('LTRIM(RTRIM(f.servno____)) AS servno, f.mtr_______ AS mtr')
                    ->from('father AS f')
                    ->order_by('f.servno____')
                    ->get()->row();
            }

            if($qry_fahter_next) {

                $servno_next = $qry_fahter_next->servno;
                $mtr_next = $qry_fahter_next->mtr;

                $row = $conn->select('
                        LTRIM(RTRIM(f.servno____)) AS servno,
                        LTRIM(RTRIM(f.group_____)) AS g,
                        LTRIM(RTRIM(f.dist______)) AS d,
                        LTRIM(RTRIM(f.lot_______)) AS l,
                        LTRIM(RTRIM(f.book______)) AS b,
                        f.mtr_______ AS mtr,
                        LTRIM(RTRIM(f.class_____)) AS class,
                        LTRIM(RTRIM(f.multcd____)) AS multcd,
                        LTRIM(RTRIM(f.name______)) AS name,
                        LTRIM(RTRIM(f.addr______)) AS addr,
                        LTRIM(RTRIM(f.condte____)) AS contractdate,
                        LTRIM(RTRIM(f.status____)) AS status,
                        LTRIM(RTRIM(f.stadte____)) AS conndate,
                        LTRIM(RTRIM(f.mtrser____)) AS mtrno,
                        LTRIM(RTRIM(f.serial____)) AS mtrserial,
                        f.load______ AS load,
                        f.kwh1______ AS kwh_01,
                        f.kwh2______ AS kwh_02,
                        f.kwh3______ AS kwh_03,
                        f.kwh4______ AS kwh_04,
                        f.kwh5______ AS kwh_05,
                        f.kwh6______ AS kwh_06,
                        f.kwh7______ AS kwh_07,
                        f.kwh8______ AS kwh_08,
                        f.kwh9______ AS kwh_09,
                        f.kwh10_____ AS kwh_10,
                        f.kwh11_____ AS kwh_11,
                        f.kwh12_____ AS kwh_12,
                        f.amt1______ AS amt_01,
                        f.amt2______ AS amt_02,
                        f.amt3______ AS amt_03,
                        f.amt4______ AS amt_04,
                        f.amt5______ AS amt_05,
                        f.amt6______ AS amt_06,
                        f.amt7______ AS amt_07,
                        f.amt8______ AS amt_08,
                        f.amt9______ AS amt_09,
                        f.amt10_____ AS amt_10,
                        f.amt11_____ AS amt_11,
                        f.amt12_____ AS amt_12,
                        f.amt13_____ AS amt_13,
                        f.bill1_____ AS bill_01,
                        f.bill2_____ AS bill_02,
                        f.bill3_____ AS bill_03,
                        f.bill4_____ AS bill_04,
                        f.bill5_____ AS bill_05,
                        f.bill6_____ AS bill_06,
                        f.bill7_____ AS bill_07,
                        f.bill8_____ AS bill_08,
                        f.bill9_____ AS bill_09,
                        f.bill10____ AS bill_10,
                        f.bill11____ AS bill_11,
                        f.bill12____ AS bill_12,
                        s.mrseq
                    ')
                    ->from('father AS f')
                    ->join('seqtab AS s', 's.servno = f.servno____ AND s.mtrser = f.mtrser____', 'left')
                    ->where(array('f.servno____' => $qry_fahter_next->servno, 'f.mtr_______' => $qry_fahter_next->mtr))
                    ->order_by('f.servno____')
                    ->get()->row();

                if ($row) {

                    $custname = $row->servno . ' - ' . $row->name;

                    // CHECK AND UPDATE FIRST
                    $qry_main_check = $this->db->select('sysid, ownerid, types')
                        ->from('customer_accounts_main')
                        ->where(array('servicenumber' => $row->servno, 'mtr' => $row->mtr))
                        ->get()->row();

                    if($qry_main_check) {
                        // ##############################################################
                        // UPDATE EXISTING ##############################################

                        $acctid = $qry_main_check->sysid;

                        // MRSEQ
                        $this->db->where(array('acctid' => $acctid, 'status' => 1));
                        $this->db->update('customer_accounts_mtrseq', array('status' => 0));

                        $ins_arr = array(
                            'mrseq' => $row->mrseq,
                            'acctid' => $acctid,
                            'createdby' => user_id(),
                            'updatedby' => user_id(),
                        );
                        $this->db->insert('customer_accounts_mtrseq', $ins_arr);


                        // GDLBID
                        $get_gdlb = $this->db->select('gdlb.sysid')
                            ->from('gdlb_main AS gdlb')
                            ->join('address_districts AS ads', 'gdlb.d = ads.sysid', 'left')
                            ->where(array('g' => $row->g, 'ads.codes' => $row->d, 'l' => $row->l, 'b' => $row->b))
                            ->get()->row();

                        // RATE CLASS ID
                        $get_rateclassid = $this->db->select('sysid')->from('rate_class_specification')
                            ->where(array('codes' => $row->class))
                            ->get()->row();

                        // MULTCODE ID
                        $get_multcodeid = $this->db->select('sysid')->from('billing_rates_main_multiplier')
                            ->where(array('codes' => $row->multcd))
                            ->get()->row();

                        if ($get_gdlb && $get_rateclassid && $get_multcodeid) {
                            // UPDATE
                            $name = ucfirst(utf8_decode($row->name));
                            $ins_own_arr = array('name' => $name);
                            $this->db->insert('customer_accounts_name_legacy', $ins_own_arr);
                            $ownerid = $this->db->insert_id();


                            // INSERT MIGRATION HERE
                            $gdlbid = $get_gdlb->sysid;
                            $rateclassid = $get_rateclassid->sysid;
                            $multcodeid = $get_multcodeid->sysid;

                            $contractdate = date('Y-m-d', strtotime($row->contractdate));
                            $connectdate = date('Y-m-d', strtotime($row->conndate));
                            $status = ($row->status == 1) ? 1 : 0;

                            $update_acct_main = array(
                                'datecontract' => $contractdate,
                                'dateconnected' => $connectdate,
                                'ownerid' => $ownerid,
                                'types' => 5,
                                'gdlb' => $gdlbid,
                                'mtrno' => $row->mtrno,
                                'mtrserial' => $row->mtrserial,
                                'mtr' => $row->mtr,
                                'rateclassid' => $rateclassid,
                                'multid' => $multcodeid,
                                'status' => $status
                            );
                            $this->db->where('sysid', $acctid);
                            $this->db->update('customer_accounts_main', $update_acct_main);


                            // #############################################################
                            // UPDATE AR
                            $upd_ar_arr = array(
                                'amt_01' => $row->amt_01,
                                'amt_02' => $row->amt_02,
                                'amt_03' => $row->amt_03,
                                'amt_04' => $row->amt_04,
                                'amt_05' => $row->amt_05,
                                'amt_06' => $row->amt_06,
                                'amt_07' => $row->amt_07,
                                'amt_08' => $row->amt_08,
                                'amt_09' => $row->amt_09,
                                'amt_10' => $row->amt_10,
                                'amt_11' => $row->amt_11,
                                'amt_12' => $row->amt_12,
                                'amt_13' => $row->amt_13,
                                'kwh_01' => $row->kwh_01,
                                'kwh_02' => $row->kwh_02,
                                'kwh_03' => $row->kwh_03,
                                'kwh_04' => $row->kwh_04,
                                'kwh_05' => $row->kwh_05,
                                'kwh_06' => $row->kwh_06,
                                'kwh_07' => $row->kwh_07,
                                'kwh_08' => $row->kwh_08,
                                'kwh_09' => $row->kwh_09,
                                'kwh_10' => $row->kwh_10,
                                'kwh_11' => $row->kwh_11,
                                'kwh_12' => $row->kwh_12,
                                'billno_01' => $row->bill_01,
                                'billno_02' => $row->bill_02,
                                'billno_03' => $row->bill_03,
                                'billno_04' => $row->bill_04,
                                'billno_05' => $row->bill_05,
                                'billno_06' => $row->bill_06,
                                'billno_07' => $row->bill_07,
                                'billno_08' => $row->bill_08,
                                'billno_09' => $row->bill_09,
                                'billno_10' => $row->bill_10,
                                'billno_11' => $row->bill_11,
                                'billno_12' => $row->bill_12,
                            );
                            $this->db->where(array(
                                'acctid' => $acctid,
                                'mtr' => $row->mtr
                            ));
                            $this->db->update('customer_accounts_ar', $upd_ar_arr);
                            $err_msg = $this->db->_error_message();
                            $data['error']['ar'][] = array('servno' => $row->servno, 'msg' => $err_msg);

                        }
                    } else {
                        // ##############################################################
                        // INSERT NEW ###################################################

                        // GDLBID
                        $get_gdlb = $this->db->select('gdlb.sysid')
                            ->from('gdlb_main AS gdlb')
                            ->join('address_districts AS ads', 'gdlb.d = ads.sysid', 'left')
                            ->where(array('g' => $row->g, 'ads.codes' => $row->d, 'l' => $row->l, 'b' => $row->b))
                            ->get()->row();
                        // RATE CLASS ID
                        $get_rateclassid = $this->db->select('sysid')->from('rate_class_specification')
                            ->where(array('codes' => $row->class))
                            ->get()->row();
                        // MULTCODE ID
                        $get_multcodeid = $this->db->select('sysid')->from('billing_rates_main_multiplier')
                            ->where(array('codes' => $row->multcd))
                            ->get()->row();
                        if ($get_gdlb && $get_rateclassid && $get_multcodeid) {
                            // INSERT OWNER INFO LEGACY
                            $name = ucfirst(utf8_decode($row->name));
                            $ins_own_arr = array('name' => $name);
                            $ins_owner = $this->db->insert('customer_accounts_name_legacy', $ins_own_arr);
                            $ownerid = $this->db->insert_id();
                            if ($ins_owner) {
                                // INSERT MIGRATION HERE
                                $gdlbid = $get_gdlb->sysid;
                                $rateclassid = $get_rateclassid->sysid;
                                $multcodeid = $get_multcodeid->sysid;

                                $contractdate = date('Y-m-d', strtotime($row->contractdate));
                                $connectdate = date('Y-m-d', strtotime($row->conndate));

                                $status = ($row->status == 1) ? 1 : 0;
                                $ins_acct_arr = array(
                                    'servicenumber' => $row->servno,
                                    'createdby' => 1,
                                    'datecontract' => $contractdate,
                                    'dateconnected' => $connectdate,
                                    'ownerid' => $ownerid,
                                    'types' => 5,
                                    'gdlb' => $gdlbid,
                                    'mtrno' => $row->mtrno,
                                    'mtrserial' => $row->mtrserial,
                                    'mtr' => $row->mtr,
                                    'rateclassid' => $rateclassid,
                                    'multid' => $multcodeid,
                                    'status' => $status
                                );

                                //$data['acctinfo'][] = $ins_acct_arr;

                                // INSERT ACCOUNT
                                $ins_acct = $this->db->insert('customer_accounts_main', $ins_acct_arr);
                                $acctid = $this->db->insert_id();


                                if ($ins_acct) {

                                    // INSERT MRSEQ
                                    $ins_mrseq_arr = array(
                                        'mrseq' => $row->mrseq, 'acctid' => $acctid, 'createdby' => user_id(), 'updatedby' => user_id()
                                    );
                                    $this->db->insert('customer_accounts_mtrseq', $ins_mrseq_arr);

                                    // INSERT ADDR INFO IF ACCOUNT CREATED
                                    $ins_addr_arr = array(
                                        'acctid' => $acctid,
                                        'country' => 175,
                                        'addrspecific' => ucfirst(utf8_decode($row->addr))
                                    );
                                    $this->db->insert('customer_accounts_address', $ins_addr_arr);

                                    // INSERT LOAD
                                    $ins_load_arr = array(
                                        'acctid' => $acctid,
                                        'load' => $row->load,
                                        'createdby' => 1
                                    );
                                    $this->db->insert('customer_accounts_load_logs', $ins_load_arr);


                                    $ins_arr = array(
                                        'acctid' => $acctid,
                                        'mtr' => $row->mtr,
                                        'amt_01' => $row->amt_01,
                                        'amt_02' => $row->amt_02,
                                        'amt_03' => $row->amt_03,
                                        'amt_04' => $row->amt_04,
                                        'amt_05' => $row->amt_05,
                                        'amt_06' => $row->amt_06,
                                        'amt_07' => $row->amt_07,
                                        'amt_08' => $row->amt_08,
                                        'amt_09' => $row->amt_09,
                                        'amt_10' => $row->amt_10,
                                        'amt_11' => $row->amt_11,
                                        'amt_12' => $row->amt_12,
                                        'amt_13' => $row->amt_13,
                                        'kwh_01' => $row->kwh_01,
                                        'kwh_02' => $row->kwh_02,
                                        'kwh_03' => $row->kwh_03,
                                        'kwh_04' => $row->kwh_04,
                                        'kwh_05' => $row->kwh_05,
                                        'kwh_06' => $row->kwh_06,
                                        'kwh_07' => $row->kwh_07,
                                        'kwh_08' => $row->kwh_08,
                                        'kwh_09' => $row->kwh_09,
                                        'kwh_10' => $row->kwh_10,
                                        'kwh_11' => $row->kwh_11,
                                        'kwh_12' => $row->kwh_12,
                                        'billno_01' => $row->bill_01,
                                        'billno_02' => $row->bill_02,
                                        'billno_03' => $row->bill_03,
                                        'billno_04' => $row->bill_04,
                                        'billno_05' => $row->bill_05,
                                        'billno_06' => $row->bill_06,
                                        'billno_07' => $row->bill_07,
                                        'billno_08' => $row->bill_08,
                                        'billno_09' => $row->bill_09,
                                        'billno_10' => $row->bill_10,
                                        'billno_11' => $row->bill_11,
                                        'billno_12' => $row->bill_12,
                                    );
                                    $this->db->insert('customer_accounts_ar', $ins_arr);
                                    $data['error'][] = $this->db->_error_message();

                                } else {
                                    $this->db->insert('customer_migrate_xexist', array('servno' => $row->servno, 'rem' => 'ACCT'));
                                }

                            }

                        } else {
                            // @TODO insert error message here
                            $this->db->insert('customer_migrate_xexist', array('servno' => $row->servno, 'rem' => 'EXT'));
                        }
                    }
                }

            }

            $percent_ind = $num / $total_cnt;
            if ($percent_ind < 1) {
                $num = $num + 1;
                $per = ($percent_ind * 100);

                $check_logs = $this->db->select('desc')
                    ->from('prime_data_migration_logs')
                    ->where('desc', 'fatherupdate')
                    ->get()->row();

                if($check_logs) {
                    $upd_arr = array(
                        'dataid' => $servno_next,
                        'num' => $num
                    );
                    $this->db->where('desc', 'fatherupdate');
                    $this->db->update('prime_data_migration_logs', $upd_arr);
                }else{
                    $ins_arr = array(
                        'dataid' => $servno_next,
                        'num' => $num,
                        'desc' => 'fatherupdate'
                    );
                    $this->db->insert('prime_data_migration_logs', $ins_arr);
                }
            } else {
                $end = true;
                $per = 100;

                //SMTP & mail configuration
                $this->load->library('email');
                $config = array(
                    'protocol' => 'smtp',
                    'smtp_host' => 'ssl://smtp.googlemail.com',
                    'smtp_port' => 465,
                    'smtp_user' => 'bills.peco@gmail.com',
                    'smtp_pass' => 'P3C02018!!',
                    'mailtype' => 'html',
                    'charset' => 'utf-8'
                );

                $this->email->initialize($config);
                $this->email->set_mailtype("html");
                $this->email->set_newline("\r\n");

                //Email content
                $this->email->to('lfaderon@gmail.com');
                $this->email->from('no-reply@panayelectric.com', 'PECO SYSTEM - Update');
                $this->email->subject('PECO SYSTEM - Update');
                $this->email->message('Updated ('.$num.') father done!');
                $this->email->send();
                $this->email->clear(TRUE);

                $check_logs = $this->db->select('desc')
                    ->from('prime_data_migration_logs')
                    ->where('desc', 'fatherupdate')
                    ->get()->row();

                if($check_logs) {
                    $upd_arr = array(
                        'dataid' => $servno_next,
                        'num' => $num
                    );
                    $this->db->where('desc', 'fatherupdate');
                    $this->db->update('prime_data_migration_logs', $upd_arr);
                }else{
                    $ins_arr = array(
                        'dataid' => $servno_next,
                        'num' => $num,
                        'desc' => 'fatherupdate'
                    );
                    $this->db->insert('prime_data_migration_logs', $ins_arr);
                }

            }
        }



        $data['end'] = $end;
        $data['per'] = round($per, 2);
        $data['servno'] = $servno_next;
        $data['mtr'] = $mtr_next;
        $data['custname'] = $custname;
        $data['num'] = $num;

        return json_encode($data);
    }

    function test_query() {
        $data = array();
        $num = $this->input->post('num');
        $sysid = $this->input->post('sysid');
        $per = 0;
        $end = false;

        $emp_arr = false;

        if($num==0) {
            $this->db->where('sysid > ', 0);
        }else{
            $this->db->where('sysid < ', $sysid);
        }

        $qry_emp = $this->db->select('sysid')
            ->from('prime_employee_main')
            ->where(array('status' => 1))
            ->order_by('sysid', 'desc')
            ->get()->row();
        $qry_emp_cnt = $this->db->select('COUNT(sysid) AS cnt')
            ->from('prime_employee_main')
            ->where(array('status' => 1))
            ->order_by('sysid', 'desc')
            ->get()->row();
        if($qry_emp) {
            $emp_arr = get_employee_info($qry_emp->sysid);
        }else{
            $emp_arr = false;
        }

        $total_cnt = $qry_emp_cnt->cnt;
        $percent_ind = $num / $total_cnt;
        if ($qry_emp) {
            $num = $num + 1;
            $sysid = $qry_emp->sysid;
            $per = ($percent_ind * 100);
        } else {
            $end = true;
            $per = 100;
        }

        $data['empname'] = ($emp_arr) ? $emp_arr->lastname.', '.$emp_arr->firstname : 'Done!';

        $data['end'] = $end;
        $data['per'] = round($per, 2);
        $data['sysid'] = $sysid;
        $data['num'] = $num;


        return json_encode($data);
    }

    function update_sequence_from_legacy() {
        $data = array();
        $q = false;

        $update_cnt = 0;
        if (pecoapps_conn()) {
            $conn = $this->load->database('pecoapps', TRUE);
            $conn->initialize();
            $qry = $conn->select('mrseq, servno, mtr, mtrser')
                ->from('seqtab')
                ->where(array('mrseq > ' => 0))
                ->get();

            if ($qry->num_rows() > 0) {

                foreach ($qry->result() as $row) {


                    $qry_acctinfo = $this->db->select('sysid')
                        ->from('customer_accounts_main')
                        ->where(array('servicenumber' => $row->servno, 'mtrno' => $row->mtrser))
                        ->get()->row();
                    $update = false;
                    if($qry_acctinfo) {
                        $this->db->where(array('acctid' => $qry_acctinfo->sysid, 'status' => 1));
                        $this->db->update('customer_accounts_mtrseq', array('status' => 0));

                        $ins_arr = array(
                            'mrseq' => $row->mrseq,
                            'acctid' => $qry_acctinfo->sysid,
                            'createdby' => user_id(),
                            'updatedby' => user_id(),
                        );
                        $ins = $this->db->insert('customer_accounts_mtrseq', $ins_arr);
                        if($ins) {
                            $update_cnt += 1;
                        }
                    }

                    // customer_accounts_mtrseq
                    $data['list'][] = array(
                        'seqtab' => $row->mrseq,
                        'servno' => $row->servno,
                        'mtr' => $row->mtr,
                        'mtrser' => $row->mtrser,
                        'updated' => $update,
                    );
                }
            }
        }
        if($update_cnt) {
            $q = true;
        }
        $data['qry'] = $q;
        return json_encode($data);
    }

    function update_from_legacy_seqtab() {
        $data = array();
        $gdlbid = $this->input->post('gdlbid');
        $q = false;
        $qry_gdlb_info = $this->db->query("
            SELECT dist.codes, gdlb.l, gdlb.b
            FROM gdlb_main AS gdlb
            JOIN address_districts AS dist ON gdlb.d = dist.sysid
            WHERE gdlb.sysid = {$gdlbid}
        ")->row();
        if($qry_gdlb_info) {
            if (pecoapps_conn()) {
                $conn = $this->load->database('pecoapps', TRUE);
                $conn->initialize();
                $qry = $conn->select()
                    ->from('seqtab')
                    ->where(array('ref > ' => 0, 'dist' => $qry_gdlb_info->codes, 'lot' => $qry_gdlb_info->l, 'book' => $qry_gdlb_info->b))
                    ->get();

                if ($qry->num_rows() > 0) {
                    $q = true;
                    foreach ($qry->result() as $row) {

                        $check_userid = $this->db->select()
                            ->from('prime_system_users_legacy_code')
                            ->where('telcode', $row->ref)
                            ->get()->row();

                        if($check_userid) {
                            $qry_acctinfo = $this->db->select('sysid')
                                ->from('customer_accounts_main')
                                ->where(array('servicenumber' => $row->servno, 'mtr' => $row->mtr))
                                ->get()->row();
                            if ($qry_acctinfo) {
                                $this->db->where(array('acctid' => $qry_acctinfo->sysid, 'userid'));
                                $this->db->update('reading_schedule_specific', array('status' => 0));

                                $ins_arr = array(
                                    'acctid' => $qry_acctinfo->sysid,
                                    'userid' => $check_userid->userid,
                                    'createdby' => user_id(),
                                    'updatedby' => user_id(),
                                );
                                $this->db->insert('reading_schedule_specific', $ins_arr);
                            }
                        }
                    }
                }
            }
        }
        $data['qry'] = $q;
        return json_encode($data);
    }

    function get_legacy_seqtab()
    {
        $data = array();
        $gdlbid = $this->input->post('gdlbid');

        $qry_gdlb_info = $this->db->query("
            SELECT dist.codes, gdlb.l, gdlb.b
            FROM gdlb_main AS gdlb
            JOIN address_districts AS dist ON gdlb.d = dist.sysid
            WHERE gdlb.sysid = {$gdlbid}
        ")->row();

        if($qry_gdlb_info) {
            if (pecoapps_conn()) {
                $conn = $this->load->database('pecoapps', TRUE);
                $conn->initialize();
                $qry = $conn->select()
                    ->from('seqtab')
                    ->where(array('dist' => $qry_gdlb_info->codes, 'lot' => $qry_gdlb_info->l, 'book' => $qry_gdlb_info->b, 'grp != ' => 6))
                    ->get();

                if ($qry->num_rows() > 0) {
                    foreach ($qry->result() as $row) {


                        $check_userid = $this->db->select()
                            ->from('prime_system_users_legacy_code')
                            ->where('telcode', $row->ref)
                            ->get()->row();

                        if($check_userid) {
                            $qry_acctinfo = $this->db->select('sysid')
                                ->from('customer_accounts_main')
                                ->where(array('servicenumber' => $row->servno, 'mtr' => $row->mtr))
                                ->get()->row();
                            if($qry_acctinfo) {
                                $get_specific_tag = $this->db->select()
                                    ->from('reading_schedule_specific')
                                    ->where(array('acctid' => $qry_acctinfo->sysid))
                                    ->get()->row();
                                if($get_specific_tag) {
                                    if ($get_specific_tag->userid == $check_userid->userid) {
                                        $stat = '<span class="label label-success">Updated!</span>';
                                    } else {
                                        $stat = '<span class="label label-warning">Outdated!</span>';
                                    }
                                }else{
                                    $stat = '<span class="label label-info">Unassigned!</span>';
                                }
                            }else{
                                $stat = '<span class="label label-danger">Account Info.</span>';
                            }
                        }else{
                            $stat = '<span class="label label-danger">User ID</span>';
                        }

                        $data['list'][] = array(
                            'servno' => $row->servno,
                            'mtr' => $row->mtr,
                            'mtrno' => $row->mtrser,
                            'ref' => $row->ref,
                            'status' => $stat,
                        );
                    }
                }
            }
        }
        return json_encode($data);
    }

}