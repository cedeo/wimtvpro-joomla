<?php 
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
require_once ( JPATH_BASE . "/components/com_wimtvpro/includes/function.php" );

$coid = $_GET["coid"];
$type = $_GET["put"];

$db2 = JFactory::getDBO();
$query2 = $db2->getQuery(true);
$app = &JFactory::getApplication();
$params = JComponentHelper::getParams('com_wimtvpro');
$height = $params->get('wimtv_heightPreview');
$width = $params->get('wimtv_widthPreview');
$skinName = $params->get('wimtv_nameSkin');

$query2->select(array('title','urlPlay', 'urlThumbs', 'contentidentifier'));

$query2->from('#__wimtvpro_videos');
$query2->where("contentidentifier='" . $coid . "'");

// Reset the query using our newly populated query object.
$db2->setQuery($query2);
	
// Load the results as a list of stdClass objects.
$arrayPlay = $db2->loadObjectList(); 
if ($error = $db2->getErrorMsg()) {
	throw new Exception($error);
}



$videos = "<div class='videos' id='" . $coid . "' style='text-align:center;float:left;'>";
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

$videos .= "</div>";

?>

<form enctype="multipart/form-data"
	action="<?php echo JRoute::_('index.php?option=com_wimtvpro&view=putVideos&layout=edit&put=' . $type . '&coid=' . $coid); ?>" 
	method="post" name="adminForm" id="wimtvpro-form">
	<fieldset class="adminform">
		<legend>
			<?php echo  $arrayPlay[0]->title; ?>
		</legend>
		
		<?php echo $videos;?>
		
		<ul style="float:left; margin:0 0 0 10px;" class="adminformlist">
			
			

<?php 
switch ($type) {
	
	case "cc":
		echo "<h1>CREATIVE COMMONS</h1>";
		$option[] = JHTML::_( 'select.option', 'BY_NC_SA', '<p class="cc_set"><img  src="http://www.wim.tv/wimtv-webapp/images/cclicense/Attribution Non-commercial No Derivatives.png" 	title="Attribution Non-Commercial No Derivatives"/> Attribution Non-Commercial No Derivatives</p>' );
		$option[] = JHTML::_( 'select.option', 'BY_NC_ND','<p class="cc_set"><img src="http://www.wim.tv/wimtv-webapp/images/cclicense/Attribution Non-commercial Share Alike.png" 	title="Attribution Non-Commercial Share Alike" /> Attribution Non-Commercial Share Alike</p>' );
		$option[] = JHTML::_( 'select.option', 'BY_NC','<p class="cc_set"><img src="http://www.wim.tv/wimtv-webapp/images/cclicense/Attribution Non-commercial.png" 			title="Attribution Non-Commercial" /> Attribution Non-Commercial</p>' );
		$option[] = JHTML::_( 'select.option', 'BY_ND','<p class="cc_set"><img src="http://www.wim.tv/wimtv-webapp/images/cclicense/Attribution No Derivatives.png" 			title="Attribution No Derivatives" /> Attribution No Derivatives</p>' );
		$option[] = JHTML::_( 'select.option', 'BY_SA','<p class="cc_set"><img src="http://www.wim.tv/wimtv-webapp/images/cclicense/Attribution Share Alike.png" 				title"Attribution Share Alike"/> Attribution Share Alike</p>' );
		$option[] = JHTML::_( 'select.option', 'BY','<p class="cc_set"><img src="http://www.wim.tv/wimtv-webapp/images/cclicense/Attribution.png" 						title="Attribution" /> Attribution</p>' );
		
		echo "<li style='margin:0 0 10px 0;'>" . JHTML::_('select.radiolist', $option, 'cc_type', array('style'=>'display:none')) . "</li>";
		
	break;
	
	case "ppv":
		echo "<h1>PAY PER VIEW</h1>";
		echo '<p style="margin:0 0 10px 0;"><input type="text" name="amount" class="amount" value="00" />.<input type="text" name="amount_cent" class="amount_cent" value="00" maxlength="2"/>';
		echo 'Euro<input type="hidden" name="currency" class="currency" value="EUR"></p>';

	break;

	case "free":
		echo "<h1>FREE OF CHARGE</h1>";
		echo "<h2>Do you want your video to be visible to all for free?</h2>";
		
	break;
	
}


?>


			
		</ul>
	</fieldset>

	<input type="hidden" name="task" value="mymedias.edit" />
	<input type="hidden" name="type" value="<?php echo $type; ?>">
	<input type="hidden" name="coid" value="<?php echo $coid; ?>">
		<?php echo JHtml::_('form.token'); ?>

</form>