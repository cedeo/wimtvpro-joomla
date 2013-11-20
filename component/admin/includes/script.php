<?php
  

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

  $username = $_GET['username'];
  $password = $_GET['password'];
  $basePath = $_GET['basePath'];
  $credential = $username . ":" . $password;
  

  switch ($function) {
    
    case "urlCreate":
      $url_createurl = $basePath . "liveStream/uri?name=" . urlencode($_GET['titleLive']);

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL,  $url_createurl);
      curl_setopt($ch, CURLOPT_VERBOSE, 0);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
      curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
      curl_setopt($ch, CURLOPT_USERPWD, $credential);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept-Language:' . $_SERVER['HTTP_ACCEPT_LANGUAGE']));

	  
      $response = curl_exec($ch);
      echo $response;
      curl_close($ch);
    break;
    case "passCreate":
      $url_passcreate =$basePath . "users/" . get_option("wp_userwimtv") . "/updateLivePwd";
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL,  $url_passcreate);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
       curl_setopt($ch, CURLOPT_USERPWD, $credential);
	   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	   curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept-Language:' . $_SERVER['HTTP_ACCEPT_LANGUAGE']));

      curl_setopt($ch, CURLOPT_POSTFIELDS,"liveStreamPwd=" . $_GET['newPass']);      
      $response = curl_exec($ch);
      echo $response;

      curl_close($ch);
	  die();
    break;
    
    
    case "createIframe":

    	if ($directorySkin!="") {
			$skin = "&skin=" . $directorySkin;
		}
		$url = $basePath . "videos/" . $id . '/embeddedPlayers';
		$url .= "?get=1&width=" . $_GET["width"] . "px&height=" . $_GET["height"] . "px". $skin;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,  $url);
		curl_setopt($ch, CURLOPT_VERBOSE, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_USERPWD, $_GET["credential"]);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		//$iframeInsert = urlencode(curl_exec($ch));
		$iframeInsert = curl_exec($ch);
    	echo $iframeInsert;
    
    	curl_close($ch);
    	die();
    break;
    
    
    default:
      echo "Non entro";
      die();
  }
    
?>