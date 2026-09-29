<?php
/**
 * Created by PhpStorm.
 * User: ITD-SE
 * Date: 7/3/2018
 * Time: 4:47 PM
 */

class Model_person extends CI_Model
{
    function get_person_info($id = false) {
        $res = false;
        if($id) {
            $name_arr = explode('-', $id);
            if (count($name_arr) > 2) {
                $firstname = str_replace('_', ' ', $name_arr[0]);
                $middlename = str_replace('_', ' ', $name_arr[1]);
                $lastname_arr = explode('.', str_replace('_', ' ', $name_arr[2]));
                $lastname = $lastname_arr[0];
                $this->db->where(array('lastname' => $lastname, 'middlename' => $middlename, 'firstname' => $firstname));
            } else {
                $firstname = str_replace('_', ' ', $name_arr[0]);
                $lastname_arr = explode('.', str_replace('_', ' ', $name_arr[1]));
                $lastname = $lastname_arr[0];
                $this->db->where(array('lastname' => $lastname, 'firstname', $firstname));
            }
            $query = $this->db->select()->from('person')->get()->row();
            if ($query) {
                $res = (object)array(
                    'fullname' => $query->firstname . ' ' . $query->middlename . ' ' . $query->lastname,
                    'lastname' => $query->lastname,
                    'middlename' => $query->middlename,
                    'firstname' => $query->firstname,
                    'sysid' => $query->sysid);
            }
        }

        return $res;
    }

}