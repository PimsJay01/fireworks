<?php 
/* ------------------------------------------------------------------------- */
/* - Nom         : Index
/* - Type        : Page par defaut
/* - Description :
/* - Auteur      :
/* ------------------------------------------------------------------------- */
include('inc/php/start.php'); ?>
<!DOCTYPE html>
<html lang="fr">
    <?php
    // Controle si l'utilisateur est connecté
    if($session->get_state() == 0) {
        // L'utilisateur n'est pas connecté
        addPage('calendar');
//         addPage('login');
    }
    else
    // Controle si l'identité du joueur est connue
    if($session->get_player() == 0) {
        // L'identité du joueur n'est pas connue
        addPage('identification');
    }
    else
    // Controle si une page a été demandée
    if(!isset($_GET['page'])) {
        // Chargement de la page par defaut
        addPage('calendar'); // calendar
    }
    // Gestion des pages
    else {
        $page = $_GET['page'];
        // On s'assure que les pages demandées existes
        switch($page) {
            case 'identification' : 
            case 'calendar' : 
            case 'event' : 
            case 'players' : 
            case 'player' : 
            case 'photo' : 
            case 'sheet' : 
            case 'forum' : 
            case 'messages' : 
            case 'mail' : 
            case 'test_mail' : 
                addPage($page);
            break;
            case 'liste' : 
                addExport($page);
            break;
            default : 
                addPage('error');
            break;
        }
    }
    ?>
</html>