<?php
class Team{

    var $id;
    var $name;
  
    private $result;
	
	function __construct() {}
	
	public function select($id = 0) {
        // Sécurité contre le piratage (valeur numérique attendue)
        $id = escapeInt($id);
		//construction de la requête
		$bdd = new Bdd();
		$bdd->set_from('team');
		if($id > 0) {
            $bdd->set_where($bdd->equal('id',$id)); 
        }
        $bdd->set_order('id');
		//récupération des données
		$this->result = $bdd->select();
	}
	
	public function next() {
		if($row = $this->result->fetch_object()) {
		
			$this->id = $row->id;
			$this->name = stripslashes($row->name);
			
			return true;
		}
		
		return false;
	}
}
?>