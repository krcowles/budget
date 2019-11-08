<?php
/**
 * This module will scan the budget from top to bottom and distribute the entered
 * income into accounts where income has yet to be received (empty or partial).
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license
 */
require "getBudgetData.php";

header("Location: ../main/budget.php");