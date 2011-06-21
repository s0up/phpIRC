<?php
$irc->function_trigger("!ninjatweet", "ninjatweet");
function ninjatweet($arg, $obj){
	preg_match("/\<text\>(.+)\<\/text\>/", file_get_contents("http://twitter.com/statuses/user_timeline/291910794.xml"), $res);
	$obj->say($obj->channel, "The last ninjatweet was $res[1]");
}
?>