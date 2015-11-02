<?php
class Player {
  
    var $id;
	var $team;
    var $name;
	var $phone;
	var $mail;
	var $adresse;
	var $licence;
	var $state;
	var $jersey;
	var $size;
	var $photo;
	
	private $result;
	
	function __construct() {}
	
	public function select($id = 0) {
		// Sécurité contre le piratage (valeur numérique attendue)
		$id = escapeInt($id);
		//construction de la requête
		$bdd = new Bdd();
		$bdd->set_from('player');
		$bdd->add_left_join('jersey',$bdd->link('jersey','num','player','jersey'));
		$bdd->set_where($bdd->equal('hide',0));
		if($id > 0) {
			$bdd->add_where($bdd->equal('id',$id));	
		}
		$bdd->set_order('name');
		//récupération des données
		$this->result = $bdd->select();
	}
	
	public function select_team($team = 0) {
        // Sécurité contre le piratage (valeur numérique attendue)
        $team = escapeInt($team);
        //construction de la requête
        $bdd = new Bdd();
        $bdd->set_from('player');
        $bdd->add_left_join('jersey',$bdd->link('jersey','num','player','jersey'));
        $bdd->set_where($bdd->equal('hide',0));
        if($team > 0) {
            $bdd->add_where($bdd->equal('team',$team)); 
        }
        $bdd->set_order('name');
        //récupération des données
        $this->result = $bdd->select();
    }
    
    public function select_sheet($team = 0) {
        // Sécurité contre le piratage (valeur numérique attendue)
        $team = escapeInt($team);
        //construction de la requête
        $bdd = new Bdd();
        $bdd->set_from('player');
        $bdd->add_left_join('jersey',$bdd->link('jersey','num','player','jersey'));
        $bdd->set_where($bdd->equal('hide',0));
        if($team > 0) {
            $bdd->add_where($bdd->equal('team',$team)); 
        }
        $bdd->set_order('jersey');
        $bdd->add_order('name');
        //récupération des données
        $this->result = $bdd->select();
    }
	
	public function insert($team, $name, $phone, $mail, $adresse, $licence, $state, $jersey) {
        // Sécurité contre le piratage
        $team = escapeInt($team);
        $name = escapeText($name);
        $phone= escapeText($phone);
        $mail= escapeText($mail);
        $adresse = escapeText(nl2br($adresse));
        $licence = escapeInt($licence);
        $state = escapeText($state);
        $jersey = escapeInt($jersey);
        //construction de la requête
        $bdd = new Bdd();
        $bdd->set_value('team',$team);
        $bdd->add_value('name',$name);
        $bdd->add_value('phone',$phone);
        $bdd->add_value('mail',$mail);
        $bdd->add_value('adresse',$adresse);
        $bdd->add_value('licence',$licence);
        $bdd->add_value('state',$state);
        $bdd->add_value('jersey',$jersey);
        return $bdd->insert('player');
    }
	
	public function update($id, $team, $name, $phone, $mail, $adresse, $licence, $state, $jersey) {
        // Sécurité contre le piratage
        $id = escapeInt($id);
        $team = escapeInt($team);
        $name = escapeText($name);
        $phone= escapeText($phone);
        $mail= escapeText($mail);
        $adresse = escapeText(nl2br($adresse));
        $licence = escapeInt($licence);
        $state = escapeText($state);
        $jersey = escapeInt($jersey);
        //construction de la requête
        $bdd = new Bdd();
        $bdd->set_value('team',$team);
        $bdd->add_value('name',$name);
        $bdd->add_value('phone',$phone);
        $bdd->add_value('mail',$mail);
        $bdd->add_value('adresse',$adresse);
        $bdd->add_value('licence',$licence);
        $bdd->add_value('state',$state);
        $bdd->add_value('jersey',$jersey);
        $bdd->set_where($bdd->equal('id',$id));
        $bdd->add_where($bdd->equal('hide',0));
        $bdd->update('player');
	}
	
	public function delete($id, $player) {
        // Sécurité contre le piratage (valeur numérique attendue)
        $id = escapeInt($id);
        $player = escapeInt($player);
        //construction de la requête
        $bdd = new Bdd();
        $bdd->set_value('hide',$player);
        $bdd->set_where($bdd->equal('player',$id));
        $bdd->add_where($bdd->equal('hide',0));
        $bdd->update('player');
    }
	
	public function count() {	
		return mysqli_num_rows($this->result);
	}
	
	public function next() {
		if($row = $this->result->fetch_object()) {
		
			$this->id = $row->id;
			$this->team = $row->team;
			$this->name = stripslashes($row->name);
			$this->phone = $row->phone;
			$this->mail = $row->mail;
			$this->adresse = stripslashes($row->adresse);
			$this->licence = $row->licence;
			$this->state = stripslashes($row->state);
			$this->jersey = $row->jersey;
			$this->size = stripslashes($row->size);
			$this->photo = stripslashes($row->photo);
			
			return true;
		}
		
		return false;
	}
}
?>