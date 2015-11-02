<?php
class Presence{

    var $event;
	var $player;
    var $present;
  
    private $result;
	
	function __construct() {}
	
	public function select($event) {
		// Sécurité contre le piratage (valeur numérique attendue)
		$event = escapeInt($event);
		// Construction de la requête
		$bdd = new Bdd();
		$bdd->set_select('event');
		$bdd->add_select('id'); // id player
		$bdd->add_select('present');
		$bdd->set_from('presence');
		$bdd->add_from('player','id','presence','player');
		$bdd->set_where($bdd->equal('event',$event));
		$bdd->set_order('name'); // name player
		// Récupération des données
		$this->result = $bdd->select();
	}
	
	public function insert($event = 0, $player = 0, $present = 0) {
        // Sécurité contre le piratage (valeur numérique attendue)
        $event = escapeInt($event);
        $player = escapeInt($player);
        $present = escapeInt($present);
        // Construction de la requête
        $bdd = new Bdd();
        $bdd->set_where($bdd->equal('event',$event));
        $bdd->add_where($bdd->equal('player',$player));
        $bdd->delete('presence');
        // Construction de la requête
        $bdd = new Bdd();
        $bdd->set_value('event',$event);
        $bdd->add_value('player',$player);
        $bdd->add_value('present',$present);
        $bdd->insert('presence');
    }
	
	public function next() {
		if($row = $this->result->fetch_object()) {
		
			$player = new Player();
			$player->select($row->id);
			if($player->next()) {
			
				$this->event = $row->event;
				$this->player = $player;
				$this->present = $row->present;
				
				return true;
			}
		}
		
		return false;
	}
}
?>