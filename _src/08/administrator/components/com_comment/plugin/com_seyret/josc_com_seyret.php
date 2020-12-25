<?php
defined('_JEXEC')  or die('Restricted access');
/*
 * enable joomlacomment from seyrets backend
 * 
 *  HTML part of the code can be changed ! according to the theme...
 */
	global $option;
	require_once(JPATH_SITE."/components/com_comment/joscomment/utils.php");

	$database = JFactory::getDBO();
	$database->setQuery( "SELECT * FROM #__seyret_items WHERE id = '$id' LIMIT 1" );
	$row = $database->loadObjectList();
	$comObject = JOSC_utils::ComPluginObject($option,$row[0]);
	$params=null;
	$comments=JOSC_utils::execJoomlaCommentPlugin($comObject, $row[0], $params, true);
	unset($comObject);
?>
