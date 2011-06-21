<?php
class IRC
{
	protected $crl;
	protected $fp;
	protected $host;
	protected $port;
	protected $username;
	protected $password;
	protected $nick;
	protected $localhost;
	protected $fullname;
	public $channel;
	protected $commands;
	protected $chat_commands;
	protected $message_trigger;
	protected $verbose;
	private $err_num;
	private $err_msg;
	function __construct($host, $port, $username, $password, $nick, $channel, $fullname)
	{
		$this->crl = "\r\n";
		$this->host = $host;
		$this->port = $port;
		$this->username = $username;
		$this->password = $password;
		$this->nick = $nick;	
		$this->localhost = 'localhost';
		$this->fullname = $fullname;
		$this->channel = $channel;
		$this->chat_commands = array();
		$this->commands = array();
		$this->message_trigger = false;
		$this->verbose = true;
	}
	function connect(){
		$this->fp = fsockopen($this->host,$this->port, $err_num, $err_msg);
		if(!$this->fp){
			return false;
		} else { 
			return true;
		}
	}
	function set_var($var, $val){
		$this->$var = $val;
	}
	function get_var($var){
		return $this->$var;
	}
	function chat_trigger($trigger_name, $response, $type = "CHANNEL"){
		array_push($this->chat_commands, array('trigger_name' => $trigger_name, 'response' => $response, 'type' => $type));
		return true;
	}
	function function_trigger($trigger_name, $function, $type = "CHANNEL"){
		array_push($this->commands, array('trigger_name' => $trigger_name, 'function' => $function, 'type' => $type));
		return true;
	}
	function send($data){
		fputs($this->fp, $data . $this->crl);
	}
	function say($to, $msg){
		echo "Said " .'PRIVMSG '.$to.' :' . $msg. $this->crl;
		fputs($this->fp,'PRIVMSG '.$to.' :' . $msg. $this->crl);
		return true;
	}
	function nick($nick){
		$this->nick = $nick;
		fputs($this->fp, "NICK ".$nick.$this->crl);
	}
	function leave_channel($channel){
		fputs($this->fp, "PART ".$this->channel.$this->crl);
		return true;
	}
	function join_channel($channel){
		fputs($this->fp, "PART ".$this->channel.$this->crl);
		$this->channel = $channel;
		fputs($this->fp, "JOIN ".$this->channel.$this->crl);
		return true;
	}
	function start($identify = false){
		$Header = 'NICK '.$this->nick . $this->crl;
		$Header .= 'USER '.$this->username.' '.$this->localhost.' '.$this->host.' :'.$this->fullname . $this->crl;
		fputs($this->fp, $Header);
		sleep(2);
		if($identify == true){
			fputs($this->fp, 'PRIVMSG nickserv :identify '.$this->username.' '.$this->password.  $this->crl);
		}
		while (!feof($this->fp)) {
			$response .= fgets($this->fp, 1024);
			//echo $response . $this->crl;
			if(substr($response, 0, 4) == "PING"){
				$tmp = explode("PING :", $response);
				$tmp = trim($tmp[1]);
				fputs($this->fp,'PONG ' . $tmp . $this->crl);
			}
			if(strpos($response, 'You are now identified for')){
				fputs($this->fp, 'JOIN ' . $this->channel . $this->crl);
			}
			$offset = strpos($response, $this->crl);
			$data = substr($response,0,$offset);
			$response = substr($response,$offset+2);
			if (substr($data,0,1) == ':') {
				$offsetA = strpos($data, ' ');
				$dFrom = substr($data,1,$offsetA-1);
				$offsetB = strpos($data, ' :');
				$dCommand = substr($data,$offsetA+1,$offsetB-$offsetA-1);
				$offsetC = strpos($data, '!');
				$dNick = substr($data,1,$offsetC-1);
				$iText = substr($data,$offsetB+2);
				if($this->verbose == true){ echo "$offsetA : $dFrom : $offsetB : $dCommand : $offsetC : $dNick : $iText\n"; } 
				if(substr($dCommand, 0, 7) == "PRIVMSG"){
					$tmp = explode(" ", $dCommand, 2);
					$from = $tmp[1];
					if($from[0] == '#'){
						$type = "CHANNEL";
						$from = $dNick;
					}else{ $type = "PRIVMSG";  $from = $dNick; } 
					$ret = array("type" => $type, "from" => $from, "message" => $iText);
					foreach($this->chat_commands as $k=>$v){
						if(substr($iText, 0, strlen($v['trigger_name'])) == $v['trigger_name']){
							if($v['type'] == 'CHANNEL' && $type == 'CHANNEL'){
								$this->say($this->channel, $v['response']);
							} elseif($v['type'] == 'PRIVMSG' && $type == 'PRIVMSG'){
								$this->say($from, $v['response']);
							}
						}
					}
					foreach($this->commands as $k=>$v){
						if(substr($iText, 0, strlen($v['trigger_name'])) == $v['trigger_name']){
							if($v['type'] == 'CHANNEL'){
								call_user_func($v['function'], array("from" => $from, "type" => $type, "command" => str_replace("$v[trigger_name] ", "", $iText), 'channel' => $this->channel), $this);
							} elseif($v['type'] == 'PRIVMSG'){
								call_user_func($v['function'], array("from" => $from, "type" => $type, "command" => str_replace("$v[trigger_name] ", "", $iText)), $this);
							}
						}
					}
					$ret = array("type" => $type, "from" => $from, "message" => $iText, 'channel' => $this->channel);
					if($this->message_trigger){ call_user_func($this->message_trigger, $ret, $this); } 
				}
			}
		}
	}
}
?>