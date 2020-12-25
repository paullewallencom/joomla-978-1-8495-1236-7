<?php
defined('_JEXEC') or die('Restricted access');
/*
 * include the following instructions :
<!-- START joomlacomment INSERT -->
<div class="" style="">
<?php
	$option = JRequest::getCMD('option');
	require_once(JPATH . DS . 'administrator' . DS . 'components' . DS . 'com_comment' . DS . 'plugin' . DS . $option . DS . 'josc_com_REPLACEnewplugin.php');
?>
</div>
<!-- END OF joomlacomment INSERT -->

 * 	in the following file :
 *	components/com_REPLACEnewplugin/...
 *  at the following place : ....
 */

	$option = JRequest::getCMD('option');
	require_once(JPATH_SITE . DS . 'components' . DS. 'com_comment' . DS . 'joscomment' . DS . 'utils.php');

	$comObject = JOSC_utils::ComPluginObject($option,$REPLACErow);
	echo JOSC_utils::execJoomlaCommentPlugin($comObject, $REPLACErow, $REPLACEparams, true);
	unset($comObject);
?>
