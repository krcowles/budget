/**
 * @fileoverview Simply detect a sequence of keystrokes on the main budget display
 * page in order to take admin control and invoke debugging features
 * 
 * @author Ken Cowles
 * @version 1.0
 */
$(function() {

var sequence = [];
var backdoor = ".=d";  // any 3-letter lowercase sequence works here
document.addEventListener('keydown', event => {
    let key = event.key;
    if (key !== backdoor[0] && key !== backdoor[1] && key !== backdoor[2]) {
        sequence = [];
    } else
        sequence.push(key);
        let code = sequence.join("");
        if (code === backdoor) {
            // place some debug tool here?
            sequence = [];
        }
});

});