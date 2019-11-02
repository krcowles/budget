<?php
/** 
 * This script allows the user to setup autopay for registered cr/dr cards
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
require "../utilities/getBudgetData.php"; // includes cleanup function

if (file_exists($credit_data)) {
    $ccdat = fopen($credit_data, "r");
    $cards = fgetcsv($ccdat);
    $cards = cleanupExcel($cards);
    if ($cards[0] === 'None') {
        fclose($ccdat);
        echo "Nodat";
        exit;
    } else {
        fclose($ccdat);
        $options = '<option value="start">Select Method:</option>';
        $no_of_opts = count($cards);
        for ($y=0; $y<count($cards); $y+=2) {
            $options .= '<option value="' . $cards[$y] . '">' . 
                $cards[$y] . '</option>';
        }
    }
    if ($status !== 'OK') {
        echo $status; // otherwise set in getBudgetData.php
    }
} else {
    echo "Nodat";
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title>Autopay Setup</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="description"
        content="Rolling 4-month budget tracker" />
    <meta name="author" content="Ken Cowles" />
    <meta name="robots" content="nofollow" />
    <link href="../styles/standards.css" type="text/css" rel="stylesheet" />
    <link href="../styles/charges.css" type="text/css" rel="stylesheet" >
    <style type="text/css">
        #ap {margin-left:24px;}
        .date {font-size:16px;padding-top:6px;padding-bottom:0px;}
        td {vertical-align:middle;}
        textarea {height: 24px; width:46px;}
        select {width: 100%;}
    </style>
    <script src="../scripts/jquery-1.12.1.js"></script>
</head>

<body>
<!-- init values for javascript to set select boxes -->
<?php for($k=0; $k<count($account_names); $k++) : ?>
<p id="init<?= $k;?>" style="display:none"><?= $autopay[$k];?></p>
<?php endfor; ?>
<form id="ap" action="saveAP.php" method="POST">
<span class="NormalHeading">Register up to 10 items for autopayment via either 
    credit cards or debit cards/bank withdrawals</span><br />
    <button style="margin-top:24px;" type="submit">Save Changes</button>
    <button style="margin-left:40px;" id="bud">Return to Budget</button><br /><br />
    <table>
        <colgroup>
            <col style="width:200px" />
            <col style="width:108px" />
            <col style="width:108px" />
            <col style="width:108px" />
            <col style="width:108px" />
            <col style="width:180px" />
            <col style="width:50px" />
        </colgroup>
        <thead>
            <tr>
                <th>Acct</th>
                <th>Monthly</th>
                <th>Previous</th>
                <th>Previous</th>
                <th>Current</th>
                <th>Autopay</th>
                <th>Day</th>
            </tr>
        </thead>
        <tbody>
            <?php for($x=0; $x<count($account_names); $x++) : ?>
            <tr>
                <td style="text-align:left;"><?= $account_names[$x];?></td>
                <td><?= $budgets[$x];?></td>
                <td><?= $prev0[$x];?></td>
                <td><?= $prev1[$x];?></td>
                <td><?= $current[$x];?></td>
                <td><select id="sel<?= $x;?>"
                    name="amethod[]"><?= $options;?></select></td>
                <td><textarea name="day[]" 
                    class="date"><?= $day[$x];?></textarea></td>
            </tr>
            <?php endfor; ?>
        </tbody>
    </table>
</form>
<script type="text/javascript">
    var $selects = $('select[id^=sel]');
    var $selvals = $('p[id^=init');
    $selects.each(function(indx) {
        var sid = '#sel' + indx;
        var pid = '#init' + indx;
        var initval = $(pid).text();
        if (initval !== '') {
            $(sid).val(initval);
        } else {
            $(sid).val("start");
        }
    });
    $('#bud').on('click', function(ev) {
        ev.preventDefault();
        window.open("../main/budget.php", "_self");
    });
</script>
</body>

</html>