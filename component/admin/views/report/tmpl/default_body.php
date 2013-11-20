<?php
/**
 * default body template file for HelloWorlds view of HelloWorld component
 *
 * @version		$Id: default_body.php 46 2010-11-21 17:27:33Z chdemko $
 * @package		Joomla16.Tutorials
 * @subpackage	Components
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @author		Christophe Demko
 * @link		http://joomlacode.org/gf/project/helloworld_1_6/
 * @license		License GNU General Public License version 2 or later
 */

$app = &JFactory::getApplication();
$params = JComponentHelper::getParams('com_wimtvpro');
$username = $params->get('wimtv_username');
$password = $params->get('wimtv_password');


$view_page = wimtvpro_alert_reg($username,$password);

if ($view_page){

	global $user,$wpdb;

	$table_name = '#__wimtvpro_video';

	$baseReport = "http://www.wim.tv:3131/api/";
	
	$megabyte = 1024*1024;

	if ((isset($_POST['from'])) && (isset($_POST['to'])) && (trim($_POST['from'])!="") && (trim($_POST['to'])!="")) {
		$from = $_POST['from'];
		$to = $_POST['to'];
		//convert to  (YYYY-MM-DD)
		$current_month=FALSE;
		list($year_from, $month_from, $day_from) = explode('-',$from);
		//$from_tm = mktime(0, 0, 0, $month, $day, $year);
		list($year_to, $month_to, $day_to) = explode('-',$to);
		//$to_tm = mktime(0, 0, 0,  $month, $day, $year);

		$from_tm = strtotime( $year_from . "-" . $month_from . "-" . $day_from . " 00:00:00.00")*1000;
		$to_tm = strtotime( $year_to . "-" . $month_to . "-" . $day_to . " 00:00:00.00")*1000;

		$from_dmy =$month_from . "/" . $day_from . "/" . $year_from;
		$to_dmy= $month_to . "/" . $day_to . "/" . $year_to;

	} else {
		$current_month=TRUE;

		$d = new DateTime(date('Y-m-d'));

		$d->modify('first day of this month');
		$from_dmy = $d->format('Y-m-d');

		$d->modify('last day of this month');
		$to_dmy = $d->format('Y-m-d');

	}

	if ($current_month==TRUE){

		$url_view  = $baseReport . "users/" . $username . "/views";
		$title_views = "Views (current month)";
			
		$url_stream = $baseReport . "users/" . $username . "/streams";
		$title_streams = "Streams (current month)";
		$url_view_single = $baseReport . "views/@";

			
		$url_info_user = $baseReport . "users/" . $username;
		$title_user = "Current Month  <a href='#' id='customReport'>Change Date</a> ";
		$style_date = "display:none;";
		$url_packet = $baseReport . "users/" . $username . "/commercialPacket/usage";
			
	} else {

		$url_view = $baseReport . "users/" . $username . "/views_by_time?from=" . $from_tm . "&to=" . $to_tm;
		$title_views = "Views (from " . $from . " to " . $to . ")";
			
		$url_stream = $baseReport . "users/" . $username . "/streams?from=" . $from_tm . "&to=" . $to_tm ;
		$title_streams = "Streams (from " . $from . " to " . $to . ")";
		$url_view_single = $baseReport . "views/@?from=" . $from_tm . "&to=" . $to_tm ;
			
		$url_info_user = $baseReport . "users/" . $username . "?from=" . $from_tm . "&to=" . $to_tm . "&format=json";
			
		$title_user = "<a href='index.php?option=com_wimtvpro&view=report'>Current Month</a> Change Date";



	}

	echo "<h1>Report user Wimtv " . $username . "</h1>";


	echo '
			<script type="text/javascript">
			jQuery(document).ready(function(){
			jQuery.noConflict();
			jQuery("#customReport").click(function(){
			jQuery("#fr_custom_date").fadeToggle();
			jQuery("#changeTitle").html("<a href=\'' . JRoute::_('index.php?option=com_wimtvpro&view=report') . '\'>Current Month</a> Change Date");
});

			jQuery(".tabs span").click(function(){
			var idSpan = jQuery(this).attr("id");
			jQuery(".view").fadeOut();
			jQuery("#view_" + idSpan).fadeIn();
			jQuery(".tabs span").attr("class","");
			jQuery(this).attr("class","active");
});

});
			</script>
			';


	echo "<h3 id='changeTitle'>" . $title_user . "</h3>";

	echo '<div class="registration" id="fr_custom_date" style="' . $style_date . '">
  
	<fieldset>
	<div style="float:left"><label style="float:left;"> From </label> ';
	echo JHTML::calendar($from,'from','edit-from'); //%Y-%m-%d
	echo '</div><div style="float:left"><label style="float:left;"> To </label> ';
	echo JHTML::calendar($to,'to','edit-to');
	echo '</div><div style="float:left"><input type="submit" value=">" class="button button-primary" />';
	echo '</div></fieldset>
	</div>';

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url_info_user);
	curl_setopt($ch, CURLOPT_VERBOSE, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	$response = curl_exec($ch);
	curl_close($ch);

	$traffic_json = json_decode($response);
	$traffic = $traffic_json->traffic;
	$storage = $traffic_json->storage;


	if (isset($url_packet)) {
			
		$ch2 = curl_init();
		curl_setopt($ch2, CURLOPT_URL, $url_packet);
		curl_setopt($ch2, CURLOPT_VERBOSE, 0);
		curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch2, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch2, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		$response2 = curl_exec($ch2);
		curl_close($ch2);

		$commercialPacket_json = json_decode($response2);
		$currentPacket = $commercialPacket_json->current_packet;
		if (($currentPacket->id)>0) $namePacket =  $currentPacket->name;
		else $namePacket =  $currentPacket->error;
		echo "<p>Your Commercial Packet: <b>" . $namePacket . "</p> ";

		$traffic_of = " of " . $currentPacket->band_human;
		$storage_of = " of " . $currentPacket->storage_human;

		$traffic_bar = "<div class='progress'><div class='bar' style='width:" . $commercialPacket_json->traffic->percent . "%'>" . $commercialPacket_json->traffic->percent_human . "%</div></div>";
		$storage_bar = "<div class='progress'><div class='bar' style='width:" . $commercialPacket_json->storage->percent . "%'>" . $commercialPacket_json->storage->percent_human . "%</div></div>";

		$byteToMb = "<b>" . $commercialPacket_json->traffic->current_human . '</b>' . $traffic_of . $traffic_bar;
		$byteToMbS = "<b>" . $commercialPacket_json->storage->current_human . '</b>' . $storage_of . $storage_bar;
			
	} else {
			
		$byteToMb = "<b>" . round($traffic/ $megabyte, 2) . ' MB</b>';
		$byteToMbS = "<b>" . round($storage/ $megabyte, 2) . ' MB</b>';

			
	}

	//$commercialPacket = $traffic_json->commercialPacket;
	if ($traffic=="") {
		echo "<p>You account don't generate traffic in this period.</p>";
	} else {
		echo "<p>Traffic: " . $byteToMb . "</p>";
		echo "<p>Storage space: " . $byteToMbS . "</p>";




		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url_stream);
		curl_setopt($ch, CURLOPT_VERBOSE, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		$response = curl_exec($ch);
		curl_close($ch);
		$arrayStream = json_decode($response);
			
		echo '
		<div class="summary"><div class="tabs">
		<span id="stream" class="active">View Streams</span><span id="graph">View graph</span>
		</div>
		<div id="view_stream" class="view">
		<h3>' . $title_streams . '</h3>';
		echo '<table><tr>
		<th class="manage-column column-title">Video</th>
		<th class="manage-column column-title">Views</th>
		<th class="manage-column column-title">Activate view</th>
		<th class="manage-column column-title">Max viewers</th>
		</tr>
		';
			
		$dateNumber = array();
		$dateTraffic = array();
		foreach ($arrayStream as $value){
			
			// Get a db connection.
			$db = JFactory::getDbo();
			
			// Create a new query object.
			$query = $db->getQuery(true);
			
			// Select all records from the user profile table where key begins with "custom.".
			// Order it by the ordering field.
			$query->select(array('*'));
			$query->from("#__wimtvpro_videos");
			$query->where("contentidentifier='" . $value->contentId . "'");

			// Reset the query using our newly populated query object.
			$db->setQuery($query);
			
			// Load the results as a list of stdClass objects.
			$arrayPlay = $db->loadObjectList();
			
			$thumbs = $arrayPlay[0]->urlThumbs;
			$thumbs = str_replace('\"','',$thumbs);
			if ((isset($value->title))) $video = $thumbs . "<br/><b>" . $value->title . "</b><br/>" . $value->type ;
			else $video = $thumbs . "<br/>" . $value->id;

			$html_view_exp = "<b>Total " . $value->views . " Views</b><br/>";
			$view_exp = $value->views_expanded;
			if (count($view_exp)>0) {
				$html_view_exp .= "<table class='wp-list-table'>
						<tr>
						<th class='manage-column column-title' style='font-size:10px;'>End Time</th>
						<th class='manage-column column-title' style='font-size:10px;'>Duration</th>
						<th class='manage-column column-title' style='font-size:10px;'>Traffic</th>
						</tr>
						";
				foreach ($view_exp as $value_exp){
					$value_exp->traffic =  round($value_exp->traffic / $megabyte, 2) . " MB";
					$date_human =  date('Y/m/d', ($value_exp->end_time/1000));
					$html_view_exp .= "<tr>";
					$html_view_exp .= "<td style='font-size:10px;'>" . $date_human . "</td>";
					$html_view_exp .= "<td style='font-size:10px;'>" . $value_exp->duration . "s</td>";
					$html_view_exp .= "<td style='font-size:10px;'>" . $value_exp->traffic  . "</td>";
					$html_view_exp .= "</tr>";

					if (isset($dateNumber[$date_human])) $dateNumber[$date_human] = $dateNumber[$date_human] + 1;
					else $dateNumber[$date_human] = 1;

					if (isset($dateTraffic[$date_human])) array_push($dateTraffic[$date_human], $value_exp->traffic);
					else $dateTraffic[$date_human] = array($value_exp->traffic);


				}
				$html_view_exp .= "</table>";
			} else
			{
				$html_view_exp .= "";
			}
			echo "
					<tr class='alternate'>
					<td class='image'>" .  $video . "</td>
					  <td>" .  $html_view_exp . "</td>
					  		<td>" . $value->viewers . "</td>
					  				<td>" .  $value->max_viewers . "</td>
					  						</tr>";

		}
		echo "</table></div>";


		echo "<div id='view_graph' class='view'>";
		$dateRange = getDateRange($from_dmy, $to_dmy);
		$count_date = count($dateRange);
		$count_single= 0;
		$traffic_single = 0;
		echo "<div class='cols'>";
		if (count($dateNumber)>0) {
			$number_view_max = max($dateNumber);
			$single_percent = (100/$number_view_max);
		} else {
			$number_view_max =0;
			$single_percent = 0;
		}
		$single_taffic_media = array();
		foreach ($dateTraffic as $dateFormat => $traffic_number){
			$single_taffic_media[$dateFormat] = round(array_sum($dateTraffic[$dateFormat]) / count($dateTraffic[$dateFormat]),2);
		}
		if (count($single_taffic_media)>0) {
			$traffic_view_max = max($single_taffic_media);
			$single_traffic_percent = (100/$traffic_view_max);
		} else {
			$traffic_view_max =0;
			$single_traffic_percent = 0;
		}
		echo "<div class='col'><div class='date'>Date</div><div class='title'>Total view</div><div class='title'>Average Traffic</div></div>";
		for ($i=0;$i<$count_date;$i++){
			if (isset($dateNumber[$dateRange[$i]])) $count_single = $single_percent * $dateNumber[$dateRange[$i]];
			if (isset($single_taffic_media[$dateRange[$i]])) $traffic_single = $single_traffic_percent * $single_taffic_media[$dateRange[$i]];

			echo "<div class='col' >
					<div class='date'>" . $dateRange[$i] . "</div>
							<div class='countview'><div class='bar' style='width:" . $count_single . "%'>";
			if ($dateNumber[$dateRange[$i]]>1) echo $dateNumber[$dateRange[$i]] . " viewers";
			if ($dateNumber[$dateRange[$i]]==1) echo $dateNumber[$dateRange[$i]] . " viewer";
			echo "</div></div>
					<div class='countview'><div class='barTraffic' style='width:" . $traffic_single . "%'>";
			if ($single_taffic_media[$dateRange[$i]]>0) echo $single_taffic_media[$dateRange[$i]] . " MB";
			echo "</div></div>
					</div>";
			$count_single = 0;
			$traffic_single = 0;
		}

		echo "</div>";
		echo "</div>";

			
	}


}



