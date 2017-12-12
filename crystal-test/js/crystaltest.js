function getCrystals(){


  $.getJSON('crystaltest.json').done(function (data){
    $.each(data.crystals, function(key, val){
      console.log(key + " : " + val);
    
    var msg = "<div class='crystal' id='crystal" + key + "'>"; 
        msg += "<div id='crystalimg" + key + "'>" + "<a href='#'>" + "<img src=" + val.crystalimage + ">" + "</a>" +"</div>";
     msg += "</div>";
  




    $('.crystal-test-grid').append(msg); 



var crystalinfo = "<div class='crystalinfo' id='crystalinfo" + key + "'>";
 crystalinfo += "<div id='crystalinfoimg" + key + "'>" + "<img src=" + val.crystalimage + ">" + "</div>";
    crystalinfo += "<div class='titleArea' id='titleArea" + key + "'><h2>" + val.title + "</h2>";
      crystalinfo += "<div class='nickName' id='nickName" + key + "'>" + val.nickname + "</div>";
      crystalinfo += " ";
      crystalinfo += "<div class='sub' id='sub" + key + "'>" + val.description + "</div>";
      crystalinfo += "<br/>";
      crystalinfo += "<div class='crystalLink' id='crystalLink" + key + "'><a href=" + val.crystallink + " target='_blank'> Shop This Crystal >> </a></div></div>";

     
      $('.crystal-test-info').append(crystalinfo);


 $('.crystalinfo').hide();

var event = event;

$('#crystal' + key).click(function(event){
  event.preventDefault();
  $(this).css('-webkit-filter', "grayscale(100%)"); /* Safari 6.0 - 9.0 */
  $(this).css('filter', "grayscale(100%)");
$('#crystalinfo' + key).show();
});


$('#reset').click(function(){
  location.reload(true);
});




var modal = document.getElementById('modal');
var btn = document.getElementById('getcrystalized');
var close = document.getElementById('modal-close');

$(btn).click(function(){
modal.style.display = "block";

});

$(close).click(function(){
  modal.style.display = "none";
  location.reload(true);
});



var acc = document.getElementsByClassName("accordion");
var i;

for (i = 0; i < acc.length; i++) {
  acc[i].onclick = function() {
    this.classList.toggle("active");
    var panel = this.nextElementSibling;
    if (panel.style.maxHeight){
      panel.style.maxHeight = null;
    } else {
      panel.style.maxHeight = panel.scrollHeight + "px";
    } 
  }
}


});
  });
};


















$(document).ready(function() {
getCrystals();
});


 


  