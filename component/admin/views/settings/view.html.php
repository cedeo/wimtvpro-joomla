<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.view' );
jimport( 'joomla.html.pagination' );
require_once ( JPATH_BASE . "/components/com_wimtvpro/includes/function.php" );


class wimtvproViewsettings extends JView
{
	function display($tpl = null)
	{
		
		$extension = 'com_wimtvpro';
		$lang = JFactory::getLanguage();
		$source = JPATH_ADMINISTRATOR . '/components/' . $extension;
		$lang->load("$extension.sys", JPATH_ADMINISTRATOR, null, false, false)
		||    $lang->load("$extension.sys", $source, null, false, false)
		||    $lang->load("$extension.sys", JPATH_ADMINISTRATOR, $lang->getDefault(), false, false)
		||    $lang->load("$extension.sys", $source, $lang->getDefault(), false, false);
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
		
		JToolBarHelper::title( JText::_('WimTVPro') . ": " .  JText::_( "COM_WIMTVPRO_TITLE_SETTING" ), 'wimtv' );
		
		if (($username!="username" && $password!="password") && ($username!="" && $password!="")){
			
			
			JToolBarHelper::divider();

			JToolBarHelper::save('settings.save', JText::_("COM_WIMTVPRO_UPDATE"));
			JToolBarHelper::divider();
		
		} else
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
