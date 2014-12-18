<?php

/**
 * Questo file viene chiamato direttamente via richiesta HTTP, ed espone alcune funzioni necessarie al funzionamento del plugin.
 * Le funzioni eseguite vengono scelte in base al parametro GET 'namefunction'.
 */
error_reporting(0);

require_once ( "api/wimtv_api.php" );

// NS:
//$function = "";
//$id = "";
//$acid = "";
//$ordina = "";
//
//if (isset($_GET['namefunction']))
//    $function = $_GET["namefunction"];
//if (isset($_GET['id']))
//    $id = $_GET['id'];
//if (isset($_GET['acquiredId']))
//    $acid = $_GET['acquiredId'];
//if (isset($_GET['showtimeId']))
//    $stid = $_GET['showtimeId'];
//if (isset($_GET['ordina']))
//    $ordina = $_GET['ordina'];

$function = isset($_GET['namefunction']) ? $_GET['namefunction'] : "";
$id = isset($_GET['id']) ? $_GET['id'] : "";
$acid = isset($_GET['acquiredId']) ? $_GET['acquiredId'] : "";
$ordina = isset($_GET['ordina']) ? $_GET['ordina'] : "";

//$username = $_GET['username'];
//$password = $_GET['password'];
//$basePath = $_GET['basePath'];
//$credential = $username . ":" . $password;

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
        // NS: We changed the behaviour of this "case" because 
        // the method apiChangePassword(...) calls a remote API that
        // seems to be unavailable.
        // We now call apiEditProfile(...)
        // $response = apiChangePassword($_GET['newPass']);
        // echo $response;
        // die();

        $params = array();
        $params["liveStreamPwd"] = $_GET['newPass'];
        $response = apiEditProfile($params);
        $arrayjsonst = json_decode($response);
        $response_string = "";
        if ($arrayjsonst->result == "SUCCESS") {
            $response_string = _e("Update successful", "wimtvpro");
        } else {
            foreach ($arrayjsonst->messages as $message) {
                $response_string .= $message->field . " : " . $message->message . "<br/>";
            }
        }
        return $response_string;
        break;
    case "createIframe":
        /**
         * Ritorna l'iframe di un video embedded, prendendolo attraverso una chiamata alle api di wim.tv
         */
        if (isset($directorySkin) && $directorySkin != "") {
            $skin = "&skin=" . $directorySkin;
        } else {
            $skin = "";
        }
        $params = "get=1&width=" . $_GET["width"] . "px&height=" . $_GET["height"] . "px" . $skin;
        $iframeInsert = apiGetPlayerShowtime($id, $params);

        // NS: the restored parameters dont give the expected resize behaviour
        // hence we mangle the response iframe on the fly
        $pattern = "/width=\"(\d+)\" height=\"(\d+)\"/";
        $replacement = "width=\"" . $_GET["width"] . "\" height=\"" .  $_GET["height"] . "\"";
        $iframeInsert = preg_replace($pattern, $replacement, $iframeInsert);

        echo $iframeInsert;
        break;
}
?>