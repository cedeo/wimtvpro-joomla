<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');
jimport( 'joomla.application.component.controller' );
jimport('joomla.application.component.helper');

require_once ( JPATH_BASE . "/components/com_wimtvpro/includes/function.php" );
require_once ( JPATH_BASE . "/components/com_wimtvpro/includes/api/wimtv_api.php" );

/**
 * WIMTVPRO MEDIA Controller
*/
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controlleradmin library
jimport('joomla.application.component.controlleradmin');

class WimtvproControllermymedias extends JControllerAdmin
{
	/**
	 * Proxy for getModel.
	 * @since	1.6
	 */
	public function getModel($name = 'mymedia', $prefix = 'wimtvproModel')
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
	

	
	public function sync()
	{
		$params = JComponentHelper::getParams('com_wimtvpro');
		$username = $params->get('wimtv_username');
		syncWimtvpro($username,"mymedias");
    }
	
	public function delete()
	{
		$input = JFactory::getApplication()->input;
		$pks = $input->post->get('cid', array(), 'array');
		
		foreach ($pks as $id) {
			$response = apiDeleteVideo($id);
			$arrayjsonst = json_decode($response);
			if ($arrayjsonst->result=="SUCCESS"){
					
				$db = JFactory::getDbo();
					
				$query = $db->getQuery(true);
					
				// delete all custom keys for user 1001.
				$conditions = array(
						"contentidentifier = '"  . $id  . "'");
					
				$query->delete($db->quoteName('#__wimtvpro_videos'));
				$query->where($conditions);
					
				$db->setQuery($query);
					
				try {
					$db->query(); // $db->execute(); for Joomla 3.0.
					//JError::raiseWarning( 100, $response);
					$message = $arrayjsonst->message;
				} catch (Exception $e) {
					// catch the error.
					JError::raiseWarning( 100, "Error connection." );
					$errorRemove ++;
				}
	
			}
				
		}
		
		if ($errorRemove==0){
			//JFactory::getApplication()->enqueueMessage($message);
			$link = JURI::base() . "index.php?option=com_wimtvpro&view=mymedias";
			JFactory::getApplication()->redirect($link , $message, 'Redirect' );
		} else {
			JError::raiseWarning( 100, "Error connection." );
		}

	
	}

	function cancel(){
		$link = JURI::base() . "option=com_wimtvpro&view=mymedias";
		JFactory::getApplication()->redirect($link);
	}
	
	function removeShowtime(){
		$id = $_GET["id"];
		$coid = $_GET["coid"];
		$fields = array("position='0'","state=''","showtimeIdentifier=''");
		
		// Conditions for which records should be updated.
		$conditions = array("  contentidentifier = '"  . $coid . "' ");
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->update($db->quoteName("#__wimtvpro_videos"))->set($fields)->where($conditions);
		
		$db->setQuery($query);
		
		try {
			$db->query(); // Use $db->execute() for Joomla 3.0.
		} catch (Exception $e) {
			throw new Exception($e);
		}

		$response = apiDeleteFromShowtime($coid, $id);
		JError::raiseWarning( 100, $response->message );
	}
	
}

