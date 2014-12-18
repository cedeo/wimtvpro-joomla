<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');
jimport('joomla.application.component.controllerform');

/**
 * Questa view mostra la pagina di modifica di un evento live.
 */
class WimtvproViewwimlive extends JView {

    protected $form = null;

    public function display($tpl = null) {
        // get the Data
        $form = $this->get('Form');
        $item = $this->get('Item');
        $script = $this->get('Script');

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            JError::raiseError(500, implode('<br />', $errors));
            return false;
        }


        JRequest::setVar('hidemainmenu', true);

        // Assign the Data
        $this->form = $form;
        $this->item = $item;
        $this->script = $script;

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
    protected function addToolBar() {
        $isNew = isset($_GET["cid"]) ? $_GET["cid"] : "";
        JToolBarHelper::title(!isset($isNew) ? JText::_('COM_WIMTVPRO_TITLE_LIVE_NEW') : JText::_('COM_WIMTVPRO_TITLE_LIVE_EDIT'), 'wimtvpro');
        JToolBarHelper::save('wimlive.save');
        JToolBarHelper::cancel('wimlive.cancel', $isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE');
    }

    /**
     * Method to set up the document properties
     *
     * @return void
     */
    protected function setDocument() {
        $isNew = !isset($_GET["cid"]) || ( $_GET["cid"] < 1);
        $document = JFactory::getDocument();
        $document->setTitle(JText::_('COM_WIMTVPRO_ADMINISTRATION'));

        $document->addStyleSheet(JURI::base() . "components/com_wimtvpro/assets/css/wimtvpro.css");
        $document->addScript(JURI::base() . "components/com_wimtvpro/assets/js/jquery-2.0.2.min.js");
        $document->addScript(JURI::base() . "components/com_wimtvpro/assets/js/wimtvpro.js");
    }

}