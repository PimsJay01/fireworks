<?php
include('../functions.php');
addBdd();
addBddClasse('calendar');

if(isset($_POST)){

    $session = new Session();

    if(isset($_POST['date_start']) AND isset($_POST['date_end'])) {
        
        $calendar = new Calendar();
        $calendar->select($_POST['date_start'],$_POST['date_end'],$session->get_team(),$session->get_player());
        
        $i = 0;
        while($calendar->next()) {
            $fontColor = 'black';
            if($calendar->present != NULL) {
                if($calendar->present)
                    $fontColor = '#008800';
                else
                    $fontColor = '#CC0000';
            }
            $events[$i] = (object) array('id' => $calendar->id, 'type' => $calendar->type, 'fontColor' => $fontColor, 'date' => $calendar->date);
            $i++;
        }
        
        // Ecriture de l'objet JSON contenant les infos qui vont être renvoyées
        header('Content-type: application/json');  
        if(isset($events)) {
            echo json_encode(array('statut' => 'ok', 'events' => $events));
        }
        else {
            echo json_encode(array('statut' => 'empty', 
				'team' => $session->get_team(),
				'player' => $session->get_player(),
				'i' => $i,
            ));
        }
        
        exit(0);
    }
}
?>