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
    $('table').each(function(i) {
        if (selinits[i] !== '') {
            let inits = selinits[i].split("|");
            let $selects = $(this).find('select');
            $selects.each(function(j) {
                $(this).val(inits[j]);
                let selname = 'cr' + i + 'chgd[]';
                $(this).attr('name', selname)
            });
        }
    });
});