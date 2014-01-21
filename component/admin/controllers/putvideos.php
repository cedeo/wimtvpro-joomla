<?php


// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');
require_once ( JPATH_BASE . "/components/com_wimtvpro/includes/api/wimtv_api.php" );


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
		//Retrieve file details from uploaded file, sent from upload form
		$file = JRequest::getVar('jform', null, 'files', 'array');	
		//Import filesystem libraries. Perhaps not necessary, but does not hurt
		jimport('joomla.filesystem.file');		

		$type = $_POST['type'];
		$coid = $_POST['coid'];
        $video = json_decode(apiGetDetailsVideo($coid));
        $status = $video->status;

		
		switch ($type) {
		
			case "cc":
				$licenseType ="CREATIVE_COMMONS";
				$ccType = $_POST['cc_type'];
		
			break;
		
			case "ppv":
		
				$licenseType ="TEMPLATE_LICENSE";
				$paymentMode ="PAYPERVIEW";
				$pricePerView = $_POST["amount"] . "." . $_POST['amount_cent'];
				$pricePerViewCurrency = $_POST["currency"];
		
			break;
		
			case "free":
				
				$licenseType ="TEMPLATE_LICENSE";
				$paymentMode ="FREEOFCHARGE";
		
			break;
		
		}

        $post = array();
		if ($licenseType!="")
			$post["licenseType"] = $licenseType;
		if ($paymentMode!="")
			$post["paymentMode"] = $paymentMode;
		if ($ccType!="")
			$post["ccType"] = $ccType;
		if ($pricePerView!="")
			$post["pricePerView"]  = $pricePerView;
		if ($pricePerViewCurrency!="")
			$post["pricePerViewCurrency"] = $pricePerViewCurrency;

        $post = array("licenseType" => $licenseType,
                      "paymentMode" => $paymentMode,
                      "ccType" => $ccType,
                      "pricePerView" => $pricePerView,
                      "pricePerViewCurrency" => $pricePerViewCurrency);
        if ($status != "OWNED") {
            $acquid = $video->relationId;
            $response = apiPublishAcquiredOnShowtime($coid, $acquid, $post);
        } else {
            $response = apiPublishOnShowtime($coid, $post);
        }

		
        $state = "showtime";
        $array_response = json_decode($response);

        if ($array_response->result=="SUCCESS") {
            $this->setRedirect('index.php?option=com_wimtvpro&view=mymedias&sync=true', false);
        } else {
            $error = "";
            foreach ($array_response->messages as $message) {
                $error .= $message->field . ":" . $message->message . "<br/>";
            }
            JError::raiseWarning( 100, $error  );
            $this->setRedirect('index.php?option=com_wimtvpro&view=putVideos&layout=edit&coid=' . $coid  . '&put=' . $type, false);
        }
		
	}
	

}
