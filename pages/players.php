<?php 
/* ------------------------------------------------------------------------- */
/* - Nom         : Players
/* - Type        : Pages
/* - Description : Liste de tous les joueurs de l'équipe
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
$team = (isset($_GET['team_id'])) ? $_GET['team_id'] : ((isset($_GET['set_team_id'])) ? $_GET['set_team_id'] : $session->get_team());
$player_id = (isset($_GET['set_player_id'])) ? $_GET['set_player_id'] : $session->get_player();

addTop('Effectif');
?>

<script>
/* ------------------------------------------------------------------------- */
/* Script jQuery (Quand la page jQueryMobile est chargée...)                 */
/* ------------------------------------------------------------------------- */
$('div:jqmData(role="page")').on('pagebeforeshow', function() {

    $('#viewteam-player').on('change', function() {
        changePage('players',{team_id: $('#viewteam-player option:selected').val()});
    });
    
    $('#sheet-player').on('click', function() {
        changePage('sheet',{team_id: $('#viewteam-player option:selected').val()});
    });
    
    $('#export-player').on('click', function() {
        changePage('liste',{team_id: $('#viewteam-player option:selected').val()});
    });

    $('.players-player').on('click', function() {
        changePage('player',{player_id: $(this).attr('id')});
    });

    <?php
    /* Scripts d'administration -------------------------------------------- */
    if($session->admin()): ?>

    $('#btnadd-player').on('click', function() {
        changePage('player',{
            team_player: $('#team-player option:selected').val(),
            name_player: $('#name-player').val(),
            licence_player: $('#licence-player').val(),
            jersey_player: $('#jersey-player option:selected').val(),
            phone_player: $('#phone-player').val(),
            state_player: $('#state-player option:selected').text(),
            mail_player: $('#mail-player').val(),
            adresse_player: $('#adresse-player').val()
        });
    });

    <?php endif; ?>
    
});
</script>

<?php
/* ------------------------------------------------------------------------- */
/* Functions PHP pour la page                                                */
/* ------------------------------------------------------------------------- */
function photo($name) {
	if($name == '') {
		return 'test.bmp';
	}
	return $name;
}
?>

<?php
/* ------------------------------------------------------------------------- */
/* Eléments graphiques jQuery Mobile qui composent la page                   */
/* ------------------------------------------------------------------------- */
?><div data-role="content">

    <?php 
    $teams = new Team();
    $teams->select(); ?>
    <div class="ui-field-contain" >
        <select id="viewteam-player" data-theme="c" >
            <option value="-2" <?php if($team == -2) echo 'selected="selected"'; ?> >Tous les joueurs</option>
            <option value="-1"<?php if($team == -1) echo 'selected="selected"'; ?>  >Joueurs du club</option>
            <option value="0" <?php if($team == 0) echo 'selected="selected"'; ?> >Joueurs sans équipes</option>
            <?php while($teams->next()): ?>
            <option value="<?php echo $teams->id; ?>" <?php if($team == $teams->id) echo 'selected="selected"'; ?> ><?php echo $teams->name; ?></option>
            <?php endwhile; ?>
        </select>
    </div>
    
    <?php if($team == -2): ?>
    <a href="" data-icon="action" id="export-player" data-role="button" data-theme="b" >Exporter les infos</a>
    <?php elseif($team > 0): ?>
    <a href="" data-icon="bars" id="sheet-player" data-role="button" data-theme="b" >Feuille de match</a>
    <?php endif; ?>
    
    <?php if($session->admin()): 
    $players = new Player();
    $players->select_team($team);
    $mails = '';
    while($players->next()){
        if($players->mail != '')
        $mails .= $players->mail . '; ';
    }
    ?>
    <a href="mailto:<?php echo $mails; ?>" data-icon="mail" data-role="button" data-theme="b" >Ecrire un mail</a>
    <?php endif; ?>
    
    <ul data-role="listview" data-theme="c" data-inset="true" >
        <?php
		$players = new Player();
		$players->select_team($team);
		if(!$players->count()) echo '<h1 style="text-align: center;" >Aucun joueur</h1>';
		while($players->next()): ?>
			<li data-theme="c" <?php if($player_id == $players->id) echo 'data-icon="star"'; ?> >
				<a class="players-player" href="" data-transition="slide" id="<?php echo $players->id; ?>" >
					<img src="inc/img/175x250!/<?php echo photo($players->photo); ?>"></img>
					<h3 class="ui-li-heading"><?php echo $players->name; ?></h3>
					<p class="ui-li-desc"><?php echo '#' . $players->licence; if(!empty($players->adresse)) echo ' - ' . $players->adresse; ?></p>
					<?php if($players->jersey != 0): ?>
                        <div class="ui-li-count"><?php echo $players->jersey; ?></div>
                    <?php endif; ?>
				</a>
			</li>
		<?php endwhile; ?>
    </ul>
    
    <?php 
    /* Elements d'administration ------------------------------------------- */
    if($session->admin()): ?>
    
    <?php 
    $teams = new Team();
    $teams->select();
    
    $jersey = new Jersey();
    $jersey->select_free($players->jersey); ?>
    
    <div data-role="popup" id="popupadmin" data-theme="c" class="ui-corner-all">
        <div style="padding:5px 10px; min-width:210px;">
            <select id="team-player" >
                <option value="0" >Aucune équipe</option>
                <?php while($teams->next()): ?>
                 <option value="<?php echo $teams->id; ?>" <?php if($team == $teams->id) echo 'selected="selected"';?> ><?php echo $teams->name; ?></option>
                <?php endwhile; ?>
            </select>
            <input type="text" id="name-player" placeholder="Nom Prénom" />
            <input type="number" id="licence-player" pattern="[0-9]{1,4}" placeholder="Licence" />
            <select id="jersey-player" >
                <option value="0" >Aucun maillot</option>
                <?php while($jersey->next()): ?>
                 <option value="<?php echo $jersey->num; ?>" ><?php echo 'Maillot #' . $jersey->num . ' (' . $jersey->size . ')'; ?></option>
                <?php endwhile; ?>
            </select>
            <input type="tel" id="phone-player" placeholder="Numéro de téléphone" />
            <select id="state-player" >
                <option value="0" >Présent</option>
                <option value="1" >Blessé</option>
                <option value="2" >Indisponible</option>
            </select>
            <input type="text" id="mail-player" placeholder="Adresse e-mail" />
            <textarea id="adresse-player" placeholder="Adresse postal" ></textarea>
            <input type="submit" id="btnadd-player" value="Confirmer" data-theme="b" />
        </div>
    </div>
    
    <?php endif; ?>
    
</div>

<?php 
/* ------------------------------------------------------------------------- */
/* Boutons de navigations (Calendrier, Joueurs, Forum, Lougout)              */
/* ------------------------------------------------------------------------- */
addBottom(2); 
?> 
