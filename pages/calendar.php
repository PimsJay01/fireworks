<?php 
/* ------------------------------------------------------------------------- */
/* - Nom         : Calendar
/* - Type        : Pages
/* - Description : Affiche un calendrier par mois avec les évènements marqué
/* d'un rond de couleur selon le type. Un clique sur le rond permet d'atteindre
/* la page d'évènements avec ses informations.
/* - Auteur      : Jérémy Gobet
/* ------------------------------------------------------------------------- */

global $session;
/* ------------------------------------------------------------------------- */
/* Chargement des classes pour l'utilisation de la base de données           */
/* ------------------------------------------------------------------------- */
addBddClasses(array('calendar','event','team'));

/* ------------------------------------------------------------------------- */
/* Entête de la page avec le titre et le bouton de retour                    */
/* ------------------------------------------------------------------------- */
// Suppression d'un évèvement
// Si la requête est légitime (compte admin)
if($session->admin()) {
    // si toute les données sont reçues
    if(isset($_GET['del_event_id'])) {
        $event = new Event();
        $event->delete($_GET['del_event_id'],$session->get_player());
    }
}

addTop('Calendrier');
?>

<script>
/* ------------------------------------------------------------------------- */
/* Script jQuery (Quand la page jQueryMobile est chargée...)                 */
/* ------------------------------------------------------------------------- */
$('div:jqmData(role="page")').on('pagebeforeshow', function() {

	var cal = $('#std72-calendar');
	
	// Lorsque le mois affiché sur le calendrier change...
	cal.on('dateChange',function(event, options) {
        showLoading();
		// Requête AJAX pour réccupèrer les évènements du mois en cours de visionnement
		$.ajax({
			type: "POST",
			url : "ajax/calendar.php", 
			data : {date_start: options.start, date_end: options.end},
			dataType : "json"
		})
        // Réponse à la requête AJAX positive
        .done(function(options) {
            if(options.statut == 'ok') {
                // Ajoute chaque évenements dans le calendrier géré par le widget
                $.each(options.events, function(index, event) {
                    cal.calendar('addEvent', {
                        date: event.date,
                        color: event.type,
                        fontColor: event.fontColor,
                        click: function() {
                            changePage('event',{event_id: event.id});
                        }
                    });
                });
            }
            hideLoading();
        })
        // Réponse à la requête AJAX négative
        .fail(function(request, error) { // Info Debuggage si erreur         
            console.log('fail : ' + request.responseText);
        });   
	});
	
	// Création du widget graphique pour la gestion du calendrier avec initialisation des mots en francais
	cal.calendar({
		weekdays: ['Lun','Mar','Mer','Jeu','Ven','Sam','Dim'],
		month: ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Decembre']
	});
	
	$(window).resize(function() {
        cal.calendar('refresh');
	});
	
	<?php
	/* Scripts d'administration -------------------------------------------- */
	if($session->admin()): ?>
	
	// Désactive ou active le champ "adversaire" selon le type d'évènement
	$('#type-calendar').on('change', function() {
		if($(this).val() > 4) {
			$('#adv-calendar').textinput('disable');
		}
		else {
			$('#adv-calendar').textinput('enable');
		}
	});
	
	// Envoi des données pour l'ajout d'un évenement dans le calendrier
	$('#btnadd-calendar').on('click', function() {
		// Vérifie que la date et l'heure sont correcte
		if(!isNaN(Date.parse($('#date-calendar').val())) && $('#time-calendar').val().match('^([01]?[0-9]|2[0-3]):[0-5][0-9]$')) {
			
			changePage('event',{
                team_event: $('#team-calendar').val(),
                type_event: $('#type-calendar').val(),
                date_event: $('#date-calendar').val(),
                time_event: $('#time-calendar').val(),
                loc_event: $('#loc-calendar').val(),
                opp_event: $('#opp-calendar').val(),
                summary_event: $('#summary-calendar').val()
            });
		}
	});
	
	<?php endif; ?>

});
</script>

<?php 
/* ------------------------------------------------------------------------- */
/* Functions PHP poour la page                                               */
/* ------------------------------------------------------------------------- */
?>

<?php 
/* ------------------------------------------------------------------------- */
/* Eléments graphiques jQuery Mobile qui composent la page                   */
/* ------------------------------------------------------------------------- */
?><div data-role="content">

	<div id="std72-calendar" ></div>
	
	<?php 
	/* Elements d'administration ------------------------------------------- */
	if($session->admin()): ?>
	
	<div id="popupadmin" data-role="popup" data-theme="c" class="ui-corner-all">
		<div style="padding:5px 10px; min-width:210px;">
			<select id="team-calendar" >
				<option value="0">Toutes les équipes</option>
				<?php $team = new Team(); 
				$team->select();
				while($team->next()): ?>
					<option value="<?php echo $team->id; ?>"><?php echo $team->name; ?></option>
				<?php endwhile; ?>
			</select>
			<select id="type-calendar" >
				<option value="0">Entrainement</option>
				<option value="1">Championnat</option>
				<option value="2">Coupe</option>
				<option value="3">Challenge</option>
				<option value="4">Repas</option>
				<option value="5">Fete</option>
				<option value="6">Voyage</option>
				<option value="7">Autre</option>
			</select>
			<input type="date" id="date-calendar" value="<?php echo date('Y-m-d'); ?>" />
			<input type="time" id="time-calendar" value="20:00" />
			<input type="text" id="loc-calendar" placeholder="Salle" />
			<input type="text" id="opp-calendar" placeholder="Adversaire" />
			<textarea id="summary-calendar" placeholder="Description" ></textarea>
			<input type="submit" id="btnadd-calendar" value="Confirmer" data-theme="b" />
		</div>
	</div>
	
	<?php endif; ?>
    
</div>

<?php 
/* ------------------------------------------------------------------------- */
/* Boutons de navigations (Calendrier, Joueurs, Forum, Lougout)              */
/* ------------------------------------------------------------------------- */
addBottom(1);
?>