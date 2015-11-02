<?php 
/* ------------------------------------------------------------------------- */
/* - Nom         : Photo
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
addTop('Changement de la photo', 'players');
?>

<script>
/* ------------------------------------------------------------------------- */
/* Script jQuery (Quand la page jQueryMobile est chargée...)                 */
/* ------------------------------------------------------------------------- */
function beforeSubmit () {
    //check whether browser fully supports all File API
   if (window.File && window.FileReader && window.FileList && window.Blob)
    {
        
        if( !$('#file-photo').val()) //check empty input filed
        {
            $("#output-photo").html("Sélectionnez un fichier");
            return false
        }
        
        var fsize = $('#file-photo')[0].files[0].size; //get file size
        var ftype = $('#file-photo')[0].files[0].type; // get file type
        

        //allow only valid image file types 
        switch(ftype)
        {
            case 'image/png': case 'image/gif': case 'image/jpeg': case 'image/pjpeg':
                break;
            default:
                $("#output-photo").html("Le type <b>"+ftype+"</b> n'est pas supporté!");
                return false
        }
        
        //Allowed file size is less than 2MB (2097152)
        if(fsize > 2097152) 
        {
            $("#output-photo").html("La taille du fichier dépasse 2M");
            return false
        }
                
        $('#submit-photo').hide();
        $('#progressbar-photo').show();
        $("#output-photo").html("Téléchargement...");  
    }
    else
    {
        //Output error to older unsupported browsers that doesn't support HTML5 File API
        $("#output-photo").html("Le navigateur doit être mise à jous!");
        return false;
    }
}

function onProgress(event, position, total, percentComplete) {
    $('#progressbar-photo').children('input').val(percentComplete);
}

function afterSuccess() {

}

$('div:jqmData(role="page")').on('pagebeforeshow', function() {

	$('<input>').appendTo('#progressbar-photo').attr({'name':'slider','id':'slider','data-highlight':'true','min':'0','max':'100','value':'50','type':'range'}).slider({
        create: function( event, ui ) {
            $(this).parent().find('input').hide();
            // Fix for some FF versions
            $(this).parent().find('input').css('margin-left','-9999px');
            $(this).parent().find('.ui-slider-track').css('margin','0');
            $(this).parent().find('.ui-slider-handle').hide();
        }
    }).slider("refresh");
    
    var options = { 
        target: '#output-photo', 
        beforeSubmit: beforeSubmit,
        uploadProgress: onProgress, //upload progress callback 
        success: afterSuccess,
        resetForm: true  
    }; 
    
    $('#form-photo').submit(function() { 
        $(this).ajaxSubmit(options);            
        return false; 
    });
	
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

    <form action="ajax/photo.php" method="POST" enctype="multipart/form-data" id="form-photo" onSubmit="return false;" >
        <div class="ui-input-text ui-shadow-inset ui-corner-all ui-btn-shadow ui-body-c" >
            <input type="file" name="image_file" id="file-photo" data-role="none" class="ui-input-text ui-body-c" />
<!--         </span> -->
        </div>
        <input type="submit" value="Confirmer" id="submit-photo" data-theme="b" />
    </form>
    
    <p id="output-photo" ></p>
    
    <div id="progressbar-photo" style="display:none;" ></div>
	
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
