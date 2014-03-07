<?php
/**
 * Questo file viene chiamato direttamente via richiesta HTTP, ed espone alcune funzioni necessarie al funzionamento del plugin.
 * Le funzioni eseguite vengono scelte in base al parametro GET 'namefunction'.
 */
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

  $username = $_GET['username'];
  $password = $_GET['password'];
  $basePath = $_GET['basePath'];
  $credential = $username . ":" . $password;

  switch ($function) {
    
    case "urlCreate":
        /**
         * Richiede che venga passato anche come parametro GET 'titleLive'.
         * Crea e ritorna l'url del video live con titolo passato.
         */
        $response = apiCreateUrl(urlencode($_GET['titleLive']));
        echo $response;
        break;
    case "passCreate":
        /**
         * Richiede che venga passato anche come parametro GET 'newPass'.
         * Cambia la password dei live dell'utente autenticato.
         */
        $response = apiChangePassword($_GET['newPass']);
        echo $response;
        break;
    case "createIframe":
        /**
         * Ritorna l'iframe di un video embedded, prendendolo attraverso una chiamata alle api di wim.tv
         */
        if ($directorySkin!="") {
			$skin = "&skin=" . $directorySkin;
		}
		$params = "get=1&width=" . $_GET["width"] . "px&height=" . $_GET["height"] . "px". $skin;
		$iframeInsert = apiGetPlayerShowtime($id, $params);
    	echo $iframeInsert;
        break;
  }
    
?>