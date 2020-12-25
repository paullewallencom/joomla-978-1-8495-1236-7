<?php
defined('_JEXEC') or die('Restricted access');
$option = JRequest::getCMD('option');
require_once(JPATH_SITE . DS . 'components' . DS. 'com_comment' . DS . 'joscomment' . DS . 'utils.php');
$comObject = JOSC_utils::ComPluginObject($option, $row, 0, $row->catid);
echo JOSC_utils::execJoomlaCommentPlugin($comObject, $row, $row, true);
unset($comObject);
?>
