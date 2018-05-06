<?php
// // $dbhost = $_SERVER['aalq50qu52s8m1'];
// // $dbport = $_SERVER['3306'];
// // $dbname = $_SERVER['ebdb'];
// // $charset = 'utf8' ;

// // $dsn = "mysql:host={$dbhost};port={$dbport};dbname={$dbname};charset={$charset}";
// // $dbuser = $_SERVER['iamwho1123'];
// // $dbpass = $_SERVER['pig8525168'];

// $pdo = new PDO($dsn, $dbuser, $dbpass);
$dbhost = 'localhost';
$dbuser = 'id5635354_root';
$dbpass = 'pig8525168';
$dbname = 'id5635354_web';

$conn = mysqli_connect($dbhost, $dbuser, $dbpass) or die('Error with MySQL connection');
mysqli_query($conn,"SET NAMES 'utf8'");
mysqli_select_db($conn,$dbname);

if($_SERVER['REQUEST_METHOD'] == 'GET'){
  try
  {
    $cap = filter_input(INPUT_GET, 'cap', FILTER_SANITIZE_URL);
  }
  catch(Exception $e)
  {
    header('HTTP/1.1 400 Bad request'); 
    echo $e->getMessage();
  }
  //echo $cap;
  $sql="SELECT caption FROM `video` WHERE `videoID`='$cap'";
  $result=mysqli_query($conn,$sql);
  $row = mysqli_fetch_array($result);
  //echo mysql_num_rows($result);
  # Response caption's JSON string
  print $row['caption'];
  
}
?>