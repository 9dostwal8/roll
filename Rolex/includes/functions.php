<?php
$errors = array();

/*--------------------------------------------------------------*/
/* Function for Remove escapes special
/* characters in a string for use in an SQL statement
/*--------------------------------------------------------------*/
function real_escape($str)
{
  global $con;
  $escape = mysqli_real_escape_string($con, $str);
  return $escape;
}
/*--------------------------------------------------------------*/
/* Function for Remove html characters
/*--------------------------------------------------------------*/
function remove_junk($str)
{
  $str = nl2br($str);
  $str = htmlspecialchars(strip_tags($str, ENT_QUOTES));
  return $str;
}
/*--------------------------------------------------------------*/
/* Function for Uppercase first character
/*--------------------------------------------------------------*/
function first_character($str)
{
  $val = str_replace('-', " ", $str);
  $val = ucfirst($val);
  return $val;
}
/*--------------------------------------------------------------*/
/* Function for Checking input fields not empty
/*--------------------------------------------------------------*/
function validate_fields($var)
{
  global $errors;
  foreach ($var as $field) {
    $val = remove_junk($_POST[$field]);
    if (isset($val) && $val == '') {
      $errors = $field . " can't be blank.";
      return $errors;
    }
  }
}
/*--------------------------------------------------------------*/
/* Function for Display Session Message
   Ex echo displayt_msg($message);
/*--------------------------------------------------------------*/
function display_msg($msg = '')
{
  $output = array();
  if (!empty($msg)) {
    foreach ($msg as $key => $value) {
      $output = "<div class=\"alert alert-{$key}\">";
      $output .= "<a href=\"#\" class=\"close\" data-dismiss=\"alert\">&times;</a>";
      $output .= remove_junk(first_character($value));
      $output .= "</div>";
    }
    return $output;
  } else {
    return "";
  }
}
/*--------------------------------------------------------------*/
/* Function for redirect
/*--------------------------------------------------------------*/
function redirect($url, $permanent = false)
{
  if (headers_sent() === false) {
    header('Location: ' . $url, true, ($permanent === true) ? 301 : 302);
  }

  exit();
}
/*--------------------------------------------------------------*/
/* Function for find out total saleing price, buying price and profit
/*--------------------------------------------------------------*/
function total_price($totals)
{
  $sum = 0;
  $sub = 0;
  foreach ($totals as $total) {
    $sum += $total['total_saleing_price'];
    $sub += $total['total_buying_price'];
    $profit = $sum - $sub;
  }
  return array($sum, $profit);
}
/*--------------------------------------------------------------*/
/* Function for Readable date time
/*--------------------------------------------------------------*/
function read_date($str)
{
  if ($str)
    return date('M j, Y', strtotime($str));
  else
    return null;
}
/*--------------------------------------------------------------*/
/* Function for  Readable Make date time
/*--------------------------------------------------------------*/
function make_date()
{
  return strftime("%Y-%m-%d %H:%M:%S", time());
}
/*--------------------------------------------------------------*/
/* Function for  Readable date time
/*--------------------------------------------------------------*/
function count_id()
{
  static $count = 1;
  return $count++;
}
/*--------------------------------------------------------------*/
/* Function for Creting random string
/*--------------------------------------------------------------*/
function randString($length = 5)
{
  $str = '';
  $cha = "0123456789abcdefghijklmnopqrstuvwxyz";

  for ($x = 0; $x < $length; $x++)
    $str .= $cha[mt_rand(0, strlen($cha))];
  return $str;
}

function get_conversion_rate()
{
  global $db;
  $sql = "SELECT value FROM settings WHERE name='usd_to_iqd_rate' LIMIT 1";
  $result = $db->query($sql);
  if ($db->num_rows($result) > 0) {
    $row = $db->fetch_assoc($result);
    return (float) $row['value'];
  } else {
    // Default rate if not set in database
    return 1450.00;
  }
}

function update_conversion_rate($rate)
{
  global $db;
  $rate = (float) $rate;
  $sql = "INSERT INTO settings (name, value) VALUES ('usd_to_iqd_rate', '{$rate}')
          ON DUPLICATE KEY UPDATE value='{$rate}'";
  return $db->query($sql);
}

function count_returns()
{
  global $db;
  $sql = "SELECT COUNT(id) as total FROM returns";
  $result = $db->query($sql);
  $return = $db->fetch_assoc($result);
  return $return;
}


?>