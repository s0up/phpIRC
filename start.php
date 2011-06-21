<?php
include("core/class/phpIRC.php");
include("core/class/function.php");
include("custom/config.php");
include("custom/function.php");
//load irc object
$irc = new IRC($host, $port, $username, $password, $nick, $channel, $fullname);
$irc->set_var("message_trigger", "message_trigger");
//load core functions
$func = new Func();
//Load user defined plugins
if ($handle = opendir('plugins')) {
    while (false !== ($file = readdir($handle))) {
       if($file != "." & $file != ".."){
	   		require_once("plugins/$file");
	   }
       	
    }
}
//start irc
if($irc->connect()){
	$irc->start(true); //true if you want to identify
} else { 
	echo "Couldn't connect to server\r\n"; die;
}
?>