<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/* External-API license activation extra response field value
 * usage:
 * {[license_type]} for returning the license type
 * {[client_email]} for returning client's email address
 * {[license_expiry]} for returning license expiry date
 * {[support_expiry]} for returning support expiry date
 * {[updates_expiry]} for returning updates expiry date
 * anything else will be returned as a string
*/
$config['extra_field_response'] = "stuff_here";

/*LicenseBox Configurations End*/