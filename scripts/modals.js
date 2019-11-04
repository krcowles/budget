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
        $(window).on('resize', function() {
            $modal.css({
                top: locate.top + 40,
                left: locate.left - 100
            });
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
    function autopay(aploc, acct, bal, defobj) {
        $('#payit').after($close);
        $close.css('margin-left', '40px');
        var vert = aploc.offset();
        var modtop = vert.top -6;
        var horiz = $('#expense').offset();
        var modleft = horiz.left;
        $modal.css({
            top: modtop,
            left: modleft
        });
        $close.on('click', function () {
            $content.empty();
            $modal.detach();
            defobj.resolve();
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
            // separate code for each form
            if (modid === 'expense') {
                payExpense();
            } else if (modid === 'deposit') {
                makeDeposit();
            } else if (modid === 'edit_chg') {
                editCredit(settings.ivals, settings.chgitem, 
                        settings.chgid, settings.def);
            } else if (modid === 'autopay') {
                autopay(settings.loc, settings.acctbal,
                    settings.cbkbal, settings.def);
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