<?php


// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');

/**
 * WIMTVPRO MEDIA Controller
*/
class WimtvproControllerwimlive extends JControllerForm
{
	public function __construct($config = array())
	{
		$this->view_list = 'wimlives';
		parent::__construct($config);
	}
	
	public function getModel($name = 'wimlive', $prefix = 'wimtvproModel')
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
	
	
	public function save()
	{
		
		$app = &JFactory::getApplication();
		$params = JComponentHelper::getParams('com_wimtvpro');
		$basePathWimtv = $params->get('wimtv_basepath');
		$username = $params->get('wimtv_username');
		$password = $params->get('wimtv_password');
		$credential = $username . ":" . $password;
		
		$directory  = JPATH_COMPONENT . DS . "uploads" . DS;
		$error = 0;
		
		//Retrieve file details from uploaded file, sent from upload form
		$file = JRequest::getVar('jform', null, 'files', 'array');
		//Import filesystem libraries. Perhaps not necessary, but does not hurt
		jimport('joomla.filesystem.file');

		$jform = JRequest::getVar('jform');
		$name = $jform['name'];
		$payperview =$jform['payperview'];
		$url = $jform['Url'];
		$public = $jform['Public'];
		$record = $jform['Record'];
		$giorno = $jform['Giorno'];
		$orain = $jform['Ora'];
		$durationin = $jform['Duration'];
		$timelive = $jform['timelivejs'];
		$giorno  = str_replace ("-","/",$jform['Giorno']);
		
		if (strlen(trim($name))==0) {
			JError::raiseWarning( 100,"You must write a wimlive's name." );
			$error ++;
		}
		if (strlen(trim($payperview))==0) {
			JError::raiseWarning( 100,"You must write a price for your event (or free of charge)." );
			$error ++;
		}
		if (strlen(trim($url))==0) {
			JError::raiseWarning( 100,"You must write a url.");
			$error ++;
		}
		if (strlen(trim($giorno))==0) {
			JError::raiseWarning( 100,"You must write a day of your event.");
			$error ++;
		}
		if (strlen(trim($orain))==0) {
			JError::raiseWarning( 100,"You must write a hour of your event.");
			$error ++;
		}
		if (strlen(trim($durationin))==0) {
			JError::raiseWarning( 100,"You must write a duration of your event.");
			$error ++;
		}
		
		if (!isset($public)) {
			JError::raiseWarning( 100,"You must check if you event is public or private.");
			$error ++;
		}
		
		if ($error==0) {
		
			if ($payperview=="0")
				$typemode = "FREEOFCHARGE";
			else {
				 
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $basePathWimtv . "uuid");
		
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
				$paymentCode= curl_exec($ch);
				curl_close($ch);
				 
				$typemode = "PAYPERVIEW&pricePerView=" . $payperview . "&ccy=EUR&paymentCode=" . $paymentCode;
			}

			if ($orain!="") $ora = explode(":", $orain);
			else {
				$ora[0] = "";
				$ora[1] = "";
			}
			if ($durationin!="") {
				$separe_duration = explode(":", $durationin);
				$duration = ($separe_duration[0] * 60) + $separe_duration[1];
			}
			else $duration = 0;

			$userpeer = $username;
			$fields_string = "name=" . $name . "&url=" . $url . "&eventDate=" . $giorno . "&paymentMode=" . $typemode;
			$fields_string .= "&eventHour=" . $ora[0] . "&eventMinute=" . $ora[1] . "&duration=" . $duration . "&durationUnit=Minute&publicEvent=" . $public;
		
			$fields_string .= "&timezone=" . $timelive ;
		
			$fields_string .= "&recordEvent=" . $record;
		

			$url_live =  $basePathWimtv . "liveStream/" . $userpeer . "/" . $userpeer . "/hosts";
			if ($_GET["cid"]!="")  
				$url_live .= "/" . $_GET['cid'];
			
			$url_live .= "?timezone=" . $timelive ;
			
			echo $url_live;
			echo $credential;
			echo $fields_string;
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url_live);
	
			curl_setopt($ch, CURLOPT_USERPWD, $credential);
			curl_setopt($ch, CURLOPT_POST, TRUE);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			$response = curl_exec($ch);
			curl_close($ch);
			if ($response!=""){
				$message = json_decode($response);
			
				$result = $message->{"result"};
				if ($result=="SUCCESS") {
					
					//Redirect Wimlive
					$link = "index.php?option=com_wimtvpro&view=wimlives";
					JFactory::getApplication()->enqueueMessage('Live Successfully');
					$this->setRedirect('index.php?option=com_wimtvpro&view=wimlives', $message);
				}
				else {
					$formset_error = "";
					foreach ($message->messages as $value) {
						if ($value->message!="")
							$formset_error .= $value->field . "=" . $value->message;
					}
					$app->enqueueMessage("API wimtvpro error: " . $formset_error . "</strong></p>" . $result, 'error');
					$this->setRedirect('index.php?option=com_wimtvpro&view=wimlive&layout=edit',false);
					
				}
				
			
			} 
		
		
		} else {
			
			$this->setRedirect('index.php?option=com_wimtvpro&view=wimlive&layout=edit');
			
		}
	
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
		$url_live =  $basePath . "liveStream/" . $username . "/" . $username . "/hosts";
		foreach ($pks as $id) {
	
			$url_live .= "/" .  $id;
	      $ch = curl_init();
	      curl_setopt($ch, CURLOPT_URL, $url_live);
	      curl_setopt($ch, CURLOPT_VERBOSE, 0);
	      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
	      curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	      curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	      curl_setopt($ch, CURLOPT_USERPWD, $credential);
	      curl_setopt($ch, CURLOPT_POST, TRUE);
	      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	      $response = curl_exec($ch);
	      $message = $response;
	      curl_close($ch); 
	
		}
	
		if ($errorRemove==0){
	
			//JFactory::getApplication()->enqueueMessage($message);
			
			$this->setRedirect('index.php?option=com_wimtvpro&view=wimlives', $message);
		} else {
			JError::raiseWarning( 100, "Error connection." );
			$this->setRedirect('index.php?option=com_wimtvpro&view=wimlive&layout=edit', $message);
			
		}
	
	
	}
	

}

