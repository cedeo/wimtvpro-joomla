<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelform library
jimport('joomla.application.component.modeladmin');

/**
 * HelloWorld Model
*/
class WimtvproModelwimlive extends JModelAdmin
{
	
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_wimtvpro.wimlive', 'wimlive', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form))
		{
			return false;
		}

		
		return $form;
	}
	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.6
	 */
	
	public function getItem($pk = null){
		$app = &JFactory::getApplication();
		$params = JComponentHelper::getParams('com_wimtvpro');
		$basePathWimtv = $params->get('wimtv_basepath');
		$username = $params->get('wimtv_username');
		$password = $params->get('wimtv_password');
		$credential = $username . ":" . $password;
		
		$userpeer = $username;
		
		if (isset($_GET['cid'])) {
		
			$id =  $_GET['cid'];
			
			/*$url_live_embedded = $basePathWimtv . "liveStream/" . $userpeer . "/" . $userpeer . "/hosts/" . $id;
			
			$ch_embedded= curl_init();
	
			
			curl_setopt($ch_embedded, CURLOPT_URL, $url_live_embedded);
			curl_setopt($ch_embedded, CURLOPT_VERBOSE, 0);
			
			curl_setopt($ch_embedded, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch_embedded, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($ch_embedded, CURLOPT_USERPWD, $credential);
			curl_setopt($ch_embedded, CURLOPT_SSL_VERIFYPEER, FALSE);*/
			$embedded = apiEmbeddedLive($id); //curl_exec($ch_embedded);

			$arrayjson_live = json_decode($embedded);

			$array_data["name"] = $arrayjson_live->name;
			$array_data["Url"] = $arrayjson_live->url;
			
			if ($arrayjson_live->paymentMode) $payment_mode="0";
			else {
				$payment_mode =  $arrayjson_live->pricePerView;
			}
				
			$array_data["payperview"] = $payment_mode;
			$array_data["Public"] = $arrayjson_live->publicEvent ? 'true' : 'false';
			$array_data["Record"] = $arrayjson_live->recordEvent ? 'true' : 'false';


			$array_data["Giorno"] = str_replace ("/","-",$arrayjson_live->eventDate);

			if (intval($arrayjson_live ->eventMinute)<10) $arrayjson_live ->eventMinute = "0" . $arrayjson_live ->eventMinute;
			$oraMin = $arrayjson_live ->eventHour . ":" . $arrayjson_live ->eventMinute;

			$array_data["Ora"] = $oraMin;
			if ( $arrayjson_live -> durationUnit=="Minute") {
				$tempo = $arrayjson_live->duration;
				$ore = floor($tempo / 60);
				$minuti = $tempo % 60;
				$durata = $ore . ":";
				if ($minuti<10)
					$durata .= "0";
				$durata .= $minuti;
			} else 
				$durata = $arrayjson_live->duration;
				
			
			$array_data["Duration"] = $durata;
		
			
			return $array_data;
		}
	}
	
	protected function loadFormData()
	{
		$data = JFactory::getApplication()->getUserState('com_wimtvpro.edit.wimlive.data', array());
		
		// Check the session for previously entered form data.
		if (isset($_GET['cid'])) {
			
			if (empty($data))
			{
				$data = $this->getItem();
			}
	
		}
		return $data;
	}
}