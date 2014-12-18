<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');
require_once ( JPATH_BASE . "/components/com_wimtvpro/includes/api/wimtv_api.php" );

/**
 * Questo controller gestisce la view che permette la creazione e la modifica di un evento live.
 */
class WimtvproControllerwimlive extends JControllerForm {

    public function __construct($config = array()) {
        $this->view_list = 'wimlives';
        parent::__construct($config);
    }

    public function getModel($name = 'wimlive', $prefix = 'wimtvproModel') {
        $model = parent::getModel($name, $prefix, array('ignore_request' => true));
        return $model;
    }

    public function save() {
        $app = &JFactory::getApplication();
        $params = JComponentHelper::getParams('com_wimtvpro');
        $basePathWimtv = $params->get('wimtv_basepath');
        $username = $params->get('wimtv_username');
        $password = $params->get('wimtv_password');
        $credential = $username . ":" . $password;

        $directory = JPATH_COMPONENT . DS . "uploads" . DS;
        $error = 0;

        //Retrieve file details from uploaded file, sent from upload form
        $file = JRequest::getVar('jform', null, 'files', 'array');
        //Import filesystem libraries. Perhaps not necessary, but does not hurt
        jimport('joomla.filesystem.file');

        $jform = JRequest::getVar('jform');
        $name = $jform['name'];
        $payperview = $jform['payperview'];
        $url = $jform['Url'];
        $public = $jform['Public'];
        $record = $jform['Record'];
        $orain = $jform['Ora'];
        $durationin = $jform['Duration'];
        $timelive = $jform['timelivejs'];
        $giorno = str_replace("-", "/", $jform['Giorno']);

        if (strlen(trim($name)) == 0) {
            JError::raiseWarning(100, JText::_("COM_WIMTVPRO_LIVE_ERROR_TITLE"));
            $error++;
        }
        if (strlen(trim($payperview)) == 0) {
            JError::raiseWarning(100, JText::_("COM_WIMTVPRO_LIVE_ERROR_PRICE"));
            $error++;
        }
        if (strlen(trim($url)) == 0) {
            JError::raiseWarning(100, JText::_("COM_WIMTVPRO_LIVE_ERROR_URL"));
            $error++;
        }
        if (strlen(trim($giorno)) == 0) {
            JError::raiseWarning(100, JText::_("COM_WIMTVPRO_LIVE_ERROR_DAY"));
            $error++;
        }
        if (strlen(trim($orain)) == 0) {
            JError::raiseWarning(100, JText::_("COM_WIMTVPRO_LIVE_ERROR_HOUR"));
            $error++;
        }
        if (strlen(trim($durationin)) == 0) {
            JError::raiseWarning(100, JText::_("COM_WIMTVPRO_LIVE_ERROR_TIME"));
            $error++;
        }

        if (!isset($public)) {
            JError::raiseWarning(100, "You must check if you event is public or private.");
            $error++;
        }

        $ccy = "EUR";
        $pricePerView = "0";

        if ($error == 0) {

            if ($payperview == "0") {
                $typemode = "FREEOFCHARGE";
                $pricePerView = "0";
            } else {
//                $paymentCode = apiGetUUID();
//                $typemode = "PAYPERVIEW&pricePerView=" . $payperview . "&ccy=EUR&paymentCode=" . $paymentCode;
                $typemode = "PAYPERVIEW";
                $pricePerView = $payperview;
            }

            if ($orain != "")
                $ora = explode(":", $orain);
            else {
                $ora[0] = "";
                $ora[1] = "";
            }
            if ($durationin != "") {
                $separe_duration = explode(":", $durationin);
                $duration = ($separe_duration[0] * 60) + $separe_duration[1];
            } else {
                $duration = 0;
            }

            // GET A PAYMENT CODE FROM SERVER
            $paymentCodeResponse = apiGetUUID();
            $paymentCode = isset($paymentCodeResponse->body) ? $paymentCodeResponse->body : "";

            $post = array(
                "name" => $name,
                "url" => $url,
                "eventDate" => $giorno,
                "paymentMode" => $typemode,
                "eventHour" => $ora[0],
                "eventMinute" => $ora[1],
                "duration" => $duration,
                "durationUnit" => "Minute",
                "publicEvent" => $public,
                "recordEvent" => $record,
                "timezone" => $timelive,
                "paymentCode" => $paymentCode,
                "pricePerView" => $pricePerView,
                "ccy" => $ccy
            );

            if (isset($_GET["cid"]) && $_GET["cid"] != "") {
                $response = apiModifyLive($_GET['cid'], $post, $timelive);
            } else {
                $response = apiAddLive($post, $timelive);
            }

            if ($response != "") {
                $message = json_decode($response);

                $result = $message->{"result"};
                if ($result == "SUCCESS") {
                    // Redirect Wimlive
                    $link = "index.php?option=com_wimtvpro&view=wimlives";
                    JFactory::getApplication()->enqueueMessage(JText::_("COM_WIMTVPRO_LIVE_UPDATE"));
                    $this->setRedirect('index.php?option=com_wimtvpro&view=wimlives', $message);
                } else {
                    $formset_error = "";
                    foreach ($message->messages as $value) {
                        if ($value->message != "") {
                            $formset_error .= $value->field . "=" . $value->message;
                        }
                    }
                    // NS
                    $enqueueMessage = "API wimtvpro error: " . $formset_error . "</strong></p>" . $result;
                    JError::raiseWarning(100, JText::_($enqueueMessage));
                    $this->setRedirect('index.php?option=com_wimtvpro&view=wimlive&layout=edit', false);
                }
            }
        } else {

            $this->setRedirect('index.php?option=com_wimtvpro&view=wimlive&layout=edit', false);
        }
    }

    public function edit() {
        $input = JFactory::getApplication()->input;
        //var_dump ($input);
        $pks = $input->post->get('cid', array(), 'array');

        if (count($pks) > 1) {
            JFactory::getApplication()->enqueueMessage("You can select only one event to edit");
            $this->setRedirect('index.php?option=com_wimtvpro&view=wimlives');
        } else {
            $this->setRedirect('index.php?option=com_wimtvpro&view=wimlive&layout=edit&cid=' . $pks[0]);
        }
    }

    public function delete() {
        $input = JFactory::getApplication()->input;
        //var_dump ($input);
        $pks = $input->post->get('cid', array(), 'array');
        foreach ($pks as $id) {
            $response = apiDeleteLive($id);
            $message = $response;
        }
        //TODO: che cos'è $errorRemove??

        if ($errorRemove == 0) {

            //JFactory::getApplication()->enqueueMessage($message);

            $this->setRedirect('index.php?option=com_wimtvpro&view=wimlives', $message);
        } else {
            JError::raiseWarning(100, "Error connection.");
            $this->setRedirect('index.php?option=com_wimtvpro&view=wimlive&layout=edit', $message);
        }
    }

}

