<?php 
/* ------------------------------------------------------------------------- */
/* - Nom         : Subject
/* - Type        : Pages
/* - Description : 
/* - Auteur      : Jérémy Gobet
/* ------------------------------------------------------------------------- */

/* ------------------------------------------------------------------------- */
/* Chargement des classes pour l'utilisation de la base de données           */
/* ------------------------------------------------------------------------- */
addBddClasse('forum');
addBddClasse('player');
addBddClasse('presence');
addBddClasse('event');
addBddClasse('messages');

/* ------------------------------------------------------------------------- */
/* Entête de la page avec le titre et le bouton de retour                    */
/* ------------------------------------------------------------------------- */
$title = 'Sujets';
include('top.php');
?>

<script>
/* ------------------------------------------------------------------------- */
/* Script jQuery (Quand la page jQueryMobile est chargée...)                 */
/* ------------------------------------------------------------------------- */
$('div:jqmData(role="page")').on('pagebeforeshow',function(){

	$('.liens-forum').on('click', function() {
		console.log('test');
		changePage('forum',{forum_id:$(this).attr('id').substring(2)});
	});
	
	$('.liens-messages').on('click', function() {
		changePage('messages',{messages_id: $(this).attr('id').substring(2)});
	});

});
</script>

<?php
/* ------------------------------------------------------------------------- */
/* Eléments graphiques jQuery Mobile qui composent la page                   */
/* ------------------------------------------------------------------------- */
?><div data-role="content">

	<ul data-role="listview" data-divider-theme="b" data-inset="true">
        <li data-role="list-divider" role="heading">
            Discutions générales
        </li>
		<?php 
		$team_id = $_COOKIE['team_id'];
		$forum = new Forum($team_id);
		$forum->select();
		while($forum->next()): ?>
			<li data-theme="c" >
				<a class="liens-forum" href="" data-transition="slide" id="f_<?php echo $forum->id; ?>" >
					<?php echo $forum->name; ?>
					<div class="ui-li-count"><?php echo '99'; ?></div>
				</a>
			</li>
		<?php endwhile; ?>
    </ul>
	
	<ul data-role="listview" data-divider-theme="b" data-inset="true">
        <li data-role="list-divider" role="heading">
            Discutions d'évènements
        </li>
		<?php
		$team_id = $_COOKIE['team_id'];
		$messages = new Messages();
		$messages->select($team_id);
		while($messages->next()): ?>
			<li data-theme="c" >
				<a class="liens-messages" href="" data-transition="slide" id="m_<?php echo $messages->id; ?>" >
					<?php echo $messages->event->type . ' du ' . $messages->event->date; ?>
					<div class="ui-li-count"><?php echo '99'; ?></div>
				</a>
			</li>
		<?php endwhile; ?>
    </ul>
    
</div>

<?php 
/* ------------------------------------------------------------------------- */
/* Boutons de navigations (Calendrier, Joueurs, Forum, Lougout)              */
/* ------------------------------------------------------------------------- */
$button_num = 3;
include('bottom.php'); 
?> 