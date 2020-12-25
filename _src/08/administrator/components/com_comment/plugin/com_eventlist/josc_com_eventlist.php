<?php
defined('_JEXEC')  or die('Restricted access');
/*
 * include the following instructions in com_eventlist/views/details/tmpl/default.php :

<!-- START joomlacomment INSERT -->
<div class="details" style="">
<?php
	global $option;
	require_once(JPATH_SITE."/administrator/components/com_comment/plugin/$option/josc_com_eventlist.php");
?>
</div>
<!-- END OF joomlacomment INSERT -->

 * 	
 *	
 *
 *  HTML part of the code can be changed ! according to the theme...
 */

	global $option;
	require_once(JPATH_SITE.DS.'components'.DS.'com_comment'.DS.'joscomment'.DS.'utils.php');
	$comObject = JOSC_utils::ComPluginObject($option,$this->row);
	echo JOSC_utils::execJoomlaCommentPlugin($comObject, $this->row, $params, true);
	unset($comObject);
?>
