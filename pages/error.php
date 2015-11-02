<?php 
/* ------------------------------------------------------------------------- */
/* - Nom         : Error
/* - Type        : Pages
/* - Description : Page d'erreur 404
/* - Auteur      : Jérémy Gobet
/* ------------------------------------------------------------------------- */

/* ------------------------------------------------------------------------- */
/* Entête de la page avec le titre et le bouton de retour                    */
/* ------------------------------------------------------------------------- */
addTop('Erreur');

/* ------------------------------------------------------------------------- */
/* Eléments graphiques jQuery Mobile qui composent la page                   */
/* ------------------------------------------------------------------------- */
?><div data-role="content">

	<p style="text-align: center;" >La page demandée n'a pas été trouvée.</p>
    
</div>

<?php 
/* ------------------------------------------------------------------------- */
/* Boutons de navigations (Calendrier, Joueurs, Forum, Lougout)              */
/* ------------------------------------------------------------------------- */
addBottom();
?> 