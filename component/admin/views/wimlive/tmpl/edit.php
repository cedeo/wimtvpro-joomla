<?php 
// No direct access
defined('_JEXEC') or die('Restricted access');
require_once ( JPATH_BASE . "/components/com_wimtvpro/includes/api/wimtv_api.php" );

JHtml::_('behavior.tooltip');

$id = $_GET["cid"];
$timezone = $_GET["timezone"];
$session = JFactory::getSession();
$session->set('timezone', $timezone);

echo '
<script>
var url_pathPlugin = "' . JURI::base() . 'components/com_wimtvpro/' . '";

jQuery(document).ready(function(){
	var timezone = -(new Date().getTimezoneOffset())*60*1000;
	//console.log(timezone);
	if (!"' . $timezone . '".length) {
        window.location.href = window.location.href + "&timezone=" + timezone;
    }
	jQuery(".timelivejs").val(timezone);
});
</script>';



if ($id) {
    $embedded = apiEmbeddedLive($id, $timezone); //curl_exec($ch_embedded);
    $arrayjson_live = json_decode($embedded);
} else {
    $arrayjson_live = array();
}


?>
<form enctype="multipart/form-data"
	action="<?php echo JRoute::_('index.php?option=com_wimtvpro&view=wimlives&layout=edit&cid='. $_GET["cid"]); ?>"
	method="post" name="adminForm" id="wimtvpro-form">
	<fieldset class="adminform">
		<legend>
			<?php echo JText::_( 'COM_WIMTVPRO_TITLE_LIVE' ); ?>
		</legend>
		<ul class="adminformlist">
			<?php foreach ($this->form->getFieldset() as $field): ?>
			    <li><?php echo $field->label;echo $field->input;?></li>
			<?php endforeach; ?>
		</ul>
	</fieldset>
	<div>

	
	<input type="hidden" name="task" value="wimlive.edit" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
