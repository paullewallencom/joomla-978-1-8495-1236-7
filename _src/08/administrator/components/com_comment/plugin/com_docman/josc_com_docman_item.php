<?php
defined('_JEXEC')  or die('Restricted access');
/*
 * include the following instructions :

<!-- START joomlacomment INSERT -->
<div class="dm_description" style="text-align:right;">
<?php
	global $option;
	require(JPATH_SITE."/administrator/components/com_comment/plugin/$option/josc_com_docman_item.php");
?>
</div>
<!-- END OF joomlacomment INSERT -->

 * 	at the end of the following file :
 *	components/com_docman/themes/.../templates/documents/list_item.tpl.php
 *
 *  HTML part of the code can be changed ! according to the theme...
 */

	global $option;
	require_once(JPATH_SITE."/components/com_comment/joscomment/utils.php");

	$comObject = JOSC_utils::ComPluginObject($option,$this->doc->data);
	echo JOSC_utils::execJoomlaCommentPlugin($comObject, $this->doc->data, $this->doc->data->params, true);
	unset($comObject);
?>
