<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();
jimport( 'joomla.application.component.view' );

/**
 * Questa view mostra la tabella dei video live (WimLive)
 */
class WimtvproViewwimlives extends JView
{
	function display($tpl = null)
	{
		 
		// Set the toolbar
		$this->addToolBar();
		 
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

		JToolBarHelper::title( JText::_('WimTVPro') . ": " .  JText::_( "COM_WIMTVPRO_TITLE_LIVE" ), 'wimtv' );
		if (($username!="username" && $password!="password") || ($username!="" && $password!="")){
			JToolBarHelper::addNewX('wimlive.add');  //Upload new Media
			//JToolBarHelper::custom('mymedia.download', 'download', 'assets/images/download.png', JText::_("Download"), true); //Download a video
			JToolBarHelper::editList('wimlive.edit', JText::_("MYMEDIA_CONFIRM_EDIT")); //'edit', 'edit', JText::_("MYMEDIA_CONFIRM_EDIT"), true);
			JToolBarHelper::custom('wimlive.delete', 'delete', 'delete', JText::_("MYMEDIA_CONFIRM_DELETE"), true);
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