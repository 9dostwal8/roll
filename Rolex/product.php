<?php
$page_title = 'All Product';
require_once('includes/load.php');
page_require_level(2);

// Set default currency (USD)
if (!isset($_SESSION['currency'])) {
    $_SESSION['currency'] = 'USD';
}

// Get conversion rate from database
$conversion_rate = get_conversion_rate();

// Handle currency change
if (isset($_POST['change_currency'])) {
    $_SESSION['currency'] = $_POST['currency'];
    redirect('product.php', false);
}

// Handle conversion rate change
if (isset($_POST['update_rate'])) {
    $new_rate = (float)$_POST['conversion_rate'];
    if (update_conversion_rate($new_rate)) {
        $session->msg('s', 'Conversion rate updated successfully');
        redirect('product.php', false);
    } else {
        $session->msg('d', 'Failed to update conversion rate');
        redirect('product.php', false);
    }
}

$products = join_product_table();

?>
<?php include_once('layouts/header.php'); ?>

<style>
    .product-images {
        display: flex;
        gap: 5px;
        flex-wrap: wrap;
    }

    .product-images img {
        width: 40px;
        height: 40px;
        object-fit: cover;
        border: 1px solid #ddd;
        border-radius: 50%;
    }

    .product-images img:hover {
        transform: scale(1.5);
        z-index: 10;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
    }
</style>
<div class="row">
    <div class="col-md-12">
        <?php echo display_msg($msg); ?>
    </div>
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading clearfix">
                <div class="pull-right">
                    <form method="post" action="" class="form-inline" style="display: inline-block; margin-right: 10px;">
                        <div class="form-group">
                            <label class="control-label">Currency:</label>
                            <select name="currency" class="form-control input-sm" onchange="this.form.submit()">
                                <option value="USD" <?php echo ($_SESSION['currency'] == 'USD') ? 'selected' : ''; ?>>USD ($)</option>
                                <option value="IQD" <?php echo ($_SESSION['currency'] == 'IQD') ? 'selected' : ''; ?>>IQD (د.ع)</option>
                            </select>
                            <input type="hidden" name="change_currency" value="1">
                        </div>
                    </form>

                    <form method="post" action="" class="form-inline" style="display: inline-block; margin-right: 10px;">
                        <div class="form-group">
                            <label class="control-label">1 USD = </label>
                            <input type="number" name="conversion_rate" class="form-control input-sm"
                                value="<?php echo $conversion_rate; ?>"
                                step="0.01" min="0" style="width: 100px;">
                            <label class="control-label">IQD</label>
                            <button type="submit" name="update_rate" class="btn btn-default btn-sm">Update</button>
                        </div>
                    </form>

                    <a href="add_product.php" class="btn btn-primary"><?php echo $lang['Add New']; ?></a>
                </div>
            </div>
            <div class="panel-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">#</th>
                            <th><?php echo $lang['Photo']; ?></th>
                            <th><?php echo $lang['Product Title']; ?></th>
                            <th class="text-center" style="width: 10%;"><?php echo $lang['Reference Number']; ?></th>
                            <th class="text-center" style="width: 10%;"><?php echo $lang['Serial Number']; ?></th>
                            <th class="text-center" style="width: 10%;"><?php echo $lang['Supplier']; ?></th>
                            <th class="text-center" style="width: 10%;"><?php echo $lang['Buying Price']; ?></th>
                            <th class="text-center" style="width: 10%;"><?php echo $lang['Selling Price']; ?></th>
                            <th class="text-center" style="width: 10%;"><?php echo $lang['Warranty Date']; ?></th>
                            <th class="text-center" style="width: 100px;"><?php echo $lang['Actions']; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td class="text-center"><?php echo count_id(); ?></td>
                                <td>
                                    <div class="product-images">
                                        <!-- Main Image -->
                                        <?php if ($product['media_id'] === '0'): ?>
                                            <img class="img-avatar" src="uploads/products/no_image.png" alt="">
                                        <?php else: ?>
                                            <img class="img-avatar" src="uploads/products/<?php echo $product['image']; ?>" alt="">
                                        <?php endif; ?>

                                        <!-- Second Image -->
                                        <?php if (!empty($product['image_2'])): ?>
                                            <img class="img-avatar" src="uploads/products/<?php echo $product['image_2']; ?>" alt="">
                                        <?php endif; ?>

                                        <!-- Third Image -->
                                        <?php if (!empty($product['image_3'])): ?>
                                            <img class="img-avatar" src="uploads/products/<?php echo $product['image_3']; ?>" alt="">
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td><?php echo remove_junk($product['name']); ?></td>
                                <td class="text-center"><?php echo remove_junk($product['reference_number']); ?></td>
                                <td class="text-center"><?php echo remove_junk($product['serial_number']); ?></td>
                                <td class="text-center"><?php echo remove_junk($product['supply_name'] ?? 'N/A'); ?></td>
                                <td class="text-center">
                                    <?php
                                    if ($_SESSION['currency'] == 'IQD') {
                                        echo number_format($product['buy_price'] * $conversion_rate) . ' د.ع';
                                    } else {
                                        echo '$' . remove_junk($product['buy_price']);
                                    }
                                    ?>
                                </td>
                                <td class="text-center">
                                    <?php
                                    if ($_SESSION['currency'] == 'IQD') {
                                        echo number_format($product['sale_price'] * $conversion_rate) . ' د.ع';
                                    } else {
                                        echo '$' . remove_junk($product['sale_price']);
                                    }
                                    ?>
                                </td>
                                <td class="text-center"><?php echo !empty($product['warranty_date']) ? read_date($product['warranty_date']) : 'N/A'; ?></td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="edit_product.php?id=<?php echo (int)$product['id']; ?>" class="btn btn-info btn-xs" title="Edit" data-toggle="tooltip">
                                            <span class="glyphicon glyphicon-edit"></span>
                                        </a>
                                        <a href="delete_product.php?id=<?php echo (int)$product['id']; ?>"
                                            class="btn btn-danger btn-xs"
                                            title="Delete"
                                            data-toggle="tooltip"
                                            onclick="return confirm('Are you sure you want to delete this product?');">
                                            <span class="glyphicon glyphicon-trash"></span>
                                        </a>

                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?> 
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include_once('layouts/footer.php'); ?>