<?php
defined('_JEXEC') or die('Restricted access');

/*
 * the #__comment table stores all !jocomment comments
 */
class TableComment extends JTable {
    var $id = null;
    var $contentid = null;
    var $component = null;
    var $ip = 0;
    var $userid = null;
    var $usertype = null;
    var $date = null;
    var $name = null;
    var $email = null;
    var $website = null;
    var $notify = null;
    var $title = null;
    var $comment = null;
    var $published = 0;
    var $voting_yes = null;
    var $voting_no = null;
    var $parentid = null;
    var $importedtable = null;
    var $importedid = null;
    var $importedparentid = null;

    function __construct(&$db)
    {
	parent::__construct('#__comment', 'id', $db);
    }
}
?>
