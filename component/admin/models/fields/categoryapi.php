<?php
 
defined('JPATH_BASE') or die;
 
jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');
 
/**
 * Custom Field class for the Joomla Framework.
 *
 * @package             Joomla.Administrator
 * @subpackage          com_wimtvpro
 * @since               1.6
 */
class JFormFieldcategoryApi extends JFormField 
{
        /**
         * The form field type.
         *
         * @var         string
         * @since       1.6
         */
        protected $type = 'categoryApi';
 
        /**
         * Method to get the field options.
         *
         * @return      array   The field option objects.
         * @since       1.6
         */
        public function getInput()
        {
        	$html = '<select style=" height:100px"  multiple="multiple" name="'.$this->name.'">';
        	$params = JComponentHelper::getParams('com_wimtvpro');
        	$basePathWimtv = $params->get('wimtv_basepath');
        	
                // Initialize variables.
               // $options = array();
 
                $url_categories = $basePathWimtv . "videoCategories";
                
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url_categories);
                
                curl_setopt($ch, CURLOPT_VERBOSE, 0);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                
                $response = curl_exec($ch);
                $category_json = json_decode($response);
                $options = array();
                foreach ($category_json as $cat) {
                	
                	foreach ($cat as $sub) {
                		$options .= '<optgroup label="' . $sub->name . '">';
                		foreach ($sub->subCategories as $subname) {
                			$options .= '<option value="' . $sub->name . '|' . $subname->name . '">' . $subname->name . '</option>';
                		}
                		$options .= '</optgroup>';
                	}
                }
                curl_close($ch);
             $html .= $options;
             $html .= "</select>";   
                return $html;
        }
}