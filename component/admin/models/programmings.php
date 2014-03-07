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

}