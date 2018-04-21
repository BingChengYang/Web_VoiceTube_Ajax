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
# Response thumb's HTML
for($i = ($page-1)*4; $i < (($page-1)*4) + $numShowVedio; $i++)
{
  $instance = $contents[$i];

print <<<STRING
        <div class="single-thumb col-xs-6 col-sm-4 col-md-3 col-lg-3">
          <div class="thumbnail" data-video-id="$instance->data_video_id">
            <div class="photo" video_id = "$instance->data_video_id">
              <a href="$instance->a_href">
                <img class="lazy" src="$instance->img_src" data-original="$instance->img_src" alt="$instance->img_alt">
              </a>
              <span class="readed-label" data-video-id="$instance->data_video_id">
                <img src="$instance->img_readed" alt=" " rel="tooltip" data-original-title="Watched">
              </span>
              <span class="label label-inverse photo-label">
                <span class="video-time">$instance->video_time</span>
              </span>
            </div>
            <div class="caption">
              <h5 class="index-thumbnail-title" rel="tooltip" data-original-title="$instance->data_original_title">
                <a href="$instance->a_href">$instance->h5_a_href
                </a>
              </h5>
              <div class="pull-left">
                <div>$instance->view</div>
              </div>
              <div class="clearfix"></div>
              <div class="thumbnail-tags">
                <a href="$instance->level_href">
                  <span class="label label-info">$instance->level</span>
                </a>
                <a href="$instance->chinese_href">
                  <span class="label" rel="tooltip" data-original-title="$instance->chinese_tag">$instance->chinese_caption
                  </span>
                </a>
                <a href="$instance->pronounciation_href">
                  <span class="label label-warning" rel="tooltip" data-original-title="$instance->pronounciation_tag">$instance->pronounciation
                  </span>
                </a>
              </div>
            </div>
          </div>
        </div>    
STRING;
}

?>

