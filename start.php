<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);

//load core
chainload("core");
//load user defined stuff
chainload("custom");

//create object
$irc = new IRC(IRC_HOST, IRC_PORT, IRC_USERNAME, IRC_PASSWORD, IRC_NICK, IRC_CHANNEL, IRC_FULL_NAME);

//load core functions
$func = new Func();

//Load user defined plugins
chainload('plugins');

//chainload method
function chainload($directory){
	global $irc;
	foreach (glob("$directory/*.php") as $filename)
	{
	    require_once($filename);
	}
}

//start irc
if($irc->connect()){
	$irc->start(true); //true if you want to identify
} else { 
	echo "Couldn't connect to server\r\n"; die;
}
?>