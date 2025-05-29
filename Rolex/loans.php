<?php
// Page title
$page_title = 'Loans Management';

// Include necessary files
require_once('includes/load.php');

// Handle language change
if (isset($_POST['change_language'])) {
    $selected_language = $_POST['language'];
    $_SESSION['language'] = $selected_language;
    $session->msg("s", "Language changed successfully.");
    redirect('loans.php', false);
}

// Handle loan submission
if (isset($_POST['add_loan'])) {
    $req_fields = array('borrower_name', 'loan_amount', 'date_issued', 'due_date');
    validate_fields($req_fields);

    if (empty($errors)) {
        $borrower = remove_junk($db->escape($_POST['borrower_name']));
        $amount = remove_junk($db->escape($_POST['loan_amount']));
        $interest = isset($_POST['interest_rate']) ? remove_junk($db->escape($_POST['interest_rate'])) : 0;
        $issued = remove_junk($db->escape($_POST['date_issued']));
        $due = remove_junk($db->escape($_POST['due_date']));
        $desc = remove_junk($db->escape($_POST['description']));

        $query = "INSERT INTO loans (borrower_name, amount, interest_rate, date_issued, due_date, description) 
                 VALUES ('$borrower', '$amount', '$interest', '$issued', '$due', '$desc')";
        
        if ($db->query($query)) {
            $session->msg("s", "Loan added successfully.");
            redirect('loans.php', false);
        } else {
            $session->msg("d", "Sorry, failed to add loan.");
            redirect('loans.php', false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('loans.php', false);
    }
}

// Handle loan payment
if (isset($_POST['add_payment'])) {
    $req_fields = array('loan_id', 'payment_amount', 'payment_date');
    validate_fields($req_fields);

    if (empty($errors)) {
        $loan_id = remove_junk($db->escape($_POST['loan_id']));
        $amount = remove_junk($db->escape($_POST['payment_amount']));
        $date = remove_junk($db->escape($_POST['payment_date']));
        $notes = remove_junk($db->escape($_POST['payment_notes']));

        // Add payment record
        $payment_query = "INSERT INTO loan_payments (loan_id, amount, payment_date, notes) 
                         VALUES ('$loan_id', '$amount', '$date', '$notes')";
        
        if ($db->query($payment_query)) {
            // Update loan status if fully paid
            $update_query = "UPDATE loans SET paid_amount = paid_amount + $amount 
                            WHERE id = $loan_id";
            $db->query($update_query);
            
            $session->msg("s", "Payment recorded successfully.");
            redirect('loans.php', false);
        } else {
            $session->msg("d", "Sorry, failed to record payment.");
            redirect('loans.php', false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('loans.php', false);
    }
}

// Get date range filter if set
$from_date = isset($_GET['from_date']) ? $_GET['from_date'] : date('Y-m-01');
$to_date = isset($_GET['to_date']) ? $_GET['to_date'] : date('Y-m-d');

// Fetch loans based on date range
$loans_query = "SELECT l.*, 
               (SELECT SUM(amount) FROM loan_payments WHERE loan_id = l.id) AS paid_amount
               FROM loans l 
               WHERE date_issued BETWEEN '$from_date' AND '$to_date' 
               ORDER BY date_issued DESC";
$loans = $db->query($loans_query);

// Include the header
include_once('layouts/header.php');
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong>
                    <span class="glyphicon glyphicon-th"></span>
                    <span>Loan Management</span>
                </strong>
                <div class="pull-right">
                    <form method="get" action="loans.php" class="form-inline">
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
            <div class="panel-body">
                <!-- Add New Loan Form -->
                <div class="panel panel-default" style="margin-bottom: 20px;">
                    <div class="panel-heading">
                        <strong>Add New Loan</strong>
                    </div>
                    <div class="panel-body">
                        <form method="post" action="loans.php" class="clearfix">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Borrower Name</label>
                                        <input type="text" class="form-control" name="borrower_name" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Loan Amount</label>
                                        <input type="number" step="0.01" class="form-control" name="loan_amount" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Interest Rate (%)</label>
                                        <input type="number" step="0.01" class="form-control" name="interest_rate" value="0">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Date Issued</label>
                                        <input type="date" class="form-control" name="date_issued" value="<?php echo date('Y-m-d'); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Due Date</label>
                                        <input type="date" class="form-control" name="due_date" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Description</label>
                                <textarea class="form-control" name="description" rows="2"></textarea>
                            </div>
                            <button type="submit" name="add_loan" class="btn btn-primary">Add Loan</button>
                        </form>
                    </div>
                </div>

                <!-- Loans Table -->
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Borrower</th>
                            <th>Amount</th>
                            <th>Interest</th>
                            <th>Issued Date</th>
                            <th>Due Date</th>
                            <th>Paid</th>
                            <th>Balance</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($loans->num_rows > 0): ?>
                            <?php while($loan = $loans->fetch_assoc()): 
                                $paid = $loan['paid_amount'] ?? 0;
                                $balance = $loan['amount'] - $paid;
                                $status = ($balance <= 0) ? 'Paid' : 
                                         (strtotime($loan['due_date']) < time() ? 'Overdue' : 'Active');
                            ?>
                            <tr>
                                <td><?php echo htmlentities($loan['borrower_name']); ?></td>
                                <td class="text-right"><?php echo number_format($loan['amount'], 2); ?></td>
                                <td class="text-right"><?php echo $loan['interest_rate']; ?>%</td>
                                <td><?php echo date('m/d/Y', strtotime($loan['date_issued'])); ?></td>
                                <td><?php echo date('m/d/Y', strtotime($loan['due_date'])); ?></td>
                                <td class="text-right"><?php echo number_format($paid, 2); ?></td>
                                <td class="text-right"><?php echo number_format($balance, 2); ?></td>
                                <td>
                                    <span class="label label-<?php 
                                        echo $status == 'Paid' ? 'success' : 
                                             ($status == 'Overdue' ? 'danger' : 'primary'); 
                                    ?>">
                                        <?php echo $status; ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-xs btn-info" data-toggle="modal" data-target="#paymentModal<?php echo $loan['id']; ?>">
                                        Add Payment
                                    </button>
                                    <a href="view_loan.php?id=<?php echo $loan['id']; ?>" class="btn btn-xs btn-warning">
                                        Details
                                    </a>
                                </td>
                            </tr>
                            
                            <!-- Payment Modal -->
                            <div class="modal fade" id="paymentModal<?php echo $loan['id']; ?>" tabindex="-1" role="dialog">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                            <h4 class="modal-title">Add Payment for <?php echo htmlentities($loan['borrower_name']); ?></h4>
                                        </div>
                                        <form method="post" action="loans.php">
                                            <div class="modal-body">
                                                <input type="hidden" name="loan_id" value="<?php echo $loan['id']; ?>">
                                                <div class="form-group">
                                                    <label>Payment Amount</label>
                                                    <input type="number" step="0.01" class="form-control" name="payment_amount" max="<?php echo $balance; ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label>Payment Date</label>
                                                    <input type="date" class="form-control" name="payment_date" value="<?php echo date('Y-m-d'); ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label>Notes</label>
                                                    <textarea class="form-control" name="payment_notes" rows="2"></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                <button type="submit" name="add_payment" class="btn btn-primary">Record Payment</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9">No loans found for the selected period</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    /* Hide non-print elements */
    .no-print,
    .sidebar,
    .header,
    .top-bar,
    .main-menu,
    .navbar,
    .nav,
    footer,
    .footer,
    .logo,
    .page-title,
    .breadcrumb,
    .btn,
    .form-inline,
    .form-group,
    .panel-heading,
    .modal,
    .pull-right,
    .print-hidden {
        display: none !important;
    }

    /* Reset and style base */
    body, html {
        margin: 0;
        padding: 20px;
        font-size: 13px;
        font-family: "Arial", sans-serif;
        background: #fff;
        color: #000;
    }

    /* Printable content area */
    .printable-area {
        width: 100%;
    }

    .panel-body {
        padding: 0;
        margin: 0;
    }

    /* Table styling */
    table {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid #444;
        margin-top: 10px;
    }

    thead {
        background-color: #f0f0f0;
    }

    th, td {
        border: 1px solid #444;
        padding: 8px;
        text-align: left;
        vertical-align: top;
    }

    tr:nth-child(even) {
        background-color: #fafafa;
    }

    tr:hover {
        background-color: #f1f1f1;
    }

    th {
        background-color: #eaeaea;
        font-weight: bold;
    }

    /* Label styling */
    .label {
        color: #000 !important;
        background: #e0e0e0 !important;
        border: 1px solid #000 !important;
        padding: 3px 6px !important;
        font-size: 11px !important;
        border-radius: 4px;
        display: inline-block;
    }

    /* Optional: Headline for printed reports */
    .print-title {
        font-size: 18px;
        font-weight: bold;
        text-align: center;
        margin-bottom: 20px;
    }

    /* Remove background and shadows */
    * {
        background: transparent !important;
        box-shadow: none !important;
    }
}
</style>


<?php
// Include the footer
include_once('layouts/footer.php');
?>