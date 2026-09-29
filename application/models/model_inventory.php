<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Model_inventory extends CI_Model
{
    function tbl_get_data_initialization() {
        $codes = $this->input->post('codes');
        $sql = $this->db->select('sysid, names, desc')
            ->from('prime_types_parameter')
            ->where('codes', $codes)
            ->get();
        $data = array();
        if($sql->num_rows()>0) {
            foreach($sql->result() as $row) {
                $data['list'][] = array(
                    'expand' => $row->sysid,
                    'codes' => $row->names,
                    'descs' => $row->desc,
                    'ownership' => '',
                    'status' => '',
                    'control' => '',
                );
            }
        }

        return json_encode($data);
    }

    function data_add_initialization() {
        $input = $this->input->post();
        $table = $this->input->post('table');

        unset($input['table']);
        $this->db->trans_begin();
        $this->db->insert('prime_types_parameter', $input);
        $data = db_trans($this->db);
        $data['table'] = $table;
        return json_encode($data);
    }

    function tbl_get_products()
    {
        $data = array();
        $sql = $this->db->query("SELECT
                                    ip.sysid, 
                                    ii.descs, 
                                    tp.`desc` AS brand, 
                                    ip.remarks
                                FROM inventory_items AS ii
                                    LEFT JOIN inventory_products AS ip ON ii.sysid = ip.itemid
                                    LEFT JOIN inventory_brands AS ib ON ip.sysid = ib.prodid
                                    LEFT JOIN prime_types_parameter AS tp ON ib.typesid = tp.sysid
                                    WHERE ip.`status` = 1
                            ");
        if($sql->num_rows()>0) {
            foreach($sql->result() as $row) {
                $control = '';
                $control .= '<a class="btn btn-xs btn-danger inline" href="javascript:;" id="btn_delete"><i class="fa fa-times"></i></a>';
                $control .= '<a class="btn btn-xs btn-info inline" href="javascript:;" id=""><i class="fa fa-search"></i></a>';
                $data['list'][] = array(
                    'expand' => $row->sysid,
                    'product' => $row->descs,
                    'brand' => $row->brand,
                    'remarks' => $row->remarks,
                    'status' => 'Active',
                    'control' => $control
                );
            }
        }
        return json_encode($data);
    }

    function tbl_get_stocks() {
        $data = array();


        $sql_stocks = $this->db->query("
            SELECT
                ims.serials,
                ims.names AS `desc`,
                supp.descs AS supplier,
                sa.address,
                ism.itemid,
                ism.brandid,
                ism.qty,
                ism.price,
                ism.purchasedate,
                ism.sysid,
                pu.unit_code AS `units`
            FROM
                inventory_stocks_main AS ism
                LEFT JOIN items_main_spec AS ims ON ims.sysid = ism.itemid
                LEFT JOIN inventory_suppliers AS supp ON ism.suppid = supp.sysid
                LEFT JOIN inventory_suppliers_address AS sa ON sa.supplierid = supp.sysid
                LEFT JOIN prime_unit AS pu ON pu.sysid = ims.unitid
                WHERE ism.`status` = 1
                ORDER BY ism.datecreated DESC
        ");
        if($sql_stocks->num_rows()>0) {
            foreach($sql_stocks->result() as $row) {

                $re_order_point = 0.5;

                $sysid = $row->sysid;

                $brand_sql = get_types_name($row->brandid);
                $brand = ($brand_sql) ? $brand_sql->names : 'Unknown';

                $control = '';
                $control .= '<a href="#frm_inventory_edit_stocks" data-arr="'.$row->sysid.'" title="Edit Stocks" data-toggle="ajax-modal" class="btn btn-default inline btn-xs"><i class="fa fa-edit"></i></a>';
                $control .= '<a href="#frm_inventory_view_stocks" data-arr="'.$row->sysid.'" title="View Stocks ('.$sysid.' - '.$row->desc.' - ' . $brand. ')" data-toggle="ajax-modal" class="btn btn-primary inline btn-xs"><i class="fa fa-search"></i></a>';
                $control .= '<a href="#frm_inventory_request_stocks" data-arr="'.$row->sysid.'" title="Request Stocks" data-toggle="ajax-modal" class="btn btn-success inline btn-xs"><i class="fa fa-download"></i></a>';
                $control .= '<a href="#frm_inventory_stocks_out" data-arr="'.$row->sysid.'" title="Release Stocks" data-toggle="ajax-modal" class="btn btn-danger inline btn-xs"><i class="fa fa-sign-out"></i></a>';

                $serials = $row->serials;
                if(empty($row->serials)) {
                    $serials = '<i class="fa fa-refresh"></i> Generate';
                }

                // query stocks returned
                $sql_stocks_return = $this->db->query("SELECT SUM(qty) AS qty FROM inventory_stocks_return 
                                                    WHERE stockid = {$sysid} AND `status` = 1")->row();
                $return = ($sql_stocks_return) ? $sql_stocks_return->qty : 0;

                // query stocks out
                $sql_stocks_out = $this->db->query("SELECT SUM(qty) AS qty FROM inventory_stocks_out 
                                                    WHERE stockid = {$sysid} AND `status` = 1")->row();
                $released = ($sql_stocks_out) ? $sql_stocks_out->qty : 0;


                $qty = $row->qty;
                $onhand = (($qty - $released) + $return);
                $see_release = '';

                $row_bg = '';
                $status = '<span class="text-success"><i class="fa fa-check"></i> Sufficient</span>';
                $check_reorder_point = ($onhand / $qty);

                if($check_reorder_point <= $re_order_point) {
                    $status = '<span class="text-danger"><i class="fa fa-warning"></i> Reorder</span>';
                    $row_bg = 'row-danger';
                }

                if($released>0) {
                    $see_release = '<a title="Released List" href="#inventory_released_list" data-toggle="ajax-modal" class="btn btn-default btn-xs inline pull-left"><i class="fa fa-search"></i></a>';
                }

                $requested = ''; // QUERY FROM CAD

                $supplier = '';
                $supplier .= $row->supplier. '<br>';
                $supplier .= '<small class="font-red-flamingo">'.$row->address.'</small>';


                $product_image = '';
                $product_image .= '<img width=\'300px\' src=\''.base_url('uploads/attachments/inventory/products/00001/trina.jpg').'\'/>';

                $product = '';
                $product .= '<a  href="#" data-toggle="popover" data-trigger="hover" data-content="'.$product_image.'" class="popovers pull-right"><i class="fa fa-image"></i></a>';
                $product .= $row->desc;



                $data['list'][] = array(
                    'expand' => $sysid,
                    'stockid' => $sysid,
                    'serial' =>  $serials,
                    'storage' =>  'Main',
                    'supplier' => $supplier,
                    'product' => $product,
                    'brand' => $brand,
                    'qty' => $row->qty,
                    'requested' => $requested,
                    'released' => $see_release . $released,
                    'return' => $return,
                    'onhand' => $onhand,
                    'price' => number_format($row->price, 2),
                    'unit' => strtoupper($row->units),
                    'purchasedate' => $row->purchasedate,
                    'status' => $status,
                    'control' => $control,
                    'rowbg' => $row_bg
                );
            }
        }
        return json_encode($data);
    }

    function tbl_get_stock_in() {
        $data = array();
        $stockid = $this->input->post('stockid');
        $status = $this->input->post('status');


        $status_where = ($status) ? " AND status = $status " : " AND status > 0";
        $sql = $this->db->query("SELECT * FROM inventory_stocks_items WHERE stockid = $stockid $status_where");

        if($sql->num_rows()>0) {
            foreach($sql->result() as $row) {
                $controls = '';
                $controls .= '<a href="javascript:;" data-id="" class="btn btn-danger btn-xs inline" id="del_stock_in_item"><i class="fa fa-times"></i></a>';
                $data['list'][] = array(
                    'num' => $row->sysid,
                    'serials' => $row->serials,
                    'date' => $row->datecreated,
                    'status' => get_types_label_format($row->status),
                    'control' => $controls,
                );
            }
        }

        return json_encode($data);
    }

    function draft_stock_in() {
        $stockid = $this->input->post('stockid');
        $serials = $this->input->post('serials');


        $this->db->trans_begin();
        $ins_arr = array(
            'stockid' => $stockid,
            'serials' => $serials,
            'createdby' => user_id(),
            'updatedby' => user_id(),
            'status' => 307
        );
        $this->db->insert('inventory_stocks_items', $ins_arr);
        return json_encode(db_trans($this->db, false, false, false));
    }

    function save_stock_in() {
        $stockid = $this->input->post('stockid');

        $this->db->trans_begin();
        $this->db->update('inventory_stocks_items',
            array(
                'status' => 304,
                'updatedby' => user_id(),
            ),
            array(
                'stockid' => $stockid
            )
        );
        return json_encode(db_trans($this->db));
    }


    function query_stock_out() {
        $data = array();
        $save = $this->input->post('save');
        $input_return = $this->input->post('return');
        $codes = $this->input->post('codes');
        $save_qty = $this->input->post('qty');
        $row = $this->db->query("SELECT
                                    ism.sysid,
                                    ism.itemid,
                                    ims.`names`,
                                    isi.serials,
                                    ism.qty 
                                FROM
                                    inventory_stocks_items AS isi
                                    INNER JOIN inventory_stocks_main AS ism ON isi.stockid = ism.sysid
                                    INNER JOIN items_main_spec AS ims ON ism.itemid = ims.sysid 
                                WHERE
                                    isi.serials = '$codes'
                                    ")->row();
        $onhand = 0;
        if($row) {
            // query stocks out
            $sql_stocks_out = $this->db->query("SELECT SUM(qty) AS qty FROM inventory_stocks_out 
                                                    WHERE stockid = {$row->sysid} 
                                                    AND `status` = 1")->row();
            $released = ($sql_stocks_out) ? $sql_stocks_out->qty : 0;


            // query stocks returned
            $sql_stocks_return = $this->db->query("SELECT SUM(qty) AS qty FROM inventory_stocks_return 
                                                    WHERE stockid = {$row->sysid} AND `status` = 1")->row();
            $return = ($sql_stocks_return) ? $sql_stocks_return->qty : 0;

            $qty = $row->qty;
            $onhand = (($qty - $released) + $return);

            // save
            if($save) {
                $this->db->insert('inventory_stocks_out',
                    array(
                        'stockid' => $row->sysid,
                        'itemid' => $row->itemid,
                        'qty' => $save_qty
                    )
                );

                $data['title'] = 'PAE Inventory';
                $data['func'] = 'success';
                $data['msg'] = 'Stock out save!';
            }

            // return
            if($input_return) {
                $this->db->insert('inventory_stocks_return',
                    array(
                        'stockid' => $row->sysid,
                        'itemid' => $row->itemid,
                        'qty' => $save_qty
                    )
                );

                $data['title'] = 'PAE Inventory';
                $data['func'] = 'success';
                $data['msg'] = 'Stock returned save!';
            }
        } else {
            $data['msg'] = 'Not found!';
        }
        $data['qry'] = ($row) ? true : false;
        $data['qty'] = $onhand;
        $data['desc'] = ($row) ? $row->names : '';
        return json_encode($data);
    }

    function add_stocks() {
        $data = array();
        $itemid = $this->input->post('itemid');
        $supplierid = $this->input->post('supplierid');
        $qty = $this->input->post('qty');
        $brand = $this->input->post('brand');
        $price = $this->input->post('price');
        $date = $this->input->post('date');
        $this->db->trans_begin();
        $ins_arr = array(
            'itemid' => $itemid,
            'brandid' => $brand,
            'suppid' => $supplierid,
            'qty' => $qty,
            'price' => $price,
            'purchasedate' => $date,
            'createdby' => user_id(),
            'updatedby' => user_id()
        );
        $this->db->insert('inventory_stocks_main', $ins_arr);
        return json_encode(db_trans($this->db));
    }

    function tbl_products() {
        $data = array();
        $sql = $this->db->query("
             SELECT
                ims.serials,
                ims.names AS `desc`,
                supp.descs AS supplier,
                ism.itemid,
                ism.brandid,
                ism.qty,
                ism.sysid 
            FROM
                inventory_stocks_main AS ism
                INNER JOIN items_main_spec AS ims ON ims.sysid = ism.itemid
                INNER JOIN inventory_suppliers AS supp ON ism.suppid = supp.sysid
                WHERE ism.`status` = 1
				GROUP BY 
                ims.names,
                supp.descs
        ");
        if($sql->num_rows()>0) {
            foreach($sql->result() as $row) {


                // query stocks out
                $sql_stocks_out = $this->db->query("SELECT SUM(qty) AS qty FROM inventory_stocks_out 
                                                    WHERE stockid = {$row->sysid} 
                                                    AND `status` = 1")->row();
                $released = ($sql_stocks_out) ? $sql_stocks_out->qty : 0;


                // query stocks returned
                $sql_stocks_return = $this->db->query("SELECT SUM(qty) AS qty FROM inventory_stocks_return 
                                                    WHERE stockid = {$row->sysid} AND `status` = 1")->row();
                $return = ($sql_stocks_return) ? $sql_stocks_return->qty : 0;

                $qty = $row->qty;
                $onhand = (($qty - $released) + $return);

                $brand_sql = get_types_name($row->brandid);
                $brand = ($brand_sql) ? $brand_sql->names : 'Unknown';
                $data['list'][] = array(
                    'num' => $row->sysid,
                    'supplier' => $row->supplier,
                    'product' => $row->desc,
                    'brand' => $brand,
                    'qty' => $onhand,
                    'control' => ''
                );

            }
        }

        return json_encode($data);

    }

    function tbl_get_suppliers() {
        $data = array();
        $sql = $this->db->query("
                SELECT
                    supp.sysid,
                    supp.descs,
                    sa.address 
                FROM
                    inventory_suppliers AS supp
                    LEFT JOIN inventory_suppliers_address AS sa ON sa.supplierid = supp.sysid 
                WHERE
                    supp.`STATUS` = 1
            ");
        if($sql->num_rows() > 0) {
            foreach($sql->result() as $row) {
                $telephone = $this->get_supplier_contact($row->sysid, 1050);
                $cellphone = $this->get_supplier_contact($row->sysid, 1051);
                $email = $this->get_supplier_contact($row->sysid, 1053);
                $data['list'][] = array(
                    'num' => $row->sysid,
                    'name' => $row->descs,
                    'address' => $row->address,
                    'telephone' => $telephone,
                    'cellphone' => $cellphone,
                    'email' => $email,
                    'control' => ''
                );
            }
        }
        return json_encode($data);
    }

    function get_supplier_contact($suppid, $typesid) {
        $sql = $this->db->query("SELECT * FROM inventory_suppliers_contact WHERE supplierid = $suppid AND typesid = $typesid AND status = 1 ORDER BY sysid DESC")->row();
        return ($sql) ? $sql->contact : '';
    }


    function stock_details() {
        $data = array();


        $html = '';



        $html .= '<div class="row margin-bottom-5">';



        $html .= '<div class="col-md-3">';
        $html .= '<ul class="list-group summary column no-border list-group-sm">';
        $html .= '<li class="list-group-item">';
        $html .= '<span class="col-md-4 label-name">Last Update</span>';
        $html .= '<span class="col-md-8 label-default">'.date('Y-m-d').'</span>';
        $html .= '</li>';
        $html .= '<li class="list-group-item">';
        $html .= '<span class="col-md-4 label-name">Updated By</span>';
        $html .= '<span class="col-md-8 label-default">'.get_users_info(1)->username.'</span>';
        $html .= '</li>';
        $html .= '</ul>';
        $html .= '</div>';

        $html .= '<div class="col-md-3">';
        $html .= '<ul class="list-group summary column no-border list-group-sm">';
        $html .= '<li class="list-group-item">';
        $html .= '<span class="col-md-4 label-name">Verification</span>';
        $html .= '<span class="col-md-8 label-default"></span>';
        $html .= '</li>';
        $html .= '<li class="list-group-item">';
        $html .= '<span class="col-md-4 label-name">Verified</span>';
        $html .= '<span class="col-md-8 label-default"></span>';
        $html .= '</li>';
        $html .= '</ul>';
        $html .= '</div>';



        $data['html'] = $html;
        return json_encode($data);
    }

    function generate_barcode($stockid = false, $codestart = false, $codecount = false) {
        $data = array();
        $html = '';
        $msg = '';
        $stockid = $this->input->post('stockid');
        $codestart = $this->input->post('codestart');
        $codecount = $this->input->post('codecount');
        $type = $this->input->post('type');




        $sql_stocks = $this->db->query("
            SELECT
                ims.serials,
                ims.names AS `desc`,
                supp.descs AS supplier,
                sa.address,
                ism.itemid,
                ism.brandid,
                ism.qty,
                ism.price,
                ism.purchasedate,
                ism.sysid,
                pu.unit_code AS `units`
            FROM
                inventory_stocks_main AS ism
                LEFT JOIN items_main_spec AS ims ON ims.sysid = ism.itemid
                LEFT JOIN inventory_suppliers AS supp ON ism.suppid = supp.sysid
                LEFT JOIN inventory_suppliers_address AS sa ON sa.supplierid = supp.sysid
                LEFT JOIN prime_unit AS pu ON pu.sysid = ims.unitid
                WHERE ism.`sysid` = $stockid
                ORDER BY ism.datecreated DESC
        ")->row();
        $item_code = 'Unknown';
        $item_desc = ($sql_stocks) ? $sql_stocks->desc: false;
        $item_supp = ($sql_stocks) ? $sql_stocks->supplier : '';
        if($item_desc) {
            $item_code = $item_desc . $item_supp;
        } else {
            if($item_supp != '') {
                $item_code = $item_supp;
            }
        }

        // check if existing table
        $sql_codes = $this->db->query("SELECT * FROM inventory_serialcodes WHERE stockid = $stockid AND status = 1");


        if($type == 1) {
            $html .= '<div class="row">';
            if($sql_codes->num_rows()>0) {
                foreach($sql_codes->result() as $row) {
                    $html .= '<div class="text-align-center" style="position: relative; display: inline-block; width: 25%; margin-bottom: 15px;">';
                    $html .= '<span style="font-size: 9px;">'.$row->descs . '</span><br>';
                    $html .= '<img alt="' . $row->serialcode . '" style="min-height: 60px;" src="' . base_url() . 'barcode.php?text=' . $row->serialcode . '" /><br>';
                    $html .= '<span style="font-size: 20px;">PAE '.$row->serialcode.'</span>';
                    $html .= '</div>';
                }
            }else {
                if ($codecount && $codecount > 0) {
                    for ($i = 1; $i <= $codecount; $i++) {
                        $code = str_pad(($codestart + $i), 8, '0', STR_PAD_LEFT);
                        $html .= '<div class="text-align-center" style="position: relative; display: inline-block; width: 25%; margin-bottom: 15px;">';
                        $html .= '<span style="font-size: 9px;">'.$item_code . '</span><br>';
                        $html .= '<img alt="' . $code . '" style="min-height: 60px;" src="' . base_url() . 'barcode.php?text=' . $code . '" /><br>';
                        $html .= '<span style="font-size: 20px;">PAE '.$code.'</span>';
                        $html .= '</div>';
                    }
                }
            }
            $html .= '</div>';
            $msg = '<h3><i class="fa fa-print text-success"></i>Printing...</h3>';
        } else {
            if ($codecount && $codecount > 0) {
                $this->db->update('inventory_serialcodes', array('status' => 0), array('stockid' => $stockid));
                for ($i = 1; $i <= $codecount; $i++) {
                    $code = str_pad(($codestart + $i), 8, '0', STR_PAD_LEFT);
                    $sql_chk_codes = $this->db->query("SELECT * FROM inventory_serialcodes WHERE stockid = $stockid AND status = 1 AND serialcode = '$code'")->row();
                    if($sql_chk_codes == false) {
                        $this->db->insert('inventory_serialcodes', array(
                            'stockid' => $stockid,
                            'serialcode' => $code,
                            'descs' => $item_code
                        ));
                    }
                }
                $msg = '<h3><i class="fa fa-check text-success"></i>Serial codes table has been updated!</h3>';
            }else{
                $msg = '<h3><i class="fa fa-times text-warning"></i>Please review series.</h3>';
            }
        }

        $data['msg'] = $msg;
        $data['html'] = $html;
        return json_encode($data);

    }

    function set_barcode($code)
    {
        //generate barcode
        return Zend_Barcode::render('code128', 'image', array('text'=>$code), array());
    }

}