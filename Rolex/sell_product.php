<?php
$conn = new mysqli("localhost", "root", "", "inventory_system");

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $quantity = intval($_POST['quantity']);
    $price = floatval($_POST['price']);
    $buy_price = floatval($_POST['buy_price']);
    $total = $quantity * $price;
    $date = date('Y-m-d H:i:s');
    $customer_name = $_POST['customer_name'];
    $customer_phone = $_POST['customer_phone'];
    $serial_number = $_POST['serial_number'];
    $reference_number = $_POST['reference_number'];
    $sold_by = 1; // Replace with logged-in user ID

    $conn->begin_transaction();
    try {
        $stmt = $conn->prepare("INSERT INTO sales1 (product_id, product_name, quantity, price, buy_price, total, date, customer_name, customer_phone, serial_number, reference_number, sold_by)
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isidddsssssi", $product_id, $product_name, $quantity, $price, $buy_price, $total, $date, $customer_name, $customer_phone, $serial_number, $reference_number, $sold_by);
        $stmt->execute();

        $last_id = $conn->insert_id;

        $conn->query("DELETE FROM products WHERE id = $product_id");

        $conn->commit();

        // Redirect if "Sell & Print" was clicked
        if (isset($_POST['sell_and_print'])) {
            header("Location: print_invoice.php?sale_id=" . $last_id);
            exit;
        } else {
            echo "<div class='alert success'><i class='fas fa-check-circle'></i> Product sold successfully!</div>";
        }
    } catch (Exception $e) {
        $conn->rollback();
        echo "<div class='alert error'><i class='fas fa-exclamation-circle'></i> Failed to sell: {$e->getMessage()}</div>";
    }
}


// Handle date filter - default to today
$today = date('Y-m-d');
$date_from = isset($_GET['date_from']) ? $conn->real_escape_string($_GET['date_from']) : $today;
$date_to = isset($_GET['date_to']) ? $conn->real_escape_string($_GET['date_to']) : $today;

$date_filter = " WHERE date BETWEEN '$date_from 00:00:00' AND '$date_to 23:59:59'";
$sales_result = $conn->query("SELECT * FROM sales1 $date_filter ORDER BY date DESC");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Sell Product | Inventory System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/sell_product.css">
    <style>
        .btn-print {
    background-color: #3498db;
    color: white;
    padding: 4px 10px;
    border-radius: 4px;
    text-decoration: none;
}
.btn-print:hover {
    background-color: #2980b9;
}

    </style>

</head>

<body>


    <div class="container">
        <?php include 'css/sellproductbar.php'; ?>
        <h2><i class="fas fa-cash-register"></i> Product Sales</h2>

        <!-- Search and Sell Form -->
        <form method="GET" class="search-form">
            <input type="text" name="search" placeholder="Search by name, code, reference, serial or price..." required>
            <button type="submit"><i class="fas fa-search"></i> Search</button>
        </form>

        <?php
        if (isset($_GET['search'])) {
            $search = $conn->real_escape_string($_GET['search']);

            $result = $conn->query("
                SELECT * FROM products 
                WHERE name LIKE '%$search%' 
                OR product_code LIKE '%$search%'
                OR reference_number LIKE '%$search%'
                OR serial_number LIKE '%$search%'
                OR sale_price = '$search'
            ");

            if ($result->num_rows > 0) {
                $product = $result->fetch_assoc();
                ?>

                <form method="POST" class="product-form">
                    <div class="product-info">
                        <p><strong>Product:</strong> <?= $product['name'] ?> (<?= $product['product_code'] ?>)</p>
                        <p><strong>Buy Price:</strong> $<?= number_format($product['buy_price'], 2) ?></p>
                        <p><strong>Sale Price:</strong> $<?= number_format($product['sale_price'], 2) ?></p>
                        <p><strong>Available:</strong> 1 (single product)</p>
                    </div>

                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                    <input type="hidden" name="product_name" value="<?= $product['name'] ?>">
                    <input type="hidden" name="price" value="<?= $product['sale_price'] ?>">
                    <input type="hidden" name="buy_price" value="<?= $product['buy_price'] ?>">

                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-hashtag"></i> Quantity</label>
                            <input type="number" name="quantity" min="1" max="1" value="1" required>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-calendar-day"></i> Date</label>
                            <input type="date" name="date" value="<?= date('Y-m-d') ?>" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-user"></i> Customer Name</label>
                            <input type="text" name="customer_name" required>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-phone"></i> Customer Phone</label>
                            <input type="text" name="customer_phone" placeholder="Optional">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-barcode"></i> Serial Number</label>
                            <input type="text" name="serial_number" value="<?= $product['serial_number'] ?>">
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-file-alt"></i> Reference Number</label>
                            <input type="text" name="reference_number" value="<?= $product['reference_number'] ?>">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-cart-plus"></i> Sell Product
                    </button>
                    <button type="submit" name="sell_and_print" class="btn btn-secondary">
                        <i class="fas fa-print"></i> Sell & Print
                    </button>
                </form>

                <?php
            } else {
                echo "<div class='no-product'><i class='fas fa-box-open'></i> No product found matching your search</div>";
            }
        }
        ?>

        <!-- Sales Table -->
        <h3 class="section-title"><i class="fas fa-history"></i> Recent Sales</h3>

        <form method="GET" class="date-filter">
            <input type="hidden" name="search"
                value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
            <div class="form-group">
                <label for="date_from">From Date</label>
                <input type="date" name="date_from" id="date_from" value="<?= $date_from ?>">
            </div>
            <div class="form-group">
                <label for="date_to">To Date</label>
                <input type="date" name="date_to" id="date_to" value="<?= $date_to ?>">
            </div>
            <button type="submit"><i class="fas fa-filter"></i> Filter</button>
            <?php if (isset($_GET['date_from']) || isset($_GET['date_to'])): ?>
                <a href="?" class="reset-filter"><i class="fas fa-times"></i> Reset Filter</a>
            <?php endif; ?>
        </form>

        <?php if ($sales_result->num_rows > 0): ?>
            <table class="sales-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Product</th>
                        <th>Customer</th>
                        <th>Buy Price</th>
                        <th>Sell Price</th>
                        <th>Total</th>
                        <th>Reference</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($sale = $sales_result->fetch_assoc()): ?>
                        <tr>
                            <td><?= date('M d, Y h:i A', strtotime($sale['date'])) ?></td>
                            <td><?= $sale['product_name'] ?></td>
                            <td><?= $sale['customer_name'] ?></td>
                            <td>$<?= number_format($sale['buy_price'], 2) ?></td>
                            <td>$<?= number_format($sale['price'], 2) ?></td>
                            <td>$<?= number_format($sale['total'], 2) ?></td>
                            <td><?= $sale['reference_number'] ?></td>
                            <td><span class="badge badge-success">Completed</span></td>
                            <td>
                                <a href="print_invoice.php?sale_id=<?= $sale['id'] ?>" target="_blank"
                                    class="btn btn-sm btn-print">
                                    <i class="fas fa-print"></i> Print
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="no-sales"><i class="fas fa-chart-line"></i> No sales records found for selected date range</div>
        <?php endif; ?>
    </div>

    <script>
        document.querySelector('.product-form')?.addEventListener('submit', function () {
            const btn = this.querySelector('button[type="submit"]');
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            btn.disabled = true;
        });

        document.addEventListener('DOMContentLoaded', function () {
            const dateFrom = document.getElementById('date_from');
            const dateTo = document.getElementById('date_to');
            const today = new Date().toISOString().split('T')[0];

            if (dateFrom && !dateFrom.value) dateFrom.value = today;
            if (dateTo && !dateTo.value) dateTo.value = today;
        });
    </script>
</body>

</html>