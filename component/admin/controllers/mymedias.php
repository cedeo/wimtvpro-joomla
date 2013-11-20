<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');
jimport( 'joomla.application.component.controller' );
jimport('joomla.application.component.helper');

require_once ( JPATH_BASE . "/components/com_wimtvpro/includes/function.php" );
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
		$app = &JFactory::getApplication();
		$params = JComponentHelper::getParams('com_wimtvpro');
		$username = $params->get('wimtv_username');
		$password = $params->get('wimtv_password');
		$basePath = $params->get('wimtv_basepath');
		$urlVideosDetailWimtv = trim($params->get('wimtv_urlVideosDetailWimtv'));
		$url_video = $basePath . $urlVideosDetailWimtv;
		$credential = $username . ":" . $password;
		syncWimtvpro($username,$credential,$url_video,"mymedias");
		
	}
	
	public function delete()
	{
		$app = &JFactory::getApplication();
		$params = JComponentHelper::getParams('com_wimtvpro');
		$username = $params->get('wimtv_username');
		$password = $params->get('wimtv_password');
		$basePath = $params->get('wimtv_basepath');		
		$credential = $username . ":" . $password;
		$input = JFactory::getApplication()->input;
		//var_dump ($input);
		$pks = $input->post->get('cid', array(), 'array');
		
		foreach ($pks as $id) {
		
			$ch = curl_init();
			$url_delete = $basePath . 'videos';
			$url_delete .= "/" . $id;
				
				
			curl_setopt($ch, CURLOPT_URL, $url_delete);
			curl_setopt($ch, CURLOPT_VERBOSE, 0);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($ch, CURLOPT_USERPWD, $credential);
			
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept-Language:' . $_SERVER['HTTP_ACCEPT_LANGUAGE']));
				
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			$response = curl_exec($ch);
			curl_close($ch);
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
					$result = $db->query(); // $db->execute(); for Joomla 3.0.
						
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
		
		$state="";

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
			$result = $db->query(); // Use $db->execute() for Joomla 3.0.
		} catch (Exception $e) {
			throw new Exception($e);
		}
		
		$app = &JFactory::getApplication();
		$params = JComponentHelper::getParams('com_wimtvpro');
		$username = $params->get('wimtv_username');
		$password = $params->get('wimtv_password');
		$basePath = $params->get('wimtv_basepath');
		$credential = $username . ":" . $password;
		
		//Richiamo API
		//https://www.wim.tv/wimtv-webapp/rest/videos/{contentIdentifier}/showtime/{showtimeIdentifier}
		//curl -u {username}:{password} -X DELETE https://www.wim.tv/wimtv-webapp/rest/videos/{contentIdentifier}/showtime/{showtimeIdentifier}
		$url_remove_public_wimtv = $basePath . "videos/" . $coid . "/showtime/" . $id;
		echo $url_remove_public_wimtv;
		//This API allows posting an ACQUIRED video on the Web my streaming for public streaming.
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url_remove_public_wimtv);
		curl_setopt($ch, CURLOPT_VERBOSE, 0);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_USERPWD, $credential);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept-Language:' . $_SERVER['HTTP_ACCEPT_LANGUAGE']));
		
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		$response = curl_exec($ch);


		$message = json_encode($response);
		
		JError::raiseWarning( 100, $response->message );

		
	}
	
}

