<?php
require_once(LIB_PATH_INC . DS . "config.php");

class MySqli_DB
{

  private $con;
  public $query_id;

  function __construct()
  {
    $this->db_connect();
  }

  /*--------------------------------------------------------------*/
  /* Function for Open database connection
  /*--------------------------------------------------------------*/
  public function db_connect()
  {
    $this->con = mysqli_connect(DB_HOST, DB_USER, DB_PASS);
    if (!$this->con) {
      die(" Database connection failed:" . mysqli_connect_error());
    } else {
      $select_db = $this->con->select_db(DB_NAME);
      if (!$select_db) {
        die("Failed to Select Database" . mysqli_connect_error());
      }
    }
  }
  /*--------------------------------------------------------------*/
  /* Function for Close database connection
  /*--------------------------------------------------------------*/

  public function db_disconnect()
  {
    if (isset($this->con)) {
      mysqli_close($this->con);
      unset($this->con);
    }
  }
  /*--------------------------------------------------------------*/
  /* Function for mysqli query
  /*--------------------------------------------------------------*/
  public function query($sql)
  {

    if (trim($sql != "")) {
      $this->query_id = $this->con->query($sql);
    }
    if (!$this->query_id)
      // only for Develope mode
      die("Error on this Query :<pre> " . $sql . "</pre>");
    // For production mode
    //  die("Error on Query");

    return $this->query_id;

  }

  /*--------------------------------------------------------------*/
  /* Function for Query Helper
  /*--------------------------------------------------------------*/
  public function fetch_array($statement)
  {
    return mysqli_fetch_array($statement);
  }
  public function fetch_object($statement)
  {
    return mysqli_fetch_object($statement);
  }
  public function fetch_assoc($statement)
  {
    return mysqli_fetch_assoc($statement);
  }
  public function num_rows($statement)
  {
    return mysqli_num_rows($statement);
  }
  public function insert_id()
  {
    return mysqli_insert_id($this->con);
  }
  public function affected_rows()
  {
    return mysqli_affected_rows($this->con);
  }
  /*--------------------------------------------------------------*/
  /* Function for Remove escapes special
  /* characters in a string for use in an SQL statement
  /*--------------------------------------------------------------*/
  public function escape($str)
  {
    return $this->con->real_escape_string($str);
  }
  /*--------------------------------------------------------------*/
  /* Function for while loop
  /*--------------------------------------------------------------*/
  public function while_loop($loop)
  {
    global $db;
    $results = array();
    while ($result = $this->fetch_array($loop)) {
      $results[] = $result;
    }
    return $results;
  }

}

$db = new MySqli_DB();
function find_sales_by_date($start_date, $end_date)
{
  global $db;
  $sql = "SELECT s.*, p.name AS product_name, p.buy_price, p.categorie_id AS category_id, 
         m.file_name AS product_image 
         FROM sales s 
         LEFT JOIN products p ON s.product_id = p.id 
         LEFT JOIN media m ON p.media_id = m.id 
         WHERE s.date BETWEEN '{$start_date}' AND '{$end_date}'
         ORDER BY s.date DESC";
  return find_by_sql($sql);
}


function monthlySalesByMonth($year, $month)
{
  global $db;

  // Format month to always be 2 digits (01-12)
  $month = str_pad($month, 2, '0', STR_PAD_LEFT);

  $start_date = "{$year}-{$month}-01";
  $end_date = date("Y-m-t", strtotime($start_date));

  $sql = "SELECT s.*, p.name 
          FROM sales s 
          LEFT JOIN products p ON s.product_id = p.id 
          WHERE s.date BETWEEN '{$start_date}' AND '{$end_date}'
          ORDER BY s.date DESC";

  return find_by_sql($sql);
}


?>