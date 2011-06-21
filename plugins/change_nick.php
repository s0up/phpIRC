<?php
$irc->function_trigger("!nick", "changenick");
function changenick($arg, $obj){
	echo "Changing nick\n";
	$obj->nick($arg['command']);
	return true;
}
?>