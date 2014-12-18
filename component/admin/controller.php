<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
// import Joomla controller library
jimport('joomla.application.component.controller');

/**
 * General Controller of Wimtvpro component
 */
class WimtvproController extends JController {

    public function display($cachable = false, $urlparams = false) {
        JRequest::setVar('view', JRequest::getCmd('view', 'mymedias'));

        parent::display($cachable);
    }

}