<?php
defined('_JEXEC') or die('Restricted access');

$option = 'com_sobi2';
require_once(JPATH_SITE . DS . 'components' . DS. 'com_comment' . DS . 'joscomment' . DS . 'utils.php');
$comObject = JOSC_utils::ComPluginObject($option,$mySobi);
echo JOSC_utils::execJoomlaCommentPlugin($comObject, $mySobi, $mySobi->params, true);
unset($comObject);
?>
