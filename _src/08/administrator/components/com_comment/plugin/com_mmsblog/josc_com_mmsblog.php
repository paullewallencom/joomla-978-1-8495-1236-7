<?php
defined('_JEXEC') or die('Restricted access');
/*
 * include the following instructions :

<!-- START joomlacomment INSERT -->
<div class="dm_description" style="text-align:center; vertical-align: bottom;">
<?php
global $option;
require(JPATH_SITE."/administrator/components/com_comment/plugin/$option/josc_com_mmsblog.php");
?>
</div>
<!-- END OF joomlacomment INSERT -->

 * 	at the end of the following file :
 *	components/com_mmsblog/views/item/tmpl/default.php
 *
 *  HTML part of the code can be changed ! according to the theme...
 */

	global $option;
	require_once(JPATH_SITE.DS.'components'.DS.'com_comment'.DS.'joscomment'.DS.'utils.php');

	$comObject = JOSC_utils::ComPluginObject($option,$this->row);
	echo JOSC_utils::execJoomlaCommentPlugin($comObject, $this->row, $this->row->params, true);
	unset($comObject);
?>
