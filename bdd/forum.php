<?php
class Forum{

    var $id;
    var $event;
	var $team;
    var $name;
    
    var $new_messages;
  
    private $result;
	
	function __construct() {}
	
	private function get_type($type) {
        $types = Array("Entraînement","Championnat","Coupe","Challenge","Repas","Fête","Voyage","Autre");
        return $types[$type];
	}
	
	public function select_forum($forum) {
        // Sécurité contre le piratage (valeur numérique attendue)
        $forum = escapeInt($forum);
        // Construction de la requête
        $bdd = new Bdd();
        $bdd->set_select('id');
        $bdd->add_select('event');
        $bdd->add_select('team');
        $bdd->add_select('name');
        $bdd->set_from('forum');
        $bdd->set_where($bdd->equal('id',$forum));
        $bdd->add_where($bdd->equal('hide',0));
        // Récupération des données
        $this->result = $bdd->select();
    }
	
	public function select_forum_team($team) {
		// Sécurité contre le piratage (valeur numérique attendue)
		$team = escapeInt($team);
		// Construction de la requête
		$bdd = new Bdd();
		$bdd->set_select($bdd->dot('forum','id'));
        $bdd->add_select($bdd->dot('forum','event'));
        $bdd->add_select($bdd->dot('forum','team'));
        $bdd->add_select($bdd->dot('forum','name'));
		$bdd->set_from('forum');
// 		$bdd->add_left_join('messages',$bdd->select_max('date','messages',$bdd->dot('forum','id') . ' = ' . $bdd->dot('messages','forum'),$bdd->desc('date')));
        $bdd->add_left_join('messages',$bdd->select_max($bdd->dot('messages','id'),'messages',$bdd->dot('forum','id') . ' = ' . $bdd->dot('messages','forum')));
		$bdd->set_where($bdd->or_($bdd->equal('team',0),$bdd->equal('team',$team)));
		$bdd->add_where($bdd->equal($bdd->dot('forum','event'),0));
		$bdd->add_where($bdd->equal($bdd->dot('forum','hide'),0));
		$bdd->set_order($bdd->desc($bdd->dot('messages','date')));
		$bdd->add_order($bdd->dot('forum','name'));
		// Récupération des données
		$this->result = $bdd->select();
	}
	
	public function select_event($event) {
        // Sécurité contre le piratage (valeur numérique attendue)
        $event = escapeInt($event);
        // Construction de la requête
        $bdd = new Bdd();
        $bdd->set_select('id');
        $bdd->add_select('event');
        $bdd->add_select('team');
        $bdd->add_select('name');
        $bdd->set_from('forum');
        $bdd->set_where($bdd->equal('event',$event));
        $bdd->add_where($bdd->equal('hide',0));
        // Récupération des données
        $this->result = $bdd->select();
    }
    
    public function select_event_team($team) {
        // Sécurité contre le piratage (valeur numérique attendue)
        $team = escapeInt($team);
        // Construction de la requête
        $bdd = new Bdd();
        $bdd->set_select($bdd->dot('forum','id'));
        $bdd->add_select($bdd->dot('forum','event'));
        $bdd->add_select($bdd->dot('forum','team'));
        $bdd->add_select($bdd->dot('forum','name'));
        $bdd->add_select($bdd->dot('messages','date'));
        $bdd->set_from('forum');
        $bdd->add_from('event','id','forum','event');
        $bdd->add_left_join('messages',$bdd->select_max($bdd->dot('messages','id'),'messages',$bdd->dot('forum','id') . ' = ' . $bdd->dot('messages','forum')));
        $bdd->set_where($bdd->or_($bdd->equal($bdd->dot('forum','team'),0),$bdd->equal($bdd->dot('forum','team'),$team)));
        $bdd->add_where($bdd->not_equal($bdd->dot('forum','event'),0));
        $bdd->add_where($bdd->equal($bdd->dot('forum','hide'),0));
        $bdd->set_order($bdd->desc($bdd->dot('messages','date')));
        $bdd->add_order($bdd->desc($bdd->dot('event','date')));
        // Récupération des données
        $this->result = $bdd->select(0,10);
    }
    
    public function insert($team, $name) {
        // Sécurité contre le piratage (valeur numérique attendue)
        $team = escapeInt($team);
        $name = escapeText($name);
        // Construction de la requête
        $bdd = new Bdd();
        $bdd->set_value('event',0);
        $bdd->add_value('team',$team);
        $bdd->add_value('name',$name);
        return $bdd->insert('forum');
    }
    
    public function update($id, $team, $name) {
        // Sécurité contre le piratage (valeur numérique attendue)
        $id = escapeInt($id);
        $team = escapeInt($team);
        $name = escapeText($name);
        // Construction de la requête
        $bdd = new Bdd();
        $bdd->set_value('event',0);
        $bdd->add_value('team',$team);
        $bdd->add_value('name',$name);
        $bdd->set_where($bdd->equal('id',$id));
        $bdd->add_where($bdd->equal('hide',0));
        $bdd->update('forum');
    }
    
    public function delete($id, $player) {
        // Sécurité contre le piratage (valeur numérique attendue)
        $id = escapeInt($id);
        $player = escapeInt($player);
        // Construction de la requête
        $bdd = new Bdd();
        $bdd->set_value('hide',$player);
        $bdd->set_where($bdd->equal('id',$id));
        $bdd->add_where($bdd->equal('hide',0));
        $bdd->update('forum');
    }
	
	public function next($player) {
        $player = escapeInt($player);
		if($row = $this->result->fetch_object()) {
			
			$this->id = $row->id;
			$this->event = $row->event;
			$this->team = $row->team;
			$this->name = stripslashes($row->name);
			
			$nbr_messages = 0;
			$messages = new Messages();
			$messages->select_forum($this->id);
			$nbr_messages = $messages->count($this->id);
			
			$view = new View();
			$view->select($this->id, $player);
			if($view->next()) {
				$this->new_messages = max(0,$nbr_messages - $view->messages);
			}
			else {
				$this->new_messages = $nbr_messages;
			}
			
			return true;
		}
		
		return false;
	}
	
	public function first() {
        if($row = $this->result->fetch_object()) {
            
            $this->id = $row->id;
            $this->event = $row->event;
            $this->team = $row->team;
            $this->name = stripslashes($row->name);
            
            return true;
        }
        
        return false;
    }
}
?>