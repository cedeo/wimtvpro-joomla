<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
// load tooltip behavior

require_once JPATH_ROOT . '/components/com_content/helpers/route.php';

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
$function	= JRequest::getCmd('function', 'jSelectArticle');
JHtml::_('behavior.tooltip');
$app = &JFactory::getApplication();
$params = JComponentHelper::getParams('com_wimtvpro');
$username = $params->get('wimtv_username');
$password = $params->get('wimtv_password');
$height = $params->get('wimtv_heightPreview');
$width = $params->get('wimtv_widthPreview');
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
$dirJwPlayer = JURI::base()  . "/components/com_wimtvpro/assets/js/jwplayer/player.swf";
$view_page = wimtvpro_alert_reg($username,$password);
if ($view_page){
	

	
	
?>
<form action="<?php echo JRoute::_('index.php?option=com_wimtvpro&view=mystreamings'); ?>"
	method="post" name="adminForm">
	
	<div class="videosElenco">
		
		
		<?php if (isset($_GET["inserInto"])) { echo  "<h1>Video</h1>"; }?>
		
		<table class="adminlist wimtvpro">
			<thead>
				<?php echo $this->loadTemplate('head');?>
			</thead>
			<tfoot>
				<?php echo $this->loadTemplate('foot');?>
			</tfoot>
			<tbody>
				<?php echo $this->loadTemplate('body');?>
			</tbody>
		</table>
	
	</div>
	
	<div class="playlistElenco">
		<h1>PlayList Free</h1>
		<?php if (!isset($_GET["inserInto"])) {?>
			
		    <p>Create a playlist of videos (ONLY FREE) to be<br/>inserted within your website</p>
			
			<?php //My Playlist ?>
			<p><a href="index.php?option=com_wimtvpro&view=myplaylist&layout=edit">Create new Playlist</a></p>
		
		<?php 
		} 
			$app = &JFactory::getApplication();
			$params = JComponentHelper::getParams('com_wimtvpro');
			$username = $params->get('wimtv_username');
			
			$db = JFactory::getDBO();
			$query = $db->getQuery(true);
			$query->select('*');
			$query->from('#__wimtvpro_playlist');
			$query->where("uid='" . $username . "'");
			
			// Reset the query using our newly populated query object.
			$db->setQuery($query);
			
			// Load the results as a list of stdClass objects.
			$arrayPlayList = $db->loadObjectList();
			if ($error = $db->getErrorMsg()) {
				throw new Exception($error);
			}	
			
			//var_dump ($arrayPlayList);
			
	
			$count = 1;
			
			foreach($arrayPlayList as $field){
					$listVideo = $field->listVideo;
					$arrayVideo = explode(",", $listVideo);
					if ($listVideo=="") $countVideo = 0;
					else $countVideo = count($arrayVideo);
					
					if (!isset($_GET["inserInto"])) {
						echo '<div class="playlist" id="playlist_' . $count . '" rel="' . $field->id . '"><span class="icon_selectPlay"></span>' . $field->name .  '(<span class="counter">' . $countVideo . '</span>)<span class="icon_deletePlay"></span><span class="icon_modTitlePlay"></span>';
						echo '<a href="' .  JURI::base() . '?option=com_wimtvpro&view=embeddedplaylist&layout=edit&id=' . $field->id . '"><span class="icon_viewPlay"></span></a>';
					} else 
					{
						echo '<div class="playlist" id="playlist_' . $count . '" rel="' . $field->id . '">' . $field->name .  '(<span class="counter">' . $countVideo . '</span>)';	
					
						$iframeInsert = urlencode("<iframe src='index.php?option=com_wimtvpro&view=embeddedplaylist&tmpl=component&id=" . $field->id . "' width='100%' height='" . ($height + 70) . "px' frameborder='0' style='overflow:none;'></iframe>");
										
						echo '<br/><a href="#" onclick="var iframe = unescape(\'' . $iframeInsert . '\'); iframe = iframe.replace(/\+/g, \' \');   window.parent.jInsertEditorText(iframe,\'' . $_GET["e_name"] . '\');window.parent.SqueezeBox.close();">' . JText::_('COM_MEDIA_INSERT') . '</a>';
					}
					
					echo '</div>';
					$count +=1;


			}

			
		?>
		
	
	</div>
	
	<div>
		<input type="hidden" name="task" value="" /> <input type="hidden"
			name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $this->sortColumn; ?>" />
        <input type="hidden" name="filter_order_Dir" value="<?php echo $this->sortDirection; ?>" />

			
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>


<?php } ?>