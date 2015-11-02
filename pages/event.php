<?php 
/* ------------------------------------------------------------------------- */
/* - Nom         : Event
/* - Type        : Pages
/* - Description : Description de l'évenement du calendrier. Il permet aussi
/* d'avoir un lien vers un forum associé à l'évenement ainsi qu'un 
/* récapitulatif des joueurs présent à l'évenement.
/* - Auteur      : Jérémy Gobet
/* ------------------------------------------------------------------------- */

global $session;
/* ------------------------------------------------------------------------- */
/* Chargement des classes pour l'utilisation de la base de données           */
/* ------------------------------------------------------------------------- */
addBddClasses(array('team','player','presence','event','forum','messages','view'));

/* ------------------------------------------------------------------------- */
/* Entête de la page avec le titre et le bouton de retour                    */
/* ------------------------------------------------------------------------- */
$event = new Event();
$title = false;

// Ajout ou modification d'un évèvement
// Si la requête est légitime (compte admin)
if($session->admin()) {
	// si toute les données sont reçues
	if(isset($_GET['team_event']) AND isset($_GET['type_event']) AND isset($_GET['date_event']) AND isset($_GET['time_event']) AND isset($_GET['loc_event']) AND isset($_GET['opp_event']) AND isset($_GET['summary_event'])) {
        // Si c'est une modification d'un évènement
        if(isset($_GET['event_id'])) {
            $event->update(
                $_GET['event_id'],
                $_GET['team_event'],
                $_GET['type_event'],
                $_GET['date_event'],
                $_GET['time_event'],
                $_GET['loc_event'],
                $_GET['opp_event'],
                $_GET['summary_event']
            );
        }
        else {
            $_GET['event_id'] = $event->insert(
                $_GET['team_event'],
                $_GET['type_event'],
                $_GET['date_event'],
                $_GET['time_event'],
                $_GET['loc_event'],
                $_GET['opp_event'],
                $_GET['summary_event']
            );
        }
	}
}

if(isset($_GET['event_id'])) {
	// Si il y a un changement à faire dans les présences
	if(isset($_GET['yes_player'])) {
        $presence = new Presence();
		$presence->insert($_GET['event_id'],$_GET['yes_player'],1);
	}
	else
	if(isset($_GET['no_player'])) {
        $presence = new Presence();
		$presence->insert($_GET['event_id'],$_GET['no_player'],0);
	}	
	
	$event->select($_GET['event_id']);
	if($event->next()) {
		$title = $event->type;
	}
}

$player = new Player($session->get_player());

addTop($title,'calendar');
?>

<script>
/* ------------------------------------------------------------------------- */
/* Script jQuery (Quand la page jQueryMobile est chargée...)                 */
/* ------------------------------------------------------------------------- */
$('div:jqmData(role="page")').on('pagebeforeshow', function() {

	$('.messages-event').on('click', function() {
		changePage('messages',{forum_id: $(this).attr('id').substr(6)});
	});
	
	$('.player-event').on('click', function() {
		changePage('player',{player_id: $(this).attr('id').substr(7)});
	});
	
	$('#pos-event').on('click', function() {
		changePage('event',{
			event_id: <?php echo $_GET['event_id']; ?>,
			yes_player: <?php echo $session->get_player(); ?>
		});
	});
	
	$('#neg-event').on('click', function() {
		changePage('event',{
			event_id: <?php echo $_GET['event_id']; ?>,
			no_player: <?php echo $session->get_player(); ?>
		});
	});
	
	<?php
    /* Scripts d'administration -------------------------------------------- */
    if($session->admin()): ?>
    
    // Envoi des données pour l'ajout d'un évenement dans le calendrier
    $('#btnedit-event').on('click', function() {
        // Vérifie que la date et l'heure sont correcte
        if(!isNaN(Date.parse($('#date-event').val())) && $('#time-event').val().match('^([01]?[0-9]|2[0-3]):[0-5][0-9]$')) {
            
            changePage('event',{
                event_id: <?php echo $_GET['event_id']; ?>,
                team_event: $('#team-event').val(),
                type_event: $('#type-event').val(),
                date_event: $('#date-event').val(),
                time_event: $('#time-event').val(),
                loc_event: $('#loc-event').val(),
                opp_event: $('#opp-event').val(),
                summary_event: $('#summary-event').val()
            });
        }
        else{
            alert('La date et/ou l\'heure n\'est/ne sont pas comformes!');
        }
    });
	
    // Envoi des données pour l'ajout d'un évenement dans le calendrier
    $('#btndel-event').on('click', function() {
        changePage('calendar',{
            del_event_id: <?php echo $_GET['event_id']; ?>
        });
    });
    
    <?php endif; ?>
    
});
</script>

<?php
/* ------------------------------------------------------------------------- */
/* Functions PHP poour la page                                               */
/* ------------------------------------------------------------------------- */
function liste($players) {
	$temp = '';
	for($i=0; $i<sizeof($players); $i++) {
        $temp .= '<li id="player_' . $players[$i]->id . '" data-theme="c" >
            <a class="player-event" href="" data-transition="slide" id="player_' . $players[$i]->id . '" >'
				. $players[$i]->name;
		if($players[$i]->jersey != 0)
			$temp .= '<div class="ui-li-count" >' . $players[$i]->jersey . '</div>';
        $temp .= '</a>
        </li>';
	}
	return $temp;
}

function initChecked($players) {
    global $session;
    
	foreach($players as $player) {
		if($player->id == $session->get_player()) {
			return 'checked="checked"';
		}
	}
	return '';
}

function futurEvent($date) {
    return time() < (strtotime($date) + 86400);
}

?> 

<?php if($title):
/* ------------------------------------------------------------------------- */
/* Eléments graphiques jQuery Mobile qui composent la page                   */
/* ------------------------------------------------------------------------- */
?>
<div data-role="content">
	<table width="100%">
		<?php if(!empty($event->opponent)): ?>
		<tr>
			<td valign="top">
				<p><strong>Adversaire</strong></p>
			</td>
			<td valign="top">
				<p><?php echo $event->opponent; ?></p>
			</td>
		</tr>
		<?php endif; ?>
		<tr>
			<td valign="top">
				<p><strong>Date</strong></p>
			</td>
			<td valign="top">
				<p><?php echo $event->date; ?></p>
			</td>
		</tr>
		<tr>
			<td valign="top">
				<p><strong>Heure</strong></p>
			</td>
			<td valign="top">
				<p><?php echo substr($event->time,0,5); ?></p>
			</td>
		</tr>
		<tr>
			<td valign="top">
				<p><strong>Lieu</strong></p>
			</td>
			<td valign="top">
				<p><?php echo $event->location; ?></p>
			</td>
		</tr>
		<?php if(!empty($event->summary)): ?>
		<tr>
			<td colspan="2" valign="top">
				<p><strong>Description</strong></p>
			</td>
		</tr>
		<tr>
			<td colspan="2" valign="top">
				<p><?php echo $event->summary; ?></p>
			</td>
		</tr>
		<?php endif; ?>
    </table>
	
	<ul data-role="listview" data-divider-theme="b" data-inset="true">
        <li id="notif" data-theme="c">
            <?php
            $forum = new Forum();
            $forum->select_event($_GET['event_id']);
            if($forum->next($session->get_player())): ?>
                <a class="messages-event" href="" id="forum_<?php echo $forum->id; ?>" data-transition="slide" >
                <?php 
                $messages = new Messages();
                $messages->select_forum($forum->id);
                if($messages->last()): ?>
                        <h3 class="ui-li-heading">Voir les messages</h3>
                        <p class="ui-li-desc"><b><?php echo $messages->name; ?></b> - <?php echo $messages->text; ?></p>
                        <?php if($forum->new_messages > 0): ?>
                            <div class="ui-li-count"><?php echo $forum->new_messages; ?></div>
                        <?php endif; ?>
                <?php else: ?>
                        <h3 class="ui-li-heading">Ecrire un message</h3>
                <?php endif; 
            endif; ?>
			</a>
		</li>
    </ul>
    
    <?php if(futurEvent($event->date)): ?>
    <fieldset data-role="controlgroup" data-type="vertical" data-theme="b" >
		<input id="pos-event" name="state-event" type="radio" data-icon="check" data-iconpos="left" <?php echo initChecked($event->players_yes); ?> />
		<label for="pos-event" >
			Présent
		</label>
		<input id="neg-event" name="state-event" type="radio" data-icon="delete" data-iconpos="left" <?php echo initChecked($event->players_no); ?> />
		<label for="neg-event" >
			Absent
		</label>
    </fieldset >
    <?php endif; ?>
    
    <ul data-role="listview" data-divider-theme="c" data-inset="true">
        <li data-role="list-divider" role="heading">
            <?php echo sizeof($event->players_yes); ?> Présent<?php echo (sizeof($event->players_yes)>1)? 's' : ''; ?>
        </li>
		<?php echo liste($event->players_yes); ?>
        <li data-role="list-divider" role="heading">
            <?php echo sizeof($event->players_no); ?> Absent<?php echo (sizeof($event->players_no)>1)? 's' : ''; ?>
        </li>
		<?php echo liste($event->players_no); ?>
    </ul>
	
	<?php 
    /* Elements d'administration ------------------------------------------- */
    if($session->admin() AND isset($event)): ?>
	
	<div data-role="popup" id="popupadmin" data-theme="c" class="ui-corner-all">
		<div style="padding:5px 10px; min-width:210px;">
			<select id="team-event" >
				<option value="0" <?php if($event->team == 0) echo 'selected="selected"'; ?> >Toutes les équipes</option>
				<?php $team = new Team(); 
				$team->select();
				while($team->next()): ?>
					<option value="<?php echo $team->id; ?>" <?php if($event->team == $team->id) echo 'selected="selected"'; ?> ><?php echo $team->name; ?></option>
				<?php endwhile; ?>
			</select>
			<select id="type-event" >
                <?php for($i=0; $i<8; $i++): ?>
                    <option value="<?php echo $i; ?>" <?php if($event->type==getTypeEvent($i)) echo 'selected="selected"'; ?> ><?php echo getTypeEvent($i); ?></option>
                <?php endfor; ?>
			</select>
			<input type="date" id="date-event" value="<?php echo $event->date; ?>" />
			<input type="time" id="time-event" value="<?php echo substr($event->time,0,5); ?>" />
			<input type="text" id="loc-event" value="<?php echo $event->location; ?>" placeholder="Salle" />
			<input type="text" id="opp-event" value="<?php echo $event->opponent; ?>" placeholder="Adversaire" />
			<textarea id="summary-event" placeholder="Description" ><?php echo $event->summary; ?></textarea>
			<input type="submit" id="btnedit-event" value="Confirmer" data-theme="b" />
			<input type="submit" id="btndel-event" value="Delete" data-theme="b" />
		</div>
	</div>
	
	<?php endif; ?>
	
</div>
<?php endif; ?>

<?php 
/* ------------------------------------------------------------------------- */
/* Boutons de navigations (Calendrier, Joueurs, Forum, Lougout)              */
/* ------------------------------------------------------------------------- */
addBottom(1);
?> 
