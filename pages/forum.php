<?php 
/* ------------------------------------------------------------------------- */
/* - Nom         : Forum
/* - Type        : Pages
/* - Description : 
/* - Auteur      : Jérémy Gobet
/* ------------------------------------------------------------------------- */

global $session;
/* ------------------------------------------------------------------------- */
/* Chargement des classes pour l'utilisation de la base de données           */
/* ------------------------------------------------------------------------- */
addBddClasses(array('forum','messages','view','team'));

/* ------------------------------------------------------------------------- */
/* Entête de la page avec le titre et le bouton de retour                    */
/* ------------------------------------------------------------------------- */
// Suppression d'un évèvement
// Si la requête est légitime (compte admin)
if($session->admin()) {
    // si toute les données sont reçues
    if(isset($_GET['del_forum_id'])) {
        $forum = new Forum();
        $forum->delete($_GET['del_forum_id'],$session->get_player());
    }
}

addTop('Forum');
?>

<script>
/* ------------------------------------------------------------------------- */
/* Script jQuery (Quand la page jQueryMobile est chargée...)                 */
/* ------------------------------------------------------------------------- */
$('div:jqmData(role="page")').on('pagebeforeshow',function(){

	$('.messages-forum').on('click', function() {
		changePage('messages',{forum_id: $(this).attr('id')});
	});
	
	<?php
    /* Scripts d'administration -------------------------------------------- */
    if($session->admin()): ?>

    $('#btnadd-forum').on('click', function() {
        changePage('messages',{
            team_forum: $('#team-forum option:selected').val(),
            name_forum: $('#name-forum').val()
        });
    });

    <?php endif; ?>

});
</script>

<?php
/* ------------------------------------------------------------------------- */
/* Eléments graphiques jQuery Mobile qui composent la page                   */
/* ------------------------------------------------------------------------- */
?><div data-role="content">

	<ul data-role="listview" data-divider-theme="c" data-inset="true">
        <li data-role="list-divider" role="heading">
            Discutions générales
        </li>
		<?php 
		$forum = new Forum();
		$forum->select_forum_team($session->get_team());
		while($forum->next($session->get_player())): ?>
			<li data-theme="c" >
				<a class="messages-forum" data-transition="slide" id="<?php echo $forum->id; ?>" >
					<?php echo $forum->name; ?>
					<?php if($forum->new_messages > 0): ?>
						<div class="ui-li-count"><?php echo $forum->new_messages; ?></div>
					<?php endif; ?>
				</a>
			</li>
		<?php endwhile; ?>
    </ul>
	
	<ul data-role="listview" data-divider-theme="c" data-inset="true">
        <li data-role="list-divider" role="heading">
            Discutions d'évènements
        </li>
		<?php
		$forum->select_event_team($session->get_team());
		while($forum->next($session->get_player())): ?>
			<li data-theme="c" >
				<a class="messages-forum" data-transition="slide" id="<?php echo $forum->id; ?>" >
					<?php echo $forum->name; ?>
					<?php if($forum->new_messages > 0): ?>
						<div class="ui-li-count"><?php echo $forum->new_messages; ?></div>
					<?php endif; ?>
				</a>
			</li>
		<?php endwhile; ?>
    </ul>
    
    <?php 
    /* Elements d'administration ------------------------------------------- */
    if($session->admin()): ?>
    
    <?php 
    $team = new Team(); 
    $team->select();
    ?>
    
    <div data-role="popup" id="popupadmin" data-theme="c" class="ui-corner-all">
        <div style="padding:5px 10px; min-width:210px;">
            <select id="team-forum" >
                <option value="0">Toutes les équipes</option>
                <?php while($team->next()): ?>
                    <option value="<?php echo $team->id; ?>" ><?php echo $team->name; ?></option>
                <?php endwhile; ?>
            </select>
            <input type="text" id="name-forum" placeholder="Nom" />
            <input type="submit" id="btnadd-forum" value="Confirmer" data-theme="b" />
        </div>
    </div>
    
    <?php endif; ?>
    
</div>

<?php
/* ------------------------------------------------------------------------- */
/* Boutons de navigations (Calendrier, Joueurs, Forum, Lougout)              */
/* ------------------------------------------------------------------------- */
addBottom(3);
?>