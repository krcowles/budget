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
<h4>The following charges were incurred in <?= $period;?><h4>
<h5>NOTE: You can sort by clicking the column header</h5>
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
        <?php for ($k=0; $k<count($ramt); $k++) : ?>
            <?php if ($k > 0 && $k%2 === 1) : ?>
            <tr class="even">
            <?php else: ?>
            <tr>
            <?php endif; ?>
            <td><?= $rdate[$k];?></td>
            <?php if ($rpaid[$k] === "Y") : ?>
            <td>Paid</td>
            <?php else: ?>
            <td class="red">Unpaid</td>
            <?php endif; ?>
            <td><?= $rmethod[$k];?></td>
            <td><?= $rcdname[$k];?></td>
            <td><?= $ramt[$k];?></td>
            <td><?= $rpayee[$k];?></td>
            <td><?= $racct[$k];?></td>
        </tr>
        <?php endfor; ?>
    </tbody>
</table>
