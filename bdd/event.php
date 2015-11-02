<?php
class Event{

    var $id;
	var $team;
    var $type;
	var $date;
	var $time;
	var $location;
	var $opponent;
	var $summary;
	
	var $players_yes = array();
	var $players_no = array();
  
    private $result;
	
	function __construct() {}
	
	public function select($id) {
		// Sécurité contre le piratage (valeur numérique attendue)
		$id = escapeInt($id);
		//construction de la requête
		$bdd = new Bdd();
		$bdd->set_from('event');
		$bdd->set_where($bdd->equal('id',$id));
		$bdd->add_where($bdd->equal('hide',0));
		//récupération des données
		$this->result = $bdd->select(0,1);
	}
	
	public function insert($team, $type, $date, $time, $location, $opponent, $summary) {
		// Sécurité contre le piratage
		$team = escapeInt($team);
		$type = escapeInt($type);
		$date = escapeDate($date);
		$time = escapeTime($time);
		$location = escapeText($location);
		$opponent = escapeText($opponent);
		$summary = escapeText($summary);
		//construction de la requête
		$bdd = new Bdd();
		$bdd->set_select('id');
		$bdd->set_from('event');
		$bdd->set_where($bdd->equal('date',$date));
		$bdd->add_where($bdd->or_($bdd->equal('team',0),$bdd->equal('team',$team)));
		$bdd->add_where($bdd->equal('hide',0));
		$result = $bdd->select(0,1);
		// Si il y a déjà un évenement à cette date là
		if($temp = $result->fetch_object()) {
			return $temp->id;
		}
		$bdd = new Bdd();
		$bdd->set_value('team',$team);
		$bdd->add_value('type',$type);
		$bdd->add_value('date',$date);
		$bdd->add_value('time',$time);
		$bdd->add_value('location',$location);
		$bdd->add_value('opponent',$opponent);
		$bdd->add_value('summary',$summary);
		$id = $bdd->insert('event');
		// Ajout d'un forum de discution pour l'évenement
        $bdd = new Bdd();
        $bdd->set_value('event',$id);
        $bdd->add_value('team',$team);
        $bdd->add_value('name',getTypeEvent($type) . ' ' . $date);
        $bdd->insert('forum');
        return $id;
	}
	
	public function update($id, $team, $type, $date, $time, $location, $opponent, $summary) {
        // Sécurité contre le piratage
        $id = escapeInt($id);
        $team = escapeInt($team);
        $type = escapeInt($type);
        $date = escapeDate($date);
        $time = escapeTime($time);
        $location = escapeText($location);
        $opponent = escapeText($opponent);
        $summary = escapeText($summary);
        //construction de la requête
        $bdd = new Bdd();
        $bdd->set_select('id');
        $bdd->set_from('event');
        $bdd->set_where($bdd->equal('date',$date));
        $bdd->add_where($bdd->or_($bdd->equal('team',0),$bdd->equal('team',$team)));
        $bdd->add_where($bdd->not_equal('id',$id));
        $bdd->add_where($bdd->equal('hide',0));
        $result = $bdd->select(0,1);
        // Si il y a déjà un évenement à cette date là
        if($temp = $result->fetch_object()) {
            return $this->id;
        }
        $bdd = new Bdd();
        $bdd->set_value('team',$team);
        $bdd->add_value('type',$type);
        $bdd->add_value('date',$date);
        $bdd->add_value('time',$time);
        $bdd->add_value('location',$location);
        $bdd->add_value('opponent',$opponent);
        $bdd->add_value('summary',$summary);
        $bdd->set_where($bdd->equal('id',$id));
        $bdd->update('event');
        // Maj du forum de discution pour l'évenement
        $bdd = new Bdd();
        $bdd->set_value('team',$team);
        $bdd->add_value('name',getTypeEvent($type) . ' ' . $date);
        $bdd->set_where($bdd->equal('event',$id));
        $bdd->add_where($bdd->equal('hide',0));
        $bdd->update('forum');
        return $id;
    }
	
	public function delete($id, $player) {
        // Sécurité contre le piratage (valeur numérique attendue)
        $id = escapeInt($id);
        $player = escapeInt($player);
        //construction de la requête
        $bdd = new Bdd();
        $bdd->set_value('hide',$player);
        $bdd->set_where($bdd->equal('id',$id));
        $bdd->add_where($bdd->equal('hide',0));
        $bdd->update('event');
        // Suppression du forum de discution pour l'évenement
        $bdd = new Bdd();
        $bdd->set_value('hide',$player);
        $bdd->set_where($bdd->equal('event',$id));
        $bdd->add_where($bdd->equal('hide',0));
        $bdd->update('forum');
    }
	
	public function next() {
		if($row = $this->result->fetch_object()) {
		
			$this->id = $row->id;
			$this->team = $row->team;
			$this->type = getTypeEvent($row->type);
			$this->date = $row->date;
			$this->time = $row->time;
			$this->location = stripslashes($row->location);
			$this->opponent = stripslashes($row->opponent);
			$this->summary = stripslashes($row->summary);

			$presence = new Presence();
			$presence->select($row->id);
			$yes = 0;
			$no = 0;
			while($presence->next()) {
				if($presence->present > 0)
					$this->players_yes[$yes++] = $presence->player;
				else
					$this->players_no[$no++] = $presence->player;
			}
			
			return true;
		}
		
		return false;
	}
}
?>