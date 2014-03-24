<?php
/*
/*
 * https://noczone.com
 * License : http://www.mozilla.org/MPL/2.0/
 * 
 * Connection to NocZone.com is encrypted 
 * No one can get your server details unless they knew the 
 * URL to this file, and the Servers Secure Key
 * 
 */
include 'config_template.php';
include 'encryption.php';
// Auto Loader
function autoloader($mod)
{
    $modName = substr($mod, 7);
    require_once 'modules/' . $modName . '/' . $modName . '.php';
}

spl_autoload_register('autoloader');
/////////////////////////////////////
$data = encryption::noczone_decrypt($_POST["data"]);
if ($data === false) {
    exit;
} // if wrong decryption exit !
$remove_chars = array('.', '\\', '/'); // to remove any illegal file paths
$data = str_replace($remove_chars, '', trim($data)) . ','; // add a comma in case only one module was called
//
$modules = explode(',', $data);
$resource_report = array();
foreach ($modules as $module_name) {
    if (trim($module_name) != '') {
        try {
            $mod = 'module_' . strtolower(trim($module_name));
            $tmp_mod = new $mod();
            $resource_report[$module_name] = $tmp_mod->run();
        } catch (Exception $e) {
            // just proceed to next one
        }
    }
}
$output = encryption::noczone_encrypt(json_encode($resource_report) . str_repeat(' ', 128));
echo $output;
