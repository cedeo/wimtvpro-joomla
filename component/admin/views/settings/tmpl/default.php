<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
// load tooltip behavior
JHtml::_('behavior.tooltip');

$app = &JFactory::getApplication();
$params = JComponentHelper::getParams('com_wimtvpro');
$username = $params->get('wimtv_username');
$password = $params->get('wimtv_password');
$basePath = $params->get('wimtv_basepath');
$credential = $username . ":" . $password;
$height = $params->get('wimtv_heightPreview');
$width = $params->get('wimtv_widthPreview');
$skin = $params->get('wimtv_nameSkin');
$view_page = wimtvpro_alert_reg($username,$password);

$directory  = JPATH_COMPONENT . DS . "uploads" . DS . "skin";

if (!is_dir($directory)) {
	$directory_create = mkdir(JPATH_COMPONENT . DS . "uploads" . DS . "skin");
}

if (is_dir($directory)) {
	if ($directory_handle = opendir($directory)) {
		//Read directory for skin JWPLAYER
		$elencoSkin[""] = "-- Base Skin --";
		while (($file = readdir($directory_handle)) !== FALSE) {
			if ((!is_dir($file)) && ($file!=".") && ($file!="..")) {
				$explodeFile = explode("." , $file);
				if ($explodeFile[1]=="zip")
					$elencoSkin[$explodeFile[0]] = $explodeFile[0];
			}
		}
		closedir($directory_handle);
	}
	//Create option select form Skin
	$createSelect = "";
	foreach ($elencoSkin as $key => $value){
		$createSelect .= "<option value='" . $key . "'";
		if ($value==$skin)  $createSelect .= " selected='selected' ";
		$createSelect .= ">" . $value . "</option>";
	}
}
$submenu = "<div id='submenu-box'><div class='m'><ul id='submenu'>";
if ($view_page){
	$submenu .= "<li><a href='index.php?option=com_wimtvpro&view=settings' class='";
	if (!isset($_GET["credential"]) AND !isset($_GET["update"]) AND !isset($_GET["pack"])) $submenu .= "active";
	$submenu .= " config'>Skin</a>";
	$submenu .= "<li><a href='index.php?option=com_wimtvpro&view=settings&pack=1' class='";
	if ($_GET["pack"]=="1") $submenu .= "active";
	$submenu .= " pricing'>Pricing</a>";
	$submenu .= "<li><a href='index.php?option=com_wimtvpro&view=settings&update=1' class='";
	if ($_GET["update"]=="1") $submenu .= "active";
	$submenu .= " payment'>Payment</a>";
	$submenu .= "<li><a href='index.php?option=com_wimtvpro&view=settings&update=2' class='";
	if ($_GET["update"]=="2") $submenu .= "active";
	$submenu .= " live'>WimLive Configuration</a>";
	$submenu .= "<li><a href='index.php?option=com_wimtvpro&view=settings&update=3' class='";
	if ($_GET["update"]=="3") $submenu .= "active";
	$submenu .= " user'>Update Personal Info</a>";
	$submenu .= "<li><a href='index.php?option=com_wimtvpro&view=settings&update=4' class='";
	if ($_GET["update"]=="4") $submenu .= "active";
	$submenu .= " other'>Features</a> ";
	$submenu .= "<li><a href='index.php?option=com_wimtvpro&view=settings&credential=1' class='";
	if ($_GET["credential"]=="1") $submenu .= "active";
	$submenu .= " other'>Credential</a> ";
}
$submenu .= "</ul><div class='clr'></div></div></div>";
echo $submenu;

if ($view_page){

	//SKIN
	if (!isset($_GET["credential"]) AND !isset($_GET["update"]) AND !isset($_GET["pack"])) {
	
	?>
		<form action="<?php echo JRoute::_('index.php?option=com_wimtvpro&view=settings'); ?>"
			method="post" name="adminForm" enctype="multipart/form-data">
			
			<fieldset class="adminform">
				<legend>Upload and/or choose your skin player into <a target='new' href='http://www.longtailvideo.com/addons/skins'>page Jwplayer</a> for your videos</legend>
				
				<ul class="adminformlist">
					<li><label>Name Skin</label>
					<select id="edit-nameskin" name="nameSkin" class="form-select"><?php echo $createSelect; ?></select>
					</li>
					
					<li>
					<label for="edit-uploadskin"> or upload new<br/>skin player </label>
					<input type="file" id="edit-uploadskin" name="uploadSkin" size="100" class="form-file" />
							
					<div class="empty"></div>Only zip. Save into a public URL <br/>
						For running the skin selected, copy the file <a href='http://plugins.longtailvideo.com/crossdomain.xml' target='_new'>crossdomain.xml</a> to the root directory (e.g. http://www.mysite.it). You can do it all from your FTP program (e.g. FileZila, Classic FTP, etc).
						So open up your FTP client program. First, identify your root directory. This is the folder titled or beginning with www -- and this is where you ultimately need to move that pesky crossdomain.xml. Now all you have to do is find it.
				</li>
				</ul>	
			</fieldset>
			
			<fieldset class="adminform">
				<legend>Dimensions of player for your videos </legend>
				
				<ul class="adminformlist">
					<li><label for="edit-heightpreview">Height (default: 280) </label>
					<input type="text" id="edit-heightpreview" name="heightPreview" value="<?php echo $height;?>" size="20" maxlength="200" class="form-text" />
					</li>
					<li><label for="edit-widthpreview">Width (default: 500) </label>
					<input type="text" id="edit-widthpreview" name="widthPreview" value="<?php echo $width;?>" size="20" maxlength="200" class="form-text" />
					</li>
					
				</ul>
			
			</fieldset>
			
			<input type="hidden" name="task" value="settings.edit" />
				<?php echo JHtml::_('form.token'); ?>
			
		</form>


<?php 
	
	} else {

		/*$urlUpdate = $basePath . "profile";

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $urlUpdate);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_USERPWD, $credential);*/
		$response = apiGetProfile(); // curl_exec($ch);
		$dati = json_decode($response, true);
		switch ($_GET['update']){
	
		case "1": //Payment
	
			
			echo '<div class="clear"></div>
				  <p>If you wish to make financial transactions on Wim.tv (buy video, sell video content or watch on pay per view), you must complete the following fields. You can choose to store your information now or do it later by clicking the settings button from your personal page.</p>';
			echo '
				
				  <form action="index.php?option=com_wimtvpro&view=settings&update=1" method="post" name="adminForm" enctype="multipart/form-data">
					 <fieldset class="adminform">
							<legend>PayPal</legend>
							<ul class="adminformlist">
								<li><label for="paypalEmail">Paypal Email</label>
									<input type="text" id="edit-paypalEmail" name="paypalEmail" value="' . $dati['paypalEmail'] . '" size="100" maxlength="100"/>
								</li>
							</ul>
					</fieldset>
				
					 <fieldset class="adminform">
							<legend>Tax Info</legend>
							<ul class="adminformlist">
								<li><label for="taxCode">Tax Code</label>
								<input type="text" id="edit-taxCode" name="taxCode" value="' . $dati['taxCode'] . '" size="80" maxlength="20"/>
								</li>
								<li><label for="vatCode">or Vat Code</label>
								<input type="text" id="edit-vatCode" name="vatCode" value="' . $dati['vatCode'] . '" size="80" maxlength="20"/>
								</li>
						</ul>
					</fieldset>
	
					 <fieldset class="adminform">
							<legend>Billing Address</legend>
							<ul class="adminformlist">
								<li><label for="billingAddress[street]">Street</label>
								<input type="text" id="edit-billingAddressStreet" name="billingAddress[street]" value="' . $dati['billingAddress']['street'] . '" size="100" maxlength="100"/>
								</li>
								<li><label for="billingAddress[city]">City</label>
								<input type="text" id="edit-billingAddressCity" name="billingAddress[city]" value="' . $dati['billingAddress']['city'] . '" size="100" maxlength="100"/>
								</li>
	
								<li><label for="billingAddress[state]">State</label>
								<input type="text" id="edit-billingAddressCity" name="billingAddress[state]" value="' . $dati['billingAddress']['state'] . '" size="100" maxlength="100"/>
								</li>
					
								<li><label for="billingAddress[zipCode]">Zip Code</label>
								<input type="text" id="edit-billingAddressCity" name="billingAddress[zipCode]" value="' . $dati['billingAddress']['zipCode'] . '" size="100" maxlength="100"/>
								</li>
						</ul>
					</fieldset>';
	
			echo '<input type="hidden" name="task" value="settings.edit" />';
			JHtml::_('form.token'); 

	
	
			echo '</form>';
	
		
			break;
				
		case "2": //Live

			if (!isset($dati['liveStreamPwd'])) $dati['liveStreamPwd']= "";
			if ($dati['liveStreamPwd']=="null") $dati['liveStreamPwd']= "";
	
			echo '<div class="clear"></div>
				  <p>In this section you can enable the more functional live streaming settings for your needs. Choose between "Live streaming" to stream your own events, or use the features reserved for event Resellers and event Organizers to sell and organize live events.</p>';
			echo '
				
				  <script>
				
				  	jQuery(document).ready(function() {
				 
				    	jQuery("#edit-liveStreamEnabled,#edit-eventResellerEnabled,#edit-eventOrganizerEnabled").click(
	
				    	function() {
				    		var name = jQuery(this).attr("name");
				    		if (jQuery(this).attr("checked")=="checked") {
				    			jQuery("." + name).remove();
				    		}
				    		else {
				  
				    			jQuery("<input>").attr({
								    type: "hidden",
								    value: "false",
								    name: name ,
								    class: name ,
								}).appendTo(".hidden_value");
	
				    		}
				    	})
				 
				    });
				
				  </script>
				
				  <form action="index.php?option=com_wimtvpro&view=settings&update=2" method="post" name="adminForm" enctype="multipart/form-data">
					 <fieldset class="adminform">
								<legend>WimLive</legend>
						<ul class="adminformlist">
								<li><label for="liveStreamEnabled">Live streaming</label>
									<input type="checkbox" id="edit-liveStreamEnabled" name="liveStreamEnabled" value="true"';
									if (strtoupper($dati['liveStreamEnabled'])=="TRUE") echo ' checked="checked"';
									echo  '/>
									  <div class="empty"></div>Enables the feature that allows you to broadcast your live streaming events with WimTV. 
								</li>
								<li><label for="liveStreamPwd">Password</label></th>
									<input type="password" id="edit-liveStreamPwd" name="liveStreamPwd" value="' . $dati['liveStreamPwd'] .  '"/>
									<div class="empty"></div>This password is required for the live streaming 
									
								</li>
	
								<li><label for="eventResellerEnabled">Live stream events resale</label></li>
									
									  <input type="checkbox" id="edit-eventResellerEnabled" name="eventResellerEnabled" value="true"
									  ';
									if (strtoupper($dati['eventResellerEnabled'])=="TRUE") echo ' checked="checked"';
									echo '
									  /><div class="empty"></div> Enables you to resell the streaming of live events organized bu other Web TVs. 
									
								</li>
					
								<li><label for="eventOrganizerEnabled">Live stream<br/>events organizing</label></th>
									
									  <input type="checkbox" id="edit-eventOrganizerEnabled" name="eventOrganizerEnabled" value="true"
									  ';
										if (strtoupper($dati['eventOrganizerEnabled'])=="TRUE") echo ' checked="checked"';
										echo '
									  />
									  <div class="empty"></div> Enables the feature that allows you to organize live streaming events. 
								</li>
							</ul>
						</fieldset>';
	
			echo '<input type="hidden" name="task" value="settings.edit" />';
			JHtml::_('form.token');
			echo "</form>";
	
				
			//"liveStreamPwd": "-- pwd per il live di wim.tv --",
			//"liveStreamEnabled": "-- abilita live true|false --"
			//eventResellerEnabled": "-- abilita event reselling true|false --",
			//"eventOrganizerEnabled": "-- abilita event organizing true|false --",
				
			break;
				
		case "3": //Update personal information
			
				
			 echo '<form action="index.php?option=com_wimtvpro&view=settings&update=3" method="post" name="adminForm" enctype="multipart/form-data">
					 <fieldset class="adminform"><legend>Personal Information</legend>';

				
			echo '
				  
					<ul class="adminformlist">
								<li>
								<label for="edit-name">Name<span class="form-required" title="">*</span></label>
								<input type="text" id="edit-name" name="name" value="' . $dati['name'] . '" size="40" maxlength="200"/>
						    	</li>
								<li><label for="edit-Surname">Surname<span class="form-required" title="">*</span></label>
									<input type="text" id="edit-Surname" name="surname" value="' . $dati['surname'] . '" size="40" maxlength="200"/></li>
								<li><label for="edit-Email">Email<span class="form-required" title="">*</span></label>
								<input type="text" id="edit-email" name="email" value="' . $dati['email'] . '" size="80" maxlength="200"/></li>
						
								<li><label for="sex">Gender<span class="form-required" title="">*</span></label>';
							
										$arr = array(
												JHTML::_('select.option', 'M', JText::_('M') ),
												JHTML::_('select.option', 'F', JText::_('F') ),
								
										);
										 echo JHTML::_('select.genericlist', $arr, 'sex', null, 'value', 'text', $dati['sex']);

								echo '</li>
	
								<li><label for="dateOfBirth">Date Of Birth</label>';
								//convert date		
								list($d, $m, $y) = explode('/',$dati["dateOfBirth"]);
								if ($y>date("y")) $y= "19" . $y;
								else  $y= "20" . $y;
								echo JHTML::calendar($d ."-" . $m ."-" . $y,'dateOfBirth','edit-giorno','%d-%m-%Y');//%Y-%m-%d

								echo '</li>
	
	
				
					</fieldset>';
									
	
			echo '
				<fieldset class="adminform">
					<legend>Your social networks</legend>
				
					<ul class="adminformlist">
								<li>
								<label for="facebookUri">Facebook Url</label>
								<input  type="text"  id="edit-facebookURI" name="facebookUri" value="' . $dati['facebookURI'] . '" size="100" maxlength="100">
								</li>
							<li><label for="twitterUri">Twitter Url</label></th>
								<input  type="text"  id="edit-twitterURI" name="twitterUri" value="' . $dati['twitterURI'] . '" size="100" maxlength="100">
								</li>
	
	
							<li><label for="linkedInUri">LinkedIn Url</label>
								<input  type="text"  id="edit-LinkedInUri" name="linkedInUri" value="' . $dati['linkedInURI'] . '" size="100" maxlength="100">
								</li>
					</ul>
				    		</fieldset>';
				echo '<input type="hidden" name="task" value="settings.edit" />';
			JHtml::_('form.token');
			echo "</form>";
	
				
			break;
				
				
		case "4": //Features
			echo '
			        <script type="text/javascript">
			  		jQuery(document).ready(function(){
			  		  jQuery( "#hidePublicShowtimeVideos" ).change( function(){
	
			          	if  (jQuery(this).val()=="false") {
					      	jQuery("#viewPage").fadeIn();
					      }else{
					      	jQuery("#viewPage").fadeOut();
	
					      }
	
			          });
	
			  		});
			  		</script>
			     	';
			
			
				
				
			echo '
				 <form action="index.php?option=com_wimtvpro&view=settings&update=4" method="post" name="adminForm" enctype="multipart/form-data">
				<fieldset>
									  		<ul class="adminformlist">
						<li><label for="edit-name">Index and show public videos into WimTv\'s site</label>
							';
						
						$arr = array(
								JHTML::_('select.option', 'false', JText::_('Yes') ),
								JHTML::_('select.option', 'true', JText::_('No') ),
						
						);
						echo JHTML::_('select.genericlist', $arr, 'hidePublicShowtimeVideos', null, 'value', 'text', $dati['hidePublicShowtimeVideos']);
							
			
	
					echo '</li></ul>
				
				</fieldset>
					 <fieldset id="viewPage"';
				
			if ( $dati['hidePublicShowtimeVideos']=="true") echo ' style="display:none; "';
				
			echo ' >	<ul class="adminformlist">
				
						<legend>Page into WimTv</legend>
	
								<ul class="adminformlist">
								<li><label for="pageName">Page Name</label>
									<input  type="text"  id="edit-pageName" name="pageName" value="' . $dati['pageName'] . '" size="100" maxlength="100">
								</li>
							<li><label for="pageDescription">Page<br/>Description</label>
								
									<textarea  type="text" style="width:260px; height:90px;" id="edit-pageDescription" name="pageDescription">' . $dati['pageDescription'] . '</textarea>
								</li>

							</ul>
	
	
					  </fieldset>';
			echo '<input type="hidden" name="task" value="settings.edit" />';
			JHtml::_('form.token');
			echo "</form>";
	
				
			//"liveStreamPwd": "-- pwd per il live di wim.tv --",
			//"liveStreamEnabled": "-- abilita live true|false --"
			//eventResellerEnabled": "-- abilita event reselling true|false --",
			//"eventOrganizerEnabled": "-- abilita event organizing true|false --",
	
				
			break;
	
		}
		
		if (isset($_GET["credential"])){
			echo '<form action="index.php?option=com_wimtvpro&view=settings&credential=1" method="post" name="adminForm" enctype="multipart/form-data">
			<fieldset><legend>Insert your credential</legend>
					
			<ul class="adminformlist">
			<li>
			<label class="" for="jform_wimtv_username" id="jform_wimtv_username-lbl">Username WimTV</label>								<input type="text" value="' . $username . '" id="jform_wimtv_username" name="wimtv_username">				</li>
			<li>
			<label class="" for="jform_wimtv_password" id="jform_wimtv_password-lbl">Password WimTV</label>								<input type="password" value="' . $password . '" id="jform_wimtv_password" name="wimtv_password">				</li>
			
			</ul>
			
			</fieldset>';
			echo '<input type="hidden" name="task" value="settings.edit" />';
			JHtml::_('form.token');
			echo "</form>";
			
		
		}
		
		if (isset($_GET["pack"])){
			
			if (isset($_GET["upgrade"])){
				$upgrade = $_GET['upgrade'];
				$directoryCookie  = JPATH_COMPONENT . DS . "uploads" . DS . "cookie";
				
				if (!is_dir($directoryCookie)) {
					$directory_create = mkdir(JPATH_COMPONENT . DS . "uploads" . DS . "cookie");
				}
				$fileCookie = "cookies_" . $username . "_" . $upgrade  . ".txt";
				
				if (!is_file($directoryCookie. "/" . $fileCookie)) {
					$f = fopen($directoryCookie. "/" . $fileCookie,"w");
					fwrite($f,"");
					fclose($f);
				}
				//Update Packet
				$data = array("name" => $upgrade);
				$data_string = json_encode($data);
					
				// chiama
				$ch = curl_init();
				$my_page =  JURI::base() . "/index.php?option=com_wimtvpro&view=settings&pack=1&success=" . $upgrade;
				
				
				curl_setopt($ch, CURLOPT_URL,  $basePath . "userpacket/payment/pay?externalRedirect=true&success=" . urlencode ($my_page));
				curl_setopt($ch, CURLOPT_VERBOSE, 0);
					
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
				curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
				curl_setopt($ch, CURLOPT_USERPWD, $credential);
					
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
					
				curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Content-Type: application/json', 'Accept-Language:' . $_SERVER['HTTP_ACCEPT_LANGUAGE'],
				'Content-Length: ' . strlen($data_string))
				);
				
				
				// salva cookie di sessione
				curl_setopt($ch, CURLOPT_COOKIEJAR, $directoryCookie . "/" . $fileCookie);
				$result = curl_exec($ch);
				curl_close($ch);
				$arrayjsonst = json_decode($result);
				if (($arrayjsonst->result=="REDIRECT") || ($arrayjsonst->result=="MESSAGE")) {
					
					JError::raiseNotice( 100, $arrayjsonst->message . "<br/><a href='" . $arrayjsonst->successUrl . "'>Yes</a> | <a href='index.php?option=com_wimtvpro&view=settings&pack=1'>No</a>");
					
					
				}  else {
				
					//var_dump($arrayjsonst);
				
				}
			}
			
			if (isset($_GET['success'])) {

				//controlla stato pagamento
					
				/*$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $basePath . "userpacket/payment/check");
				curl_setopt($ch, CURLOPT_VERBOSE, 0);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept-Language:' .  $_SERVER['HTTP_ACCEPT_LANGUAGE']));
				curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
				curl_setopt($ch, CURLOPT_USERPWD, $credential);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
					
				$fileCookie = "cookies_" . $username . "_" . $_GET['success'] . ".txt";
					
				// Recupera cookie sessione
				curl_setopt($ch, CURLOPT_COOKIEFILE, $directoryCookie . "/" . $fileCookie );*/


                $cookie = $directoryCookie . "/" . $fileCookie;
				$result = apiCheckPayment($cookie); //curl_exec($ch);
				$arrayjsonst = json_decode($result);

			}
			
			$url_packet_user = $basePath . "userpacket/" . $username;
			
			/*$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url_packet_user);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept-Language:' . $_SERVER['HTTP_ACCEPT_LANGUAGE']));
			curl_setopt($ch, CURLOPT_VERBOSE, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);*/
			
			$response = apiGetPacket(); //curl_exec($ch);
			$packet_user_json = json_decode($response);
			//var_dump ($response);
			$id_packet_user = $packet_user_json->id;
			$createDate_packet_user = $packet_user_json->createDate;
			$updateDate_packet_user = $packet_user_json->updateDate;
			
			$createDate = date('d/m/Y', $createDate_packet_user/1000);
			$updateDate = date('d/m/Y', $updateDate_packet_user/1000);
			$dateRange = getDateRange($createDate , $updateDate );
			
			$count_date = $packet_user_json->daysLeft;
			//$count_date = count($dateRange)-1;

            /*$url_packet = $basePath . "commercialpacket";

            $header = array("Accept-Language:" . $_SERVER['HTTP_ACCEPT_LANGUAGE']);


            $ch2 = curl_init();
            curl_setopt($ch2, CURLOPT_URL, $url_packet);
            curl_setopt($ch2, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch2, CURLOPT_VERBOSE, 0);
            curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch2, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch2, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);*/
			
			$response2 = apiCommercialPacket(); //url_exec($ch2);
			 
			//$info = curl_getinfo($ch2);
			
			
			$packet_json = json_decode($response2);
			 
			
			 
			//var_dump ($response2);
			
			echo "<table class='wp-list-table widefat fixed pages'>";
			echo "<thead><tr><th></th>";
			foreach ($packet_json -> items as $a) {
			
				echo "<th><b>" . $a->name . "</b></th>";
				 
			}
			
			echo "</thead>";
			echo "<tbody>";
			echo "<tr class='alternate'>";
			echo "<td>Band</td>";
			foreach ($packet_json -> items as $a) {
				echo "<td>" . $a->band . " GB</td>";
			}
			
			echo "</tr>";
			
			echo "<tr>";
			echo "<td>Storage</td>";
			foreach ($packet_json -> items as $a) {
				echo "<td>" . $a->storage . " GB</td>";
			}
			
			echo "</tr>";
			
			echo "<tr class='alternate'>";
			echo "<td>Support</td>";
			foreach ($packet_json -> items as $a) {
				echo "<td>" . $a->support . "</td>";
			}
			
			echo "</tr>";
			
			
			echo "<tr>";
			echo "<td>Price</td>";
			foreach ($packet_json -> items as $a) {
				echo "<td>" . number_format($a->price,2) . " &euro; / month</td>";
			}
			
			echo "</tr>";
			
			echo "<tr class='alternate'>";
			echo "<td></td>";
			foreach ($packet_json -> items as $a) {
				//echo "<td>" . $a->dayDuration . " - " . $a->id . "</td>";
				echo "<td>";
				if ($id_packet_user==$a->id) {
					 
					echo "<img  src='" . JURI::base() . "components/com_wimtvpro/assets/images/check.png' title='Checked'><br/>";
					echo $count_date . "day left";
				}
				else {
					echo "<a href='index.php?option=com_wimtvpro&view=settings&pack=1";
					echo "&upgrade=" . $a->name;
					echo "'><img class='icon_upgrade' src='" . JURI::base() . "components/com_wimtvpro/assets/images/uncheck.png' title='Upgrade'>";
					echo "</a>";
				}
				echo "</td>";
			}
			
			echo "</tr>";
			
			
			
			echo "</tbody>";
			echo "</table>";
			
			echo "<h3>You have a free trial of 30 days to try the WimTVPro plugin.</h3>
			<h3>After 30 days you can subscribe a plan that suit your needs.</h3>
			<h3>All plans come with all features, only changes the amount of bandwidth and storage available.</h3>
			<h3>Enyoy your WimTVPro video plugin!</h3>";
			
		}
	
	}	
		
} 

?>
