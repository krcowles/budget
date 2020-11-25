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
<p class="SmallHeading">The following charges were incurred in <?= $period;?><br />
NOTE: You can sort by clicking the column header</p>
<table class="sortable">
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
            <th data-sort="date">Date Incurred</th>
            <th data-sort="std">Status</th>
            <th data-sort="std">Method</th>
            <th data-sort="std">Card Used</th>
            <th data-sort="amt">Amount</th>
            <th data-sort="std">Payee</th>
            <th data-sort="std">Deducted From</th>
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
