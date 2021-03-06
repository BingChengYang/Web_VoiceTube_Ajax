<?php

#setting connection with MySQL

$dbhost = 'localhost';
$dbuser = 'id5825308_root';
$dbpass = 'pig8525168';
$dbname = 'id5825308_web';
$conn = mysqli_connect($dbhost, $dbuser, $dbpass) or die('Error with MySQL connection');
mysqli_query($conn,"SET NAMES 'utf8'");
mysqli_select_db($conn,$dbname);

$contentPath = "content.JSON";

if($_SERVER['REQUEST_METHOD'] == 'GET'){
  try
  {
  $page = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_URL);
  if($page == null) $page = 1;
    # Load content from JSON file
    if(is_readable($contentPath))  
      $jContent = file_get_contents($contentPath);
    else                           
      throw new Exception("Cannot load content!");

  }
  catch(Exception $e)
  {
    header('HTTP/1.1 400 Bad request'); 
    echo $e->getMessage();
  }

  # Convert all JSON string to object 
  $contents = json_decode($jContent);

  #insert jContent into MySQL
  foreach ($contents as $content) {
    # code...
    // load video caption from file and insert it into database
    $captionPath = 'captions/caption_' . $content->data_video_id .'.JSON';
    # Load content from JSON file
    if(is_readable($captionPath))  
      $jCaption = file_get_contents($captionPath);
    else                           
      throw new Exception("Cannot load content!");

    $jCaption = mysqli_real_escape_string($conn,$jCaption);

    $sql="SELECT * FROM `video` WHERE `videoID`='$content->data_video_id'";
    $result=mysqli_query($conn,$sql);
    
    if(mysqli_num_rows($result)==0){  //if the video is not in table , then insert it
      $videoInfo = mysqli_real_escape_string($conn,json_encode($content));
      
      $sql = "INSERT INTO video(videoID,videoInfo,caption) VALUES('$content->data_video_id','$videoInfo','$jCaption')";
        mysqli_query($conn,$sql) or die($content->data_video_id);
    }
  }
  
  #calculate the number of video need to send back
  //count the max number of video can show
  $sql="SELECT COUNT(*) FROM `video`"; 
  $result=mysqli_query($conn,$sql);
  $row = mysqli_fetch_array($result);
  //echo $row['COUNT(*)'];
  $maxShowVideo = $row['COUNT(*)'];
  $numShowVideo = 0;
  $pageShowVideo = 16;
  if($pageShowVideo * $page > $maxShowVideo){
    $numShowVideo = $maxShowVideo - ($page-1)*$pageShowVideo;
  }else{
    $numShowVideo = $pageShowVideo;
  }
}
?>

<?php
# Response thumb's JSON string
$first = true;
print '['; # Make an array
for($i = ($page-1)*$pageShowVideo; $i < (($page-1)*$pageShowVideo) + $numShowVideo; $i++)
{
  //take thumbnail from DB
  if($first) $first = false;
  else print ',';
  $tableIndex = $i+1;
  $sql="SELECT videoInfo FROM `video` WHERE `id`='$tableIndex'";
  $result=mysqli_query($conn,$sql);
  $row = mysqli_fetch_array($result);
  print $row['videoInfo'];
}
print ']';
?>

