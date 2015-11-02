<?php 
/* ------------------------------------------------------------------------- */
/* - Nom         : Player
/* - Type        : Pages
/* - Description : Affiche les informations sur le joueur et permet de 
sélectionner le joueur correspondant à l'utilisateur connecté puis de modifier
les paramètres pour ce joueur.
/* - Auteur      : Jérémy Gobet
/* ------------------------------------------------------------------------- */

global $session;
/* ------------------------------------------------------------------------- */
/* Chargement des classes pour l'utilisation de la base de données           */
/* ------------------------------------------------------------------------- */
addBddClasses(array('player','team','jersey'));

/* ------------------------------------------------------------------------- */
/* Entête de la page avec le titre et le bouton de retour                    */
/* ------------------------------------------------------------------------- */
$player = new Player(); 
$title = false;

// Si la requête est légitime (compte admin)
if($session->admin()) {
    // Si il y a une requête SQL
    if(isset($_GET['team_player']) AND isset($_GET['name_player']) AND isset($_GET['phone_player']) AND isset($_GET['mail_player']) AND isset($_GET['adresse_player']) AND isset($_GET['licence_player']) AND isset($_GET['state_player']) AND isset($_GET['jersey_player'])) {
        // Si c'est une modification d'un joueur
        if(isset($_GET['player_id'])) {
            $player->update(
                $_GET['player_id'],
                $_GET['team_player'],
                $_GET['name_player'],
                $_GET['phone_player'],
                $_GET['mail_player'],
                $_GET['adresse_player'],
                $_GET['licence_player'],
                $_GET['state_player'],
                $_GET['jersey_player']
            );
        }
        else {
            $_GET['player_id'] = $player->insert(
                $_GET['team_player'],
                $_GET['name_player'],
                $_GET['phone_player'],
                $_GET['mail_player'],
                $_GET['adresse_player'],
                $_GET['licence_player'],
                $_GET['state_player'],
                $_GET['jersey_player']
            );
        }
    }  
}
        
if(isset($_GET['player_id'])) {
	$player->select($_GET['player_id']);
	if($player->next()) {
		$title = $player->name;
	}
}

addTop($title, 'players');
?>

<script>
/* ------------------------------------------------------------------------- */
/* Script jQuery (Quand la page jQueryMobile est chargée...)                 */
/* ------------------------------------------------------------------------- */
$('div:jqmData(role="page")').on('pagebeforeshow', function() {

    <?php if(($session->get_player() == $_GET['player_id']) OR $session->admin()): ?>

    $('#btnedit-player').on('click', function() {
        changePage('player',{
            player_id: <?php echo $_GET['player_id']; ?>,
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

	$('#me-player').on('click', function() {
		changePage('players',{
			set_player_id: $(this).attr('data-player'),
            set_team_id: $(this).attr('data-team')
		});
	});
	
	$('#notme-player').on('click', function() {
		changePage('identification');
	});

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

function googlemap($adresse) {
    $adresse = str_replace(' ','+',$adresse);
    return 'https://maps.google.com/maps?q='. $adresse . '&hl=fr&ie=UTF8';
}
?>

<?php if(isset($title)):
/* ------------------------------------------------------------------------- */
/* Eléments graphiques jQuery Mobile qui composent la page                   */
/* ------------------------------------------------------------------------- */
?>
<div data-role="content">
	<table width="100%" >
		<tr>
            <td width="25%" >
                <div style="max-width: 200px; ">
                    <img id="logo-player" src="inc/img/<?php echo photo($player->photo); ?>" alt="image" style="width: 100%">
                </div>
            </td>
            <td valign="top">
                <table width="100%" >
                    <tr>
                        <td rowspan="4" width="8px" ></td>
                        <td valign="top" width="16px" >
                            <div class="ui-btn ui-shadow ui-corner-all ui-icon-tag ui-btn-icon-notext" style="margin: 0;" >Tag</div>
                        </td>
                        <td rowspan="4" width="8px" ></td>
                        <td valign="top" >
                            <p style="margin: 5px; white-space: nowrap; " ><?php echo $player->licence; ?></p>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top">
                            <div class="ui-btn ui-shadow ui-corner-all ui-icon-player ui-btn-icon-notext" style="margin: 0;" >Player</div>
                        </td>
                        <td valign="top">
                            <?php if($player->jersey != 0): ?>
                            <p style="margin: 5px; white-space: nowrap; " ><?php echo $player->jersey . ' (' . $player->size . ')'; ?></p>
                            <?php else: ?>
                            <p style="margin: 5px; white-space: nowrap; " >Aucun maillot</p>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top">
                            <div class="ui-btn ui-shadow ui-corner-all ui-icon-phone ui-btn-icon-notext" style="margin: 0;" >Phone</div>
                        </td>
                        <td valign="top">
                            <p style="margin: 5px; white-space: nowrap; " ><?php echo $player->phone; ?></p>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top">
                            <div class="ui-btn ui-shadow ui-corner-all ui-icon-info ui-btn-icon-notext" style="margin: 0;" >Info</div>
                        </td>
                        <td valign="top">
                            <p style="margin: 5px; white-space: nowrap; " ><?php echo $player->state; ?></p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
		<tr>
			<td colspan="2" valign="top">
				<div data-role="collapsible-set" data-theme="b" data-content-theme="c" data-collapsed-icon="arrow-r" data-expanded-icon="arrow-d">
					<div data-role="collapsible">
						<h3>Informations supplémentaires</h3>
						<ul data-role="listview" data-inset="false" data-theme="c" >
                            <?php if($player->mail != ''): ?>
                                <li data-role="list-divider">Adresse e-mail</li>
                                <li data-icon="mail" ><a href="mailto:<?php echo $player->mail; ?>" style="text-overflow: ellipsis; white-space: normal;" ><?php echo $player->mail; ?></a></li>
                            <?php endif; ?>
                            <?php if($player->adresse != ''): ?>
                                <li data-role="list-divider">Adresse postal</li>
                                <li data-icon="location" ><a href="<?php echo googlemap($player->adresse); ?>" target="_blank" style="text-overflow: ellipsis; white-space: normal;" ><?php echo $player->adresse; ?></a></li>
                            <?php endif; ?>
                        </ul>
					</div>
				</div>
			</td>
		</tr>
	</table>
	
	<?php
	/* Elements d'édition -------------------------------------------------- */
	if($session->get_player() == $player->id): ?>
	
    <ul data-role="listview" data-divider-theme="c" data-inset="true" >
        <li data-role="list-divider" role="heading">
            Paramètres pour le joueur
        </li>
        <li data-theme="c" class="ui-disabled">
            <a id="notif-player" href="" data-transition="fade" >
                <h3 class="ui-li-heading">Notifications par mail</h3>
                <p class="ui-li-desc">Aucune</p>
            </a>
        </li>
        <li data-theme="c" class="ui-disabled">
            <a id="img-player" href="" data-transition="fade" >
                Changer de photo
            </a>
        </li>
        <li data-theme="c" class="ui-disabled">
            <a id="edit-player" href="#popupadmin" data-rel="popup" data-transition="fade" >
                Editer le compte
            </a>
        </li>
        <li data-theme="c">
            <a id="notme-player" href="" data-transition="fade" >
                Changer de compte
            </a>
        </li>
    </ul>
		
	<?php endif; ?>
	
	<?php 
	/* Elements d'administration ------------------------------------------- */
	if(($session->get_player() == $player->id) OR $session->admin()): ?>
	
	<?php 
	$teams = new Team();
	$teams->select();
	
	$jersey = new Jersey();
	$jersey->select_free($player->jersey); ?>
	
    <div data-role="popup" id="popupadmin" data-theme="c" class="ui-corner-all">
        <div style="padding:5px 10px; min-width:210px;">
            <select id="team-player" >
                <option value="0" >Aucune équipe</option>
                <?php while($teams->next()): ?>
                <option value="<?php echo $teams->id; ?>" <?php if($player->team == $teams->id) echo 'selected="selected"'; ?> ><?php echo $teams->name; ?></option>
                <?php endwhile; ?>
            </select>
            <input type="text" id="name-player" value="<?php echo $player->name; ?>" placeholder="Nom Prénom" />
            <input type="number" id="licence-player" pattern="[0-9]{1,4}" value="<?php echo $player->licence; ?>" placeholder="Licence" />
            <select id="jersey-player" >
                <option value="0" >Aucun maillot</option>
                <?php while($jersey->next()): ?>
                <option value="<?php echo $jersey->num; ?>" <?php if($player->jersey == $jersey->num) echo 'selected="selected"'; ?> ><?php echo 'Maillot #' . $jersey->num . ' (' . $jersey->size . ')'; ?></option>
                <?php endwhile; ?>
            </select>
            <input type="tel" id="phone-player" value="<?php echo $player->phone; ?>" placeholder="Numéro de téléphone" />
            <select id="state-player" >
                <option value="0" <?php if($player->state=='Présent') echo 'selected="selected"'; ?> >Présent</option>
                <option value="1" <?php if($player->state=='Blessé') echo 'selected="selected"'; ?> >Blessé</option>
                <option value="2" <?php if($player->state=='Indisponible') echo 'selected="selected"'; ?> >Indisponible</option>
            </select>
            <input type="text" id="mail-player" placeholder="Adresse e-mail" value="<?php echo $player->mail; ?>" />
            <textarea id="adresse-player" placeholder="Adresse postal" ><?php echo $player->adresse; ?></textarea>
            <input type="submit" id="btnedit-player" value="Confirmer" data-theme="b" />
        </div>
    </div>
        
    <?php endif; ?>
    
	<?php
	/* Elements d'édition -------------------------------------------------- */
	if($session->get_player() != $player->id): ?>
	
    <a id="me-player" data-player="<?php echo $player->id; ?>" data-team="<?php echo $player->team; ?>" data-role="button" href="" data-icon="check" data-iconpos="left" data-theme="b" >
        C'est moi
    </a>
		
	<?php endif; ?>
    
</div>
<?php endif; ?>

<?php 
/* ------------------------------------------------------------------------- */
/* Boutons de navigations (Calendrier, Joueurs, Forum, Lougout)              */
/* ------------------------------------------------------------------------- */
addBottom(2);
?> 
