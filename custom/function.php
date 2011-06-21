<?php

function pastebin_string($str){
	if(function_exists(curl_init)){
		$ch = curl_init("http://pastebin.com/api_public.php");
		$str = urlencode($str);
		curl_setopt ($ch, CURLOPT_POST, true);
		curl_setopt ($ch, CURLOPT_POSTFIELDS, "paste_code=$str");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_NOBODY, 0);
		$response = curl_exec($ch);
		return $response;
	} else { 
		return "Curl not installed";
	}
}

?>