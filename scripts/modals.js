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
                method: "GET",
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
    // modal function executed when settings.id == 'expense'
    function payExpense(deferred) {
        $content.append($close);
        $close.css('margin-left', '216px');
        $close.text("Cancel");
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
            executeScript('../edit/saveAcctEdits.php', ajaxdata, 'making payment', deferred);
        });
        $close.on('click', function() {
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
    // modal function executed when settings.id == 'income'
    function distribute(deferred) {
        $('#dist').after($close);
        $close.css('margin-left', '150px');
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
                       deferred.resolve();
                       location.reload();
                   }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    deferred.resolve();
                    var msg = "Problem encountered distributing income:" +
                     " Error " + errorThrown + "; " + textStatus;
                    alert(msg);
                }
            });
            modal.close();
        });
        $close.on('click', function () {
            deferred.resolve();
            modal.close();
        });
    }
    // modal function executed when settings.id == 'rename'
    function nameit() {
        $modal.css({
            top: 140,
            left: 300
        });
        $('#doit').after($close);
        $close.css('margin-left', '120px');
        var acct_name = $('#selacct option:selected').text();
        $(document).on('change', '#selacct', function() {
            acct_name = this.value;
        });
        $newname = $('#newname');
        var newname = "None";
        $newname.on('change', function() {
            newname = this.value;
        });
        $('#doit').on('click', function() {
            var ajaxdata = {newname: newname, acct_name: acct_name};
            $.ajax({
                url: "../edit/saveAcctEdits.php",
                method: "GET",
                dataType: "text",
                data: ajaxdata,
                success: function(results) {
                    if (results === "OK") {
                        modal.close();
                        location.reload();
                    } else {
                        alert("Problem renaming account");
                        modal.close();
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    msg = "Problem encountered renaming account: " +
                        textStatus + "; Error: " + errorThrown;
                    alert(msg);
                    modal.close();
                }
            });
        });
        $close.on('click', function () {
            modal.close();
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
            var ajaxdata = {acct_name: newacct, monthly: monthly};
            $.ajax({
                url: '../edit/saveAcctEdits.php',
                method: "GET",
                data: ajaxdata,
                dataType: "text",
                success: function(results) {
                    if (results == "OK") {
                        deferred.resolve();
                        modal.close();
                        location.reload();
                    } else {
                        alert("Problem encounted adding account");
                        deferred.resolve();
                        modal.close();
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    msg = "Problem encountered adding account: " +
                        textStatus + "; Error: " + errorThrown;
                    alert(msg);
                    deferred.resolve();
                }
            });
        });
        $close.on('click', function() {
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
            ajaxdata = {newfunds: deposit_funds};
            $.ajax({
                url: "../edit/saveAcctEdits.php",
                method: "GET",
                dataType: "text",
                data: ajaxdata,
                success: function(results) {
                    if (results === "OK") {
                        deferred.resolve();
                        modal.close();
                        location.reload();
                    } else {
                        deferred.resolve();
                        alert("Problem encountered making deposit");
                        modal.close();
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    deferred.resolve();
                    msg = "Problem encountered making deposit: " +
                        textStatus + "; Error: " + errorThrown;
                    alert(msg);
                    modal.close();
                }
            });
        });
        $close.on('click', function() {
            deferred.resolve();
            modal.close();
        });
    }
    // modal function executed when settings.id == 'xfr'
    function xfrfunds(from, to, deferred) {
        $('#xfrfrom').append(from);
        $('#xfrto').append(to);
        $('#transfer').after($close);
        $close.css('margin-left', '80px');
        var acct_name = $('#selacct option:selected').text();
        var to_acct = $('#second option:selected').text();
        var xframt = 0;
        $(document).on('change', '#selacct', function() {
            acct_name = this.value;
        });
        $(document).on('change', '#second', function() {
            to_acct = this.value;
        });
        $('#xframt').on('change', function() {
            xframt = this.value;
        });
        $('#transfer').on('click', function() {
            ajaxdata = {from: acct_name, to: to_acct, sum: xframt};
            $.ajax({
                url: '../edit/saveAcctEdits.php',
                data: ajaxdata,
                dataType: "text",
                method: "GET",
                success: function(results) {
                    if (results === "OK") {
                        deferred.resolve();
                        modal.close();
                        location.reload();
                    } else {
                        deferred.resolve();
                        alert("Problem encountered transferring funds");
                        modal.close();
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    deferred.resolve();
                    msg = "Problem encountered making deposit: " +
                        textStatus + "; Error: " + errorThrown;
                    alert(msg);
                    modal.close();
                }
            });
        });
        $close.on('click', function() {
            deferred.resolve();
            modal.close();
        });
    }
    // modal function executed when settings.id == 'mvacct'
    function mvacct() {
        $('#mvit').after($close);
        $close.css('margin-left', '200px');
        $modal.css({
            top: 60,
            left: 700
        });
        var acct_name = $('#fromlist option:selected').text();
        var above_acct = $('#tolist:selected').text();
        $(document).on('change', '#fromlist', function() {
            acct_name = this.value;
        });
        $(document).on('change', '#tolist', function() {
            above_acct = this.value;
        });
        $('#mvit').on('click', function() {
            var ajaxdata = {mvfrom: acct_name, mvto: above_acct};
            $.ajax({
                url: '../edit/saveAcctEdits.php',
                method: "GET",
                data: ajaxdata,
                dataType: "text",
                success: function(results) {
                    if (results === "OK") {
                        modal.close()
                        location.reload();
                    } else {
                        alert("Problem encountered moving account");
                        modal.close();
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    msg = "Problem encountered moving account: " +
                        textStatus + "; Error: " + errorThrown;
                    alert(msg);
                    modal.close();
                }
            });
        });
        $close.on('click', function() {
            modal.close();
        });
    }
    // modal function executed when settings.id == 'delacct'
    function delacct() {
        $('#delit').after($close);
        $close.css('margin-left', '128px');
        $modal.css({
            top: 60,
            left: 700
        });
        var subj_acct = $('#fromlist option:selected').text();
        $(document).on('change', '#fromlist', function() {
            subj_acct = this.value;
        });
        $('#delit').on('click', function() {
            var ajaxdata = {deletion: subj_acct};
            $.ajax({
                url: '../edit/saveAcctEdits.php',
                method: "GET",
                data: ajaxdata,
                dataType: "text",
                success: function(results) {
                    if (results === "OK") {
                        modal.close()
                        location.reload();
                    } else {
                        alert("Problem encountered deleting account");
                        modal.close();
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    msg = "Problem encountered deleting account: " +
                        textStatus + "; Error: " + errorThrown;
                    alert(msg);
                    modal.close();
                }
            })
        });
        $close.on('click', function() {
            modal.close();
        });
    }
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
            var ajaxdata = {cdnme: newcard, cdtyp: newtype};
            executeScript('../edit/cardEdits.php', ajaxdata, 'adding new card', deferred);
        });
        $close.on('click', function() {
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
            } else if (modid === 'expense') {
                payExpense(settings.deferred);
            } else if (modid === 'deposit') {
                makeDeposit(settings.deferred);
            } else if (modid === 'edit_chg') {
                editCredit(settings.ivals, settings.chgitem, 
                        settings.chgid, settings.def);
            } else if (modid === 'autopay') {
                autopay(settings.method, settings.acct_name, settings.row_no);
            } else if (modid === 'income') {
                distribute(settings.deferred);
            } else if (modid === 'rename') {
                nameit();
            } else if (modid === 'addacct') {
                newacct(settings.deferred);
            } else if (modid === 'xfr') {
                xfrfunds(settings.from, settings.to, settings.deferred);
            } else if (modid === 'mvacct') {
                mvacct();
            } else if (modid === 'delacct') {
                delacct();
            } else if (modid === 'addcd') {
                addcard(settings.deferred);
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