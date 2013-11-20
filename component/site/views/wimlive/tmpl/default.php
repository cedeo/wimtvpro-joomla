<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
$mydoc = &JFactory::getDocument();

$mytitle = $mydoc->getTitle();

?>
<h1><?php echo $mytitle;?></h1>

<?php 
$type = "list";
if (!isset($_POST["timezone"])){


	

	?>
	
	<script type="text/javascript">
	jQuery(document).ready(function(){
		var timezone = -(new Date().getTimezoneOffset())*60*1000;

		var url = window.location.href; 
		jQuery("#timezone").val(timezone);
		jQuery("#redirectForm").attr("action",url);
		document.getElementById("redirectForm").submit();
		
  		
	});
			          
	</script>
	<form action="#" id="redirectForm" method="POST"><input id="timezone" type="hidden" name="timezone" value="" /></form>
	<?php

} else {

	//echo "QUA I LIVE " . $_GET["timezone"];
	// Vedere il video wimtvpro_elencoLive("0", "video");
	
	$app = &JFactory::getApplication();
	$params = JComponentHelper::getParams('com_wimtvpro');
	$username = $params->get('wimtv_username');
	$password = $params->get('wimtv_password');
	$basePath = $params->get('wimtv_basepath');
	$replaceContentWimtv = $params->get('wimtv_replaceContentWimtv');
	$urlThumbsWimtv = $params->get('wimtv_urlThumbsWimtv');
	$credential = $username . ":" . $password;
	$userpeer = $username;
	
	$url_live_select = $basePath  . "liveStream/" . $userpeer . "/" . $userpeer . "/hosts?timezone=" . $timezone;
	$url_live_select .= "&active=true";
	
	$ch_select = curl_init();
	curl_setopt($ch_select, CURLOPT_URL, $url_live_select);
	curl_setopt($ch_select, CURLOPT_VERBOSE, 0);
	curl_setopt($ch_select, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch_select, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	curl_setopt($ch_select, CURLOPT_USERPWD, $credential);
	curl_setopt($ch_select, CURLOPT_SSL_VERIFYPEER, FALSE);
	$json  =curl_exec($ch_select);
	$arrayjson_live = json_decode($json);


	$count = -1;

	if ($arrayjson_live ){
		
		//Primo Live
		
		
		
		
		//wimtvpro_elencoLive("video", "0") 
		//Live Successivi
		
		foreach ($arrayjson_live->{"hosts"} as $key => $value) {
			$count ++;
			
			if ($count==0){
				$identifier = $value -> identifier;
				$url_live_embedded = $basePath . "liveStream/" . $userpeer . "/" . $userpeer . "/hosts/" . $identifier . "/embed?timezone=" . $timezone;
				$ch_embedded = curl_init();
				$header[] = "Accept: text/xml,application/xml,application/xhtml+xml,";
				curl_setopt($ch_embedded, CURLOPT_URL, $url_live_embedded);
				curl_setopt($ch_embedded, CURLOPT_VERBOSE, 0);
				curl_setopt($ch_embedded, CURLOPT_HTTPHEADER, $header);
				curl_setopt($ch_embedded, CURLOPT_RETURNTRANSFER, TRUE);
				curl_setopt($ch_embedded, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
				curl_setopt($ch_embedded, CURLOPT_USERPWD, $credential);
				curl_setopt($ch_embedded, CURLOPT_SSL_VERIFYPEER, FALSE);
				$embedded_iframe = curl_exec($ch_embedded);
					
				$name = "<b>" . $name . "</b>";
				$day =  "Begins to " . $day;
				$output = $name . "<br/>";
				$output .= $data . " " . $oraMin  . "<br/>" . $durata . "<br/>";
				$output .= $embedded_iframe;
			} else {
			
				$name = $value -> name;
				if (isset($value -> url))
					$url =  $value -> url;
				else
					$url = "";
				$day =  $value -> eventDate;
				$payment_mode =  $value -> paymentMode;
				if ($payment_mode=="FREEOFCHARGE") $payment_mode="Free";
				else {
					$payment_mode=  $value->pricePerView . " &euro;";
				}
				if ( $value -> durationUnit=="Minute") {
					$tempo = $value->duration;
					$ore = floor($tempo / 60);
					$minuti = $tempo % 60;
					$durata = $ore . " h ";
					if ($minuti<10)
				  $durata .= "0";
					$durata .= $minuti . " min";
				}
				else
				 $durata =  $value->duration . " " . $value -> durationUnit;
		
				$identifier = $value -> identifier;
				$url_live_embedded = $basePath . "liveStream/" . $userpeer . "/" . $userpeer . "/hosts/" . $identifier . "/embed?timezone=" . $timezone;
				$ch_embedded = curl_init();
		
				$ch_details= curl_init();
		
				//read iframe
		
				curl_setopt($ch_details, CURLOPT_URL, $url_live_embedded);
				curl_setopt($ch_details, CURLOPT_VERBOSE, 0);
				curl_setopt($ch_details, CURLOPT_RETURNTRANSFER, TRUE);
				curl_setopt($ch_details, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
				curl_setopt($ch_details, CURLOPT_USERPWD, $credential);
				curl_setopt($ch_details, CURLOPT_SSL_VERIFYPEER, FALSE);
				$details_live = curl_exec($ch_details);
				$livedate = json_decode($details_live);
				$data = $livedate->eventDate;
				if (intval($livedate->eventMinute)<10) $livedate->eventMinute = "0" .  $livedate->eventMinute;
				$oraMin = $livedate->eventHour . ":" . $livedate->eventMinute;
				$timeToStart= $livedate->timeToStart;
				$timeLeft = $livedate->timeLeft;
		
				// $urlPeer = "http://peer.wim.tv:8080/wimtv-webapp/rest";
				//$embedded_code = htmlentities(curl_exec($ch_embedded));
				//$embedded_iframe = '<iframe id="com-wimlabs-player" name="com-wimlabs-player" src="' . $urlPeer . '/liveStreamEmbed/' . $identifier . '/player?width=692&height=440" style="min-width: 692px; min-height: 440px;"></iframe>';
		
		
			  if ($count==0) $output_suc .= "";
			  elseif ($count>0) $output_suc .="<li><b>" . $name . "</b> " . $payment_mode . " - " . $data . " " . $oraMin . " - " . $durata . "</li>";
			  else $output_suc .="<li><b>" . $name . "</b> " . $payment_mode . " - " . $data . " " . $oraMin   . " - " . $durata . "</li>";
		
			}	  
			  
		  $count++;
		}
	}
	if ($count<0)
		$output = "Aren't Event Live";
	
	
	echo $output . "<br/>UPCOMING EVENT<br/>" . $output_suc ;
	
}






?>