<?php
/**
 * Questa view mostra le statistiche prese da stats.wim.tv
 */
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view' );

require_once ( JPATH_BASE . "/components/com_wimtvpro/includes/function.php" );
class wimtvproViewreport extends JView
{
	function display($tpl = null)
	{

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}


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
		 
		JToolBarHelper::title(JText::_('WimTVPro') . ": " . JText::_( 'COM_WIMTVPRO_TITLE_REPORT' ), 'wimtv' );
		
		
		if (($username=="username" && $password=="password") || ($username=="" && $password==""))
			JToolBarHelper::preferences('com_wimtvpro');
		//JToolBarHelper::addNewX('mymedia.add');
		//JToolBarHelper::editListX('mymedia.edit');
		//JToolBarHelper::deleteList( JText::_( 'MYMEDIA_CONFIRM_DELETE' ),'mymedias.delete' );
	}
	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument()
	{
		$document = JFactory::getDocument();
		 
		$document->addStyleSheet(JURI::base() . "components/com_wimtvpro/assets/css/wimtvpro.css");
		$document->addScript(JURI::base() . "components/com_wimtvpro/assets/js/jquery-2.0.2.min.js");
		$document->setTitle(JText::_('COM_WIMTVPRO_ADMINISTRATION'));
	}

}