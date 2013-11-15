<?php
/*Created by Aidan Taylor - www.aidantaylor.net*/

class database {
	public static $table_prefix = "";
	
	public function __construct($database) {
		$this->select($database);//select database
	}
	
    public function select($database) {
        if (mysql_select_db($database)) {
            return true;//if database selected return true
        } else {
            return false;//if database not selected return false
        }
    }
    
    public function query($string) {
        if ($q = mysql_query($string)) {
            return $q;//if query successful
        } else {
            die("MYSQL error: " . mysql_error());//if query failed
        }
    }
    
    public function num($query) {
		return mysql_num_rows($query);//return sql rows
    }
    
    public function assoc($query) {
		return mysql_fetch_assoc($query);//return sql assoc
    }
    
    public function arr($query) {
           return mysql_fetch_array($query);//return sql array
    }
	
	public function object($query) {
		return mysql_fetch_object($query);//return sql object
    }
    
    public function escape($query) {
        return mysql_real_escape_string($query);//return sql escaped string
    }
}

class sql {
    public function __construct($host = "localhost", $user = "root", $pass = "", $database = false) {
        if ($this->connect($host, $user, $pass)) {//try to connect to mysql
            if ($database !== false) {//if database needs to be selected then continue
                if (!$this->database = (object) $this->select($database)) {
					die("Failed to select the database {$database}");//display error if failed to select database
				}
            }
        } else {
            die("Failed to connect to mysql @ {$host}");//if failed to connect to mysql, display error
        }
    }
	
	public function select($database) {
		if ($database = new database($database)) {
			return $database;//return the database object if connected
		} else {
			return false;//return false if not connected
		}
	}
    
    public function connect($host = "localhost", $user = "root", $pass = "") {
        if ($this->con = mysql_connect($host, $user, $pass)) {
            return true;//return true if mysql connection was established 
        } else {
            return false;//return false if error
        }
    }
	
	public function encrypt($string){
		$key = "this string has been incredibly encrypted by aidan taylor and no one could ever decrypt this without this very long key also this key is for database";
		
		return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $string, MCRYPT_MODE_CBC, md5(md5($key))));//encrypt string and return
	}
		
	public function decrypt($string){
		$key = "this string has been incredibly encrypted by aidan taylor and no one could ever decrypt this without this very long key also this key is for database";
		
		return rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($string), MCRYPT_MODE_CBC, md5(md5($key))), "\0");//decrypt string and return
	}
    
    public function clean($input, $db = true) {
        if (is_array($input)) {//if input is array
            foreach ($input as $key => $value) {
                $input[$key] = $this->clean($value, $db);//loop array and clean each value
            }
            
            return $input;//return array
        } else {
            $input = trim($input);//remove whitespace such as \t, \n, \r ect.
            $input = htmlentities($input, ENT_COMPAT);//change html tags into entities e.g. &lt;b&gt; is a <b> tag
            
            if ($db == true) {
                $input = database::escape($input);//if for database return escaped string
            }
            
            return $input;//return string
        }
    }
}
?>