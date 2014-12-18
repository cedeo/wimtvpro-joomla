<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');
jimport('joomla.application.component.controller');
jimport('joomla.application.component.helper');

require_once ( JPATH_BASE . "/components/com_wimtvpro/includes/function.php" );
/**
 * Questo controller gestisce la view che permette la visualizzazione della tabella WimVod
 */
// No direct access to this file
// NS
// defined('_JEXEC') or die('Restricted access');
// import Joomla controlleradmin library
jimport('joomla.application.component.controlleradmin');

/**
 * HelloWorlds Controller
  jimport( 'joomla.application.component.controller' );
  /**
 * WIMTVPRO MEDIA Controller
 */
// No direct access to this file
// NS
//defined('_JEXEC') or die('Restricted access');
// import Joomla controlleradmin library
jimport('joomla.application.component.controlleradmin');

/**
 * HelloWorlds Controller
 */
class wimtvproControllermystreamings extends JControllerAdmin {

    /**
     * Proxy for getModel.
     * @since	1.6
     */
    public function getModel($name = 'mystreamings', $prefix = 'wimtvproModel') {
        $model = parent::getModel($name, $prefix, array('ignore_request' => true));
        return $model;
    }

    function cancel() {
        $link = JURI::base() . "index.php?option=com_wimtvpro&view=mystreamings";
        JFactory::getApplication()->redirect($link);
    }

    function addPlaylist() {

        $idPlayList = $_GET["idPlayList"];
        $idVideo = $_GET["id"];


        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select('listVideo,name');
        $query->from('#__wimtvpro_playlist');
        $query->where("id='" . $idPlayList . "'");

        // Reset the query using our newly populated query object.
        $db->setQuery($query);

        // Load the results as a list of stdClass objects.
        $playlist = $db->loadObjectList();
        if ($error = $db->getErrorMsg()) {
            throw new Exception($error);
        }

        foreach ($playlist as $record) {
            $listVideo = $record->listVideo;
            $name = $record->name;
        }

        //Check if this file exist

        if (strpos($listVideo, trim($idVideo)) > -1) {
            echo "This video exist into " . $name . " playlist.";
            die();
        } else {

            // UPDATE into DB (campo listVideo)
            $listVideo = trim($listVideo);
            if ($listVideo == "")
                $listVideo = $idVideo;
            else
                $listVideo = $listVideo . "," . $idVideo;

            $fields = array(
                "listVideo = '" . $listVideo . "'"
            );

            // Conditions for which records should be updated.
            $conditions = array(
                "id='" . $idPlayList . "'");

            $query->update($db->quoteName("#__wimtvpro_playlist"))->set($fields)->where($conditions);

            $db->setQuery($query);
            try {
                $result = $db->query(); // Use $db->execute() for Joomla 3.0.
            } catch (Exception $e) {
                throw new Exception($e);
            }

            die();
        }
    }

    function deletePlaylist() {

        $idPlayList = $_GET["idPlayList"];
        $db = JFactory::getDbo();

        $query = $db->getQuery(true);

        // delete all custom keys for user 1001.
        $conditions = array(
            "id='" . $idPlayList . "'");

        $query->delete($db->quoteName('#__wimtvpro_playlist'));
        $query->where($conditions);

        $db->setQuery($query);

        try {
            $result = $db->query(); // $db->execute(); for Joomla 3.0.
        } catch (Exception $e) {
            // catch the error.
        }
    }

    public function sync() {
        $params = JComponentHelper::getParams('com_wimtvpro');
        $username = $params->get('wimtv_username');
        syncWimtvpro($username, "mystreamings");
    }

}
