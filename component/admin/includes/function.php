<?php

require_once ( "api/wimtv_api.php" );

function syncWimtvpro ($username, $page) {
	$table_name = '#__wimtvpro_videos';

    $response = apiGetVideos();
    //JFactory::getApplication()->enqueueMessage($response);
	$array_json_videos = json_decode($response);

	if ($array_json_videos==NULL) {
	
		JError::raiseWarning( 100, "COM_WITVPRO_ERROR_WIMTVPRO" );
	}
	else {
		//$num = (array)simplexml_load_string($response);
		$i=0;
		foreach ($array_json_videos -> items as $a) {
			foreach ($a as $key => $value) {
				$array_all_videos[$i][$key] = $value;
			}
			$i++;
		}
		
		$num = count($array_json_videos);
		if ($num > 0 ) {
			$elenco_video_wimtv = array();
			$elenco_video_wp = array();
			
	
			// Get a db connection.
			$db = JFactory::getDbo();
			
			// Create a new query object.
			$query = $db->getQuery(true);
			
			// Select all records from the user profile table where key begins with "custom.".
			// Order it by the ordering field.
			$query->select(array('contentidentifier'));
			$query->from($table_name );
			$query->where("uid='" . $username . "'");

			
			// Reset the query using our newly populated query object.
			$db->setQuery($query);
			
			// Load the results as a list of stdClass objects.
			//$array_videos_new_wp = $db->loadObjectList();
			$array_videos_new_wp = $db->loadResultArray();
			if ($error = $db->getErrorMsg()) {
				throw new Exception($error);
			}
		
			
			if ($array_videos_new_wp){
				foreach ($array_videos_new_wp as $record) {
					array_push($elenco_video_wp, $record);
				}
			}
			
			/* Information detail videos into Showtime */

            $json_st  = wimtvpro_detail_showtime(FALSE, 0);
            $arrayjson_st = json_decode($json_st);
			$values_st = $arrayjson_st->items;


			foreach ($values_st as $key => $value) {
				$array_st[$value -> {"contentId"}]["showtimeIdentifier"] = $value-> {"showtimeIdentifier"};
			}
			if ($array_all_videos) {
				foreach ($array_all_videos as $video) {
					$url_video = $video["actionUrl"];
					$status = $video["status"];
					//$acquired_identifier = $video["acquired_identifier "];
					$title= $video["title"];
					$urlVideo= $video["streamingUrl"]->streamer . "$$" . $video["streamingUrl"]->file . "$$" . $video["streamingUrl"]->auth_token;
					$duration= $video["duration"];
					$content_item =  $video["contentId"];
					$url_thumbs = '<img src="' . $video["thumbnailUrl"] . '"  title="' . $title . '" class="wimtv-thumbnail" />';
					$categories  = "";
					$valuesc_cat_st = "";
					foreach ($video["categories"] as $key => $value) {
						$valuesc_cat_st .= $value->categoryName;
						$categories .= $valuesc_cat_st;
						foreach ($value -> subCategories as $key => $value) {
							$categories .= " / " . $value -> categoryName;
						}
						$categories .= "<br/>";
					}

                    array_push($elenco_video_wimtv, $content_item);
					if (trim($content_item)!="") {
						//controllo se il video esiste
						$trovato = FALSE;
						//controllo se il video eiste in DRUPAL ma non pi&#65533; in WIMTV
						if ($array_videos_new_wp){
							foreach ($array_videos_new_wp as $record) {
								$content_itemAll = $record ;
								if ($content_itemAll == $content_item) {
									$trovato = TRUE;
								}
							}
						}
						$pos_wimtv="";
						$showtime_identifier ="";
						if (isset($array_st[$content_item])) {
							$pos_wimtv="showtime";
							$showtime_identifier = $array_st[$content_item]["showtimeIdentifier"];
                        }
						else {
							$pos_wimtv="";
                        }
	
						if (!$trovato) {


                            // Create and populate an object.
							$insert_video = new stdClass();
							$insert_video->uid = $username;
							$insert_video->contentidentifier=$content_item;
							$insert_video->mytimestamp=time();
							$insert_video->position=0;
							$insert_video->state='';
							$insert_video->viewVideoModule=$pos_wimtv;		
							$insert_video->status = $status;
							$insert_video->acquiredIdentifier = $acquired_identifier;
							$insert_video->urlThumbs = mysql_real_escape_string($url_thumbs);
							$insert_video->category =  $categories;
							$insert_video->urlPlay =  mysql_real_escape_string($urlVideo);
							$insert_video->title =  mysql_real_escape_string($title);
							$insert_video->duration = $duration;
							$insert_video->showtimeidentifier = $showtime_identifier;



                            try {
								// Insert the object into the user profile table.
								$result = JFactory::getDbo()->insertObject('#__wimtvpro_videos', $insert_video);
							} catch (Exception $e) {
								throw new Exception($e);
							}




                        }
						else {
	
							// Fields to update.
							$fields = array(
									"state = '" . $pos_wimtv . "'",
									" status = '" . $status . "'",
									" title = '" . mysql_real_escape_string($title) . "'",
									" urlThumbs = '" . mysql_real_escape_string($url_thumbs) . "'",
									" urlPlay = '" . mysql_real_escape_string($urlVideo) . "'",
									" duration = '" . $duration . "'",
									" showtimeidentifier = '" . $showtime_identifier . "'",
									" category = '" . $categories . "'"
							);

                            // Conditions for which records should be updated.
							$conditions = array(
									"  contentidentifier = '"  . $content_item . "' ");
							$db3 = JFactory::getDbo();
							$query3 = $db3->getQuery(true);
							$query3->update($db3->quoteName($table_name))->set($fields)->where($conditions);
	
							$db3->setQuery($query3);

                            try {
								$result = $db3->query(); // Use $db->execute() for Joomla 3.0.
							} catch (Exception $e) {
								throw new Exception($e);
							}


                        }
					}
				}
			} else {
				JError::raiseNotice( 100, "You aren't videos" );
			}
	
			//var_dump(array_diff($elenco_video_wp ,$elenco_video_wimtv ));
			$delete_into_wp = array_diff($elenco_video_wp, $elenco_video_wimtv);

            foreach ($delete_into_wp as  $value) {
			
				
				$db = JFactory::getDBO();

                $query->delete($table_name);
				$query->where(array('contentidentifier'=> $value));
				$db->setQuery($query);


                try {
					$result = $db->query(); // $db->execute(); for Joomla 3.0.
				} catch (Exception $e) {
					throw new Exception($e);
				}
					
			}

		}
		else {
			JError::raiseWarning( 100, 'Never elements' );
		}
	}

    $link = 'index.php?option=com_wimtvpro&view=' . $page;
    JFactory::getApplication()->redirect($link);
}

//MY STREAMING: This API allows to list videos in my streaming public area. Even details may be returned
function wimtvpro_detail_showtime($single, $st_id) {

	if (!$single) {
        $response = apiGetShowtimes();
        $array_detail = $response->body;
    } else {
        $array_detail = apiGetDetailsShowtime($st_id);
	}
    return $array_detail;
}




function getDateRange($startDate, $endDate, $format="Y/m/d"){

	//Create output variable

	$datesArray = array();

	//Calculate number of days in the range

	$total_days = round(abs(strtotime($endDate) - strtotime($startDate)) / 86400, 0) + 1;

	if($days<0) {
		return false;
	}

	//Populate array of weekdays and counts

	for($day=0; $day<$total_days; $day++)

	{

		$datesArray[] = date($format, strtotime("{$startDate} + {$day} days"));

	}

	//Return results array

	return $datesArray;

}


function wimtvpro_alert_reg($username,$password,$stamp=true){
	//If user isn't register or not inser user and password
	if (($username=="username" && $password=="password") || ($username=="" && $password=="")){
		
		$ahref= '<a class="modal" href="index.php?option=com_config&amp;view=component&amp;component=com_wimtvpro&amp;path=&amp;tmpl=component" rel="{handler: \'iframe\', size: {x: 875, y: 550}, onClose: function() {}}">';
		
		$ahrefReg= '<a class="modal" href="index.php?option=com_wimtvpro&amp;view=register&amp;tmpl=component&amp;layout=edit" rel="{handler: \'iframe\', size: {x: 875, y: 550}, onClose: function() {}}">';
		if ($stamp)
		JError::raiseWarning( 100, "Please " . $ahref  . "configuration you wimtv account</a> or " . $ahrefReg . " register </a>" );
		return FALSE;
	} else {
		return TRUE;
	}
}


function wimtvpro_viever_jwplayer($userAgent,$contentId,$video,$dirJwPlayer){

	$isiPad = (bool) strpos($userAgent,'iPad');
	$urlPlay = explode("$$",$video[0]->urlPlay);
	$isiPhone = (bool) strpos($userAgent,'iPhone');
	if ($isiPad  || $isiPhone) {
		$urlPlayIPadIphone = "";
		$contentId = $video[0]->contentidentifier;
		$response = apiGetDetailsVideo($contentId);
		$arrayjson   = json_decode($response);

		$urlPlayIPadIphone = $arrayjson->streamingUrl->streamer;
		$configFile = "'file': '" . $urlPlayIPadIphone . "',";

	}	else {

		$configFile = "'flashplayer':'" . $dirJwPlayer . "','file': '" . $urlPlay[1] . "','streamer':'" . $urlPlay[0] . "',";

	}
	return $configFile;
}


function createIframePlaylist($arrayVideo,$dirJwPlayer,$user="admin"){
	
	$app = &JFactory::getApplication();
	$params = JComponentHelper::getParams('com_wimtvpro');
	$username = $params->get('wimtv_username');
	$password = $params->get('wimtv_password');
	$basePath = $params->get('wimtv_basepath');
	$urlThumbsWimtv = $params->get('wimtv_urlThumbsWimtv');
	$replaceContentWimtv = $params->get('wimtv_replaceContentWimtv');
	$height = $params->get('wimtv_heightPreview');
	$width = $params->get('wimtv_widthPreview');
	$skinName = $params->get('wimtv_nameSkin');
	$credential = $username . ":" . $password;
	if (count($arrayVideo)==0){
	
		$output = "Never Videos";
	
	} else {
		$videoList = " AND ( 1=2";
		foreach ($arrayVideo as $value){
	
			$videoList .= " OR showtimeIdentifier='" . $value . "'";
	
		}
		$videoList .= " ) ";
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__wimtvpro_videos');
		$query->order("position ASC");
		$query->where("uid='" . $username . "' " . $videoList);
		$db->setQuery($query);
		$array_videos_new_wp = $db->loadObjectList();
	
		foreach ($array_videos_new_wp as &$row)
		{
	
			/*$param_thumb = $basePath . str_replace($replaceContentWimtv, $row->contentidentifier, $urlThumbsWimtv);
	
			$ch_thumb = curl_init();
			curl_setopt($ch_thumb, CURLOPT_URL, $param_thumb);
			curl_setopt($ch_thumb, CURLOPT_VERBOSE, 0);
			curl_setopt($ch_thumb, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch_thumb, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($ch_thumb, CURLOPT_USERPWD, $credential);
			curl_setopt($ch_thumb, CURLOPT_SSL_VERIFYPEER, FALSE);*/
			$replace_video = apiGetThumbsVideo($row->contentidentifier); //curl_exec($ch_thumb);
	
			$query2 = $db->getQuery(true);
			$query2->select('*');
			$query2->from('#__wimtvpro_videos');
			$query2->where("contentidentifier='" . $row->contentidentifier . "'");
			$db->setQuery($query2);
			$video = $db->loadObjectList();
	
	
			$configFile  = wimtvpro_viever_jwplayer($_SERVER['HTTP_USER_AGENT'], $row->contentidentifier,$video,FALSE);
			$playlist = "{" . $configFile . " 'image':'" . $replace_video  . "','title':'" . $row->title . "'},";
	
		}
		$output = "<div id='container_playlist" .  $row->id  . "'></div>";
		$playlistSize = "30%";
		$dimensions = "width: '100%',height:'" . $height . "px',";
	
		$output .= "<script type='text/javascript'>jwplayer('container_playlist" .  $row->id  . "').setup({";
	
		
		
		$option = $array_playlist[0]->option;
		$array_option = explode(",",$option);
		$options = array();
		foreach ($array_option as $value){
	
			$array = explode(":",$value);
	
			if ($array[0]!="")
				$options[$array[0]] = $array[1];
	
		}
		if ($options["loop"]!="no") $output .= "'repeat':'always',";
	
		$isiPhone = (bool)strpos($_SERVER['HTTP_USER_AGENT'],'iPhone');
		$isiPad = (bool) strpos($_SERVER['HTTP_USER_AGENT'],'iPad');
		if (!$isiPad  AND !$isiPhone)
			$output .= "'flashplayer':'" . $dirJwPlayer . "',";
		
		if ($user=="admin")
			$directory  = JURI::base() . "components/com_wimtvpro/uploads/skin";
		else
			$directory  = JURI::base() . "administrator/components/com_wimtvpro/uploads/skin";
		if ($skinName!="") {
			$output .= "'skin':'" . $directory  . "/" . $skinName . ".zip',";
		}
	
		$output .= $dimensions . "'playlist': [" .  $playlist . "],'playlist.position': 'right',	'playlist.size': '" . $playlistSize  . "'});</script>&nbsp;";
	
	
	}
	return $output;
}
