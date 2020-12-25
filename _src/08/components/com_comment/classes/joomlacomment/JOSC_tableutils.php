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
class JOSC_TableUtils {
	private static $instances = null;
	function getTableList()
	{
		$database =& JFactory::getDBO();

		$database->setQuery( 'SHOW TABLES' );
		return $database->loadResultArray();
	}

	public function existsTable($name) {
		if(!isset(self::$instances[$name])) {
			$database =& JFactory::getDBO();
			$name = $database->replacePrefix($name);
			$database->setQuery("SHOW TABLES LIKE '$name';");
			self::$instances[$name] = ($database->loadResult()) ? true : false;
		}

		return self::$instances[$name];
	}

	function TableColumnsGet( $tablename, $key='' ) {
		$database =& JFactory::getDBO();

		$database->setQuery("SHOW COLUMNS FROM $tablename");
   		return ( $database->loadObjectList($key) );
	}

	function TableFieldCheck( $fieldname, &$tablecols ) {

		if (!$tablecols) return false;
    		$found = false;

    	foreach( $tablecols as $col ) {
    		if ($col->Field == $fieldname) {
        		$found = true;
         		break;
        	}
    	}

    	return( $found );
	}
}
?>
