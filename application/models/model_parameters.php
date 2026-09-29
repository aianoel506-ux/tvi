<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Model_parameters extends CI_Model
{
	
	function str_replace_deep($search, $replace, $subject) 
	{ 
		if (is_array($subject)) 
		{ 
			foreach($subject as &$oneSubject) 
				$oneSubject = str_replace_deep($search, $replace, $oneSubject); 
			unset($oneSubject); 
			return $subject; 
		} else { 
			return str_replace($search, $replace, $subject); 
		} 
	}
}


	
?>