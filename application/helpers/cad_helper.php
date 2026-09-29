<?php
if(!function_exists('address_name')) {
    function address_name($type,$id) {
        $ci = get_instance();
        $address = array(
            'brgy' => array('address_barangay','texts'),
            'dist' => array('address_districts','names'),
            'city' => array('address_city','descriptions')
        );

        $keys = array_keys($address);

        $desc = '';

        if (in_array($type,$keys)) {
            $tbl = $address[$type][0];
            $col = $address[$type][1];
            $qry = $ci->db->select($col)
                ->from($tbl)
                ->where('sysid', $id)->get()->row();

            if ($qry) {
                $desc = $qry->$col;
            }
            return $desc;
        } else {
            return false;
        }
    }
}

if(!function_exists('get_rate_type')) {
    function get_rate_type($id) {
        $ci = get_instance();
        $qry = $ci->db->select('classifications')->from('prime_system_rate_class_main')
            ->where(array('sysid' => $id))
            ->get()->row();

        $name = $qry->classifications;
        return $name;
    }
}

if(!function_exists('check_application_duplicate')) {
    function check_application_duplicate() {
        $ci = &get_instance();
        $apptype = $ci->input->post('apptype');
        $corpdesc = $ci->input->post('corpname');
        $corpbranch = $ci->input->post('corpbranch');
        $lastname = $ci->input->post('lastname');
        $firstname = $ci->input->post('firstname');
        $middlename = $ci->input->post('middlename');
        $suffix = $ci->input->post('suffix');

        $application = false;

        if ($apptype == 1) {
            $where = ($suffix) ? ' AND t.titleid = '.$suffix : '';

            $qry_person = $ci->db->query("
            SELECT p.* 
            FROM person AS p
            LEFT JOIN person_title as t on t.personid = p.sysid
            WHERE lastname = '$lastname'
            AND firstname = '$firstname'
            AND middlename LIKE '%$middlename%'
            ".$where)->row();

            $personid = ($qry_person) ? $qry_person->sysid : false;

            if ($personid) {
                $find_qry = $ci->db->select('*')
                    ->from('application_customers_details')
                    ->where(array('personid' => $personid, 'status' => 1))
                    ->get()->row();

                $application = ($find_qry) ? $find_qry : false;
            }
        }

        /*  @TODO: Create query to find Gov't and Commercial applications already exist. */

        if ($apptype == 2) {

            if ($corpbranch != '') {
                $ci->db->where('cb.names',$corpbranch);
            }

            $find_qry = $ci->db->select('acd.*')
                ->from('application_customers_details AS acd')
                ->join('application_customers_corporation AS acc','acc.appid = acd.sysid AND acc.status = 1','left')
                ->join('corporation AS c','acc.corpid = c.sysid','left')
                ->join('corporation_branches AS cb','c.sysid = cb.corpid','left')
                ->where(array('c.descs' => $corpdesc,'acd.status' => 1 ))
                ->get()->row();

            $application = ($find_qry) ? $find_qry : false;
        }

        if ($apptype == 3) {

            if ($corpbranch != '') {
                $ci->db->where('gb.names',$corpbranch);
            }

            $find_qry = $ci->db->select('acd.*')
                ->from('application_customers_details AS acd')
                ->join('application_customers_corporation AS acc','acc.appid = acd.sysid','left')
                ->join('government_main AS g','acc.corpid = g.sysid','left')
                ->join('government_main_branches AS gb','g.sysid = gb.corpid','left')
                ->where(array('g.descs' => $corpdesc,'acc.status' => 1 ))
                ->get()->row();

            $application = ($find_qry) ? $find_qry : false;
        }

        return $application;
    }
}

if (!function_exists('get_tssr_layout')) {
    function get_tssr_layout($id) {
        $ci = &get_instance();
        $id = ($id) ? $id : $ci->input->post('id');
        $data = array();
        $info = array();
        $info['appid'] = $id;

        $app = application_info($id);
        $title = '';
        $data['docid'] = false;
        if ($app) {
            $info['app'] = $app;
            $title = 'PAE'.str_pad($app->essrno, 5, "0", STR_PAD_LEFT).' - '.ucwords(strtolower($app->appname)).' TSSR';
        }

        $saved = $ci->db->select('sysid,html')
            ->from('prime_documents_main')
            ->where(array('dataid' => $id, 'doctype' => 3436, 'status' => 1))
            ->get()->row();

        if ($saved) {
            $html = $saved->html;
            $data['docid'] = $saved->sysid;
        } else {
            $published_qry = $ci->db->select()
                ->from('application_customers_system_size')
                ->where(array('appid' => $id, 'status =' => 305))
                ->get()->row();

            if ($published_qry) {
                $survey = $published_qry;
                $info['survey'] = $survey;
                $creator = user_info($survey->createdby);
                $info['author'] = ucwords(strtolower($creator->firstname)) . (($creator->middlename) ? ' ' . $creator->middlename[0] . '.' : '') . ' ' . ucwords(strtolower($creator->lastname));
                $details_qry = $ci->db->select()
                    ->from('application_customers_survey_details')
                    ->where(array('logid' => $survey->sysid, 'status' => 1))
                    ->get();

                if ($details_qry->num_rows() > 0) {
                    foreach ($details_qry->result() AS $details) {
                        $infotype = $details->infotype;
                        $details = (array)$details;
                        foreach ($details as $detkey => $detval) {
                            if ($detkey == 'measurements' || $detkey == 'remarks') {
                                $info['details'][$infotype][$detkey] = $detval;
                            }
                        }
                    }
                }

                $info_qry = $ci->db->select()
                    ->from('application_customers_survey_info')
                    ->where(array('logid' => $survey->sysid, 'status' => 1))
                    ->get()->row();

                if ($info_qry) {
                    $info['info'] = $info_qry;
                }
            }

            $team_qry = $ci->db->select('empid')
                ->from('application_customers_team_assignment')
                ->where(array('appid' => $id, 'moduleid' => 15, 'status' => 1))
                ->get();

            if ($team_qry->num_rows() > 0) {
                foreach ($team_qry->result() as $row) {
                    $person = get_employee_info($row->empid);
                    $info['team'][] = ucwords(strtolower($person->firstname)) . (($person->middlename) ? ' ' . $person->middlename[0] . '.' : '') . ' ' . ucwords(strtolower($person->lastname));
                }
            }

            $file_directory = FCPATH . "uploads/attachments/cad/applications/" . str_pad($id, 6, "0", STR_PAD_LEFT) . "/Assessment/Survey/";
            $file_url = base_url() . "uploads/attachments/cad/applications/" . str_pad($id, 6, "0", STR_PAD_LEFT) . "/Assessment/Survey/";
            $map = directory_map($file_directory, FALSE, TRUE);
            $files = array();

            if ($map && count($map) > 0) {
                foreach ($map as $file) {
                    $filename = explode('_', $file);
                    if (isset($filename[2])) {
                        $files[strtolower($filename[0])][] = $file_directory . $file;
                    } else {
                        $files[strtolower($filename[0])] = $file_directory . $file;
                    }
                }
            }

            $info['files'] = $files;

            $html = $ci->load->view('custom/templates/tssr', $info, true);
        }
        //$data['title'] = $title;
        //$data['info'] = $info;
        //$data['html'] = $html;
        return $html;
    }
}

if (!function_exists('rehash_pdf_img')) {
    function rehash_pdf_img($html) {
        $domDoc = new DOMDocument();
        $domDoc->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NOWARNING | LIBXML_NOERROR);
        $xpath = new DOMXPath($domDoc);

        foreach ($xpath->query('//img') as $img) {
            $imgPath = urldecode($img->getAttribute('src'));
            $base64img = convert_base64_img($imgPath);
            //$newimg = $domDoc->createElement('img');
            $img->setAttribute('src',$base64img);
        }

        foreach ($xpath->query('//ul') as $ul) {
            $styles = explode(';',$ul->getAttribute('style'));
            if (count($styles) > 0) {
                $attrib = array();
                foreach ($styles as $attr) {
                    $atr = explode(': ',$attr);
                    $attrib[trim($atr[0])] = $atr[1];
                }

                /*echo "<pre>";
                print_r ($attrib);
                echo "</pre>";
                exit();*/
                $bulletimg = $attrib['list-style-image'];
                $ini = strpos($bulletimg, '(');
                if ($ini == 0) return '';
                $ini += strlen('(');
                $len = strpos($bulletimg, ')', $ini) - $ini;
                $bulletpath = substr($bulletimg,$ini,$len);
                $base64bullet = convert_base64_img($bulletpath);
                $attrib['list-style-image'] = 'url('.$base64bullet.')';

                $newstyle = '';
                foreach ($attrib AS $key => $value) {
                    $newstyle .= $key.': '.$value.'; ';
                }

                $ul->setAttribute('style',$newstyle);
            }
        }

        //$html .= $saved->html;
        return $domDoc->saveHTML();
    }
}
if (!function_exists('rehash_pdf')) {
    function rehash_pdf($html) {
        $domDoc = new DOMDocument();
        $domDoc->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NOWARNING | LIBXML_NOERROR);
        $xpath = new DOMXPath($domDoc);
        $png = array();

        foreach ($xpath->query("//*text()[contains(., '.png')]") as $img) {
            $png[] = $img;
            /*$imgPath = urldecode($img->getAttribute('src'));
            $base64img = convert_base64_img($imgPath);
            //$newimg = $domDoc->createElement('img');
            $img->setAttribute('src',$base64img);*/
        }

        //$html .= $saved->html;
        return $png;
    }
}