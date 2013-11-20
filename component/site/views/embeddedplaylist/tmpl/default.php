<?php 
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
require_once ( JPATH_BASE . "/administrator/components/com_wimtvpro/includes/function.php" );
$dirJwPlayer = JURI::base()  . "administrator/components/com_wimtvpro/assets/js/jwplayer/player.swf";
$app = &JFactory::getApplication();
$params = JComponentHelper::getParams('com_wimtvpro');
$height = $params->get('wimtv_heightPreview');
$width = $params->get('wimtv_widthPreview');
$skinName = $params->get('wimtv_nameSkin');
$basePath = $params->get('wimtv_basepath');
$username = $params->get('wimtv_username');
$id = $_GET["id"];

$db = JFactory::getDBO();
$query = $db->getQuery(true);
$query->select('*');
$query->from('#__wimtvpro_playlist');
$query->where("uid='" . $username . "' AND id=" . $id);
	
// Reset the query using our newly populated query object.
$db->setQuery($query);
	
// Load the results as a list of stdClass objects.
$arrayPlayList = $db->loadObjectList();
if ($error = $db->getErrorMsg()) {
	throw new Exception($error);
}

$title = $arrayPlayList[0]->name;
$listVideo =  $arrayPlayList[0]->listVideo;
$arrayVideo = explode(",", $listVideo);

$output= createIframePlaylist($arrayVideo,$dirJwPlayer,"user");
	
echo $output;

?>
