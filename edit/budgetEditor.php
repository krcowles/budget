<?php
/**
 * This module can use this script to make edits to the budget data.
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
session_start();
require_once "../utilities/getAccountData.php";
require_once "../utilities/timeSetup.php";
$old_bud = array_sum($budgets);
$old_bud = "$" . number_format($old_bud, 0, ".", ",");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Budget Editor</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="description"
        content="Rolling 3-month budget tracker" />
    <meta name="author" content="Ken Cowles" />
    <meta name="robots" content="nofollow" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="../styles/bootstrap.min.css" type="text/css" rel="stylesheet" />
    <link href="../styles/modals.css" type="text/css" rel="stylesheet" />
    <link href="../styles/budgetEditor.css" type="text/css" rel="stylesheet" />
    <style>
        td { padding-bottom: 0px; }
    </style>
</head>

<body>
<?php require "../main/navbar.php"; ?>
<div id="page">
<h3>Note: If you make changes,
    be sure to 'Save Edits'</h3>
<h4>You can add, delete, or rename accounts separately 
    using the menu: <em>Budget Mgr</em></h4>
<form id="form" action="saveBudgetEdits.php" method="post">
    <div>
        <button id="save" class="btn btn-secondary" type="button">
                Save Edits</button>
        <br /><br />
        <table id="bud_mgr">
            <thead>
                <tr>
                    <th>Budget Item</th>
                    <th>Monthly Budget</th>
                    <th>Current Balance</th>
                </tr>
            </thead>
            <tbody>
            <?php for ($j=0; $j<count($account_names); $j++) : ?>
            <tr>
                <td><?= $account_names[$j];?></td>
                <td ><textarea class="monitor" rows="1" cols="8"
                    name="edbud[]"><?=$budgets[$j];?></textarea></td>
                <td><textarea rows="1" cols="14"
                    name="edcurr[]"><?=$current[$j];?></textarea></td>
            </tr>
            <?php endfor; ?>
            </tbody>
        </table>
        <div id="budsum"><span>Total Monthly Allocation: </span>
            <span id="allocation"><?=$old_bud;?></span><br />
        </div>
    </div>
</form>
</div>
<?php require "../main/bootstrapModals.html"; ?>

<script src="https://unpkg.com/@popperjs/core@2.4/dist/umd/popper.min.js"></script>
<script src="../scripts/bootstrap.min.js"></script>
<script src="../scripts/jquery.min.js"></script>
<script src="../scripts/menus.js"></script>
<script>
    const formatter = new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
        minimumFractionDigits: 0
    });
    $('#edbuds').addClass('active');
    $('#save').on('click', function() {
        $('form').trigger('submit');
    });
    var tbl_pos = $('#bud_mgr').offset();
    var twidth = $('#bud_mgr').width() + 60;
    $('#budsum').css({
        top: tbl_pos.top + 40,
        left: twidth
    });
    $('.monitor').each(function() {
            $(this).on('change', function(ev) {
                var curbud = parseInt($(this).text());
                var newbud = parseInt(ev.target.value);
                var delta  = newbud - curbud;
                var cursum = $('#allocation').text().substr(1);
                var cursum_cnvt = parseInt(cursum.replace(/[^\d\.]/, ''));
                var newsum = cursum_cnvt + delta;
                var sumtxt = formatter.format(newsum)
                $('#allocation').html(sumtxt);
            });
        }
    );
</script>

</body>

</html>
