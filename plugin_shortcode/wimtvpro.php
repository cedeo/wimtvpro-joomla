<?php

defined('_JEXEC') or die;
$wimtv_includes_path = realpath(dirname(__FILE__)."/../../../administrator/components/com_wimtvpro/includes");
require_once ($wimtv_includes_path . "/api/wimtv_api.php" );
require_once ( $wimtv_includes_path . "/function.php" );

class plgContentWimtvpro extends JPlugin {

    /**
     * Constructor
     *
     * @access      protected
     * @param       object  $subject The object to observe
     * @param       array   $config  An array that holds the plugin configuration
     * @since       1.5
     */
    public function __construct(& $subject, $config) {
        parent::__construct($subject, $config);
        $this->loadLanguage();
    }
/**
 * Shortcode pattern is: [wimtv]07584726-388a-46b7-b519-b075efc4b6a4|500|280[/wimtv]
 */
    public function onContentPrepare($content, $article, $params, $limit) {
        $nameSkin = JComponentHelper::getParams('com_wimtvpro')->get('nameSkin');
        preg_match_all('/\[wimtv\](.*?)\[\/wimtv\]/is', $article->text, $matches);
        $i = 0;
        foreach ($matches[0] as $match) {
            $shortcode_content = $matches[1][$i];
            $format_video = explode("|", $shortcode_content);
            $jsonst = wimtvpro_detail_showtime(TRUE, $format_video[0]);
            $arrayjsonst = json_decode($jsonst);
            if (isset($arrayjsonst->{"showtimeIdentifier"})) {
//                $showtimeidentifier = $arrayjsonst->{"showtimeIdentifier"};
                $contentid = $arrayjsonst->{"contentId"};
                if ($nameSkin) {
                    $directory = file_create_url('public://skinWim');
                    $skin = $directory . "/" . $params->get('nameSkin') . ".zip";
                } else {
                    // NS: IN THIS CASE WE PASS NO SKIN (see also wimtvpro_embedded()
                    // in: wimtvpro/required/embedded.inc
                    //$base_url . "/" . drupal_get_path('module', 'wimtvpro') . "/skin/default.zip";
                    $skin = "";
                }
                // NS: WE HAVE RESTORED THE PREVIOUS (commented) PARAMETERS
                // $params = "get=1&skin=".$skin;
                $apiParams = "get=1&width=" . $format_video[1] . "&height=" . $format_video[2] . "&skin=" . $skin;

                $response = apiGetPlayerShowtime($contentid, $apiParams); // curl_exec($ch);
                // NS: the restored parameters dont give the expected resize behaviour
                // hence we mangle the response iframe on the fly
                $pattern = "/width=\"(\d+)\" height=\"(\d+)\"/";
                $replacement = "width=\"" . $format_video[1] . "\" height=\"" . $format_video[2] . "\"";
                $iframe = preg_replace($pattern, $replacement, $response);
                $article->text = str_replace($match, $iframe, $article->text);
            } else {
                $iframe = JText::_("The video isn't into WimVod");
                $article->text = str_replace($match, $iframe, $article->text);
            }
            $i++;
        }
    }

}
?>