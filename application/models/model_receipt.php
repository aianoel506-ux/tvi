<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of model_receipt
 *
 * @author PECO
 */
class Model_receipt extends CI_model{
    function two_cols($col_1 = '', $col_2 = '', $pesoSign = false){
        $col_2 = ($pesoSign ? 'PhP'.$col_2 : $col_2);
        //$space = 56 - strlen($col_1.$col_2);//Font_B has 56 characters/line
        if(strlen($col_1 . $col_2) < 40) {
            $space = 40 - strlen($col_1 . $col_2);//Font_B has 50 characters/line for Dot Matrix Printer
        }else{
            $space = 40;
        }
        return $col_1.str_repeat(" ", $space).$col_2."\n";
    }
    function two_cols_a($col_1 = '', $col_2 = '', $check = false){
        $col_2 = $col_2.($check?" /":"  ");
        //$space = 56 - strlen($col_1.$col_2);//Font_B has 56 characters/line
        if(strlen($col_1 . $col_2) < 40) {
            $space = 40 - strlen($col_1 . $col_2);//Font_B has 50 characters/line for Dot Matrix Printer
        }else{
            $space = 40;
        }
        return $col_1.str_repeat(" ", $space).$col_2."\n";

    }
    function three_cols($col_1 = '', $col_2 = '', $col_3 = ''){
        $space = str_repeat(" ", floor((40 - strlen($col_1.$col_2.$col_3))/2));//Font_B has 56 characters/line
        return $col_1.$space.$col_2.$space.$col_3."\n";
    }
    function four_cols($col_1 = '', $col_2 = '', $col_3 = '', $col_4 = '', $check = false){
        /*Objective: four columns with properly aligned data.*/
        $col_1 = $col_1.(str_repeat(" ", 6-strlen($col_1)));
        $col_2 = $col_2.(str_repeat(" ", 17-strlen($col_2)));
        $col_3 = $col_3.(str_repeat(" ", 6-strlen($col_3)));
        $col_4 = (str_repeat(" ", 25-strlen($col_4))).$col_4.($check?" /":"  ");

        return $col_1.$col_2.$col_3.$col_4."\n";
    }

    function four_cols_br($col_1 = '', $col_2 = '', $col_3 = '', $col_4 = '', $check = false){
        /*Objective: four columns with properly aligned data.*/
        $col_1 = $col_1.(str_repeat(" ", 17-strlen($col_1)));
        $col_2 = $col_2.(str_repeat(" ", 17-strlen($col_2)));
        $col_3 = $col_3.(str_repeat(" ", 17-strlen($col_3)));
        $col_4 = (str_repeat(" ", 25-strlen($col_4))).$col_4.($check?" /":"  ");

        return $col_1.$col_2.$col_3.$col_4."\n";
    }


    function space_both_sides($string = ''){
        /*we fill empty spaces with string spaces in order to span our underline across the width of the receipt.*/
        $space = str_repeat(" ",floor((40-strlen($string))/2));//56 is the total number of FONT_B (smallest font of Epson TM-T88IV) characters per line
        return $space.$string.$space."\n";
    }
    function space_right($string = ''){
        /*we fill empty spaces with string spaces in order to span our underline across the width of the receipt.*/
        $space = str_repeat(" ",40-strlen($string));//56 is the total number of FONT_B (smallest font of Epson TM-T88IV) characters per line
        return $string.$space."\n";
    }
    function add_leading_zero($string = ''){
        $zero = str_repeat("0", 10-strlen($string));
        return $zero.$string;//does not have \n, so that it can be used in combination with functions with new lines at the end.
    }

    function printer_hostname(){
        $computer_name = $this->input->post('computer_name');
        if (!empty($computer_name)){
            return $computer_name;
        }else{
            $ip = $_SERVER['REMOTE_ADDR'];
            //return "ITD-SE"; //used only for testing
            return exec("nmblookup -A $ip | grep '<00' | grep -v GROUP | awk '{print $1}'"); //get the computer name of $ip, only works when server is Linux
        }
    }

}
