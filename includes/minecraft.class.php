<?php
require_once("minecraft.xpaw.class.php");

class Minecraft {
	/*
	 * Class by Aidan Taylor
	 * Site: www.aidantaylor.net
	 * Utilizes XPAW's minecraft UDP script (see bottom of file)
	 */
	
    public function __construct($ip, $port = 25565, $timeout = 2, $udp = false, $udp_port = 25565) {
		$this->ip = $ip;//set ip
		$this->port = $port;//set port
		$this->udp = $udp;//set udp
		$this->udp_port = $udp_port;//set udp port
		$this->timeout = $timeout;//set connectiont timeout (seconds)
		
		if ($data = $this->connect($this->ip, $this->port, $this->timeout, $this->udp, $this->udp_port)) {//attempt server connection
			$this->offline = false;//connection successful, set offline to false
			
			if ($this->udp === false) {
				$this->formatData($data);//format data if a tcp request was done
			} else {
				foreach($this->formatted as $key => $value) {
					$this->{$key} = $value;//format each array element if a udp query was done
				}
			}
        } else {
			$this->offline = true;//connection unseccessful, set offline to true
		}
    }
	
	public function GetPlayers() {
		if ($this->udp && isset($this->Query)) {
			return $this->Query->GetPlayers();//return players only if udp was done (tcp does not support this)
		}
		
		return false;
	}
	
	public function connect17($ip, $port = 25565, $timeout = 2, $udp = false, $udp_port = 25565) {
		$this->udp = false;//set udp to false
		$this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);//create tcp socket
		
		socket_set_option($this->socket, SOL_SOCKET, SO_SNDTIMEO, array(//set socket timeout (SO_SNDTIMEO)
			'sec' => (int) $timeout,
			'usec' => 0
		));
		
		socket_set_option($this->socket, SOL_SOCKET, SO_RCVTIMEO, array(//set  socket timeout (SO_RCVTIMEO)
			'sec' => (int) $timeout,
			'usec' => 0
		));
				
		if ($this->socket === false || @socket_connect($this->socket, $ip, $port) === false) {//attempt connection
			$this->data = "Server Offline!";//failed to connect, set data to server offline
			return false;
		}
		
		$length = strlen($ip);
		$data = pack('cccca*', hexdec($length), 0, 0x04, $length, $ip) . pack('nc', $port, 0x01);
		socket_send($this->socket, $data, strlen($data), 0); //send the handshake to the server
		socket_send($this->socket, "\x01\x00", 2, 0);//send request to get server status
		
		if($this->readVarInt() < 10) {
			return false;
		}
		
		socket_read($this->socket, 1);//This will be equal to 0
		
		$len = $this->readVarInt();//read json string length
		$received = @socket_read($this->socket, $len, PHP_NORMAL_READ);//read socket
		socket_close($this->socket);//close the socket
		
		$this->data = json_decode($received, true);//set data to the return
		
		return $this->data;//give back data
	}
	
	public function connect($ip, $port = 25565, $timeout = 2, $udp = false, $udp_port = 25565) {
		if ($udp === true) { //detect udp
			$this->udp = true;//set global udp to true
			$Query = new MinecraftQuery();//create new udp class instance
			
			try {
				$Query->Connect($ip, $udp_port, $timeout);//attempt to connect to server via udp
			} catch(MinecraftQueryException $e) {
				$this->offline = true;//connection unseccessful, set offline to true
				$this->error = $e->getMessage();//failed to connect, set error to the xpaw message
				die($this->error);
				return false;
			}
			
			$this->data = $Query->GetInfo();//query sucessful, set data
			$this->formatted = $Query->GetInfo();//set formatted information
			$this->Query = $Query;//save the class for later use or reference
			
			return $this->data;//return data
		} else {
			$this->data = $this->connect17($ip, $port = 25565, $timeout = 2, $udp = false, $udp_port = 25565);
			
			if ($this->data !== false) {
				$this->data17 = true;//let the script know its json data
				return $this->data;//give back data
			} else {
				$this->udp = false;//set udp to false
				$this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);//create tcp socket
				
				socket_set_option($this->socket, SOL_SOCKET, SO_SNDTIMEO, array(//set socket timeout (SO_SNDTIMEO)
					'sec' => (int) $timeout,
					'usec' => 0
				));
				socket_set_option($this->socket, SOL_SOCKET, SO_RCVTIMEO, array(//set  socket timeout (SO_RCVTIMEO)
					'sec' => (int) $timeout,
					'usec' => 0
				));
				
				if ($this->socket === false || @socket_connect($this->socket, $ip, $port) === false) {//attempt connection
					$this->data = "Server Offline!";//failed to connect, set data to server offline
					return false;
				}
				
				socket_send($this->socket, "\xFE\x01", 2, 0);//send request to get server status
				$len = @socket_recv($this->socket, $data, 512, 0);//read the socket for the return
				socket_close($this->socket);//close the socket
				
				if ($len < 4 || $data[0] !== "\xFF") {//check for valid data
					return false;
				}
				
				$data = substr($data, 3); // Strip packet header (kick message packet and short length)
				$data = iconv('UTF-16BE', 'UTF-8', $data);//encode the data
				
				$this->data = $data;//set data to the return
				
				return $this->data;//give back data
			}
		}
	}
	
	public function formatData($data) {
		if (isset($this->data17) && $this->data17 === true) {
			$array = array(//set the data into an array thats readable
				'Protocol' => $data['version']['protocol'],
				'Version' => $data['version']['name'],
				'HostName' => $data['description'],
				'Players' => $data['players']['online'],
				'MaxPlayers' => $data['players']['max'],
				'favicon' => $data['favicon']
			);
		} else {
			if ($data[1] === "\xA7" && $data[2] === "\x31") {//detect server 1.3<
				$data = explode("\x00", $data);//turn data into array
				
				$array = array(//set the data into an array thats readable
					'Protocol' => IntVal($data[1]),
					'Version' => $data[2],
					'HostName' => $data[3],
					'Players' => IntVal($data[4]),
					'MaxPlayers' => IntVal($data[5])
				);
			} else {
				$data = explode("\xA7", $data);//turn data into array
				
				$array = array(//set the data into an array thats readable
					'Protocol' => 0,
					'Version' => '1.3',
					'HostName' => SubStr($data[0], 0, -1),
					'MaxPlayers' => isset($data[2]) ? IntVal($data[2]) : 0,
					'Players' => isset($data[1]) ? IntVal($data[1]) : 0
				);
			}
		}
			
		$array = $this->formatColour($array);//format the colour of the array (adds HTML colours)
		
		$this->formatted = $array;//set the formatted array
		
		foreach($this->formatted as $key => $value) {//loop through formatting
			if (is_array($value)) {//detect array
				$this->{$key} = (object) $value;//if array, set the class value to an object
			} else {
				$this->{$key} =  ucfirst(strtolower($value));//capitalize the first letter of each word in the value and set class value
			}
		}
		
		return $array;//return the array
	}
	
	public function formatColour($formatted, $colourHTML = false) {
		if (is_array($formatted)) {//detect array
			foreach ($formatted as $key => $value) {
				$array[$key] = $this->formatColour($value);//loop array and format colour for each value
			}
			
			$this->raw = $formatted;//set the raw data (unformatted)
			$this->formatted = $array;//set the formatted data
			return $array;//return array
		} elseif (strpos($formatted, "\xA7")) {//detect valid colour data
			$string = explode("\xA7", str_replace("\xc2", "", $formatted));//turn each coloured section into an array element
			$return = array();//set the return to an array
			
			foreach ($string as $key => $value) {//loop string
				$rest = substr($value, 1);//define the string (no colour code)
				$first = substr($value, 0, 1);//define which colour the string is
				
				if ($colourHTML === true) {
					$return[$key] = $this->addColourHTML($rest, $first);//add the html colour to the string (if colourHTML is true) to return
				} else {
					$return[$key] = $rest;//add the string to return
				}
			}
			
			return implode("", $return);//implode the array into a string
		} else {
			return str_replace("\xc2", "", $formatted);//replace anonymous characters and return
		}
	}
	
	public function addColourHTML($string, $colour) {
		$colours = array(0 => "#000000", 1 => "#0000AA", 2 => "#00AA00", 3 => "#00AAAA", 4 => "#AA0000", 5 => "#AA00AA", 6 => "#FFAA00", 7 => "#AAAAAA", 8 => "#555555", 9 => "#5555FF",
					"a" => "#55FF55", "b" => "#55FFFF", "c" => "#FF55555", "d" => "#FF55FF", "e" => "#FFFF55", "f" => "#FFFFFF", );//define minecraft colours
		$colour = $colours[$colour];//set the hex colour
		
		return "<span style=\"color:{$colour};\">{$string}</span>";//return html
	}
	
	//Function wrote by XPaw - https://github.com/xPaw/
	private function readVarInt() {
		$i = 0;
		$j = 0;
		
		while(true) {
			$k = @socket_read($this->socket, 1);
			
			if($k === false) {
				return 0;
			}
			
			$k = ord($k);
			
			$i |= ($k & 0x7F) << $j++ * 7;
			
			if($j > 5) {
				return false;
			}
			
			if(($k & 0x80) != 128) {
				break;
			}
		}
		
		return $i;
	}
}
?>