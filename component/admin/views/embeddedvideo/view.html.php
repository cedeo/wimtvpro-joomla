<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * Questa view mostra l'anteprima di un video on demand.
 */
class wimtvproViewembeddedvideo extends JView {

    /**
     * View form
     *
     * @var		form
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
        $page = $_GET["page"] . ".cancel";
        JToolBarHelper::title(JText::_('Video'));
        //JToolBarHelper::save('putVideos.save');
        $alt = (isset($isNew) && $isNew) ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE';
        JToolBarHelper::cancel($page, $alt);
    }

    /**
     * Method to set up the document properties
     *
     * @return void
     */
    protected function setDocument() {
        // NS:
        // $isNew = ($this->item->id < 1);
        $isNew = isset($this->item->id) ? ($this->item->id < 1) : false;
        $document = JFactory::getDocument();
        $document->setTitle(JText::_('Video'));

        $document->addStyleSheet(JURI::base() . "components/com_wimtvpro/assets/css/wimtvpro.css");
        $document->addScript(JURI::base() . "components/com_wimtvpro/assets/js/jquery-2.0.2.min.js");
        $document->addScript(JURI::base() . "components/com_wimtvpro/assets/js/wimtvpro.js");
        $document->addScript(JURI::base() . "components/com_wimtvpro/assets/js/jwplayer/jwplayer.js");
    }

}