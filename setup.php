<?php
/*
 * http://noczone.com
 * 
 * License : http://www.mozilla.org/MPL/2.0/
 * 
 * Open this file from the browser to link to setup your server on noczone.com
 * please edit config.php (rename from _config.php to config.php)
 * CURL must be installed in order to be able to complete the setup
 * to install curl for PHP on ubuntu :
 * sudo apt-get install curl libcurl3 libcurl3-dev php5-curl
 * then restart apache :
 * sudo service apache2 restart
 * 
 */
include 'config.php';

$cuUrl = getCurrentURL(); // to get the current url of this script
$cuUrl = str_replace('setup.php', '', $cuUrl); // remove the setup.php
$cuUrl.= str_repeat(' ', 1024); // 
$ch = curl_init();
$data['controller'] = 'setup';
$data['act'] = 'server_resource';
$data['server'] = config::$access_key;
$data['data'] = config::noczone_encrypt($cuUrl);
$data['json'] = date("U");


curl_setopt($ch, CURLOPT_URL, "http://noczone.com/ajax.php");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$server_output = curl_exec($ch);

curl_close($ch);
$respone = json_decode($server_output);
if ($respone[0]->result == '1'){
    echo 'Server has been setup !';
}else{
    echo 'Error setting up the server !<br>Please check config.php file and try again<br>
        For more information please visit this link : <a href="https://noczone.com/?page=static&subject=resource_monitoring">https://noczone.com/?page=static&subject=resource_monitoring</a>';
}
//////////////////////////////////////////
function getCurrentURL() {
    $pageURL = 'http';
    if ($_SERVER["HTTPS"] == "on") {
        $pageURL .= "s";
    }
    $pageURL .= "://";
    if ($_SERVER["SERVER_PORT"] != "80") {
        $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
    } else {
        $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
    }
    return $pageURL;
}
