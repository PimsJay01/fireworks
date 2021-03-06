<?php 
/* ------------------------------------------------------------------------- */
/* - Nom         : 
/* - Type        : Pages
/* - Description : 
/* - Auteur      : Jérémy Gobet
/* ------------------------------------------------------------------------- */

global $session;
/* ------------------------------------------------------------------------- */
/* Chargement des classes pour l'utilisation de la base de données           */
/* ------------------------------------------------------------------------- */
// addBddClasse(''); addBddClasses(array('','','',''));

/* ------------------------------------------------------------------------- */
/* Entête de la page avec le titre et le bouton de retour                    */
/* ------------------------------------------------------------------------- */
addTop($title, $back);
?>

<script>
/* ------------------------------------------------------------------------- */
/* Script jQuery (Quand la page jQueryMobile est chargée...)                 */
/* ------------------------------------------------------------------------- */
function def () {
    // Functions
}

$('div:jqmData(role="page")').on('pagebeforeshow', function() {

	// JS Ready
	
	<?php
    /* Scripts d'administration -------------------------------------------- */
    if($session->admin()): ?>
    
    // Admin
    
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

	<!-- Content -->
	
	<?php 
    /* Elements d'administration ------------------------------------------- */
    if($session->admin()): ?>
    
    <!-- Admin -->
    
    <?php endif; ?>
    
</div>

<?php 
/* ------------------------------------------------------------------------- */
/* Boutons de navigations (Calendrier, Joueurs, Forum, Lougout)              */
/* ------------------------------------------------------------------------- */
addBottom();
?> 
