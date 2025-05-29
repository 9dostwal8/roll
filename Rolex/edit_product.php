<?php
$page_title = 'Edit product';
require_once('includes/load.php');

// Get conversion rate from database
$conversion_rate = get_conversion_rate();

// Set default currency (USD)
if(!isset($_SESSION['currency'])) {
    $_SESSION['currency'] = 'USD';
}

$product = find_by_id('products', (int)$_GET['id']);
$all_categories = find_all('categories');
$all_photo = find_all('media');

if (!$product) {
    $session->msg("d", "Missing product id.");
    redirect('product.php');
}

if (isset($_POST['product'])) {
    $req_fields = array('product-title', 'product-categorie', 'product-quantity', 'buying-price', 'saleing-price', 'price-currency', 'reference-number', 'serial-number');
    validate_fields($req_fields);

    if (empty($errors)) {
        $p_name   = remove_junk($db->escape($_POST['product-title']));
        $p_cat    = (int)$_POST['product-categorie'];
        $p_qty    = remove_junk($db->escape($_POST['product-quantity']));
        $currency = remove_junk($db->escape($_POST['price-currency']));
        $p_ref    = remove_junk($db->escape($_POST['reference-number']));
        $p_serial = remove_junk($db->escape($_POST['serial-number']));
        $p_supply = remove_junk($db->escape($_POST['supply-name']));
        $p_warranty = remove_junk($db->escape($_POST['warranty-date']));

        // Convert prices to USD if entered in IQD
        if ($currency == 'IQD') {
            $p_buy  = remove_junk($db->escape($_POST['buying-price'])) / $conversion_rate;
            $p_sale = remove_junk($db->escape($_POST['saleing-price'])) / $conversion_rate;
        } else {
            $p_buy  = remove_junk($db->escape($_POST['buying-price']));
            $p_sale = remove_junk($db->escape($_POST['saleing-price']));
        }

        // Handle images
        $media_id = isset($_POST['product-photo-1']) ? remove_junk($db->escape($_POST['product-photo-1'])) : 0;
        $image_2  = isset($_POST['product-photo-2']) ? remove_junk($db->escape($_POST['product-photo-2'])) : 0;
        $image_3  = isset($_POST['product-photo-3']) ? remove_junk($db->escape($_POST['product-photo-3'])) : 0;

        // Build the query safely using an array
        $fields = [];
        $fields[] = "name ='{$p_name}'";
        $fields[] = "quantity ='{$p_qty}'";
        $fields[] = "buy_price ='{$p_buy}'";
        $fields[] = "sale_price ='{$p_sale}'";
        $fields[] = "categorie_id ='{$p_cat}'";
        $fields[] = "media_id='{$media_id}'";
        $fields[] = "image_2='{$image_2}'";
        $fields[] = "image_3='{$image_3}'";
        $fields[] = "reference_number='{$p_ref}'";
        $fields[] = "serial_number='{$p_serial}'";
        $fields[] = "supply_name='{$p_supply}'";
        $fields[] = "warranty_date=" . ($p_warranty ? "'{$p_warranty}'" : "NULL");

        $query = "UPDATE products SET " . implode(", ", $fields);
        $query .= " WHERE id ='{$product['id']}'";

        $result = $db->query($query);
        if ($result && $db->affected_rows() === 1) {
            $session->msg('s', "Product updated successfully");
            redirect('product.php', false);
        } else {
            $session->msg('d', ' Sorry failed to update! ' . $db->error);
            redirect('edit_product.php?id=' . $product['id'], false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('edit_product.php?id=' . $product['id'], false);
    }
}
?>

<?php include_once('layouts/header.php'); ?>

<div class="row">
    <div class="col-md-12">
        <?php echo display_msg($msg); ?>
    </div>
</div>
<div class="row">
    <div class="panel panel-default">
        <div class="panel-heading">
            <strong>
                <span class="glyphicon glyphicon-th"></span>
                <span>Edit Product</span>
            </strong>
        </div>
        <div class="panel-body">
            <div class="col-md-7">
                <form method="post" action="edit_product.php?id=<?php echo (int)$product['id'] ?>">
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="glyphicon glyphicon-th-large"></i>
                            </span>
                            <input type="text" class="form-control" name="product-title" value="<?php echo remove_junk($product['name']);?>" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <select class="form-control" name="product-categorie" required>
                                    <option value="">Select a category</option>
                                    <?php foreach ($all_categories as $cat): ?>
                                        <option value="<?php echo (int)$cat['id']; ?>" <?php if($product['categorie_id'] === $cat['id']): echo "selected"; endif; ?>>
                                            <?php echo remove_junk($cat['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <input type="text" class="form-control" name="supply-name" placeholder="Supplier Name" value="<?php echo remove_junk($product['supply_name'] ?? ''); ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <input type="text" class="form-control" name="reference-number" placeholder="Reference Number" value="<?php echo remove_junk($product['reference_number'] ?? ''); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <input type="text" class="form-control" name="serial-number" placeholder="Serial Number" value="<?php echo remove_junk($product['serial_number'] ?? ''); ?>" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Product Images</label>
                        <div class="row">
                            <!-- Main Image -->
                            <div class="col-md-4">
                                <select class="form-control" name="product-photo-1" required>
                                    <option value="">Select Main Image</option>
                                    <?php foreach ($all_photo as $photo): ?>
                                        <option value="<?php echo (int)$photo['id'];?>" <?php if($product['media_id'] === $photo['id']): echo "selected"; endif; ?>>
                                            <?php echo $photo['file_name'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <!-- Second Image -->
                            <div class="col-md-4">
                                <select class="form-control" name="product-photo-2">
                                    <option value="">No Second Image</option>
                                    <?php foreach ($all_photo as $photo): ?>
                                        <option value="<?php echo (int)$photo['id'];?>" <?php if(isset($product['image_2']) && $product['image_2'] === $photo['id']): echo "selected"; endif; ?>>
                                            <?php echo $photo['file_name'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <!-- Third Image -->
                            <div class="col-md-4">
                                <select class="form-control" name="product-photo-3">
                                    <option value="">No Third Image</option>
                                    <?php foreach ($all_photo as $photo): ?>
                                        <option value="<?php echo (int)$photo['id'];?>" <?php if(isset($product['image_3']) && $product['image_3'] === $photo['id']): echo "selected"; endif; ?>>
                                            <?php echo $photo['file_name'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <small class="text-muted">First image will be used as main product image</small>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="qty">Quantity</label>
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="glyphicon glyphicon-shopping-cart"></i>
                                        </span>
                                        <input type="number" class="form-control" name="product-quantity" value="<?php echo remove_junk($product['quantity']); ?>" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="qty">Buying price</label>
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="glyphicon glyphicon-usd"></i>
                                        </span>
                                        <input type="number" step="0.01" class="form-control" name="buying-price" 
                                            value="<?php echo ($_SESSION['currency'] == 'IQD') ? remove_junk($product['buy_price'] * $conversion_rate) : remove_junk($product['buy_price']); ?>" required>
                                        <span class="input-group-addon">
                                            <select name="price-currency" style="border: none; background: transparent; padding: 0;">
                                                <option value="USD" <?php echo ($_SESSION['currency'] == 'USD') ? 'selected' : ''; ?>>USD</option>
                                                <option value="IQD" <?php echo ($_SESSION['currency'] == 'IQD') ? 'selected' : ''; ?>>IQD</option>
                                            </select>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="qty">Selling price</label>
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="glyphicon glyphicon-usd"></i>
                                        </span>
                                        <input type="number" step="0.01" class="form-control" name="saleing-price" 
                                            value="<?php echo ($_SESSION['currency'] == 'IQD') ? remove_junk($product['sale_price'] * $conversion_rate) : remove_junk($product['sale_price']); ?>" required>
                                        <span class="input-group-addon">
                                            <select name="price-currency" style="border: none; background: transparent; padding: 0;">
                                                <option value="USD" <?php echo ($_SESSION['currency'] == 'USD') ? 'selected' : ''; ?>>USD</option>
                                                <option value="IQD" <?php echo ($_SESSION['currency'] == 'IQD') ? 'selected' : ''; ?>>IQD</option>
                                            </select>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="warranty-date">Warranty Date</label>
                                <input type="date" class="form-control" name="warranty-date" 
                                    value="<?php echo !empty($product['warranty_date']) ? $product['warranty_date'] : ''; ?>">
                            </div>
                            <div class="col-md-6 text-muted small" style="padding-top: 25px;">
                                <?php echo sprintf("Current conversion rate: 1 USD = %s IQD", number_format($conversion_rate, 2)); ?>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" name="product" class="btn btn-danger">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once('layouts/footer.php'); ?>