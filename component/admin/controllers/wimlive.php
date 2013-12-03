<?php


// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');
require_once ( JPATH_BASE . "/components/com_wimtvpro/includes/api/wimtv_api.php" );

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
				$paymentCode = apiGetUUID();
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
		
            $post = array("name"    => $name,
                          "url"     => $url,
                          "eventDate" => $giorno,
                          "paymentMode" => $typemode,
                          "eventHour" => $ora[0],
                          "eventMinute" => $ora[1],
                          "duration" => $duration,
                          "durationUnit" => "Minute",
                          "publicEvent" => $public,
                          "timezone" => $timelive,
                          "recordEvent" => $record);

			if ($_GET["cid"]!="")  
				$response = apiModifyLive($_GET['cid'], $post);
            else
			    $response = apiAddLive($post); //

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
		$input = JFactory::getApplication()->input;
		//var_dump ($input);
		$pks = $input->post->get('cid', array(), 'array');
		foreach ($pks as $id) {
	      $response = apiDeleteLive($id);
	      $message = $response;
		}
        //TODO: che cos'è $errorRemove??
	
		if ($errorRemove==0){
	
			//JFactory::getApplication()->enqueueMessage($message);
			
			$this->setRedirect('index.php?option=com_wimtvpro&view=wimlives', $message);
		} else {
			JError::raiseWarning( 100, "Error connection." );
			$this->setRedirect('index.php?option=com_wimtvpro&view=wimlive&layout=edit', $message);
			
		}
	
	
	}
	

}

