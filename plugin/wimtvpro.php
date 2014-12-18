<?php

defined('_JEXEC') or die;

class plgButtonWimtvpro extends JPlugin {

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
     * Display the button
     *
     * @return array A four element array of (article_id, article_title, category_id, object)
     */
    function onDisplay($name) {
        $js = "
		function jSelectArticle(id, title, catid, object, link, lang) {
			var hreflang = '';
			if (lang !== '') {
				var hreflang = ' hreflang = \"' + lang + '\"';
			}
			var tag = '<a' + hreflang + ' href=\"' + link + '\">' + title + '</a>';
			jInsertEditorText(tag, '" . $name . "');
			SqueezeBox.close();
		}";

        $doc = JFactory::getDocument();
        $doc->addScriptDeclaration($js);



        JHtml::_('behavior.modal');
        $asset = isset($asset) ? $asset : "";
        $author = isset($author) ? $author : "";
        $link = 'index.php?option=com_wimtvpro&amp;inserInto=true&amp;view=mystreamings&amp;tmpl=component&amp;e_name=' . $name . '&amp;asset=' . $asset . '&amp;author=' . $author;

        $button = new JObject();
        $button->set('modal', true);
        $button->set('link', $link);
        $button->set('text', JText::_('Video & Playlist'));
        $button->set('name', 'article');
        $button->set('options', "{handler: 'iframe', size: {x: 900, y: 420}}");

        return $button;
    }
}

?>