// modal object definition
var modal = (function() {
    // Local/private to "modal"; invoked only on instantiation
    var $window = $(window);
    var $modal = $('<div class="modal" style="background-color:floralwhite;position:absolute"/>'); 
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
    // Add or subtract amounts in a cell
    function modifyData(jQloc, operand, operation, chgType) {
        // jQloc is the jQuery object holding the subject row to be modified
        cellData = parseFloat(jQloc.eq(4).children().eq(1).text());
        if (operation === 'sub') {
            cellData -= operand;
        } else { // presumably 'add'
            cellData += operand;
        }
        if (cellData < 0) {
            jQloc.eq(4).children().eq(1).addClass('negative');
        }
        cellData = $.number(cellData, 2);
        jQloc.eq(4).children().eq(1).text(cellData);
        if (chgType === 'Account' || chgType === 'Debit') {
            // fix the balance
            $balance = $('#balances').find('td').eq(4).children().eq(1);
            var balance = $balance.text().replace(",", "");
            balance = parseFloat(balance);
            if (operation === 'sub') {
                balance -= operand;
            } else {
                balance += operand;
            }
            var newbal = $.number(balance, 2);
            if (newbal < 0) {
                $balance.addClass('negative');
            }
            $balance.text(newbal);
        }  
        return;
    }
    // modal function executed when settings.id == 'expense'
    function payExpense() {
        $content.append($close);
        $close.css('left', '220px');
        var locate = $('#expense').offset();
        $modal.css({
            top: locate.top + 40,
            left: locate.left - 100
        });
        var account = $('#selacct option:selected').text();
        var $account = $('#selacct');
        var chargeto = $('#cc option:selected').text();
        var $card = $('#cc');
        var amount = 0;
        var $expensed = $('#expamt');
        $account.on('change', function() {
            account = this.value;
        });
        $card.on('change', function() {
            chargeto = this.value;
        });
        $expensed.on('change', function() {
            amount = this.value;
        });
        $('#pay').on('click', function() {
            //alert("Pay " + amount + ": Picked " + account + "; Charged " + chargeto);
            $('#roll3 tbody tr').each(function() {
                var $aname = $(this).find('td');
                var aname = $aname.eq(0).text();
                if (aname == account) {
                    modifyData($aname, amount, 'sub','Account');
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
            $content.empty().append(settings.content.html());
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