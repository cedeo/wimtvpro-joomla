<?php 
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');

echo '
<script>
    
jQuery(document).ready(function(){
	var timezone = -(new Date().getTimezoneOffset())*60*1000;
	jQuery(".timelivejs").val(timezone);	
});
</script>';

$app = &JFactory::getApplication();
$params = JComponentHelper::getParams('com_wimtvpro');
$basePathWimtv = $params->get('wimtv_basepath');
$username = $params->get('wimtv_username');
$password = $params->get('wimtv_password');
$credential = $username . ":" . $password;

$userpeer = $username;


$id = $_GET["cid"];

$url_live_embedded = $basePathWimtv . "liveStream/" . $userpeer . "/" . $userpeer . "/hosts/" . $id;

$ch_embedded= curl_init();

curl_setopt($ch_embedded, CURLOPT_URL, $url_live_embedded);
curl_setopt($ch_embedded, CURLOPT_VERBOSE, 0);

curl_setopt($ch_embedded, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch_embedded, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($ch_embedded, CURLOPT_USERPWD, $credential);
curl_setopt($ch_embedded, CURLOPT_SSL_VERIFYPEER, FALSE);
$embedded= curl_exec($ch_embedded);
$arrayjson_live = json_decode($embedded);


?>
<form enctype="multipart/form-data"
	action="<?php echo JRoute::_('index.php?option=com_wimtvpro&view=wimlives&layout=edit&cid='. $_GET["cid"]); ?>"
	method="post" name="adminForm" id="wimtvpro-form">
	<fieldset class="adminform">
		<legend>
			<?php echo JText::_( 'COM_WIMTVPRO_TITLE_LIVE' ); ?>
		</legend>
		<ul class="adminformlist">
			<?php foreach($this->form->getFieldset() as $field): ?>
			
		
			
			<li><?php echo $field->label;echo $field->input;?></li>
			<?php endforeach; ?>
		</ul>
	</fieldset>
	<div>

	
	<input type="hidden" name="task" value="wimlive.edit" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>

<?php 




?>