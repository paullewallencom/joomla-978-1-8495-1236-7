<?php
defined('_JEXEC')  or die('Direct Access to this location is not allowed.');

/*
 * Copyright (c) Daniel Dimitrov (http://compojoom.com) . All rights reserved.
 * License http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *
 * Compojoom Comment is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * Compojoom Comment is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA  02110-1301, USA.
 */
require_once(JPATH_ADMINISTRATOR. DS . 'components' . DS . 'com_comment' . DS . 'library' . DS . 'JOSC_config.php');
require_once(JPATH_SITE. DS . 'components' . DS . 'com_comment' . DS . 'joscomment' . DS . 'utils.php');

function com_uninstall() {
	/*
	 * check where or not configuration for com_content is set and if _complete uninstall is true - delete tables
	 * in future we should split the configuration to main and one for plugins, but for the moment
	 * this is the only way to do it.
	 */
	$null = null;
	$comObject = JOSC_utils::ComPluginObject('com_content', $null);
	$config = JOSC_config::getConfig(0, $comObject);

	if($config->_complete_uninstall) {
		_removeTables();
		echo '<p>' . JText::_('UNINSTALL COMPLETE MODE PARAMETER HAS VALUE YES') . ' : ';
		echo JText::_('Compojoom Comment tables have been deleted') . '</p>';
	}
	else {
		echo '<p>' . JText::_('UNINSTALL COMPLETE MODE PARAMETER HAS VALUE NO') . ' : ';
		echo JText::_('Compojoom Comment tables have NOT BEEN deleted') . '</p>';
	}
	_uninstallPlugin();
}

function _removeTables() {
	$database =& JFactory::getDBO();
	$queries = array(
		'#__comment' => 'DROP TABLE IF EXISTS' . $database->nameQuote('#__comment'),
		'#__comment_captcha' => 'DROP TABLE IF EXISTS' . $database->nameQuote('#__comment_captcha'),
		'#__comment_joomvertising' => 'DROP TABLE IF EXISTS' . $database->nameQuote('#__comment_joomvertising'),
		'#__comment_setting' => 'DROP TABLE IF EXISTS' . $database->nameQuote('#__comment_setting'),
		'#__comment_voting' => 'DROP TABLE IF EXISTS' . $database->nameQuote('#__comment_voting')
	);

	foreach($queries as $key => $query) {
		$database->setQuery($query);
		if($database->query()) {
			echo '<p>' . JText::_('DATABASE TABLE DELETED') . ' ' . $key . '</p>';
		} else {
			echo '<p>' . JText::_('ENABLE TO DELETE TABLE') . ' ' . $key . '</p>';
		}
	}
	return true;
}

function _uninstallPlugin() {
	$database =& JFactory::getDBO();
	$query = 'DELETE FROM ' . $database->nameQuote('#__plugins')
		. ' WHERE ' . $database->nameQuote('element') . ' = ' . $database->Quote('joscomment');
	$database->setQuery($query);
	if($database->query()) {
		echo '<p>' . JText::_('THE CONTENT PLUGIN WAS DELETED FROM DATABASE') . '</p>';
	}

	$filesPath = array (
		JPATH_SITE. DS . 'plugins' . DS . 'content' . DS . 'joscomment.php',
		JPATH_SITE. DS . 'plugins' . DS . 'content' . DS . 'joscomment.xml'
	);

	$filesDeleted = JFile::delete($filesPath);
	if($filesDeleted) {
		echo '<p>' . JText::_('THE CONTENT PLUGIN FILES WERE DELETED') . '</p>';
	}

	echo '<p><b>' . JText::_('COMPOJOOM COMMENT WAS SUCCESSFULLY UNINSTALLED') . '</b></p>';

	return true;
}
?>
