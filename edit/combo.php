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

<?php
require "../utilities/getAccountData.php";
if ($noOfItems > 0) {
    $action = "edit or add items to your current 'Non-Monthly Expenses' account";
    $saving = "Return to Budget";
} else {
    $action = "create a list of non-monthly expenses to track in a single account";
    $saving = "Add to Budget";
}
?>

<div id="content">
<form method="post" action="saveCombo.php">
    <h4>This page allows you to <?=$action;?>. The following data must be entered
    or modified - no field is optional. Rows will be added automatically.
    Please select the 'Save' button to save the results and place/keep the account
    on your budget page. You can exit without saving by selecting the
    "Return: Don't Save" button
    </h4>

    <button id="savit" type="submit" class="btn btn-success">
        Save and <?=$saving;?></button>
    <button id="nosave" type="button" class="btn btn-secondary">
        Return: Don't Save</button><br /><br />
    <input id="newbies" type="hidden" name="newvals" value="0" />

    <p id="oldcnt" style="display:none;"><?=$noOfItems;?></p>
    <?php if ($noOfItems > 0) : ?>
    <div>
        <h5>The following items may be edited:</h5>
    </div>
    <table id="old_entries">
        <colgroup>
        
            <col span="1" style="width: 30%;">
            <col span="1" style="width: 20%;">
            <col span="1" style="width: 15%;">
            <col span="1" style="width: 15%;">
            <col span="1" style="width: 15%;">
        </colgroup>
        <thead>
            <tr>  
                <th>Expense Item</th>
                <th>Occurrence</th>
                <th>Each Payment</th>
                <th>1st Payment Mo</th>
                <th>Pay [ ] Years</th>
            </tr>
        </thead>
        <tbody>
            <?php for ($i=0; $i<$noOfItems; $i++) : ?>
            <tr id="old<?=$i;?>"class="itemrow">
                <td style="display:none;"><input type="text" name="orec[]"
                    value="<?=$items[$i]['record'];?>" /></td>
                <td class="add1"><input type="text" name="oitem[]"
                    value="<?=$items[$i]['item'];?>"</td>
                <td class="add2"><?=$old_payopts;?>
                    <input id="op<?=$i;?>" style="display:none"
                        value="<?=$items[$i]['freq'];?>" /></td>
                <td class="add3"><input type="text" name="oamt[]"
                    value="<?=$items[$i]['amt'];?>"</td>
                <td class="add4"><?=$old_opts;?>
                    <input id="of<?=$i;?>" style="display:none;"
                        value="<?=$items[$i]['first'];?>" /></td>
                <td class="sayr"><input type="text" name="osa_yr[]"
                    value="<?=$items[$i]['SA_yr'];?>"</td>
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


<script src="https://unpkg.com/@popperjs/core@2.4/dist/umd/popper.min.js"></script>
<script src="../scripts/bootstrap.min.js"></script>
<script src="../scripts/jquery.min.js"></script>
<script src="../scripts/combo.js"></script>

</body>
</html>