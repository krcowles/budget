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
<br />
<h4>The following charges were incurred in <?= $period;?></h4>
<h5>NOTE: You can sort by clicking the column header</h5>
<div style="font-size: 22px;">
    <a href="user_annual.xlsx" download>Click to Download as Excel</a>
    &nbsp;&nbsp;NOTE: The report takes some time to complete... wait until
    tab activity has stopped.
</div><br />
<?php for ($k=1; $k<=12; $k++) : ?>
    <h5>In <?=$month_names[$k-1];?>:</h5>
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
            <?php for ($n=0; $n<count($mo[$k]); $n++) : ?>
                <?php if ($n > 0 && $n%2 === 1) : ?>
                <tr class="even">
                <?php else: ?>
                <tr>
                <?php endif; ?>
                <td><?= $mo[$k][$n][2];?></td>
                <?php if ($mo[$k][$n][6] === "Y") : ?>
                <td>Paid</td>
                <?php else: ?>
                <td class="red">Unpaid</td>
                <?php endif; ?>
                <td><?= $mo[$k][$n][0];?></td>
                <td><?= $mo[$k][$n][1];?></td>
                <td><?= $mo[$k][$n][3];?></td>
                <td><?= $mo[$k][$n][4];?></td>
                <td><?= $mo[$k][$n][5];?></td>
            </tr>
            <?php endfor; ?>
        </tbody>
    </table><br />
    <?php
    // Setup Excel Spreadsheet
    if (file_exists("user_annual.xlsx")) {
        unlink("user_annual.xlsx");
    }
    $monthly_excel_data = [];
    $indx = 0;
    for ($j=1; $j<12; $j++) {
        foreach ($mo[$j] as $modata) {
            $row = 'A' . $rowno;
            $amtcell  = 'E' . $rowno++;
            $unixTime = strtotime($modata[2]);
            $monthly_excel_data[0] 
                = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($unixTime);
            $monthly_excel_data[1] = $modata[6] === 'Y' ? 'Paid' : 'Unpaid';
            $monthly_excel_data[2] = $modata[0];
            $monthly_excel_data[3] = $modata[1];
            $monthly_excel_data[4] = $modata[3];
            $monthly_excel_data[5] = $modata[4];
            $monthly_excel_data[6] = $modata[5];
            $spreadsheet->getActiveSheet()->fromArray(
                $monthly_excel_data,
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
            $indx++;
        }
    }
    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save("user_annual.xlsx");
    ?>
<?php endfor; ?>
