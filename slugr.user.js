// ==UserScript==
// @name                Slugr
// @namespace           http://osl.ugr.es/projects/
// @description         Toma un URL y devuelve una URL corta
// @include             *
// ==/UserScript==

var button = document.createElement('span');
var this_a = document.createElement('a');
this_a.setAttribute('href','#');
this_a.addEventListener('click', get_url, false );
var slugr = document.createTextNode('SLUGR');
this_a.appendChild(slugr);
button.appendChild(this_a);
document.body.insertBefore(button, document.body.firstChild);


function get_url () {
    var this_url = document.URL;
    GM_xmlhttpRequest({method: 'GET', 
		url: 'http://sl.ugr.es/sluger.php?esp=JASON&modo=new&url=' + escape( this_url ),
		onload: function( resultado ) {
		alert(resultado.responseText);
 		var respuesta;
		eval( 'respuesta = '+resultado.responseText );
		alert("URL corto: http://"+respuesta.url);
	    }
	});
}
