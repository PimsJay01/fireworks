<?php
class Messages{

    var $id;
	var $forum;
	var $player;
	var $name;
	var $date;
	var $time;
	var $text;
  
    private $result;
	
	function __construct() {}
	
	public function select_forum($forum) {
		// Sécurité contre le piratage (valeur numérique attendue)
		$forum = escapeInt($forum);
		//construction de la requête
		$bdd = new Bdd();
		$bdd->set_select($bdd->dot('messages','id'));
		$bdd->add_select('forum');
		$bdd->add_select('player');
		$bdd->add_select('name');
		$bdd->add_select('date');
		$bdd->add_select('time');
		$bdd->add_select('text');
		$bdd->set_from('messages');
		$bdd->add_from('player','id','messages','player');
		$bdd->set_where($bdd->equal('forum',$forum));
		$bdd->add_where($bdd->equal($bdd->dot('messages','hide'),0));
		$bdd->set_order($bdd->dot('messages','id'));
		//récupération des données
		$this->result = $bdd->select();
		// Mise à jour des messages vues
		
	}
    
    public function select_message($forum, $id) {
        // Sécurité contre le piratage (valeur numérique attendue)
        $forum = escapeInt($forum);
        $id = escapeInt($id);
        //construction de la requête
        $bdd = new Bdd();
        $bdd->set_select($bdd->dot('messages','id'));
        $bdd->add_select('forum');
        $bdd->add_select('player');
        $bdd->add_select('name');
        $bdd->add_select('date');
        $bdd->add_select('time');
        $bdd->add_select('text');
        $bdd->set_from('messages');
        $bdd->add_from('player','id','messages','player');
        $bdd->set_where($bdd->equal('forum',$forum));
        $bdd->add_where($bdd->greater($bdd->dot('messages','id'),$id+1));
        $bdd->add_where($bdd->equal($bdd->dot('messages','hide'),0));
        $bdd->set_order($bdd->dot('messages','id'));
        //récupération des données
        $this->result = $bdd->select();
    }
    
    public function insert_message($id, $player, $date, $time, $text) {
        // Sécurité contre le piratage (valeur numérique attendue)
        $id = escapeInt($id);
        $player = escapeInt($player);
        $date = escapeDate($date);
        $time = escapeTime($time);
        $text = escapeText($text);
        //construction de la requête
        $bdd = new Bdd();
        $bdd->set_value('forum',$id);
        $bdd->add_value('player',$player);
        $bdd->add_value('date',$date);
        $bdd->add_value('time',$time);
        $bdd->add_value('text',$text);
        //récupération des données
        return $bdd->insert('messages');
    }
    
    public function delete($id, $player) {
        // Sécurité contre le piratage (valeur numérique attendue)
        $id = escapeInt($id);
        $player = escapeInt($player);
        //construction de la requête
        $bdd = new Bdd();
        $bdd->set_value('hide',$player);
        $bdd->set_where($bdd->equal('messages',$id));
        $bdd->add_where($bdd->equal('hide',0));
        $bdd->update('messages');
    }
	
	public function count() {	
		return mysqli_num_rows($this->result);
	}
	
	public function next() {
		if($row = $this->result->fetch_object()) {
			
            $this->id = $row->id;
            $this->forum = $row->forum;
            $this->player = $row->player;
            $this->name = stripslashes($row->name);
            $this->date = $row->date;
            $this->time = $row->time;
            $this->text = stripslashes($row->text);
        
            return true;
		}
		
		return false;
	}
	
	public function last() {
		$this->result->data_seek($this->count() - 1);
		if($row = $this->result->fetch_object()) {
			
            $this->id = $row->id;
            $this->forum = $row->forum;
            $this->player = $row->player;
            $this->name = stripslashes($row->name);
            $this->date = $row->date;
            $this->time = $row->time;
            $this->text = stripslashes($row->text);
        
            return true;
		}
		
		return false;
	}
}
?>