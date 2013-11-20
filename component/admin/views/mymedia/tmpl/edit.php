<?php 
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');



?>
<form enctype="multipart/form-data"
	action="<?php echo JRoute::_('index.php?option=com_wimtvpro&layout=edit&id=' . $this->item->id); ?>"
	method="post" name="adminForm" id="wimtvpro-form">
	<fieldset class="adminform">
		<legend>
			<?php echo JText::_( 'COM_WIMTVPRO_UPLOAD_TITLE' ); ?>
		</legend>
		<ul class="adminformlist">
			<?php foreach($this->form->getFieldset() as $field): ?>
			<li><?php echo $field->label;echo $field->input;?></li>
			<?php endforeach; ?>
		</ul>
	</fieldset>
	<div>
	<input type="hidden" name="task" value="mymedias.edit" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>

<?php 




?>