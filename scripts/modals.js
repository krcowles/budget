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
    // modal function used to verify user's password or send link to reset
    function getpass(usrname) {
        $drag.detach();
        var logpos = $('#login').offset();
        var logtop = logpos.top;
        var logwd = logpos.left + $('#login').width() + 12;
        $modal.css({
            top: logtop,
            left: logwd
        });
        // enter username into form element:
        $('#moduser').val(usrname);
        document.getElementById("passin").focus(); 
        $('form').submit(function(ev) {
            ev.preventDefault();
            passwd = $('#passin').val();
            validateUser(usrname, passwd);
        });
        // OR send a link to reset the password:
        $('#redopass').on('click', function() {
            var unused = new $.Deferred();
            var ajaxdata = {parm: 'passwd', email: usrname};
            sendUserMail(ajaxdata, unused);
        });
    }
    // modal function used to send email to user with user's name
    function userName(deferred) {
        $drag.detach();
        $('#sendmail').after($close);
        $close.css('margin-left', '112px');
        $('#sendmail').on('click', function() {
            var mailaddr = $('#umail').val();
            var ajaxdata = {parm: 'uname', email: mailaddr};
            sendUserMail(ajaxdata, deferred);
        });
        $close.on('click', function() {
            deferred.resolve();
            modal.close();
        });
    }
    // reusable ajax code to sendmail to user
    function sendUserMail(ajaxdata, deferred) {
        $.ajax({
            url: 'admin/sendmail.php',
            method: "POST",
            data: ajaxdata,
            dataType: "text",
            success: function(results) {
                if (results === 'ok') {
                    alert("An email has been sent");
                } else if (results === 'bad') {
                    alert("The information you typed\n" +
                        "is not a well-formed email addresss");
                } else if (results === 'nofind') {
                    alert("Your email/username could not be located in our records");
                }
                deferred.resolve();
                modal.close();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                var msg = "Unable to complete the process of sending email:\n"
                    + "Contact site master or try again\n" +
                    "Error: " + errorThrown + "; " + textStatus;
                alert(msg);
                deferred.resolve();
                modal.close();
            }
        });
    }
    // general purpose function to execute ajax based on input arguments
    function executeScript(url, ajaxdata, errtype, deferred) {
            var msgtxt = "Problem encountered " + errtype;
            $.ajax({
                url: url,
                method: "POST",
                data: ajaxdata,
                dataType: "text",
                success: function(results) {
                    if (results === "OK") {
                        deferred.resolve();
                        modal.close()
                        location.reload();
                    } else {
                        deferred.resolve();
                        alert(msgtxt);
                        modal.close();
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    deferred.resolve();
                    msg = msgtxt + ":\n" + textStatus + "; Error: " + errorThrown;
                    alert(msg);
                    modal.close();
                }
            });
    }
    function getSelectValue(domId) {
        var value = domId[domId.selectedIndex].value;
        return value;
    }
    // function called when settings.id == 'autopay'
    function autopay(method, acct_name, deferred) {
        $('#modal_table').after($close);
        $close.css('margin-top', '12px');
        $close.text("Exit");
        var divht = $content.height() + 12 + 'px';
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
                ajaxdata = {method: method[idno], acct: acct_name[idno],
                    amt: payment, payee: payee};
                    executeScript('../utilities/makeAutopayment.php', ajaxdata,
                    "executing autopayment", deferred);
            });
        });
        $close.on('click', function () {
            deferred.resolve();
            modal.close();
        });
    }
    // modal function executed when settings.id == 'expense'
    function payExpense(deferred) {
        $content.append($close);
        $close.css('margin-left', '216px');
        $close.text("Cancel");
        var $selacct = $('#fsel0 .fullsel');
        var $selcd = $('#csel0 .allsel');
        var $expensed = $('#expamt');
        var $payee = $('#payee');
        // initial values
        var acctname = getSelectValue($selacct[0]);
        var charge = '';
        var amount = 0;
        var payee = 'None specified';
        // updated values
        $selacct.on('change', function() {
            acctname = this.value;
        });
        $selcd.on('change', function() {
            charge = this.value;
        });
        $expensed.on('change', function() {
            amount = this.value;
        });
        $payee.on('change', function() {
            payee = this.value;
        });
        $('#pay').on('click', function() {
            // validate:
            if (charge === '') {
                alert("You have not selected a payment method");
                return;
            } else if (amount === 0) {
                alert("You have not entered an amount to pay");
                return;
            } else if (!$.isNumeric(amount)) {
                alert("The amount entered is not a number");
                return;
            } else if (payee === 'None specified' || payee === '') {
                alert("You have not entered a payee");
                return;
            }
            var ajaxdata = {id: 'payexp', acct_name: acctname,
                method: charge, amt: amount, payto: payee};
            executeScript('../edit/saveAcctEdits.php', ajaxdata,
                'making payment', deferred);
        });
        $close.on('click', function() {
            deferred.resolve();
            modal.close();
        });
    }
    // modal function executed when settings.id == 'income'
    function distribute(deferred) {
        $('#dist').after($close);
        $close.css('margin-left', '150px');
        var funds = 0;
        $funds = $('#incamt').on('change', function() {
            funds = this.value;
        });
        $('#dist').on('click', function() {
            var ajaxdata = {id: 'income', funds: funds};
            executeScript('../edit/saveAcctEdits.php', ajaxdata,
                "distributing income", deferred);
            modal.close();
        });
        $close.on('click', function () {
            deferred.resolve();
            modal.close();
        });
    }
    // modal function executed when settings.id == 'deposit'
    function makeDeposit(deferred) {
        $('#depfunds').after($close);
        $close.css('margin-left', '22px');
        var deposit_funds = 0;
        var $deposit = $('#depo');
        $deposit.on('change', function() {
            deposit_funds = this.value
        });
        $('#depfunds').on('click', function() {
            ajaxdata = {id: 'otdeposit', newfunds: deposit_funds};
            executeScript('../edit/saveAcctEdits.php', ajaxdata,
                "making deposit", deferred);
        });
        $close.on('click', function() {
            deferred.resolve();
            modal.close();
        });
    }
    // modal function executed when settings.id == 'xfr'
    function xfrfunds(deferred) {
        $('#transfer').after($close);
        $close.css('margin-left', '80px');
        var $from = $('#xfrfrom .fullsel');
        $from[0].id = 'from'
        var $to = $('#xfrto .fullsel');
        $to[0].id = 'to';
        var fromacct = getSelectValue($from[0]);
        var toacct = getSelectValue($to[0]);
        var xframt = 0;
        $(document).on('change', '#from', function() {
            fromacct = this.value;
        });
        $(document).on('change', '#to', function() {
            toacct = this.value;
        });
        $('#xframt').on('change', function() {
            xframt = this.value;
        });
        $('#transfer').on('click', function() {
            ajaxdata = {id: 'xfr', from: fromacct, to: toacct, sum: xframt};
            executeScript("../edit/saveAcctEdits.php", ajaxdata,
                "transferring funds", deferred);
        });
        $close.on('click', function() {
            deferred.resolve();
            modal.close();
        });
    }
    // modal function executed when settings.id == 'recon'
    function reconcile(deferred) {
        $('#usecard').after($close);
        $close.css('margin-left', '68px');
        var $ccbox = $('#ccsel0 .ccsel');
        $ccbox[0].id = 'reccd';
        var usecd = '';
        $('#reccd').on('change', function() {
            usecd = this.value;
        });
        $('#usecard').on('click', function() {
            if (usecd === '') {
                alert("You have not selected card to reconcile");
                return;
            }
            deferred.resolve();
            var recloc = "../utilities/reconcile.php?card=" + usecd;
            window.open(recloc, "_self");
            modal.close();
        });
        $close.on('click', function() {
            deferred.resolve();
            modal.close();
        });
    }
    // modal function executed when settings.id == 'setup_ap'
    function setupAutopay(deferred) {
        $('#perfauto').after($close);
        $close.css('margin-left', '130px');
        var $acctsel = $('#apsel .fullsel')  // used on three previous occasions
        var against = getSelectValue($acctsel[0]);
        $acctsel[0].id = 'sapacct';
        $('#sapacct').on('change', function() {
            against = this.value;
        });
        var $cardsel = $('#ccselap .allsel');
        var use = ''
        $cardsel[0].id = 'forap';
        $('#forap').on('change', function() {
            use = this.value;
        });
        var dom = '';
        $('#useday').on('change', function() {
            dom = this.value;
        });
        $('#perfauto').on('click', function() {
            if (use === '') {
                alert("You have not entered a payment method");
                return;
            } else if (dom === '') {
                alert("You have not entered a day-of-month for autopay");
                return;
            }
            ajaxdata = {id: 'apset', acct: against, method: use, day: dom};
            executeScript('../edit/saveAcctEdits.php', ajaxdata,
                'setting up autopay', deferred);
        });
        $close.on('click', function() {
            deferred.resolve();
            modal.close();
        });
    }
    // modal function executed when settings.id == 'del_ap'
    function deleteAutopay(deferred) {
        $('#remap').after($close);
        $close.css('margin-left', '30px');
        var $delacct = $('#delapacct .fullsel'); // four previous on page
        var dacct = getSelectValue($delacct[0]);
        $delacct[0].id = "dapay";
        $('#dapay').on('change', function() {
            dacct = this.value;
        });
        $('#remap').on('click', function() {
            ajaxdata = {id: 'delapay', acct: dacct};
            executeScript('../edit/saveAcctEdits.php', ajaxdata, 
                'deleting autopay', deferred);
        });
        $close.on('click', function() {
            deferred.resolve();
            modal.close();
        });
    }
    // modal function executed when settings.id = 'addcd'
    function addcard(deferred) {
        $('#newcd').after($close);
        $close.css('margin-left', '100px');
        var newcard = '';
        var newtype = $('#cdprops option:selected').text();
        $(document).on('change', '#cdprops', function() {
            newtype = this.value;
        });
        $('#cda').on('change', function() {
            newcard = this.value;
        });
        $('#newcd').on('click', function() {
            var ajaxdata = {id: 'addcd', cdname: newcard,
                cdtype: newtype};
            executeScript('../edit/saveAcctEdits.php', ajaxdata, 
                'adding new card', deferred);
        });
        $close.on('click', function() {
            deferred.resolve();
            modal.close();
        });
    }
    // modal function executed when settings.id = 'delcd'
    function deleteCrDr(deferred) {
        $('#godel').after($close);
        $close.css('margin-left', '54px');
        var $dc = $('#deletecard .allsel');
        var dc = '';
        $dc[0].id = "dcid";
        $('#dcid').on('change', function() {
            dc = this.value;
        });
        $('#godel').on('click', function() {
            if (dc === '') {
                alert("You have not selected a card to delete");
                return;
            }
            ajaxdata = {id: 'decard', target: dc};
            executeScript('../edit/saveAcctEdits.php', ajaxdata,
                'deleting Cr/Dr card', deferred);
        });
        $close.on('click', function() {
            deferred.resolve();
            modal.close()
        });
    }
    // modal function executed when settings.id == 'addacct'
    function newacct(deferred) {
        $('#addit').after($close);
        $close.css('margin-left', '170px');
        var newacct = "None";
        $newacct = $('#newacct');
        var monthly = 0;
        $monthly = $('#mo');
        $newacct.on('change', function() {
            newacct = this.value;
        });
        $monthly.on('change', function() {
            monthly = this.value;
        });
        $('#addit').on('click', function() {
            var ajaxdata = {id: 'addacct',
                    acct_name: newacct, monthly: monthly};
            executeScript('../edit/saveAcctEdits.php', ajaxdata,
                'adding new account', deferred);
        });
        $close.on('click', function() {
            deferred.resolve();
            modal.close();
        });
    }
    // modal function executed when settings.id == 'delacct'
    function delete_acct(deferred) {
        $('#delit').after($close);
        $close.css('margin-left', '48px');
        var $acct = $('#remacct .partsel');
        $acct[0].id = 'dacct';
        var todelete = getSelectValue($acct[0]);
        $('#dacct').on('change', function() {
            todelete = this.value;
        });
        $('#delit').on('click', function() {
            var ans = confirm("Are you sure? (if balance not $0, adjust other accts)");
            if (ans) { 
                var ajaxdata = {id: 'acctdel', acct: todelete};
                executeScript('../edit/saveAcctEdits.php', ajaxdata,
                    'deleting account', deferred);
            } else {
                alert("No action taken");
            }
        });
        $close.on('click', function() {
            deferred.resolve();
            modal.close();
        });
    }
    // modal function executed when settings.id == 'mvacct'
    function mvacct(deferred) {
        $('#mvit').after($close);
        $close.css('margin-left', '120px');
        var $mv = $('#mvfrom .partsel');
        $mv[0].id = 'takeacct';
        var mover = getSelectValue($mv[0]);
        var $above = $('#mvto .partsel');
        $above[0].id = 'above';
        var ontopof = getSelectValue($above[0]);
        $('#takeacct').on('change', function() {
            mover = this.value;
        });
        $('#above').on('change', function() {
            ontopof = this.value;
        });
        $('#mvit').on('click', function() {
            if (mover == ontopof) {
                alert("From and To are the same - no action taken");
            } else {
                var ajaxdata = {id: 'move', mvfrom: mover, mvto: ontopof};
                executeScript('../edit/saveAcctEdits.php', ajaxdata,
                    'moving account', deferred);
            }
        });
        $close.on('click', function() {
            deferred.resolve();
            modal.close();
        });
    }
    // modal function executed when settings.id == 'rename'
    function rename(deferred) {
        $('#ren').after($close);
        $close.css('margin-left', '40px');
        var $raccts = $('#asel .fullsel');
        $raccts[0].id = 'racct';
        var racct = getSelectValue($raccts[0]);
        $('#racct').on('change', function() {
            racct = this.value
        });
        var rname = $('#newname').val();
        $('#newname').on('change', function() {
            rname = this.value;
        });
        $('#ren').on('click', function() {
            var ajaxdata = {id: 'rename', newname: rname, acct: racct};
            executeScript('../edit/saveAcctEdits.php', ajaxdata,
                'renaming account', deferred);   
        });
        $close.on('click', function () {
            deferred.resolve();
            modal.close();
        });
    }
    // modal function executed when settings.id == 'edit_chg'
    function editCredit(inputvals, locater, cardinfo, defobj) {
        $('#svmodal').after($close);
        $close.css('margin-left', '40px');
        $close.css('margin-bottom', '12px');
        var pos = locater.offset();
        $modal.css({
            top: pos.top,
            left: 560
        });
        // place the goods into the modal:
        var $row = $content.find('table[id=modtbl] tr').eq(0);
        var $dropdown = $row.find('td').eq(1).children().eq(0);
        $dropdown.val(inputvals[0]);
        var $date = $content.find('input[id=de]');
        var $payee = $content.find('input[id=pay]');
        var $amt = $content.find('input[id=namt]');
        $date.attr('value', inputvals[1]);
        $payee.attr('value', inputvals[2]);
        $amt.attr('value', inputvals[3]);
        var ajaxsel = inputvals[0];
        var ajaxdte = inputvals[1];
        var ajaxpay = inputvals[2];
        var ajaxamt = inputvals[3];
        // look for value changes
        $('#modsel').on('change', function() {
            ajaxsel = $('#modsel option:selected').val();
        });
        $date.on('change', function() {
            ajaxdte = $(this).val();
        });
        $payee.on('change', function() {
            ajaxpay = $(this).val();
        });
        $amt.on('change', function() {
            ajaxamt = $(this).val()
        });
        $close.on('click', function() {
            locater.children().each(function() {
                $(this).css('background-color', 'white');
            });
            locater.find('input').prop('checked', false);
            defobj.resolve();
            modal.close();
        });
        $('#svmodal').on('click', function() {
            var ajaxdata = {cno: cardinfo.cdno, item: cardinfo.itemno, 
                chg: ajaxsel, date: ajaxdte, payee: ajaxpay, amount: ajaxamt};
            executeScript('../edit/saveEditedCharge.php', ajaxdata, 
                'editing charges', deferred);
        });
    }
    // modal function executed when settings.id == 'morpt'
    function monthly(deferred) {
        $('#genmo').after($close);
        $close.css('margin-left', '80px');
        var $mosel = $('#rptmo');
        var mosel = getSelectValue($mosel[0]);
        $('#rptmo').on('change', function() {
            mosel = this.value;
        });
        $('#genmo').on('click', function() {
            var getdata = '../utilities/reports.php?id=morpt&mo=' + mosel;
            window.open(getdata, "_self");
        });
        $close.on('click', function () {
            deferred.resolve();
            modal.close();
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
                padding: '8px',
                top: 140,
                left: 640
            }).appendTo('body');
            var modal_box = $modal[0];
            dragElement(modal_box);
            // separate code for each form
            if (modid === 'login') {
                getpass(settings.usr);
            } else if (modid == 'usrmail') {
                userName(settings.deferred);
            } else if (modid === 'autopay') {
                autopay(settings.method, settings.acct_name, settings.deferred);
            } else if (modid === 'expense') {
                payExpense(settings.deferred);
            } else if (modid === 'income') {
                distribute(settings.deferred);
            } else if (modid === 'deposit') {
                makeDeposit(settings.deferred);
            } else if (modid === 'xfr') {
                xfrfunds(settings.deferred);
            } else if (modid === 'recon') {
                reconcile(settings.deferred);
            } else if (modid === 'setup_ap') {
                setupAutopay(settings.deferred);
            } else if (modid === 'del_ap') {
                deleteAutopay(settings.deferred);
            } else if (modid === 'addcd') {
                addcard(settings.deferred);
            } else if (modid === 'delcard') {
                deleteCrDr(settings.deferred);
            } else if (modid === 'addacct') {
                newacct(settings.deferred);
            } else if (modid === 'delacct') {
                delete_acct(settings.deferred);
            } else if (modid === 'mvacct') {
                mvacct(settings.deferred);
            } else if (modid === 'rename') {
                rename(settings.deferred);
            } else if (modid === 'edit_chg') {
                editCredit(settings.ivals, settings.chgitem, 
                    settings.chgid, settings.deferred);
            } else if (modid === 'morpt') {
                monthly(settings.deferred);
            }
        },
        close: function() {
            $content.empty();
            $modal.detach();
            $(window).off('resize', modal.center);
        }
    };
}());  // modal is an IIFE
// all modals are draggable:
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