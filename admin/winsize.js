var divbox = document.getElementById('box');
var rect = divbox.getBoundingClientRect();
var ht = parseInt(rect.height);
var wd = parseInt(rect.width);
alert("Height by width: " + ht + " x " + wd);