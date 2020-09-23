<?php
/**
 * This script extracts all expenses (paid or unpaid) from the 'Charges' table
 * and formats them in tabular format for display to the user.
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
?>
<p class="SmallHeading">The following charges were incurred in <?= $period;?></p>
<table>
    <colgroup>
        <col style="width:120px" />
        <col style="width:100px" />
        <col style="width:80px" />
        <col style="width:120px" />
        <col style="width:120px" />
        <col style="width:180px" />
        <col style="width:160px" />
    </colgroup>
    <thead>
        <tr>
            <th>Date Incurred</th>
            <th>Status</th>
            <th>Method</th>
            <th>Card Used</th>
            <th>Amount</th>
            <th>Payee</th>
            <th>Deducted From</th>
        </tr>
    </thead>
    <tbody>
        <?php for ($k=0; $k<count($amt); $k++) : ?>
            <?php if ($k > 0 && $k%2 === 1) : ?>
            <tr class="even">
            <?php else: ?>
            <tr>
            <?php endif; ?>
            <td><?= $date[$k];?></td>
            <?php if ($paid[$k] === "Y") : ?>
            <td>Paid</td>
            <?php else: ?>
            <td class="red">Unpaid</td>
            <?php endif; ?>
            <td><?= $method[$k];?></td>
            <td><?= $cdname[$k];?></td>
            <td><?= $amt[$k];?></td>
            <td><?= $payee[$k];?></td>
            <td><?= $acct[$k];?></td>
        </tr>
        <?php endfor; ?>
    </tbody>
</table>
