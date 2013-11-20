<?php


// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');

/**
 * WIMTVPRO MEDIA Controller
*/
class WimtvproControllerputvideos extends JControllerForm
{
	public function __construct($config = array())
	{
		$this->view_list = 'mymedias';
		parent::__construct($config);
	}
	
	public function getModel($name = 'putVideo', $prefix = 'wimtvproModel')
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
		
		//Retrieve file details from uploaded file, sent from upload form
		$file = JRequest::getVar('jform', null, 'files', 'array');	
		//Import filesystem libraries. Perhaps not necessary, but does not hurt
		jimport('joomla.filesystem.file');		

		$type = $_POST['type'];
		$coid =$_POST['coid'];
		
		
		switch ($type) {
		
			case "cc":
				$licenseType ="CREATIVE_COMMONS";
				$ccType = $_POST['cc_type'];
		
			break;
		
			case "ppv":
		
				$licenseType ="TEMPLATE_LICENSE";
				$paymentMode ="PAYPERVIEW";
				$pricePerView = $_POST["amount"] + "." + $_POST['amount_cent'];
				$pricePerViewCurrency = $_POST["currency"];
		
			break;
		
			case "free":
				
				$licenseType ="TEMPLATE_LICENSE";
				$paymentMode ="FREEOFCHARGE";
		
			break;
		
		}
	
		$app = &JFactory::getApplication();
		$params = JComponentHelper::getParams('com_wimtvpro');
		$username = $params->get('wimtv_username');
		$password = $params->get('wimtv_password');
		$basePath = $params->get('wimtv_basepath');
		$credential = $username . ":" . $password;

		$license_type = "";
		if ($licenseType!="")
			$license_type = "licenseType=" . $licenseType;
		$payment_mode = "";
		if ($paymentMode!="")
			$payment_mode = "&paymentMode=" . $paymentMode;
		$cc_type = "";
		if ($ccType!="")
			$cc_type= "&ccType=" . $ccType;
		$price_per_view  = "";
		if ($pricePerView!="")
			$price_per_view  = "&pricePerView=" . $pricePerView;
		$price_per_view_currency = "";
		if ($pricePerViewCurrency!="")
			$price_per_view_currency = "&pricePerViewCurrency=" . $pricePerViewCurrency;
		$post_field = $license_type . $payment_mode . $cc_type . $price_per_view . $price_per_view_currency;
		

		//Richiamo API  http://www.wim.tv/wimtv-webapp/rest/videos/{contentIdentifier}/showtime
		//curl -u {username}:{password} -d "license_type=TEMPLATE_LICENSE&paymentMode=PAYPERVIEW&pricePerView=50.00&pricePerViewCurrency=EUR" http://www.wim.tv/wimtv-webapp/rest/videos/{contentIdentifier}/showtime
		
		$url_post_public_wimtv = $basePath . "videos/" . $coid  . "/showtime";

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url_post_public_wimtv);
		curl_setopt($ch, CURLOPT_VERBOSE, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_USERPWD, $credential);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept-Language:' . $_SERVER['HTTP_ACCEPT_LANGUAGE']));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_field);
		$response = curl_exec($ch);

		
	      $state = "showtime";
	      $array_response = json_decode($response);

	      if ($array_response->result=="SUCCESS"){
	      
		     require_once ( JPATH_BASE . "/components/com_wimtvpro/includes/function.php" );
		     $urlVideosDetailWimtv = trim($params->get('wimtv_urlVideosDetailWimtv'));
		     $url_video = $basePath . $urlVideosDetailWimtv;
		     syncWimtvpro ($username,$credential,$url_video,"mymedias");
		     
		     //JFactory::getApplication()->redirect($link , $error, 'Redirect' );
		     $this->setRedirect('index.php?option=com_wimtvpro&view=mymedias', false);
		     	
		      
		 
			} else {
				$error = "";
				foreach ($array_response->messages as $message) {
					
					$error .= $message->field . ":" . $message->message . "<br/>";
					
				}
				
				JError::raiseWarning( 100, $error  );
				$this->setRedirect('index.php?option=com_wimtvpro&view=putVideos&layout=edit&coid=' . $coid  . '&put=' . $type, false);
					
			}

			
			
	    curl_close($ch);

		
	}
	

}
