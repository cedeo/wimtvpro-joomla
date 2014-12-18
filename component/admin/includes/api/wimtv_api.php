<?php

/**
 * Written by walter at 30/10/13
 * Updated by Netsense in 2014
 */
// NS: As we need Joomla Framework to be bootstrapped
// we check whether the constant JPATH_ROOT has been defined
// and eventually we "require" base Joomla files
// (ie: defines.php and framework.php)
$jFrameworkBootstrapped = defined("JPATH_ROOT");
if (!$jFrameworkBootstrapped) {
    define('_JEXEC', 1); //  This will allow to access file outside of joomla.
    //defined( '_JEXEC')  or die( 'Restricted access' );// Use this if you wanna access file only in Joomla.
    define('DS', DIRECTORY_SEPARATOR);
    define('JPATH_BASE', $_SERVER['DOCUMENT_ROOT']);
    require_once ( JPATH_BASE . DS . 'includes' . DS . 'defines.php' );
    require_once ( JPATH_BASE . DS . 'includes' . DS . 'framework.php' );
}

include_once("api.php");
// NS: THE FOLLOWING include/require ARE ALREADY
// CALLED IN "framework.php". WE DON'T NEED THEM ANY MORE.
//if (JComponentHelper == 'JComponentHelper') {
//    define('_JEXEC', 1);
//    include_once( '../../../../libraries/import.php' ); // framework
//    include_once( '../../../../configuration.php' );
//    
//include_once(JPATH_PLATFORM . '/import.php'); // framework
//include_once(JPATH_PLATFORM . '/../configuration.php');
//jimport('joomla.application.component.helper');
//}
jimport('joomla.application.component.helper');

use Api\Api;
use \Httpful\Mime;
use \Httpful\Request;

function initApi($host, $username, $password) {
    Api::initApiAccessor($host, $username, $password);
}

function getApi() {
    return Api::getApiAccessor();
}

function apiCreateUrl($name) {
    $apiAccessor = getApi();
    $request = $apiAccessor->getRequest('liveStream/uri?name=' . $name);
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request);
}

function apiEmbeddedLive($hostId, $timezone = null) {
    $apiAccessor = getApi();
    $url = 'liveStream/' . $apiAccessor->username . '/' . $apiAccessor->username . '/hosts/' . $hostId;
    if ($timezone)
        $url .= "?timezone=" . $timezone;
    $request = $apiAccessor->getRequest($url);
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request, Mime::JSON, 'it-it');
}

function apiGetProfile() {
    // NS
    //    $apiAccessor = getApi();
    //    $request = $apiAccessor->getRequest('profile');
    //    $request = $apiAccessor->authenticate($request);
    //    return $apiAccessor->execute($request);
    $apiAccessor = getApi();
    $request = $apiAccessor->getRequest('profile');
    $request = $apiAccessor->authenticate($request);
    // NS: UPDATED APIs
    //    return  $apiAccessor->execute($request);
    return $apiAccessor->execute($request, 'application/json');
}

function apiEditProfile($params) {
    $apiAccessor = getApi();
    $request = $apiAccessor->postRequest('profile');
    $request->sends(Mime::JSON);
    $request->body($params);
    $request = $apiAccessor->authenticate($request);
    //    return $apiAccessor->execute($request, Mime::JSON);
    return $apiAccessor->execute($request, 'application/json');
}

function apiChangePassword($password) {
    /*
     * ATTENZIONE: API NON SUPPORTATA (vedi apiEditProfile)
     */

    //    $apiAccessor = getApi();
    //    $request = $apiAccessor->putRequest("users/" . $apiAccessor->username . "/updateLivePwd");
    //    $params = array('liveStreamPwd' => $password);
    //    $request->body($params);
    //    $request = $apiAccessor->authenticate($request);
    //    return $apiAccessor->execute($request);
}

function apiGetShowtimes() {
    $apiAccessor = getApi();
// NS: use new authenticated API to avoid the hidePublicShowtimeVideos bug
//    $request = $apiAccessor->getRequest('users/' . $apiAccessor->username . '/showtime?details=true');
    $request = $apiAccessor->getRequest('showtimeVideos?details=true');
    $request = $apiAccessor->authenticate($request);
    $response = $apiAccessor->execute($request, 'application/json');
    return $response;
}

function apiGetLiveEvents($timezone, $activeOnly) {
    $apiAccessor = getApi();
    $url = $apiAccessor->liveHostsUrl . '?timezone=' . $timezone;
    if ($activeOnly) {
        $url .= '&active=true';
    }
    $request = $apiAccessor->getRequest($url);
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request, 'application/json', 'it-it');
    return $res;
}

function apiGetLive($host_id, $timezone = "") {
    $apiAccessor = getApi();
    $url = $apiAccessor->liveHostsUrl . '/' . $host_id;
    if (strlen($timezone))
        $url .= '?timezone=' . $timezone;
    $request = $apiAccessor->getRequest($url);
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request, 'application/json', 'it-it');
}

function apiGetLiveIframe($host_id, $timezone = "") {
    $apiAccessor = getApi();
    $url = $apiAccessor->liveHostsUrl . '/' . $host_id . '/embed';
    if (strlen($timezone))
        $url .= '?timezone=' . $timezone;
    $request = $apiAccessor->getRequest($url);
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request, 'text/xml, application/xml', 'it-it');
}

function apiAddLive($parameters, $timezone = null) {
    $apiAccessor = getApi();
    $url = $apiAccessor->liveHostsUrl;
    if ($timezone)
        $url .= '?timezone=' . $timezone;
    $request = $apiAccessor->postRequest($url);
    $request->body($parameters);
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request, 'application/json', 'it-it');
}

function apiModifyLive($host_id, $parameters, $timezone = null) {
    $apiAccessor = getApi();
    $url = $apiAccessor->liveHostsUrl . '/' . $host_id;
    if ($timezone)
        $url .= '?timezone=' . $timezone;
    $request = $apiAccessor->postRequest($url);
    $request->body($parameters);
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request, 'application/json', 'it-it');
}

function apiPublishOnShowtime($id, $parameters) {
    $apiAccessor = getApi();
    $request = $apiAccessor->postRequest('videos/' . $id . '/showtime');
    $request->body($parameters);
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request);
}

function apiPublishAcquiredOnShowtime($id, $acquiredId, $parameters) {
    $apiAccessor = getApi();
    $request = $apiAccessor->postRequest('videos/' . $id . '/acquired/' . $acquiredId . '/showtime');
    $request->body($parameters);
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request);
}

function apiGetThumbsVideo($contentId) {
    $apiAccessor = getApi();
    $request = $apiAccessor->getRequest('videos/' . $contentId . '/thumbnail');
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request, 'text/xml, application/xml');
}

//function apiGetDetailsVideo($contentId) {
//    $apiAccessor = getApi();
//    $request = $apiAccessor->getRequest('videos/' . $contentId . '?details=true');
//    $request = $apiAccessor->authenticate($request);
//    return $apiAccessor->execute($request, "application/json");
//}

function apiGetDetailsVideo($contentId, &$error_response = null) {
    $apiAccessor = getApi();
    $request = $apiAccessor->getRequest('videos/' . $contentId . '?details=true');
    $request = $apiAccessor->authenticate($request);
    $response = $apiAccessor->execute($request, "application/json");
    if ($response == "" && isset($error_response)) {
        $error_response = $apiAccessor->execute($request);
    }
    return $response;
//    return $apiAccessor->execute($request);
}

function apiGetDetailsShowtime($id) {
    $apiAccessor = getApi();
    $request = $apiAccessor->getRequest('users/' . $apiAccessor->username . '/showtime/' . $id . '/details');
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request, "application/json");
}

function apiGetPlayerShowtime($id, $parameters) {
    $apiAccessor = getApi();
    $request = $apiAccessor->getRequest('videos/' . $id . "/embeddedPlayers?" . $parameters);
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request);
}

function apiDeleteLive($host_id) {
    $apiAccessor = getApi();
    $request = $apiAccessor->deleteRequest($apiAccessor->liveHostsUrl . '/' . $host_id);
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request);
}

function apiGetVideoCategories() {
    $apiAccessor = getApi();
    $request = $apiAccessor->getRequest('videoCategories');
    return $apiAccessor->execute($request);
}

function apiGetUUID() {
    $apiAccessor = getApi();
    $request = $apiAccessor->getRequest('uuid');
    return $apiAccessor->execute($request);
}

function apiUpload($parameters) {
    $apiAccessor = getApi();
    $request = $apiAccessor->postRequest('videos?uploadIdentifier=' . $parameters['uploadIdentifier']);
    $request->body($parameters);
    $request->sends(Mime::UPLOAD);
    $request->attach(array('file' => $parameters['file']));
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request);
}

function apiDownload($hostId) {
    $apiAccessor = getApi();
    $request = $apiAccessor->downloadRequest('videos/' . $hostId . '/download');
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request, "");
}

function apiGetVideos($details = true) {
    if ($details) {
        $details = 'true';
    } else {
        $details = 'false';
    }
    $apiAccessor = getApi();
    $request = $apiAccessor->getRequest('videos?details=' . $details);
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request, "application/json");
}

function apiDeleteVideo($hostId) {
    $apiAccessor = getApi();
    $request = $apiAccessor->deleteRequest('videos/' . $hostId);
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request);
}

function apiDeleteFromShowtime($id, $stid) {
    $apiAccessor = getApi();
    $request = $apiAccessor->deleteRequest('videos/' . $id . '/showtime/' . $stid);
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request);
}

function apiRegistration($params) {
    $apiAccessor = getApi();
    $request = $apiAccessor->postRequest('register');
    $request->sendsJson();
    $request->body(json_encode($params));
    return $apiAccessor->execute($request, 'application/json');
    // NS
    // return $apiAccessor->execute($request);    
}

function apiCheckPayment($cookie) {
    $apiAccessor = getApi();
    $request = $apiAccessor->getRequest('userpacket/payment/check');
    $request->setCookieFile($cookie);
    return $apiAccessor->execute($request);
}

function apiGetUploadProgress($contentIdentifier) {
    $apiAccessor = getApi();
    $request = $apiAccessor->getRequest('uploadProgress/' . $contentIdentifier);
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request);
}

function apiUpgradePacket($redirect_url, $cookie, $params) {
    $apiAccessor = getApi();
    $request = $apiAccessor->postRequest('userpacket/payment/pay?externalRedirect=true&success=' . $redirect_url);
    $request = $apiAccessor->authenticate($request);
    $request->setCookieJar($cookie);
    $request->sends(Mime::JSON);
    $request->body($params);
    $request->addHeader('Content-Lenght', strlen($params));
    return $apiAccessor->execute($request);
}

function apiGetPacket() {
    $apiAccessor = getApi();
    $request = $apiAccessor->getRequest('userpacket/' . $apiAccessor->username);
    return $apiAccessor->execute($request, Mime::JSON);
}

function apiCommercialPacket() {
    $apiAccessor = getApi();
    $request = $apiAccessor->getRequest('commercialpacket');
    return $apiAccessor->execute($request, Mime::JSON);
}

//PROGRAMMING
function apiProgrammingPool() {
    $apiAccessor = getApi();
    $request = $apiAccessor->getRequest('programmingPool');
    $request = $apiAccessor->authenticate($request);
    $request->sendsAndExpects(Mime::JSON);
    $request->addOnCurlOption(CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    return $apiAccessor->execute($request);
}

function apiGetCurrentProgrammings($qs) {
    $apiAccessor = getApi();
    $request = $apiAccessor->getRequest('currentProgramming?' . $qs);
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request);
}

function apiPostProgrammings($qs) {
    $apiAccessor = getApi();
    $request = $apiAccessor->postRequest("programmings");
    $request = $apiAccessor->authenticate($request);
    $request->body($qs);
    return $apiAccessor->execute($request);
}

function apiGetProgrammings() {
    $apiAccessor = getApi();
    $request = $apiAccessor->getRequest('programmings');
    $request = $apiAccessor->authenticate($request);
    $request->sendsAndExpects(Mime::JSON);
    $request->addOnCurlOption(CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    return $apiAccessor->execute($request);
}

function apiRemoveItemProgramming($progId, $qs) {
    $apiAccessor = getApi();
    $request = $apiAccessor->deleteRequest("programming/" . $progId . "?" . $qs);
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request);
}

function apiDeleteProgramming($progId) {
    $apiAccessor = getApi();
    $request = $apiAccessor->deleteRequest("programming/" . $progId);
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request);
}

function apiDetailsProgramming($programming_id) {
    $apiAccessor = getApi();
    $request = $apiAccessor->getRequest('programming/' . $programming_id);
    $request = $apiAccessor->authenticate($request);
    $request->sendsAndExpects(Mime::JSON);
    $request->addOnCurlOption(CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    return $apiAccessor->execute($request, 'application/json');
}

function apiAddItem($progId, $params) {
    $apiAccessor = getApi();
    $request = $apiAccessor->postRequest('programming/' . $progId . '/items');
    $request->body($params);
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request);
}

function apiGetCalendar($progId, $qs) {
    $apiAccessor = getApi();
    $request = $apiAccessor->getRequest("programming/" . $progId . "/calendar?" . $qs);
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request);
}

function apiDeleteItems($progId, $itemId) {
    $apiAccessor = getApi();
    $request = $apiAccessor->deleteRequest("programming/" . $progId . "/item/" . $itemId);
    $request = $apiAccessor->authenticate($request);

    return $apiAccessor->execute($request);
}

function apiUpdateItems($progId, $itemId, $params) {
    $apiAccessor = getApi();
    $request = $apiAccessor->postRequest("programming/" . $progId . "/item/" . $itemId);
    $request->body($params);
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request);
}

function apiMimicItem($progId) {
    $apiAccessor = getApi();
    $request = $apiAccessor->postRequest("programming/" . $progId . "/mimic");
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request);
}

$params = JComponentHelper::getParams('com_wimtvpro');
$basePathWimtv = $params->get('wimtv_basepath');  //"http://192.168.31.198:8082/wimtv-webapp/rest/"; //
$username = $params->get('wimtv_username');
$password = $params->get('wimtv_password');
initApi($basePathWimtv, $username, $password);
?>