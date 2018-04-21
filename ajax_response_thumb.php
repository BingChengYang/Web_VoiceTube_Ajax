<?php

$contentPath = "content.JSON";

if($_SERVER['REQUEST_METHOD'] == 'GET'){
  try
  {
  $page = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_URL);

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
  
  #calculate the number of video need to send back
  $maxShowVedio = 9;
  $numShowVedio = 0;
  if(4 * $page > $maxShowVedio){
    $numShowVedio = $maxShowVedio - ($page-1)*4;
  }else{
    $numShowVedio = 4;
  }
}
?>

<?php
# Response thumb's JSON string
$first = true;
print '['; # Make an array
for($i = ($page-1)*4; $i < (($page-1)*4) + $numShowVedio; $i++)
{
  if($first) $first = false;
  else print ',';
  print json_encode($contents[$i]);
}
print ']';
?>

