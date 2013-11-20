<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');
jimport( 'joomla.application.component.controller' );
require_once ( JPATH_BASE . "/components/com_wimtvpro/includes/function.php" );
/**
 * WIMTVPRO setting Controller
*/
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controlleradmin library
jimport('joomla.application.component.controlleradmin');

/**
 * HelloWorlds Controller
jimport( 'joomla.application.component.controller' );
/**
 * WIMTVPRO MEDIA Setting
*/
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controlleradmin library
jimport('joomla.application.component.controlleradmin');

/**
 * HelloWorlds Controller
*/
class wimtvproControllersettings extends JControllerAdmin
{
	/**
	 * Proxy for getModel.
	 * @since	1.6
	 */
	public function getModel($name = 'settings', $prefix = 'wimtvproModel')
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
	
	function save(){
		$app = &JFactory::getApplication();
		$params = JComponentHelper::getParams('com_wimtvpro');
		$username = $params->get('wimtv_username');
		$password = $params->get('wimtv_password');
		$basePath = $params->get('wimtv_basepath');
		$credential = $username . ":" . $password;
		//SAVE SKIN AND JWPLAYER
		
		if (!isset($_GET["credential"]) AND !isset($_GET["update"]) AND !isset($_GET["pack"])) {
		
			//Directory Upload Skin
			$directory  = JPATH_COMPONENT . DS . "uploads" . DS . "skin";
	
			//Import filesystem libraries. Perhaps not necessary, but does not hurt
			jimport('joomla.filesystem.file');
			$nameSkin = $_POST['nameSkin'];
			$height =$_POST['heightPreview'];
			$width = $_POST['widthPreview'];
			
			$file = $_FILES["uploadSkin"]['name'];
			$tmpfile =  $_FILES["uploadSkin"]['tmp_name'];
	
			$arrayFile = explode(".", $file);
			if (!empty($file)) {
				if ($arrayFile[1] != "zip") {
					JError::raiseWarning( 100,"This file isn't format correct for jwplayer's skin");
					$error ++;
				} else {
					if (filesize($tmpfile) > 10485760) {
						JError::raiseWarning( 100,"Uploaded file is " .  round(filesize($tmpfile) / 1048576, 2) . "Kb. It must be less than 10Mb.");
						echo '</strong></p></div>';
						$error ++;
					} else {
						if ( false === @move_uploaded_file( $tmpfile, $directory . "/" . $file) ) {
							JError::raiseWarning( 100,"Internal error " .  $directory . "/" .  $file);
							$error ++;
						}
						$params->set('wimtv_nameSkin',$arrayFile[0]);
					}
				}
			}else {
		    	$params->set('wimtv_nameSkin',$_POST['nameSkin']);
		    	JFactory::getApplication()->enqueueMessage('Skin update Successfully');
		    }
	
			
			$params->set('wimtv_heightPreview',$height);
			$params->set('wimtv_widthPreview',$width);
			
			// Get a new database query instance
			$db = JFactory::getDBO();
			$query = $db->getQuery(true);
			$params = JComponentHelper::getParams('com_wimtvpro');
			// Build the query
			$query->update('#__extensions AS a');
			$query->set('a.params = ' . $db->quote((string)$params));
			$query->where('a.element = "com_wimtvpro"');
			
			// Execute the query
			$db->setQuery($query);
			$db->query();
			
			$conf = JFactory::getConfig();
			//JError::raiseWarning( 100,"You must check if you event is public or private.");
			$options = array(
					'defaultgroup' => '_system',
					'cachebase' => $conf->get('cache_path', JPATH_SITE . '/cache')
			);
			
			$cache = JCache::getInstance('callback', $options);
			$cache->clean();
		} else {	
			
			if (isset($_GET["credential"])){
				$page = "&credential=1";
				$usernameNew =$_POST['wimtv_username'];
				$passwordNew = $_POST['wimtv_password'];
				
				$params->set('wimtv_username',$usernameNew);
				$params->set('wimtv_password',$passwordNew);
					
				// Get a new database query instance
				$db = JFactory::getDBO();
				$query = $db->getQuery(true);
				$params = JComponentHelper::getParams('com_wimtvpro');
				// Build the query
				$query->update('#__extensions AS a');
				$query->set('a.params = ' . $db->quote((string)$params));
				$query->where('a.element = "com_wimtvpro"');
					
				// Execute the query
				$db->setQuery($query);
				$db->query();
					
				$conf = JFactory::getConfig();
				//JError::raiseWarning( 100,"You must check if you event is public or private.");
				$options = array(
						'defaultgroup' => '_system',
						'cachebase' => $conf->get('cache_path', JPATH_SITE . '/cache')
				);
					
				$cache = JCache::getInstance('callback', $options);
				$cache->clean();
				JFactory::getApplication()->enqueueMessage('Credential update Successfully');
				
			}
			
			if (isset($_GET["update"])){
			
				$page = "&update=" . $_GET["update"];
				
				//UPDATE PROFILE
				foreach ($_POST as $key=>$value){
					if ($value=="")  unset($_POST[$key]);
					//$key = str_replace("Uri","URI",$key);
					$dati[$key] = $value;
					
				}
				$dati["dateOfBirth"]  = str_replace ("-","/",$dati["dateOfBirth"] );
				unset($dati['task']);
				unset($dati['token']);
				$jsonValue = json_encode($dati);
				$urlUpdate = $basePath . "profile";
				$ch = curl_init();
				//form_set_error("eee",var_dump($dati));
				curl_setopt($ch, CURLOPT_URL, $urlUpdate);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: application/json","Accept: application/json","Accept-Language:" . $_SERVER['HTTP_ACCEPT_LANGUAGE']));
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
				curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
				curl_setopt($ch, CURLOPT_USERPWD, $credential);
				curl_setopt($ch, CURLOPT_POST, TRUE);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonValue);
				$response = curl_exec($ch);
				$arrayjsonst = json_decode($response);
				curl_close($ch);
				if (isset($arrayjsonst->result) && ($arrayjsonst->result!="SUCCESS")) {
					$testoErrore = "";
					foreach ($arrayjsonst->messages as $message){
						$testoErrore .=  $message->field . " : " .  $message->message . "<br/>";
					}
					JError::raiseWarning( 100,$testoErrore);
								
				}else {
					JFactory::getApplication()->enqueueMessage($arrayjsonst->msg);
	
				}
			} 
			
			
		}
		
		$this->setRedirect('index.php?option=com_wimtvpro&view=settings' . $page , $message);
			
		
	}
	
	
	
}
