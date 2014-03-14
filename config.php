<?php

/*
 * https://noczone.com
 * 
 * you can find access and secure key by checking your server on noczone.com
 * 
 */

class config {

    public static $access_key = ''; // your server access key
    public static $secure_key = ''; // your server secure key
    // modules config , add new modules config here
    public static $module = array(              // all module names small case
        'cpuram' => array(
                        'cores' => 8,           // how many cores you have
                        'watch' => array('apache2','mysqld'),   //services to watch [use TOP to find more]
                        ),           
    );             

// do not edit anything below
    public static function noczone_encrypt($data) {
        $key = hash('SHA256', static::$secure_key, true);
   	$block = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
        $data.= md5($data);
        $pad = $block - (strlen($data) % $block);
        $data .= str_repeat(chr($pad), $pad);
        srand();
        $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC), MCRYPT_RAND);
        if (strlen($iv_base64 = rtrim(base64_encode($iv), '=')) != 22)
            return false;
        $encrypted = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $data, MCRYPT_MODE_CBC, $iv));
        return $iv_base64 . $encrypted;
    }

    public static function noczone_decrypt($encrypted) {
        $key = hash('SHA256', static::$secure_key, true);
   	$iv = base64_decode(substr($encrypted, 0, 22) . '==');
        $encrypted = substr($encrypted, 22);
        $decrypted = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, base64_decode($encrypted), MCRYPT_MODE_CBC, $iv);
	$pad = ord($decrypted[($len = strlen($decrypted)) - 1]);
        $decrypted = substr($decrypted, 0, strlen($decrypted) - $pad);
        $hash = substr($decrypted, -32);
        $decrypted = substr($decrypted, 0, -32);
        if (md5($decrypted) != $hash)
            return false;
        return $decrypted;
    }

}
