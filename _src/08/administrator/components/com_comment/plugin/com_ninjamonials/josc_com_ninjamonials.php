<?php
defined('_JEXEC') or die('Restricted access');

function output($row, $params) {

    require_once(JPATH_SITE.DS.'components'.DS.'com_comment'.DS.'joscomment'.DS.'utils.php');

    $comObject = JOSC_utils::ComPluginObject('com_ninjamonials',$row);
    $comments =  JOSC_utils::execJoomlaCommentPlugin($comObject, $row, $params, true);
    unset($comObject);

    return $comments;
}
?>
