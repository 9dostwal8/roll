<ul>
  <li>
    <a href="admin.php">
      <i class="glyphicon glyphicon-home"></i>
      <span><?php echo $lang['Dashboard']; ?></span>
    </a>
  </li>


  <li>
    <a href="#" class="submenu-toggle">
      <i class="glyphicon glyphicon-th-large"></i>
      <span><?php echo $lang['Products']; ?></span>
    </a>
    <ul class="nav submenu">
      <li><a href="product.php"><?php echo $lang['Manage Products']; ?></a> </li>
      <li><a href="add_product.php"><?php echo $lang['Add Products']; ?></a> </li>
    </ul>
  </li>
  <li>
    <a href="sell_product.php" class="submenu-toggle">
      <i class="glyphicon glyphicon-credit-card"></i>
      <span><?php echo $lang['Sales of Products']; ?></span>
    </a>

  </li>
 
  <li>
    <a href="./sales_report.php" class="submenu-toggle">
      <i class="glyphicon glyphicon-duplicate"></i>
      <span><?php echo $lang['Sales Report']; ?></span>
    </a>
    

  </li>

  <li>
    <a href="#" class="submenu-toggle">
      <i class="glyphicon glyphicon-user"></i>
      <span><?php echo $lang['User Management']; ?></span>
    </a>
    <ul class="nav submenu">
      <li><a href="group.php"><?php echo $lang['Manage Groups']; ?></a> </li>
      <li><a href="users.php"><?php echo $lang['Manage Users']; ?></a> </li>
    </ul>
  </li>
  <li>
    <a href="media.php">
      <i class="glyphicon glyphicon-picture"></i>
      <span><?php echo $lang['Media Files']; ?></span>
    </a>
  </li>

  <li>
    <a href="setting.php">
      <i class="glyphicon glyphicon-asterisk"></i>
      <span><?php echo $lang['Settings']; ?></span>
    </a>
  </li>
</ul>