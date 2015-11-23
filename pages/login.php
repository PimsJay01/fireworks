<?php 
/* ------------------------------------------------------------------------- */
/* - Nom         : Login
/* - Type        : Pages
/* - Description : Page de connexion pour les joueurs du club. Le mot de passe
/*                 correct doit-être transmis pour la connexion.
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
		if($('#key-login').val().length)
			$('[type="submit"]').button('enable');
		else
			$('[type="submit"]').button('disable');
	}
	
	buttonState();
	
	$('[type="password"]').on('keyup', function(event) {
		buttonState();
		// Confirme le formulaire si ENTER est pressé
		if(event.which == 13){
			$('[type="submit"]').click();
		}
	});
});
</script>

<?php 
/* ------------------------------------------------------------------------- */
/* Functions PHP poour la page                                               */
/* ------------------------------------------------------------------------- */
// function initChecked($team_id) {
//     global $session;
// 	// Si l'équipe du joueur est contenu dans les cookies
// 	if($session->get_team() == $team_id) {
// 		// Les checkbox sont initialisés
// 		return 'checked="checked"';
// 	}
// 	return '';
// }
?>

<?php 
/* ------------------------------------------------------------------------- */
/* Eléments graphiques jQuery Mobile qui composent la page                   */
/* ------------------------------------------------------------------------- */
?><div data-role="content">

    <div style="width: 100%; text-align: center;" >
        <img id="logo-login" src="inc/img/logo_fireworks.png" alt="image" style="width: 100%; max-width: 583px; margin: auto">
    </div>
    
    <?php // Permet d'atteindre la page demandée après la connexion
    $action = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    if(!isset($_GET['page']) OR ($_GET['page'] == 'login')) {
        if(strpos($action,'?') > 0)
            $action = substr($action,0,strpos($action,'?'));
        $action .= "?page=calendar";
    }
    ?>
    
    <form method="POST" action="<?php echo $action; ?>" enctype="multipart/form-data" >
    
        <?php if(isset($_POST['pass_key'])): ?>
            <style>
                .bar-error{
                    background-color: #F00;
                    color: white;
                    text-shadow: none;
                    font-size: 18px;
                    text-align: center;
                    margin-bottom: 25px;
                }
            </style>
            <div class="bar-error ui-bar ui-corner-all">
                <p>Le mot clé fourni est erroné !</p>
            </div>
        <?php endif; ?>
    
        <div data-role="fieldcontain" data-theme="c" >
            <label for="key-login">
                Mot clé de l'équipe
            </label>
            <input id="key-login" name="pass_key" placeholder="" value="" type="password" data-theme="c" >
        </div>
        
        <div data-role="fieldcontain">
            <label for="checkbox-login">
                Rester connecté
            </label>
            <?php if(isset($_POST['checkbox-login'])): ?>
                <input type="checkbox" name="checkbox-login" id="checkbox-login" data-theme="c" checked>
            <?php else: ?>
                <input type="checkbox" name="checkbox-login" id="checkbox-login" data-theme="c" >
            <?php endif; ?>
        </div>

        <input value="Confirmer" data-theme="b" type="submit" >
	</form>

</div>