<?php
include('../functions.php');
addBdd();
addBddClasse('view');

if(isset($_POST)){

    $session = new Session();

    if(isset($_POST['forum_id']) AND isset($_POST['messages'])) {
        
        $view = new View();
        $view = $view->update($_POST['forum_id'],$_POST['messages'],$session->get_player());
        
        exit(0);
    }
}
?>