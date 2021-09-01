$(function () {
    $('.datepicker').datepicker({
        dateFormat: 'yy-mm-dd'
    });
    var $amount = $('.amt');
    scaleTwoNumber($amount);
    $('#exp4').addClass('active');
    $('#svchgs').on('click', function() {
        $('form').trigger('submit');
    });
    if (selinits.length > 0) {
        $('table.sortable').each(function(i) {
            if (selinits[i] !== '') {
                let sel_vals = selinits[i];
                let inits = sel_vals.split("|");
                let $selects = $(this).find('select');
                $selects.each(function(j) {
                    this.id = 'sel' + j;
                    $(this).val(inits[j]);
                    let selname = 'cr' + i + 'chgd[]';
                    $(this).attr('name', selname)
                });
            }
        });
    }
});