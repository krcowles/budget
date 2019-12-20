$(function() {

var user = $('#user').text();
    $('#back').on('click', function() {
        var budpg = "../main/displayBudget.php?user=" + user;
        window.open(budpg, "_self");
});
positionExpenses();
function positionExpenses() {
    var divloc = $('#exps').offset();
    var hrpos = $('#barloc').offset();
    $('#innerexp').css({
        top: hrpos.top - 8,
        left: divloc.left
    });
}
$('#edcred').on('click', function() {
    window.open('../edit/editCreditCharges.php?user=' + user, "_self");
});
$('#edexp').on('click', function() {
    alert("NOTE: You will be able to modify paid expense data from the last\n" +
        "30 days, including checks and debit card charges. Any changes in the\n" +
        "$ amounts will be reflected in the associated account(s)");
    window.open('../edit/editExpenses.php?user=' + user, "_self");
});

// reset all checkboxes:
$('.mvtocr').prop('checked', false);
$('.mvtodr').prop('checked', false);
$('input[id^=cr]').prop('checked', false);
$('input[id^=dr]').prop('checked', false);
$('input[id^=to]').prop('checked', false);
// cancel button:'
var hrpos = $('#exphr').offset();
$('#canc').css('top', hrpos.top - 40 + 'px');
$('#canc').css('left', hrpos.left + 4 + 'px');
function enableCancel(xfrtype) {
    $('#canc').css('display', 'block');
    $('#canc').on('mouseover', function() {
        $(this).css('color', 'white');
    });
    $('#canc').on('mouseout', function() {
        $(this).css('color', 'brown');
    });
    $('#canc').on('click', function() {
        window.location.reload();
    });
}
// process 'move' actions
$('#e2c').on('click', function() {
    enableCancel();
    var selected = false;
    var msg = "NOTE: Moving a paid (deducted) expense to\na credit card charge" +
        " will increase\nyour checkbook balance";
    var ans = confirm(msg);
    if (ans) {
        var from;
        var to;
        $('#canc').on('click', function() {
            $('.hddr').removeClass('blinking');
            $('.mvdr').css('display', 'none');
            $('mvtocr').removeClass('blinking');
            $('mvtocr').css('display', 'none');
        });
        $('.mvdr').css('display', 'inline');
        $('.hddr').addClass('blinking');
        $('input[id^=dr').on('click', function() {
            if ($(this).prop('checked') && !selected) {
                selected = true;
                $('.hddr').removeClass('blinking');
                var frmid = this.id;
                from = frmid.substr(2);
                $('.mvtocr').css('display', 'inline');
                $('.mvtocr').addClass('blinking');
                $('.mvtocr input').on('click', function() {
                    $('.mvtocr').removeClass('blinking');
                    var toid = $(this).attr('id');
                    to = toid.substr(4);
                    var query = '../edit/moveExp.php?type=e2c&user=' + user +
                        '&frm=' + from + "&to=" + to;
                    window.open(query, "_self");
                });
            } else if ($(this).prop('checked') && selected) {
                alert("Only one item may be selected: to change,\nUncheck the " +
                    "current item, then check the one you want");
                $(this).prop('checked', false);
            } else if (!$(this).prop('checked')) {
                // undo display w/blink
                $('.mvtocr').removeClass('blinking');
                $('.mvtocr').css('display', 'none');
                selected = false;
            }
        });
    }
});
$('#c2c').on('click', function() {
    enableCancel();
    var selected = false;
    var from;
    var to;
    $('.mvcr').css('display', 'inline');
    $('.hdcr').addClass('blinking');
    $('input[id^=cr').on('click', function() {
        if ($(this).prop('checked') && !selected) {
            selected = true;
            $('.hdcr').removeClass('blinking');
            var fromid = this.id;
            from = fromid.substr(2);
            // hide only the cards in groups not selected
            var $td = $(this).parent();
            var $tbl = $td.parent().parent().parent();
            $tbl[0].id = 'selectedtbl';
            $('.mvcr').each(function() {
                // find the <th> element with class 'hdcr'
                var $thistbl = $(this).parent().parent().parent();
                if ($(this).hasClass('hdcr') 
                        && $thistbl.attr('id') !== 'selectedtbl') {
                    $(this).css('display', 'none');
                    var $tds = $thistbl.find('tbody tr td.mvcr');
                    //.css('display', 'none');
                    $tds.css('display', 'none');
                }
            });
            // turn on other cc selectors
            $('table').each(function() {
                if ($(this).attr('id') !== 'selectedtbl') {
                    var $spanto = $(this).prev().prev();
                    if ($spanto.hasClass('mvtocr')) {
                        $spanto.css('display', 'inline');
                        $spanto.addClass('blinking');
                    }
                }
            });
            $('input[id^=tocr]').on('click', function() {
                var toid = this.id;
                to = toid.substr(4);
                var query = "../edit/moveExp.php?type=c2c&user=" + user +
                    "&frm=" + from + "&to=" + to;
                window.open(query, "_self");
            });
        } else if ($(this).prop('checked') && selected) {
            alert("Only one item may be selected: to change,\nUncheck the " +
                "current item, then check the one you want");
            $(this).prop('checked', false);
        } else if (!$(this).prop('checked')) {
            // undo display w/blink
            selected = false;
        }
    });
});
$('#c2e').on('click', function() {
    enableCancel();
    var selected = false;
    var msg = "NOTE: Moving a credit charge to a paid expense\n" +
        "will decrease your checkbook balance";
    var ans = confirm(msg);
    if (ans) {
        var from;
        var to;
        $('.mvcr').css('display', 'inline');
        $('.hdcr').addClass('blinking');
        $('input[id^=cr]').on('click', function() {
            if ($(this).prop('checked') && !selected) {
                selected = true;
                $('.hdcr').removeClass('blinking');
                var fromid = this.id;
                from = fromid.substr(2);
                $('.mvtodr').css('display', 'inline');
                $('.mvtodr').addClass('blinking');
                $('input[id^=todr]').on('click', function() {
                    $('.mvtodr').removeClass('blinking');
                    var toid = this.id;
                    to = toid.substr(4);
                    var query = '../edit/moveExp.php?type=c2e&user=' + user +
                        '&frm=' + from + "&to=" + to;
                    window.open(query, "_self");
                });
            } else if ($(this).prop('checked') && selected) {
                alert("Only one item may be selected: to change,\nUncheck the " +
                    "current item, then check the one you want");
                $(this).prop('checked', false);
            } else if (!$(this).prop('checked')) {
                 // undo display w/blink
                 $('.mvtodr').removeClass('blinking');
                 $('.mvtodr').css('display', 'none');
                 selected = false;
            }
        });
    }
});

$(window).resize(positionExpenses);

});