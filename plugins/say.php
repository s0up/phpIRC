<?php
$irc->function_trigger("!say", "say");
function say($arg, $obj){
	$obj->say($obj->channel, $arg['command']);
	return true;
}
?>