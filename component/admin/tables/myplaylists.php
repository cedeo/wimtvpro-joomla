<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
class wimtvproTablemyplaylists extends JTable
{
	function __construct( &$db ) {
		parent::__construct('#__wimtvpro_playlist', 'id', $db);
	}
}