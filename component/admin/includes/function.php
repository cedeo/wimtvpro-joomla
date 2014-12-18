<?php

require_once ( "api/wimtv_api.php" );

function syncWimtvpro($username, $page) {
    $table_name = '#__wimtvpro_videos';

    $response = apiGetVideos();
    //JFactory::getApplication()->enqueueMessage($response);
    $array_json_videos = json_decode($response);

    if ($array_json_videos == NULL) {
        JError::raiseWarning(100, "COM_WITVPRO_ERROR_WIMTVPRO");
    } else {
        //$num = (array)simplexml_load_string($response);
        $i = 0;
        foreach ($array_json_videos->items as $a) {
            foreach ($a as $key => $value) {
                $array_all_videos[$i][$key] = $value;
            }
            $i++;
        }

        $num = count($array_json_videos);
        if ($num > 0) {
            $elenco_video_wimtv = array();
            $elenco_video_wp = array();

            // Get a db connection.
            $db = JFactory::getDbo();

            // Create a new query object.
            $query = $db->getQuery(true);

            // Select all records from the user profile table where key begins with "custom.".
            // Order it by the ordering field.
            $query->select(array('contentidentifier'));
            $query->from($table_name);
            $query->where("uid='" . $username . "'");

            // Reset the query using our newly populated query object.
            $db->setQuery($query);

            // Load the results as a list of stdClass objects.
            //$array_videos_new_wp = $db->loadObjectList();
            $array_videos_new_wp = $db->loadResultArray();
            if ($error = $db->getErrorMsg()) {
                throw new Exception($error);
            }

            if ($array_videos_new_wp) {
                foreach ($array_videos_new_wp as $record) {
                    array_push($elenco_video_wp, $record);
                }
            }

            /* Information detail videos into Showtime */

            $json_st = wimtvpro_detail_showtime(FALSE, 0);
            $arrayjson_st = json_decode($json_st);
            $values_st = $arrayjson_st->items;

            foreach ($values_st as $key => $value) {
                $array_st[$value->{"contentId"}]["showtimeIdentifier"] = $value->{"showtimeIdentifier"};
            }
            if ($array_all_videos) {
                foreach ($array_all_videos as $video) {
                    $url_video = $video["actionUrl"];
                    $status = $video["status"];
                    //$acquired_identifier = $video["acquired_identifier "];
                    $title = $video["title"];
                    $urlVideo = $video["streamingUrl"]->streamer . "$$" . $video["streamingUrl"]->file . "$$" . $video["streamingUrl"]->auth_token;
                    $duration = $video["duration"];
                    $content_item = $video["contentId"];
                    $url_thumbs = '<img src="' . $video["thumbnailUrl"] . '"  title="' . $title . '" class="wimtv-thumbnail" />';
                    $categories = "";
                    $valuesc_cat_st = "";
                    foreach ($video["categories"] as $key => $value) {
                        $valuesc_cat_st .= $value->categoryName;
                        $categories .= $valuesc_cat_st;
                        foreach ($value->subCategories as $key => $value) {
                            $categories .= " / " . $value->categoryName;
                        }
                        $categories .= "<br/>";
                    }

                    array_push($elenco_video_wimtv, $content_item);
                    if (trim($content_item) != "") {
                        //controllo se il video esiste
                        $trovato = FALSE;
                        //controllo se il video eiste in DRUPAL ma non pi&#65533; in WIMTV
                        if ($array_videos_new_wp) {
                            foreach ($array_videos_new_wp as $record) {
                                $content_itemAll = $record;
                                if ($content_itemAll == $content_item) {
                                    $trovato = TRUE;
                                }
                            }
                        }
                        $pos_wimtv = "";
                        $showtime_identifier = "";
                        if (isset($array_st[$content_item])) {
                            $pos_wimtv = "showtime";
                            $showtime_identifier = $array_st[$content_item]["showtimeIdentifier"];
                        } else {
                            $pos_wimtv = "";
                        }

                        if (!$trovato) {
                            // Create and populate an object.
                            $insert_video = new stdClass();
                            $insert_video->uid = $username;
                            $insert_video->contentidentifier = $content_item;
                            $insert_video->mytimestamp = time();
                            $insert_video->position = 0;
                            $insert_video->state = '';
                            $insert_video->viewVideoModule = $pos_wimtv;
                            $insert_video->status = $status;
                            //$insert_video->acquiredIdentifier = $acquired_identifier;
                            $insert_video->urlThumbs = $url_thumbs; //mysql_real_escape_string($url_thumbs);
                            $insert_video->category = $categories;
                            $insert_video->urlPlay = $urlVideo; //mysql_real_escape_string($urlVideo);
                            $insert_video->title = $title; //mysql_real_escape_string($title);
                            $insert_video->duration = $duration;
                            $insert_video->showtimeidentifier = $showtime_identifier;

                            try {
                                // Insert the object into the user profile table.
                                $result = JFactory::getDbo()->insertObject('#__wimtvpro_videos', $insert_video);
                            } catch (Exception $e) {
                                throw new Exception($e);
                            }
                        } else {

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
                                "  contentidentifier = '" . $content_item . "' ");
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
                JError::raiseNotice(100,JText::_('COM_WITVPRO_ERROR_WIMTVPRO_NOVIDEOS'));
            }

            //var_dump(array_diff($elenco_video_wp ,$elenco_video_wimtv ));
            $delete_into_wp = array_diff($elenco_video_wp, $elenco_video_wimtv);

            foreach ($delete_into_wp as $value) {
                $db = JFactory::getDBO();

                $query = $db->getQuery(true);
                $query->delete($table_name);
                $query->where(array("contentidentifier = '" . $value . "'"));
                $db->setQuery($query);

                try {
                    $result = $db->query(); // $db->execute(); for Joomla 3.0.
                } catch (Exception $e) {
                    throw new Exception($e);
                }
            }
        } else {
            JError::raiseWarning(100, 'Never elements');
        }
    }

    $link = 'index.php?option=com_wimtvpro&view=' . $page;
    JFactory::getApplication()->redirect($link);
}

//MY STREAMING: This API allows to list videos in my streaming public area.
//Even details may be returned
function wimtvpro_detail_showtime($single, $st_id) {
    if (!$single) {
//        $response = apiGetShowtimes();
//        $array_detail = $response->body;
        $array_detail = apiGetShowtimes();
    } else {
        $array_detail = apiGetDetailsShowtime($st_id);
    }
    return $array_detail;
}

function getDateRange($startDate, $endDate, $format = "Y/m/d") {

    //Create output variable

    $datesArray = array();

    //Calculate number of days in the range

    $total_days = round(abs(strtotime($endDate) - strtotime($startDate)) / 86400, 0) + 1;

    if ($days < 0) {
        return false;
    }

    //Populate array of weekdays and counts

    for ($day = 0; $day < $total_days; $day++) {

        $datesArray[] = date($format, strtotime("{$startDate} + {$day} days"));
    }

    //Return results array

    return $datesArray;
}

function wimtvpro_alert_reg($username, $password, $stamp = true) {
    // NS: 
    // 
    //If user isn't register or not inser user and password
    // $params = JComponentHelper::getParams('com_wimtvpro');
    // $basePathWimtv = $params->get('wimtv_basepath');   //"http://192.168.31.198:8082/wimtv-webapp/rest/"; //
    // initApi($basePathWimtv, $username, $password);
    $jsonProfile = json_decode(apiGetProfile());

    // NS
    // if (!json_decode(apiGetProfile())->name) {
    if ($jsonProfile == null || !isset($jsonProfile->name)) {
        $ahref = '<a class="modal" href="index.php?option=com_config&amp;view=component&amp;component=com_wimtvpro&amp;path=&amp;tmpl=component" rel="{handler: \'iframe\', size: {x: 875, y: 550}, onClose: function() {}}">';
        $ahrefReg = '<a class="modal" href="index.php?option=com_wimtvpro&amp;view=register&amp;tmpl=component&amp;layout=edit" rel="{handler: \'iframe\', size: {x: 875, y: 550}, onClose: function() {}}">';
        if ($stamp) {
            JFactory::getApplication()->enqueueMessage(JText::_('COM_WIMTVPRO_CONFIG_WARNING1') . $ahrefReg . JText::_('COM_WIMTVPRO_CONFIG_REGISTER') . "</a>|" . $ahref . JText::_('COM_WIMTVPRO_CONFIG_LOGIN') . " </a>", "error");
        }
        return FALSE;
    } else {
        return TRUE;
    }
}

function wimtvpro_viever_jwplayer($userAgent, $contentId, $video, $dirJwPlayer) {

    $isiPad = (bool) strpos($userAgent, 'iPad');
    $urlPlay = explode("$$", $video[0]->urlPlay);
    $isiPhone = (bool) strpos($userAgent, 'iPhone');
    if ($isiPad || $isiPhone) {
        $urlPlayIPadIphone = "";
        $contentId = $video[0]->contentidentifier;
        $response = apiGetDetailsVideo($contentId);
        $arrayjson = json_decode($response);

        $urlPlayIPadIphone = $arrayjson->streamingUrl->streamer;
        $configFile = "'file': '" . $urlPlayIPadIphone . "',";
    } else {

        $configFile = "'flashplayer':'" . $dirJwPlayer . "','file': '" . $urlPlay[1] . "','streamer':'" . $urlPlay[0] . "',";
    }
    return $configFile;
}

function createIframePlaylist($arrayVideo, $dirJwPlayer, $user = "admin") {
    $app = &JFactory::getApplication();
    $params = JComponentHelper::getParams('com_wimtvpro');
    $username = $params->get('wimtv_username');
    $height = $params->get('wimtv_heightPreview');
    $width = $params->get('wimtv_widthPreview');

    if (!count($arrayVideo)) {
        $output = "No Videos";
    } else {
        $videoList = " AND ( 1=2";
        foreach ($arrayVideo as $value) {
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

        $playlist = "";

        foreach ($array_videos_new_wp as &$row) {
            $replace_video = apiGetThumbsVideo($row->contentidentifier);

            $query2 = $db->getQuery(true);
            $query2->select('*');
            $query2->from('#__wimtvpro_videos');
            $query2->where("contentidentifier='" . $row->contentidentifier . "'");
            $db->setQuery($query2);
            $video = $db->loadObjectList();


            $configFile = wimtvpro_viever_jwplayer($_SERVER['HTTP_USER_AGENT'], $row->contentidentifier, $video, FALSE);
            $playlist .= "{" . $configFile . " 'image':'" . $replace_video . "','title':'" . $row->title . "'},";
        }
        $output = "<div id='container_playlist" . $row->id . "'></div>";
        $playlistSize = "30%";
        $dimensions = "width: '" . $width . "',height:'" . $height . "px',";

        $output .= "<script type='text/javascript'>jwplayer('container_playlist" . $row->id . "').setup({";

        $options = array();
        $array_option = array();
        if (isset($array_playlist[0]->option)) {
            $option = $array_playlist[0]->option;
            $array_option = explode(",", $option);
            foreach ($array_option as $value) {

                $array = explode(":", $value);

                if ($array[0] != "")
                    $options[$array[0]] = $array[1];
            }
        }
        if (isset($options["loop"]) && $options["loop"] != "no") {
            $output .= "'repeat':'always',";
        }

        $isiPhone = (bool) strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone');
        $isiPad = (bool) strpos($_SERVER['HTTP_USER_AGENT'], 'iPad');
        if (!$isiPad AND !$isiPhone)
            $output .= "'flashplayer':'" . $dirJwPlayer . "',";

        if ($user == "admin") {
            $directory = JURI::base() . "components/com_wimtvpro/uploads/skin";
        } else {
            $directory = JURI::base() . "administrator/components/com_wimtvpro/uploads/skin";
        }
//        if ($skinName != "") {
//            $output .= "'skin':'" . $directory . "/" . $skinName . ".zip',";
//        }
        if (!isset($skinName) || $skinName == "") {
            $skinName ="wimtv";
        }
        $output .= "'skin':'" . $directory . "/" . $skinName . ".zip',";

        $output .= $dimensions . "'playlist': [" . $playlist . "],'playlist.position': 'right',	'playlist.size': '" . $playlistSize . "'});</script>";
    }
    return $output;
}

/*
 * NS: DATABASE SECTION
 */
define("VIDEO_TABLE_NAME", "#__wimtvpro_videos");

function dbGetUserVideosId($user, $filter = "") {
    $where = "";
    switch ($filter) {
        case "pending":
            $where = " uid='$user' AND status LIKE '%|%'";
            break;
        default:
            $where = " uid='$user'";
            break;
    }
    // Get a db connection.
    $db = JFactory::getDbo();

    // Create a new query object.
    $query = $db->getQuery(true);
    $query->select(array('contentidentifier'));
    $query->from(VIDEO_TABLE_NAME);
    $query->where($where);

    // Reset the query using our newly populated query object.
    $db->setQuery($query);
    // Load the results as a list of stdClass objects.
    $array_videos = $db->loadObjectList();
//    $array_videos = $db->loadResultArray();
    if ($error = $db->getErrorMsg()) {
        throw new Exception($error);
    }
//    foreach ($array_videos as $db_record) {
//        print $db_record->contentidentifier ."<br/>";
//    }
//    die;
    return $array_videos;
}

function dbDeleteVideo($contentIdentifier) {
    $db = JFactory::getDBO();

    $query = $db->getQuery(true);
    $query->delete(VIDEO_TABLE_NAME);
    $query->where(array("contentidentifier = '" . $contentIdentifier . "'"));
    $db->setQuery($query);

    try {
        $result = $db->query(); // $db->execute(); for Joomla 3.0.
    } catch (Exception $e) {
        throw new Exception($e);
    }
}

function dbUpdateVideo($state, $status, $title, $urlThumbs, $urlPlay, $duration, $showtimeId, $categories, $contentId, $acquired_identifier) {
    // Fields to update.
    $fields = array(
        "state = '" . $state . "'",
        " status = '" . $status . "'",
        " title = '" . mysql_real_escape_string($title) . "'",
        " urlThumbs = '" . mysql_real_escape_string($urlThumbs) . "'",
        " urlPlay = '" . mysql_real_escape_string($urlPlay) . "'",
        " duration = '" . $duration . "'",
        " acquiredIdentifier = '" . $acquired_identifier . "'",
        " showtimeidentifier = '" . $showtimeId . "'",
        " category = '" . $categories . "'"
    );

    // Conditions for which records should be updated.
    $conditions = array("  contentidentifier = '" . $contentId . "' ");
    $db = JFactory::getDbo();
    $query = $db->getQuery(true);

    $query->update($db->quoteName(VIDEO_TABLE_NAME))
            ->set($fields)
            ->where($conditions);

    $db->setQuery($query);

    try {
        $result = $db->query(); // Use $db->execute() for Joomla 3.0.
    } catch (Exception $e) {
        throw new Exception($e);
    }
}

/*
 * NS: SMART SYNC SECTION
 */

class wimtvpro_smartSync {

    static function sync($syncType) {
        $methodName = "sync_" . $syncType;
        if (method_exists(__CLASS__, $methodName)) {
            self::$methodName();
        } else {
            die("<h3>Sync method does not exist!</h3>");
        }
        $error_response = "";
    }

    static function sync_pending() {
        $params = JComponentHelper::getParams('com_wimtvpro');
        $username = $params->get('wimtv_username');
        $db_pending_video_array = dbGetUserVideosId($username, "pending");

        foreach ($db_pending_video_array as $db_record) {
            $error_response = "";
            // NS: We removed the "&" from error response to avoid problem with php 5.4
//            $api_video_detail_response = apiGetDetailsVideo($db_record->contentidentifier, &$error_response);
            $api_video_detail_response = apiGetDetailsVideo($db_record->contentidentifier, $error_response);

            // VIDEO HAS NOT YET TRANSCODED OR NOT EXISTS
            if ($api_video_detail_response == "") {
                $notReadyString = "The video is not ready yet";
//                $errorBody = isset($error_response->body) ? $error_response->body : "";                
                $errorBody = $error_response->body;
                $notReady = strstr($errorBody, $notReadyString);

                if ($notReady == false) {
                    // VIDEO NOT FOUND IN REMOTE SERVER: delete it from local cache
                    dbDeleteVideo($db_record->contentidentifier);
                } else {
                    // VIDEO IS STILL TRANSCODING
                    continue;
                }
            }
            // SYNC RECORD USING RECEIVED REMOTE INFO
            else {
                $api_video_details = $api_video_detail_response->body;
                $state = ""; // WE DO NOT SET THIS STUFF BECAUSE WE ARE JUST UPDATING NEWLY UPLOADED VIDEOS
                $status = $api_video_details->status;
                $title = $api_video_details->title;
                $url_thumbs = '<img src="' . $api_video_details->thumbnailUrl . '"  title="' . $title . '" class="wimtv-thumbnail" />';
                if (isset($api_video_details->streamingUrl)) {
                    $urlVideo = $api_video_details->streamingUrl->streamer . "$$";
                    $urlVideo .= $api_video_details->streamingUrl->file . "$$";
                    $urlVideo .= $api_video_details->streamingUrl->auth_token;
                }
                $duration = $api_video_details->duration;
                $showtime_identifier = ""; // WE DO NOT SET THIS STUFF BECAUSE WE ARE JUST UPDATING NEWLY UPLOADED VIDEOS
                $categories = "";
                $valuesc_cat_st = "";
                foreach ($api_video_details->categories as $key => $value) {
                    $valuesc_cat_st .= $value->categoryName;
                    $categories .= $valuesc_cat_st;
                    foreach ($value->subCategories as $key => $value) {
                        $categories .= " / " . $value->categoryName;
                    }
                    $categories .= "<br/>";
                }
                $content_item = $api_video_details->contentId;
                $acquired_identifier = isset($api_video_details->relationId) ? $api_video_details->relationId : "";
                dbUpdateVideo($state, $status, $title, $url_thumbs, $urlVideo, $duration, $showtime_identifier, $categories, $content_item, $acquired_identifier);
            }
        }
    }

}