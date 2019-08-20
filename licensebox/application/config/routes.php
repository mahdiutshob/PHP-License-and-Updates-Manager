<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['api/check_connection_ext'] = 'api_external/check_connection_ext';
$route['api/latest_version'] = 'api_external/latest_version';
$route['api/check_update'] = 'api_external/check_update';
$route['api/download_update/(:any)/(:any)'] = 'api_external/download_update/type/$1/vid/$2';
$route['api/get_update_size/(:any)/(:any)'] = 'api_external/get_update_size/type/$1/vid/$2';
$route['api/activate_license'] = 'api_external/activate_license';
$route['api/check_license'] = 'api_external/check_license';
$route['api/verify_license'] = 'api_external/verify_license';
$route['api/deactivate_license'] = 'api_external/deactivate_license';

$route['api/check_connection_int'] = 'api_internal/check_connection_int';
$route['api/add_product'] = 'api_internal/add_product';
$route['api/get_product'] = 'api_internal/get_product';
$route['api/mark_product_active'] = 'api_internal/mark_product_active';
$route['api/mark_product_inactive'] = 'api_internal/mark_product_inactive';
$route['api/create_license'] = 'api_internal/create_license';
$route['api/get_license'] = 'api_internal/get_license';
$route['api/block_license'] = 'api_internal/block_license';
$route['api/unblock_license'] = 'api_internal/unblock_license';

$route['api/download_update'] = 'api_external/download_update';
$route['api/get_update_size'] = 'api_external/get_update_size';
$route['api/(:any)/(:any)/(:any)/(:any)'] = 'api_external/error';
$route['api/(:any)/(:any)/(:any)'] = 'api_external/error';
$route['api/(:any)/(:any)'] = 'api_external/error';
$route['api/(:any)'] = 'api_external/error';
$route['api'] = 'api_external/error';

$route['licenses/create'] = 'licenses/create';
$route['licenses/get_licenses'] = 'licenses/get_licenses';
$route['licenses/edit'] = 'licenses/edit';
$route['licenses/delete'] = 'licenses/delete';
$route['licenses/block'] = 'licenses/block';
$route['licenses/unblock'] = 'licenses/unblock';
$route['licenses/(:any)'] = 'licenses/view/$1';
$route['licenses'] = 'licenses/index';

$route['generate_external'] = 'products/generate_external_helper';
$route['generate_internal'] = 'products/generate_internal_helper';
$route['products/add'] = 'products/add';
$route['products/edit'] = 'products/edit';
$route['products/delete'] = 'products/delete';
$route['products/versions/add'] = 'products/add_version';
$route['products/versions/delete'] = 'products/delete_version';
$route['products/versions'] = 'products/view_versions';
$route['products/(:any)'] = 'products/view/$1';
$route['products'] = 'products/index';

$route['activations/get_activations'] = 'activations/get_activations';
$route['activations/delete'] = 'activations/delete';
$route['activations/activate'] = 'activations/activate';
$route['activations/deactivate'] = 'activations/deactivate';
$route['activations/(:any)'] = 'activations/view/$1';
$route['activations'] = 'activations/index';

$route['update_downloads/get_update_downloads'] = 'downloads/get_update_downloads';
$route['update_downloads/delete'] = 'downloads/delete';
$route['update_downloads/(:any)'] = 'downloads/view/$1';
$route['update_downloads'] = 'downloads/index';

$route['users/get_activities'] = 'users/get_activities';
$route['activities'] = 'users/activities';
$route['account'] = 'users/account';
$route['general'] = 'users/general';
$route['users/delete_api_key'] = 'users/delete_api_key';
$route['login'] = 'users/login';
$route['logout'] = 'users/logout';
$route['forgot_password'] = 'users/forgot_password';
$route['reset_password/(:any)/(:any)'] = 'users/reset_password/$1/$2';
$route['reset_password'] = 'users/reset_password';

$route['default_controller'] = 'pages/view';
$route['(:any)'] = 'pages/view/$1';

$route['404_override'] = 'pages/view_404';
$route['translate_uri_dashes'] = FALSE;

