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
$income_yr = $period;
$depositReq = "SELECT * FROM `Deposits` WHERE `userid` = ? AND YEAR(`date`) = ?;";
$depositQ = $pdo->prepare($depositReq);
$depositQ->execute([$_SESSION['userid'], $income_yr]);
$deposits = $depositQ->fetchAll(PDO::FETCH_ASSOC);
$excel_data = [];
foreach ($deposits as $excel) {
    $row       = 'A' . $rowno;
    $amtcell   = 'B' . $rowno;
    $desc_cell = 'C' . $rowno++;
    $unixTime = strtotime($excel['date']);
    $excel_data[0] = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($unixTime);
    $excel_data[1] = $excel['amount'];
    $color = false;
    if ($excel['otd'] === 'N') {
        $excel_data[2] = '[Regular Monthly Income]';
        $color = true;
    } else {
        $excel_data[2] = $excel['description'];
    }
    $spreadsheet->getActiveSheet()->fromArray(
        $excel_data,
        null,
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
    if ($color) {
        $spreadsheet->getActiveSheet()->getStyle($desc_cell)->getFont()->getColor()
            ->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_DARKGREEN);
        $color = false;
    }
}
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
$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
$writer->save("user_income.xlsx");
?>
<br />
<div class="inc" style="font-size: 22px;">
    <a href="user_income.xlsx" download>Click to Download as Excel</a>
    &nbsp;&nbsp;NOTE: The report may takes some time to complete... wait until
    tab activity has stopped.
</div><br />
<h4 class="inc">Annual Summary for <?=$income_yr;?>:</h4>
<div class="inc">
<table style="margin-top:8px;">
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
</div>

<h4 class="inc">Daily Activity:</h4>
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
