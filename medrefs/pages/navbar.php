<?php
/**
 * Create the user's version of the navigtion menu based on user's values
 * stored in the database. 
 * PHP Version 7.2
 * 
 * @package MedRefs
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
require_once "../../database/global_boot.php";

// Prepare data for javascript access via HTML:
$menu_setup_req = "SELECT `menu`,`active` FROM `Settings` WHERE `userid`=:id;";
$menu_setup = $mdo->prepare($menu_setup_req);
$menu_setup->execute([":id" => $_SESSION['userid']]);
$menu_items = $menu_setup->fetch(PDO::FETCH_ASSOC);
$items = explode("|", $menu_items['menu']);
$itm_cnt = count($items);
$active = $menu_items['active'];
$getPageDataReq = "SELECT `menu_item`,`row_length` FROM `UserPages`" . 
  " WHERE `userid`=?;";
$getPageData = $mdo->prepare($getPageDataReq);
$getPageData->execute([$_SESSION['userid']]);
$pages = $getPageData->fetchAll(PDO::FETCH_ASSOC);

// fetchAll yields array, even if empty
$noOfPages = count($pages);
$existing = [];
$table_width = 0;
if ($noOfPages) {
    foreach ($pages as $page) {
        array_push($existing, $page['menu_item']);
        if ($active == $page['menu_item']) {
            $table_width = $page['row_length'];
        }
    }
}
$js_pages = implode("|", $existing);
?>

<nav id="navbar" class="navbar navbar-expand-sm navbar-dark">
  <div class="container-fluid">
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
      data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
      aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <!-- The complete set of Navbar Actions -->
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item ni-space">
          <a class="nav-link active" aria-current="page"
              href="../pages/main.php">Home</a>
        </li>
        <li class="nav-item ni-space dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="menumgr"
              role="button" data-bs-toggle="dropdown" aria-expanded="false">
              Menu Manager
          </a>
          <ul id="menu_mgr" class="dropdown-menu" aria-labelledby="menumgr">
            <li><a id="addmenu" class="dropdown-item"
                href="#">Add Menu Item</a></li>
            <li><a id="renmenu" class="dropdown-item"
                href="#">Rename Menu Item</a></li>
            <li><a id="delmenu" class="dropdown-item"
                href="#">Delete Menu Item</a></li>
            <li><a id="spechome" class="dropdown-item"
                href="#">Specify Home Display</a></li>
          </ul>
        </li>
        <li class="nav-item ni-space dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="usermenu"
              role="button" data-bs-toggle="dropdown" aria-expanded="false">
              My Pages
          </a>
          <ul id="my_menu" class="dropdown-menu" aria-labelledby="usermenu">
            <?php for ($j=0;$j<$itm_cnt;$j++) :?>
            <li><a id="u<?=$j;?>" class="dropdown-item uitems"
                href="#"><?=$items[$j];?></a></li>
            <?php endfor; ?>
          </ul>
        </li>
      </ul>
      <!--  end navbar selection  -->
    </div>
  </div>
</nav>
<div id="logo">
  <div id="pgheader">
      <div id="leftside">
          <img id="leftie" src="../images/medleft.png"/>
          &nbsp;&nbsp;<span id="ltxt">Medical References</span>
      </div>
      <div id="center">Create New List</div>
      <div id="rightside">
          <span id="rtxt">Personalized Data</span>&nbsp;&nbsp;
          <img id="rightie" src="../images/medright.png" />
      </div>
  </div>   
</div>
<!-- $_SESSION-determined items: -->
<p id="cookies_choice" class="noshow"><?=$_SESSION['cookies'];?></p>
<?php if (isset($admin) && $admin) : ?>
<p id="admin" class="noshow">admin</p>
<?php endif; ?>
<!-- javascript data for menu-settings -->
<p id="menu_list" class="noshow"><?=$menu_items['menu'];?></p>
<p id="item_no" class="noshow"><?=$menu_items['active'];?></p>
<p id="pg_cnt" class="noshow"><?=$noOfPages;?></p>
<p id="upages" class="noshow"><?=$js_pages;?></p>
<p id="tbl_width" class="noshow"><?=$table_width;?></p>
<script src="../scripts/logo.js"></script>
<script src="../scripts/navbar.js"></script>
