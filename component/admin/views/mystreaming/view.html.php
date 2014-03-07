<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();
jimport( 'joomla.application.component.view' );


/**
 * Questa view mostra
 */
class wimtvproViewmystreamingsinsert extends JView
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

	}
	/**
	 * Method to set up the document properties
	 *
	 */
	protected function setDocument()
	{
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_WIMTVPRO_SETTING'));
	}
}