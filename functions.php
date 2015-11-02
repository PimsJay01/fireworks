<?php // Fonctions PHP pour la page

include('private.php');

// Ajoute un accès sur la page demandée
function addPage($page) {
    echo '<head>';
    include('inc/php/meta.php');
    include('inc/php/css.php');
    include('inc/php/js_loc.php');
    echo '</head>';
    echo '<body>';
	echo '<div data-role="page" id="' . $page . '" >';
	include('pages/' . $page . '.php');
	echo '</div>';
	echo '</body>';
}

function addExport($page) {
    header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
    header("Content-Disposition: attachment; filename=effectif_fireworks.xls");
    header("Content-Transfer-Encoding: binary");
//     ob_clean();
    echo '<head>';
    include('inc/php/meta.php');
//     include('inc/php/css.php');
    echo '</head>';
    echo '<body>';
    include('pages/' . $page . '.php');
    echo '</body>';
}

function addTop($title = '', $back = false) {
    global $top_back, $top_title;
    $top_back = $back;
    $top_title = $title;
    include('inc/php/top.php');
}

function addBottom($btn = 0, $keyword = false) {
    global $bottom_btn, $bottom_keyword;
    $bottom_btn = $btn;
    $bottom_keyword = $keyword;
    include('inc/php/bottom.php');
}

function addBdd() {
    include('bdd/connect.php');
    include('bdd/bdd.php');
    include('inc/php/session.php');
}

// Ajoute une classe pour la gestion d'une table de la base de données
function addBddClasse($classe) {
	include('bdd/' . $classe . '.php');
}

function addBddClasses($classes = array()) {
    foreach($classes as $classe) {
        addBddClasse($classe);
    }
}

function escapeInt($int = 0) {
//   return (is_integer($int)) ? (int)$int : 0;
    return (int)$int;
}

function escapeText($text = '') {
    return (is_string($text)) ? $text : '';
}

function escapeTime($time = '00:00') {
    return $time;
}

function escapeDate($date = '') {
    return $date;
}

function getTypeEvent($event) {
    $types = Array("Entraînement","Championnat","Coupe","Challenge","Repas","Fête","Voyage","Autre");
    return $types[$event];
}

function getColorEvent($event) {
    $colors = Array("#BBBBBB","#EEDD88","#DDDDEE","#DD9944","#DD6666","#99EE99","#FF8833","#DDAADD");
    return $colors[$event];
}

?>