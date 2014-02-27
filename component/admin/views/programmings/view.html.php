<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');
jimport('joomla.application.component.controllerform');
class WimtvproViewprogrammings extends JView
{
	/**
	 * View form
	 *
	 * @var		form
	 */

	/**
	 * @return void
	 */

    public $programmings;

    public function display($tpl = null)
    {

        $task = isset($_GET['task']) ? $_GET['task'] : null;
        $progId = isset($_GET['progId']) ? $_GET['progId'] : null;

        if ($task == "delete" && $progId) {
            apiDeleteProgramming($progId);
        }

        if ($this->getLayout() == "default") {
            $model = $this->getModel();
            $this->programmings = $model->getProgrammings();
        }
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
		JToolBarHelper::title(JText::_('COM_WIMTVPRO_TITLE_PROGRAMMINGS'), "wimtv");
        if ($this->getLayout() == "default") {
            JToolBarHelper::divider();
            JToolBarHelper::addNew('programmings.add','Add');
        }
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
		
	}
}