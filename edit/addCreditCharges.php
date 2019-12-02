
<?php
/**
 * This script is available to the user to add outstanding (unpaid) Credit
 * card charges that were skipped or forgotten during new budget creation.
 * PHP Version 7.1
 *
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to Date
 */
$user = filter_input(INPUT_GET, 'user');
require "../utilities/getCards.php";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title>New Credit Card Charges</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="description"
        content="Rolling 4-month budget tracker" />
    <meta name="author" content="Ken Cowles" />
    <meta name="robots" content="nofollow" />
    <link href="../styles/standards.css" type="text/css" rel="stylesheet" />
    <style type="text/css">
        #mainpg { margin-left: 16px; }
        #back   { margin-left: 80px; }
        #save, #back { margin-bottom: 4px; }
        input  {height: 20px; font-size: 14px; }
        .dates { width: 100px; }
        .amt   { width: 80px; }
        .pay   { width: 200px; }
    </style>
</head>

<body>
<div id="mainpg">
    <p id="user" style="display:none;"><?= $user;?></p>
    <span class="LargeHeading">You may add credit card charges that were
        forgotten or skipped during creation of your budget. Enter any
        outstanding/unpaid charges below.</span><br /><br />
    <form id="cdform" method="POST" action="saveNewCharges.php">
        <input type="hidden" name="user" value="<?= $user;?>" />
        <input type="hidden" name="addons" value="Y" />
        <button id="save">Save Charges</button>
        <button id="back">Return to Budget</button><br />
        <span class="SmallHeading">Note: When you select "Save Charges",
            the data you entered will be saved, and new entries will be
            available.</span><br /><br />
        <div id="enew">
            <span class="NormalHeading">Enter your new expense information
                below. (Outstanding/Unpaid Charges Only)</span><br /><br />
            <?php for ($y=0; $y<count($cr); $y++) : ?>
            <span class="SmallHeading">Add outstanding charges 
                for <span style="color:brown;"><?= $cr[$y];?></span>:</span>
                <input type="hidden" name="cname[]" value="<?= $cr[$y];?>" />
                <div id="data">
                <?php for ($z=0; $z<4; $z++) : ?>
                Date Expense Entered (Use: yyyy-mm-dd) <input class="dates" 
                    type="input" name="edate[]" />
                Amount Paid: <input class="amt" type="text"
                    name="eamt[]" />&nbsp;&nbsp;
                Payee: <input class="pay" type="text" name="epay[]" /><br />
                <?php endfor; ?>
                </div>
            <?php endfor; ?>
        </div>
        <div id="entered">
        </div>
    </form>
</div>

<script src="../scripts/jquery-1.12.1.js" type="text/javascript"></script>
<script type="text/javascript">
    $('#back').on('click', function(ev) {
        ev.preventDefault();
        var home = "../main/displayBudget.php?user=" + $('#user').text();
        window.open(home, "_self")
    });

</body>

</html>