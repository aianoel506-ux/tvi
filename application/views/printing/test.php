<?php


// PECO HELPER // application/helpers
$printer = windows_printer_connector($compname);
$printer -> setJustification(Escpos::JUSTIFY_CENTER);
$printer -> setFont(Escpos::FONT_B);//set font size 
//$printer -> feed(1);
$printer -> text("THIS IS A TEST PRINT.\n");
$printer -> text("USER: ".user_id()."\n");
$printer -> setUnderline(true);
$printer -> text(sql_time()->DATECOMPLETE."\n");
$printer -> setUnderline(false);
$space = str_repeat(" ",floor((56-strlen($string))/2));//56 is the total number of FONT_B (smallest font of Epson TM-T88IV) characters per line
$printer -> text('_');
$printer -> feed(1);
$printer -> cut();
$printer -> close();