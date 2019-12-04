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
        currently entered:</span><br /><br />
    <div id="eentered">
        <?php for ($e=0; $e<count($exIds); $e++) : ?>
        <p id="cd<?= $e;?>" 
            style="display:none;"><?= $aeCard[$e];?></p>
        Date Entered: <textarea class="exp dates"
            name="aeedate[]"><?= $aeDate[$e];?></textarea>
        Credit Card Used:
        <span id="crcd<?= $e?>"><?= $ccHtml;?></span>
        Amount Paid: <textarea class="exp amts"
            name="aeeamt[]"><?= $aeAmt[$e];?></textarea>
        Payee: <textarea class="exp"
            name="aeepay[]"><?= $aePayee[$e];?></textarea>
        Delete: <input type="checkbox" name="edel[]" 
            value="<?= $exIds[$e]?>" />
        <input type="hidden" name="expids[]"
            value="<?= $exIds[$e];?>" />
        <br />
        <?php endfor; ?>
    </div>
<?php endif; ?>
