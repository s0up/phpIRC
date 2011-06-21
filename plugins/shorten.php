<?php
$irc->function_trigger("!shorten", "shorten");
function shorten($arg, $obj){
	$url = file_get_contents("http://i4c.org/api.php?url=".$arg['command']);
	$obj->send("PRIVMSG ".$obj->channel ." :".$url);
}
?>