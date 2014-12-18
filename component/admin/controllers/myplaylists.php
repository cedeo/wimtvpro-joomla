<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');
jimport( 'joomla.application.component.controller' );
require_once ( JPATH_BASE . "/components/com_wimtvpro/includes/function.php" );
/**
 * Questo controller gestisce la view che permette la visualizzazione della tabella delle playlist
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controlleradmin library
jimport('joomla.application.component.controlleradmin');

/**
 * HelloWorlds Controller
jimport( 'joomla.application.component.controller' );
/**
 * WIMTVPRO MEDIA Controller
*/
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controlleradmin library
jimport('joomla.application.component.controlleradmin');

/**
 * HelloWorlds Controller
*/
class WimtvproControllermyplaylists extends JControllerAdmin
{
	/**
	 * Proxy for getModel.
	 * @since	1.6
	 */
	public function getModel($name = 'myplaylist', $prefix = 'wimtvproModel')
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
	
	public function delete()
	{
		

	
	}
	

	
}
