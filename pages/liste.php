<?php 
/* ------------------------------------------------------------------------- */
/* - Nom         : Liste des joueurs
/* - Type        : Pages
/* - Description : ...
/* - Auteur      : Jérémy Gobet
/* ------------------------------------------------------------------------- */

global $session;
/* ------------------------------------------------------------------------- */
/* Chargement des classes pour l'utilisation de la base de données           */
/* ------------------------------------------------------------------------- */
addBddClasses(array('player','jersey','team'));

/* ------------------------------------------------------------------------- */
/* Entête de la page avec le titre et le bouton de retour                    */
/* ------------------------------------------------------------------------- */
$player_id = (isset($_GET['set_player_id'])) ? $_GET['set_player_id'] : $session->get_player(); ?>

<table>
    <tr style="text-align: left;" >
        <th>Nom &amp; Prénom</th>
        <th>Equipe</th>
        <th>Licence</th>
        <th>Maillot</th>
        <th>Téléphone</th>
        <th>Mail</th>
        <th>Adresse</th>
    </tr>
    <?php
    $tab_teams = array();
    $teams = new Team();
    $teams->select();
    while($teams->next()) {
      $tab_teams[$teams->id] = $teams->name;
    }
    
    $players = new Player();
    $players->select_team(-2);
    if(!$players->count()) echo '<tr><td colspan="6" >Aucun joueur</td></tr>';
    while($players->next()): ?>
        <tr>
            <td><?php echo $players->name; ?></td>
            <td><?php echo $tab_teams[$players->team]; ?></td>
            <td><?php echo $players->licence; ?></td>
            <td><?php if($players->jersey): ?>
                <?php echo $players->jersey . ' (' . $players->size . ')'; ?>
            <?php endif; ?></td>
            <td><?php echo $players->phone; ?></td>
            <td><?php echo $players->mail; ?></td>
            <td><?php echo $players->adresse; ?></td>
        </tr>
    <?php endwhile; ?>
</table>