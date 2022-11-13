/**
 * @fileoverview Check for existing session or cookie; if logged in, jump to main page
 * @author Ken Cowles
 * 
 * @version 1.0 First release of medrefs-in-budget
 */
$(function(){

if ($('#status').text() === 'ok') {
   window.open("pages/main.php", "_self");
}

var startup = new bootstrap.Modal(document.getElementById('startup'));
$('#begin').on('click', function(ev) {
   ev.preventDefault();
   startup.show();
});
$('#newlist').on('click', function() {
   window.open("pages/main.php", "_self");
});

});
