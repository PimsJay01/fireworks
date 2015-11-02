<?php 
/* ------------------------------------------------------------------------- */
/* - Nom         : Subject
/* - Type        : Pages
/* - Description : Permet de consulter les messages sur un sujet ou un 
évenement du calendrier.
/* - Auteur      : Jérémy Gobet
/* ------------------------------------------------------------------------- */

global $session;
/* ------------------------------------------------------------------------- */
/* Chargement des classes pour l'utilisation de la base de données           */
/* ------------------------------------------------------------------------- */
addBddClasses(array('forum','messages','view','team'));

/* ------------------------------------------------------------------------- */
/* Entête de la page avec le titre et le bouton de retour                    */
/* ------------------------------------------------------------------------- */
$forum = new Forum();
$title = false;

// Si la requête est légitime (compte admin)
if($session->admin()) {
    // Si il y a une requête SQL
    if(isset($_GET['team_forum']) AND isset($_GET['name_forum'])) {
        
        // Si c'est une modification d'un forum
        if(isset($_GET['forum_id'])) {
            $forum->update(
                $_GET['forum_id'],
                $_GET['team_forum'],
                $_GET['name_forum']
            );
        }
        else {
            $_GET['forum_id'] = $forum->insert(
                $_GET['team_forum'],
                $_GET['name_forum']
            );
        }
    }
}

if(isset($_GET['forum_id'])) {
    $forum->select_forum($_GET['forum_id']);
    if($forum->next($session->get_player())) {
        $title = $forum->name;
    }
}

addTop($title, 'forum');
?>

<script>
/* ------------------------------------------------------------------------- */
/* Script jQuery (Quand la page jQueryMobile est chargée...)                 */
/* ------------------------------------------------------------------------- */
function checkMessages() {
    var id = 0;
    var main = $('.ui-content[role="main"]');
    var div = main.children('div:last');
    if(div.length) {
        id = div.attr('id');
    }
    
    // Réccupère les derniers messages écrit sur le chat
    $.ajax({
        type: "POST",
        url : "ajax/messages.php", 
        data : {forum_id: <?php echo $_GET['forum_id']; ?>, message_id: id},
        dataType : "json"
    })
    // Réponse à la requête AJAX positive
    .done(function(options) {
        if(options.statut == 'ok') {
            if(options.messages.length) {
                addMessages(main, options.messages);
            }
            else {
                main.children('p:contains("Chargement...")').html('Aucun message');
            }
        }
        hideLoading();
    });
    $('#refresh').css('display','block');
}

function startCheckMessages() {
    return window.setInterval(checkMessages, 10000);
}

// Add messages from bdd
function addMessages(main, messages) {
    main.children('p:contains("Chargement...")').remove();
    main.children('p:contains("Aucun message")').remove();
    
    $.each(messages, function(index, message) {
        // Date
        var last = $('.messages-date:last').html();
        if(last != message.date) {
            $('<p class="messages-date" style="width: 100%; float: left; text-align: center;" >' + message.date + '</p>').appendTo(main);
        }    
        if(message.player == <?php echo $session->get_player(); ?>) {
            addMessageInt(main, message);
        }
        else {
            addMessageExt(main, message)
        }
    });
    majViewMessages(main);
    $('html, body').animate({ scrollTop: 10000 });
};

// Add intern messages
function addMessageInt(main, message) {  
    var box = $('<div id="' + message.id + '" class="ui-body ui-body-b ui-corner-all" style="width: 80%; margin-bottom: 5px; float: right; background-color: #8CC63F;" data-theme="c" ></div>').appendTo(main);
    
    $('<p style="margin: 0px; float: left;" >' + message.text + '</p>').appendTo(box);
    
    $('<p style="margin: 0px; float: right; position: relative; bottom: 0px; right: 0px; font-style: italic;" >' + message.time + '</p>').appendTo(box);
}

// Add extern messages
function addMessageExt(main, message) {
    var box = $('<div id="' + message.id + '" class="ui-body ui-body-b ui-corner-all" style="width: 80%; margin-bottom: 5px; float: left;" data-theme="c" ></div>').appendTo(main);
    
    $('<p style="margin: 0px;" ><b>' + message.name + '</b></p>').appendTo(box);
    
    $('<p style="margin: 0px; float: left;" >' + message.text + '</p>').appendTo(box);
    
    $('<p style="margin: 0px; float: right; position: relative; bottom: 0px; right: 0px; font-style: italic;" >' + message.time + '</p>').appendTo(box);
}

function majViewMessages(main) {
    var messages = main.children('div').length;
    $.ajax({
        type: "POST",
        url : "ajax/view.php", 
        data : {forum_id: <?php echo $_GET['forum_id']; ?>, messages: messages},
        dataType : "json"
    });
}

$('div:jqmData(role="page")').on('pagebeforeshow', function() {

    showLoading();
    
    var clock = startCheckMessages();
    checkMessages();
	
	$('#keyboard').on('keypress', function(event) {
        if(event.which == 13) {
            event.preventDefault();
            var text = $('#keyboard').val();
            if(text != '') {
                var id = 0;
                var div = $('.ui-content[role="main"]').children('div:last');
                if(div.length) {
                    id = div.attr('id');
                }
                window.clearInterval(clock);
                // Requête AJAX pour ajouter le nouveau message
                $.ajax({
                    type: "POST",
                    url : "ajax/messages.php", 
                    data : {forum_id: <?php echo $_GET['forum_id']; ?>, text: text},
                    dataType : "json"
                })
                // Réponse à la requête AJAX positive
                .done(function(options) {
                    if(options.statut == 'ok') {
                        checkMessages();
                    }
                    clock = startCheckMessages();
                })
                // Réponse à la requête AJAX négative
                .fail(function(request, error) { // Info Debuggage si erreur         
                    console.log('fail : ' + request.responseText);
                    clock = startCheckMessages();
                });
                $('#keyboard').val('');
            }
        }
	});
	
	<?php
    /* Scripts d'administration -------------------------------------------- */
    if($session->admin()): ?>
    
    // Envoi des données pour l'ajout d'un évenement dans le calendrier
    $('#btnedit-messages').on('click', function() {
        changePage('messages',{
            forum_id: <?php echo $_GET['forum_id']; ?>,
            team_forum: $('#team-messages').val(),
            name_forum: $('#name-messages').val()
        });
    });
    
    // Envoi des données pour l'ajout d'un évenement dans le calendrier
    $('#btndel-messages').on('click', function() {
        changePage('forum',{
            del_forum_id: <?php echo $_GET['forum_id']; ?>
        });
    });
    
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

    <p style="text-align: center;" >Chargement...</p>
    
    <?php 
    /* Elements d'administration ------------------------------------------- */
    if($session->admin() AND isset($forum)): ?>
    
    <div data-role="popup" id="popupadmin" data-theme="c" class="ui-corner-all">
        <div style="padding:5px 10px; min-width:210px;">
            <select id="team-messages" >
                <option value="0" <?php if($forum->team == 0) echo 'selected="selected"'; ?> >Toutes les équipes</option>
                <?php $team = new Team(); 
                $team->select();
                while($team->next()): ?>
                    <option value="<?php echo $team->id; ?>" <?php if($forum->team == $team->id) echo 'selected="selected"'; ?> ><?php echo $team->name; ?></option>
                <?php endwhile; ?>
            </select>
            <input type="text" id="name-messages" value="<?php echo $title; ?>" placeholder="Nom du forum" />
            <input type="submit" id="btnedit-messages" value="Confirmer" data-theme="b" />
            <input type="submit" id="btndel-messages" value="Delete" data-theme="b" />
        </div>
    </div>
    
    <?php endif; ?>
    
</div>

<?php 
/* ------------------------------------------------------------------------- */
/* Boutons de navigations (Calendrier, Joueurs, Forum, Lougout)              */
/* ------------------------------------------------------------------------- */
addBottom(3, true);
?> 
