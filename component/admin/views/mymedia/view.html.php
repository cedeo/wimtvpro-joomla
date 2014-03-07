<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * Questa view mostra la pagina che permette l'upload di un video su WimBox
 */
class WimtvproViewmymedia extends JView
{
	/**
	 * View form
	 *
	 * @var		form
	 */
	protected $form = null;

	/**
	 * Displays the view
	 */
	public function display($tpl = null)
	{
		// get the Data
		$form = $this->get('Form');
		$item = $this->get('Item');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}
		// Assign the Data
		$this->form = $form;
		$this->item = $item;

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
		$isNew = ($this->item->vid == 0);
		JToolBarHelper::title($isNew ? JText::_('COM_WIMTVPRO_MYMEDIA_NEW') : JText::_('COM_WIMTVPRO_MYMEDIA_EDIT'), 'wimtvpro');
		JToolBarHelper::save('mymedia.save');
		JToolBarHelper::cancel('mymedia.cancel', $isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE');
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
				$document->setTitle(JText::_('COM_WIMTVPRO_ADMINISTRATION'));
		
		$document->addStyleSheet(JURI::base() . "components/com_wimtvpro/assets/css/wimtvpro.css");
		$document->addScript(JURI::base() . "components/com_wimtvpro/assets/js/jquery-2.0.2.min.js");
		$document->addScript(JURI::base() . "components/com_wimtvpro/assets/js/wimtvpro.js");
		
	}
}