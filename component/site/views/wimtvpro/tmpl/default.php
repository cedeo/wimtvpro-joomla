<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
$mydoc = &JFactory::getDocument();

$mytitle = $mydoc->getTitle();
?>
<h1><?php echo $mytitle; ?></h1>



<?php
require_once ( JPATH_BASE . "/administrator/components/com_wimtvpro/includes/function.php" );
$dirJwPlayer = JURI::base() . "administrator/components/com_wimtvpro/assets/js/jwplayer/player.swf";
$app = &JFactory::getApplication();
$params = JComponentHelper::getParams('com_wimtvpro');
$username = $params->get('wimtv_username');
$password = $params->get('wimtv_password');
$basePath = $params->get('wimtv_basepath');
$replaceContentWimtv = $params->get('wimtv_replaceContentWimtv');
$urlThumbsWimtv = $params->get('wimtv_urlThumbsWimtv');
$credential = $username . ":" . $password;

//Select Showtime
$param_st = $basePath . "users/" . $username . "/showtime?details=true";
$ch_st = curl_init();
curl_setopt($ch_st, CURLOPT_URL, $param_st);
curl_setopt($ch_st, CURLOPT_VERBOSE, 0);
curl_setopt($ch_st, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch_st, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($ch_st, CURLOPT_USERPWD, $credential);
curl_setopt($ch_st, CURLOPT_SSL_VERIFYPEER, FALSE);
$details_st = curl_exec($ch_st);
$arrayjSonST = json_decode($details_st);

$stLicense = array();
foreach ($arrayjSonST->items as $st) {
    $stLicense[$st->showtimeIdentifier] = $st->licenseType;
}

$db = JFactory::getDBO();
$query = $db->getQuery(true);
$query->select('*');
$query->from('#__wimtvpro_videos');
$query->order("position ASC");
$query->where("uid='" . $username . "' AND showtimeIdentifier!=''");
$db->setQuery($query);
$array_videos_new_wp = $db->loadObjectList();

foreach ($array_videos_new_wp as &$row) {

    $param_thumb = $basePath . str_replace($replaceContentWimtv, $row->contentidentifier, $urlThumbsWimtv);
    $ch_thumb = curl_init();
    curl_setopt($ch_thumb, CURLOPT_URL, $param_thumb);
    curl_setopt($ch_thumb, CURLOPT_VERBOSE, 0);
    curl_setopt($ch_thumb, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch_thumb, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch_thumb, CURLOPT_USERPWD, $credential);
    curl_setopt($ch_thumb, CURLOPT_SSL_VERIFYPEER, FALSE);
    $replace_video = curl_exec($ch_thumb);

    $query2 = $db->getQuery(true);
    $query2->select('*');
    $query2->from('#__wimtvpro_videos');
    $query2->where("contentidentifier='" . $row->contentidentifier . "'");
    $db->setQuery($query2);
    $video = $db->loadObjectList();


    $configFile = wimtvpro_viever_jwplayer($_SERVER['HTTP_USER_AGENT'], $row->contentidentifier, $video, FALSE);
    $playlist .= "{" . $configFile . " 'image':'" . $replace_video . "','title':'" . $row->title . "'},";

    /* $wimtvpro_url = "index.php?option=com_wimtvpro&layout=edit&view=embeddedvideo&page=mystreamings&coid=" . $row->contentidentifier;

      $replace_video = '<div class="thumb"><img src="' . $replace_video . '" title="' . $title . '" style="width: 200px; height:140px; " />';
      $replace_video  = "<a class='wimtv-thumbnail' href='" . $wimtvpro_url . "'>" . $replace_video . "</a>";
     */


    //$dimensions = "width:" . variable_get('widthPreview') . ",height:" . variable_get('heightPreview')-10;

    $output .= "<div id='container_playlist'></div>";
    $playlistSize = "30%";
    $dimensions = "width: '100%',";

    $output .= "<script type='text/javascript'>jwplayer('container_playlist').setup({";

    //if (variable_get('nameSkin')!="") $output .= "skin: '" . $directory . "/" . variable_get('nameSkin') . ".zip',";

    $option = $array_playlist[0]->option;
    $array_option = explode(",", $option);
    $options = array();
    foreach ($array_option as $value) {

        $array = explode(":", $value);

        if ($array[0] != "")
            $options[$array[0]] = $array[1];
    }
    if ($options["loop"] != "no")
        $output .= "'repeat':'always',";

    $isiPhone = (bool) strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone');
    $isiPad = (bool) strpos($_SERVER['HTTP_USER_AGENT'], 'iPad');
    if (!$isiPad AND !$isiPhone)
        $output .= "'flashplayer':'" . $dirJwPlayer . "',";
    //if (variable_get('nameSkin')!="") $output .= "skin: '" . $directory . "/" . variable_get('nameSkin') . ".zip',";
    //else $output .= "skin: '" . $directory . "/skin/default.zip',";

    $output .= $dimensions . "'playlist': [" . $playlist . "],'playlist.position': 'right',	'playlist.size': '" . $playlistSize . "'});</script>&nbsp;";


    echo $output;
}
?>