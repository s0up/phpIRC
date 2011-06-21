<?php
class Func{
	function process_message($message){
		global $irc;
		$commands = $irc->get_var('commands');
		foreach($commands as $k=>$v){
			if(function_exists($v['function'].'_global')){
				call_user_func($v['function'].'_global', $message);
			}
		}
	}
}
?>