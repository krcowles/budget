// modal object definition
var modal = (function() {
    // Local/private to "modal"; invoked only on instantiation
    var $window = $(window);
    var $modal = $('<div class="modal" style="background-color:floralwhite;position:absolute"/>'); 
    var $drag = $('<div id="header">Drag Here</div>');
    var $content = $('<div class="modal-content"/>');
    var closeHtml = '<button class="modal-close">Cancel</button>';
    var $close = $(closeHtml);

    $modal.append($content);
    // bind to doc for repeated use of modal popups
    $(document).on('click', '.modal-close', function(ev) {
        ev.preventDefault();
        modal.close();
    });

    /**
     * The following (private) functions are used to process form data, and
     * are called by the (public) modal.open function, based on the modal id
     * passed via settings.
     */
    // modal function executed when settings.id == 'expense'
    function payExpense() {
        $content.append($close);
        $close.css('left', '216px');
        $close.text("Cancel");
        var locate = $('#expense').offset();
        $modal.css({
            top: locate.top + 40,
            left: locate.left - 100
        });
        // id the options present in the <select> box
        var opts = [];
        var selbox = document.getElementById('selacct');
        for (var j=0; j<selacct.options.length; j++) {
            opts[j] = selbox.options[j].value;
        }
        var acctrows = opts.length;
        // objects to register user entries/changes
        var $account = $('#selacct');
        var $chargeto = $('#cc');
        var $expensed = $('#expamt');
        var $payee = $('#payee');
        // initial values
        var acctname = $('#selacct option:selected').text();
        var editrow = 0;
        var chargeto = $('#cc option:selected').text();
        var amount = 0;
        var payee = 'None specified';
        $account.on('change', function() {
            acctname = this.value;
            for (var j=0; j<acctrows; j++) {
                if (acctname === opts[j]) {
                    editrow = j;
                    return;
                }
            }
        });
        $chargeto.on('change', function() {
            chargeto = this.value;
        });
        $expensed.on('change', function() {
            amount = this.value;
        });
        $payee.on('change', function() {
            payee = this.value;
        });
        $('#pay').on('click', function() {
            var ajaxdata = {acct_name: acctname, edit_row: editrow, 
                chg_type: chargeto, amt: amount, payto: payee};
            $.ajax({
                url: "../edit/saveAcctEdits.php",
                data: ajaxdata,
                dataType: "text",
                method: "GET",
                success: function(results) {
                    if (results == "OK") {
                        modal.close;
                        location.reload();
                    } else {
                        alert("Problem encountered executing payment");
                    }
                    
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    var msg = "Problem encountered; Code: " + errorThrown +
                        "; " + txtStatus;
                    alert(msg);
                }
            });
        });
    }
    // function executed when settings.id == 'deposit'
    function makeDeposit() {

    }
    // function executed when settings.id == 'edit_chg' (edit a Cr charge)
    function editCredit(inputvals, locater, cardinfo, defobj) {
        $('#svmodal').after($close);
        $close.css('margin-left', '40px');
        $close.css('margin-bottom', '12px');
        var pos = locater.offset();
        $modal.css({
            top: pos.top,
            left: 560
        });
        var $chargeto = $content.find('input[id=chg]');
        var $date = $content.find('input[id=de]');
        var $payee = $content.find('input[id=pay]');
        var $amt = $content.find('input[id=namt]');
        $chargeto.attr('value', inputvals[0]);
        $date.attr('value', inputvals[1]);
        $payee.attr('value', inputvals[2]);
        $amt.attr('value', inputvals[3]);

        $close.on('click', function() {
            locater.children().each(function() {
                $(this).css('background-color', 'white');
            });
            locater.find('input').prop('checked', false);
            defobj.resolve();
            modal.close;
        });
        $('#svmodal').on('click', function() {
            // get all changes
            var charge = $chargeto.val();
            var date   = $date.val();
            var payee  = $payee.val();
            var amt    = $amt.val();
            var ajaxdata = {cno: cardinfo.cdno, item: cardinfo.itemno, chg: charge,
                date: date, payee: payee, amount: amt}
            $.ajax({
                url: 'saveEditedCharge.php',
                data: ajaxdata,
                dataType: "text",
                method: "GET",
                success: function(results) {
                    if (results !== 'OK') {
                        alert("Error saving edited charge:\n" + results);
                    } else {
                        $close.click();
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    var msg = "modals.js ajax to saveEditedCharge.php failed\n" +
                        textStatus + "Error code: " + errorThrown;
                    alert(msg);
                }
            });
        });
    }
    // function called when settings.id == 'autopay'
    function autopay( method, acct_name, row) {
        $('#modal_table').after($close);
        $close.css('margin-top', '12px');
        $close.text("Finished");
        $modal.css({
            top: 40,
            left: 600
        });
        var divht = $content.height() + 24 + 'px';
        $modal.css({
            height: divht
        });
        $('button[id^=paymt]').each(function() {
            $(this).on('click', function() {
                var idstr = this.id;
                var idno = parseInt(idstr.substr(5));
                var inpid = '#amt' + idno;
                var payment = parseFloat($(inpid).val());
                var payid = '#payee' + idno;
                var payto = $(payid).val();
                var elements = payto.split();
                for (var n=0; n<elements.length; n++) {
                    if (n !== elements.length - 1) {
                        elements[n] += '%20';
                    }
                }
                var payee = elements.join().trim();
                window.open('../utilities/updateAP.php?row=' + row[idno] + 
                    '&use=' + method[idno] + '&amt=' + payment +
                    '&payto=' + payee + '&acct=' + acct_name[idno], "_self");
            });
        });
        $close.on('click', function () {
            $content.empty();
            $modal.detach();
        });
    }
    function distribute() {
        $('#dist').after($close);
        $close.css('margin-left', '148px');
        $modal.css({
            top: 60,
            left: 200
        });
        var funds = 0;
        $funds = $('#incamt').on('change', function() {
            funds = this.value;
        });
        $('#dist').on('click', function() {
            var ajaxdata = {funds: funds};
            $.ajax({
                url: "../utilities/enterIncome.php",
                method: "GET",
                data: ajaxdata,
                dataType: "text",
                success: function(results) {
                   if (results == "OK") {
                       alert("Good");
                       location.reload();
                   }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    var msg = "Problem encountered distributing income:" +
                     " Error " + errorThrown + "; " + textStatus;
                    alert(msg);
                }
            });
            modal.close;
        });
        $close.on('click', function () {
           modal.close;
        });
    }
    
    // public methods
    return {
        center: function() {
            var top = Math.max($window.height() - $modal.outerHeight(), 0) / 2;
            var left = Math.max($window.width() - $modal.outerWidth(), 0) / 2;
            $modal.css({
                    top: top + $window.scrollTop(),
                    left: left + $window.scrollLeft()
            });
        },
        open: function(settings) {
            var modid = settings.id;
            $content.empty().append($drag).append(settings.content.html());
            $modal.css({
                width: settings.width || auto,
                height: settings.height || auto,
                border: '2px solid',
                padding: '8px'
            }).appendTo('body');
            var modal_box = $modal[0];
            dragElement(modal_box);
            // separate code for each form
            if (modid === 'expense') {
                payExpense();
            } else if (modid === 'deposit') {
                makeDeposit();
            } else if (modid === 'edit_chg') {
                editCredit(settings.ivals, settings.chgitem, 
                        settings.chgid, settings.def);
            } else if (modid === 'autopay') {
                autopay(settings.method, settings.acct_name, settings.row_no);
            } else if (modid === 'income') {
                distribute();
            } else {
                modal.center();
                $(window).on('resize', modal.center);
            }
        },
        close: function() {
            $content.empty();
            $modal.detach();
            $(window).off('resize', modal.center);
        }
    };
}());  // modal is an IIFE
// autopay modal is draggable:
function dragElement(elmnt) {
    var pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;
    if (document.getElementById(elmnt.id + "header")) {
      // if present, the header is where you move the DIV from:
      document.getElementById(elmnt.id + "header").onmousedown = dragMouseDown;
    } else {
      // otherwise, move the DIV from anywhere inside the DIV:
      elmnt.onmousedown = dragMouseDown;
    }
    function dragMouseDown(e) {
      e = e || window.event;
      e.preventDefault();
      // get the mouse cursor position at startup:
      pos3 = e.clientX;
      pos4 = e.clientY;
      document.onmouseup = closeDragElement;
      // call a function whenever the cursor moves:
      document.onmousemove = elementDrag;
    }
    function elementDrag(e) {
      e = e || window.event;
      e.preventDefault();
      // calculate the new cursor position:
      pos1 = pos3 - e.clientX;
      pos2 = pos4 - e.clientY;
      pos3 = e.clientX;
      pos4 = e.clientY;
      // set the element's new position:
      elmnt.style.top = (elmnt.offsetTop - pos2) + "px";
      elmnt.style.left = (elmnt.offsetLeft - pos1) + "px";
    }
    function closeDragElement() {
      // stop moving when mouse button is released:
      document.onmouseup = null;
      document.onmousemove = null;
    }
}