<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

$route['default_controller'] 			= "peco";
$route['404_override'] 					= 'pages/error404';


$route['setup/db']	 			        = "setup/db";
$route['setup/slogin']	 			    = "setup/slogin";
$route['setup/resettranssess']	 	    = "setup/resettranssess";


// INDEXING
$route['customer/(:any)']	 			= "customer/index/$1";
$route['profile/(:any)']	 			= "profile/index/$1";
$route['ajax/(:any)']	 			    = "ajax/index/$1";
$route['person/(:any)']	 			    = "person/index/$1";


// SPECIFIC PAGES
$route['customer/newentry'] 			= "customer/newentry";
$route['profile/newentry'] 				= "profile/newentry";
$route['profile/addroletouser'] 		= "profile/addroletouser";
// DYNAMIC ROUTES
/*require_once( BASEPATH .'database/DB'. EXT );
$db =& DB();
$query = $db ->select( 'pmm.name, pmnm.hash AS subnav' )
			 ->from( 'prime_module_main pmm' )
			 ->join( 'prime_module_navigations_main AS pmnm', 'pmnm.parent = pmm.sysid', 'left' )
			 ->get();
$result = $query->result();
foreach( $result as $row )
{
    $route[ 'module/'.strtolower(str_replace(' ', '-',$row->name)) ]                 = strtolower(str_replace(' ', '-', 'module/'));
    $route[ 'module/'.strtolower(str_replace(' ', '-',$row->name.'/:any')) ]         = strtolower(str_replace(' ', '-', 'module/'.$row->subnav));
}*/


/* End of file routes.php */
/* Location: ./application/config/routes.php */