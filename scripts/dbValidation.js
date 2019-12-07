/**
 * NOTE: the 'change' event seems to trigger PRIOR TO the DOM focus changing,
 * therefore it became necessary to re-focus on the changed element AFTER the
 * change event processing is finished - hence the setTimeout function.
 */ 
function integerValue(jqClass) {
    jqClass.each(function(indx) {
        var tmpid = "nbud" + indx;
        this.id = tmpid;
        var jqid = "#" + tmpid;
        $(this).on('change', function() {
            var badInt = false;
            var entry = $(this).val();
            var amt = +entry;
            if (isNaN(amt)) {
                badInt = true;   
            } else {
                if (!Number.isInteger(amt)) {
                    badInt = true;
                }
            }
            if (badInt) {
                alert("Please use whole numbers only (dollars, no cents)");
                $(this).val('');
                setTimeout(function() {
                    $(jqid).get(0).focus();
                    badInt = false;
                }, 5);
            }
        });
    });
}
function scaleTwoNumber(jqClass) {
    jqClass.each(function(indx) {
        var badNo = false;
        var tid = "nbal" + indx;
        var jq = "#" + tid;
        this.id = tid;
        $(this).on('change', function() {
            var entry = $(this).val();
            var amt = +entry;
            if (isNaN(amt)) {
                badNo = true;
            } else {
                var decpos = entry.indexOf('.');
                if (decpos !== -1 && entry.length - (decpos + 1) > 2) {
                    badNo = true;
                }
            }
            if (badNo) {
                alert("Nummeric entries only: 2 decimals places max");
                $(this).val('');
                setTimeout(function() {
                    $(jq).get(0).focus();
                    badNo = false;
                }, 5);
            }
        });
    });
}
