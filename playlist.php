<?php

$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = 'pig8525168';
$dbname = 'web';
//$conn = mysql_connect($dbhost, $dbuser, $dbpass) or die('Error with MySQL connection');
$conn = mysqli_connect($dbhost, $dbuser, $dbpass) or die('Error with MySQL connection');
mysqli_query($conn,"SET NAMES 'utf8'");
mysqli_select_db($conn,$dbname);

$api_key = 'AIzaSyAgWfAeq7kHU0PhMls1BqT0fMs-9iHNEv8';
$listid = $_GET["listid"];

function getYouTubeVideoTime($timestr)
{
	if(strpos($timestr, 'H') !== false){
		$temp = explode('H',$timestr);
		$hour = explode('T',$temp[0]);
		$tmp = explode('M',$temp[1]);
		$min = $tmp[0];
		$sec = explode('S',$tmp[1]);
		if(intval($hour[1]) < 10){
			if(intval($min) < 10){
				if(intval($sec[0]) < 10) { $finalTime = "0".$hour[1].":"."0".$min.":"."0".$sec[0]; }
				else { $finalTime = "0".$hour[1].":"."0".$min.":".$sec[0]; }
			
			}
			else{
				if(intval($sec[0]) < 10) { $finalTime = "0".$hour[1].":".$min.":"."0".$sec[0]; }
				else { $finalTime = "0".$hour[1].":".$min.":".$sec[0]; }
			}
		}
		else{
			if(intval($min) < 10){
				if(intval($sec[0]) < 10) { $finalTime = $hour[1].":"."0".$min.":"."0".$sec[0]; }
				else { $finalTime = $hour[1].":"."0".$min.":".$sec[0]; }
			
			}
			else{
				if(intval($sec[0]) < 10) { $finalTime = $hour[1].":".$min.":"."0".$sec[0]; }
				else { $finalTime = $hour[1].":".$min.":".$sec[0]; }
			}
		}
		
	}
	else{
		$tmp = explode('M',$timestr);
		$min = explode('T',$tmp[0]);
		$sec = explode('S',$tmp[1]);
		if(intval($min[1]) < 10){
			if(intval($sec[0]) < 10) { $finalTime = "0".$min[1].":"."0".$sec[0]; }
			else { $finalTime = "0".$min[1].":".$sec[0]; }
			
		}
		else{
			if(intval($sec[0]) < 10) { $finalTime = $min[1].":"."0".$sec[0]; }
			else { $finalTime = $min[1].":".$sec[0]; }
		}
	}
	
	return $finalTime;
}

// $next_page = "https://www.googleapis.com/youtube/v3/playlistItems?part=snippet&playlistId=PLm_3vnTS-pvmZFuF3L1Pyhqf8kTTYVKjW&key=AIzaSyAgWfAeq7kHU0PhMls1BqT0fMs-9iHNEv8&pageToken="."CAUQAA";

$playlist_info_url = "https://www.googleapis.com/youtube/v3/playlists?part=snippet&id=".$listid."&key=".$api_key;
$playlist_info = json_decode(file_get_contents($playlist_info_url));

$api_list_url = "https://www.googleapis.com/youtube/v3/playlistItems?part=snippet&maxResults=50&playlistId=".$listid."&key=".$api_key;
$listdata = json_decode(file_get_contents($api_list_url));


$playlist_totalResults = $listdata->pageInfo->totalResults;
$playlist_resultsPerPage = $listdata->pageInfo->resultsPerPage;
$needToNextPage = false;
$totalPageNum = 1;

if($playlist_resultsPerPage < $playlist_totalResults){ 
	$needToNextPage = true;
	if($playlist_totalResults%$playlist_resultsPerPage == 0)
		$totalPageNum = $playlist_totalResults/$playlist_resultsPerPage;
	else
		$totalPageNum = $playlist_totalResults/$playlist_resultsPerPage + 1;
}


// start to print the page
echo '<head>

		<title lang="zh-TW">Playlist</title>

		<!-- Required meta tags -->
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">

		<!-- Bootstrap CSS -->
		<!-- Latest compiled and minified CSS -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		<!-- jQuery library -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
		<!-- Latest compiled JavaScript -->
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

		<!-- Infinite scroll -->
		<script src="js/infinite-scroll.pkgd.min.js"></script>

		<!-- My CSS -->

	</head>';

echo '<div class="container">';
	echo '<div class="row">';
		echo '<div class="col-xs-12 col-sm-12 col-md-10 col-lg-10">';
			echo "<h2>".$playlist_info->items[0]->snippet->localized->title."</h2><br>";
			
			echo '<table class="table table-condensed">';

			echo '<tbody>';

				// start for-loop to show the videos in playlist & insert in SQL
				$page_url = $api_list_url;
				$idx = 1;
				for($i = 1; $i <= $totalPageNum; $i++){
					$pagedata = json_decode(file_get_contents($page_url));

					for($v = 0; $v < count($pagedata->items); $v++){
						//get caption language first
						$listItem = $pagedata->items[$v];
						$id = $listItem->snippet->resourceId->videoId;
						$api_url = 'https://www.googleapis.com/youtube/v3/videos?part=snippet%2CcontentDetails%2Cstatistics&id='.$id.'&key='.$api_key;
						
						$data = json_decode(file_get_contents($api_url));
						$image = 'https://img.youtube.com/vi/'.$id.'/0.jpg';

						//build thumbnail information
						$json_thumbnail = '{"data_video_id" : "'.$id.'",'
						.'"a_href" : "player.html?id='.$id.'&file=caption_'.$id.'",'
						.'"img_src" : "https://img.youtube.com/vi/'.$id.'/0.jpg",'
						.'"img_alt" : "'.$data->items[0]->snippet->title.' Image",'
						.'"video_time" : "'.getYouTubeVideoTime($data->items[0]->contentDetails->duration).'",'
						.'"data_original_title" : "'.$data->items[0]->snippet->title.'",'
						.'"h5_a_href" : "'.$data->items[0]->snippet->title.'"}';


						
						if($data->items[0]->contentDetails->caption == 'true'){
							$lan_api = 'https://www.googleapis.com/youtube/v3/captions?part=snippet&videoId='.$id.'&key='.$api_key;
							$lan_data = json_decode(file_get_contents($lan_api));

							$language = $lan_data->items[0]->snippet->language;

							$g_api_url = 'https://www.youtube.com/api/timedtext?lang='.$language.'&v='.$id;

							$xml_data = file_get_contents($g_api_url);
							$xml_data = str_replace(array("\n", "\r", "\t"), '', $xml_data);
					     	$xml_data = trim(str_replace('"', "'", $xml_data));
							// SimpleXml parser
							$json_caption = '{"en":[';
							$xml = simplexml_load_string($xml_data);
							if($xml != null){
								foreach($xml->children() as $texts) { 
									$json_caption = $json_caption.'{"start":"'.$texts['start'].'",'
																.'"dur":"'.$texts['dur'].'",'
																.'"text":"'.$texts.'"},';
								    
								} 
								$json_caption = substr($json_caption, 0, -1);
								$json_caption = $json_caption.']}';
								$json_caption = mysqli_real_escape_string($conn,$json_caption);
						    	$videoInfo = mysqli_real_escape_string($conn,$json_thumbnail);
								$sql="SELECT * FROM `video` WHERE `videoID`='$id'";
						    	$result=mysqli_query($conn,$sql);

						    	// echo $videoInfo;
						    	// echo $json_caption	;
						    
						    	if(mysqli_num_rows($result)==0){  //if the video is not in table , then insert
						  
						  	    	$sql = "INSERT INTO video(videoID,videoInfo,caption) VALUES('$id','$videoInfo','$json_caption')";
						        	mysqli_query($conn,$sql) or die($id);
						    	}
					    	}
						}
						else{
							$videoInfo = mysqli_real_escape_string($conn,$json_thumbnail);
							$sql="SELECT * FROM `video` WHERE `videoID`='$id'";
						    $result=mysqli_query($conn,$sql);

						    
						    if(mysqli_num_rows($result)==0){  //if the video is not in table , then insert
						  
						  	    $sql = "INSERT INTO video(videoID,videoInfo) VALUES('$id','$videoInfo')";
						        mysqli_query($conn,$sql) or die($id);
						    }
							// no caption, maybe header to another html
						}

						// Then, show on table
      					echo "<tr>
				        		<td>".$idx."</td>";
      					echo '<td>
      							<a href="player.html?id='.$listItem->snippet->resourceId->videoId.'&file=caption_'.$listItem->snippet->resourceId->videoId.'">
      							<img src="'.$listItem->snippet->thumbnails->default->url.'"></a></td>';
      					echo '<td><a href="player.html?id='.$listItem->snippet->resourceId->videoId.'&file=caption_'.$listItem->snippet->resourceId->videoId.'">'.$listItem->snippet->title.'</a></td>
      						 </tr>';
      					$idx++;
					}
					// still have next page
					if($i != $totalPageNum){ 
						$playlist_nextPageToken = $pagedata->nextPageToken;
						$page_url = "https://www.googleapis.com/youtube/v3/playlistItems?part=snippet&maxResults=50&playlistId=".$listid."&key=".$api_key."&pageToken=".$playlist_nextPageToken;
					}
				}

    		echo '</tbody>';

			echo '</table>';

		echo '</div>';
		echo '<div class="col-xs-0 col-sm-0 col-md-2 col-lg-2">';
		echo '</div>';
	echo "</div>";
echo '</div>';
// print_r($playlist_info);


?>