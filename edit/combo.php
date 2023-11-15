<?php
/**
 * This page will either create a new non-monthlies account, or
 * if one already exists, it will allow the user to edit it.
 * Note that any existing data which can be edited requires MySQL
 * update, and new data provided will require MySQL insert, hence
 * these two tables will have different POSTing names.
 * PHP Version 7.4
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
session_start();
$user = $_SESSION['userid'];
require "../database/global_boot.php";
require "../utilities/timeSetup.php";
require "../utilities/getAccountData.php";
require "../utilities/getCards.php";

/**
 * This section contains 4 html string definitions for the various
 * <select>s used on the page. The arrays will be filled from these
 * assignments
 */
// define payment frequency option <selects>s
$old_payopts = '<select class="opayfreq" name="ofreq[]">';
$new_payopts = '<select class="npayfreq" name="nfreq[]">';
$payopts = <<<FREQOPTS
    <option value="Select Frequency">Select Frequency</option>
    <option value="Bi-Annually">Every Other Year</option>
    <option value="Annually">Annually</option>
    <option value="Semi-Annually">Semi-Annually</option>
    <option value="Quarterly">Quarterly</option>
    <option value="Bi-Monthly">Every Other Month</option>
</select>
FREQOPTS;
$old_payopts .= $payopts;
$new_payopts .= $payopts;

// define month <select> drop-downs
$old_opts = '<select class="omonth" name="ofirst[]">';
$new_opts = '<select class="nmonth" name="nfirst[]">';
$opts = '<option value="99">Month</option>';
for ($i=0; $i<12; $i++) {
    $opts .= '<option value="' . $month_names[$i] . '">' .
            $month_names[$i] . '</option>';
}
$opts .= '</select'>
$old_opts .= $opts;
$new_opts .= $opts;

// define alternate year choice <selects>
$eyears = '<select class="oyears" name="oyrs[]">';
$nyears = '<select class="nyears" name="eyrs[]">';
$altyrs = <<<ALTYRS
    <option value="Odd/Even?">Odd/Even?</option>
    <option value="Odd">Odd yrs</option>
    <option value="Even">Even yrs</option>
</select>
ALTYRS;
$eyears .= $altyrs;
$nyears .= $altyrs;

// define autopay selects
$eapname = 'class="old_ap" name="oap[]" ';
$napname = 'class="new_ap" name="nap[]" ';
$eap = substr_replace($allCardsHtml, $eapname, 8, 0);
$eap = str_replace("SELECT ONE", "SELECT", $eap);
$eap = str_replace(
    '<option value="Check or Draft">Check or Draft</option>', '', $eap
);
$nap = substr_replace($allCardsHtml, $napname, 8, 0);
$nap = str_replace("SELECT ONE", "SELECT", $nap);
$nap = str_replace(
    '<option value="Check or Draft">Check or Draft</option>', '', $nap
);
/**
 * Get the user's data from the database
 */
$itemReq = "SELECT * FROM `Irreg` WHERE `userid`=?";
$current = $pdo->prepare($itemReq);
$current->execute([$user]);
$items = $current->fetchAll();
$noOfItems = count($items); // items already in database
if ($noOfItems > 0) {
    $action = "edit or add items to your current 'Non-Monthly Expenses' account";
    $saving = "Return to Budget";
} else {
    $action = "create a list of non-monthly expenses to track in a single account";
    $saving = "Add to Budget";
}

/**
 * Prepare data for display in tables
 * NOTE: 'newbies' will require id's to detect if row is properly filled
 */

// javascript arrays holding current <select> values
$ofreq    = [];
$omonth   = [];
$osa      = [];
$aptype   = [];
// days corresponding to specified autopays [ap]] 
$apdayval = []; 

$napid = "id='nap0' ";  // 'new row' table
$newap = substr_replace($allCardsHtml, $napid, 8, 0);  // new row table

/**
 * Parse user's data for display in editable table and retrieve data
 * for display of 'current state' at the bottom of the page.
 */
$next_dues = []; 
$waityr = [];
for ($k=0; $k<$noOfItems; $k++) {
    $waityr[$k] = false;
}
for ($i=0; $i<$noOfItems; $i++) {
    $ofreq[$i] = $items[$i]['freq'];
    $omonth[$i] = $items[$i]['first'];
    $osa[$i] = empty($items[$i]['SA_yr']) ? "" : $items[$i]['SA_yr'];
    $aptype[$i] = empty($items[$i]['APType']) ? "" : $items[$i]['APType'];
    $apdayval[$i] = $items[$i]['APDay'] == '0' ? "" : $items[$i]['APDay'];
    $stats = prepNonMonthly(
        $items[$i]['freq'], $items[$i]['first'], $items[$i]['amt'],
        $items[$i]['SA_yr'], $items[$i]['mo_pd'], $items[$i]['yr_pd'],
        $month_names, $thismo, $thisyear
    );
    $waityr[$i]    = $stats[1];
    $next_dues[$i] = $stats[2];
}
// prep arrays for import by javascript
$js_freq  = json_encode($ofreq);
$js_month = json_encode($omonth);
$js_sayr  = json_encode($osa);
$js_type  = json_encode($aptype);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Non-Monthly Expense Account</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="description"
        content="Plan and Manage non-monthly expenses" />
    <meta name="author" content="Ken Cowles" />
    <meta name="robots" content="nofollow" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="../styles/bootstrap.min.css" type="text/css" rel="stylesheet" />
    <link href="../styles/combo.css" type="text/css" rel="stylesheet" />
</head>

<body>
<div class="content">
    <form method="post" action="saveCombo.php">
        <h4>This page allows you to <?=$action;?>. The following data must be
        entered or modified - no field is optional. Rows will be added automatically.
        Please select the 'Save' button to save the results and place/keep the
        account on your budget page. You can exit without saving by selecting the
        "Return: Don't Save" button
        </h4>

        <button id="review" type="submit" class="btn btn-success">
            Save and Review Edits</button>
        <button id="savit" type="button" class="btn btn-success">
            Save / <?=$saving;?></button>
        <button id="nosave" type="button" class="btn btn-secondary">
            Return: Don't Save</button><br /><br />
        <input id="return_type" type="hidden" name="return_type"
            value="" />
        <input id="newbies" type="hidden" name="newvals" value="0" />

        <p id="oldcnt" style="display:none;"><?=$noOfItems;?></p>
        <?php if ($noOfItems > 0) : ?>
        <div>
            <h5>The following items may be edited:</h5>
        </div>
        <table id="old_entries">
            <colgroup>
                <col span="1" style="width: 24%;">
                <col span="1" style="width: 18%;">
                <col span="1" style="width: 8%;">
                <col span="1" style="width: 14%;">
                <col span="1" style="width: 13%;">
                <col span="1" style="width: 13%;">
                <col span="1" style="width: 4%;">
                <col span="1" style="width: 6%;">
            </colgroup>
            <thead>
                <tr>  
                    <th>Expense Item</th>
                    <th>Occurrence</th>
                    <th>Each<br />Payment</th>
                    <th>[1st] Pay Month</th>
                    <th>Paymnt Years</th>
                    <th class="rms">AutoPay<br />With</th>
                    <th style="padding:4px;">Day</th>
                    <th class="rms">Delete</th>
                </tr>
            </thead>
            <tbody>
                <?php for ($i=0; $i<$noOfItems; $i++) : ?>
                <tr id="old<?=$i;?>"class="itemrow">
                    <td style="display:none;"><input type="text" name="orec[]"
                        value="<?=$items[$i]['record'];?>" /></td>
                    <td class="add1"><input id="it<?=$i;?>"  name="item[]"
                        value="<?=$items[$i]['item'];?>" /></td>
                    <td class="add2"><?=$old_payopts;?></td>
                    <td class="add3"><input type="text" name="oamt[]"
                        value="<?=$items[$i]['amt'];?>" /></td>
                    <td class="add4"><?=$old_opts;?></td>
                    <td class="sayr rms"><?=$eyears;?></td>
                    <td class="aptype rms"><?=$eap;?></td>
                    <td class="apday"><input type="text" name="oapday[]"
                        value="<?=$apdayval[$i];?>" /></td>
                    <td class="rms dels"><input type="checkbox" name="rms[]"
                        value="<?=$items[$i]['record'];?>" /></td>
                </tr>
                <?php endfor; ?>
            </tbody>
        </table>
        <br />
        <?php endif; ?>

        <div>
            <h5>You may add entries here...&nbsp;&nbsp;
            <button id="newrow" type="button" class="btn btn-success btn-sm">
                Add A Row</button>
            </h5>
        </div>
        <table id="new_entries">
            <colgroup>
                <col span="1" style="width: 25%;">
                <col span="1" style="width: 18%;">
                <col span="1" style="width: 9%;">
                <col span="1" style="width: 13%;">
                <col span="1" style="width: 16%;">
                <col span="1" style="width: 15%;">
                <col span="1" style="width: 6%;">
            </colgroup>
            <thead>
                <tr>  
                    <th>Expense Item</th>
                    <th>Occurrence</th>
                    <th>Each<br />Payment</th>
                    <th class="rms">[1st] Pay Month</th>
                    <th>Payment Years<br />(If every other)</th>
                    <th class="rms">AutoPay<br />With</th>
                    <th>Day</th>
                </tr>
            </thead>
            <tbody>
                <tr id="new0" class="itemrow">
                    <td class="add1"><input type="text" name="nitem[]"
                        placeholder="Expense Item" /></td>
                    <td class="add2"><?=$new_payopts;?></td>
                    <td class="add3"><input type="text" name="namt[]"
                        placeholder="Amount" /></td>
                    <td class="add4 rms"><?=$new_opts;?></td>
                    <td class="sayr rms"><?=$nyears;?></td>
                    <td class="aptype rms"><?=$nap;?></td>
                    <td class="apday"><input type="text" name="napday[]"
                        value="" /></td>
                </tr>
            </tbody>
        </table>
    </form>
</div>
<hr />
<div class="content">
    <p>This is the current state of the non-monthlies account. The amounts shown
    represent the accumulated funds as of the beginning of <?=$current_month;?>.
    </p> 
    <table id="current_state">
        <colgroup>
            <col span="1" style="width: 30%;">
            <col span="1" style="width: 22%;">
            <col span="1" style="width: 16%;">
            <col span="1" style="width: 16%;">
            <col span="1" style="width: 16%;">
        </colgroup>
        <thead>
            <tr>  
                <!-- first cell is hidden / no header here -->
                <th>Expense Item</th>
                <th>Paid</th>
                <th>Each Payment</th>
                <th>Next Due</th>
                <th>Accum. To Date</th>
            </tr>
        </thead>
        <tbody>
            <?php for ($j=0; $j<$noOfItems; $j++) : ?>
            <tr>
                <td><?=$items[$j]['item'];?></td>
                <?php if ($items[$j]['freq'] === 'Bi-Annually') : ?>
                    <td>Bi-Annually [<?=$items[$j]['SA_yr'];?>]</td>
                <?php else : ?>
                    <td><?=$items[$j]['freq'];?></td>
                <?php endif; ?>
                <td><?=$items[$j]['amt'];?></td>
                <?php if ($waityr[$j]) : ?>
                    <td class="gray"><?=$month_names[$next_dues[$j]];?></td>
                <?php else : ?>
                    <td><?=$month_names[$next_dues[$j]];?></td>
                <?php endif; ?>
                <td><?=$items[$j]['funds'];?></td>
            </tr>
            <?php endfor; ?>
        </tbody>
    </table>
</div><br /><br />

<script src="https://unpkg.com/@popperjs/core@2.4/dist/umd/popper.min.js"></script>
<script src="../scripts/bootstrap.min.js"></script>
<script src="../scripts/jquery.min.js"></script>
<script type="text/javascript">
    var orgfreq  = <?=$js_freq;?>;
    var orgmonth = <?=$js_month;?>;
    var orgsa = <?=$js_sayr;?>;
    var orgtypes = <?=$js_type;?>;  
</script>
<script src="../scripts/combo.js"></script>

</body>
</html>