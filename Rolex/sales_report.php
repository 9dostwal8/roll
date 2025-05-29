<?php
$page_title = 'Sale Report';
require_once('includes/load.php');
page_require_level(1);

// Initialize variables
$sales = array();
$report_type = $_POST['report_type'] ?? 'daily';
$start_date = $_POST['start_date'] ?? date('Y-m-d');
$end_date = $_POST['end_date'] ?? date('Y-m-d');
$report_title = '';

// Handle form
if (isset($_POST['generate_report'])) {
    if ($report_type == 'custom') {
        $report_title = "Custom Report from " . format_date($start_date) . " to " . format_date($end_date);
        $sales = get_sales_by_date_range($start_date, $end_date);
    } elseif ($report_type == 'daily') {
        $report_title = "Daily Report for " . format_date($start_date);
        $sales = get_sales_by_date($start_date);
    } elseif ($report_type == 'weekly') {
        $report_title = "Weekly Report for Week of " . format_date($start_date);
        $end_date = date('Y-m-d', strtotime($start_date . ' + 6 days'));
        $sales = get_sales_by_date_range($start_date, $end_date);
    } elseif ($report_type == 'monthly') {
        $report_title = "Monthly Report for " . date('F Y', strtotime($start_date));
        $end_date = date('Y-m-t', strtotime($start_date));
        $sales = get_sales_by_date_range($start_date, $end_date);
    }
}

// Sales functions
function get_sales_by_date_range($start_date, $end_date) {
    global $db;
    $start = $start_date . " 00:00:00";
    $end = $end_date . " 23:59:59";
    $sql = "SELECT s.*, p.name AS product_name FROM sales1 s 
            LEFT JOIN products p ON s.product_id = p.id 
            WHERE s.date BETWEEN '{$start}' AND '{$end}' 
            ORDER BY s.date DESC";
    $result = $db->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function get_sales_by_date($date) {
    return get_sales_by_date_range($date, $date);
}

function format_date($date) {
    return date('M j, Y', strtotime($date));
}

function calculate_totals($sales) {
    $total_quantity = 0;
    $total_amount = 0;
    $total_profit = 0;

    foreach ($sales as $sale) {
        $total_quantity += $sale['quantity'];
        $total_amount += $sale['total'];
        $profit = ($sale['price'] - $sale['buy_price']) * $sale['quantity'];
        $total_profit += $profit;
    }

    return [
        'total_quantity' => $total_quantity,
        'total_amount' => $total_amount,
        'total_profit' => $total_profit
    ];
}
?>

<?php include_once('layouts/header.php'); ?>

<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong><span class="glyphicon glyphicon-th"></span> Sales Report</strong>
      </div>
      <div class="panel-body">
        <form method="post" action="" class="clearfix">
          <div class="form-group">
            <label for="report_type">Report Type</label>
            <select class="form-control" name="report_type" id="report_type" onchange="updateDateFields()">
              <option value="daily" <?php echo ($report_type == 'daily') ? 'selected' : ''; ?>>Daily</option>
              <option value="weekly" <?php echo ($report_type == 'weekly') ? 'selected' : ''; ?>>Weekly</option>
              <option value="monthly" <?php echo ($report_type == 'monthly') ? 'selected' : ''; ?>>Monthly</option>
              <option value="custom" <?php echo ($report_type == 'custom') ? 'selected' : ''; ?>>Custom</option>
            </select>
          </div>

          <div class="form-group" id="start_date_group">
            <label for="start_date">Start Date</label>
            <input type="date" class="form-control" name="start_date" id="start_date" value="<?php echo $start_date; ?>">
          </div>

          <div class="form-group" id="end_date_group" style="display: <?php echo ($report_type == 'custom') ? 'block' : 'none'; ?>">
            <label for="end_date">End Date</label>
            <input type="date" class="form-control" name="end_date" id="end_date" value="<?php echo $end_date; ?>">
          </div>

          <button type="submit" name="generate_report" class="btn btn-primary">Generate Report</button>
          <?php if (!empty($sales)): ?>
            <button type="button" class="btn btn-success" onclick="window.print()">Print Report</button>
          <?php endif; ?>
        </form>

        <?php if (!empty($sales)): ?>
          <div class="report-section" id="report-section" style="padding-top: -10px;">
            <div class="print-header text-center">
              <img src="libs/images/LogoRolex.png" alt="Company Logo" style="height: 80px; margin-bottom: 10px;">
              <p style="margin: 0;">Phone: +964 750 000 0000</p>
              <p style="margin-bottom: 10px;"><?php echo $report_title; ?></p>
              <hr style="border: 1px solid #000;">
            </div>

            <p class="text-center">Generated on: <?php echo date('M j, Y H:i:s'); ?></p>

            <?php $totals = calculate_totals($sales); ?>
            <div class="summary-box">
              <h4>Summary</h4>
              <p>Total Items Sold: <?php echo $totals['total_quantity']; ?></p>
              <p>Total Sales Amount: $ <?php echo number_format($totals['total_amount'], 2); ?></p>
              <p>Total Profit: $ <?php echo number_format($totals['total_profit'], 2); ?></p>
            </div>

            <table class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>Date</th>
                  <th>Product</th>
                  <th>Buy Price</th>
                  <th>Sale Price</th>
                  <th>Total</th>
                  <th>Profit</th>
                  <th>Customer</th>
                  <th>Reference</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($sales as $sale):
                    $profit = ($sale['price'] - $sale['buy_price']) * $sale['quantity'];
                ?>
                  <tr>
                    <td><?php echo format_date($sale['date']); ?></td>
                    <td><?php echo $sale['product_name']; ?></td>
                    <td><?php echo number_format($sale['buy_price'], 2); ?></td>
                    <td><?php echo number_format($sale['price'], 2); ?></td>
                    <td><?php echo number_format($sale['total'], 2); ?></td>
                    <td><?php echo number_format($profit, 2); ?></td>
                    <td><?php echo $sale['customer_name']; ?></td>
                    <td><?php echo $sale['reference_number']; ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
              <tfoot>
                <tr>
                  <th colspan="4">Totals</th>
                  
                  <th><?php echo number_format($totals['total_amount'], 2); ?></th>
                  <th><?php echo number_format($totals['total_profit'], 2); ?></th>
                  <th colspan="2"></th>
                </tr>
              </tfoot>
            </table>
          </div>
        <?php elseif (isset($_POST['generate_report'])): ?>
          <div class="alert alert-info">No sales found for the selected period.</div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<script>
function updateDateFields() {
  var type = document.getElementById('report_type').value;
  var endGroup = document.getElementById('end_date_group');
  var startInput = document.getElementById('start_date');
  if (type === 'custom') {
    endGroup.style.display = 'block';
    startInput.type = 'date';
  } else {
    endGroup.style.display = 'none';
    startInput.type = (type === 'weekly') ? 'week' : (type === 'monthly') ? 'month' : 'date';
  }
}
window.onload = updateDateFields;
</script>

<style>
@media print {
  * {
    visibility: hidden;
  }

  body, html {
    margin: 0;
    padding: 20px;
    font-family: Arial, sans-serif;
    color: #000;
  }

  #report-section, #report-section * {
    visibility: visible;
  }

  #report-section {
    position: fixed;
    left: 0;
    top: 0;
    width: 100%;
  }

  .print-header {
    text-align: center;
    margin-bottom: 20px;
  }

  .summary-box {
    border: 1px solid #000;
    padding: 10px;
    background-color: #f2f2f2;
    margin-top: 15px;
  }

  .summary-box h4 {
    border-bottom: 1px solid #888;
    margin-bottom: 10px;
    font-weight: bold;
  }

  table.table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    font-size: 12px;
  }

  .table th, .table td {
    border: 1px solid #000;
    padding: 6px;
    text-align: center;
  }

  .table th {
    background-color: #e0e0e0;
  }

  tfoot th {
    background-color: #d0d0d0;
  }

  hr {
    border: none;
    border-top: 1px solid #000;
  }
}
</style>

<?php include_once('layouts/footer.php'); ?>
