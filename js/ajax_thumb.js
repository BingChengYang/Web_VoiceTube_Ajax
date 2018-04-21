// Setting var and new ajax request
var pageNum = 1;
var ajaxRequest1 = new XMLHttpRequest();

ajaxRequest1.onreadystatechange = function() {
    if(ajaxRequest1.readyState == 4 && ajaxRequest1.status == "200") {
       // get the ajax response successfully
       var videoData1 = ajaxRequest1.responseText;
       // use renderHTML() to add the data into HTML
       renderHTML(videoData1);
    }
}

// Function for adding response text into HTML
function renderHTML(jcontent){

  var thum = document.getElementById("thumb");
  thum.insertAdjacentHTML('beforeend', jcontent);
}

// Send request to server
var queryString  = "?page=";
ajaxRequest1.open('GET', 'ajax_response_thumb.php' + queryString + String(pageNum), true);
ajaxRequest1.send(null);
// End of loading first page




