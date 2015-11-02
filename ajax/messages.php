<?php
include('../functions.php');
addBdd();
addBddClasses(array('messages','player'));

if(isset($_POST)){

    $session = new Session();

    // Get messages
    if(isset($_POST['forum_id']) AND isset($_POST['message_id'])) {
        
        $messages = new Messages();
        $messages->select_message($_POST['forum_id'],$_POST['message_id']);
        
        $msg = array();
        while($messages->next()) {
            $msg[] = array(
                'id' => $messages->id,
                'player' => $messages->player,
                'name' => $messages->name,
                'date' => $messages->date,
                'time' => $messages->time,
                'text' => $messages->text
            );
        }
        
        // Ecriture de l'objet JSON contenant les infos qui vont être renvoyées
        header('Content-type: application/json');  
        if(isset($msg)) {
            echo json_encode(array(
                'statut' => 'ok',
                'messages' => $msg
            ));
        }
        else {
            echo json_encode(array('statut' => 'empty'));
        }
        
        exit(0);
    }
    else
    // Put message
    if(isset($_POST['forum_id']) AND isset($_POST['text'])) {

        $date = date('Y-m-d');
        $time = date('H:i:s');
        
        $text = htmlspecialchars($_POST['text']);
        $text = str_replace(array("\r\n", "\r", "\n"), "<br />", $text);
        $text = preg_replace('/((http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?)/', '<a href="${1}" target="”_blank”">${1}</a> ', $text);
        
        $messages = new Messages();
        $id = $messages->insert_message($_POST['forum_id'],$session->get_player(),$date,$time,$text);
        
        // Ecriture de l'objet JSON contenant les infos qui vont être renvoyées
        header('Content-type: application/json');  
        if(isset($id) AND isset($date) AND isset($time)) {
            echo json_encode(array(
                'statut' => 'ok'/*,
                'id' => $id,
                'date' => $date,
                'time' => $time,
                'text' => $text*/
            ));
        }
        else {
            echo json_encode(array('statut' => 'empty'));
        }
        
        exit(0);
    }
}
?>