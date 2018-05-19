// check when pressing the add_subtitle button 
var s_table = document.getElementById("subtitle_table");
var add_s = document.getElementById("add_subtitle");
add_s.onclick = checkNewSubtitle;

// request the video's caption by ajax if it already has 
$.ajax({
          url: 'ajax_response_caption.php?cap='+videoId,
          dataType: 'text',
          type: 'get',
          success: function( data, textStatus, jQxhr ){
              renderCapHTML(JSON.parse(data));
          }
        });

function renderCapHTML(jcontent){
  // console.log("jcontent: " + jcontent);
  for(var key in jcontent.en)
  {
    var jstart = Number(jcontent.en[key].start);
    var jdur = Number(jcontent.en[key].dur);
    var jend = jstart + jdur;
    var osm = Math.floor(jstart / 60);
    var oss = Math.floor(jstart - osm * 60);
    var osms = Math.floor((jstart - osm * 60 - oss) * 10);
    var oem = Math.floor(jend / 60);
    var oes = Math.floor(jend - oem * 60);
    var oems = Math.floor((jend - oem * 60 - oes) * 10);
    var osub = String(jcontent.en[key].text);

    var tmp = $('#subtitle_table');
      tmp.append(
        '<tr>'+
          '<td Name="Time" width="170">'+
            '<input type="number" min="0" max="59" onchange="checkStartEndTime()" '+'value="'+osm+'"/>'+
            ':<input type="number" min="0" max="59" onchange="checkStartEndTime()" '+'value="'+oss+'"/>'+
            '.<input type="number" min="0" max="9" onchange="checkStartEndTime()" '+'value="'+osms+'"/><br>- '+
            '<input type="number" min="0" max="59" onchange="checkStartEndTime()" '+'value="'+oem+'"/>'+
            ':<input type="number" min="0" max="59" onchange="checkStartEndTime()" '+'value="'+oes+'"/>'+
            '.<input type="number" min="0" max="9" onchange="checkStartEndTime()" '+'value="'+oems+'"/>'+
          '</td>'+
          '<td Name="Subtitle" >'+osub+'</td>'+
          '<td Name="Operate" width="90" >' + 
            '<input type="button" value="Delete" onclick="deleteRow(this)">'+
            '<br>'+
            '<input type="button" value="Create" onclick="insertRow(this)">'+
          '</td>'+
        '</tr>');
    // console.log("start : " + jcontent.en[key].start + " dur : " + jcontent.en[key].dur + "\n");
    // console.log("text : " + jcontent.en[key].text + "\n");
  }
  SetTableCanEdit(s_table);
}

// Add new subtitle
function checkNewSubtitle(){
    var asub = document.getElementById("a_sub");
    var currentTime = player.getCurrentTime();
    var asm =  Math.floor(currentTime / 60);
    var ass = Math.floor(currentTime - (asm * 60));
    var asms = Math.floor((currentTime - (asm * 60) - ass) * 10);
    var em = asm;
    // default time for each new caption = 2 sec. 
    var es = ass + 2; 
    if(es >= 60){
      es = es - 60;
      em = em + 1;
    }

    // check whether the caption's block is empty
    if(asub.value === "" || asub.value.trim().length === 0){
      asub.focus();
      asub.select();
      alert("Subtitle can not be blank.");
    }
    else{
      // can add subtitle
      var tmp = $('#subtitle_table');
      tmp.append(
        '<tr>'+
          '<td Name="Time" width="170">'+
            '<input type="number" min="0" max="59" onchange="checkStartEndTime()" '+'value="'+Number(asm)+'"/>'+
            ':<input type="number" min="0" max="59" onchange="checkStartEndTime()" '+'value="'+Number(ass)+'"/>'+
            '.<input type="number" min="0" max="9" onchange="checkStartEndTime()" '+'value="'+Number(asms)+'"/><br>- '+
            '<input type="number" min="0" max="59" onchange="checkStartEndTime()" '+'value="'+Number(em)+'"/>'+
            ':<input type="number" min="0" max="59" onchange="checkStartEndTime()" '+'value="'+Number(es)+'"/>'+
            '.<input type="number" min="0" max="9" onchange="checkStartEndTime()" '+'value="'+Number(asms)+'"/>'+
          '</td>'+
          '<td Name="Subtitle" >'+asub.value+'</td>'+
          '<td Name="Operate" width="90" >' + 
            '<input type="button" value="Delete" onclick="deleteRow(this)">'+
            '<br>'+
            '<input type="button" value="Create" onclick="insertRow(this)">'+
          '</td>'+
        '</tr>');
      asub.value = "";
      if(s_table.rows.length != 0){
        sortTable();
        SetTableCanEdit(s_table);
      }
    }
}

// maintain the order of subtitle
function sortTable() {
  var rows, switching, i, x, y, shouldSwitch;

  switching = true;
  /*Make a loop that will continue until
  no switching has been done:*/
  while (switching) {
    //start by saying: no switching is done:
    switching = false;
    rows = s_table.getElementsByTagName("TR");
    /*Loop through all table rows (except the
    first, which contains table headers):*/
    for (i = 0; i < (rows.length - 1); i++) {
      //start by saying there should be no switching:
      shouldSwitch = false;
      /*Get the two elements you want to compare,
      one from current row and one from the next:*/
      // x = rows[i].getElementsByTagName("TD")[0];
      x = rows[i].cells[0].querySelectorAll('input')[0];
      y = rows[i + 1].cells[0].querySelectorAll('input')[0];
      //check if the two rows should switch place:
      // console.log("out=> x: " + x.value + " y: " + y.value);
      if (Number(x.value) > Number(y.value)) {
        //if so, mark as a switch and break the loop:
        shouldSwitch= true;
        break;
      }
      else if(Number(x.value) === Number(y.value)){
        x1 = rows[i].cells[0].querySelectorAll('input')[1];
        y1 = rows[i + 1].cells[0].querySelectorAll('input')[1];
        if(Number(x1.value) > Number(y1.value)){
          shouldSwitch= true;
          break;
        }
        else if(Number(x1.value) === Number(y1.value)){
          x2 = rows[i].cells[0].querySelectorAll('input')[2];
          y2 = rows[i + 1].cells[0].querySelectorAll('input')[2];
          if(Number(x2.value) > Number(y2.value)){
            shouldSwitch= true;
            break;
          }
        }
      }
    }
    if (shouldSwitch) {
      /*If a switch has been marked, make the switch
      and mark that a switch has been done:*/
      rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
      switching = true;
    }
  }
}

// to check whether the change of the time is legal
function checkStartEndTime(){
  var sm, ss, sms, em, es, ems;
  var nsm, nss, nsms, nem, nes, nems;
  var haveFault = false;
  rows = s_table.getElementsByTagName("TR");
  for (i = 0; i < rows.length; i++) {
      sm = Number(rows[i].cells[0].querySelectorAll('input')[0].value);
      ss = Number(rows[i].cells[0].querySelectorAll('input')[1].value);
      sms = Number(rows[i].cells[0].querySelectorAll('input')[2].value);
      em = Number(rows[i].cells[0].querySelectorAll('input')[3].value);
      es = Number(rows[i].cells[0].querySelectorAll('input')[4].value);
      ems = Number(rows[i].cells[0].querySelectorAll('input')[5].value);

      // check the start-end relation first
      if(em < sm){
          haveFault = true;
          // console.log("em : " + em + " sm : " + sm);
          // rows[i].cells[0].querySelectorAll('input')[3].focus();
          // rows[i].cells[0].querySelectorAll('input')[3].select();
          break;
      }
      else if(em === sm){
          if(es < ss){
            haveFault = true;
            // console.log("es : " + es + " ss : " + ss);
            // rows[i].cells[0].querySelectorAll('input')[4].focus();
            // rows[i].cells[0].querySelectorAll('input')[4].select();
            break;
          }
          else if(es === ss){
              if(ems <= sms){
                haveFault = true;
                // console.log("ems : " + ems + " sms : " + sms);
                // rows[i].cells[0].querySelectorAll('input')[5].focus();
                // rows[i].cells[0].querySelectorAll('input')[5].select();
                break;
              }
          }
      }

      // have next row
      if(i != (rows.length - 1)){
        nsm = Number(rows[i+1].cells[0].querySelectorAll('input')[0].value);
        nss = Number(rows[i+1].cells[0].querySelectorAll('input')[1].value);
        nsms = Number(rows[i+1].cells[0].querySelectorAll('input')[2].value);
        nem = Number(rows[i+1].cells[0].querySelectorAll('input')[3].value);
        nes = Number(rows[i+1].cells[0].querySelectorAll('input')[4].value);
        nems = Number(rows[i+1].cells[0].querySelectorAll('input')[5].value);  

        // then check whether there is time range conflict between two subtitles
        if(em > nsm){
            haveFault = true;
            // console.log("em : " + em + " nsm : " + nsm);
            // rows[i].cells[0].querySelectorAll('input')[3].focus();
            // rows[i].cells[0].querySelectorAll('input')[3].select();
            break;
        }
        else if(em === nsm){
            if(es > nss){
                haveFault = true;
                // console.log("es : " + es + " nss : " + nss);
                break;
            }
        }
      }

  }
  if(haveFault){
    alert("illegal time!");
    return false;
  }
  else{
    sortTable();
    return true;
  }
}


// make subtitle editable
function SetTableCanEdit(table){    
  for(var i = 0; i < table.rows.length; i++){    
     SetRowCanEdit(table.rows[i]);    
  }    
}
    
function SetRowCanEdit(row){  
    // action for subtitle, return object
    row.cells[1].onclick = function (){    
      var editcell = function() {
        return function (element, editType){
          CreateTextBox(element, element.innerHTML);
        };
      };
      var next = editcell();
      next(this); 
    }  
}



function CreateTextBox(element, value){    
  //检查编辑状态，如果已经是编辑状态，跳过    
  var editState = element.getAttribute("EditState");    
  if(editState != "true"){    
     //创建文本框    
     var textBox = document.createElement("INPUT");    
     textBox.type = "text";    
     textBox.className="EditCell_TextBox";    
        
        
     //设置文本框当前值    
     if(!value){
      value = element.getAttribute("Value");    
     }     
     textBox.value = value;    
        
     // set value when no focus  
     textBox.onblur = function (){
        CancelEditCell(this.parentNode, this.value);    
     }    
     //向当前单元格添加文本框    
     element.innerHTML = "";   
     element.appendChild(textBox);    
     textBox.focus();    
     textBox.select();    
          
     element.setAttribute("EditState", "true");     
  }    
    
}

function CancelEditCell(element, value, text){    
  element.setAttribute("Value", value);    
  if(text){    
     element.innerHTML = text;    
  }else{    
     element.innerHTML = value;    
  }    
  element.setAttribute("EditState", "false");    
}  

if(s_table.rows.length != 0){
  SetTableCanEdit(s_table);
}

// delete and insert row
function deleteRow(r) {
    var idx = r.parentNode.parentNode.rowIndex;
    s_table.deleteRow(idx);
}
function insertRow(r){
  var idx = r.parentNode.parentNode.rowIndex;
  // add the time part for new row's cell
  var tmpm = Number(s_table.rows[idx].cells[0].querySelectorAll('input')[3].value), 
      tmps = Number(s_table.rows[idx].cells[0].querySelectorAll('input')[4].value),
      tmpms = Number(s_table.rows[idx].cells[0].querySelectorAll('input')[5].value);

  // check the next row's time to avoid conflict
  var tmpnextm = Number(s_table.rows[idx+1].cells[0].querySelectorAll('input')[0].value), 
      tmpnexts = Number(s_table.rows[idx+1].cells[0].querySelectorAll('input')[1].value),
      tmpnextms = Number(s_table.rows[idx+1].cells[0].querySelectorAll('input')[2].value);

  var gap = (tmpnextm * 60 + tmpnexts + tmpnextms * 0.1) - (tmpm * 60 + tmps + tmpms * 0.1);// the dur between two caption
  // can insert new row
  if(gap >= 1)
  {
    var row = s_table.insertRow(idx+1);
    var cell1 = row.insertCell(0);
    var cell2 = row.insertCell(1);
    var cell3 = row.insertCell(2);
    
    var t1 = document.createElement("input");
    t1.type = "number"; t1.min = "0"; t1.max = "59"; t1.onchange = function(){ checkStartEndTime(); }; 
    t1.value = tmpm;
    cell1.appendChild(t1);
    cell1.append(':');
    var t2 = document.createElement("input");
    t2.type = "number"; t2.min = "0"; t2.max = "59"; t2.onchange = function(){ checkStartEndTime(); }; 
    t2.value = tmps;
    cell1.appendChild(t2);
    cell1.append('.');
    var t3 = document.createElement("input");
    t3.type = "number"; t3.min = "0"; t3.max = "9"; t3.onchange = function(){ checkStartEndTime(); }; 
    t3.value = tmpms;
    cell1.appendChild(t3);

    var br = document.createElement("br");
    cell1.appendChild(br); cell1.append('- ');
    // the dur between two caption is < 2 sec.
    if(gap < 2)
    {
      var t4 = document.createElement("input");
      t4.type = "number"; t4.min = "0"; t4.max = "59"; t4.onchange = function(){ checkStartEndTime(); };  
      t4.value = tmpnextm;
      cell1.appendChild(t4);
      cell1.append(':');
      var t5 = document.createElement("input");
      t5.type = "number"; t5.min = "0"; t5.max = "59"; t5.onchange = function(){ checkStartEndTime(); };  
      t5.value = tmpnexts;
      cell1.appendChild(t5);
      cell1.append('.');
      var t6 = document.createElement("input");
      t6.type = "number"; t6.min = "0"; t6.max = "9"; t6.onchange = function(){ checkStartEndTime(); }; 
      t6.value = tmpnextms;
      cell1.appendChild(t6);

      // add subtitle part for new row's cell
      cell2.innerHTML = "click to edit";

      // add operating button for new row's cell
      var t7 = document.createElement("input");
      t7.type = "button"; t7.value = "Delete"; t7.onclick = function(){ deleteRow(this); };
      cell3.appendChild(t7);
      var br2 = document.createElement("br");
      cell3.appendChild(br2);
      var t8 = document.createElement("input");
      t8.type = "button"; t8.value = "Create"; t8.onclick = function(){ insertRow(this); };
      cell3.appendChild(t8);
    }
    else
    {
      tmps += 2;
      if(tmps >= 60){
        tmpm += 1;
        tmps -= 60;
      }
      var t4 = document.createElement("input");
      t4.type = "number"; t4.min = "0"; t4.max = "59"; t4.onchange = function(){ checkStartEndTime(); };  
      t4.value = tmpm;
      cell1.appendChild(t4);
      cell1.append(':');
      var t5 = document.createElement("input");
      t5.type = "number"; t5.min = "0"; t5.max = "59"; t5.onchange = function(){ checkStartEndTime(); };  
      t5.value = tmps;
      cell1.appendChild(t5);
      cell1.append('.');
      var t6 = document.createElement("input");
      t6.type = "number"; t6.min = "0"; t6.max = "9"; t6.onchange = function(){ checkStartEndTime(); }; 
      t6.value = tmpms;
      cell1.appendChild(t6);

      // add subtitle part for new row's cell
      cell2.innerHTML = "click to edit";

      // add operating button for new row's cell
      var t7 = document.createElement("input");
      t7.type = "button"; t7.value = "Delete"; t7.onclick = function(){ deleteRow(this); };
      cell3.appendChild(t7);
      var br2 = document.createElement("br");
      cell3.appendChild(br2);
      var t8 = document.createElement("input");
      t8.type = "button"; t8.value = "Create"; t8.onclick = function(){ insertRow(this); };
      cell3.appendChild(t8); 
    }
    SetTableCanEdit(s_table);
    sortTable();
  }
  else{
    alert("There is no enough time duration for new subtitle.");
  }
}

// send the edited caption to php to save in db
var save_btn = document.getElementById("save_subtitle");
save_btn.onclick = sendEditedCaption;

function sendEditedCaption(){

  if(checkStartEndTime()){
      var captionstring = '{"en":[';
      var tmpstart, tmpdur;
      var tmpsm, tmpss, tmpsms, tmpem, tmpes, tmpems, tmpsub;

      // loop the table to get "start", "dur", "text"
      for(var i = 0; i < s_table.rows.length; i++)
      {
        tmpsm = Number(s_table.rows[i].cells[0].querySelectorAll('input')[0].value);
        tmpss = Number(s_table.rows[i].cells[0].querySelectorAll('input')[1].value);
        tmpsms = Number(s_table.rows[i].cells[0].querySelectorAll('input')[2].value);
        tmpem = Number(s_table.rows[i].cells[0].querySelectorAll('input')[3].value);
        tmpes = Number(s_table.rows[i].cells[0].querySelectorAll('input')[4].value);
        tmpems = Number(s_table.rows[i].cells[0].querySelectorAll('input')[5].value);
        tmpsub = String(s_table.rows[i].cells[1].innerHTML);

        tmpstart = tmpsm * 60 + tmpss + tmpsms * 0.1;
        tmpdur = (((tmpem * 60 + tmpes + tmpems * 0.1) * 1000 - (tmpsm * 60 + tmpss + tmpsms * 0.1) * 1000) / 1000).toFixed(2);
        captionstring = captionstring + '{"start":"' + tmpstart + '","dur":"' + tmpdur
                        + '","text":"' + tmpsub + '"},'
      }
      captionstring = captionstring.substr(0, captionstring.length - 1);
      captionstring = captionstring + ']}';

      // send back the edited caption
      $.post("edit_caption_response.php?id="+videoId,
              {
                newcaption : captionstring
              },
              function(data){
                  console.log(data);
                  location.href = "player.html?id="+videoId +"&file=caption_" + videoId;
              }
            );
  }
}


