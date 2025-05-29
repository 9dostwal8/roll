<?php
$page_title = 'Admin Home Page';
require_once('includes/load.php');
// Checkin What level user has permission to view this page

?>
<?php
$c_categorie = count_by_id('categories');
$c_product = count_by_id('products');
$c_sale = count_by_id('sales1');
$c_user = count_by_id('users');
$c_expense = count_by_id('expenses'); // Add this line for expenses count
$c_loan = count_by_id('loans'); // Add this line for loans count
$products_sold = find_higest_saleing_product('1000');
$recent_products = find_recent_product_added('500');
$recent_sales = find_recent_sale_added('500');


?>
<?php include_once('layouts/header.php'); ?>

<div class="row">
  <div class="col-md-6">
    <?php echo display_msg($msg); ?>
  </div>
</div>

<div class="row" style="height: 125px;">
  <!-- Existing Cards -->
  <a href="users.php" style="color:black;">
    <div class="col-md-2">
      <div class="panel panel-box clearfix">
        <div class="panel-icon pull-left bg-secondary1">
          <i class="glyphicon glyphicon-user" style="position: relative; top: 3px; right: 12px;"></i>
        </div>
        <div class="panel-value pull-right">
          <h2 class="margin-top"> <?php echo $c_user['total']; ?> </h2>
          <p class="text-muted"><?php echo $lang['Users']; ?></p>
        </div>
      </div>
    </div>
  </a>

  <a href="product.php" style="color:black;">
    <div class="col-md-2">
      <div class="panel panel-box clearfix">
        <div class="panel-icon pull-left bg-blue2">
          <i class="glyphicon glyphicon-shopping-cart" style="position: relative; top: 3px; right: 14px;"></i>
        </div>
        <div class="panel-value pull-right">
          <h2 class="margin-top"> <?php echo $c_product['total']; ?> </h2>
          <p class="text-muted"><?php echo $lang['Products']; ?></p>
        </div>
      </div>
    </div>
  </a>

  <a href="sell_product.php" style="color:black;">
    <div class="col-md-2">
      <div class="panel panel-box clearfix">
        <div class="panel-icon pull-left bg-green">
          <i class="glyphicon glyphicon-usd" style="position: relative; top: 3px; right: 15px;"></i>
        </div>
        <div class="panel-value pull-right">
          <h2 class="margin-top"> <?php echo $c_sale['total']; ?></h2>
          <p class="text-muted"><?php echo $lang['Sales']; ?></p>
        </div>
      </div>
    </div>
  </a>

  <!-- New Report Card -->


  <a href="sales_report.php" style="color:black;">
    <div class="col-md-2">
      <div class="panel panel-box clearfix">
        <div class="panel-icon pull-left bg-blue2">
          <i class="glyphicon glyphicon-file" style="position: relative; top: 3px; right: 12px;"></i>
        </div>
        <div class="panel-value pull-right">
          <h2 class="margin-top"> <i class="glyphicon glyphicon-stats"></i> </h2>
          <p class="text-muted"><?php echo $lang['Sales Reports']; ?></p>
        </div>
      </div>
    </div>
  </a>

  <!-- New Expense Card -->
  <a href="expence.php" style="color:black;">
    <div class="col-md-2">
      <div class="panel panel-box clearfix">
        <div class="panel-icon pull-left bg-red">
          <i class="glyphicon glyphicon-log-out" style="position: relative; top: 3px; right: 12px;"></i>
        </div>
        <div class="panel-value pull-right">
          <h2 class="margin-top"> <?php echo $c_expense['total']; ?> </h2>
          <p class="text-muted"><?php echo $lang['Expenses']; ?></p>
        </div>
      </div>
    </div>
  </a>

  <!-- New Loan Card -->
  <a href="loans.php" style="color:black;">
    <div class="col-md-2">
      <div class="panel panel-box clearfix">
        <div class="panel-icon pull-left bg-secondary1">
          <i class="glyphicon glyphicon-transfer" style="position: relative; top: 3px; right: 12px;"></i>
        </div>
        <div class="panel-value pull-right">
          <h2 class="margin-top"> <?php echo $c_loan['total']; ?> </h2>
          <p class="text-muted"><?php echo $lang['Loans']; ?></p>
        </div>
      </div>
    </div>
  </a>
</div>

<!-- New Income Card -->
<a href="incomeSt.php" style="color:black;">
  <div class="col-md-2">
    <div class="panel panel-box clearfix">
      <div class="panel-icon pull-left bg-green">
        <i class="glyphicon glyphicon-plus-sign" style="position: relative; top: 3px; right: 12px;"></i>
      </div>
      <div class="panel-value pull-right">
        <h2 class="margin-top"><i class="glyphicon glyphicon-stats"></i></h2>
        <p class="text-muted"><?php echo $lang['Income']; ?></p>
      </div>
    </div>
  </div>
</a>




<?php include_once('layouts/footer.php'); ?>