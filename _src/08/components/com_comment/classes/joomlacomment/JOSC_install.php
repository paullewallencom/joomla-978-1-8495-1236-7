<?php
defined('_JEXEC') or die('Direct Access to this location is not allowed.');
/***************************************************************
*  $Revision$
*
*  Copyright notice
*
*  Copyright 2009 Daniel Dimitrov. (http://compojoom.com)
*  All rights reserved
*
*  This script is part of the !JoomlaComment project. The !joomlaComment project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*  A copy is found in the textfile GPL.txt and important notices to the license
*  from the author is found in LICENSE.txt distributed with these scripts.
*
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
class JOSC_install {

    function checkCompatibility( &$install_log )
    {
        $database =& JFactory::getDBO();

        /* tables captcha and voting installed in the xml */
        $query = array();

        /*
         * #__comment
         */
        $columns = JOSC_TableUtils::TableColumnsGet( '#__comment' );
        $install_log .= "#__comment update :<br />";
    /*
     *  voting_yes,  voting_no
     */
        $fieldname = 'voting_yes';
        if (!JOSC_TableUtils::TableFieldCheck( $fieldname, $columns )) {
            $query[] = "ALTER TABLE `#__comment` "
            . "\n ADD `voting_yes` INT(10) NOT NULL default '0' "
            . "\n AFTER `published`;"
            ;
            $install_log .= "- update of $fieldname.<br />";
        } else {
            $install_log .= "- $fieldname exist.<br />";
        }
        $fieldname = 'voting_no';
        if (!JOSC_TableUtils::TableFieldCheck( $fieldname, $columns )) {
            $query[] = "ALTER TABLE `#__comment` "
            . "\n ADD `voting_no` INT(10) NOT NULL default '0' "
            . "\n AFTER `voting_yes`;"
            ;
            $install_log .= "- update of $fieldname.<br />";
        } else {
            $install_log .= "- $fieldname exist.<br />";
        }

    /*
     *  parentid
     */
        $fieldname = 'parentid';
        if (!JOSC_TableUtils::TableFieldCheck( $fieldname, $columns )) {
            $query[] = "ALTER TABLE `#__comment` "
            . "\n ADD `parentid` INT(10) NOT NULL default '-1' "
            . "\n AFTER `voting_no`;"
            ;
            $install_log .= "- update of $fieldname.<br />";
        } else {
            $install_log .= "- $fieldname exist.<br />";
        }

    /*
     *  email
     */
        $fieldname = 'email';
        if (!JOSC_TableUtils::TableFieldCheck( $fieldname, $columns )) {
            $query[] = "ALTER TABLE `#__comment` "
            . "\n ADD `email` VARCHAR(50) "
            . "\n AFTER `name`;"
            ;
            $install_log .= "- update of $fieldname.<br />";
        } else {
            $install_log .= "- $fieldname exist.<br />";
        }

    /*
     *  website
     */
        $fieldname = 'website';
        if (!JOSC_TableUtils::TableFieldCheck( $fieldname, $columns )) {
            $query[] = "ALTER TABLE `#__comment` "
            . "\n ADD `website` VARCHAR(100) "
            . "\n AFTER `email`;"
            ;
            $install_log .= "- update of $fieldname.<br />";
        } else {
            $install_log .= "- $fieldname exist.<br />";
        }

    /*
     *  notify
     */
        $fieldname = 'notify';
        if (!JOSC_TableUtils::TableFieldCheck( $fieldname, $columns )) {
            $query[] = "ALTER TABLE `#__comment` "
            . "\n ADD `notify` TINYINT(1) NOT NULL default '0' "
            . "\n AFTER `website`;"
            ;
            $install_log .= "- update of $fieldname.<br />";

            $query[] = "ALTER TABLE `#__comment` "
            . "\n ADD INDEX `contentid` ( `contentid` );"
            ; //optimisation: many search by contentid
            $install_log .= "- create index contentid.<br />";
        } else {
            $install_log .= "- $fieldname exist.<br />";
        }

    /*
     *  userid
     */
        $fieldname = 'userid';
        if (!JOSC_TableUtils::TableFieldCheck( $fieldname, $columns )) {
            $query[] = "ALTER TABLE `#__comment` "
            . "\n ADD `userid` INT(11)"
            . "\n AFTER `ip`;"
            ;
            $install_log .= "- update of $fieldname.<br />";
        } else {
            $install_log .= "- $fieldname exist.<br />";
        }


    /*
     *  component
     */
        $fieldname = 'component';
        if (!JOSC_TableUtils::TableFieldCheck( $fieldname, $columns )) {
            $query[] = "ALTER TABLE `#__comment` "
            . "\n ADD `component` VARCHAR(50)  NOT NULL default '' "
            . "\n AFTER `contentid`;"
            ;
            $install_log .= "- update of $fieldname.<br />";

            $query[] = "ALTER TABLE `#__comment` "
            . "\n ADD INDEX `com_contentid` ( `component`, `contentid` );"
            ; //optimisation: many search by component/contentid
            $install_log .= "- create index com_contentid.<br />";

        } else {
            $install_log .= "- $fieldname exist.<br />";
        }

    /*
     *  importtable
     *  importid
     *  importparentid
     */
        $fieldname = 'importtable';
        if (!JOSC_TableUtils::TableFieldCheck( $fieldname, $columns )) {
            $query[] = "ALTER TABLE `#__comment` "
            . "\n ADD `importtable` VARCHAR(30) NOT NULL default '' "
            . "\n AFTER `parentid`;"
            ;
            $install_log .= "- update of $fieldname.<br />";

        } else {
            $install_log .= "- $fieldname exist.<br />";
        }
        $fieldname = 'importid';
        if (!JOSC_TableUtils::TableFieldCheck( $fieldname, $columns )) {
            $query[] = "ALTER TABLE `#__comment` "
            . "\n ADD `importid` INT(10) NOT NULL default '0' "
            . "\n AFTER `importtable`;"
            ;
            $install_log .= "- update of $fieldname.<br />";

        } else {
            $install_log .= "- $fieldname exist.<br />";
        }
        $fieldname = 'importparentid';
        if (!JOSC_TableUtils::TableFieldCheck( $fieldname, $columns )) {
            $query[] = "ALTER TABLE `#__comment` "
            . "\n ADD `importparentid` INT(10) NOT NULL default '-1' "
            . "\n AFTER `importid`;"
            ;
            $install_log .= "- update of $fieldname.<br />";

        } else {
            $install_log .= "- $fieldname exist.<br />";
        }

    /*
     *  title 30 to 50
     */
        $fieldname = 'title';
        $row = JOSC_TableUtils::TableColumnsGet( '#__comment', 'Field' );
        if ($row && (strtolower($row[$fieldname]->Type)!="varchar(50)")) {
            $query[] = "ALTER TABLE `#__comment` "
            . "\n CHANGE `title` `title` VARCHAR(50) NOT NULL default '' "
            ;
            $install_log .= "- update of $fieldname.<br />";
        }

        /*
         * #__comment_setting
         */
        $columns = JOSC_TableUtils::TableColumnsGet( '#__comment_setting' );
        if (!$columns) {
                /* CREATE TABLE */
            $install_log .= "Create #__comment_setting table.<br />";
            $query[] = JOSC_install::getQuery_Create__comment_setting();

        } else {
                /* UPDATE TABLE */
            $columns = JOSC_TableUtils::TableColumnsGet( '#__comment_setting' );
            $install_log .= "#__comment_setting update :<br />";
            /*
             *  name
             */
            $fieldname = 'set_name';
            if (!JOSC_TableUtils::TableFieldCheck( $fieldname, $columns )) {
                $query[] = "ALTER TABLE `#__comment_setting` "
                . "\n ADD `set_name` VARCHAR(50)  NOT NULL default '' "
                . "\n AFTER `id`;"
                ;
                $install_log .= "- update of $fieldname.<br />";
            } else {
                $install_log .= "- $fieldname exist.<br />";
            }
            /*
             *  component
             */
            $fieldname = 'set_component';
            if (!JOSC_TableUtils::TableFieldCheck( $fieldname, $columns )) {
                $query[] = "ALTER TABLE `#__comment_setting` "
                . "\n ADD `set_component` VARCHAR(50)  NOT NULL default '' "
                . "\n AFTER `set_name`;"
                ;
                $install_log .= "- update of $fieldname.<br />";
            } else {
                $install_log .= "- $fieldname exist.<br />";
            }
            /*
             *  sectionid
             */
            $fieldname = 'set_sectionid';
            if (!JOSC_TableUtils::TableFieldCheck( $fieldname, $columns )) {
                $query[] = "ALTER TABLE `#__comment_setting` "
                . "\n ADD `set_sectionid` INT(11) NOT NULL default '0' "
                . "\n AFTER `set_component`;"
                ;
                $install_log .= "- update of $fieldname.<br />";
            } else {
                $install_log .= "- $fieldname exist.<br />";
            }
        }

        /*
         * Execute queries and set resulting log
         */
        $install_log2 = "";
        if (count($query)>0) {
            foreach ($query as $sql) {
                $database->SetQuery($sql);
                if(!$result = $database->query()) {
                    $install_log2 .= "Install error: " . $database->stderr() . "<br />" . $sql ."<br /><br />";
                }
            }
        }
        $install_log .= $install_log2;
        return (!$install_log2);  // true if no error / false if error
    }

    function checkDatabase( &$install_log )
    {
        $database =& JFactory::getDBO();

        if (JOSC_TableUtils::existsTable('#__comment')) {

            return( JOSC_install::checkCompatibility( $install_log ) );

        } else {

                /*
                 * #__comment
                 */
            $install_log .= "Create #__comment table.<br />";
                /* in case of change, don't forget to update the JOSC_josComment class */
            $query = JOSC_install::getQuery_Create__comment();

            $database->SetQuery($query);
            $result = $database->query();
            /*
             * component/contentid index
             */
            if ($result) {
                $install_log .= "Create com_contentid index<br />";
                $query = "ALTER TABLE `#__comment` ADD INDEX `com_contentid` ( `component`, `contentid` )";
                $database->SetQuery($query);
                $result = $database->query();
            }
                        /*
                         * check result
                         */
            if(!$result) {
                $install_log .= "Install error: " . $database->stderr() . "<br /><br />";
                return false; // or die(_JOOMLACOMMENT_SAVINGFAILED);
            }

                /*
                 * #__comment_setting
                 */
            $install_log .= "Create #__comment_setting table.<br />";
            $query = JOSC_install::getQuery_Create__comment_setting();
            $database->SetQuery($query);
            $result = $database->query();
                        /*
                         * check result
                         */
            if(!$result) {
                $install_log .= "Install error: " . $database->stderr() . "<br /><br />";
                return false; // or die(_JOOMLACOMMENT_SAVINGFAILED);
            }

                /*
                 * #__comment_captcha
                 */
            $install_log .= "Create #__comment_captcha table.<br />";
            $query = JOSC_install::getQuery_Create__comment_captcha();
            $database->SetQuery($query);
            $result = $database->query();
                        /*
                         * check result
                         */
            if(!$result) {
                $install_log .= "Install error: " . $database->stderr() . "<br /><br />";
                return false; // or die(_JOOMLACOMMENT_SAVINGFAILED);
            }

                /*
                 * #__comment_voting
                 */
            $install_log .= "Create #__comment_voting table.<br />";
            $query = JOSC_install::getQuery_Create__comment_voting();
            $database->SetQuery($query);
            $result = $database->query();
                        /*
                         * check result
                         */
            if(!$result) {
                $install_log .= "Install error: " . $database->stderr() . "<br /><br />";
                return false; // or die(_JOOMLACOMMENT_SAVINGFAILED);
            }



            return true;
        }
    }

    function getQuery_Create__comment()
    {
        $query = "CREATE TABLE `#__comment` (
                `id` INT(10) NOT NULL auto_increment,
                `contentid` INT(10) NOT NULL default '0',
                `component` VARCHAR(50) NOT NULL default '',
                `ip` VARCHAR(15) NOT NULL default '',
                `userid` int(11),
                `usertype` VARCHAR(25) NOT NULL default 'Unregistered',
                `date` DATETIME NOT NULL default '0000-00-00 00:00:00',
                `name` VARCHAR(30) NOT NULL default '',
                `email` VARCHAR(50) NOT NULL default '',
                `website` VARCHAR(100) NOT NULL default '',
                `notify` TINYINT(1) NOT NULL default '0',
                `title` VARCHAR(50) NOT NULL default '',
                `comment` TEXT NOT NULL,
                `published` TINYINT(1) NOT NULL default '0',
                `voting_yes` INT(10) NOT NULL default '0',
                `voting_no` INT(10) NOT NULL default '0',
                `parentid` INT(10) NOT NULL default '-1',
                `importtable` VARCHAR(30) NOT NULL default '',
                `importid` INT(10) NOT NULL default '0',
                `importparentid` INT(10) NOT NULL default '-1',
                PRIMARY KEY  (`id`)) type=MyISAM;";
        return $query;
    }

        function getQuery_Create__comment_setting()
        {
            /* in case of change, don't forget to update the JOSC_josComment class */
	    $query = "CREATE TABLE `#__comment_setting` (
               `id` INT(11) NOT NULL auto_increment,
               `set_name` VARCHAR(50) NOT NULL default '',
               `set_component` VARCHAR(50) NOT NULL default '',
               `set_sectionid` INT(11) NOT NULL default '0',
               `params` text NOT NULL,
               PRIMARY KEY  (`id`))  type=MyISAM";
            return $query;
        }

        function getQuery_Create__comment_captcha()
        {
            $query = "CREATE TABLE IF NOT EXISTS `#__comment_captcha` (
        `ID` int(11) NOT NULL auto_increment,
        `insertdate` datetime NOT NULL default '0000-00-00 00:00:00',
        `referenceid` varchar(100) NOT NULL default '',
        `hiddentext` varchar(100) NOT NULL default '',
        PRIMARY KEY (`ID`)) type=MyISAM";
            return $query;
        }

        function getQuery_Create__comment_voting()
        {
            $query = "CREATE TABLE IF NOT EXISTS `#__comment_voting` (
        `id` INT(10) NOT NULL default '0',
        `ip` VARCHAR(15) NOT NULL default '',
        `time` INTEGER NOT NULL default '0') type=MyISAM";
            return $query;
        }

        function createImportSetting($execute=true)
        {
            $database =& JFactory::getDBO();

            $result = true;
                /* in case of change, don't forget to update the josImportSetting class */
            $query = "CREATE TABLE `#__comment_importsetting` (
                        `id` INT(10) NOT NULL auto_increment,
                        `tablename` VARCHAR(100) ";

            $columns = JOSC_TableUtils::TableColumnsGet( '#__comment' );
            if ($columns) {
                foreach($columns as $col) {
                    if ($col->Field != 'id')
                    $query .= ",`$col->Field` VARCHAR(100)";
                }
                $query .= ", PRIMARY KEY  (`id`)) type=MyISAM;";
                if ($execute) {
                    $database->SetQuery($query);
                    $result = $database->query();
                } else {
                    $result = $query;
                }
            } else {
                $query .= ") type=MyISAM;";
            }
            return $result;
        }
}
?>
