<?php
defined('_JEXEC') or die();
jimport( 'joomla.application.component.modellist' );
class WimtvproModelmystreamingsinsert extends JModelList
{
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array('position', 'title');
		}
		parent::__construct($config);



	}
	function getListQuery()
	{

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
		$title= $db->escape($this->getState('filter.title'));
		if (!empty($title)) {
			$query->where("uid='" . $username . "' AND showtimeIdentifier!='' AND (title LIKE '% " . $title . "%')");
		}
		return $query;
	}

	protected function populateState($ordering = null, $direction = null)
	{
		parent::populateState('position', 'ASC');
	}


}