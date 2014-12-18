<?php

defined('_JEXEC') or die();
jimport('joomla.application.component.modellist');

class WimtvproModelmystreamings extends JModelList {

    public function __construct($config = array()) {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array('position', 'title');
        }
        parent::__construct($config);
    }

    function getListQuery() {
        $params = JComponentHelper::getParams('com_wimtvpro');
        $username = $params->get('wimtv_username');

        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('#__wimtvpro_videos');

        $query->order($db->escape($this->getState('list.ordering', 'position')) . ' ' .
                $db->escape($this->getState('list.direction', 'ASC')));

        $query->where("uid='" . $username . "' AND showtimeIdentifier!=''");

        // Filter company
        $title = $db->escape($this->getState('filter.search'));
//        print "<H1>t: $title " . $this->getState('filter.search') . "</h1>";

        if (!empty($title)) {
            $query->where("uid='" . $username . "' AND showtimeIdentifier!='' AND (title LIKE '% " . $title . "%')");
        }

        return $query;
    }

    function reorder() {

        echo "Riordino";
    }

    public function saveorder($idArray = null, $lft_array = null) {
        //var_dump ($lft_array);

        foreach ($lft_array as $cid => $newPos) {

            $db = JFactory::getDbo();

            $query = $db->getQuery(true);

            // Fields to update.
            $fields = array(
                "position=" . $newPos);

            // Conditions for which records should be updated.
            $conditions = array(
                "contentidentifier='" . $cid . "'");

            $query->update($db->quoteName("#__wimtvpro_videos"))->set($fields)->where($conditions);

            $db->setQuery($query);
            try {
                $result = $db->query(); // Use $db->execute() for Joomla 3.0.
            } catch (Exception $e) {
                throw new Exception($e);
            }
        }
    }

    protected function populateState($ordering = null, $direction = null) {
        // NS:
        //  parent::populateState('position', 'ASC');
        parent::populateState('id', 'ASC');

        $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        //Omit double (white-)spaces and set state
        $this->setState('filter.search', preg_replace('/\s+/', ' ', $search));
    }

}