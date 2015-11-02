<?php
class Connect {

	var $mysqli;

	private $host = "localhost";
	private $user = "fireworksbbc";
	private $bdd = "fireworks";
	
	function __construct() {
	
        global $pass;
        
		/* connection avec MySQL */
		$this->mysqli = new mysqli($this->host,$this->user,$pass,$this->bdd);
	
		if (mysqli_connect_errno($this->mysqli)) {
			echo "Echec de la connexion à MySQL :" . mysqli_connect_error();
			return false;
		}
		
		$this->mysqli->query("SET NAMES UTF8");
	}
	
	public function escape($value) {
		// Stripslashes is gpc on
        if (get_magic_quotes_gpc())
        {
            $value = stripslashes($value);
        }
        // Quote if not a number or a numeric string
        if (!is_numeric($value))
        {
            $value = $this->mysqli->real_escape_string($value);
        }
        return $value;
	}
	
	public function query($query) {
		$result = $this->mysqli->query($query);
		$this->mysqli->close();
		return $result;
	}
	
	public function insert($query) {
		$this->mysqli->query($query);
		$result = $this->mysqli->insert_id;
		$this->mysqli->close();
		return $result;
	}
	
	
}
?>