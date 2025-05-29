<?php
// Database connection with error handling
$conn = new mysqli("localhost", "root", "", "inventory_system");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Validate and sanitize input
if (!isset($_GET['sale_id']) || !is_numeric($_GET['sale_id'])) {
    die("Invalid Sale ID.");
}

$sale_id = intval($_GET['sale_id']);

// Prepared statement for security
$stmt = $conn->prepare("SELECT * FROM sales1 WHERE id = ?");
$stmt->bind_param("i", $sale_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Sale not found.");
}

$sale = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Invoice #<?= $sale['id'] ?></title>
    <link rel="stylesheet" href="css/print_invoice.css">
</head>

<body onload="window.print()">
    <div class="invoice-box">
        <div class="watermark">INVOICE</div>

        <div class="header">
            <div class="company-info">
                <img src="libs/images/LogoRolex.png" alt="Company Logo" style="max-width: 150px; margin-bottom: 10px;">
                <h1>Rolex</h1>
                <p>123 Business St, City, Country</p>
                <p>Phone: +1 (555) 123-4567 | Email: info@company.com</p>
            </div>


            <div class="invoice-info">
                <h2>INVOICE</h2>
                <p>#<?= $sale['id'] ?></p>
                <p>Date: <?= date('M d, Y', strtotime($sale['date'])) ?></p>
                <p>Due Date: <?= date('M d, Y', strtotime('+7 days', strtotime($sale['date']))) ?></p>
            </div>
        </div>

        <div class="two-columns">
            <div class="column">
                <div class="section">
                    <div class="section-title">Bill To</div>
                    <div class="info">
                        <span class="info-label">Name:</span>
                        <span class="info-value"><?= htmlspecialchars($sale['customer_name']) ?></span>
                    </div>
                    <div class="info">
                        <span class="info-label">Phone:</span>
                        <span class="info-value"><?= htmlspecialchars($sale['customer_phone']) ?></span>
                    </div>
                    <div class="info">
                        <span class="info-label">Address:</span>
                        <span class="info-value">123 Customer St, City</span>
                    </div>
                </div>
            </div>


        </div>

        <div class="section">
            <div class="section-title">Items</div>
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th>Serial No</th>
                        <th class="text-center">Price</th>
                        <th class="text-right">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?= htmlspecialchars($sale['product_name']) ?></td>
                        <td><?= htmlspecialchars($sale['serial_number']) ?></td>
                        <td class="text-right">$<?= number_format($sale['price'], 2) ?></td>
                        <td class="text-right">$<?= number_format($sale['total'], 2) ?></td>
                    </tr>
                    <!-- Additional items would go here -->
                </tbody>
            </table>
        </div>

        <div class="total-section">
            <div class="total-row grand-total">
                <span class="total-label">Total Amount:</span>
                <span class="total-value">$<?= number_format($sale['total'], 2) ?></span>
            </div>
        </div>



        <div class="footer">
            <p>Thank you for your Purchase !</p>
            <p class="no-print">This invoice was generated on <?= date('M d, Y h:i A') ?></p>
        </div>
    </div>

    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()"
            style="padding: 10px 20px; background: #4b6cb7; color: white; border: none; border-radius: 4px; cursor: pointer;">Print
            Invoice</button>
        <button onclick="window.close()"
            style="padding: 10px 20px; background: #666; color: white; border: none; border-radius: 4px; cursor: pointer; margin-left: 10px;">Close
            Window</button>
    </div>
</body>

</html>