resource_monitor
================
*version 1.5*
-------------

Resource monitor client for NOC Zone services


This feature gives you the ability to monitor servers resources in real-time.

* CPU and RAM averages 
* Per process CPU and RAM (processes you chose)
* Disk status and usage


To start , you have to :
1. Download Resource Monitor PHPClient `git clone git@github.com:NOCZone/resource_monitor.git`

2. Extract all files on your server, under any directory which is accessible from the browser (http://yourwebsite.com/just_any_directory)
3. Edit the `config.php` file :
* Enter values for the $access_key and $secure_key (found in the servers page)
* Edit how many CPU cores your server have ( 'cores' => 8 ) replace 8 with the total number of cores
* Add the processes you want to monitor in the "watch" array . You can get the correct names by issuing the command `top` in the terminal.
4. Open the setup page (http://yourwebsite.com/the_directoy_with_client/setup.php).

A message saying `Server has been setup !` will appear, if it didn't please feel free to contact us. 


Few Tips:
* Open the setup page using HTTPS for more security.
* All data transmitted between your server and Noczone is encrypted using the access and secure keys (don't lose them).
* The server should run PHP, and have CURL installed
* The script should be able to at least shell_exec TOP and DF commands.
