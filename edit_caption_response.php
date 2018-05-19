<?php
	$dbhost = 'localhost';
	$dbuser = 'id5825308_root';
	$dbpass = 'pig8525168';
	$dbname = 'id5825308_web';
	//$conn = mysql_connect($dbhost, $dbuser, $dbpass) or die('Error with MySQL connection');
	$conn = mysqli_connect($dbhost, $dbuser, $dbpass) or die('Error with MySQL connection');
	mysqli_query($conn,"SET NAMES 'utf8'");
	mysqli_select_db($conn,$dbname);


	$id = '';
	if($_SERVER['REQUEST_METHOD'] == 'POST'){
		try{
	    	$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_URL);
		}catch(Exception $e){
	    	header('HTTP/1.1 400 Bad request'); 
	   		echo $e->getMessage();
		}
	}
  	$editedJcaption = $_POST['newcaption'];

  	$json_caption = mysqli_real_escape_string($conn,$editedJcaption);
	$sql="SELECT * FROM `video` WHERE `videoID`='$id'";
	$result=mysqli_query($conn,$sql);
	if(mysqli_num_rows($result)!=0){  //if the video is in table , then update caption
		$sql = "UPDATE `video` SET caption='$json_caption' WHERE `videoID`='$id'";
		mysqli_query($conn,$sql) or die($id);
	}
	//header('Location: player.html?id='.$id.'&file=caption_'.$id);
  	//echo "data : $editedJcaption";

?>