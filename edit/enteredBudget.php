<?php
/**
 * This section of code gets inserted into newBudgetPanels.php only when
 * the user has already made and saved budget entries.
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
?>
<?php if ($aedata) : ?>
    <span class="NormalHeading">You can edit the data you have 
        currently entered:
    </span><br /><br />
    <div id="entered">
        <?php for ($j=0; $j<count($aeNames); $j++) : ?>
            Budget Item: <textarea class="acctname"
                name="svdname[]"><?= $aeNames[$j];?></textarea>
            Monthly Budget: <textarea class="bud"
                name="svdbud[]"><?= $aeBudamt[$j];?></textarea>
            Current Value: <textarea class="bal"
            name="svdbal[]"><?= $aeCurr[$j];?></textarea>&nbsp;&nbsp;
            Delete: <input type="checkbox" name="remove[]"
                value="<?= $aeIds[$j];?>"><br />
            <input type="hidden" name="ids[]" 
                value="<?= $aeIds[$j];?>" />
        <?php endfor; ?>
    </div><br />
<?php endif; ?>