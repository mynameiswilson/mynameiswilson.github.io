<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$route['default_controller'] = "tumblr/page";
$route['404_override'] = '';

$route['(:any)']        = "tumblr/page/$1";
$route['(:any)/(:num)'] = "tumblr/page/$1/$2";

/* End of file routes.php */
/* Location: ./application/config/routes.php */
