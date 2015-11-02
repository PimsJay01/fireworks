<?php 
/* ------------------------------------------------------------------------- */
/* - Nom         : Mail
/* - Type        : Tâche
/* - Description : Envoi des mails pour informer les joueurs des derniers
                   évenements et messages sur le site.
/* - Auteur      : Jérémy Gobet
/* ------------------------------------------------------------------------- */

global $session;
/* ------------------------------------------------------------------------- */
/* Chargement des classes pour l'utilisation de la base de données           */
/* ------------------------------------------------------------------------- */
addBddClasses(array('player'));

$test = ':-O ???';

$headers = "From: notification@fireworks-bbc.ch\r\n";
$headers .= "Reply-To: comite@fireworks-bbc.ch\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html; charset=UTF-8\r\n";

$players = new Player();
// $players->select_team(0);
$players->select(1); // DEV

// while($players->next()) {
if($players->next()) { // DEV
  // Si un mail peut-être envoyé par rapport à la période du dernier envoyé
  if(mailCanBeSend($players)){
    $subject = "Les évènements et les messages que tu as raté!";
    $message = "<html><body>";
    $message .= mailEvents($players);
    $message .= mailMessages($players);
    $message .= "</body></html>";
    mail($players->mail, $subject, $message, $headers);
  }
  else {
    if(mailForAllMessages($players)) {
      $subject = "Il y a du nouveau sur le forum!";
      $message = "<html><body>";
      $message .= mailMessages($players);
      $message .= "</body></html>";
      mail($players->mail, $subject, $message, $headers);
    }
  }
}

/* ------------------------------------------------------------------------- */
/* Functions PHP poour la page                                               */
/* ------------------------------------------------------------------------- */
function mailCanBeSend($player) {
  // Si le joueur souhaite recevoir des mails de notifications
  if($player->event_mail > 0) {
  
    $today = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
    
    // Si nous somme samedi midi
//     if((date("w",$today) == '6') AND (date('G',$today) == '12')) {

      if($player->last_mail == '0000-00-00')
        return true;

      $d = substr($player->last_mail,8,2);
      $m = substr($player->last_mail,5,2);
      $y = substr($player->last_mail,0,4);
      
      $last = mktime(0, 0, 0, $m, $d, $y);
      
      $diff = $last - $today;
      // Si le dernier message date de plus de 7 jours
      if(intval($diff / 86400) >= 7)
        return true;
//     }  
  }
  
  return false;
}

function mailForAllMessages($player) {
  // Si le joueur souhaite recevoir des mails de notifications des nouveaux messages chaque heures
  if($player->messages_mail > 1) {
    return true;
  }
  return false;
}

function mailEvents($player) {
  
  return "";
}

function mailMessages($player) {
  
  return "";
}
?>

<p><?php echo $test; ?></p>
