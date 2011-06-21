<?php
$irc->set_var("message_trigger", "newmessage");
function newmessage($arg, $obj){
	global $func;
	$func->process_message($arg);
}
?>