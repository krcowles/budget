<?php
/**
 * This script is invoked by ajax to retrieve existing account data from the
 * file and present it in the newBudget.php form.
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
require "../utilities/timeSetup.php";

$accounts = fopen($budget_data, "r");
if ($accounts === false) {
    echo 'nofile';
} else {
    $html = '';
    $term = '">';
    $acct = 'Account Name: <textarea class="acctname" name="svdname[]" ' . 
        'form="form" value="';
    $bud  = '</textarea> ' .
        'Monthly Budget: <textarea class="bud" name="svdbud[]" form="form" value="';
    $bal  = '</textarea> ' . 
        'Current value: <textarea class="bal" name="svdbal[]" form="form" value="';
    $cbx  = '</textarea>&nbsp;&nbsp;' .
        'Delete <input type="checkbox" name="remove[]" ' .
            'form="form" value="';
    $eol = '" /><br />' . PHP_EOL;
    $header = true;
    $rec_count = 0;
    while (($acctdat = fgetcsv($accounts)) !== false) {
        if ($header) {
            $header = false;
        } else {
            if ($acctdat[0] === "Temporary Accounts") {
                break;
            } else {
                $html .= $acct . $rec_count . $term . $acctdat[0] .
                    $bud . $rec_count . $term . $acctdat[1] .
                    $bal . $rec_count . $term . $acctdat[4] . 
                    $cbx . $rec_count . $eol;
                $rec_count++;
            }
        }
    }
    echo $html;
}
