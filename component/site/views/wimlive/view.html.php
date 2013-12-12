<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * HTML View class for the wimtvproViewwimlive Component
 */
class wimtvproViewwimlive extends JView
{
        // Overwriting JView display method
        function display($tpl = null) 
        {
        	$document = JFactory::getDocument();

        	$document->addScript(JURI::base() . "administrator/components/com_wimtvpro/assets/js/jquery-2.0.2.min.js");

        	$document->addScript(JURI::base() . "administrator/components/com_wimtvpro/assets/js/jwplayer/jwplayer.js");
                // Display the view
                parent::display($tpl);
        }
}