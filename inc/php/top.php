<?php
/* ------------------------------------------------------------------------- */
/* - Nom         : Top
/* - Type        : 
/* - Description : Entête des pages avec un titre, un bouton de retour
/* optionnel et un bouton d'administration si l'utilisateur est un 
/* administrateur.
/* - Auteur      : Jérémy Gobet
/* ------------------------------------------------------------------------- */
?>

<?php global $session; ?>

<script>
/* ------------------------------------------------------------------------- */
/* Script jQuery (Quand la page jQueryMobile est chargée...)                 */
/* ------------------------------------------------------------------------- */
$('div:jqmData(role="page")').on('pagebeforeshow', function() {

	<?php if($top_back): ?>
		$('#left-top').on('click', function() {
			changePage('<?php echo $top_back; ?>');
		});
	<?php endif; ?>

});
</script>

<?php 
/* ------------------------------------------------------------------------- */
/* Eléments graphiques jQuery Mobile qui composent la barre de navigation    */
/* ------------------------------------------------------------------------- */
?><div id="top" data-theme="a" data-role="header" data-position="fixed">
 	<h1 id="title-top">
        <?php echo $top_title; ?>
    </h1>
	
	<?php if($top_back): ?>
    <a id="left-top" class="ui-btn-left" href="#" data-icon="arrow-l" data-iconpos="notext">Retour</a>
	<?php endif; ?>
	
	<?php if($session->admin()): ?>
    <a id="right-top" class="ui-btn-right" href="#popupadmin" data-rel="popup" data-icon="gear" data-iconpos="notext" data-transition="fade" >Admin</a>
	<?php endif; ?>
</div> 
