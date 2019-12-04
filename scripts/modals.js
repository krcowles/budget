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
    // modal function used to get user's password
    function getpass(usrname) {
        var logpos = $('#login').offset();
        var logtop = logpos.top - 4;
        var logwd = logpos.left + $modal.width() + 30;
        $modal.css({
            top: logtop,
            left: logwd
        });
        $('#moduser').val(usrname);
        document.getElementById("passin").focus(); 
        $('form').submit(function(ev) {
            ev.preventDefault();
            passwd = $('#passin').val();
            validateUser(usrname, passwd);
        });
    }
    // general purpose function to execute ajax on input arguments
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
        var charge = getSelectValue($selcd[0]);
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
            var ajaxdata = {id: 'payexp', user: g_user, acct_name: acctname,
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
            var ajaxdata = {id: 'income', user: g_user, funds: funds};
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
            ajaxdata = {id: 'otdeposit', user: g_user, newfunds: deposit_funds};
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
            ajaxdata = {id: 'xfr', user: g_user, 
                from: fromacct, to: toacct, sum: xframt};
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
        var $ccbox = $('#ccsel0 .ccsel');
        $ccbox[0].id = 'reccd';
        $close.css('margin-left', '76px');
        var usecd = getSelectValue($ccbox[0]);
        $('#reccd').on('change', function() {
            usecd = this.value;
        });
        $('#usecard').on('click', function() {
            deferred.resolve();
            var recloc = "../utilities/reconcile.php?user=" + g_user 
                + "&card=" + usecd;
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
        var $cardsel = $('#ccselap .allsel');  // also used in Pay Expense
        var use = getSelectValue($cardsel[0]);
        $cardsel[0].id = 'forap';
        $('#forap').on('change', function() {
            use = this.value;
        });
        var dom = $('#useday').text();
        $('#useday').on('change', function() {
            dom = this.value;
        });
        $('#perfauto').on('click', function() {
            ajaxdata = {id: 'apset', user: g_user, acct: against,
                method: use, day: dom};
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
            ajaxdata = {id: 'delapay', user: g_user, acct: dacct};
            executeScript('../edit/saveAcctEdits.php', ajaxdata, 
                'deleting autopay', deferred);
        });
        $close.on('click', function() {
            deferred.resolve();
            modal.close();
        });
    }
    // modal function executed when settings.id = 'aadcd'
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
            var ajaxdata = {id: 'addcd', user: g_user, 
                cdname: newcard, cdtype: newtype};
            executeScript('../edit/saveAcctEdits.php', ajaxdata, 
                'adding new card', deferred);
        });
        $close.on('click', function() {
            deferred.resolve();
            modal.close();
        });
    }
    function deleteCrDr(deferred) {
        $('#godel').after($close);
        $close.css('margin-left', '54px');
        var $dc = $('#deletecard .allsel');
        var dc = getSelectValue($dc[0]);
        $dc[0].id = "dcid";
        $('#dcid').on('change', function() {
            dc = this.value;
        });
        $('#godel').on('click', function() {
            ajaxdata = {id: 'decard', user: g_user, target: dc};
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
            var ajaxdata = {id: 'addacct', user: g_user,
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
    function delacct(deferred) {
        $('#delit').after($close);
        $close.css('margin-left', '48px');
        var $acct = $('#delacct .partsel');
        $acct[0].id = 'dacct';
        var todelete = getSelectValue($acct[0]);
        $('#dacct').on('change', function() {
            todelete = this.value;
        });
        $('#delit').on('click', function() {
            var ans = confirm("Are you sure (is balance $0)?");
            if (ans) { 
                var ajaxdata = {id: 'acctdel', user: g_user,
                    acct: todelete};
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
                var ajaxdata = {id: 'move', user: g_user, mvfrom: mover,
                    mvto: ontopof};
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
            var ajaxdata = {id: 'rename', user: g_user,
            newname: rname, acct: racct};
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
                chg: ajaxsel, date: ajaxdte, payee: ajaxpay, amount: ajaxamt}
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
                        location.reload();
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
                delacct(settings.deferred);
            } else if (modid === 'mvacct') {
                mvacct(settings.deferred);
            } else if (modid === 'rename') {
                rename(settings.deferred);
            } else if (modid === 'edit_chg') {
                editCredit(settings.ivals, settings.chgitem, 
                        settings.chgid, settings.def);
            }  else if (modid === 'autopay') {
                autopay(settings.deferred);
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