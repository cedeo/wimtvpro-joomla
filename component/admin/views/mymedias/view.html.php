<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.view' );
jimport( 'joomla.html.pagination' );
require_once ( JPATH_BASE . "/components/com_wimtvpro/includes/function.php" );
require_once ( JPATH_BASE . "/components/com_wimtvpro/includes/api/wimtv_api.php" );

class wimtvproViewmymedias extends JView
{
	function display($tpl = null)
	{
		// Get data from the model
		$items = $this->get('Items');
		$pagination = $this->get('Pagination');
		$state            = $this->get('State');
		$this->sortDirection = $state->get('list.direction');
		$this->sortColumn = $state->get('list.ordering');
		$this->searchterms      = $state->get('filter.search');
		
		$extension = 'com_wimtvpro';
		$lang = JFactory::getLanguage();
		$source = JPATH_ADMINISTRATOR . '/components/' . $extension;
		$lang->load("$extension.sys", JPATH_ADMINISTRATOR, null, false, false)
		||    $lang->load("$extension.sys", $source, null, false, false)
		||    $lang->load("$extension.sys", JPATH_ADMINISTRATOR, $lang->getDefault(), false, false)
		||    $lang->load("$extension.sys", $source, $lang->getDefault(), false, false);
		
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
		$app = &JFactory::getApplication();
		$params = JComponentHelper::getParams('com_wimtvpro');
		$username = $params->get('wimtv_username');
		$password = $params->get('wimtv_password');
		
		JToolBarHelper::title( JText::_('WimTVPro') . ": " .  JText::_( "COM_WIMTVPRO_TITLE_MEDIA" ), 'wimtv' );
		
		if (($username!="username" && $password!="password") && ($username!="" && $password!="")){
			
			JToolBarHelper::custom('mymedias.sync', 'sync', 'assets/images/sync.png', JText::_("COM_WIMTVPRO_SYNC"), false);
			
			JToolBarHelper::divider();
			JToolBarHelper::addNewX('mymedia.add','Upload');  //Upload new Media
			//JToolBarHelper::custom('mymedia.download', 'download', 'assets/images/download.png', JText::_("Download"), true); //Download a video
			//JToolBarHelper::editListX('mymedia.edit');
			JToolBarHelper::custom('mymedias.delete', 'delete', 'delete', JText::_("MYMEDIA_CONFIRM_DELETE"), true);
			JToolBarHelper::divider();
		
		}else
			JToolBarHelper::preferences('com_wimtvpro');
	}
	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument()
	{
		$document = JFactory::getDocument();		
		$document->setTitle(JText::_('COM_WIMTVPRO_ADMINISTRATION'));
		$document->addStyleSheet(JURI::base() . "components/com_wimtvpro/assets/css/wimtvpro.css");
		$document->addScript(JURI::base() . "components/com_wimtvpro/assets/js/jquery-2.0.2.min.js");
		$document->addScript(JURI::base() . "components/com_wimtvpro/assets/js/wimtvpro.js");
		JHTML::_('behavior.modal');
	}

}
