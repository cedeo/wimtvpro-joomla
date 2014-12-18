<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');
jimport('joomla.application.component.controllerform');

/**
 * Questa view mostra la lista dei programmings, e ha due layout: edit e default.
 * Attraverso il parametro GET layout, è possibile scegliere il layout da applicare alla view.
 * TODO: Questa è la maniera corretta di utilizzare una view di Joomla.
 * TODO: Le altre view sono divise in (view) e (view)s per permettere l'editing e il display.
 * TODO: Andrebbe corretta questa cosa utilizzando una sola view e più layout.
 */
class WimtvproViewprogrammings extends JView
{

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