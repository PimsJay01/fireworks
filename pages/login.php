<?php 
/* ------------------------------------------------------------------------- */
/* - Nom         : Login
/* - Type        : Pages
/* - Description : Page de connexion pour les joueurs de fireworks. L'équipe 
et un mots de passe correct doivent être transmit pour se connecter.
/* - Auteur      : Jérémy Gobet
/* ------------------------------------------------------------------------- */

global $session;
/* ------------------------------------------------------------------------- */
/* Chargement des classes pour l'utilisation de la base de données           */
/* ------------------------------------------------------------------------- */
addBddClasse('team');

/* ------------------------------------------------------------------------- */
/* Entête de la page avec le titre et le bouton de retour                    */
/* ------------------------------------------------------------------------- */
addTop('Connexion');
?>

<script>
/* ------------------------------------------------------------------------- */
/* Script jQuery (Quand la page jQueryMobile est chargée...)                 */
/* ------------------------------------------------------------------------- */
$('div:jqmData(role="page")').on('pagebeforeshow',function(){

	function buttonState(){
		if(($('#fw1-login:checked').length || $('#fw2-login:checked').length) && $('#key-login').val().length)
			$('[type="submit"]').button('enable');
		else
			$('[type="submit"]').button('disable');
	}
	
	buttonState();
	
	$('[type="radio"]').on('change', function() {
		buttonState();
	});
	
	$('[type="password"]').on('keyup', function(event) {
		buttonState();
		// Confirme le formulaire si ENTER est pressé
		if(event.which == 13){
			$('[type="submit"]').click();
		}
	});
	
// 	$('[type="submit"]').on('click', function() {
// 		// Vérifie si une équipe à été sélectionné
// 		if($('input[type=radio]:checked').length && $('#key-login').val().length) {
// 			changePage('calendar',{
// 				team_id: $('.team-login:checked:first').val(),
// 				pass_key: $('#key-login').val()
// 			});
// 		}
// 	});

});
</script>

<?php 
/* ------------------------------------------------------------------------- */
/* Functions PHP poour la page                                               */
/* ------------------------------------------------------------------------- */
function initChecked($team_id) {
    global $session;
	// Si l'équipe du joueur est contenu dans les cookies
	if($session->get_team() == $team_id) {
		// Les checkbox sont initialisés
		return 'checked="checked"';
	}
	return '';
}
?>

<?php 
/* ------------------------------------------------------------------------- */
/* Eléments graphiques jQuery Mobile qui composent la page                   */
/* ------------------------------------------------------------------------- */
?><div data-role="content">

    <div style="width: 100%; text-align: center;" >
        <img id="logo-login" src="inc/img/logo_fireworks.png" alt="image" style="width: 100%; max-width: 583px; margin: auto">
    </div>
    
    <?php // Permet d'atteindre la page demndée après la connexion
    $action = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    if(!isset($_GET['page']) OR ($_GET['page'] == 'login')) {
        if(strpos($action,'?') > 0)
            $action = substr($action,0,strpos($action,'?'));
        $action .= "?page=calendar";
    }
    ?>
    
    <form method="POST" action="<?php echo $action; ?>" enctype="multipart/form-data" >
	<div id="team-login" data-role="fieldcontain" data-theme="b" >
		<fieldset data-role="controlgroup" data-type="vertical" data-theme="b" >
			<legend>
				Sélectionnez votre équipe
			</legend>
			<?php 
			$teams = new Team();
			$teams->select();
			while($teams->next()): ?>
				<input id="fw<?php echo $teams->id; ?>-login" class="team-login" name="team_id" type="radio" value="<?php echo $teams->id; ?>" <?php echo initChecked($teams->id); ?> >
				<label for="fw<?php echo $teams->id; ?>-login" >
					<?php echo $teams->name; ?>
				</label>
			<?php endwhile; ?>
		</fieldset>
	</div>
    
	<div data-role="fieldcontain" data-theme="c" >
		<label for="key-login">
			Mot clé de l'équipe 
			<?php if(isset($_POST['pass_key'])): ?>
				: <font color="red" >Erroné! Ressayez</font>
			<?php endif; ?>
        </label>
		<!-- Ajouter action sur le onEnter --> 
		<input id="key-login" name="pass_key" placeholder="" value="" type="password" data-theme="c" >
	</div>

	<input value="Confirmer" data-theme="b" type="submit" >
	</form>

</div>