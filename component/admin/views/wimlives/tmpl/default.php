<?php 
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
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
        //console.log("' . $urllive . '");
        jQuery.ajax({
                context: this,
                url:  "' . $urllive  . '",
                type: "POST",
                dataType: "html",
                async: false,
                data: "type=table&timezone =" + timezone  + "&id=all&onlyActive=true&username=' . $username . '&password=' . $password . '&basePath=' . $basePath . '",
                success: function(response) {
                    //console.log(response);
                    jQuery(".live_table tbody").html(response);
                },

        });
    });
</script>';
		
?>


<form action="<?php echo JRoute::_('index.php?option=com_wimtvpro&view=wimlives'); ?>" method="post" name="adminForm">

	<p><?php echo JText::_( 'COM_WIMTVPRO_LIVE_DESC1' ); ?></p>
    <p><?php echo JText::_( 'COM_WIMTVPRO_LIVE_DESC2' ); ?></p>
    
   
    <ol>
        <li><p><?php echo JText::_( 'COM_WIMTVPRO_LIVE_DESC3_OL1' ); ?></p></li>
        <li><p><?php echo JText::_( 'COM_WIMTVPRO_LIVE_DESC3_OL2' ); ?></p></li>
    </ol>

	<p><?php echo JText::_( 'COM_WIMTVPRO_LIVE_SCHEDULE_DESC' ); ?></p>
	
	<div id="editcell">
		<table class="adminlist live_table" id="tableLive" >
			<thead>
				<tr>
					
					<th></th>
					<th><?php echo JText::_( 'COM_WIMTVPRO_LIVE_TITLE' ); ?></th>
					<th><?php echo JText::_( 'COM_WIMTVPRO_LIVE_NOW' ); ?></th>
					<th><?php echo JText::_( 'COM_WIMTVPRO_LIVE_PPV' ); ?></th>
					<th><?php echo JText::_( 'COM_WIMTVPRO_LIVE_URL' ); ?></th>
					<th><?php echo JText::_( 'COM_WIMTVPRO_LIVE_SCHEDULE' ); ?></th>
					<th><?php echo JText::_( 'COM_WIMTVPRO_LIVE_EMBED' ); ?></th>
					<!--th><?php echo JText::_( 'COM_WIMTVPRO_LIVE_ACTION' ); ?></th-->
					
					
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

