<?php
$page_title = 'Edit Sale';
require_once('includes/load.php');
// Checking what level user has permission to view this page
page_require_level(3);

// Get conversion rate from database
$conversion_rate = get_conversion_rate();

if(!isset($_GET['id']) || empty($_GET['id'])) {
    $session->msg("d","Missing sale id.");
    redirect('sales.php');
}

$sale = find_by_id('sales', (int)$_GET['id']);
if(!$sale) {
    $session->msg("d","Invalid sale id.");
    redirect('sales.php');
}

$product = find_by_id('products', $sale['product_id']);

if(isset($_POST['update_sale'])) {
    $req_fields = array('title','quantity','price','total', 'date');
    validate_fields($req_fields);
    
    if(empty($errors)) {
        $p_id      = $db->escape((int)$product['id']);
        $s_qty     = $db->escape((int)$_POST['quantity']);
        $s_total   = $db->escape($_POST['total']);
        $date      = $db->escape($_POST['date']);
        $s_date    = date("Y-m-d", strtotime($date));

        $sql  = "UPDATE sales SET";
        $sql .= " product_id='{$p_id}', qty={$s_qty}, price='{$s_total}', date='{$s_date}'";
        $sql .= " WHERE id='{$sale['id']}'";
        
        $result = $db->query($sql);
        if($result && $db->affected_rows() === 1) {
            update_product_qty($s_qty, $p_id);
            $session->msg('s', "Sale updated.");
            redirect('edit_sale.php?id='.$sale['id'], false);
        } else {
            $session->msg('d', 'Sorry, failed to update!');
            redirect('sales.php', false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('edit_sale.php?id='.(int)$sale['id'], false);
    }
}
?>
<?php include_once('layouts/header.php'); ?>

<div class="row">
    <div class="col-md-12">
        <?php echo display_msg($msg); ?>
    </div>
</div>
<?php echo $lang['Sale']; ?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading clearfix">
                <strong>
                    <span class="glyphicon glyphicon-edit"></span>
                    <span><?php echo $lang['Edit Sale']; ?></span>
                </strong>
                <div class="pull-right">
                    <a href="sales.php" class="btn btn-default">
                        <i class="glyphicon glyphicon-chevron-left"></i><?php echo $lang['Back to Sales']; ?>
                    </a>
                    <button onclick="printReceipt()" class="btn btn-success">
                        <i class="glyphicon glyphicon-print"> </i><?php echo $lang['Print Receipt']; ?>
                    </button>
                </div>
            </div>
            <div class="panel-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th><?php echo $lang['Products']; ?></th>
                            <th><?php echo $lang['Quantity']; ?></th>
                            <th><?php echo $lang['Price (USD/IQD)']; ?></th>
                            <th><?php echo $lang['Total (USD/IQD)']; ?></th>
                            <th><?php echo $lang['Date']; ?></th>
                            <th><?php echo $lang['Actions']; ?></th>
                        </tr>
                    </thead>
                    <tbody id="product_info">
                        <tr>
                            <form method="post" action="edit_sale.php?id=<?php echo (int)$sale['id']; ?>">
                                <td>
                                    <input type="text" class="form-control" name="title" value="<?php echo remove_junk($product['name']); ?>" readonly>
                                </td>
                                <td>
                                    <input type="number" class="form-control" name="quantity" value="<?php echo (int)$sale['qty']; ?>" min="1">
                                </td>
                                <td>
                                    <div class="input-group">
                                        <span class="input-group-addon">$</span>
                                        <input type="text" class="form-control" name="price" value="<?php echo remove_junk($product['sale_price']); ?>" readonly>
                                        <span class="input-group-addon" style="border-left: 1px solid #ccc;">
                                            <?php echo number_format($product['sale_price'] * $conversion_rate, 2); ?> د.ع
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <div class="input-group">
                                        <span class="input-group-addon">$</span>
                                        <input type="text" class="form-control" name="total" value="<?php echo remove_junk($sale['price']); ?>">
                                        <span class="input-group-addon" style="border-left: 1px solid #ccc;">
                                            <?php echo number_format($sale['price'] * $conversion_rate, 2); ?> د.ع
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <input type="date" class="form-control datepicker" name="date" value="<?php echo remove_junk($sale['date']); ?>">
                                </td>
                                <td>
                                    <button type="submit" name="update_sale" class="btn btn-primary">
                                        <i class="glyphicon glyphicon-ok"></i><?php echo $lang['Update']; ?> 
                                    </button>
                                </td>
                            </form>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Enhanced Receipt Design with Dual Currency -->
<div id="receipt" style="display:none; font-family: 'Helvetica Neue', Arial, sans-serif; width: 320px; margin: 0 auto; padding: 20px; border: 2px solid #333; box-shadow: 0 0 15px rgba(0,0,0,0.2); background-color: #fff;">
    <!-- Header -->
    <div style="text-align: center; margin-bottom: 20px; border-bottom: 2px dashed #333; padding-bottom: 15px;">
        <h2 style="margin: 0; color: #333; font-size: 24px; font-weight: bold;">سکرابی وەستا فەرهاد</h2>
        <!-- <p style="margin: 5px 0 0; font-size: 14px; color: #666;">123 Business Street, City</p> -->
        <p style="margin: 3px 0 0; font-size: 14px; color: #666;">Phone: 0770 136 3823 - 0770 044 3911</p>
    </div>
    
    <!-- Receipt Info -->
    <div style="text-align: center; margin-bottom: 15px;">
        <h3 style="margin: 0 0 5px; font-size: 18px; color: #333;">پسوڵەی فرۆشتن</h3>
        <p style="margin: 0; font-size: 13px;">
            <strong>Receipt #:</strong> <?php echo str_pad($sale['id'], 6, '0', STR_PAD_LEFT); ?><br>
            <strong>Date:</strong> <?php echo date('M j, Y h:i A', strtotime($sale['date'])); ?><br>
            <strong>Exchange Rate:</strong> 1 USD = <?php echo $conversion_rate; ?> IQD
        </p>
    </div>
    
    <!-- Items Table -->
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 15px;">
        <thead>
            <tr style="border-bottom: 1px solid #333;">
                <th style="text-align: left; padding: 5px 0; font-size: 14px;">ITEM</th>
                <th style="text-align: center; padding: 5px 0; font-size: 14px;">QTY</th>
                <th style="text-align: right; padding: 5px 0; font-size: 14px;">PRICE</th>
                <th style="text-align: right; padding: 5px 0; font-size: 14px;">TOTAL</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="padding: 8px 0; font-size: 14px; border-bottom: 1px dashed #ccc;"><?php echo remove_junk($product['name']); ?></td>
                <td style="text-align: center; padding: 8px 0; font-size: 14px; border-bottom: 1px dashed #ccc;"><?php echo (int)$sale['qty']; ?></td>
                <td style="text-align: right; padding: 8px 0; font-size: 14px; border-bottom: 1px dashed #ccc;">
                    $<?php echo number_format($product['sale_price'], 2); ?><br>
                    <small><?php echo number_format($product['sale_price'] * $conversion_rate, 2); ?> د.ع</small>
                </td>
                <td style="text-align: right; padding: 8px 0; font-size: 14px; border-bottom: 1px dashed #ccc;">
                    $<?php echo number_format($sale['price'], 2); ?><br>
                    <small><?php echo number_format($sale['price'] * $conversion_rate, 2); ?> د.ع</small>
                </td>
            </tr>
        </tbody>
    </table>
    
    <!-- Totals -->
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
        <tr>
            <td style="text-align: right; padding: 5px 0; font-size: 14px;"><strong>Subtotal:</strong></td>
            <td style="text-align: right; padding: 5px 0; font-size: 14px; width: 30%;">
                $<?php echo number_format($sale['price'], 2); ?><br>
                <small><?php echo number_format($sale['price'] * $conversion_rate, 2); ?> د.ع</small>
            </td>
        </tr>
        <tr style="border-top: 1px solid #333;">
            <td style="text-align: right; padding: 8px 0; font-size: 16px; font-weight: bold;">TOTAL:</td>
            <td style="text-align: right; padding: 8px 0; font-size: 16px; font-weight: bold;">
                $<?php echo number_format($sale['price'], 2); ?><br>
                <small><?php echo number_format($sale['price'] * $conversion_rate, 2); ?> د.ع</small>
            </td>
        </tr>
    </table>
    
    <!-- Footer -->
    <div style="text-align: center; border-top: 2px dashed #333; padding-top: 15px;">
        <p style="margin: 5px 0; font-size: 12px; font-style: italic;">Thank you for your business!</p>
        <p style="margin: 5px 0; font-size: 11px; color: #666;"><?php echo date('M j, Y h:i A'); ?></p>
        <p style="margin: 5px 0; font-size: 11px; color: #666;">Staff: <?php echo $_SESSION['user_id']; ?></p>
    </div>
</div>

<script>
function printReceipt() {
    var receiptContent = document.getElementById('receipt').innerHTML;
    var originalContent = document.body.innerHTML;
    
    document.body.innerHTML = receiptContent;
    window.print();
    
    document.body.innerHTML = originalContent;
    window.location.reload();
}

// Auto-calculate total when quantity changes
document.querySelector('input[name="quantity"]').addEventListener('input', function() {
    var quantity = parseFloat(this.value) || 0;
    var price = parseFloat(document.querySelector('input[name="price"]').value) || 0;
    var total = (quantity * price).toFixed(2);
    document.querySelector('input[name="total"]').value = total;
    
    // Update IQD display
    var iqdTotal = (total * <?php echo $conversion_rate; ?>).toFixed(2);
    document.querySelector('input[name="total"]').nextElementSibling.textContent = iqdTotal + ' د.ع';
});
</script>

<?php include_once('layouts/footer.php'); ?>