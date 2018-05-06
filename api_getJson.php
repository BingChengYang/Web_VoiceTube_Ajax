<?php
//////////// video's tiltle, time and picture

//setting db connection
$dbhost = 'localhost';
$dbuser = 'id5635354_root';
$dbpass = 'pig8525168';
$dbname = 'id5635354_web';
//$conn = mysql_connect($dbhost, $dbuser, $dbpass) or die('Error with MySQL connection');
$conn = mysqli_connect($dbhost, $dbuser, $dbpass) or die('Error with MySQL connection');
mysqli_query($conn,"SET NAMES 'utf8'");
mysqli_select_db($conn,$dbname);

function getYouTubeVideoID($url)
{
	$queryString = parse_url($url, PHP_URL_QUERY);
	parse_str($queryString, $params);
	if(isset($params['v']) && strlen($params['v']) > 0){
		return $params['v'];
	}
	else{
		return "";
	}
}

//playlist
function getYouTubePlaylistID($listurl)
{
	$queryString2 = parse_url($listurl, PHP_URL_QUERY);
	parse_str($queryString2, $params2);
	if(isset($params2['list']) && strlen($params2['list']) > 0){
		return $params2['list'];
	}
	else{
		return "";
	}
}

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

$api_key = 'AIzaSyAgWfAeq7kHU0PhMls1BqT0fMs-9iHNEv8';

$video_url = $_GET["videourl"];

// playlist
if(getYouTubeVideoID($video_url) == ""){
	// there is no v=... in url
	if(strpos($video_url, 'list') !== false){
		// valid playlist's url
		$listid = getYouTubePlaylistID($video_url);

		// playlist
		header('Location: playlist.php?listid='.$listid);

	}
	else{
		echo "Invalid url!";
	}
}
else{
	$id = getYouTubeVideoID($video_url);
	$api_url = 'https://www.googleapis.com/youtube/v3/videos?part=snippet%2CcontentDetails%2Cstatistics&id='.$id.'&key='.$api_key;


	$data = json_decode(file_get_contents($api_url));

	$image = 'https://img.youtube.com/vi/'.$id.'/0.jpg';

	$json_thumbnail = '{"data_video_id" : "'.$id.'",'
						.'"a_href" : "player.html?id='.$id.'&file=caption_'.$id.'",'
						.'"img_src" : "https://img.youtube.com/vi/'.$id.'/0.jpg",'
						.'"img_alt" : "'.$data->items[0]->snippet->title.' Image",'
						.'"video_time" : "'.getYouTubeVideoTime($data->items[0]->contentDetails->duration).'",'
						.'"data_original_title" : "'.$data->items[0]->snippet->title.'",'
						.'"h5_a_href" : "'.$data->items[0]->snippet->title.'"}';


	//////////// get caption and put it into database;

	if($data->items[0]->contentDetails->caption == 'true'){
				//get language
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
		if($xml!=null){
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

	    
	    	if(mysqli_num_rows($result)==0){  //if the video is not in table , then insert
	  
	  	    	$sql = "INSERT INTO video(videoID,videoInfo,caption) VALUES('$id','$videoInfo','$json_caption')";
	        	mysqli_query($conn,$sql) or die($id);
	    	}
		}	

	}
	else{
		// no caption, maybe header to another html
	}
	header('Location: player.html?id='.$id.'&file=caption_'.$id);

	// there is v=... in url, then check whethere the video is in list
	if(strpos($video_url, 'list') !== false){}
	else{}
}
/////////////////////////// end of option //////////////////////////

 

?>




