<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
require_once ( JPATH_BASE . "/components/com_wimtvpro/includes/api/wimtv_api.php" );

// import Joomla modelform library
jimport('joomla.application.component.modellist');

/**
 * HelloWorld Model
 */
class WimtvproModelprogrammings extends JModelList
{
    public $programmings;

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return	mixed	The data for the form.
     * @since	1.6
     */

    public function getItem($pk = null){

        return array();
    }

    public function getProgrammings() {
        if (!$this->programmings) {
            $response = json_decode(apiGetProgrammings());
            $programmings = $response->programmings;
            $this->programmings = $programmings;
        }
        return $this->programmings;
    }

    public function setProgrammings($programmings) {
        return $this->programmings = $programmings;
    }

    public function edit()
    {
        $input = JFactory::getApplication()->input;
        //var_dump ($input);
        $pks = $input->post->get('cid', array(), 'array');

        if (count($pks)>1){
            JFactory::getApplication()->enqueueMessage("You can select only one event to edit");
            $this->setRedirect('index.php?option=com_wimtvpro&view=wimlives');
        } else {
            $this->setRedirect('index.php?option=com_wimtvpro&view=wimlive&layout=edit&cid=' . $pks[0]);

        }


    }
}