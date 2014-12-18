<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
JFormHelper::loadFieldClass('list');
$view = JRequest::getCmd('view');
//Get companie options
JFormHelper::addFieldPath(JPATH_COMPONENT . '/models/fields');
$title = JFormHelper::loadFieldType('MyMedias', false);
//$titleOptions=$title->getOptions(); // works only if you set your field getOptions on public!!
?>
<fieldset id="filter-bar">
    <div class="filter-search fltlft">
        <?php JText::_('Search title video'); ?> 
        <input type="text" 
               name="filter_search"
               id="filter_search"
               value="<?php echo $this->escape($this->searchterms); ?>"
               title="<?php echo JText::_('Search title video'); ?>" />
        <button type="submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
        <button type="button" onclick="document.id('filter_search').value = '';
                this.form.submit();">
                    <?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>
        </button>
    </div>

    <div class="label_orderbydate">
        <?php echo JHtml::_('grid.sort', 'COM_WIMTVPRO_STATE_ORBERBYDATE', 'id', $this->sortDirection, $this->sortColumn); ?>
    </div>

</fieldset>

<tr>
    <th width="1%"></th>
    <th><?php
//        echo JHtml::_('grid.sort', 'Video', 'position', $this->sortDirection, $this->sortColumn); 
        echo JText::_('Video');
        ?>
    <th><?php echo JHtml::_('grid.sort', 'COM_WIMTVPRO_UPLOAD_TITLE_FIELD_TITLE', 'title', $this->sortDirection, $this->sortColumn); ?></th>
    <th width="8%">
        <?php
        //echo JText::_('COM_WIMTVPRO_STATE');
        echo JHtml::_('grid.sort', 'COM_WIMTVPRO_STATE', 'state', $this->sortDirection, $this->sortColumn);
        ?>
    </th>
    <th><?php echo 'Download'; ?>
</tr>
