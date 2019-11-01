<?php
/**
 * This is an effort to replace the broken and impossible to maintain, thanks
 * to Microsoft, 'budget.xlsm'.
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license
 */
require "budget_setup.php";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title>Budget Tracker</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="description"
        content="Rolling 4-month budget tracker" />
    <meta name="author" content="Ken Cowles" />
    <meta name="robots" content="nofollow" />
    <link href="standards.css" type="text/css" rel="stylesheet" />
    <link href="charges.css" type="text/css" rel="stylesheet" />
</head>

<body>
    <div id="budget">
        <table id="4month">
            <colgroup>
                <col style="width:200px">
                <col style="width:108px">
                <col style="width:140px">
                <col style="width:140px">
                <col style="width:140px">
                <col style="width:90px">
                <col style="width:64px">
            </colgroup>
            <thead>
                <tr>
                    <th>Account Name</th>
                    <th class="heavy-right">Budget</th>
                    <th><?= $month[0];?></th>
                    <th><?= $month[1];?></th>
                    <th><?= $month[2];?></th>
                    <th>AutoPay</th>
                    <th>Day</th>
                </tr>
            </thead>
            <tbody>
                <?php for($j=1; $j<count($entries); $j++) : ?>
                <tr>
                    <td class="acct"><?= $entries[$j][0];?></td>
                    <td class="amt"><?= $entries[$j][1];?></td>
                    <td class="mo1" data-value="<?= $entries[$j][2];?>"></td>
                    <td class="mo2" data-value="<?= $entries[$j][3];?>"></td>
                    <td class="mo3" data-value="<?= $entries[$j][4];?>"></td>
                    <td></td>
                    <td></td>
                </tr>
                <?php endfor; ?>
                <tr>
                    <td class="temp" style="text-align:center;">Temporary Accounts</td>
                    <td class="temp"></td>
                    <td class="temp"></td>
                    <td class="temp"></td>
                    <td class="temp"></td>
                    <td class="temp"></td>
                    <td class="temp"></td>
                </tr>
                <?php for ($k=0; $k<count($temps); $k++) : ?>
                <tr>
                    <td class="acct"><?= $temps[$k][0];?></td>
                    <td class="amt"><?= $temps[$k][1];?></td>
                    <td class="mo0" data-value="<?= $temps[$k][2];?>"></td>
                    <td class="mo1" data-value="<?= $temps[$k][3];?>"></td>
                    <td class="mo2" data-value="<?= $temps[$k][4];?>"></td>
                    <td></td>
                    <td></td>
                </tr>
                <?php endfor; ?>
            </tbody>
        </table>
    </div>
    <p>
        <a href="http://validator.w3.org/check?uri=referer">
            <img src="http://www.w3.org/Icons/valid-xhtml10"
            alt="Valid XHTML 1.0 Strict" height="31" width="88" />
        </a>
    </p>
</body>
</html>