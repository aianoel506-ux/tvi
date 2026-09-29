<?php
if(!function_exists('upload_options')) {
    function upload_options($full_path, $new_name)
    {
        //upload an image options
        $config = array();

        $config['upload_path'] = $full_path;
        $config['allowed_types'] = 'gif|jpg|png';
        $config['max_size'] = 5000;
        $config['max_width'] = 4024;
        $config['max_height'] = 3768;
        //$config['encrypt_name']         = TRUE;
        $config['file_name'] = $new_name;

        return $config;
    }
}