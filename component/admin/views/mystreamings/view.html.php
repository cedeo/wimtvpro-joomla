<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.view' );
jimport( 'joomla.html.pagination' );
require_once ( JPATH_BASE . "/components/com_wimtvpro/includes/function.php" );
class WimtvproViewmystreamings extends JView
{
	function display($tpl = null)
	{
		// Get data from the model
		$items = $this->get('Items');
		$pagination = $this->get('Pagination');
		$state            = $this->get('State');
		$this->sortDirection = $state->get('list.direction');
		$this->sortColumn = $state->get('list.ordering');
		
		$extension = 'com_wimtvpro';
		$lang = JFactory::getLanguage();
		$source = JPATH_ADMINISTRATOR . '/components/' . $extension;
		$lang->load("$extension.sys", JPATH_ADMINISTRATOR, null, false, false)
		||    $lang->load("$extension.sys", $source, null, false, false)
		||    $lang->load("$extension.sys", JPATH_ADMINISTRATOR, $lang->getDefault(), false, false)
		||    $lang->load("$extension.sys", $source, $lang->getDefault(), false, false);
		
		$document = JFactory::getDocument();
		$document->addStyleSheet(JURI::base() . "components/com_wimtvpro/assets/css/wimtvpro.css");
		$document->addScript(JURI::base() . "components/com_wimtvpro/assets/js/jquery-2.0.2.min.js");
		
		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}
		// Assign data to the view
		$this->items = $items;
		$this->pagination = $pagination;

		// Set the toolbar
		$this->addToolBar();

		/*$input = JFactory::getApplication()->input;
		 $view = $input->getCmd('view', '');
		WimtvproHelper::addSubmenu($view);*/

		// Display the template
		parent::display($tpl);

		// Set the document
		$this->setDocument();
	}


	/* Setting the toolbar
	 */
	protected function addToolBar()
	{
		
		// Toolbar
		$params = JComponentHelper::getParams('com_wimtvpro');
		$username = $params->get('wimtv_username');
		$password = $params->get('wimtv_password');
		$credential = $username . ":" . $password;
		JToolBarHelper::title( JText::_('WimTVPro') . ": " .  JText::_( "COM_WIMTVPRO_TITLE_STREAMING" ), 'wimtv' );
		
		if (($username!="username" && $password!="password") && ($username!="" && $password!="")){
			
			JToolBarHelper::custom('mystreamings.sync', 'sync', 'assets/images/sync.png', JText::_("COM_WIMTVPRO_SYNC"), false);
			JToolBarHelper::divider();
			//JToolBarHelper::custom('mymedia.download', 'download', 'assets/images/download.png', JText::_("Download"), true); //Download a video
			
		}else
			JToolBarHelper::preferences('com_wimtvpro');
	
	}
	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument()
	{

		$params = JComponentHelper::getParams('com_wimtvpro');
		$username = $params->get('wimtv_username');
		$password = $params->get('wimtv_password');
		$credential = $username . ":" . $password;
		$skinName = $params->get('wimtv_nameSkin');
		$basePath = $params->get('wimtv_basepath');
		$document = JFactory::getDocument();		
		$document->setTitle(JText::_('COM_WIMTVPRO_ADMINISTRATION'));
		$document->addStyleSheet(JURI::base() . "components/com_wimtvpro/assets/css/wimtvpro.css");
		$document->addScript(JURI::base() . "components/com_wimtvpro/assets/js/jquery-2.0.2.min.js");
		$document->addScript(JURI::base() . "components/com_wimtvpro/assets/js/wimtvpro.js");
		if (isset($_GET["inserInto"])) {
			if ($skinName!="") {
				$directorySkin  = JURI::base() . "administrator/components/com_wimtvpro/uploads/skin";
				$directorySkin = $directorySkin   . "/" . $skinName . ".zip";
			}
			
			$document->addScriptDeclaration('
				
				jQuery(document).ready(function() {
 
						jQuery(".iframeClicked").click(function(){
								id = jQuery(this).attr("id");
								jQuery("input").each(function(){
									if (jQuery(this).attr("id")=="width_" + id){
										width = jQuery(this).val();
									}
									if (jQuery(this).attr("id")=="height_" + id){
										height = jQuery(this).val();
									}
									if (jQuery(this).attr("id")=="iframe_" + id){
										iframe = jQuery(this).val();
									}
								})
								jQuery.ajax({
									context: this,
									url : "components/com_wimtvpro/includes/script.php",
									type: "GET",
									data: "namefunction=createIframe&basePath=' . $basePath . '&directory=' . $directorySkin .  '&credential=' . $credential . '&id=" +id + "&width=" + width + "&height=" + height , 
					
									success: function(response) {
										window.parent.jInsertEditorText(response,"' .  $_GET["e_name"] . '");
										window.parent.SqueezeBox.close();	
								},
								error: function(request,error) {
									alert(request.responseText);
								}	
							});

				
						})
				 
				});
								
			 ');
		
		}
			
		
		
		
	}

}
