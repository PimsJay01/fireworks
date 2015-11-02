<?php 
/* ------------------------------------------------------------------------- */
/* - Nom         : Sheet
/* - Type        : Pages
/* - Description : Feuille de match pour l'équipe
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
$team = (isset($_GET['team_id'])) ? $_GET['team_id'] : $session->get_team();

addTop('Feuille de match', 'players');
?>

<script>
/* ------------------------------------------------------------------------- */
/* Script jQuery (Quand la page jQueryMobile est chargée...)                 */
/* ------------------------------------------------------------------------- */
$('div:jqmData(role="page")').on('pagebeforeshow', function() {

    <?php
    /* Scripts d'administration -------------------------------------------- */
    if($session->admin()): ?>

    

    <?php endif; ?>
    
});
</script>

<?php
/* ------------------------------------------------------------------------- */
/* Functions PHP pour la page                                                */
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
		$players->select_sheet($team);
		if(!$players->count()) echo '<h1 style="text-align: center;" >Aucun joueur</h1>';
		while($players->next()): ?>
            <?php $icon = false;
            switch($players->state){
                case 'Blessé' : $icon = 'wheelchair'; break;
                case 'Indisponible' : $icon = 'suitcase'; break;
            }
            ?>
			<li data-theme="c" data-icon="<?php echo $icon; ?>" >
			<?php if($icon) echo '<a href="" >'; ?>
                <?php echo '<b>' . $players->licence . ' ' . $players->name . '</b>'; ?>
                <?php if($players->jersey != 0): ?>
                    <div class="ui-li-count"><?php echo $players->jersey; ?></div>
                <?php endif; ?>
            <?php if($icon) echo '</a>'; ?>
			</li>
		<?php endwhile; ?>
    </ul>
    
    <?php 
    /* Elements d'administration ------------------------------------------- */
    if($session->admin()): ?>
    
    
    
    <?php endif; ?>
    
</div>

<?php 
/* ------------------------------------------------------------------------- */
/* Boutons de navigations (Calendrier, Joueurs, Forum, Lougout)              */
/* ------------------------------------------------------------------------- */
addBottom(2);
?> 
