<?php
/* ------------------------------------------------------------------------- */
/* - Nom         : Bottom
/* - Type        : 
/* - Description : Boutons de navigations pour les pages. Elle est composé des
/* 4 boutons suivants : Calendrier, Joueurs, Forum, Lougout.
/* - Auteur      : Jérémy Gobet
/* ------------------------------------------------------------------------- */

// Si la variable n'existe pas
$bottom_btn = isset($bottom_btn) ? $bottom_btn : 0;
?>

<script>
/* ------------------------------------------------------------------------- */
/* Script jQuery (Quand la page jQueryMobile est chargée...)                 */
/* ------------------------------------------------------------------------- */
$('div:jqmData(role="page")').on('pagebeforeshow', function() {

	$('.lien-bottom').on('click', function() {
	
		changePage($(this).attr('id'));
		
	});

});
</script>

<?php 
/* ------------------------------------------------------------------------- */
/* Functions PHP poour la page                                               */
/* ------------------------------------------------------------------------- */
function activeButton($button_id, $bottom_btn) {
	if($button_id == $bottom_btn)
		return 'class="lien-bottom ui-btn-active ui-state-persist"';
	return 'class="lien-bottom"';
}
?>

<?php 
/* ------------------------------------------------------------------------- */
/* Eléments graphiques jQuery Mobile qui composent la barre de navigation    */
/* ------------------------------------------------------------------------- */
?><div id="bottom" data-theme="a" data-role="footer" data-position="fixed">

	<?php if($bottom_keyword): ?>
         <textarea name="textarea" id="keyboard" style="margin: 1px 0px;" data-theme="c" ></textarea>
	<?php endif; ?>
	
	<div id="btn-bottom" data-role="navbar" data-iconpos="top" >
		<ul>
			<li>
				<a id="calendar" href="#" data-transition="fade" data-theme="" data-icon="grid" <?php echo activeButton(1, $bottom_btn); ?> >
					Calendrier
				</a>
			</li>
			<li>
				<a id="players" href="#" data-transition="fade" data-theme="" data-icon="male" <?php echo activeButton(2, $bottom_btn); ?> >
					Joueurs
				</a>
			</li>
			<li>
				<a id="forum" href="#" data-transition="fade" data-theme="" data-icon="comments-o" <?php echo activeButton(3, $bottom_btn); ?> >
					Forum
				</a>
			</li>
			<li>
				<a id="login" href="#" data-transition="fade" data-theme="" data-icon="delete" <?php echo activeButton(4, $bottom_btn); ?> >
					Logout
				</a>
			</li>
		</ul>
	</div>
    
</div>