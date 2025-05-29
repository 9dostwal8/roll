<?php
$page_title = 'Add Product';
require_once('includes/load.php');
$all_categories = find_all('categories');
$all_photo = find_all('media');

// Get conversion rate from database
$conversion_rate = get_conversion_rate();

// Handle form submission
if (isset($_POST['add_product'])) {
  $req_fields = array(
    'product-title',
    'product-categorie',
    'reference-number',
    'serial-number',
    'supply-name',
    'warranty-date',
    'buying-price',
    'saleing-price',
    'price-currency'
  );
  validate_fields($req_fields);

  if (empty($errors)) {
    $p_name     = remove_junk($db->escape($_POST['product-title']));
    $p_cat      = remove_junk($db->escape($_POST['product-categorie']));
    $ref_number = remove_junk($db->escape($_POST['reference-number']));
    $serial     = remove_junk($db->escape($_POST['serial-number']));
    $supply     = remove_junk($db->escape($_POST['supply-name']));
    $warranty   = remove_junk($db->escape($_POST['warranty-date']));
    $currency   = remove_junk($db->escape($_POST['price-currency']));

    // Check for unique serial number
    $serial_check = $db->query("SELECT id FROM products WHERE serial_number = '{$serial}' LIMIT 1");
    if ($db->num_rows($serial_check) > 0) {
      $session->msg('d', "Error: Serial number already exists.");
      redirect('add_product.php', false);
    }

    // Convert prices to USD if needed
    if ($currency == 'IQD') {
      $p_buy  = remove_junk($db->escape($_POST['buying-price'])) / $conversion_rate;
      $p_sale = remove_junk($db->escape($_POST['saleing-price'])) / $conversion_rate;
    } else {
      $p_buy  = remove_junk($db->escape($_POST['buying-price']));
      $p_sale = remove_junk($db->escape($_POST['saleing-price']));
    }

    $media_id = isset($_POST['product-photo-1']) ? remove_junk($db->escape($_POST['product-photo-1'])) : 0;
    $image_2  = isset($_POST['product-photo-2']) ? remove_junk($db->escape($_POST['product-photo-2'])) : 0;
    $image_3  = isset($_POST['product-photo-3']) ? remove_junk($db->escape($_POST['product-photo-3'])) : 0;

    $date = make_date();

    $query  = "INSERT INTO products (";
    $query .= "name, reference_number, serial_number, supply_name, warranty_date, buy_price, sale_price, categorie_id, media_id, image_2, image_3, date";
    $query .= ") VALUES (";
    $query .= "'{$p_name}', '{$ref_number}', '{$serial}', '{$supply}', '{$warranty}', '{$p_buy}', '{$p_sale}', '{$p_cat}', '{$media_id}', '{$image_2}', '{$image_3}', '{$date}'";
    $query .= ")";

    if ($db->query($query)) {
      $session->msg('s', "Product added successfully");
      redirect('add_product.php', false);
    } else {
      $session->msg('d', 'Sorry, failed to add product: ' . $db->error);
      redirect('product.php', false);
    }
  } else {
    $session->msg("d", $errors);
    redirect('add_product.php', false);
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
  <div class="col-md-8">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span><?php echo $lang['Add New Product']; ?></span>
        </strong>
      </div>
      <div class="panel-body">
        <div class="col-md-12">
          <form method="post" action="add_product.php" class="clearfix" enctype="multipart/form-data">
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon">
                  <i class="glyphicon glyphicon-th-large"></i>
                </span>
                <input type="text" class="form-control" name="product-title"
                  placeholder="<?php echo $lang['Brand']; ?>" required>
              </div>
            </div>

            <div class="form-group">
              <div class="row">
                <div class="col-md-6">
                  <input type="text" name="reference-number" class="form-control" placeholder="Reference Number" required>
                </div>
                <div class="col-md-6">
                  <input type="text" name="serial-number" class="form-control" placeholder="Serial Number (Unique)" required>
                </div>
              </div>
            </div>

            <div class="form-group">
              <div class="row">
                <div class="col-md-6">
                  <input type="text" name="supply-name" class="form-control" placeholder="Supply Name" required>
                </div>
                <div class="col-md-6">
                  <input type="date" name="warranty-date" class="form-control" placeholder="Warranty Date" required>
                </div>
              </div>
            </div>


            <div class="form-group">
              <div class="row">
                <div class="col-md-6">
                  <select class="form-control" name="product-categorie" required>
                    <option value=""><?php echo $lang['Select Product Category']; ?></option>
                    <?php foreach ($all_categories as $cat): ?>
                      <option value="<?php echo (int) $cat['id'] ?>">
                        <?php echo $cat['name'] ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>

              </div>
            </div>

            <!-- Rest of your form fields (images, prices, etc.) remain the same -->
            <div class="form-group">
              <label><?php echo $lang['Product Images']; ?> (Max 3)</label>
              <div class="row">
                <div class="col-md-4">
                  <select class="form-control" name="product-photo-1" required>
                    <option value=""><?php echo $lang['Select Main Product Photo']; ?></option>
                    <?php foreach ($all_photo as $photo): ?>
                      <option value="<?php echo (int) $photo['id'] ?>">
                        <?php echo $photo['file_name'] ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="col-md-4">
                  <select class="form-control" name="product-photo-2">
                    <option value=""><?php echo $lang['Optional Second Photo']; ?></option>
                    <?php foreach ($all_photo as $photo): ?>
                      <option value="<?php echo (int) $photo['id'] ?>">
                        <?php echo $photo['file_name'] ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="col-md-4">
                  <select class="form-control" name="product-photo-3">
                    <option value=""><?php echo $lang['Optional Third Photo']; ?></option>
                    <?php foreach ($all_photo as $photo): ?>
                      <option value="<?php echo (int) $photo['id'] ?>">
                        <?php echo $photo['file_name'] ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
              <small class="text-muted"><?php echo $lang['First image will be used as main product image']; ?></small>
            </div>

            <div class="form-group">
              <div class="row">
                <div class="col-md-4">
                  <div class="input-group">
                    <span class="input-group-addon">
                      <i class="glyphicon glyphicon-shopping-cart"></i>
                    </span>
                    <input type="number" class="form-control" name="product-quantity"
                      placeholder="<?php echo $lang['Product Quantity']; ?>" required>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="input-group">
                    <span class="input-group-addon">
                      <i class="glyphicon glyphicon-usd"></i>
                    </span>
                    <input type="number" step="0.01" class="form-control" name="buying-price"
                      placeholder="<?php echo $lang['Buying Price']; ?>" required>
                    <span class="input-group-addon">
                      <select name="price-currency" style="border: none; background: transparent; padding: 0;">
                        <option value="USD">USD</option>
                        <option value="IQD">IQD</option>
                      </select>
                    </span>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="input-group">
                    <span class="input-group-addon">
                      <i class="glyphicon glyphicon-usd"></i>
                    </span>
                    <input type="number" step="0.01" class="form-control" name="saleing-price"
                      placeholder="<?php echo $lang['Selling Price']; ?>" required>
                    <span class="input-group-addon">
                      <select name="price-currency" style="border: none; background: transparent; padding: 0;">
                        <option value="USD">USD</option>
                        <option value="IQD">IQD</option>
                      </select>
                    </span>
                  </div>
                </div>
              </div>
              <div class="row" style="margin-top: 10px;">
                <div class="col-md-12 text-muted small">
                  <?php echo sprintf("Current conversion rate: 1 USD = %s IQD", number_format($conversion_rate, 2)); ?>
                </div>
              </div>
            </div>



            <button type="submit" name="add_product" class="btn btn-danger"><?php echo $lang['Add product']; ?></button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>



<?php include_once('layouts/footer.php'); ?>