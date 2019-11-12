<?php
/** 
 * This script presents a form in which the user can enter or edit credit and/or 
 * debit card infomation. If there is currently no file, the user was able
 * to choose not to create one and also not be reminded every time the budget
 * is opened. The flag 'num' == 0 is used to determine how to proceed.
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
require "../utilities/getCrData.php";

$create = filter_input(INPUT_GET, 'num');
// 0 implies nothing entered, don't bother me the next time budget is opened
if ($create === '0') {
    // this is script segment should only be executed once
    $cdfile = fopen($credit_data, "w");
    $nodata = array('None');
    fputcsv($cdfile, $nodata);
    fclose($cdfile);
    header("Location: budget.php");
} else {
    $card_rec = 0;
    $oldcard = [];
    $oldtype = [];
    for ($m=0; $m<6; $m++) {
        $oldcard[$m] = "";
        $oldtype[$m] = "";
    }
    if (file_exists($credit_data)) {
        // existing data (if any) for edit updates; if 'None', create new via form
        $cdfile = fopen($credit_data, "r");
        $card_dat = fgetcsv($cdfile);
        $card_dat = cleanupExcel($card_dat);
        if ($card_dat[0] !== 'None') { 
            for ($n=0; $n<count($card_dat); $n+=2) {
                $oldcard[$card_rec] = $card_dat[$n];
                $oldtype[$card_rec] = $card_dat[$n+1];
                $card_rec++;
            }
        }
        fclose($cdfile);
        $new_cards = 6 - $card_rec;
    } // otherwise a new file will be created by this form
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title>Credit/Debit Card Setup</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="description"
        content="Rolling 4-month budget tracker" />
    <meta name="author" content="Ken Cowles" />
    <meta name="robots" content="nofollow" />
    <link href="../styles/standards.css" type="text/css" rel="stylesheet" />
    <style type="text/css">
        input {height:18px; font-size:14px;}
    </style>
    <script src="../scripts/jquery-1.12.1.js"></script>
</head>

<body>

<form style="margin-left:24px;" id="cards"
        action="saveCrDrInfo.php" method="POST">
    <span class="NormalHeading">Edit/Enter up to six credit and/or debit
        cards below:</span><br />
        You may enter/edit current charges against these accounts separately
        by selecting "Account Management Tools->Edit Credit Charges" on the
        budget tracking home page.<br /><br />
    <?php if ($card_rec > 0) : ?>   
        <?php for($i=0; $i<$card_rec; $i++) : ?>
            Unique Card Name: <input type="text" name="card[]"
                value="<?= $oldcard[$i]; ?>" />
            <p id="type<?= $i;?>" style="display:none"><?= $oldtype[$i];?></p>
            &nbsp;&nbsp;Card Type: <select id="sel<?= $i;?>" name="ctype[]">
                <option value="Credit">Credit Card</option>
                <option value="Debit">Debit Card</option>
            </select><br /><br />
        <?php endfor; ?>
    <?php endif; ?>
    <?php if ($new_cards > 0) : ?>
        <?php for ($j=$card_rec; $j<6; $j++) : ?>
            Unique Card Name: <input type="text" name="card[]"
                value="" />&nbsp;&nbsp;Card Type:
            <select name="ctype[]">
                <option value="Credit">Credit Card</option>
                <option value="Debit">Debit Card</option>
            </select><br /><br />
        <?php endfor; ?>
    <?php endif; ?>

    <button id="save">Save Data</button>
    <span id="done" style="position:relative;left:40px;">
        <button>Done</button>&nbsp;&nbsp;(Return to Budget 
            Tracking home page)</span>
</form>

<script type="text/javascript">
    var no_of_cards = parseInt(<?= $card_rec;?>);
    if (no_of_cards > 0) {
        for(var i=0; i<no_of_cards; i++) {
            var selid = "#sel" + i;
            var cdtype = "#type" + i;
            var content = $(cdtype).text();
            $(selid).val(content);
        }
    }
    $('#done').on('click', function(ev) {
        ev.preventDefault();
        window.open("../main/budget.php", "_self");
    });
</script>

</body>

</html>