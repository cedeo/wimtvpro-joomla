<?php 
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
$mainframe =& JFactory::getApplication();

$app = &JFactory::getApplication();
$params = JComponentHelper::getParams('com_wimtvpro');
$username = $params->get('wimtv_username');
$password = $params->get('wimtv_password');
$basePath = $params->get('wimtv_basepath');
$replaceContentWimtv = $params->get('wimtv_replaceContentWimtv');
$urlThumbsWimtv = $params->get('wimtv_urlThumbsWimtv');
$credential = $username . ":" . $password;

$urllive =  "components/com_wimtvpro/includes/live.php";
echo '	
<script>
    var timezone = -(new Date().getTimezoneOffset())*60*1000;
jQuery(document).ready(function(){ 
	jQuery.ajax({
			context: this,
			url:  "' . $urllive  . '", 		      
			type: "POST",
			dataType: "html",
			async: false,
			data: "type=table&timezone =" + timezone  + "&id=all&onlyActive=true&username=' . $username . '&password=' . $password . '&basePath=' . $basePath . '",  
			success: function(response) {

				jQuery(".live_table tbody").html(response);
			},
				
	});
});			
</script>';
		
?>


<form action="<?php echo JRoute::_('index.php?option=com_wimtvpro&view=wimlives'); ?>" method="post" name="adminForm">


	<p>Here you can create live streaming events to be published on the pages of the site.</p>
	<p>To use this service you must have installed on your pc a video encoding software (e.g. Adobe Flash Media Live Encoder, Wirecast etc.) or you can broadcast directly from your webcam, simply clicking the icon below under the "Live now" column.</p>
	<p>By clicking the icon, the producer will open in a new browser tab, keep it open during the whole transmission.</p>
	
	
	<div id="editcell">
		<table class="adminlist live_table" id="tableLive" >
			<thead>
				<tr>
					
					<th></th>
					<th>Name</th>
					<th>Live Now</th>
					<th>Pay-Per-View</th>
					<th>URL	Streaming</th>
					<th>Date</th>
					<th>Embed Code</th>
					<th>Action</th>
					
					
				</tr>
			</thead>
			
			<tbody>
				
			</tbody>
		</table>
	</div>
	
	<div>
		<input type="hidden" name="task" value="" /> 
		<input type="hidden" name="boxchecked" value="0" />

			
		<?php echo JHtml::_('form.token'); ?>
	</div>
	
</form>

