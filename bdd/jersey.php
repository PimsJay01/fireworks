<?php
class Jersey{

    var $num;
	var $size;
	var $info;
  
    private $result;
	
	function __construct() {}
	
	public function select() {
		// Construction de la requête
		$bdd = new Bdd();
		$bdd->set_from('jersey');
		$bdd->set_order('num');
		//récupération des données
		$this->result = $bdd->select();
	}
	
	public function select_free($jersey) {
        // Sécurité contre le piratage (valeur numérique attendue)
        $jersey = escapeInt($jersey);
        // Construction de la requête
        $bdd = new Bdd();
        $bdd->set_select('num');
        $bdd->add_select('size');
        $bdd->add_select('info');
        $bdd->set_from('jersey');
        $bdd->add_left_join('player',$bdd->link('player','jersey','jersey','num'));
        $bdd->set_where($bdd->or_($bdd->equal_null('id'),$bdd->equal('num',$jersey)));
        $bdd->set_order('num');
        //récupération des données
        $this->result = $bdd->select();
    }
	
	public function next() {
		if($row = $this->result->fetch_object()) {
			
			$this->num = $row->num;
			$this->size = $row->size;
			$this->info = stripslashes($row->info);
			
			return true;
		}
		
		return false;
	}
}
?>