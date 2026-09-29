<?php


class Model_purchasing extends CI_Model
{
    function tbl_suppliers()
    {
        $data = array();
        $sql = $this->db->query("SELECT
                                    s.sysid, 
                                    s.descs, 
                                    isa.address, 
                                    isc.contact,
                                    GROUP_CONCAT(CONCAT(isc.typesid, '-', isc.contact)) AS contact_arr
                                FROM inventory_suppliers AS s
                                    LEFT JOIN inventory_suppliers_address AS isa ON  s.sysid = isa.supplierid AND isa.`status` = 1
                                    LEFT JOIN inventory_suppliers_contact AS isc ON s.sysid = isc.supplierid
                                WHERE s.`status` = 1");
        if($sql->num_rows()>0) {
            foreach($sql->result() as $row) {
                $email = '';
                $phone = '';
                $contact_arr = explode(',', $row->contact_arr);
                if(is_array($contact_arr) & count($contact_arr)>0) {
                    foreach($contact_arr as $crow) {
                        $contact_arr_1 = explode('-', $crow);
                        if($contact_arr_1[0] == 1053) { // EMAIL CODE
                            $email = $contact_arr_1[1];
                        }else{
                            $phone = $contact_arr_1[1];
                        }
                    }
                }
                $data['list'][] = array(
                    'expand' => $row->sysid,
                    'name' => $row->descs,
                    'address' => $row->address,
                    'phone' => $phone,
                    'email' => $email,
                    'products' => 0,
                    'purchasedqty' => number_format(0, 0),
                    'purchasedamt' => number_format(0, 2),
                    'control' => ''
                );
            }
        }
        return json_encode($data);
    }


}