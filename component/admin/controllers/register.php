<?php


// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');
jimport( 'joomla.form.form' );
/**
 * Questo controller gestisce la view che permette la registrazione di un utente su wim.tv
 */
class WimtvproControllerRegister extends JControllerForm
{
	public function __construct($config = array())
	{
		$this->view_list = 'mymedias';
		parent::__construct($config);
	}
	
	public function getModel($name = 'register', $prefix = 'wimtvproModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}

	public function save()
	{
		JHtml::_('behavior.modal');
		$app = &JFactory::getApplication();
		$model          = $this->getModel();
		$task           = $this->getTask();
		$context        = "$this->_option.edit.$this->_context";
		$params = JComponentHelper::getParams('com_wimtvpro');

		jimport('joomla.filesystem.file');
		$jform = JRequest::getVar('jform');
		$data	= JRequest::getVar('jform', array(), 'post', 'array');
		$rep_password = $jform['main']['reg_RepeatPassword'];
		$password = $jform['main']['reg_Password'];
		$acceptEula = $jform['main']['reg_acceptEula'];
		$name = $jform['main']['reg_name'];
		$surname = $jform['main']['reg_surname'];
		$email =  $jform['main']['reg_email'];
		$username = $jform['main']['reg_Username'];
		$sex = $jform['main']['reg_sex'];

        $post = array("acceptEula"  => $acceptEula,
                      "name"        => $name,
                      "surname"     => $surname,
                      "email"       => $email,
                      "username"    => $username,
                      "password"    => $password,
                      "role"        => "webtv",
                      "sex"         => $sex,
                      "dateOfBirth" => "01/01/1900");

		$response = apiRegistration($post);
		$arrayjsonst = json_decode($response);

		$context        = "com_wimtvpro.edit.register";
		
		$return = parent::save();
	
		if ($arrayjsonst){
			if ($arrayjsonst->result=="SUCCESS") {
				$message = $arrayjsonst->message;
				
				$params->set('wimtv_username', $username);
				$params->set('wimtv_password', $password);
				
				$app->enqueueMessage($message,'success');
				
				// Get a new database query instance
				$db = JFactory::getDBO();
				$query = $db->getQuery(true);
				
				// Build the query
				$query->update('#__extensions AS a');
				$query->set('a.params = ' . $db->quote((string)$params));
				$query->where('a.element = "com_wimtvpro"');
				
				// Execute the query
				$db->setQuery($query);
				$db->query();
				
				$conf = JFactory::getConfig();
				
				$options = array(
						'defaultgroup' => '_system',
						'cachebase' => $conf->get('cache_path', JPATH_SITE . '/cache')
				);
				
				$cache = JCache::getInstance('callback', $options);
				$cache->clean();
				
				$this->setRedirect('index.php?option=com_wimtvpro&view=register&tmpl=component&layout=edit&refresh=1', $message);
				
				return true;
				
				
			} else {
				foreach ($arrayjsonst->messages as $message){
					$testoErrore .=  $message->field . " : " .  $message->message . "<br/>";
				}	
				$message = $testoErrore;
				$app->enqueueMessage($message, 'error');
				$app->setUserState('com_wimtvpro.edit.register.data', $data);
				$this->setRedirect('index.php?option=com_wimtvpro&view=register&tmpl=component&layout=edit', false);
				
			
			}
		}

		
	
		
	}
	
	
	

}
