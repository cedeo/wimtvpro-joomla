<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
?>
<?php
$k = 0;
$i = 0;

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
$details_st  =curl_exec($ch_st);
$arrayjSonST = json_decode( $details_st);

$stLicense = array();
foreach ($arrayjSonST->items as $st){
	$stLicense[$st->showtimeIdentifier] = $st->licenseType;
}


foreach ($this->items as &$row)
{
	$checked = JHTML::_('grid.id', $i++, $row->id );
	
	$param_thumb = $basePath . str_replace($replaceContentWimtv, $row->contentidentifier, $urlThumbsWimtv);
	$ch_thumb = curl_init();
	curl_setopt($ch_thumb, CURLOPT_URL, $param_thumb);
	curl_setopt($ch_thumb, CURLOPT_VERBOSE, 0);
	curl_setopt($ch_thumb, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch_thumb, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	curl_setopt($ch_thumb, CURLOPT_USERPWD, $credential);
	curl_setopt($ch_thumb, CURLOPT_SSL_VERIFYPEER, FALSE);
	$replace_video  =curl_exec($ch_thumb);
	
	$isfound = false;
	if (!strstr($replace_video, 'Not Found'))
		$isfound = true;
	
	if ( $row->showtimeIdentifier!=""){
		$licenseType = $stLicense[$row->showtimeIdentifier];
		$action = '<div class="putVideo"><a class="jgrid" href="javascript:void(0);"  title="' . JText::_('COM_WIMTVPRO_STATE_REMOVE') . '"><span class="state publish icon_RemoveshowtimeInto" id="' . $row->contentidentifier . '|' . $row->showtimeIdentifier . '" rel="mymedias"></span></a>';
		
		
	}
	else {
		$action = '<a class="jgrid" href="javascript:void(0);" title="' . JText::_('COM_WIMTVPRO_STATE_ADD') . '"><span class="state unpublish icon_Putshowtime" id="' . $row->contentidentifier . '"></span></a>';
		$form_st = '
		<div class="free">FREE OF CHARGE</div>
		<div class="cc">CREATIVE COMMONS</div>
		<div class="ppv">PAY PER VIEW</div>
   	 	';
		$action .= "<div class='formVideo'>" . $form_st . "</div>";
		$licenseType = "";
	}
	
	$wimtvpro_url = "index.php?option=com_wimtvpro&layout=edit&view=embeddedvideo&page=mymedia&&coid=" . $row->contentidentifier;
	$replace_video = '<div class="thumb"><img src="' . $replace_video . '" title="' . $title . '" class="" />';
	$replace_video  = "<a class='wimtv-thumbnail' href='" . $wimtvpro_url . "'>" . $replace_video . "</a>";
	
	if ($licenseType!="") $replace_video .= '<div class="icon_licence ' . $licenseType . '"></div>';
	$replace_video  .= '</div>';
	if ( $row->showtimeIdentifier==""){
		$checked = JHTML::_('grid.id', $i++, $row->contentidentifier );
	} else {
		$checked = "";
	}
	
	if ($isfound) {
	?>
    <tr class="row<?php echo $k?>">
    	<td><?php echo $checked;?></td>
		<td><?php echo $replace_video;?></td>
		<td><?php echo $row->title;?></td>

		<td><?php //echo JHtml::_('jgrid.published',  $state, $row->showtimeIdentifier, 'myplaylist.', 'cb', $item->publish_up, $item->publish_down); ?>
	
		<?php echo $action;?>

	
	
		</td>
		<td>
			<a class="jgrid" href="javascript:void(0);" title="Download">
				<span class="icon-32-download" id="<?php echo $row->contentidentifier . '|' . $status_array[1];?>" rel="<?php echo $basePath;?>">
					<span class="text">Download</span>
				</span>
			</a>
		</td>

	</tr>
<?php
    $k = 1 - $k;
  }

}
?>


