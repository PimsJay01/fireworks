<?php
class Session{
	
	function __construct() {
        if(!isset($_SESSION['state']) OR !isset($_SESSION['team_id']) OR !isset($_SESSION['player_id'])) {
            $this->createSession();
        }
	}
	
	public function set_player($player) {
        // Sécurité contre le piratage
        $player = (int)$player;
        //construction de la requête
        $bdd = new Bdd();
        $bdd->set_value('player',$player);
        $bdd->set_where($bdd->equal('cookie',$this->getKey()));
        $bdd->update('info');
        // Conserve l'équipe du joueur dans la session
        $_SESSION['player_id'] = $player;
    }
    
    public function get_player() {
        return $_SESSION['player_id'];
    }
    
    public function set_team($team) {
        // Sécurité contre le piratage
        $team = (int)$team;
        //construction de la requête
        $bdd = new Bdd();
        $bdd->set_value('team',$team);
        $bdd->set_where($bdd->equal('cookie',$this->getKey()));
        $bdd->update('info');
        // Conserve l'équipe du joueur dans la session
        $_SESSION['team_id'] = $team;
    }
    
    public function get_team() {
        return $_SESSION['team_id'];
    }
    
    public function set_state($state) {
        // Sécurité contre le piratage
        $state = (int)$state;
        //construction de la requête
        $bdd = new Bdd();
        $bdd->set_value('state',$state);
        $bdd->set_where($bdd->equal('cookie',$this->getKey()));
        $bdd->update('info');
        // Conserve l'équipe du joueur dans la session
        $_SESSION['state'] = $state;
    }
    
    public function get_state() {
        return $_SESSION['state'];
    }
    
    public function admin() {
        return ($_SESSION['state'] > 1);
    }
	
	public function logout() {
        $this->set_state(0);
	}
	
	/* PRIVATE FUNCTIONS */
	
	private function createSession() {
        $bdd = new Bdd();
        $bdd->set_select('player');
        $bdd->add_select('team');
        $bdd->add_select('state');
        $bdd->set_from('info');
        $bdd->set_where($bdd->equal('cookie',$this->getKey()));
        //récupération des données
        $result = $bdd->select(0,1);
        if($row = $result->fetch_object()) {
            $_SESSION['player_id'] = $row->player;
            $_SESSION['team_id'] = $row->team;
            $_SESSION['state'] = $row->state;
            
            return true;
        }
        return false;
    }
	
	private function getKey() {
        if(!isset($_COOKIE['session_key'])) {
            // Création d'une clé pour sauvegarder la session
            $key = $this->generateUniqueKey();
            $this->saveKey($key);
            return $key;
        }
        return $_COOKIE['session_key'];
	}
	
	private function saveKey($key) {
        $bdd = new Bdd();
        $bdd->set_value('player',0);
        $bdd->add_value('team',0);
        $bdd->add_value('state',0);
        $bdd->add_value('cookie',$key);
        $bdd->insert('info');
        setcookie('session_key',$key,time()+30*24*3600,null,null,false,true);
    }
	
	// Génére un clé unique
    private function generateUniqueKey($length = 128) {
        $key = '';
        do{
            $key = $this->generateRandomString($length);
            $bdd = new Bdd();
            $bdd->set_from('info');
            $bdd->set_where($bdd->equal('cookie',$key));
            //récupération des données
            $result = $bdd->select(0,1);
        }while($result->fetch_object());
        return $key;
    }

    // Gènère une clé aléatoire
    private function generateRandomString($length) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }
}
?>