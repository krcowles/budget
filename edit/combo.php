<?php
/**
 * This page will either create a new non-monthlies account, or
 * if one already exists, it will allow the user to edit it.
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

// define payment frequency options
$old_payopts = '<select name="ofreq[]">';
$new_payopts = '<select name="freq[]">';
$payopts = <<<PAYOPTS
    <option value="Payment Frequency">Payment Frequency</option>
    <option value="Bi-Annually">Every Other Year</option>
    <option value="Annually">Annually</option>
    <option value="Semi-Annually">Semi-Annually</option>
    <option value="Quarterly">Quarterly</option>
    <option value="Bi-Monthly">Every Other Month</option>
</select>
PAYOPTS;
$old_payopts .= $payopts;
$new_payopts .= $payopts;

// define month-select drop-down
$old_opts = '<select name="ofirst[]">';
$new_opts = '<select name="first[]">';
$opts = '<option value="99">Select Month</option>';
for ($i=0; $i<12; $i++) {
    $opts .= '<option value="' . $month_names[$i] . '">' .
            $month_names[$i] . '</option>';
}
$opts .= '</select'>
$old_opts .= $opts;
$new_opts .= $opts;

// extract any existing user data
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
 * Prepare data for display in status table
 */
$next_dues = [];
$waityr = [];
for ($k=0; $k<$noOfItems; $k++) {
    $waityr[$k] = false;
}

for ($i=0; $i<$noOfItems; $i++) {
    $first_mo = $items[$i]['first']; // <string> month name
    $index_mo = array_search($first_mo, $month_names); // 0-based index
    $dist_months = [];  // digits representing month_names indices
    $dist_months[0] = $index_mo;
    $payfreq = getFrequency($items[$i]['freq']);
    $incr_months = intval(12/$payfreq);
    $eoyr = false;
    if (!empty($items[$i]['mo_pd'])) {
        $month_paid = array_search($items[$i]['mo_pd'], $month_names);
        $acct_paid = $month_paid === $thismo ? true : false;
    } else {
        $acct_paid = false;
    } 
    // calculate 'next_due' payment
    if ($payfreq === 1 || $payfreq === 0.5) { // annual or every-other yr payments
        $next_dues[$i] = $dist_months[0];
        if (!empty($items[$i]['SA_yr'])) {
            if ($items[$i]['SA_yr'] === 'Odd' && $thisyear%2 === 1
                && !empty($items[$i]['mo_pd'])
            ) {
                $waityr[$i] = true;
            } elseif ($items[$i]['SA_yr'] === 'Odd' && $thisyear%2 === 0) {
                $waityr[$i] = true;
            }

            if ($items[$i]['SA_yr'] === 'Even' && $thisyear%2 === 0
                && !empty($items[$i]['mo_pd'])
            ) {
                $waityr[$i] = true;
            } elseif ($items[$i]['SA_yr'] === 'Even' && $thisyear%2 === 1) {
                $waityr[$i] = true;
            }
        }
    }
    if ($payfreq > 1) { // multiple distribution months per annum
        for ($j=1; $j<$payfreq; $j++) {
            if ($dist_months[$j-1] + $incr_months > 11) {
                // adjust for base 0: $pay_incr -1
                $next_mo = ($incr_months - 1) - (11 - $dist_months[$j-1]);
            } else {
                $next_mo = $dist_months[$j-1] + $incr_months;
            }
            $dist_months[$j] = $next_mo;
        }
        sort($dist_months);
        // next due month:
        $payouts = count($dist_months);
        for ($j=0; $j<$payouts; $j++) {
            if ($thismo <= $dist_months[$j]) {
                if ($thismo === $dist_months[$j] && $acct_paid) {
                    if ($j === $payouts -1) {
                        $next = $dist_months[0];
                    } else {
                        $next = $dist_months[$j+1];
                    }
                } elseif ($thismo === $dist_months[$j] && !$acct_paid) {
                    $next = $dist_months[$j];
                } else { 
                    $next = $dist_months[$j];
                }
                $next_dues[$i] = $next;
                break;
            }
        }   
    }
}
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
        <button id="savit" type="submit" class="btn btn-success">
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
                <col span="1" style="width: 28%;">
                <col span="1" style="width: 20%;">
                <col span="1" style="width: 15%;">
                <col span="1" style="width: 15%;">
                <col span="1" style="width: 15%;">
                <col span="1" style="width: 7%;">
            </colgroup>
            <thead>
                <tr>  
                    <th>Expense Item</th>
                    <th>Occurrence</th>
                    <th>Each Payment</th>
                    <th>1st Payment Mo</th>
                    <th>Pay [ ] Years</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <tbody>
                <?php for ($i=0; $i<$noOfItems; $i++) : ?>
                <tr id="old<?=$i;?>"class="itemrow">
                    <td style="display:none;"><input type="text" name="orec[]"
                        value="<?=$items[$i]['record'];?>" /></td>
                    <td class="add1"><input type="text" name="oitem[]"
                        value="<?=$items[$i]['item'];?>" /></td>
                    <td class="add2"><?=$old_payopts;?>
                        <input id="op<?=$i;?>" style="display:none"
                            value="<?=$items[$i]['freq'];?>" /></td>
                    <td class="add3"><input type="text" name="oamt[]"
                        value="<?=$items[$i]['amt'];?>" /></td>
                    <td class="add4"><?=$old_opts;?>
                        <input id="of<?=$i;?>" style="display:none;"
                            value="<?=$items[$i]['first'];?>" /></td>
                    <td class="sayr"><input type="text" name="osa_yr[]"
                        value="<?=$items[$i]['SA_yr'];?>" /></td>
                    <td class="rms"><input type="checkbox" name="rms[]"
                        value="<?=$items[$i]['record'];?>" /></td>
                </tr>
                <?php endfor; ?>
            </tbody>
        </table>
        <br />
        <?php endif; ?>

        <div>
            <h5>You may add entries here...</h5>
        </div>
        <table id="new_entries">
            <colgroup>
                <col span="1" style="width: 40%;">
                <col span="1" style="width: 25%;">
                <col span="1" style="width: 20%;">
                <col span="1" style="width: 15%;">
            </colgroup>
            <thead>
                <tr>
                    <th>Expense Item</th>
                    <th>Occurrence</th>
                    <th>Each Payment</th>
                    <th>1st Payment Mo</th>
                </tr>
            </thead>
            <tbody>
                <tr id="new1" class="itemrow">
                    <td class="add1"><input type="text" name="item[]"
                        placeholder="Expense Item" />
                    <td class="add2">
                        <?=$new_payopts;?>
                        <input type="hidden" name="alts[]" value=""/>
                    </td>
                    <td class="add3"><input type="text" name="amt[]"
                        placeholder="Amount" /></td>
                    <td class="add4"><?=$new_opts;?></td>
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
<script src="../scripts/combo.js"></script>

</body>
</html>