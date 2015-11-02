<?php
class Calendar{

    var $id;
	var $team;
    var $type;
	var $date;
	var $present;
  
    private $result;
	
	function __construct() {}
	
	public function select($start, $end, $team, $player) {
        // Sécurité contre le piratage (valeur numérique attendue)
        $start = escapeDate($start);
        $end = escapeDate($end);
        $team = escapeInt($team);
        $player = escapeInt($player);
		//construction de la requête
		$bdd = new Bdd();
		$bdd->set_select('id');
		$bdd->add_select('team');
		$bdd->add_select('type');
		$bdd->add_select('date');
		$bdd->add_select('present');
		$bdd->set_from('event');
		$bdd->add_left_join('presence',$bdd->and_($bdd->link('event','id','presence','event'),$bdd->equal('player',$player)));
		$bdd->set_where($bdd->greater('date',$start));
		$bdd->add_where($bdd->smaller('date',$end));
		$bdd->add_where($bdd->equal('hide',0));
		$bdd->add_where($bdd->or_($bdd->equal('team',0),$bdd->equal('team',$team)));
		$bdd->set_order('date');
		//récupération des données
		$this->result = $bdd->select();
	}
	
	public function next() {
		
		if($row = $this->result->fetch_object()) {
			$this->id = $row->id;
			$this->team = $row->team;
			$this->type = getColorEvent($row->type);
			$this->date = $row->date;
			$this->present = $row->present;
			
			return true;
		}
		return false;
	}
}
?>