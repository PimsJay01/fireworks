<?php 
// Démarrage de la session
session_start();
// Fonctions PHP pour les pages
include('functions.php');
addBdd();
$session = new Session();

// Si il y a une demande de deconnexion
if(isset($_GET['page']) AND ($_GET['page'] == 'login')) {
	$session->logout();
}
else
// Si il y a une tentative de connexion
if(isset($_POST['pass_key']) AND isset($_POST['team_id'])) {
    // Vérifier si l'id correspond à celui d'une équipe de la bdd
    // ...
    // Sauvegarde de l'équipe
    $session->set_team((int)$_POST['team_id']);
    // Sauvegarde l'état de l'utilisateur
    $state = 0;
    if($_POST['pass_key'] == $mdp1) {
        $state = 1;
    }
    if($_POST['pass_key'] == $mdp2) {
        $state = 2;
    }
    $session->set_state($state);
}
else
// Si il a une demande d'identification
if(isset($_GET['set_player_id']) AND isset($_GET['set_team_id'])) {
    // Conserve l'identifiant du joueur dans les cookies
    $session->set_player((int)$_GET['set_player_id']);
    // Conserve l'équipe du joueur dans les cookies
    $session->set_team((int)$_GET['set_team_id']);
}
?>