<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
JFormHelper::loadFieldClass('list');
$view = JRequest::getCmd('view');
//Get companie options
JFormHelper::addFieldPath(JPATH_COMPONENT . '/models/fields');
$title = JFormHelper::loadFieldType('MyStreamings', false);
//$titleOptions=$title->getOptions(); // works only if you set your field getOptions on public!!
// NS
$listDirn = isset($listDirn) ? $listDirn : "";
$listOrder = isset($listOrder) ? $listOrder : "";

$searchTerm = isset($_POST['filter_search']) ? $this->escape($_POST['filter_search']) : "";

if (!isset($_GET["inserInto"])) {
    ?>
    <fieldset id="filter-bar">
        <div class="filter-search fltlft">
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


<?php } ?>


<tr>

    <?php if (!isset($_GET["inserInto"])) {
        ?>
        <th style="display:none;" width="1%"><input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" /></th>

        <th width='5%'>
            <?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ORDERING', 'a.lft', $listDirn, $listOrder); ?>
            <?php echo JHtml::_('grid.order', $this->items, 'filesave.png', 'mystreamings.saveorder'); ?>	
        </th>
    <?php } ?>
    <th><?php
//        echo JHtml::_('grid.sort', 'Video', 'position', $this->sortDirection, $this->sortColumn);
        echo JText::_('Video');
        ?>
    <th><?php echo JHtml::_('grid.sort', 'COM_WIMTVPRO_UPLOAD_TITLE_FIELD_TITLE', 'title', $this->sortDirection, $this->sortColumn); ?></th>
    <th><?php echo JText::_('COM_WIMTVPRO_UPLOAD_TITLE_FIELD_SHORTCODE'); ?></th>
    <?php if (!isset($_GET["inserInto"])) { ?>
        <th width="1%"><?php echo JText::_('COM_WIMTVPRO_STATE'); ?>
        <th><?php echo 'Download'; ?></th>
        <th><?php echo 'Playlist'; ?></th>
    <?php } ?>
    <?php if (isset($_GET["inserInto"])) { ?>
        <th><?php echo 'Insert'; ?></th>
    <?php } ?>
</tr>
