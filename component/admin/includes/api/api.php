<?php


namespace Api;

require("Httpful/Bootstrap.php");
\Httpful\Bootstrap::init();

/**
 * Written by walter at 16/10/13
 */
use \Httpful\Request;
use \Httpful\Mime;

class Api {
    private $host = null;
    public $username = null;
    public $liveHostsUrl;
    public $password = null;
    private static $instance;
    private static $analytics;

    function __construct($host, $username, $password) {
        $this->host = $host;
        $this->username = $username;
        $this->liveHostsUrl = 'liveStream/' . $this->username . '/' . $this->username . '/hosts';
        $this->password = $password;
    }

    static function getApiAccessor() {
        return Api::$instance;
    }

    static function initApiAccessor($host, $username, $password) {
        Api::$instance = new Api($host, $username, $password);
    }

    static function getAnalyticsApi() {
        return Api::$analytics;
    }

    static function initAnalyticsApi($host, $username, $password) {
        Api::$analytics = new Api($host, $username, $password);
    }

    function compileUrl($subUrl) {
        $url = $this->host . $subUrl;
        return $url;
    }

    function getRequest($subUrl) {
        $url = $this->compileUrl($subUrl);
        return Request::get($url);
    }

    function postRequest($subUrl) {
        $url = $this->compileUrl($subUrl);
        $request = Request::post($url);
        $request->sendsType(Mime::FORM);
        return $request;
    }

    function deleteRequest($subUrl) {
        $url = $this->compileUrl($subUrl);
        return Request::delete($url);
    }

    function downloadRequest($subUrl) {
        $request = Api::getRequest($subUrl);
        $request->no_body = true;
        return $request;
    }

    function authenticate($request) {
        return $request->authenticateWith($this->username, $this->password);
    }

    function execute($request, $expectedMimeType='text/html', $defaultLanguage=null) {
        $request->expects($expectedMimeType);
        $request->addHeader("X-Wimtv-Pro-Plugin-Name", "joomla");
        if (!$defaultLanguage) {
            $request->addHeader('Content-language', $_SERVER["HTTP_ACCEPT_LANGUAGE"]);
            $request->addHeader('Accept-Language', $_SERVER["HTTP_ACCEPT_LANGUAGE"]);
        } else {
            $request->addHeader('Content-language', $defaultLanguage);
            $request->addHeader('Accept-Language', $defaultLanguage);
        }
        $request->_curlPrep();
        try {
            $result = $request->send();
        } catch (\Exception $exception) {
            // NS
            // var_dump($request);
            // trigger_error($exception->getMessage(), E_USER_NOTICE);
            $result = "";
        }
        return $result;
    }

}


?>
