<?php
class View{

    var $forum;
	var $messages;
	var $player;
  
    private $result;
	
	function __construct() {}
	
	public function select($forum, $player) {
		// Sécurité contre le piratage (valeur numérique attendue)
		$forum = escapeInt($forum);
		$player = escapeInt($player);
		// Construction de la requête
		$bdd = new Bdd();
		$bdd->set_select('forum');
		$bdd->add_select('messages');
		$bdd->add_select('player');
		$bdd->set_from('view');
		$bdd->set_where($bdd->equal('forum',$forum));
		$bdd->add_where($bdd->equal('player',$player));
		//récupération des données
		$this->result = $bdd->select();
	}
	
	public function update($forum, $messages, $player) {
        // Sécurité contre le piratage (valeur numérique attendue)
        $forum = escapeInt($forum);
        $messages = escapeInt($messages);
        $player = escapeInt($player);
        // Construction de la requête
        $bdd = new Bdd();
        $bdd->set_where($bdd->equal('forum',$forum));
        $bdd->add_where($bdd->equal('player',$player));
        $bdd->delete('view');
        $bdd = new Bdd();
        // Construction de la requête
        $bdd->set_value('forum',$forum);
        $bdd->add_value('messages',$messages);
        $bdd->add_value('player',$player);
        $bdd->insert('view');
	}
	
	public function next() {
		if($row = $this->result->fetch_object()) {
			
			$this->forum = $row->forum;
			$this->messages = $row->messages;
			$this->player = $row->player;
			
			return true;
		}
		
		return false;
	}
}
?>