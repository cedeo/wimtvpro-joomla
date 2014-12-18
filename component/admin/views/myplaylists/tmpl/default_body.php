<?php
/**
 * default body template file for HelloWorlds view of HelloWorld component
 *
 * @version		$Id: default_body.php 46 2010-11-21 17:27:33Z chdemko $
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
<?php
$k = 0;
$i = 0;

foreach ($this->items as &$row)
{
	$checked = JHTML::_('grid.id', $i++, $row->vid );
	$link = JRoute::_( 'index.php?option=' . $option . '&task=mymedias.edit&id=' . $row->vid );
	?>
<tr class="row<?php echo $k?>">
	<td><?php echo $checked;?></td>
	<td><a href="<?php echo $link?>"> <?php echo $row->urlThumbs;?>
	</a>
	</td>
	<td><a href="<?php echo $link?>"> <?php echo $row->title;?>
	</a>
	</td>

	<td><a href="<?php echo $link?>"> <?php echo $row->id;?>
	</a>
	</td>

</tr>
<?php
$k = 1 - $k;
}

?>

