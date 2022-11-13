<?php
/**
 * These modals are used to modify the user's menu selections on 
 * the navigation bar.
 * PHP Version 7.4
 * 
 * @package MedRefs
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
?>
<div id="useradd" class="modal" tabindex="-1"
    aria-labelled-by="Add Menu Item" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add A Menu Item</h5>
                <button type="button" class="btn-close"
                    data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Specify the name for the new menu item to add:<br />
                [Limit 24 characters]<br />
                <input id="madd" type="text" maxlength="24" />
            </div>
            <div class="modal-footer">
                <button id="addit" type="button"
                    class="btn btn-secondary">Add Menu Item</button>
                <button type="button" class="btn btn-secondary"
                    data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div id="userren" class="modal fade" tabindex="-1"
    aria-labelledby="Change Item Name" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    Rename a Menu Item</h5>
                <button type="button" class="btn-close"
                    data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Select an item to rename:<br />
                <select id="rensel">
                    <?php foreach ($items as $item) : ?>
                    <option value="<?=$item;?>"><?=$item;?></option>
                    <?php endforeach; ?>
                </select><br />
                Enter the new name:<br />
                <input id="newname" type="text" />
            </div>
            <div class="modal-footer">
                <button id="chgit" type="button"
                    class="btn btn-secondary">Change Menu Item</button>
                <button type="button" class="btn btn-secondary"
                    data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div id="userdel" class="modal fade" tabindex="-1"
    aria-labelledby="Delete Item" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    Remove a Menu Item</h5>
                <button type="button" class="btn-close"
                    data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
               Select the item to be removed; Data associated with this
               item will be lost:<br />
               <select id="delsel">
                    <?php foreach ($items as $item) : ?>
                    <option value="<?=$item;?>"><?=$item;?></option>
                    <?php endforeach; ?>
                </select><br />
            </div>
            <div class="modal-footer">
                <button id="delit" type="button"
                    class="btn btn-secondary">Remove Menu Item</button>
                <button type="button" class="btn btn-secondary"
                    data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div id="userdef" class="modal fade" tabindex="-1"
    aria-labelledby="Specify Default Display" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    Specify Default Display Item</h5>
                <button type="button" class="btn-close"
                    data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
               Select the item which you wish to display by default
               on the home page:<br />
               <select id="defsel">
                    <?php foreach ($items as $item) : ?>
                    <option value="<?=$item;?>"><?=$item;?></option>
                    <?php endforeach; ?>
                </select><br />
            </div>
            <div class="modal-footer">
                <button id="defhome" type="button"
                    class="btn btn-secondary">Set As Default</button>
                <button type="button" class="btn btn-secondary"
                    data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
