<?php
// Start the session
session_start();

// Page title
$page_title = 'Expense Management';

// Include necessary files
require_once('includes/load.php');

// Handle language change
if (isset($_POST['change_language'])) {
    $selected_language = $_POST['language'];
    $_SESSION['language'] = $selected_language;
    $session->msg("s", "Language changed successfully.");
    redirect('expence.php', false);
}

// Handle expense submission
if (isset($_POST['add_expense'])) {
    $req_fields = array('expense_date', 'subject', 'amount', 'description');
    validate_fields($req_fields);

    if (empty($errors)) {
        $date = remove_junk($db->escape($_POST['expense_date']));
        $subject = remove_junk($db->escape($_POST['subject']));
        $amount = remove_junk($db->escape($_POST['amount']));
        $description = remove_junk($db->escape($_POST['description']));

        $query = "INSERT INTO expenses (date, subject, amount, description) VALUES ('$date', '$subject', '$amount', '$description')";
        
        if ($db->query($query)) {
            $session->msg("s", "Expense added successfully.");
            redirect('expence.php', false);
        } else {
            $session->msg("d", "Sorry, failed to add expense.");
            redirect('expence.php', false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('expence.php', false);
    }
}

// Get date range filter if set
$from_date = isset($_GET['from_date']) ? $_GET['from_date'] : date('Y-m-01');
$to_date = isset($_GET['to_date']) ? $_GET['to_date'] : date('Y-m-d');

// Fetch expenses based on date range
$expenses_query = "SELECT * FROM expenses WHERE date BETWEEN '$from_date' AND '$to_date' ORDER BY date DESC";
$expenses = $db->query($expenses_query);

// Calculate total expenses
$total_expense = 0;
if ($expenses->num_rows > 0) {
    while($expense = $expenses->fetch_assoc()) {
        $total_expense += $expense['amount'];
    }
    // Reset pointer
    $expenses->data_seek(0);
}

// Include the header
include_once('layouts/header.php');
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong>
                    <span class="glyphicon glyphicon-th"></span>
                    <span>Add New Expense</span>
                </strong>
            </div>
            <div class="panel-body">
                <form method="post" action="expence.php" class="clearfix">
                    <div class="form-group">
                        <label for="expense_date">Date</label>
                        <input type="date" class="form-control" name="expense_date" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <input type="text" class="form-control" name="subject" placeholder="e.g. Rent, Utilities, Supplies" required>
                    </div>
                    <div class="form-group">
                        <label for="amount">Amount</label>
                        <div class="input-group">
                            <span class="input-group-addon">$</span>
                            <input type="number" step="0.01" class="form-control" name="amount" placeholder="0.00" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" name="description" rows="2" placeholder="Details about this expense"></textarea>
                    </div>
                    <button type="submit" name="add_expense" class="btn btn-primary">Add Expense</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong>
                    <span class="glyphicon glyphicon-list"></span>
                    <span>Expense Records</span>
                </strong>
                <div class="pull-right">
                    <form method="get" action="expence.php" class="form-inline">
                        <div class="form-group">
                            <label>From:</label>
                            <input type="date" class="form-control input-sm" name="from_date" value="<?php echo $from_date; ?>">
                        </div>
                        <div class="form-group">
                            <label>To:</label>
                            <input type="date" class="form-control input-sm" name="to_date" value="<?php echo $to_date; ?>">
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                        <a href="print_expenses.php?from_date=<?php echo $from_date; ?>&to_date=<?php echo $to_date; ?>" class="btn btn-default btn-sm" target="_blank">
                            <span class="glyphicon glyphicon-print"></span> Print
                        </a>
                    </form>
                </div>
            </div>
            <div class="panel-body">
                <table class="table table-bordered table-striped">
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
                            <tr>
                                <td><?php echo htmlentities($expense['date']); ?></td>
                                <td><?php echo htmlentities($expense['subject']); ?></td>
                                <td><?php echo htmlentities($expense['description']); ?></td>
                                <td class="text-right">$<?php echo number_format($expense['amount'], 2); ?></td>
                            </tr>
                            <?php endwhile; ?>
                            <tr class="info">
                                <td colspan="3" class="text-right"><strong>Total:</strong></td>
                                <td class="text-right"><strong>$<?php echo number_format($total_expense, 2); ?></strong></td>
                            </tr>
                        <?php else: ?>
                            <tr>
                                <td colspan="4">No expenses found for the selected period</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
// Include the footer
include_once('layouts/footer.php');
?>