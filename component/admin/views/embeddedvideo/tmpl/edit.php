<?php 
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
require_once ( JPATH_BASE . "/components/com_wimtvpro/includes/function.php" );
require_once ( JPATH_BASE . "/components/com_wimtvpro/includes/api/wimtv_api.php" );

$app = &JFactory::getApplication();
$params = JComponentHelper::getParams('com_wimtvpro');
$username = $params->get('wimtv_username');
$password = $params->get('wimtv_password');
$basePath = $params->get('wimtv_basepath');
$replaceContentWimtv = $params->get('wimtv_replaceContentWimtv');
$urlThumbsWimtv = $params->get('wimtv_urlThumbsWimtv');
$credential = $username . ":" . $password;
$coid = $_GET["coid"];
$page = $_GET["page"];
$db2 = JFactory::getDBO();
$query2 = $db2->getQuery(true);

$query2->select(array('*'));

$query2->from('#__wimtvpro_videos');
$query2->where("contentidentifier='" . $coid . "'");

// Reset the query using our newly populated query object.
$db2->setQuery($query2);

// Load the results as a list of stdClass objects.
$arrayPlay = $db2->loadObjectList();
if ($error = $db2->getErrorMsg()) {
	throw new Exception($error);
}

$params = JComponentHelper::getParams('com_wimtvpro');
$username = $params->get('wimtv_username');
$password = $params->get('wimtv_password');
$height = $params->get('wimtv_heightPreview');
$width = $params->get('wimtv_widthPreview');
$skinName = $params->get('wimtv_nameSkin');

$videos = "<div class='videos' id='" . $coid . "' style='text-align:center;width:100%'>";


if ($arrayPlay[0]->showtimeIdentifier==""){

	//Video not streaming
	$videos .= "<div id='container'></div>";
	

	$dimensions = "width: '" . $width . "px', height: '" . $height . "px',";

	$urlPlay = explode("$$", $arrayPlay[0]->urlPlay);

	if (!isset($arrayPlay[0]->urlThumbs)) $thumbs[1] = "";
	else $thumbs = explode ('"',$arrayPlay[0]->urlThumbs);
	$thumbs = str_replace('\\','',$thumbs);
	$dirJwPlayer = JURI::base() . "components/com_wimtvpro/assets/js/jwplayer/player.swf";

	$configFile  = wimtvpro_viever_jwplayer($_SERVER['HTTP_USER_AGENT'],$coid,$arrayPlay,$dirJwPlayer);

	$videos .= "<script type='text/javascript'>jwplayer('container').setup({";
	$directory  = JURI::base() . "components/com_wimtvpro/uploads/skin";
	if ($skinName!="") {
		$skin = "'skin':'" . $directory  . "/" . $skinName . ".zip',";
	}




	$videos .= $skin . $dimensions . $configFile . " image: '" . $thumbs[1] . "',
});</script>";

} else
{


	//Video in Streaming

	$contentItem = $coid ;
	$streamItem = $arrayPlay[0]->showtimeIdentifier;
	$jSonST = wimtvpro_detail_showtime(true, $streamItem);
	$arrayjSonST = json_decode($jSonST);
	$arrayST["showtimeIdentifier"] = $arrayjSonST->{"showtimeIdentifier"};
	$arrayST["duration"] = $arrayjSonST->{"duration"};
	$arrayST["categories"] = $arrayjSonST->{"categories"};
	$arrayST["description"] = $arrayjSonST->{"description"};
	$arrayST["thumbnailUrl"] = $arrayjSonST->{"thumbnailUrl"};
	$arrayST["contentId"] = $arrayjSonST->{"contentId"};
	$arrayST["url"] = $arrayjSonST->{"url"};
	
	if ($skinName!="") {
		$directory  = JURI::base() . "components/com_wimtvpro/uploads/skin";
		$skin = "&skin=" . $directory  . "/" . $skinName . ".zip";
	}
	$params = "get=1&width=" . $width . "px&height=" . $height . "px" .  $skin;
	$response = apiGetPlayerShowtime($coid, $params);

	$videos .= $response;
	
	$videos .= "<p>" . $arrayST["description"] . "</p>";
	$videos .= "<p>Duration: <b>" . $arrayST["duration"] . "</b>";
	if (count($arrayST["categories"])>0){
		$videos .= "<br/>Categories<br/>";
		foreach ($arrayST["categories"] as $key => $value) {
			$valuescCatST = "<i>" . $value->categoryName . ":</i> ";
			$videos .= $valuescCatST;
			foreach ($value->subCategories as $key => $value) {
				$videos .= $value->categoryName . ", ";
			}
			$videos .= substr($output, 0, -2);
			$videos .= "<br/>";
		}
		$videos .= "</p>";
	}
}

$videos .= "</div>";

?>

<form enctype="multipart/form-data"
	action="<?php echo JRoute::_('index.php?option=com_wimtvpro&view=' . $page ); ?>"
	method="post" name="adminForm" id="wimtvpro-form">
	<fieldset class="adminform">
		<legend>
			<?php echo  $arrayPlay[0]->title; ?>
		</legend>

		<?php echo $videos;?>
        
        <?php 
		
		$embedded= str_replace('<?xml version="1.0" encoding="UTF-8"?>','',$response);

		
		if ($arrayST["showtimeIdentifier"]!="")
			echo "<p>Embedded:</p><textarea style='resize: none; width:90%;height:70px;font-size:10px' readonly='readonly' onclick='this.focus(); this.select();'>" . htmlentities($embedded) . "</textarea>"; 
		?>
        
	</fieldset>

	<input type="hidden" name="task" value="<?php echo $page; ?>.edit" />
    <input type="hidden" name="coid" value="<?php echo $coid; ?>">
	<?php echo JHtml::_('form.token'); ?>

</form>
