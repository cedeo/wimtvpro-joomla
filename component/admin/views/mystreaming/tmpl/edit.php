<?php 
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$option = JRequest::getCmd('option');

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
?>
<form action="index.php" method="post" name="adminForm"
	id="persona-admin-form" class="form-validate">
	<input type="hidden" name="option" value="<?=$option?>" /> <input
		type="hidden" name="task" value="" /> <input type="hidden" name="id"
		value="<?=$this->item->id?>" />
	<?php echo JHtml::_('form.token'); ?>

	<fieldset class="adminform">
		<legend>
			<?=JText::_( 'DETAILS' ); ?>
		</legend>
		<ul class="adminformlist">
			<?php    foreach ($this->form->getFieldset() as $field) { ?>
			<li><?php echo $field->label;?> <?php echo $field->input;?></li>
			<?php   } ?>
		</ul>
	</fieldset>
</form>
