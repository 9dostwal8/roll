<?php $user = current_user();

// Default to English if no language is set
if (!isset($_SESSION['language'])) {
  $_SESSION['language'] = 'en';
}

// Include the appropriate language file
$language_file = 'lang_' . $_SESSION['language'] . '.php';
if (file_exists($language_file)) {
  include($language_file);
} else {
  include('../lang_en.php'); // Fallback to English
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title><?php if (!empty($page_title))
    echo remove_junk($page_title);
  elseif (!empty($user))
    echo ucfirst($user['name']);
  else
    echo "Inventory Management System"; ?>
  </title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css" />
  <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker3.min.css" />
  <link rel="stylesheet" href="libs/css/main.css" />
</head>

<body>
  <?php if ($session->isUserLoggedIn(true)): ?>
    <header id="header">
      <div class="logo pull-left"> AllSafe </div>
      <div class="header-content">
        <div class="header-date pull-left">
          <strong>
            <?php
            $tomorrow = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
            $date_str = date("l, F j, Y", $tomorrow);
            echo $date_str; ?>
          </strong>
        </div>
        <div class="pull-right clearfix">
          <ul class="info-menu list-inline list-unstyled">
            <li class="profile">
              <a href="#" data-toggle="dropdown" class="toggle" aria-expanded="false">
                <img src="uploads/users/<?php echo $user['image']; ?>" alt="user-image" class="img-circle img-inline">
                <span><?php echo remove_junk(ucfirst($user['name'])); ?> <i class="caret"></i></span>
              </a>
              <ul class="dropdown-menu">
                <li>
                  <a href="profile.php?id=<?php echo (int) $user['id']; ?>">
                    <i class="glyphicon glyphicon-user"></i>
                    <?php echo $lang['Profile']; ?>
                  </a>
                </li>
                <li>
                  <a href="edit_account.php" title="edit account">
                    <i class="glyphicon glyphicon-cog"></i>
                    <?php echo $lang['Settings']; ?>
                  </a>
                </li>
                <li class="last">
                  <a href="logout.php">
                    <i class="glyphicon glyphicon-off"></i>
                    <?php echo $lang['Logout']; ?>
                  </a>
                </li>
              </ul>
            </li>
          </ul>
        </div>
      </div>
    </header>
    <div class="sidebar">
      <?php if ($user['user_level'] === '1' || $user['user_level'] === '3'): ?>
        <!-- admin menu -->
        <?php include_once('admin_menu.php'); ?>
      <?php endif; ?>

    </div>
  <?php endif; ?>

  <div class="page">
    <div class="container-fluid">