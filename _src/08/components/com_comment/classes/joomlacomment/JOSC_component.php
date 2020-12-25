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
class JOSC_component  extends JObject {
    var $_component;
    var	$_sectionid;
    var	$_id; /* content_id */
    var $_official;

    public function __construct($component='',$sectionid=0,$id=0) {
		$this->_component 	= $component;
		$this->_sectionid 	= $sectionid;
		$this->_id			= $id;
		/*
		 * set official property for backward compatibility in custom plugins
		 */
		switch ($this->_component) {
			case 'com_content':
			case 'com_docman':
			case 'com_eventlist':
			case 'com_joomlaflasgames':
			case 'com_puarcade':
			case 'com_seyret':
				$this->_official = true;
				break;
			default:
				$this->_official = false;
				break;
		}
	}
}

?>
