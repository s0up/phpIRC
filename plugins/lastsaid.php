<?php
$lastsaid = array();
$seen = array();

$irc->function_trigger("!seen", "seen");
function seen($arg, $obj){
	global $seen;
	if(!$seen[strtolower($arg['command'])]){ $obj->say($obj->channel, "I haven't seen ".$arg['command']." around lately..."); return false; }
	$seentime = time_since(time() - $seen[strtolower($arg['command'])]);
	echo $seentime."\r\n";
	$obj->say($obj->channel, $arg['command'] . " was last seen ".$seentime. " ago");
}
$irc->function_trigger("!lastsaid", "lastsaid");
function lastsaid($arg, $obj){
	global $lastsaid;
	global $seen;
	$seentime = time_since(time() - $seen[strtolower($arg['command'])]);
	$obj->say($obj->channel, $arg['command']." last said : ".'"'.str_replace(chr(1), '"', $lastsaid[strtolower($arg['command'])]) . '" ' .$seentime." ago");
}

function lastsaid_global($msg){
	global $lastsaid;
	global $seen;
	$lastsaid[strtolower($msg['from'])] = $msg['message'];
	$seen[strtolower($msg['from'])] = time();
}
function time_since($since) {
    $chunks = array(
        array(60 * 60 * 24 * 365 , 'year'),
        array(60 * 60 * 24 * 30 , 'month'),
        array(60 * 60 * 24 * 7, 'week'),
        array(60 * 60 * 24 , 'day'),
        array(60 * 60 , 'hour'),
        array(60 , 'minute'),
        array(1 , 'second')
    );

    for ($i = 0, $j = count($chunks); $i < $j; $i++) {
        $seconds = $chunks[$i][0];
        $name = $chunks[$i][1];
        if (($count = floor($since / $seconds)) != 0) {
            break;
        }
    }

    $print = ($count == 1) ? '1 '.$name : "$count {$name}s";
    return $print;
}
?>