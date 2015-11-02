<?php 
/* ------------------------------------------------------------------------- */
/* - Nom         : Identification
/* - Type        : Pages
/* - Description : Permet d'identifier le joueur pendant la pahse de connexion.
Cette information est conecervée dans les cookies.
/* - Auteur      : Jérémy Gobet
/* ------------------------------------------------------------------------- */

global $session;
/* ------------------------------------------------------------------------- */
/* Chargement des classes pour l'utilisation de la base de données           */
/* ------------------------------------------------------------------------- */
addBddClasse('player');

/* ------------------------------------------------------------------------- */
/* Entête de la page avec le titre et le bouton de retour                    */
/* ------------------------------------------------------------------------- */
addTop('Identifie toi');
?>

<script>
/* ------------------------------------------------------------------------- */
/* Script jQuery (Quand la page jQueryMobile est chargée...)                 */
/* ------------------------------------------------------------------------- */
$('div:jqmData(role="page")').on('pagebeforeshow', function() {

	$('.player-identification').on('click', function() {
		changePage('calendar',{
            set_player_id: $(this).attr('data-player'),
            set_team_id: $(this).attr('data-team')
		});
	});

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
    
    <ul data-role="listview" data-divider-theme="b">
		<?php 
		$players = new Player();
		$players->select();
		while($players->next()): ?>
			<li data-theme="c">
				<a class="player-identification" href="" data-transition="slide" data-player="<?php echo $players->id; ?>" data-team="<?php echo $players->team; ?>" >
					<?php echo $players->name; ?>
					<?php if($players->jersey): ?>
                        <div class="ui-li-count"><?php echo $players->jersey; ?></div>
                    <?php endif; ?>
				</a>
			</li>
		<?php endwhile; ?>
    </ul>
    
</div>