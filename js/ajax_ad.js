// Setting var and new ajax request
var adNum = 1;
var ajaxRequest2 = new XMLHttpRequest();

ajaxRequest2.onreadystatechange = function() {
    if(ajaxRequest2.readyState == 4 && ajaxRequest2.status == "200") {
      // get the ajax response successfully
      var adData = ajaxRequest2.responseText;
      renderAdHTML(adData); // use renderHTML() to add the data into HTML
      ajaxRequest2.abort();
    }
}

// Function for adding response text into HTML
function renderAdHTML(jcontent){

  var sidecoll = document.getElementById("sidecol");
  sidecoll.insertAdjacentHTML('beforeend', jcontent);

}

// Send request to server
var queryString2  = "?ad=";
ajaxRequest2.open('GET', 'ajax_response_ad.php' + queryString2 + String(adNum), true);
ajaxRequest2.send(null);
// End of sending ajax ad's request




