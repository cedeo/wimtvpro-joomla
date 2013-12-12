<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HelloWorld View
*/
class wimtvproViewembeddedplaylist extends JView
{
	/**
	 * View form
	 *
	 * @var		form
	 */
	protected $form = null;

	/**
	 * @return void
	 */
	public function display($tpl = null)
	{
		
		

		// Set the toolbar
		$this->addToolBar();

		// Display the template
		parent::display($tpl);

		// Set the document
		$this->setDocument();
	}

	/**
	 * Setting the toolbar
	 */
	protected function addToolBar()
	{
		JRequest::setVar('hidemainmenu', true);
		$page = "mystreamings.cancel";
		JToolBarHelper::title(JText::_('Playlist'));
		//JToolBarHelper::save('putVideos.save');
		JToolBarHelper::cancel( $page, $isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE');
	}
	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument()
	{
		$isNew = ($this->item->id < 1);
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_WIMTVPRO_STATE_ADD'));
		
		$document->addStyleSheet(JURI::base() . "components/com_wimtvpro/assets/css/wimtvpro.css");
		$document->addScript(JURI::base() . "components/com_wimtvpro/assets/js/jquery-2.0.2.min.js");
		$document->addScript(JURI::base() . "components/com_wimtvpro/assets/js/wimtvpro.js");
		$document->addScript(JURI::base() . "components/com_wimtvpro/assets/js/jwplayer/jwplayer.js");
		
	}
}