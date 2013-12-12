
<?php
defined('_JEXEC') or die();
jimport( 'joomla.application.component.modellist' );
require_once ( JPATH_BASE . "/components/com_wimtvpro/includes/function.php" );

class WimtvproModelmymedias extends JModelList
{
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array('position', 'title');
		}
		parent::__construct($config);
		
		
		
	}

    function sync() {
        $params = JComponentHelper::getParams('com_wimtvpro');
        $username = $params->get('wimtv_username');
        syncWimtvpro($username,"mymedias");
    }

    function getListQuery()
	{
		$app = &JFactory::getApplication();
		$params = JComponentHelper::getParams('com_wimtvpro');
		$username = $params->get('wimtv_username');

		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__wimtvpro_videos');
		
		$query->order($db->escape($this->getState('list.ordering', 'position')) . ' ' .
				$db->escape($this->getState('list.direction', 'ASC')));
		
		$query->where("uid='" . $username . "'");
		
		// Filter company
		$regex = str_replace(' ', '|', $this->getState('filter.search'));

		$title= $db->escape($this->getState('filter.search'));
		if (!empty($title)) {
			$query->where("(title LIKE '%" . $title . "%')");
		}

		return $query;
	}
	
	protected function populateState($ordering = null, $direction = null) 
	{
		parent::populateState('position', 'ASC');
		// Load the filter state.
		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		//Omit double (white-)spaces and set state
		$this->setState('filter.search', preg_replace('/\s+/',' ', $search));
		//Filter (dropdown) company
		$state = $this->getUserStateFromRequest($this->context.'.filter.title', 'filter_title', '', 'string');
		$this->setState('filter.title', $state);
	}
	
		
}