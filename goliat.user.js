// ==UserScript==
// @name           Goliat
// @namespace      http://osl.ugr.es/projects
// @description    Acorta automáticamente URLs usando SLUGR
// @include        http://*.ugr.es
// ==/UserScript==

// Adaptado de Auto URL Shortener, de Lucas de Castro
// http://userscripts.org/scripts/show/45667
var serviceUrl = 'http://sl.ugr.es';

// Feedback while loading short url
var feedbackId = 'AutoUrlShortenerFeedback';
loadingElement = document.createElement('div');
loadingElement.setAttribute('id', feedbackId);
loadingElement.setAttribute('style', 'position:fixed; right: 2px; top: 2px; background: #a02c2c; color: white; padding: 2px;')
loadingElement.innerHTML = 'Recuperando URL corto...';
loadingElement.style.display = 'none';
document.body.appendChild(loadingElement);

// Event Handler
function onChange(event) {
  var element = event.currentTarget; 

  /* regex from http://snippets.dzone.com/posts/show/452 */
  var regex = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/g

  urls = element.value.match(regex);
  if (urls) {
    for(i in urls) {
      var url = urls[i]
      // No puede ser de SLUGR
      if (url.substring(0, 15) == serviceUrl) continue;

      GM_xmlhttpRequest({
        method: 'GET',
		  url:    'http://sl.ugr.es/sluger.php?esp=JASON&modo=new&url=' + escape( url ),
		  onreadystatechange: function (responseDetails) {
		  document.getElementById(feedbackId).style.display = 'block'
		      },
		  onload: function(resultado) {
		  try {
		      alert(resultado.responseText);
		      var respuesta;
		      eval( 'respuesta = '+resultado.responseText );
		      var newUrl = "http://"+respuesta;
		      if (confirm("Cambia '" + url + "' a '" + newUrl + "'?")) {
			  element.value = element.value.replace(url, newUrl)
			      }
		  } catch (err) {
		      alert('Vaya, no funciona SLUGR /')
			  }
		  
		  document.getElementById(feedbackId).style.display = 'none'
		      },
		  });
    }
  }
}


// Adding Event Listeners
var elements = document.frames['right'].getElementsByTagName('textarea');

for (i in elements) {
  try {
    elements[i].addEventListener("change", onChange, true);
  } catch (err) { /* dont try it at home ;) */ }
}