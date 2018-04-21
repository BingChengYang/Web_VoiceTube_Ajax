<?php

if($_SERVER['REQUEST_METHOD'] == 'GET'){
  try
  {
  $capFile = filter_input(INPUT_GET, 'cap', FILTER_SANITIZE_URL);

  $captionPath = 'captions/' . $capFile .'.JSON';
    # Load content from JSON file
    if(is_readable($captionPath))  
      $capContent = file_get_contents($captionPath);
    else                           
      throw new Exception("Cannot load content!");

  }
  catch(Exception $e)
  {
    header('HTTP/1.1 400 Bad request'); 
    echo $e->getMessage();
  }

  # Convert all JSON string to object 
  $capContents = json_decode($capContent);
  
}
?>

<?php
# Response caption's HTML

foreach($capContents->en as $key => $caption)
{
print <<<STRING
                  <div id="show-caption-table">
                    <td class="align-top" width="25">
                      <a href="javascript:;" onclick="playCaptions('$caption->start', '$caption->dur')">
                        <span class="glyphicon glyphicon-play"></span>
                      </a>
                    </td>
                    <td id="seq-$key" start="$caption->start" end="$caption->dur">$caption->text</td>
                  </div>
STRING;
}
?>
