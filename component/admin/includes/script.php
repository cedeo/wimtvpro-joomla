<?php

error_reporting(0);

require_once ( "api/wimtv_api.php" );

  $function = "";
  $id="";
  $acid="";
  $ordina = "";

  if (isset($_GET['namefunction']))
    $function= $_GET["namefunction"];
  if (isset($_GET['id']))
    $id = $_GET['id'];
  if (isset($_GET['acquiredId']))
    $acid = $_GET['acquiredId'];
  if (isset($_GET['showtimeId']))
    $stid = $_GET['showtimeId'];
  if (isset($_GET['ordina']))
    $ordina = $_GET['ordina'];
if (isset($_GET['basePath']))
    $directorySkin = $_GET['basePath'];
	
  $username = $_GET['username'];
  $password = $_GET['password'];
  $basePath = $_GET['basePath'];
  $credential = $username . ":" . $password;

  switch ($function) {
    
    case "urlCreate":
      $response = apiCreateUrl(urlencode($_GET['titleLive']));
      echo $response;
    break;
    case "passCreate":
      $response = apiChangePassword($_GET['newPass']);
      echo $response;
    break;
    case "createIframe":
    	if ($directorySkin!="") {
			$skin = "&skin=" . $directorySkin;
		}
		$params = "get=1&width=" . $_GET["width"] . "px&height=" . $_GET["height"] . "px". $skin;
		$iframeInsert = apiGetPlayerShowtime($id, $params);
    	echo $iframeInsert;
    break;
    default:
      echo "Non entro";
  }
    
?>