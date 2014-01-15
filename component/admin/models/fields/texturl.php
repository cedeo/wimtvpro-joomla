<?php
 
defined('JPATH_BASE') or die;
 
jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('text');
 
/**
 * Custom Field class for the Joomla Framework.
 *
 * @package             Joomla.Administrator
 * @subpackage          com_wimtvpro
 * @since               1.6
 */
class JFormFieldtexturl extends JFormField 
{
        /**
         * The form field type.
         *
         * @var         string
         * @since       1.6
         */
        protected $type = 'texturl';
 
        /**
         * Method to get the field options.
         *
         * @return      array   The field option objects.
         * @since       1.6
         */
        public function getInput()
        {
			
        	$html = '<select style=" height:100px"  multiple="multiple" name="'.$this->name.'">';
        	$app = &JFactory::getApplication();
        	$params = JComponentHelper::getParams('com_wimtvpro');
        	$basePathWimtv = $params->get('wimtv_basepath');
        	$username = $params->get('wimtv_username');
        	$password = $params->get('wimtv_password');
        	
        	 $html = '<input type="text" name="'. $this->name.'" value="'. $this->value .'" size="100" maxlength="800" id="edit-url">';
                
                $html .= '<div class="description" style="clear:both">' .  JText::_("COM_WIMTVPRO_FIELD_URL");

                $html .= '
                       <b class="createUrl" style="';
                    
			if ($this->value=="") $html .= 'display: inline;';
				else $html .= 'display: none;';
				$html .= '">' .  JText::_("COM_WIMTVPRO_FIELD_URL_OBTAIN") . '</b>
				   <b class="removeUrl"';
		    
        	if ($this->value=="") $html .= 'display: none;';
		    else $html .= 'display: block;';
		    $html .= '>REMOVE YOUR URL</b>
		       <br><div class="passwordUrlLive">Password Live is missing, insert a password for live streaming: <input type="password" id="passwordLive"> <b class="createPass">Salva</b>
		       </div>
		     </div>';
        	$urlscript =  "components/com_wimtvpro/includes/script.php";
		     $html .= '
		     		<script>
		     		jQuery(document).ready(function(){ 
		     		
		     		//Request new URL for create a wimlive Url
						jQuery(".createUrl").click(function(){
						  jQuery.ajax({
								context: this,
								url:  "' . $urlscript . '", 
								type: "GET",
								dataType: "html",
								data:{
									namefunction: "urlCreate",
						     		username: "' . $username . '",
						     		password: "' . $password  . '",
						     		basePath: "' . $basePathWimtv . '",
									titleLive: jQuery("#edit-name").val()	
								},
								success: function(response) {
								  var json =  jQuery.parseJSON(response);
								  var result = json.result;
								  if (result=="SUCCESS"){
								  	jQuery("#edit-url").attr("readonly", "readonly");
								  	jQuery("#edit-url").attr("value", json.liveUrl);
								  	jQuery(this).hide();
									jQuery(".removeUrl").show();
								  } else {
								    //alert (response);
								    alert ("Insert a password for live streaming is required");
								    jQuery(".passwordUrlLive").show();
								    jQuery(".createPass").click(function(){
								     jQuery.ajax({
								     context: this,
								     url:  "components/com_wimtvpro/includes/script.php", 
								     type: "GET",
								     dataType: "html",
								     data:{
									  namefunction: "passCreate",
									  newPass: jQuery("#passwordLive").val()
								     },
					                 success: function(response) {
					                 	alert (response);
					                 	jQuery(".passwordUrlLive").hide();
					                 }
								    });
						            });
								  }
								},
								error: function(request,error) {
									alert(request.responseText);
								}	
							});
					     });
					   jQuery(".removeUrl").click(function(){
					     jQuery(this).hide();
					     jQuery(".createUrl").show();
					     jQuery("#edit-url").removeAttr("readonly");
					     jQuery("#edit-url").removeAttr("disabled");
					     jQuery("#edit-url").val("");	
					   });
							     		
		     		});
		     		
		     		</script>
		     		
		     		';
		     
                return $html;
        }
}