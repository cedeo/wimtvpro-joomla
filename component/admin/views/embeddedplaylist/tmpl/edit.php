<?php 
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
require_once ( JPATH_BASE . "/components/com_wimtvpro/includes/function.php" );
require_once ( JPATH_BASE . "/components/com_wimtvpro/includes/api/wimtv_api.php" );

$dirJwPlayer = JURI::base()  . "/components/com_wimtvpro/assets/js/jwplayer/player.swf";
$app = &JFactory::getApplication();
$params = JComponentHelper::getParams('com_wimtvpro');
$basePath = $params->get('wimtv_basepath');
$height = $params->get('wimtv_heightPreview');
$width = $params->get('wimtv_widthPreview');
$skinName = $params->get('wimtv_nameSkin');
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
if (count($arrayVideo) == 1 && !$arrayVideo[0]) $arrayVideo = array();
$output= createIframePlaylist($arrayVideo,$dirJwPlayer);
	


?>

<style type="text/css">
    div[id^="container_playlist"] {
        display: inline-block;
    }
</style>
<form enctype="multipart/form-data"
	action="<?php echo JRoute::_('index.php?option=com_wimtvpro&view=' . $page ); ?>" 
	method="post" name="adminForm" id="wimtvpro-form">
	<fieldset class="adminform">
		<legend>
			<?php echo  $title; ?>
		</legend>
		<div style="text-align: center">
            <?php echo $output;?>
		</div>
		
		<?php echo "<p>Embedded:</p><textarea style='resize: none; width:90%;height:70px;font-size:10px' readonly='readonly' onclick='this.focus(); this.select();'>" . htmlentities($output) . "</textarea>"; ?>

	</fieldset>

	<input type="hidden" name="task" value="mystreamings.edit" />

	<input type="hidden" name="id" value="<?php echo $id; ?>">
		<?php echo JHtml::_('form.token'); ?>

</form>