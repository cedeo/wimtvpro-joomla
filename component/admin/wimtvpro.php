<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
// Require the base controller
jimport('joomla.application.component.controller');
// Create the controller
$controller = JController::getInstance('WimTvPro');
// Perform the Request task
$controller->execute( JRequest::getCmd('task') );
// Redirect if set by the controller
$controller->redirect();