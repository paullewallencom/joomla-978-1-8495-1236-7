<?php
defined('_JEXEC') or die('Restricted access');
$option = JRequest::getCMD('option');
require_once(JPATH_SITE . DS . 'components' . DS. 'com_comment' . DS . 'joscomment' . DS . 'utils.php');
$comObject = JOSC_utils::ComPluginObject($option, $this->row, 0, $this->row->category_id);
echo JOSC_utils::execJoomlaCommentPlugin($comObject, $this->row, $this->row, true);
unset($comObject);
?>
