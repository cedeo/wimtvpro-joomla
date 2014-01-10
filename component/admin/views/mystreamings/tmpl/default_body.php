<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
jimport( 'joomla.html.pagination' );
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
require_once ( JPATH_BASE . "/components/com_wimtvpro/includes/api/wimtv_api.php" );

$k = 0;
$i = 0;

$user		= JFactory::getUser();

$app = &JFactory::getApplication();
$params = JComponentHelper::getParams('com_wimtvpro');
$username = $params->get('wimtv_username');
$password = $params->get('wimtv_password');
$basePath = $params->get('wimtv_basepath');
$replaceContentWimtv = $params->get('wimtv_replaceContentWimtv');
$urlThumbsWimtv = $params->get('wimtv_urlThumbsWimtv');
$credential = $username . ":" . $password;
$height = $params->get('wimtv_heightPreview');
$width = $params->get('wimtv_widthPreview');
$skinName = $params->get('wimtv_nameSkin');


//Select Showtime

$details_st = apiGetShowtimes();
$arrayjSonST = json_decode( $details_st);

$stLicense = array();
foreach ($arrayjSonST->items as $st){
	$stLicense[$st->showtimeIdentifier] = $st->licenseType;
}

$originalOrders = array();

foreach ($this->items as $i => $row)
{
	
	$checked = JHTML::_('grid.id', $i++, $row->id );
	
	$canChange = true;
	$saveOrder = true;
	
	$param_thumb = $basePath . str_replace($replaceContentWimtv, $row->contentidentifier, $urlThumbsWimtv);
	$ch_thumb = curl_init();
	curl_setopt($ch_thumb, CURLOPT_URL, $param_thumb);
	curl_setopt($ch_thumb, CURLOPT_VERBOSE, 0);
	curl_setopt($ch_thumb, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch_thumb, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	curl_setopt($ch_thumb, CURLOPT_USERPWD, $credential);
	curl_setopt($ch_thumb, CURLOPT_SSL_VERIFYPEER, FALSE);
	$replace_video = curl_exec($ch_thumb);
	if ( $row->showtimeIdentifier!=""){
		$licenseType = $stLicense[$row->showtimeIdentifier];
		
		$action = '<a class="jgrid" href="javascript:void(0);"  title="' . JText::_('COM_WIMTVPRO_STATE_REMOVE') . '"><span class="state publish icon_RemoveshowtimeInto" id="' . $row->contentidentifier . '|' . $row->showtimeIdentifier . '" rel="mystreamings"></span></a>';
	}
	$wimtvpro_url = "index.php?option=com_wimtvpro&layout=edit&view=embeddedvideo&page=mystreamings&coid=" . $row->contentidentifier;
	
	$replace_video = '<div class="thumb"><img src="' . $replace_video . '" title="' . $title . '" style="width: 200px; height:140px; " />';
	
	if (!isset($_GET["inserInto"])) {
		$replace_video  = "<a class='wimtv-thumbnail' href='" . $wimtvpro_url . "'>" . $replace_video . "</a>";
	}
	if ($licenseType!="") $replace_video .= '<div class="icon_licence ' . $licenseType . '"></div>';
	$replace_video  .= '</div>';
	

	
	?>
	
	
<tr class="row<?php echo $k?>">

		<td style="display:none;" class="center">
		<?php echo JHtml::_('grid.id', $k, $item->id); ?>
		</td>

    <?php
    
    	if ($row->state=="") $state = 0;
    	else $state = 1;
    	
    	$status_array = explode("|",$row -> status);
    
    ?>
    
    <?php if (!isset($_GET["inserInto"])) {
    
    	$ordering = true;
    	
    	if ($row->position==0)
    		$orderkey++ ;
    	else
    		$orderkey = $row->position;
    	
    	
    	?>
		<td class='order'>

		
		<?php if ($canChange) : ?>
			<?php if ($saveOrder) : ?>
					<span><?php echo $this->pagination->orderUpIcon($k, $row->contentidentifier == @$this->items[$k-1]->contentidentifier, 'mystreamings.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
					<span><?php echo $this->pagination->orderDownIcon($k, $this->pagination->total, $row->contentidentifier == @$this->items[$k+1]->contentidentifier, 'mystreamings.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
				<?php endif; ?>
				<?php $disabled = $saveOrder ?  '' : 'disabled="disabled"'; ?>
				
				<input type="text" name="order[<?php echo $row->contentidentifier;?>]" size="5" value="<?php echo $orderkey;?>" <?php echo $disabled ?> class="text-area-order" />
				<?php $originalOrders[] = $orderkey; ?>
			
			<?php else : ?>
				<?php echo $orderkey;?>
		<?php endif; ?>
	</td>
	<?php } ?>
	<td><?php echo $replace_video;?></td>
	<td><?php echo $row->title;?></td>
	
	
	<?php if (!isset($_GET["inserInto"])) {?>
		<td><?php  echo $action;	?></td>
		<td>
			<a class="jgrid" href="javascript:void(0);"  title="Download">
				<span class="icon-32-download" id="<?php echo $row->contentidentifier . '|' . $status_array[1];?>" rel="<?php echo $basePath;?>">
					<span class="text">Download</span>
				</span>
			</a>
		</td>
		
		<td class="center">
		<span class='icon_playlist' rel='<?php echo $row->showtimeIdentifier; ?>' title='Add to Playlist selected'></span>
		
		</td>
		
	<?php } ?>
	
	<?php if (isset($_GET["inserInto"])) {
		$id =  $row->contentidentifier;

		
		?>
		<td>
			W  <input type="text" value="<?php echo $width?>" id="width_<?php echo $row->contentidentifier; ?>"> px
			<br/>H  <input type="text" value="<?php echo $height?>"  id="height_<?php echo $row->contentidentifier; ?>"> px <br/>
			<input type='hidden' id='iframe_<?php echo $row->contentidentifier; ?>' value='<?php echo $iframeInsert;?>' />
			<a href="#" id="<?php echo $row->contentidentifier; ?>" class="iframeClicked" onclick=""><?php echo JText::_('COM_MEDIA_INSERT') ?></a>
		</td>
	<?php } ?>

</tr>
<?php
$k++;
}

?>
<input type="hidden" name="original_order_values" value="<?php echo implode($originalOrders, ','); ?>" />