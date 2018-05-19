// add href for button

var btn = document.getElementById("edit_btn");
var newa = document.createElement("a");
newa.href = "edit_caption.html?id=" + videoId;
newa.innerHTML = "Edit caption";

btn.appendChild(newa);