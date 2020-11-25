<?php
/**
 * This script retrieves all income and on-time deposits and reports the
 * results
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
$depositReq = "SELECT * FROM `Deposits` WHERE `userid` = ?;";
$depositQ = $pdo->prepare($depositReq);
$depositQ->execute([$_SESSION['userid']]);
$deposits = $depositQ->fetchAll(PDO::FETCH_ASSOC);
?>
<table style="margin-top:24px;margin-left:12px;">
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
