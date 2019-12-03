
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
require "../utilities/getAccountData.php";
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
    <p id="cdcnt" style="display:none;"><?= count($cr);?></p>
    <span class="LargeHeading">You may add credit card charges that were
        forgotten or skipped during creation of your budget.</span><br />
        <span class="SmallHeading">When you
        'Save Charges', new charge entry boxes will appear. Enter any
        outstanding/unpaid charges below. Note: you may review/edit
        your new charges from the main budget page - they will not be
        shown here after saving.</span><br /><br />
    <form id="cdform" method="POST" action="saveCreditAdds.php">
        <input type="hidden" name="user" value="<?= $user;?>" />
        <button id="save">Save Charges</button>
        <button id="back">Return to Budget</button><br />
        <span class="SmallHeading">Note: When you select "Save Charges",
            the data you entered will be saved, and new entries will be
            available (saved entries will not be displayed).</span><br /><br />
        <div>
            <?php for ($y=0; $y<count($cr); $y++) : ?>
                <span class="SmallHeading">Add outstanding charges for
                    <span style="color:brown;"><?= $cr[$y];?></span>
                </span>:
                <div class="data">
                <?php for ($z=0; $z<4; $z++) : ?>
                    Date of Charge (yyyy-mm-dd): <input class="dates" 
                        type="input" name="ndate[]" />
                    Amount Paid: <input class="amt" type="text"
                        name="namt[]" />&nbsp;&nbsp;
                    Payee: <input class="pay" type="text" name="npay[]" />
                    Deduct from: <span id="cd<?= $y;?>it<?= $z;?>">
                    <?= $fullsel;?></span><br />
                <?php endfor; ?>
                </div><br />
            <?php endfor; ?>
        </div>
    </form>
</div>

<script src="../scripts/jquery-1.12.1.js" type="text/javascript"></script>
<script src="../scripts/addCreditCharges.js" type="text/javascript"></script>
    
</body>

</html>