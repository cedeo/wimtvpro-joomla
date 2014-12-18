<?php


// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');

/**
 * Questo controller gestisce la view che permette la creazione di una playlist
 */
class WimtvproControllermyplaylist extends JControllerForm
{
	public function __construct($config = array())
	{
		$this->view_list = 'myplaylists';
		parent::__construct($config);
	}
	
	public function getModel($name = 'myplaylist', $prefix = 'wimtvproModel')
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
	
	
	public function save()
	{
		
		$app = &JFactory::getApplication();
		$params = JComponentHelper::getParams('com_wimtvpro');
		$username = $params->get('wimtv_username');
		$jform = JRequest::getVar('jform');
		$name = $jform['name'];

		$insert_playlist = new stdClass();
		$insert_playlist->name = $name;
		$insert_playlist->uid = $username;
		$insert_playlist->listVideo = "";
		try {
			// Insert the object into the user profile table.
			$result = JFactory::getDbo()->insertObject('#__wimtvpro_playlist', $insert_playlist);
			
			//Redirect MyStreaming
			$link = JURI::base() . "index.php?option=com_wimtvpro&view=mystreamings";
			JFactory::getApplication()->redirect($link , 'Create new playlist', 'Redirect' );

			
		} catch (Exception $e) {
			throw new Exception($e);

		}
	
	}
	
}
