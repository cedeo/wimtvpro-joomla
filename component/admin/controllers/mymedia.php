<?php


// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');

/**
 * WIMTVPRO MEDIA Controller
*/
class WimtvproControllermymedia extends JControllerForm
{
	public function __construct($config = array())
	{
		$this->view_list = 'mymedias';
		parent::__construct($config);
	}
	
	public function getModel($name = 'mymedia', $prefix = 'wimtvproModel')
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
		$filename = $file['tmp_name']["videofile"];
		$urlfile = $file['tmp_name']["videofile"];
		$jform = JRequest::getVar('jform');
		$titlefile = $jform['titlefile'];
		$descriptionfile =$jform['descriptionfile'];
		$video_category = $jform['videocategory'];

		// Required
		if (strlen(trim($titlefile))==0) {
			$error ++;
			JError::raiseWarning( 100, 'You must write a title' );
		}
		
		if ((strlen(trim($urlfile))>0) && ($error==0)) {

			$unique_temp_filename = $directory .  "/" . time() . '.' . preg_replace('/.*?\//', '',"tmp");
			$unique_temp_filename = str_replace("\\" , "/" , $unique_temp_filename);
			if (@move_uploaded_file( $urlfile , $unique_temp_filename)) {
				//echo "copiato";
			}else{
				JError::raiseWarning( 100, "Video isn't copy" );
			}
		
			$table_name = '#__wimtvpro_video';
		
			//UPLOAD VIDEO INTO WIMTV
			set_time_limit(0);
			//connect at API for upload video to wimtv
			$ch = curl_init();
			$url_upload = $basePathWimtv . 'videos';
		
			curl_setopt($ch, CURLOPT_URL, $url_upload);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: multipart/form-data"));
			curl_setopt($ch, CURLOPT_VERBOSE, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($ch, CURLOPT_USERPWD, $credential);
			curl_setopt($ch, CURLOPT_POST, TRUE);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			//add category/ies (if exist)
			$category_tmp = array();
			$subcategory_tmp = array();
				
			$post= array("file" => "@" . $unique_temp_filename,"title" => $titlefile,"description" => $descriptionfile);
			if (isset($video_category)) {
				$id=0;
				foreach ($video_category as $cat) {
					$subcat = explode("|", $cat);
					$post['category[' . $id . ']'] = $subcat[0];
					$post['subcategory[' . $id . ']'] = $subcat[1];
					$id++;
				}
			}
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
			$response = curl_exec($ch);
			curl_close($ch);
			$arrayjsonst = json_decode($response);
			//var_dump ($arrayjsonst);
			if (isset($arrayjsonst->contentIdentifier)) {

				//JFactory::getApplication()->enqueueMessage('Upload successfully');
		
				// Create and populate an object.
				$insert_video = new stdClass();
				$insert_video->uid = $username;
				$insert_video->contentidentifier=$arrayjsonst->contentIdentifier;
				$insert_video->mytimestamp=time();
				$insert_video->position=0;
				$insert_video->state='';
				$insert_video->viewVideoModule='3';
				$insert_video->status = 'OWNED|'  . $filename;
				$insert_video->acquiredIdentifier = '';
				$insert_video->urlThumbs = '';
				$insert_video->category =  '';
				$insert_video->urlPlay =  '';
				$insert_video->title =  $titlefile;
				$insert_video->duration = '';
				$insert_video->showtimeidentifier = '';
		
		
				try {
					// Insert the object into the user profile table.
					$result = JFactory::getDbo()->insertObject('#__wimtvpro_videos', $insert_video);
					
					//Redirect MyMedia
					$link = JRoute::_("index.php?option=com_wimtvpro&view=mymedias");
					JFactory::getApplication()->redirect($link , 'Upload successfully', 'Redirect' );

					
				} catch (Exception $e) {
					throw new Exception($e);
				}
				
				unlink($unique_temp_filename);
				
		
			}
				
			else{
				$error ++;
				JError::raiseWarning( 100, "Upload error" );
		
			}
				
		} else {
			$error ++;

			JError::raiseWarning( 100, 'You must upload a file' );
		}
		
	}
	

}
