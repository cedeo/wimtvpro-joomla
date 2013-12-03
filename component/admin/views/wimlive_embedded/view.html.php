<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

class wimtvproViewwimlive_embedded extends JView
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

		// Display the template
		parent::display($tpl);

		// Set the document
		$this->setDocument();
	}

	protected function setDocument()
	{
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_WIMTVPRO_ADMINISTRATION'));
		$document->addStyleSheet(JURI::base() . "components/com_wimtvpro/assets/css/wimtvpro.css");
		$document->addScript(JURI::base() . "components/com_wimtvpro/assets/js/jquery-2.0.2.min.js");
		$document->addScript(JURI::base() . "components/com_wimtvpro/assets/js/wimtvpro.js");
		$document->addScript(JURI::base() . "components/com_wimtvpro/assets/js/swfObject/swfobject.js");
	}
}