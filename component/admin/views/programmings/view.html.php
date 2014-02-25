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
    public $nameProgramming = "";

    public function display($tpl = null)
    {
        // get the Data
        /*$item = $this->get('Item');
        $script = $this->get('Script');

        // Check for errors.
        if (count($errors = $this->get('Errors')))
        {
                JError::raiseError(500, implode('<br />', $errors));
                return false;
        }

        JRequest::setVar('hidemainmenu', true);

        // Assign the Data*/

        // Set the toolbar

        if ($this->getLayout() == "default") {
            $model = $this->getModel();
            $this->programmings = $model->getProgrammings();
        } else {
            $this->nameProgramming = isset($_GET['nameProgramming']) ? $_GET["nameProgramming"] : "";
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
		JToolBarHelper::title(JText::_('COM_WIMTVPRO_TITLE_PROGRAMMINGS'), 'wimtvpro');
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