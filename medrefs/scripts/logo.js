"use strict";
/**
 * @fileoverview This is reusable code for sizing logo elements
 *
 * @author Ken Cowles
 * @version 1.0 First release / responsive design
 */
/**
 * Small screens:
 */
var ss = function (vw) {
    if (vw < 500) {
        $('#ltxt').text('Refs');
        $('#rtxt').text('Data');
    }
    return;
};
// @media (width)
var vw = Math.max(document.documentElement.clientWidth || 0, window.innerWidth || 0);
ss(vw);
// for testing only
$(window).on('resize', function () {
    vw = Math.max(document.documentElement.clientWidth || 0, window.innerWidth || 0);
    ss(vw);
});
// position title in the logo
var title = $('#center').text();
