<?php
/**
 * Created by PhpStorm.
 * User: ITD-SE
 * Date: 10/24/2018
 * Time: 2:39 PM
 */


$blob = $this->input->post('ids');


$file_pic_arr = glob($blob);

$home_dir = explode('./', $blob);
$actual_dir = explode('*.*', $home_dir[1]);

$html = '';

$file_cnt = count($file_pic_arr);
?>

<div class="row" style="margin: 20px 20px;">
    <div class="col-md-9">
        <?php echo '<p>Files: ' . $file_cnt . '</p>'; ?>
    <div class="meter-thumbnail-pics big" id="mtr_pics" style="overflow: auto; height: 500px;">
<?php

if($file_cnt>0) {
    foreach($file_pic_arr as $mtr) {
        $pic_arr = explode('/', $mtr);
        $pic_name = end($pic_arr);
        $html .= '<div class="items">';
        $html .= '<button type="button" data-dir="" data-acct="" data-file="" data-year="" data-month="" class="btn btn-danger btn-xs" id="btn_delete_permanent"><i class="fa fa-times"></i></button>';
        $html .= '<a target="_blank" href="' . base_url($actual_dir[0] .  $pic_name )  . '" class="fancybox-button view-text inside bg-yellow-casablanca bg-font-yellow-casablanca">View</a>';
        $html .= '<img class="img-responsive" style="width: 100%; height: 100%;" src="' .base_url($actual_dir[0] .  $pic_name ) . '">';
        $html .= '</div>';

    }
}else{
    $html .= '<p>No file found!</p>';
}
echo $html;

?>
</div>
</div>
<div class="col-md-3">
    Select file for details
</div>
</div>
