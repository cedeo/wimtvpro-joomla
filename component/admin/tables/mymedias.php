<?php

// No direct access
defined('_JEXEC') or die('Restricted access');

class wimtvproTablemymedias extends JTable {

    function __construct(&$db) {
        parent::__construct('#__wimtvpro_videos', 'id', $db);
    }

}