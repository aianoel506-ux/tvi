<?php


class Model_upload extends CI_Model
{
    function employee_attachments() {
        $id = $this->input->post('empid');
        $data = array();
        $map = directory_map('./uploads/employeeaccomp/' . $id . '/');

        $html = '';
        $num_rows = 0;
        if($map && count($map) > 0) {
            $html .= '<div class="row tiles">';
            foreach($map as $row) {
                if(!is_array($row)) {
                    $num_rows += 1;
                    $img_path = FCPATH . 'uploads/employeeaccomp/' . $id . '/' . $row;
                    $img_url = base_url('uploads/employeeaccomp/' . $id . '/' . $row);

                    $is_image = is_image($img_path);

                    // IF IMAGE IS TRUE CREATE IMG RESPONSIVE ZOOM
                    if ($is_image) {
                        $html .= '<div class="tile" style="background: url(' . $img_url . ') center center no-repeat; background-size: 100% 100%;">';
                        $html .= '<a href="' . $img_url . '" class="cbp-caption cbp-lightbox iframe" data-title="" rel="attachements">';
                        $html .= '<div class="tile-body" >';
                        $html .= '<i class="fa fa-search"></i>';
                        $html .= '</div>';
                        $html .= '</a>';
                        $html .= '<div class="tile-object">';
                        $html .= '<div class="name" style="text-align: center !important; width: 85%; padding-top: 20px !important; overflow: hidden;"> ' . $row . ' </div>';
                        $html .= '</div>';
                        $html .= '<div class="btn-group item-control">';
                        $html .= '<a id="btn_del_attachement" class="btn btn-danger btn-xs inline" href="javascript:;" data-id="' . $id . '" data-file="' . $row . '"><i class="fa fa-times"></i></a>';
                        $html .= '</div>';
                        $html .= '</div>';

                    } else {
                        $html .= '<div class="tile bg-green">';
                        $html .= '<a target="_blank" href="' . $img_url . '" class="cbp-caption cbp-lightbox iframe" data-title="" rel="attachements">';
                        $html .= '<div class="tile-body" >';
                        $html .= '<i class="fa fa-file-text"></i>';
                        $html .= '</div>';
                        $html .= '</a>';
                        $html .= '<div class="tile-object">';
                        $html .= '<div class="name" style="text-align: center !important; width: 85%; margin-top: 10px; overflow: hidden;"> ' . $row . ' </div>';
                        $html .= '</div>';

                        $html .= '<div class="btn-group item-control">';
                        $html .= '<a id="btn_del_attachement"  class="btn btn-danger btn-xs inline" href="javascript:;" data-id="' . $id . '" data-file="' . $row . '"><i class="fa fa-times"></i></a>';
                        $html .= '</div>';

                        $html .= '</div>';
                    }
                }
            }

            $html .= '</div>';
        }
        if($num_rows>0) {
            $data['html'] = $html;
        }else{
            $data['html'] = '<h4><i class="fa fa-times text-danger"></i> No file found!</h4>';
        }

        $data['map'] = $map;
        return json_encode($data);
    }

    function employee_delete_attahment() {
        $data = array();
        $qry = false;
        $id = $this->input->post('id');
        $file = $this->input->post('file');
        $img_path = FCPATH . 'uploads/employeeaccomp/' . $id . '/';
        $img_path_del = $img_path . 'deleted/';

        if(!file_exists($img_path_del)) {
            mkdir($img_path_del, 0777, true);
        }else{
            chmod($img_path_del, 0777);
        }

        if(rename($img_path . $file, $img_path_del . $file)){
            $qry = true;
        }

        $data['qry'] = $qry;
        return json_encode($data);
    }
}