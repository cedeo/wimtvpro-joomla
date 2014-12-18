<?php 
// No direct access
defined('_JEXEC') or die('Restricted access');
require_once ( JPATH_BASE . "/components/com_wimtvpro/includes/api/wimtv_api.php" );

JHtml::_('behavior.tooltip');

$app = &JFactory::getApplication();
$params = JComponentHelper::getParams('com_wimtvpro');
$username = $params->get('wimtv_username');
$profile = json_decode(apiGetProfile());
$password = $profile->liveStreamPwd;

$credential = $username . ":" . $password;

$id =  $_GET['id'];

$embedded = apiEmbeddedLive($id);
$arrayjson_live = json_decode($embedded);
$url =  $arrayjson_live->url;
$title = $arrayjson_live->name;
$stream_url = substr($url, 0, strrpos( $url, '/'));
$stream_name = substr($url, strrpos( $url, '/') + 1, strlen($url));

?>

<div id="page">	<h1>Producer Live <?php echo $title;?></h1>
  
<p>On this page you can view the video you're broadcasting live. Keep it open during the whole transmission.</p>
<div  class="pageproducer">
<div id="producer"></div>
</div>


<script type="text/javascript">
jQuery(document).ready(function(){ 

	var xiSwfUrlStr = "<?php echo JURI::base();?>components/com_wimtvpro/assets/js/swfObject/playerProductInstall.swf";
	console.log(xiSwfUrlStr );
	var flashvars = {};
    var params = {};
    params.quality = "high";
    params.bgcolor = "#ffffff";
    params.allowscriptaccess = "sameDomain";
    params.allowfullscreen = "true";
    var attributes = {};
    attributes.align = "left";

	swfobject.embedSWF("<?php echo JURI::base();?>components/com_wimtvpro/assets/js/swfObject/producer.swf", "producer", "640", "480", "11.4.0",xiSwfUrlStr, flashvars, params, attributes );
	setTimeout(function () {
		producer = jQuery('#producer')[0];
	    console.log(producer);
	    
	    producer.setCredentials('<?php echo $username ?>', '<?php echo $password; ?>');
    	producer.setUrl(decodeURIComponent('<?php echo $stream_url;?>'));
    	producer.setStreamName('<?php echo $stream_name;?>');
	    producer.setStreamWidth(640);
	    producer.setStreamHeight(480);
	    producer.connect();
	}, 1000);
    
});
</script>
</div>


