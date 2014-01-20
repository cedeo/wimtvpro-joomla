<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.view' );
jimport( 'joomla.html.pagination' );

class WimtvproViewmyplaylists extends JView
{
	function display($tpl = null)
	{
		// Get data from the model
		$items = $this->get('Items');
		$pagination = $this->get('Pagination');
		
		$document = JFactory::getDocument();
		$document->addStyleSheet(JURI::base() . "components/com_wimtvpro/assets/css/wimtvpro.css");
		$document->addScript(JURI::base() . "components/com_wimtvpro/assets/js/jquery-2.0.2.min.js");
		
		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}
		// Assign data to the view
		$this->items = $items;
		$this->pagination = $pagination;

		// Set the toolbar
		$this->addToolBar();

		/*$input = JFactory::getApplication()->input;
		 $view = $input->getCmd('view', '');
		WimtvproHelper::addSubmenu($view);*/

		// Display the template
		parent::display($tpl);

		// Set the document
		$this->setDocument();
	}


	/* Setting the toolbar
	 */
	protected function addToolBar()
	{
		// Toolbar
		$params = JComponentHelper::getParams('com_wimtvpro');
		$username = $params->get('wimtv_username');
		$password = $params->get('wimtv_password');
		
		JToolBarHelper::title( 'Playlist', 'wimtv' );
		
		
		JToolBarHelper::preferences('com_wimtvpro');
		JToolBarHelper::addNewX('myplaylist.add');  //Upload new Media
		//JToolBarHelper::editListX('mymedia.edit');
		JToolBarHelper::custom('myplaylists.delete', 'delete', 'delete', JText::_("MYMEDIA_CONFIRM_DELETE"), true);
	}
	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument()
	{
		$document = JFactory::getDocument();
		//JHTML::_('custom.tablesorter');
		$document->setTitle(JText::_('COM_WIMTVPRO_ADMINISTRATION'));
		JText::script('COM_WIMTVPRO_MSG_PLAYLIST_ADDEDVIDEO');
		JText::script('COM_WIMTVPRO_MSG_PLAYLIST_ERROR');
		$document->addStyleSheet(JURI::base() . "components/com_wimtvpro/assets/css/wimtvpro.css");
		$document->addScript(JURI::base() . "components/com_wimtvpro/assets/js/jquery-2.0.2.min.js");
		$document->addScript(JURI::base() . "components/com_wimtvpro/assets/js/wimtvpro.js");
	}

}