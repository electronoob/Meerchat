<?php
/*
 * Meercat, the bot that takes the hassle out of developing Meerchat IRC Services.
 * 
 */
 

require_once 'Classes/Socket.class.php';
require_once 'Classes/Server.class.php';
//require_once 'Classes/Client.class.php';
require_once 'Classes/Protocol.class.php';
require_once 'Classes/Config.class.php';
require_once 'Classes/MySQL.class.php';
require_once 'Classes/Database.class.php';
require_once 'Classes/Log.class.php';

/* service modules, NickServ etc. */
require_once 'Classes/Module.class.php';

echo "----- Loading modules -----\n";
require_once 'Modules.conf.php';
foreach (Module::list_modules() as $loaded_module){
	echo $loaded_module."\n";
}
echo "-----\n";

// now server is instantiated we need to populate server_instance in protocol class
$server = new Server();
Protocol::set_server_instance ($server);
// get settings from the Server.conf.php file
require_once('Server.conf.php');

// turn on database
Database::init();


// save pid
/* not available always */
/*
$pid = posix_getpid();
file_put_contents('Arial.pid', $pid);
*/
$server->start_server();
