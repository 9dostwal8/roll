<?php
ob_start();
require_once('includes/load.php');
if ($session->isUserLoggedIn(true)) {
  redirect('home.php', false);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Panel</title>
  <!-- Google Font -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

<style>
  body {
    margin: 0;
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #DCD7C9 0%, #DCD7C9 100%);
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
  }

  .login-page {
    background: white;
    padding: 40px 30px;
    border-radius: 20px;
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.2);
    width: 100%;
    max-width: 400px;
    animation: fadeIn 1.2s ease-out;
  }

  .text-center {
    text-align: center;
    margin-bottom: 30px;
  }

  .text-center .logo {
    width: 80px;
    height: auto;
    margin-bottom: 10px;
  }

  h1 {
    font-size: 28px;
    font-weight: 600;
    color: #333;
  }

  label {
    display: block;
    margin-bottom: 6px;
    color: #555;
    font-weight: 500;
  }

  .form-group {
    margin-bottom: 20px;
  }

  .form-control {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #ddd;
    border-radius: 12px;
    background-color: #f9f9f9;
    transition: border 0.3s ease;
    font-size: 14px;
  }

  .form-control:focus {
    border-color: #255F38;
    outline: none;
    background-color: #fff;
  }

  .btn {
    width: 100%;
    padding: 12px;
    border: none;
    background-color: #255F38;
    color: white;
    font-size: 16px;
    font-weight: 500;
    border-radius: 12px;
    cursor: pointer;
    transition: background 0.3s ease, transform 0.2s ease;
  }

  .btn:hover {
    background-color: #732d91;
    transform: translateY(-2px);
  }

  /* Optional message styling */
  .msg {
    background-color: #fce4ec;
    color: #c2185b;
    padding: 10px;
    border-radius: 10px;
    margin-bottom: 20px;
    text-align: center;
    font-size: 14px;
  }

  @keyframes fadeIn {
    from {
      opacity: 0;
      transform: scale(0.95);
    }
    to {
      opacity: 1;
      transform: scale(1);
    }
  }
</style>

</head>

<body>
  <div class="login-page">
    <div class="text-center">
      <img src="libs/images/LogoRolex.png" alt="Logo" class="logo"> <!-- Replace 'logo.png' with your logo path -->
      <h1>Login Panel</h1>
    </div>
    <?php echo display_msg($msg); ?>
    <form method="post" action="auth.php" class="clearfix">
      <div class="form-group">
        <label for="username" class="control-label">Username</label>
        <input type="text" class="form-control" name="username" placeholder="Username">
      </div>
      <div class="form-group">
        <label for="Password" class="control-label">Password</label>
        <input type="password" name="password" class="form-control" placeholder="Password">
      </div>
      <div class="form-group">
        <button type="submit" class="btn btn-danger">Login</button>
      </div>
    </form>
  </div>
</body>

</html>
<?php include_once('layouts/footer.php'); ?>