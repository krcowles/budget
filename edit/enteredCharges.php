<?php
/**
 * This section of code gets inserted into newBudgetPanels.php only when
 * the user has already made and saved credit card charges.
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
?>
<?php if ($aeexp) : ?>
    <span class="NormalHeading">You can edit the data you have 
        already entered:</span><br /><br />
    <div id="eentered">
        <?php for ($e=0; $e<count($exIds); $e++) : ?>
        <p id="cd<?= $e;?>" 
            style="display:none;"><?= $aeCard[$e];?></p>
        Credit Card Used:
        <span id="crcd<?= $e?>"><?= $ccHtml;?></span>&nbsp;&nbsp;
        Date of Expense: <input type="text"  class="datepicker exp dates"
            name="aeedate[]" value="<?= $aeDate[$e];?>" />&nbsp;&nbsp;
        Amount Paid: <textarea class="amts" rows="1" cols="8"
            name="aeeamt[]"><?= $aeAmt[$e];?></textarea>&nbsp;&nbsp;
        Payee: <textarea rows="1" cols="30"
            name="aeepay[]"><?= $aePayee[$e];?></textarea>&nbsp;&nbsp;
        Delete: <input type="checkbox" name="edel[]" 
            value="<?= $exIds[$e]?>" />
        <input type="hidden" name="expids[]"
            value="<?= $exIds[$e];?>" />
        <br />
        <?php endfor; ?>
    </div>
<?php endif; ?>
