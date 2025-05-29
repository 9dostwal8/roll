<?php
// Start the session
session_start();

// Page title
$page_title = 'Settings';

// Include necessary files
require_once('includes/load.php');

// Handle language change
if (isset($_POST['change_language'])) {
    $selected_language = $_POST['language'];
    $_SESSION['language'] = $selected_language;
    $session->msg("s", "Language changed successfully.");
    redirect('setting.php', false);
}

// Fetch all categories (if needed)
$all_categories = find_all('categories');
if (!$all_categories) {
    $all_categories = [];
}

// Include the header
include_once('layouts/header.php');
?>

<!-- Add Bootstrap Modal for About Popup -->
<div class="modal fade" id="aboutModal" tabindex="-1" role="dialog" aria-labelledby="aboutModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="aboutModalLabel">About Our Company</h4>
      </div>
      <div class="modal-body text-center">
        <!-- Company Logo - Replace with your actual logo path -->
        <img src="libs/images/logo.png" alt="Company Logo" style="max-width: 200px; margin-bottom: 20px;">
        
        <h3>AllSafe Management System</h3>
        <p>Version: 1.0.0</p>
        
        <div class="well" style="margin-top: 20px;">
          <h4>Developed By:</h4>
          <p><strong>AllSafe Techno Solutions </strong></p>
          <p> Sulaymaniyah, Kurdstan - Iraq</p>
          <p>Email: dosty.tech98@gmail.com</p>
          <a href="https://www.allsafe-techno.com" target="_blank"><img src="libs/images/allsafe-logo.png" alt="AllSafe Techno Solutions" style="max-width: 150px; margin-bottom: 20px;"></a>
          <p>Phone: 0770 226 9722</p>
        </div>
        
        <div class="well">
          <h4>Lead Developer:</h4>
          <p><strong>Dosty Rebwar</strong></p>
          <p>Senior Software Engineer</p>
          <p>Email: dosty.tech98@gmail.com</p>
        </div>
        
        <p class="text-muted">© <?php echo date('Y'); ?> All Rights Reserved</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<div class="row">
    <div class="col-md-12">
        <?php echo display_msg($msg); ?>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong>
                    <span class="glyphicon glyphicon-cog"></span>
                    <span><?php echo $lang['Settings']; ?></span>
                </strong>
                <div class="pull-right">
                    <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#aboutModal">
                        <span class="glyphicon glyphicon-info-sign"></span> About
                    </button>
                </div>
            </div>
            <div class="panel-body">
                <!-- Language Dropdown Form -->
                <form method="post" action="setting.php">
                    <div class="form-group">
                        <label for="language"><?php echo $lang['Select Language']; ?></label>
                        <select class="form-control" name="language" id="language">
                            <option value="en" <?php echo (isset($_SESSION['language']) && $_SESSION['language'] == 'en') ? 'selected' : ''; ?>>English</option>
                            <option value="ku" <?php echo (isset($_SESSION['language']) && $_SESSION['language'] == 'ku') ? 'selected' : ''; ?>>Kurdish (کوردی)</option>
                        </select>
                    </div>
                    <button type="submit" name="change_language" class="btn btn-primary"><?php echo $lang['Change Language']; ?></button>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong>
                    <span class="glyphicon glyphicon-save"></span>
                    <span>Database Backup</span>
                </strong>
            </div>
            <div class="panel-body">
                <p>Click the button below to download a backup of your database.</p>
                <a href="download_backup.php" class="btn btn-success">
                    <span class="glyphicon glyphicon-download"></span> Download Backup
                </a>
            </div>
        </div>
    </div>
</div>


<?php
// Include the footer
include_once('layouts/footer.php');
?>