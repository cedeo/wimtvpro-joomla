<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Script file of com_wimtvpro component
 */
class com_wimtvproInstallerScript {

    /**
     * Method to install the extension
     * $parent is the class calling this method
     *
     * @return void
     */
    function install($parent) {
        // echo '<p>The module has been installed</p>';
        //$parent->getParent()->setRedirectURL('index.php?option=com_wimtvpro');
    }

    /**
     * Method to uninstall the extension
     * $parent is the class calling this method
     *
     * @return void
     */
    function uninstall($parent) {
        // echo '<p>' . JText::_('COM_WIMTVPRO_UNINSTALL_TEXT') . '</p>';
        // echo '<p>The module has been uninstalled</p>';
    }

    /**
     * Method to update the extension
     * $parent is the class calling this method
     *
     * @return void
     */
    function update($parent) {
        // $parent is the class calling this method
        // echo '<p>' . JText::sprintf('COM_WIMTVPRO_UPDATE_TEXT', $parent->get('manifest')->version) . '</p>';
        // echo '<p>The module has been updated to version' . $parent->get('manifest')->version . '</p>';
    }

    /**
     * Method to run before an install/update/uninstall method
     * $parent is the class calling this method
     * $type is the type of change (install, update or discover_install)
     *
     * @return void
     */
    function preflight($type, $parent) {
        // $parent is the class calling this method
        // $type is the type of change (install, update or discover_install)
        // echo '<p>' . JText::_('COM_WIMTVPRO_PREFLIGHT_' . $type . '_TEXT') . '</p>';
        // echo '<p>Anything here happens before the installation/update/uninstallation of the module</p>';
    }

    /**
     * Method to run after an install/update/uninstall method
     * $parent is the class calling this method
     * $type is the type of change (install, update or discover_install)
     *
     * @return void
     */
    function postflight($type, $parent) {
        // $parent is the class calling this method
        // $type is the type of change (install, update or discover_install)
        // echo '<p>' . JText::_('COM_WIMTVPRO_POSTFLIGHT_' . $type . '_TEXT') . '</p>';
        // echo '<p>Anything here happens after the installation/update/uninstallation of the module</p>';

        $db = JFactory::getDbo();

        // Fields to update.
        $defaultRules = '{"core.admin":{"7":1},"core.manage":{"7":1}}';
        $fields = array(
            "rules = '" . $defaultRules . "'"
        );

        // Conditions for which records should be updated.
        $conditions = array(
            $db->quoteName('name') . " = 'com_wimtvpro'",
            $db->quoteName('title') . " = 'com_wimtvpro'",
        );

        $query = $db->getQuery(true);
        $query->update($db->quoteName('#__assets'))
                ->set($fields)
                ->where($conditions);

        $db->setQuery($query);

        try {
            $db->query(); // Use $db->execute() for Joomla 3.0.
        } catch (Exception $e) {
            throw new Exception($e);
        }

        // STORE wimtv_basepath PARAMETER TO ALLOW USER FIRST REGISTRATION
        $this->presetParameters();
    }

    function presetParameters() {
        // STORE wimtv_basepath PARAMETER TO ALLOW USER FIRST REGISTRATION
        $db = JFactory::getDbo();
        $params = JComponentHelper::getParams('com_wimtvpro');
        $params->set('wimtv_basepath', 'https://www.wim.tv/wimtv-webapp/rest/');
        $params->set('wimtv_replaceContentWimtv', "{contentIdentifier}");
        $params->set('wimtv_urlThumbsWimtv', "videos/{contentIdentifier}/thumbnail");
        $params->set('wimtv_heightPreview', "280");
        $params->set('wimtv_widthPreview', "500");
        $params->set('wimtv_nameSkin', "wimtv");

        $query = $db->getQuery(true);
        $params = JComponentHelper::getParams('com_wimtvpro');
        // Build the query
        $query->update('#__extensions AS a');
        $query->set('a.params = ' . $db->quote((string) $params));
        $query->where('a.element = "com_wimtvpro"');

        // Execute the query
        $db->setQuery($query);
        $db->query();
    }

}