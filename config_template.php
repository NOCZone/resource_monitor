<?php

/*
 * https://noczone.com
 * 
 * you can find access and secure key by checking your server on noczone.com
 * 
 */

class config{

    public static $access_key = ''; // your server access key
    public static $secure_key = ''; // your server secure key
    // modules config , add new modules config here
    public static $module = array( // all module names small case
        'cpuram' => array(
            'cores' => 8, // how many cores you have
            'watch' => array('apache2', 'mysqld'), //services to watch [use TOP to find more]
        ),
    );
}
