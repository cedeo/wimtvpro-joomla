<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
// load tooltip behavior
JHtml::_('behavior.tooltip');

$app = &JFactory::getApplication();
$params = JComponentHelper::getParams('com_wimtvpro');
$username = $params->get('wimtv_username');
$password = $params->get('wimtv_password');


$view_page = wimtvpro_alert_reg($username,$password);
if ($view_page){
?>
<form action="<?php echo JRoute::_('index.php?option=com_wimtvpro'); ?>"
	method="post" name="adminForm">
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
	<div>
		<input type="hidden" name="task" value="" /> <input type="hidden"
			name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $this->sortColumn; ?>" />
        <input type="hidden" name="filter_order_Dir" value="<?php echo $this->sortDirection; ?>" />
			
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>


<?php } ?>
