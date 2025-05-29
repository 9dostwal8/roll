<?php


// Page title
$page_title = 'Financial Reports';

// Include necessary files
require_once('includes/load.php');
// Restrict access based on user level
$user = current_user(); // Get the currently logged-in user info

if ($user['user_level'] == 3) {
    // Show access denied message and stop script
    echo "<div style='padding: 20px; color: red; font-weight: bold; font-size: 18px; text-align: center;'>
            Access Denied: You are not allowed to view this page.
          </div>";
    include_once('layouts/footer.php');
    exit();
}


// Handle language change
if (isset($_POST['change_language'])) {
    $selected_language = $_POST['language'];
    $_SESSION['language'] = $selected_language;
    $session->msg("s", "Language changed successfully.");
    redirect('IncomeSt.php', false);
}

// Get date range filter if set
$from_date = isset($_GET['from_date']) ? $_GET['from_date'] : date('Y-m-01');
$to_date = isset($_GET['to_date']) ? $_GET['to_date'] : date('Y-m-d');

// Calculate Sales Revenue
$sales_revenue = $db->query("SELECT SUM(total) AS total FROM sales1 WHERE date BETWEEN '$from_date' AND '$to_date'")->fetch_assoc();
$total_sales = $sales_revenue['total'] ?? 0;

// Calculate Cost of Goods Sold (COGS)
$cogs = $db->query("SELECT SUM(buy_price * quantity) AS total FROM sales1 WHERE date BETWEEN '$from_date' AND '$to_date'")->fetch_assoc();
$total_cogs = $cogs['total'] ?? 0;

// Calculate Gross Profit
$gross_profit = $total_sales - $total_cogs;

// Calculate Total Expenses
$expenses = $db->query("SELECT SUM(amount) AS total FROM expenses WHERE date BETWEEN '$from_date' AND '$to_date'")->fetch_assoc();
$total_expenses = $expenses['total'] ?? 0;

// Calculate Loan Interest Income
$loan_interest = $db->query("SELECT SUM((amount * interest_rate/100)) AS total FROM loans WHERE date_issued BETWEEN '$from_date' AND '$to_date'")->fetch_assoc();
$total_interest = $loan_interest['total'] ?? 0;

// Calculate Net Income
$net_income = $gross_profit + $total_interest - $total_expenses;

// Include the header
include_once('layouts/header.php');
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong>
                    <span class="glyphicon glyphicon-stats"></span>
                    <span>Income Statement</span>
                </strong>
                <div class="pull-right">
                    <form method="get" action="IncomeSt.php" class="form-inline">
                        <div class="form-group">
                            <label>From:</label>
                            <input type="date" class="form-control input-sm" name="from_date" value="<?php echo $from_date; ?>">
                        </div>
                        <div class="form-group">
                            <label>To:</label>
                            <input type="date" class="form-control input-sm" name="to_date" value="<?php echo $to_date; ?>">
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                        <button type="button" class="btn btn-default btn-sm" onclick="window.print()">
                            <span class="glyphicon glyphicon-print"></span> Print
                        </button>
                    </form>
                </div>
            </div>
            <div class="panel-body" >
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <table class="table table-bordered">
                            <thead>
                                <tr class="info">
                                    <th colspan="2" class="text-center">
                                        INCOME STATEMENT<br>
                                        For Period: <?php echo date('F j, Y', strtotime($from_date)); ?> to <?php echo date('F j, Y', strtotime($to_date)); ?>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Revenue Section -->
                                <tr>
                                    <td><strong>Sales Revenue</strong></td>
                                    <td class="text-right"><?php echo number_format($total_sales, 2); ?></td>
                                </tr>
                                <tr>
                                    <td><em>&nbsp;&nbsp;Cost of Goods Sold</em></td>
                                    <td class="text-right"><?php echo number_format($total_cogs, 2); ?></td>
                                </tr>
                                <tr class="active">
                                    <td><strong>Gross Profit</strong></td>
                                    <td class="text-right"><strong><?php echo number_format($gross_profit, 2); ?></strong></td>
                                </tr>
                                
                                <!-- Other Income -->
                                <tr>
                                    <td><strong>Other Income</strong></td>
                                    <td class="text-right"><?php echo number_format($total_interest, 2); ?></td>
                                </tr>
                                
                                <!-- Expenses Section -->
                                <tr>
                                    <td><strong>Operating Expenses</strong></td>
                                    <td class="text-right"><?php echo number_format($total_expenses, 2); ?></td>
                                </tr>
                                
                                <!-- Net Income -->
                                <tr class="<?php echo ($net_income >= 0) ? 'success' : 'danger'; ?>">
                                    <td><strong>NET INCOME</strong></td>
                                    <td class="text-right"><strong><?php echo number_format($net_income, 2); ?></strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Detailed Breakdown -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <strong>Sales Details</strong>
                            </div>
                            <div class="panel-body">
                                <?php 
                                $top_products = $db->query("
                                    SELECT product_name, SUM(quantity) as qty, SUM(total) as total 
                                    FROM sales1 
                                    WHERE date BETWEEN '$from_date' AND '$to_date'
                                    GROUP BY product_id 
                                    ORDER BY total DESC 
                                    LIMIT 5
                                ");
                                ?>
                                <table class="table table-condensed">
                                    <tr>
                                        <th>Product</th>
                                        <th class="text-right">Qty</th>
                                        <th class="text-right">Total</th>
                                    </tr>
                                    <?php while($product = $top_products->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $product['product_name']; ?></td>
                                        <td class="text-right"><?php echo $product['qty']; ?></td>
                                        <td class="text-right"><?php echo number_format($product['total'], 2); ?></td>
                                    </tr>
                                    <?php endwhile; ?>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <strong>Expense Breakdown</strong>
                            </div>
                            <div class="panel-body">
                                <?php 
                                $top_expenses = $db->query("
                                    SELECT subject, SUM(amount) as total 
                                    FROM expenses 
                                    WHERE date BETWEEN '$from_date' AND '$to_date'
                                    GROUP BY subject 
                                    ORDER BY total DESC 
                                    LIMIT 5
                                ");
                                ?>
                                <table class="table table-condensed">
                                    <tr>
                                        <th>Expense Category</th>
                                        <th class="text-right">Amount</th>
                                    </tr>
                                    <?php while($expense = $top_expenses->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $expense['subject']; ?></td>
                                        <td class="text-right"><?php echo number_format($expense['total'], 2); ?></td>
                                    </tr>
                                    <?php endwhile; ?>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    /* Hide non-print UI elements */
    header,
    .main-header,
    .header,
    .navbar,
    nav,
    .panel-heading,
    .sidebar,
    .top-bar,
    .main-menu,
    .breadcrumb,
    .page-title,
    .footer,
    footer,
    .logo,
    .btn,
    .form-inline,
    .form-group,
    .modal,
    .no-print {
        display: none !important;
    }

    /* Page setup to remove default margins */
    @page {
        size: A4 portrait;
        margin: 0;
    }

    html, body {
        padding-right: 250px;
        margin: 0 !important;
        padding: 0 !important;
        width: 100% !important;
        height: 100% !important;
        background: #fff !important;
        color: #000 !important;
        font-size: 13px;
        font-family: "Arial", sans-serif;
        line-height: 1.5;
    }

    /* Force all main containers to remove margin/padding */
    .container, .row, .col-md-12, .col-md-8, .col-md-6, .col-md-offset-2, main, section, article {
        margin: 0 !important;
        padding: 0 !important;
        width: 100% !important;
        float: none !important;
    }

    /* Reset box sizing globally */
    * {
        background: transparent !important;
        box-shadow: none !important;
        box-sizing: border-box;
    }

    /* Tables: clean and full width */
    table {
        width: 100% !important;
        border-collapse: collapse;
        margin: 0 0 20px 0 !important;
        font-size: 13px;
    }

    thead {
        background-color: #f0f0f0 !important;
    }

    th, td {
        border: 1px solid #444 !important;
        padding: 8px;
        text-align: left;
        vertical-align: top;
    }

    tr:nth-child(even) {
        background-color: #f9f9f9 !important;
    }

    tr:hover {
        background-color: #f1f1f1 !important;
    }

    th {
        background-color: #eaeaea !important;
        font-weight: bold;
    }

    /* Text alignment helpers */
    .text-right {
        text-align: right !important;
    }

    .text-center {
        text-align: center !important;
    }

    /* Print title formatting */
    .print-title {
        font-size: 20px;
        font-weight: bold;
        text-align: center;
        margin-bottom: 20px;
        border-bottom: 2px solid #000;
        padding-bottom: 10px;
    }

    /* Table row highlights */
    .success td {
        background-color: #d4edda !important;
        font-weight: bold;
    }

    .danger td {
        background-color: #f8d7da !important;
        font-weight: bold;
    }

    /* Prevent page breaks in critical blocks */
    table, tr, td, th, .container, .row, .print-title {
        page-break-inside: avoid !important;
    }
}
</style>





<?php
// Include the footer
include_once('layouts/footer.php');
?>