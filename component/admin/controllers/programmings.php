<?php


// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');
require_once ( JPATH_BASE . "/components/com_wimtvpro/includes/api/wimtv_api.php" );
/**
 * Questo controller gestisce la view che permette la visualizzazione e la modifica dei palinsesti
 */
class WimtvproControllerprogrammings extends JControllerForm
{

    public function __construct($config = array())
    {
        $this->view_list = 'programmings';
        parent::__construct($config);
    }

    public function getModel($name = 'programmings', $prefix = 'wimtvproModel')
    {
        $model = parent::getModel($name, $prefix, array('ignore_request' => true));
        return $model;
    }


    public function add()
    {
        $input = JFactory::getApplication()->input;
        $pks = $input->post->get('task', "");
        if ($pks == "add") {
            $link = JURI::base() . "index.php?option=com_wimtvpro&view=programmings&layout=edit";
            JFactory::getApplication()->redirect($link);
        }
    }

}
