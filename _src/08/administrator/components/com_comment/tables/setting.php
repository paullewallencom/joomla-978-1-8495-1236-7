<?php

/*
 * the #__comment_settings table stores all !jocomment plugin settings
 */
class TableSetting extends JTable {
	/** @var int Primary key */
	var $id 				= 1;
	/** @var string */
	var $set_name			= '';
	/** @var string */
	var $set_component			= '';
	/** @var integer */
	var $set_sectionid			= 0;
	/** @var string */
	var $params				= null;

    function __construct(&$db)
    {
        parent::__construct('#__comment_setting', 'id', $db);
    }

}
?>
