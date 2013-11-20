<?php
/**
 * default head template file for HelloWorlds view of HelloWorld component
 *
 * @version		$Id: default_head.php 51 2010-11-22 01:33:21Z chdemko $
 * @package		Wimtvpro
 * @subpackage	Components
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @author		Christophe Demko
 * @link		http://joomlacode.org/gf/project/helloworld_1_6/
 * @license		License GNU General Public License version 2 or later
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
?>


		<tr>
			<th width="1%"><input type="checkbox" onclick="Joomla.checkAll(this)"
				title="<?=JText::_( 'Check All' )?>" value="" name="checkall-toggle">
			</th>
			<th><?php echo  JHtml::_('grid.sort', 'URLVIDEO', 'urlThumbs', $listDirn, $listOrder); ?>
			</th>
			<th><?php echo  JHtml::_('grid.sort', 'TITLE', 'title', $listDirn, $listOrder); ?>
			</th>
			<th><?php echo JHtml::_('grid.sort', 'ID', 'id', $listDirn, $listOrder); ?>
			</th>
		</tr>
