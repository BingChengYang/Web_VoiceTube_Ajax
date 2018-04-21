<?php

$adPath = "ad.JSON";

if($_SERVER['REQUEST_METHOD'] == 'GET'){
  try
  {
    $adnum = filter_input(INPUT_GET, 'ad', FILTER_SANITIZE_URL);

    # Read ad from JSON file
    if(is_readable($adPath))  
      $jAd = file_get_contents($adPath);
    else                           
      throw new Exception("Cannot load ad!");

  }
  catch(Exception $e)
  {
    header('HTTP/1.1 400 Bad request'); 
    echo $e->getMessage();
  }

  # Convert all JSON string to object 
  $ads = json_decode($jAd);
  
}
?>

<?php
# Response ad's HTML

foreach($ads as $ad)
{
print <<<STRING
                      <li>
                        <div>
                           <a target="_blank" href="$ad->href">$ad->title</a>
                           <div class="short_company">$ad->company</div>
                        </div>
                      </li>
STRING;
}
?>

