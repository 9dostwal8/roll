<?php

require_once('includes/load.php');

// Get date range from URL
$from_date = isset($_GET['from_date']) ? $_GET['from_date'] : date('Y-m-01');
$to_date = isset($_GET['to_date']) ? $_GET['to_date'] : date('Y-m-d');

// Fetch expenses
$expenses_query = "SELECT * FROM expenses WHERE date BETWEEN '$from_date' AND '$to_date' ORDER BY date DESC";
$expenses = $db->query($expenses_query);

// Calculate total
$total = 0;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Expense Report: <?php echo $from_date; ?> to <?php echo $to_date; ?></title>
    <style>
        body { font-family: Arial; }
        .report-header { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background-color: #f2f2f2; }
        .text-right { text-align: right; }
        .total-row { font-weight: bold; }
        @media print {
            .no-print { display: none; }
            body { padding: 20px; }
        }
    </style>
</head>
<body>
    <div class="report-header">
        <h2>Expense Report</h2>
        <h3><?php echo date('F j, Y', strtotime($from_date)); ?> to <?php echo date('F j, Y', strtotime($to_date)); ?></h3>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Subject</th>
                <th>Description</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            <?php if($expenses->num_rows > 0): ?>
                <?php while($expense = $expenses->fetch_assoc()): ?>
                <?php $total += $expense['amount']; ?>
                <tr>
                    <td><?php echo date('m/d/Y', strtotime($expense['date'])); ?></td>
                    <td><?php echo htmlentities($expense['subject']); ?></td>
                    <td><?php echo htmlentities($expense['description']); ?></td>
                    <td class="text-right">$<?php echo number_format($expense['amount'], 2); ?></td>
                </tr>
                <?php endwhile; ?>
                <tr class="total-row">
                    <td colspan="3" class="text-right">Total Expenses:</td>
                    <td class="text-right">$<?php echo number_format($total, 2); ?></td>
                </tr>
            <?php else: ?>
                <tr>
                    <td colspan="4">No expenses found for this period</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    
    <div class="no-print" style="margin-top: 20px; text-align: center;">
        <button onclick="window.print()" class="btn btn-primary">Print Report</button>
        <button onclick="window.close()" class="btn btn-default">Close</button>
    </div>
</body>
</html>