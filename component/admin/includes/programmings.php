<?php

/**
 * Created by JetBrains PhpStorm.
 * User: walter
 * Date: 24/02/14
 * Time: 14.43
 * To change this template use File | Settings | File Templates.
 */
/**
 * Le funzioni vengono chiamate dal js che si occupa di gestire le funzioni di creazione e modifica dei palinsesti.
 * Il routing viene fatto in base al parametro GET 'api'.
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

error_reporting(0);
require_once ( realpath(".") . "/api/wimtv_api.php" );

header('Access-Control-Allow-Origin: *');
header('Response: HTTP/1.1 200 OK');

$api = $_GET['api'];
switch ($api) {
    case "calendar":
        calendar();
        break;
    case "addItem":
        addItem();
        break;
    case "pool":
        pool();
        break;
    case "currentProgramming":
        currentProgramming();
        break;
    case "programmings":
        programmings();
        break;
    case "removeItem":
        removeItem();
        break;
    case "deleteItems":
        deleteItems();
        break;
    case "updateItem":
        updateItem();
        break;

    default:
        echo "ERRORE";
}

function calendar() {
    header('Content-type: application/json');

    $qs = $_SERVER['QUERY_STRING'];
    parse_str($qs, $qs_array);
    $progId = $qs_array['progId'];

    $response = apiGetCalendar($progId, $qs);
    echo $response;
    die();
}

function addItem() {
    header('Content-type: application/json');

    $qs = $_SERVER['QUERY_STRING'];
    parse_str($qs, $qs_array);
    $progId = $qs_array['progId'];

    $response = apiAddItem($progId, $_POST);
    echo $response;
    die();
}

function pool() {
    header('Content-type: text/html');

    $qs = $_SERVER['QUERY_STRING'];
    parse_str($qs, $qs_array);
    $response = apiProgrammingPool();
    echo $response;

    die();
}

function currentProgramming() {
    header('Content-type: text/html');

    $qs = $_SERVER['QUERY_STRING'];
    parse_str($qs, $qs_array);
    $response = apiGetCurrentProgrammings($qs);
    echo $response;
    die();
}

function programmings() {
    header('Content-type: application/json');

    $response = apiPostProgrammings($_POST);
    echo $response;
    die();
}

function removeItem() {
    header('Content-type: application/json');

    $qs = $_SERVER['QUERY_STRING'];
    parse_str($qs, $qs_array);
    $progId = $qs_array['progId'];

    $response = apiRemoveItemProgramming($progId, $qs);
    echo $response;
    die();
}

function deleteItems() {
    header('Content-type: application/json');

    $qs = $_SERVER['QUERY_STRING'];
    parse_str($qs, $qs_array);
    $progId = $qs_array['progId'];
    $itemId = $qs_array['itemId'];

    $response = apiDeleteItems($progId, $itemId);
    echo $response;
    die();
}

function updateItem() {
    header('Content-type: application/json');

    $qs = $_SERVER['QUERY_STRING'];
    parse_str($qs, $qs_array);
    $progId = $qs_array['progId'];
    $itemId = $qs_array['itemId'];

    $response = apiUpdateItems($progId, $itemId, $_POST);
    echo $response;
    die();
}

function mimicItem() {
    header('Content-type: application/json');
    $qs = $_SERVER['QUERY_STRING'];
    parse_str($qs, $qs_array);
    $progId = $qs_array['progId'];

    $response = apiMimicItem($progId);
    echo $response;
    die();
}