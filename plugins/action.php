<?php
$irc->function_trigger("!action", "action");
function action($arg, $obj){
	$obj->send("PRIVMSG ".$obj->channel ." :".chr(1)."ACTION ".$arg['command'].chr(1));
}
?>