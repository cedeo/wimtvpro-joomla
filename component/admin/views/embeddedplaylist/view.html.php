<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * Questa view mostra l'anteprima di una playlist, con il codice di embedding associato.
 */
class wimtvproViewembeddedplaylist extends JView {

    /**
     * View form
     *
     */
    protected $form = null;

    /**
     * Displays the view
     */
    public function display($tpl = null) {
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
        JRequest::setVar('hidemainmenu', true);
        
        JToolBarHelper::title(JText::_('Playlist'));
        //JToolBarHelper::save('putVideos.save');
        $alt = (isset($isNew) && $isNew) ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE';
        $page = "mystreamings.cancel";
        JToolBarHelper::cancel($page, $alt);
    }

    /**
     * Method to set up the document properties
     *
     */
    protected function setDocument() {
        // NS:
        // $isNew = ($this->item->id < 1);
        $isNew = isset($this->item->id) ? ($this->item->id < 1) : false;
        $document = JFactory::getDocument();
        $document->setTitle(JText::_('COM_WIMTVPRO_STATE_ADD'));

        $document->addStyleSheet(JURI::base() . "components/com_wimtvpro/assets/css/wimtvpro.css");
        $document->addScript(JURI::base() . "components/com_wimtvpro/assets/js/jquery-2.0.2.min.js");
        $document->addScript(JURI::base() . "components/com_wimtvpro/assets/js/wimtvpro.js");
        $document->addScript(JURI::base() . "components/com_wimtvpro/assets/js/jwplayer/jwplayer.js");
    }

}