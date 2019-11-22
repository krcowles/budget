<?php
/**
 * This script simply redirects to another page as a means to avoid exposing the
 * user info in a query string when javascript opens a new window.
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license
 */
$newpage = filter_input(INPUT_POST, 'pg');
