<?php
/**
 * This script retrieves all income and one-time deposits, then reports
 * the results in tabular form.
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
$income_yr = filter_input(INPUT_GET, 'incyr');

$depositReq = "SELECT * FROM `Deposits` WHERE `userid` = ? AND YEAR(`date`) = ?;";
$depositQ = $pdo->prepare($depositReq);
$depositQ->execute([$_SESSION['userid'], $income_yr]);
$deposits = $depositQ->fetchAll(PDO::FETCH_ASSOC);
$sources = [];
$latest  = [];
foreach ($deposits as $deposit) {
    if ($deposit['otd'] === 'N') { // this is monthly income
        $key = "Monthly Income";
    } else {
        $key = $deposit['description'];
    }
    $srckeys = array_keys($sources);
    if (!in_array($key, $srckeys)) {
        $sources[$key] = $deposit['amount'];
        $latest[$key]  = $deposit['date'];
    } else {
        $prevsum = $sources[$key];
        $newsum  = $prevsum + $deposit['amount'];
        $sources[$key] = $newsum;
        $thisdate = explode("-", $deposit['date']);
        $lastdate = explode("-", $latest[$key]);
        if ($thisdate[1] > $lastdate[1]) {
            $latest[$key] = implode("-", $thisdate);
        } elseif ($thisdate[1] == $lastdate[1]) {
            if ($thisdate[2] > $lastdate[2]) {
                $latest[$key] = implode("-", $thisdate);
            }
        }
    }
}
?>
<h4 class="inc">Annual Summary:</h4>
<table style="margin-top:8px;margin-left:12px;">
    <colgroup>
        <col style="width:120px;" />
        <col style="width:100px" />
        <col style="width:320px" />
    </colgroup>
    <thead>   
        <tr>
            <th>Last Deposit</th>
            <th>Total</th>
            <th>Source</th>
        </tr> 
    </thead>
    <tbody>
        <?php foreach ($sources as $key => $value) :?>
        <tr>
            <td><?=$latest[$key];?></td>
            <td><?=dataPrep($value, 'prev0');?></td>
            <td><?=$key;?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<h4 class="inc">Activity:</h4>
<table style="margin-top:8px;margin-left:12px;">
    <colgroup>
        <col style="width:120px" />
        <col style="width:100px" />
        <col style="width:320px" />
    </colgroup>
    <thead>
        <tr>
            <th data-sort="date">Deposit Date</th>
            <th data-sort="std">Amount</th>
            <th data-sort="std">Memo</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($deposits as $deposit) : ?>
            <tr>
                <td><?=$deposit['date'];?></td>
                <td><?=dataPrep($deposit['amount'], 'prev0');?></td>
                <?php if ($deposit['otd'] === 'Y') : ?>
                    <td><?=$deposit['description'];?></td>
                <?php else : ?>
                    <td>Monthly Income</td>
                <?php endif; ?>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
