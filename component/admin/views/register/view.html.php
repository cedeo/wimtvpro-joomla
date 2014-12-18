<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');
require_once ( JPATH_BASE . "/components/com_wimtvpro/includes/function.php" );

/**
 * Questa view permette all'utente di registrarsi su wim.tv
 */
class WimtvproViewregister extends JView {

    /**
     * View form
     *
     * @var		form
     */
    protected $form = null;

    /**
     * @return void
     */
    public function display($tpl = null) {
        // get the Data
        $form = $this->get('Form');
        $item = $this->get('Item');

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
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
    protected function addToolBar() {
        $username = isset($username) ? $username : "";
        $password = isset($password) ? $password : "";

        $view_page = wimtvpro_alert_reg($username, $password, false);
        if (!$view_page) {
            JRequest::setVar('hidemainmenu', true);
            $isNew = (isset($this->item->vid) && $this->item->vid == 0);
            JToolBarHelper::title(JText::_('REGISTRATION WIMTV'), 'wimtvpro');
            JToolBarHelper::save('register.save');
        }

        $this->document->setTitle(JText::_('JGLOBAL_EDIT_PREFERENCES'));
    }

    /**
     * Method to set up the document properties
     *
     * @return void
     */
    protected function setDocument() {
        $username = isset($username) ? $username : "";
        $password = isset($password) ? $password : "";
        $view_page = wimtvpro_alert_reg($username, $password, false);
        if (!$view_page) {
            $isNew = isset($this->item->id) && ($this->item->id < 1);
            $document = JFactory::getDocument();
            $document->setTitle(JText::_('COM_WIMTVPRO_ADMINISTRATION'));

            $document->addStyleSheet(JURI::base() . "components/com_wimtvpro/assets/css/wimtvpro.css");
            $document->addScript(JURI::base() . "components/com_wimtvpro/assets/js/jquery-2.0.2.min.js");
            $document->addScript(JURI::base() . "components/com_wimtvpro/assets/js/wimtvpro.js");
        } else {

            echo "You are register wimtv!";
        }
    }

}