<?php
/***************************************************************
*  Copyright notice
*
*  Copyright 2009 Daniel Dimitrov. (http://compojoom.com)
*  All rights reserved
*
*  This script is part of the Compojoom Comment project. The Compojoom Comment project is
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

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class commentModelMaintenance extends JModel {
	public $sysInfo = array();
	public function getSystemInformation() {
		$sysInfo['php'] = phpversion();
		$sysInfo['phpRecommended'] = '5.2.2';
		$sysInfo['mysql'] = $this->getMysqlVersion();
		$sysInfo['mysqlRecommended'] = '5.0';
		
		$sysInfo['jversion'] = JVERSION;
		$sysInfo['jversionRecommended'] = '1.5.15';

		$sysInfo['warnings'] = $this->compareVersions($sysInfo);
		return $sysInfo;
	}

	private function compareVersions($sysInfo) {
		$warning = '';
		if(version_compare($sysInfo['php'], $sysInfo['phpRecommended']) == -1) {
			$warning .= 'You can encounter problems with your php version. Please ask your host to upgrade to at least php' . $sysInfo['phpRecommended'] . '<br />';
		}
		if(version_compare($sysInfo['mysql'], $sysInfo['mysqlRecommended']) == -1) {
			$warning .= 'You can encounter problems with your mysql version. Please ask your host to upgrade to at least mysql' .$sysInfo['mysqlRecommended'] . '<br />' ;
		}

		if(version_compare($sysInfo['jversion'], $sysInfo['jversionRecommended']) == -1) {
			$warning .= 'You can encounter problems with your Joomla version. Please upgrade to at least joomla 1.5.12 or bigger.';
		}
		
		return $warning;
	}

	private function getMysqlVersion() {
		$db = JFactory::getDBO();
		$query = 'SELECT VERSION() as ve';

		$db->setQuery($query);
		$result = $db->loadObject();

		return $result->ve;
	}
}

?>