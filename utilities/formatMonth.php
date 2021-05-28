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
// Setup Excel Spreadsheet
if (file_exists("user_monthly.xlsx")) {
    unlink("user_monthly.xlsx");
}
$monthly_excel_data = [];
for ($j=0; $j<count($rdate); $j++) {
    $row = 'A' . $rowno;
    $amtcell  = 'E' . $rowno++;
    $unixTime = strtotime($rdate[$j]);
    $monthly_excel_data[0] = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel( $unixTime );
    $monthly_excel_data[1] = $rpaid[$j] === 'Y' ? 'Paid' : 'Unpaid';
    $monthly_excel_data[2] = $rmethod[$j];
    $monthly_excel_data[3] = $rcdname[$j];
    $monthly_excel_data[4] = $ramt[$j];
    $monthly_excel_data[5] = $rpayee[$j];
    $monthly_excel_data[6] = $racct[$j];
    $spreadsheet->getActiveSheet()->fromArray(
        $monthly_excel_data,
        NULL,
        $row,
    );
    $spreadsheet->getActiveSheet()->getStyle($row)->getNumberFormat()
        ->setFormatCode(
            \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_XLSX15
    );
    $spreadsheet->getActiveSheet()->getStyle($amtcell)->getNumberFormat()
        ->setFormatCode(
            \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_ACCOUNTING_USD
    );
}
$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
$writer->save("user_monthly.xlsx");
?>
<br />
<h4>The following charges were incurred in <?= $period;?><h4>
<h5>NOTE: You can sort by clicking the column header</h5>
<div style="font-size: 22px;">
    <a href="user_monthly.xlsx" download>Click to Download as Excel</a>
</div><br />
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
