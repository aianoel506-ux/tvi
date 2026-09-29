<?php
/**
 * Created by PhpStorm.
 * User: ITD-SE
 * Date: 5/30/2018
 * Time: 1:22 PM
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

Class Model_user extends CI_Model
{
    function get_trouble_call_list()
    {

    }


    function add_shortcut() {
        $data = array();
        $moduleid = $this->input->post('moduleid');
        $qry = false;

        $this->db->trans_begin();
        $this->db->where(array('userid' => user_id(), 'moduleid' => $moduleid, 'status' => 1));
        $this->db->update('prime_system_users_module_shortcut', array('status' => 0));

        $ins_arr = array(
            'moduleid' => $moduleid,
            'userid' => user_id(),
        );
        $this->db->insert('prime_system_users_module_shortcut', $ins_arr);

        if($this->db->trans_status() == true) {
            $qry = true;
            $this->db->trans_commit();
        }else{
            $this->db->trans_rollback();
        }

        $data['qry'] = $qry;
        return json_encode($data);
    }

    function dell_shortcut() {
        $data = array();
        $moduleid = $this->input->post('moduleid');
        $qry = false;

        $this->db->trans_begin();
        $this->db->where(array('userid' => user_id(), 'moduleid' => $moduleid, 'status' => 1));
        $this->db->update('prime_system_users_module_shortcut', array('status' => 0));

        if($this->db->trans_status() == true) {
            $qry = true;
            $this->db->trans_commit();
        }else{
            $this->db->trans_rollback();
        }

        $data['qry'] = $qry;
        return json_encode($data);
    }

    function get_user_access() {
        $data = array();

        $html = '';

        $qry_user_shortcut = $this->db->select(
            '
                ums.sysid,
                sn.sysid AS moduleid,
                sn.name, 
                sn.desc,
                sn.type,
                sn.hashcode,
                sn.pagefile,
                sn.url,
                sn.icon,
                sn.htmlclass
            '
        )
            ->from('prime_system_users_module_shortcut AS ums')
            ->join('prime_module_navigations_main AS sn', 'sn.sysid = ums.moduleid')
            ->where(array('ums.status' => 1, 'ums.userid' => user_id()))
            ->get();

        if($qry_user_shortcut->num_rows() > 0) {
            foreach ($qry_user_shortcut->result() as $row) {
                $link_action = ($row->type > 2) ? 'target="_blank "' : '';
                $qry_session_module = $this->db->select()->from('prime_module_users_logs')
                    ->where(array('moduleid' => $row->moduleid, 'userid' => user_id()))
                    ->get()->row();

                if($row->type == 3) {
                    $link = base_url($row->url);
                }else {
                    $link = base_url('module/' . $row->hashcode . '/' . $row->url);
                }
                $html .= '<div class="col-lg-2 col-md-4 col-xs-12" id="shortcut_item">';
                $html .= '<div class="mt-element-ribbon bg-grey-steel">';
                $html .= '<div class="ribbon ribbon-shadow ribbon-color-success uppercase">';
                $html .= '<a '.$link_action.' href="'.$link.'" class="btn btn-default btn-xs inline pull-right "><i class="fa '.$row->icon.' text-'.$row->htmlclass.' fa-fw"></i> '.$row->name.'</a>';
                $html .= '</div>';
                $html .= '<p class="ribbon-content small font-blue" style="min-height: 50px; padding-top: 8px !important; padding-bottom: 5px !important;">';

                if($qry_session_module) {
                    $html .= 'Last Visit: <br>' . $qry_session_module->datecreated;
                }

                $html .= '</p>';
                $html .= '<p class="ribbon-content">';
                $html .= '<a href="javascript:;" id="btn_remove" data-id="'.$row->moduleid.'" class="btn btn-danger btn-xs inline"><i class="fa fa-times"></i> Remove</a>';
                $html .= '<a <?php '.$link_action.' href="'.$link.'" class="btn btn-default btn-xs inline pull-right"><i class="fa fa-search"></i> View</a>';
                $html .= '</p>';
                $html .= '</div>';
                $html .= '</div>';

            }
        } else {
            $html .= '<div class="col-md-12">';
            $html .= '<h4>No shortcut found!</h4>';
            $html .= '</div>';
        }

        $data['html'] = $html;
        return json_encode($data);
    }

}