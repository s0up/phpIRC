<?php
$irc->function_trigger("!join", "joinchannel");
function joinchannel($arg, $obj){
	if($arg['command'][0] == '#'){
		$obj->join_channel($arg['command']);
	}
}
?>