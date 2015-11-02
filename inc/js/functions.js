/**
 * Functions JavaScript pour le site
 *
 * @author Jérémy Gobet <jeremy.gobet.72@gmail.com>
 * @version "1.0.0"
 */

// Permet le changement de la page mobile via AJAX
function changePage(page, datas) {
	
	var url = 'index.php?page=' + page;
	
	if(datas !== undefined) {
		$.each(datas,function(index,data) {
			url += '&' + index + '=' + data;
		});
	}
	
	window.location.replace(encodeURI(url));
    
//     window.location.replace(datas.serialize());
	
}

function showLoading() {
    var interval = setInterval(function(){
        $.mobile.loading('show');
        clearInterval(interval);
    },1);
}

function hideLoading() {
    var interval = setInterval(function(){
        $.mobile.loading('hide');
        clearInterval(interval);
    },1);
}
