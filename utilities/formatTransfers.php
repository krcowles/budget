<?php
/**
 * This script utilizes the transfer data acquired in reports.php to form
 * a listing of user transfers for the specified year. That listing can be
 * exported to an xls spreadsheet.
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
?>
<br />
<h4><?=$hdr1;?></h4>
<h5>NOTE: You can sort by clicking the column header</h5>
<br />
<table class="sortable">
    <colgroup>
        <col style="width:120px" />
        <col style="width:200px" />
        <col style="width:200px" />
        <col style="width:100px" />
    </colgroup>
    <thead>
        <tr>
            <th data-sort="date">Date Incurred</th>
            <th data-sort="std">Acct Transferred From</th>
            <th data-sort="std">Acct Transferred To</th>
            <th data-sort="amt">Amount</th>
        </tr>
    </thead>
    <tbody>
        <?php for ($k=0; $k<count($transfers); $k++) : ?>
        <tr>
            <td><?=$transfers[$k]['date'];?></td>
            <td><?=$transfers[$k]['from'];?></td>
            <td><?=$transfers[$k]['to'];?></td>
            <td><?=$transfers[$k]['amt'];?></td>
        </tr>
        <?php endfor; ?>
    </tbody>
</table><br />
