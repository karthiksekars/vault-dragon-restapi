<?php

defined('BASEPATH') OR exit('No direct script access allowed');


$route['404_override']         = 'cobject/index_404';
$route['translate_uri_dashes'] = FALSE;

$route['default_controller'] = 'cobject/index_api';
$route['object']        = 'cobject/index_api';
$route['object/(:any)']        = 'cobject/index_api/$1';
