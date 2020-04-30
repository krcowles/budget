<?php
/**
 * This section of code gets inserted into newBudgetPanels.php only when
 * the user has already made and saved credit/debit card entries.
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
?>
<?php if ($aecards) : ?>
    <span class="NormalHeading">You can edit the data you have 
        currently entered:</span><br /><br />
    <div id="centered">
        <?php for ($c=0; $c<count($cdNames); $c++) : ?>
        <p id="oc<?= $c;?>" 
            style="display:none;"><?= $cdTypes[$c];?></p>
        Card name: <textarea class="ocname" rows="1" cols="20"
        name="svdcard[]"><?= $cdNames[$c];?></textarea>
        Card type: <select name="svdtype[]" id="seloc<?= $c;?>">
            <option value="Credit">Credit</option>
            <option value="Debit">Debit</option>
        </select>&nbsp;&nbsp;
        Delete: <input type="checkbox" name="cdel[]" 
            value="<?= $cdIds[$c]?>" />
        <input type="hidden" name="cdids[]"
            value="<?= $cdIds[$c];?>" />
        <br />
        <?php endfor; ?>
    </div>
<?php endif; ?>
