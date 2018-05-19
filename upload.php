<?php
	$dbhost = 'localhost';
	$dbuser = 'root';
	$dbpass = 'pig8525168';
	$dbname = 'web';
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

	function convertTime($time){
		$totalTime = 0;
		$arr = split(':', $time);
		$arrExtend = split(',', $arr[2]);
		//echo $arr[0].'<br/>';
		$totalTime = $totalTime + $arr[0]*3600000 + $arr[1]*60000 + $arrExtend[0]*1000 + $arrExtend[1];
		return $totalTime; //this is ms
	}
	//i need to check this is srt file or not (to do)
	if ($_FILES['file']['error'] > 0){
		echo "Error".$_FILES['file']['error'];
	}else{
		// echo "檔案名稱: " . $_FILES["file"]["name"]."<br/>";
		// echo "檔案類型: " . $_FILES["file"]["type"]."<br/>";
		// echo "檔案大小: " . ($_FILES["file"]["size"] / 1024)." Kb<br />";
		// echo "暫存名稱: " . $_FILES["file"]["tmp_name"];
		$extension=pathinfo($_FILES['file']['name'],PATHINFO_EXTENSION);//取得檔案副檔名
		if (file_exists("upload/" . $_FILES["file"]["name"])){
			echo "檔案已經存在，請勿重覆上傳相同檔案";
		}else{
			if(in_array($extension,array('srt'))){//檢查檔案副檔名
				move_uploaded_file($_FILES["file"]["tmp_name"],"upload/".$_FILES["file"]["name"]);
								// parse srt
				define('SRT_STATE_SUBNUMBER', 0);
				define('SRT_STATE_TIME',      1);
				define('SRT_STATE_TEXT',      2);
				define('SRT_STATE_BLANK',     3);

				$lines   = file("upload/".$_FILES["file"]["name"]);

				$subs    = array();
				$state   = SRT_STATE_SUBNUMBER;
				$subNum  = 0;
				$subText = '';
				$subTime = '';

				foreach($lines as $line) {
					$line = str_replace(array("\n", "\r", "\t"), '', $line);
		     		$line = trim(str_replace('"', "'", $line));
				    switch($state) {
				        case SRT_STATE_SUBNUMBER:
				        	$first = 0;
				            $subNum = trim($line);
				            $state  = SRT_STATE_TIME;
				            break;

				        case SRT_STATE_TIME:
				            $subTime = trim($line);
				            $state   = SRT_STATE_TEXT;
				            break;

				        case SRT_STATE_TEXT:
				            if (trim($line) == '' && $first != 0) {
				                $sub = new stdClass;
				                $sub->number = $subNum;
				                list($sub->startTime, $sub->stopTime) = explode(' --> ', $subTime);
				                $sub->text   = $subText;
				                $subText     = '';
				                $state       = SRT_STATE_SUBNUMBER;

				                $subs[]      = $sub;
				            } else {
				            	$first = $first + 1;
				                $subText .= $line;
				            }
				            break;
				    }
				}

				if ($state == SRT_STATE_TEXT) {
				    // if file was missing the trailing newlines, we'll be in this
				    // state here.  Append the last read text and add the last sub.
				    $sub->text = $subText;
				    $subs[] = $sub;
				}
				// change srt to json and insert into db
				$json_caption = '{"en":[';
				foreach($subs as $sub) {
					$startTime = convertTime($sub->startTime);
					$stopTime = convertTime($sub->stopTime);
					$dur = ($stopTime - $startTime)/1000;
					$dur = round($dur,3);
					$stopTime = round($stopTime/1000,3);
					$startTime = round($startTime/1000,3);
					// echo "sta".$startTime."<br/>";
					// echo "sto".$stopTime."<br/>";
					// echo "dur".$dur."<br/>";
					if($startTime<$stopTime){
					    $json_caption = $json_caption.'{"start":"'.$startTime.'",'
														.'"dur":"'.$dur.'",'
														.'"text":"'.$sub->text.'"},';
					}
				}
				$json_caption = substr($json_caption, 0, -1);
				$json_caption = $json_caption.']}';
				$json_caption = mysqli_real_escape_string($conn,$json_caption);
				$sql="SELECT * FROM `video` WHERE `videoID`='$id'";
			    $result=mysqli_query($conn,$sql);
			    if(mysqli_num_rows($result)!=0){  //if the video is in table , then update caption
			  	    $sql = "UPDATE `video` SET caption='$json_caption' WHERE `videoID`='$id'";
			        mysqli_query($conn,$sql) or die($id);
			    }
			    unlink("upload/".$_FILES["file"]["name"]);
				//echo $json_caption;
				//$json_caption = mysqli_real_escape_string($conn,$json_caption);
				header('Location:edit_caption.html?id='.$id.'&file=caption_'.$id);
			}else{
				echo '不允許該檔案格式';
			}
		}
	}
?>